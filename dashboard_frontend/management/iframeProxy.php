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
        
        case "getOrionEntityList":
            $page = file_get_contents($orionBaseUrl . "/v2/entities");
            break;
        
        case "getOrionEntityAttributes":
            $entity = $_REQUEST['entity'];
            $page = file_get_contents($orionBaseUrl . "/v2/entities/" . $entity . "/attrs");
            break;
        
        case "getActuatorWidgetTypes":
            $type = $_REQUEST['type'];
            $result = [];
            
            $link = mysqli_connect($host, $username, $password);
            mysqli_select_db($link, $dbname);
            
            $q = "SELECT * FROM Dashboard.Widgets WHERE widgetCategory = 'actuator' AND widgetType LIKE '%" . $type . "%'";
            $r = mysqli_query($link, $q);
            
            if($r)
            {
                $result['detail'] = "Ok";
                $result['list'] = [];
                while($row = mysqli_fetch_assoc($r)) 
                {
                    array_push($result['list'], $row);
                }
            }
            else
            {
                $result['detail'] = "Ko";
            }
            
            $page = json_encode($result);
            break;
        
        case "notificatorRemoteLogout":
            session_start();
            notificatorLogout($_SESSION['loggedUsername'], $notificatorApiUsr, $notificatorApiPwd, $notificatorUrl, $ldapTool);
            break;
    }
    
    echo $page;
    

