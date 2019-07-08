<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */


function checkLdapMembership($connection, $userDn, $tool, $baseDn) 
{
    $result = ldap_search($connection, $baseDn, '(&(objectClass=posixGroup)(memberUid=' . $userDn . '))');
    $entries = ldap_get_entries($connection, $result);
    foreach ($entries as $key => $value) 
    {
        if(is_numeric($key)) 
        {
           if($value["cn"]["0"] == $tool) 
           {
              return true;
           }
        }
    }

    return false;
 }

function checkLdapRole($connection, $userDn, $role, $baseDn) 
 {
    $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalRole)(cn=' . $role . ')(roleOccupant=' . $userDn . '))');
    $entries = ldap_get_entries($connection, $result);
    foreach ($entries as $key => $value) 
    {
       if(is_numeric($key)) 
       { 
          if($value["cn"]["0"] == $role) 
          {
             return true;
          }
       }
    }

    return false;
}

function checkLdapOrganization($connection, $userDn, $baseDn)
{
    $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalUnit)(l=' . $userDn . '))');
  //  $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalUnit))');
    $entries = ldap_get_entries($connection, $result);
    $orgsArray = [];
    foreach ($entries as $key => $value)
    {
        if(is_numeric($key))
        {
            if($value["ou"]["0"] != '' )
            {
                array_push($orgsArray, $value["ou"]["0"]);
            }
        }
    }
    
    if (sizeof($orgsArray) > 1) {
        return "DISIT";
    } else {
        return $orgsArray[0];
    }
  //  return "";
}

function checkLdapGroup($connection, $userDn, $baseDn, $userOrg)
{
//    $result = ldap_search($connection, $baseDn, '(&(objectClass=groupOfNames)(member=' . $userDn . ')(objectClass=organizationalUnit)(ou=' . $userOrg . '))');
    $result = ldap_search($connection, $baseDn, '(&(objectClass=groupOfNames)(member=' . $userDn . '))');
    $entries = ldap_get_entries($connection, $result);
    $groupsArray = [];
    foreach ($entries as $key => $value)
    {
        if(is_numeric($key))
        {
            if($value["ou"]["0"] != '' )
            {
                array_push($groupsArray, $value["ou"]["0"]);
            }
        }
    }

    if (sizeof($groupsArray) > 1) {
        return $groupsArray;
    } else {
        return "";
    }
    //  return "";
}

function checkLdapGroupUsers($connection, $userDn, $baseDn, $userOrg)
{
    $result = ldap_search($connection, $baseDn, '(&(objectClass=groupOfNames)(member='. $userOrg . '))');
    //  $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalUnit))');
    $entries = ldap_get_entries($connection, $result);
    $orgsArray = [];
    foreach ($entries as $key => $value)
    {
        if(is_numeric($key))
        {
            if($value["ou"]["0"] != '' )
            {
                array_push($orgsArray, $value["ou"]["0"]);
            }
        }
    }

    if (sizeof($orgsArray) > 1) {
        return "Other";
    } else {
        return $orgsArray[0];
    }
    //  return "";
}

function checkUserLevel($username, $host, $usernameDb, $passwordDb)
{
    $usrLevel = "none";
    $linkNew = new mysqli($host, $usernameDb, $passwordDb, "profiledb");
    //  $result = ldap_search($connection, $baseDn, '(&(objectClass=organizationalUnit))');

    $query = "SELECT * FROM iot.roles_levels WHERE username = '$username'";
    $r = mysqli_query($linkNew, $query);

    if($r)
    {
        if(mysqli_num_rows($r) > 0)
        {
            $row = mysqli_fetch_assoc($r);
            $usrLevel = $row['level'];
            return $usrLevel;
        }
        else
        {
            return $usrLevel;
        }
    }
    else
    {
        return "none";
    }

    return "";
}

function checkSession($role, $redirect = '') {
  $goodSession=true;
  if(isset($_SESSION['refreshToken'])) {
      $parts = explode(".", $_SESSION['refreshToken']);
      $claims =  json_decode(base64_decode($parts[1]));
      if(isset($claims->exp) && $claims->exp < time()) {
        //refresh token expired
        $goodSession=false;
      }
  } else if(isset($_SESSION['loggedRole'])) {
    $goodSession=false;
  }
  if(!$goodSession) {
      $f=fopen("/tmp/checkSession.log","a");
      if(isset($claims->exp))
        fwrite($f,date('c')." invalid session, expired refresh token, exp: ".($claims->exp-time())." sub: $claims->sub tkn: ".$_SESSION['refreshToken']."\n");
      else
        fwrite($f,date('c')." invalid session, no refresh token for ".$_SESSION['loggedUsername']."/".$_SESSION['loggedRole']." \n");
      fclose($f);    
  }
  if(!isset($_SESSION['loggedRole']) || !$goodSession) {
    $_SESSION = array();
    $_SESSION['isPublic'] = true;
    $curRole = 'Public';
    //$_SESSION['loggedRole'] = "Public";
    //$_SESSION['loggedUsername']  = "";
    //$_SESSION['loggedType']  = "-";
    //$_SESSION['loggedOrganization']  = "none";
    //$_SESSION['loggedUserLevel']  = "-";      
    $_SESSION['sessionEndTime'] = time()+24*3600;
  } else {
    $_SESSION['isPublic'] = false;
    $curRole = $_SESSION['loggedRole'];
  }
  
  //redirect to login or the specified redirect if the current role level is lower than the minimum required role level or if the session is expired
  $roles=array('Public','Observer','Manager','AreaManager','ToolAdmin','RootAdmin');
  if(!$goodSession || array_search($curRole, $roles) < array_search($role, $roles) ) {
    if(!$redirect) {
      $redirect = 'ssoLogin.php';
    }
    header("Location: $redirect");
    exit();
  }
}

function checkDashboardId($link, $id, $user) {
  if(isset($_SESSION['loggedRole']) && $_SESSION['loggedUsername']=='RootAdmin')
    return true;
  
  if(!isset($user)) {
    $user = $_SESSION['loggedUsername'];
  }
  $r = mysqli_query($link, "SELECT Id FROM Dashboard.Config_dashboard WHERE Id = $id AND user='$user'");
  if($r && mysqli_num_rows($r)>0)
    return true;
  return false;
}

function escapeForJS($varToBeEscaped) {
    return addslashes($varToBeEscaped);
}

function escapeForHTML($varToBeEscaped) {
    return htmlspecialchars($varToBeEscaped);
}

function escapeForSQL($varToBeEscaped, $db) {
    if (is_numeric($varToBeEscaped)) {
        return $varToBeEscaped;
    } else {
        return mysqli_real_escape_string($db, $varToBeEscaped);
    }
}

function checkVarType($varToBeChecked, $expectedType) {
    $filter = "";
    $validatedVar = "false";
    if (strcmp($expectedType, 'integer') == 0 || strcmp($expectedType, 'int') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_INT);
    } else if (strcmp($expectedType, 'float') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_FLOAT);
    } else if (strcmp($expectedType, 'boolean') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_BOOLEAN);
    } else if (strcmp($expectedType, 'url') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_URL);
    } else if (strcmp($expectedType, 'ip') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_IP);
    } else if (strcmp($expectedType, 'email') == 0) {
        $validatedVar = filter_var($varToBeChecked, FILTER_VALIDATE_EMAIL);
    }
    return $validatedVar;
}

function checkWidgetNameInDashboard($link, $widgetName, $dashId) {
    if (checkVarType($dashId, "integer") === false) {
        eventLog("Returned the following ERROR in common.php when checking var type for for dashboard_id = ".$dashId.": ".$dashId." is not an integer as expected. Exit from script.");
        exit();
    };
    $query = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '" . $dashId . "' AND name_w = '" . escapeForSQL($widgetName, $link) . "';";
    $r = mysqli_query($link, $query);

    if($r)
    {
        if(mysqli_num_rows($r) > 0)
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

function checkAlphaNum($entry) {
    if (ctype_alnum($entry)) {
        return true;
    } else {
        return false;
    }
}

function checkAlphaNumAndSpaces($entry) {
    if (ctype_alnum(str_replace(' ', '', $entry))) {
        return true;
    } else {
        return false;
    }
}

function eventLog($msgArray)
{
    $string="";
    $logData['event_datetime']='['.date('D Y-m-d h:i:s A').'] [client '.$_SERVER['REMOTE_ADDR'].']';
    if(is_array($msgArray))
    {
        foreach($msgArray as $msg)
            $string.=$logData['event_datetime']." ".$msg."rn";
    }
    else
    {
        $string.=$logData['event_datetime']." ".$msgArray."\r\n";
    }

    $stCurLogFileName='log_'.date('Ymd').'.txt';
    $fHandler=fopen("../logs/".$stCurLogFileName,'a+');
    fwrite($fHandler,$string);
    fclose($fHandler);
}

function eventLogReq($msgArray)
{
    $string="";
    $logData['event_datetime']='['.date('D Y-m-d h:i:s A').'] [client '.$_SERVER['REMOTE_ADDR'].']';
    if(is_array($msgArray))
    {
        foreach($msgArray as $key => $value) {
            $string .= $logData['event_datetime'] . " [" . $key . "] = " . $value . "\r\n";
        }
    }
    else
    {
        $string.=$logData['event_datetime']." ".$msgArray."\r\n";
    }

    $stCurLogFileName='log_'.date('Ymd').'.txt';
    $fHandler=fopen("../logs/".$stCurLogFileName,'a+');
    fwrite($fHandler,$string);
    fclose($fHandler);
}