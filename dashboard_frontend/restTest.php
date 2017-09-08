<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
   <head>
      <meta charset="UTF-8">
      
      <!-- jQuery -->
      <script src="js/jquery-1.10.1.min.js"></script>
      
      <title></title>
   </head>
   <body>
      <h1>Alarm manager tester</h1>
      <h2 id="result"></h2>
      <h3 id="detail"></h3>
      
      <script type='text/javascript'>
             $.ajax({
                url: "http://localhost/Notificator/restInterface.php",
                data: {
                   apiUsr: "alarmManager",
                   apiPwd: "d0c26091b8c8d4c42c02085ff33545c1", //MD5
                   operation: "remoteLogin",
                   app: "Dashboard",
                   appUsr: "Mino",
                   appPwd: "a1a1a1a1"
                },
                type: "POST",
                async: false,
                dataType: 'json',
                success: function (data) 
                {
                   $("#result").html("Remote login - Correct");
                   $("#detail").html(JSON.stringify(data));
                },
                error: function (data)
                {
                   $("#result").html("Remote login- Error");
                   $("#detail").html(JSON.stringify(data));
                }
             });
   
             /*$.ajax({
                url: "http://localhost/Notificator/restInterface.php",
                data: {
                   apiUsr: "alarmManager",
                   apiPwd: "d0c26091b8c8d4c42c02085ff33545c1", //MD5
                   operation: "remoteLogout",
                   app: "Dashboard",
                   appUsr: "Mino"
                },
                type: "POST",
                async: true,
                dataType: 'json',
                success: function (data) 
                {
                   console.log("Correct");
                   console.log(data);
                   $("#result").html("Remote logout - Correct");
                   $("#detail").html(JSON.stringify(data));
                },
                error: function (data)
                {
                   console.log("Error");
                   console.log(data);
                   $("#result").html("Remote logout - Error");
                   $("#detail").html(JSON.stringify(data));
                }
             });*/
      </script>   
   </body>
</html>

