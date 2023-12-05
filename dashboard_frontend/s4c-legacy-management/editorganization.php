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
//////
$name = 'Organization';
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
        $final_array = array();
        //
        $lenght = $entriesOrg["count"];
        for ($i = 0; $i < $lenght; $i++) {
            $org = strval($entriesOrg[$i]['ou'][0]);
            $org01 = explode('cn=', $org);
            $org02 = explode(',dc=', $org01[1]);
            $final = $org02[0];
            $array_org[$i]= $org;
        }
        $jsonEntry= json_encode($entriesOrg);
        //echo($jsonEntry);
        $entry_l = $entriesOrg['count'];
        //echo($entry_l);
        for ($z = 0; $z < $entry_l; $z++) {
            $row['org'] = $entriesOrg[$z]['ou'][0];
            $row['users']= '';
            $row['dtUsers']= '';
            $user_arr = array();
            //var_dump($entriesOrg[$z]['l']);
            if($entriesOrg[$z]['l']){
                $num = $entriesOrg[$z]['l']['count'];
                $ind = 0;
                for($z1 = 0; $z1 < $num; $z1++) {
                    if ($entriesOrg[$z]['l'][$z1]){
                        $us1 = strval($entriesOrg[$z]['l'][$z1]);
                        $orgZ1 = explode('cn=', $us1);
                        $orgZ2 = explode(',dc=', $orgZ1[1]);
                       // $array_org[$ind]=  $orgZ2[0];
                        $user_arr[$ind]=  $orgZ2[0];
                        $ind++;
                    }
                    
                }
                $row['users']= json_encode($user_arr);
                $curr_users="";
                //
                $queyOrg = "SELECT users FROM Organizations WHERE organizationName ='".$entriesOrg[$z]['ou'][0]."'";
                $resultOrg = mysqli_query($link, $queyOrg);
                if (mysqli_num_rows($resultOrg) > 0) {
                    while ($row0 = mysqli_fetch_assoc($resultOrg)) {
                        //
                        if(($row0['users'] !="")&&($row0['users'] != null)){
                        $curr_users = $row0['users'];
                        }else{
                            $curr_users = '';
                        };
                        //
                    }
                }
                //
                $row['dtUsers']= $curr_users;
                //$row['users']= json_encode($entriesOrg[$z]['l']);
            }
           // $row['users']= json_encode($entriesOrg[$z]['l']);
            array_push($final_array, $row);

        }
        echo json_encode($final_array);
       /* $lenght1 = count($entriesOrg);
        for($y = 0; $y < $lenght1; $y++){
            var_dump($entriesOrg[$y]['ou']);
            $row['org'] = $entriesOrg[$y]['ou'];
        }*/
        //echo json_encode($array_org);
       // var_dump($array_org);


        if (in_array($name, $array_org)) {
           // echo "Yet existing orgnazition in LDAP";
            //
            //$row['org'] =  $array_org[$y];
            //echo json_encode($array_org);
            $lenght1 = count($array_org);

        for ($y = 0; $y < $lenght1; $y++) {
            $row['org'] = $array_org[$y];
           // echo($row['org']);
            $searchFilter = "(&(ou=".$array_org[$y].")(objectClass=person))"; 
            $resultldapUsers = ldap_search($connection, $ldapBaseDN, $searchFilter);
            $entriesUsers = ldap_get_entries($connection, $resultldapUsers);
            $array_uesrs = array();
            //echo json_encode($entriesUsers);
            $row['users']= $entriesUsers;
            $count = $entriesUsers["count"];
            for($z = 0; $z < $count; $z++) {
                $us1 = strval($entriesUsers[$z]['cn'][0]);
                $array_org[$z]= $us1;
            }
            $row['users']= json_encode($array_org);
            //
            $row['dtUsers']=0;
            //
            array_push($final_array, $row);
        }
        //echo json_encode($final_array);
            //var_dump($array_org);
            //
        } else {
            //echo ('Creation');
            // $new_ou = "ou=" . $name . "," . $ldapBaseDN . "";
            //echo($new_ou);
            //
            //$create_ou = ldap_add($connection, "ou=" . $name . "," . $ldapBaseDN . "", $newou);
        }
        ldap_close($connection);
        //
        ////////////
    } else {
        echo ('NOT CONNCTION');
        
    }
///////////////
      
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
         //   if ($ldapOrganization == true) {
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
                        //$create_ou = ldap_add($connection, "ou=" . $name . "," . $ldapBaseDN . "", $newou);
                        //
                        if (ldap_add($connection, "ou=" . $name . "," . $ldapBaseDN . "", $newou)) {
                           // echo "Created ou";
                        } else {
                            $ldapErrorNo = ldap_errno($connection);
                            $ldapErrorStr = ldap_err2str($ldapErrorNo);
                            echo "Error creation (Code: $ldapErrorNo, Message: $ldapErrorStr).";
                        }
                        //
                    }
                    ldap_close($connection);
                    //
                    ////////////
                } else {
                    
                }
          //  }
            //
        }
    } else if ($action == 'delete_data') {
        if ($role_session_active == 'RootAdmin') {
            $id = mysqli_real_escape_string($link, $_REQUEST['id']);
            $id = filter_var($id, FILTER_SANITIZE_STRING);

            $name = mysqli_real_escape_string($link, $_REQUEST['name']);
            $name = filter_var($name, FILTER_SANITIZE_STRING);

            $query = "DELETE FROM Organizations WHERE  organizationName = '" . $name."'";
            $result = mysqli_query($link, $query);
            if ($result) {
            ///////////////
           // echo('ldapOrganization: '.$ldapOrganization);
            //if ($ldapOrganization == true) {
                $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
                $resultldap = ldap_search($connection, $ldapBaseDN, '(cn=Dashboard)');
                $entries = ldap_get_entries($connection, $resultldap);
                //
                if ($connection) {
                    //echo("ou=" . $name . "," . $ldapBaseDN . "");
                    //ou=New_org,dc=ldap,dc=organization,dc=com
                    //ou=New_org,dc=ldap,dc=organization,dc=com
                    $org_del = "ou=" . $name . "," . $ldapBaseDN;
                    ////////////////////
                    $groups = [];
                    $organization = $name;
                   // $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                   // if ($connection) {
                        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                        $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
        
                        $searchFilter = "(objectClass=*)"; // Filtra gli elementi con OU 'Organization' e di classe 'person'
                         $searchResult = ldap_search($connection, $ldapBaseDN, '(objectClass=*)');
                         $entriesOrg = ldap_get_entries($connection, $searchResult);
                         //echo json_encode($entriesOrg);
                        if ($entriesOrg){
        
                            $lenght = $entriesOrg["count"];
                            
                            for ($i = 0; $i < $lenght; $i++) {
                                $org = $entriesOrg[$i];
                                if($org['member']){
                                   // echo($org['ou']['0']);
                                   if ($org['ou']['0'] == $organization){
                                    $curr_group = $org['cn']['0'];
                                    $entryToDeleteDn = 'cn='.$curr_group.',ou='.$organization.',dc=ldap,dc=organization,dc=com';
                                    $delete_ou = ldap_delete($connection, $entryToDeleteDn);
                                    //array_push($groups, $org['cn']['0']);
                                   }
                                }
                                ////////
                               
                            }
                            //echo json_encode($groups);
                        }
                    //////////////////////////////////
                    $delete_ou = ldap_delete($connection, $org_del);
                    if ($delete_ou) {
                        // 
                        echo('ok');
                    } else {
                        //echo(ldap_error($connection));
                        $ldapErrorNo = ldap_errno($connection);
                        $ldapErrorStr = ldap_err2str($ldapErrorNo);
                        echo "Errore nella cancellazione dell'entry (Codice: $ldapErrorNo, Messaggio: $ldapErrorStr).";
                        //echo('ko');
                    }
                    //
                }
                ldap_close($connection);
            }else{
                echo ("Error during deletion!");
            }
            /////////////////
            /*if ($result) {
                echo('ok');
            } else {
                echo('ko');
            }*/
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
        //if ($role_session_active == 'RootAdmin') {
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
                if($bind){
                    $ldapUsername = "cn=" . $user . "," . $ldapBaseDN;
                    //echo('ldapUsername: '.$ldapUsername);
                   // $checkldap = (checkLdapMembership($connection, $ldapUsername, $ldapToolName, $ldapBaseDN));
                    //if($checkldap) {
                    $add_org = ldap_mod_add($connection, "ou=" . $org . "," . $ldapBaseDN . "", array('l' => $ldapUsername));
                            if ($add_org) {

                               // $result['org'] = "OK";
                               // $result['index'] = 1;
                               $message = "User successfully added to organization";
                            } else {
                                $message = "error during updating new organization";
                                //$result['org'] = "error during updating new organization";
                                //$result['index'] = 0;
                            }
                   // }
                }
                echo($message);
/*
                $ldapUsername = "cn=" . $user . "," . $ldapBaseDN;
                $checkldap = (checkLdapMembership($connection, $ldapUsername, $ldapToolName, $ldapBaseDN));
                $organization = '-';
                if($checkldap) {
                    $organization = checkLdapOrganization($connection, $ldapUsername, $ldapBaseDN);
                    $checkldap = ($organization==$org);
                }

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
            //}
            //
           /* if ($checkldap == true) {
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
                $message = "User " . $user . " Yet assigned to organization  $organization";
            }*/
        }
       
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
            $org = filter_var($org, FILTER_SANITIZE_STRING);
            //
            $curr_users = "";
            //$query0 = "SELECT * FROM Organizations WHERE id=" . $id . ";";
            $query0 = "SELECT * FROM Organizations WHERE organizationName=" . $org . ";";
            $result0 = mysqli_query($link, $query0);
            if (mysqli_num_rows($result0) > 0) {
                while ($row0 = mysqli_fetch_assoc($result0)) {
                    //
                    $curr_users = $row0['users'];
                    echo($row0['users']);
                    //
                }
            }
            $user1 = str_replace($user, '', $curr_users);
            //
            echo($curr_users);
            //
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
            $query = "UPDATE Organizations SET users = '" . $user1 . "'     WHERE organizationName = " . $org;
            $result = mysqli_query($link, $query);
            ////GESTIRE ELIMIAZIONE UTENTE DA LDAP/////
                ////////
                $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                if ($connection) {
                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
                    ////
                    if ($bind) {
                        //
                        $username = $user;
                        $groupCn = $org;
                        $ou = "ou=$org,dc=ldap,dc=organization,dc=com";
                        $dn = "cn=" . strtolower($username) . "," . $ldapBaseDN;
                        //ou=Organization,dc=ldap,dc=organization,dc=com
                        $del_org = ldap_mod_del($connection, $ou, array('l' => $dn));
                        if($del_org){
                            $message = "User successfully deleted by organization";
                    }else{
                        $ldapErrorNo = ldap_errno($connection);
                        $ldapErrorStr = ldap_err2str($ldapErrorNo);
                        echo "Error during deleting (Code: $ldapErrorNo, Messagge: $ldapErrorStr).";
                    }
                        ///
                        //
                        ////
                    }
                    //
                    //$message = "User successfully deleted by organization";
                    ////
                }else{
                ///////
                $message = "Error during deletion";
                }
            //////////
            echo($message);
            //
        } else {
            
        }
    } else if ($action == 'select_data') {
        if ($role_session_active == 'RootAdmin') {
        $id=$_REQUEST['id'];
        //$query = "SELECT * FROM Organizations WHERE id='".$id."';";
        $query = "SELECT * FROM Organizations WHERE organizationName='".$id."';";
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
    } else if($action == 'add_group'){
        $name = mysqli_real_escape_string($link, $_REQUEST['group']);
        $organization = mysqli_real_escape_string($link, $_REQUEST['organization']);
            $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
            if ($connection) {
                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);

                        $org2 = strtolower($organization);
                        $newEntryDn = "cn=userrootadmin,dc=ldap,dc=organization,dc=com"; 
                        //
                        $newou = array();
                        //$newou["dn"] =  "cn=$name,ou=$organization,dc=ldap,dc=$org2,dc=com";
                        $newou["member"] =  $newEntryDn;
                        $newou["objectClass"][0] = "groupOfNames";
                        $newou["objectClass"][1] = "top";
                        //$newou["objectClass"]= "groupOfNames";
                        $newou["ou"] = $organization;
                        $newou["cn"] = $name;

                        $newEntry = json_encode($newou);

                        $dn = "cn=$name,ou=$organization,dc=ldap,dc=organization,dc=com";
                        //
                       //echo($newEntry);
                        //$create_ou = ldap_add($connection, $ldapAdminDN, $newEntry);

                        if (ldap_add($connection, $dn, $newou)){
                            echo('Created');
                        }else{
                          //echo "Errore nella creazione del child: " . ldap_error($connection);
                          $ldapErrorNo = ldap_errno($connection);
                          $ldapErrorStr = ldap_err2str($ldapErrorNo);
                          echo($ldapErrorStr);
                        }
            }
    }else if($action =='get_gropus'){
        $groups = [];
            $organization = mysqli_real_escape_string($link, $_REQUEST['org']);
            $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
            if ($connection) {
                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);

                $searchFilter = "(objectClass=*)"; // Filtra gli elementi con OU 'Organization' e di classe 'person'
                 $searchResult = ldap_search($connection, $ldapBaseDN, '(objectClass=*)');
                 $entriesOrg = ldap_get_entries($connection, $searchResult);
                 //echo json_encode($entriesOrg);
                if ($entriesOrg){

                    $lenght = $entriesOrg["count"];
                    
                    for ($i = 0; $i < $lenght; $i++) {
                        $org = $entriesOrg[$i];
                        if($org['member']){
                           // echo($org['ou']['0']);
                           if ($org['ou']['0'] == $organization){
                            array_push($groups, $org['cn']['0']);
                           }
                        }
                        ////////
                       
                    }
                    echo json_encode($groups);

                    //echo json_encode($groups);
                }else{
                    //NOTIHING
                }
            }
    }else if($action == 'delete_group'){
        $name = mysqli_real_escape_string($link, $_REQUEST['group']);
        $organization = mysqli_real_escape_string($link, $_REQUEST['org']);
        $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
        $org2 = strtolower($organization);
        $entryToDeleteDn = 'cn='.$name.',ou='.$organization.',dc=ldap,dc='.$org2.',dc=com';
        //echo($entryToDeleteDn);
            if ($connection) {
                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                if (ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd)) {
                    // Prova a cancellare l'entry
                    if (ldap_delete($connection, $entryToDeleteDn)) {
                        echo "Group successfully deleted.";
                    } else {
                        $ldapErrorNo = ldap_errno($connection);
                        $ldapErrorStr = ldap_err2str($ldapErrorNo);
                        echo "Error during deleting (Code: $ldapErrorNo, Messagge: $ldapErrorStr).";
                    }
                } else {
                    echo "Error LDAP authentication: " . ldap_error($connection);
                }
            }

        }else if($action == 'edit_dtUsers'){
            $users = $_REQUEST['users'];
            $org = $_REQUEST['org'];
            $newUsers= implode(',', $users);
            //
            $query = "UPDATE Organizations SET users = '$newUsers' WHERE organizationName = '$org'";
            $result = mysqli_query($link, $query);
        if ($result) {
            echo "User list successfully modified";
        }else{
            echo "Error during operation";
        }
           
            //
           // echo json_encode($users);
        } else{
    
        }
}
?>
