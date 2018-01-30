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

        <!-- jQuery -->
        <script src="../js/jquery-1.10.1.min.js"></script>
        
        <!-- Bootstrap core CSS -->
        <link href="../css/bootstrap.css" rel="stylesheet">
        <script src="../js/bootstrap.min.js"></script>
        
        <!-- JQUERY UI -->
        <script src="../js/jqueryUi/jquery-ui.js"></script>
        
        <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
        
        <!--<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
        
        <!-- Highcharts -->
        <script src="../js/highcharts/code/highcharts.js"></script>
        <script src="../js/highcharts/code/modules/exporting.js"></script>
        <script src="../js/highcharts/code/highcharts-more.js"></script>
        <script src="../js/highcharts/code/modules/solid-gauge.js"></script>
        <script src="../js/highcharts/code/highcharts-3d.js"></script>
        
        <link href="../css/dashboard.css" rel="stylesheet">
        <!--<link href="../css/pageTemplate.css" rel="stylesheet">-->
    </head>
    <body id="loginBody" class="guiPageBody">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 centerWithFlex" id="loginMainTitle">Dashboard Management System</div>  
                <div class="hidden-xs hidden-sm col-md-6" id="loginLeftCol">
                    <!--<div class="row">
                        <div class="col-md-12 loginFeaturesCell">
                            <div class="col-xs-12 loginFeaturesCellTxt">
                                37 ready-to-use widget types
                            </div>
                            <div id="loginCarousel" data-ride="carousel" class="col-xs-12 loginFeaturesCellContent carousel slide">
                                <div class="carousel-inner" role="listbox">
                                    <div id="loginCarousel1" class="item active centerWithFlex">
                                        One
                                    </div>
                                    <div id="loginCarousel2" class="item centerWithFlex">
                                        Two
                                    </div>
                                    <div id="loginCarousel3" class="item centerWithFlex">
                                        Three
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>-->
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
                        <form id="loginForm" role="form" method="post" action="">
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
                               <button type="submit" id="loginConfirmBtn" name="login" class="btn confirmBtn internalLink">Login</button>
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
               
               //$("#loginCarousel .carousel-inner > .item").width($("#loginCarousel").width());
               
               $(window).resize(function(){
                    var colsHeight = $('#loginBody').height() - $('#loginMainTitle').height() - $('#loginFooter').height();
                    $('#loginLeftCol').height(colsHeight);
                    $('#loginRightCol').height(colsHeight);
                    var loginFormCntMargin = parseInt((colsHeight - $('#loginFormContainer').height()) / 2);
                    $('#loginFormContainer').css("margin-top", loginFormCntMargin + "px");
                    var loginFeaturesContainerMargin = parseInt((colsHeight - $('#loginFeaturesContainer').height()) / 2);
                    $('#loginFeaturesContainer').css("margin-top", loginFeaturesContainerMargin + "px");
               });
               
               /*Highcharts.chart('loginCarousel1', {
                    chart: {
                        type: 'bar',
                        backgroundColor: 'transparent'
                    },
                    exporting: {
                      enabled: false  
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: ['Africa', 'America', 'Asia', 'Europe', 'Oceania'],
                        title: {
                            text: null
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valueSuffix: ''
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -40,
                        y: 80,
                        floating: true,
                        borderWidth: 1,
                        backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                        shadow: true
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: 'Year 1800',
                        data: [107, 31, 635, 203, 2]
                    }, {
                        name: 'Year 1900',
                        data: [133, 156, 947, 408, 6]
                    }, {
                        name: 'Year 2012',
                        data: [1052, 954, 4250, 740, 38]
                    }]
                });   

                Highcharts.chart('loginCarousel2', {
                    chart: {
                        type: 'area',
                        backgroundColor: 'transparent'
                    },
                    title: {
                        text: ''
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                      enabled: false  
                    },
                    xAxis: {
                        allowDecimals: false,
                        labels: {
                            formatter: function () {
                                return this.value; // clean, unformatted number for year
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: ''
                        },
                        labels: {
                            formatter: function () {
                                return this.value / 1000 + 'k';
                            }
                        }
                    },
                    plotOptions: {
                        area: {
                            pointStart: 1940,
                            marker: {
                                enabled: false,
                                symbol: 'circle',
                                radius: 2,
                                states: {
                                    hover: {
                                        enabled: true
                                    }
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'USA',
                        data: [null, null, null, null, null, 6, 11, 32, 110, 235, 369, 640,
                            1005, 1436, 2063, 3057, 4618, 6444, 9822, 15468, 20434, 24126,
                            27387, 29459, 31056, 31982, 32040, 31233, 29224, 27342, 26662,
                            26956, 27912, 28999, 28965, 27826, 25579, 25722, 24826, 24605,
                            24304, 23464, 23708, 24099, 24357, 24237, 24401, 24344, 23586,
                            22380, 21004, 17287, 14747, 13076, 12555, 12144, 11009, 10950,
                            10871, 10824, 10577, 10527, 10475, 10421, 10358, 10295, 10104]
                    }, {
                        name: 'USSR/Russia',
                        data: [null, null, null, null, null, null, null, null, null, null,
                            5, 25, 50, 120, 150, 200, 426, 660, 869, 1060, 1605, 2471, 3322,
                            4238, 5221, 6129, 7089, 8339, 9399, 10538, 11643, 13092, 14478,
                            15915, 17385, 19055, 21205, 23044, 25393, 27935, 30062, 32049,
                            33952, 35804, 37431, 39197, 45000, 43000, 41000, 39000, 37000,
                            35000, 33000, 31000, 29000, 27000, 25000, 24000, 23000, 22000,
                            21000, 20000, 19000, 18000, 18000, 17000, 16000]
                    }]
                });

                Highcharts.chart('loginCarousel3', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        backgroundColor: 'transparent'
                    },
                    exporting: {
                      enabled: false  
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: ''
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Brands',
                        colorByPoint: true,
                        data: [{
                            name: 'IE',
                            y: 56.33
                        }, {
                            name: 'Chrome',
                            y: 24.03,
                            sliced: true,
                            selected: true
                        }, {
                            name: 'Firefox',
                            y: 10.38
                        }, {
                            name: 'Safari',
                            y: 4.77
                        }, {
                            name: 'Opera',
                            y: 0.91
                        }, {
                            name: 'Other',
                            y: 0.2
                        }]
                    }]
                });  
               
               $('#loginCarousel').carousel({
                   interval: 3200,
                   pause: null
               });*/
               
               /*$("#button_login").click(function()
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
               });*/
            });
        </script>
   
