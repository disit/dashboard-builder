<?php
/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

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
   include('../config.php');
   include('process-form.php'); 
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Management System</title>

        <!-- Bootstrap core CSS -->
        <link href="../css/bootstrap.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../css/signin.css" rel="stylesheet">

        <!-- jQuery -->
        <script src="../js/jquery-1.10.1.min.js"></script>
        
        <!-- JQUERY UI -->
        <script src="../js/jqueryUi/jquery-ui.js"></script>
        
        <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        
        <link href="../css/dashboard.css" rel="stylesheet">
    </head>
    <body>
        <div id="container-form" class="container">
            <div id="panel-form" class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Login</h3>
                </div>
                <div class="panel-body">
                    <form id="form-login" class="form-signin" role="form" method="post" action="">
                        <h2 class="form-signin-heading">Dashboard Management System</h2>
                        <label for="inputUsername" class="sr-only">Username</label>
                        <input type="username" id="inputUsername" name="loginUsername" class="form-control" placeholder="Username" required autofocus>
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input type="password" id="inputPassword" name="loginPassword" class="form-control" placeholder="Password" required>
                        <div class="checkbox">
                            <label>
                                <!--<input type="checkbox" value="remember-me"> Remember me-->
                            </label>
                        </div>
                        <p>
                           <button id="button_login" name="login" class="btn btn-primary btn-lg btn-block" type="submit">Sign in</button>
                        </p>
                        <?php if(isset($_REQUEST['sessionExpired'])){echo '<p>Session expired</p>';}?>
                    </form>
                </div>
            </div>    
        </div> <!-- /container -->


        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>

        <script src="../js/bootstrap.min.js"></script>
        
        <script type='text/javascript'>
            $(document).ready(function ()
            {
               var notificatorUrl = "<?php echo $notificatorUrl; ?>";
               var internalDest = false;
               
               $("#button_login").click(function()
               {
                  $.ajax({
                     url: notificatorUrl,
                     data: {
                        apiUsr: "alarmManager",
                        apiPwd: "d0c26091b8c8d4c42c02085ff33545c1", //MD5
                        operation: "remoteLogin",
                        app: "Dashboard",
                        appUsr: $("#inputUsername").val(),
                        appPwd: $("#inputPassword").val()
                     },
                     type: "POST",
                     async: true,
                     dataType: 'json',
                     success: function (data) 
                     {
                        console.log("Remote login OK");
                        console.log(JSON.stringify(data));
                     },
                     error: function (data)
                     {
                        console.log("Remote login KO");
                        console.log(JSON.stringify(data));
                     }
                  });
               });
            });
        </script>
    </body>
</html>
