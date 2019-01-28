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
   /*include('process-form.php');
   
   session_start();
    
   $envFile = parse_ini_file("../conf/environment.ini");
   
    if(!isset($_SESSION['loggedRole'])&&($envFile['environment']['value'] != 'dev'))
    {
        header("location: ssoLogin.php");
    }*/
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Snap4City</title>

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
                <div class="col-xs-12 centerWithFlex" id="loginMainTitle">Snap4City</div>  
                <div class="col-xs-12" id="loginLeftCol">
                    <div class="col-xs-12" id="loginFeaturesContainer">
                        <div class="row">
                            <div class="col-xs-12 loginFeaturesCell">
                                <div class="col-xs-12 loginFeaturesCellTxt" style="font-size: 36px">
                                    The dashboard you tried to open is not available: it may have been deleted or have technical issues.
                                </div>
                                <div class="col-xs-12 loginFeaturesCellContent centerWithFlex">
                                    <i class="fa fa-close" style="color: #f3cf58; font-size: 72px"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xs-12 centerWithFlex" id="loginFooter"><a href="https://www.disit.org" target="_blank">DISIT</a></div>  
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
   
