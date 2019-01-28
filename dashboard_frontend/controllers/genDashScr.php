<?php
    include '../config.php';
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    error_reporting(E_ALL);
    $response = [];
    
    $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardIdUnderEdit']);
    
    //$reqBody = json_decode(file_get_contents('php://input'));
    //$file = fopen("C:\dashboardLog.txt", "w");
    
    if($_FILES['dashboardScrInput']['size'] > 0)
    {
        try
        {
            $uploadFolder = "../img/dashScr/dashboard".$dashboardId."/";
            
            if(!file_exists("../img/dashScr/"))
            {
                $oldMask = umask(0);
                mkdir("../img/dashScr/", 0777);
                umask($oldMask);
            }
            
            if(!file_exists($uploadFolder))
            {
                $oldMask = umask(0);
                mkdir($uploadFolder, 0777);
                umask($oldMask);
            }
            
            $files = glob($uploadFolder.'*');
            foreach($files as $file)
            { 
              if(is_file($file))
              {
                  unlink($file); 
              }
            }
            
            $filename = $_FILES['dashboardScrInput']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            $fname = "lastDashboardScr." . $ext;
            if(move_uploaded_file($_FILES['dashboardScrInput']['tmp_name'], $uploadFolder.$fname))
            {
                $q = "UPDATE Dashboard.Config_dashboard SET screenshotFilename = '$fname' WHERE Id = $dashboardId";
                $r = mysqli_query($link, $q);
    
                if($r)
                {
                    $response['result'] = 'Ok';
                }
                else
                {
                    $response['result'] = 'QueryKo';
                }
            }
            else
            {
                $response['result'] = 'UploadFileKo';
            }
        } 
        catch (Exception $ex) 
        {
            $response['result'] = 'UploadFileKo';
        }
    }
    else
    {
        $response['result'] = 'FileSizeZero';
    }
    
    echo json_encode($response);
