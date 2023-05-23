<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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

include('../config.php');
if (!isset($_SESSION)) {
  session_start();
}

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

checkSession('RootAdmin');

//echo "ldap_connect($ldapServer, $ldapPort, $ldapAdminDN, $ldapAdminPwd)<br>";

//first connection for paged query
$ds = ldap_connect($ldapServer, $ldapPort);
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
if ($ldapAdminDN) {
  $bind = ldap_bind($ds, $ldapAdminDN, $ldapAdminPwd);
  echo 'auth<br>';
} else {
  $bind = ldap_bind($ds);
  echo 'no auth<br>';
}
if(!$bind) {
  echo "BIND failed, wrong user & password or anonymous bind not allowed<br>";
  exit();
}
  
//second connection for search organization and update user data
$dss = ldap_connect($ldapServer, $ldapPort);
ldap_set_option($dss, LDAP_OPT_PROTOCOL_VERSION, 3);
if ($ldapAdminDN) {
  $bind = ldap_bind($dss, $ldapAdminDN, $ldapAdminPwd);
} else {
  $bind = ldap_bind($dss);
}

//Iterate all users on ldap
$pageSize = 100;

$i = 1;
$n = 1;
$cookie = '';
do {
  if (!ldap_control_paged_result($ds, $pageSize, true, $cookie)) {
    echo "FAIL ldap_control_paged_result<br>";
    break;
  }

  //$result = ldap_search($ds, $ldapBaseDN, '(&(objectClass=inetOrgPerson)(!(ou=*)))');
  $result = ldap_search($ds, $ldapBaseDN, 'objectClass=inetOrgPerson');
  if (!result) {
    echo "FAIL ldap_search<br>";
    break;
  }
  $entries = ldap_get_entries($ds, $result);
  if (!$entries) {
    echo "FAIL ldap_get_entries<br>";
    break;
  }

  foreach ($entries as $key => $value) {
    if (isset($value['dn'])) {
      $userDn = $value['dn'];
      $curr_org='';
      if(isset($value['ou'])) {
        $curr_org = $value['ou'][0];
      }
      $org = checkLdapOrganization($dss, $userDn, $ldapBaseDN);
      $username = $value['cn'][0];
      $cryptedUsername = encryptOSSL($username, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
      if($org!=$curr_org) {
        echo $i++ . ") dn:$userDn user:$username org:$org/$curr_org uid:$cryptedUsername ";
        //var_dump($value);
        if (isset($_GET['update'])) {
          $attrs = array('uid' => $cryptedUsername, 'ou' => $org);
          if (!ldap_mod_replace($dss, $userDn, $attrs))
            echo 'FAILED ' . ldap_error($ds) . '<br>';
          else
            echo "UPDATED <br>";
        } else {
          echo "NOT updated<br>";
        }
      }
    }
  }
  ldap_control_paged_result_response($ds, $result, $cookie);
} while ($cookie !== null && $cookie != '');

