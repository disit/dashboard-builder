package main

import (
	"context"
	"database/sql"
	"encoding/json"
	"flag"
	"fmt"
	"log"
	"net/http"
	"os"
	"runtime"
	"runtime/debug"
	"strings"
	"sync"
	"time"

	oidc "github.com/coreos/go-oidc"
	"github.com/go-ini/ini"
	"github.com/go-sql-driver/mysql"
	"github.com/gomodule/redigo/redis"
	"github.com/gorilla/websocket"
	uuid "github.com/satori/go.uuid"
)

var manager = ClientManager{

	register:   make(chan *WebsocketUser),
	unregister: make(chan *WebsocketUser),
	clients:    make(map[string]*WebsocketUser),
	replyAll:   make(chan []byte),
}

var upgrader = websocket.Upgrader{
	CheckOrigin: func(r *http.Request) bool {
		return true
	},
}

//var tpl = template.Must(template.ParseFiles("test.html"))
var mu sync.Mutex
var db *sql.DB
var ws = buildAndInit()
var port = getPort()
var dashboard = ws.dbname
var addr = flag.String("addr", "0.0.0.0:"+port, "http service address")

const (
	//tempo concesso per scrivere un messaggio al peer.
	writeWait = 10 * time.Second

	//tempo concesso per leggere il prossimo pong msg dal peer.
	pongWait = 3600 * time.Second

	//manda pings al peer con questo periodo; deve essere minore del pongWait.
	pingPeriod = (pongWait * 9) / 10
)

func main() {

	flag.Parse()
	log.SetFlags(log.LstdFlags | log.Lshortfile)

	initDB()

	defer db.Close()

	go manager.start()
	http.HandleFunc("/", handleConnections)
	if ws.redisEnabled == "yes" {
		ctx := context.Background()
		go startRedis(ctx, ws.redisAddress)
	}
	// go countGo()

	log.Printf("serving on port " + port + "")
	log.Fatal(http.ListenAndServe(*addr, nil))

}

/*
func home(w http.ResponseWriter, r *http.Request) {
	tpl.Execute(w, "ws://"+r.Host+"/server")
}
*/

func readEnvOrIni(section *ini.Section, envPrefix string, key string, activeEnv string) string {
	if value := os.Getenv("WSS_" + strings.ToUpper(envPrefix+key)); value != "" {
		return value
	}
	return section.Key(key + "[" + activeEnv + "]").String()
}

// funzione di inizializ. con parsing dei file ini e creazione della mappa dei widgets.

func buildAndInit() *WebSocketServer {

	envFileContent, err := ini.Load("../conf/environment.ini")
	if err != nil {
		fmt.Printf("Fail to read file: %v", err)
		os.Exit(1)
	}
	genFileContent, err := ini.Load("../conf/general.ini")
	if err != nil {
		fmt.Printf("Fail to read file: %v", err)
		os.Exit(1)
	}
	dbFileContent, err := ini.Load("../conf/database.ini")
	if err != nil {
		fmt.Printf("Fail to read file: %v", err)
		os.Exit(1)
	}
	wsServerContent, err := ini.Load("../conf/webSocketServer.ini")
	if err != nil {
		fmt.Printf("Fail to read file: %v", err)
		os.Exit(1)
	}

	ssoFileContent, err := ini.Load("../conf/sso.ini")
	if err != nil {
		fmt.Printf("Fail to read file: %v", err)
		os.Exit(1)
	}
	redisServerContent, err := ini.Load("../conf/redis.ini")
	if err != nil {
		fmt.Printf("Fail to read file: %v", err)
		os.Exit(1)
	}
	ownershipContent, err := ini.Load("../conf/ownership.ini")
	if err != nil {
		fmt.Printf("Fail to read file: %v", err)
		os.Exit(1)
	}
	ldapContent, err := ini.Load("../conf/ldap.ini")
	if err != nil {
		fmt.Printf("Fail to read file: %v", err)
		os.Exit(1)
	}

	wss := new(WebSocketServer)

	a := envFileContent.Sections()
	wss.activeEnv = a[0].Key("environment[value]").String()
	log.Print("active env: ", wss.activeEnv)

	a = genFileContent.Sections()
	wss.dbhost = readEnvOrIni(a[0], "DB", "host", wss.activeEnv)
	log.Print(wss.dbhost)
	a = dbFileContent.Sections()
	wss.username = readEnvOrIni(a[0], "DB", "username", wss.activeEnv)
	wss.password = readEnvOrIni(a[0], "DB", "password", wss.activeEnv)
	wss.dbname = readEnvOrIni(a[0], "", "dbname", wss.activeEnv)

	a = wsServerContent.Sections()
	wss.serverAddress = readEnvOrIni(a[0], "", "wsServerAddress", wss.activeEnv)
	wss.serverPort = readEnvOrIni(a[0], "", "wsServerInternalPort", wss.activeEnv)
	if wss.serverPort == "" {
		wss.serverPort = readEnvOrIni(a[0], "", "wsServerPort", wss.activeEnv)
	}
	wss.validOrigins = readEnvOrIni(a[0], "", "validOrigins", wss.activeEnv)
	wss.requireToken = readEnvOrIni(a[0], "", "requireToken", wss.activeEnv)
	wss.debug = readEnvOrIni(a[0], "", "debug", wss.activeEnv) == "true"
	wss.debugKey = readEnvOrIni(a[0], "", "debugKey", wss.activeEnv)

	a = ssoFileContent.Sections()
	wss.clientSecret = readEnvOrIni(a[0], "", "ssoClientSecret", wss.activeEnv)
	wss.clientID = readEnvOrIni(a[0], "", "ssoClientId", wss.activeEnv)
	wss.ssoHost = readEnvOrIni(a[0], "", "ssoHost", wss.activeEnv)
	wss.ssoIssuer = readEnvOrIni(a[0], "", "ssoIssuer", wss.activeEnv)
	log.Print("sso: issuer ", wss.ssoIssuer)

	a = redisServerContent.Sections()
	wss.redisEnabled = readEnvOrIni(a[0], "", "redisEnabled", wss.activeEnv)
	wss.redisAddress = readEnvOrIni(a[0], "", "redisHost", wss.activeEnv)
	wss.redisPassword = readEnvOrIni(a[0], "", "redisPwd", wss.activeEnv)
	log.Print("redis enabled ", wss.redisEnabled, " ", wss.redisAddress, " ", wss.redisPassword)

	a = ownershipContent.Sections()
	wss.ownershipURL = readEnvOrIni(a[0], "", "ownershipApiBaseUrl", wss.activeEnv)
	log.Print("ownership: ", wss.ownershipURL)

	a = ldapContent.Sections()
	wss.ldapServer = readEnvOrIni(a[0], "", "ldapServer", wss.activeEnv)
	wss.ldapPort = readEnvOrIni(a[0], "", "ldapPort", wss.activeEnv)
	wss.ldapBaseDN = readEnvOrIni(a[0], "", "ldapBaseDN", wss.activeEnv)
	log.Print("ldap: ", wss.ldapServer, ":", wss.ldapPort, " ", wss.ldapBaseDN)

	wss.clientWidgets = make(map[string][]*WebsocketUser)

	ctx := context.Background()
	for {
		wss.oidcProvider, err = oidc.NewProvider(ctx, wss.ssoIssuer)
		if err != nil {
			log.Print("ERROR init OIDC Provider: ", err, " waiting 30s...")
			time.Sleep(30 * time.Second)
		} else {
			log.Print("OIDC Provider ", wss.ssoIssuer, " initalized")
			break
		}
	}

	return wss

}

// goroutine che si occupa dei subscribes/unsubscribes e dell'inoltro delle risposte.

func (manager *ClientManager) start() {
	for {
		select {
		case conn := <-manager.register:
			manager.clients[conn.id] = conn
			if ws.debug {
				log.Println("INFO A new socket has connected: ", len(manager.clients), " from: ", conn.ClientIp, " ", conn.WidgetUniqueName, " ", conn.MetricName)
			}

		case conn := <-manager.unregister:
			if _, ok := manager.clients[conn.id]; ok {
				close(conn.send)
				delete(manager.clients, conn.id)

				closed(conn)
				if ws.debug {
					log.Println("INFO A socket has disconnected: ", len(manager.clients), " from: ", conn.ClientIp, " ", conn.WidgetUniqueName, " ", conn.MetricName)
				}
			}
		case message := <-manager.replyAll:
			dat := processingMsg2(message)

			var conn string
			if dat["metricName"] == nil {
				conn = dat["widgetUniqueName"].(string)
			} else {
				conn = dat["metricName"].(string)
			}
			if ws.debug {
				log.Print("INFO sending msg for ", conn, " to ", len(ws.clientWidgets[conn]), " clients")
			}
			mu.Lock()
			for key := range ws.clientWidgets[conn] {
				//log.Print(conn, " clients: ", ws.clientWidgets[conn])
				if ws.clientWidgets[conn][key].sendingAck {
					ws.clientWidgets[conn][key].sendingAck = false
				} else {
					sendOnChannelNoPanic(ws.clientWidgets[conn][key].send, message)
					//select {
					//case ws.clientWidgets[conn][key].send <- message:
					/*default:
					close(ws.clientWidgets[conn][key].send)
						closed(ws.clientWidgets[conn][key])
						delete(ws.clientWidgets, ws.clientWidgets[conn][key].id)*/
					//}
				}
			}
			mu.Unlock()
		}
	}
}

// send message on a channel, recover panic if it is closed
func sendOnChannelNoPanic(c chan []byte, m []byte) {
	defer func() {
		if r := recover(); r != nil {
			fmt.Println("Recovered write channel panic:\n", string(debug.Stack()))
		}
	}()
	select {
	case c <- m:
	}
}

// handler che fa l'upgrade a websocket, reindirizza i client alla registrazione e lancia le routine di read e write.

func handleConnections(w http.ResponseWriter, r *http.Request) {
	clientIP, origin := FromRequest(r)
	validOrigin := origin != "" && strings.Contains(ws.validOrigins, origin)
	if !validOrigin {
		log.Print("WARNING invalid origin \"", origin, "\" from ", clientIP)
	}
	conn, err := (&upgrader).Upgrade(w, r, nil)
	if err != nil {

		log.Print("ERROR upgrade:", err)
		return

	}
	client := &WebsocketUser{id: uuid.Must(uuid.NewV4(), nil).String(), socket: conn, send: make(chan []byte), ClientIp: clientIP, ValidOrigin: validOrigin, Origin: origin}
	//log.Print(client.clientIp)
	manager.register <- client

	go client.reader()
	go client.writer()

}

// funzione di chiusura connessione che provvede a fare l'unset dei parametri.

func closed(u *WebsocketUser) {
	var unsetKey int
	op := false
	if u.UserType == "widgetInstance" {
		//log.Print(len(ws.clientWidgets[u.metricName]))
		mu.Lock()
		if len(ws.clientWidgets[u.MetricName]) == 1 {
			publish([]byte("unsubscribe"+u.MetricName), "default")
		}
		mu.Unlock()
	}

	if u.WidgetUniqueName != nil && u.WidgetUniqueName != "" {
		mu.Lock()

		widgets := ws.clientWidgets
		name := u.MetricName
		if name == "" {
			name = u.WidgetUniqueName.(string)
		}
		for value := range widgets[name] {
			if widgets[name][value] == u {
				unsetKey = value
				op = true
				break
			}
		}
		if op {
			widgets[name][unsetKey] = widgets[name][len(widgets[name])-1]
			widgets[name][len(widgets[name])-1] = nil
			widgets[name] = widgets[name][:len(widgets[name])-1]
			ws.clientWidgets = widgets
		}
		//log.Print("closed: removed user for ", name, " count: ", len(widgets[name]))
		mu.Unlock()
	} else {
		log.Print("WARNING closed: user with no widgetUniqueName")
	}

}

// routine di read per la lettura dei messaggi. Viene lanciato dbComunication per il processing e la logica sui messaggi.

func (c *WebsocketUser) reader() {
	defer func() {
		manager.unregister <- c
		c.socket.Close()
	}()
	c.socket.SetReadDeadline(time.Now().Add(pongWait))
	c.socket.SetPongHandler(func(string) error { c.socket.SetReadDeadline(time.Now().Add(pongWait)); return nil })

	for {
		_, message, err := c.socket.ReadMessage()
		if err != nil {
			if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway, websocket.CloseAbnormalClosure) {
				log.Printf("ERROR read: %v", err)
			}
			break
		}
		if ws.debug {
			log.Printf("INFO recv: %s", message)
		}
		dbCommunication(message, c)
	}

}

//routine di write che raccoglie i messaggi di risposta e invia verso i destinatari.

func (c *WebsocketUser) writer() {
	ticker := time.NewTicker(pingPeriod)
	defer func() {
		c.socket.Close()
		ticker.Stop()
	}()
	for {
		select {
		case message, ok := <-c.send:
			c.socket.SetWriteDeadline(time.Now().Add(writeWait))
			if !ok {
				c.socket.WriteMessage(websocket.CloseMessage, []byte{})
				return
			}
			c.socket.WriteMessage(websocket.TextMessage, message)

		case <-ticker.C:
			c.socket.SetWriteDeadline(time.Now().Add(writeWait))
			if err := c.socket.WriteMessage(websocket.PingMessage, nil); err != nil {
				return
			}
		}
	}
}

// funzione fittizia che uso per testare  via via le query

func dbTest() {

	db, err := sql.Open("mysql", "root:emanuele@tcp(127.0.0.1:3306)/dashboard")
	if err != nil {
		fmt.Println(err)
	} else {
		fmt.Println("Connection Established")
	}
	defer db.Close()
	var count2 int
	dash := db.QueryRow("SELECT COUNT(*) FROM Dashboard.Config_dashboard "+
		"WHERE title_header = ? AND user = ?;", "asg", "disit")
	err3 := dash.Scan(&count2)
	if err3 != nil {
		panic(err.Error())
	}
	fmt.Println(count2)

	myString := `{"msgType" : "Firenze", "metricName" : 3, "newValue" : {"personNumber": 321, "lat": 6.05}}`
	mapstr, newVal := processingMsg([]byte(myString))
	fmt.Println(mapstr)
	fmt.Println(newVal)

}

//funzioni per la decodifica dei messaggi json; sono usate in dbProcessing.

func processingMsg2(jsonMsg []byte) map[string]interface{} {
	var dat map[string]interface{}
	err := json.Unmarshal(jsonMsg, &dat)
	if err != nil {
		log.Println("ERROR JSON Decoding error:", err)
	}
	return dat
}

func processingMsg(jsonMsg []byte) (map[string]interface{}, map[string]interface{}) {
	var dat map[string]interface{}
	err := json.Unmarshal(jsonMsg, &dat)
	if err != nil {
		log.Println("ERROR Decoding error:", err, " msg:", jsonMsg)
	}
	parse, err := json.Marshal(dat["newValue"])

	//dat["newValue"] = fmt.Sprint(string(parse))
	var jsonParsed map[string]interface{}
	json.Unmarshal(parse, &jsonParsed)

	return dat, jsonParsed

}

// apro la connessione al database

func initDB() {

	config := mysql.NewConfig()

	config.User = ws.username
	config.Passwd = ws.password
	config.Net = "tcp"
	config.Addr = ws.dbhost
	config.DBName = ws.dbname
	confstring := config.FormatDSN()
	log.Print(confstring)
	var err error
	db, err = sql.Open("mysql", confstring)
	if err != nil {
		log.Panic(err)
	}
	db.SetMaxIdleConns(5)
	db.SetConnMaxLifetime(60 * time.Second)
	db.SetMaxOpenConns(20)
	//db.SetMaxOpenConns(qualche quantita` da definire)

}

// funzione lanciata dal main tramite goroutine per far partire il listener sul canale Pub/Sub.

func startRedis(ctx context.Context, redisServerAddr string) {
	for {
		err := listenPubSubChannels(ctx, redisServerAddr,
			func() error {
				// la callback di start e` un buon posto per implementare il riempimento
				// delle notifiche perse. Per adesso, non fa essenzialmente nulla.

				return nil
			},
			func(channel string, message []byte) error {
				if ws.debug {
					log.Printf("INFO REDIS channel: %s, received message: %s\n", channel, message)
				}
				manager.replyAll <- message
				return nil
			}, "newData")

		if err != nil {
			log.Print("ERROR REDIS ", err)
		}

		time.Sleep(3 * time.Second)
		log.Print("REDIS Recovering...")
	}
}

// listenPubSubChannels ascolta i messaggi nel canale Redis di Pub/Sub. la funzione
// onStart e` chiamata subito dopo la sottoscrizione ai canali. la funzione onMessage
// e` invece chiamata dopo ogni messaggio.

func listenPubSubChannels(ctx context.Context, redisServerAddr string,
	onStart func() error,
	onMessage func(channel string, data []byte) error,
	channels ...string) error {

	// Un ping viene settato con questo periodo per controllare l'integrita`
	// della connessione e del server.

	const healthCheckPeriod = time.Minute

	c, err := redis.Dial("tcp", redisServerAddr,
		// il read timeout sul server dovrebbe essere piu` grande del periodo di ping.
		redis.DialReadTimeout(healthCheckPeriod+10*time.Second),
		redis.DialWriteTimeout(10*time.Second))
	if err != nil {
		return err
	}
	defer c.Close()
	c.Do("AUTH", ws.redisPassword)

	psc := redis.PubSubConn{Conn: c}

	if err := psc.Subscribe(redis.Args{}.AddFlat(channels)...); err != nil {
		return err
	}

	mu.Lock()
	if len(ws.clientWidgets) > 0 {
		//log.Print("ok")
		for conn := range ws.clientWidgets {
			publish([]byte("subscribe"+conn), "default")
		}
	}
	mu.Unlock()

	done := make(chan error, 1)

	// Lancia una goroutine per ricevere notifiche dal server.
	go func() {
		for {
			switch n := psc.Receive().(type) {
			case error:
				done <- n
				return
			case redis.Message:
				data := string(n.Data)
				if len(data) >= 9 {
					if data[0:9] == "subscribe" {
						psc.Subscribe(data[9:])
						break
					}

					if data[0:11] == "unsubscribe" {
						psc.Unsubscribe(data[11:])
						break
					}
				}
				if err := onMessage(n.Channel, n.Data); err != nil {
					done <- err
					return
				}
			case redis.Subscription:
				switch n.Count {
				case len(channels):
					// notifica l'applicazione quando tutti i canali sono sottoscritti.
					if err := onStart(); err != nil {
						done <- err
						return
					}
				case 0:
					// ritorna dalla goroutine quando tutti i canali sono disiscritti.
					done <- nil
					return
				}
			}
		}
	}()

	ticker := time.NewTicker(healthCheckPeriod)
	defer ticker.Stop()
loop:
	for err == nil {
		select {
		case <-ticker.C:

			// Manda il ping per testare la salute della connessione e del server.
			// se il corrispondente pong non e' ricevuto, allora la ricezione sulla
			// connessione andra` in timeout e la corrispondente goroutine ritornera`.

			if err = psc.Ping(""); err != nil {
				break loop
			}
		case <-ctx.Done():
			break loop
		case err := <-done:
			// ritorna errore dalla goroutine di ricezione.
			log.Print(err)
			return err
		}
	}

	// Segnala alla goroutine di ricezione di uscire disiscrivendo dai canali .
	psc.Unsubscribe()

	// Aspetta il completamento della goroutine.
	return <-done
}

// funzione per il publish su redis.

func publish(msg []byte, channel string) {
	if ws.redisEnabled != "yes" {
		return
	}
	if channel == "default" {
		channel = "newData"
	}
	c, err := redis.Dial("tcp", ws.redisAddress)
	if err != nil {
		log.Print(err)
		return
	}
	//log.Print("publish on redis ", channel, " msg:", string(msg))
	defer c.Close()
	c.Do("AUTH", ws.redisPassword)
	c.Do("PUBLISH", channel, msg)
}

func countGo() {
	for {
		time.Sleep(5 * time.Second)
		log.Print(runtime.NumGoroutine())
		log.Print(manager.clients)

	}
}

func getPort() string {
	if len(os.Args) > 1 {
		return os.Args[1]
	}
	return ws.serverPort
}
