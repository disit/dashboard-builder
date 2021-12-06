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


include '../config.php';
require '../sso/autoload.php';

session_start();
//checkSession('RootAdmin');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);
error_reporting(E_ERROR);

//$action = $_REQUEST['action'];
$action = mysqli_real_escape_string($link, $_REQUEST['action']);
$action = filter_var($action, FILTER_SANITIZE_STRING);
//
if (isset($_SESSION['loggedRole'])) {
    $role_session_active = $_SESSION['loggedRole'];

    if ($action == 'get_data') {
        $query = "SELECT * FROM Organizations;";
        $result = mysqli_query($link, $query);
        if ($result) {
            $dashboardParams = [];
            $arr = [];
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $arr['id'] = $row['id'];
                    $arr['organizationName'] = $row['organizationName'];
                    $arr['kbUrl'] = $row['kbUrl'];
                    $arr['gpsCentreLatLng'] = $row['gpsCentreLatLng'];
                    $arr['zoomLevel'] = $row['zoomLevel'];
                    $arr['lang'] = $row['lang'];
                    if ($row['drupalUrl'] == null) {
                        $arr['drupalUrl'] = '';
                    } else {
                        $arr['drupalUrl'] = $row['drupalUrl'];
                    }
                    if ($row['orgUrl'] == null) {
                        $arr['orgUrl'] = '';
                    } else {
                        $arr['orgUrl'] = $row['orgUrl'];
                    }
                    if ($row['welcomeUrl'] == null) {
                        $arr['welcomeUrl'] = '';
                    } else {
                        $arr['welcomeUrl'] = $row['welcomeUrl'];
                    }
                    if ($row['users'] == null) {
                        $arr['users'] = '';
                    } else {
                        $arr['users'] = $row['users'];
                    }
                    if ($row['broker'] == null) {
                        $arr['broker'] = '';
                    } else {
                        $arr['broker'] = $row['broker'];
                    }
                    if ($row['orionIP'] == null) {
                        $arr['orionIP'] = '';
                    } else {
                        $arr['orionIP'] = $row['orionIP'];
                    }
                    if ($row['orthomapJson'] == null) {
                        $arr['orthomapJson'] = '';
                    } else {
                        $arr['orthomapJson'] = $row['orthomapJson'];
                    }
                    if ($row['kbIP'] == null) {
                        $arr['kbIP'] = '';
                    } else {
                        $arr['kbIP'] = $row['kbIP'];
                    }

                    //
                    array_push($dashboardParams, $arr);
                    //
                }
            }
            echo json_encode($dashboardParams);
        }
    } else if ($action == 'edit_data') {
        //
        if ($role_session_active == 'RootAdmin') {
            //
            $id = mysqli_real_escape_string($link, $_REQUEST['id']);
            $id = filter_var($id, FILTER_SANITIZE_STRING);
            //
            $name = mysqli_real_escape_string($link, $_REQUEST['name']);
            $name = filter_var($name, FILTER_SANITIZE_STRING);
            //
            $kb = mysqli_real_escape_string($link, $_REQUEST['kb']);
            $kb = filter_var($kb, FILTER_SANITIZE_STRING);
            //
            $zoom = mysqli_real_escape_string($link, $_REQUEST['zoom']);
            $zoom = filter_var($zoom, FILTER_SANITIZE_STRING);
            //
            $drupal = mysqli_real_escape_string($link, $_REQUEST['drupal']);
            $drupal = filter_var($drupal, FILTER_SANITIZE_STRING);
            //
            $org = mysqli_real_escape_string($link, $_REQUEST['org']);
            $org = filter_var($org, FILTER_SANITIZE_STRING);
            //
            $welc = mysqli_real_escape_string($link, $_REQUEST['welc']);
            $welc = filter_var($welc, FILTER_SANITIZE_STRING);
            //
            $gps = mysqli_real_escape_string($link, $_REQUEST['gps']);
            $gps = filter_var($gps, FILTER_SANITIZE_STRING);

            $old_name = mysqli_real_escape_string($link, $_REQUEST['old_name']);
            $old_name = filter_var($old_name, FILTER_SANITIZE_STRING);
            //
            ////////////////
            $broker = mysqli_real_escape_string($link, $_REQUEST['broker']);
            $broker = filter_var($broker, FILTER_SANITIZE_STRING);

            $orionIP = mysqli_real_escape_string($link, $_REQUEST['orionIP']);
            $orionIP = filter_var($orionIP, FILTER_SANITIZE_STRING);

            $orthomapJson = mysqli_real_escape_string($link, $_REQUEST['orthomapJson']);
            //$orthomapJson = filter_var($orthomapJson, FILTER_SANITIZE_STRING);
            $orthomap = json_encode($orthomapJson);

            $kbIP = mysqli_real_escape_string($link, $_REQUEST['kbIP']);
            $kbIP = filter_var($kbIP, FILTER_SANITIZE_STRING);
            ///////////////
            $query = "UPDATE Organizations SET 
                    kbUrl = '" . $kb . "', 
                    zoomLevel = '" . $zoom . "', 
                    drupalUrl = '" . $drupal . "', 
                    orgUrl = '" . $org . "', 
                    welcomeUrl = '" . $welc . " ',
                        gpsCentreLatLng = '" . $gps . "',
                        broker = '" . $broker . "',    
                        orionIP = '" . $orionIP . "',
                        orthomapJson = '" . $orthomapJson . "',
                        kbIP = '" . $kbIP . "'    
                WHERE id = " . $id;
            //echo($query);
            ///////////////////////
            $result = mysqli_query($link, $query);
            if ($result) {
                //
                echo('ok');
            } else {
                echo('ko');
            }
        }

        //
    } else if ($action == 'new_data') {
        //
        if ($role_session_active == 'RootAdmin') {
            //
            $name = mysqli_real_escape_string($link, $_REQUEST['name']);
            $name = filter_var($name, FILTER_SANITIZE_STRING);
            //
            $kb = mysqli_real_escape_string($link, $_REQUEST['kb']);
            $kb = filter_var($kb, FILTER_SANITIZE_STRING);
            //
            $zoom = mysqli_real_escape_string($link, $_REQUEST['zoom']);
            $zoom = filter_var($zoom, FILTER_SANITIZE_STRING);
            //
            $drupal = mysqli_real_escape_string($link, $_REQUEST['drupal']);
            $drupal = filter_var($drupal, FILTER_SANITIZE_STRING);
            //
            $org = mysqli_real_escape_string($link, $_REQUEST['org']);
            $org = filter_var($org, FILTER_SANITIZE_STRING);
            //
            $welc = mysqli_real_escape_string($link, $_REQUEST['welc']);
            $welc = filter_var($welc, FILTER_SANITIZE_STRING);

            $gps = mysqli_real_escape_string($link, $_REQUEST['gps']);
            $gps = filter_var($gps, FILTER_SANITIZE_STRING);
            //
            $broker = mysqli_real_escape_string($link, $_REQUEST['broker']);
            $broker = filter_var($broker, FILTER_SANITIZE_STRING);
            //
            $orthomap = mysqli_real_escape_string($link, $_REQUEST['orthomap']);
            //$orthomap = filter_var($orthomap, FILTER_SANITIZE_STRING);
            $orthomap = json_encode($orthomap);
            
            $orion = mysqli_real_escape_string($link, $_REQUEST['orion']);
            $orion = filter_var($orion, FILTER_SANITIZE_STRING);
            
            $kbIP = mysqli_real_escape_string($link, $_REQUEST['kbIP']);
            $kbIP = filter_var($kbIP, FILTER_SANITIZE_STRING);
            
            //
    //INSERT LDAP
            $query = "INSERT INTO Organizations (organizationName, kbUrl, zoomLevel, drupalUrl, orgUrl, welcomeUrl, gpsCentreLatLng, broker, orthomapJson, orionIP, kbIP)
                         VALUES ('" . $name . "', '" . $kb . "', '" . $zoom . "', '" . $drupal . "', '" . $org . "', '" . $welc . "','" . $gps . "','".$broker."',".$orthomap.",'".$orion."', '".$kbIP."')";
            $result = mysqli_query($link, $query);
            if ($result) {
                echo('ok');
            } else {
                echo('ko');
            }

//ldapOrganization
            if ($ldapOrganization == true) {
                $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                if ($connection) {
                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
                    $resultldap = ldap_search($connection, $ldapBaseDN, '(cn=Dashboard)');
                    $entries = ldap_get_entries($connection, $resultldap);
                    ///
                    $resultldapOrg = ldap_search($connection, $ldapBaseDN, '(objectClass=organizationalUnit)');
                    $entriesOrg = ldap_get_entries($connection, $resultldapOrg);
                    $array_org = array();
                    //
                    $lenght = $entriesOrg["count"];
                    for ($i = 0; $i < $lenght; $i++) {
                        $org = strval($entriesOrg[$i]['ou'][0]);
                        $org01 = explode('cn=', $org);
                        $org02 = explode(',dc=', $org01[1]);
                        $final = $org02[0];
                        $array_org[$i] = $org;
                    }


                    if (in_array($name, $array_org)) {
                        echo "Yet existing orgnazition in LDAP";
                    } else {
                        //echo ('Creation');
                        // $new_ou = "ou=" . $name . "," . $ldapBaseDN . "";
                        //echo($new_ou);
                        $newou = array();
                        $newou["objectClass"][0] = "top";
                        $newou["objectClass"][1] = "organizationalUnit";
                        $newou["ou"] = $name;
                        //
                        $create_ou = ldap_add($connection, "ou=" . $name . "," . $ldapBaseDN . "", $newou);
                    }
                    ldap_close($connection);
                    //
                    ////////////
                } else {
                    
                }
            }
            //
        }
    } else if ($action == 'delete_data') {
        if ($role_session_active == 'RootAdmin') {
            $id = mysqli_real_escape_string($link, $_REQUEST['id']);
            $id = filter_var($id, FILTER_SANITIZE_STRING);

            $name = mysqli_real_escape_string($link, $_REQUEST['name']);
            $name = filter_var($name, FILTER_SANITIZE_STRING);

            $query = "DELETE FROM Organizations WHERE  id = " . $id;
            $result = mysqli_query($link, $query);
            ///////////////
            if ($ldapOrganization == true) {
                $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
                $resultldap = ldap_search($connection, $ldapBaseDN, '(cn=Dashboard)');
                $entries = ldap_get_entries($connection, $resultldap);
                //
                if ($connection) {
                    //echo('connected');
                    $delete_ou = ldap_delete($connection, "ou=" . $name . "," . $ldapBaseDN . "");
                    if ($delete_ou) {
                        // 
                    } else {
                        echo(ldap_error($connection));
                    }
                    //
                }
                ldap_close($connection);
            }
            /////////////////
            if ($result) {
                echo('ok');
            } else {
                echo('ko');
            }
        }
    } else if ($action == 'user_list') {
        $id = mysqli_real_escape_string($link, $_REQUEST['id']);
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        /////////
        $query = "SELECT users FROM Organizations WHERE id=" . $id . ";";
        $result = mysqli_query($link, $query);
        if ($result) {
            $dashboardParams = [];
            $arr = [];
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    //
                    $pieces = explode(",", $row['users']);
                    //
                    $arr['users'] = $pieces;
                    //
                    array_push($dashboardParams, $arr);
                    //
                }
            }
            echo json_encode($dashboardParams);
        }
        //////////////
    } else if ($action == 'add_user') {
        //nothing
        if ($role_session_active == 'RootAdmin') {
            $message = "";
            $id = mysqli_real_escape_string($link, $_REQUEST['id']);
            $id = filter_var($id, FILTER_SANITIZE_STRING);
            //
            $user = mysqli_real_escape_string($link, $_REQUEST['user']);
            $user = filter_var($user, FILTER_SANITIZE_STRING);

            $org = mysqli_real_escape_string($link, $_REQUEST['org']);
            $org = filter_var($org, FILTER_SANITIZE_STRING);

            //
            $array_org = array();
            //echo($guery);
            $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
            if ($connection) {
                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);

                $ldapUsername = "cn=" . $user . "," . $ldapBaseDN;
                $checkldap = (checkLdapMembership($connection, $ldapUsername, $ldapToolName, $ldapBaseDN));
                $organization = '-';
                if($checkldap) {
                    $organization = checkLdapOrganization($connection, $ldapUsername, $ldapBaseDN);
                    $checkldap = ($organization==$org);
                }
/*
                $resultldap = ldap_search($connection, $ldapBaseDN, '(cn=Dashboard)');
                $entries = ldap_get_entries($connection, $resultldap);
                ///
                $resultldapOrg = ldap_search($connection, $ldapBaseDN, '(objectClass=organizationalPerson)');
                $entriesOrg = ldap_get_entries($connection, $resultldapOrg);
                $array_org = array();
                $num = count($entriesOrg);
                $checkldap = false;
                for ($i = 0; $i < $num; $i++) {
                    $mystring = $entriesOrg[$i]['dn'];
                    if (strpos($mystring, $user)) {
                        $checkldap = true;
                    }
                }
                // var_dump($array_org);
*/
            }
            //
            if ($checkldap == true) {
                $curr_users = "";
                $query0 = "SELECT users FROM Organizations WHERE id=" . $id . ";";
                $result0 = mysqli_query($link, $query0);
                if (mysqli_num_rows($result0) > 0) {
                    while ($row0 = mysqli_fetch_assoc($result0)) {
                        //
                        $curr_users = $row0['users'];
                        //
                    }
                }
                if (strpos($curr_users, $user)) {
                    //
                    $user1 = $curr_users;
                } else {
                    if (($curr_users == "") || ($curr_users == null)) {
                        $user1 = $user;
                    } else {
                        $user1 = $curr_users . ',' . $user;
                    }
                }
                //$user1 = $user;
                $query = "UPDATE Organizations SET 
                    users = '" . $user1 . "'    
                WHERE id = " . $id;
                $result = mysqli_query($link, $query);
                //
                //
            $message = "User successfully added to organization";
                //
            } else {
                $message = "Username " . $user . " not correct org:$organization";
            }
        }
        echo($message);
        //
//
    } else if ($action == 'delete_list') {
        if ($role_session_active == 'RootAdmin') {
            $id = mysqli_real_escape_string($link, $_REQUEST['id']);
            $id = filter_var($id, FILTER_SANITIZE_STRING);
            //
            $user = mysqli_real_escape_string($link, $_REQUEST['user']);
            $user = filter_var($user, FILTER_SANITIZE_STRING);
            //
            $org = mysqli_real_escape_string($link, $_REQUEST['org']);
            $org = filter_var($user, FILTER_SANITIZE_STRING);
            //
            $curr_users = "";
            $query0 = "SELECT * FROM Organizations WHERE id=" . $id . ";";
            $result0 = mysqli_query($link, $query0);
            if (mysqli_num_rows($result0) > 0) {
                while ($row0 = mysqli_fetch_assoc($result0)) {
                    //
                    $curr_users = $row0['users'];
                    //
                }
            }
            $user1 = str_replace($user, '', $curr_users);
            $user1 = str_replace(',,', ',', $user1);
            $last_letter = substr($user1, -1);
            $first_letter = substr($user1, 0, 1);
            if ($last_letter == ",") {
                $user1 = substr_replace($user1, "", -1);
            }
            if ($first_letter == ",") {
                $user1 = str_replace(",", "", $user1);
            }
            //
            if ($user1 == "") {
                $user1 = null;
            }
            //
            $query = "UPDATE Organizations SET users = '" . $user1 . "'     WHERE id = " . $id;
            $result = mysqli_query($link, $query);
            //
            $message = "User successfully deleted by organization";

            echo($message);
            //
        } else {
            
        }
    } else if ($action == 'select_data') {
        if ($role_session_active == 'RootAdmin') {
        $id=$_REQUEST['id'];
        $query = "SELECT * FROM Organizations WHERE id='".$id."';";
        $result = mysqli_query($link, $query);
        if ($result) {
            $dashboardParams = [];
            $arr = [];
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $arr['id'] = $row['id'];
                    $arr['organizationName'] = $row['organizationName'];
                    $arr['kbUrl'] = $row['kbUrl'];
                    $arr['gpsCentreLatLng'] = $row['gpsCentreLatLng'];
                    $arr['zoomLevel'] = $row['zoomLevel'];
                    $arr['lang'] = $row['lang'];
                    if ($row['drupalUrl'] == null) {
                        $arr['drupalUrl'] = '';
                    } else {
                        $arr['drupalUrl'] = $row['drupalUrl'];
                    }
                    if ($row['orgUrl'] == null) {
                        $arr['orgUrl'] = '';
                    } else {
                        $arr['orgUrl'] = $row['orgUrl'];
                    }
                    if ($row['welcomeUrl'] == null) {
                        $arr['welcomeUrl'] = '';
                    } else {
                        $arr['welcomeUrl'] = $row['welcomeUrl'];
                    }
                    if ($row['users'] == null) {
                        $arr['users'] = '';
                    } else {
                        $arr['users'] = $row['users'];
                    }
                    if ($row['broker'] == null) {
                        $arr['broker'] = '';
                    } else {
                        $arr['broker'] = $row['broker'];
                    }
                    if ($row['orionIP'] == null) {
                        $arr['orionIP'] = '';
                    } else {
                        $arr['orionIP'] = $row['orionIP'];
                    }
                    if ($row['orthomapJson'] == null) {
                        $arr['orthomapJson'] = '';
                    } else {
                        $arr['orthomapJson'] = $row['orthomapJson'];
                    }
                    if ($row['kbIP'] == null) {
                        $arr['kbIP'] = '';
                    } else {
                        $arr['kbIP'] = $row['kbIP'];
                    }

                    //
                    array_push($dashboardParams, $arr);
                    //
                }
            }
            echo json_encode($dashboardParams);
        }
        }
    } else {
        
    }
}
?>
