<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

  This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */

//include 'process-form.php';
include '../config.php';
include '../session.php';
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

//$page = "iframeApp.php?linkUrl=https://www.snap4city.org/drupal/openid-connect/login&linkId=snap4cityPortalLink&pageTitle=www.snap4city.org&fromSubmenu=false";
if(isset($_REQUEST['redirect'])) 
{
   $page = $_REQUEST['redirect'];
}
else
{
    $currDom = $_SERVER['HTTP_HOST'];
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);

    $domQ = "SELECT * FROM Dashboard.Domains WHERE domains LIKE '%$currDom%'";
    $r = mysqli_query($link, $domQ);

    $page = "dashboards.php"; //iframeApp.php?linkUrl=https://www.snap4city.org/drupal/openid-connect/login&linkId=snap4cityPortalLink&pageTitle=www.snap4city.org&fromSubmenu=false";
    if($r)
    {
        if(mysqli_num_rows($r) > 0)
        {
            $row = mysqli_fetch_assoc($r);
            $landingPageUrl = $row['landingPageUrl'];
            $landingPageTitle = $row['landingPageTitle'];
            $landingPageLinkId = $row['landingPageLinkId'];
            $landingPageFromSubmenu = $row['landingPageFromSubmenu'];
            if(strpos($landingPageUrl, "http://")===0 || strpos($landingPageUrl, "https://")===0) {
              $page = "iframeApp.php?linkUrl=" . $landingPageUrl . "&linkId=" . $landingPageLinkId . "&pageTitle=" . $landingPageTitle . "&fromSubmenu=$landingPageFromSubmenu";
            } else {
              $page = $landingPageUrl;
            }           
        }
    }
}

$ldapRole = null;
$ldapOk = false;

$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret );

$oidc->providerConfigParam(array('authorization_endpoint' => $ssoAuthorizationEndpoint));
$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
$oidc->providerConfigParam(array('userinfo_endpoint' => $ssoUserinfoEndpoint));
$oidc->providerConfigParam(array('jwks_uri' => $ssoJwksUri));
$oidc->providerConfigParam(array('issuer' => $ssoIssuer));
$oidc->providerConfigParam(array('end_session_endpoint' => $ssoEndSessionEndpoint));

$oidc->addScope(array('openid', 'username', 'profile'));
$oidc->setRedirectURL($appUrl . '/management/ssoLogin.php?redirect='.urlencode($page));

try {
  $oidc->authenticate();
} catch(Exception $ex) {
  echo $ex->getMessage()." Autentication problems.<br><a href='logout.php'>Logout</a>";
  //header("location: ".$appUrl . "/management/ssoLogin.php?");
  exit; 
}
$usernameD = $oidc->requestUserInfo('preferred_username');
$ldapUsername = "cn=" . $usernameD . "," . $ldapBaseDN;
$ds = ldap_connect($ldapServer, $ldapPort);
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
if($ldapAdminDN)
  $bind = ldap_bind($ds, $ldapAdminDN, $ldapAdminPwd);
else
  $bind = ldap_bind($ds);

if ($ds && $bind) {
  if(isset($ssoPreLdapExtension)) {
    include "ssoPreLdapExtension.php";
  }
  $organizations = checkLdapOrganizations($ds, $ldapUsername, $ldapBaseDN);
  $organization = $organizations[0];
  $groups = checkLdapGroup($ds, $ldapUsername, $ldapBaseDN, $organization);
  if (is_null($organization)) {
    $organization = "Other";
  } else if ($organization == "") {
    $organization = "Other";
  }

  if (checkLdapMembership($ds, $ldapUsername, $ldapToolName, $ldapBaseDN)) {
    if (checkLdapRole($ds, $ldapUsername, "RootAdmin", $ldapBaseDN)) {
      $ldapRole = "RootAdmin";
      $ldapOk = true;
    }
    else
    {
        if (checkLdapRole($ds, $ldapUsername, "ToolAdmin", $ldapBaseDN)) {
            $ldapRole = "ToolAdmin";
            $ldapOk = true;
          } else {
            if (checkLdapRole($ds, $ldapUsername, "AreaManager", $ldapBaseDN)) {
              $ldapRole = "AreaManager";
              $ldapOk = true;
            } else {
              if (checkLdapRole($ds, $ldapUsername, "Manager", $ldapBaseDN)) {
                $ldapRole = "Manager";
                $ldapOk = true;
              } else {
                if (checkLdapRole($ds, $ldapUsername, "Observer", $ldapBaseDN)) {
                  $ldapRole = "Observer";
                  $ldapOk = true;
                } else {
                    echo "cannot find a role for $usernameD, wait for administrator approval.<br><a href='logout.php'>Logout</a>";
                    exit;                  
                }
              }
            }
          }
    } 
  } else {
    $_SESSION['refreshToken'] = $oidc->getRefreshToken();
    if(isset($ssoExtension)) {
      include "ssoExtension.php";
    } else {
      echo "user $usernameD cannot use this tool, please wait for administrator approval.<br><a href='logout.php'>Logout</a>";
      exit;
    }
  }
} else {
    echo "cannot bind to LDAP, try later.<br><a href='logout.php'>Logout</a>";
    exit;
}
if(isset($ldapSetOrganization) && $ldapSetOrganization=='yes' && ($organization=='DISIT' || $organization=='Other')) {
    $add_org = ldap_mod_add($ds, "ou=" . $ldapDefaultOrganization . "," . $ldapBaseDN . "", array('l' => $ldapUsername));
    if ($add_org == 1) {
        $organization = $ldapDefaultOrganization;
    }
}

$stopFlag = 1;
$userLevel = checkUserLevel($usernameD, $sql_host_pd, $usrDb, $pwdDb);
if ($ldapOk) {
  ini_set('session.gc_maxlifetime', $sessionDuration);
  session_set_cookie_params($sessionDuration);
  session_destroy(); //delete old session
  session_regenerate_id();
  session_start();
  $_SESSION['sessionEndTime'] = time() + $sessionDuration;
  $_SESSION['loggedUsername'] = $usernameD;
  $_SESSION['loggedRole'] = $ldapRole;
  $_SESSION['loggedType'] = "ldap";
  $_SESSION['refreshToken'] = $oidc->getRefreshToken();
  $_SESSION['accessToken'] = $oidc->getAccessToken();
  $_SESSION['idToken'] = $oidc->getIdToken();
  $_SESSION['loggedOrganization'] = $organization;
  $_SESSION['loggedOrganizations'] = $organizations;
  
  $_SESSION['loggedUserGroups'] = $groups;
  $_SESSION['loggedUserLevel'] = $userLevel;
  $_SESSION['isPublic'] = false;
  if ($_COOKIE['layout'] == 'new_layout') {
      $_SESSION['newLayout'] = true;
  }
  
  setcookie("organization", $organization, time() + (86400), "/", $cookieDomain); // 86400 = 1 day
  
  $link = mysqli_connect($host, $username, $password);
  mysqli_select_db($link, $dbname);
  $orgParamsQuery = "SELECT * FROM Dashboard.Organizations WHERE organizationName = '$organization'";
  $r = mysqli_query($link, $orgParamsQuery);

  if($r) {
        if($row = mysqli_fetch_assoc($r)) {
            $orgId = $row['id'];
            $orgName = $row['organizationName'];
            $orgKbUrl = $row['kbUrl'];
            $orgGpsCentreLatLng = $row['gpsCentreLatLng'];
            $orgZoomLevel = $row['zoomLevel'];
            $orgLang = $row['lang'];
            $orgBroker = $row['broker'];
            $orionIP = $row['orionIP'];
        //    $_SESSION['orgId'] = $orgId;
            $_SESSION['orgKbUrl'] = $orgKbUrl;
            $_SESSION['orgGpsCentreLatLng'] = $orgGpsCentreLatLng;
            $_SESSION['orgZoomLevel'] = $orgZoomLevel;
            $_SESSION['orgLang'] = $orgLang;
            $_SESSION['orgBroker'] = $orgBroker;
            $_SESSION['orionIP'] = $orionIP;
        } else {
            
        }
  } else {
        
  }
  
  //header("Location: dashboards.php?fromSubmenu=false&linkId=dashboardsLink");
  header("Location: $page");
} else {
  $refreshToken = $_SESSION['refreshToken'];
  $_SESSION = array();

  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
  }

  session_destroy();

  $tkn = $oidc->refreshToken($refreshToken);
  $oidc->signOut($tkn->id_token, $appUrl . "/management/ssoLogin.php");
}
