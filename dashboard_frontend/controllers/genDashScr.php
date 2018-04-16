<?php
    include '../config.php';
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    error_reporting(E_ALL);
    $response = [];
    
    $dashboardId = $_REQUEST['dashboardId'];
    
    //$reqBody = json_decode(file_get_contents('php://input'));
    //$file = fopen("C:\dashboardLog.txt", "w");
    
    if(isset($_FILES['file']) and !$_FILES['file']['error'])
    {
        try
        {
            $uploadFolder = "../img/dashScr/dashboard".$dashboardId."/";
            $oldMask = umask(0);
            mkdir("../img/dashScr/", 0777);
            mkdir($uploadFolder, 0777);
            umask($oldMask);

            $fname = "lastDashboardScr" . ".png";
            move_uploaded_file($_FILES['file']['tmp_name'], $uploadFolder.$fname);
            
            $response['result'] = 'Ok';
        } 
        catch (Exception $ex) 
        {
            $response['result'] = 'Ko';
        }
    }
    
    echo json_encode($response);
