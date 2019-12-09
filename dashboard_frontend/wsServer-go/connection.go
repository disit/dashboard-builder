package main

import (
	"bytes"
	"context"
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"strconv"

	"errors"
	"net"
	"strings"

	"gopkg.in/ldap.v3"

	oidc "github.com/coreos/go-oidc"
)

var cidrs []*net.IPNet

func init() {

	maxCidrBlocks := []string{
		"127.0.0.1/8",    // localhost
		"10.0.0.0/8",     // 24-bit block
		"172.16.0.0/12",  // 20-bit block
		"192.168.0.0/16", // 16-bit block
		"169.254.0.0/16", // link local address
		"::1/128",        // localhost IPv6
		"fc00::/7",       // unique local address IPv6
		"fe80::/10",      // link local address IPv6
	}

	cidrs = make([]*net.IPNet, len(maxCidrBlocks))
	for i, maxCidrBlock := range maxCidrBlocks {
		_, cidr, _ := net.ParseCIDR(maxCidrBlock)
		cidrs[i] = cidr
	}
}

type Message struct {
	MsgType    string      `json:"msgType"`
	MetricName string      `json:"metricName"`
	NewValue   interface{} `json:"newValue"`
}

type EmitterMessage struct {
	MsgType          string      `json:"msgType"`
	WidgetUniqueName string      `json:"widgetUniqueName"`
	NewValue         interface{} `json:"newValue"`
	MsgId            int64       `json:"msgId"`
}

type WebSocketServer struct {
	activeEnv     string
	dbhost        string
	username      string
	password      string
	dbname        string
	serverAddress string
	serverPort    string
	redisAddress  string
	redisPassword string
	redisEnabled  string
	clientSecret  string
	clientID      string
	ssoHost       string
	ssoIssuer     string
	ownershipUrl  string
	ldapServer    string
	ldapPort      string
	ldapBaseDN    string
	validOrigins  string
	requireToken  string
	clientWidgets map[string][]*WebsocketUser
}

type callBody struct {
	ElementId   int64  `json:"elementId"`
	ElementType string `json:"elementType"`
	ElementName string `json:"elementName"`
}

type ClientManager struct {
	clients    map[string]*WebsocketUser
	register   chan *WebsocketUser
	unregister chan *WebsocketUser
	replyAll   chan []byte
}

func ownershipRegisterDash(newDashId int64, title interface{}, dat map[string]interface{}) string {

	callBody := callBody{ElementId: newDashId, ElementType: "DashboardID", ElementName: title.(string)}
	jsonValue, _ := json.Marshal(callBody)

	apiURL := ws.ownershipUrl + "/v1/register/?accessToken=" + fmt.Sprint(dat["accessToken"])

	resp, err := http.Post(apiURL, "application/json", bytes.NewBuffer(jsonValue))
	if err != nil {
		log.Print(err)
		return "Ko"
	} else {
		defer resp.Body.Close()
		fmt.Println("response Status:", resp.Status)
		fmt.Println("response Headers:", resp.Header)
		body, _ := ioutil.ReadAll(resp.Body)
		fmt.Println("response Body:", string(body))
		return "Ok"
	}
}

func ownershipLimitsDash(dat map[string]interface{}) (int, int, error) {
	apiURL := ws.ownershipUrl + "/v1/limits/?type=DashboardID&accessToken=" + fmt.Sprint(dat["accessToken"])

	resp, err := http.Get(apiURL)
	if err != nil {
		log.Print(err)
		return 0, 0, err
	} else {
		defer resp.Body.Close()
		fmt.Println("response Status:", resp.Status)
		fmt.Println("response Headers:", resp.Header)
		body, _ := ioutil.ReadAll(resp.Body)
		fmt.Println("response Body:", string(body))
		var result map[string]interface{}
		json.Unmarshal(body, &result)
		limits := result["limits"].([]interface{})[0].(map[string]interface{})
		limit, _ := strconv.Atoi(limits["limit"].(string))
		current, _ := strconv.Atoi(limits["current"].(string))

		return limit, current, nil
	}
}

/*func getUserinfo(client *oauth2., endpoint string, token string) (jose.Claims, error) {
	req, err := http.NewRequest(http.MethodGet, endpoint, nil)
	if err != nil {
		return nil, err
	}
	req.Header.Set(authorizationHeader, fmt.Sprintf("Bearer %s", token))

	resp, err := client.HttpClient().Do(req)
	if err != nil {
		return nil, err
	}
	if resp.StatusCode != http.StatusOK {
		return nil, errors.New("token not validate by userinfo endpoint")
	}
	content, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		return nil, err
	}
	var claims map[string]interface{}
	if err := json.Unmarshal(content, &claims); err != nil {
		return nil, err
	}

	return claims, nil
}*/

func checkToken(accessToken string, clientID string) (string, string, error) {
	log.Print("checkToken")
	ctx := context.Background()
	provider, err := oidc.NewProvider(ctx, ws.ssoIssuer)
	if err != nil {
		log.Print(err)
		return "", "", err
	}

	verifier := provider.Verifier(&oidc.Config{SkipClientIDCheck:true})
	idToken, err := verifier.Verify(ctx, accessToken)
	if err != nil {
		log.Print(err)
		return "", "", err
	}
	var claims map[string]interface{}
	if err := idToken.Claims(&claims); err != nil {
		return "", "", err
	}
	if claims["aud"]!=nil {
		if claims["aud"]!=clientID {
			log.Print("checkToken 'aud' claim is '",claims["aud"],"' expected ", clientID)
			return "", "", errors.New("'aud' claim is not valid")
		}
	} else {
		if claims["azp"] == nil {
			log.Print("checkToken aud and azp claims missing")
			return "", "", errors.New("missing 'aud' and 'azp' claims")
		} else if claims["azp"]!=clientID {
			log.Print("checkToken azp claim is '",claims["aud"],"' expected ", clientID)
			return "", "", errors.New("'azp' claim is not valid")
		}
	}
	var role string
	var username string
	if claims["username"] != nil {
		username = claims["username"].(string)
	} else if claims["preferred_username"] != nil {
		username = claims["preferred_username"].(string)
	}
	log.Print(claims)
	roles, ok := claims["roles"].([]interface{})
	if ok {
		for _, value := range roles {
			if value == "RootAdmin" {
				role = value.(string)
				break
			} else if value == "ToolAdmin" {
				role = value.(string)
				break
			} else if value == "AreaManager" {
				role = value.(string)
				break
			} else if value == "Manager" {
				role = value.(string)
				break
			} else if value == "Observer" {
				role = value.(string)
				break
			} else if value == "Public" {
				role = value.(string)
				break
			}
		}
	} else {
		log.Print("cannot convert roles")
	}
	return username, role, nil
}

func getOrganization(username string) (string, error) {
	baseDN := ws.ldapBaseDN
	l, err := ldap.Dial("tcp", fmt.Sprintf("%s:%s", ws.ldapServer, ws.ldapPort))
	if err != nil {
		log.Print(err)
		return "", err
	}
	defer l.Close()

	searchRequest := ldap.NewSearchRequest(
		baseDN, // The base dn to search
		ldap.ScopeWholeSubtree, ldap.NeverDerefAliases, 0, 0, false,
		"(&(objectClass=organizationalUnit)(l=cn="+username+","+baseDN+"))", // The filter to apply
		[]string{"dn", "ou"},                                                // A list attributes to retrieve
		nil,
	)

	sr, err := l.Search(searchRequest)
	if err != nil {
		log.Print(err)
		return "", err
	}
	organization := "Other"
	if len(sr.Entries) == 1 {
		entry := sr.Entries[0]
		organization = entry.GetAttributeValue("ou")
	}
	//log.Print("Organization: " + username + "-->" + organization)
	return organization, nil
}

func isPrivateAddress(address string) (bool, error) {
	ipAddress := net.ParseIP(address)
	if ipAddress == nil {
		return false, errors.New("address is not valid")
	}

	for i := range cidrs {
		if cidrs[i].Contains(ipAddress) {
			return true, nil
		}
	}

	return false, nil
}

// FromRequest ritorna il reale IP address dagli http request headers.
func FromRequest(r *http.Request) (string, string) {

	xRealIP := r.Header.Get("X-Real-Ip")
	xForwardedFor := r.Header.Get("X-Forwarded-For")
	origin := r.Header.Get("Origin")

	if xRealIP == "" && xForwardedFor == "" {
		var remoteIP string

		if strings.ContainsRune(r.RemoteAddr, ':') {
			remoteIP, _, _ = net.SplitHostPort(r.RemoteAddr)
		} else {
			remoteIP = r.RemoteAddr
		}

		return remoteIP, origin
	}

	for _, address := range strings.Split(xForwardedFor, ",") {
		address = strings.TrimSpace(address)
		isPrivate, err := isPrivateAddress(address)
		if !isPrivate && err == nil {
			return address, origin
		}
	}

	return xRealIP, origin
}
