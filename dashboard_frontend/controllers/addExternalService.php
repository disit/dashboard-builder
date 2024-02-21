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
    include '../opensearch/OpenSearchS4C.php';
	$open_search = new OpenSearchS4C();
    error_reporting(E_ERROR | E_NOTICE);
    date_default_timezone_set('Europe/Rome');

    session_start();
    checkSession('AreaManager');

    if (isset($_SESSION['loggedOrganization'])){
        $organization = $_SESSION['loggedOrganization'];
    } else {
        $organization = "Other";
    }

    if ($organization != "DISIT" && $organization != "Other") {
        $organizationArray = $organization;
    } else {
        $organizationArray = "[\'DISIT\', \'Other\']";
    }
    /* if (isset($_SESSION['loggedRole'])){
         $userRole = $_SESSION['loggedRole'];
     } else {
         $userRole = "";
     }

     if (isset($_SESSION['loggedUser'])){
         $user = $_SESSION['loggedUser'];
     } else {
         $user = "";
     }   */
    /*  if ($organization == "Other" && $userRole == "Root") {

      }*/

    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);

    $response = NULL;
    
 //   $nature = mysqli_real_escape_string($link, $_REQUEST['nature']);
    if (sanitizePostString('nature') == null) {       // New pentest COMMON CTR
        $nature = mysqli_real_escape_string($link, sanitizeGetString('nature'));
    } else {
        $nature = mysqli_real_escape_string($link, sanitizePostString('nature'));
    }
 //   $sub_nature = mysqli_real_escape_string($link, $_REQUEST['subnature']);
    if (sanitizePostString('subnature') == null) {       // New pentest COMMON CTR
        $sub_nature = mysqli_real_escape_string($link, sanitizeGetString('subnature'));
    } else {
        $sub_nature = mysqli_real_escape_string($link, sanitizePostString('subnature'));
    }
    $sub_nature = str_replace("/", "_", $sub_nature);
    $sub_nature = str_replace(".", "_", $sub_nature);
    $sub_nature = str_replace("\\", '_', $sub_nature);
    $sub_nature = str_replace(":", "_", $sub_nature);
  //  $param0 = mysqli_real_escape_string($link, $_REQUEST['param']);
    if (sanitizePostString('param') == null) {       // New pentest COMMON CTR
        $param0 = mysqli_real_escape_string($link, sanitizeGetString('param'));
    } else {
        $param0 = mysqli_real_escape_string($link, sanitizePostString('param'));
    }
    $lastCheck = date("Y-m-d H:i:s");

    //controllo param//
    $check = strstr($param0,'https');
    if($check)
    {
	$param = $param0;
    }
    else
    {
	$check2 = strstr($param0,'http');
	if($check2)
        {
            $param = str_replace('http','https',$param0);
	}
        else
        {
            $param = 'https://' . $param0;
	}
    }
    
    if($_FILES['getIcon']['size'] > 0)
    {
        $filename = $_FILES['getIcon']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = $sub_nature . "." . $ext;
        $filename = preg_replace('/\s+/', '_', $filename);
        $microAppExtServIcon = $filename;
    }
    else
    {
        $microAppExtServIcon = "standardIcon.png";
    }
    
    $q = "INSERT INTO DashboardWizard (nature, high_level_type, sub_nature, low_level_type, unique_name_id, instance_uri, get_instances, last_date, last_value, unit, metric, saved_direct, kb_based, sm_based, user, widgets, parameters, healthiness, microAppExtServIcon, lastCheck, ownership, organizations) " .
         "VALUES ('$nature','External Service', '$sub_nature', NULL, 'ExternalContent', NULL, NULL, NULL, NULL, 'webpage', 'no', 'direct', NULL, 'no', NULL, NULL,'$param', 'true', '$microAppExtServIcon', '$lastCheck', 'public', '$organizationArray')";
    $r = mysqli_query($link, $q);

    if(isset($useOpenSearch) && $useOpenSearch == "yes") {
        $open_search->createUpdateDocumentDashboardWizard(
            $nature,
            'External Service',
            $sub_nature,
            '',
            'ExternalContent',
            '',
            '',
            'webpage',
            'no',
            'direct',
            '',
            'no',
            '',
            '',
            $param,
            '',
            '',
            'true',
            $lastCheck,
            'public',
            $organizationArray,
            '',
            '',
            '',
            $sub_nature,
            'ExternalContent',
            '',
            '',
            '',
            '',
            '',
            $microAppExtServIcon

        );
    }
    
    
    if($r)
    {
        mysqli_close($link);
        //Logo del servizio
        $uploadFolder = "../img/externalServices/";
        
        if(!file_exists($uploadFolder))
        {
            $oldMask = umask(0);
            mkdir($uploadFolder, 0777);
            umask($oldMask);
        }
        
        if($_FILES['getIcon']['size'] > 0)
        {
            if(!move_uploaded_file($_FILES['getIcon']['tmp_name'], $uploadFolder.$filename))
            {  
                $queryFail = true;
            }
            else 
            {
               chmod($uploadFolder.$filename, 0666); 
            }
        }
        
        $response['result'] = "Ok";
        $response['url'] = escapeForHTML($param);
    }
    else
    {
        $response['result'] = "Ko";
    }
    
    echo json_encode($response);
?>