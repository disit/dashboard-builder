<?php
    include '../config.php';
    
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
			
		case "updateHeaderColor":
			$newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
			
			$query = "UPDATE Dashboard.Config_widget_dashboard SET frame_color_w = '$newColor' WHERE name_w = '$widgetName'";
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

		case "updateTitleColor":
			$newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
			
			$query = "UPDATE Dashboard.Config_widget_dashboard SET headerFontColor = '$newColor' WHERE name_w = '$widgetName'";
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