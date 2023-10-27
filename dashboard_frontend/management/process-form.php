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

    include '../config.php';
    require '../phpmailer/PHPMailerAutoload.php';
    
    //Definizioni di funzione
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
            return "'" . $original . "'";   // CAMBIATO CON SECURITY ENFORCEMENT CONTROLLARE !
        }
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
            else if(($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "RootAdmin") || ($_SESSION['loggedRole'] == "ToolAdmin"))
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
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    error_reporting(E_ERROR | E_NOTICE);
    
    date_default_timezone_set('Europe/Rome');
    
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
        $ldapUsername = "cn=". $username . "," . $ldapBaseDN;
        $password = mysqli_real_escape_string($link, $_POST['loginPassword']);
        $ldapPassword = $_POST['loginPassword'];
        $ldapOk = false;
        $ldapError = false;
        $dbError = false;
        $errorMsg = false;
        $notFoundMsg = false;
        $resultMsg = "init";
        
        //Per prima cosa verifichiamo se è su LDAP, altrimenti su account list locale
        if($ldapActive == "yes")
        {
            $ds = ldap_connect($ldapServer, $ldapPort);
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            $bind = ldap_bind($ds, $ldapUsername, $ldapPassword);
            if($ds && $bind)
            {
                if(checkLdapMembership($ds, $ldapUsername, $ldapToolName, $ldapBaseDN))
                {
                   if(checkLdapRole($ds, $ldapUsername, "RootAdmin", $ldapBaseDN))
                   {
                        $ldapRole = "RootAdmin";
                        $ldapOk = true;
                        ini_set('session.gc_maxlifetime', $sessionDuration);
                        session_set_cookie_params($sessionDuration);
                        session_start();
                        session_regenerate_id();
                        $_SESSION['sessionEndTime'] = time() + $sessionDuration;
                        $_SESSION['loggedUsername'] = $username;
                        $_SESSION['loggedPassword'] = $ldapPassword;
                        $_SESSION['loggedRole'] = "RootAdmin";
                        $_SESSION['loggedType'] = "ldap";
                        mysqli_close($link);
                        $resultMsg = "Ok";
                   } 
                   else
                   {
                        if(checkLdapRole($ds, $ldapUsername, "ToolAdmin", $ldapBaseDN))
                        {
                           $ldapRole = "ToolAdmin";
                           $ldapOk = true;
                           ini_set('session.gc_maxlifetime', $sessionDuration);
                           session_set_cookie_params($sessionDuration);
                           session_start();
                           session_regenerate_id();
                           $_SESSION['sessionEndTime'] = time() + $sessionDuration;
                           $_SESSION['loggedUsername'] = $username;
                           $_SESSION['loggedPassword'] = $ldapPassword;
                           $_SESSION['loggedRole'] = "ToolAdmin";
                           $_SESSION['loggedType'] = "ldap";
                           mysqli_close($link);
                           $resultMsg = "Ok";
                        }
                        else
                        {
                            if(checkLdapRole($ds, $ldapUsername, "AreaManager", $ldapBaseDN))
                            {
                               $ldapRole = "AreaManager";
                               $ldapOk = true;
                               ini_set('session.gc_maxlifetime', $sessionDuration);
                               session_set_cookie_params($sessionDuration);
                               session_start();
                               session_regenerate_id();
                               $_SESSION['sessionEndTime'] = time() + $sessionDuration;
                               $_SESSION['loggedUsername'] = $username;
                               $_SESSION['loggedPassword'] = $ldapPassword;
                               $_SESSION['loggedRole'] = "AreaManager";
                               $_SESSION['loggedType'] = "ldap";
                               mysqli_close($link);
                               $resultMsg = "Ok";
                            }
                            else
                            {
                               if(checkLdapRole($ds, $ldapUsername, "Manager", $ldapBaseDN))
                               {
                                  $ldapRole = "Manager";
                                  $ldapOk = true;
                                  ini_set('session.gc_maxlifetime', $sessionDuration);
                                  session_set_cookie_params($sessionDuration);
                                  session_start();
                                  session_regenerate_id();
                                  $_SESSION['sessionEndTime'] = time() + $sessionDuration;
                                  $_SESSION['loggedUsername'] = $username;
                                  $_SESSION['loggedPassword'] = $ldapPassword;
                                  $_SESSION['loggedRole'] = "Manager";
                                  $_SESSION['loggedType'] = "ldap";
                                  mysqli_close($link);
                                  $resultMsg = "Ok";
                               }
                            }
                        }
                   }
                   
                /*   $organization = checkLdapOrganization($ds, $ldapUsername, $ldapBaseDN);
                   if (is_null($organization)) {
                        $organization = "none";
                        $organizationLdap = "Other";
                   } else if ($organization == "") {
                        $organization = "none";
                        $organizationLdap = "Other";
                   } else {
                        $organizationLdap = $organization;
                   }
                   $_SESSION['loggedOrganization'] = $organizationLdap; */
                   
                }
            }
            else
            {
                if($ds == false)
                {
                    $ldapError = "LDAP connection not possibile";
                }
                else
                {
                    if($bind == false)
                    {
                        $ldapError = "LDAP bind not possibile";
                    }
                }
            }
        }
        else
        {
            $ldapError = "LDAP module not active";
        }
        
        //Verifica su lista account locali se LDAP fallisce
        if(!$ldapOk)
        {
            $md5Pwd = md5($password);
            $query = "SELECT * FROM Dashboard.Users WHERE username = '$username' AND password = '$md5Pwd' AND status = 1 AND admin <> 'Observer'";
            $result = mysqli_query($link, $query);

            if($result == false) 
            {
                $dbError = "Database query error";
            }
            else
            {
                if(mysqli_num_rows($result) > 0) 
                {
                   $row = $result->fetch_assoc();
                   ini_set('session.gc_maxlifetime', $sessionDuration);
                   session_set_cookie_params($sessionDuration);
                   session_start();
                   session_regenerate_id();
                   $_SESSION['sessionEndTime'] = time() + $sessionDuration;
                   $_SESSION['loggedUsername'] = $username;
                   $_SESSION['loggedPassword'] = $ldapPassword;
                   $_SESSION['loggedRole'] = $row["admin"];
                   $_SESSION['loggedType'] = "local";
                   //notificatorLogin($username, $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $ldapToolName);
                   mysqli_close($link);
                   $resultMsg = "Ok";
                } 
                else 
                {
                    mysqli_close($link);
                    $notFoundMsg = "Username/password not recognized: please try again";
                }
            }
        }
        
        if($resultMsg != "Ok")
        {
            $resultMsg = "";
            if($ldapError != false)
            {
               $resultMsg = $resultMsg . $ldapError . "<br/>";
            }

            if($dbError != false)
            {
               $resultMsg = $resultMsg . $dbError . "<br/>";
            }

            if($errorMsg != false)
            {
               $resultMsg = $resultMsg . $errorMsg . "<br/>";
            }
            
            if($notFoundMsg != false)
            {
               $resultMsg = $resultMsg . $notFoundMsg . "<br/>";
            }
        }
        
        echo $resultMsg;
        exit();
    } 
    else if(isset($_REQUEST['addDashboard']))
    {
        session_start();
        checkSession('Manager');
        
        $name_dashboard = mysqli_real_escape_string($link, $_POST['inputTitleDashboard']); 
        $title = mysqli_real_escape_string($link, $_POST['inputTitleDashboard']);  
        $subtitle = mysqli_real_escape_string($link, $_POST['inputSubTitleDashboard']);  
        $color = mysqli_real_escape_string($link, $_POST['inputColorDashboard']);  
        $background = mysqli_real_escape_string($link, $_POST['inputColorBackgroundDashboard']);  
        $externalColor = mysqli_real_escape_string($link, $_POST['inputExternalColorDashboard']);  
        $nCols = mysqli_real_escape_string($link, $_POST['inputWidthDashboard']);  
        $headerFontColor = mysqli_real_escape_string($link, $_POST['headerFontColor']);  
        $headerFontSize = mysqli_real_escape_string($link, $_POST['headerFontSize']);
        $viewMode = mysqli_real_escape_string($link, $_POST['inputDashboardViewMode']);
        $addLogo = false;
        
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
                    $logoLink = escapeForSQL($_POST['dashboardLogoLinkInput'], $link);
                    // NEW P-TEST: mettere apici a $ncols
                    $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                    (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                    `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`,`color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `logoFilename`, `logoLink`, `widgetsBorders`, `widgetsBordersColor`, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, last_edit_date) 
                    VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, returnManagedNumberForDb($nCols), '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$filename', '$logoLink', '$widgetsBorders', '$widgetsBordersColor', '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson', '$viewMode', CURRENT_TIMESTAMP)";
                }
                else
                {
                    $insqDbtb2 = "INSERT INTO `Dashboard`.`Config_dashboard`
                    (`Id`, `name_dashboard`, `title_header`, `subtitle_header`, `color_header`,
                    `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`,`color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `logoFilename`, `widgetsBorders`, `widgetsBordersColor`, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, last_edit_date) 
                    VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, returnManagedNumberForDb($nCols), '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$filename', '$widgetsBorders', '$widgetsBordersColor', '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson', '$viewMode', CURRENT_TIMESTAMP)";
                }

                mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
                $result3 = mysqli_query($link, $insqDbtb2);

                if($result3) 
                {
                    $newDashId = mysqli_insert_id($link);

                    if($visibility == "restrict")
                    {
                        if(isset($_POST['selectedVisibilityUsers']))
                        {
                            foreach($_POST['selectedVisibilityUsers'] as $selectedUser)
                            {
                                $insertQuery = "INSERT INTO Dashboard.DashboardsViewPermissions VALUES($newDashId, '" . escapeForSQL($selectedUser, $link) . "')";
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
                `width`, `height`, `num_rows`, `num_columns`, `user`, `status_dashboard`, `creation_date`, `color_background`,`external_frame_color`, `headerFontColor`, `headerFontSize`, `widgetsBorders`, `widgetsBordersColor`, visibility, headerVisible, embeddable, authorizedPagesJson, viewMode, last_edit_date) 
                VALUES (NULL, '$name_dashboard', '$title', '$subtitle', '$color', $width, 0, 0, $nCols, '$dashboardAuthorName', 1, now(), '$background', '$externalColor', '$headerFontColor', $headerFontSize, '$widgetsBorders', '$widgetsBordersColor', '$visibility', $headerVisible, '$embeddable', '$authorizedPagesJson', '$viewMode', CURRENT_TIMESTAMP)";
                
                $result3 = mysqli_query($link, $insqDbtb2) or die(mysqli_error($link));
                
                if($result3) 
                {
                     $newDashId = mysqli_insert_id($link);                           

                     if($visibility == "restrict")
                     {
                         if(isset($_POST['selectedVisibilityUsers']))
                         {
                             foreach($_POST['selectedVisibilityUsers'] as $selectedUser)
                             {
                                 $insertQuery = "INSERT INTO Dashboard.DashboardsViewPermissions VALUES($newDashId, '" . escapeForSQL($selectedUser, $link) . "')";
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
                    echo 'alert("Error during dashboard creation: please repeat the procedure.");';
                    echo 'window.location.href = "dashboards.php";';
                    echo '</script>';
                }
            }
        }
        
        if(!$queryFail)
        {
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
                    echo '</script>';  
                }   
                else   
                {  
                    if(!is_writable($uploadFolder))
                    {
                        echo '<script type="text/javascript">';
                        echo 'alert("Directory dashLogos is not writable");';
                        echo '</script>';
                    }
                    else
                    {
                        $filename = $_FILES['dashboardLogoInput']['name'];

                        if(!move_uploaded_file($_FILES['dashboardLogoInput']['tmp_name'], $uploadFolder.$filename))  
                        {  
                            echo '<script type="text/javascript">';
                            echo 'alert("Error during logo upload.");';
                            echo '</script>'; 
                        }  
                        else  
                        {  
                            chmod($uploadFolder.$filename, 0666);
                        }
                    }
                } 
            }
            
            header("location: dashboards.php?newDashId=" . $newDashId . "&newDashAuthor=" . $dashboardAuthorName . "&newDashTitle=" . urlencode($title));
            
        }
    } 
    else if(isset($_REQUEST['add_widget'])) 
    {
        session_start();
        checkSession('Manager');
                
            $id_dashboard = $_REQUEST['dashboardIdUnderEdit'];
            if (checkVarType($id_dashboard, "integer") === false) {
                eventLog("Returned the following ERROR in process-form.php for dashboard_id = ".$id_dashboard.": ".$id_dashboard." is not an integer as expected. Exit from script.");
                exit();
            }
        //    $dashboardName = $_REQUEST['currentDashboardTitle'];
            if (sanitizePostString('currentDashboardTitle') === null) {       // New pentest COMMON CTR
                $dashboardName = mysqli_real_escape_string($link, sanitizeGetString('currentDashboardTitle'));
            } else {
                $dashboardName = mysqli_real_escape_string($link, sanitizePostString('currentDashboardTitle'));
            }
        //    $dashboardAuthorName = $_REQUEST['dashboardUser'];
            if (sanitizePostString('dashboardUser') === null) {       // New pentest COMMON CTR
                $dashboardAuthorName = mysqli_real_escape_string($link, sanitizeGetString('dashboardUser'));
            } else {
                $dashboardAuthorName = mysqli_real_escape_string($link, sanitizePostString('dashboardUser'));
            }
        //    $dashboardEditor = $_REQUEST['dashboardEditor'];
            if (sanitizePostString('dashboardEditor') === null) {       // New pentest COMMON CTR
                $dashboardEditor = mysqli_real_escape_string($link, sanitizeGetString('dashboardEditor'));
            } else {
                $dashboardEditor = mysqli_real_escape_string($link, sanitizePostString('dashboardEditor'));
            }
        //    $creator = $_REQUEST['dashboardEditor'];
            $creator = $dashboardEditor;
        
            $nextId = 1;
            $firstFreeRow = escapeForSQL($_POST['firstFreeRowInput'], $link);
            if (checkVarType($firstFreeRow, "integer") === false) {
                if (!is_null($firstFreeRow)) {
                    eventLog("Returned the following ERROR in process-form.php for dashboard_id = " . $id_dashboard . ": First Free Row (" . $firstFreeRow . ") is not an integer (or NULL) as expected. Exit from script.");
                    exit();
                }
            }
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
                $infoJson = NULL;
                $legendFontSize = NULL;
                $legendFontColor = NULL;
                $dataLabelsFontSize = NULL;
                $dataLabelsFontColor = NULL;
                $barsColorsSelect = NULL;
                $barsColors = NULL;
                $chartType = NULL;
                $secondaryYAxis = NULL;
                $dataLabelsDistance = NULL;
                $dataLabelsDistance1 = NULL;
                $dataLabelsDistance2 = NULL;
                $dataLabels = NULL;
                $dataLabelsRotation = NULL;
                $xAxisDataset = NULL;
                $lineWidth = NULL;
                $alrLook = NULL;
                $TypicalTimeTrendM = NULL;
                $TrendTypeM = NULL;
                $ReferenceDateM = NULL;
                $TTTDate = NULL;
                $dayHourView = NULL;
                $computationType = NULL;
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
		        $calendarM = NULL;
                $showContentLoadM = NULL;
                $exportM = NULL;
                $enableFullscreenTab = 'no';
                $enableFullscreenModal = 'no'; 
             //   $fontFamily = mysqli_real_escape_string($link, $_REQUEST['inputFontFamilyWidget']);
                if (sanitizePostString('inputFontFamilyWidget') === null) {       // New pentest COMMON CTR
                    $fontFamily = mysqli_real_escape_string($link, sanitizeGetString('inputFontFamilyWidget'));
                } else {
                    $fontFamily = mysqli_real_escape_string($link, sanitizePostString('inputFontFamilyWidget'));
                }
                $newOrionEntityJson = NULL;
                $attributeName = NULL;
                $groupByAttr = NULL;
                
                if(isset($_REQUEST['actuatorTarget']))
                {
                    $actuatorTarget = mysqli_real_escape_string($link, sanitizePostString('actuatorTarget'));
                }
                else
                {
                    $actuatorTarget = NULL;
                }
                
                $creationDate = date('Y-m-d H:i:s');
         
                if($_REQUEST['widgetCategory'] == "actuator")
                {
                    if($_REQUEST['actuatorTarget'] == 'broker')
                    {
                        $widgetActuatorType = escapeForSQL(sanitizePostString('widgetActuatorType'), $link);
                    }
                    else
                    {
                        $widgetActuatorType = escapeForSQL(sanitizePostString('widgetActuatorTypePersonalApps'), $link);
                        $id_metric = str_replace('.', '_', str_replace('-', '_', $_REQUEST['personalAppsInputs']));
                    }
                    
                    $type_widget = $widgetActuatorType;
                    
                    if($type_widget == "widgetNumericKeyboard")
                    {
                        $styleParametersArray = array('displayColor' => sanitizeString('addKeyboardDisplayColor'), 'displayFontColor' => sanitizeString('addKeyboardDisplayFontColor'), 'btnColor' => sanitizeString('addKeyboardBtnColor'), 'btnFontColor' => sanitizeString('addKeyboardBtnFontColor'));
                        $styleParameters = json_encode($styleParametersArray);
                    }
                    
                    if($type_widget == "widgetGeolocator")
                    {
                     //   $styleParametersArray = array('viewMode' => $_REQUEST['addSwitchButtonViewMode'], 'buttonRadius' => $_REQUEST['addSwitchButtonRadius'], 'color' => $_REQUEST['addSwitchButtonColor'], 'clickColor' => $_REQUEST['addSwitchButtonClickColor'], 'symbolColor' => $_REQUEST['addSwitchButtonSymbolColor'], 'symbolClickColor' => $_REQUEST['addSwitchButtonSymbolClickColor'], 'neonEffect' => $_REQUEST['addSwitchButtonNeonEffect'], 'textColor' => $_REQUEST['addSwitchButtonTextColor'], 'textClickColor' => $_REQUEST['addSwitchButtonTextClickColor'], 'textFontSize' => $_REQUEST['addSwitchButtonTextFontSize'], 'displayFontSize' => $_REQUEST['addSwitchButtonDisplayFontSize'], 'displayFontColor' => $_REQUEST['addSwitchButtonDisplayFontColor'], 'displayFontClickColor' => $_REQUEST['addSwitchButtonDisplayFontClickColor'], 'displayColor' => $_REQUEST['addSwitchButtonDisplayColor'], 'displayRadius' => $_REQUEST['addSwitchButtonDisplayRadius'], 'displayWidth' => $_REQUEST['addSwitchButtonDisplayWidth'], 'displayHeight' => $_REQUEST['addSwitchButtonDisplayHeight']);
                        $styleParametersArray = array('viewMode' => sanitizePostString('addSwitchButtonViewMode'), 'buttonRadius' => sanitizePostString('addSwitchButtonRadius'), 'color' => sanitizePostString('addSwitchButtonColor'), 'clickColor' => sanitizePostString('addSwitchButtonClickColor'), 'symbolColor' => sanitizePostString('addSwitchButtonSymbolColor'), 'symbolClickColor' => sanitizePostString('addSwitchButtonSymbolClickColor'), 'neonEffect' => sanitizePostString('addSwitchButtonNeonEffect'), 'textColor' => sanitizePostString('addSwitchButtonTextColor'), 'textClickColor' => sanitizePostString('addSwitchButtonTextClickColor'), 'textFontSize' => sanitizePostInt('addSwitchButtonTextFontSize'), 'displayFontSize' => sanitizePostInt('addSwitchButtonDisplayFontSize'), 'displayFontColor' => sanitizePostString('addSwitchButtonDisplayFontColor'), 'displayFontClickColor' => sanitizePostString('addSwitchButtonDisplayFontClickColor'), 'displayColor' => sanitizePostString('addSwitchButtonDisplayColor'), 'displayRadius' => sanitizePostInt('addSwitchButtonDisplayRadius'), 'displayWidth' => sanitizePostInt('addSwitchButtonDisplayWidth'), 'displayHeight' => sanitizePostInt('addSwitchButtonDisplayHeight'));
                        $styleParameters = json_encode($styleParametersArray);
                    }
                    
                    if($type_widget == "widgetImpulseButton")
                    {
                        $styleParametersArray = array('viewMode' => sanitizePostString('addSwitchButtonViewMode'), 'buttonRadius' => sanitizePostString('addSwitchButtonRadius'), 'color' => sanitizePostString('addSwitchButtonColor'), 'clickColor' => sanitizePostString('addSwitchButtonClickColor'), 'symbolColor' => sanitizePostString('addSwitchButtonSymbolColor'), 'symbolClickColor' => sanitizePostString('addSwitchButtonSymbolClickColor'), 'neonEffect' => sanitizePostString('addSwitchButtonNeonEffect'), 'textColor' => sanitizePostString('addSwitchButtonTextColor'), 'textClickColor' => sanitizePostString('addSwitchButtonTextClickColor'), 'textFontSize' => sanitizePostInt('addSwitchButtonTextFontSize'), 'displayFontSize' => sanitizePostInt('addSwitchButtonDisplayFontSize'), 'displayFontColor' => sanitizePostString('addSwitchButtonDisplayFontColor'), 'displayFontClickColor' => sanitizePostString('addSwitchButtonDisplayFontClickColor'), 'displayColor' => sanitizePostString('addSwitchButtonDisplayColor'), 'displayRadius' => sanitizePostInt('addSwitchButtonDisplayRadius'), 'displayWidth' => sanitizePostInt('addSwitchButtonDisplayWidth'), 'displayHeight' =>sanitizePostInt('addSwitchButtonDisplayHeight'));
                        $styleParameters = json_encode($styleParametersArray);
                    }
                    
                    if($type_widget == "widgetOnOffButton")
                    {
                        $styleParametersArray = array('viewMode' => sanitizePostString('addSwitchButtonViewMode'), 'buttonRadius' => sanitizePostInt('addSwitchButtonRadius'), 'offColor' => sanitizePostString('addSwitchButtonOffColor'), 'onColor' => sanitizePostString('addSwitchButtonOnColor'), 'symbolOffColor' => sanitizePostString('addSwitchButtonSymbolOffColor'), 'symbolOnColor' => sanitizePostString('addSwitchButtonSymbolOnColor'), 'neonEffect' => sanitizePostString('addSwitchButtonNeonEffect'), 'textOffColor' => sanitizePostString('addSwitchButtonTextOffColor'), 'textOnColor' => sanitizePostString('addSwitchButtonTextOnColor'), 'textFontSize' => sanitizePostInt('addSwitchButtonTextFontSize'), 'displayFontSize' => sanitizePostInt('addSwitchButtonDisplayFontSize'), 'displayOffColor' => sanitizePostString('addSwitchButtonDisplayOffColor'), 'displayOnColor' => sanitizePostString('addSwitchButtonDisplayOnColor'), 'displayColor' => sanitizePostString('addSwitchButtonDisplayColor'), 'displayRadius' => sanitizePostInt('addSwitchButtonDisplayRadius'), 'displayWidth' => sanitizePostInt('addSwitchButtonDisplayWidth'), 'displayHeight' => sanitizePostInt('addSwitchButtonDisplayHeight'));
                        $styleParameters = json_encode($styleParametersArray);
                    }
                    
                    if($type_widget == "widgetKnob")
                    {
                        $styleParametersArray = array('indicatorRadius' => sanitizePostFloat('addKnobIndicatorRadius'), 'displayRadius' => sanitizePostFloat('addKnobDisplayRadius'), 'startAngle' => sanitizePostFloat('addKnobStartAngle'), 'endAngle' => sanitizePostFloat('addKnobEndAngle'), 'displayColor' => sanitizePostString('addKnobDisplayColor'), 'ticksColor' => sanitizePostInt('addKnobTicksColor'), 'labelsFontSize' => sanitizePostInt('addKnobLabelsFontSize'), 'labelsFontColor' => sanitizePostString('addKnobLabelsFontColor'), 'increaseValue' => sanitizePostFloat('addKnobIncreaseValue'));
                        $styleParameters = json_encode($styleParametersArray);
                    }
                }
                else
                {
                    if($_REQUEST['metricsCategory'] === 'app')
                    {
                     //   $id_metric = str_replace('.', '_', str_replace('-', '_', mysqli_real_escape_string($link, $_REQUEST['select-metricNR'])));
                        if (sanitizePostString('select-metricNR') == null) {       // New pentest COMMON CTR
                            $id_metric = mysqli_real_escape_string($link, sanitizeGetString('select-metricNR'));
                        } else {
                            $id_metric = mysqli_real_escape_string($link, sanitizePostString('select-metricNR'));
                        }
                    //    $type_widget = mysqli_real_escape_string($link, $_REQUEST['select-widgetNR']);
                        if (sanitizePostString('select-widgetNR') == null) {       // New pentest COMMON CTR
                            $type_widget = mysqli_real_escape_string($link, sanitizeGetString('select-widgetNR'));
                        } else {
                            $type_widget = mysqli_real_escape_string($link, sanitizePostString('select-widgetNR'));
                        }
                    }
                    else 
                    {
                    //    $id_metric = mysqli_real_escape_string($link, str_replace('.', '_', str_replace('-', '_', $_REQUEST['select-metric'])));
                        if (sanitizePostString('select-metric') === null) {       // New pentest COMMON CTR
                            $id_metric = mysqli_real_escape_string($link, sanitizeGetString('select-metric'));
                        } else {
                            $id_metric = mysqli_real_escape_string($link, sanitizePostString('select-metric'));
                        }
                    //    $type_widget = mysqli_real_escape_string($link, $_REQUEST['select-widget']);
                        if (sanitizePostString('select-widget') === null) {       // New pentest COMMON CTR
                            $type_widget = mysqli_real_escape_string($link, sanitizeGetString('select-widget'));
                        } else {
                            $type_widget = mysqli_real_escape_string($link, sanitizePostString('select-widget'));
                        }
                    }
                }
                
                if($type_widget == "widgetPrevMeteo")
                {
                 //   $styleParametersArray = array('orientation' => $_REQUEST['orientation'], 'language' => $_REQUEST['language'], 'todayDim' => $_REQUEST['todayDim'], 'backgroundMode' => $_REQUEST['backgroundMode'], 'iconSet' => $_REQUEST['iconSet']);
                    $styleParametersArray = array('orientation' => sanitizePostString('orientation'), 'language' => sanitizePostString('language'), 'todayDim' => sanitizePostString('todayDim'), 'backgroundMode' => sanitizePostString('backgroundMode'), 'iconSet' => sanitizePostString('iconSet'));
                    $styleParameters = json_encode($styleParametersArray);
                }
                
                if($type_widget == "widgetSelectorNew")
                {
                    if (sanitizePostString('iconTextMode') === null) {
                        $iconText = sanitizeGetString('iconTextMode');
                    } else {
                        $iconText = sanitizePostString('iconTextMode');
                    }

                    if (sanitizePostString('mapPinIcon') === null) {
                        $mapPinIcon = sanitizeGetString('mapPinIcon');
                    } else {
                        $mapPinIcon = sanitizePostString('mapPinIcon');
                    }

               //   $styleParametersArray = array('activeFontColor' => $_REQUEST['addGisActiveQueriesFontColor']);
                    if (sanitizePostString('addGisActiveQueriesFontColor') === null) {       // New pentest COMMON CTR
                        $styleParametersArray = array('activeFontColor' => sanitizeGetString('addGisActiveQueriesFontColor'), 'iconText' => $iconText, 'mapPinIcon' => $mapPinIcon);
                    } else {
                        $styleParametersArray = array('activeFontColor' => sanitizePostString('addGisActiveQueriesFontColor'), 'iconText' => $iconText, 'mapPinIcon' => $mapPinIcon);
                    }
                    $styleParameters = json_encode($styleParametersArray);
                }

                if(($type_widget == "widgetSelector") || ($type_widget == "widgetSelectorTech"))
                {
                    //   $styleParametersArray = array('activeFontColor' => $_REQUEST['addGisActiveQueriesFontColor']);
                    if (sanitizePostString('addGisActiveQueriesFontColor') === null) {       // New pentest COMMON CTR
                        $styleParametersArray = array('activeFontColor' => sanitizeGetString('addGisActiveQueriesFontColor'));
                    } else {
                        $styleParametersArray = mysqli_real_escape_string($link, sanitizePostString('addGisActiveQueriesFontColor'));
                    }
                    $styleParameters = json_encode($styleParametersArray);
                }
                
                if($type_widget == "widgetSelectorWeb")
                {
                    if (sanitizePostString('iconTextMode') === null) {
                        $iconText = sanitizeGetString('iconTextMode');
                    } else {
                        $iconText = sanitizePostString('iconTextMode');
                    }
              //    $styleParametersArray = array('activeFontColor' => $_REQUEST['addGisActiveQueriesFontColor'], 'rectDim' => $_REQUEST['addGisRectDim']);
              //      $styleParametersArray = array('activeFontColor' => sanitizePostString('addGisActiveQueriesFontColor'), 'rectDim' => sanitizePostString('addGisRectDim'));
                    if (sanitizePostString('addGisActiveQueriesFontColor') === null) {       // New pentest COMMON CTR
                        $activeFontColor = sanitizeGetString('addGisActiveQueriesFontColor');
                    } else {
                        $activeFontColor = sanitizePostString('addGisActiveQueriesFontColor');
                    }
                    if (sanitizePostString('addGisRectDim') === null) {       // New pentest COMMON CTR
                        $rectDim = sanitizeGetString('addGisRectDim');
                    } else {
                        $rectDim = sanitizePostString('addGisRectDim');
                    }
                  $styleParametersArray = array('activeFontColor' => $activeFontColor, 'rectDim' => $rectDim, 'iconText' => $iconText);
                  $styleParameters = json_encode($styleParametersArray);
                }
                
                if($type_widget == "widgetClock")
                {
                  if(isset($_REQUEST['addWidgetClockData']))
                  {
                  //   $clockData = $_REQUEST['addWidgetClockData'];
                      if (sanitizePostString('addWidgetClockData') == null) {       // New pentest COMMON CTR
                          $clockData = mysqli_real_escape_string($link, sanitizeGetString('addWidgetClockData'));
                      } else {
                          $clockData = mysqli_real_escape_string($link, sanitizePostString('addWidgetClockData'));
                      }
                  }

                  if(isset($_REQUEST['addWidgetClockFont']))
                  {
                   //  $clockFont = $_REQUEST['addWidgetClockFont'];
                      if (sanitizePostString('addWidgetClockFont') == null) {       // New pentest COMMON CTR
                          $clockFont = mysqli_real_escape_string($link, sanitizeGetString('addWidgetClockFont'));
                      } else {
                          $clockFont = mysqli_real_escape_string($link, sanitizePostString('addWidgetClockFont'));
                      }
                  }
                  
                  $styleParametersArray = array('clockData' => $clockData, 'clockFont' => $clockFont);
                  $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetFirstAid")
                {
                    if(isset($_POST['showTableFirstCell'])&&($_POST['showTableFirstCell']!=""))
                    {
                        $showTableFirstCell = mysqli_real_escape_string($link, sanitizePostString('showTableFirstCell'));
                    }

                    if(isset($_POST['tableFirstCellFontSize'])&&($_POST['tableFirstCellFontSize']!=""))
                    {
                        $tableFirstCellFontSize = mysqli_real_escape_string($link, sanitizePostInt('tableFirstCellFontSize'));
                    }

                    if(isset($_POST['tableFirstCellFontColor'])&&($_POST['tableFirstCellFontColor']!=""))
                    {
                        $tableFirstCellFontColor = mysqli_real_escape_string($link, sanitizePostString('tableFirstCellFontColor'));
                    }

                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('rowsLabelsFontSize'));
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColor'));
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('colsLabelsFontSize'));
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColor'));
                    }

                    if(isset($_POST['rowsLabelsBckColor'])&&($_POST['rowsLabelsBckColor']!=""))
                    {
                        $rowsLabelsBckColor = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsBckColor'));
                    }
                    
                    if(isset($_POST['tableBorders'])&&($_POST['tableBorders']!=""))
                    {
                        $tableBorders = mysqli_real_escape_string($link, sanitizePostString('tableBorders'));
                    }

                    if(isset($_POST['tableBordersColor'])&&($_POST['tableBordersColor']!=""))
                    {
                        $tableBordersColor = mysqli_real_escape_string($link, sanitizePostString('tableBordersColor'));
                    }

                    $styleParametersArray = array('showTableFirstCell' => $showTableFirstCell, 'tableFirstCellFontSize' => $tableFirstCellFontSize, 'tableFirstCellFontColor' => $tableFirstCellFontColor, 'rowsLabelsFontSize' => $rowsLabelsFontSize, 'rowsLabelsFontColor' => $rowsLabelsFontColor, 'colsLabelsFontSize' => $colsLabelsFontSize, 'colsLabelsFontColor' => $colsLabelsFontColor, 'rowsLabelsBckColor' => $rowsLabelsBckColor, 'colsLabelsBckColor' => $colsLabelsBckColor, 'tableBorders' => $tableBorders, 'tableBordersColor' => $tableBordersColor);
                    $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetPieChart")
                {
                    if(isset($_POST['legendFontSize'])&&($_POST['legendFontSize']!=""))
                    {
                        $legendFontSize = mysqli_real_escape_string($link, sanitizePostInt('legendFontSize'));
                    }

                    if(isset($_POST['legendFontColorPicker'])&&($_POST['legendFontColorPicker']!=""))
                    {
                        $legendFontColor = mysqli_real_escape_string($link, sanitizePostString('legendFontColorPicker'));
                    }

                    if(isset($_POST['dataLabelsDistance'])&&($_POST['dataLabelsDistance']!=""))
                    {
                        $dataLabelsDistance = mysqli_real_escape_string($link, sanitizePostFloat('dataLabelsDistance'));
                    }

                    if(isset($_POST['dataLabelsDistance1'])&&($_POST['dataLabelsDistance1']!=""))
                    {
                        $dataLabelsDistance1 = mysqli_real_escape_string($link, sanitizePostFloat('dataLabelsDistance1'));
                    }

                    if(isset($_POST['dataLabelsDistance2'])&&($_POST['dataLabelsDistance2']!=""))
                    {
                        $dataLabelsDistance2 = mysqli_real_escape_string($link, sanitizePostFloat('dataLabelsDistance2'));
                    }

                    if(isset($_POST['dataLabels'])&&($_POST['dataLabels']!=""))
                    {
                        $dataLabels = mysqli_real_escape_string($link, sanitizePostString('dataLabels'));
                    }

                    if(isset($_POST['dataLabelsFontSize'])&&($_POST['dataLabelsFontSize']!=""))
                    {
                        $dataLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('dataLabelsFontSize'));
                    }

                    if(isset($_POST['dataLabelsFontColor'])&&($_POST['dataLabelsFontColor']!=""))
                    {
                        $dataLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('dataLabelsFontColor'));
                    }

                    if(isset($_POST['innerRadius1'])&&($_POST['innerRadius1']!=""))
                    {
                        $innerRadius1 = mysqli_real_escape_string($link, sanitizePostFloat('innerRadius1'));
                    }

                    if(isset($_POST['startAngle'])&&($_POST['startAngle']!=""))
                    {
                        $startAngle = mysqli_real_escape_string($link, sanitizePostFloat('startAngle'));
                    }

                    if(isset($_POST['endAngle'])&&($_POST['endAngle']!=""))
                    {
                        $endAngle = mysqli_real_escape_string($link, sanitizePostFloat('endAngle'));
                    }

                    if(isset($_POST['outerRadius1'])&&($_POST['outerRadius1']!=""))
                    {
                        $outerRadius1 = mysqli_real_escape_string($link, sanitizePostFloat('outerRadius1'));
                    }

                    if(isset($_POST['innerRadius2'])&&($_POST['innerRadius2']!=""))
                    {
                        $innerRadius2 = mysqli_real_escape_string($link, sanitizePostFloat('innerRadius2'));
                    }

                    if(isset($_POST['centerY'])&&($_POST['centerY']!=""))
                    {
                        $centerY = mysqli_real_escape_string($link, sanitizePostFloat('centerY'));
                    }

                    if(isset($_POST['colorsSelect1'])&&($_POST['colorsSelect1']!=""))
                    {
                        $colorsSelect1 = mysqli_real_escape_string($link, sanitizePostString('colorsSelect1'));
                    }

                    if(isset($_POST['colors1'])&&($_POST['colors1']!=""))
                    {
                        $temp = json_decode(sanitizeJson($_POST['colors1']));     // SANITIZE QUI ??
                        $colors1 = [];
                        foreach ($temp as $color) 
                        {
                            array_push($colors1, $color);
                        }
                    }

                    if(isset($_POST['colorsSelect2'])&&($_POST['colorsSelect2']!=""))
                    {
                        $colorsSelect2 = mysqli_real_escape_string($link, sanitizePostString('colorsSelect2'));
                    }

                    if(isset($_POST['colors2'])&&($_POST['colors2']!=""))
                    {
                        $temp = json_decode(sanitizeJson($_POST['colors2']));     // SANITIZE QUI ??
                        $colors2 = [];
                        foreach ($temp as $color) 
                        {
                            array_push($colors2, $color);
                        }
                    }

                    if(isset($_POST['groupByAttr'])&&($_POST['groupByAttr']!=""))
                    {
                        $groupByAttr = mysqli_real_escape_string($link, sanitizePostString('groupByAttr'));      // New pentest
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
                    $styleParametersArray['groupByAttr'] = $groupByAttr;
                    $styleParameters = json_encode($styleParametersArray);
                }

                if(($type_widget == "widgetLineSeries") || ($type_widget == "widgetCurvedLineSeries") || ($type_widget == "widgetDataCube"))
                {
                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = sanitizePostInt('rowsLabelsFontSize');
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColor'));
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('colsLabelsFontSize'));
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColor'));
                    }

                    if(isset($_POST['dataLabelsFontSize'])&&($_POST['dataLabelsFontSize']!=""))
                    {
                        $dataLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('dataLabelsFontSize'));
                    }

                    if(isset($_POST['dataLabelsFontColor'])&&($_POST['dataLabelsFontColor']!=""))
                    {
                        $dataLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('dataLabelsFontColor'));
                    }

                    if(isset($_POST['legendFontSize'])&&($_POST['legendFontSize']!=""))
                    {
                        $legendFontSize = mysqli_real_escape_string($link, sanitizePostInt('legendFontSize'));
                    }

                    if(isset($_POST['legendFontColor'])&&($_POST['legendFontColor']!=""))
                    {
                        $legendFontColor = mysqli_real_escape_string($link, sanitizePostString('legendFontColor'));
                    }

                    if(isset($_POST['barsColorsSelect'])&&($_POST['barsColorsSelect']!=""))
                    {
                        $barsColorsSelect = mysqli_real_escape_string($link, sanitizePostString('barsColorsSelect'));
                    }

                    if(isset($_POST['chartType'])&&($_POST['chartType']!=""))
                    {
                        $chartType = mysqli_real_escape_string($link, sanitizePostString('chartType'));
                    }

                    if(isset($_POST['secondaryYAxis'])&&($_POST['secondaryYAxis']!=""))
                    {
                        $secondaryYAxis = mysqli_real_escape_string($link, sanitizePostString('secondaryYAxis'));
                    }

                    if(isset($_POST['dataLabels'])&&($_POST['dataLabels']!=""))
                    {
                        $dataLabels = mysqli_real_escape_string($link, sanitizePostString('dataLabels'));
                    }

                    if(isset($_POST['xAxisDataset'])&&($_POST['xAxisDataset']!=""))
                    {
                        $xAxisDataset = mysqli_real_escape_string($link, sanitizePostString('xAxisDataset'));
                    }

                    if(isset($_POST['lineWidth'])&&($_POST['lineWidth']!=""))
                    {
                        $lineWidth = mysqli_real_escape_string($link, sanitizePostInt('lineWidth'));
                    }

                    if(isset($_POST['alrLook'])&&($_POST['alrLook']!=""))
                    {
                        $alrLook = mysqli_real_escape_string($link, sanitizePostString('alrLook'));
                    }

                    if(isset($_POST['barsColors'])&&($_POST['barsColors']!=""))
                    {
                        $temp = json_decode(sanitizeJson($_POST['barsColors']));      // SANITIZE QUI ??
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
                    $styleParametersArray['secondaryYAxis'] = $secondaryYAxis;
                    $styleParametersArray['dataLabels'] = $dataLabels;
                    $styleParametersArray['xAxisDataset'] = $xAxisDataset;
                    $styleParametersArray['lineWidth'] = $lineWidth;
                    $styleParametersArray['alrLook'] = $alrLook;
                    $styleParametersArray['barsColors'] = $barsColors;
                    $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetCalendar")
                {
                    if(isset($_POST['barsColors'])&&($_POST['barsColors']!=""))
                    {
                        $temp = json_decode(sanitizeJson($_POST['barsColors']));      // SANITIZE QUI ??
                        $barsColors = [];
                        foreach ($temp as $color)
                        {
                            array_push($barsColors, $color);
                        }
                    }

                    if(isset($_POST['meanSum'])&&($_POST['meanSum']!=""))
                    {
                        $meanSum = mysqli_real_escape_string($link, sanitizePostString('meanSum'));
                    }

                    if(isset($_POST['calendarViewMode'])&&($_POST['calendarViewMode']!=""))
                    {
                        $calendarViewMode = mysqli_real_escape_string($link, sanitizePostString('calendarViewMode'));
                    }

                    $styleParametersArray = array();
                    $styleParametersArray['barsColors'] = $barsColors;
                    $styleParametersArray['meanSum'] = $meanSum;
                    $styleParametersArray['calendarViewMode'] = $calendarViewMode;
                    $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetBarSeries")
                {
                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = sanitizePostInt('rowsLabelsFontSize');        // New pentest
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColor'));     // New pentest
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('colsLabelsFontSize'));      // New pentest
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColor'));     // New pentest
                    }

                    if(isset($_POST['dataLabelsFontSize'])&&($_POST['dataLabelsFontSize']!=""))
                    {
                        $dataLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('dataLabelsFontSize'));      // New pentest
                    }

                    if(isset($_POST['dataLabelsFontColor'])&&($_POST['dataLabelsFontColor']!=""))
                    {
                        $dataLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('dataLabelsFontColor'));     // New pentest
                    }

                    if(isset($_POST['legendFontSize'])&&($_POST['legendFontSize']!=""))
                    {
                        $legendFontSize = mysqli_real_escape_string($link, sanitizePostInt('legendFontSize'));      // New pentest
                    }

                    if(isset($_POST['legendFontColor'])&&($_POST['legendFontColor']!=""))
                    {
                        $legendFontColor = mysqli_real_escape_string($link, sanitizePostString('legendFontColor'));     // New pentest
                    }

                    if(isset($_POST['barsColorsSelect'])&&($_POST['barsColorsSelect']!=""))
                    {
                        $barsColorsSelect = mysqli_real_escape_string($link, sanitizePostString('barsColorsSelect'));       // New pentest
                    }

                    if(isset($_POST['chartType'])&&($_POST['chartType']!=""))
                    {
                        $chartType = mysqli_real_escape_string($link, sanitizePostString('chartType'));     // New pentest
                    }

                    if(isset($_POST['dataLabels'])&&($_POST['dataLabels']!=""))
                    {
                        $dataLabels = mysqli_real_escape_string($link, sanitizePostString('dataLabels'));       // New pentest
                    }

                    if(isset($_POST['dataLabelsRotation'])&&($_POST['dataLabelsRotation']!=""))
                    {
                        $dataLabelsRotation = mysqli_real_escape_string($link, sanitizePostString('dataLabelsRotation'));       // New pentest
                    }

                    if(isset($_POST['groupByAttr'])&&($_POST['groupByAttr']!=""))
                    {
                        $groupByAttr = mysqli_real_escape_string($link, sanitizePostString('groupByAttr'));      // New pentest
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
		            $styleParametersArray['calendarM'] = $calendarM;

                    if(isset($_POST['barsColors'])&&($_POST['barsColors']!=""))
                    {
                        $temp = json_decode(sanitizeJson($_POST['barsColors']));  // SANITIZE QUI ??
                        $barsColors = [];
                        foreach ($temp as $color) 
                        {
                            array_push($barsColors, $color);
                        }
                    }

                    $styleParametersArray['barsColors'] = $barsColors;
                    $styleParametersArray['groupByAttr'] = $groupByAttr;
                    $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetRadarSeries")
                {
                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('rowsLabelsFontSize'));
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColor'));
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('colsLabelsFontSize'));
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColor'));
                    }

                    if(isset($_POST['dataLabelsFontSize'])&&($_POST['dataLabelsFontSize']!=""))
                    {
                        $dataLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('dataLabelsFontSize'));
                    }

                    if(isset($_POST['dataLabelsFontColor'])&&($_POST['dataLabelsFontColor']!=""))
                    {
                        $dataLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('dataLabelsFontColor'));
                    }

                    if(isset($_POST['legendFontSize'])&&($_POST['legendFontSize']!=""))
                    {
                        $legendFontSize = mysqli_real_escape_string($link, sanitizePostInt('legendFontSize'));
                    }

                    if(isset($_POST['legendFontColor'])&&($_POST['legendFontColor']!=""))
                    {
                        $legendFontColor = mysqli_real_escape_string($link, sanitizePostString('legendFontColor'));
                    }

                    if(isset($_POST['gridLinesWidth'])&&($_POST['gridLinesWidth']!=""))
                    {
                        $gridLinesWidth = mysqli_real_escape_string($link, sanitizePostInt('gridLinesWidth'));
                    }

                    if(isset($_POST['gridLinesColor'])&&($_POST['gridLinesColor']!=""))
                    {
                        $gridLinesColor = mysqli_real_escape_string($link, sanitizePostString('gridLinesColor'));
                    }

                    if(isset($_POST['linesWidth'])&&($_POST['linesWidth']!=""))
                    {
                        $linesWidth = mysqli_real_escape_string($link, sanitizePostInt('linesWidth'));
                    }

                    if(isset($_POST['barsColorsSelect'])&&($_POST['barsColorsSelect']!=""))
                    {
                        $barsColorsSelect = mysqli_real_escape_string($link, sanitizePostString('barsColorsSelect'));
                    }

                    if(isset($_POST['alrThrLinesWidth'])&&($_POST['alrThrLinesWidth']!=""))
                    {
                        $alrThrLinesWidth = mysqli_real_escape_string($link,sanitizePostInt('alrThrLinesWidth'));
                    }

                    if(isset($_POST['dataLabels'])&&($_POST['dataLabels']!=""))
                    {
                        $dataLabels = mysqli_real_escape_string($link, sanitizePostString('dataLabels'));
                    }

                    if(isset($_POST['dataLabelsRotation'])&&($_POST['dataLabelsRotation']!=""))
                    {
                        $dataLabelsRotation = mysqli_real_escape_string($link, sanitizePostString('dataLabelsRotation'));   // CTR oppure sanitizePostInt ???
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
                        $temp = json_decode(sanitizeJson($_POST['barsColors']));      // SANITIZE QUI ?
                        $barsColors = [];
                        foreach ($temp as $color) 
                        {
                            array_push($barsColors, $color);
                        }
                    }

                    $styleParametersArray['barsColors'] = $barsColors;
                    $styleParameters = json_encode($styleParametersArray);
                }

                if($type_widget == "widgetTable")
                {
                    if(isset($_POST['showTableFirstCell'])&&($_POST['showTableFirstCell']!=""))
                    {
                        $showTableFirstCell = mysqli_real_escape_string($link, sanitizePostString('showTableFirstCell'));      // New pentest
                    }

                    if(isset($_POST['tableFirstCellFontSize'])&&($_POST['tableFirstCellFontSize']!=""))
                    {
                        $tableFirstCellFontSize = mysqli_real_escape_string($link, sanitizePostInt('tableFirstCellFontSize'));      // New pentest
                    }

                    if(isset($_POST['tableFirstCellFontColor'])&&($_POST['tableFirstCellFontColor']!=""))
                    {
                        $tableFirstCellFontColor = mysqli_real_escape_string($link, sanitizePostString('tableFirstCellFontColor'));     // New pentest
                    }

                    if(isset($_POST['rowsLabelsFontSize'])&&($_POST['rowsLabelsFontSize']!=""))
                    {
                        $rowsLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('rowsLabelsFontSize'));      // New pentest
                    }

                    if(isset($_POST['rowsLabelsFontColor'])&&($_POST['rowsLabelsFontColor']!=""))
                    {
                        $rowsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColor'));     // New pentest
                    }

                    if(isset($_POST['colsLabelsFontSize'])&&($_POST['colsLabelsFontSize']!=""))
                    {
                        $colsLabelsFontSize = mysqli_real_escape_string($link, sanitizePostInt('colsLabelsFontSize'));      // New pentest
                    }

                    if(isset($_POST['colsLabelsFontColor'])&&($_POST['colsLabelsFontColor']!=""))
                    {
                        $colsLabelsFontColor = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColor'));     // New pentest
                    }

                    if(isset($_POST['rowsLabelsBckColor'])&&($_POST['rowsLabelsBckColor']!=""))
                    {
                        $rowsLabelsBckColor = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsBckColor'));      // New pentest
                    }

                    if(isset($_POST['colsLabelsBckColor'])&&($_POST['colsLabelsBckColor']!=""))
                    {
                        $colsLabelsBckColor = mysqli_real_escape_string($link, sanitizePostString('colsLabelsBckColor'));       // New pentest
                    }

                    if(isset($_POST['tableBorders'])&&($_POST['tableBorders']!=""))
                    {
                        $tableBorders = mysqli_real_escape_string($link, sanitizePostString('tableBorders'));       // New pentest
                    }

                    if(isset($_POST['tableBordersColor'])&&($_POST['tableBordersColor']!=""))
                    {
                        $tableBordersColor = mysqli_real_escape_string($link, sanitizePostString('tableBordersColor'));     // New pentest
                    }

                    $styleParametersArray = array('showTableFirstCell' => $showTableFirstCell, 'tableFirstCellFontSize' => $tableFirstCellFontSize, 'tableFirstCellFontColor' => $tableFirstCellFontColor, 'rowsLabelsFontSize' => $rowsLabelsFontSize, 'rowsLabelsFontColor' => $rowsLabelsFontColor, 'colsLabelsFontSize' => $colsLabelsFontSize, 'colsLabelsFontColor' => $colsLabelsFontColor, 'rowsLabelsBckColor' => $rowsLabelsBckColor, 'colsLabelsBckColor' => $colsLabelsBckColor, 'tableBorders' => $tableBorders, 'tableBordersColor' => $tableBordersColor);
                    $styleParameters = json_encode($styleParametersArray);
                }
                
                if($type_widget == "widgetProtezioneCivile" || $type_widget == "widgetProtezioneCivileFirenze")
                {
                  if(isset($_POST['meteoTabFontSize']) && ($_POST['meteoTabFontSize'] != "") && ($_POST['meteoTabFontSize'] != null))
                  {
                      $meteoTabFontSize = mysqli_real_escape_string($link, sanitizePostInt('meteoTabFontSize'));    // New pentest COMMON
                  }

                  if(isset($_POST['genTabFontSize']) && ($_POST['genTabFontSize'] != "") && ($_POST['genTabFontSize'] != null))
                  {
                      $genTabFontSize = mysqli_real_escape_string($link, sanitizePostInt('genTabFontSize'));    // New pentest COMMON
                  }

                  if(isset($_POST['genTabFontColor']) && ($_POST['genTabFontColor'] != "") && ($_POST['genTabFontColor'] != null))
                  {
                      $genTabFontColor = mysqli_real_escape_string($link, sanitizePostString('genTabFontColor'));       // New pentest COMMON
                  }
                  
                  $styleParametersArray = array('meteoTabFontSize' => $meteoTabFontSize, 'genTabFontSize' => $genTabFontSize, 'genTabFontColor' => $genTabFontColor);
                  $styleParameters = json_encode($styleParametersArray);
                }

                if(isset($_POST['inputHeaderFontColorWidget'])&&($_POST['inputHeaderFontColorWidget']!=""))
                {
                    $headerFontColor = mysqli_real_escape_string($link, sanitizePostString('inputHeaderFontColorWidget'));      // New pentest COMMON
                }

                if(isset($_POST['inputZoomControlsColor'])&&($_POST['inputZoomControlsColor']!="")&&(($type_widget == 'widgetExternalContent')||($type_widget =="widgetGisWFS")))
                {
                    $zoomControlsColor = mysqli_real_escape_string($link, sanitizePostString('inputZoomControlsColor'));    // New pentest COMMON CTR
                }

                if(isset($_POST['inputControlsPosition'])&&($_POST['inputControlsPosition']!="")&&(($type_widget == 'widgetExternalContent')||($type_widget =="widgetGisWFS")))
                {
                    $controlsPosition = mysqli_real_escape_string($link, sanitizePostString('inputControlsPosition'));      // New pentest COMMON CTR
                }

                if(isset($_POST['inputShowTitle'])&&($_POST['inputShowTitle']!="")/*&&($type_widget == 'widgetExternalContent')*/)
                {
                    $showTitle = mysqli_real_escape_string($link, sanitizePostString('inputShowTitle'));    // New pentest COMMON
                }

                if(isset($_POST['inputControlsVisibility'])&&($_POST['inputControlsVisibility']!="")&&(($type_widget == 'widgetExternalContent')||($type_widget =="widgetGisWFS")))
                {
                    $controlsVisibility = mysqli_real_escape_string($link, sanitizePostString('inputControlsVisibility'));      // New pentest COMMON CTR
                }

                if(isset($_POST['inputDefaultTab']) && ($_POST['inputDefaultTab'] != "") && ($_POST['inputDefaultTab'] != null))
                {
                    $defaultTab = mysqli_real_escape_string($link, sanitizePostString('inputDefaultTab'));      // New pentest COMMON CTR
                }
                
                //Aggiunta del campo della tabella "config_widget_dashboard" per i messaggi informativi
                $message_widget = mysqli_real_escape_string($link, sanitizePostString('widgetInfoEditor'));     // New pentest COMMON

                //colore della finestra
                $frame_color = NULL;

                if(isset($_POST['inputTitleWidget'])&&($_POST['inputTitleWidget']!=""))
                {
                    $title_widget = mysqli_real_escape_string($link, sanitizePostString('inputTitleWidget'));       // New pentest COMMON
                    $title_widget = htmlentities($title_widget, ENT_QUOTES|ENT_HTML5);
                }

                if(isset($_POST['inputFreqWidget'])&&($_POST['inputFreqWidget']!=""))
                {
                    $freq_widget = mysqli_real_escape_string($link, sanitizePostInt('inputFreqWidget'));        // New pentest COMMON
                }

                if(isset($_POST['inputFrameColorWidget'])&&($_POST['inputFrameColorWidget']!=""))
                {
                    $frame_color = mysqli_real_escape_string($link, sanitizePostString('inputFrameColorWidget'));       // New pentest COMMON
                }

               //Parametri
               $parameters = NULL;
               
               if(isset($_POST['parameters'])&&($_POST['parameters'] != ""))
               {
                  if(($type_widget == 'widgetServerStatus')||($type_widget == 'widgetBarContent')||($type_widget == 'widgetColumnContent')||($type_widget == 'widgetGaugeChart')||($type_widget == 'widgetSingleContent')||($type_widget == 'widgetSpeedometer')||($type_widget == 'widgetTimeTrend')||($type_widget == 'widgetTimeTrendCompare'))
                  {
                     if($_POST['alrThrSel'] == "yes")
                     {
                      //  $parameters = $_POST['parameters'];     // SANITIZE RELAXED JSON ??
                        $parameters = sanitizeJsonRelaxed($_POST['parameters']);       // SANITIZE RELAXED JSON ??
                     }
                  }
                  else
                  {
                   //  $parameters = $_POST['parameters'];        // SANITIZE JSON ??
                       $parameters = sanitizeJsonRelaxed($_POST['parameters']);       // SANITIZE RELAXED JSON ??
                  }
               } 

                //Gestione parametri per widget di stato del singolo processo
                if($id_metric == 'Process')
                {

                  $schedulerName = sanitizePostString('schedulerName');
                  $jobArea = sanitizePostString('jobArea');
                  $jobGroup = sanitizePostString('jobGroup');
                  $jobName = sanitizePostString('jobName');
                  $parametersArray = array('schedulerName' => $schedulerName, 'jobArea' => $jobArea, 'jobGroup' => $jobGroup, 'jobName' => $jobName);
                  $parameters = json_encode($parametersArray);
                }
		
                if($_REQUEST['widgetCategory'] != "actuator")
                {
                    if($_REQUEST['metricsCategory'] == 'ds')
                    {
                        if(isset($_POST['inputUrlWidget']))
                        {
                            if(preg_match('/^ *$/', $_POST['inputUrlWidget'])) 
                            {
                               $url_widget = "none";
                            }
                            else
                            {
                               $url_widget = mysqli_real_escape_string($link, sanitizePostString('inputUrlWidget'));    // New pentest COMMON
                            }
                        }
                        else
                        {
                            $url_widget = "none";
                        }
                    }
                    else
                    {
                        if(isset($_POST['inputUrlWidgetNR']))
                        {
                            if(preg_match('/^ *$/', $_POST['inputUrlWidgetNR'])) 
                            {
                               $url_widget = "none";
                            }
                            else
                            {
                               $url_widget = mysqli_real_escape_string($link, sanitizePostString('inputUrlWidgetNR'));      // New pentest CTR
                            }
                        }
                    }
                }
                else
                {
                    $url_widget = "none";
                }
                	
                if((($type_widget == 'widgetExternalContent')||($type_widget =="widgetGisWFS"))&&($_REQUEST['widgetMode'] == "selectorWebTarget"))
                {
                    $url_widget = json_encode(array('homepage' => $url_widget, 'widgetMode' => 'selectorWebTarget'));
                }

                $comune_widget = NULL;
                
                if(isset($_POST['inputComuneWidget']) && $_POST['inputComuneWidget'] != "") 
                {
                    $comune_widget = strtoupper(sanitizePostString('inputComuneWidget'));
                } 
                
                if($_REQUEST['widgetCategory'] == "actuator")
                {
                    if($_REQUEST['actuatorTarget'] == 'broker')
                    {
                        $name_widget = preg_replace('/\+/', '', escapeForSQL($_REQUEST['entityType'], $link)) . "_" . escapeForSQL($id_dashboard, $link) . "_" . $type_widget . $nextId;
                        $name_widget = str_replace("/", "_", $name_widget);
                        $name_widget = str_replace(".", "_", $name_widget);
                        $name_widget = str_replace("\\", '_', $name_widget);
                        $name_widget = str_replace(":", "_", $name_widget);
                        $newOrionEntity = [];
                        $newOrionEntity['id'] = $name_widget;
                        $newOrionEntity['type'] = escapeForSQL($_REQUEST['entityType'], $link);
                        $newOrionEntity['type']  = str_replace("/", "_", $newOrionEntity['type'] );
                        $newOrionEntity['type']  = str_replace(".", "_", $newOrionEntity['type'] );
                        $newOrionEntity['type']  = str_replace("\\", '_', $newOrionEntity['type'] );
                        $newOrionEntity['type']  = str_replace(":", "_", $newOrionEntity['type'] );
                        $newOrionEntity['entityDesc'] = ["type" => "String", "value" => $_REQUEST['entityDesc']];
                        $newOrionEntity['entityCreator'] = ["value" => $creator, "type" => "String"];
                        $newOrionEntity['creationDate'] = ["value" => $creationDate, "type" => "String"];
                        $newOrionEntity['actuatorDeleted'] = ["value" => false, "type" => "Boolean"];
                        $newOrionEntity['actuatorDeletionDate'] = ["value" => NULL, "type" => "String"];
                        $newOrionEntity['actuatorCanceller'] = ["value" => NULL, "type" => "String"];
                        $newOrionEntity[$_REQUEST['entityAttrName']] = ["type" => $_REQUEST['entityAttrType'], "value" => $_REQUEST['entityAttrStartValue'], "metadata" => ["attrDesc" => ["value" => $_REQUEST['entityAttrDesc'], "type" => "String"]]];
                        $attributeName = escapeForSQL($_REQUEST['entityAttrName'], $link);
                        $newOrionEntityJson = json_encode($newOrionEntity);
                    }
                    else
                    {
                        $name_widget = preg_replace('/\+/', '', escapeForSQL($_REQUEST['personalAppsInputs'], $link)) . "_" . escapeForSQL($id_dashboard, $link) . "_" . $type_widget . $nextId;
                        $name_widget = str_replace("/", "_", $name_widget);
                        $name_widget = str_replace(".", "_", $name_widget);
                        $name_widget = str_replace("\\", '_', $name_widget);
                        $name_widget = str_replace(":", "_", $name_widget);
                    }
                }
                else
                {
                    $name_widget = preg_replace('/\+/', '', $id_metric) . "_" . escapeForSQL($id_dashboard, $link) . "_" . $type_widget . $nextId;
                    $name_widget = str_replace("/", "_", $name_widget);
                    $name_widget = str_replace(".", "_", $name_widget);
                    $name_widget = str_replace("\\", '_', $name_widget);
                    $name_widget = str_replace(":", "_", $name_widget);
                }
                
                $int_temp_widget = NULL;
                if(isset($_POST['select-IntTemp-Widget']) && $_POST['select-IntTemp-Widget'] != "") 
                {
                    $int_temp_widget = sanitizePostString('select-IntTemp-Widget');

                    if($_POST['select-IntTemp-Widget'] != "Nessuno") 
                    {
                        $name_widget = $name_widget . "_" . preg_replace('/ /', '', escapeForSQL(sanitizePostString('select-IntTemp-Widget'), $link));
                        $name_widget = str_replace("/", "_", $name_widget);
                        $name_widget = str_replace(".", "_", $name_widget);
                        $name_widget = str_replace("\\", '_', $name_widget);
                        $name_widget = str_replace(":", "_", $name_widget);
                    }
                }

                //Va messo qui perché serve il nome del widget
                if(($type_widget == "widgetSelector")||($type_widget == "widgetSelectorWeb"))
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
                 //  $styleParametersArray["borderRadius"] = $_REQUEST['addWidgetBtnRadius'];
                    if (sanitizePostString('addWidgetBtnRadius') == null) {       // New pentest
                        $styleParametersArray["borderRadius"] = mysqli_real_escape_string($link, sanitizeGetString('addWidgetBtnRadius'));
                    } else {
                        $styleParametersArray["borderRadius"] = mysqli_real_escape_string($link, sanitizePostString('addWidgetBtnRadius'));
                    }
                   
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
                   
               //    $styleParametersArray["showText"] = $_REQUEST['addWidgetShowButtonText'];
                    if (sanitizePostString('addWidgetShowButtonText') == null) {       // New pentest
                        $styleParametersArray["showText"] = mysqli_real_escape_string($link, sanitizeGetString('addWidgetShowButtonText'));
                    } else {
                        $styleParametersArray["showText"] = mysqli_real_escape_string($link, sanitizePostString('addWidgetShowButtonText'));
                    }

                    $styleParametersArray["openNewTab"] = "yes";
                    $styleParametersArray["shadow"] = "no";
                   
                  $styleParameters = json_encode($styleParametersArray); 
                }

                $inputUdmWidget = NULL;
                if(isset($_POST['inputUdmWidget']) && ($_POST['inputUdmWidget'] != "")) 
                {
                    $inputUdmWidget = mysqli_real_escape_string($link, sanitizePostString('inputUdmWidget'));       // New pentest COMMON CTR
                    $inputUdmWidget = htmlentities($inputUdmWidget, ENT_QUOTES|ENT_HTML5);
                }
                
                $inputUdmPosition = NULL;
                if(isset($_POST['inputUdmPosition']) && ($_POST['inputUdmPosition'] != "")) 
                {
                    $inputUdmPosition = mysqli_real_escape_string($link, sanitizePostString('inputUdmPosition'));       // New pentest COMMON CTR
                }

                $fontSize = NULL;
                if(isset($_POST['inputFontSize']) && ($_POST['inputFontSize'] != '') && (!empty($_POST['inputFontSize']))) 
                {
                    $fontSize = mysqli_real_escape_string($link, sanitizePostInt('inputFontSize'));     // New pentest COMMON
                }

                $fontColor = NULL;
                if(isset($_POST['inputFontColor']) && ($_POST['inputFontColor'] != '') && (!empty($_POST['inputFontColor']))) 
                {
                    $fontColor = mysqli_real_escape_string($link, sanitizePostString('inputFontColor'));        // New pentest COMMON
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
                    $viewMode = mysqli_real_escape_string($link, sanitizePostString('addWidgetFirstAidMode'));
                }
                else
                {
                    if(isset($_POST['widgetEventsMode'])) 
                    {
                        $viewMode = mysqli_real_escape_string($link, sanitizePostString('widgetEventsMode'));
                    }
                    else
                    {
                        if($type_widget == 'widgetMap')
                        {
                            $viewMode = 'additive';
                        }
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
                    
                    $updTargetQuery = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetEvents%' AND id_dashboard = '". escapeForSQL($id_dashboard, $link) . "'";
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
                              echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard . '&dashboardAuthorName=' . urlencode($dashboardAuthorName) . '&dashboardEditorName=' . urlencode($creator) . '&dashboardTitle=' . urlencode($dashboardName);
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
                        echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard . '&dashboardAuthorName=' . urlencode($dashboardAuthorName) . '&dashboardEditorName=' . urlencode($creator) . '&dashboardTitle=' . urlencode($dashboardName);
                        echo '</script>';
                     }
                }
                ////////////////////////widgetGisWFS/////////////////////////////
                if($type_widget =="widgetGisWFS"){
                    $zoomFactor = 1;
                    $scaleX = 1;
                    $scaleY = 1;                    
                    //Aggiornamento della lista dei target degli widget events
                    mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);                    
                    $updTargetQuery = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetEvents%' AND id_dashboard = '". escapeForSQL($id_dashboard, $link) . "'";
                    $resultUpdTargetQuery = mysqli_query($link, $updTargetQuery);                    
                    if($resultUpdTargetQuery)
                    {
                        while($row = mysqli_fetch_array($resultUpdTargetQuery)) 
                        {
                           $targetList = json_decode($row['parameters'], true);
                           $widgetId = $row['Id'];
                           $targetList[$name_widget] = [];
                           $updatedTargetList = json_encode($targetList);
                           //$updatedTargetList
                           /*MODIFICA AL $updatedTargetList*/
                             $json_parameters0 = $targetList;
                            $coordinates0 = $json_parameters0->{'latLng'};
                            $var_zoom0 = $json_parameters0->{'zoom'};
                            $lat0 = $coordinates0[1];
                            $lng0 = $coordinates0[0];
                            $lat1 = str_replace('"','',$lat0);
                            $lng1 = str_replace('"','',$lng0);
                            $var_zoom1 = str_replace('"', '', $var_zoom0);
                            $updatedTargetList = '{"latLng":['.$lng1.','.$lat1.'],"zoom":'.$var_zoom1.'}';
                            //$updatedTargetList = '{"latLng":['.$coordinates0.',0],"zoom":'.$var_zoom1.',"lat1":"'.$lat1.'","lng1":"'.$lng1.'"}';
                           //
                           $query5 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                           $result5 = mysqli_query($link, $query5);
                           if(!$result5)
                           {
                              $rollbackResult = mysqli_rollback($link);
                              mysqli_close($link);
                              echo '<script type="text/javascript">';
                              echo 'alert("Error while updating widget target lists");';
                              echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard . '&dashboardAuthorName=' . urlencode($dashboardAuthorName) . '&dashboardEditorName=' . urlencode($creator) . '&dashboardTitle=' . urlencode($dashboardName);
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
                        echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard . '&dashboardAuthorName=' . urlencode($dashboardAuthorName) . '&dashboardEditorName=' . urlencode($creator) . '&dashboardTitle=' . urlencode($dashboardName);
                        echo '</script>';
                     }
                     //
                     /*PARAMTERI DEFAULT*/
                     if (($parameters == null)||($parameters == '')){
                             $parameters = '{"latLng":[11.255751,43.76971],"zoom":11}';
                             //
                     }else{
                         //Controlli sulla forma del json
                         
                         $json_parameters = json_decode($parameters);
                         $coordinates = $json_parameters->{'latLng'};
                         $var_zoom = $json_parameters->{'zoom'};
                        $parameters = '{"latLng":['.$coordinates[1].','.$coordinates[0].'],"zoom":'.$var_zoom.'}';
                          
                         //////
                     }
                     /*
                     if (($url_widget == null)||($url_widget == '')){
                             $url_widget = 'gisTarget';
                     }else{
                             $url_widget = 'gisTarget';
                     }
                     */
                     /*gisTarget*/
                     //
                }
                //////////////////////////////////////
                
                if(isset($_REQUEST['addWidgetRegisterGen']))
                {
                //   $notificatorRegistered = escapeForSQL($_REQUEST['addWidgetRegisterGen'], $link);
                //   $notificatorEnabled = escapeForSQL($_REQUEST['addWidgetRegisterGen'], $link);
                    if (sanitizePostString('addWidgetRegisterGen') == null) {       // New pentest
                        $notificatorRegistered = mysqli_real_escape_string($link, sanitizeGetString('addWidgetRegisterGen'));
                    } else {
                        $notificatorRegistered = mysqli_real_escape_string($link, sanitizePostString('addWidgetRegisterGen'));
                    }
                    if (sanitizePostString('addWidgetRegisterGen') == null) {       // New pentest
                        $notificatorEnabled = mysqli_real_escape_string($link, sanitizeGetString('addWidgetRegisterGen'));
                    } else {
                        $notificatorEnabled = mysqli_real_escape_string($link, sanitizePostString('addWidgetRegisterGen'));
                    }

                }
                else
                {
                   $notificatorRegistered = 'no';
                   $notificatorEnabled = 'no';
                }
                
               if(($type_widget == 'widgetTrafficEvents') || ($type_widget == 'widgetTrafficEventsNew'))
               {
                  //31/08/2017 - Patch temporanea in attesa di avere tempo di mettere i controlli sul form
               //   $styleParameters = '{"choosenOption":"events", "timeUdm":"MINUTE", "time":90, "events":50, "defaultCategory":"' . $_REQUEST['addWidgetDefaultCategory'] . '"}';
                   if (sanitizePostString('addWidgetDefaultCategory') == null) {       // New pentest
                       $styleParameters = '{"choosenOption":"events", "timeUdm":"MINUTE", "time":90, "events":50, "defaultCategory":"' . sanitizeGetString('addWidgetDefaultCategory') . '"}';
                   } else {
                       $styleParameters = '{"choosenOption":"events", "timeUdm":"MINUTE", "time":90, "events":50, "defaultCategory":"' . sanitizePostString('addWidgetDefaultCategory') . '"}';
                   }
               }
               
                if(isset($_REQUEST['enableFullscreenTab']))
                {
                //   $enableFullscreenTab = escapeForSQL($_REQUEST['enableFullscreenTab'], $link);
                    if (sanitizePostString('enableFullscreenTab') == null) {       // New pentest
                        $enableFullscreenTab = mysqli_real_escape_string($link, sanitizeGetString('enableFullscreenTab'));
                    } else {
                        $enableFullscreenTab = mysqli_real_escape_string($link, sanitizePostString('enableFullscreenTab'));
                    }
                }
                
                if(isset($_REQUEST['enableFullscreenModal']))
                {
                //   $enableFullscreenModal = escapeForSQL($_REQUEST['enableFullscreenModal'], $link);
                    if (sanitizePostString('enableFullscreenModal') == null) {       // New pentest
                        $enableFullscreenModal = mysqli_real_escape_string($link, sanitizeGetString('enableFullscreenModal'));
                    } else {
                        $enableFullscreenModal = mysqli_real_escape_string($link, sanitizePostString('enableFullscreenModal'));
                    }
                }
                
                $defParamsQuery = "SELECT * FROM Dashboard.Widgets WHERE id_type_widget = '$type_widget'";
                $rDefParamsQuery = mysqli_query($link, $defParamsQuery);
                
                $defaultParametersJson = mysqli_fetch_assoc($rDefParamsQuery)['defaultParameters'];
                $defaultParameters = json_decode($defaultParametersJson);
                
                if(array_key_exists('chartColor', $defaultParameters))
                {
                    $chartColor = $defaultParameters->chartColor;
                }
                else
                {
                    $chartColor = null;
                }
                
                
                                                
                    $name_widget = preg_replace('/%20/', 'NBSP', $name_widget);
                    $name_widget = str_replace("/", "_", $name_widget);
                    $name_widget = str_replace(".", "_", $name_widget);
                    $name_widget = str_replace("\\", '_', $name_widget);
                    $name_widget = str_replace(":", "_", $name_widget);
                
                    $insqDbtb3 = "INSERT INTO Dashboard.Config_widget_dashboard(Id, name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, frequency_w, temporal_range_w, temporal_compare_w, municipality_w, infoMessage_w, link_w, parameters, frame_color_w, udm, udmPos, fontSize, fontColor, controlsPosition, showTitle, controlsVisibility, zoomFactor, defaultTab, zoomControlsColor, scaleX, scaleY, headerFontColor, styleParameters, infoJson, serviceUri, viewMode, hospitalList, notificatorRegistered, notificatorEnabled, enableFullscreenTab, enableFullscreenModal, fontFamily, entityJson, attributeName, creator, creationDate, actuatorTarget, chartColor) " .
                                 "VALUES($nextId , " . returnManagedStringForDb(escapeForSQL($name_widget, $link)) . ", " . returnManagedNumberForDb(escapeForSQL($id_dashboard, $link)) . ", " . returnManagedStringForDb($id_metric) . ", " . returnManagedStringForDb($type_widget) . ", " . returnManagedNumberForDb($firstFreeRow) . ", " . returnManagedNumberForDb($nCol) . ", " . returnManagedNumberForDb($sizeRowsWidget) . ", " . returnManagedNumberForDb($sizeColumnsWidget) . ", " . returnManagedStringForDb($title_widget) . ", " . returnManagedStringForDb($color_widget) . ", " . returnManagedStringForDb($freq_widget) . ", " . returnManagedStringForDb(escapeForSQL($int_temp_widget, $link)) . ", " . returnManagedStringForDb(escapeForSQL($int_comp_widget_m , $link)) . ", " . returnManagedStringForDb(escapeForSQL($comune_widget, $link)) . ", " . returnManagedStringForDb($message_widget) . ", " . returnManagedStringForDb($url_widget) . ", " . returnManagedStringForDb(escapeForSQL($parameters, $link)) . ", " . returnManagedStringForDb($frame_color) . ", " . returnManagedStringForDb($inputUdmWidget) . ", " . returnManagedStringForDb($inputUdmPosition) . ", " . returnManagedNumberForDb($fontSize) . ", " . returnManagedStringForDb($fontColor) . ", " . returnManagedStringForDb($controlsPosition) . ", " . returnManagedStringForDb($showTitle) . ", " . returnManagedStringForDb($controlsVisibility) . ", " . returnManagedNumberForDb($zoomFactor) . ", " . returnManagedStringForDb($defaultTab) . ", " . returnManagedStringForDb($zoomControlsColor) . ", " . returnManagedNumberForDb($scaleX) . ", " . returnManagedNumberForDb($scaleY) . ", " . returnManagedStringForDb($headerFontColor) . ", " . returnManagedStringForDb(escapeForSQL($styleParameters, $link)) . ", " . returnManagedStringForDb($infoJson) . ", " . returnManagedStringForDb($serviceUri) . ", " . returnManagedStringForDb($viewMode) . ", " . returnManagedStringForDb($hospitalList) . ", " . returnManagedStringForDb($notificatorRegistered) . ", " . returnManagedStringForDb($notificatorEnabled) . ", " . returnManagedStringForDb($enableFullscreenTab) . ", " . returnManagedStringForDb($enableFullscreenModal) . ", " . returnManagedStringForDb($fontFamily) . ", " . returnManagedStringForDb(escapeForSQL($newOrionEntityJson, $link)) . ", " . returnManagedStringForDb($attributeName) . ", " . returnManagedStringForDb($creator) . ", " . returnManagedStringForDb($creationDate) . ", " . returnManagedStringForDb($actuatorTarget) . ", " . returnManagedStringForDb($chartColor) . ")";
                    
                    $result4 = mysqli_query($link, $insqDbtb3);
                    
                    if($result4) 
                    {
                        //Creazione entità Orion per widget actuator su broker
                        if(($_REQUEST['widgetCategory'] == "actuator")&&($_REQUEST['actuatorTarget'] == 'broker'))
                        {
                            $orionAddEntityUrl = $orionBaseUrl. "/v2/entities";

                            $orionCallOptions = array(
                                'http' => array(
                                    'header'  => "Content-type: application/json\r\n",
                                    'method'  => 'POST',
                                    'content' => $newOrionEntityJson,
                                    'timeout' => 30
                                )
                            );

                            try
                            {
                               $context  = stream_context_create($orionCallOptions);
                               $callResult = file_get_contents($orionAddEntityUrl, false, $context);
                               
                               if(strpos($http_response_header[0], '201 Created') === false)
                               {            
                                    $delActuatorQuery = "DELETE FROM Dashboard.Config_widget_dashboard WHERE Id = $nextId";
                                    $delActuatorQueryResult = mysqli_query($link, $delActuatorQuery);
                                    mysqli_close($link);
                                    
                                    echo '<script type="text/javascript">';
                                    echo 'alert("Orion entity creation was not possibile: please try again");';
                                    echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard . '&dashboardAuthorName=' . urlencode($dashboardAuthorName) . '&dashboardEditorName=' . urlencode($creator) . '&dashboardTitle=' . urlencode($dashboardName);
                                    echo '</script>';
                                    exit();
                               }
                               else
                               {
                                   //Inserimento primo record di attuazione
                                   $entityId = escapeForSQL($name_widget , $link);
                                   $actionTime = date('Y-m-d H:i:s');
                                   $value = escapeForSQL($_REQUEST['entityAttrStartValue'], $link);
                                   $username = $creator;
                                   $ipAddress = $_SERVER['REMOTE_ADDR'];
                                   
                                   $firstValueQuery = "INSERT INTO Dashboard.ActuatorsEntitiesValues(entityId, actionTime, value, username, ipAddress, actuationResult, actuationResultTime) " .
                                                      "VALUES('$entityId', '$actionTime', '$value', '$username', '$ipAddress', 'Ok', '$actionTime')";
                                   
                                   $queryResult = mysqli_query($link, $firstValueQuery);
                                   mysqli_close($link);
                               }
                            }
                            catch (Exception $ex) 
                            {
                                $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
                                mysqli_select_db($link, $dbname);            
                                $delActuatorQuery = "DELETE FROM Dashboard.Config_widget_dashboard WHERE Id = $nextId";
                                $delActuatorQueryResult = mysqli_query($link, $delActuatorQuery);
                                mysqli_close($link);

                                echo '<script type="text/javascript">';
                                echo 'alert("Orion entity creation was not possibile: please try again");';
                                echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard . '&dashboardAuthorName=' . urlencode($dashboardAuthorName) . '&dashboardEditorName=' . urlencode($creator) . '&dashboardTitle=' . urlencode($dashboardName);
                                echo '</script>';
                                exit();
                            }
                        }
                        
                        //Inserimento valore iniziale in tabella ActuatorsAppsValues
                        if(($_REQUEST['widgetCategory'] == "actuator")&&($_REQUEST['actuatorTarget'] == 'app'))
                        {
                            $actionTime = date('Y-m-d H:i:s');
                            $metricToSearch = escapeForSQL($_REQUEST['personalAppsInputs'], $link);
                            
                            $startValue = escapeForSQL($_REQUEST['personalAppsInputsStartValueHidden'], $link);
                            
                            $qa = "INSERT INTO Dashboard.ActuatorsAppsValues(widgetName, actionTime, value, username, ipAddress, actuationResult, actuationResultTime) " .
                                  "VALUES ('" . escapeForSQL($name_widget, $link) . "', '$actionTime', '$startValue', '" . escapeForSQL($creator, $link) . "', '0:0:0:0', 'Ok', '$actionTime')";
                            
                            $ra = mysqli_query($link, $qa);
                        }
                        
                        header("location: dashboard_configdash.php?dashboardId=" . $id_dashboard . "&dashboardAuthorName=" . urlencode($dashboardAuthorName) . "&dashboardEditorName=" . urlencode($creator) . "&dashboardTitle=" . urlencode($dashboardName));
                        
                        if(isset($_REQUEST['addWidgetRegisterGen']))
                        {
                           if($_REQUEST['addWidgetRegisterGen'] == "yes")
                           {
                              $url = $notificatorUrl;
                              $genOriginalName = preg_replace('/\s+/', '+', $title_widget);
                              $genOriginalType = preg_replace('/\s+/', '+', $id_metric);
                              $containerName = preg_replace('/\s+/', '+', $dashboardName);
                              $appUsr = preg_replace('/\s+/', '+', $creator); 
                              
                              if(isset($_REQUEST['dashboardIdToEdit'])&&isset($_REQUEST['currentDashboardTitle'])&&isset($_REQUEST['dashboardUser']))
                              {
                                  $containerUrl = $appUrl . "/view/indexNR.php?iddasboard=" . base64_encode($id_dashboard); 
                              }
                              else if(isset($_SESSION['dashboardId'])&&isset($_SESSION['dashboardTitle']))
                              {
                                  $containerUrl = $appUrl . "/view/index.php?iddasboard=" . base64_encode($id_dashboard); 
                              }
                              
                            //  $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventGenerator&appName=' . $notificatorAppName . '&appUsr=' . urlencode($appUsr) . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName) . '&url=' . urlencode($containerUrl);
                              $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventGenerator&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&url=' . $containerUrl;
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

                                            // $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName) . '&eventType=' . $eventName . 'thrCnt=1';
                                             $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&eventType=' . $eventName . 'thrCnt=1';
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
                        
                        try 
                        {
                            $lastEditDateQuery = "UPDATE Dashboard.Config_dashboard SET last_edit_date = CURRENT_TIMESTAMP WHERE Id = '". escapeForSQL($id_dashboard, $link) . "'";
                            $lastEditDateResult = mysqli_query($link, $lastEditDateQuery);
                        } 
                        catch (Exception $ex) 
                        {
                            //Per ora nessuna gestione specifica
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
                        echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard . '&dashboardAuthorName=' . urlencode($dashboardAuthorName) . '&dashboardEditorName=' . urlencode($creator) . '&dashboardTitle=' . urlencode($dashboardName);
                        echo '</script>';
                    }
            }
            else 
            {
                mysqli_close($link);
                echo '<script type="text/javascript">';
                echo 'alert("Error: Ripetere inserimento widget");';
                echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard . '&dashboardAuthorName=' . urlencode($dashboardAuthorName) . '&dashboardEditorName=' . urlencode($creator) . '&dashboardTitle=' . urlencode($dashboardName);
                echo '</script>';
            }
    } //Fine caso add_widget
    else if(isset($_REQUEST['openDashboardToEdit']))
    {
        session_start();
        checkSession('Manager');
        
        $isAdmin = $_SESSION['loggedRole'];
        $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
        //$dashboardName = mysqli_real_escape_string($link, $_POST['selectedDashboardName']);
        //$dashboardAuthorName = mysqli_real_escape_string($link, $_POST['selectedDashboardAuthorName']);
        
        //Reperimento da DB del dashboardId e dell'id dell'autore della dashboard
        $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '". $dashboardId. "';";
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
                    header("location: dashboard_configdash.php?dashboardId=" . $dashboardId);
                }
                else
                {
                    header("location: unauthorizedUser.php");
                }
            }
            else if(($_SESSION['loggedRole'] == "AreaManager") || ($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "RootAdmin"))
            {
                //Utente amministratore, edita qualsiasi dashboard
                if((isset($_SESSION['loggedUsername']))&&(isset($_SESSION['dashboardId']))&&(isset($_SESSION['dashboardAuthorName'])))
                {
                    header("location: dashboard_configdash.php?dashboardId=" . $dashboardId);
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
    else if (isset($_REQUEST['disable_dashboard']))
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
        checkSession('Manager');
                
        if(isset($_REQUEST['dashboardIdToEdit'])&&isset($_REQUEST['currentDashboardTitle'])&&isset($_REQUEST['dashboardUser']))
        {
            $dashboardName2 = $_REQUEST['currentDashboardTitle'];
            $lastEditor = $_REQUEST['dashboardUser'];
            $id_dashboard2 = $_REQUEST['dashboardIdToEdit'];
            if (checkVarType($id_dashboard2, "integer") === false) {
                eventLog("Returned the following ERROR in process-form.php for dashboard_id = ".$id_dashboard2.": ".$id_dashboard2." is not an integer as expected. Exit from script.");
                exit();
            }
        }
        else if(isset($_REQUEST['dashboardIdUnderEdit'])&&isset($_REQUEST['dashboardUser']))
        {
          //  $dashboardName2 = $_REQUEST['currentDashboardTitle'];
            if (sanitizePostString('currentDashboardTitle') == null) {       // New pentest
                $dashboardName2 = mysqli_real_escape_string($link, sanitizeGetString('currentDashboardTitle'));
            } else {
                $dashboardName2 = mysqli_real_escape_string($link, sanitizePostString('currentDashboardTitle'));
            }
            $dashboardAuthor = $_REQUEST['dashboardUser'];
            if (sanitizePostString('dashboardUser') == null) {       // New pentest
                $dashboardAuthor = mysqli_real_escape_string($link, sanitizeGetString('dashboardUser'));
            } else {
                $dashboardAuthor = mysqli_real_escape_string($link, sanitizePostString('dashboardUser'));
            }
        //    $lastEditor = $_REQUEST['dashboardEditor'];
            if (sanitizePostString('dashboardEditor') == null) {       // New pentest
                $lastEditor = mysqli_real_escape_string($link, sanitizeGetString('dashboardEditor'));
            } else {
                $lastEditor = mysqli_real_escape_string($link, sanitizePostString('dashboardEditor'));
            }
            $id_dashboard2 = $_REQUEST['dashboardIdUnderEdit'];
            if (checkVarType($id_dashboard2, "integer") === false) {
                eventLog("Returned the following ERROR in process-form.php for dashboard_id = ".$id_dashboard2.": ".$id_dashboard2." is not an integer as expected. Exit from script.");
                exit();
            }
        }
        
        $widgetIdM = $_REQUEST['widgetIdM'];
        if (checkVarType($widgetIdM, "integer") === false) {
            eventLog("Returned the following ERROR in process-form.php for dashboard_id = ".$id_dashboard2.": widgetIdM (".$widgetIdM.") is not an integer as expected. Exit from script.");
            exit();
        }
        
        if($_REQUEST['widgetCategoryHiddenM'] == "actuator")
        {
            if($_REQUEST['widgetActuatorTargetM'] == 'broker')
            {
                $type_widget_m = mysqli_real_escape_string($link, $_REQUEST['widgetActuatorTypeM']);
            }
            else
            {
                $type_widget_m = mysqli_real_escape_string($link, $_REQUEST['widgetActuatorTypeAppsHiddenM']);
            }
        }
        else
        {
            $type_widget_m = mysqli_real_escape_string($link, sanitizePostString('select-widget-m'));
            $name_widget_m = $_POST['inputNameWidgetM'];
            // CONTROLLA SE ESISTE IL WIDGET o MEGLIO se Appartiene alla Dashboard in uso
            if (checkWidgetNameInDashboard($link, $name_widget_m, $id_dashboard2) === false) {
                eventLog("Returned the following ERROR in process-form.php for dashboard_id = ".$id_dashboard2.": the widget ".$name_widget_m." is not instantiated or allowed in this dashboard.");
                exit();
            }
        }
        
        $title_widget_m = NULL;
        $color_widget_m = mysqli_real_escape_string($link, sanitizePostString('inputColorWidgetM'));     // New pentest
        $freq_widget_m = NULL; 
        $col_m = mysqli_real_escape_string($link, sanitizePostInt('inputColumn-m'));     // New pentest
        if (checkVarType($col_m, "integer") === false) {
            if (!is_null($col_m)) {
                eventLog("Returned the following ERROR in process-form.php for dashboard_id = " . $id_dashboard . ": col_m (" . $col_m . ") is not an integer (or NULL) as expected. Exit from script.");
                exit();
            }
        }
        $row_m = mysqli_real_escape_string($link, sanitizePostInt('inputRows-m'));   // New pentest
        if (checkVarType($row_m, "integer") === false) {
            if (!is_null($row_m)) {
                eventLog("Returned the following ERROR in process-form.php for dashboard_id = " . $id_dashboard . ": row_m (" . $row_m . ") is not an integer (or NULL) as expected. Exit from script.");
                exit();
            }
        }
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
        $infoJsonM = NULL;
        $legendFontSizeM = NULL;
        $legendFontColorM = NULL;
        $dataLabelsFontSizeM = NULL;
        $dataLabelsFontColorM = NULL;
        $barsColorsSelectM  = NULL;
        $chartTypeM = NULL;
        $secondaryYAxisM = NULL;
        $dataLabelsDistanceM = NULL;
        $dataLabelsDistance1M = NULL;
        $dataLabelsDistance2M = NULL;
        $dataLabelsM = NULL;
        $dataLabelsRotationM = NULL;
        $xAxisDatasetM = NULL;
        $lineWidthM = NULL;
        $alrLookM = NULL;
        $TypicalTimeTrendM = NULL;
        $TrendTypeM = NULL;
        $ReferenceDateM = NULL;
        $TTTDate = NULL;
        $dayHourView = NULL;
        $computationType = NULL;
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
        $xAxisFormat = NULL;
        $yAxisType = NULL;
        $xAxisLabel = NULL;
        $yAxisLabel = NULL;
        $rowParameters = NULL;
        $defaultUnit = NULL;
        $yAxisMin = NULL;
        $yAxisMax = NULL;
        $secondaryYAxisVuM = NULL;
        $secondaryYAxisLab = NULL;
        $secondaryYAxisMin = NULL;
        $secondaryYAxisMax = NULL;
		$calendarM = 'no';
        $enableCKEditor = NULL;
    //    $fontFamily = mysqli_real_escape_string($link, $_REQUEST['inputFontFamilyWidgetM']);
        if (sanitizePostString('inputFontFamilyWidgetM') === null) {       // New pentest
            $fontFamily = mysqli_real_escape_string($link, sanitizeGetString('inputFontFamilyWidgetM'));
        } else {
            $fontFamily = mysqli_real_escape_string($link, sanitizePostString('inputFontFamilyWidgetM'));
        }
        $groupByAttrM = NULL;
        $areaChartOpacityM = NULL;
        
        $lastEditDate = date('Y-m-d H:i:s');
        
        if($_REQUEST['widgetCategoryHiddenM'] == "actuator")
        {
            if($type_widget_m == "widgetNumericKeyboard")
            {
                $styleParametersArrayM = array('displayColor' => sanitizeString('editKeyboardDisplayColor'), 'displayFontColor' => sanitizeString('editKeyboardDisplayFontColor'), 'btnColor' => sanitizeString('editKeyboardBtnColor'), 'btnFontColor' => sanitizeString('editKeyboardBtnFontColor'));
            }
            
            if($type_widget_m == "widgetGeolocator")
            {
                //$styleParametersArrayM = array('viewMode' => $_REQUEST['editSwitchButtonViewMode'], 'buttonRadius' => $_REQUEST['editSwitchButtonRadius'], 'color' => $_REQUEST['editSwitchButtonColor'], 'clickColor' => $_REQUEST['editSwitchButtonClickColor'], 'symbolColor' => $_REQUEST['editSwitchButtonSymbolColor'],'symbolClickColor' => $_REQUEST['editSwitchButtonSymbolClickColor'], 'neonEffect' => $_REQUEST['editSwitchButtonNeonEffect'], 'textColor' => $_REQUEST['editSwitchButtonTextColor'],'textClickColor' => $_REQUEST['editSwitchButtonTextClickColor'], 'textFontSize' => $_REQUEST['editSwitchButtonTextFontSize'], 'displayFontSize' => $_REQUEST['editSwitchButtonDisplayFontSize'], 'displayFontColor' => $_REQUEST['editSwitchButtonDisplayFontColor'], 'displayFontClickColor' => $_REQUEST['editSwitchButtonDisplayFontClickColor'], 'displayColor' => $_REQUEST['editSwitchButtonDisplayColor'], 'displayRadius' => $_REQUEST['editSwitchButtonDisplayRadius'], 'displayWidth' => $_REQUEST['editSwitchButtonDisplayWidth'], 'displayHeight' => $_REQUEST['editSwitchButtonDisplayHeight']);
                $styleParametersArrayM = array('viewMode' => sanitizePostString('editSwitchButtonViewMode'), 'buttonRadius' => sanitizePostString('editSwitchButtonRadius'), 'color' => sanitizePostString('editSwitchButtonColor'), 'clickColor' => sanitizePostString('editSwitchButtonClickColor'), 'symbolColor' => sanitizePostString('editSwitchButtonSymbolColor'),'symbolClickColor' => sanitizePostString('editSwitchButtonSymbolClickColor'), 'neonEffect' => sanitizePostString('editSwitchButtonNeonEffect'), 'textColor' => sanitizePostString('editSwitchButtonTextColor'),'textClickColor' => sanitizePostString('editSwitchButtonTextClickColor'), 'textFontSize' => sanitizePostInt('editSwitchButtonTextFontSize'), 'displayFontSize' => sanitizePostInt('editSwitchButtonDisplayFontSize'), 'displayFontColor' => sanitizePostString('editSwitchButtonDisplayFontColor'), 'displayFontClickColor' => sanitizePostString('editSwitchButtonDisplayFontClickColor'), 'displayColor' => sanitizePostString('editSwitchButtonDisplayColor'), 'displayRadius' => sanitizePostInt('editSwitchButtonDisplayRadius'), 'displayWidth' => sanitizePostInt('editSwitchButtonDisplayWidth'), 'displayHeight' => sanitizePostInt('editSwitchButtonDisplayHeight'));
            }
            
            if($type_widget_m == "widgetImpulseButton")
            {
                $styleParametersArrayM = array('viewMode' => sanitizePostString('editSwitchButtonViewMode'), 'buttonRadius' => sanitizePostString('editSwitchButtonRadius'), 'color' => sanitizePostString('editSwitchButtonColor'), 'clickColor' => sanitizePostString('editSwitchButtonClickColor'), 'symbolColor' => sanitizePostString('editSwitchButtonSymbolColor'),'symbolClickColor' => sanitizePostString('editSwitchButtonSymbolClickColor'), 'neonEffect' => sanitizePostString('editSwitchButtonNeonEffect'), 'textColor' => sanitizePostString('editSwitchButtonTextColor'),'textClickColor' => sanitizePostString('editSwitchButtonTextClickColor'), 'textFontSize' => sanitizePostInt('editSwitchButtonTextFontSize'), 'displayFontSize' => sanitizePostInt('editSwitchButtonDisplayFontSize'), 'displayFontColor' => sanitizePostString('editSwitchButtonDisplayFontColor'), 'displayFontClickColor' => sanitizePostString('editSwitchButtonDisplayFontClickColor'), 'displayColor' => sanitizePostString('editSwitchButtonDisplayColor'), 'displayRadius' => sanitizePostInt('editSwitchButtonDisplayRadius'), 'displayWidth' => sanitizePostInt('editSwitchButtonDisplayWidth'), 'displayHeight' => sanitizePostInt('editSwitchButtonDisplayHeight'));
            }
            
            if($type_widget_m == "widgetOnOffButton")
            {
                $styleParametersArrayM = array('viewMode' => sanitizePostString('editSwitchButtonViewMode'), 'buttonRadius' => sanitizePostInt('editSwitchButtonRadius'), 'offColor' => sanitizePostString('editSwitchButtonOffColor'), 'onColor' => sanitizePostString('editSwitchButtonOnColor'), 'symbolOffColor' => sanitizePostString('editSwitchButtonSymbolOffColor'),'symbolOnColor' => sanitizePostString('editSwitchButtonSymbolOnColor'), 'neonEffect' => sanitizePostString('editSwitchButtonNeonEffect'), 'textOffColor' => sanitizePostString('editSwitchButtonTextOffColor'),'textOnColor' => sanitizePostString('editSwitchButtonTextOnColor'), 'textFontSize' => sanitizePostInt('editSwitchButtonTextFontSize'), 'displayFontSize' => sanitizePostInt('editSwitchButtonDisplayFontSize'), 'displayOffColor' => sanitizePostString('editSwitchButtonDisplayOffColor'), 'displayOnColor' => sanitizePostString('editSwitchButtonDisplayOnColor'), 'displayColor' => sanitizePostString('editSwitchButtonDisplayColor'), 'displayRadius' => sanitizePostInt('editSwitchButtonDisplayRadius'), 'displayWidth' => sanitizePostInt('editSwitchButtonDisplayWidth'), 'displayHeight' => sanitizePostInt('editSwitchButtonDisplayHeight'));
            }
            
            if($type_widget_m == "widgetKnob")
            {
                $styleParametersArrayM = array('indicatorRadius' =>sanitizePostFloat('editKnobIndicatorRadius'), 'displayRadius' => sanitizePostFloat('editKnobDisplayRadius'), 'startAngle' => sanitizePostFloat('editKnobStartAngle'), 'endAngle' => sanitizePostFloat('editKnobEndAngle'), 'displayColor' => sanitizePostString('editKnobDisplayColor'), 'ticksColor' => sanitizePostString('editKnobTicksColor'), 'labelsFontSize' => sanitizePostInt('editKnobLabelsFontSize'), 'labelsFontColor' => sanitizePostString('editKnobLabelsFontColor'), 'increaseValue' => sanitizePostFloat('editKnobIncreaseValue'));
            }
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if($type_widget_m == "widgetPrevMeteo")
        {
            //$styleParametersArrayM = array('orientation' => $_REQUEST['orientationM'], 'language' => $_REQUEST['languageM'], 'todayDim' => $_REQUEST['todayDimM'], 'backgroundMode' => $_REQUEST['backgroundModeM'], 'iconSet' => $_REQUEST['iconSetM']);
            $styleParametersArrayM = array('orientation' => sanitizePostString('orientationM'), 'language' => sanitizePostString('languageM'), 'todayDim' => sanitizePostString('todayDimM'), 'backgroundMode' => sanitizePostString('backgroundModeM'), 'iconSet' => sanitizePostString('iconSetM'));
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if(($type_widget_m == "widgetSelectorNew"))
        {
            if (sanitizePostString('iconTextMode') === null) {
                $iconText = sanitizeGetString('iconTextMode');
            } else {
                $iconText = sanitizePostString('iconTextMode');
            }
            //   $styleParametersArray = array('activeFontColor' => $_REQUEST['addGisActiveQueriesFontColor']);

            if (sanitizePostString('mapPinIcon') === null) {
                $mapPinIcon = sanitizeGetString('mapPinIcon');
            } else {
                $mapPinIcon = sanitizePostString('mapPinIcon');
            }

            if (sanitizePostString('editGisActiveQueriesFontColor') === null) {       // New pentest COMMON CTR
                $styleParametersArrayM = array('activeFontColor' => sanitizeGetString('editGisActiveQueriesFontColor'), 'iconText' => $iconText, 'mapPinIcon' => $mapPinIcon);
            } else {
                $styleParametersArrayM = array('activeFontColor' => sanitizePostString('editGisActiveQueriesFontColor'), 'iconText' => $iconText, 'mapPinIcon' => $mapPinIcon);
            }
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if(($type_widget_m == "widgetSelector") || ($type_widget_m == "widgetSelectorTech"))
        {
        //  $styleParametersArrayM = array('activeFontColor' => $_REQUEST['editGisActiveQueriesFontColor']);
            if (sanitizePostString('editGisActiveQueriesFontColor') === null) {       // New pentest COMMON CTR
                $styleParametersArrayM = array('activeFontColor' => sanitizeGetString('editGisActiveQueriesFontColor'));
            } else {
                $styleParametersArrayM = mysqli_real_escape_string($link, sanitizePostString('editGisActiveQueriesFontColor'));
            }
          $styleParametersM = json_encode($styleParametersArrayM);
        }
        
        if($type_widget_m == "widgetSelectorWeb")
        {
            if (sanitizePostString('iconTextMode') === null) {
                $iconText = sanitizeGetString('iconTextMode');
            } else {
                $iconText = sanitizePostString('iconTextMode');
            }
        //  $styleParametersArrayM = array('activeFontColor' => $_REQUEST['editGisActiveQueriesFontColor'], 'rectDim' => $_REQUEST['editGisRectDim']);
            //      $styleParametersArrayM = array('activeFontColor' => sanitizePostString('editGisActiveQueriesFontColor'), 'rectDim' => sanitizePostString('editGisRectDim'));
            if (sanitizePostString('editGisActiveQueriesFontColor') == null) {       // New pentest COMMON CTR
                $activeFontColorM = sanitizeGetString('editGisActiveQueriesFontColor');
            } else {
                $activeFontColorM = sanitizePostString('editGisActiveQueriesFontColor');
            }
            if (sanitizePostString('editGisRectDim') == null) {       // New pentest COMMON CTR
                $rectDimM = sanitizeGetString('editGisRectDim');
            } else {
                $rectDimM = sanitizePostString('editGisRectDim');
            }
            $styleParametersArrayM = array('activeFontColor' => $activeFontColorM, 'rectDim' => $rectDimM, 'iconText' => $iconText);
          $styleParametersM = json_encode($styleParametersArrayM);
        }
        
        if($type_widget_m == "widgetClock")
        {
           if(isset($_REQUEST['editWidgetClockData']))
           {
           //   $clockDataM = $_REQUEST['editWidgetClockData'];
               if (sanitizePostString('editWidgetClockData') == null) {       // New pentest COMMON CTR
                   $clockDataM = mysqli_real_escape_string($link, sanitizeGetString('editWidgetClockData'));
               } else {
                   $clockDataM = mysqli_real_escape_string($link, sanitizePostString('editWidgetClockData'));
               }
           }

           if(isset($_REQUEST['editWidgetClockFont']))
           {
            //  $clockFontM = $_REQUEST['editWidgetClockFont'];
               if (sanitizePostString('editWidgetClockFont') == null) {       // New pentest COMMON CTR
                   $clockFontM = mysqli_real_escape_string($link, sanitizeGetString('editWidgetClockFont'));
               } else {
                   $clockFontM = mysqli_real_escape_string($link, sanitizePostString('editWidgetClockFont'));
               }
           }

           $styleParametersArrayM = array('clockData' => escapeForSQL($clockDataM, $link), 'clockFont' => escapeForSQL($clockFontM, $link));
           $styleParametersM = json_encode($styleParametersArrayM);
        }
        
        
         if($type_widget_m == "widgetProtezioneCivile" || $type_widget_m == "widgetProtezioneCivileFirenze")
         {
           if(isset($_POST['meteoTabFontSizeM']) && ($_POST['meteoTabFontSizeM'] != "") && ($_POST['meteoTabFontSizeM'] != null))
           {
               $meteoTabFontSize = mysqli_real_escape_string($link, sanitizePostInt('meteoTabFontSizeM'));
           }

           if(isset($_POST['genTabFontSizeM']) && ($_POST['genTabFontSizeM'] != "") && ($_POST['genTabFontSizeM'] != null))
           {
               $genTabFontSize = mysqli_real_escape_string($link, sanitizePostInt('genTabFontSizeM'));
           }

           if(isset($_POST['genTabFontColorM']) && ($_POST['genTabFontColorM'] != "") && ($_POST['genTabFontColorM'] != null))
           {
               $genTabFontColor = mysqli_real_escape_string($link, sanitizePostString('genTabFontColorM'));
           }

           $styleParametersArrayM = array('meteoTabFontSize' => $meteoTabFontSize, 'genTabFontSize' => $genTabFontSize, 'genTabFontColor' => $genTabFontColor);
           $styleParametersM = json_encode($styleParametersArrayM);
         }

        if($type_widget_m == "widgetFirstAid")
        {
            if(isset($_POST['showTableFirstCellM'])&&($_POST['showTableFirstCellM']!=""))
            {
                $showTableFirstCellM = mysqli_real_escape_string($link, sanitizePostString('showTableFirstCellM'));
            }

            if(isset($_POST['tableFirstCellFontSizeM'])&&($_POST['tableFirstCellFontSizeM']!=""))
            {
                $tableFirstCellFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('tableFirstCellFontSizeM'));
            }

            if(isset($_POST['tableFirstCellFontColorM'])&&($_POST['tableFirstCellFontColorM']!=""))
            {
                $tableFirstCellFontColorM = mysqli_real_escape_string($link, sanitizePostString('tableFirstCellFontColorM'));
            }

            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('rowsLabelsFontSizeM'));
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColorM'));
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('colsLabelsFontSizeM'));
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColorM'));
            }

            if(isset($_POST['rowsLabelsBckColorM'])&&($_POST['rowsLabelsBckColorM']!=""))
            {
                $rowsLabelsBckColorM = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsBckColorM'));
            }
            
            if(isset($_POST['tableBordersM'])&&($_POST['tableBordersM']!=""))
            {
                $tableBordersM = mysqli_real_escape_string($link, sanitizePostString('tableBordersM'));
            }

            if(isset($_POST['tableBordersColorM'])&&($_POST['tableBordersColorM']!=""))
            {
                $tableBordersColorM = mysqli_real_escape_string($link, sanitizePostString('tableBordersColorM'));
            }

            $styleParametersArrayM = array('showTableFirstCell' => $showTableFirstCellM, 'tableFirstCellFontSize' => $tableFirstCellFontSizeM, 'tableFirstCellFontColor' => $tableFirstCellFontColorM, 'rowsLabelsFontSize' => $rowsLabelsFontSizeM, 'rowsLabelsFontColor' => $rowsLabelsFontColorM, 'colsLabelsFontSize' => $colsLabelsFontSizeM, 'colsLabelsFontColor' => $colsLabelsFontColorM, 'rowsLabelsBckColor' => $rowsLabelsBckColorM, 'colsLabelsBckColor' => $colsLabelsBckColorM, 'tableBorders' => $tableBordersM, 'tableBordersColor' => $tableBordersColorM);
            $styleParametersM = json_encode($styleParametersArrayM);
        }
        
        if($type_widget_m == "widgetPieChart")
        {
            if(isset($_POST['legendFontSizeM'])&&($_POST['legendFontSizeM']!=""))
            {
                $legendFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('legendFontSizeM'));
            }

            if(isset($_POST['legendFontColorPickerM'])&&($_POST['legendFontColorPickerM']!=""))
            {
                $legendFontColorM = mysqli_real_escape_string($link, sanitizePostString('legendFontColorPickerM'));
            }

            if(isset($_POST['dataLabelsDistanceM'])&&($_POST['dataLabelsDistanceM']!=""))
            {
                $dataLabelsDistanceM = mysqli_real_escape_string($link, sanitizePostFloat('dataLabelsDistanceM'));
            }

            if(isset($_POST['dataLabelsDistance1M'])&&($_POST['dataLabelsDistance1M']!=""))
            {
                $dataLabelsDistance1M = mysqli_real_escape_string($link, sanitizePostFloat('dataLabelsDistance1M'));
            }

            if(isset($_POST['dataLabelsDistance2M'])&&($_POST['dataLabelsDistance2M']!=""))
            {
                $dataLabelsDistance2M = mysqli_real_escape_string($link, sanitizePostFloat('dataLabelsDistance2M'));
            }

            if(isset($_POST['dataLabelsM'])&&($_POST['dataLabelsM']!=""))
            {
                $dataLabelsM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsM'));
            }

            if(isset($_POST['dataLabelsFontSizeM'])&&($_POST['dataLabelsFontSizeM']!=""))
            {
                $dataLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('dataLabelsFontSizeM'));
            }

            if(isset($_POST['dataLabelsFontColorM'])&&($_POST['dataLabelsFontColorM']!=""))
            {
                $dataLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsFontColorM'));
            }

            if(isset($_POST['innerRadius1M'])&&($_POST['innerRadius1M']!=""))
            {
                $innerRadius1M = mysqli_real_escape_string($link, sanitizePostFloat('innerRadius1M'));
            }

            if(isset($_POST['startAngleM'])&&($_POST['startAngleM']!=""))
            {
                $startAngleM = mysqli_real_escape_string($link, sanitizePostFloat('startAngleM'));
            }

            if(isset($_POST['endAngleM'])&&($_POST['endAngleM']!=""))
            {
                $endAngleM = mysqli_real_escape_string($link, sanitizePostFloat('endAngleM'));
            }

            if(isset($_POST['centerYM'])&&($_POST['centerYM']!=""))
            {
                $centerYM = mysqli_real_escape_string($link, sanitizePostFloat('centerYM'));
            }

            if(isset($_POST['outerRadius1M'])&&($_POST['outerRadius1M']!=""))
            {
                $outerRadius1M = mysqli_real_escape_string($link, sanitizePostFloat('outerRadius1M'));
            }

            if(isset($_POST['innerRadius2M'])&&($_POST['innerRadius2M']!=""))
            {
                $innerRadius2M = mysqli_real_escape_string($link, sanitizePostFloat('innerRadius2M'));
            }

            if(isset($_POST['colorsSelect1M'])&&($_POST['colorsSelect1M']!=""))
            {
                $colorsSelect1M = mysqli_real_escape_string($link, sanitizePostString('colorsSelect1M'));
            }

            if(isset($_POST['colors1M'])&&($_POST['colors1M']!=""))
            {
                $temp = json_decode(sanitizeJson($_POST['colors1M']));        // SANITIZE QUI ?
                $colors1M = [];
                foreach ($temp as $color) 
                {
                    array_push($colors1M, $color);
                }
            }

            if(isset($_POST['colorsSelect2M'])&&($_POST['colorsSelect2M']!=""))
            {
                $colorsSelect2M = sanitizePostString('colorsSelect2M');
            }

            if(isset($_POST['colors2M'])&&($_POST['colors2M']!=""))
            {
                $temp = json_decode(sanitizeJson($_POST['colors2M']));        // SANITIZE QUI ??
                $colors2M = [];
                foreach ($temp as $color) 
                {
                    array_push($colors2M, $color);
                }
            }

            if(isset($_POST['deviceLabelsM_0'])&&($_POST['deviceLabelsM_0']!=""))
            {
                $deviceLabels = [];
                $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_0'));     // New pentest
                array_push($deviceLabels, $label);
                $k = 1;
                while (isset($_POST['deviceLabelsM_'.$k])&&($_POST['deviceLabelsM_'.$k]!="")) {
                    $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_'.$k));     // New pentest
                    array_push($deviceLabels, $label);
                    $k++;
                }
            }

            if(isset($_POST['groupByAttrM'])&&($_POST['groupByAttrM']!=""))
            {
                $groupByAttrM = mysqli_real_escape_string($link, sanitizePostString('groupByAttrM'));      // New pentest
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
            $styleParametersArrayM['editDeviceLabels'] = $deviceLabels;
            $styleParametersArrayM['groupByAttr'] = $groupByAttrM;
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if($type_widget_m == "widgetGaugeChart") {
            if(isset($_POST['setMaxValueM'])&&($_POST['setMaxValueM']!=""))
            {
                $setMaxValueM = mysqli_real_escape_string($link, sanitizePostInt('setMaxValueM'));
            }
            $styleParametersArrayM = array();
            $styleParametersArrayM['setMaxValue'] = $setMaxValueM;
            if(isset($_POST['setMinValueM'])&&($_POST['setMinValueM']!=""))
            {
                $setMinValueM = mysqli_real_escape_string($link, sanitizePostInt('setMinValueM'));
            }
            $styleParametersArrayM['setMinValue'] = $setMinValueM;
            if(isset($_POST['setDecimalPlacesM'])&&($_POST['setDecimalPlacesM']!=""))
            {
                $setDecimalPlacesM = mysqli_real_escape_string($link, sanitizePostInt('setDecimalPlacesM'));
            }
            $styleParametersArrayM['setDecimalPlaces'] = $setDecimalPlacesM;
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if($type_widget_m == "widgetSpeedometer") {
            if(isset($_POST['setMaxValueM'])&&($_POST['setMaxValueM']!=""))
            {
                $setMaxValueM = mysqli_real_escape_string($link, sanitizePostInt('setMaxValueM'));
            }
            $styleParametersArrayM = array();
            $styleParametersArrayM['setMaxValue'] = $setMaxValueM;
            if(isset($_POST['setMinValueM'])&&($_POST['setMinValueM']!=""))
            {
                $setMinValueM = mysqli_real_escape_string($link, sanitizePostInt('setMinValueM'));
            }
            $styleParametersArrayM['setMinValue'] = $setMinValueM;
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if(($type_widget_m == "widgetLineSeries") || ($type_widget_m == "widgetCurvedLineSeries") || ($type_widget_m == "widgetDataCube"))
        {

            if($type_widget_m == "widgetDataCube" && isset($_POST['defaultUnit']) && ($_POST['defaultUnit']!=""))
            {
                $defaultUnit = mysqli_real_escape_string($link, sanitizePostString('defaultUnit'));
            }

            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('rowsLabelsFontSizeM'));
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColorM'));
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('colsLabelsFontSizeM'));
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColorM'));
            }

            if(isset($_POST['dataLabelsFontSizeM'])&&($_POST['dataLabelsFontSizeM']!=""))
            {
                $dataLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('dataLabelsFontSizeM'));
            }

            if(isset($_POST['dataLabelsFontColorM'])&&($_POST['dataLabelsFontColorM']!=""))
            {
                $dataLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsFontColorM'));
            }

            if(isset($_POST['legendFontSizeM'])&&($_POST['legendFontSizeM']!=""))
            {
                $legendFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('legendFontSizeM'));
            }

            if(isset($_POST['legendFontColorM'])&&($_POST['legendFontColorM']!=""))
            {
                $legendFontColorM = mysqli_real_escape_string($link, sanitizePostString('legendFontColorM'));
            }

            if(isset($_POST['barsColorsSelectM'])&&($_POST['barsColorsSelectM']!=""))
            {
                $barsColorsSelectM = mysqli_real_escape_string($link, sanitizePostString('barsColorsSelectM'));
            }

            if(isset($_POST['chartTypeM'])&&($_POST['chartTypeM']!=""))
            {
                $chartTypeM = mysqli_real_escape_string($link, sanitizePostString('chartTypeM'));
            }
			//calendarM
			if(isset($_POST['calendarM'])&&($_POST['calendarM']!=""))
            {
                $calendarM = mysqli_real_escape_string($link, sanitizePostString('calendarM'));
            }

            if(isset($_POST['secondaryYAxisM'])&&($_POST['secondaryYAxisM']!=""))
            {
                $secondaryYAxisM = mysqli_real_escape_string($link, sanitizePostString('secondaryYAxisM'));
            }

            if(isset($_POST['dataLabelsM'])&&($_POST['dataLabelsM']!=""))
            {
                $dataLabelsM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsM'));
            }

            if(isset($_POST['xAxisDatasetM'])&&($_POST['xAxisDatasetM']!=""))
            {
                $xAxisDatasetM = sanitizePostString('xAxisDatasetM');
            }

            if(isset($_POST['lineWidthM'])&&($_POST['lineWidthM']!=""))
            {
                $lineWidthM = sanitizePostInt('lineWidthM');
            }

            if(isset($_POST['alrLookM'])&&($_POST['alrLookM']!=""))
            {
                $alrLookM = sanitizePostString('alrLookM');
            }
            if(isset($_POST['TypicalTimeTrendM'])&&($_POST['TypicalTimeTrendM']!=""))
            {
                $TypicalTimeTrendM = sanitizePostString('TypicalTimeTrendM');
            }
            if(isset($_POST['TrendTypeM'])&&($_POST['TrendTypeM']!=""))
            {
                $TrendTypeM = sanitizePostString('TrendTypeM');
            }
            if(isset($_POST['ReferenceDateM'])&&($_POST['ReferenceDateM']!=""))
            {
                $ReferenceDateM = sanitizePostString('ReferenceDateM');
            }
            $result_total = $_POST['TTTDate'];
            $result_explode = explode('|', $result_total);
            if (isset($result_explode[0])&&($result_explode[0]!="")){ 
                $TTTDate = $result_explode[0];
            }
            if (isset($result_explode[1])&&($result_explode[1]!="")){ 
                $computationType = $result_explode[1];
            }
            if(isset($_POST['dayHourView'])&&($_POST['dayHourView']!=""))
            {
                $dayHourView = sanitizePostString('dayHourView');
            }
            if(isset($_POST['areaChartOpacityM'])&&($_POST['areaChartOpacityM']!=""))
            {
                $areaChartOpacityM = sanitizePostString('areaChartOpacityM');
            }

            if(isset($_POST['showContentLoadM'])&&($_POST['showContentLoadM']!=""))
            {
                $showContentLoadM = mysqli_real_escape_string($link, sanitizePostString('showContentLoadM'));
            }

            if(isset($_POST['exportM'])&&($_POST['exportM']!=""))
            {
                $exportM = mysqli_real_escape_string($link, sanitizePostString('exportM'));
            }
            
            //if(isset($_POST['TTTDate'])&&($_POST['TTTDate']!=""))
            //{
            //    $TTTDate = sanitizePostString('TTTDate');
            //}
            //if(isset($_POST['computationType'])&&($_POST['computationType']!=""))
            //{
            //    $computationType = sanitizePostString('computationType');
            //}

            $styleParametersArrayM = array();
            if ($type_widget_m == "widgetDataCube") {
                $styleParametersArrayM['defaultUnit'] = $defaultUnit;
            }
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
            $styleParametersArrayM['secondaryYAxisM'] = $secondaryYAxisM;
            $styleParametersArrayM['dataLabels'] = $dataLabelsM;
            $styleParametersArrayM['xAxisDataset'] = $xAxisDatasetM;
            $styleParametersArrayM['lineWidth'] = $lineWidthM;
            $styleParametersArrayM['alrLook'] = $alrLookM;
            $styleParametersArrayM['areaChartOpacityM'] = $areaChartOpacityM;
			$styleParametersArrayM['calendarM'] = $calendarM;
            $styleParametersArrayM['showContentLoadM'] = $showContentLoadM;
            $styleParametersArrayM['exportM'] = $exportM;

            if(isset($_POST['deviceLabelsM_0'])&&($_POST['deviceLabelsM_0']!=""))
            {
                $deviceLabels = [];
                $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_0'));
                array_push($deviceLabels, $label);
                $k = 1;
                while (isset($_POST['deviceLabelsM_'.$k])&&($_POST['deviceLabelsM_'.$k]!="")) {
                    $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_'.$k));
                    array_push($deviceLabels, $label);
                    $k++;
                }
            }
            $styleParametersArrayM['editDeviceLabels'] = $deviceLabels;

            if(isset($_POST['barsColorsM'])&&($_POST['barsColorsM']!=""))
            {
                $temp = json_decode(sanitizeJson($_POST['barsColorsM']));     // SANITIZE QUI ??
                $barsColorsM = [];
                foreach ($temp as $color) 
                {
                    array_push($barsColorsM, $color);
                }
            }
            $styleParametersArrayM['barsColors'] = $barsColorsM;

            if(isset($_POST['xAxisFormat'])&&($_POST['xAxisFormat']!=""))
            {
                $xAxisFormat = sanitizePostString('xAxisFormat');
            }
            $styleParametersArrayM['xAxisFormat'] = $xAxisFormat;

            if(isset($_POST['yAxisType'])&&($_POST['yAxisType']!=""))
            {
                $yAxisType = sanitizePostString('yAxisType');
            }
            $styleParametersArrayM['yAxisType'] = $yAxisType;

            if(isset($_POST['xAxisLabel'])&&($_POST['xAxisLabel']!=""))
            {
                $xAxisLabel = sanitizePostString('xAxisLabel');
            }
            $styleParametersArrayM['xAxisLabel'] = $xAxisLabel;

            if(isset($_POST['yAxisLabel'])&&($_POST['yAxisLabel']!=""))
            {
                $yAxisLabel = sanitizePostString('yAxisLabel');
            }
            $styleParametersArrayM['yAxisLabel'] = $yAxisLabel;

            if(isset($_POST['yAxisMin'])&&($_POST['yAxisMin']!="")) {
                $yAxisMin = sanitizePostString('yAxisMin');
            }
            $styleParametersArrayM['yAxisMin'] = $yAxisMin;

            if(isset($_POST['yAxisMax'])&&($_POST['yAxisMax']!="")) {
                $yAxisMax = sanitizePostString('yAxisMax');
            }
            $styleParametersArrayM['yAxisMax'] = $yAxisMax;

            if(isset($_POST['secondaryYAxisVuM'])&&($_POST['secondaryYAxisVuM']!="")) {
                $secondaryYAxisVuM = sanitizePostString('secondaryYAxisVuM');
            }
            $styleParametersArrayM['secondaryYAxisVuM'] = $secondaryYAxisVuM;

            if(isset($_POST['secondaryYAxisLab'])&&($_POST['secondaryYAxisLab']!="")) {
                $secondaryYAxisLab = sanitizePostString('secondaryYAxisLab');
            }
            $styleParametersArrayM['secondaryYAxisLab'] = $secondaryYAxisLab;

            if(isset($_POST['secondaryYAxisMin'])&&($_POST['secondaryYAxisMin']!="")) {
                $secondaryYAxisMin = sanitizePostString('secondaryYAxisMin');
            }
            $styleParametersArrayM['secondaryYAxisMin'] = $secondaryYAxisMin;

            if(isset($_POST['secondaryYAxisMax'])&&($_POST['secondaryYAxisMax']!="")) {
                $secondaryYAxisMax = sanitizePostString('secondaryYAxisMax');
            }
            $styleParametersArrayM['secondaryYAxisMax'] = $secondaryYAxisMax;

            if(isset($_POST['enableCKEditor'])&&($_POST['enableCKEditor']!="")) {
                $enableCKEditor = mysqli_real_escape_string($link, sanitizePostString('enableCKEditor'));
            }
            $styleParametersArrayM['enableCKEditor'] = $enableCKEditor;

            $styleParametersM = json_encode($styleParametersArrayM);

            if(isset($_POST['parametersM'])&&($_POST['parametersM']!="")) {
                $rowParameters = sanitizeJsonRelaxed($_POST['parametersM']);
                $parametersArray = json_decode($rowParameters, false);
                if (is_array($parametersArray)) {
                    for ($i = (sizeof($parametersArray)) - 1; $i >= 0; $i--) {
                        if (isset($parametersArray[$i]->deleted)) {
                            if ($parametersArray[$i]->deleted == true) {
                                //   array_map('unlink', glob("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i . "/*"));
                                unset($parametersArray[$i]);
                            }
                        }
                    }
                    $rowParameters = json_encode(array_values($parametersArray));
                } else if (is_object($parametersArray)) {
                    $rowParameters = json_encode($parametersArray);
                }

                $_POST['parametersM'] = NULL;
            }

        }

        if($type_widget_m == "widgetCalendar")
        {
            $styleParametersArrayM = array();

            if(isset($_POST['barsColorsM'])&&($_POST['barsColorsM']!=""))
            {
                $temp = json_decode(sanitizeJson($_POST['barsColorsM']));     // SANITIZE QUI ??
                $barsColorsM = [];
                foreach ($temp as $color)
                {
                    array_push($barsColorsM, $color);
                }
            }
            $styleParametersArrayM['barsColors'] = $barsColorsM;

            if(isset($_POST['meanSum'])&&($_POST['meanSum']!=""))
            {
                $meanSum = sanitizePostString('meanSum');
            }
            $styleParametersArrayM['meanSum'] = $meanSum;

            if(isset($_POST['calendarViewMode'])&&($_POST['calendarViewMode']!=""))
            {
                $calendarViewMode = sanitizePostString('calendarViewMode');
            }
            $styleParametersArrayM['calendarViewMode'] = $calendarViewMode;

            $styleParametersM = json_encode($styleParametersArrayM);

            if(isset($_POST['parametersM'])&&($_POST['parametersM']!="")) {
                $rowParameters = sanitizeJsonRelaxed($_POST['parametersM']);
                $parametersArray = json_decode($rowParameters, false);
                for($i = (sizeof($parametersArray)) - 1; $i >= 0; $i--) {
                    if (isset($parametersArray[$i]->deleted)) {
                        if ($parametersArray[$i]->deleted == true) {
                            //   array_map('unlink', glob("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i . "/*"));
                            unset($parametersArray[$i]);
                        }
                    }
                }
                $rowParameters = json_encode(array_values($parametersArray));
                $_POST['parametersM'] = NULL;
            }
        }

        if($type_widget_m == "widgetBarSeries")
        {
            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('rowsLabelsFontSizeM'));     // New pentest
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColorM'));    // New Pentest
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('colsLabelsFontSizeM'));      // New pentest
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColorM'));    // New pentest
            }

            if(isset($_POST['dataLabelsFontSizeM'])&&($_POST['dataLabelsFontSizeM']!=""))
            {
                $dataLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('dataLabelsFontSizeM'));     // New pentest
            }

            if(isset($_POST['dataLabelsFontColorM'])&&($_POST['dataLabelsFontColorM']!=""))
            {
                $dataLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsFontColorM'));    // New pentest
            }

            if(isset($_POST['legendFontSizeM'])&&($_POST['legendFontSizeM']!=""))
            {
                $legendFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('legendFontSizeM'));     // New pentest
            }

            if(isset($_POST['legendFontColorM'])&&($_POST['legendFontColorM']!=""))
            {
                $legendFontColorM = mysqli_real_escape_string($link, sanitizePostString('legendFontColorM'));    // New pentest
            }

            if(isset($_POST['barsColorsSelectM'])&&($_POST['barsColorsSelectM']!=""))
            {
                $barsColorsSelectM = mysqli_real_escape_string($link, sanitizePostString('barsColorsSelectM'));      // New pentest
            }

            if(isset($_POST['chartTypeM'])&&($_POST['chartTypeM']!=""))
            {
                $chartTypeM = mysqli_real_escape_string($link, sanitizePostString('chartTypeM'));    // New pentest
            }
			
			if(isset($_POST['calendarM'])&&($_POST['calendarM']!=""))
            {
                $calendarM = mysqli_real_escape_string($link, sanitizePostString('calendarM'));    // New pentest
            }
			
			
			if(isset($_POST['calendarM'])&&($_POST['calendarM']!=""))
            {
                $calendarM = mysqli_real_escape_string($link, sanitizePostString('calendarM'));    // New pentest
            }

            if(isset($_POST['dataLabelsM'])&&($_POST['dataLabelsM']!=""))
            {
                $dataLabelsM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsM'));      // New pentest
            }

            if(isset($_POST['dataLabelsRotationM'])&&($_POST['dataLabelsRotationM']!=""))
            {
                $dataLabelsRotationM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsRotationM'));      // New pentest
            }

            if(isset($_POST['deviceLabelsM_0'])&&($_POST['deviceLabelsM_0']!=""))
            {
                $deviceLabels = [];
                $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_0'));     // New pentest
                array_push($deviceLabels, $label);
                $k = 1;
                while (isset($_POST['deviceLabelsM_'.$k])&&($_POST['deviceLabelsM_'.$k]!="")) {
                    $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_'.$k));     // New pentest
                    array_push($deviceLabels, $label);
                    $k++;
                }
            }

            if(isset($_POST['groupByAttrM'])&&($_POST['groupByAttrM']!=""))
            {
                $groupByAttrM = mysqli_real_escape_string($link, sanitizePostString('groupByAttrM'));      // New pentest
            }

            if(isset($_POST['sortBarValuesM'])&&($_POST['sortBarValuesM']!=""))
            {
                $sortBarValuesM = mysqli_real_escape_string($link, sanitizePostString('sortBarValuesM'));      // New pentest
            }

            if(isset($_POST['enableCKEditor'])&&($_POST['enableCKEditor']!=""))
            {
                $enableCKEditor = mysqli_real_escape_string($link, sanitizePostString('enableCKEditor'));
            }

            if(isset($_POST['showContentLoadM'])&&($_POST['showContentLoadM']!=""))
            {
                $showContentLoadM = mysqli_real_escape_string($link, sanitizePostString('showContentLoadM'));
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
            $styleParametersArrayM['editDeviceLabels'] = $deviceLabels;
			$styleParametersArrayM['calendarM'] = $calendarM;
            $styleParametersArrayM['enableCKEditor'] = $enableCKEditor;
            $styleParametersArrayM['showContentLoadM'] = $showContentLoadM;

            if(isset($_POST['barsColorsM'])&&($_POST['barsColorsM']!=""))
            {
                $temp = json_decode(sanitizeJson($_POST['barsColorsM']));     // SANITIZE QUI ??
                $barsColorsM = [];
                foreach ($temp as $color) 
                {
                    array_push($barsColorsM, $color);
                }
            }

            $styleParametersArrayM['barsColors'] = $barsColorsM;
            $styleParametersArrayM['groupByAttr'] = $groupByAttrM;
            $styleParametersArrayM['sortBarValuesM'] = $sortBarValuesM;
            $styleParametersM = json_encode($styleParametersArrayM);

            if(isset($_POST['parametersM'])&&($_POST['parametersM']!="")) {
                $rowParameters = sanitizeJsonRelaxed($_POST['parametersM']);
                $parametersArray = json_decode($rowParameters, false);
                for($i = (sizeof($parametersArray)) - 1; $i >= 0; $i--) {
                    if (isset($parametersArray[$i]->deleted)) {
                        if ($parametersArray[$i]->deleted == true) {
                            //   array_map('unlink', glob("../img/widgetSelectorImages/" . $name_widget_m . "/q" . $i . "/*"));
                            unset($parametersArray[$i]);
                        }
                    }
                }
                $rowParameters = json_encode(array_values($parametersArray));
                $_POST['parametersM'] = NULL;
            }

        }

        if($type_widget_m == "widgetRadarSeries")
        {
            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('rowsLabelsFontSizeM'));
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColorM'));
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('colsLabelsFontSizeM'));
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColorM'));
            }

            if(isset($_POST['dataLabelsFontSizeM'])&&($_POST['dataLabelsFontSizeM']!=""))
            {
                $dataLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('dataLabelsFontSizeM'));
            }

            if(isset($_POST['dataLabelsFontColorM'])&&($_POST['dataLabelsFontColorM']!=""))
            {
                $dataLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsFontColorM'));
            }

            if(isset($_POST['legendFontSizeM'])&&($_POST['legendFontSizeM']!=""))
            {
                $legendFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('legendFontSizeM'));
            }

            if(isset($_POST['legendFontColorM'])&&($_POST['legendFontColorM']!=""))
            {
                $legendFontColorM = mysqli_real_escape_string($link,sanitizePostString('legendFontColorM'));
            }

            if(isset($_POST['gridLinesWidthM'])&&($_POST['gridLinesWidthM']!=""))
            {
                $gridLinesWidthM = sanitizePostInt('gridLinesWidthM');
            }

            if(isset($_POST['gridLinesColorM'])&&($_POST['gridLinesColorM']!=""))
            {
                $gridLinesColorM = sanitizePostString('gridLinesColorM');
            }

            if(isset($_POST['linesWidthM'])&&($_POST['linesWidthM']!=""))
            {
                $linesWidthM = sanitizePostInt('linesWidthM');
            }

            if(isset($_POST['barsColorsSelectM'])&&($_POST['barsColorsSelectM']!=""))
            {
                $barsColorsSelectM = mysqli_real_escape_string($link, sanitizePostString('barsColorsSelectM'));
            }

            if(isset($_POST['alrThrLinesWidthM'])&&($_POST['alrThrLinesWidthM']!=""))
            {
                $alrThrLinesWidthM = mysqli_real_escape_string($link, sanitizePostInt('alrThrLinesWidthM'));
            }

            if(isset($_POST['dataLabelsM'])&&($_POST['dataLabelsM']!=""))
            {
                $dataLabelsM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsM'));
            }

            if(isset($_POST['dataLabelsRotationM'])&&($_POST['dataLabelsRotationM']!=""))
            {
                $dataLabelsRotationM = mysqli_real_escape_string($link, sanitizePostString('dataLabelsRotationM'));     // CTR !!
            }

            if(isset($_POST['deviceLabelsM_0'])&&($_POST['deviceLabelsM_0']!=""))
            {
                $deviceLabels = [];
                $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_0'));     // New pentest
                array_push($deviceLabels, $label);
                $k = 1;
                while (isset($_POST['deviceLabelsM_'.$k])&&($_POST['deviceLabelsM_'.$k]!="")) {
                    $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_'.$k));     // New pentest
                    array_push($deviceLabels, $label);
                    $k++;
                }
            }

            if(isset($_POST['enableCKEditor'])&&($_POST['enableCKEditor']!=""))
            {
                $enableCKEditor = mysqli_real_escape_string($link, sanitizePostString('enableCKEditor'));
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
            $styleParametersArrayM['editDeviceLabels'] = $deviceLabels;
            $styleParametersArrayM['enableCKEditor'] = $enableCKEditor;

            if(isset($_POST['barsColorsM'])&&($_POST['barsColorsM']!=""))
            {
                $temp = json_decode(sanitizeJson($_POST['barsColorsM']));     // SANITIZE QUI ??
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
            if(isset($_POST['showTableFirstCellM'])&&($_POST['showTableFirstCellM']!=""))
            {
                $showTableFirstCellM = mysqli_real_escape_string($link, sanitizePostString('showTableFirstCellM'));    // New pentest
            }

            if(isset($_POST['tableFirstCellFontSizeM'])&&($_POST['tableFirstCellFontSizeM']!=""))
            {
                $tableFirstCellFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('tableFirstCellFontSizeM'));    // New pentest
            }

            if(isset($_POST['tableFirstCellFontColorM'])&&($_POST['tableFirstCellFontColorM']!=""))
            {
                $tableFirstCellFontColorM = mysqli_real_escape_string($link, sanitizePostString('tableFirstCellFontColorM'));      // New pentest
            }

            if(isset($_POST['rowsLabelsFontSizeM'])&&($_POST['rowsLabelsFontSizeM']!=""))
            {
                $rowsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostInt('rowsLabelsFontSizeM'));        // New pentest
            }

            if(isset($_POST['rowsLabelsFontColorM'])&&($_POST['rowsLabelsFontColorM']!=""))
            {
                $rowsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsFontColorM'));       // New pentest
            }

            if(isset($_POST['colsLabelsFontSizeM'])&&($_POST['colsLabelsFontSizeM']!=""))
            {
                $colsLabelsFontSizeM = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontSizeM'));     // New pentest
            }

            if(isset($_POST['colsLabelsFontColorM'])&&($_POST['colsLabelsFontColorM']!=""))
            {
                $colsLabelsFontColorM = mysqli_real_escape_string($link, sanitizePostString('colsLabelsFontColorM'));       // New pentest
            }

            if(isset($_POST['rowsLabelsBckColorM'])&&($_POST['rowsLabelsBckColorM']!=""))
            {
                $rowsLabelsBckColorM = mysqli_real_escape_string($link, sanitizePostString('rowsLabelsBckColorM'));     // New pentest
            }

            if(isset($_POST['colsLabelsBckColorM'])&&($_POST['colsLabelsBckColorM']!=""))
            {
                $colsLabelsBckColorM = mysqli_real_escape_string($link, sanitizePostString('colsLabelsBckColorM'));     // New pentest
            }

            if(isset($_POST['tableBordersM'])&&($_POST['tableBordersM']!=""))
            {
                $tableBordersM = mysqli_real_escape_string($link, sanitizePostString('tableBordersM'));     // New pentest
            }

            if(isset($_POST['tableBordersColorM'])&&($_POST['tableBordersColorM']!=""))
            {
                $tableBordersColorM = mysqli_real_escape_string($link, sanitizePostString('tableBordersColorM'));       // New pentest
            }

            if(isset($_POST['showColorMapM0'])&&($_POST['showColorMapM0']!=""))
            {
                $colorMapElementsM = '[';
                $colorMapName = mysqli_real_escape_string($link, sanitizePostString('showColorMapM0'));
                $colorMapArray = file_get_contents($heatmapUrl . "getColorMap.php?metricName=" . $colorMapName);
                if ($colorMapArray) {
                    if ($colorMapArray != "[]") {
                        $colorMapElementsM = $colorMapElementsM . '{"id": 0, "name": "' . $colorMapName . '", "colorMap": ' . $colorMapArray . '}';
                    }
                }
                $k = 1;
                while (isset($_POST['showColorMapM'.$k])&&($_POST['showColorMapM'.$k]!="")) {
                    $colorMapName = mysqli_real_escape_string($link, sanitizePostString('showColorMapM'.$k));
                    $colorMapArray = file_get_contents($heatmapUrl . "getColorMap.php?metricName=" . $colorMapName);
                    if ($colorMapArray) {
                        if ($colorMapArray != "[]") {
                            if (strcmp(substr($colorMapElementsM, 0, 7), '[{"id":') == 0) {
                                $colorMapElementsM = $colorMapElementsM . ', {"id": ' . $k . ', "name": "' . $colorMapName . '", "colorMap": ' . $colorMapArray . '}';
                            } else {
                                $colorMapElementsM = $colorMapElementsM . '{"id": ' . $k . ', "name": "' . $colorMapName . '", "colorMap": ' . $colorMapArray . '}';
                            }
                        }
                    }
                    $k++;
                }
                $colorMapElementsM = $colorMapElementsM . "]";
            }

            if(isset($_POST['deviceLabelsM_0'])&&($_POST['deviceLabelsM_0']!=""))
            {
                $deviceLabels = [];
                $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_0'));     // New pentest
                array_push($deviceLabels, $label);
                $k = 1;
                while (isset($_POST['deviceLabelsM_'.$k])&&($_POST['deviceLabelsM_'.$k]!="")) {
                    $label = mysqli_real_escape_string($link, sanitizePostString('deviceLabelsM_'.$k));     // New pentest
                    array_push($deviceLabels, $label);
                    $k++;
                }
            }

            $styleParametersArrayM = array('showTableFirstCell' => $showTableFirstCellM, 'tableFirstCellFontSize' => $tableFirstCellFontSizeM, 'tableFirstCellFontColor' => $tableFirstCellFontColorM, 'rowsLabelsFontSize' => $rowsLabelsFontSizeM, 'rowsLabelsFontColor' => $rowsLabelsFontColorM, 'colsLabelsFontSize' => $colsLabelsFontSizeM, 'colsLabelsFontColor' => $colsLabelsFontColorM, 'rowsLabelsBckColor' => $rowsLabelsBckColorM, 'colsLabelsBckColor' => $colsLabelsBckColorM, 'tableBorders' => $tableBordersM, 'tableBordersColor' => $tableBordersColorM, 'editDeviceLabels' => $deviceLabels, 'colorMaps' => $colorMapElementsM);
            $styleParametersM = json_encode($styleParametersArrayM);
        }

        if(isset($_POST['inputHeaderFontColorWidgetM']) && ($_POST['inputHeaderFontColorWidgetM']!=""))
        {
            $headerFontColorM = mysqli_real_escape_string($link, sanitizePostString('inputHeaderFontColorWidgetM'));    // New pentest COMMON
        }

        if(isset($_POST['inputControlsVisibilityM']) && ($_POST['inputControlsVisibilityM']!=""))
        {
            $controlsVisibility = mysqli_real_escape_string($link, sanitizePostString('inputControlsVisibilityM'));     // New pentest COMMON CTR
        }

        //MANCA ZOOM FACTOR PERCHE' DEV'ESSERE EDITATO SOLO DAI CONTROLLI GRAFICI, NON DAL FORM

        if(isset($_POST['inputControlsPositionM']) && ($_POST['inputControlsPositionM']!=""))
        {
            $controlsPosition = mysqli_real_escape_string($link, sanitizePostString('inputControlsPositionM'));     // New pentest COMMON CTR
        }

        if(isset($_POST['inputShowTitleM']) && ($_POST['inputShowTitleM']!=""))
        {
            $showTitle = mysqli_real_escape_string($link, sanitizePostString('inputShowTitleM'));   // New pentest COMMON
        }

        if(isset($_POST['inputTitleWidgetM']) && ($_POST['inputTitleWidgetM']!=""))
        {
            $title_widget_m = mysqli_real_escape_string($link, sanitizePostString('inputTitleWidgetM'));
            $title_widget_m = htmlentities($title_widget_m, ENT_QUOTES|ENT_HTML5);      // New pentest COMMON
        }

        if(isset($_POST['inputDefaultTabM']) && ($_POST['inputDefaultTabM']!=""))
        {
            $inputDefaultTabM = mysqli_real_escape_string($link, sanitizePostString('inputDefaultTabM'));
        }

        if(isset($_POST['inputFreqWidgetM']) && ($_POST['inputFreqWidgetM']!=""))
        {
            $freq_widget_m = mysqli_real_escape_string($link, sanitizePostInt('inputFreqWidgetM'));     // New pentest   COMMON se si mette stringa sanitizePostInt mette 1 OK ?
        }

        if(isset($_POST['select-frameColor-Widget-m']) && ($_POST['select-frameColor-Widget-m']!=""))
        {
            $color_frame_m = mysqli_real_escape_string($link, sanitizePostString('select-frameColor-Widget-m'));        // New pentest COMMON
        }

        if((isset($_POST['parametersM']))&&($_POST['parametersM'] != ""))
        {
            if(($type_widget_m == 'widgetServerStatus')||($type_widget_m == 'widgetBarContent')||($type_widget_m == 'widgetColumnContent')||($type_widget_m == 'widgetGaugeChart')||($type_widget_m == 'widgetSingleContent')||($type_widget_m == 'widgetSpeedometer')||($type_widget_m == 'widgetTimeTrend')||($type_widget_m == 'widgetTimeTrendCompare'))
            {
               if($_POST['alrThrSelM'] != "no")
               {
              //     $parametersM = $_POST['parametersM'];        // SANITIZE RELAXED  JSON ??
                  $parametersM = sanitizeJsonRelaxed($_POST['parametersM']);
               }
            }
            else
            {
             //   $parametersM = $_POST['parametersM'];        // SANITIZE RELAXED  JSON ??
                $parametersM = sanitizeJsonRelaxed($_POST['parametersM']);
               
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
                   if(($type_widget_m == 'widgetSelector')||($type_widget_m == 'widgetSelectorWeb')||($type_widget_m == "widgetSelectorNew")||($type_widget_m == "widgetSelectorTech"))
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
                                     /*   if ($parametersSelectorArray->queries[$i]->iconPoolImg == '') {
                                            unset($parametersSelectorArray->queries[$i]->iconPoolImg);
                                        }*/
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
                   }else{
                       if($type_widget_m == 'widgetGisWFS'){
                           //
                                      //  $targetList = json_decode($row['parameters'], true);
                          // $widgetId = $row['Id'];
                          // $targetList[$name_widget] = [];
                           //$updatedTargetList = json_encode($targetList);
                           //$updatedTargetList
                           /*MODIFICA AL $updatedTargetList*/
                             $json_parameters0 = json_decode($_POST['parametersM']);
                            $coordinates0 = $json_parameters0->{'latLng'};
                            $var_zoom0 = $json_parameters0->{'zoom'};
                            $lat0 = $coordinates0[1];
                            $lng0 = $coordinates0[0];
                            $lat1 = str_replace('"','',$lat0);
                            $lng1 = str_replace('"','',$lng0);
                            $var_zoom1 = str_replace('"', '', $var_zoom0);
                            $parametersM = '{"latLng":['.$lat1.','.$lng1.'],"zoom":'.$var_zoom1.'}';
                            //$parametersM = $_POST['parametersM'];
                           //
                       }
                       //
                   }
               }
            }

            if ($type_widget_m == "widgetMap") {
                if(isset($_POST['showOrthomapsM'])&&($_POST['showOrthomapsM']!="")) {

                    $showOrthomapsM = mysqli_real_escape_string($link, sanitizePostString('showOrthomapsM'));
                    if ($showOrthomapsM == "yes") {
                        $queryOrthomaps = "SELECT orthomapJson FROM Dashboard.Organizations Orgs INNER JOIN Dashboard.Config_dashboard Dash ON Dash.id = " . mysqli_real_escape_string($link, $id_dashboard2) . " AND Dash.organizations = Orgs.organizationName;";
                        $resOrthomaps = mysqli_query($link, $queryOrthomaps);

                        if($resOrthomaps)
                        {
                            $currRow = mysqli_fetch_assoc($resOrthomaps);
                            if (sizeof($currRow) > 0) {
                                $orthomapJsonM = $currRow['orthomapJson'];
                            }
                        }
                        $infoJsonM = "yes";
                        // $parametersM = json_encode($orthomapJsonM);
                    //   $parametersM = $orthomapJsonM;
                        $orthomapJsonArray = json_decode($orthomapJsonM, true);
                        if(isset($_POST['parametersM']) && ($_POST['parametersM']!="")) {
                            $parametersM = sanitizeJsonRelaxed($_POST['parametersM']);
                            $parametersArray = json_decode($parametersM);
                            if ($parametersArray->dropdownMenu) {
                                // if an orthomap json already exists, update only latLng and zoom
                                $latLngCenterMap = $parametersArray->latLng;
                                $zoomMap = $parametersArray->zoom;
                                $parametersArray->latLng = $latLngCenterMap;
                                $parametersArray->zoom = $zoomMap;
                                $parametersM = json_encode($parametersArray);
                            } else {
                                // if there isn't any orthomap json, create it using the organization template
                                $tempParametersArray = $parametersArray;
                                $latLngCenterMap = $tempParametersArray->latLng;
                                $zoomMap = $tempParametersArray->zoom;
                                $orthomapJsonArray['latLng'] = $latLngCenterMap;
                                $orthomapJsonArray['zoom'] = $zoomMap;
                                $parametersM = json_encode($orthomapJsonArray);
                            }
                        }

                    } else if ($showOrthomapsM == "no") {
                        if(isset($_POST['parametersM']) && ($_POST['parametersM']!="")) {
                            $parametersM = sanitizeJsonRelaxed($_POST['parametersM']);
                            $tempParametersArray = json_decode($parametersM);
                            $latLngCenterMap = $tempParametersArray->latLng;
                            $zoomMap = $tempParametersArray->zoom;
                            $parametersArray = array('latLng' => $latLngCenterMap, 'zoom' => $zoomMap);
                            $parametersM = json_encode($parametersArray);
                            $infoJsonM = "no";
                        }
                    }

                    if(isset($_POST['defaultOrthomapM'])&&($_POST['defaultOrthomapM']!="")) {
                        $styleParametersM =  array('showOrthomaps' => sanitizePostString('showOrthomapsM'), 'defaultOrthomap' => sanitizePostString('defaultOrthomapM'));
                    } else {
                        $styleParametersM =  array('showOrthomaps' => sanitizePostString('showOrthomapsM'));
                    }

                    $styleParametersM = json_encode($styleParametersM);
                }

            }

            if ($type_widget_m == "widget3DMapDeck") {
                // orthomaps section
                $queryOrthomaps = "SELECT orthomapJson FROM Dashboard.Organizations Orgs INNER JOIN Dashboard.Config_dashboard Dash ON Dash.id = " . mysqli_real_escape_string($link, $id_dashboard2) . " AND Dash.organizations = Orgs.organizationName;";
                $resOrthomaps = mysqli_query($link, $queryOrthomaps);
                if($resOrthomaps)
                {
                    $currRow = mysqli_fetch_assoc($resOrthomaps);
                    if (sizeof($currRow) > 0) {
                        $orthomapJsonM = $currRow['orthomapJson'];
                    }
                }
                $infoJsonM = "yes";
                $orthomapJsonArray = json_decode($orthomapJsonM, true);
                if(isset($_POST['parametersM']) && ($_POST['parametersM']!="")) {
                    $parametersM = sanitizeJsonRelaxed($_POST['parametersM']);
                    $parametersArray = json_decode($parametersM);
                    $tempParametersArray = json_decode($parametersM);
                    if ($parametersArray->dropdownMenu) {
                        // if an orthomap json already exists, update only latLng and zoom
                        $latLngCenterMap = $parametersArray->latLng;
                        $zoomMap = (int)$parametersArray->zoom;
                        $pitchMap = $tempParametersArray->pitch;
                        $bearingMap = $tempParametersArray->bearing;
                        $mapType = $tempParametersArray->mapType;

                        $parametersArray->latLng = $latLngCenterMap;
                        $parametersArray->zoom = $zoomMap;
                        $parametersArray->pitch = $pitchMap;
                        $parametersArray->bearing = $bearingMap;
                        $parametersArray->mapType = $mapType;
                        $parametersM = json_encode($parametersArray);
                    } else {
                        // if there isn't any orthomap json, create it using the organization template
                        $tempParametersArray = $parametersArray;
                        $latLngCenterMap = $tempParametersArray->latLng;
                        $zoomMap = $tempParametersArray->zoom;
                        $pitchMap = $tempParametersArray->pitch;
                        $bearingMap = $tempParametersArray->bearing;
                        $mapType = $tempParametersArray->mapType;

                        $orthomapJsonArray['latLng'] = $latLngCenterMap;
                        $orthomapJsonArray['zoom'] = $zoomMap;
                        $orthomapJsonArray['pitch'] = $pitchMap;
                        $orthomapJsonArray['bearing'] = $bearingMap;
                        $orthomapJsonArray['type'] = $mapType;

                        $parametersM = json_encode($orthomapJsonArray);
                    }
                }
                if(isset($_POST['defaultOrthomapM'])&&($_POST['defaultOrthomapM']!="")) {
                    $styleParametersM =  array('showOrthomaps' => 'yes', 'defaultOrthomap' => sanitizePostString('defaultOrthomapM'));
                } else {
                    $styleParametersM =  array('showOrthomaps' => 'yes');
                }

                // terrain section
                $index = 1;
                while (true) {
                    if (isset($_POST['gisTargetTerrainQueryTP'.$index])) {
                        if (!isset($styleParametersM['terrains'])) {
                            $styleParametersM['terrains'] = [];
                        }
                        $styleParametersM['terrains']['TP'.$index]['query'] = sanitizePostString('gisTargetTerrainQueryTP'.$index);
                        $styleParametersM['terrains']['TP'.$index]['elevationDecoder']['rScaler'] = sanitizePostFloat('gisTargetRedEncodingTP'.$index);
                        $styleParametersM['terrains']['TP'.$index]['elevationDecoder']['gScaler'] = sanitizePostFloat('gisTargetGreenEncodingTP'.$index);
                        $styleParametersM['terrains']['TP'.$index]['elevationDecoder']['bScaler'] = sanitizePostFloat('gisTargetBlueEncodingTP'.$index);
                        $styleParametersM['terrains']['TP'.$index]['elevationDecoder']['offset'] = sanitizePostFloat('gisTargetOffsetEncodingTP'.$index);
                        if (isset($_POST['terrainHasBBTP'.$index])&&$_POST['terrainHasBBTP'.$index] == 'on') {
                            $styleParametersM['terrains']['TP'.$index]['bbox']['north'] = sanitizePostFloat('gisTargetNorthBBTP'.$index);
                            $styleParametersM['terrains']['TP'.$index]['bbox']['east'] = sanitizePostFloat('gisTargetEastBBTP'.$index);
                            $styleParametersM['terrains']['TP'.$index]['bbox']['south'] = sanitizePostFloat('gisTargetSouthBBTP'.$index);
                            $styleParametersM['terrains']['TP'.$index]['bbox']['west'] = sanitizePostFloat('gisTargetWestBBTP'.$index);
                        }
                        $index++;
                    } else {
                        break;
                    }
                }

                // building section
                $styleParametersM['buildingType'] = sanitizePostString('buildingTypeM');
                $styleParametersM['buildingColors'] = [];
                $styleParametersM['buildingColors']['Default'] = sanitizePostString('gisTargetBuildingDefaultColorM');
                $styleParametersM['buildingColors']['Cult'] = sanitizePostString('gisTargetBuildingCultColorM');
                $styleParametersM['buildingColors']['Culture'] = sanitizePostString('gisTargetBuildingCultureColorM');
                $styleParametersM['buildingColors']['PublicService'] = sanitizePostString('gisTargetBuildingPublicServiceColorM');
                $styleParametersM['buildingColors']['Shopping'] = sanitizePostString('gisTargetBuildingShoppingColorM');
                $styleParametersM['buildingColors']['Station'] = sanitizePostString('gisTargetBuildingStationColorM');
                $styleParametersM['buildingColors']['University'] = sanitizePostString('gisTargetBuildingUniversityColorM');
                $styleParametersM['buildingColors']['HealthCare'] = sanitizePostString('gisTargetBuildingHealthCareColorM');
                $styleParametersM['buildingColors']['School'] = sanitizePostString('gisTargetBuildingSchoolColorM');
                $styleParametersM['buildingColors']['Bank'] = sanitizePostString('gisTargetBuildingBankColorM');

                // light section
                if(isset($_POST['useLightingM']) && $_POST['useLightingM'] == "yes") {
                    $styleParametersM['useLighting'] = 'yes';
                    $styleParametersM['lightTimestamp'] = sanitizePostString('lightTimestampM');
                    $styleParametersM['directionalLightColor'] = sanitizePostString('directionalLightColorM');
                    $styleParametersM['directionalLightIntensity'] = sanitizePostInt('directionalLightIntensityM');
                    $styleParametersM['ambientLightColor'] = sanitizePostString('ambientLightColorM');
                    $styleParametersM['ambientLightIntensity'] = sanitizePostInt('ambientLightIntensityM');
                } else {
                    $styleParametersM['useLighting'] = 'no';
                }

                $styleParametersM = json_encode($styleParametersM);
                eventLog('Updating style parameters for widget3DMapDeck' . $styleParametersM);
            }

        }

        if(isset($_POST['inputFontSizeM']) && ($_POST['inputFontSizeM']!=""))
        {
            $fontSizeM = mysqli_real_escape_string($link, sanitizePostInt('inputFontSizeM'));       // New pentest COMMON
        }
        else
        {
            $fontSizeM = NULL;  
        }

        if(isset($_POST['inputFontColorM']) && ($_POST['inputFontColorM']!=""))
        {
            $fontColorM = mysqli_real_escape_string($link, sanitizePostString('inputFontColorM'));  // New pentest COMMON
        }
        else
        {
            $fontColorM = NULL;  
        }

        //Gestione parametri per widget di stato del singolo processo
        if($type_widget_m == 'widgetProcess')
        {

            $schedulerNameM = sanitizePostString('schedulerNameM');
            $jobAreaM = sanitizePostString('jobAreaM');
            $jobGroupM = sanitizePostString('jobGroupM');
            $jobNameM = sanitizePostString('jobNameM');
            $parametersArrayM = array('schedulerName' => $schedulerNameM, 'jobArea' => $jobAreaM, 'jobGroup' => $jobGroupM, 'jobName' => $jobNameM);
            $parametersM = json_encode($parametersArrayM);
        }

        if(isset($_POST['select-IntTemp-Widget-m']) && ($_POST['select-IntTemp-Widget-m'] != "") &&($type_widget_m != 'widgetProtezioneCivile' && $type_widget_m != 'widgetProtezioneCivileFirenze'))
        {
            $int_temp_widget_m = mysqli_real_escape_string($link, $_POST['select-IntTemp-Widget-m']);
        }
        else if (($type_widget_m == 'widgetCurvedLineSeries') || ($type_widget_m == 'widgetCalendar') || ($type_widget_m == 'widgetDataCube'))
        {
            $temporalRangeQuery = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w = '" . escapeForSQL($name_widget_m, $link) . "' AND id_dashboard = '" . escapeForSQL($id_dashboard2, $link) . "'";
            $temporalRangeRs = mysqli_query($link, $temporalRangeQuery);

            if($temporalRangeRs)
            {
                $currRow = mysqli_fetch_assoc($temporalRangeRs);
                if (sizeof($currRow) > 0) {
                    $int_temp_widget_m = $currRow['temporal_range_w'];
                }
            }
        }
        else
        {
            $int_temp_widget_m = NULL;
        }

        
        if(isset($_POST['editWidgetComparePeriod']) && ($_POST['editWidgetComparePeriod'] != "") &&($type_widget_m != 'widgetProtezioneCivile' && $type_widget_m != 'widgetProtezioneCivileFirenze'))
        {
            $int_comp_widget_m = mysqli_real_escape_string($link, $_POST['editWidgetComparePeriod']);
        }
        else if ($type_widget_m == 'widgetCurvedLineSeries')
        {
            $temporalRangeQuery = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w = '" . escapeForSQL($name_widget_m, $link) . "' AND id_dashboard = '" . escapeForSQL($id_dashboard2, $link) . "'";
            $temporalRangeRs = mysqli_query($link, $temporalRangeQuery);

            if($temporalRangeRs)
            {
                $currRow = mysqli_fetch_assoc($temporalRangeRs);
                if (sizeof($currRow) > 0) {
                    $int_comp_widget_m = $currRow['temporal_compare_w'];
                }
            }
        }
        else
        {
            $int_comp_widget_m = NULL;
        }
        
        
        if (isset($_POST['inputComuneWidgetM']) && ($_POST['inputComuneWidgetM'] != "") &&($type_widget_m != 'widgetProtezioneCivile' && $type_widget_m != 'widgetProtezioneCivileFirenze'))
        {
            $comune_widget_m = mysqli_real_escape_string($link, sanitizePostString('inputComuneWidgetM'));
        }
        else 
        {
            $comune_widget_m = NULL;
        }
        
        if($_REQUEST['widgetCategoryHiddenM'] != "actuator")
        {
            if(isset($_POST['urlWidgetM'])&&($_POST['urlWidgetM'] != ""))
            {
                if(preg_match('/^ *$/', $_POST['urlWidgetM'])) 
                {
                   $url_m = "none";
                }
                else
                {
                   if ($type_widget_m == "widgetExternalContent" && strpos($_POST['urlWidgetM'], 'selectorWebTarget') !== false) {
                       $url_m = mysqli_real_escape_string($link, sanitizeJson($_POST['urlWidgetM']));
                   } else {
                       $url_m = mysqli_real_escape_string($link, sanitizePostString('urlWidgetM'));     // New pentest COMMON
                   }
                }                                    
            }
            else
            {
                $url_m = "none";
            }
        }
        else
        {
            $url_m = "none";
        }
        
        if((($type_widget_m == "widgetExternalContent")&&($type_widget_m == "widgetGisWFS"))&&($_REQUEST['widgetModeM'] == "selectorWebTarget"))
        {
            $url_m = json_encode(array('homepage' => $url_m, 'widgetMode' => 'selectorWebTarget'));
        }
        
        $inputUdmWidget = NULL;
        if(isset($_POST['inputUdmWidgetM']) && $_POST['inputUdmWidgetM'] != "") 
        {
            $inputUdmWidget = mysqli_real_escape_string($link, sanitizePostString('inputUdmWidgetM'));      // New pentest
            $inputUdmWidget = htmlentities($_POST['inputUdmWidgetM'], ENT_QUOTES|ENT_HTML5);
        }
        
        $inputUdmPosition = NULL;
        if(isset($_POST['inputUdmPositionM']) && ($_POST['inputUdmPositionM'] != "")) 
        {
            $inputUdmPosition = mysqli_real_escape_string($link, sanitizePostString('inputUdmPositionM'));
        }
        
        $serviceUri = NULL;
        if(isset($_POST['serviceUriM']) && ($_POST['serviceUriM'] != '') && (!empty($_POST['serviceUriM']))) 
        {
            $serviceUri = mysqli_real_escape_string($link, sanitizePostString('serviceUriM'));      // New pentest CTR
        }

        $viewMode = NULL;
        if(isset($_POST['editWidgetFirstAidMode']) && ($_POST['editWidgetFirstAidMode'] != '') && (!empty($_POST['editWidgetFirstAidMode']))) 
        {
            $viewMode = mysqli_real_escape_string($link, sanitizePostString('editWidgetFirstAidMode'));
        }
        else
        {
            if(isset($_POST['widgetEventsModeM'])) 
            {
                $viewMode = mysqli_real_escape_string($link,sanitizePostString('widgetEventsModeM'));
            } else {
                $viewMode = mysqli_real_escape_string($link, 'additive');
            }
        }

        $hospitalList = NULL;
        if(isset($_POST['hospitalListM']) && ($_POST['hospitalListM'] != '') && (!empty($_POST['hospitalListM']))) 
        {
            $hospitalList = escapeForSQL($_POST['hospitalListM'], $link);
        }
        
        $lastSeries = NULL;
        
         if(($type_widget_m == 'widgetTrafficEvents') || ($type_widget_m == 'widgetTrafficEventsNew'))
         {
            //31/08/2017 - Patch temporanea in attesa di avere tempo di mettere i controlli sul form
         //   $styleParametersM = '{"choosenOption":"events", "timeUdm":"MINUTE", "time":90, "events":50, "defaultCategory":"' . $_REQUEST['editWidgetDefaultCategory'] . '"}';
             if (sanitizePostString('editWidgetDefaultCategory') == null) {       // New pentest
                 $styleParametersM = '{"choosenOption":"events", "timeUdm":"MINUTE", "time":90, "events":50, "defaultCategory":"' . sanitizeGetString('editWidgetDefaultCategory') . '"}';
             } else {
                 $styleParametersM = '{"choosenOption":"events", "timeUdm":"MINUTE", "time":90, "events":50, "defaultCategory":"' . sanitizePostString('editWidgetDefaultCategory') . '"}';
             }
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
            $styleParametersArray["openNewTab"] = $_REQUEST['editWidgetOpenNewTab'];
            $styleParametersArray["shadow"] = $_REQUEST['editWidgetShadow'];
            $styleParametersM = json_encode($styleParametersArray); 
         }

         // messi qui html element per i vari widget per link in new tab nelle opzioni
        if($type_widget_m == "widgetSingleContent" || $type_widget_m == "widgetTimeTrendCompare" || $type_widget_m == "widgetFirstAid")
        {
         //   $styleParametersArray["openNewTab"] = $_REQUEST['editWidgetOpenNewTab'];
            if (sanitizePostString('editWidgetOpenNewTab') == null) {       // New pentest
                $styleParametersArray["openNewTab"] = mysqli_real_escape_string($link, sanitizeGetString('editWidgetOpenNewTab'));
            } else {
                $styleParametersArray["openNewTab"] = mysqli_real_escape_string($link, sanitizePostString('editWidgetOpenNewTab'));
            }
            if ($type_widget_m == "widgetSingleContent" || $type_widget_m == "widgetTimeTrendCompare") {
                if(isset($_POST['showContentLoadM'])&&($_POST['showContentLoadM']!="")) {
                    $showContentLoadM = mysqli_real_escape_string($link, sanitizePostString('showContentLoadM'));
                    $styleParametersArray['showContentLoadM'] = $showContentLoadM;
                }
            }
            $styleParametersM = json_encode($styleParametersArray);
        }

        if($type_widget_m == "widgetTimeTrend")
        {
            //   $styleParametersArray["openNewTab"] = $_REQUEST['editWidgetOpenNewTab'];
            if (sanitizePostString('editWidgetOpenNewTab') == null) {       // New pentest
                $styleParametersArray["openNewTab"] = mysqli_real_escape_string($link, sanitizeGetString('editWidgetOpenNewTab'));
            } else {
                $styleParametersArray["openNewTab"] = mysqli_real_escape_string($link, sanitizePostString('editWidgetOpenNewTab'));
            }
            if (sanitizePostString('viewUdm') == null) {       // New pentest
                $styleParametersArray["viewUdm"] = mysqli_real_escape_string($link, sanitizeGetString('viewUdm'));
            } else {
                $styleParametersArray["viewUdm"] = mysqli_real_escape_string($link, sanitizePostString('viewUdm'));
            }
            if (sanitizePostInt('xOffsetUdm') == null) {       // New pentest
                $styleParametersArray["xOffsetUdm"] = mysqli_real_escape_string($link, sanitizeGetInt('xOffsetUdm'));
            } else {
                $styleParametersArray["xOffsetUdm"] = mysqli_real_escape_string($link, sanitizePostInt('xOffsetUdm'));
            }
            if (sanitizePostString('exportM') == null) {       // New pentest
                $styleParametersArray["exportM"] = mysqli_real_escape_string($link, sanitizeGetString('exportM'));
            } else {
                $styleParametersArray["exportM"] = mysqli_real_escape_string($link, sanitizePostString('exportM'));
            }
            $styleParametersM = json_encode($styleParametersArray);
        }

        if(isset($_REQUEST['enableFullscreenTabM']))
        {
        //   $enableFullscreenTabM = escapeForSQL($_REQUEST['enableFullscreenTabM'], $link);
            if (sanitizePostString('enableFullscreenTabM') == null) {       // New pentest
                $enableFullscreenTabM = mysqli_real_escape_string($link, sanitizeGetString('enableFullscreenTabM'));
            } else {
                $enableFullscreenTabM = mysqli_real_escape_string($link, sanitizePostString('enableFullscreenTabM'));
            }
        }

        if(isset($_REQUEST['enableFullscreenModalM']))
        {
        //   $enableFullscreenModalM = escapeForSQL($_REQUEST['enableFullscreenModalM'], $link);
            if (sanitizePostString('enableFullscreenModalM') == null) {       // New pentest
                $enableFullscreenModalM = mysqli_real_escape_string($link, sanitizeGetString('enableFullscreenModalM'));
            } else {
                $enableFullscreenModalM = mysqli_real_escape_string($link, sanitizePostString('enableFullscreenModalM'));
            }
        }
        
        /*Verifichiamo se è già stato registrato o no sul notificatore:
          1) Se non registrato --> lo registriamo
          2) Se già registrato --> ne aggiorniamo validità e titolo (nome generatore sul notificatore)
         */
        
        $notificatorQuery = "SELECT notificatorRegistered, notificatorEnabled, title_w FROM Dashboard.Config_widget_dashboard WHERE name_w = '" . escapeForSQL($name_widget_m, $link) . "' AND id_dashboard = '" . escapeForSQL($id_dashboard2, $link) . "'";
        $notificatorRs = mysqli_query($link, $notificatorQuery);
        
        if($notificatorRs)
        {
           $notificatorRow = mysqli_fetch_assoc($notificatorRs);
           $notificatorRegistered = $notificatorRow['notificatorRegistered'];
           $notificatorRegisteredOld = $notificatorRow['notificatorRegistered'];
           $notificatorEnabled = $notificatorRow['notificatorEnabled'];
           $notificatorEnabledOld = $notificatorRow['notificatorEnabled'];
           $genOriginalName = html_entity_decode($notificatorRow['title_w'], ENT_HTML5);
           
           if($notificatorRegistered == "no")
           {
               if(isset($_REQUEST['editWidgetRegisterGen']))
               {
               //   $notificatorRegisteredNew = escapeForSQL($_REQUEST['editWidgetRegisterGen'], $link);
               //   $notificatorEnabledNew = escapeForSQL($_REQUEST['editWidgetRegisterGen'], $link);
                   if (sanitizePostString('editWidgetRegisterGen') == null) {       // New pentest COMMON CTR
                       $notificatorRegisteredNew = mysqli_real_escape_string($link, sanitizeGetString('editWidgetRegisterGen'));
                   } else {
                       $notificatorRegisteredNew = mysqli_real_escape_string($link, sanitizePostString('editWidgetRegisterGen'));
                   }
                   $notificatorEnabledNew = $notificatorRegisteredNew;
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
               //   $notificatorEnabledNew = escapeForSQL($_REQUEST['editWidgetRegisterGen'], $link);
                   if (sanitizePostString('editWidgetRegisterGen') == null) {       // New pentest COMMON CTR
                       $notificatorEnabledNew = mysqli_real_escape_string($link, sanitizeGetString('editWidgetRegisterGen'));
                   } else {
                       $notificatorEnabledNew = mysqli_real_escape_string($link, sanitizePostString('editWidgetRegisterGen'));
                   }
               }
               else
               {
                  $notificatorEnabledNew = 'no';
               }
           }

           if($_REQUEST['widgetCategoryHiddenM'] == "viewer")
           {
               $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET type_w = " . returnManagedStringForDb($type_widget_m) . ", size_columns = " . returnManagedNumberForDb($col_m) . ", size_rows = " . returnManagedNumberForDb($row_m)
                       . ", title_w = " . returnManagedStringForDb(html_entity_decode(escapeForSQL($title_widget_m, $link), ENT_HTML5)) . ", color_w = " . returnManagedStringForDb($color_widget_m) . ", frequency_w = " . returnManagedNumberForDb($freq_widget_m) . ", temporal_range_w = " . returnManagedStringForDb($int_temp_widget_m)
               //        . ", municipality_w = " . returnManagedStringForDb($comune_widget_m) . ", link_w = " . returnManagedStringForDb($url_m) . ", parameters = " . returnManagedStringForDb($parametersM)
                       . ", temporal_compare_w = " . returnManagedStringForDb($int_comp_widget_m)
                       . ", TypicalTimeTrend = " . returnManagedStringForDb($TypicalTimeTrendM)
                       . ", TrendType = " . returnManagedStringForDb($TrendTypeM)
                       . ", ReferenceDate = " . returnManagedStringForDb($ReferenceDateM) 
                       . ", TTTDate = " . returnManagedStringForDb($TTTDate) 
                       . ", dayhourview = " . returnManagedStringForDb($dayHourView) 
                       . ", computationType = " . returnManagedStringForDb($computationType) 
                       . ", municipality_w = " . returnManagedStringForDb($comune_widget_m) . ", link_w = " . returnManagedStringForDb($url_m) . ($type_widget_m == "widgetTracker" ? "parameters = parameters" : ", parameters = " . returnManagedStringForDb(escapeForSQL($parametersM, $link)))
                       . ", frame_color_w = " . returnManagedStringForDb($color_frame_m) . ", udm = " . returnManagedStringForDb($inputUdmWidget) . ", udmPos = " . returnManagedStringForDb($inputUdmPosition) . ", fontSize = " . returnManagedNumberForDb($fontSizeM) . ", infoJson = " . returnManagedStringForDb($infoJsonM)
                       . ", fontColor = " . returnManagedStringForDb($fontColorM) . ", controlsPosition = " . returnManagedStringForDb($controlsPosition) . ", showTitle = " . returnManagedStringForDb($showTitle) . ", controlsVisibility = " . returnManagedStringForDb($controlsVisibility)
                       . ", defaultTab = " . returnManagedNumberForDb($inputDefaultTabM) . ", zoomControlsColor = " . returnManagedStringForDb($zoomControlsColorM) . ", headerFontColor = " . returnManagedStringForDb($headerFontColorM) . ", styleParameters = " . returnManagedStringForDb(escapeForSQL($styleParametersM, $link)) . ", rowParameters = COALESCE(" . returnManagedStringForDb(escapeForSQL($rowParameters, $link)) . ", rowParameters)"
                       . ", serviceUri = " . returnManagedStringForDb($serviceUri) . ($type_widget_m == "widgetMap" ? "" : ", viewMode = " . returnManagedStringForDb($viewMode)) . ", hospitalList = " . returnManagedStringForDb($hospitalList) . ", lastSeries = " . returnManagedStringForDb($lastSeries) . ", notificatorRegistered = " . returnManagedStringForDb($notificatorRegisteredNew) . ", notificatorEnabled = " . returnManagedStringForDb($notificatorEnabledNew) . ", enableFullscreenTab = " . returnManagedStringForDb($enableFullscreenTabM) . ", enableFullscreenModal = " . returnManagedStringForDb($enableFullscreenModalM) . ", fontFamily = ". returnManagedStringForDb($fontFamily) . ", lastEditor = " . returnManagedStringForDb(escapeForSQL($lastEditor, $link)) . ", lastEditDate = " . returnManagedStringForDb($lastEditDate) . " WHERE Id = '$widgetIdM' AND id_dashboard = '" . escapeForSQL($id_dashboard2, $link) . "'";
           }
           else
           {
               $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET type_w = " . returnManagedStringForDb($type_widget_m) . ", size_columns = " . returnManagedNumberForDb($col_m) . ", size_rows = " . returnManagedNumberForDb($row_m)
                       . ", title_w = " . returnManagedStringForDb(html_entity_decode(escapeForSQL($title_widget_m, $link), ENT_HTML5)) . ", color_w = " . returnManagedStringForDb($color_widget_m) . ", frequency_w = " . returnManagedNumberForDb($freq_widget_m) . ", temporal_range_w = " . returnManagedStringForDb($int_temp_widget_m)
               //        . ", municipality_w = " . returnManagedStringForDb($comune_widget_m) . ", link_w = " . returnManagedStringForDb($url_m) . ", parameters = " . returnManagedStringForDb($parametersM) 
                       . ", temporal_compare_w = " . returnManagedStringForDb($int_comp_widget_m)
                       . ", TypicalTimeTrend = " . returnManagedStringForDb($TypicalTimeTrendM)
                       . ", TrendType = " . returnManagedStringForDb($TrendTypeM)
                       . ", ReferenceDate = " . returnManagedStringForDb($ReferenceDateM)
                       . ", TTTDate = " . returnManagedStringForDb($TTTDate)
                       . ", dayhourview = " . returnManagedStringForDb($dayHourView)
                       . ", computationType = " . returnManagedStringForDb($computationType) 
                       . ", municipality_w = " . returnManagedStringForDb($comune_widget_m) . ", link_w = " . returnManagedStringForDb($url_m) . ($type_widget_m == "widgetTracker" ? "parameters = parameters" : ", parameters = " . returnManagedStringForDb(escapeForSQL($parametersM, $link)))
                       . ", frame_color_w = " . returnManagedStringForDb($color_frame_m) . ", udm = " . returnManagedStringForDb($inputUdmWidget) . ", udmPos = " . returnManagedStringForDb($inputUdmPosition) . ", fontSize = " . returnManagedNumberForDb($fontSizeM)
                       . ", fontColor = " . returnManagedStringForDb($fontColorM) . ", controlsPosition = " . returnManagedStringForDb($controlsPosition) . ", showTitle = " . returnManagedStringForDb($showTitle) . ", controlsVisibility = " . returnManagedStringForDb($controlsVisibility)
                       . ", defaultTab = " . returnManagedNumberForDb($inputDefaultTabM) . ", zoomControlsColor = " . returnManagedStringForDb($zoomControlsColorM) . ", headerFontColor = " . returnManagedStringForDb($headerFontColorM) . ", styleParameters = " . returnManagedStringForDb(escapeForSQL($styleParametersM, $link)) . ", rowParameters = COALESCE(" . returnManagedStringForDb(escapeForSQL($rowParameters, $link)) . ", rowParameters)"
                       . ", serviceUri = " . returnManagedStringForDb($serviceUri) . ($type_widget_m == "widgetMap" ? "" : ", viewMode = " . returnManagedStringForDb($viewMode)) . ", hospitalList = " . returnManagedStringForDb($hospitalList) . ", lastSeries = " . returnManagedStringForDb($lastSeries) . ", notificatorRegistered = " . returnManagedStringForDb($notificatorRegisteredNew)
                       . ", notificatorEnabled = " . returnManagedStringForDb($notificatorEnabledNew) . ", enableFullscreenTab = " . returnManagedStringForDb($enableFullscreenTabM) . ", enableFullscreenModal = " . returnManagedStringForDb($enableFullscreenModalM) . ", fontFamily = ". returnManagedStringForDb($fontFamily)
                       . ", lastEditor = " . returnManagedStringForDb(escapeForSQL($lastEditor, $link)) . ", lastEditDate = " . returnManagedStringForDb($lastEditDate)
                       . " WHERE Id = '$widgetIdM' AND id_dashboard = '" . escapeForSQL($id_dashboard2, $link) . "'";
           }
           
           $result7 = mysqli_query($link, $upsqDbtb);
           
            if($result7) 
            {
                    try 
                    {
                        $lastEditDateQuery = "UPDATE Dashboard.Config_dashboard SET last_edit_date = CURRENT_TIMESTAMP WHERE Id = '" . escapeForSQL($id_dashboard2, $link) . "'";
                        $lastEditDateResult = mysqli_query($link, $lastEditDateQuery);
                    } 
                    catch (Exception $ex) 
                    {
                        //Per ora nessuna gestione specifica
                    }
                    
                    mysqli_close($link);
                    
                    header("location: dashboard_configdash.php?dashboardId=" . $id_dashboard2 . "&dashboardAuthorName=" . urlencode($dashboardAuthor) . "&dashboardEditorName=" . urlencode($lastEditor) . "&dashboardTitle=" . urlencode($dashboardName2));
                    
                    //1) Se non registrato e viene richiesto di abilitarlo da GUI --> lo registriamo (con registrazione dei tipi di evento, come in add);
                    if($notificatorRegistered == 'no')
                    {
                       if(isset($_REQUEST['editWidgetRegisterGen']))
                       {
                          if($notificatorRegisteredNew == 'yes')
                          {
                            $url = $notificatorUrl;
                            $genOriginalName = html_entity_decode(preg_replace('/\s+/', '+', $title_widget_m), ENT_HTML5);
                            $genOriginalType = preg_replace('/\s+/', '+', $_REQUEST['metricWidgetM']);
                            $containerName = preg_replace('/\s+/', '+', $dashboardName2);
                            $appUsr = preg_replace('/\s+/', '+', $lastEditor); 
                            
                            if(isset($_REQUEST['dashboardIdToEdit'])&&isset($_REQUEST['currentDashboardTitle'])&&isset($_REQUEST['dashboardUser']))
                            {
                                $containerUrl = $appUrl . "/view/indexNR.php?iddasboard=" . base64_encode($id_dashboard2); 
                            }
                            else if(isset($_REQUEST['dashboardIdUnderEdit'])&&isset($_REQUEST['dashboardUser']))
                            {
                                $containerUrl = $appUrl . "/view/index.php?iddasboard=" . base64_encode($id_dashboard2); 
                            }
                            
                           // $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventGenerator&appName=' . $notificatorAppName . '&appUsr=' . urlencode($appUsr) . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName) . '&url=' . urlencode($containerUrl);
                            $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventGenerator&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&url=' . $containerUrl;
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

                                           // $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName) . '&eventType=' . $eventName . '&thrCnt=1';
                                           $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&eventType=' . $eventName . '&thrCnt=1';
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
                         $genNewName = html_entity_decode(preg_replace('/\s+/', '+', $title_widget_m), ENT_HTML5);
                   //      $genOriginalType = preg_replace('/\s+/', '+', $_REQUEST['metricWidgetM']);
                         $genOriginalType = preg_replace('/\s+/', '+', sanitizePostString('metricWidgetM'));
                   //      $alrThrSelM = preg_replace('/\s+/', '+', $_REQUEST['alrThrSelM']);
                         $alrThrSelM = preg_replace('/\s+/', '+', sanitizePostString('alrThrSelM'));
                         $containerName = preg_replace('/\s+/', '+', $dashboardName2);
                         $appUsr = preg_replace('/\s+/', '+', $lastEditor); 

                         //$setEventsValidityTrue = "false";//Se era già registrato non riabilitiamo tutti i suoi eventi, sennò riabilitiamo anche quelli vecchi, per entrambi i casi di provenienza

                         if($notificatorEnabledNew == 'yes')
                         {
                            $validity = 1;
                         }
                         else
                         {
                            $validity = 0;
                         }

                         if(isset($_REQUEST['dashboardIdToEdit'])&&isset($_REQUEST['currentDashboardTitle'])&&isset($_REQUEST['dashboardUser']))
                         {
                            $containerUrl = $appUrl . "/view/indexNR.php?iddasboard=" . base64_encode($id_dashboard2); 
                         }
                         else if(isset($_REQUEST['dashboardIdUnderEdit'])&&isset($_REQUEST['dashboardUser']))
                         {
                            $containerUrl = $appUrl . "/view/index.php?iddasboard=" . base64_encode($id_dashboard2); 
                         }
                         
                         //$data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=setGeneratorValidity&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $genOriginalName . '&generatorNewName=' . $genNewName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . "&validity=" . $validity . "&setEventsValidityTrue=" . $setEventsValidityTrue;
                         $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=setGeneratorValidity&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $genOriginalName . '&generatorNewName=' . $genNewName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&validity=' . $validity. '&url=' . $containerUrl;
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
                                     //   $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName) . '&eventType=' . $newEventName . '&thrCnt=1';
                                        $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&eventType=' . $newEventName . '&thrCnt=1';
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
                                         //  $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=updateEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName) . '&oldEventType=' . $oldEventName . '&newEventType=' . $newEventName;
                                           $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=updateEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&oldEventType=' . $oldEventName . '&newEventType=' . $newEventName;
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
                                      //  $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName) . '&eventType=' . $eventName;
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
                                //  $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteAllGeneratorEventTypes&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName);
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

                                      //  $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName) . '&eventType=' . $eventName . 'thrCnt=1';
                                        $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=registerEventType&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . $containerName . '&eventType=' . $eventName . 'thrCnt=1';
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
                            //   $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=disableAllGeneratorEventTypes&appName=' . $notificatorAppName . '&generatorOriginalName=' . $genOriginalName . '&generatorOriginalType=' . $genOriginalType . '&containerName=' . urlencode($containerName);
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
                echo 'alert("Error: repeat update widget: schiantata result7");';
                echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard2 . '&dashboardAuthorName=' . urlencode($dashboardAuthor) . '&dashboardEditorName=' . urlencode($lastEditor) . '&dashboardTitle=' . urlencode($dashboardName2);
                echo '</script>';
            }
         }
         else 
         {
            mysqli_close($link);
            echo '<script type="text/javascript">';
            echo 'alert("Error: repeat update widget");';
            echo 'window.location.href = dashboard_configdash.php?dashboardId=' . $id_dashboard2 . '&dashboardAuthorName=' . urlencode($dashboardAuthor) . '&dashboardEditorName=' . urlencode($lastEditor) . '&dashboardTitle=' . urlencode($dashboardName2);
            echo '</script>';
         }
    } 
    else if(isset($_REQUEST['addMetricType'])) 
    {
        session_start();
        checkSession('ToolAdmin');
        
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
          
            if($_POST['sameDataAlarmCount'] != "Not active")
            {
                $sameDataAlarmCount = mysqli_real_escape_string($link, $_POST['sameDataAlarmCount']);
            }
            else
            {
                $sameDataAlarmCount = null;
            }
            
            $hasNegativeValues = mysqli_real_escape_string($link, $_POST['hasNegativeValues']);
            if($_POST['oldDataEvalTime'] != "Not active")
            {
                $oldDataEvalTime = mysqli_real_escape_string($link, $_POST['oldDataEvalTime']);
            }
            else
            {
                $oldDataEvalTime = null;
            }
            
            $process = mysqli_real_escape_string($link, $_POST['process']);
            
            if($process == "HttpProcess")
            {
                $query1 = "{" .
                    "\"headers\" : {" .
                            "\"Accept\" : \"text/plain\"" .
                    "}," .
                    "\"method\" : \"" . mysqli_real_escape_string($link, $_POST['serverTestHttpMethod']) . "\"," .    
                    "\"payload\" : {	}," .
                    "\"token\" : \"" . mysqli_real_escape_string($link, $_POST['serverTestToken']) . "\"," .
                    "\"url\" : \"" . mysqli_real_escape_string($link, $_POST['serverTestUrl']) . "\"" .
                "}";
                    
                $dataSource = "none";
                $dataArea = null;
                $dataSourceDescription = null;
                
                /*$statusMetricName = $metricName;
                
                $re1 = '((?:[a-z][a-z]+))';//Stringa qualsiasi
                $re2 = '(_)';//Underscore
                $re3 = '(status)';//Stringa 'status'

                if(!preg_match_all("/".$re1.$re2.$re3."/is", $statusMetricName))
                {
                    $statusMetricName = $statusMetricName . "_status";
                }*/
                
                $statusMetricName = preg_replace('/_status/', "", $metricName);
                $statusMetricName = $statusMetricName . "_status";
                $responseTimeMetricName = preg_replace('/_status/', "_responseTime", $statusMetricName);
                
                $q = "INSERT INTO Dashboard.Descriptions(IdMetric, description, status, query, query2, queryType, metricType, frequency, processType, area, source, description_short, dataSource, storingData, municipalityOption, timeRangeOption, sameDataAlarmCount, oldDataEvalTime, hasNegativeValues, process, boundToMetric) 
                      VALUES('$statusMetricName', '$fullDescription', 'Attivo', " . returnManagedStringForDb($query1) . ", " . returnManagedStringForDb($query2) . ", '$dataSourceType', '$resultType', '$updateFrequency', '$processType', " . returnManagedStringForDb($dataArea) . ", " . returnManagedStringForDb($dataSourceDescription) . ", '$shortDescription', 'none', 1, 0, 0, " . returnManagedNumberForDb($sameDataAlarmCount) . ", " . returnManagedNumberForDb($oldDataEvalTime) . ", 0, " . returnManagedStringForDb($process) . ", NULL), 
                      ('$responseTimeMetricName', 'Response time of " . $_POST['serverTestUrl'] . "', 'Attivo', " . returnManagedStringForDb($query1) . ", " . returnManagedStringForDb($query2) . ", '$dataSourceType', 'Intero', '$updateFrequency', 'responseTime', " . returnManagedStringForDb($dataArea) . ", " . returnManagedStringForDb($dataSourceDescription) . ", 'Response time of " . $_POST['serverTestUrl'] . "', 'none', 1, 0, 0, " . returnManagedNumberForDb($sameDataAlarmCount) . ", " . returnManagedNumberForDb($oldDataEvalTime) . ", 0, " . returnManagedStringForDb($process) . ", '$statusMetricName')";    
                
            }
            else
            {
                $q = "INSERT INTO Dashboard.Descriptions(IdMetric, description, status, query, query2, queryType, metricType, frequency, processType, area, source, description_short, dataSource, storingData, municipalityOption, timeRangeOption, sameDataAlarmCount, oldDataEvalTime, hasNegativeValues, process) 
                      VALUES('$metricName', '$fullDescription', 'Attivo', " . returnManagedStringForDb($query1) . ", " . returnManagedStringForDb($query2) . ", '$dataSourceType', '$resultType', '$updateFrequency', '$processType', " . returnManagedStringForDb($dataArea) . ", " . returnManagedStringForDb($dataSourceDescription) . ", '$shortDescription', '$dataSource', '$storingData', '$cityContext', '$timeRange', " . returnManagedNumberForDb($sameDataAlarmCount) . ", " . returnManagedNumberForDb($oldDataEvalTime) . ", '$hasNegativeValues', " . returnManagedStringForDb($process) . ")";                    
            }
            
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
        checkSession('ToolAdmin');
        
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
            
            if($_POST['sameDataAlarmCountM'] != "Not active")
            {
                $sameDataAlarmCount = mysqli_real_escape_string($link, $_POST['sameDataAlarmCountM']);
            }
            else
            {
                $sameDataAlarmCount = null;
            }
            
            $hasNegativeValues = mysqli_real_escape_string($link, $_POST['hasNegativeValuesM']);
            
            if($_POST['oldDataEvalTimeM'] != "Not active")
            {
                $oldDataEvalTime = mysqli_real_escape_string($link, $_POST['oldDataEvalTimeM']);
            }
            else
            {
                $oldDataEvalTime = null;
            }
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
            
            if($process == "HttpProcess")
            {
                $query1 = "{" .
                    "\"headers\" : {" .
                            "\"Accept\" : \"text/plain\"" .
                    "}," .
                    "\"method\" : \"" . mysqli_real_escape_string($link, $_POST['serverTestHttpMethodM']) . "\"," .    
                    "\"payload\" : {	}," .
                    "\"token\" : \"" . mysqli_real_escape_string($link, $_POST['serverTestTokenM']) . "\"," .
                    "\"url\" : \"" . mysqli_real_escape_string($link, $_POST['serverTestUrlM']) . "\"" .
                "}";
                    
                $dataSource = "none";
                $dataArea = null;
                $dataSourceDescription = null;
                
                /*$statusMetricName = $metricName;
                
                $re1 = '((?:[a-z][a-z]+))';//Stringa qualsiasi
                $re2 = '(_)';//Underscore
                $re3 = '(status)';//Stringa 'status'

                if(!preg_match_all("/".$re1.$re2.$re3."/is", $statusMetricName))
                {
                    $statusMetricName = $statusMetricName . "_status";
                }*/
                
                $statusMetricName = preg_replace('/_status/', "", $metricName);
                $statusMetricName = $statusMetricName . "_status";
                $responseTimeMetricName = preg_replace('/_status/', "_responseTime", $statusMetricName);
                
                mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
                
                //$file = fopen("C:\dashboardLog.txt", "w");
                $q1 = "UPDATE Dashboard.Descriptions SET IdMetric = '$responseTimeMetricName', description = 'Response time of " . escapeForSQL($_POST['serverTestUrlM'], $link) . "', query = " . returnManagedStringForDb($query1) . ", frequency = '$updateFrequency', description_short = 'Response time of " . escapeForSQL($_POST['serverTestUrlM'], $link) . "', sameDataAlarmCount = " . returnManagedNumberForDb($sameDataAlarmCount) . ", oldDataEvalTime = " . returnManagedNumberForDb($oldDataEvalTime) . ", boundToMetric = '$statusMetricName' WHERE Descriptions.boundToMetric IN(SELECT desc2.IdMetric FROM(SELECT desc3.* FROM Dashboard.Descriptions AS desc3 WHERE desc3.id = $metricId) AS desc2) AND Descriptions.processType = 'responseTime' AND Descriptions.process = 'HttpProcess'";
                //fwrite($file, "Query1: " . $q1 . "\n");
                $r1 = mysqli_query($link, $q1);

                if($r1)
                {
                    //fwrite($file, "OK Q1: \n");
                    $q2 = "UPDATE Dashboard.Descriptions SET IdMetric = '$statusMetricName', description = '$fullDescription', query = " . returnManagedStringForDb($query1) . ", query2 = " . returnManagedStringForDb($query2) . ", queryType = '$dataSourceType', metricType = '$resultType', frequency = $updateFrequency, processType = '$processType', area = '$dataArea', source = '$dataSourceDescription', description_short = '$shortDescription', dataSource = " . returnManagedStringForDb($dataSource) . ", storingData = '$storingData', municipalityOption = '$cityContext', timeRangeOption = '$timeRange', sameDataAlarmCount = " . returnManagedNumberForDb($sameDataAlarmCount) . ", oldDataEvalTime = " . returnManagedNumberForDb($oldDataEvalTime) . ", hasNegativeValues = '$hasNegativeValues', process = " . returnManagedStringForDb($process) . " WHERE Descriptions.id = $metricId";
                    //fwrite($file, "Query2: " . $q2 . "\n");
                    $r2 = mysqli_query($link, $q2);
                    if($r2)
                    {
                        //fwrite($file, "OK Q2: \n");
                        mysqli_commit($link);
                        mysqli_close($link);
                        echo "Ok";
                    } 
                    else 
                    {
                        //fwrite($file, "Ko Q2: \n");
                        mysqli_rollback($link);
                        mysqli_close($link);
                        echo "Ko";
                    }
                    
                } 
                else 
                {
                    //fwrite($file, "Ko Q1: \n");
                    mysqli_rollback($link);
                    mysqli_close($link);
                    echo "Ko";
                }
            }
            else
            {
                $q = "UPDATE Dashboard.Descriptions SET IdMetric = '$metricName', description = '$fullDescription', query = " . returnManagedStringForDb($query1) . ", query2 = " . returnManagedStringForDb($query2) . ", queryType = '$dataSourceType', metricType = '$resultType', frequency = '$updateFrequency', processType = '$processType', area = '$dataArea', source = '$dataSourceDescription', description_short = '$shortDescription', dataSource = " . returnManagedStringForDb($dataSource) . ", storingData = '$storingData', municipalityOption = '$cityContext', timeRangeOption = '$timeRange', sameDataAlarmCount = " . returnManagedNumberForDb($sameDataAlarmCount) . ", oldDataEvalTime = " . returnManagedNumberForDb($oldDataEvalTime) . ", hasNegativeValues = '$hasNegativeValues', process = " . returnManagedStringForDb($process) . " WHERE Descriptions.id = $metricId";
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
        
        
    } 
    else if(isset($_REQUEST['deleteMetric']))//Escape 
    {
        session_start();
        checkSession('ToolAdmin');
                
        mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
        
        if(isset($_SESSION['loggedRole']))
        {
            $metricId = mysqli_real_escape_string($link, $_REQUEST['metricId']);
            
            //Altrimenti dà errore, cfr. https://stackoverflow.com/questions/45494/mysql-error-1093-cant-specify-target-table-for-update-in-from-clause
            $q = "DELETE FROM Dashboard.Descriptions WHERE boundToMetric IN(SELECT desc2.IdMetric FROM(SELECT desc3.* FROM Dashboard.Descriptions AS desc3 WHERE id = '$metricId') AS desc2)";
           
            $r = mysqli_query($link, $q);
            
            if($r)
            {
                $q2 = "DELETE FROM Dashboard.Descriptions WHERE id = $metricId";
                $r2 = mysqli_query($link, $q2);
                if($r2) 
                {
                    mysqli_commit($link);
                    mysqli_close($link);
                    echo "Ok";
                } 
                else 
                {
                    mysqli_rollback($link);
                    mysqli_close($link);
                    echo "Ko";
                }
            }
            else
            {
                mysqli_rollback($link);
                mysqli_close($link);
                echo "Ko";
            }
        }
    } 
    else if(isset($_REQUEST['updateMetricStatus']))
    {
        session_start();
        checkSession('ToolAdmin');
        
        if(isset($_SESSION['loggedRole']))
        {
            $metricId = mysqli_real_escape_string($link, $_REQUEST['metricId']);
            $newStatus = mysqli_real_escape_string($link, $_REQUEST['newStatus']);

            $q = "UPDATE Dashboard.Descriptions SET Descriptions.status = '$newStatus' WHERE Descriptions.id = '$metricId'";
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
        checkSession('Manager');
                
        if(isset($_SESSION['loggedRole']))
        {
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
            $dashboardNewStatus = mysqli_real_escape_string($link, $_REQUEST['newStatus']);

            $q = "UPDATE Dashboard.Config_dashboard SET Config_dashboard.status_dashboard = '" . $dashboardNewStatus . "' WHERE Config_dashboard.Id = '" . $dashboardId . "'";
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
        checkSession('RootAdmin');
        
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
        checkSession('RootAdmin');

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
        checkSession('RootAdmin');

        if(isset($_SESSION['loggedRole']))
        {
            $id = mysqli_real_escape_string($link, $_POST['id']);
        
            $q = "DELETE FROM Dashboard.DataSource WHERE intId = '" . $id . "'";
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
        checkSession('RootAdmin');

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
                 "VALUES('$id_type_widget', '$source_php_widget', '$min_row', '$max_row', '$min_col', '$max_col', " . returnManagedStringForDb($widgetType) . ", '$unique_metric', " . returnManagedNumberForDb($number_metrics_widget) . ")";
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
        checkSession('RootAdmin');

        if(isset($_SESSION['loggedRole']))
        {
            $id = mysqli_real_escape_string($link, $_POST['id']);
        
            $q = "DELETE FROM Dashboard.Widgets WHERE id = '" . $id . "'";
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
        checkSession('RootAdmin');

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

            $q = "UPDATE Dashboard.Widgets SET id_type_widget = '$id_type_widget', source_php_widget = '$source_php_widget', min_row = '$min_row',  max_row = '$max_row', min_col = '$min_col', max_col = '$max_col', widgetType = " . returnManagedStringForDb($widgetType) . ", unique_metric = '$unique_metric', number_metrics_widget = " . returnManagedNumberForDb($number_metrics_widget) . " WHERE id = '$id'";
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
        checkSession('Manager');
        
        $zoomFactor = mysqli_real_escape_string($link, $_REQUEST['zoomFactorUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET zoomFactor = " . returnManagedNumberForDb($zoomFactor) . " WHERE Id = '" . $idWidget ."'";
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
        checkSession('Manager');
        
        $scaleX = mysqli_real_escape_string($link, $_REQUEST['scaleXUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET scaleX = " . returnManagedNumberForDb($scaleX) . " WHERE Id = '" . $idWidget . "'";
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
        checkSession('Manager');
        
        $scaleY = mysqli_real_escape_string($link, $_REQUEST['scaleYUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET scaleY = " . returnManagedNumberForDb($scaleY) . " WHERE Id = '" . $idWidget . "'";
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
        checkSession('Manager');
        
        $width = mysqli_real_escape_string($link, $_REQUEST['widthUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET size_columns = " . returnManagedNumberForDb($width) . " WHERE Id = '" . $idWidget . "'";
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
        checkSession('Manager');
        
        $height = mysqli_real_escape_string($link, $_REQUEST['heightUpdated']);
        $idWidget = mysqli_real_escape_string($link, $_REQUEST['idWidget']);
        
        $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET size_rows = " . returnManagedNumberForDb($height) . " WHERE Id = '" . $idWidget . "'";
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

            $upsqDbtb = "UPDATE Dashboard.Config_widget_dashboard SET lastSeries = " . returnManagedStringForDb(escapeForSQL($updatedSeries, $link)) . " WHERE name_w = '" . $widgetName . "'";
            $resultQuery = mysqli_query($link, $upsqDbtb);
        }
        mysqli_close($link);
    }
    elseif(isset($_REQUEST['showHideDashboardHeader'])) 
    {
        session_start();
        checkSession('Manager');
                
        if(isset($_SESSION['loggedRole']))
        {
            $newStatus = mysqli_real_escape_string($link, $_REQUEST['showHideDashboardHeader']);
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
            $query = "UPDATE Dashboard.Config_dashboard SET headerVisible = '$newStatus' WHERE Id = '" . $dashboardId ."'";
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
    elseif(isset($_REQUEST['getDashboardTitlesList']))
    {
        session_start();
        
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
    elseif(isset($_REQUEST['updateConfigFile']))
    {
        session_start();
        
        if(isset($_SESSION['loggedRole']))
        {
           if($_SESSION['loggedRole'] == "RootAdmin")
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
                        // CONTROLLARE IN SEGUITO
                        $fileOriginalContent["environment"]["value"] = $newActiveEnv;
                        break;

                    default:
                        // CONTROLLARE IN SEGUITO
                        $dataFromForm = json_decode($_REQUEST['data']);     // SANITIZE QUI ??

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
           if($_SESSION['loggedRole'] == "RootAdmin")
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
