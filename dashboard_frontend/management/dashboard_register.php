<?php
    session_start();
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
        
        <!-- jQuery core JS-->
        <script src="../js/jquery-1.10.1.min.js"></script>
        
        <!-- Bootstrap core JS -->
        <script src="../js/bootstrap.min.js"></script>

        <!-- Bootstrap core CSS -->
        <link href="../css/bootstrap.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../css/signin.css" rel="stylesheet">
        
        <!-- JQUERY UI -->
        <!--<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>-->
        <script src="../js/jqueryUi/jquery-ui.js"></script>
        
        <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    </head>
    <body>
        <?php
            if(!isset($_SESSION['loggedRole']))
            {
                echo '<script type="text/javascript">';
                echo 'window.location.href = "unauthorizedUser.php";';
                echo '</script>';
            }
            else if(($_SESSION['loggedRole'] != "Manager") && ($_SESSION['loggedRole'] != "AreaManager") && ($_SESSION['loggedRole'] != "ToolAdmin"))
            {
                echo '<script type="text/javascript">';
                echo 'window.location.href = "unauthorizedUser.php";';
                echo '</script>';
            }
        ?>
        <div id="container-form" class="container">
            <div id="panel-form" class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Registrazione utenti</h3>
                </div>
                <div class="panel-body">
                    <form id="form-register" name="form-register" class="form-signin" role="form" method="post" action="process-form.php" data-toggle="validator">
                        <h2 class="form-signin-heading">Dashboard Management System</h2>
                        <div class="row">
                        <label for="inputUsername" class="sr-only">Username</label>
                        <input type="username" id="inputUsername" name="inputUsername" class="form-control" placeholder="Username" pattern="[A-Za-z0-9_]+" title="Sono ammessi lettere, numeri e _" required autofocus>
                        </div>
                        <div class="row">
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="row">
                        <label for="inputRePassword" class="sr-only">Re-Password</label>
                        <input type="password" id="inputRePassword" name="inputRePassword" class="form-control" placeholder="RePassword" required>
                        </div>
                        <div class="row">
                        <label for="inputNameUser" class="sr-only">Nome</label>                        
                        <input type="text" id="inputNameUser" name="inputNameUser" class="form-control" placeholder="Nome" required>
                        </div>
                        <div class="row">
                        <label for="inputSurnameUser" class="sr-only">Cognome</label>
                        <input type="text" id="inputSurnameUser" name="inputSurnameUser" class="form-control" placeholder="Cognome" required>
                        </div>
                        <div class="row">
                        <label for="email" class="sr-only">Email</label>
                        <input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="row">
                        <label for="admin">Amministratore</label>
                        <input type="checkbox" name="adminCheck" id="checkadmin" class="checkAdmin" value="1"/>
                        </div>
                        <p>
                            <?php
                                if(isset($_SESSION['loggedRole']))
                                {
                                    if(($_SESSION['loggedRole'] == "ToolAdmin")||($_SESSION['loggedRole'] == "AreaManager")||($_SESSION['loggedRole'] == "Manager"))
                                    {
                                        echo '<button id="button_annulla" name="register_cancel" class="btn btn-default btn-lg" type="button" onclick="location.href = \'dashboard_mng.php\'">Annulla</button>';
                                        echo '<button id="button_register_confirm" name="register_confirm" class="btn btn-primary btn-lg" type="submit">Conferma</button>';
                                    }
                                }
                            ?>
                        </p>
                    </form>
                </div>
            </div>    
        </div>
        
        <script type='text/javascript'>
            $(document).ready(function () 
            {
                var internalDest = false;
                $('#checkadmin').on('click', function(){
                    if ($('#checkadmin').prop('checked', true))
                    {
                       $('#checkadmin').attr('value','1');
                    } 
                    else 
                    {
                        $('#checkadmin').attr('value','0'); 
                    }
                });

                var password = document.getElementById("inputPassword");
                var confirm_password = document.getElementById("inputRePassword");

                function validatePassword() 
                {
                    if (password.value !== confirm_password.value) 
                    {
                        confirm_password.setCustomValidity("Passwords doesn't Match");
                    } 
                    else 
                    {
                        confirm_password.setCustomValidity('');
                    }
                }

                password.onchange = validatePassword;
                confirm_password.onkeyup = validatePassword;
            });
        </script>
    </body>
</html>