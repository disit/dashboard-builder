<?php
/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

    include '../config.php';
    require '../sso/autoload.php';
    use Jumbojett\OpenIDConnectClient;
   
    //Altrimenti restituisce in output le warning
    error_reporting(E_ERROR | E_NOTICE);
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    session_start(); 
    
    //Definizioni di funzione
    function isToolAdmin($localLink, $checkedUsr, $checkedPwd, $ldapServer, $ldapPort, $localLdapFlag, $ldapBaseDn)
    {
        $toolAdmins = [];
        $ldapUsername = "cn=". $checkedUsr . "," . $ldapBaseDn;
        if($localLdapFlag == "yes")
        {
            $ds1 = ldap_connect($ldapServer, $ldapPort);
            ldap_set_option($ds1, LDAP_OPT_PROTOCOL_VERSION, 3);
            $bind = ldap_bind($ds1, $ldapUsername, $checkedPwd);

            if($bind)
            {
                $toolAdminsResult = ldap_search(
                    $ds1, $ldapBaseDn, 
                    '(cn=Dashboard)'
                 );

                 $toolAdminsEntries = ldap_get_entries($ds1, $toolAdminsResult);

                 foreach($toolAdminsEntries as $key => $value) 
                 {
                    for($index = 0; $index < (count($value["memberuid"]) - 1); $index++)
                    { 
                       $ldapusr = $value["memberuid"][$index];
                       if(checkLdapRole($ds1, $ldapusr, "RootAdmin", $ldapBaseDn))
                       {
                          $usr = str_replace("cn=", "", $ldapusr);
                          $usr = str_replace(",$ldapBaseDn", "", $usr);

                          array_push($toolAdmins, $usr); 
                       }
                    }
                 }

                 //ldap_unbind($ds2);
                 ldap_unbind($ds1);
            }
        }
        
        $permissionQuery4 = "SELECT username FROM Dashboard.Users WHERE admin = 'RootAdmin'";
        $permissionResult4 = mysqli_query($localLink, $permissionQuery4);
        if($permissionResult4)
        {
           if(mysqli_num_rows($permissionResult4) > 0) 
           {
              while($row = $permissionResult4->fetch_assoc()) 
              {
                 array_push($toolAdmins, $row["username"]);
              }
           }
        }
        
        if(in_array($checkedUsr, $toolAdmins))
        {
           return true;
        }
        else
        {
           return false;
        }
    }

    function getDashboardParams($link) 
    {
        if(isset($_GET['dashboardId']) && !empty($_GET['dashboardId']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
            if (checkVarType($dashboardId, "integer") === false) {
                eventLog("Returned the following ERROR in getDashboardData.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
                exit();
            }
        }
        else 
        {
           return false;
        }
        
        $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId'";
        $result = mysqli_query($link, $query);
        
        return mysqli_fetch_array($result);
    }
    
    function getDashboardWidgets($link)
    {
        if(isset($_GET['dashboardId']) && !empty($_GET['dashboardId']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_GET['dashboardId']);
            if (checkVarType($dashboardId, "integer") === false) {
                eventLog("Returned the following ERROR in getDashboardData.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
                exit();
            }
        }
        else 
        {
           return false;
        }
        
        $query = "SELECT * FROM Config_widget_dashboard AS dashboardWidgets " .
                 "LEFT JOIN Widgets AS widgetTypes ON dashboardWidgets.type_w = widgetTypes.id_type_widget " .
                 "LEFT JOIN Descriptions AS metrics ON dashboardWidgets.id_metric = metrics.IdMetric " .   
                 "LEFT JOIN NodeRedMetrics AS nrMetrics ON dashboardWidgets.id_metric = nrMetrics.name " . 
                 "LEFT JOIN NodeRedInputs AS nrInputs ON dashboardWidgets.id_metric = nrInputs.name " . 
                 "WHERE dashboardWidgets.id_dashboard = '$dashboardId' " .
                 "AND dashboardWidgets.canceller IS NULL " .
                 "AND dashboardWidgets.cancelDate IS NULL " . 
                 "ORDER BY dashboardWidgets.n_row, dashboardWidgets.n_column ASC";
        
        $result = mysqli_query($link, $query);
        $dashboardWidgets = array();

        if(mysqli_num_rows($result) > 0) 
        {
            while($row = mysqli_fetch_assoc($result)) 
            {
                array_push($dashboardWidgets, $row);
            }
        }
        mysqli_close($link);
        return $dashboardWidgets;
    }
    
    //Corpo dell'API        
    if(!$link->set_charset("utf8")) 
    {
       echo '<script type="text/javascript">';
       echo 'alert("KO");';
       echo '</script>';
       printf("Error loading character set utf8: %s\n", $link->error);
       exit();
    }
    
    $dashboardId = escapeForSQL($_REQUEST['dashboardId'], $link);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in getDashboardData.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    }
    $query = "SELECT Dashboard.Config_dashboard.visibility AS visibility, Dashboard.Users.admin AS role, Dashboard.Config_dashboard.user AS username FROM Dashboard.Config_dashboard " .
             "LEFT JOIN Dashboard.Users " .
             "ON Dashboard.Config_dashboard.user = Dashboard.Users.username " .   
             "WHERE Dashboard.Config_dashboard.Id = '$dashboardId'";

    $result = mysqli_query($link, $query);
    $response = [];
    $ds = null;
    $authorOrigin = null;

    if($result)
    {
        $row = mysqli_fetch_array($result);
        $visibility = $row["visibility"];
        $authorUsername = $row["username"];
        $authorLdapUsername = "cn=". $row["username"] . "," . $ldapBaseDN;
        $response["visibility"] = $visibility;

        if(($row["role"] == NULL) || ($row["role"] == "NULL"))
        {
            //Autore LDAP
            if($ldapActive == "yes")
            {
                $ds = ldap_connect($ldapServer, $ldapPort);
                ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                //Lasciamo questo bind anonimo, è solo un controllo sul ruolo dell'autore, a questo punto di codice non abbiamo credenziali utente
                $bind = ldap_bind($ds);

                if(checkLdapRole($ds, $authorLdapUsername, "Manager", $ldapBaseDN))
                {
                   $authorRole = "Manager";
                }
                else
                {
                  if(checkLdapRole($ds, $authorLdapUsername, "AreaManager", $ldapBaseDN))
                  {
                      $authorRole = "AreaManager";
                  }
                  else
                  {
                     if(checkLdapRole($ds, $authorLdapUsername, "ToolAdmin", $ldapBaseDN))
                     {
                        $authorRole = "ToolAdmin";
                     }
                     else
                     {
                        if(checkLdapRole($ds, $authorLdapUsername, "RootAdmin", $ldapBaseDN))
                        {
                           $authorRole = "RootAdmin";
                        }
                        else
                        {
                            //Caso in cui l'utente proviene da NodeRED e non è né censito su LDAP né su DB locale: come patch lo marchiamo come "Manager"
                            $authorRole = "Manager";
                        }
                     }
                  }
                }

                ldap_unbind($ds);
                $authorOrigin = 'ldap';
            }
        }
        else
        {
           $authorRole = $row["role"];
        //   $authorOrigin = 'local';
           $authorOrigin = '';
        }

        switch($visibility)
        {
            //Ok
            case "public":
                $response["dashboardParams"] = getDashboardParams($link);
                $response["dashboardWidgets"] = getDashboardWidgets($link);
                break;

            //VISIBILITA' RISTRETTA 
            default:
               //OK - UTENTI LOCALI LEGACY
               if($authorOrigin == 'local')
               {
                   //Utente collegato all'applicazione
                   if((isset($_SESSION['loggedUsername']))&&($_REQUEST['loggedUserFirstAttempt'] == "true"))
                   {
                        $applicantUsername = $_SESSION['loggedUsername'];
                        //Controlliamo se è l'autore
                        $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId' AND user = '$applicantUsername'";
                        $permissionResult1 = mysqli_query($link, $permissionQuery1);
                        if($permissionResult1)
                        {
                            $row = mysqli_fetch_array($permissionResult1);
                            if($row["isAuthor"] > 0)
                            {
                                $response["detail"] = "Ok";
                                $response["context"] = "View";
                                $response["dashboardParams"] = getDashboardParams($link);
                                $response["dashboardWidgets"] = getDashboardWidgets($link);
                            }
                            else
                            {
                                if($_SESSION['loggedRole'] == 'RootAdmin')
                                {
                                    $response["detail"] = "Ok";
                                    $response["context"] = "View";
                                    $response["dashboardParams"] = getDashboardParams($link);
                                    $response["dashboardWidgets"] = getDashboardWidgets($link);
                                }
                                else
                                {
                                    $response["detail"] = "Ko";
                                }
                            }
                        }
                   }
                   //Utente NON collegato all'applicazione
                   else
                   {
                       $proceed = false;
                        //Caso credenziali non fornite
                       if(($_REQUEST['username'] == "") || ($_REQUEST['password'] == ""))
                       {
                           $response["detail"] = "credentialsMissing";
                           $proceed = false; 
                       }
                       else//Credenziali fornite
                       {
                           //Controllo presenza credenziali fornite su elenco utenti locale
                           $applicantUsername = escapeForSQL($_REQUEST['username'], $link);
                           $applicantPassword = escapeForSQL($_REQUEST['password'], $link);
                           $applicantPasswordMd5 = md5($applicantPassword);

                           $query = "SELECT count(*) AS isRegistered FROM Dashboard.Users WHERE username = '$applicantUsername' AND password = '$applicantPasswordMd5'";
                           $result = mysqli_query($link, $query);
                           if($result)
                           {
                                $row = mysqli_fetch_array($result);

                                if($row["isRegistered"] <= 0)
                                {
                                  $response["detail"] = "userNotRegistered";
                                  $proceed = false;
                                }
                                else
                                {
                                    $proceed = true;
                                }
                           }
                           else 
                           {
                              $response["detail"] = "checkUserQueryKo";
                              $proceed = false;
                           }
                       }
                       
                       if($proceed)
                       {
                            //Controlliamo se è l'autore
                            $permissionQuery1 = "SELECT count(*) AS isAuthor FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId' AND user = '$applicantUsername'";
                            $permissionResult1 = mysqli_query($link, $permissionQuery1);
                            if($permissionResult1)
                            {
                                $row = mysqli_fetch_array($permissionResult1);
                                if($row["isAuthor"] > 0)
                                {
                                    $response["detail"] = "Ok";
                                    $response["context"] = "View";
                                    $response["dashboardParams"] = getDashboardParams($link);
                                    $response["dashboardWidgets"] = getDashboardWidgets($link);
                                }
                                else
                                {
                                    $response["detail"] = "Ko";
                                }
                            }
                       }
                   }
               }
               else//CASO LDAP-SSO
               {
                   $proceed = false;
                   //OK - Utente collegato all'applicazione
                   if((isset($_SESSION['loggedUsername']))&&($_REQUEST['loggedUserFirstAttempt'] == "true"))
                   {
                       //Controlliamo se è autore
                       if($_SESSION['loggedUsername'] == $authorUsername)
                       {
                           $proceed = true;
                       }
                       else
                       {
                           //Se non è autore, o è RootAdmin o ha la delega
                           if($_SESSION['loggedRole'] == 'RootAdmin')
                           {
                               $proceed = true;
                           }
                           else
                           {
                                if(isset($_SESSION['refreshToken'])) 
                                {
                                    //1) Reperimento elenco sue dashboard tramite chiamata ad api di ownership 
                                    $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                                    $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

                                    $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

                                    $accessToken = $tkn->access_token;
                                    $_SESSION['refreshToken'] = $tkn->refresh_token;
                                    //Se non è autore, controlliamo se ha delega
                                //    $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . $authorUsername . "/delegator?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";
                                    $apiUrl = $personalDataApiBaseUrl . "/v2/username/" . rawurlencode($_SESSION['loggedUsername']) . "/delegated?accessToken=" . $accessToken. "&sourceRequest=dashboardmanager";

                                    $options = array(
                                        'http' => array(
                                                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                                'method'  => 'GET',
                                                'timeout' => 30,
                                                'ignore_errors' => true
                                        )
                                    );

                                    $context  = stream_context_create($options);
                                    $delegatedDashboardsJson = file_get_contents($apiUrl, false, $context);

                                    $delegatedDashboards = json_decode($delegatedDashboardsJson);

                                    $hasDelegation = false;
                                    for($i = 0; $i < count($delegatedDashboards); $i++) 
                                    {
                                        if($delegatedDashboards[$i]->elementId == $dashboardId)
                                        {
                                            $hasDelegation = true;    // MOD GP
                                          /*  if($delegatedDashboards[$i]->usernameDelegated == $_SESSION['loggedUsername'])
                                            {
                                                $hasDelegation = true;
                                            }   */
                                        }
                                    }

                                    if($hasDelegation)
                                    {
                                        $proceed = true;
                                    }
                                }
                                else
                                {
                                    $proceed = false;
                                }
                           }
                       }
                       
                       
                   }
                   //Utente NON collegato all'applicazione
                   else
                   {
                       //Questo else è spostato in testa al file index.php
                       //header("Location: ../management/ssoLogin.php?redirect=https://main.snap4city.org/view/index.php?iddasboard=NDc4");
                       
                   }//Fine else utente NON collegato all'applicazione
                   
                   if($proceed)
                   {
                       $response["detail"] = "Ok";
                       $response["context"] = "View";
                       $response["dashboardParams"] = getDashboardParams($link);
                       $response["dashboardWidgets"] = getDashboardWidgets($link);
                   }
                   else
                   {
                       $response["detail"] = "Ko";
                   }
               }
        } 
    }
    else
    {
        $response["detail"] = "checkVisibilityQueryKo";
    }

    echo json_encode($response);
    mysqli_close($link);
    
   
    

