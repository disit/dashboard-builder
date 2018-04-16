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
    
    switch($action)
    {
        case "updateHeaderColor":
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
            $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET color_header = '$newColor' WHERE Id = $dashboardId";
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
        
        case "updateHeaderFontColor":
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
            $newColor = mysqli_real_escape_string($link, $_REQUEST['newColor']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET headerFontColor = '$newColor' WHERE Id = $dashboardId";
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
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
            $newTitle = mysqli_real_escape_string($link, $_REQUEST['newTitle']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET name_dashboard = '$newTitle', title_header = '$newTitle' WHERE Id = $dashboardId";
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
            
        case "updateSubtitle":
            $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
            $newSubtitle = mysqli_real_escape_string($link, $_REQUEST['newSubtitle']);
            
            $query = "UPDATE Dashboard.Config_dashboard SET subtitle_header = '$newSubtitle'WHERE Id = $dashboardId";
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