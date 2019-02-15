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
        return "Other";
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
  if(!isset($_SESSION['loggedRole']))
  {
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
  $roles=array('Public','Observer','Manager','AreaManager','ToolAdmin','RootAdmin');
  if(array_search($role, $roles) >  array_search($curRole, $roles)) {
    if(!$redirect)
      $redirect = 'ssoLogin.php';
    header("Location: $redirect");
    #echo array_search($role, $roles)." ".array_search($_SESSION['loggedRole'], $roles);
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