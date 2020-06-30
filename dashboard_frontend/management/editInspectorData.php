<?php

include '../config.php';
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

session_start();
//if (isset($_SESSION['loggedUsername'])) {
    /*     * ************** */
    if (isset($_SESSION['refreshToken'])) {
        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
        $accessToken = $tkn->access_token;
        $_SESSION['refreshToken'] = $tkn->refresh_token;
        error_reporting(E_ERROR);
        //ini_set('display_errors', 1);
        //error_reporting(-1);
        $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
        mysqli_set_charset($link, 'utf8');
        mysqli_select_db($link, $dbname_processes);
        if (isset($_SESSION['loggedRole'])) {
            $role_session_active = $_SESSION['loggedRole'];
            //if ($role_session_active == "RootAdmin") {
            if ($role_session_active == "RootAdmin") {
                ////*********Modifica Root ********/////
                
                //
                $mod_lic =mysqli_real_escape_string($link,$_POST['mod_lic']);
                //
                //$mod_lic = $_POST['mod_lic'];
                $mod_prov0    = mysqli_real_escape_string($link,$_POST['mod_prov']);
                $mod_prov = filter_var($mod_prov0 , FILTER_SANITIZE_STRING);
                //
                $mod_add0    = mysqli_real_escape_string($link,$_POST['mod_add']);
                $mod_add = filter_var($mod_add0 , FILTER_SANITIZE_STRING);
                //$mod_prov = $_POST['mod_prov'];
                $mod_mail0    = mysqli_real_escape_string($link,$_POST['mod_mail']);
                $mod_mail = filter_var($mod_mail0 , FILTER_SANITIZE_STRING);
                //$mod_add = $_POST['mod_add'];
                //$mod_mail = $_POST['mod_mail'];
                 $mod_tel0    = mysqli_real_escape_string($link,$_POST['mod_tel']);
                 $mod_tel = filter_var( $mod_tel0 , FILTER_SANITIZE_STRING);
                //
                //$mod_tel = $_POST['mod_tel'];
                //$mod_web = $_POST['mod_web'];
                $mod_web0    = mysqli_real_escape_string($link,$_POST['mod_web']);
                $mod_web = filter_var($mod_web0 , FILTER_SANITIZE_STRING);
                //
                //$mod_ref = $_POST['mod_ref'];
                $mod_ref0    = mysqli_real_escape_string($link,$_POST['mod_ref']);
                $mod_ref  = filter_var($mod_ref0 , FILTER_SANITIZE_STRING);
                //
                //$id_mod = $_POST['id_mod'];
                $id_mod0    = mysqli_real_escape_string($link, $_POST['id_mod']);
                $id_mod = filter_var($id_mod0 , FILTER_SANITIZE_STRING);
                //
                //$id_row_hlt=$_POST['id_row_hlt'];
                $id_row_hlt0    = mysqli_real_escape_string($link, $_POST['id_row_hlt']);
                $id_row_hlt = filter_var($id_row_hlt0 , FILTER_SANITIZE_STRING);
                //
                //$id_hlt = $_POST['id_hlt'];
                $id_hlt0    = mysqli_real_escape_string($link, $_POST['id_hlt']);
                $id_hlt = filter_var($id_hlt0 , FILTER_SANITIZE_STRING);
                //
                if (($id_hlt == 'POI')||($id_hlt =='External Service')||($id_mod =='')||($id_mod =='ExternalContent')){
                    $id_mod = $id_row_hlt;
                }else{
                    //$id_mod = $_POST['id_mod'];
                    $id_mod0    = mysqli_real_escape_string($link,  $_POST['id_mod']);
                     $id_mod = filter_var($id_mod0 , FILTER_SANITIZE_STRING);
                    //
                }
                /////******************/////

                ///devices, process_manager_graph

                $query_search = "SELECT * FROM devices,process_manager_responsible WHERE devices.process = process_manager_responsible.process_name AND devices.device_name ='" . $id_mod . "';";
                $result_search = mysqli_query($link, $query_search) or die(mysqli_error($link));
                $total1 = $result_search->num_rows;
                if ($total1 > 0) {
                    ///
                    $query0 = "UPDATE process_manager_responsible, devices 
                            SET "
                            . "process_manager_responsible.licence ='" . $mod_lic . "', "
                            . "process_manager_responsible.responsible ='" . $mod_prov . "', "
                            . "process_manager_responsible.address ='" . $mod_add . "', "
                            . "process_manager_responsible.mail ='" . $mod_mail . "', "
                            . "process_manager_responsible.reference_person ='" . $mod_ref . "', "
                            . "process_manager_responsible.telephone ='" . $mod_tel . "', "
                            . "process_manager_responsible.webpage ='" . $mod_web . "' " .
                            "       WHERE devices.process = process_manager_responsible.process_name AND devices.device_name='" . $id_mod . "';";
                    echo($query0);
                    $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                    //print_r($result0);
                    header("location:inspector.php");
                    ////
                } else {
                    ////
                    //SEARCH device
                    $query_search_dev = "SELECT * FROM devices WHERE device_name='" . $id_mod . "';";
                    $result_search_dev = mysqli_query($link, $query_search_dev) or die(mysqli_error($link));
                    $total2 = $result_search_dev->num_rows;
                    if ($total2 > 0) {
                        ///
                        $update_device = "UPDATE devices SET process= '" . $id_mod . "' WHERE device_name='" . $id_mod . "';";
                        $result_device = mysqli_query($link, $update_device) or die(mysqli_error($link));
                        //if ($result_device) {
                        //CHECKIF responsible
                        $check_responsbile = "SELECT * FROM process_manager_responsible WHERE process_name='" . $id_mod . "'";
                        $result_check_responsbile = mysqli_query($link, $check_responsbile) or die(mysqli_error($link));
                        $total_check = $result_check_responsbile->num_rows;
                        if ($total_check > 0) {
                            $query0 = "UPDATE process_manager_responsible 
                            SET "
                                    . "process_manager_responsible.licence ='" . $mod_lic . "', "
                                    . "process_manager_responsible.responsible ='" . $mod_prov . "', "
                                    . "process_manager_responsible.address ='" . $mod_add . "', "
                                    . "process_manager_responsible.mail ='" . $mod_mail . "', "
                                    . "process_manager_responsible.reference_person ='" . $mod_ref . "', "
                                    . "process_manager_responsible.telephone ='" . $mod_tel . "', "
                                    . "process_manager_responsible.webpage ='" . $mod_web . "' " .
                                    "       WHERE process_manager_responsible.process_name='" . $id_mod . "';";
                            //echo($query0);
                            $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                        } else {
                            $update_licence = "INSERT INTO process_manager_responsible (process_name, licence, responsible, address, mail, reference_person, telephone, webpage) VALUES ('" . $id_mod . "','" . $mod_lic . "','" . $mod_prov . "', '" . $mod_add . "', '" . $mod_mail . "','" . $mod_ref . "','" . $mod_tel . "', '" . $mod_web . "')";
                            $result_licence = mysqli_query($link, $update_licence) or die(mysqli_error($link));
                        }
                        //
                        //CHECK sul graph
                        $check_graph = "SELECT * FROM process_manager_graph WHERE Process_name='" . $id_mod . "'";
                        $result_check_graph = mysqli_query($link, $check_graph) or die(mysqli_error($link));
                        $total_check_graph = $result_check_graph->num_rows;
                        if ($total_check_graph > 0) {
                            //notinh
                        } else {
                            //
                            $update_graph = "INSERT INTO process_manager_graph(Process_name) VALUES ('" . $id_mod . "')";
                            $update_graph = mysqli_query($link, $update_graph) or die(mysqli_error($link));
                        }
                        header("location:inspector.php");
                    } else {
                        //$insert_device="INSERT INTO devices(device_name) VALUES ('".$id_mod ."')";
                        $insert_device = "INSERT INTO devices (device_name, process) VALUES ('" . $id_mod . "', '" . $id_mod . "');";
                        $result_device = mysqli_query($link, $insert_device) or die(mysqli_error($link));
                        //
                        //CHECK IF responsible
//CHECKIF responsible
                        $check_responsbile = "SELECT * FROM process_manager_responsible WHERE process_name='" . $id_mod . "'";
                        $result_check_responsbile = mysqli_query($link, $check_responsbile) or die(mysqli_error($link));
                        $total_check = $result_check_responsbile->num_rows;
                        if ($total_check > 0) {
                            $query0 = "UPDATE process_manager_responsible 
                            SET "
                                    . "process_manager_responsible.licence ='" . $mod_lic . "', "
                                    . "process_manager_responsible.responsible ='" . $mod_prov . "', "
                                    . "process_manager_responsible.address ='" . $mod_add . "', "
                                    . "process_manager_responsible.mail ='" . $mod_mail . "', "
                                    . "process_manager_responsible.reference_person ='" . $mod_ref . "', "
                                    . "process_manager_responsible.telephone ='" . $mod_tel . "', "
                                    . "process_manager_responsible.webpage ='" . $mod_web . "' " .
                                    "       WHERE process_manager_responsible.process_name='" . $id_mod . "';";
                            //echo($query0);
                            $result0 = mysqli_query($link, $query0) or die(mysqli_error($link));
                        } else {
                            $update_licence = "INSERT INTO process_manager_responsible (process_name, licence, responsible, address, mail, reference_person, telephone, webpage) VALUES ('" . $id_mod . "','" . $mod_lic . "','" . $mod_prov . "', '" . $mod_add . "', '" . $mod_mail . "','" . $mod_ref . "','" . $mod_tel . "', '" . $mod_web . "')";
                            $result_licence = mysqli_query($link, $update_licence) or die(mysqli_error($link));
                        }
                        //
                        //CHECK sul graph
                        $check_graph = "SELECT * FROM process_manager_graph WHERE Process_name='" . $id_mod . "'";
                        $result_check_graph = mysqli_query($link, $check_graph) or die(mysqli_error($link));
                        $total_check_graph = $result_check_graph->num_rows;
                        if ($total_check_graph > 0) {
                            //notinh
                        } else {
                            //
                            $update_graph = "INSERT INTO process_manager_graph(Process_name) VALUES ('" . $id_mod . "')";
                            $update_graph = mysqli_query($link, $update_graph) or die(mysqli_error($link));
                        }
                        //
                        //echo('Error Update');
                        header("location:inspector.php");
                    }

                    header("location:inspector.php");
                    ///
                }
            } else {
                header("location:inspector.php");
                //exit;
            }
        } else {
           header("location:inspector.php");
            //exit;
        }
    } else {
        header("location:inspector.php");
        //exit;
    }
//} else {
//  header("location:inspector.php");
    //exit;
//}
?>