<?php
    include '../config.php';
    define('REST_API_ROOT', '/api/v1/');
    define('ROCKET_CHAT_INSTANCE', $chatBaseUrl);
    include "../rocket-chat-rest-client/RocketChatClient.php";
    include "../rocket-chat-rest-client/RocketChatUser.php";
    include "../rocket-chat-rest-client/RocketChatChannel.php";
    
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
    
    function updateLastUsedColors($newColor, $dashboardId, $link)
    {
        $q1 = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId'";
        $r1 = mysqli_query($link, $q1);

        if($r1) 
        {
            $row1 = mysqli_fetch_assoc($r1);
            $lastUsedColors = json_decode($row1['lastUsedColors']);
            
            array_pop($lastUsedColors);
            array_unshift($lastUsedColors, $newColor);
            
            $lastUsedColorsJson = json_encode($lastUsedColors);
            
            $q2 = "UPDATE Dashboard.Config_dashboard SET lastUsedColors = '$lastUsedColorsJson' WHERE Id = '$dashboardId'";
            $r2 = mysqli_query($link, $q2);

            if($r2) 
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    session_start();
    checkSession('Manager');

    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    error_reporting(E_ERROR | E_NOTICE);
    $response = [];
    date_default_timezone_set('Europe/Rome');
    
    if(!$link->set_charset("utf8")) 
    {
        echo '<script type="text/javascript">';
        echo 'alert("Error loading character set utf8: %s\n");';
        echo '</script>';
        exit();
    }

    $action = mysqli_real_escape_string($link, $_REQUEST['action']);
    $dashboardId = mysqli_real_escape_string($link, $_REQUEST[$action=='updateAdvancedProperties' ? 'dashboardIdUnderEdit' : 'dashboardId']);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in updateDashboard.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    };
    if(!checkDashboardId($link, $dashboardId)) {
        eventLog("invalid request for updateDashboard.php for dashboardId = $dashboardId user: ".$_SESSION['loggedUsername']);
        exit;
    }      
    
    switch($action)
    {
        case "updateHeaderColor":
            $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET color_header = '$newColor' WHERE Id = '$dashboardId'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
                updateLastUsedColors($newColor, $dashboardId, $link);
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;
        
        case "updateHeaderFontColor":
            $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET headerFontColor = '$newColor' WHERE Id = '$dashboardId'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
                updateLastUsedColors($newColor, $dashboardId, $link);
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;
            
        case "updateAreaColor":
            $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET color_background = '$newColor' WHERE Id = '$dashboardId'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
                updateLastUsedColors($newColor, $dashboardId, $link);
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break; 
            
        case "updateFrameColor":
            $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET external_frame_color = '$newColor' WHERE Id = '$dashboardId'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
                updateLastUsedColors($newColor, $dashboardId, $link);
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;       
            
        case "updateTitle":
            $newTitle = mysqli_real_escape_string($link, $_REQUEST['newTitle']);
            $dashboardTitle = mysqli_real_escape_string($link, $_REQUEST['dashboardTitle']);

            if (strpos($newTitle, '&') !== false) {
                $response['detail'] = 'queryKo_ampersend';
                break;
            }

            if (strpos($newTitle, "'") !== false || strpos($newTitle, '"') !== false) {
                $response['detail'] = 'queryKo_quotes';
                break;
            }

            $querySel = "SELECT name_dashboard FROM Dashboard.Config_dashboard WHERE Id = '$dashboardId'";
            $resultSel = mysqli_query($link, $querySel);

            if($resultSel) 
            {
                if($resultSel->num_rows > 0) 
                {

                  while($row = mysqli_fetch_array($resultSel)) 
                  {
                      $oldContainerName = $row['name_dashboard'];
                  }
                }
            }
            else
            {
               // Nothing...
            }
            
            $query = "UPDATE Dashboard.Config_dashboard SET name_dashboard = '$newTitle', title_header = '$newTitle' WHERE Id = '$dashboardId'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
                $nameGroup=urldecode ($nameGroup);
                $nameGroup=strtolower(str_replace(" ", "", str_replace('%2520','',str_replace('%20', '', $dashboardTitle."-".$dashboardId))));
                $nameGroup = str_replace('à', 'a', $nameGroup);
                $nameGroup = str_replace('è', 'e', $nameGroup);
                $nameGroup = str_replace('é', 'e', $nameGroup);
                $nameGroup = str_replace('ì', 'i', $nameGroup);
                $nameGroup = str_replace('ò', 'o', $nameGroup);
                $nameGroup = str_replace('ù', 'u', $nameGroup);
                $nameGroup = str_replace('å', 'a', $nameGroup);
                $nameGroup = str_replace('ë', 'e', $nameGroup);
                $nameGroup = str_replace('ô', 'o', $nameGroup);
                $nameGroup = str_replace('á', 'a', $nameGroup);
                $nameGroup = str_replace('ç', 'c', $nameGroup);
                $nameGroup = str_replace('ÿ', 'y', $nameGroup);
                $nameGroup=preg_replace("/[^a-zA-Z0-9_-]/", "", $nameGroup);
                $admin = new \RocketChat\User();
                $admin->login();
                $channelArc = new \RocketChat\Channel('N');
                $infoChannel=$channelArc->infoByName($nameGroup);
                $newName=urldecode ($newName);
                $newName=strtolower(str_replace(" ", "", str_replace('%2520','',str_replace('%20', '', $newTitle."-".$dashboardId))));
                $newName = str_replace('à', 'a', $newName);
                $newName = str_replace('è', 'e', $newName);
                $newName = str_replace('é', 'e', $newName);
                $newName = str_replace('ì', 'i', $newName);
                $newName = str_replace('ò', 'o', $newName);
                $newName = str_replace('ù', 'u', $newName);
                $newName = str_replace('å', 'a', $newName);
                $newName = str_replace('ë', 'e', $newName);
                $newName = str_replace('ô', 'o', $newName);
                $newName = str_replace('á', 'a', $newName);
                $newName = str_replace('ç', 'c', $newName);
                $newName = str_replace('ÿ', 'y', $newName);
                $newName=preg_replace("/[^a-zA-Z0-9_-]/", "", $newName);
               if(isset($infoChannel->channel->_id)){
                $channelArc->rename($infoChannel->channel->_id,$newName);
                $admin->logout();
                }
                
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            
            // Aggiorniamo il Notificatore per modificare il campo corrispondente al widget title nella tabell eventGenerators
              $newContainerName = $newTitle;
              $newContainerName = preg_replace('/\s+/', '+', $newContainerName);
              $oldContainerName = preg_replace('/\s+/', '+', $oldContainerName);
              $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=updateContainerName&appName=' . $notificatorAppName . '&oldContainerName=' . $oldContainerName . '&newContainerName=' . $newContainerName;
              $url = $notificatorUrl.$data;

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
            
            break;
            
        case "updateSubtitle":
            $newSubtitle = mysqli_real_escape_string($link, $_REQUEST['newSubtitle']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET subtitle_header = '$newSubtitle' WHERE Id = '$dashboardId'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;
            
        case "updateEmbedList":
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
            if (checkVarType($dashboardId, "integer") === false) {
                eventLog("Returned the following ERROR in updateDashboard.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
                exit();
            };
            $newList = $_REQUEST['newList'];
            
            if(($newList != "[]")&&($newList != ""))
            {
               $embeddable = "yes";
            }
            else
            {
                $embeddable = "no";
                $newList = "[]";
            }
            
            $query = "UPDATE Dashboard.Config_dashboard SET embeddable = '$embeddable', authorizedPagesJson = '$newList' WHERE Id = '$dashboardId'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;       
        
        case "updateAdvancedProperties":
            mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
            $queryFail = false;
            $response = array();

            $dashboardAuthor = $_REQUEST['dashboardUser'];
            $dashboardEditor = $_REQUEST['dashboardEditor'];
            $dashboardTitle = $_REQUEST['currentDashboardTitle'];

            $nCols = mysqli_real_escape_string($link, $_POST['inputWidthDashboard']); 

            $filename = NULL;
            $logoLink = NULL;
            $bckFilename = NULL;
            if(isset($_POST['dashBckImgFlag']))
            {
                $useBckImg = 'yes';
            }
            else
            {
                $useBckImg = 'no';
            }
            
            if(isset($_POST['inputDashboardViewMode']))
            {
                $viewMode = "alwaysResponsive";
            }
            else
            {
                $viewMode = "fixed";
            }
            
            if(isset($_POST['headerVisible']))
            {
                $headerVisible = 1;
            }
            else
            {
                $headerVisible = 0;
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

                $filename = $_FILES['dashboardLogoInput']['name'];

                if(!move_uploaded_file($_FILES['dashboardLogoInput']['tmp_name'], $uploadFolder.$filename))  
                {  
                    mysqli_close($link);
                    $queryFail = true;
                }
                else 
                {
                   chmod($uploadFolder.$filename, 0666); 
                   
                    //Upload eventuale file di background della dashboard
                    if($_FILES['dashboardBckImg']['size'] > 0)
                    {
                        $bckFolder = "../img/dashBackgrounds/dashboard" . $dashboardId . "/";
                        if(!file_exists("../img/dashBackgrounds/"))
                        {
                            $oldMask = umask(0);
                            mkdir("../img/dashBackgrounds/", 0777);
                            umask($oldMask);
                        }

                        if(!file_exists($bckFolder))
                        {
                            $oldMask = umask(0);
                            mkdir($bckFolder, 0777);
                            umask($oldMask);
                        }
                        else
                        {
                            $oldFiles = glob($bckFolder . '*');
                            foreach($oldFiles as $fileToDel)
                            { 
                                if(is_file($fileToDel))
                                {
                                   unlink($fileToDel);
                                }
                            }
                        }

                        $bckFilename = $_FILES['dashboardBckImg']['name'];

                        if(!move_uploaded_file($_FILES['dashboardBckImg']['tmp_name'], $bckFolder.$bckFilename))  
                        {  
                            mysqli_close($link);
                            $queryFail = true;
                        }
                        else 
                        {
                            // NEW P-TEST: mettere apici a $ncols
                            $query = "UPDATE Dashboard.Config_dashboard SET width = $width, num_columns = $nCols, logoFilename = '$filename', logoLink = '$logoLink', headerVisible = $headerVisible, viewMode='$viewMode', bckImgFilename = " . returnManagedStringForDb($bckFilename) . ", useBckImg = '$useBckImg', last_edit_date = CURRENT_TIMESTAMP WHERE Id = '$dashboardId'";
                            $result = mysqli_query($link, $query);  

                             if(!$result)
                             {
                                 $rollbackResult = mysqli_rollback($link);
                                 mysqli_close($link);
                                 $queryFail = true;
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
                    }
                    else
                    {
                      //  $query = "UPDATE Dashboard.Config_dashboard SET width = $width, num_columns = $nCols, logoFilename = '$filename', logoLink = '$logoLink', headerVisible = $headerVisible, viewMode='$viewMode', bckImgFilename = " . returnManagedStringForDb($bckFilename) . ", useBckImg = '$useBckImg', last_edit_date = CURRENT_TIMESTAMP WHERE Id = $dashboardId";
                        $query = "UPDATE Dashboard.Config_dashboard SET width = $width, num_columns = $nCols, logoFilename = '$filename', logoLink = '$logoLink', headerVisible = $headerVisible, viewMode='$viewMode', useBckImg = '$useBckImg', last_edit_date = CURRENT_TIMESTAMP WHERE Id = $dashboardId";
                        $result = mysqli_query($link, $query);  

                         if(!$result)
                         {
                            $rollbackResult = mysqli_rollback($link);
                            mysqli_close($link);
                            $queryFail = true;
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
                }
            }//Nessun nuovo file caricato
            else
            {
                //Upload eventuale file di background della dashboard
                if($_FILES['dashboardBckImg']['size'] > 0)
                {
                    $bckFolder = "../img/dashBackgrounds/dashboard" . $dashboardId . "/";
                    if(!file_exists("../img/dashBackgrounds/"))
                    {
                        $oldMask = umask(0);
                        mkdir("../img/dashBackgrounds/", 0777);
                        umask($oldMask);
                    }

                    if(!file_exists($bckFolder))
                    {
                        $oldMask = umask(0);
                        mkdir($bckFolder, 0777);
                        umask($oldMask);
                    }
                    else
                    {
                        $oldFiles = glob($bckFolder . '*');
                        foreach($oldFiles as $fileToDel)
                        { 
                            if(is_file($fileToDel))
                            {
                               unlink($fileToDel);
                            }
                        }
                    }

                    $bckFilename = $_FILES['dashboardBckImg']['name'];

                    if(!move_uploaded_file($_FILES['dashboardBckImg']['tmp_name'], $bckFolder.$bckFilename))  
                    {  
                        mysqli_close($link);
                        $queryFail = true;
                    }
                    else 
                    {
                        $query = "UPDATE Dashboard.Config_dashboard SET width = $width, num_columns = $nCols, logoLink = '$logoLink', headerVisible = $headerVisible, viewMode='$viewMode', bckImgFilename = " . returnManagedStringForDb($bckFilename) . ", useBckImg = '$useBckImg', last_edit_date = CURRENT_TIMESTAMP WHERE Id = $dashboardId";
                        $result = mysqli_query($link, $query);

                        if(!$result)
                        {
                           $rollbackResult = mysqli_rollback($link);
                           mysqli_close($link);
                           $queryFail = true;
                        }
                        else
                        {
                           $response["newLogo"] = "NO";
                           $response["logoLink"] = $logoLink;
                           $response["width"] = $width;
                           $response["num_cols"] = $nCols;
                        }
                    }
                }
                else
                {
                    $query = "UPDATE Dashboard.Config_dashboard SET width = $width, num_columns = $nCols, logoLink = '$logoLink', headerVisible = $headerVisible, viewMode='$viewMode', useBckImg = '$useBckImg', last_edit_date = CURRENT_TIMESTAMP WHERE Id = $dashboardId";
                    $result = mysqli_query($link, $query);

                    if(!$result)
                    {
                       $rollbackResult = mysqli_rollback($link);
                       mysqli_close($link);
                       $queryFail = true;
                    }
                    else
                    {
                       $response["newLogo"] = "NO";
                       $response["logoLink"] = $logoLink;
                       $response["width"] = $width;
                       $response["num_cols"] = $nCols;
                    }
                }
            }

            if(!$queryFail) 
            {
                mysqli_commit($link);
                mysqli_close($link);
                header("location: ../management/dashboard_configdash.php?dashboardId=" . $dashboardId . "&dashboardAuthorName=" . $dashboardAuthor . "&dashboardEditorName=" . $dashboardEditor . "&dashboardTitle=" . urlencode($dashboardTitle));
            }
            else
            {
                mysqli_rollback($link);
                mysqli_close($link);
                header("location: ../management/dashboard_configdash.php?dashboardId=" . $dashboardId . "&dashboardAuthorName=" . $dashboardAuthor . "&dashboardEditorName=" . $dashboardEditor . "&dashboardTitle=" . urlencode($dashboardTitle) . "&updateFail=true");
            }
            break;  
            
        case "addSeparator":
            $selqDbtbMaxSel2 = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'Dashboard' AND TABLE_NAME = 'Config_widget_dashboard'";
            $resultMaxSel2 = mysqli_query($link, $selqDbtbMaxSel2);

            //Calcolo del next id
            $rowMaxSel2 = mysqli_fetch_array($resultMaxSel2);
            if ((!is_null($rowMaxSel2['AUTO_INCREMENT'])) && (!empty($rowMaxSel2['AUTO_INCREMENT']))) 
            {
                $nextId = $rowMaxSel2['AUTO_INCREMENT'];
            }

            //Calcolo del first free row
            $q2 = "SELECT MAX(n_row + size_rows) AS maxRow FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '$dashboardId'";
            $r2 = mysqli_query($link, $q2);

            if($r2)
            {
                $row2 = mysqli_fetch_assoc($r2);
                if($row2['maxRow'] == null)
                {
                    $firstFreeRow = 1;
                }
                else
                {
                    $firstFreeRow = $row2['maxRow'];
                }

                $n_row = $firstFreeRow;
                $n_column = 1;
                $size_rows = 1;
                $size_columns = 8;
                $id_metric = "Separator";
                $type_w = "widgetSeparator";
                $name_w = preg_replace('/\+/', '', $id_metric) . "_" . $dashboardId . "_" . $type_w . $nextId;
                $name_w = preg_replace('/%20/', 'NBSP', $name_w);
                $title_w = "Separator - " . time();
                $color_w = "#FFFFFF";
                $link_w = "none";
                $frame_color_w = "#EEEEEE";
                $showTitle = "no";
                $hasTimer = "no";
                $headerFontColor = "#000000";
                $frequency_w = 6000;
                $notificatorRegistered = "no";
                $notificatorEnabled = "no";
                $enableFullscreenTab = "no";
                $enableFullscreenModal = "no";
                $fontFamily = "Auto";
                $creator = $_SESSION['loggedUsername'];
                $borderColor = "#FFFFFF";
                
                $newInsQuery = "INSERT INTO Dashboard.Config_widget_dashboard(name_w, id_dashboard, id_metric, type_w, n_row, n_column, size_rows, size_columns, title_w, color_w, link_w, frame_color_w, showTitle, headerFontColor, notificatorRegistered, notificatorEnabled, enableFullscreenTab, enableFullscreenModal, fontFamily, creator, borderColor) " .
                               "VALUES('$name_w', '$dashboardId', '$id_metric', '$type_w', $n_row, $n_column, $size_rows, $size_columns, '$title_w', '$color_w', '$link_w', '$frame_color_w', '$showTitle', '$headerFontColor', '$notificatorRegistered', '$notificatorEnabled', '$enableFullscreenTab', '$enableFullscreenModal', '$fontFamily', '$creator', '$borderColor')";
                
                $insR = mysqli_query($link, $newInsQuery);

                if($insR) 
                {
                    $response['detail'] = 'Ok';
                    $response['widgetId'] = $nextId;
                    $response['Id'] = $nextId;
                    $response['id_dashboard'] = $dashboardId;
                    $response['name_w'] = $name_w;
                    $response['title_w'] = $title_w;
                    $response['id_metric'] = $id_metric;
                    $response['type_w'] = $type_w;
                    $response['n_row'] = $n_row;
                    $response['n_column'] = $n_column;
                    $response['size_rows'] = $size_rows;
                    $response['size_columns'] = $size_columns;
                    $response['frame_color_w'] = $frame_color_w;
                    $response['headerFontColor'] = $headerFontColor;
                    $response['borderColor'] = $borderColor;
                    $response['color_w'] = $color_w;
                    $response['showTitle'] = $showTitle;
                    $response['hasTimer'] = $hasTimer;
                    $response['frequency_w'] = $frequency_w;
                    $response['zoomFactor'] = 1;
                    
                }
                else
                {
                    $response['detail'] = 'queryKo';
                }
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;    
            
        case "updateBackOverlayOpacity":
            $newValue = mysqli_real_escape_string($link, $_REQUEST['newValue']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET backOverlayOpacity = $newValue WHERE Id = '$dashboardId'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
                updateLastUsedColors($newColor, $dashboardId, $link);
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;
            
        default:
            break;
    }
    
    mysqli_commit($link);
    echo json_encode($response);