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

include '../config.php';
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

session_start();
if (isset($_SESSION['loggedUsername'])) {
    /*     * ************** */
    if (isset($_SESSION['refreshToken'])) {
        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
        $accessToken = $tkn->access_token;
        $_SESSION['refreshToken'] = $tkn->refresh_token;

        //error_reporting(E_ERROR);
$link = mysqli_connect($host, $username, $password);
        //error_reporting(E_ALL);
        //ini_set('display_errors', 1);
        //error_reporting(-1);
        mysqli_select_db($link, $dbname);
        if (isset($_SESSION['loggedRole'])) {
            $role_session_active = $_SESSION['loggedRole'];

      
            if ($role_session_active == "RootAdmin") {

                function hash_password($password) { // SSHA with random 4-character salt
                    $salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 4)), 0, 4);
                    return '{SSHA}' . base64_encode(sha1($password . $salt, TRUE) . $salt);
                }

                function control_mail($ldapServer, $ldapPort, $ldapBaseDN, $mail) {
                    $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                    $attr = array('mail');
                    //$mail
                    $filter = "(mail=" . $mail . ")";
                    //$attr['mail'] =  $mail;
                    $resultldap = ldap_search($connection, $ldapBaseDN, $filter, $attr);
    $entries = ldap_get_entries($connection, $resultldap);
                    $count = $entries["count"];
                    $control_mail = $count;
    
                    return $control_mail;
	}

                $link = mysqli_connect($host, $username, $password);


                $action = $_REQUEST['action'];
                if ($action == 'get_list') {

                    //
                    //
                    //LISTA DEGLI UTENTI
                    $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
                    $resultldap = ldap_search($connection, $ldapBaseDN, '(cn=Dashboard)');
                    $entries = ldap_get_entries($connection, $resultldap);


                    //
                    $list_users = $entries[0]['memberuid'];

                    //LISTA DELLE ORGANIZZAZIONI
                    $resultldapOrg = ldap_search($connection, $ldapBaseDN, '(objectClass=organizationalUnit)');
                    $entriesOrg = ldap_get_entries($connection, $resultldapOrg);
                    //echo($list_org);
                    //
      //
     //LISTA DEI RUOLI ToolAdmin
                    $resultldapToolAdmin = ldap_search($connection, $ldapBaseDN, '(cn=ToolAdmin)');
                    $entriesToolAdmin = ldap_get_entries($connection, $resultldapToolAdmin);
                    $array_toolAdmin = array_map('strtolower', $entriesToolAdmin[0]['roleoccupant']);
                    //
                    //
     //
      $resultldapRootAdmin = ldap_search($connection, $ldapBaseDN, '(cn=RootAdmin)');
                    $entriesRootAdmin = ldap_get_entries($connection, $resultldapRootAdmin);
                    $array_RootAdmin = array_map('strtolower', $entriesRootAdmin[0]['roleoccupant']);
                    //
                    //
     //
      $resultldapManager = ldap_search($connection, $ldapBaseDN, '(cn=Manager)');
                    $entriesManager = ldap_get_entries($connection, $resultldapManager);
                    $array_manager = array_map('strtolower', $entriesManager[0]['roleoccupant']);
                    //
                    //
     //
     $resultldapAreaManager = ldap_search($connection, $ldapBaseDN, '(cn=AreaManager)');
                    $entriesAreaManager = ldap_get_entries($connection, $resultldapAreaManager);
                    $array_AreaManger = array_map('strtolower', $entriesAreaManager[0]['roleoccupant']);
//
    $resultldapObserver = ldap_search($connection, $ldapBaseDN, '(cn=Observer)');
                    $entriesObserver = ldap_get_entries($connection, $resultldapObserver);
                    $array_Observer = array_map('strtolower', $entriesObserver[0]['roleoccupant']);
                    //
                    //
     
    $array_users = array();
                    //
                    $lenght = $list_users["count"];
                    for ($i = 0; $i < $lenght; $i++) {
                        $user = strval($list_users[$i]);
                        $pass = "";
                        //
                        //
        $rest01 = explode('cn=', $user);
                        $rest02 = explode(',dc=', $rest01[1]);
                        $final_username = $rest02[0];
                        //
                        //
        $resultldap0 = ldap_search($connection, $ldapBaseDN, '(cn=' . $final_username . ')');
                        $entries0 = ldap_get_entries($connection, $resultldap0);
                        if (isset($entries0[0]['mail'][0])) {
                            $pass = $entries0[0]['mail'][0];
        	}
                        //
                        //
                        $org = null;
                        $role = null;
                        //
                        $lenghtOrg = $entriesOrg['count'];
                        //
                        for ($y = 0; $y < $lenghtOrg; $y++) {
                            $array_ut = $entriesOrg[$y]['l'];
                            $user_min = strtolower($user);
                            if (in_array($user, $array_ut)) {
                                $org = $entriesOrg[$y]['ou'][0];
	}	
                        }
                        //
                        //RUOLO
                        if (in_array($user, $array_toolAdmin)) {
                            $role = 'ToolAdmin';
                        }
                        if (in_array($user, $array_RootAdmin)) {
                            $role = 'RootAdmin';
                        }
                        if (in_array($user, $array_manager)) {
                            $role = 'Manager';
                        }
                        if (in_array($user, $array_AreaManger)) {
                            $role = 'AreaManager';
                        }
                        if (in_array($user, $array_Observer)) {
                            $role = 'Observer';
                        }
                        //
                        $array_users[$i]["IdUser"] = strval($i);
                        $array_users[$i]["username"] = $final_username;
                        $array_users[$i]["organization"] = $org;
                        $array_users[$i]["status"] = null;
                        $array_users[$i]["reg_data"] = null;
                        $array_users[$i]["password"] = null;
                        $array_users[$i]["mail"] = $pass;
                        $array_users[$i]["admin"] = $role;
                        $array_users[$i]["cn"] = $list_users[$i];

                        //
                    }

                    echo json_encode($array_users);
                    /*                     * ** */
                } else if ($action == 'add_user') {
                    //ADD USER
                    $results = array();
                    //
                    //***************************//
                    //$ldapAdminDN = $ldapAdminDN;
                    //error_reporting(E_ERROR | E_PARSE);
                    //
                    //LISTA DEGLI UTENTI
                    $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd)or die("ERROR IN BIND");
                    //**************************//
                    if (isset($_POST['new_username']) && ($_POST['new_username'] != "")) {
                        $new_username = htmlspecialchars($_POST['new_username']);
                        $newPassw = htmlspecialchars($_POST['new_password']);
                        $mail = htmlspecialchars($_POST['new_email']);
                        $org = htmlspecialchars($_POST['org']);
                        $new_userType = htmlspecialchars($_POST['new_userType']);
                        if (($new_userType == "") || ($new_userType == null)) {
                            $new_userType = "Manager";
                        }
                        //
                        $uid = encryptOSSL($new_username, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
                        //$ldapBaseDN = $ldapBaseDN;
                        $data = array();
                        $data['objectClass'][0] = 'inetOrgPerson';
                        $data['sn'] = strtolower($new_username);
                        $data['userPassword'] = hash_password($newPassw);
                        $data['uid']= $uid;
                        $data['ou']=  $org;

                        $control_mail = control_mail($ldapServer, $ldapPort, $ldapBaseDN, $mail);
                        if ($control_mail == 0) {
                            if (!ldap_add($connection, "cn=" . strtolower($new_username) . "," . $ldapBaseDN . "", $data)) {

                                $results['result'] = 'error';
                                echo json_encode($results);
                                //
                            } else {
                                //AGGIUNGI PARAMTERI
                                $serverctrls02['mail'] = $mail;
                                //
                                $dn = "cn=" . strtolower($new_username) . "," . $ldapBaseDN . "";
                                ldap_mod_add($connection, "cn=" . $new_userType . "," . $ldapBaseDN . "", array('roleOccupant' => $dn));
                                ///
                                $array_group = explode(',', $ldapToolGroups);
                                foreach ($array_group as $value_group) {
                                    ldap_mod_add($connection, "cn=" . $value_group . "," . $ldapBaseDN . "", array('memberUid' => $dn));
                                }
                                //
                                ldap_mod_add($connection, "ou=" . $org . "," . $ldapBaseDN . "", array('l' => $dn));
                                ldap_mod_replace($connection, $dn, $serverctrls02);
                                //$mail
                                $results['result'] = 'success';
                                echo json_encode($results);
                                /////
                            }
                        } else {
                            $results['result'] = 'not data';
                            echo json_encode($results);
                        }
                        //**************************//
                    } else {
                        $results['result'] = 'password yet used';
                        echo json_encode($results);
                    }
                } else if ($action == 'edit user') {
                    //
                    //error_reporting(E_ERROR);
                    //LISTA DEGLI UTENTI
                    $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
                    $data_2 = array();

                    $values = "";
                    $new_username = htmlspecialchars($_POST['user']);
                    $result = array();
                    //if(isset($_POST['user'])){
                    $result['index'] = 1;
                    $result['password'] = "not modified";
                    //echo($new_username);
                    $dn = "cn=" . strtolower($new_username) . "," . $ldapBaseDN;
                    $dn_role = "cn=" . $new_username . "," . $ldapBaseDN;
                    //
                    //
                    //MODIFY UID
                    $new_uid = encryptOSSL($new_username, $encryptionInitKey, $encryptionIvKey, $encryptionMethod);
                    $serverctrls['uid'] = $new_uid;
                    if (isset($_POST['org'])) {
                        $new_org = htmlspecialchars($_POST['org']);
                        $serverctrls['ou'] = $new_org;
                    }
                    ///////////
                    $del_uid = ldap_mod_replace($connection, $dn, $serverctrls);
                     if ($del_uid) {
                                        $result['uid'] = "OK";
                                        $result['index'] = 1;
                                } else {
                                    $result['uid'] = "error during uid or ou attribute modifying";
                                    $result['index'] = 0;
                                }
                    ////
                    //
                    //ROLE
                    if ((isset($_POST['role'])) && (isset($_POST['old_role']))) {
                        $old_userType = htmlspecialchars($_POST['old_role']);
                        $new_userType = htmlspecialchars($_POST['role']);
                        if ($new_userType !== $old_userType) {

                            if ($old_userType !== '-') {
                                $del = ldap_mod_del($connection, "cn=" . $old_userType . "," . $ldapBaseDN, array('roleOccupant' => $dn_role));
                            } else {
                                $del = true;
                            }
                            if ($del) {
                                $add = ldap_mod_add($connection, "cn=" . $new_userType . "," . $ldapBaseDN, array('roleOccupant' => $dn_role));
                                if ($add) {

                                    $result['role'] = "OK";
                                    $result['index'] = 1;
                                } else {

                                    $result['role'] = "error modify role";
                                    $result['index'] = 0;
                                }
                            } else {

                                $result['role'] = "error deleting old role";
                                $result['index'] = 0;
                            }
                        } else {

                            $result['role'] = "not modified";
                        }
                    }
                    //
                    //PASSWORD
                    //password
                    if ((isset($_POST['password'])) && (isset($_POST['old_pass']))) {

                        //
                        if (($_POST['password'] != "") && ($_POST['password'] != null) && ($_POST['old_pass'] != "") && ($_POST['old_pass'] != null)) {
                            if ($_POST['old_pass'] == $_POST['password']) {

                                $old_pass = hash_password($_POST['old_pass']);
                                $new_pass = hash_password($_POST['password']);
                                $serverctrls = array();
                                $serverctrls['userPassword'] = $new_pass;
                                //
                                $pass = ldap_mod_replace($connection, $dn, $serverctrls);
                                if ($pass) {
                                    $result['password'] = 'OK';
                                    $result['index'] = 1;
                                } else {
                                    $result['password'] = 'Error during password creation';
                                    $result['index'] = 0;
                                }
                                ////
                            } else {
                                $result['password'] = 'Password not correct';
                                $result['index'] = 0;
                            }
                        } else {
                            $result['password'] = 'not modified';
                        }
                    }
                    //
                    //ORG
                    if ((isset($_POST['org'])) && (isset($_POST['old_org']))) {
                        $old_org = htmlspecialchars($_POST['old_org']);
                        $new_org = htmlspecialchars($_POST['org']);
                        if (($old_org == '-') || ($old_org == '') || ($old_org == null)) {
                            $add_org = ldap_mod_add($connection, "ou=" . $new_org . "," . $ldapBaseDN . "", array('l' => $dn));
                            if ($add_org) {

                                $result['org'] = "OK";
                                $result['index'] = 1;
                            } else {

                                $result['org'] = "error during updating new organization";
                                $result['index'] = 0;
                            }
                        } else {
                            if ($new_org !== $old_org) {
                                $del_org = ldap_mod_del($connection, "ou=" . $old_org . "," . $ldapBaseDN . "", array('l' => $dn));
                                if ($del_org) {
                                    $add_org = ldap_mod_add($connection, "ou=" . $new_org . "," . $ldapBaseDN . "", array('l' => $dn));
                                    if ($add_org) {

                                        $result['org'] = "OK";
                                        $result['index'] = 1;
                                    } else {

                                        $result['org'] = "error during updating new organization";
                                        $result['index'] = 0;
                                    }
                                } else {

                                    $result['org'] = "error during deleting old organization";
                                    $result['index'] = 0;
                                }
                            }
                        }
                    } else {
                        $result['org'] = "not modified";
                    }
                    //
                    //GROUP
                    if ((isset($_POST['mail'])) && (isset($_POST['old_mail']))) {
                        $old_ldapMail = htmlspecialchars($_POST['old_mail']);
                        $new_ldapMail = htmlspecialchars($_POST['mail']);
                        if ($new_ldapMail !== $old_ldapMail) {
                            //$mod = ldap_mod_replace;
                            $control_mail = control_mail($ldapServer, $ldapPort, $ldapBaseDN, $new_ldapMail);
                            if ($control_mail == 0) {
                                $serverctrls02['mail'] = $new_ldapMail;
                                $mail_mod = ldap_mod_replace($connection, $dn, $serverctrls02);
                                if ($mail_mod) {
                                    $result['mail'] = 'OK';
                                    $result['index'] = 1;
                                } else {
                                    $result['mail'] = 'error';
                                    $result['index'] = 0;
                                }
                            } else {
                                $result['mail'] = 'Mail yet used';
                                $result['index'] = 0;
                            }
                        } else {
                            $result['mail'] = 'not modified';
                        }
                    } else {
                        //echo('ERROR CAMBIO MAIL');
                    }
                    
    echo json_encode($result);
                    //
                } else if ($action == 'list_org') {
                    //
                    error_reporting(E_ERROR);
                    //LISTA DEGLI UTENTI
                    $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);
                    $resultldap = ldap_search($connection, $ldapBaseDN, '(cn=Dashboard)');
                    $resultldapOrg = ldap_search($connection, $ldapBaseDN, '(objectClass=organizationalUnit)');
                    $entriesOrg = ldap_get_entries($connection, $resultldapOrg);
                    ///
                    $array_org = array();
                    //
                    $lenght = $entriesOrg["count"];
                    for ($i = 0; $i < $lenght; $i++) {
                        $org = strval($entriesOrg[$i]['ou'][0]);
                        //
                        $org01 = explode('cn=', $org);
                        $org02 = explode(',dc=', $org01[1]);
                        $final = $org02[0];
                        $array_org[$i] = $org;
}

                    ////
                    echo json_encode($array_org);
                    //*******//
                } else if ($action == 'delete_user') {
                    //
                    //error_reporting(E_ERROR | E_PARSE);
                    //
                    $results = array();
                    $connection = ldap_connect($ldapServer, $ldapPort)or die("That LDAP-URI was not parseable");
                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $bind = ldap_bind($connection, $ldapAdminDN, $ldapAdminPwd);

                    if (isset($_POST['username'])) {
                        $username = htmlspecialchars($_POST['username']);
                        //
                        $role = htmlspecialchars($_POST['role']);
                        $org = htmlspecialchars($_POST['org']);
                        $dn = "cn=" . strtolower($username) . "," . $ldapBaseDN;
                        $dn_role = "cn=" . $username . "," . $ldapBaseDN;

                        if ($role != "-") {
                            $del_role = ldap_mod_del($connection, "cn=" . $role . "," . $ldapBaseDN, array('roleOccupant' => $dn_role));
                            if ($del_role) {
                                $results['role'] = 'success';
                            } else {
                                $results['role'] = 'failure';
                            }
                        }

                        if ($org != "-") {
                            $del_org = ldap_mod_del($connection, "ou=" . $org . "," . $ldapBaseDN, array('l' => $dn));
                            if ($del_org) {
                                $results['org'] = 'success';
                            } else {
                                $results['org'] = 'failure';
                            }
                        }

                        $array_group = explode(',', $ldapToolGroups);
                        foreach ($array_group as $value_group) {
                            $del_dash = ldap_mod_del($connection, "cn=" . $value_group . "," . $ldapBaseDN, array('memberUid' => $dn));
                            if ($del_dash) {
                                $results['dash'] = 'success';
                            } else {
                                $results['dash'] = 'failure';
                            }
                        }
                        $result = ldap_delete($connection, $dn);
                        if ($result) {
                            //
                            //return result code, if delete fails
                            $results['result'] = 'success';
                        } else {
                            $results['result'] = 'error';
                        }
                    }
                    echo json_encode($results);
                } else {
                    exit();
                }
            } else {
                echo ("You are not authorized to access ot this data!");
                exit;
            }
        } else {
            echo ("You are not authorized to access ot this data!");
            exit;
        }
    } else {
        echo ("You are not authorized to access ot this data!");
        exit;
    }
} else {
    echo ("You are not authorized to access ot this data!");
    exit;
}
