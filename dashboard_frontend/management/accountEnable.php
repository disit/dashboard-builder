<?php
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
   include('../config.php');
   session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Management System - Account activation</title>

        <!-- Bootstrap core CSS -->
        <link href="../css/bootstrap.css" rel="stylesheet">

        <!-- jQuery -->
        <script src="../js/jquery-1.10.1.min.js"></script>
        
        <!-- JQUERY UI -->
        <script src="../js/jqueryUi/jquery-ui.js"></script>
        
        <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
        
        <!--<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
        
        <!-- Custom CSS -->
        <link href="../css/dashboard.css" rel="stylesheet">
        
        <!-- Scripts file -->
        <script src="../js/accountManagement.js"></script>
    </head>
    <body class="dashboardViewBodyAuth">
        <?php
            if((!isset($_REQUEST['user']))||(!isset($_REQUEST['email']))||(!isset($_REQUEST['hash'])))
            {
                echo '<script type="text/javascript">';
                echo 'window.location.href = "unauthorizedUser.php";';
                echo '</script>';
            }
            else
            {
               $link = mysqli_connect($host, $username, $password) or die();
               mysqli_select_db($link, $dbname);
               $user = $_REQUEST['user'];
               $email = $_REQUEST['email'];
               $hash = $_REQUEST['hash'];
               $query = "SELECT * FROM Dashboard.Users WHERE username = '$user' AND email = '$email' AND activationHash = '$hash'";
               $result = mysqli_query($link, $query) or die(mysqli_error($link));

               if($result)
               {
                  if($result->num_rows <= 0) 
                  {
                     echo '<script type="text/javascript">';
                     echo 'window.location.href = "unauthorizedUser.php";';
                     echo '</script>';
                  }  
               }
            }
        ?>
        <div class="row">
            <div class="col-xs-12 centerWithFlex" id="loginMainTitle">Dashboard Management System</div>
        </div>
        
        <div id="authFormDarkBackground">
            <div class="row">
                <div id="enableAccountFormContainer" class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
                    <div class="col-xs-12" id="loginFormTitle" class="centerWithFlex" style="margin-top: 15px; margin-bottom: 15px">
                       Account activation
                    </div>
                    <h5>Dear <b><?php echo $_REQUEST['user'] ?></b>, please choose a password and click the button below in order to activate your account and be able to login the system.<br/><br/>
                        You will receive an e-mail containing your account details after activation.</h5>
                    <form id="authForm" class="form-signin" role="form" method="post" action="">
                        <div class="col-xs-12" id="accountActivationFormRow">
                            <div class="col-xs-12 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" class="modalInputTxt" id="accountActivationPwd" name="accountActivationPwd" required> 
                                </div>
                                <div class="modalFieldLabelCnt">Password</div>
                                <div id="accountActivationPwdMsg" class="accountEditSubfieldContainer"></div> 
                            </div>
                            <div class="col-xs-12 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="password" class="modalInputTxt" id="accountActivationConfirmPwd" name="accountActivationConfirmPwd" required> 
                                </div>
                                <div class="modalFieldLabelCnt">Password confirmation</div>
                                <div id="accountActivationConfirmPwdMsg" class="accountEditSubfieldContainer"></div> 
                            </div>
                        </div>
                        
                        <div class="row" id="accountActivationActivatingRow" style="margin-bottom: 15px">
                            <div class="col-xs-12 centerWithFlex">Enabling account, please wait</div>
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                        </div>
                        <div class="row" id="accountActivationOkRow" style="margin-bottom: 15px">
                            <div class="col-xs-12 centerWithFlex">Account successfully enabled: an e-mail containing your account details has been sent to your mailbox.</div>
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                        </div>
                        <div class="row" id="accountActivationKoRow" style="margin-bottom: 15px">
                            <div class="col-xs-12 centerWithFlex">Account enabling failed: please try again.</div>
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                        </div>
                    <div class="col-xs-12 centerWithFlex" id="accountActivationBtnRow" style="margin-top: 15px; margin-bottom: 15px">
                        <button type="reset" id="accountActivationCancelBtn" class="btn cancelBtn" style="margin-right: 15px">Reset</button>
                        <button type="button" id="accountActivationBtn" name="accountActivationBtn" class="btn confirmBtn internalLink" disabled>Activate</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
       
        <!--<div id="container-form" class="container">
            <div id="panel-form" class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Dashboard Management System</h3>
                </div>
                <div class="panel-body">
                        <h2>Account activation</h2>
                        <h5>Dear <b><?php echo $_REQUEST['user'] ?></b>, please choose a password and click the button below in order to activate your account and be able to login the system.<br/><br/>
                        You will receive an e-mail containing your account details after activation.</h5>
                        <div class="row">
                           <div class="col-md-6 col-md-offset-3">
                              <div class="accountEditSubfieldContainer">Password</div>
                              <div class="accountEditSubfieldContainer">
                                 <input type="password" id="accountActivationPwd" name="accountActivationPwd">
                              </div>
                              <div id="accountActivationPwdMsg" class="accountEditSubfieldContainer"></div>    
                           </div>
                        </div> 
                        
                        <div class="row">
                           <div class="col-md-6 col-md-offset-3">
                              <div class="accountEditSubfieldContainer">Password confirmation</div>
                              <div class="accountEditSubfieldContainer">
                                 <input type="password" id="accountActivationConfirmPwd" name="accountActivationConfirmPwd">
                              </div>
                              <div id="accountActivationConfirmPwdMsg" class="accountEditSubfieldContainer"></div>    
                           </div>
                        </div> 
                        
                        <div id="accountActivationBtnRow" class="row">
                           <div class="col-md-6 col-md-offset-3">
                              <button id="accountActivationBtn" name="login" class="btn btn-primary" type="button" disabled="true">Activate account</button>
                           </div>
                        </div>
                        
                        <div class="row" id="accountActivationActivatingRow">
                           <div class="col-md-10 col-md-offset-1">
                              <div class="accountEditSubfieldContainer">Enabling account, please wait</div>
                              <div class="accountEditSubfieldContainer"><i class="fa fa-spinner fa-spin" style="font-size:32px"></i></div>    
                           </div>
                        </div>
                        
                        <div class="row" id="accountActivationOkRow">
                           <div class="col-md-10 col-md-offset-1">
                              <div>Account successfully enabled: an e-mail containing your account details has been sent to your mailbox.</div>
                              <div><i class="fa fa-check" style="font-size:32px"></i></div>    
                           </div>
                        </div> 
                        
                        <div class="row" id="accountActivationKoRow">
                           <div class="col-md-10 col-md-offset-1">
                              <div>Account enabling failed: please try again.</div>
                              <div><i class="fa fa-frown-o" style="font-size:32px"></i></div>    
                           </div>
                        </div>
                </div>
            </div>    
        </div>--> 
        
        <script type='text/javascript'>
         $(document).ready(function () 
         {
            var loginFormCntMargin = parseInt(($('#authFormDarkBackground').height() - $('#enableAccountFormContainer').height()) / 2);
            $('#enableAccountFormContainer').css("margin-top", loginFormCntMargin + "px");

            $(window).resize(function(){
                var loginFormCntMargin = parseInt(($('#authFormDarkBackground').height() - $('#enableAccountFormContainer').height()) / 2);
                $('#enableAccountFormContainer').css("margin-top", loginFormCntMargin + "px");
            }); 
             
            enableAccountPageSetup();

            $("#accountActivationBtn").click(function(){
               enableAccount("<?php echo $_REQUEST['user'] ?>", "<?php echo $_REQUEST['email'] ?>", $("#accountActivationPwd").val(), "<?php echo $_REQUEST['hash'] ?>");
            });

         });//Fine document ready
     </script>
    </body>
</html>
