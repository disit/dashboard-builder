<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Rocket chat API test</title>
    </head>
    <body>
        <?php
            try
            {
                define('REST_API_ROOT', '/api/v1/');
                define('ROCKET_CHAT_INSTANCE', 'http://192.168.0.47:3000');
                include "RocketChatClient.php";
                include "RocketChatUser.php";
                //include "../rocket-chat-rest-client/RocketChatChannel.php";
                include "RocketChatGroup.php";
                //include "../rocket-chat-rest-client/RocketChatSettings.php";
                
            }
            catch(Exception $e)
            {
                echo "Error";
                echo $e->getMessage() . "\n";
                echo $e->getTraceAsString();
            }

            function chatIsActive($nameDashboard) {
                $admin = new \RocketChat\User('admin', 'admin');
                $check=false;
                $groupRocket=($admin->list_groups());
                for ($i=0; $i<count($groupRocket);$i++){
                    if($groupRocket[$i]->name==$nameDashboard){
                     $check=true;
                     break;
                    }
                
                }
                return check;    
                }
               
           
        ?>    
    </body>
</html>