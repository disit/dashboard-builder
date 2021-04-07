<?php
/* Dashboard Builder.
   Copyright (C) 2021 DISIT Lab https://www.disit.org - University of Florence

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

include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

//Altrimenti restituisce in output le warning
error_reporting(E_ERROR | E_NOTICE);
mysqli_select_db($link, $dbname);
session_start();

$proceed = false;
//OK - Utente collegato all'applicazione
if(isset($_SESSION['loggedUsername']))
{
    //Controlliamo se è autore
    if($_SESSION['loggedUsername'] == $authorUsername)
    {
        $proceed = true;
    }
    else {

        if (isset($_SESSION['refreshToken'])) {
            //1) Reperimento elenco sue dashboard tramite chiamata ad api di ownership
            $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
            $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

            $accessToken = $tkn->access_token;
            $_SESSION['refreshToken'] = $tkn->refresh_token;
        }
    }

}

$response["detail"] = "Ok";
$response["accessToken"] = $accessToken;

echo json_encode($response);
