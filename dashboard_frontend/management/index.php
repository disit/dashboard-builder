<?php
/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
   
   session_start();
    
   $envFile = parse_ini_file("../conf/environment.ini");
   
    /*if(!isset($_SESSION['loggedRole'])&&($envFile['environment']['value'] != 'dev'))
    {
        header("location: ssoLogin.php");
    }*/
 //checkSession('Public');
   header("location: iframeApp.php?linkUrl=https://www.snap4city.org/drupal&linkId=snap4cityPortalLink&pageTitle=www.snap4city.org&fromSubmenu=false");
   exit();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php include "mobMainMenuClaim.php" ?></title>

        <!-- jQuery -->
        <script src="../js/jquery-1.10.1.min.js"></script>
        
        <!-- Bootstrap core CSS -->
        <link href="../css/bootstrap.css" rel="stylesheet">
        <script src="../js/bootstrap.min.js"></script>
        
        <!-- JQUERY UI -->
        <script src="../js/jqueryUi/jquery-ui.js"></script>
        
        <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
        
        <!-- Highcharts -->
        <script src="../js/highcharts/code/highcharts.js"></script>
        <script src="../js/highcharts/code/modules/exporting.js"></script>
        <script src="../js/highcharts/code/highcharts-more.js"></script>
        <script src="../js/highcharts/code/modules/solid-gauge.js"></script>
        <script src="../js/highcharts/code/highcharts-3d.js"></script>
        
        <link href="../css/dashboard.css" rel="stylesheet">
    </head>
    <body id="loginBody" class="guiPageBody">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 centerWithFlex" id="loginMainTitle">Dashboard Management System</div>  
                <div class="hidden-xs hidden-sm col-md-6" id="loginLeftCol">
                    <div class="col-xs-12" id="loginFeaturesContainer">
                        <div class="row">
                            <div class="col-md-4 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt">
                                    Drag&drop widgets positioning grid
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-arrows" style="color: #f3cf58"></i>
                                </div>
                            </div>
                            <div class="col-md-4 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt">
                                    4 different user profile types
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-user" style="color: #33cc33"></i>
                                </div>
                            </div>
                            <div class="col-md-4 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt">
                                    37 ready-to-use widget types
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-pie-chart" style="color: #d84141"></i>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt">
                                    Public or restricted access dashboards
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-lock" style="color: #1a8cff"></i>
                                </div>
                            </div>
                            <div class="col-md-4 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt">
                                    Database & API datasources
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-database" style="color: #ff66ff"></i>
                                </div>
                            </div>
                            <div class="col-md-4 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt">
                                    Thresholds definition & alerting
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-bell-o" style="color: rgba(0, 162, 211, 1)"></i>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt">
                                    Cross-widget interactions
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-arrows-h" style="color: #00e6e6"></i>
                                </div>
                            </div>
                            <div class="col-md-4 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt">
                                    Data geolocation on map
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-map-marker" style="color: #ff9933"></i>
                                </div>
                            </div>
                            <div class="col-md-4 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt">
                                    Embed customizer & previewer
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-object-ungroup" style="color: #59c0b9"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-xs-offset-0 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-0" id="loginRightCol"> 
                    <div class="col-xs-12 col-md-6 col-md-offset-3" id="loginFormContainer">
                        <form id="loginForm" role="form" method="post" action="ssoLogin.php">
                            <div class="col-xs-12" id="loginFormTitle" class="centerWithFlex">
                               Login
                            </div>
                            <div class="col-xs-12" id="loginFormBody">
                                <div class="col-xs-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" id="inputUsername" name="loginUsername" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Username</div>
                                </div>
                                <div class="col-xs-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="password" class="modalInputTxt" id="inputPassword" name="loginPassword" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Password</div>
                                </div>
                                <div class="col-xs-12 modalCell">
                                    <div id="loginFormMessage"></div>
                                </div>
                                <?php if(isset($_REQUEST['sessionExpired'])){ ?>
                                    <div class="col-xs-12 modalCell">
                                        <div class="modalFieldLabelCnt">Session expired</div>
                                    </div>
                                <?php    
                                    }
                                ?>
                            </div>
                            <div class="col-xs-12 centerWithFlex" id="loginFormFooter">
                               <button type="reset" id="loginCancelBtn" class="btn cancelBtn" data-dismiss="modal">Reset</button>
                               <button type="button" id="loginConfirmBtn" name="login" class="btn confirmBtn internalLink">Login</button>
                               <button type="submit" id="ssoBtn" name="ssoLogin" class="btn confirmBtn internalLink">SSO</button>
                            </div>
                        </form>    
                    </div>
                </div>
                <div class="col-xs-12 centerWithFlex" id="loginFooter">Developed by&nbsp;<a href="https://www.disit.org" target="_blank">DISIT</a></div>  
            </div>

        </div>
     </body>
</html>    
            
        <script type='text/javascript'>
            $(document).ready(function ()
            {
               var notificatorUrl = "<?php echo $notificatorUrl; ?>";
               var internalDest = false;
               
               var colsHeight = $('#loginBody').height() - $('#loginMainTitle').height() - $('#loginFooter').height();
               $('#loginLeftCol').height(colsHeight);
               $('#loginRightCol').height(colsHeight);
               var loginFormCntMargin = parseInt((colsHeight - $('#loginFormContainer').height()) / 2);
               $('#loginFormContainer').css("margin-top", loginFormCntMargin + "px");
               var loginFeaturesContainerMargin = parseInt((colsHeight - $('#loginFeaturesContainer').height()) / 2);
               $('#loginFeaturesContainer').css("margin-top", loginFeaturesContainerMargin + "px");
               $('#loginFormMessage').parents('div.modalCell').hide();
               
               $(window).resize(function(){
                    var colsHeight = $('#loginBody').height() - $('#loginMainTitle').height() - $('#loginFooter').height();
                    $('#loginLeftCol').height(colsHeight);
                    $('#loginRightCol').height(colsHeight);
                    var loginFormCntMargin = parseInt((colsHeight - $('#loginFormContainer').height()) / 2);
                    $('#loginFormContainer').css("margin-top", loginFormCntMargin + "px");
                    var loginFeaturesContainerMargin = parseInt((colsHeight - $('#loginFeaturesContainer').height()) / 2);
                    $('#loginFeaturesContainer').css("margin-top", loginFeaturesContainerMargin + "px");
               });
               
               $('#loginConfirmBtn').click(function(){
                   $.ajax({
                        url: "./process-form.php",
                        data: {
                            login: true,
                            loginUsername: $('#inputUsername').val(), 
                            loginPassword: $('#inputPassword').val()
                        },
                        type: "POST",
                        async: true,
                        //dataType: 'json',
                        success: function(data) 
                        {
                            switch(data)
                            {
                                case "Ok":
                                    $('#loginFormMessage').html("");
                                    location.href = "dashboards.php?fromSubmenu=false&linkId=dashboardsLink";
                                    break;
                                    
                                default:
                                    $('#loginFormMessage').parents('div.modalCell').show();
                                    $('#loginFormMessage').html(data);
                                    break;
                            }
                        },
                        error: function(data)
                        {
                            $('#loginFormMessage').parents('div.modalCell').show();
                            $('#loginFormMessage').html("Error in login call, open console for details");
                            console.log("Error: " + data);
                        }
                    });
               });
            });
        </script>
   
