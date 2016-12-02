<?php
/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

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

        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
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
                    </form>
                    <!--
                    <div class="col-md-12 control">
                        <div id="footer-form-login">
                            Non hai un account! 
                            <a href="#" onClick="location.href = 'dashboard_register.php'">
                                Registrati qui
                            </a>
                        </div>
                    </div>
                    -->
                </div>
            </div>    
        </div> <!-- /container -->


        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>

        <script src="../js/bootstrap.min.js"></script>
        <?php
        include('process-form.php'); // Includes Login Script
        ?>
    </body>
</html>
