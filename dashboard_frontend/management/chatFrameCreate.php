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
        <link rel="stylesheet" href="../css/chat.css" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body leftmargin="0" topmargin="0">

        <?php
        include '../config.php';
        include "../rocket-chat-rest-client/RocketChatClient.php";
        include "../rocket-chat-rest-client/RocketChatUser.php";
        include "../rocket-chat-rest-client/RocketChatChannel.php";
        define('REST_API_ROOT', '/api/v1/');
        define('ROCKET_CHAT_INSTANCE', $chatBaseUrl);
        require '../sso/autoload.php';

        use Jumbojett\OpenIDConnectClient;

session_start();
        session_write_close();

        $nameChat = $_REQUEST['nameChat'];
        $idChat = $_REQUEST['idChat'];
        $idUserChat = $_REQUEST['idUserChat'];
        $idDash = $_REQUEST['idDash'];

         function archive() {
            $admin = new \RocketChat\User();
            $admin->login();
            $chan = new \RocketChat\Channel($_REQUEST['nameChat']);
            $chan->rename($_REQUEST['idChat'], $_REQUEST['nameChat'] . '-' . $_SESSION['loggedUsername'] . '-' . date('d.m.y-G:i:s'));
            $chan->archive($_REQUEST['idChat']);
            $admin->logout();
            echo '<i class="fa fa-check" aria-hidden="true" style="font-size:24px;color:white;"></i><font color="#eeeeee" size="3em">Chat Archived</font>';
        }
        
        function createChatAct() {
            $admin = new \RocketChat\User();
            $admin->login();
            $a = new \RocketChat\Channel($_REQUEST['nameChat']);
            $a->create();
            $channel = $a->info();
            $a->inviteId($_REQUEST['idUserChat'], json_decode(json_encode($channel), true)['channel']['_id']);
            $a->addOwnerId(json_decode(json_encode($channel), true)['channel']['_id'], $_REQUEST['idUserChat']);
            $admin->logout();
            //$urlRed = "https://chat.snap4city.org/channel/".$_REQUEST['nameChat']."?layout=embedded";
            //echo '<iframe id="chatIframe" class="chatIframe" src="' . $urlRed . '" style="height: 700px;"></iframe>';
        }

        function isPublic($idDash, $addMem) {

            error_reporting(E_ERROR | E_NOTICE);
            date_default_timezone_set('Europe/Rome');
            if (isset($_SESSION['refreshToken'])) {
                $oidc = new OpenIDConnectClient('https://www.snap4city.org', 'php-dashboard-builder', '0afa15e8-87b9-4830-a60c-5fd4da78a9c4');
                $oidc->providerConfigParam(array('token_endpoint' => 'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/token'));
                $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                $accessToken = $tkn->access_token;
                $_SESSION['refreshToken'] = $tkn->refresh_token;
                $service_url = "http://192.168.0.10:8080/datamanager/api/v1/username/ANONYMOUS/delegation/check?accessToken=" . $accessToken . "&sourceRequest=chatmanager&elementID=" . $idDash;
                //$service_url = "http://192.168.0.10:8080/datamanager/api/v1/username/ANONYMOUS/delegation/check?accessToken=".$accessToken."&sourceRequest=dashboardmanager&elementID=67";
                $curl = curl_init($service_url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $curl_response = curl_exec($curl);
                curl_close($curl);
                $arr = json_decode($curl_response, true);
                if (!$arr["result"]) {
                    $service_url = "http://192.168.0.10:8080/datamanager/api/v1/username/" . $addMem . "/delegation/check?accessToken=" . $accessToken . "&sourceRequest=chatmanager&elementID=" . $idDash;
                    $curl = curl_init($service_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $curl_response = curl_exec($curl);
                    curl_close($curl);
                    $arr = json_decode($curl_response, true);
                }
            }
            return ($arr["result"]);
        }

        function addUser($nameIns) {
            $checkUser = true;
            $checkPublicDash = isPublic($_REQUEST['idDash'], $nameIns);

            if ($checkPublicDash) {
                $admin = new \RocketChat\User();
                $admin->login();
                $userChat = $admin->infoByUsername($nameIns);
                $userIdAdd = $userChat->user->_id;
                if ($userChat->success) {
                    $chan = new \RocketChat\Channel($_REQUEST['nameChat']);
                    $chan->inviteId($userIdAdd, $_REQUEST['idChat']);
                    $checkUser = false;
                }
                $admin->logout();
                if ($checkUser) {
                    echo '<i class="fa fa-times" aria-hidden="true" style="font-size:24px;color:white;"></i><font color="#eeeeee" size="3em">User not found</font>';
                } else {
                    echo '<i class="fa fa-check" aria-hidden="true" style="font-size:24px;color:white;"></i><font color="#eeeeee" size="3em">User added</font>';
                }
            } else {
                echo '<i class="fa fa-times" aria-hidden="true" style="font-size:24px;color:white;"></i><font color="#eeeeee" size="3em">User not delegate for this Dashboard</font>';
            }
        }

        if ($idChat == 'archived') {
            echo "<div id='create' style='background-color:#8b9fa7;height:150px;padding:10px;'><font color='#eeeeee' size='3em'><br> Chat archived. Recharge the page to create a new chat</br></br></font>";
        } else if ($idChat == 'id') {
            echo "<div id='create' style='background-color:#8b9fa7;padding:10px;'><font color='#eeeeee' size='3em'><br> No chat for this dashboard. Do you want to create it?<br></font>";
            ?> 
            <form method="post" action="" name="bottone1_2">
                <input type="submit" value="Create Chat" name="CreaChat" onclick="actionMessage()">
            </form>
            <script>
                function actionMessage() {
                    document.getElementById("messageBox1").innerHTML = "<i class='fa fa-circle-o-notch fa-spin fa-2x fa-fw' style='color:white;'></i>";
                }

            </script>
        </div>
        <div id="messageBox1" style="height:40px;background-color:#8b9fa7;">
            <?php
            if ($_POST["CreaChat"]) {
                createChatAct();
                $admin = new \RocketChat\User();
                $admin->login();
                $channel = new \RocketChat\Channel('N');
                $infoChannel = $channel->infoByName($_REQUEST['nameChat']);
                $idNewChat = $infoChannel->channel->_id;
                $description = '[Dashboard](https://main.snap4city.org/view/index.php?iddasboard=' . base64_encode($_REQUEST['idDash']) . ')';
                $channel->setUrl($infoChannel->channel->_id, $description);
                $admin->logout();
                $urlRed = "chatFrameCreate.php?nameChat=" . $_REQUEST['nameChat'] . "&idDash=" . $_REQUEST['idDash'] . "&idChat=" . $idNewChat . "&idUserChat=" . $_REQUEST['idUserChat'];
                header("Location: $urlRed");
            }
            echo '</div>';
        } else {
            ?> 
            <div align="right" style='background-color:#8b9fa7;padding-top:10px;'>
                <form method="post" action="" name="bottone1_2">
                    <input type="text" name="NickName" value="">
                    <input type="submit" value="Add User" name="AddUser" onclick="actionMessage()" >
                </form>
            </div>
            <script>
                function actionMessage() {
                    document.getElementById("messageBox").innerHTML = "<i class='fa fa-circle-o-notch fa-spin fa-2x fa-fw' style='color:white;'></i>";
                    return false;
                }
            </script>
            <div id="messageBox" style="height: 40px;background-color:#8b9fa7;">
                <?php
                if ($_POST["AddUser"]) {
                    addUser($_POST['NickName']);
                }
                echo '</div>';
                
           ?>
                <div align="right" style='background-color:#8b9fa7;padding-bottom:10px'>
                    <form method="post" action="" name="bottone1_2">
                        <input type="submit" value="Archive Chat" name="Archive" onclick="actionMessage()" >
                    </form>
                </div>
                <?php
                if ($_POST["Archive"]) {
                    archive();
                    $urlRed = "chatFrameCreate.php?nameChat=" . $_REQUEST['nameChat'] . "&idDash=" . $_REQUEST['idDash'] . "=&idChat=archived&idUserChat=" . $_REQUEST['idUserChat'];
                    header("Location: $urlRed");
                }
                echo '</div>';
                echo '<iframe id="chatIframe" class="chatIframe" src="'.$chatBaseUrl.'/channel/' . $_REQUEST['nameChat'] . '?layout=embedded" style="height: 550px;"></iframe>';
            
            }
            ?>

            </body>
            </html>