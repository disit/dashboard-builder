<?php
    include('../config.php');
    include('process-form.php');
    
    
    switch($_REQUEST['action'])
    {
        case "getPcGeneralInfo":
            $page = file_get_contents($pcGeneralBulletinUrl);
            break;
        
        case "getPcMeteoInfo":
            $page = file_get_contents($bannerMeteoAlertUrl);
            break;
        
        case "getMeteoForecast":
            $page = file_get_contents($serviceMapUrlPrefix . "api/v1/?serviceUri=" . $_REQUEST['cityServiceUri']);
            break;
        
        case "notificatorRemoteLogout":
            session_start();
            //$file = fopen("C:\dashboardLog.txt", "w");
            //fwrite($file, "notificatorRemoteLogout\n");
            notificatorLogout($_SESSION['loggedUsername'], $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $ldapTool);
            break;
    }
    
    echo $page;
    

