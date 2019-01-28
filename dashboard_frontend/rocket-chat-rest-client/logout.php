<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function talklogout() {
    include '../config.php';
    $service_url = 'http://192.168.0.56:3000/api/v1/logout';
    $ch = curl_init($service_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-Auth-Token: ' . $_COOKIE["rc_token"],
        'X-User-Id: ' . $_COOKIE["rc_uid"],
        "Content-Type: application/json"
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $curl_response = curl_exec($ch);
    curl_close($ch);
    return json_decode($curl_response);
}

talklogout();



