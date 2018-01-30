<?php
    /* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
    require '../phpmailer/PHPMailerAutoload.php';
    
    //Definizioni di funzione
    function notificatorLogin($username, $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $notificatorToolName)
    {
        if($notificatorUrl != "")
        {
            $usr = md5($username);
            $clientApplication = md5($notificatorToolName);

            $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=remoteLogin&usr=' . $usr . '&clientApplication=' . $clientApplication;
            $notificatorUrl = $notificatorUrl.$data;

            $options = array(
                  'http' => array(
                          'header'  => "Content-type: application/json\r\n",
                          'method'  => 'POST',
                          'timeout' => 30
                  )
            );

            try
            {
                   $context  = stream_context_create($options);
                   $callResult = @file_get_contents($notificatorUrl, false, $context);
            }
            catch (Exception $ex) 
            {
                   //Non facciamo niente di specifico in caso di mancata risposta dell'host
            }
        }
    }
    
    function notificatorLogout($username, $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $notificatorToolName)
    {
        /*$logoutAuthObj = ['username' => $username, 'clientApplication' => $ldapTool];
        $logoutAuthJson = json_encode($logoutAuthObj);
        $logoutAuthJsonMd5 = md5($logoutAuthJson);*/
        $usr = md5($username);
        $clientApplication = md5($notificatorToolName);
        
        $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=remoteLogout&usr=' . $usr . '&clientApplication=' . $clientApplication;
        $notificatorUrl = $notificatorUrl.$data;
      
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/json\r\n",
              'method'  => 'POST',
              'timeout' => 30
          )
      );

      try
      {
         $context  = stream_context_create($options);
         $callResult = @file_get_contents($notificatorUrl, false, $context);
         
         //$file = fopen("C:\dashboardLog.txt", "w");
         //fwrite($file, "Call result: " . $callResult . "\n");
      }
      catch (Exception $ex) 
      {
         //Non facciamo niente di specifico in caso di mancata risposta dell'host
      }
    }
    
    function returnManagedStringForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return "'" . $original . "'";
        }
    }
    
    function returnManagedNumberForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return $original;
        }
    }
    
    function checkLdapMembership($connection, $userDn, $tool) 
    {
         $result = ldap_search($connection, 'dc=ldap,dc=disit,dc=org', '(&(objectClass=posixGroup)(memberUid=' . $userDn . '))');
         $entries = ldap_get_entries($connection, $result);
         foreach ($entries as $key => $value) 
         {
            if(is_numeric($key)) 
            {
               if($value["cn"]["0"] == $tool) 
               {
                  return true;
               }
            }
         }
         return false;
     }
   

   function checkLdapRole($connection, $userDn, $role) 
   {
      $result = ldap_search($connection, 'dc=ldap,dc=disit,dc=org', '(&(objectClass=organizationalRole)(cn=' . $role . ')(roleOccupant=' . $userDn . '))');
      $entries = ldap_get_entries($connection, $result);
      foreach ($entries as $key => $value) 
      {
         if(is_numeric($key)) 
         {
            if($value["cn"]["0"] == $role) 
            {
               return true;
            }
         }
      }
      return false;
  }
    
    
    function canEditDashboard()
    {
        $result = false;
        if(isset($_SESSION['loggedRole']))
        {
            if($_SESSION['loggedRole'] == "Manager")
            {
                //Utente non amministratore, edita una dashboard solo se ne é l'autore
                if((isset($_SESSION['loggedUsername']))&&(isset($_SESSION['dashboardId']))&&(isset($_SESSION['dashboardAuthorName']))&&($_SESSION['loggedUsername'] == $_SESSION['dashboardAuthorName']))
                {
                    $result = true;
                }
            }
            else if(($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "ToolAdmin"))
            {
                //Utente amministratore, edita qualsiasi dashboard
                if((isset($_SESSION['loggedUsername']))&&(isset($_SESSION['dashboardId']))&&(isset($_SESSION['dashboardAuthorName'])))
                {
                    $result = true;
                }
            }
        }
        return $result;
    }

    //Fine definizioni di funzione
    
    //Corpo dell'API
    $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
    mysqli_select_db($link, $dbname);
    error_reporting(E_ERROR | E_NOTICE);
    
    if(!$link->set_charset("utf8")) 
    {
        echo '<script type="text/javascript">';
        echo 'alert("Error loading character set utf8: %s\n");';
        echo '</script>';
        exit();
    }

    if(isset($_REQUEST['register_confirm']))
    {
        session_start();
        if(isset($_SESSION['loggedRole']))
        {
            $username = mysqli_real_escape_string($link, $_POST['inputUsername']);
            $password = mysqli_real_escape_string($link, $_POST['inputPassword']); 
            $firstname = mysqli_real_escape_string($link, $_POST['inputNameUser']); 
            $lastname = mysqli_real_escape_string($link, $_POST['inputSurnameUser']);
            $email = mysqli_real_escape_string($link, $_POST['inputEmail']);

            //24/03/2017 - Cambierà via via che implementiamo la nuova profilazione utente
            if(isset($_POST['adminCheck'])) 
            {
                $valueAdmin = 1;
            } 
            else 
            {
                $valueAdmin = 0;
            }

            $selqDbtbCheck = "SELECT * FROM `Dashboard`.`Users` WHERE username='$username'";
            $resultCheck = mysqli_query($link, $selqDbtbCheck) or die(mysqli_error($link));

            if(mysqli_num_rows($resultCheck) > 0) 
            { 
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Username già in uso da altro utente: Ripetere registrazione");';
                echo 'window.location.href = "dashboard_register.php";';
                echo '</script>';
            } 
            else 
            {
                $insqDbtb = "INSERT INTO Dashboard.Users(IdUser, username, password, name, surname, email, reg_data, status, ret_code, admin) VALUES (NULL, '$username', '$password', '$firstname', '$lastname', '$email', now(), 1, 1, '$valueAdmin')";
                $result = mysqli_query($link, $insqDbtb) or die(mysqli_error($link));

                if($result) 
                {
                    mysqli_close($link);
                    echo '<script type="text/javascript">';
                    echo 'alert("Registrazione avvenuta con successo");';
                    echo 'window.location.href = "dashboard_mng.php";';
                    echo '</script>';
                } 
                else
                {
                    mysqli_close($link);
                    echo '<script type="text/javascript">';
                    echo 'alert("Error: Ripetere registrazione");';
                    echo 'window.location.href = "dashboard_register.php";';
                    echo '</script>';
                }
            }
        }
    }
    else if(isset($_REQUEST['login']))
    {
        $username = mysqli_real_escape_string($link, $_POST['loginUsername']);
        $ldapUsername = "cn=". $_POST['loginUsername'] . ",dc=ldap,dc=disit,dc=org";
        $password = mysqli_real_escape_string($link, $_POST['loginPassword']);
        $ldapPassword = $_POST['loginPassword'];
        $ldapOk = false;
        $file = fopen("C:\dashboardLog.txt", "w");
        fwrite($file, "LDAP server: " . $ldapServer . "\n");
        fwrite($file, "LDAP port: " . $ldapPort . "\n");
        //Per prima cosa verifichiamo se è su LDAP, altrimenti su account list locale
        $ds = ldap_connect($ldapServer, $ldapPort);
        fwrite($file, "LDAP connect: " . $ds . "\n");
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $bind = ldap_bind($ds, $ldapUsername, $ldapPassword);
        
        if($ldapActive == "yes")
        {
            if($ds && $bind)
            {
                if(checkLdapMembership($ds, $ldapUsername, $ldapTool))
                {
                   if(checkLdapRole($ds, $ldapUsername, "ToolAdmin"))
                   {
                      $ldapRole = "ToolAdmin";
                      $ldapOk = true;
                      ini_set('session.gc_maxlifetime', $sessionDuration);
                      session_set_cookie_params($sessionDuration);
                      session_start();
                      session_regenerate_id();
                      $_SESSION['loggedUsername'] = $username;
                      $_SESSION['loggedRole'] = "ToolAdmin";
                      $_SESSION['loggedType'] = "ldap";
                      notificatorLogin($username, $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $ldapTool);
                      mysqli_close($link);
                      header("location: dashboards.php");
                   }
                   else
                   {
                       if(checkLdapRole($ds, $ldapUsername, "AreaManager"))
                       {
                          $ldapRole = "AreaManager";
                          $ldapOk = true;
                          ini_set('session.gc_maxlifetime', $sessionDuration);
                          session_set_cookie_params($sessionDuration);
                          session_start();
                          session_regenerate_id();
                          $_SESSION['loggedUsername'] = $username;
                          $_SESSION['loggedRole'] = "AreaManager";
                          $_SESSION['loggedType'] = "ldap";
                          notificatorLogin($username, $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $ldapTool);
                          mysqli_close($link);
                          header("location: dashboards.php");
                       }
                       else
                       {
                          if(checkLdapRole($ds, $ldapUsername, "Manager"))
                          {
                             $ldapRole = "Manager";
                             $ldapOk = true;
                             ini_set('session.gc_maxlifetime', $sessionDuration);
                             session_set_cookie_params($sessionDuration);
                             session_start();
                             session_regenerate_id();
                             $_SESSION['loggedUsername'] = $username;
                             $_SESSION['loggedRole'] = "Manager";
                             $_SESSION['loggedType'] = "ldap";
                             notificatorLogin($username, $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $ldapTool);
                             mysqli_close($link);
                             header("location: dashboards.php");
                          }
                       }
                   }
                }
            }
            else
            {
                
                fwrite($file, "LDAP fail\n");
            }
        }
        else
        {
            
            fwrite($file, "LDAP not active\n");
        }
        
        //Verifica su lista account locali se LDAP fallisce
        if(!$ldapOk)
        {
            $md5Pwd = md5($password);
            $query = "SELECT * FROM Dashboard.Users WHERE username = '$username' AND password = '$md5Pwd' AND status = 1 AND admin <> 'Observer'";
            $result = mysqli_query($link, $query);

            if($result == false) 
            {
                die(mysqli_error($link));
            }

            if(mysqli_num_rows($result) > 0) 
            {
               $row = $result->fetch_assoc();
               ini_set('session.gc_maxlifetime', $sessionDuration);
               session_set_cookie_params($sessionDuration);
               session_start();
               session_regenerate_id();
               $_SESSION['sessionEndTime'] = time() + $sessionDuration;
               $_SESSION['loggedUsername'] = $username;
               $_SESSION['loggedRole'] = $row["admin"];
               $_SESSION['loggedType'] = "local";
               notificatorLogin($username, $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $ldapTool);
               mysqli_close($link);
               header("location: dashboards.php");
            } 
            else 
            {
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Username e/o password errata/i: ripetere login");';
                echo 'window.location.href = "index.php";';
                echo '</script>';
            }
        }
    } 
    else if(isset($_REQUEST['addDashboard']))//Escape 
    {
        session_start();
        //$name_dashboard = mysqli_real_escape_string($link, $_POST['inputNameDashboard']);
        $name_dashboard = mysqli_real_escape_string($link, $_POST['inputTitleDashboard']); 
        $title = mysqli_real_escape_string($link, $_POST['inputTitleDashboard']);  
        $subtitle = mysqli_real_escape_string($link, $_POST['inputSubTitleDashboard']);  
        $color = mysqli_real_escape_string($link, $_POST['inputColorDashboard']);  
        $background = mysqli_real_escape_string($link, $_POST['inputColorBackgroundDashboard']);  
        $externalColor = mysqli_real_escape_string($link, $_POST['inputExternalColorDashboard']);  
        $nCols = mysqli_real_escape_string($link, $_POST['inputWidthDashboard']);  
        $headerFontColor = mysqli_real_escape_string($link, $_POST['headerFontColor']);  
        $headerFontSize = mysqli_real_escape_string($link, $_POST['headerFontSize']);  
        $logoLink = null;
        $filename = null;
        if(isset($_POST['widgetsBorders']))
        {
            $widgetsBorders = "yes";
        }
        else
        {
            $widgetsBorders = "no";
        }
        
        $widgetsBordersColor = mysqli_real_escape_string($link, $_POST['inputWidgetsBordersColor']);
        $visibility = mysqli_real_escape_string($link, $_POST['inputDashboardVisibility']);
        
        if(isset($_POST['headerVisible']))
        {
            $headerVisible = 1;
        }
        else
        {
            $headerVisible = 0;
        }
        
        if(isset($_POST['authorizedPagesJson']))
        {
            if(($_POST['authorizedPagesJson'] != "[]")&&($_POST['authorizedPagesJson'] != ""))
            {
               $embeddable = "yes";
               $authorizedPagesJson = mysqli_real_escape_string($link, $_POST['authorizedPagesJson']);
            }
            else
            {
                $embeddable = "no";
                $authorizedPagesJson = "[]";
            }
        }
        else
        {
            $embeddable = "no";
            $authorizedPagesJson = "[]";
        }
        
        $selectedVisibilityUsers = [];
        $dashboardAuthorName = $_SESSION['loggedUsername'];
        $queryFail = false;
        if($_FILES['dashboardLogoInput']['size'] > 0)
        {
            $addLogo = true;
            $selqDbtbCheck2 = "SELECT * FROM Dashboard.Config_dashboard WHERE WHERE title_header = '$title' AND user = '$dashboardAuthorName'";
            $resultCheck2 = mysqli_query($link, $selqDbtbCheck2);

            if(mysqli_num_rows($resultCheck2) > 0) 
            { 
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Chosen dashboard title is already in use: please choose another one.");';
                echo 'window.location.href = "dashboards.php";';
                echo '</script>';
            }
            else 
            {
                //New version: lasciamo gli addendi espliciti per agevolare la lettura
                $width = ($nCols * 78) + 10;

                if($_POST['dashboardLogoLinkInput'] != '')
                {
                    $logoLink = $_POST['dashboardLogoLinkInput'];

                    $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                    (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                    `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`,`color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `logoFilename`, `logoLink`, `widgetsBorders`, `widgetsBordersColor`, visibility, headerVisible, embeddable, authorizedPagesJson) 
                    VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$filename', '$logoLink', '$widgetsBorders', '$widgetsBordersColor', '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson')";
                }
                else
                {
                    $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                    (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                    `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`,`color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `logoFilename`, `widgetsBorders`, `widgetsBordersColor`, visibility, headerVisible, embeddable, authorizedPagesJson) 
                    VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$filename', '$widgetsBorders', '$widgetsBordersColor', '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson')";
                }

                mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
                $result3 = mysqli_query($link, $insqDbtb2);

                if($result3) 
                {
                    $_SESSION['dashboardAuthorName'] = $_SESSION['loggedUsername'];
                    $_SESSION['dashboardId'] = mysqli_insert_id($link);
                    $newDashId = $_SESSION['dashboardId'];

                    if($visibility == "restrict")
                    {
                        if(isset($_POST['selectedVisibilityUsers']))
                        {
                            foreach($_POST['selectedVisibilityUsers'] as $selectedUser)
                            {
                                $insertQuery = "INSERT INTO Dashboard.DashboardsViewPermissions VALUES($newDashId, '$selectedUser')";
                                $result4 = mysqli_query($link, $insertQuery) or die(mysqli_error($link));
                                if(!$result4)
                                {
                                    $rollbackResult = mysqli_rollback($link);
                                    mysqli_close($link);
                                    $queryFail = true;
                                    echo '<script type="text/javascript">';
                                    echo 'alert("Error during dashboard creation: please repeat the procedure.");';
                                    echo 'window.location.href = "dashboards.php";';
                                    echo '</script>';
                                    die();
                                }
                            }
                        }
                    }
                } 
                else 
                {
                    $rollbackResult = mysqli_rollback($link);
                    mysqli_close($link);
                    $queryFail = true;
                    echo '<script type="text/javascript">';
                    echo 'alert("Error during dashboard creation: please repeat the procedure.");';
                    echo 'window.location.href = "dashboards.php";';
                    echo '</script>';
                    die();
                }
            }
        }
        else
        {
            //Nessun file caricato
            $selqDbtbCheck2 = "SELECT * FROM Dashboard.Config_dashboard WHERE title_header = '$title' AND user = '$dashboardAuthorName'";
            $resultCheck2 = mysqli_query($link, $selqDbtbCheck2) or die(mysqli_error($link));

            if (mysqli_num_rows($resultCheck2) > 0) 
            { 
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Il nome dato alla nuova dashboard è già in uso: ripetere creazione dashboard");';
                echo 'window.location.href = "dashboards.php";';
                echo '</script>';
            }
            else 
            {
                //New version: lasciamo gli addendi espliciti per agevolare la lettura
                $width = ($nCols * 78) + 10;

                mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
                
                $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`, `color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `widgetsBorders`, `widgetsBordersColor`, visibility, headerVisible, embeddable, authorizedPagesJson) 
                VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$widgetsBorders', '$widgetsBordersColor', '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson')";
                
                $result3 = mysqli_query($link, $insqDbtb2) or die(mysqli_error($link));
                
                if($result3) 
                {
                     $_SESSION['dashboardAuthorName'] = $_SESSION['loggedUsername'];
                     $_SESSION['dashboardId'] = mysqli_insert_id($link);
                     $newDashId = $_SESSION['dashboardId'];

                     if($visibility == "restrict")
                     {
                         if(isset($_POST['selectedVisibilityUsers']))
                         {
                             foreach($_POST['selectedVisibilityUsers'] as $selectedUser)
                             {
                                 $insertQuery = "INSERT INTO Dashboard.DashboardsViewPermissions VALUES($newDashId, '$selectedUser')";
                                 $result4 = mysqli_query($link, $insertQuery);
                                 if(!$result4)
                                 {
                                     $rollbackResult = mysqli_rollback($link);
                                     mysqli_close($link);
                                     $queryFail = true;
                                     echo '<script type="text/javascript">';
                                     echo 'alert("Error during dashboard creation: please repeat the procedure.");';
                                     echo 'window.location.href = "dashboards.php";';
                                     echo '</script>';
                                     die();
                                 }
                             }
                         }
                     }
                } 
                else 
                {
                    mysqli_close($link);
                    echo '<script type="text/javascript">';
                    echo 'alert("Error: Ripetere creazione dashboard");';
                    echo 'window.location.href = "dashboards.php";';
                    echo '</script>';
                }
            }
        }
        
        if(!$queryFail)
        {
            $_SESSION['dashboardAuthorName'] = $_SESSION['loggedUsername'];
            $_SESSION['dashboardId'] = $newDashId;
            $_SESSION['dashboardAuthorRole'] = $_SESSION['loggedRole'];
            $commit = mysqli_commit($link);
            mysqli_close($link);
            if($addLogo)
            {
                //Logo della dashboard
                $uploadFolder = "../img/dashLogos/dashboard".$newDashId."/";
                $oldMask = umask(0);
                mkdir("../img/dashLogos/", 0777);
                mkdir($uploadFolder, 0777);
                umask($oldMask);

                if(!is_dir($uploadFolder))  
                {  
                    echo '<script type="text/javascript">';
                    echo 'alert("Directory dashLogos/"' . $newDashId . '"/ could not be created");';
                    //echo 'window.location.href = "dashboards.php";';
                    echo '</script>';  
                }   
                else   
                {  
                    if(!is_writable($uploadFolder))
                    {
                        echo '<script type="text/javascript">';
                        echo 'alert("Directory dashLogos is not writable");';
                        //echo 'window.location.href = "dashboards.php";';
                        echo '</script>';
                    }
                    else
                    {
                        $filename = $_FILES['dashboardLogoInput']['name'];

                        if(!move_uploaded_file($_FILES['dashboardLogoInput']['tmp_name'], $uploadFolder.$filename))  
                        {  
                            echo '<script type="text/javascript">';
                            echo 'alert("Error during logo upload.");';
                            //echo 'window.location.href = "dashboards.php";';
                            echo '</script>'; 
                        }  
                        else  
                        {  
                            chmod($uploadFolder.$filename, 0666);
                        }
                    }
                } 
            }
            header("location: dashboard_configdash.php");
        }
    } 
    else if(isset($_REQUEST['editDashboard']))
    {
        session_start();
        mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
        $queryFail = false;
        $response = array();
        $dashboardId = $_SESSION['dashboardId'];
        $dashboardName = $_SESSION['dashboardTitle']; //Si chiama title per un vecchio refuso, non cambiare
        //$dashboardIdFromRequest = mysqli_real_escape_string($link, $_REQUEST['dashboardIdFromRequest']); 
        //$newDashboardName = mysqli_real_escape_string($link, $_REQUEST['inputTitleDashboard']);
        $newDashboardTitle = mysqli_real_escape_string($link, $_REQUEST['inputTitleDashboard']); 
        $newDashboardSubtitle = mysqli_real_escape_string($link, $_REQUEST['inputSubTitleDashboard']); 
        $newDashboardColor = mysqli_real_escape_string($link, $_REQUEST['inputColorDashboard']);
        $nCols = mysqli_real_escape_string($link, $_POST['inputWidthDashboard']); 
        $newDashboardBckColor =  mysqli_real_escape_string($link, $_REQUEST['inputColorBackgroundDashboard']); 
        $newDashboardExtColor = mysqli_real_escape_string($link, $_REQUEST['inputExternalColorDashboard']); 
        $headerFontSize = mysqli_real_escape_string($link, $_REQUEST['headerFontSize']);
        if(isset($_POST['widgetsBorders']))
        {
            $widgetsBorders = "yes";
        }
        else
        {
            $widgetsBorders = "no";
        }
        $widgetsBordersColor = mysqli_real_escape_string($link, $_REQUEST['inputWidgetsBordersColor']); 
        $headerFontColor = mysqli_real_escape_string($link, $_REQUEST['headerFontColor']); 
        $visibility = mysqli_real_escape_string($link, $_POST['inputDashboardVisibility']);
        $filename = NULL;
        $logoLink = NULL;
        if(isset($_POST['headerVisible']))
        {
            $headerVisible = 1;
        }
        else
        {
            $headerVisible = 0;
        }

        if(isset($_POST['authorizedPagesJson']))
        {
            if(($_POST['authorizedPagesJson'] != "[]")&&($_POST['authorizedPagesJson'] != ""))
            {
               $embeddable = "yes";
               $authorizedPagesJson = mysqli_real_escape_string($link, $_POST['authorizedPagesJson']);
            }
            else
            {
                $embeddable = "no";
                $authorizedPagesJson = "[]";
            }
        }
        else
        {
            $embeddable = "no";
            $authorizedPagesJson = "[]";
        }

        //New version: lasciamo gli addendi espliciti per agevolare la lettura
        $width = ($nCols * 78) + 10;

        //Logo della dashboard
        $uploadFolder = "../img/dashLogos/dashboard" . $dashboardId . "/";

        if(($_REQUEST['dashboardLogoLinkInput'] != NULL) && ($_REQUEST['dashboardLogoLinkInput'] != ''))
        {
            $logoLink = mysqli_real_escape_string($link, $_REQUEST['dashboardLogoLinkInput']); 
        }

        //Nuovo file caricato, si cancella il vecchio e si aggiorna il nome del file su DB.
        if($_FILES['dashboardLogoInput']['size'] > 0)
        {
            if(!file_exists("../img/dashLogos/"))
            {
                $oldMask = umask(0);
                mkdir("../img/dashLogos/", 0777);
                umask($oldMask);
            }

            if(!file_exists($uploadFolder))
            {
                $oldMask = umask(0);
                mkdir($uploadFolder, 0777);
                umask($oldMask);
            }
            else
            {
                $oldFiles = glob($uploadFolder . '*');
                foreach($oldFiles as $fileToDel)
                { 
                    if(is_file($fileToDel))
                    {
                       unlink($fileToDel);
                    }
                }
            }

            /*$pointIndex = strrpos($_FILES['dashboardLogoInput']['name'], ".");
            $extension = substr($_FILES['dashboardLogoInput']['name'], $pointIndex);
            $filename = 'logo'.$extension;*/
            $filename = $_FILES['dashboardLogoInput']['name'];

            if(!move_uploaded_file($_FILES['dashboardLogoInput']['tmp_name'], $uploadFolder.$filename))  
            {  
                echo 'Something has gone wrong during logo upload: dashboard update has been cancelled';
                mysqli_close($link);
                exit();
            }
            else 
            {
               chmod($uploadFolder.$filename, 0666); 
               $query = "UPDATE Dashboard.Config_dashboard SET title_header = '$newDashboardTitle', subtitle_header = '$newDashboardSubtitle', color_header = '$newDashboardColor', width = $width, num_columns = $nCols, color_background = '$newDashboardBckColor', external_frame_color = '$newDashboardExtColor', headerFontColor = '$headerFontColor', headerFontSize = $headerFontSize, logoFilename = '$filename', logoLink = '$logoLink', widgetsBorders = '$widgetsBorders', widgetsBordersColor = '$widgetsBordersColor', visibility = '$visibility', headerVisible = $headerVisible, embeddable = '$embeddable', authorizedPagesJson = '$authorizedPagesJson' WHERE Id = $dashboardId";
               $result = mysqli_query($link, $query);  

                if(!$result)
                {
                    $rollbackResult = mysqli_rollback($link);
                    mysqli_close($link);
                    $queryFail = true;
                    echo '<script type="text/javascript">';
                    echo 'alert("Error during dashboard update: please repeat the procedure.");';
                    echo 'window.location.href = "dashboard_configdash.php";';
                    echo '</script>';
                    die();
                }
                else
                {
                  $response["newLogo"] = "YES";
                  $response["fileName"] = $uploadFolder . $filename;
                  $response["logoLink"] = $logoLink;
                  $response["width"] = $width;
                  $response["num_cols"] = $nCols;
                }
            }
        }//Nessun nuovo file caricato
        else
        {
            $query = "UPDATE Dashboard.Config_dashboard SET title_header = '$newDashboardTitle', subtitle_header = '$newDashboardSubtitle', color_header = '$newDashboardColor', width = $width, num_columns = $nCols, color_background = '$newDashboardBckColor', external_frame_color = '$newDashboardExtColor', headerFontColor = '$headerFontColor', headerFontSize = $headerFontSize, logoLink = '$logoLink', widgetsBorders = '$widgetsBorders', widgetsBordersColor = '$widgetsBordersColor', visibility = '$visibility', headerVisible = $headerVisible, embeddable = '$embeddable', authorizedPagesJson = '$authorizedPagesJson' WHERE Id = $dashboardId";
            $result = mysqli_query($link, $query);

            if(!$result)
            {
               $rollbackResult = mysqli_rollback($link);
               mysqli_close($link);
               $queryFail = true;
               echo '<script type="text/javascript">';
               echo 'alert("Error during dashboard update: please repeat the procedure.");';
               echo 'window.location.href = "dashboard_configdash.php";';
               echo '</script>';
               die();
            }
            else
            {
               $response["newLogo"] = "NO";
               $response["logoLink"] = $logoLink;
               $response["width"] = $width;
               $response["num_cols"] = $nCols;
            }
        }

        //Cancellazione vecchi permessi di visualizzazione
         $delOldPermissionsQuery = "DELETE FROM Dashboard.DashboardsViewPermissions WHERE IdDashboard = $dashboardId";
         $delOldPermissionsResult = mysqli_query($link, $delOldPermissionsQuery);

         if(!$delOldPermissionsResult)
         {
             $rollbackResult = mysqli_rollback($link);
             mysqli_close($link);
             $queryFail = true;
             echo '<script type="text/javascript">';
             echo 'alert("Error during dashboard update: please repeat the procedure.");';
             echo 'window.location.href = "dashboard_configdash.php";';
             echo '</script>';
             die();
         }
         else
         {
            if($visibility == "restrict")
            {
               foreach($_POST['selectedVisibilityUsers'] as $selectedUser)
               {
                  $insertQuery = "INSERT INTO Dashboard.DashboardsViewPermissions VALUES($dashboardId, '$selectedUser')";
                  $result4 = mysqli_query($link, $insertQuery);
               }
            }
         }

        if(!$queryFail) 
        {
            $commit = mysqli_commit($link);
            mysqli_close($link);
            header("location: dashboard_configdash.php");
        }
    }
    else if(isset($_REQUEST['add_widget'])) 
    {
        session_start();
        /*if(isset($_POST['textarea-selected-metrics']) && $_POST['textarea-selected-metrics'] != "") 
        {*/
            $id_dashboard = $_SESSION['dashboardId'];
            $dashboardName = $_SESSION['dashboardTitle'];
            $nextId = 1;
            $firstFreeRow = $_POST['firstFreeRowInput'];
            $selqDbtbMaxSel2 = "SELECT MAX(Id) AS MaxId FROM Dashboard.Config_widget_dashboard";
            $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2);
            if($resultMaxSel2) 
            {
                while($rowMaxSel2 = mysqli_fetch_array($resultMaxSel2)) 
                {
                    if ((!is_null($rowMaxSel2['MaxId'])) && (!empty($rowMaxSel2['MaxId']))) 
                    {
                        $nextId = $rowMaxSel2['MaxId'] + 1;
                    }
                }
                
                $actuatorEntity = NULL;
                $actuatorAttribute = NULL;
                $widgetActuatorType = NULL;
                $id_metric = NULL; 
                $type_widget = NULL; 
                $title_widget = NULL;
                $color_widget = mysqli_real_escape_string($link, $_POST['inputColorWidget']); 
                $freq_widget = NULL;
                $sizeRowsWidget = mysqli_real_escape_string($link, $_POST['inputSizeRowsWidget']); 
                $sizeColumnsWidget = mysqli_real_escape_string($link, $_POST['inputSizeColumnsWidget']); 
                $controlsPosition = NULL;
                $showTitle = NULL;
                $controlsVisibility = NULL;
                $zoomFactor = NULL;
                $scaleX = NULL;
                $scaleY = NULL;
                $defaultTab = NULL;
                $zoomControlsColor = NULL;
                $headerFontColor = NULL;
                $styleParameters = NULL;
                $showTableFirstCell = NULL;
                $tableFirstCellFontSize = NULL;
                $tableFirstCellFontColor = NULL;
                $rowsLabelsFontSize = NULL;
                $rowsLabelsFontColor = NULL;
                $colsLabelsFontSize = NULL;
                $colsLabelsFontColor = NULL;
                $rowsLabelsBckColor = NULL;
                $colsLabelsBckColor = NULL;
                $tableBorders = NULL;
                $tableBordersColor = NULL;
                $infoJsonObject = NULL;
                $infoJson = NULL;
                $legendFontSize = NULL;
                $legendFontColor = NULL;
                $dataLabelsFontSize = NULL;
                $dataLabelsFontColor = NULL;
                $barsColorsSelect = NULL;
                $barsColors = NULL;
                $chartType = NULL;
                $dataLabelsDistance = NULL;
                $dataLabelsDistance1 = NULL;
                $dataLabelsDistance2 = NULL;
                $dataLabels = NULL;
                $dataLabelsRotation = NULL;
                $xAxisDataset = NULL;
                $lineWidth = NULL;
                $alrLook = NULL;
                $colorsSelect = NULL;
                $colors = NULL;
                $colorsSelect1 = NULL;
                $colors1 = NULL;
                $innerRadius1 = NULL;
                $outerRadius1 = NULL;
                $innerRadius2 = NULL;
                $startAngle = NULL;
                $endAngle = NULL;
                $centerY = NULL;
                $gridLinesWidth = NULL;
                $gridLinesColor = NULL;
                $linesWidth = NULL;
                $alrThrLinesWidth = NULL;
                $clockData = NULL;
                $clockFont = NULL;
                $rectDim = NULL;
                $enableFullscreenTab = 'no';
                $enableFullscreenModal = 'no'; 
                $fontFamily = mysqli_real_escape_string($link, $_REQUEST['inputFontFamilyWidget']);
                
                if($_REQUEST['widgetCategory'] == "actuator")
                {
                    $actuatorEntity = $_REQUEST['widgetEntity'];
                    $actuatorAttribute = $_REQUEST['widgetAttribute'];
                    $widgetActuatorType = $_REQUEST['widgetActuatorType'];
                    $type_widget = $widgetActuatorType;
                    
                    if($type_widget == "widgetKnob")
                    {
                        $styleParametersArray = array('startAngle' => $_REQUEST['addKnobStartAngle'], 'endAngle' => $_REQUEST['addKnobEndAngle']);
                        $styleParameters = json_encode($styleParametersArray);
                    }
                }
                else
                {
                    $id_metric = mysqli_real_escape_string($link, $_POST['textarea-selected-metrics']); 
                    $type_widget = mysqli_real_escape_string($link, $_POST['select-widget']); 
                }
                
                if($type_widget == "widgetPrevMeteo")
                {
                    $styleParametersArray = array('orientation' => $_REQUEST['orientation'], 'language' => $_REQUEST['language'], 'todayDim' => $_REQUEST['todayDim'], 'backgroundMode' => $_REQUEST['backgroundMode'], 'iconSet' => $_REQUEST['iconSet']);
                    $styleParameters = json_encode($styleParametersArray);
                }
                
                if($type_widget == "widgetSelector")
                {
                  if(isset($_REQUEST['addGisRectDim']))
                  {
                     $rectDim = $_REQUEST['addGisRectDim'];
                  }
                  
                  $styleParametersArray = array('rectDim' => $rectDim, 'activeFontColor' => $_REQUEST['addGisActiveQueriesFontColor']);
                  $styleParameters = json_encode($styleParametersArray);
                }
                
                if($type_widget == "widgetClock")
                {
                  if(isset($_REQUEST['addWidgetClockData']))
                  {
                     $clockData = $_REQUEST['addWidgetClockData'];
                  }

                  if(isset($_REQUEST['addWidgetClockFont']))
                  {
                     $clockFont = $_REQUEST['addWidgetClockFont'];
                  }
                  
                  $styleParametersArray = array('clockData' => $clockData, 'clockFont' => $clockFont);
                  $styleParameters = json_encode($styleParametersArray);
                }
                
                if($type_widget == "widgetFirstAid")
                {
                    $infoNamesJsonFirstAxis = json_decode($_POST['infoNamesJsonFirstAxis']);
                    $infoNamesJsonSecondAxis = json_decode($_POST['infoNamesJsonSecondAxis']);
                    $infoJsonObject = [];
                    $infoJsonFirstAxis = [];
                    $infoJsonSecondAxis = [];

                    foreach ($infoNamesJsonFirstAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonFirstAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonSecondAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    $infoJsonObject["firstAxis"] = $infoJsonFirstAxis;
                    $infoJsonObject["secondAxis"] = $infoJsonSecondAxis;

                    $infoJson = json_encode($infoJsonObject);

                    if(isset($_POST['showTableFirstCell'])&&($_POST['showTableFirstCell']!=""))
                    {
                        $showTableFirstCell = mysqli_real_escape_string($link, $_POST['showTableFirstCell']); 
                    }

                    if(isset($_POST['tableFirstCellFontSize'])&&($_POST['tableFirstCellFontSize']!=""))
                    {
                        $tableFirstCellFontSize = mysqli_real_escape_string($link, $_POST['tableFirstCellFontSize']);
                    }

                    if(isset($_POST['tableFirstCellFontColor'])&&($_POST['tableFirstCellFontColor']!=""))
                    {
                        $tableFirstCellFontColor = mysqli_real_escape_string($link, $_POST['tableFirstCellFontColor']); 
                    }

                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSize']); 
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColor']); 
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, $_POST['colsLabelsFontSize']);
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, $_POST['colsLabelsFontColor']); 
                    }

                    if(isset($_POST['rowsLabelsBckColor'])&&($_POST['rowsLabelsBckColor']!=""))
                    {
                        $rowsLabelsBckColor = mysqli_real_escape_string($link, $_POST['rowsLabelsBckColor']); 
                    }
                    
                    if(isset($_POST['tableBorders'])&&($_POST['tableBorders']!=""))
                    {
                        $tableBorders = mysqli_real_escape_string($link, $_POST['tableBorders']);
                    }

                    if(isset($_POST['tableBordersColor'])&&($_POST['tableBordersColor']!=""))
                    {
                        $tableBordersColor = mysqli_real_escape_string($link, $_POST['tableBordersColor']); 
                    }

                    $styleParametersArray = array('showTableFirstCell' => $showTableFirstCell, 'tableFirstCellFontSize' => $tableFirstCellFontSize, 'tableFirstCellFontColor' => $tableFirstCellFontColor, 'rowsLabelsFontSize' => $rowsLabelsFontSize, 'rowsLabelsFontColor' => $rowsLabelsFontColor, 'colsLabelsFontSize' => $colsLabelsFontSize, 'colsLabelsFontColor' => $colsLabelsFontColor, 'rowsLabelsBckColor' => $rowsLabelsBckColor, 'colsLabelsBckColor' => $colsLabelsBckColor, 'tableBorders' => $tableBorders, 'tableBordersColor' => $tableBordersColor);
                    $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetPieChart")
                {
                    if(isset($_POST['infoNamesJsonFirstAxis'])&&($_POST['infoNamesJsonFirstAxis']!="")&&(isset($_POST['infoNamesJsonSecondAxis']))&&($_POST['infoNamesJsonSecondAxis']!=""))
                    {
                        $infoNamesJsonFirstAxis = json_decode($_POST['infoNamesJsonFirstAxis']);
                        $infoNamesJsonSecondAxis = json_decode($_POST['infoNamesJsonSecondAxis']);
                        $infoJsonObject = [];
                        $infoJsonFirstAxis = [];
                        $infoJsonSecondAxis = [];

                        foreach ($infoNamesJsonFirstAxis as $name) 
                        {
                            //Hack per metriche contenenti indirizzi IP
                            $name = preg_replace('/\./', "_", $name);
                            $infoJsonFirstAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                        }
                        unset($name);

                        foreach ($infoNamesJsonSecondAxis as $name) 
                        {
                            //Hack per metriche contenenti indirizzi IP
                            $name = preg_replace('/\./', "_", $name);
                            $infoJsonSecondAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                        }
                        unset($name);

                        $infoJsonObject["firstAxis"] = $infoJsonFirstAxis;
                        $infoJsonObject["secondAxis"] = $infoJsonSecondAxis;

                        $infoJson = json_encode($infoJsonObject);
                    }

                    if(isset($_POST['legendFontSize'])&&($_POST['legendFontSize']!=""))
                    {
                        $legendFontSize = mysqli_real_escape_string($link, $_POST['legendFontSize']);
                    }

                    if(isset($_POST['legendFontColorPicker'])&&($_POST['legendFontColorPicker']!=""))
                    {
                        $legendFontColor = mysqli_real_escape_string($link, $_POST['legendFontColorPicker']); 
                    }

                    if(isset($_POST['dataLabelsDistance'])&&($_POST['dataLabelsDistance']!=""))
                    {
                        $dataLabelsDistance = mysqli_real_escape_string($link, $_POST['dataLabelsDistance']);
                    }

                    if(isset($_POST['dataLabelsDistance1'])&&($_POST['dataLabelsDistance1']!=""))
                    {
                        $dataLabelsDistance1 = mysqli_real_escape_string($link, $_POST['dataLabelsDistance1']); 
                    }

                    if(isset($_POST['dataLabelsDistance2'])&&($_POST['dataLabelsDistance2']!=""))
                    {
                        $dataLabelsDistance2 = mysqli_real_escape_string($link, $_POST['dataLabelsDistance2']); 
                    }

                    if(isset($_POST['dataLabels'])&&($_POST['dataLabels']!=""))
                    {
                        $dataLabels = mysqli_real_escape_string($link, $_POST['dataLabels']);
                    }

                    if(isset($_POST['dataLabelsFontSize'])&&($_POST['dataLabelsFontSize']!=""))
                    {
                        $dataLabelsFontSize = mysqli_real_escape_string($link, $_POST['dataLabelsFontSize']);
                    }

                    if(isset($_POST['dataLabelsFontColor'])&&($_POST['dataLabelsFontColor']!=""))
                    {
                        $dataLabelsFontColor = mysqli_real_escape_string($link, $_POST['dataLabelsFontColor']); 
                    }

                    if(isset($_POST['innerRadius1'])&&($_POST['innerRadius1']!=""))
                    {
                        $innerRadius1 = mysqli_real_escape_string($link, $_POST['innerRadius1']); 
                    }

                    if(isset($_POST['startAngle'])&&($_POST['startAngle']!=""))
                    {
                        $startAngle = mysqli_real_escape_string($link, $_POST['startAngle']); 
                    }

                    if(isset($_POST['endAngle'])&&($_POST['endAngle']!=""))
                    {
                        $endAngle = mysqli_real_escape_string($link, $_POST['endAngle']); 
                    }

                    if(isset($_POST['outerRadius1'])&&($_POST['outerRadius1']!=""))
                    {
                        $outerRadius1 = mysqli_real_escape_string($link, $_POST['outerRadius1']); 
                    }

                    if(isset($_POST['innerRadius2'])&&($_POST['innerRadius2']!=""))
                    {
                        $innerRadius2 = mysqli_real_escape_string($link, $_POST['innerRadius2']); 
                    }

                    if(isset($_POST['centerY'])&&($_POST['centerY']!=""))
                    {
                        $centerY = mysqli_real_escape_string($link, $_POST['centerY']); 
                    }

                    if(isset($_POST['colorsSelect1'])&&($_POST['colorsSelect1']!=""))
                    {
                        $colorsSelect1 = mysqli_real_escape_string($link, $_POST['colorsSelect1']); 
                    }

                    if(isset($_POST['colors1'])&&($_POST['colors1']!=""))
                    {
                        $temp = json_decode($_POST['colors1']);
                        $colors1 = [];
                        foreach ($temp as $color) 
                        {
                            array_push($colors1, $color);
                        }
                    }

                    if(isset($_POST['colorsSelect2'])&&($_POST['colorsSelect2']!=""))
                    {
                        $colorsSelect2 = mysqli_real_escape_string($link, $_POST['colorsSelect2']); 
                    }

                    if(isset($_POST['colors2'])&&($_POST['colors2']!=""))
                    {
                        $temp = json_decode($_POST['colors2']);
                        $colors2 = [];
                        foreach ($temp as $color) 
                        {
                            array_push($colors2, $color);
                        }
                    }

                    $styleParametersArray = array();
                    $styleParametersArray['legendFontSize'] = $legendFontSize;
                    $styleParametersArray['legendFontColor'] = $legendFontColor;
                    $styleParametersArray['dataLabelsDistance'] = $dataLabelsDistance;
                    $styleParametersArray['dataLabelsDistance1'] = $dataLabelsDistance1;
                    $styleParametersArray['dataLabelsDistance2'] = $dataLabelsDistance2;
                    $styleParametersArray['dataLabels'] = $dataLabels;
                    $styleParametersArray['dataLabelsFontSize'] = $dataLabelsFontSize;
                    $styleParametersArray['dataLabelsFontColor'] = $dataLabelsFontColor;
                    $styleParametersArray['innerRadius1'] = $innerRadius1;
                    $styleParametersArray['startAngle'] = $startAngle;
                    $styleParametersArray['endAngle'] = $endAngle;
                    $styleParametersArray['centerY'] = $centerY;
                    $styleParametersArray['outerRadius1'] = $outerRadius1;
                    $styleParametersArray['innerRadius2'] = $innerRadius2;
                    $styleParametersArray['colorsSelect1'] = $colorsSelect1;
                    $styleParametersArray['colors1'] = $colors1;
                    $styleParametersArray['colorsSelect2'] = $colorsSelect2;
                    $styleParametersArray['colors2'] = $colors2;
                    $styleParameters = json_encode($styleParametersArray);
                }

                if(($type_widget == "widgetLineSeries") || ($type_widget == "widgetCurvedLineSeries"))
                {
                    $infoNamesJsonFirstAxis = json_decode($_POST['infoNamesJsonFirstAxis']);
                    $infoNamesJsonSecondAxis = json_decode($_POST['infoNamesJsonSecondAxis']);
                    $infoJsonObject = [];
                    $infoJsonFirstAxis = [];
                    $infoJsonSecondAxis = [];

                    foreach ($infoNamesJsonFirstAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonFirstAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonSecondAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    $infoJsonObject["firstAxis"] = $infoJsonFirstAxis;
                    $infoJsonObject["secondAxis"] = $infoJsonSecondAxis;

                    $infoJson = json_encode($infoJsonObject);

                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = $_POST['rowsLabelsFontSize'];
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColor']); 
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, $_POST['colsLabelsFontSize']); 
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, $_POST['colsLabelsFontColor']);
                    }

                    if(isset($_POST['dataLabelsFontSize'])&&($_POST['dataLabelsFontSize']!=""))
                    {
                        $dataLabelsFontSize = mysqli_real_escape_string($link, $_POST['dataLabelsFontSize']);
                    }

                    if(isset($_POST['dataLabelsFontColor'])&&($_POST['dataLabelsFontColor']!=""))
                    {
                        $dataLabelsFontColor = mysqli_real_escape_string($link, $_POST['dataLabelsFontColor']); 
                    }

                    if(isset($_POST['legendFontSize'])&&($_POST['legendFontSize']!=""))
                    {
                        $legendFontSize = mysqli_real_escape_string($link, $_POST['legendFontSize']); 
                    }

                    if(isset($_POST['legendFontColor'])&&($_POST['legendFontColor']!=""))
                    {
                        $legendFontColor = mysqli_real_escape_string($link, $_POST['legendFontColor']); 
                    }

                    if(isset($_POST['barsColorsSelect'])&&($_POST['barsColorsSelect']!=""))
                    {
                        $barsColorsSelect = mysqli_real_escape_string($link, $_POST['barsColorsSelect']); 
                    }

                    if(isset($_POST['chartType'])&&($_POST['chartType']!=""))
                    {
                        $chartType = mysqli_real_escape_string($link, $_POST['chartType']); 
                    }

                    if(isset($_POST['dataLabels'])&&($_POST['dataLabels']!=""))
                    {
                        $dataLabels = mysqli_real_escape_string($link, $_POST['dataLabels']); 
                    }

                    if(isset($_POST['xAxisDataset'])&&($_POST['xAxisDataset']!=""))
                    {
                        $xAxisDataset = mysqli_real_escape_string($link, $_POST['xAxisDataset']); 
                    }

                    if(isset($_POST['lineWidth'])&&($_POST['lineWidth']!=""))
                    {
                        $lineWidth = mysqli_real_escape_string($link, $_POST['lineWidth']);
                    }

                    if(isset($_POST['alrLook'])&&($_POST['alrLook']!=""))
                    {
                        $alrLook = mysqli_real_escape_string($link, $_POST['alrLook']);
                    }

                    if(isset($_POST['barsColors'])&&($_POST['barsColors']!=""))
                    {
                        $temp = json_decode($_POST['barsColors']);
                        $barsColors = [];
                        foreach ($temp as $color) 
                        {
                            array_push($barsColors, $color);
                        }
                    }

                    $styleParametersArray = array();
                    $styleParametersArray['rowsLabelsFontSize'] = $rowsLabelsFontSize;
                    $styleParametersArray['rowsLabelsFontColor'] = $rowsLabelsFontColor;
                    $styleParametersArray['colsLabelsFontSize'] = $colsLabelsFontSize;
                    $styleParametersArray['colsLabelsFontColor'] = $colsLabelsFontColor;
                    $styleParametersArray['dataLabelsFontSize'] = $dataLabelsFontSize;
                    $styleParametersArray['dataLabelsFontColor'] = $dataLabelsFontColor;
                    $styleParametersArray['legendFontSize'] = $legendFontSize;
                    $styleParametersArray['legendFontColor'] = $legendFontColor;
                    $styleParametersArray['barsColorsSelect'] = $barsColorsSelect;
                    $styleParametersArray['chartType'] = $chartType;
                    $styleParametersArray['dataLabels'] = $dataLabels;
                    $styleParametersArray['xAxisDataset'] = $xAxisDataset;
                    $styleParametersArray['lineWidth'] = $lineWidth;
                    $styleParametersArray['alrLook'] = $alrLook;
                    $styleParametersArray['barsColors'] = $barsColors;
                    $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetScatterSeries")
                {
                    $infoNamesJsonFirstAxis = json_decode($_POST['infoNamesJsonFirstAxis']);
                    $infoNamesJsonSecondAxis = json_decode($_POST['infoNamesJsonSecondAxis']);
                    $infoJsonObject = [];
                    $infoJsonFirstAxis = [];
                    $infoJsonSecondAxis = [];

                    foreach ($infoNamesJsonFirstAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonFirstAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonSecondAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    $infoJsonObject["firstAxis"] = $infoJsonFirstAxis;
                    $infoJsonObject["secondAxis"] = $infoJsonSecondAxis;

                    $infoJson = json_encode($infoJsonObject);

                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSize']); 
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColor']);
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, $_POST['colsLabelsFontSize']); 
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, $_POST['colsLabelsFontColor']);
                    }

                    if(isset($_POST['dataLabelsFontSize'])&&($_POST['dataLabelsFontSize']!=""))
                    {
                        $dataLabelsFontSize = mysqli_real_escape_string($link, $_POST['dataLabelsFontSize']); 
                    }

                    if(isset($_POST['dataLabelsFontColor'])&&($_POST['dataLabelsFontColor']!=""))
                    {
                        $dataLabelsFontColor = mysqli_real_escape_string($link, $_POST['dataLabelsFontColor']); 
                    }

                    if(isset($_POST['legendFontSize'])&&($_POST['legendFontSize']!=""))
                    {
                        $legendFontSize = mysqli_real_escape_string($link, $_POST['legendFontSize']); 
                    }

                    if(isset($_POST['legendFontColor'])&&($_POST['legendFontColor']!=""))
                    {
                        $legendFontColor = mysqli_real_escape_string($link, $_POST['legendFontColor']); 
                    }

                    if(isset($_POST['barsColorsSelect'])&&($_POST['barsColorsSelect']!=""))
                    {
                        $barsColorsSelect = mysqli_real_escape_string($link, $_POST['barsColorsSelect']); 
                    }

                    if(isset($_POST['chartType'])&&($_POST['chartType']!=""))
                    {
                        $chartType = mysqli_real_escape_string($link, $_POST['chartType']); 
                    }

                    if(isset($_POST['dataLabels'])&&($_POST['dataLabels']!=""))
                    {
                        $dataLabels = mysqli_real_escape_string($link, $_POST['dataLabels']); 
                    }

                    if(isset($_POST['dataLabelsRotation'])&&($_POST['dataLabelsRotation']!=""))
                    {
                        $dataLabelsRotation = mysqli_real_escape_string($link, $_POST['dataLabelsRotation']);
                    }

                    $styleParametersArray = array();
                    $styleParametersArray['rowsLabelsFontSize'] = $rowsLabelsFontSize;
                    $styleParametersArray['rowsLabelsFontColor'] = $rowsLabelsFontColor;
                    $styleParametersArray['colsLabelsFontSize'] = $colsLabelsFontSize;
                    $styleParametersArray['colsLabelsFontColor'] = $colsLabelsFontColor;
                    $styleParametersArray['dataLabelsFontSize'] = $dataLabelsFontSize;
                    $styleParametersArray['dataLabelsFontColor'] = $dataLabelsFontColor;
                    $styleParametersArray['legendFontSize'] = $legendFontSize;
                    $styleParametersArray['legendFontColor'] = $legendFontColor;
                    $styleParametersArray['barsColorsSelect'] = $barsColorsSelect;
                    $styleParametersArray['chartType'] = $chartType;
                    $styleParametersArray['dataLabels'] = $dataLabels;
                    $styleParametersArray['dataLabelsRotation'] = $dataLabelsRotation;

                    if(isset($_POST['barsColors'])&&($_POST['barsColors']!=""))
                    {
                        $temp = json_decode($_POST['barsColors']);
                        $barsColors = [];
                        foreach ($temp as $color) 
                        {
                            array_push($barsColors, $color);
                        }
                    }

                    $styleParametersArray['barsColors'] = $barsColors;
                    $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetBarSeries")
                {
                    $infoNamesJsonFirstAxis = json_decode($_POST['infoNamesJsonFirstAxis']);
                    $infoNamesJsonSecondAxis = json_decode($_POST['infoNamesJsonSecondAxis']);
                    $infoJsonObject = [];
                    $infoJsonFirstAxis = [];
                    $infoJsonSecondAxis = [];
                    
                    foreach($infoNamesJsonFirstAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonFirstAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);
                      
                    foreach($infoNamesJsonSecondAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonSecondAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    $infoJsonObject["firstAxis"] = $infoJsonFirstAxis;
                    $infoJsonObject["secondAxis"] = $infoJsonSecondAxis;

                    $infoJson = json_encode($infoJsonObject);

                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = $_POST['rowsLabelsFontSize'];
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColor']); 
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, $_POST['colsLabelsFontSize']); 
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, $_POST['colsLabelsFontColor']); 
                    }

                    if(isset($_POST['dataLabelsFontSize'])&&($_POST['dataLabelsFontSize']!=""))
                    {
                        $dataLabelsFontSize = mysqli_real_escape_string($link, $_POST['dataLabelsFontSize']);
                    }

                    if(isset($_POST['dataLabelsFontColor'])&&($_POST['dataLabelsFontColor']!=""))
                    {
                        $dataLabelsFontColor = mysqli_real_escape_string($link, $_POST['dataLabelsFontColor']); 
                    }

                    if(isset($_POST['legendFontSize'])&&($_POST['legendFontSize']!=""))
                    {
                        $legendFontSize = mysqli_real_escape_string($link, $_POST['legendFontSize']); 
                    }

                    if(isset($_POST['legendFontColor'])&&($_POST['legendFontColor']!=""))
                    {
                        $legendFontColor = mysqli_real_escape_string($link, $_POST['legendFontColor']);
                    }

                    if(isset($_POST['barsColorsSelect'])&&($_POST['barsColorsSelect']!=""))
                    {
                        $barsColorsSelect = mysqli_real_escape_string($link, $_POST['barsColorsSelect']); 
                    }

                    if(isset($_POST['chartType'])&&($_POST['chartType']!=""))
                    {
                        $chartType = mysqli_real_escape_string($link, $_POST['chartType']); 
                    }

                    if(isset($_POST['dataLabels'])&&($_POST['dataLabels']!=""))
                    {
                        $dataLabels = mysqli_real_escape_string($link, $_POST['dataLabels']); 
                    }

                    if(isset($_POST['dataLabelsRotation'])&&($_POST['dataLabelsRotation']!=""))
                    {
                        $dataLabelsRotation = mysqli_real_escape_string($link, $_POST['dataLabelsRotation']); 
                    }

                    $styleParametersArray = array();
                    $styleParametersArray['rowsLabelsFontSize'] = $rowsLabelsFontSize;
                    $styleParametersArray['rowsLabelsFontColor'] = $rowsLabelsFontColor;
                    $styleParametersArray['colsLabelsFontSize'] = $colsLabelsFontSize;
                    $styleParametersArray['colsLabelsFontColor'] = $colsLabelsFontColor;
                    $styleParametersArray['dataLabelsFontSize'] = $dataLabelsFontSize;
                    $styleParametersArray['dataLabelsFontColor'] = $dataLabelsFontColor;
                    $styleParametersArray['legendFontSize'] = $legendFontSize;
                    $styleParametersArray['legendFontColor'] = $legendFontColor;
                    $styleParametersArray['barsColorsSelect'] = $barsColorsSelect;
                    $styleParametersArray['chartType'] = $chartType;
                    $styleParametersArray['dataLabels'] = $dataLabels;
                    $styleParametersArray['dataLabelsRotation'] = $dataLabelsRotation;

                    if(isset($_POST['barsColors'])&&($_POST['barsColors']!=""))
                    {
                        $temp = json_decode($_POST['barsColors']);
                        $barsColors = [];
                        foreach ($temp as $color) 
                        {
                            array_push($barsColors, $color);
                        }
                    }

                    $styleParametersArray['barsColors'] = $barsColors;
                    $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetRadarSeries")
                {
                    $infoNamesJsonFirstAxis = json_decode($_POST['infoNamesJsonFirstAxis']);
                    $infoNamesJsonSecondAxis = json_decode($_POST['infoNamesJsonSecondAxis']);
                    $infoJsonObject = [];
                    $infoJsonFirstAxis = [];
                    $infoJsonSecondAxis = [];

                    foreach ($infoNamesJsonFirstAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonFirstAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonSecondAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    $infoJsonObject["firstAxis"] = $infoJsonFirstAxis;
                    $infoJsonObject["secondAxis"] = $infoJsonSecondAxis;

                    $infoJson = json_encode($infoJsonObject);

                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSize']);
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColor']); 
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, $_POST['colsLabelsFontSize']); 
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, $_POST['colsLabelsFontColor']); 
                    }

                    if(isset($_POST['dataLabelsFontSize'])&&($_POST['dataLabelsFontSize']!=""))
                    {
                        $dataLabelsFontSize = mysqli_real_escape_string($link, $_POST['dataLabelsFontSize']); 
                    }

                    if(isset($_POST['dataLabelsFontColor'])&&($_POST['dataLabelsFontColor']!=""))
                    {
                        $dataLabelsFontColor = mysqli_real_escape_string($link, $_POST['dataLabelsFontColor']); 
                    }

                    if(isset($_POST['legendFontSize'])&&($_POST['legendFontSize']!=""))
                    {
                        $legendFontSize = mysqli_real_escape_string($link, $_POST['legendFontSize']); 
                    }

                    if(isset($_POST['legendFontColor'])&&($_POST['legendFontColor']!=""))
                    {
                        $legendFontColor = mysqli_real_escape_string($link, $_POST['legendFontColor']); 
                    }

                    if(isset($_POST['gridLinesWidth'])&&($_POST['gridLinesWidth']!=""))
                    {
                        $gridLinesWidth = mysqli_real_escape_string($link, $_POST['gridLinesWidth']); 
                    }

                    if(isset($_POST['gridLinesColor'])&&($_POST['gridLinesColor']!=""))
                    {
                        $gridLinesColor = mysqli_real_escape_string($link, $_POST['gridLinesColor']); 
                    }

                    if(isset($_POST['linesWidth'])&&($_POST['linesWidth']!=""))
                    {
                        $linesWidth = mysqli_real_escape_string($link, $_POST['linesWidth']); 
                    }

                    if(isset($_POST['barsColorsSelect'])&&($_POST['barsColorsSelect']!=""))
                    {
                        $barsColorsSelect = mysqli_real_escape_string($link, $_POST['barsColorsSelect']); 
                    }

                    if(isset($_POST['alrThrLinesWidth'])&&($_POST['alrThrLinesWidth']!=""))
                    {
                        $alrThrLinesWidth = mysqli_real_escape_string($link, $_POST['alrThrLinesWidth']); 
                    }

                    if(isset($_POST['dataLabels'])&&($_POST['dataLabels']!=""))
                    {
                        $dataLabels = mysqli_real_escape_string($link, $_POST['dataLabels']); 
                    }

                    if(isset($_POST['dataLabelsRotation'])&&($_POST['dataLabelsRotation']!=""))
                    {
                        $dataLabelsRotation = mysqli_real_escape_string($link, $_POST['dataLabelsRotation']); 
                    }

                    $styleParametersArray = array();
                    $styleParametersArray['rowsLabelsFontSize'] = $rowsLabelsFontSize;
                    $styleParametersArray['rowsLabelsFontColor'] = $rowsLabelsFontColor;
                    $styleParametersArray['colsLabelsFontSize'] = $colsLabelsFontSize;
                    $styleParametersArray['colsLabelsFontColor'] = $colsLabelsFontColor;
                    $styleParametersArray['dataLabelsFontSize'] = $dataLabelsFontSize;
                    $styleParametersArray['dataLabelsFontColor'] = $dataLabelsFontColor;
                    $styleParametersArray['legendFontSize'] = $legendFontSize;
                    $styleParametersArray['legendFontColor'] = $legendFontColor;
                    $styleParametersArray['gridLinesWidth'] = $gridLinesWidth;
                    $styleParametersArray['gridLinesColor'] = $gridLinesColor;
                    $styleParametersArray['linesWidth'] = $linesWidth;
                    $styleParametersArray['barsColorsSelect'] = $barsColorsSelect;
                    $styleParametersArray['alrThrLinesWidth'] = $alrThrLinesWidth;
                    $styleParametersArray['dataLabels'] = $dataLabels;
                    $styleParametersArray['dataLabelsRotation'] = $dataLabelsRotation;

                    if(isset($_POST['barsColors'])&&($_POST['barsColors']!=""))
                    {
                        $temp = json_decode($_POST['barsColors']);
                        $barsColors = [];
                        foreach ($temp as $color) 
                        {
                            array_push($barsColors, $color);
                        }
                    }

                    $styleParametersArray['barsColors'] = $barsColors;
                    $styleParameters = json_encode($styleParametersArray);
                }

                //Nuovo campo styleParameters, il JSON viene costruito qui
                if($type_widget == "widgetTable")
                {
                    $infoNamesJsonFirstAxis = json_decode($_POST['infoNamesJsonFirstAxis']);
                    $infoNamesJsonSecondAxis = json_decode($_POST['infoNamesJsonSecondAxis']);
                    $infoJsonObject = [];
                    $infoJsonFirstAxis = [];
                    $infoJsonSecondAxis = [];

                    foreach ($infoNamesJsonFirstAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonFirstAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        //Hack per metriche contenenti indirizzi IP
                        $name = preg_replace('/\./', "_", $name);
                        $infoJsonSecondAxis[preg_replace('/_/', ".", $name)] = $_POST[$name];
                    }
                    unset($name);

                    $infoJsonObject["firstAxis"] = $infoJsonFirstAxis;
                    $infoJsonObject["secondAxis"] = $infoJsonSecondAxis;

                    $infoJson = json_encode($infoJsonObject);

                    if(isset($_POST['showTableFirstCell'])&&($_POST['showTableFirstCell']!=""))
                    {
                        $showTableFirstCell = mysqli_real_escape_string($link, $_POST['showTableFirstCell']); 
                    }

                    if(isset($_POST['tableFirstCellFontSize'])&&($_POST['tableFirstCellFontSize']!=""))
                    {
                        $tableFirstCellFontSize = mysqli_real_escape_string($link, $_POST['tableFirstCellFontSize']);
                    }

                    if(isset($_POST['tableFirstCellFontColor'])&&($_POST['tableFirstCellFontColor']!=""))
                    {
                        $tableFirstCellFontColor = mysqli_real_escape_string($link, $_POST['tableFirstCellFontColor']); 
                    }

                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSize']); 
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColor']); 
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, $_POST['colsLabelsFontSize']);
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, $_POST['colsLabelsFontColor']); 
                    }

                    if(isset($_POST['rowsLabelsBckColor'])&&($_POST['rowsLabelsBckColor']!=""))
                    {
                        $rowsLabelsBckColor = mysqli_real_escape_string($link, $_POST['rowsLabelsBckColor']); 
                    }

                    if(isset($_POST['colsLabelsBckColor'])&&($_POST['colsLabelsBckColor']!=""))
                    {
                        $colsLabelsBckColor = mysqli_real_escape_string($link, $_POST['colsLabelsBckColor']);
                    }

                    if(isset($_POST['tableBorders'])&&($_POST['tableBorders']!=""))
                    {
                        $tableBorders = mysqli_real_escape_string($link, $_POST['tableBorders']);
                    }

                    if(isset($_POST['tableBordersColor'])&&($_POST['tableBordersColor']!=""))
                    {
                        $tableBordersColor = mysqli_real_escape_string($link, $_POST['tableBordersColor']); 
                    }

                    $styleParametersArray = array('showTableFirstCell' => $showTableFirstCell, 'tableFirstCellFontSize' => $tableFirstCellFontSize, 'tableFirstCellFontColor' => $tableFirstCellFontColor, 'rowsLabelsFontSize' => $rowsLabelsFontSize, 'rowsLabelsFontColor' => $rowsLabelsFontColor, 'colsLabelsFontSize' => $colsLabelsFontSize, 'colsLabelsFontColor' => $colsLabelsFontColor, 'rowsLabelsBckColor' => $rowsLabelsBckColor, 'colsLabelsBckColor' => $colsLabelsBckColor, 'tableBorders' => $tableBorders, 'tableBordersColor' => $tableBordersColor);
                    $styleParameters = json_encode($styleParametersArray);
                }
                
                if($type_widget == "widgetProtezioneCivile")
                {
                  if(isset($_POST['meteoTabFontSize']) && ($_POST['meteoTabFontSize'] != "") && ($_POST['meteoTabFontSize'] != null))
                  {
                      $meteoTabFontSize = mysqli_real_escape_string($link, $_POST['meteoTabFontSize']);
                  }

                  if(isset($_POST['genTabFontSize']) && ($_POST['genTabFontSize'] != "") && ($_POST['genTabFontSize'] != null))
                  {
                      $genTabFontSize = mysqli_real_escape_string($link, $_POST['genTabFontSize']);
                  }

                  if(isset($_POST['genTabFontColor']) && ($_POST['genTabFontColor'] != "") && ($_POST['genTabFontColor'] != null))
                  {
                      $genTabFontColor = mysqli_real_escape_string($link, $_POST['genTabFontColor']);
                  }
                  
                  $styleParametersArray = array('meteoTabFontSize' => $meteoTabFontSize, 'genTabFontSize' => $genTabFontSize, 'genTabFontColor' => $genTabFontColor);
                  $styleParameters = json_encode($styleParametersArray);
                }

                if(isset($_POST['inputHeaderFontColorWidget'])&&($_POST['inputHeaderFontColorWidget']!=""))
                {
                    $headerFontColor = mysqli_real_escape_string($link, $_POST['inputHeaderFontColorWidget']);
                }

                if(isset($_POST['inputZoomControlsColor'])&&($_POST['inputZoomControlsColor']!="")&&($type_widget == 'widgetExternalContent'))
                {
                    $zoomControlsColor = mysqli_real_escape_string($link, $_POST['inputZoomControlsColor']);
                }

                if(isset($_POST['inputControlsPosition'])&&($_POST['inputControlsPosition']!="")&&($type_widget == 'widgetExternalContent'))
                {
                    $controlsPosition = mysqli_real_escape_string($link, $_POST['inputControlsPosition']); 
                }

                if(isset($_POST['inputShowTitle'])&&($_POST['inputShowTitle']!="")/*&&($type_widget == 'widgetExternalContent')*/)
                {
                    $showTitle = mysqli_real_escape_string($link, $_POST['inputShowTitle']); 
                }

                if(isset($_POST['inputControlsVisibility'])&&($_POST['inputControlsVisibility']!="")&&($type_widget == 'widgetExternalContent'))
                {
                    $controlsVisibility = mysqli_real_escape_string($link, $_POST['inputControlsVisibility']);
                }

                if(isset($_POST['inputDefaultTab']) && ($_POST['inputDefaultTab'] != "") && ($_POST['inputDefaultTab'] != null))
                {
                    $defaultTab = mysqli_real_escape_string($link, $_POST['inputDefaultTab']);
                }
                
                //Aggiunta del campo della tabella "config_widget_dashboard" per i messaggi informativi
                $message_widget = mysqli_real_escape_string($link, $_POST['widgetInfoEditor']);

                //colore della finestra
                $frame_color = NULL;

                if(isset($_POST['inputTitleWidget'])&&($_POST['inputTitleWidget']!=""))
                {
                    $title_widget = mysqli_real_escape_string($link, $_POST['inputTitleWidget']); 
                }

                if(isset($_POST['inputFreqWidget'])&&($_POST['inputFreqWidget']!=""))
                {
                    $freq_widget = mysqli_real_escape_string($link, $_POST['inputFreqWidget']); 
                }

                if(isset($_POST['inputFrameColorWidget'])&&($_POST['inputFrameColorWidget']!=""))
                {
                    $frame_color = mysqli_real_escape_string($link, $_POST['inputFrameColorWidget']); 
                }

               //Parametri
               $parameters = NULL;
               
               if(isset($_POST['parameters'])&&($_POST['parameters'] != ""))
               {
                  if(($type_widget == 'widgetServerStatus')||($type_widget == 'widgetBarContent')||($type_widget == 'widgetColumnContent')||($type_widget == 'widgetGaugeChart')||($type_widget == 'widgetSingleContent')||($type_widget == 'widgetSpeedometer')||($type_widget == 'widgetTimeTrend')||($type_widget == 'widgetTimeTrendCompare'))
                  {
                     if($_POST['alrThrSel'] == "yes")
                     {
                        $parameters = $_POST['parameters'];
                     }
                  }
                  else
                  {
                     $parameters = $_POST['parameters'];
                  }
               } 

                //Gestione parametri per widget di stato del singolo processo
                if($id_metric == 'Process')
                {
                  $host = $_POST['host'];
                  $user = $_POST['user'];
                  $pass = $_POST['pass'];
                  $schedulerName = $_POST['schedulerName'];
                  $jobArea = $_POST['jobArea'];
                  $jobGroup = $_POST['jobGroup'];
                  $jobName = $_POST['jobName'];
                  $parametersArray = array('host' => $host, 'user' => $user, 'pass' => $pass, 'schedulerName' => $schedulerName, 'jobArea' => $jobArea, 'jobGroup' => $jobGroup, 'jobName' => $jobName);
                  $parameters = json_encode($parametersArray);
                }

                if(isset($_POST['inputUrlWidget']))
                {
                    if(preg_match('/^ *$/', $_POST['inputUrlWidget'])) 
                    {
                       $url_widget = "none";
                    }
                    else
                    {
                       $url_widget = mysqli_real_escape_string($link, $_POST['inputUrlWidget']);
                    }
                }
                else
                {
                    $url_widget = "none";
                }

                $comune_widget = NULL;
                /*if(isset($_POST['inputComuneWidget']) && $_POST['inputComuneWidget'] != "") 
                {
                    $comune_widget = strtoupper($_POST['inputComuneWidget']);
                    $name_widget = preg_replace('/\+/', '', $id_metric) . "_" . preg_replace('/ /', '_', $comune_widget) . "_" . $id_dashboard . "_" . $type_widget . $nextId;
                } 
                else  
                {
                    $name_widget = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_widget . $nextId;
                }*/
                
                if(isset($_POST['inputComuneWidget']) && $_POST['inputComuneWidget'] != "") 
                {
                    $comune_widget = strtoupper($_POST['inputComuneWidget']);
                } 
                
                if($_REQUEST['widgetCategory'] == "actuator")
                {
                    $name_widget = preg_replace('/\+/', '', $actuatorEntity) . "_" . $id_dashboard . "_" . $type_widget . $nextId;
                }
                else
                {
                    $name_widget = preg_replace('/\+/', '', $id_metric) . "_" . $id_dashboard . "_" . $type_widget . $nextId;
                }
                

                $int_temp_widget = NULL;
                if(isset($_POST['select-IntTemp-Widget']) && $_POST['select-IntTemp-Widget'] != "") 
                {
                    $int_temp_widget = $_POST['select-IntTemp-Widget'];

                    if($_POST['select-IntTemp-Widget'] != "Nessuno") 
                    {
                        $name_widget = $name_widget . "_" . preg_replace('/ /', '', $_POST['select-IntTemp-Widget']);
                    }
                }
                
                //Va messo qui perché serve il nome del widget
                if($type_widget == "widgetSelector")
                {
                  if(!is_dir("../img/widgetSelectorImages"))
                  {
                     mkdir("../img/widgetSelectorImages/", 0777);
                  }

                  if(!is_dir("../img/widgetSelectorImages/" . $name_widget))
                  {
                      mkdir("../img/widgetSelectorImages/" . $name_widget, 0777);
                  }
                  
                  $parametersSelectorArray = json_decode($parameters, true);
                  //$file = fopen("C:\dashboardLog.txt", "a");
                    foreach($_FILES["addSelectorLogos"]["error"] as $key => $error) 
                    {
                        mkdir("../img/widgetSelectorImages/" . $name_widget . "/q" . $key, 0777);
                        
                        if($error == UPLOAD_ERR_OK) 
                        {
                            $tmp_name = $_FILES["addSelectorLogos"]["tmp_name"][$key];
                            $name = basename($_FILES["addSelectorLogos"]["name"][$key]);
                            move_uploaded_file($tmp_name, "../img/widgetSelectorImages/" . $name_widget . "/q" . $key . "/" . $name);
                            
                            $parametersSelectorArray['queries'][$key]['symbolMode'] = 'man';
                            $parametersSelectorArray['queries'][$key]['symbolFile'] = "../img/widgetSelectorImages/" . $name_widget . "/q" . $key . "/" . $name;
                        }
                        else
                        {
                            $parametersSelectorArray['queries'][$key]['symbolMode'] = 'auto';
                        }
                        
                        $parameters = json_encode($parametersSelectorArray);
                    }
                }
                
                if($type_widget == "widgetButton")
                {
                   $styleParametersArray = [];
                   $styleParametersArray["borderRadius"] = $_REQUEST['addWidgetBtnRadius'];
                   
                   if($_REQUEST['addWidgetBtnImgSelect'] == "yes")
                   {
                      if(isset($_FILES['addWidgetBtnFile']))
                      {
                        if($_FILES['addWidgetBtnFile']['size'] > 0)
                        {
                           if(!file_exists("../img/widgetButtonImages/"))
                           {
                              $oldMask = umask(0); 
                              mkdir("../img/widgetButtonImages/", 0777);
                              umask($oldMask);
                           }
                           
                           if(!file_exists("../img/widgetButtonImages/" . $name_widget))
                           {
                              $oldMask = umask(0); 
                              mkdir("../img/widgetButtonImages/" . $name_widget, 0777);
                              umask($oldMask);
                           }
                           
                           $fileUploaded = move_uploaded_file($_FILES['addWidgetBtnFile']['tmp_name'], "../img/widgetButtonImages/" . $name_widget . "/" . $_FILES['addWidgetBtnFile']['name']);
                           chmod("../img/widgetButtonImages/" . $name_widget . "/" . $_FILES['addWidgetBtnFile']['name'], 0666);
                           
                           $styleParametersArray["hasImage"] = "yes";
                           $styleParametersArray["imageWidth"] = $_REQUEST["addWidgetBtnImgWidth"];
                           $styleParametersArray["imageHeight"] = $_REQUEST["addWidgetBtnImgHeight"];
                           $styleParametersArray["imageName"] = $_FILES['addWidgetBtnFile']['name'];
                        }
                        else
                        {
                           $styleParametersArray["hasImage"] = "no";
                        }
                      }
                      else
                      {
                         $styleParametersArray["hasImage"] = "no";
                      }
                   }
                   else
                   {
                      $styleParametersArray["hasImage"] = "no";
                   }
                   
                   $styleParametersArray["showText"] = $_REQUEST['addWidgetShowButtonText'];
                   
                  $styleParameters = json_encode($styleParametersArray); 
                }

                $inputUdmWidget = NULL;
                if(isset($_POST['inputUdmWidget']) && ($_POST['inputUdmWidget'] != "")) 
                {
                    $inputUdmWidget = mysqli_real_escape_string($link, $_POST['inputUdmWidget']);
                }
                
                $inputUdmPosition = NULL;
                if(isset($_POST['inputUdmPosition']) && ($_POST['inputUdmPosition'] != "")) 
                {
                    $inputUdmPosition = mysqli_real_escape_string($link, $_POST['inputUdmPosition']);
                }

                $fontSize = NULL;
                if(isset($_POST['inputFontSize']) && ($_POST['inputFontSize'] != '') && (!empty($_POST['inputFontSize']))) 
                {
                    $fontSize = mysqli_real_escape_string($link, $_POST['inputFontSize']); 
                }

                $fontColor = NULL;
                if(isset($_POST['inputFontColor']) && ($_POST['inputFontColor'] != '') && (!empty($_POST['inputFontColor']))) 
                {
                    $fontColor = mysqli_real_escape_string($link, $_POST['inputFontColor']);
                }

                $nCol = 1;
                
                $serviceUri = NULL;
                if(isset($_POST['serviceUri']) && ($_POST['serviceUri'] != '') && (!empty($_POST['serviceUri']))) 
                {
                    $serviceUri = mysqli_real_escape_string($link, $_POST['serviceUri']);
                }
                
                $viewMode = NULL;
                if(isset($_POST['addWidgetFirstAidMode']) && ($_POST['addWidgetFirstAidMode'] != '') && (!empty($_POST['addWidgetFirstAidMode']))) 
                {
                    $viewMode = mysqli_real_escape_string($link, $_POST['addWidgetFirstAidMode']);
                }
                else
                {
                    if(isset($_POST['widgetEventsMode'])) 
                    {
                        $viewMode = mysqli_real_escape_string($link, $_POST['widgetEventsMode']);
                    }
                }
                
                $hospitalList = NULL;
                if(isset($_POST['hospitalList']) && ($_POST['hospitalList'] != '') && (!empty($_POST['hospitalList']))) 
                {
                    $hospitalList = $_POST['hospitalList'];
                }
                
                if($type_widget == 'widgetExternalContent')
                {
                    $zoomFactor = 1;
                    $scaleX = 1;
                    $scaleY = 1;
                    
                    //Aggiornamento della lista dei target degli widget events
                    mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
                    
                    $updTargetQuery = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetEvents%' AND id_dashboard = '$id_dashboard'";
                    $resultUpdTargetQuery = mysqli_query($link, $updTargetQuery);
                    
                    if($resultUpdTargetQuery)
                    {
                        while($row = mysqli_fetch_array($resultUpdTargetQuery)) 
                        {
                           $targetList = json_decode($row['parameters'], true);
                           $widgetId = $row['Id'];
                           $targetList[$name_widget] = [];
                           $updatedTargetList = json_encode($targetList);
                           $query5 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                           $result5 = mysqli_query($link, $query5);

                           if(!$result5)
                           {
                              $rollbackResult = mysqli_rollback($link);
                              mysqli_close($link);
                              echo '<script type="text/javascript">';
                              echo 'alert("Error while updating widget target lists");';
                              echo 'window.location.href = "dashboard_configdash.php";';
                              echo '</script>';
                              exit();
                           }
                        }

                        $commit = mysqli_commit($link);
                     }
                     else
                     {
                        $rollbackResult = mysqli_rollback($link);
                        mysqli_close($link);
                        echo '<script type="text/javascript">';
                        echo 'alert("Error while updating widget event producers into database");';
                        echo 'window.location.href = "dashboard_configdash.php";';
                        echo '</script>';
                     }
                }
                
                if(isset($_REQUEST['addWidgetRegisterGen']))
                {
                   $notificatorRegistered = $_REQUEST['addWidgetRegisterGen'];
                   $notificatorEnabled = $_REQUEST['addWidgetRegisterGen'];
                }
                else
                {
                   $notificatorRegistered = 'no';
                   $notificatorEnabled = 'no';
                }
                
               if($type_widget == 'widgetTrafficEvents')
               {
                  //31/08/2017 - Patch temporanea in attesa di avere tempo di mettere i controlli sul form
                  $styleParameters = '{"choosenOption":"events", "timeUdm":"MINUTE", "time":90, "events":50}';
               }
               
                if(isset($_REQUEST['enableFullscreenTab']))
                {
                   $enableFullscreenTab = $_REQUEST['enableFullscreenTab'];
                }
                
                if(isset($_REQUEST['enableFullscreenModal']))
                {
                   $enableFullscreenModal = $_REQUEST['enableFullscreenModal'];
                }
                
                
                    /*$insqDbtb3 = $link->prepare("INSERT INTO Dashboard.Config_widget_dashboard (Id, name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, frequency_w, temporal_range_w, municipality_w, infoMessage_w, link_w, parameters, frame_color_w, udm, udmPos, fontSize, fontColor, controlsPosition, showTitle, controlsVisibility, zoomFactor, defaultTab, zoomControlsColor, scaleX, scaleY, headerFontColor, styleParameters, infoJson, serviceUri, viewMode, hospitalList, notificatorRegistered, notificatorEnabled, enableFullscreenTab, enableFullscreenModal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insqDbtb3->bind_param('isissiiiisssssssssssissssdisddssssssssss', $nextId, $name_widget, $id_dashboard, $id_metric, $type_widget, $firstFreeRow, $nCol, $sizeRowsWidget, $sizeColumnsWidget, $title_widget, $color_widget, $freq_widget, $int_temp_widget, $comune_widget, $message_widget, $url_widget, $parameters, $frame_color, $inputUdmWidget, $inputUdmPosition, $fontSize, $fontColor, $controlsPosition, $showTitle, $controlsVisibility, $zoomFactor, $defaultTab, $zoomControlsColor, $scaleX, $scaleY, $headerFontColor, $styleParameters, $infoJson, $serviceUri, $viewMode, $hospitalList, $notificatorRegistered, $notificatorEnabled, $enableFullscreenTab, $enableFullscreenModal);
                    $result4 = $insqDbtb3->execute();*/
                
                    $insqDbtb3 = "INSERT INTO Dashboard.Config_widget_dashboard(Id, name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, frequency_w, temporal_range_w, municipality_w, infoMessage_w, link_w, parameters, frame_color_w, udm, udmPos, fontSize, fontColor, controlsPosition, showTitle, controlsVisibility, zoomFactor, defaultTab, zoomControlsColor, scaleX, scaleY, headerFontColor, styleParameters, infoJson, serviceUri, viewMode, hospitalList, notificatorRegistered, notificatorEnabled, enableFullscreenTab, enableFullscreenModal, fontFamily, actuatorEntity, actuatorAttribute) " .
                                 "VALUES($nextId , " . returnManagedStringForDb($name_widget) . ", " . returnManagedNumberForDb($id_dashboard) . ", " . returnManagedStringForDb($id_metric) . ", " . returnManagedStringForDb($type_widget) . ", " . returnManagedNumberForDb($firstFreeRow) . ", " . returnManagedNumberForDb($nCol) . ", " . returnManagedNumberForDb($sizeRowsWidget) . ", " . returnManagedNumberForDb($sizeColumnsWidget) . ", " . returnManagedStringForDb($title_widget) . ", " . returnManagedStringForDb($color_widget) . ", " . returnManagedStringForDb($freq_widget) . ", " . returnManagedStringForDb($int_temp_widget) . ", " . returnManagedStringForDb($comune_widget) . ", " . returnManagedStringForDb($message_widget) . ", " . returnManagedStringForDb($url_widget) . ", " . returnManagedStringForDb($parameters) . ", " . returnManagedStringForDb($frame_color) . ", " . returnManagedStringForDb($inputUdmWidget) . ", " . returnManagedStringForDb($inputUdmPosition) . ", " . returnManagedNumberForDb($fontSize) . ", " . returnManagedStringForDb($fontColor) . ", " . returnManagedStringForDb($controlsPosition) . ", " . returnManagedStringForDb($showTitle) . ", " . returnManagedStringForDb($controlsVisibility) . ", " . returnManagedNumberForDb($zoomFactor) . ", " . returnManagedStringForDb($defaultTab) . ", " . returnManagedStringForDb($zoomControlsColor) . ", " . returnManagedNumberForDb($scaleX) . ", " . returnManagedNumberForDb($scaleY) . ", " . returnManagedStringForDb($headerFontColor) . ", " . returnManagedStringForDb($styleParameters) . ", " . returnManagedStringForDb($infoJson) . ", " . returnManagedStringForDb($serviceUri) . ", " . returnManagedStringForDb($viewMode) . ", " . returnManagedStringForDb($hospitalList) . ", " . returnManagedStringForDb($notificatorRegistered) . ", " . returnManagedStringForDb($notificatorEnabled) . ", " . returnManagedStringForDb($enableFullscreenTab) . ", " . returnManagedStringForDb($enableFullscreenModal) . ", " . returnManagedStringForDb($fontFamily) . ", " . returnManagedStringForDb($actuatorEntity) . ", " . returnManagedStringForDb($actuatorAttribute) .")";
                    
                    $result4 = mysqli_query($link, $insqDbtb3);
                    
                    if($result4) 
                    {
                        mysqli_close($link);
                        
                        if(isset($_REQUEST['addWidgetShowNotificator']))
                        {
                            if($_REQUEST['addWidgetShowNotificator'] == "1")
                            {
                               header("location: dashboard_configdash.php?openNotificator=1&dashId=" . $id_dashboard ."&widgetTitle=" . $title_widget);                 
                            }
                            else
                            {
                                header("location: dashboard_configdash.php");
                            }
                        }
                        else
                        {
                            header("location: dashboard_configdash.php");
                        }
                        
                        
                        if(isset($_REQUEST['addWidgetRegisterGen']))
                        {
                           if($_REQUEST['addWidgetRegisterGen'] == "yes")
                           {
                              $url = $notificatorUrl;
                              $genOriginalName = preg_replace('/\s+/', '+', $title_widget);
                              $genOriginalType = preg_replace('/\s+/', '+', $id_metric);
                              $containerName = preg_replace('/\s+/', '+', $dashboardName);
                              $appUsr = preg_replace('/\s+/', '+', $_SESSION['loggedUsername']); 
                              $containerUrl = $appUrl . "/view/index.php?iddasboard=" . base64_encode($id_dashboard); 
                              
                              $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventGenerator&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . "&url=" . $containerUrl;
                              $url = $url.$data;
                              
                              $options = array(
                                  'http' => array(
                                      'header'  => "Content-type: application/json\r\n",
                                      'method'  => 'POST'
                                      //'timeout' => 2
                                  )
                              );

                              try
                              {
                                 $context  = stream_context_create($options);
                                 $callResult = @file_get_contents($url, false, $context);
                                 
                                 //Questo if è temporaneo, allargalo via via che aggiungi widget in grado di generare eventi.
                                 if(($type_widget == 'widgetServerStatus')||($type_widget == 'widgetBarContent')||($type_widget == 'widgetColumnContent')||($type_widget == 'widgetGaugeChart')||($type_widget == 'widgetSingleContent')||($type_widget == 'widgetSpeedometer')||($type_widget == 'widgetTimeTrend')||($type_widget == 'widgetTimeTrendCompare'))
                                 {
                                    //Invio dei tipi di evento al notificatore
                                    $paramsObj = json_decode($parameters, true);
                                    if(count($paramsObj) > 0)
                                    {
                                       if(count($paramsObj["thresholdObject"] ) > 0)
                                       {
                                          foreach($paramsObj["thresholdObject"] as $eventType)
                                          {
                                             switch($eventType["op"])
                                             {
                                                case "less":
                                                   $eventName = "Value < " . $eventType["thr1"];
                                                   break;
                                                
                                                case "lessEqual":
                                                   $eventName = "Value <= " . $eventType["thr1"];
                                                   break;
                                                
                                                case "greater":
                                                   $eventName = "Value > " . $eventType["thr1"];
                                                   break;
                                                
                                                case "greaterEqual":
                                                   $eventName = "Value >= " . $eventType["thr1"];
                                                   break;
                                                
                                                case "equal":
                                                   $eventName = "Value = " . $eventType["thr1"];
                                                   break;
                                                
                                                case "notEqual":
                                                   $eventName = "Value != " . $eventType["thr1"];
                                                   break;
                                                
                                                case "intervalOpen":
                                                   $eventName = $eventType["thr1"] . " < value < " . $eventType["thr2"];
                                                   break;
                                                
                                                case "intervalClosed":
                                                   $eventName = $eventType["thr1"] . " <= value <= " . $eventType["thr2"];
                                                   break;
                                                
                                                case "intervalLeftOpen":
                                                   $eventName = $eventType["thr1"] . " < value <= " . $eventType["thr2"];
                                                   break;
                                                
                                                case "intervalRightOpen":
                                                   $eventName = $eventType["thr1"] . " <= value < " . $eventType["thr2"];
                                                   break;
                                             }

                                             if($eventType["desc"] != "")
                                             {
                                                $eventName = $eventName . " - " . $eventType["desc"];
                                             }

                                             $eventName = preg_replace('/\s+/', '+', $eventName);

                                             $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&eventType=' . $eventName . "&thrCnt=1";
                                             $url = $url.$data;

                                             $options = array(
                                                 'http' => array(
                                                     'header'  => "Content-type: application/json\r\n",
                                                     'method'  => 'POST'
                                                     //'timeout' => 2
                                                 )
                                             );

                                             try
                                             {
                                                $context  = stream_context_create($options);
                                                $callResult = @file_get_contents($url, false, $context);
                                             }
                                             catch (Exception $ex) 
                                             {
                                                //Non facciamo niente di specifico in caso di mancata risposta dell'host
                                             }
                                          }
                                       }
                                    }
                                 }
                              }
                              catch (Exception $ex) 
                              {
                                 //Non facciamo niente di specifico in caso di mancata risposta dell'host
                              }
                           }
                        }
                    } 
                    else 
                    {
                       if($type_widget == "widgetButton" && $fileUploaded)
                       {
                          unlink("../img/widgetButtonImages/" . $name_widget . "/image" . $extension);
                          rmdir("../img/widgetButtonImages/" . $name_widget);
                       }
                       
                        mysqli_close($link);
                        echo '<script type="text/javascript">';
                        echo 'alert("Error: Ripetere inserimento widget");';
                        echo 'window.location.href = "dashboard_configdash.php";';
                        echo '</script>';
                    }
            }
            else 
            {
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Error: Ripetere inserimento widget");';
                echo 'window.location.href = "dashboard_configdash.php";';
                echo '</script>';
            }
        /*} 
        else 
        {
            echo '<script type="text/javascript">';
            echo 'alert("Error: Nessuna metrica selezionata - ripetere inserimento widget");';
            echo 'window.location.href = "dashboard_configdash.php";';
            echo '</script>';
        }*/
    } //Fine caso add_widget
    else if(isset($_REQUEST['openDashboardToEdit']))//Escape 
    {
        session_start();
        $isAdmin = $_SESSION['loggedRole'];
        $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
        //$dashboardName = mysqli_real_escape_string($link, $_POST['selectedDashboardName']);
        //$dashboardAuthorName = mysqli_real_escape_string($link, $_POST['selectedDashboardAuthorName']);
        
        //Reperimento da DB del dashboardId e dell'id dell'autore della dashboard
        $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = $dashboardId";
        $result = mysqli_query($link, $query);

        if($result) 
        {
            if($result->num_rows > 0) 
            {
                while($row = mysqli_fetch_array($result)) 
                {
                    $_SESSION['dashboardId'] = $row['Id'];
                    $_SESSION['dashboardTitle'] = $row['name_dashboard'];
                    $_SESSION['dashboardAuthorName'] = $row['user'];
                }
            }
        } 
        mysqli_close($link);
        
        if(isset($_SESSION['loggedRole']))
        {
            if($_SESSION['loggedRole'] == "Manager")
            {
                //Utente non amministratore, edita una dashboard solo se ne é l'autore
                if((isset($_SESSION['loggedUsername']))&&(isset($_SESSION['dashboardId']))&&(isset($_SESSION['dashboardAuthorName']))&&($_SESSION['loggedUsername'] == $_SESSION['dashboardAuthorName']))
                {
                    header("location: dashboard_configdash.php");
                }
                else
                {
                    header("location: unauthorizedUser.php");
                }
            }
            else if(($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "ToolAdmin"))
            {
                //Utente amministratore, edita qualsiasi dashboard
                if((isset($_SESSION['loggedUsername']))&&(isset($_SESSION['dashboardId']))&&(isset($_SESSION['dashboardAuthorName'])))
                {
                    header("location: dashboard_configdash.php");
                }
                else
                {
                    header("location: unauthorizedUser.php");
                }
            }
        }
        else
        {
            header("location: unauthorizedUser.php");
        }
    } 
    else if (isset($_REQUEST['disable_dashboard']))//Escape 
    {
        if(isset($_SESSION['loggedRole']))
        {
            $username = $_SESSION['loggedUsername'];
            $dashboardName = mysqli_real_escape_string($link, $_POST['select-dashboard-disable']);
            $new_status_dashboard = 0;

            $updqDbtb2 = "UPDATE Dashboard.Config_dashboard SET status_dashboard = '$new_status_dashboard' WHERE name_dashboard = '$dashboardName' and user = '$username'";
            $result6 = mysqli_query($link, $updqDbtb2) or die(mysqli_error($link));

            if($result6) 
            {
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Disabilitazione dashboard avvenuta con successo");';
                echo 'window.location.href = "dashboard_mng.php";';
                echo '</script>';
            } 
            else 
            {
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Error: Ripetere disabilitazione dashboard");';
                echo 'window.location.href = "dashboard_mng.php";';
                echo '</script>';
            }
        }
    } 
    else if(isset($_REQUEST['modify_widget'])) 
    {
        session_start();
        $dashboardName2 = $_SESSION['dashboardTitle'];
        $widgetIdM = $_REQUEST['widgetIdM'];
        if($_REQUEST['widgetCategoryHiddenM'] == "actuator")
        {
            $actuatorEntity = mysqli_real_escape_string($link, $_POST['widgetEntityM']);
            $actuatorAttribute = mysqli_real_escape_string($link, $_POST['widgetAttributeM']);
            $type_widget_m = mysqli_real_escape_string($link, $_POST['widgetActuatorTypeM']);
            $name_widget_m = preg_replace('/\+/', '', $actuatorEntity) . "_" . $_SESSION['dashboardId'] . "_" . $type_widget_m . $widgetIdM;
        }
        else
        {
            $type_widget_m = mysqli_real_escape_string($link, $_POST['select-widget-m']);
            $name_widget_m = $_POST['inputNameWidgetM'];
        }
        
        $title_widget_m = NULL;
        $color_widget_m = mysqli_real_escape_string($link, $_POST['inputColorWidgetM']); 
        $freq_widget_m = NULL;
        $info_m = mysqli_real_escape_string($link, $_POST['widgetInfoEditorM']); 
        $col_m = mysqli_real_escape_string($link, $_POST['inputColumn-m']); 
        $row_m = mysqli_real_escape_string($link, $_POST['inputRows-m']); 
        $color_frame_m = NULL;
        $controlsVisibility = NULL;
        $zoomFactor = NULL; //Lo si edita solo dai controlli grafici, non dal form
        $controlsPosition = NULL;
        $showTitle = NULL;
        $inputDefaultTabM = NULL;
        $zoomControlsColorM = NULL;
        $headerFontColorM = NULL;
        $parametersM = NULL;
        $styleParametersM = NULL;
        $showTableFirstCellM = NULL;
        $tableFirstCellFontSizeM = NULL;
        $tableFirstCellFontColorM = NULL;
        $rowsLabelsFontSizeM = NULL;
        $rowsLabelsFontColorM = NULL;
        $colsLabelsFontSizeM = NULL;
        $colsLabelsFontColorM = NULL;
        $rowsLabelsBckColorM = NULL;
        $colsLabelsBckColorM = NULL;
        $tableBordersM = NULL;
        $tableBordersColorM = NULL;
        $infoJsonObjectM = NULL;
        $infoJsonM = NULL;
        $legendFontSizeM = NULL;
        $legendFontColorM = NULL;
        $dataLabelsFontSizeM = NULL;
        $dataLabelsFontColorM = NULL;
        $barsColorsSelectM  = NULL;
        $chartTypeM = NULL;
        $dataLabelsDistanceM = NULL;
        $dataLabelsDistance1M = NULL;
        $dataLabelsDistance2M = NULL;
        $dataLabelsM = NULL;
        $dataLabelsRotationM = NULL;
        $xAxisDatasetM = NULL;
        $lineWidthM = NULL;
        $alrLookM = NULL;
        $colorsSelectM = NULL;
        $colorsSelect2M = NULL;
        $colorsM = NULL;
        $colors2M = NULL;
        $innerRadius1M = NULL;
        $outerRadius1M = NULL;
        $innerRadius2M = NULL;
        $startAngle = NULL;
        $endAngle = NULL;
        $centerY = NULL;
        $gridLinesWidthM = NULL;
        $gridLinesColorM = NULL;
        $linesWidthM = NULL;
        $alrThrLinesWidthM = NULL;
        $clockDataM = NULL;
        $clockFontM = NULL;
        $enableFullscreenTabM = 'no';
        $enableFullscreenModalM = 'no';
        $fontFamily = mysqli_real_escape_string($link, $_REQUEST['inputFontFamilyWidgetM']);
        
        if($type_widget_m == "widgetPrevMeteo")
        {
            $styleParametersArrayM = array('orientation' => $_REQUEST['orientationM'], 'language' => $_REQUEST['languageM'], 'todayDim' => $_REQUEST['todayDimM'], 'backgroundMode' => $_REQUEST['backgroundModeM'], 'iconSet' => $_REQUEST['iconSetM']);
            $styleParametersM = json_encode($styleParametersArrayM);
        }
        
        if($type_widget_m == "widgetSelector")
        {
          if(isset($_REQUEST['editGisRectDim']))
          {
             $rectDim = $_REQUEST['editGisRectDim'];
          }

          $styleParametersArrayM = array('rectDim' => $rectDim, 'activeFontColor' => $_REQUEST['editGisActiveQueriesFontColor']);
          $styleParametersM = json_encode($styleParametersArrayM);
        }
        
        if($type_widget_m == "widgetClock")
        {
           if(isset($_REQUEST['editWidgetClockData']))
           {
              $clockDataM = $_REQUEST['editWidgetClockData'];
           }

           if(isset($_REQUEST['editWidgetClockFont']))
           {
              $clockFontM = $_REQUEST['editWidgetClockFont'];
           }

           $styleParametersArrayM = array('clockData' => $clockDataM, 'clockFont' => $clockFontM);
           $styleParametersM = json_encode($styleParametersArrayM);
        }
        
        
         if($type_widget_m == "widgetProtezioneCivile")
         {
           if(isset($_POST['meteoTabFontSizeM']) && ($_POST['meteoTabFontSizeM'] != "") && ($_POST['meteoTabFontSizeM'] != null))
           {
               $meteoTabFontSize = mysqli_real_escape_string($link, $_POST['meteoTabFontSizeM']);
           }

           if(isset($_POST['genTabFontSizeM']) && ($_POST['genTabFontSizeM'] != "") && ($_POST['genTabFontSizeM'] != null))
           {
               $genTabFontSize = mysqli_real_escape_string($link, $_POST['genTabFontSizeM']);
           }

           if(isset($_POST['genTabFontColorM']) && ($_POST['genTabFontColorM'] != "") && ($_POST['genTabFontColorM'] != null))
           {
               $genTabFontColor = mysqli_real_escape_string($link, $_POST['genTabFontColorM']);
           }

           $styleParametersArrayM = array('meteoTabFontSize' => $meteoTabFontSize, 'genTabFontSize' => $genTabFontSize, 'genTabFontColor' => $genTabFontColor);
           $styleParametersM = json_encode($styleParametersArrayM);
         }

        if($type_widget_m == "widgetFirstAid")
        {
            $infoNamesJsonFirstAxisM = json_decode($_POST['infoNamesJsonFirstAxisM']);
            $infoNamesJsonSecondAxisM = json_decode($_POST['infoNamesJsonSecondAxisM']);
            $infoJsonObjectM = [];
            $infoJsonFirstAxisM = [];
            $infoJsonSecondAxisM = [];

            foreach ($infoNamesJsonFirstAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonFirstAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonSecondAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            $infoJsonObjectM["firstAxis"] = $infoJsonFirstAxisM;
            $infoJsonObjectM["secondAxis"] = $infoJsonSecondAxisM;

            $infoJsonM = json_encode($infoJsonObjectM);

            if(isset($_POST['showTableFirstCellM'])&&($_POST['showTableFirstCellM']!=""))
            {
                $showTableFirstCellM = mysqli_real_escape_string($link, $_POST['showTableFirstCellM']);
            }

            if(isset($_POST['tableFirstCellFontSizeM'])&&($_POST['tableFirstCellFontSizeM']!=""))
            {
                $tableFirstCellFontSizeM = mysqli_real_escape_string($link, $_POST['tableFirstCellFontSizeM']);
            }

            if(isset($_POST['tableFirstCellFontColorM'])&&($_POST['tableFirstCellFontColorM']!=""))
            {
                $tableFirstCellFontColorM = mysqli_real_escape_string($link, $_POST['tableFirstCellFontColorM']);
            }

            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSizeM']);
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColorM']);
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['colsLabelsFontSizeM']);
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['colsLabelsFontColorM']);
            }

            if(isset($_POST['rowsLabelsBckColorM'])&&($_POST['rowsLabelsBckColorM']!=""))
            {
                $rowsLabelsBckColorM = mysqli_real_escape_string($link, $_POST['rowsLabelsBckColorM']);
            }
            
            if(isset($_POST['tableBordersM'])&&($_POST['tableBordersM']!=""))
            {
                $tableBordersM = mysqli_real_escape_string($link, $_POST['tableBordersM']);
            }

            if(isset($_POST['tableBordersColorM'])&&($_POST['tableBordersColorM']!=""))
            {
                $tableBordersColorM = mysqli_real_escape_string($link, $_POST['tableBordersColorM']);
            }

            $styleParametersArrayM = array('showTableFirstCell' => $showTableFirstCellM, 'tableFirstCellFontSize' => $tableFirstCellFontSizeM, 'tableFirstCellFontColor' => $tableFirstCellFontColorM, 'rowsLabelsFontSize' => $rowsLabelsFontSizeM, 'rowsLabelsFontColor' => $rowsLabelsFontColorM, 'colsLabelsFontSize' => $colsLabelsFontSizeM, 'colsLabelsFontColor' => $colsLabelsFontColorM, 'rowsLabelsBckColor' => $rowsLabelsBckColorM, 'colsLabelsBckColor' => $colsLabelsBckColorM, 'tableBorders' => $tableBordersM, 'tableBordersColor' => $tableBordersColorM);
            $styleParametersM = json_encode($styleParametersArrayM);
        }
        
        
        if($type_widget_m == "widgetPieChart")
        {
            if(isset($_POST['infoNamesJsonFirstAxisM'])&&($_POST['infoNamesJsonFirstAxisM']!="")&&(isset($_POST['infoNamesJsonSecondAxisM']))&&($_POST['infoNamesJsonSecondAxisM']!=""))
            {
                $infoNamesJsonFirstAxisM = json_decode($_POST['infoNamesJsonFirstAxisM']);
                $infoNamesJsonSecondAxisM = json_decode($_POST['infoNamesJsonSecondAxisM']);
                $infoJsonObjectM = [];
                $infoJsonFirstAxisM = [];
                $infoJsonSecondAxisM = [];

                foreach ($infoNamesJsonFirstAxisM as $nameM) 
                {
                    //Hack per metriche contenenti indirizzi IP
                    $nameM = preg_replace('/\./', "_", $nameM);
                    $infoJsonFirstAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
                }
                unset($nameM);

                foreach ($infoNamesJsonSecondAxisM as $nameM) 
                {
                    //Hack per metriche contenenti indirizzi IP
                    $nameM = preg_replace('/\./', "_", $nameM);
                    $infoJsonSecondAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
                }
                unset($nameM);

                $infoJsonObjectM["firstAxis"] = $infoJsonFirstAxisM;
                $infoJsonObjectM["secondAxis"] = $infoJsonSecondAxisM;

                $infoJsonM = json_encode($infoJsonObjectM);
            }

            if(isset($_POST['legendFontSizeM'])&&($_POST['legendFontSizeM']!=""))
            {
                $legendFontSizeM = mysqli_real_escape_string($link, $_POST['legendFontSizeM']);
            }

            if(isset($_POST['legendFontColorPickerM'])&&($_POST['legendFontColorPickerM']!=""))
            {
                $legendFontColorM = mysqli_real_escape_string($link, $_POST['legendFontColorPickerM']);
            }

            if(isset($_POST['dataLabelsDistanceM'])&&($_POST['dataLabelsDistanceM']!=""))
            {
                $dataLabelsDistanceM = mysqli_real_escape_string($link, $_POST['dataLabelsDistanceM']);
            }

            if(isset($_POST['dataLabelsDistance1M'])&&($_POST['dataLabelsDistance1M']!=""))
            {
                $dataLabelsDistance1M = mysqli_real_escape_string($link, $_POST['dataLabelsDistance1M']);
            }

            if(isset($_POST['dataLabelsDistance2M'])&&($_POST['dataLabelsDistance2M']!=""))
            {
                $dataLabelsDistance2M = mysqli_real_escape_string($link, $_POST['dataLabelsDistance2M']);
            }

            if(isset($_POST['dataLabelsM'])&&($_POST['dataLabelsM']!=""))
            {
                $dataLabelsM = mysqli_real_escape_string($link, $_POST['dataLabelsM']);
            }

            if(isset($_POST['dataLabelsFontSizeM'])&&($_POST['dataLabelsFontSizeM']!=""))
            {
                $dataLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['dataLabelsFontSizeM']);
            }

            if(isset($_POST['dataLabelsFontColorM'])&&($_POST['dataLabelsFontColorM']!=""))
            {
                $dataLabelsFontColorM = mysqli_real_escape_string($link, $_POST['dataLabelsFontColorM']);
            }

            if(isset($_POST['innerRadius1M'])&&($_POST['innerRadius1M']!=""))
            {
                $innerRadius1M = mysqli_real_escape_string($link, $_POST['innerRadius1M']);
            }

            if(isset($_POST['startAngleM'])&&($_POST['startAngleM']!=""))
            {
                $startAngleM = mysqli_real_escape_string($link, $_POST['startAngleM']);
            }

            if(isset($_POST['endAngleM'])&&($_POST['endAngleM']!=""))
            {
                $endAngleM = mysqli_real_escape_string($link, $_POST['endAngleM']);
            }

            if(isset($_POST['centerYM'])&&($_POST['centerYM']!=""))
            {
                $centerYM = mysqli_real_escape_string($link, $_POST['centerYM']);
            }

            if(isset($_POST['outerRadius1M'])&&($_POST['outerRadius1M']!=""))
            {
                $outerRadius1M = mysqli_real_escape_string($link, $_POST['outerRadius1M']);
            }

            if(isset($_POST['innerRadius2M'])&&($_POST['innerRadius2M']!=""))
            {
                $innerRadius2M = mysqli_real_escape_string($link, $_POST['innerRadius2M']);
            }

            if(isset($_POST['colorsSelect1M'])&&($_POST['colorsSelect1M']!=""))
            {
                $colorsSelect1M = mysqli_real_escape_string($link, $_POST['colorsSelect1M']);
            }

            if(isset($_POST['colors1M'])&&($_POST['colors1M']!=""))
            {
                $temp = json_decode($_POST['colors1M']);
                $colors1M = [];
                foreach ($temp as $color) 
                {
                    array_push($colors1M, $color);
                }
            }

            if(isset($_POST['colorsSelect2M'])&&($_POST['colorsSelect2M']!=""))
            {
                $colorsSelect2M = $_POST['colorsSelect2M'];
            }

            if(isset($_POST['colors2M'])&&($_POST['colors2M']!=""))
            {
                $temp = json_decode($_POST['colors2M']);
                $colors2M = [];
                foreach ($temp as $color) 
                {
                    array_push($colors2M, $color);
                }
            }

            $styleParametersArrayM = array();
            $styleParametersArrayM['legendFontSize'] = $legendFontSizeM;
            $styleParametersArrayM['legendFontColor'] = $legendFontColorM;
            $styleParametersArrayM['dataLabelsDistance'] = $dataLabelsDistanceM;
            $styleParametersArrayM['dataLabelsDistance1'] = $dataLabelsDistance1M;
            $styleParametersArrayM['dataLabelsDistance2'] = $dataLabelsDistance2M;
            $styleParametersArrayM['dataLabels'] = $dataLabelsM;
            $styleParametersArrayM['dataLabelsFontSize'] = $dataLabelsFontSizeM;
            $styleParametersArrayM['dataLabelsFontColor'] = $dataLabelsFontColorM;
            $styleParametersArrayM['innerRadius1'] = $innerRadius1M;
            $styleParametersArrayM['startAngle'] = $startAngleM;
            $styleParametersArrayM['endAngle'] = $endAngleM;
            $styleParametersArrayM['centerY'] = $centerYM;
            $styleParametersArrayM['outerRadius1'] = $outerRadius1M;
            $styleParametersArrayM['innerRadius2'] = $innerRadius2M;
            $styleParametersArrayM['colorsSelect1'] = $colorsSelect1M;
            $styleParametersArrayM['colors1'] = $colors1M;
            $styleParametersArrayM['colorsSelect2'] = $colorsSelect2M;
            $styleParametersArrayM['colors2'] = $colors2M;
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if(($type_widget_m == "widgetLineSeries") || ($type_widget_m == "widgetCurvedLineSeries"))
        {
            $infoNamesJsonFirstAxisM = json_decode($_POST['infoNamesJsonFirstAxisM']);
            $infoNamesJsonSecondAxisM = json_decode($_POST['infoNamesJsonSecondAxisM']);
            $infoJsonObjectM = [];
            $infoJsonFirstAxisM = [];
            $infoJsonSecondAxisM = [];

            foreach ($infoNamesJsonFirstAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonFirstAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonSecondAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            $infoJsonObjectM["firstAxis"] = $infoJsonFirstAxisM;
            $infoJsonObjectM["secondAxis"] = $infoJsonSecondAxisM;

            $infoJsonM = json_encode($infoJsonObjectM);

            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSizeM']);
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColorM']);
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['colsLabelsFontSizeM']);
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['colsLabelsFontColorM']);
            }

            if(isset($_POST['dataLabelsFontSizeM'])&&($_POST['dataLabelsFontSizeM']!=""))
            {
                $dataLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['dataLabelsFontSizeM']);
            }

            if(isset($_POST['dataLabelsFontColorM'])&&($_POST['dataLabelsFontColorM']!=""))
            {
                $dataLabelsFontColorM = mysqli_real_escape_string($link, $_POST['dataLabelsFontColorM']);
            }

            if(isset($_POST['legendFontSizeM'])&&($_POST['legendFontSizeM']!=""))
            {
                $legendFontSizeM = mysqli_real_escape_string($link, $_POST['legendFontSizeM']);
            }

            if(isset($_POST['legendFontColorM'])&&($_POST['legendFontColorM']!=""))
            {
                $legendFontColorM = mysqli_real_escape_string($link, $_POST['legendFontColorM']);
            }

            if(isset($_POST['barsColorsSelectM'])&&($_POST['barsColorsSelectM']!=""))
            {
                $barsColorsSelectM = mysqli_real_escape_string($link, $_POST['barsColorsSelectM']);
            }

            if(isset($_POST['chartTypeM'])&&($_POST['chartTypeM']!=""))
            {
                $chartTypeM = mysqli_real_escape_string($link, $_POST['chartTypeM']);
            }

            if(isset($_POST['dataLabelsM'])&&($_POST['dataLabelsM']!=""))
            {
                $dataLabelsM = mysqli_real_escape_string($link, $_POST['dataLabelsM']);
            }

            if(isset($_POST['xAxisDatasetM'])&&($_POST['xAxisDatasetM']!=""))
            {
                $xAxisDatasetM = $_POST['xAxisDatasetM'];
            }

            if(isset($_POST['lineWidthM'])&&($_POST['lineWidthM']!=""))
            {
                $lineWidthM = $_POST['lineWidthM'];
            }

            if(isset($_POST['alrLookM'])&&($_POST['alrLookM']!=""))
            {
                $alrLookM = $_POST['alrLookM'];
            }

            $styleParametersArrayM = array();
            $styleParametersArrayM['rowsLabelsFontSize'] = $rowsLabelsFontSizeM;
            $styleParametersArrayM['rowsLabelsFontColor'] = $rowsLabelsFontColorM;
            $styleParametersArrayM['colsLabelsFontSize'] = $colsLabelsFontSizeM;
            $styleParametersArrayM['colsLabelsFontColor'] = $colsLabelsFontColorM;
            $styleParametersArrayM['dataLabelsFontSize'] = $dataLabelsFontSizeM;
            $styleParametersArrayM['dataLabelsFontColor'] = $dataLabelsFontColorM;
            $styleParametersArrayM['legendFontSize'] = $legendFontSizeM;
            $styleParametersArrayM['legendFontColor'] = $legendFontColorM;
            $styleParametersArrayM['barsColorsSelect'] = $barsColorsSelectM;
            $styleParametersArrayM['chartType'] = $chartTypeM;
            $styleParametersArrayM['dataLabels'] = $dataLabelsM;
            //$styleParametersArrayM['dataLabelsRotation'] = $dataLabelsRotationM;
            $styleParametersArrayM['xAxisDataset'] = $xAxisDatasetM;
            $styleParametersArrayM['lineWidth'] = $lineWidthM;
            $styleParametersArrayM['alrLook'] = $alrLookM;


            if(isset($_POST['barsColorsM'])&&($_POST['barsColorsM']!=""))
            {
                $temp = json_decode($_POST['barsColorsM']);
                $barsColorsM = [];
                foreach ($temp as $color) 
                {
                    array_push($barsColorsM, $color);
                }
            }

            $styleParametersArrayM['barsColors'] = $barsColorsM;
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if($type_widget_m == "widgetScatterSeries")
        {
            $infoNamesJsonFirstAxisM = json_decode($_POST['infoNamesJsonFirstAxisM']);
            $infoNamesJsonSecondAxisM = json_decode($_POST['infoNamesJsonSecondAxisM']);
            $infoJsonObjectM = [];
            $infoJsonFirstAxisM = [];
            $infoJsonSecondAxisM = [];

            foreach ($infoNamesJsonFirstAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonFirstAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonSecondAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            $infoJsonObjectM["firstAxis"] = $infoJsonFirstAxisM;
            $infoJsonObjectM["secondAxis"] = $infoJsonSecondAxisM;

            $infoJsonM = json_encode($infoJsonObjectM);

            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSizeM']);
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColorM']);
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['colsLabelsFontSizeM']);
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['colsLabelsFontColorM']);
            }

            if(isset($_POST['dataLabelsFontSizeM'])&&($_POST['dataLabelsFontSizeM']!=""))
            {
                $dataLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['dataLabelsFontSizeM']);
            }

            if(isset($_POST['dataLabelsFontColorM'])&&($_POST['dataLabelsFontColorM']!=""))
            {
                $dataLabelsFontColorM = mysqli_real_escape_string($link, $_POST['dataLabelsFontColorM']);
            }

            if(isset($_POST['legendFontSizeM'])&&($_POST['legendFontSizeM']!=""))
            {
                $legendFontSizeM = mysqli_real_escape_string($link, $_POST['legendFontSizeM']);
            }

            if(isset($_POST['legendFontColorM'])&&($_POST['legendFontColorM']!=""))
            {
                $legendFontColorM = mysqli_real_escape_string($link, $_POST['legendFontColorM']);
            }

            if(isset($_POST['barsColorsSelectM'])&&($_POST['barsColorsSelectM']!=""))
            {
                $barsColorsSelectM = mysqli_real_escape_string($link, $_POST['barsColorsSelectM']);
            }

            if(isset($_POST['chartTypeM'])&&($_POST['chartTypeM']!=""))
            {
                $chartTypeM = mysqli_real_escape_string($link, $_POST['chartTypeM']);
            }

            if(isset($_POST['dataLabelsM'])&&($_POST['dataLabelsM']!=""))
            {
                $dataLabelsM = mysqli_real_escape_string($link, $_POST['dataLabelsM']);
            }

            if(isset($_POST['dataLabelsRotationM'])&&($_POST['dataLabelsRotationM']!=""))
            {
                $dataLabelsRotationM = mysqli_real_escape_string($link, $_POST['dataLabelsRotationM']);
            }

            $styleParametersArrayM = array();
            $styleParametersArrayM['rowsLabelsFontSize'] = $rowsLabelsFontSizeM;
            $styleParametersArrayM['rowsLabelsFontColor'] = $rowsLabelsFontColorM;
            $styleParametersArrayM['colsLabelsFontSize'] = $colsLabelsFontSizeM;
            $styleParametersArrayM['colsLabelsFontColor'] = $colsLabelsFontColorM;
            $styleParametersArrayM['dataLabelsFontSize'] = $dataLabelsFontSizeM;
            $styleParametersArrayM['dataLabelsFontColor'] = $dataLabelsFontColorM;
            $styleParametersArrayM['legendFontSize'] = $legendFontSizeM;
            $styleParametersArrayM['legendFontColor'] = $legendFontColorM;
            $styleParametersArrayM['barsColorsSelect'] = $barsColorsSelectM;
            $styleParametersArrayM['chartType'] = $chartTypeM;
            $styleParametersArrayM['dataLabels'] = $dataLabelsM;
            $styleParametersArrayM['dataLabelsRotation'] = $dataLabelsRotationM;

            if(isset($_POST['barsColorsM'])&&($_POST['barsColorsM']!=""))
            {
                $temp = json_decode($_POST['barsColorsM']);
                $barsColorsM = [];
                foreach ($temp as $color) 
                {
                    array_push($barsColorsM, $color);
                }
            }

            $styleParametersArrayM['barsColors'] = $barsColorsM;
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if($type_widget_m == "widgetBarSeries")
        {
            $infoNamesJsonFirstAxisM = json_decode($_POST['infoNamesJsonFirstAxisM']);
            $infoNamesJsonSecondAxisM = json_decode($_POST['infoNamesJsonSecondAxisM']);
            $infoJsonObjectM = [];
            $infoJsonFirstAxisM = [];
            $infoJsonSecondAxisM = [];

            foreach ($infoNamesJsonFirstAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonFirstAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonSecondAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            $infoJsonObjectM["firstAxis"] = $infoJsonFirstAxisM;
            $infoJsonObjectM["secondAxis"] = $infoJsonSecondAxisM;

            $infoJsonM = json_encode($infoJsonObjectM);

            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSizeM']);
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColorM']);
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['colsLabelsFontSizeM']);
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['colsLabelsFontColorM']);
            }

            if(isset($_POST['dataLabelsFontSizeM'])&&($_POST['dataLabelsFontSizeM']!=""))
            {
                $dataLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['dataLabelsFontSizeM']);
            }

            if(isset($_POST['dataLabelsFontColorM'])&&($_POST['dataLabelsFontColorM']!=""))
            {
                $dataLabelsFontColorM = mysqli_real_escape_string($link, $_POST['dataLabelsFontColorM']);
            }

            if(isset($_POST['legendFontSizeM'])&&($_POST['legendFontSizeM']!=""))
            {
                $legendFontSizeM = mysqli_real_escape_string($link, $_POST['legendFontSizeM']);
            }

            if(isset($_POST['legendFontColorM'])&&($_POST['legendFontColorM']!=""))
            {
                $legendFontColorM = mysqli_real_escape_string($link, $_POST['legendFontColorM']);
            }

            if(isset($_POST['barsColorsSelectM'])&&($_POST['barsColorsSelectM']!=""))
            {
                $barsColorsSelectM = mysqli_real_escape_string($link, $_POST['barsColorsSelectM']);
            }

            if(isset($_POST['chartTypeM'])&&($_POST['chartTypeM']!=""))
            {
                $chartTypeM = mysqli_real_escape_string($link, $_POST['chartTypeM']);
            }

            if(isset($_POST['dataLabelsM'])&&($_POST['dataLabelsM']!=""))
            {
                $dataLabelsM = mysqli_real_escape_string($link, $_POST['dataLabelsM']);
            }

            if(isset($_POST['dataLabelsRotationM'])&&($_POST['dataLabelsRotationM']!=""))
            {
                $dataLabelsRotationM = mysqli_real_escape_string($link, $_POST['dataLabelsRotationM']);
            }

            $styleParametersArrayM = array();
            $styleParametersArrayM['rowsLabelsFontSize'] = $rowsLabelsFontSizeM;
            $styleParametersArrayM['rowsLabelsFontColor'] = $rowsLabelsFontColorM;
            $styleParametersArrayM['colsLabelsFontSize'] = $colsLabelsFontSizeM;
            $styleParametersArrayM['colsLabelsFontColor'] = $colsLabelsFontColorM;
            $styleParametersArrayM['dataLabelsFontSize'] = $dataLabelsFontSizeM;
            $styleParametersArrayM['dataLabelsFontColor'] = $dataLabelsFontColorM;
            $styleParametersArrayM['legendFontSize'] = $legendFontSizeM;
            $styleParametersArrayM['legendFontColor'] = $legendFontColorM;
            $styleParametersArrayM['barsColorsSelect'] = $barsColorsSelectM;
            $styleParametersArrayM['chartType'] = $chartTypeM;
            $styleParametersArrayM['dataLabels'] = $dataLabelsM;
            $styleParametersArrayM['dataLabelsRotation'] = $dataLabelsRotationM;

            if(isset($_POST['barsColorsM'])&&($_POST['barsColorsM']!=""))
            {
                $temp = json_decode($_POST['barsColorsM']);
                $barsColorsM = [];
                foreach ($temp as $color) 
                {
                    array_push($barsColorsM, $color);
                }
            }

            $styleParametersArrayM['barsColors'] = $barsColorsM;
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if($type_widget_m == "widgetRadarSeries")
        {
            $infoNamesJsonFirstAxisM = json_decode($_POST['infoNamesJsonFirstAxisM']);
            $infoNamesJsonSecondAxisM = json_decode($_POST['infoNamesJsonSecondAxisM']);
            $infoJsonObjectM = [];
            $infoJsonFirstAxisM = [];
            $infoJsonSecondAxisM = [];

            foreach($infoNamesJsonFirstAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonFirstAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonSecondAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            $infoJsonObjectM["firstAxis"] = $infoJsonFirstAxisM;
            $infoJsonObjectM["secondAxis"] = $infoJsonSecondAxisM;

            $infoJsonM = json_encode($infoJsonObjectM);

            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSizeM']);
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColorM']);
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['colsLabelsFontSizeM']);
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['colsLabelsFontColorM']);
            }

            if(isset($_POST['dataLabelsFontSizeM'])&&($_POST['dataLabelsFontSizeM']!=""))
            {
                $dataLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['dataLabelsFontSizeM']);
            }

            if(isset($_POST['dataLabelsFontColorM'])&&($_POST['dataLabelsFontColorM']!=""))
            {
                $dataLabelsFontColorM = mysqli_real_escape_string($link, $_POST['dataLabelsFontColorM']);
            }

            if(isset($_POST['legendFontSizeM'])&&($_POST['legendFontSizeM']!=""))
            {
                $legendFontSizeM = mysqli_real_escape_string($link, $_POST['legendFontSizeM']);
            }

            if(isset($_POST['legendFontColorM'])&&($_POST['legendFontColorM']!=""))
            {
                $legendFontColorM = mysqli_real_escape_string($link, $_POST['legendFontColorM']);
            }

            if(isset($_POST['gridLinesWidthM'])&&($_POST['gridLinesWidthM']!=""))
            {
                $gridLinesWidthM = $_POST['gridLinesWidthM'];
            }

            if(isset($_POST['gridLinesColorM'])&&($_POST['gridLinesColorM']!=""))
            {
                $gridLinesColorM = $_POST['gridLinesColorM'];
            }

            if(isset($_POST['linesWidthM'])&&($_POST['linesWidthM']!=""))
            {
                $linesWidthM = $_POST['linesWidthM'];
            }

            if(isset($_POST['barsColorsSelectM'])&&($_POST['barsColorsSelectM']!=""))
            {
                $barsColorsSelectM = mysqli_real_escape_string($link, $_POST['barsColorsSelectM']);
            }

            if(isset($_POST['alrThrLinesWidthM'])&&($_POST['alrThrLinesWidthM']!=""))
            {
                $alrThrLinesWidthM = mysqli_real_escape_string($link, $_POST['alrThrLinesWidthM']);
            }

            if(isset($_POST['dataLabelsM'])&&($_POST['dataLabelsM']!=""))
            {
                $dataLabelsM = mysqli_real_escape_string($link, $_POST['dataLabelsM']);
            }

            if(isset($_POST['dataLabelsRotationM'])&&($_POST['dataLabelsRotationM']!=""))
            {
                $dataLabelsRotationM = mysqli_real_escape_string($link, $_POST['dataLabelsRotationM']);
            }

            $styleParametersArrayM = array();
            $styleParametersArrayM['rowsLabelsFontSize'] = $rowsLabelsFontSizeM;
            $styleParametersArrayM['rowsLabelsFontColor'] = $rowsLabelsFontColorM;
            $styleParametersArrayM['colsLabelsFontSize'] = $colsLabelsFontSizeM;
            $styleParametersArrayM['colsLabelsFontColor'] = $colsLabelsFontColorM;
            $styleParametersArrayM['dataLabelsFontSize'] = $dataLabelsFontSizeM;
            $styleParametersArrayM['dataLabelsFontColor'] = $dataLabelsFontColorM;
            $styleParametersArrayM['legendFontSize'] = $legendFontSizeM;
            $styleParametersArrayM['legendFontColor'] = $legendFontColorM;
            $styleParametersArrayM['gridLinesWidth'] = $gridLinesWidthM;
            $styleParametersArrayM['gridLinesColor'] = $gridLinesColorM;
            $styleParametersArrayM['linesWidth'] = $linesWidthM;
            $styleParametersArrayM['barsColorsSelect'] = $barsColorsSelectM;
            $styleParametersArrayM['alrThrLinesWidth'] = $alrThrLinesWidthM;
            $styleParametersArrayM['dataLabels'] = $dataLabelsM;
            $styleParametersArrayM['dataLabelsRotation'] = $dataLabelsRotationM;

            if(isset($_POST['barsColorsM'])&&($_POST['barsColorsM']!=""))
            {
                $temp = json_decode($_POST['barsColorsM']);
                $barsColorsM = [];
                foreach ($temp as $color) 
                {
                    array_push($barsColorsM, $color);
                }
            }

            $styleParametersArrayM['barsColors'] = $barsColorsM;
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if($type_widget_m == "widgetTable")
        {
            $infoNamesJsonFirstAxisM = json_decode($_POST['infoNamesJsonFirstAxisM']);
            $infoNamesJsonSecondAxisM = json_decode($_POST['infoNamesJsonSecondAxisM']);
            $infoJsonObjectM = [];
            $infoJsonFirstAxisM = [];
            $infoJsonSecondAxisM = [];

            foreach ($infoNamesJsonFirstAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonFirstAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                //Hack per metriche contenenti indirizzi IP
                $nameM = preg_replace('/\./', "_", $nameM);
                $infoJsonSecondAxisM[preg_replace('/_/', ".", $nameM)] = $_POST[$nameM];
            }
            unset($nameM);

            $infoJsonObjectM["firstAxis"] = $infoJsonFirstAxisM;
            $infoJsonObjectM["secondAxis"] = $infoJsonSecondAxisM;

            $infoJsonM = json_encode($infoJsonObjectM);

            if(isset($_POST['showTableFirstCellM'])&&($_POST['showTableFirstCellM']!=""))
            {
                $showTableFirstCellM = mysqli_real_escape_string($link, $_POST['showTableFirstCellM']);
            }

            if(isset($_POST['tableFirstCellFontSizeM'])&&($_POST['tableFirstCellFontSizeM']!=""))
            {
                $tableFirstCellFontSizeM = mysqli_real_escape_string($link, $_POST['tableFirstCellFontSizeM']);
            }

            if(isset($_POST['tableFirstCellFontColorM'])&&($_POST['tableFirstCellFontColorM']!=""))
            {
                $tableFirstCellFontColorM = mysqli_real_escape_string($link, $_POST['tableFirstCellFontColorM']);
            }

            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontSizeM']);
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['rowsLabelsFontColorM']);
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, $_POST['colsLabelsFontSizeM']);
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, $_POST['colsLabelsFontColorM']);
            }

            if(isset($_POST['rowsLabelsBckColorM'])&&($_POST['rowsLabelsBckColorM']!=""))
            {
                $rowsLabelsBckColorM = mysqli_real_escape_string($link, $_POST['rowsLabelsBckColorM']);
            }

            if(isset($_POST['colsLabelsBckColorM'])&&($_POST['colsLabelsBckColorM']!=""))
            {
                $colsLabelsBckColorM = mysqli_real_escape_string($link, $_POST['colsLabelsBckColorM']);
            }

            if(isset($_POST['tableBordersM'])&&($_POST['tableBordersM']!=""))
            {
                $tableBordersM = mysqli_real_escape_string($link, $_POST['tableBordersM']);
            }

            if(isset($_POST['tableBordersColorM'])&&($_POST['tableBordersColorM']!=""))
            {
                $tableBordersColorM = mysqli_real_escape_string($link, $_POST['tableBordersColorM']);
            }

            $styleParametersArrayM = array('showTableFirstCell' => $showTableFirstCellM, 'tableFirstCellFontSize' => $tableFirstCellFontSizeM, 'tableFirstCellFontColor' => $tableFirstCellFontColorM, 'rowsLabelsFontSize' => $rowsLabelsFontSizeM, 'rowsLabelsFontColor' => $rowsLabelsFontColorM, 'colsLabelsFontSize' => $colsLabelsFontSizeM, 'colsLabelsFontColor' => $colsLabelsFontColorM, 'rowsLabelsBckColor' => $rowsLabelsBckColorM, 'colsLabelsBckColor' => $colsLabelsBckColorM, 'tableBorders' => $tableBordersM, 'tableBordersColor' => $tableBordersColorM);
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if(isset($_POST['inputHeaderFontColorWidgetM']) && ($_POST['inputHeaderFontColorWidgetM']!=""))
        {
            $headerFontColorM = mysqli_real_escape_string($link, $_POST['inputHeaderFontColorWidgetM']);
        }

        if(isset($_POST['inputControlsVisibilityM']) && ($_POST['inputControlsVisibilityM']!=""))
        {
            $controlsVisibility = mysqli_real_escape_string($link, $_POST['inputControlsVisibilityM']);
        }

        //MANCA ZOOM FACTOR PERCHE' DEV'ESSERE EDITATO SOLO DAI CONTROLLI GRAFICI, NON DAL FORM

        if(isset($_POST['inputControlsPositionM']) && ($_POST['inputControlsPositionM']!=""))
        {
            $controlsPosition = mysqli_real_escape_string($link, $_POST['inputControlsPositionM']);
        }

        if(isset($_POST['inputShowTitleM']) && ($_POST['inputShowTitleM']!=""))
        {
            $showTitle = mysqli_real_escape_string($link, $_POST['inputShowTitleM']);
        }

        if(isset($_POST['inputTitleWidgetM']) && ($_POST['inputTitleWidgetM']!=""))
        {
            $title_widget_m = mysqli_real_escape_string($link, $_POST['inputTitleWidgetM']);
        }

        if(isset($_POST['inputDefaultTabM']) && ($_POST['inputDefaultTabM']!=""))
        {
            $inputDefaultTabM = mysqli_real_escape_string($link, $_POST['inputDefaultTabM']);
        }

        if(isset($_POST['inputFreqWidgetM']) && ($_POST['inputFreqWidgetM']!=""))
        {
            $freq_widget_m = mysqli_real_escape_string($link, $_POST['inputFreqWidgetM']);
        }

        if(isset($_POST['select-frameColor-Widget-m']) && ($_POST['select-frameColor-Widget-m']!=""))
        {
            $color_frame_m = mysqli_real_escape_string($link, $_POST['select-frameColor-Widget-m']);
        }

        if((isset($_POST['parametersM']))&&($_POST['parametersM'] != ""))
        {
            if(($type_widget_m == 'widgetServerStatus')||($type_widget_m == 'widgetBarContent')||($type_widget_m == 'widgetColumnContent')||($type_widget_m == 'widgetGaugeChart')||($type_widget_m == 'widgetSingleContent')||($type_widget_m == 'widgetSpeedometer')||($type_widget_m == 'widgetTimeTrend')||($type_widget_m == 'widgetTimeTrendCompare'))
            {
               if($_POST['alrThrSelM'] != "no")
               {
                  $parametersM = $_POST['parametersM'];
               }
            }
            else
            {
               $parametersM = $_POST['parametersM'];
               
               //Eliminazione soglie che non sono sull'asse target per widget table
               if(($type_widget_m == 'widgetTable') || ($type_widget_m == 'widgetLineSeries') || ($type_widget_m == "widgetCurvedLineSeries"))
               {
                   $paramsDecoded = json_decode($parametersM);
                   $thrTarget = $paramsDecoded->thresholdObject->target;
                   if($thrTarget == $paramsDecoded->thresholdObject->firstAxis->desc)
                   {
                       //Se il target è il primo asse eliminiamo le eventuali soglie (impostate da GUI) dal secondo asse
                       for($i = 0; $i < count($paramsDecoded->thresholdObject->secondAxis->fields); $i++)
                       {
                           $paramsDecoded->thresholdObject->secondAxis->fields[$i]->thrSeries = array();
                       }

                       $parametersM = json_encode($paramsDecoded);
                   }
                   else 
                   {
                       if($thrTarget == $paramsDecoded->thresholdObject->secondAxis->desc)
                       {
                           //Se il target è il secondo asse eliminiamo le eventuali soglie (impostate da GUI) dal primo asse
                           for($i = 0; $i < count($paramsDecoded->thresholdObject->firstAxis->fields); $i++)
                           {
                               $paramsDecoded->thresholdObject->firstAxis->fields[$i]->thrSeries = array();
                           }

                           $parametersM = json_encode($paramsDecoded);
                       }
                       else
                       {
                           $parametersM = NULL;
                       }
                   }
               }
               else
               {
                   if($type_widget_m == 'widgetSelector')
                   {
                        if(!is_dir("../img/widgetSelectorImages"))
                        {
                           mkdir("../img/widgetSelectorImages/", 0777);
                        }

                        if(!is_dir("../img/widgetSelectorImages/" . $name_widget_m))
                        {
                            mkdir("../img/widgetSelectorImages/" . $name_widget_m, 0777);
                        }

                        $parametersSelectorArray = json_decode($parametersM, false);
                        //$file = fopen("C:\dashboardLog.txt", "a");
                        for($i = (count($parametersSelectorArray->queries) - 1); $i >= 0; $i--)
                        {
                            if(isset($parametersSelectorArray->queries[$i]->deleted))
                            {
                                if($parametersSelectorArray->queries[$i]->deleted == true)
                                {
                                    array_map('unlink', glob("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i . "/*"));
                                    rmdir("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i);
                                    
                                    $remainingDirs = scandir("../img/widgetSelectorImages/" . $name_widget_m);
                                    for($j = 0; $j < count($remainingDirs); $j++)
                                    {
                                        if(file_exists("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $j)&&($j > $i))
                                        {
                                            rename("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $j, "../img/widgetSelectorImages/" . $name_widget_m . "/q" . ($j - 1));
                                        }
                                        
                                        if(($j > $i)&&isset($parametersSelectorArray->queries[$j]->symbolFile))
                                        {
                                            $pattern = '/q(\d+)/';
                                            $replace = "/q" . ($j - 1) ."/";
                                            $parametersSelectorArray->queries[$j]->symbolFile = preg_replace($pattern, $replace, $parametersSelectorArray->queries[$j]->symbolFile);
                                        }
                                    }
                                    
                                    unset($parametersSelectorArray->queries[$i]);
                                } 
                            }
                            else 
                            {
                                if(!is_dir("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i))
                                {
                                   mkdir("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i, 0777);
                                }
                                
                                //Crash undefined offset
                                $error = $_FILES["editSelectorLogos"]["error"][$i];

                                if($error == UPLOAD_ERR_OK) 
                                {
                                    $tmp_name = $_FILES["editSelectorLogos"]["tmp_name"][$i];
                                    $name = basename($_FILES["editSelectorLogos"]["name"][$i]);

                                    //Cancellazione files preesistenti (sarà uno solo)
                                    array_map('unlink', glob("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i . "/*"));

                                    move_uploaded_file($tmp_name, "../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i . "/" . $name);

                                    $parametersSelectorArray->queries[$i]->symbolMode = 'man';
                                    $parametersSelectorArray->queries[$i]->symbolFile = "../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i . "/" . $name;
                                }
                                else
                                {
                                    //Casi di logo automatico o logo manuale ma non cambiato, non facciamo niente di specifico
                                    if($parametersSelectorArray->queries[$i]->symbolMode == 'auto')
                                    {
                                        array_map('unlink', glob("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i . "/*"));
                                        unset($parametersSelectorArray->queries[$i]->symbolFile);
                                    }
                                }
                                
                                if(isset($parametersSelectorArray->queries[$i]->added))
                                {
                                    unset($parametersSelectorArray->queries[$i]->added);
                                }
                            }
                        }
                        
                        $parametersSelectorArray->queries = array_values($parametersSelectorArray->queries);
                        $parametersM = json_encode($parametersSelectorArray);
                   }
               }
            }
        }

        if(isset($_POST['inputFontSizeM']) && ($_POST['inputFontSizeM']!=""))
        {
            $fontSizeM = mysqli_real_escape_string($link, $_POST['inputFontSizeM']);
        }
        else
        {
            $fontSizeM = NULL;  
        }

        if(isset($_POST['inputFontColorM']) && ($_POST['inputFontColorM']!=""))
        {
            $fontColorM = mysqli_real_escape_string($link, $_POST['inputFontColorM']);
        }
        else
        {
            $fontColorM = NULL;  
        }

        //Gestione parametri per widget di stato del singolo processo
        if($type_widget_m == 'widgetProcess')
        {
            $hostM = $_POST['hostM'];
            $userM = $_POST['userM'];
            $passM = $_POST['passM'];
            $schedulerNameM = $_POST['schedulerNameM'];
            $jobAreaM = $_POST['jobAreaM'];
            $jobGroupM = $_POST['jobGroupM'];
            $jobNameM = $_POST['jobNameM'];
            $parametersArrayM = array('host' => $hostM, 'user' => $userM, 'pass' => $passM, 'schedulerName' => $schedulerNameM, 'jobArea' => $jobAreaM, 'jobGroup' => $jobGroupM, 'jobName' => $jobNameM);
            $parametersM = json_encode($parametersArrayM);
        }

        $id_dashboard2 = $_SESSION['dashboardId'];

        if(isset($_POST['select-IntTemp-Widget-m']) && ($_POST['select-IntTemp-Widget-m'] != "") &&($type_widget_m != 'widgetProtezioneCivile')) 
        {
            $int_temp_widget_m = mysqli_real_escape_string($link, $_POST['select-IntTemp-Widget-m']);
        }
        else
        {
            $int_temp_widget_m = NULL;
        }

        if (isset($_POST['inputComuneWidgetM']) && ($_POST['inputComuneWidgetM'] != "") &&($type_widget_m != 'widgetProtezioneCivile')) 
        {
            $comune_widget_m = mysqli_real_escape_string($link, $_POST['inputComuneWidgetM']);
        }
        else 
        {
            $comune_widget_m = NULL;
        }

        if(isset($_POST['urlWidgetM'])&& ($_POST['urlWidgetM'] != ""))
        {
            if(preg_match('/^ *$/', $_POST['urlWidgetM'])) 
            {
               $url_m = "none";
            }
            else
            {
               $url_m = mysqli_real_escape_string($link, $_POST['urlWidgetM']);
            }                                    
        }
        else
        {
            $url_m = "none";
        }

        $inputUdmWidget = NULL;
        if(isset($_POST['inputUdmWidgetM']) && $_POST['inputUdmWidgetM'] != "") 
        {
            $inputUdmWidget = mysqli_real_escape_string($link, $_POST['inputUdmWidgetM']);
        }
        
        $inputUdmPosition = NULL;
        if(isset($_POST['inputUdmPositionM']) && ($_POST['inputUdmPositionM'] != "")) 
        {
            $inputUdmPosition = mysqli_real_escape_string($link, $_POST['inputUdmPositionM']);
        }
        
        $serviceUri = NULL;
        if(isset($_POST['serviceUriM']) && ($_POST['serviceUriM'] != '') && (!empty($_POST['serviceUriM']))) 
        {
            $serviceUri = mysqli_real_escape_string($link, $_POST['serviceUriM']);
        }

        $viewMode = NULL;
        if(isset($_POST['editWidgetFirstAidMode']) && ($_POST['editWidgetFirstAidMode'] != '') && (!empty($_POST['editWidgetFirstAidMode']))) 
        {
            $viewMode = mysqli_real_escape_string($link, $_POST['editWidgetFirstAidMode']);
        }
        else
        {
            if(isset($_POST['widgetEventsModeM'])) 
            {
                $viewMode = mysqli_real_escape_string($link, $_POST['widgetEventsModeM']);
            }
        }

        $hospitalList = NULL;
        if(isset($_POST['hospitalListM']) && ($_POST['hospitalListM'] != '') && (!empty($_POST['hospitalListM']))) 
        {
            $hospitalList = $_POST['hospitalListM'];
        }
        
        $lastSeries = NULL;
        
         if($type_widget_m == 'widgetTrafficEvents')
         {
            //31/08/2017 - Patch temporanea in attesa di avere tempo di mettere i controlli sul form
            $styleParametersM = '{"choosenOption":"events", "timeUdm":"MINUTE", "time":90, "events":50}';
         }
         
         if($type_widget_m == "widgetButton")
         { 
            $styleParametersArray = [];
            $styleParametersArray["borderRadius"] = $_REQUEST['editWidgetBtnRadius'];

            if($_REQUEST['editWidgetBtnImgSelect'] == "yes")
            { 
               $styleParametersArray["hasImage"] = "yes";
               $styleParametersArray["imageWidth"] = $_REQUEST["editWidgetBtnImgWidth"];
               $styleParametersArray["imageHeight"] = $_REQUEST["editWidgetBtnImgHeight"];
               
               if(isset($_FILES['editWidgetBtnFile']))
               { 
                 if($_FILES['editWidgetBtnFile']['size'] > 0)
                 {
                    if(!file_exists("../img/widgetButtonImages/"))
                    {
                       $oldMask = umask(0); 
                       mkdir("../img/widgetButtonImages/", 0777);
                       umask($oldMask);
                    }

                    if(!file_exists("../img/widgetButtonImages/" . $name_widget_m))
                    {
                       $oldMask = umask(0); 
                       mkdir("../img/widgetButtonImages/" . $name_widget_m, 0777);
                       umask($oldMask);
                    }
                    else
                    {
                       array_map('unlink', glob("../img/widgetButtonImages/" . $name_widget_m . "/*.*"));
                    }

                    $fileUploaded = move_uploaded_file($_FILES['editWidgetBtnFile']['tmp_name'], "../img/widgetButtonImages/" . $name_widget_m . "/" . $_FILES['editWidgetBtnFile']['name']);
                    chmod("../img/widgetButtonImages/" . $name_widget_m . "/" . $_FILES['editWidgetBtnFile']['name'], 0666);
                    $styleParametersArray["imageName"] = $_FILES['editWidgetBtnFile']['name'];
                 }
                 else
                 {
                    $filesArray = scandir("../img/widgetButtonImages/" . $name_widget_m);
                    $styleParametersArray["imageName"] = $filesArray[0];
                    
                    for($i = 0; $i < count($filesArray); $i++)
                    {
                       if(($filesArray[$i] !== ".") && ($filesArray[$i] !== ".."))
                       {
                          $styleParametersArray["imageName"] = $filesArray[$i];
                       }
                    }
                 }
               }
               else
               {
                   $filesArray = scandir("../img/widgetButtonImages/" . $name_widget_m);
                   for($i = 0; $i < count($filesArray); $i++)
                   {
                       if(($filesArray[$i] !== ".") && ($filesArray[$i] !== ".."))
                       {
                          $styleParametersArray["imageName"] = $filesArray[$i];
                       }
                   }
               }
            }
            else
            {
               $styleParametersArray["hasImage"] = "no";
               if(file_exists("../img/widgetButtonImages/")&&file_exists("../img/widgetButtonImages/" . $name_widget_m))
               {
                  array_map('unlink', glob("../img/widgetButtonImages/" . $name_widget_m . "/*.*"));
                  rmdir("../img/widgetButtonImages/" . $name_widget_m);
               }
            }
            
            $styleParametersArray["showText"] = $_REQUEST['editWidgetShowButtonText'];
            $styleParametersM = json_encode($styleParametersArray); 
         }
         
        if(isset($_REQUEST['enableFullscreenTabM']))
        {
           $enableFullscreenTabM = $_REQUEST['enableFullscreenTabM'];
        }

        if(isset($_REQUEST['enableFullscreenModalM']))
        {
           $enableFullscreenModalM = $_REQUEST['enableFullscreenModalM'];
        }
        
        /*Verifichiamo se è già stato registrato o no sul notificatore:
          1) Se non registrato --> lo registriamo
          2) Se già registrato --> ne aggiorniamo validità e titolo (nome generatore sul notificatore)
         */
        
        $notificatorQuery = "SELECT notificatorRegistered, notificatorEnabled, title_w FROM Dashboard.Config_widget_dashboard WHERE name_w = '$name_widget_m' AND id_dashboard = '$id_dashboard2'";
        $notificatorRs = mysqli_query($link, $notificatorQuery);
        
        if($notificatorRs)
        {
           $notificatorRow = mysqli_fetch_assoc($notificatorRs);
           $notificatorRegistered = $notificatorRow['notificatorRegistered'];
           $notificatorRegisteredOld = $notificatorRow['notificatorRegistered'];
           $notificatorEnabled = $notificatorRow['notificatorEnabled'];
           $notificatorEnabledOld = $notificatorRow['notificatorEnabled'];
           $genOriginalName = $notificatorRow['title_w'];
           
           if($notificatorRegistered == "no")
           {
               if(isset($_REQUEST['editWidgetRegisterGen']))
               {
                  $notificatorRegisteredNew = $_REQUEST['editWidgetRegisterGen'];
                  $notificatorEnabledNew = $_REQUEST['editWidgetRegisterGen'];
               }
               else
               {
                  $notificatorRegisteredNew = 'no';
                  $notificatorEnabledNew = 'no';
               }
           }
           else
           {
               $notificatorRegisteredNew = $notificatorRegistered;
               if(isset($_REQUEST['editWidgetRegisterGen']))
               {
                  $notificatorEnabledNew = $_REQUEST['editWidgetRegisterGen'];
               }
               else
               {
                  $notificatorEnabledNew = 'no';
               }
           }
           
           if($_REQUEST['widgetCategoryHiddenM'] == "viewer")
           {
               $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET type_w = " . returnManagedStringForDb($type_widget_m) . ", size_columns = " . returnManagedNumberForDb($col_m) . ", size_rows = " . returnManagedNumberForDb($row_m) 
                       . ", title_w = " . returnManagedStringForDb($title_widget_m) . ", color_w = " . returnManagedStringForDb($color_widget_m) . ", frequency_w = " . returnManagedNumberForDb($freq_widget_m) . ", temporal_range_w = " . returnManagedStringForDb($int_temp_widget_m) 
                       . ", municipality_w = " . returnManagedStringForDb($comune_widget_m) . ", infoMessage_w = " . returnManagedStringForDb($info_m) . ", link_w = " . returnManagedStringForDb($url_m) . ", parameters = " . returnManagedStringForDb($parametersM) 
                       . ", frame_color_w = " . returnManagedStringForDb($color_frame_m) . ", udm = " . returnManagedStringForDb($inputUdmWidget) . ", udmPos = " . returnManagedStringForDb($inputUdmPosition) . ", fontSize = " . returnManagedNumberForDb($fontSizeM) 
                       . ", fontColor = " . returnManagedStringForDb($fontColorM) . ", controlsPosition = " . returnManagedStringForDb($controlsPosition) . ", showTitle = " . returnManagedStringForDb($showTitle) . ", controlsVisibility = " . returnManagedStringForDb($controlsVisibility) 
                       . ", defaultTab = " . returnManagedNumberForDb($inputDefaultTabM) . ", zoomControlsColor = " . returnManagedStringForDb($zoomControlsColorM) . ", headerFontColor = " . returnManagedStringForDb($headerFontColorM) . ", styleParameters = " . returnManagedStringForDb($styleParametersM) 
                       . ", serviceUri = " . returnManagedStringForDb($serviceUri) . ", viewMode = " . returnManagedStringForDb($viewMode) . ", hospitalList = " . returnManagedStringForDb($hospitalList) . ", lastSeries = " . returnManagedStringForDb($lastSeries) . ", notificatorRegistered = " . returnManagedStringForDb($notificatorRegisteredNew) . ", notificatorEnabled = " . returnManagedStringForDb($notificatorEnabledNew) . ", enableFullscreenTab = " . returnManagedStringForDb($enableFullscreenTabM) . ", enableFullscreenModal = " . returnManagedStringForDb($enableFullscreenModalM) . ", fontFamily = ". returnManagedStringForDb($fontFamily) . " WHERE Id = '$widgetIdM' AND id_dashboard = $id_dashboard2";
           }
           else
           {
               $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET name_w = '$name_widget_m', type_w = " . returnManagedStringForDb($type_widget_m) . ", size_columns = " . returnManagedNumberForDb($col_m) . ", size_rows = " . returnManagedNumberForDb($row_m) 
                       . ", title_w = " . returnManagedStringForDb($title_widget_m) . ", color_w = " . returnManagedStringForDb($color_widget_m) . ", frequency_w = " . returnManagedNumberForDb($freq_widget_m) . ", temporal_range_w = " . returnManagedStringForDb($int_temp_widget_m) 
                       . ", municipality_w = " . returnManagedStringForDb($comune_widget_m) . ", infoMessage_w = " . returnManagedStringForDb($info_m) . ", link_w = " . returnManagedStringForDb($url_m) . ", parameters = " . returnManagedStringForDb($parametersM) 
                       . ", frame_color_w = " . returnManagedStringForDb($color_frame_m) . ", udm = " . returnManagedStringForDb($inputUdmWidget) . ", udmPos = " . returnManagedStringForDb($inputUdmPosition) . ", fontSize = " . returnManagedNumberForDb($fontSizeM) 
                       . ", fontColor = " . returnManagedStringForDb($fontColorM) . ", controlsPosition = " . returnManagedStringForDb($controlsPosition) . ", showTitle = " . returnManagedStringForDb($showTitle) . ", controlsVisibility = " . returnManagedStringForDb($controlsVisibility) 
                       . ", defaultTab = " . returnManagedNumberForDb($inputDefaultTabM) . ", zoomControlsColor = " . returnManagedStringForDb($zoomControlsColorM) . ", headerFontColor = " . returnManagedStringForDb($headerFontColorM) . ", styleParameters = " . returnManagedStringForDb($styleParametersM) 
                       . ", serviceUri = " . returnManagedStringForDb($serviceUri) . ", viewMode = " . returnManagedStringForDb($viewMode) . ", hospitalList = " . returnManagedStringForDb($hospitalList) . ", lastSeries = " . returnManagedStringForDb($lastSeries) . ", notificatorRegistered = " . returnManagedStringForDb($notificatorRegisteredNew) 
                       . ", notificatorEnabled = " . returnManagedStringForDb($notificatorEnabledNew) . ", enableFullscreenTab = " . returnManagedStringForDb($enableFullscreenTabM) . ", enableFullscreenModal = " . returnManagedStringForDb($enableFullscreenModalM) . ", fontFamily = ". returnManagedStringForDb($fontFamily) 
                       . ", actuatorEntity = " . returnManagedStringForDb($actuatorEntity) . ", actuatorAttribute = " . returnManagedStringForDb($actuatorAttribute)
                       . " WHERE Id = '$widgetIdM' AND id_dashboard = $id_dashboard2";
           }
           
           $result7 = mysqli_query($link, $upsqDbtb);
           
            if($result7) 
            {
                //Lasciarle separate, sennò non funziona
                $updateInfoJson = $link->prepare("UPDATE Dashboard.Config_widget_dashboard SET infoJson = ? WHERE name_w = ? AND id_dashboard = ?");
                $updateInfoJson->bind_param('ssi', $infoJsonM, $name_widget_m, $id_dashboard2);
                $result8 = $updateInfoJson->execute();
                
                if($result8)
                {
                    mysqli_close($link);
                    if(isset($_REQUEST['editWidgetShowNotificator']))
                    {
                        if($_REQUEST['editWidgetShowNotificator'] == "1")
                        {
                           header("location: dashboard_configdash.php?openNotificator=1&dashId=" . $id_dashboard2 ."&widgetTitle=" . $title_widget_m);                 
                        }
                        else
                        {
                            header("location: dashboard_configdash.php");
                        }
                    }
                    else
                    {
                        header("location: dashboard_configdash.php");
                    }

                    //1) Se non registrato e viene richiesto di abilitarlo da GUI --> lo registriamo (con registrazione dei tipi di evento, come in add);
                    if($notificatorRegistered == 'no')
                    {
                       if(isset($_REQUEST['editWidgetRegisterGen']))
                       {
                          if($notificatorRegisteredNew == 'yes')
                          {
                            $url = $notificatorUrl;
                            $genOriginalName = preg_replace('/\s+/', '+', $title_widget_m);
                            $genOriginalType = preg_replace('/\s+/', '+', $_REQUEST['metricWidgetM']);
                            $containerName = preg_replace('/\s+/', '+', $dashboardName2);
                            $appUsr = preg_replace('/\s+/', '+', $_SESSION['loggedUsername']); 

                            $containerUrl = $appUrl . "/view/index.php?iddasboard=" . base64_encode($id_dashboard2); 

                            $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventGenerator&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . "&url=" . $containerUrl;
                            $url = $url.$data;

                            $options = array(
                                'http' => array(
                                    'header'  => "Content-type: application/json\r\n",
                                    'method'  => 'POST'
                                    //'timeout' => 2
                                )
                            );

                            try
                            {
                               $context  = stream_context_create($options);
                               $callResult = @file_get_contents($url, false, $context);

                               //Questo if è temporaneo, allargalo via via che aggiungi widget in grado di generare eventi.
                               if(($type_widget_m == 'widgetServerStatus')||($type_widget_m == 'widgetBarContent')||($type_widget_m == 'widgetColumnContent')||($type_widget_m == 'widgetGaugeChart')||($type_widget_m == 'widgetSingleContent')||($type_widget_m == 'widgetSpeedometer')||($type_widget_m == 'widgetTimeTrend')||($type_widget_m == 'widgetTimeTrendCompare'))
                               {
                                  //Invio dei tipi di evento al notificatore
                                  $thrObj = json_decode($parametersM, true);
                                  if(count($thrObj) > 0)
                                  {
                                     if(count($thrObj["thresholdObject"] ) > 0)
                                     {
                                        foreach($thrObj["thresholdObject"] as $eventType)
                                        {
                                           switch($eventType["op"])
                                           {
                                              case "less":
                                                 $eventName = "Value < " . $eventType["thr1"];
                                                 break;

                                              case "lessEqual":
                                                 $eventName = "Value <= " . $eventType["thr1"];
                                                 break;

                                              case "greater":
                                                 $eventName = "Value > " . $eventType["thr1"];
                                                 break;

                                              case "greaterEqual":
                                                 $eventName = "Value >= " . $eventType["thr1"];
                                                 break;

                                              case "equal":
                                                 $eventName = "Value = " . $eventType["thr1"];
                                                 break;

                                              case "notEqual":
                                                 $eventName = "Value != " . $eventType["thr1"];
                                                 break;

                                              case "intervalOpen":
                                                 $eventName = $eventType["thr1"] . " < value < " . $eventType["thr2"];
                                                 break;

                                              case "intervalClosed":
                                                 $eventName = $eventType["thr1"] . " <= value <= " . $eventType["thr2"];
                                                 break;

                                              case "intervalLeftOpen":
                                                 $eventName = $eventType["thr1"] . " < value <= " . $eventType["thr2"];
                                                 break;

                                              case "intervalRightOpen":
                                                 $eventName = $eventType["thr1"] . " <= value < " . $eventType["thr2"];
                                                 break;
                                           }

                                           if($eventType["desc"] != "")
                                           {
                                              $eventName = $eventName . " - " . $eventType["desc"];
                                           }

                                           $eventName = preg_replace('/\s+/', '+', $eventName);

                                           $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&eventType=' . $eventName . "&thrCnt=1";
                                           $url = $url.$data;

                                           $options = array(
                                               'http' => array(
                                                   'header'  => "Content-type: application/json\r\n",
                                                   'method'  => 'POST'
                                                   //'timeout' => 2
                                               )
                                           );

                                           try
                                           {
                                              $context  = stream_context_create($options);
                                              $callResult = @file_get_contents($url, false, $context);
                                           }
                                           catch (Exception $ex) 
                                           {
                                              //Non facciamo niente di specifico in caso di mancata risposta dell'host
                                           }
                                        }
                                     }
                                  }
                               }
                            }
                            catch (Exception $ex) 
                            {
                               //Non facciamo niente di specifico in caso di mancata risposta dell'host
                            }
                          }
                       }
                    }
                    else//2) Se già registrato ne aggiorniamo sempre validità e titolo (nome generatore sul notificatore)
                    {
                       if(isset($_REQUEST['editWidgetRegisterGen']))
                       {
                         //SETTING DELLA VALIDITA' DEL GENERATORE SUL NOTIFICATORE CHIAMANDO setGeneratorValidity
                         $url = $notificatorUrl;
                         $genOriginalName = preg_replace('/\s+/', '+', $genOriginalName);
                         $genNewName = preg_replace('/\s+/', '+', $title_widget_m);
                         $genOriginalType = preg_replace('/\s+/', '+', $_REQUEST['metricWidgetM']);
                         $alrThrSelM = preg_replace('/\s+/', '+', $_REQUEST['alrThrSelM']);
                         $containerName = preg_replace('/\s+/', '+', $_SESSION['dashboardTitle']);
                         $appUsr = preg_replace('/\s+/', '+', $_SESSION['loggedUsername']); 

                         //$setEventsValidityTrue = "false";//Se era già registrato non riabilitiamo tutti i suoi eventi, sennò riabilitiamo anche quelli vecchi, per entrambi i casi di provenienza

                         if($notificatorEnabledNew == 'yes')
                         {
                            $validity = 1;
                         }
                         else
                         {
                            $validity = 0;
                         }

                         //$data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=setGeneratorValidity&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $genOriginalName . '&generatorNewName=' . $genNewName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . "&validity=" . $validity . "&setEventsValidityTrue=" . $setEventsValidityTrue;
                         $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=setGeneratorValidity&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $genOriginalName . '&generatorNewName=' . $genNewName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . "&validity=" . $validity;
                         $url = $url.$data;

                         $options = array(
                             'http' => array(
                                 'header'  => "Content-type: application/json\r\n",
                                 'method'  => 'POST'
                                 //'timeout' => 2
                             )
                         );

                         try
                         {
                            $context  = stream_context_create($options);
                            $callResult = @file_get_contents($url, false, $context);

                            //Caso edit soglie mantenendo il generatore registrato
                            if(($_REQUEST['editWidgetRegisterGen'] == "yes")&&($notificatorEnabledNew == "yes")&&($notificatorEnabled == "yes"))
                            {
                               //Se soglie abilitate aggiorniamo dalle vecchie soglie alle nuove
                               if($alrThrSelM == "yes")
                               {
                                  //Aggiornamento lista degli eventi
                                  $parametersDiff = json_decode($_REQUEST['parametersDiff'], true);

                                  //Eventi aggiunti e modificati
                                  foreach($parametersDiff["addedChangedKept"] as $eventType)
                                  {
                                     $op = $eventType["op"];
                                     $opNew = $eventType["opNew"];
                                     $thr1 = $eventType["thr1"];
                                     $thr1New = $eventType["thr1New"];
                                     $thr2 = $eventType["thr2"];
                                     $thr2New = $eventType["thr2New"];
                                     $desc = $eventType["desc"];
                                     $descNew = $eventType["descNew"];

                                     switch($op)
                                     {
                                        case "less":
                                           $oldEventName = "Value < " . $thr1;
                                           break;

                                        case "lessEqual":
                                           $oldEventName = "Value <= " . $thr1;
                                           break;

                                        case "greater":
                                           $oldEventName = "Value > " . $thr1;
                                           break;

                                        case "greaterEqual":
                                           $oldEventName = "Value >= " . $thr1;
                                           break;

                                        case "equal":
                                           $oldEventName = "Value = " . $thr1;
                                           break;

                                        case "notEqual":
                                           $oldEventName = "Value != " . $thr1;
                                           break;

                                        case "intervalOpen":
                                           $oldEventName = $thr1 . " < value < " . $thr2;
                                           break;

                                        case "intervalClosed":
                                           $oldEventName = $thr1 . " <= value <= " . $thr2;
                                           break;

                                        case "intervalLeftOpen":
                                           $oldEventName = $thr1 . " < value <= " . $thr2;
                                           break;

                                        case "intervalRightOpen":
                                           $oldEventName = $thr1 . " <= value < " . $thr2;
                                           break;
                                     }

                                     if($desc != "")
                                     {
                                       $oldEventName = $oldEventName . " - " . $desc;
                                     }

                                     $oldEventName = preg_replace('/\s+/', '+', $oldEventName);

                                     switch($opNew)
                                     {
                                        case "less":
                                           $newEventName = "Value < " . $thr1New;
                                           break;

                                        case "lessEqual":
                                           $newEventName = "Value <= " . $thr1New;
                                           break;

                                        case "greater":
                                           $newEventName = "Value > " . $thr1New;
                                           break;

                                        case "greaterEqual":
                                           $newEventName = "Value >= " . $thr1New;
                                           break;

                                        case "equal":
                                           $newEventName = "Value = " . $thr1New;
                                           break;

                                        case "notEqual":
                                           $newEventName = "Value != " . $thr1New;
                                           break;

                                        case "intervalOpen":
                                           $newEventName = $thr1New . " < value < " . $thr2New;
                                           break;

                                        case "intervalClosed":
                                           $newEventName = $thr1New . " <= value <= " . $thr2New;
                                           break;

                                        case "intervalLeftOpen":
                                           $newEventName = $thr1New . " < value <= " . $thr2New;
                                           break;

                                        case "intervalRightOpen":
                                           $newEventName = $thr1New . " <= value < " . $thr2New;
                                           break;
                                     }

                                     if($descNew != "")
                                     {
                                       $newEventName = $newEventName . " - " . $descNew;
                                     }

                                     $newEventName = preg_replace('/\s+/', '+', $newEventName);
                                     //Eventi aggiunti
                                     if(($eventType["added"]||($eventType["added"]&&$eventType["changed"]))&&(!$eventType["deleted"]))
                                     {
                                        $url = $notificatorUrl;
                                        $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&eventType=' . $newEventName . "&thrCnt=1";
                                        $url = $url.$data;

                                        $options = array(
                                            'http' => array(
                                                'header'  => "Content-type: application/json\r\n",
                                                'method'  => 'POST'
                                                //'timeout' => 2
                                            )
                                        );

                                        try
                                        {
                                           $context  = stream_context_create($options);
                                           $callResult = @file_get_contents($url, false, $context);
                                        }
                                        catch (Exception $ex) 
                                        {
                                           //Non facciamo niente di specifico in caso di mancata risposta dell'host
                                        }
                                     }//Fine gestione eventi aggiunti
                                     else
                                     {
                                        //Gestione eventi aggiornati
                                        if((!$eventType["deleted"])&&($eventType["changed"]))
                                        {
                                           $url = $notificatorUrl;
                                           $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=updateEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&oldEventType=' . $oldEventName . "&newEventType=" . $newEventName;
                                           $url = $url.$data;

                                           $options = array(
                                               'http' => array(
                                                   'header'  => "Content-type: application/json\r\n",
                                                   'method'  => 'POST'
                                                   //'timeout' => 2
                                               )
                                           );

                                           try
                                           {
                                              $context  = stream_context_create($options);
                                              $callResult = @file_get_contents($url, false, $context);
                                           }
                                           catch (Exception $ex) 
                                           {
                                              //Non facciamo niente di specifico in caso di mancata risposta dell'host
                                           }
                                        }
                                     }
                                  }//Fine del foreach degli eventi aggiunti ed editati

                                  //Gestione eventi cancellati
                                  foreach($parametersDiff["deleted"] as $eventType)
                                  {
                                     $op = $eventType["opNew"];
                                     $thr1 = $eventType["thr1New"];
                                     $thr2 = $eventType["thr2New"];
                                     $desc = $eventType["descNew"];

                                     switch($op)
                                     {
                                        case "less":
                                           $eventName = "Value < " . $thr1;
                                           break;

                                        case "lessEqual":
                                           $eventName = "Value <= " . $thr1;
                                           break;

                                        case "greater":
                                           $eventName = "Value > " . $thr1;
                                           break;

                                        case "greaterEqual":
                                           $eventName = "Value >= " . $thr1;
                                           break;

                                        case "equal":
                                           $eventName = "Value = " . $thr1;
                                           break;

                                        case "notEqual":
                                           $eventName = "Value != " . $thr1;
                                           break;

                                        case "intervalOpen":
                                           $eventName = $thr1 . " < value < " . $thr2;
                                           break;

                                        case "intervalClosed":
                                           $eventName = $thr1 . " <= value <= " . $thr2;
                                           break;

                                        case "intervalLeftOpen":
                                           $eventName = $thr1 . " < value <= " . $thr2;
                                           break;

                                        case "intervalRightOpen":
                                           $eventName = $thr1 . " <= value < " . $thr2;
                                           break;
                                     }

                                     if($desc != "")
                                     {
                                       $eventName = $eventName . " - " . $desc;
                                     }

                                     $eventName = preg_replace('/\s+/', '+', $eventName);

                                     if(($eventType["deleted"])&&(!$eventType["added"]))//Così scartiamo quelli aggiunti e subito cancellati prima del commit   
                                     {
                                        $url = $notificatorUrl;
                                        $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&eventType=' . $eventName;
                                        $url = $url.$data;

                                        $options = array(
                                            'http' => array(
                                                'header'  => "Content-type: application/json\r\n",
                                                'method'  => 'POST'
                                                //'timeout' => 2
                                            )
                                        );

                                        try
                                        {
                                           $context  = stream_context_create($options);
                                           $callResult = @file_get_contents($url, false, $context);
                                        }
                                        catch (Exception $ex) 
                                        {
                                           //Non facciamo niente di specifico in caso di mancata risposta dell'host
                                        }
                                     }
                                  }//Fine del foreach degli eventi cancellati
                               }
                               else//Se set thresholds è valorizzato a "no" disabilitiamo tutti gli eventi del generatore e cancelliamo notifiche relative a tali eventi
                               {
                                  $url = $notificatorUrl;
                                  $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteAllGeneratorEventTypes&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName;
                                  $url = $url.$data;

                                  $options = array(
                                      'http' => array(
                                          'header'  => "Content-type: application/json\r\n",
                                          'method'  => 'POST'
                                          //'timeout' => 2
                                      )
                                  );

                                  try
                                  {
                                     $context  = stream_context_create($options);
                                     $callResult = @file_get_contents($url, false, $context);
                                  }
                                  catch (Exception $ex) 
                                  {
                                     //Non facciamo niente di specifico in caso di mancata risposta dell'host
                                  }
                               }
                            }

                            //Caso passaggio da generatore disabilitato su notificatore ad abilitato su notificatore: riabilitiamo eventi presenti in form edit widget (ed eventuali relative notifiche) ed aggiungiamo nuovi
                            if(($notificatorEnabledNew == "yes")&&($notificatorEnabled == "no"))
                            {
                               $thrObj = json_decode($parametersM, true);
                               if(count($thrObj) > 0)
                               {
                                  if(count($thrObj["thresholdObject"] ) > 0)
                                  {
                                     foreach($thrObj["thresholdObject"] as $eventType)
                                     {
                                        $op = $eventType["op"];
                                        $thr1 = $eventType["thr1"];
                                        $thr2 = $eventType["thr2"];
                                        $desc = $eventType["desc"];

                                        switch($op)
                                        {
                                           case "less":
                                              $eventName = "Value < " . $thr1;
                                              break;

                                           case "lessEqual":
                                              $eventName = "Value <= " . $thr1;
                                              break;

                                           case "greater":
                                              $eventName = "Value > " . $thr1;
                                              break;

                                           case "greaterEqual":
                                              $eventName = "Value >= " . $thr1;
                                              break;

                                           case "equal":
                                              $eventName = "Value = " . $thr1;
                                              break;

                                           case "notEqual":
                                              $eventName = "Value != " . $thr1;
                                              break;

                                           case "intervalOpen":
                                              $eventName = $thr1 . " < value < " . $thr2;
                                              break;

                                           case "intervalClosed":
                                              $eventName = $thr1 . " <= value <= " . $thr2;
                                              break;

                                           case "intervalLeftOpen":
                                              $eventName = $thr1 . " < value <= " . $thr2;
                                              break;

                                           case "intervalRightOpen":
                                              $eventName = $thr1 . " <= value < " . $thr2;
                                              break;
                                        }

                                        if($eventType["desc"] != "")
                                        {
                                           $eventName = $eventName . " - " . $eventType["desc"];
                                        }

                                        $eventName = preg_replace('/\s+/', '+', $eventName);

                                        $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&eventType=' . $eventName . "&thrCnt=1";
                                        $url = $url.$data;

                                        $options = array(
                                            'http' => array(
                                                'header'  => "Content-type: application/json\r\n",
                                                'method'  => 'POST'
                                                //'timeout' => 2
                                            )
                                        );

                                        try
                                        {
                                           $context  = stream_context_create($options);
                                           $callResult = @file_get_contents($url, false, $context);
                                        }
                                        catch (Exception $ex) 
                                        {
                                           //Non facciamo niente di specifico in caso di mancata risposta dell'host
                                        }
                                     }
                                  }
                               }
                            }

                            //Caso passaggio da generatore abilitato su notificatore a disabilitato su notificatore: ne disabilitiamo tutti gli eventi (e relative notifiche) SENZA CANCELLARLI FISICAMENTE
                            if(($notificatorEnabledNew == "no")&&($notificatorEnabled == "yes"))
                            {
                               $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=disableAllGeneratorEventTypes&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName;
                               $url = $url.$data;

                               $options = array(
                                   'http' => array(
                                       'header'  => "Content-type: application/json\r\n",
                                       'method'  => 'POST'
                                       //'timeout' => 2
                                   )
                               );

                               try
                               {
                                  $context  = stream_context_create($options);
                                  $callResult = @file_get_contents($url, false, $context);
                               }
                               catch (Exception $ex) 
                               {
                                  //Non facciamo niente di specifico in caso di mancata risposta dell'host
                               }
                            }
                         }
                         catch (Exception $ex) 
                         {
                            //Non facciamo niente di specifico in caso di mancata risposta dell'host
                         }
                       }
                    }
                }
                else
                {
                    mysqli_close($link);
                    echo '<script type="text/javascript">';
                    echo 'alert("Errore: ripetere update widget");';
                    echo 'window.location.href = "dashboard_configdash.php";';
                    echo '</script>';
                }
            } 
            else 
            {
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Errore: ripetere update widget");';
                echo 'window.location.href = "dashboard_configdash.php";';
                echo '</script>';
            }
         }
         else 
         {
           mysqli_close($link);
           echo '<script type="text/javascript">';
           echo 'alert("Errore: ripetere update widget");';
           echo 'window.location.href = "dashboard_configdash.php";';
           echo '</script>';
         }
    } 
    else if(isset($_REQUEST['addMetricType'])) 
    {
        session_start();
        if(isset($_SESSION['loggedRole']))
        {
            $metricName = mysqli_real_escape_string($link, $_POST['metricName']); 
            $shortDescription = mysqli_real_escape_string($link, $_POST['shortDescription']);
            $dataArea = mysqli_real_escape_string($link, $_POST['dataArea']);
            $fullDescription = mysqli_real_escape_string($link, $_POST['fullDescription']);
            $resultType = mysqli_real_escape_string($link, $_POST['resultType']); 
            $updateFrequency = mysqli_real_escape_string($link, $_POST['updateFrequency']);
            $processType = mysqli_real_escape_string($link, $_POST['processType']);
            $cityContext = mysqli_real_escape_string($link, $_POST['cityContext']);
            $timeRange = mysqli_real_escape_string($link, $_POST['timeRange']);
            $storingData = mysqli_real_escape_string($link, $_POST['storingData']);
            $dataSourceType = mysqli_real_escape_string($link, $_POST['dataSourceType']);
            $dataSourceDescription = mysqli_real_escape_string($link, $_POST['dataSourceDescription']);
            $query1 = NULL;
            $query2 = NULL;

            if(isset($_POST['query']))
            {
                if(trim($_POST['query']) != "")
                {
                    $query1 = mysqli_real_escape_string($link, $_POST['query']);
                }
            }

            if(isset($_POST['query2']))
            {
                if(trim($_POST['query2']) != "")
                {
                    $query2 = mysqli_real_escape_string($link, $_POST['query2']);
                }
            }

            if(isset($_POST['dataSource2']))
            {
                if($_POST['dataSource2'] != "none")
                {
                    $dataSource1 = mysqli_real_escape_string($link, $_POST['dataSource']);
                    $dataSource2 = mysqli_real_escape_string($link, $_POST['dataSource2']);
                    $dataSource = $dataSource1 . "|" . $dataSource2;
                }
                else
                {
                    $dataSource = mysqli_real_escape_string($link, $_POST['dataSource']);
                }
            }
            else
            {
                $dataSource = mysqli_real_escape_string($link, $_POST['dataSource']);
            }

            $sameDataAlarmCount = mysqli_real_escape_string($link, $_POST['sameDataAlarmCount']);
            $hasNegativeValues = mysqli_real_escape_string($link, $_POST['hasNegativeValues']);
            $oldDataEvalTime = mysqli_real_escape_string($link, $_POST['oldDataEvalTime']);
            $process = mysqli_real_escape_string($link, $_POST['process']);

            $q = "INSERT INTO Dashboard.Descriptions(IdMetric, description, status, query, query2, queryType, metricType, frequency, processType, area, source, description_short, dataSource, storingData, municipalityOption, timeRangeOption, sameDataAlarmCount, oldDataEvalTime, hasNegativeValues, process) 
                  VALUES('$metricName', '$fullDescription', 'Attivo', " . returnManagedStringForDb($query1) . ", " . returnManagedStringForDb($query2) . ", '$dataSourceType', '$resultType', '$updateFrequency', '$processType', '$dataArea', '$dataSourceDescription', '$shortDescription', '$dataSource', '$storingData', '$cityContext', '$timeRange', " . returnManagedNumberForDb($sameDataAlarmCount) . ", " . returnManagedNumberForDb($oldDataEvalTime) . ", '$hasNegativeValues', " . returnManagedStringForDb($process) . ")";

            $r = mysqli_query($link, $q);

            if($r)
            {
                echo "Ok";
            } 
            else 
            {
                echo "Ko";
            }
            mysqli_close($link);
        }
    } 
    else if(isset($_REQUEST['editMetricType']))
    {
        session_start();
        
        if(isset($_SESSION['loggedRole']))
        {
            $metricId = mysqli_real_escape_string($link, $_POST['metricId']); 
            $metricName = mysqli_real_escape_string($link, $_POST['metricNameM']); 
            $shortDescription = mysqli_real_escape_string($link, $_POST['shortDescriptionM']);
            $dataArea = mysqli_real_escape_string($link, $_POST['dataAreaM']);
            $fullDescription = mysqli_real_escape_string($link, $_POST['fullDescriptionM']);
            $resultType = mysqli_real_escape_string($link, $_POST['resultTypeM']); 
            $updateFrequency = mysqli_real_escape_string($link, $_POST['updateFrequencyM']);
            $processType = mysqli_real_escape_string($link, $_POST['processTypeM']);
            $cityContext = mysqli_real_escape_string($link, $_POST['cityContextM']);
            $timeRange = mysqli_real_escape_string($link, $_POST['timeRangeM']);
            $storingData = mysqli_real_escape_string($link, $_POST['storingDataM']);
            $dataSourceType = mysqli_real_escape_string($link, $_POST['dataSourceTypeM']);
            $dataSourceDescription = mysqli_real_escape_string($link, $_POST['dataSourceDescriptionM']);
            $query = mysqli_real_escape_string($link, $_POST['queryM']);
            $sameDataAlarmCount = mysqli_real_escape_string($link, $_POST['sameDataAlarmCountM']);
            $hasNegativeValues = mysqli_real_escape_string($link, $_POST['hasNegativeValuesM']);
            $oldDataEvalTime = mysqli_real_escape_string($link, $_POST['oldDataEvalTimeM']);
            $process = mysqli_real_escape_string($link, $_POST['processM']);

            $query1 = NULL;
            $query2 = NULL;

            if(isset($_POST['queryM']))
            {
                if(trim($_POST['queryM']) != "")
                {
                    $query1 = mysqli_real_escape_string($link, $_POST['queryM']);
                }
            }

            if(isset($_POST['query2M']))
            {
                if(trim($_POST['query2M']) != "")
                {
                    $query2 = mysqli_real_escape_string($link, $_POST['query2M']);
                }
            }

            if(isset($_POST['dataSource2M']))
            {
                if($_POST['dataSource2M'] != "none")
                {
                    $dataSource1 = mysqli_real_escape_string($link, $_POST['dataSourceM']);
                    $dataSource2 = mysqli_real_escape_string($link, $_POST['dataSource2M']);
                    $dataSource = $dataSource1 . "|" . $dataSource2;
                }
                else
                {
                    $dataSource = mysqli_real_escape_string($link, $_POST['dataSourceM']);
                }
            }
            else
            {
                $dataSource = mysqli_real_escape_string($link, $_POST['dataSourceM']);
            }

            $q = "UPDATE Dashboard.Descriptions SET IdMetric = '$metricName', description = '$fullDescription', query = " . returnManagedStringForDb($query1) . ", query2 = " . returnManagedStringForDb($query2) . ", queryType = '$dataSourceType', metricType = '$resultType', frequency = '$updateFrequency', processType = '$processType', area = '$dataArea', source = '$dataSourceDescription', description_short = '$shortDescription', dataSource = " . returnManagedStringForDb($dataSource) . ", storingData = '$storingData', municipalityOption = '$cityContext', timeRangeOption = '$timeRange', sameDataAlarmCount = " . returnManagedNumberForDb($sameDataAlarmCount) . ", oldDataEvalTime = " . returnManagedNumberForDb($oldDataEvalTime) . ", hasNegativeValues = '$hasNegativeValues', process = " . returnManagedStringForDb($process) . " WHERE Descriptions.id = $metricId";
            $r = mysqli_query($link, $q);

            if($r)
            {
                echo "Ok";
            } 
            else 
            {
                echo "Ko";
            }
        }
        
        mysqli_close($link);
    } 
    else if(isset($_REQUEST['deleteMetric']))//Escape 
    {
        session_start();
        
        if(isset($_SESSION['loggedRole']))
        {
            $metricId = mysqli_real_escape_string($link, $_REQUEST['metricId']);
            $q = "DELETE FROM Dashboard.Descriptions WHERE id = $metricId";
            $r = mysqli_query($link, $q);
            if($r) 
            {
                mysqli_close($link);
                echo "Ok";
            } 
            else 
            {
                mysqli_close($link);
                echo "Ko";
            }
        }
    } 
    else if(isset($_REQUEST['updateMetricStatus']))
    {
        session_start();
        if(isset($_SESSION['loggedRole']))
        {
            $metricId = mysqli_real_escape_string($link, $_REQUEST['metricId']);
            $newStatus = mysqli_real_escape_string($link, $_REQUEST['newStatus']);

            $q = "UPDATE Dashboard.Descriptions SET Descriptions.status = '$newStatus' WHERE Descriptions.id = $metricId";
            $r = mysqli_query($link, $q);

            if($r)
            {
                mysqli_close($link);
                echo "Ok";
            }
            else
            {
                mysqli_close($link);
                echo "Ko";
            }
        }
    } 
    else if(isset($_REQUEST['modify_status_dashboard']))
    {
        session_start();
        
        if(isset($_SESSION['loggedRole']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
            $dashboardNewStatus = mysqli_real_escape_string($link, $_REQUEST['newStatus']);

            $q = "UPDATE Dashboard.Config_dashboard SET Config_dashboard.status_dashboard = " . $dashboardNewStatus . " WHERE Config_dashboard.Id = " . $dashboardId;
            $r = mysqli_query($link, $q);
            mysqli_close($link);

            if($r) 
            {
                echo "Ok";
            } 
            else 
            {
                echo "Ko";
            }
        }
    } 
    else if(isset($_POST['addDs'])) 
    {
        session_start();

        if(isset($_SESSION['loggedRole']))
        {
            $Id = mysqli_real_escape_string($link, $_POST['name']);
            $url = mysqli_real_escape_string($link, $_POST['url']); 
            $database = mysqli_real_escape_string($link, $_POST['dbName']); 
            $databaseType = mysqli_real_escape_string($link, $_POST['dbType']); 
            $username = mysqli_real_escape_string($link, $_POST['dbUsr']); 
            $password = mysqli_real_escape_string($link, $_POST['dbPwd']);

            $q = "INSERT INTO Dashboard.DataSource(Id, url, DataSource.database, username, password, databaseType) VALUES('$Id', '$url', '$database', '$username', '$password', '$databaseType')";
            $r = mysqli_query($link, $q);

            if($r) 
            {
                mysqli_close($link);
                echo "Ok";
            } 
            else 
            {
                mysqli_close($link);
                echo "Ko";
            }
        }
    } 
    else if(isset($_REQUEST['updateDs'])) 
    {
        session_start();

        if(isset($_SESSION['loggedRole']))
        {
            $id = mysqli_real_escape_string($link, $_POST['id']);
            $Id = mysqli_real_escape_string($link, $_POST['name']);
            $url = mysqli_real_escape_string($link, $_POST['url']); 
            $database = mysqli_real_escape_string($link, $_POST['dbName']); 
            $databaseType = mysqli_real_escape_string($link, $_POST['dbType']); 
            $username = mysqli_real_escape_string($link, $_POST['dbUsr']); 
            $password = mysqli_real_escape_string($link, $_POST['dbPwd']);

            $q = "UPDATE Dashboard.DataSource SET Id = '$Id', url = '$url', DataSource.database = '$database', username = '$username', password = '$password', databaseType = '$databaseType' WHERE intId = $id";
            $r = mysqli_query($link, $q);

            if($r) 
            {
                mysqli_close($link);
                echo "Ok";
            } 
            else 
            {
                mysqli_close($link);
                echo "Ko";
            }
        }
        
    }
    else if(isset($_POST['delDs']))
    {
        session_start();

        if(isset($_SESSION['loggedRole']))
        {
            $id = mysqli_real_escape_string($link, $_POST['id']);
        
            $q = "DELETE FROM Dashboard.DataSource WHERE intId = $id";
            $r = mysqli_query($link, $q);

            if($r) 
            {
                mysqli_close($link);
                echo "Ok";
            } 
            else 
            {
                mysqli_close($link);
                echo "Ko";
            }
        }
    }
    else if(isset($_POST['addWidgetType']))
    {
        session_start();

        if(isset($_SESSION['loggedRole']))
        {
            $id_type_widget = mysqli_real_escape_string($link, $_POST['widgetName']);
            $source_php_widget = mysqli_real_escape_string($link, $_POST['phpFilename']);
            $min_row = mysqli_real_escape_string($link, $_POST['minHeight']);
            $max_row = mysqli_real_escape_string($link, $_POST['maxHeight']);
            $min_col = mysqli_real_escape_string($link, $_POST['minWidth']);
            $max_col = mysqli_real_escape_string($link, $_POST['maxWidth']);
            $widgetType = mysqli_real_escape_string($link, $_POST['metricType']);
            $unique_metric = mysqli_real_escape_string($link, $_POST['uniqueMetric']);
            $number_metrics_widget = mysqli_real_escape_string($link, $_POST['metricsNumber']);

            $q = "INSERT INTO Dashboard.Widgets(id_type_widget, source_php_widget, min_row, max_row, min_col, max_col, widgetType, unique_metric, number_metrics_widget) " .
                 "VALUES('$id_type_widget', '$source_php_widget', $min_row, $max_row, $min_col, $max_col, " . returnManagedStringForDb($widgetType) . ", '$unique_metric', " . returnManagedNumberForDb($number_metrics_widget) . ")";
            $r = mysqli_query($link, $q);

            if($r) 
            {
                mysqli_close($link);
                echo "Ok";
            } 
            else 
            {
                mysqli_close($link);
                echo "Ko";
            }
        }
    }
    else if(isset($_POST['delWidgetType']))
    {
        session_start();

        if(isset($_SESSION['loggedRole']))
        {
            $id = mysqli_real_escape_string($link, $_POST['id']);
        
            $q = "DELETE FROM Dashboard.Widgets WHERE id = $id";
            $r = mysqli_query($link, $q);

            if($r) 
            {
                mysqli_close($link);
                echo "Ok";
            } 
            else 
            {
                mysqli_close($link);
                echo "Ko";
            }
        }
    }
    else if(isset($_REQUEST['editWidgetType']))
    {
        session_start();

        if(isset($_SESSION['loggedRole']))
        {
            $id = mysqli_real_escape_string($link, $_POST['id']);
            $id_type_widget = mysqli_real_escape_string($link, $_POST['widgetName']);
            $source_php_widget = mysqli_real_escape_string($link, $_POST['phpFilename']);
            $min_row = mysqli_real_escape_string($link, $_POST['minHeight']);
            $max_row = mysqli_real_escape_string($link, $_POST['maxHeight']);
            $min_col = mysqli_real_escape_string($link, $_POST['minWidth']);
            $max_col = mysqli_real_escape_string($link, $_POST['maxWidth']);
            $widgetType = mysqli_real_escape_string($link, $_POST['metricType']);
            $unique_metric = mysqli_real_escape_string($link, $_POST['uniqueMetric']);
            $number_metrics_widget = mysqli_real_escape_string($link, $_POST['metricsNumber']);

            $q = "UPDATE Dashboard.Widgets SET id_type_widget = '$id_type_widget', source_php_widget = '$source_php_widget', min_row = $min_row,  max_row = $max_row, min_col = $min_col, max_col = $max_col, widgetType = " . returnManagedStringForDb($widgetType) . ", unique_metric = '$unique_metric', number_metrics_widget = " . returnManagedNumberForDb($number_metrics_widget) . " WHERE id = $id";
            $r = mysqli_query($link, $q);

            if($r) 
            {
                mysqli_close($link);
                echo "Ok";
            } 
            else 
            {
                mysqli_close($link);
                echo "Ko";
            }
        }
    }
    elseif(isset($_REQUEST['zoomFactorUpdated'])) //Escape
    {
        session_start();
        $zoomFactor = mysqli_real_escape_string($link, $_REQUEST['zoomFactorUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET zoomFactor = " . returnManagedNumberForDb($zoomFactor) . " WHERE Id = $idWidget";
        $resultQuery = mysqli_query($link, $upsqDbtb);

        if($resultQuery) 
        {
            mysqli_close($link);
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Ripetere modifica widget");';
            echo '</script>';
        }
    }
    elseif(isset($_REQUEST['scaleXUpdated'])) //Escape
    {
        session_start();
        $scaleX = mysqli_real_escape_string($link, $_REQUEST['scaleXUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET scaleX = " . returnManagedNumberForDb($scaleX) . " WHERE Id = $idWidget";
        $resultQuery = mysqli_query($link, $upsqDbtb);

        if($resultQuery) 
        {
            mysqli_close($link);
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Ripetere modifica widget");';
            echo '</script>';
        }
    }
    elseif(isset($_REQUEST['scaleYUpdated'])) //Escape
    {
        session_start();
        $scaleY = mysqli_real_escape_string($link, $_REQUEST['scaleYUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET scaleY = " . returnManagedNumberForDb($scaleY) . " WHERE Id = $idWidget";
        $resultQuery = mysqli_query($link, $upsqDbtb);

        if($resultQuery) 
        {
            mysqli_close($link);
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Ripetere modifica widget");';
            echo '</script>';
        }
    }
    elseif(isset($_REQUEST['widthUpdated'])) //Escape
    {
        session_start();
        $width = mysqli_real_escape_string($link, $_REQUEST['widthUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET size_columns = " . returnManagedNumberForDb($width) . " WHERE Id = $idWidget";
        $resultQuery = mysqli_query($link, $upsqDbtb);

        if($resultQuery) 
        {
            mysqli_close($link);
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Ripetere modifica widget");';
            echo '</script>';
        }
    }
    elseif(isset($_REQUEST['heightUpdated']))
    {
        session_start();
        $height = mysqli_real_escape_string($link, $_REQUEST['heightUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET size_rows = " . returnManagedNumberForDb($height) . " WHERE Id = $idWidget";
        $resultQuery = mysqli_query($link, $upsqDbtb);

        if($resultQuery) 
        {
            mysqli_close($link);
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Ripetere modifica widget");';
            echo '</script>';
        }
    }
    elseif(isset($_REQUEST['updatedLastSeries']))
    {
        session_start();
        if(isset($_SESSION['loggedRole']))
        {
            $widgetName = mysqli_real_escape_string($link, $_REQUEST['widgetName']);
            $updatedSeries = $_REQUEST['updatedLastSeries'];

            $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET lastSeries = " . returnManagedStringForDb($updatedSeries) . " WHERE name_w = $widgetName";
            $resultQuery = mysqli_query($link, $upsqDbtb);
        }
        mysqli_close($link);
    }
    elseif(isset($_REQUEST['showHideDashboardHeader'])) 
    {
        session_start();
        
        if(isset($_SESSION['loggedRole']))
        {
            $newStatus = mysqli_real_escape_string($link, $_REQUEST['showHideDashboardHeader']);
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
            $query = "UPDATE Dashboard.Config_dashboard SET headerVisible = $newStatus WHERE Id = $dashboardId";
            $result = mysqli_query($link, $query);

            if($result) 
            {
               mysqli_close($link);
               echo "Ok";
            }
            else
            {
               mysqli_close($link);
               echo "Ko";
            }
        }
        
    }
    elseif(isset($_REQUEST['getMetricsForWidgetType'])) 
    {
        session_start();
        
        if(isset($_SESSION['loggedRole']))
        {
            $widgetId = mysqli_real_escape_string($link, $_REQUEST['widgetId']);
            $query1 = "SELECT Widgets.widgetType " .
                      "FROM Dashboard.Config_widget_dashboard " .
                      "LEFT JOIN Dashboard.Widgets " . 
                      "ON Config_widget_dashboard.type_w = Widgets.id_type_widget " .                     
                      "WHERE Config_widget_dashboard.name_w = '$widgetId'";
            $rs1 = mysqli_query($link, $query1);

            $result = [];

            if($rs1) 
            {
                $row1 = mysqli_fetch_array($rs1);
                $metricMacroTypes = explode("|", $row1['widgetType']);
                $metricMacroTypesForQuery = "(";

                for($i = 0; $i < count($metricMacroTypes); $i++)
                {
                    if($i < (count($metricMacroTypes) - 1))
                    {
                        $metricMacroTypesForQuery .= "'" . $metricMacroTypes[$i] . "', ";
                    }
                    else
                    {
                        $metricMacroTypesForQuery .= "'" . $metricMacroTypes[$i] . "'";
                    }
                }

                $metricMacroTypesForQuery .= ")";

                $query2 = "SELECT IdMetric FROM Dashboard.Descriptions WHERE metricType IN " . $metricMacroTypesForQuery . " ORDER BY Descriptions.IdMetric ASC";

                $rs2 = mysqli_query($link, $query2);

                while($row2 = mysqli_fetch_array($rs2)) 
                {
                    array_push($result, $row2["IdMetric"]);
                }

                //Eliminiamo i duplicati
                $result = array_unique($result);
                mysqli_close($link);
                echo json_encode($result);
            }
            else
            {
               mysqli_close($link);
               echo "Ko";
            }
        }
    }
    elseif(isset($_REQUEST['getDashboardTitlesList']))
    {
        session_start();
        
        if(isset($_SESSION['loggedRole']))
        {
            $q = "SELECT Config_dashboard.title_header FROM Dashboard.Config_dashboard";
            $r = mysqli_query($link, $q);

            $result = [];

            if($r) 
            {
                $result['detail'] = 'Ok';
                $result['titles'] = [];
                while($row = mysqli_fetch_assoc($r)) 
                {
                    array_push($result['titles'], $row["title_header"]);
                }
            }
            else
            {
               $result['detail'] = 'Ko'; 
            }

            mysqli_close($link);
            echo json_encode($result);
        }
    }
    elseif(isset($_REQUEST['updateConfigFile']))
    {
        session_start();
        
        if(isset($_SESSION['loggedRole']))
        {
           if($_SESSION['loggedRole'] == "ToolAdmin")
           {
                $fileName = $_POST['fileName'];
                $fileOriginalContent = parse_ini_file("../conf/" . $fileName);
                try 
                {
                    $fileOriginalContent = parse_ini_file("../conf/" . $fileName);
                } 
                catch(Exception $e) 
                {
                    echo "parsingOriginalFileKo";
                    exit();
                }

                try 
                {
                    $file = fopen("../conf/" . $fileName, "w");
                } 
                catch(Exception $e) 
                {
                    echo "openingOriginalFileKo";
                    exit();
                }

                switch($fileName)
                {
                    case "environment.ini":
                        $newActiveEnv = $_REQUEST['activeEnvironment'];
                        $fileOriginalContent["environment"]["value"] = $newActiveEnv;
                        break;

                    default:
                        $dataFromForm = json_decode($_REQUEST['data']);

                        for($i = 0; $i < count($dataFromForm); $i++)
                        {
                            foreach($dataFromForm[$i] as $key => $value) 
                            {
                                if($key == "name")
                                {
                                    $updatedKey = $value;
                                }
                                else
                                {
                                    if($key == "value")
                                    {
                                        $updatedValue = $value;
                                        if(strpos($updatedKey, '[dev]') !== false)
                                        {
                                            $shortKey = str_replace("[dev]", "", $updatedKey);
                                            $fileOriginalContent[$shortKey]["dev"] = $updatedValue;
                                        }

                                        if(strpos($updatedKey, '[test]') !== false)
                                        {
                                            $shortKey = str_replace("[test]", "", $updatedKey);
                                            $fileOriginalContent[$shortKey]["test"] = $updatedValue;
                                        }

                                        if(strpos($updatedKey, '[prod]') !== false)
                                        {
                                            $shortKey = str_replace("[prod]", "", $updatedKey);
                                            $fileOriginalContent[$shortKey]["prod"] = $updatedValue;
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }

                foreach($fileOriginalContent as $key => $value) 
                {
                    if(is_array($value))
                    {
                        foreach($value as $subkey => $subvalue) 
                        {
                            try 
                            {
                                fwrite($file, $key . "[" . $subkey . "] = \"" . $subvalue . "\"\n");
                            } 
                            catch(Exception $e) 
                            {
                                echo "writingOriginalFileKo";
                                exit();
                            }
                        }
                    }
                    else
                    {
                        try 
                        {
                            fwrite($file, $key . " = \"" . $value . "\"\n");
                        } 
                        catch(Exception $e) 
                        {
                            echo "writingOriginalFileKo";
                            exit();
                        }
                    }
                }

                echo "Ok";
           }
        }
    }
    elseif(isset($_REQUEST['deleteConfigFile']))
    {
        session_start();
        
        if(isset($_SESSION['loggedRole']))
        {
           if($_SESSION['loggedRole'] == "ToolAdmin")
           {
               $fileName = $_POST['fileName'];
        
                try 
                {
                    if(unlink("../conf/" . $fileName))
                    {
                        echo "Ok";
                        exit();
                    }
                    else
                    {
                        echo "deleteModuleFileKo";
                        exit();
                    }
                } 
                catch(Exception $e) 
                {
                    echo "deleteModuleFileKo";
                    exit();
                }
           }
        }
    }
