<?php

/* Dashboard Builder.
  Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
   
   include('../config.php');
session_start();
include 'process-form.php';

require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

$oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
$oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
$oidc->providerConfigParam(array('authorization_endpoint' => $ssoAuthorizationEndpoint));
$oidc->providerConfigParam(array('userinfo_endpoint' => $ssoUserinfoEndpoint));
$oidc->providerConfigParam(array('jwks_uri' => $ssoJwksUri));
$oidc->providerConfigParam(array('issuer' => $ssoIssuer));
$oidc->providerConfigParam(array('end_session_endpoint' => $ssoEndSessionEndpoint));

if (isset($_SESSION['loggedRole']) || isset($_SESSION['refreshToken'])) {
  
  if(isset($_SESSION['refreshToken'])) 
  {
    $refreshToken = $_SESSION['refreshToken'];
  }
  $newLocation = "index.php";

  if (!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) {
      setcookie("layout", "new_layout", time() + (86400), "/");
  }

  $_SESSION = array();

  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
  }

  session_destroy();

  if (isset($refreshToken)) {
    $tkn = $oidc->refreshToken($refreshToken);
    //Dev'essere assoluto, visto con Piero
    $oidc->signOut($tkn->access_token, $appUrl . "/management/" . $newLocation);
  }

  header("Location: " . $newLocation);
} else {
  $newLocation = "index.php?sessionExpired=true";
  header("Location: " . $newLocation);
}
