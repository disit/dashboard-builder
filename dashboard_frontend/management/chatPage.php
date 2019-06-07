<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link href="../css/chatIframe.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body>
         <?php
            include '../config.php';
            define('REST_API_ROOT', '/api/v1/');
            define('ROCKET_CHAT_INSTANCE', $chatBaseUrl);
            require '../sso/autoload.php';
            use Jumbojett\OpenIDConnectClient;
            session_start();
         //   session_write_close();
            
            $varC = $_REQUEST['nameDash'];
            $varB = $_REQUEST['frame'];
            $idDash=$_REQUEST['idDash'];
            $idGroup="";
            $idUserAdd="";
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
                                        
            function isPublic($idDash,$addMem, $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret) {
                                        
                                        error_reporting(E_ERROR | E_NOTICE);
                                        date_default_timezone_set('Europe/Rome');
                                         if(isset($_SESSION['refreshToken'])) 
                                        {
                                            $oidc = new OpenIDConnectClient('https://www.snap4city.org', $ssoClientId, $ssoClientSecret);
                                            $oidc->providerConfigParam(array('token_endpoint' => 'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/token'));
                                            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                                            $accessToken = $tkn->access_token;
                                            $_SESSION['refreshToken'] = $tkn->refresh_token;
                                            $service_url = $personalDataApiBaseUrl ."/v1/username/ANONYMOUS/delegation/check?accessToken=".$accessToken."&sourceRequest=dashboardmanager&elementID=".$idDash;
                                            $curl = curl_init($service_url);
                                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                            $curl_response = curl_exec($curl);
                                            curl_close($curl);
                                            $arr=json_decode($curl_response,true);
                                            if(!$arr["result"]){
                                                $service_url = $personalDataApiBaseUrl ."/v1/username/".rawurlencode($addMem)."/delegation/check?accessToken=".$accessToken."&sourceRequest=dashboardmanager&elementID=".$idDash;
                                                $curl = curl_init($service_url);
                                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                                $curl_response = curl_exec($curl);
                                                curl_close($curl);
                                                $arr=json_decode($curl_response,true);
                                                }
                                                                                
                                        }
                                        return ($arr["result"]);
                 
                                    }
            try
                    {
                    include "../rocket-chat-rest-client/RocketChatClient.php";
                    include "../rocket-chat-rest-client/RocketChatUser.php";
                    include "../rocket-chat-rest-client/RocketChatChannel.php";
                    }
                catch(Exception $e)
                    {
                    echo "Error";
                    echo $e->getMessage() . "\n";
                    echo $e->getTraceAsString();
                    } 
                $admin = new \RocketChat\User($userIdAdminChat, $passAdminChat);
                $check=false;
                function createChatAct($nameGroup)
                        {   
                        $a = new \RocketChat\Channel($nameGroup);
                        $a->create();
                        $channel=$a->info();
                        $a->invite1(findMember($_SESSION['loggedUsername'])['user']['_id'], json_decode(json_encode($channel), true)['channel']['_id']);
                        $curpage = '../management/chatPage.php?nameDash='.$nameGroup.'&frame=Btn';
                        header('Refresh: 2; url=' . $curpage);
                        }
                function addUser($nameGroup,$idGroup,$userAdd,$me,$idDash, $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret)
                    {
                    $checkUser=true;
                    $checkPublicDash=isPublic($idDash,$userAdd, $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret);
                    if($checkPublicDash){ 
                        $admin = new \RocketChat\User($userIdAdminChat, $passAdminChat);
                        $groupUser= findMember($userAdd);
                                                   
                        if($groupUser[success])
                            {
                            $chan=new \RocketChat\Channel($nameGroup);                            
                            $a = findGroup($nameGroup,$me);
                            $chan->invite1($groupUser['user']['_id'], $a['channel']['_id']);
                            $checkUser=false;
                            }
                        
                        if($checkUser){
                            echo '<i class="fa fa-times" aria-hidden="true" style="font-size:24px"></i><font color="#eeeeee" size="3em">User not found</font>';
                            }else{
                               echo '<i class="fa fa-check" aria-hidden="true" style="font-size:24px"></i><font color="#eeeeee" size="3em">User added</font>';
                            }
                        }else{
                            echo '<i class="fa fa-times" aria-hidden="true" style="font-size:24px"></i><font color="#eeeeee" size="3em">User not delegate for this Dashboard</font>';
                        }
                    }
                $me=talklogin(); 
                $nameGroup=strtolower($_REQUEST['nameDash']);
                $checkNameGroup=findGroup($nameGroup,$me);
                $check=$checkNameGroup[success];    
                $admin->login();
            if(!$check&&$varB=='Btn'){
                echo "<font color='#eeeeee' size='3em'>No chat for this dashboard. Do you want to create it?</br></br></font>";
                ?> 
                    <form method="post" action="" name="bottone1_2">
                    <input type="submit" value="Create Chat" name="CreaChat" onclick="actionMessage()">
                    </form>
                    <script>
                    function actionMessage() {
                        document.getElementById("messageBox1").innerHTML = "<i class='fa fa-circle-o-notch fa-spin fa-2x fa-fw'></i>";

                    }
                    </script>
                    <div id="messageBox1">
                     <?php
                         if ($_POST["CreaChat"]) 
                         {      
                             createChatAct($nameGroup);
                         }  
                    }
                    else if($varB=='Btn')
                    {
                    ?> 
                    </div>
                    <div>
                    <form method="post" action="" name="bottone1_2">
                    <input type="text" name="NickName" value="">
                    <input type="submit" value="Add User" name="AddUser" onclick="actionMessage()">
                    </form>
                    </div>
                    <script>
                    function actionMessage() {
                        document.getElementById("messageBox").innerHTML = "<i class='fa fa-circle-o-notch fa-spin fa-2x fa-fw'></i>";

                    }
                    </script>
                    <div id="messageBox">
                     <?php
                         if ($_POST["AddUser"]) 
                         {   
                            $me=talklogin(); 
                            addUser($nameGroup,$idGroup,$_POST['NickName'],$me,$idDash, $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret);
                          }  
                          
                }else if($check){
                 header('Location: ' . $chatBaseUrl . '/channel/'.$nameGroup.'/?layout=embedded'); 
                }else {
                $curpage = '../management/chatPage.php?nameDash='.$nameGroup;
                header('Refresh: 10; url=' . $curpage);  
                }
                ?>
                    </div>     
     </body>
</html>
