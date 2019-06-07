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
    <body>

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

        function isPublic($idDash, $addMem, $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret) {

            error_reporting(E_ERROR | E_NOTICE);
            date_default_timezone_set('Europe/Rome');
            if (isset($_SESSION['refreshToken'])) {
                $oidc = new OpenIDConnectClient('https://www.snap4city.org', $ssoClientId, $ssoClientSecret);
                $oidc->providerConfigParam(array('token_endpoint' => 'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/token'));
                $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                $accessToken = $tkn->access_token;
                $_SESSION['refreshToken'] = $tkn->refresh_token;
                $service_url = $personalDataApiBaseUrl . "/v1/username/ANONYMOUS/delegation/check?accessToken=" . $accessToken . "&sourceRequest=chatmanager&elementID=" . $idDash;
                $curl = curl_init($service_url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $curl_response = curl_exec($curl);
                curl_close($curl);
                $arr = json_decode($curl_response, true);
                if (!$arr["result"]) {
                    $service_url = $personalDataApiBaseUrl . "/v1/username/" . rawurlencode($addMem) . "/delegation/check?accessToken=" . $accessToken . "&sourceRequest=chatmanager&elementID=" . $idDash;
                    $curl = curl_init($service_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $curl_response = curl_exec($curl);
                    curl_close($curl);
                    $arr = json_decode($curl_response, true);
                }
            }
            return ($arr["result"]);
        }

        function addUser($nameIns, $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret) {
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
        ?> 


        <div>
            <form method="post" action="" name="bottone1_2">
                <input type="text" name="NickName" value="">
                <input type="submit" value="Add User" name="AddUser" onclick="actionMessage()">
            </form>
        </div>
        <script>
            function actionMessage() {
                document.getElementById("messageBox").innerHTML = "<i class='fa fa-circle-o-notch fa-spin fa-2x fa-fw' style='color:white;'></i>";
                return false;
            }
        </script>
        <div id="messageBox" style="height: 40px;">
            <?php
            if ($_POST["AddUser"]) {
                addUser($_POST['NickName'], $personalDataApiBaseUrl, $ssoClientId, $ssoClientSecret);
            }
            echo '</div>';
            ?>

    </body>
</html>
