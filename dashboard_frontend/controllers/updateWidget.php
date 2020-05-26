<?php
    include '../config.php';
    
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
    
    function updateLastUsedColors($newColor, $widgetName, $link)
    {
        $q1 = "SELECT * FROM Dashboard.Config_dashboard WHERE Id IN(SELECT id_dashboard FROM Config_widget_dashboard WHERE name_w = '$widgetName')";
        $r1 = mysqli_query($link, $q1);

        if($r1) 
        {
            $row1 = mysqli_fetch_assoc($r1);
            $dashboardId = $row1['Id'];
            $lastUsedColors = json_decode($row1['lastUsedColors']);
            
            array_pop($lastUsedColors);
            array_unshift($lastUsedColors, $newColor);
            
            $lastUsedColorsJson = json_encode($lastUsedColors);
            
            $q2 = "UPDATE Dashboard.Config_dashboard SET lastUsedColors = '$lastUsedColorsJson' WHERE Id = $dashboardId";
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
    $widgetName = mysqli_real_escape_string($link, $_REQUEST['widgetName']);

    if(!checkWidgetName($link, $widgetName)) {
        eventLog("invalid request for updateWidget.php $widgetName user: ".$_SESSION['loggedUsername']);
        exit;
    }
    
    switch($action)
    {
        case "updateTitleVisibility":
            $showTitle = mysqli_real_escape_string($link, $_REQUEST['showTitle']);
            $query = "UPDATE Dashboard.Config_widget_dashboard SET showTitle = '$showTitle' WHERE name_w = '$widgetName'";
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
		
		// MS> Persist widget content status at page load (displayed vs collapsed)
		case "updateContentVisibility":		
			$showContent = mysqli_real_escape_string($link, $_REQUEST['showContent']);
            $query = "UPDATE Dashboard.Config_widget_dashboard SET showContent = '$showContent' WHERE name_w = '$widgetName'";
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
		// <MS
		
		// MS> Persist possibility for users (viewers) to collapse widget content
		case "updateCollapsibility":		
			$collapsibility = mysqli_real_escape_string($link, $_REQUEST['collapsibility']);
            $query = "UPDATE Dashboard.Config_widget_dashboard SET collapseAllowed = '$collapsibility' WHERE name_w = '$widgetName'";
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
		// <MS	
			
        case "updateHeaderColor":
            $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);		
            $query = "UPDATE Dashboard.Config_widget_dashboard SET frame_color_w = '$newColor' WHERE name_w = '$widgetName'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
                updateLastUsedColors($newColor, $widgetName, $link);
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;

        case "updateTitleColor":
            $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
            $query = "UPDATE Dashboard.Config_widget_dashboard SET headerFontColor = '$newColor' WHERE name_w = '$widgetName'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
                updateLastUsedColors($newColor, $widgetName, $link);
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;
            
        case "updateBorderColor":
            $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
            $query = "UPDATE Dashboard.Config_widget_dashboard SET borderColor = '$newColor' WHERE name_w = '$widgetName'";
            $result = mysqli_query($link, $query);

            if($result) 
            {
                $response['detail'] = 'Ok';
                updateLastUsedColors($newColor, $widgetName, $link);
            }
            else
            {
                $response['detail'] = 'queryKo';
            }
            break;    
		
          case "updateWidth":  
                $newWidth = mysqli_real_escape_string($link, $_REQUEST['newWidth']);
                $query = "UPDATE Dashboard.Config_widget_dashboard SET size_columns = '$newWidth' WHERE name_w = '$widgetName'";
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
          
          case "updateHeight":  
              $newHeight = mysqli_real_escape_string($link, $_REQUEST['newHeight']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET size_rows = '$newHeight' WHERE name_w = '$widgetName'";
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
              
            case "updateTitle":  
              $newTitle = mysqli_real_escape_string($link, $_REQUEST['newTitle']);
           //  if (strpos($newTitle, '\'') != false || strpos($newTitle, '"') != false) {
              if (strpos($newTitle, '"') != false) {
                  $response['detail'] = 'queryQuotesKo';
                  break;
              }
              
              $querySel = "SELECT id_dashboard, title_w FROM Dashboard.Config_widget_dashboard WHERE name_w = '$widgetName'";
              $resultSel = mysqli_query($link, $querySel);

              if($resultSel) 
              {
                  if($resultSel->num_rows > 0) 
                  {
              
                    while($row = mysqli_fetch_array($resultSel)) 
                    {
                        $oldGeneratorName = $row['title_w'];
                        $idDash = $row['id_dashboard'];
                    }
                  }
              }
              
              $queryDashName = "SELECT name_dashboard FROM Dashboard.Config_dashboard WHERE Id = $idDash";
              $resultDashName = mysqli_query($link, $queryDashName);

              if($resultDashName) 
              {
                  if($resultDashName->num_rows > 0) 
                  {
              
                    while($rowDN = mysqli_fetch_array($resultDashName)) 
                    {
                        $containerName = $rowDN['name_dashboard'];
                    }
                  }
              }
              else
              {
                 // Nothing...
              }
              
           //   $newTitle = htmlentities($newTitle, ENT_QUOTES|ENT_HTML5);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET title_w = '$newTitle' WHERE name_w = '$widgetName'";
              $result = mysqli_query($link, $query);

              if($result) 
              {
                 $response['detail'] = 'Ok';
              }
              else
              {
                 $response['detail'] = 'queryKo';
              }
              
              // Aggiorniamo il Notificatore per modificare il campo corrispondente al widget title nella tabell eventGenerators
              $newGeneratorName = $newTitle;
              $newGeneratorName = preg_replace('/\s+/', '+', $newGeneratorName);
              $oldGeneratorName = preg_replace('/\s+/', '+', $oldGeneratorName);
              $containerName = preg_replace('/\s+/', '+', $containerName);
              $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=updateGeneratorName&appName=' . $notificatorAppName . '&oldGeneratorName=' . $oldGeneratorName . '&newGeneratorName=' . $newGeneratorName . '&containerName=' . $containerName;
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
              
            case "updateFrequency":  
              $newFreq = mysqli_real_escape_string($link, $_REQUEST['newFreq']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET frequency_w = '$newFreq' WHERE name_w = '$widgetName'";
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
              
            case "updateInfo":  
              $newInfo = mysqli_real_escape_string($link, $_REQUEST['newInfo']);
              $newInfo = preg_replace("/<\\/?script[^>]*>/", "", $newInfo);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET infoMessage_w = " . returnManagedStringForDb($newInfo) . " WHERE name_w = '$widgetName'";
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
              
            case "updateBackgroundColor":  
              $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET color_w = " . returnManagedStringForDb($newColor) . " WHERE name_w = '$widgetName'";
              $result = mysqli_query($link, $query);

              if($result) 
              {
                 $response['detail'] = 'Ok';
                 updateLastUsedColors($newColor, $widgetName, $link);
              }
              else
              {
                 $response['detail'] = 'queryKo';
              }
              break;
              
            case "updateChartColor":  
              $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET chartColor = " . returnManagedStringForDb($newColor) . " WHERE name_w = '$widgetName'";
              $result = mysqli_query($link, $query);

              if($result) 
              {
                 $response['detail'] = 'Ok';
                 updateLastUsedColors($newColor, $widgetName, $link);
              }
              else
              {
                 $response['detail'] = 'queryKo';
              }
              break;
              
            case "updateChartPlaneColor":  
              $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET chartPlaneColor = " . returnManagedStringForDb($newColor) . " WHERE name_w = '$widgetName'";
              $result = mysqli_query($link, $query);

              if($result) 
              {
                 $response['detail'] = 'Ok';
                 updateLastUsedColors($newColor, $widgetName, $link);
              }
              else
              {
                 $response['detail'] = 'queryKo';
              }
              break;
              
            case "updateChartAxesColor":  
              $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET chartAxesColor = " . returnManagedStringForDb($newColor) . " WHERE name_w = '$widgetName'";
              $result = mysqli_query($link, $query);

              if($result) 
              {
                 $response['detail'] = 'Ok';
                 updateLastUsedColors($newColor, $widgetName, $link);
              }
              else
              {
                 $response['detail'] = 'queryKo';
              }
              break;  
              
            case "updateChartLabelsColor":  
              $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET chartLabelsFontColor = " . returnManagedStringForDb($newColor) . " WHERE name_w = '$widgetName'";
              $result = mysqli_query($link, $query);

              if($result) 
              {
                 $response['detail'] = 'Ok';
                 updateLastUsedColors($newColor, $widgetName, $link);
              }
              else
              {
                 $response['detail'] = 'queryKo';
              }
              break;    
              
            case "updateDataLabelsFontSize":  
              $newSize = mysqli_real_escape_string($link, $_REQUEST['newSize']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET dataLabelsFontSize = '$newSize' WHERE name_w = '$widgetName'";
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
              
            case "updateTimeRange":  
              $newTimeRange = mysqli_real_escape_string($link, $_REQUEST['newTimeRange']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET temporal_range_w = '$newTimeRange' WHERE name_w = '$widgetName'";
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

            case "updateAddMode":
              $addMode = mysqli_real_escape_string($link, $_REQUEST['addMode']);
              $query = "UPDATE Dashboard.Config_widget_dashboard SET viewMOde = '$addMode' WHERE name_w = '$widgetName'";
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

        case "updateNewGridDims":
            $newWidth = mysqli_real_escape_string($link, $_REQUEST['newWidth']);
            $newHeight = mysqli_real_escape_string($link, $_REQUEST['newHeight']);
            $newNCols = mysqli_real_escape_string($link, $_REQUEST['newNCols']);
            $newNRows = mysqli_real_escape_string($link, $_REQUEST['newNRows']);
            $widgetName = mysqli_real_escape_string($link, $_REQUEST['widgetName']);
            $query = "UPDATE Dashboard.Config_widget_dashboard SET size_columns = '$newWidth', size_rows = '$newHeight', n_row = '$newNRows', n_column = '$newNCols', scaleFactor = 'yes' WHERE name_w = '$widgetName'";
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

            default:
              break;
	}
	
	mysqli_commit($link);
        echo json_encode($response);