<?php
    /* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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
    
    //Definizioni di funzione
    
    //Per ora non usata, per qualche motivo la chiamata è OK, i valori sul notificatore vengono indotti, ma non si entra nell'applicazione - Spostata lato client
    /*function notificatorLogin($notificatorUrl, $notificatorApiUsr, $notificatorApiPwd, $appUsr, $appPwd)
    {
      $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=remoteLogin&app=Dashboard&appUsr=' . $appUsr . '&appPwd=' . $appPwd;
      $notificatorUrl = $notificatorUrl.$data;
      
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/json\r\n",
              'method'  => 'POST',
              'timeout' => 3
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
    }*/
    
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
    session_start(); 
    $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
    mysqli_select_db($link, $dbname);
    error_reporting(E_ERROR | E_NOTICE);
    
    if(!$link->set_charset("utf8")) 
    {
        echo '<script type="text/javascript">';
        echo 'alert("KO");';
        echo '</script>';
        printf("Error loading character set utf8: %s\n", $link->error);
        exit();
    }

    if(isset($_REQUEST['register_confirm']))
    {
        if(isset($_SESSION['loggedRole']))
        {
            $username = mysqli_real_escape_string($link, $_POST['inputUsername']);
            $password = mysqli_real_escape_string($link, $_POST['inputPassword']); 
            $firstname = mysqli_real_escape_string($link, $_POST['inputNameUser']); 
            $lastname = mysqli_real_escape_string($link, $_POST['inputSurnameUser']);
            $email = mysqli_real_escape_string($link, $_POST['inputEmail']);

            //24/03/2017 - Cambierà via via che implementiamo la nuova profilazione utente
            if (isset($_POST['adminCheck'])) 
            {
                $valueAdmin = 1;
            } 
            else 
            {
                $valueAdmin = 0;
            }

            $selqDbtbCheck = "SELECT * FROM `Dashboard`.`Users` WHERE username='$username'";
            $resultCheck = mysqli_query($link, $selqDbtbCheck) or die(mysqli_error($link));

            if (mysqli_num_rows($resultCheck) > 0) 
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
    else if(isset($_REQUEST['login']))//Escape 
    {
        $username = mysqli_real_escape_string($link, $_POST['loginUsername']);
        $ldapUsername = "cn=". $_POST['loginUsername'] . ",dc=ldap,dc=disit,dc=org";
        $password = mysqli_real_escape_string($link, $_POST['loginPassword']);
        $ldapPassword = $_POST['loginPassword'];
        $ldapOk = false;
        
        //Per prima cosa verifichiamo se è su LDAP, altrimenti su account list locale
        $ds = ldap_connect($ldapServer, $ldapPort);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $bind = ldap_bind($ds, $ldapUsername, $ldapPassword);
        
        if($ds && $bind)
        {
            if(checkLdapMembership($ds, $ldapUsername, $ldapTool))
            {
               if(checkLdapRole($ds, $ldapUsername, "ToolAdmin"))
               {
                  $ldapRole = "ToolAdmin";
                  $ldapOk = true;
                  $_SESSION['loggedUsername'] = $username;
                  $_SESSION['loggedRole'] = "ToolAdmin";
                  $_SESSION['loggedType'] = "ldap";
                  //notificatorLogin($notificatorUrl, $notificatorApiUsr, $notificatorApiPwd, $username, $password);
                  mysqli_close($link);
                  header("location: dashboard_mng.php");
               }
               else
               {
                   if(checkLdapRole($ds, $ldapUsername, "AreaManager"))
                   {
                      $ldapRole = "AreaManager";
                      $ldapOk = true;
                      $_SESSION['loggedUsername'] = $username;
                      $_SESSION['loggedRole'] = "AreaManager";
                      $_SESSION['loggedType'] = "ldap";
                      //notificatorLogin($notificatorUrl, $notificatorApiUsr, $notificatorApiPwd, $username, $password);
                      mysqli_close($link);
                      header("location: dashboard_mng.php");
                   }
                   else
                   {
                      if(checkLdapRole($ds, $ldapUsername, "Manager"))
                      {
                         $ldapRole = "Manager";
                         $ldapOk = true;
                         $_SESSION['loggedUsername'] = $username;
                         $_SESSION['loggedRole'] = "Manager";
                         $_SESSION['loggedType'] = "ldap";
                         //notificatorLogin($notificatorUrl, $notificatorApiUsr, $notificatorApiPwd, $username, $password);
                         mysqli_close($link);
                         header("location: dashboard_mng.php");
                      }
                   }
               }
            }
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
               $_SESSION['loggedUsername'] = $username;
               $_SESSION['loggedRole'] = $row["admin"];
               $_SESSION['loggedType'] = "local";
               //notificatorLogin($notificatorUrl, $notificatorApiUsr, $notificatorApiPwd, $username, $password);
               mysqli_close($link);
               header("location: dashboard_mng.php");
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
    else if(isset($_REQUEST['creation_dashboard']))//Escape 
    {
        $name_dashboard = mysqli_real_escape_string($link, $_POST['inputNameDashboard']); 
        $title = mysqli_real_escape_string($link, $_POST['inputTitleDashboard']);  
        $subtitle = mysqli_real_escape_string($link, $_POST['inputSubTitleDashboard']);  
        $color = mysqli_real_escape_string($link, $_POST['inputColorDashboard']);  
        $background = mysqli_real_escape_string($link, $_POST['inputColorBackgroundDashboard']);  
        $externalColor = mysqli_real_escape_string($link, $_POST['inputExternalColorDashboard']);  
        $nCols = mysqli_real_escape_string($link, $_POST['inputWidthDashboard']);  
        $headerFontColor = mysqli_real_escape_string($link, $_POST['headerFontColor']);  
        $headerFontSize = mysqli_real_escape_string($link, $_POST['headerFontSize']);  
        $logoLink = null;
        $widgetsBorders = mysqli_real_escape_string($link, $_POST['widgetsBorders']); 
        $widgetsBordersColor = mysqli_real_escape_string($link, $_POST['inputWidgetsBordersColor']);
        $visibility = mysqli_real_escape_string($link, $_POST['inputDashboardVisibility']);
        $selectedVisibilityUsers = [];

        if($headerFontSize > 45)
        {
            $headerFontSize = 45;
        }
        
        $dashboardAuthorName = $_SESSION['loggedUsername'];

        //Logo della dashboard
        $uploadFolder = "../img/dashLogos/".$name_dashboard."/";
        
        $queryFail = false;

        if(isset($_POST['creation_dashboard']) && $_FILES['dashboardLogoInput']['size'] > 0)
        {
            mkdir("../img/dashLogos/");
            mkdir($uploadFolder);

            if(!is_dir($uploadFolder))  
            {  
                echo '<script type="text/javascript">';
                echo 'alert("Directory dashLogos/"' . $name_dashboard . '"/ does not exist");';
                echo 'window.location.href = "dashboard_mng.php";';
                echo '</script>';  
            }   
            else   
            {  
                if(!is_writable($uploadFolder))
                {
                    echo '<script type="text/javascript">';
                    echo 'alert("Directory dashLogos is not writable");';
                    echo 'window.location.href = "dashboard_mng.php";';
                    echo '</script>';
                }
                else
                {
                    $pointIndex = strrpos($_FILES['dashboardLogoInput']['name'], ".");
                    $extension = substr($_FILES['dashboardLogoInput']['name'], $pointIndex);
                    $filename = 'logo'.$extension;

                    if(!move_uploaded_file($_FILES['dashboardLogoInput']['tmp_name'], $uploadFolder.$filename))  
                    {  
                        echo '<script type="text/javascript">';
                        echo 'alert("Something has gone wrong during logo upload.");';
                        echo 'window.location.href = "dashboard_mng.php";';
                        echo '</script>'; 
                    }  
                    else  
                    {  
                        $selqDbtbCheck2 = "SELECT * FROM Dashboard.Config_dashboard WHERE name_dashboard='$name_dashboard' AND user='$dashboardAuthorName'";
                        $resultCheck2 = mysqli_query($link, $selqDbtbCheck2) or die(mysqli_error($link));

                        if (mysqli_num_rows($resultCheck2) > 0) 
                        { 
                            mysqli_close($link);
                            echo '<script type="text/javascript">';
                            echo 'alert("Chosen dashboard name is already in use: please choose another one.");';
                            echo 'window.location.href = "dashboard_mng.php";';
                            echo '</script>';
                        }
                        else 
                        {
                            //New version: lasciamo gli addendi espliciti per agevolare la lettura
                            $width = ($nCols * 78) + 10;

                            if($_POST['dashboardLogoLinkInput'] != '')
                            {
                                if(strpos($_POST['dashboardLogoLinkInput'], 'http://') === false) 
                                {
                                    $logoLink = 'http://' . $_POST['dashboardLogoLinkInput'];
                                }
                                else 
                                {
                                    $logoLink = $_POST['dashboardLogoLinkInput'];
                                }

                                $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                                (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                                `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`,`color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `logoFilename`, `logoLink`, `widgetsBorders`, `widgetsBordersColor`, visibility) 
                                VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$filename', '$logoLink', '$widgetsBorders', '$widgetsBordersColor', '$visibility')";
                            }
                            else
                            {
                                $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                                (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                                `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`,`color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `logoFilename`, `widgetsBorders`, `widgetsBordersColor`, visibility) 
                                VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$filename', '$widgetsBorders', '$widgetsBordersColor', '$visibility')";
                            }

                            mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
                            $result3 = mysqli_query($link, $insqDbtb2) or die(mysqli_error($link));
                            
                            if ($result3) 
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
                                                echo 'window.location.href = "dashboard_mng.php";';
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
                                echo 'window.location.href = "dashboard_mng.php";';
                                echo '</script>';
                                die();
                            }
                        }
                    }
                }
            } 
        }
        else
        {
            //Nessun file caricato
            $selqDbtbCheck2 = "SELECT * FROM Dashboard.Config_dashboard WHERE name_dashboard='$name_dashboard' AND user='$dashboardAuthorName'";
            $resultCheck2 = mysqli_query($link, $selqDbtbCheck2) or die(mysqli_error($link));

            if (mysqli_num_rows($resultCheck2) > 0) 
            { 
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Il nome dato alla nuova dashboard è già in uso: ripetere creazione dashboard");';
                echo 'window.location.href = "dashboard_mng.php";';
                echo '</script>';
            }
            else 
            {
                //New version: lasciamo gli addendi espliciti per agevolare la lettura
                $width = ($nCols * 78) + 10;

                mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
                
                $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`, `color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `widgetsBorders`, `widgetsBordersColor`, visibility) 
                VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$widgetsBorders', '$widgetsBordersColor', '$visibility')";
                
                $result3 = mysqli_query($link, $insqDbtb2) or die(mysqli_error($link));
                
                if ($result3) 
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
                                     echo 'window.location.href = "dashboard_mng.php";';
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
                    echo 'window.location.href = "dashboard_mng.php";';
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
            header("location: dashboard_configdash.php");
        }
    } 
    else if(isset($_REQUEST['add_widget']))//Escape 
    {
        if(isset($_POST['textarea-selected-metrics']) && $_POST['textarea-selected-metrics'] != "") 
        {
            $id_dashboard = $_SESSION['dashboardId'];
            $dashboardName = $_SESSION['dashboardTitle'];
            $nextId = 1;
            $firstFreeRow = $_POST['firstFreeRowInput'];
            $selqDbtbMaxSel2 = "SELECT MAX(Id) AS MaxId FROM Dashboard.Config_widget_dashboard";
            $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2) or die(mysqli_error($link));
            if($resultMaxSel2) 
            {
                while($rowMaxSel2 = mysqli_fetch_array($resultMaxSel2)) 
                {
                    if ((!is_null($rowMaxSel2['MaxId'])) && (!empty($rowMaxSel2['MaxId']))) 
                    {
                        $nextId = $rowMaxSel2['MaxId'] + 1;
                    }
                }

                $id_metric = mysqli_real_escape_string($link, $_POST['textarea-selected-metrics']); 
                $type_widget = mysqli_real_escape_string($link, $_POST['select-widget']); 
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
                $enableFullscreenTab = 'no';
                $enableFullscreenModal = 'no'; 
                
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
                        $infoJsonFirstAxis[$name] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        $infoJsonSecondAxis[$name] = $_POST[$name];
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
                            $infoJsonFirstAxis[$name] = $_POST[$name];
                        }
                        unset($name);

                        foreach ($infoNamesJsonSecondAxis as $name) 
                        {
                            $infoJsonSecondAxis[$name] = $_POST[$name];
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
                        $infoJsonFirstAxis[$name] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        $infoJsonSecondAxis[$name] = $_POST[$name];
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
                        $infoJsonFirstAxis[$name] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        $infoJsonSecondAxis[$name] = $_POST[$name];
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

                    foreach ($infoNamesJsonFirstAxis as $name) 
                    {
                        $infoJsonFirstAxis[$name] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        $infoJsonSecondAxis[$name] = $_POST[$name];
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
                        $infoJsonFirstAxis[$name] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        $infoJsonSecondAxis[$name] = $_POST[$name];
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
                        $infoJsonFirstAxis[$name] = $_POST[$name];
                    }
                    unset($name);

                    foreach ($infoNamesJsonSecondAxis as $name) 
                    {
                        $infoJsonSecondAxis[$name] = $_POST[$name];
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

                if(isset($_POST['inputShowTitle'])&&($_POST['inputShowTitle']!="")&&($type_widget == 'widgetExternalContent'))
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
                  if(($type_widget == 'widgetBarContent')||($type_widget == 'widgetColunmContent')||($type_widget == 'widgetGaugeChart')||($type_widget == 'widgetSingleContent')||($type_widget == 'widgetSpeedometer')||($type_widget == 'widgetTimeTrend')||($type_widget == 'widgetTimeTrendCompare'))
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
                if(isset($_POST['inputComuneWidget']) && $_POST['inputComuneWidget'] != "") 
                {
                    $comune_widget = strtoupper($_POST['inputComuneWidget']);
                    $name_widget = preg_replace('/\+/', '', $id_metric) . "_" . $comune_widget . "_" . $id_dashboard . "_" . $type_widget . $nextId;
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
                              mkdir("../img/widgetButtonImages/");
                           }
                           
                           if(!file_exists("../img/widgetButtonImages/" . $name_widget))
                           {
                              mkdir("../img/widgetButtonImages/" . $name_widget);
                           }
                           
                           $pointIndex = strrpos($_FILES['addWidgetBtnFile']['name'], ".");
                           $extension = substr($_FILES['addWidgetBtnFile']['name'], $pointIndex);
                           $fileUploaded = move_uploaded_file($_FILES['addWidgetBtnFile']['tmp_name'], "../img/widgetButtonImages/" . $name_widget . "/image" . $extension);
                           
                           $styleParametersArray["hasImage"] = "yes";
                           $styleParametersArray["imageWidth"] = $_REQUEST["addWidgetBtnImgWidth"];
                           $styleParametersArray["imageHeight"] = $_REQUEST["addWidgetBtnImgHeight"];
                           $styleParametersArray["imageName"] = "image" . $extension;
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
                    mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
                    
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
                
                if($insqDbtb3 = $link->prepare("INSERT INTO Dashboard.Config_widget_dashboard (Id, name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, frequency_w, temporal_range_w, municipality_w, infoMessage_w, link_w, parameters, frame_color_w, udm, udmPos, fontSize, fontColor, controlsPosition, showTitle, controlsVisibility, zoomFactor, defaultTab, zoomControlsColor, scaleX, scaleY, headerFontColor, styleParameters, infoJson, serviceUri, viewMode, hospitalList, notificatorRegistered, notificatorEnabled, enableFullscreenTab, enableFullscreenModal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) 
                {
                    $insqDbtb3->bind_param('isissiiiisssssssssssissssdisddssssssssss', $nextId, $name_widget, $id_dashboard, $id_metric, $type_widget, $firstFreeRow, $nCol, $sizeRowsWidget, $sizeColumnsWidget, $title_widget, $color_widget, $freq_widget, $int_temp_widget, $comune_widget, $message_widget, $url_widget, $parameters, $frame_color, $inputUdmWidget, $inputUdmPosition, $fontSize, $fontColor, $controlsPosition, $showTitle, $controlsVisibility, $zoomFactor, $defaultTab, $zoomControlsColor, $scaleX, $scaleY, $headerFontColor, $styleParameters, $infoJson, $serviceUri, $viewMode, $hospitalList, $notificatorRegistered, $notificatorEnabled, $enableFullscreenTab, $enableFullscreenModal);
                    $result4 = $insqDbtb3->execute();
                    if($result4) 
                    {
                        mysqli_close($link);
                        header("location: dashboard_configdash.php");
                        
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
                                 if(($type_widget == 'widgetBarContent')||($type_widget == 'widgetColunmContent')||($type_widget == 'widgetGaugeChart')||($type_widget == 'widgetSingleContent')||($type_widget == 'widgetSpeedometer')||($type_widget == 'widgetTimeTrend')||($type_widget == 'widgetTimeTrendCompare'))
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
                    die("Error message: ". $mysqli->error);
                    echo '<script type="text/javascript">';
                    echo 'alert("Error:' . $mysqli->error . '");';
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

        } 
        else 
        {
            echo '<script type="text/javascript">';
            echo 'alert("Error: Nessuna metrica selezionata - ripetere inserimento widget");';
            echo 'window.location.href = "dashboard_configdash.php";';
            echo '</script>';
        }
    } //Fine caso add_widget
    else if(isset($_REQUEST['modify_dashboard']))//Escape 
    {
        $isAdmin = $_SESSION['loggedRole'];
        $dashboardName = mysqli_real_escape_string($link, $_POST['selectedDashboardName']);
        $dashboardAuthorName = mysqli_real_escape_string($link, $_POST['selectedDashboardAuthorName']);
        
        //Reperimento da DB del dashboardId e dell'id dell'autore della dashboard
        $query = "SELECT Dashboard.Config_dashboard.Id, Dashboard.Config_dashboard.name_dashboard, Dashboard.Config_dashboard.user FROM Dashboard.Config_dashboard WHERE name_dashboard = '$dashboardName' AND Dashboard.Config_dashboard.user = '$dashboardAuthorName'";
        $result = mysqli_query($link, $query) or die(mysqli_error($link));

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
    else if(isset($_REQUEST['modify_widget']))//Escape 
    {
        $dashboardName2 = $_SESSION['dashboardTitle'];
        $type_widget_m = mysqli_real_escape_string($link, $_POST['select-widget-m']);
        $title_widget_m = NULL;
        $color_widget_m = mysqli_real_escape_string($link, $_POST['inputColorWidgetM']); 
        $freq_widget_m = NULL;
        $name_widget_m = $_POST['inputNameWidgetM'];
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
                $infoJsonFirstAxisM[$nameM] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                $infoJsonSecondAxisM[$nameM] = $_POST[$nameM];
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
                    $infoJsonFirstAxisM[$nameM] = $_POST[$nameM];
                }
                unset($nameM);

                foreach ($infoNamesJsonSecondAxisM as $nameM) 
                {
                    $infoJsonSecondAxisM[$nameM] = $_POST[$nameM];
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
                $infoJsonFirstAxisM[$nameM] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                $infoJsonSecondAxisM[$nameM] = $_POST[$nameM];
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
                $infoJsonFirstAxisM[$nameM] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                $infoJsonSecondAxisM[$nameM] = $_POST[$nameM];
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
                $infoJsonFirstAxisM[$nameM] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                $infoJsonSecondAxisM[$nameM] = $_POST[$nameM];
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

            foreach ($infoNamesJsonFirstAxisM as $nameM) 
            {
                $infoJsonFirstAxisM[$nameM] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                $infoJsonSecondAxisM[$nameM] = $_POST[$nameM];
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
                $infoJsonFirstAxisM[$nameM] = $_POST[$nameM];
            }
            unset($nameM);

            foreach ($infoNamesJsonSecondAxisM as $nameM) 
            {
                $infoJsonSecondAxisM[$nameM] = $_POST[$nameM];
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
            if(($type_widget_m == 'widgetBarContent')||($type_widget_m == 'widgetColunmContent')||($type_widget_m == 'widgetGaugeChart')||($type_widget_m == 'widgetSingleContent')||($type_widget_m == 'widgetSpeedometer')||($type_widget_m == 'widgetTimeTrend')||($type_widget_m == 'widgetTimeTrendCompare'))
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
            /*if (strpos($_POST['urlWidgetM'], 'http://') === false) 
            {
                $url_m = 'http://' . $_POST['urlWidgetM'];
            }
            else 
            {
                $url_m = $_POST['urlWidgetM'];
            }*/
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
                       mkdir("../img/widgetButtonImages/");
                    }

                    if(!file_exists("../img/widgetButtonImages/" . $name_widget_m))
                    {
                       mkdir("../img/widgetButtonImages/" . $name_widget_m);
                    }
                    else
                    {
                       array_map('unlink', glob("../img/widgetButtonImages/" . $name_widget_m . "/*.*"));
                    }

                    $pointIndex = strrpos($_FILES['editWidgetBtnFile']['name'], ".");
                    $extension = substr($_FILES['editWidgetBtnFile']['name'], $pointIndex);
                    $fileUploaded = move_uploaded_file($_FILES['editWidgetBtnFile']['tmp_name'], "../img/widgetButtonImages/" . $name_widget_m . "/image" . $extension);
                    $styleParametersArray["imageName"] = "image" . $extension;
                 }
                 else
                 {
                    $filesArray = scandir("../img/widgetButtonImages/" . $name_widget_m);
                    
                    for($i = 0; $i < count($filesArray); $i++)
                    {
                       if(strpos($filesArray[$i], 'image') !== false)
                       {
                          $styleParametersArray["imageName"] = $filesArray[$i];
                       }
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
           
            $upsqDbtb = $link->prepare("UPDATE Dashboard.Config_widget_dashboard SET type_w = ?, size_columns = ?, size_rows = ?, title_w = ?, color_w = ?, frequency_w = ?, temporal_range_w = ?, municipality_w = ?, infoMessage_w = ?, link_w = ?, parameters = ?, frame_color_w = ?, udm = ?, udmPos = ?, fontSize = ?, fontColor = ?, controlsPosition = ?, showTitle = ?, controlsVisibility = ?, defaultTab = ?, zoomControlsColor = ?, headerFontColor = ?, styleParameters = ?, infoJson = ?, serviceUri = ?, viewMode = ?, hospitalList = ?, lastSeries = ?, notificatorRegistered = ?, notificatorEnabled = ?, enableFullscreenTab = ?, enableFullscreenModal = ? WHERE name_w = ? AND id_dashboard = ?");
            $upsqDbtb->bind_param('siisssssssssssissssisssssssssssssi', $type_widget_m, $col_m, $row_m, $title_widget_m, $color_widget_m, $freq_widget_m, $int_temp_widget_m, $comune_widget_m, $info_m, $url_m, $parametersM, $color_frame_m, $inputUdmWidget, $inputUdmPosition, $fontSizeM, $fontColorM, $controlsPosition, $showTitle, $controlsVisibility, $inputDefaultTabM, $zoomControlsColorM, $headerFontColorM, $styleParametersM, $infoJsonM, $serviceUri, $viewMode, $hospitalList, $lastSeries, $notificatorRegisteredNew, $notificatorEnabledNew, $enableFullscreenTabM, $enableFullscreenModalM, $name_widget_m, $id_dashboard2);
            $result7 = $upsqDbtb->execute();
            
            if($result7) 
            {
                mysqli_close($link);
                header("location: dashboard_configdash.php");
                
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
                           if(($type_widget_m == 'widgetBarContent')||($type_widget_m == 'widgetColunmContent')||($type_widget_m == 'widgetGaugeChart')||($type_widget_m == 'widgetSingleContent')||($type_widget_m == 'widgetSpeedometer')||($type_widget_m == 'widgetTimeTrend')||($type_widget_m == 'widgetTimeTrendCompare'))
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
    else if(isset($_REQUEST['add_new_metric']))//Escape 
    {
        $valueThreshold = 'null';
        $valueThresholdEvalCount = 'null';
        $valueTresholdTime = 'null';

        $valueIdMetric = $_POST['nameMetric'];
        $valueDescription = $_POST['descriptionMetric'];
        if(isset($_POST['queryMetric'])) 
        {
            $query_composta = $_POST['queryMetric'];
            $valueQuery = $_POST['queryMetric'];
        }
        
        if(isset($_POST['queryMetric2']) && $_POST['queryMetric2'] != NULL) 
        {
            $valueQuery2 = $_POST['queryMetric2'];
            $query_composta = $valueQuery . "|" . $valueQuery2;
        }

        $valueQueryType = mysqli_real_escape_string($link, $_POST['queryTypeMetric']); 
        $valueMetricType = mysqli_real_escape_string($link, $_POST['typeMetric']); 
        $valueFrequency = mysqli_real_escape_string($link, $_POST['frequencyMetric']);
        $valueProcessType = mysqli_real_escape_string($link, $_POST['processTypeMetric']); 
        $valueArea = mysqli_real_escape_string($link, $_POST['areaMetric']); 
        $valueSource = mysqli_real_escape_string($link, $_POST['sourceMetric']); 
        $valueDescriptionShort = mysqli_real_escape_string($link, $_POST['descriptionShortMetric']); 
        
        if(isset($_POST['dataSourceMetric']))
        {
            $dataSourceComposto = mysqli_real_escape_string($link, $_POST['dataSourceMetric']); 
            $valueDataSource = mysqli_real_escape_string($link, $_POST['dataSourceMetric']); 
        }
        if(isset($_POST['dataSourceMetric2']) && $_POST['dataSourceMetric2'] != NULL) 
        {
            $valueDataSource2 = mysqli_real_escape_string($link, $_POST['dataSourceMetric2']); 
            $dataSourceComposto = $valueDataSource . "|" . $valueDataSource2;
        }
        
        $valueDataSource = mysqli_real_escape_string($link, $_POST['dataSourceMetric']);
        
        if($_POST['thresholdMetric'] != '') 
        {
            $valueThreshold = mysqli_real_escape_string($link, $_POST['thresholdMetric']); 
        } 
        else 
        {
            $valueThreshold = 'null';
        }
        
        $valueThresholdEval = mysqli_real_escape_string($link, $_POST['thresholdEvalMetric']); 
        
        if($_POST['thresholdEvalCountMetric'] != '') 
        {
            $valueThresholdEvalCount = mysqli_real_escape_string($link, $_POST['thresholdEvalCountMetric']);
        } 
        else 
        {
            $valueThresholdEvalCount = 'null';
        }
        
        if($_POST['thresholdTimeMetric'] != '') 
        {
            $valueTresholdTime = mysqli_real_escape_string($link, $_POST['thresholdTimeMetric']); 
        } 
        else 
        {
            $valueTresholdTime = 'null';
        }
        
        if (isset($_POST['storingDataMetric'])) 
        {
            $valueStoringData = 1;
        } 
        else 
        {
            $valueStoringData = 0;
        }
        
        if(isset($_POST['contextMetric'])) 
        {
            $valueMunicipalityOption = 1;
        } 
        else 
        {
            $valueMunicipalityOption = 0;
        }
        if(isset($_POST['timeRangeMetric'])) 
        {
            $valueTimeRangeOption = 1;
        } 
        else 
        {
            $valueTimeRangeOption = 0;
        }

        $insqDbtb6 = "INSERT INTO Dashboard.Descriptions(IdMetric, description, status, query, query2, queryType, metricType, frequency, processType, area, source, description_short , dataSource, threshold, thresholdEval, thresholdEvalCount, thresholdTime, storingData, municipalityOption, timeRangeOption) 
        VALUES ('$valueIdMetric',\"" . $valueDescription . "\",'Attivo',\"" . $query_composta . "\",'','$valueQueryType','$valueMetricType','$valueFrequency','$valueProcessType','$valueArea','$valueSource','$valueDescriptionShort','$dataSourceComposto', $valueThreshold , '$valueThresholdEval', $valueThresholdEvalCount,$valueTresholdTime,'$valueStoringData','$valueMunicipalityOption','$valueTimeRangeOption')";
        echo $insqDbtb6;
        $result8 = mysqli_query($link, $insqDbtb6) or die(mysqli_error($link));


        $file = "querylog.txt";
        file_put_contents($file, $result8);
        if ($result8)
        {
            mysqli_close($link);
            header("location: metrics_mng.php");
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error during metric creation");';
            echo 'window.location.href = "metrics_mng.php";';
            echo '</script>';
        } 
    } 
    else if (isset($_REQUEST['modify_metric']))//Escape 
    {
        $valueIdMetric_M = mysqli_real_escape_string($link, $_POST['modify-nameMetric']);
        $valueDescription_M = htmlspecialchars($_POST['modify-descriptionMetric'], ENT_QUOTES);

        if(isset($_POST['modify-queryMetric'])) 
        {
            $queryComposta_M = mysql_real_escape_string($_POST['modify-queryMetric']);
            $valueQuery_M = mysql_real_escape_string($_POST['modify-queryMetric']);
        }
        if(isset($_POST['modify-queryMetric2']) && $_POST['modify-queryMetric2'] != NULL)
        {
            $valueQuery2_M = mysql_real_escape_string($_POST['modify-queryMetric2']);
            $queryComposta_M = mysql_real_escape_string($valueQuery_M . "|" . $valueQuery2_M);
        }
        
        $valueQueryType_M = mysqli_real_escape_string($link, $_POST['modify-queryTypeMetric']);
        echo $valueQuery_M;
        
        $valueFrequency_M = mysqli_real_escape_string($link, $_POST['modify-frequencyMetric']);
        $valueProcessType_M = mysqli_real_escape_string($link, $_POST['modify-processTypeMetric']);
        $valueArea_M = mysqli_real_escape_string($link, $_POST['modify-areaMetric']); 
        $valueSource_M = mysqli_real_escape_string($link, $_POST['modify-sourceMetric']);
        $valueDescriptionShort_M = mysqli_real_escape_string($link, $_POST['modify-descriptionShortMetric']);
        
        if(isset($_POST['modify-dataSourceMetric'])) 
        {
            $valueDataSource_M = mysqli_real_escape_string($link, $_POST['modify-dataSourceMetric']); 
            $datasourceComposto_M = mysqli_real_escape_string($link, $_POST['modify-dataSourceMetric']);
        }
        
        if(isset($_POST['modify-datasourceMetric2']) && $_POST['modify-datasourceMetric2'] != NULL) 
        {
            $valueDataSource2_M = mysqli_real_escape_string($link, $_POST['modify-datasourceMetric2']); 
            $datasourceComposto_M = $valueDataSource_M . "|" . $valueDataSource2_M;
        }
        
        if($_POST['modify-thresholdMetric'] != '') 
        {
            $valueThreshold_M = mysqli_real_escape_string($link, $_POST['modify-thresholdMetric']);
        } 
        else 
        {
            $valueThreshold_M = 'null';
        }
        
        $valueThresholdEval_M = mysqli_real_escape_string($link, $_POST['modify-thresholdEvalMetric']);
        
        if($_POST['modify-thresholdEvalCountMetric'] != '') 
        {
            $valueThresholdEvalCount_M = mysqli_real_escape_string($link, $_POST['modify-thresholdEvalCountMetric']);
        } 
        else 
        {
            $valueThresholdEvalCount_M = 'null';
        }

        if ($_POST['modify-thresholdTime'] != '') 
        {
            $valueTresholdTime_M = mysqli_real_escape_string($link, $_POST['modify-thresholdTime']);
        } 
        else 
        {
            $valueTresholdTime_M = 'null';
        }
        
        if(isset($_POST['modify-storingDataMetric'])) 
        {
            $valueStoringData_M = 1;
        } 
        else 
        {
            $valueStoringData_M = 0;
        }
        
        if(isset($_POST['modify-contextMetric'])) 
        {
            $valueMunicipalityOption_M = 1;
        } 
        else 
        {
            $valueMunicipalityOption_M = 0;
        }
        
        if(isset($_POST['modify-timeRangeMetric'])) 
        {
            $valueTimeRangeOption_M = 1;
        } 
        else 
        {
            $valueTimeRangeOption_M = 0;
        }
        
        $updqDbtbY = "UPDATE Dashboard.Descriptions SET description = \"" . $valueDescription_M . "\", query=\"" . $queryComposta_M . "\", query2='', queryType='$valueQueryType_M', frequency='$valueFrequency_M', processType='$valueProcessType_M', area='$valueArea_M', source='$valueSource_M', description_short='$valueDescriptionShort_M', dataSource='$datasourceComposto_M', threshold=$valueThreshold_M, thresholdEval='$valueThresholdEval_M', thresholdEvalCount=$valueThresholdEvalCount_M, thresholdTime=$valueTresholdTime_M, storingData=$valueStoringData_M, municipalityOption=$valueMunicipalityOption_M, timeRangeOption=$valueTimeRangeOption_M WHERE IdMetric='$valueIdMetric_M' ";
        $resultY = mysqli_query($link, $updqDbtbY) or die(mysqli_error($link));
        
        if($resultY) 
        {
            mysqli_close($link);
            header("location: metrics_mng.php");
        }
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Errore during metric modify");';
            echo 'window.location.href = "metrics_mng.php";';
            echo '</script>';
        }
    } 
    else if (isset($_REQUEST['delete_metric']))//Escape 
    {
        echo 'eliminazione della metrica avvenuta';
        
        $name_metric_select = mysqli_real_escape_string($link, $_POST['delete_metric']); 
        $delqDbtbZ = "DELETE FROM Dashboard.Descriptions WHERE IdMetric='$name_metric_select'";
        $resultZ = mysqli_query($link, $delqDbtbZ) or die(mysqli_error($link));
        if($resultZ) 
        {
            mysqli_close($link);
            header("location: metrics_mng.php");
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error during metric delete");';
            echo 'window.location.href = "metrics_mng.php";';
            echo '</script>';
        }
    } 
    else if (isset($_REQUEST['modify-status'])) //Escape
    {
        echo 'modifica status della metrica';
        $name_metric_selected = mysqli_real_escape_string($link, $_POST['modify-status']);
        $query_select_status = "SELECT Descriptions.status FROM Dashboard.Descriptions WHERE Descriptions.IdMetric='$name_metric_selected'";
        $valore = mysqli_query($link, $query_select_status) or die(mysqli_error($link));

        if ($valore->num_rows > 0) 
        {
            while ($rowStatus = mysqli_fetch_array($valore)) 
            {
                echo ('Il valore è: ' + $valore);
                if ($rowStatus[0] == 'Non Attivo') 
                {
                    $updqDbtbStatus = "UPDATE Dashboard.Descriptions SET Descriptions.status='Attivo' WHERE Descriptions.IdMetric='$name_metric_selected'";
                } 
                else if($rowStatus[0] == 'Attivo') 
                {
                    $updqDbtbStatus = "UPDATE Dashboard.Descriptions SET Descriptions.status='Non Attivo' WHERE Descriptions.IdMetric='$name_metric_selected'";
                }
                
                $resultStatus = mysqli_query($link, $updqDbtbStatus) or die(mysqli_error($link));
                
                if ($resultStatus) 
                {
                    mysqli_close($link);
                    header("location: metrics_mng.php");
                } 
                else 
                {
                    mysqli_close($link);
                    echo '<script type="text/javascript">';
                    echo 'alert("Error during status metric modify");';
                    echo 'window.location.href = "metrics_mng.php";';
                    echo '</script>';
                }
            }
        }
    } 
    else if(isset($_REQUEST['modify_status_dashboard']))//Escape
    {
        $dashboardName = mysqli_real_escape_string($link, $_POST['selectedDashboardNameForStatusChange']);
        $dashboardAuthorName = mysqli_real_escape_string($link, $_POST['selectedDashboardAuthorNameForStatusChange']);
        
        //Reperimento da DB del nome dell'autore della dashboard, del dashboardId e dell'id dell'autore della dashboard (L'ID NON SERVE PIU')
        $query = "SELECT Dashboard.Config_dashboard.Id as dashboardId, Dashboard.Config_dashboard.status_dashboard as dashboardStatus, Users.username as dashboardAuthorName, Users.idUser as idUser FROM Dashboard.Config_dashboard INNER JOIN Users ON Config_dashboard.user = Users.IdUser WHERE name_dashboard = '$dashboardName' AND Users.username = '$dashboardAuthorName'";
        $result = mysqli_query($link, $query) or die(mysqli_error($link));
        
        if($result) 
        {
            if($result->num_rows > 0) 
            {
                while($row = mysqli_fetch_array($result)) 
                {
                    $_SESSION['dashboardId'] = $row['dashboardId'];
                    $_SESSION['dashboardAuthorName'] = $dashboardAuthorName;
                    $dashboardStatus = $row['dashboardStatus'];
                }
                
                if(canEditDashboard())
                {
                    if($dashboardStatus == "0") 
                    {
                        $dashboardNewStatus = 1;
                    } 
                    else if($dashboardStatus == "1") 
                    {
                        $dashboardNewStatus = 0;
                    }
                    
                    $update = "UPDATE Dashboard.Config_dashboard SET Config_dashboard.status_dashboard = " . $dashboardNewStatus . " WHERE Config_dashboard.Id = " . $_SESSION['dashboardId'];

                    $resultStatusDash = mysqli_query($link, $update) or die(mysqli_error($link));
                    mysqli_close($link);

                    if($resultStatusDash) 
                    {
                        header("location: dashboard_mng.php");
                    } 
                    else 
                    {
                        echo '<script type="text/javascript">';
                        echo 'alert("Error during status metric modify");';
                        echo 'window.location.href = "dashboard_mng.php";';
                        echo '</script>';
                    }
                }
                else
                {
                    header("location: unauthorizedUser.php");
                }
            }
            else
            {
                header("location: unauthorizedUser.php");
            }
        }
        else
        {
            header("location: unauthorizedUser.php");
        }
        mysqli_close($link);
    } 
    else if (isset($_REQUEST['create_dataSources']))//Escape 
    {
        $id_ds = mysqli_real_escape_string($link, $_POST['name_Id_dataSource']);
        $url_ds = mysqli_real_escape_string($link, $_POST['url_dataSource']); 
        $dataBase_ds = mysqli_real_escape_string($link, $_POST['database_dataSource']); 
        $user_ds = mysqli_real_escape_string($link, $_POST['username_dataSource']); 
        $pass_ds = mysqli_real_escape_string($link, $_POST['password_dataSource']); 
        $dataType_ds = mysqli_real_escape_string($link, $_POST['databaseType_dataSource']);

        echo $id_ds . '<br>';
        echo $url_ds . '<br>';
        echo $dataBase_ds . '<br>';
        echo $user_ds . '<br>';
        echo $pass_ds . '<br>';
        echo $dataType_ds . '<br>';

        $insDbDatasource = "INSERT INTO `Dashboard`.`DataSource` (`Id`, `url`, `database`, `username`, `password`, `databaseType`)VALUES ('$id_ds','$url_ds','$dataBase_ds','$user_ds','$pass_ds','$dataType_ds')";
        $resultDataSource = mysqli_query($link, $insDbDatasource) or die(mysqli_error($link));
        if ($resultDataSource) 
        {
            mysqli_close($link);
            header("location: dataSources_mng.php");
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Repeat Data Source creation");';
            echo 'window.location.href = "dataSources_mng.php";';
            echo '</script>';
        }
    } 
    else if (isset($_REQUEST['modify_dataSources'])) //Escape
    {
        $id_ds_M = mysqli_real_escape_string($link, $_POST['name_Id_dataSource_M']);
        $url_ds_M = mysqli_real_escape_string($link, $_POST['url_dataSource_M']); 
        $dataBase_ds_M = mysqli_real_escape_string($link, $_POST['database_dataSource_M']); 
        $user_ds_M = mysqli_real_escape_string($link, $_POST['username_dataSource_M']); 
        $pass_ds_M = mysqli_real_escape_string($link, $_POST['password_dataSource_M']); 
        $dataType_ds_M = mysqli_real_escape_string($link, $_POST['databaseType_dataSource_M']);

        $updateDataSource = "UPDATE `Dashboard`.`DataSource` SET `DataSource`.`url`='$url_ds_M', `DataSource`.`database`='$dataBase_ds_M', `DataSource`.`username`='$user_ds_M', `DataSource`.`password`='$pass_ds_M', `DataSource`.`databaseType`='$dataType_ds_M'  WHERE `DataSource`.`Id`='$id_ds_M'";
        $resultUpdateDataSource = mysqli_query($link, $updateDataSource) or die(mysqli_error($link));
        
        if ($updateDataSource) 
        {
            mysqli_close($link);
            header("location: dataSources_mng.php");
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Ripetere modifica del Datasources");';
            echo 'window.location.href = "dataSources_mng.php";';
            echo '</script>';
        }
    } 
    else if (isset($_REQUEST['add_widget_type'])) //Escape
    {
        $php_w = mysqli_real_escape_string($link, $_POST['php_w']); $_POST[''];
        $dividiPhp = preg_split("/.php/", $php_w);
        $id_w = $dividiPhp[0];

        $intero = mysqli_real_escape_string($link, $_POST['integer_w']); 
        $percentuale = mysqli_real_escape_string($link, $_POST['percentage_w']); 
        $testuale = mysqli_real_escape_string($link, $_POST['textual_w']); 
        $mappa = mysqli_real_escape_string($link, $_POST['map_w']); 
        $sce = mysqli_real_escape_string($link, $_POST['sce_w']); 
        $float= mysqli_real_escape_string($link, $_POST['float_w']); 
        $bottone= mysqli_real_escape_string($link, $_POST['button_w']); 

        $elenco_tipi= $intero.'|'.$percentuale.'|'.$testuale.'|'.$mappa.'|'.$sce.'|'.$float.'|'.$bottone;

        echo ($elenco_tipi);
        $color_w = 0;
        $type_w = NULL;
        $met_w = NULL;  

        if(isset($_POST['mnC_w'])|| $_POST['mnC_w']!=='')
        {
            $minC = mysqli_real_escape_string($link, $_POST['mnC_w']);   
        }
        else
        {
            $minC = NULL;  
        }

        if(isset($_POST['mxC_w'])|| $_POST['mxC_w']!=='')
        {
            $maxC = mysqli_real_escape_string($link, $_POST['mxC_w']); 
        }
        else
        {
            $maxC = NULL;  
        }

        if(isset($_POST['mnR_w'])|| $_POST['mnR_w']!=='')
        {
            $minR = mysqli_real_escape_string($link, $_POST['mnR_w']);   
        }
        else
        {
            $minR = NULL;  
        }      

        if(isset($_POST['mxR_w'])|| $_POST['mxR_w']!=='')
        {
            $maxR = mysqli_real_escape_string($link, $_POST['mxR_w']); 
        }
        else
        {
            $maxR = NULL;
        }

        if(isset($_POST['met_w']))
        {
            $met_w = mysqli_real_escape_string($link, $_POST['met_w']); 
        }

        if(isset($_POST['col_w'])|| $_POST['col_w']!='')
        {
            $color_w = mysqli_real_escape_string($link, $_POST['col_w']);  
        }


        if(isset($_POST['type_w']))
        {
            $type_w = mysqli_real_escape_string($link, $_POST['type_w']); 
        } 

        if(isset($_POST['metric_w']))
        {
            $metric_w = mysqli_real_escape_string($link, $_POST['metric_w']); 
        }

        if(isset($_POST['numeric_range_w']))
        {
            $range_w = 1;
        }
        else
        {
            $range_w = 0;   
        }   

        $stringa = str_replace("|||", "|", $elenco_tipi);
        $stringa = str_replace("||", "|", $stringa); 
        $stringa = str_replace("||", "|", $stringa);
        
        if(substr($stringa, 0, 1) == '|')
        {
            $stringa = substr($stringa, 1);
        }
        
        $type_w = $stringa;

        $insWid = "INSERT INTO `Dashboard`.`Widgets` (`id_type_widget`,`source_php_widget`,`min_row`,`max_row`,`min_col`,`max_col`,`widgetType`,`unique_metric`,`numeric_rangeOption`,`number_metrics_widget`,`color_widgetOption`) VALUES ('$id_w','$php_w','$minR','$maxR','$minC','$maxC','$type_w','$metric_w','$range_w','$met_w','$color_w')";
        $resultWid = mysqli_query($link, $insWid) or die(mysqli_error($link));

        if($insWid) 
        {
            mysqli_close($link);
            header("location: widgets_mng.php");
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Inserimento Widgets");';
            echo 'window.location.href = "widgets_mng.php";';
            echo '</script>';
        }
    }
    else if (isset($_REQUEST['modify_widget_type']))//Escape 
    {
        $intero_m = mysqli_real_escape_string($link, $_POST['integer_m']); 
        $percentuale_m = mysqli_real_escape_string($link, $_POST['percentage_m']); 
        $testuale_m = mysqli_real_escape_string($link, $_POST['textual_m']); 
        $mappa_m = mysqli_real_escape_string($link, $_POST['map_m']); 
        $sce_m = mysqli_real_escape_string($link, $_POST['sce_m']); 
        $float_m= mysqli_real_escape_string($link, $_POST['float_m']); 
        $bottone_m= mysqli_real_escape_string($link, $_POST['button_m']); 

        $elenco_tipi_m= $intero_m.'|'.$percentuale_m.'|'.$testuale_m.'|'.$mappa_m.'|'.$sce_m.'|'.$float_m.'|'.$bottone_m; 
        $id_m = mysqli_real_escape_string($link, $_POST['id_m']); 
        $php_m = mysqli_real_escape_string($link, $_POST['php_m']); 
        $minC_m = mysqli_real_escape_string($link, $_POST['mnC_m']); 
        $maxC_m = mysqli_real_escape_string($link, $_POST['mxC_m']); 
        $minR_m = mysqli_real_escape_string($link, $_POST['mnR_m']); 
        $maxR_m = mysqli_real_escape_string($link, $_POST['mxR_m']); 
        $met_m = mysqli_real_escape_string($link, $_POST['met_m']); 
        $color_m = mysqli_real_escape_string($link, $_POST['col_m']); 
        $type_m = mysqli_real_escape_string($link, $_POST['type_m']); 

        $stringa_m = str_replace("|||", "|", $elenco_tipi_m);
        $stringa_m = str_replace("||", "|", $stringa_m); 
        $stringa_m = str_replace("||", "|", $stringa_m);
        
        if(substr($stringa_m, 0, 1) == '|')
        {
            $stringa_m = substr($stringa_m, 1);
        }
        
        $lunghezza_m = $stringa_m.legth;
        $type_m = $stringa_m;
        $metric_m = $_POST['metric_m'];

        if (isset($_POST['numeric_range_m']))
        {
            $range_m = 1;
        }
        else 
        {
            $range_m = 0;   
        }   

        $modWid = "UPDATE `Dashboard`.`Widgets` SET `Widgets`.`source_php_widget`='$php_m', `Widgets`.`min_row`='$minR_m',`Widgets`.`max_row`='$maxR_m', `Widgets`.`min_col`='$minC_m', `Widgets`.`max_col`='$maxC_m', `Widgets`.`widgetType`='$type_m', `Widgets`.`unique_metric`='$metric_m', `widgets`.`numeric_rangeOption`='$range_m', `widgets`.`number_metrics_widget`='$met_m', `widgets`.`color_widgetOption`='$color_m' WHERE `Widgets`.`id_type_widget`='$id_m'";
        $resultModWid = mysqli_query($link, $modWid) or die(mysqli_error($link));

        if ($modWid) 
        {
            mysqli_close($link);
            header("location: widgets_mng.php");
        } 
        else 
        {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: Inserimento Widgets");';
            echo 'window.location.href = "widgets_mng.php";';
            echo '</script>';
        }
    }
    elseif(isset($_REQUEST['zoomFactorUpdated'])) //Escape
    {
        $zoomFactor = mysqli_real_escape_string($link, $_REQUEST['zoomFactorUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        $upsqDbtb = $link->prepare("UPDATE Dashboard.Config_widget_dashboard SET zoomFactor = ? WHERE Id = ? ");
        $upsqDbtb->bind_param('di', $zoomFactor, $idWidget);
        $resultQuery = $upsqDbtb->execute();

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
        $scaleX = mysqli_real_escape_string($link, $_REQUEST['scaleXUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        $upsqDbtb = $link->prepare("UPDATE Dashboard.Config_widget_dashboard SET scaleX = ? WHERE Id = ? ");
        $upsqDbtb->bind_param('di', $scaleX, $idWidget);
        $resultQuery = $upsqDbtb->execute();

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
        $scaleY = mysqli_real_escape_string($link, $_REQUEST['scaleYUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        $upsqDbtb = $link->prepare("UPDATE Dashboard.Config_widget_dashboard SET scaleY = ? WHERE Id = ? ");
        $upsqDbtb->bind_param('di', $scaleY, $idWidget);
        $resultQuery = $upsqDbtb->execute();

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
    elseif (isset($_REQUEST['widthUpdated'])) //Escape
    {
        $width = mysqli_real_escape_string($link, $_REQUEST['widthUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        $upsqDbtb = $link->prepare("UPDATE Dashboard.Config_widget_dashboard SET size_columns = ? WHERE Id = ? ");
        $upsqDbtb->bind_param('ii', $width, $idWidget);
        $resultQuery = $upsqDbtb->execute();

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
    elseif(isset($_REQUEST['heightUpdated']))//Escape 
    {
        $height = mysqli_real_escape_string($link, $_REQUEST['heightUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        $upsqDbtb = $link->prepare("UPDATE Dashboard.Config_widget_dashboard SET size_rows = ? WHERE Id = ? ");
        $upsqDbtb->bind_param('ii', $height, $idWidget);
        $resultQuery = $upsqDbtb->execute();

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
    elseif(isset($_REQUEST['updatedLastSeries']))//Escape 
    {
        $widgetName = mysqli_real_escape_string($link, $_REQUEST['widgetName']);
        $updatedSeries = $_REQUEST['updatedLastSeries'];
        $upsqDbtb = $link->prepare("UPDATE Dashboard.Config_widget_dashboard SET lastSeries = ? WHERE name_w = ? ");
        $upsqDbtb->bind_param('ss', $updatedSeries, $widgetName);
        $resultQuery = $upsqDbtb->execute();
        
        mysqli_close($link);
    }
    elseif(isset($_REQUEST['showHideDashboardHeader']))//Escape 
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
