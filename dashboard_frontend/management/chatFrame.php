<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
include('../config.php');
use Jumbojett\OpenIDConnectClient;

if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

?>

<html class="dark">
    <head>
        <meta charset="UTF-8">
        
        <link href="../css/chatIframe.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="../css/chat.css" type="text/css" />
        <!-- Font awesome icons -->
        <link rel="stylesheet" href="../css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">
    </head>
    <body>
        <?php include "../cookie_banner/cookie-banner.php"; ?>

        <?php
        include '../config.php';
        include "../rocket-chat-rest-client/RocketChatClient.php";
        include "../rocket-chat-rest-client/RocketChatUser.php";
        include "../rocket-chat-rest-client/RocketChatChannel.php";
        define('REST_API_ROOT', '/api/v1/');
        define('ROCKET_CHAT_INSTANCE', $chatBaseUrl);
        require '../sso/autoload.php';

        session_write_close();

        $nameChat = $_REQUEST['nameChat'];
        $idChat = $_REQUEST['idChat'];
        $idUserChat = $_REQUEST['idUserChat'];
        $idDash = $_REQUEST['idDash'];

        function createChatAct() {
            include '../config.php';
            $admin = new \RocketChat\User();
            $admin->login();
            $a = new \RocketChat\Channel($_REQUEST['nameChat']);
            $a->create();
            $channel = $a->info();
            $a->inviteId($_REQUEST['idUserChat'], json_decode(json_encode($channel), true)['channel']['_id']);
            $admin->logout();
            $urlRed = $chatBaseUrl."/channel/" . $_REQUEST['nameChat'] . "?layout=embedded";
            echo '<iframe id="chatIframe" class="chatIframe" src="' . $urlRed . '" style="height: 700px;"></iframe>';
        }

        function isPublic($idDash, $addMem, $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret) {

            error_reporting(E_ERROR | E_NOTICE);
            date_default_timezone_set('Europe/Rome');
            if (isset($_SESSION['refreshToken'])) {
                $oidc = new OpenIDConnectClient('https://www.snap4city.org', $ssoClientId, $ssoClientSecret);
                $oidc->providerConfigParam(array('token_endpoint' => 'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/token'));
                $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                $accessToken = $tkn->access_token;
                $_SESSION['refreshToken'] = $tkn->refresh_token;
            //    $service_url = $personalDataApiBaseUrl ."/v1/username/ANONYMOUS/delegation/check?accessToken=" . $accessToken . "&sourceRequest=chatmanager&elementID=" . $idDash;
                // MOD V3 API
                $service_url = $personalDataApiBaseUrl ."/v3/username/ANONYMOUS/delegation/check?accessToken=" . $accessToken . "&sourceRequest=chatmanager&elementID=" . $idDash . "&elementType=DashboardID";
                $curl = curl_init($service_url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $curl_response = curl_exec($curl);
                curl_close($curl);
                $arr = json_decode($curl_response, true);
                if (!$arr["result"]) {
                //    $service_url = $personalDataApiBaseUrl ."/v1/username/" . rawurlencode($addMem) . "/delegation/check?accessToken=" . $accessToken . "&sourceRequest=chatmanager&elementID=" . $idDash;
                    // MOD V3 API
                    $service_url = $personalDataApiBaseUrl ."/v3/username/" . rawurlencode($addMem) . "/delegation/check?accessToken=" . $accessToken . "&sourceRequest=chatmanager&elementID=" . $idDash . "&elementType=DashboardID";
                    $curl = curl_init($service_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $curl_response = curl_exec($curl);
                    curl_close($curl);
                    $arr = json_decode($curl_response, true);
                }
            }
            return ($arr["result"]);
        }

        function archive() {
            $admin = new \RocketChat\User();
            $admin->login();
            $chan = new \RocketChat\Channel($_REQUEST['nameChat']);
            $chan->rename($_REQUEST['idChat'], $_REQUEST['nameChat'] . '-' . $_SESSION['loggedUsername'] . '-' . date('d.m.y-G:i:s'));
            $chan->archive($_REQUEST['idChat']);
            $admin->logout();
            echo '<i class="fa fa-check" aria-hidden="true" style="font-size:24px;color:white;"></i><font color="#eeeeee" size="3em">Chat Archived</font>';
        }

        function addUser($nameIns, $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret) {
            $nameIns = str_replace(' ', '%20', $nameIns);
            $nameIns = str_replace(' ', '%20', $nameIns);
            $checkUser = true;
            $checkPublicDash = isPublic($_REQUEST['idDash'], $nameIns, $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret);
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

        if ($idChat == 'id') {
            echo "<div id='create'><font color='#eeeeee' size='3em'>No chat for this dashboard. Do you want to create it?</br></br></font>";
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
        <div id="messageBox1">
            <?php
            if ($_POST["CreaChat"]) {
                createChatAct();
                $channel = new \RocketChat\Channel('N');
                $infoChannel = $channel->infoByName($existChat);
                $idNewChat = $idChat = $infoChannel->channel->_id;
                $urlRed = "chatFrame.php?nameChat=" . $_REQUEST['nameChat'] . "&idDash=" . $_REQUEST['idDash'] . "=&idChat=" . $idNewChat . "&idUserChat=" . $_REQUEST['idUserChat'];
                header("Location: $urlRed");
            }
            echo '</div>';
        } else {
            ?> 
            <div align="right">
                <form method="post" action="" name="bottone1_2">
                    <input type="text" name="NickName" value="">
                    <input type="submit" value="Add User" name="AddUser" onclick="actionMessage()" >
                </form>
            </div>

            <script>
                function actionMessage() {
                    document.getElementById("messageBox").innerHTML = "<i class='fa fa-circle-o-notch fa-spin fa-2x fa-fw' style='color:white;'></i>";

                }
            </script>
            <div id="messageBox" style="height: 40px;">
                <?php
                if ($_POST["AddUser"]) {
                    addUser($_POST['NickName'], $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret);
                }

                echo '</div>';
                ?>
                <div align="right">
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
            }
            ?>

            </body>
            </html>

<?php } else {
    include('../s4c-legacy-management/chatFrame.php');
}
?>