<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

    <head>
        <meta charset="UTF-8">
         <link href="../css/chatIframe.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        
         <?php
                    include '../config.php';           
                    define('REST_API_ROOT', '/api/v1/');
                    define('ROCKET_CHAT_INSTANCE', $chatBaseUrl);
                    include "../rocket-chat-rest-client/RocketChatClient.php";
                    include "../rocket-chat-rest-client/RocketChatUser.php";
                    include "../rocket-chat-rest-client/RocketChatChannel.php";
                    session_start();
            
            function talklogin() {
                                        include '../config.php';  
                                        $service_url = 'http://192.168.0.56:3000/api/v1/login';
                                        $ch = curl_init();
                                        curl_setopt($ch, CURLOPT_URL,$service_url);
                                        curl_setopt($ch, CURLOPT_POST, 1);
                                        curl_setopt($ch, CURLOPT_POSTFIELDS,
                                        "username=".$userIdAdminChat."&password=".$passAdminChat);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                                        $curl_response = curl_exec($ch);
                                        curl_close($ch);
                                        return json_decode($curl_response);
                                        }
                                        
            function findGroup($nameGroup,$me) {
                                        $service_url = 'http://192.168.0.56:3000/api/v1/channels.info?roomName='.$nameGroup;
                                        $curl = curl_init($service_url);
                                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                        'X-Auth-Token: '.$me->data->authToken,
                                        'X-User-Id: '.$me->data->userId,
                                        "Content-Type: application/json"
                                        ));
                                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                        $curl_response = curl_exec($curl);
                                        curl_close($curl);
                                        return json_decode($curl_response,true);
                                        }
                                        
                function findMember($user) {
                                        $me=talklogin(); 
                                        $service_url = 'http://192.168.0.56:3000/api/v1/users.info?username='.$user;
                                        $curl = curl_init($service_url);
                                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                        'X-Auth-Token: '.$me->data->authToken,
                                        'X-User-Id: '.$me->data->userId,
                                        "Content-Type: application/json"
                                        ));
                                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                        $curl_response = curl_exec($curl);
                                        curl_close($curl);
                                        return json_decode($curl_response,true);
                                        }
                                        
                function checkMembers($nameGroup,$me){
                                        $service_url = 'http://192.168.0.56:3000//api/v1/channels.counters?roomName='.$nameGroup.'&userId='.findMember($_SESSION['loggedUsername'])['user']['_id'];
                                        $curl = curl_init($service_url);
                                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                        'X-Auth-Token: '.$me->data->authToken,
                                        'X-User-Id: '.$me->data->userId,
                                        "Content-Type: application/json"
                                        ));
                                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                        $curl_response = curl_exec($curl);
                                        curl_close($curl);
                                        return json_decode($curl_response,true);
                                        }
                
                $varA = $_REQUEST['nameDash'];
                
                $admin = new \RocketChat\User($userIdAdminChat,$passAdminChat);
                $chatGroupExists=false;
                $nameGroup=strtolower(str_replace(" ", "", $_REQUEST['nameDash']));
                $me=talklogin();
                $checkNameGroup=findGroup($nameGroup,$me);
                $isJoint=checkMembers($nameGroup,$me);
                if($checkNameGroup[success]){
                    if($_SESSION['loggedRole']=='Root---Admin'){
                                   // header('Location: ' . $chatBaseUrl . '/channel/'.$nameGroup.'/?layout=embedded');
                                    }else{
                                        if($isJoint['success']){
                                            header('Location: ' . $chatBaseUrl . '/channel/'.$nameGroup.'/?layout=embedded');
                                        }else{
                                            echo '<div id="chatEx">notExistingGroup</div>';
                                        }
                                    }
                }                        
                        
                        
                ?>
      
    </body>
