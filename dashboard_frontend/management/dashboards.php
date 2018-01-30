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
    include('process-form.php');
    session_start();
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    
    if(!isset($_SESSION['loggedRole']))
    {
        header("location: unauthorizedUser.php");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dashboard Management System</title>

        <!-- Bootstrap Core CSS -->
        <link href="../css/bootstrap.css" rel="stylesheet">

        <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">

        <!-- jQuery -->
        <script src="../js/jquery-1.10.1.min.js"></script>

        <!-- JQUERY UI -->
        <script src="../js/jqueryUi/jquery-ui.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="../js/bootstrap.min.js"></script>

        <!-- Custom Core JavaScript -->
        <script src="../js/bootstrap-colorpicker.min.js"></script>

        <!-- Bootstrap toggle button -->
       <link href="../bootstrapToggleButton/css/bootstrap-toggle.min.css" rel="stylesheet">
       <script src="../bootstrapToggleButton/js/bootstrap-toggle.min.js"></script>
       
       <!-- Bootstrap editable tables -->
       <link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
       <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

       <!-- Bootstrap table -->
       <link rel="stylesheet" href="../boostrapTable/dist/bootstrap-table.css">
       <script src="../boostrapTable/dist/bootstrap-table.js"></script>
       <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
       <script src="../boostrapTable/dist/locale/bootstrap-table-en-US.js"></script>
       
       <!-- Bootstrap slider -->
        <script src="../bootstrapSlider/bootstrap-slider.js"></script>
        <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet"/>
        
        <!-- Filestyle -->
        <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>

       <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

        <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        
        <!-- Custom CSS -->
        <link href="../css/dashboard.css" rel="stylesheet">
        
        <!-- Custom scripts -->
        <script type="text/javascript" src="../js/dashboard_mng.js"></script>
        
        <!--<link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
    </head>
    <body class="guiPageBody">
        <div class="container-fluid">
            <?php include "sessionExpiringPopup.php" ?> 
            
            <div class="row mainRow">
                <?php include "mainMenu.php" ?>
                <div class="col-xs-12 col-md-10" id="mainCnt">
                    <div class="row hidden-md hidden-lg">
                        <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                            Dashboard Management System
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-10 col-md-12 centerWithFlex" id="headerTitleCnt">Dashboards</div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <div class="row hidden-xs hidden-sm mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">Synthesis</div>
                                <div id="dashboardTotNumberCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $loggedUsername = $_SESSION['loggedUsername'];
                                            $loggedRole = $_SESSION['loggedRole'];    
                                            switch($loggedRole)
                                            {
                                                //Gestisce solo le proprie dashboard
                                                case "Manager":
                                                    $query = "SELECT count(*) AS qt FROM Dashboard.Config_dashboard WHERE Config_dashboard.user = '$loggedUsername'";
                                                    break;

                                                //Gestisce le proprie dashboard e di quelle dei manager dei pools di cui è admin 
                                                case "AreaManager":
                                                   $query = "SELECT count(*) AS qt FROM Dashboard.Config_dashboard AS dashes " .
                                                            "WHERE dashes.user = '$loggedUsername' " . //Proprie dashboard
                                                            "OR (dashes.user IN (SELECT username FROM Dashboard.UsersPoolsRelations WHERE poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$loggedUsername' AND isAdmin = 1))) ";
                                                   break;

                                                 //Gestisce tutte le dashboards
                                                 case "ToolAdmin":
                                                    $query = "SELECT count(*) AS qt FROM Dashboard.Config_dashboard";
                                                    break;
                                            }
                                            
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               $dashboardsQt = $row['qt'];
                                               echo $row['qt'];
                                            }
                                            else
                                            {
                                                $dashboardsQt = "-";
                                                echo '-';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        dashboards
                                    </div>
                                </div>
                                <div id="dashboardTotActiveCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            switch($loggedRole)
                                            {
                                                //Gestisce solo le proprie dashboard
                                                case "Manager":
                                                    $query = "SELECT count(*) AS qt FROM Dashboard.Config_dashboard WHERE Config_dashboard.user = '$loggedUsername' AND status_dashboard = 1";
                                                    break;

                                                //Gestisce le proprie dashboard e di quelle dei manager dei pools di cui è admin 
                                                case "AreaManager":
                                                   $query = "SELECT count(*) AS qt FROM Dashboard.Config_dashboard AS dashes " .
                                                            "WHERE (dashes.user = '$loggedUsername' AND dashes.status_dashboard = 1)" . //Proprie dashboard
                                                            "OR (dashes.user IN (SELECT username FROM Dashboard.UsersPoolsRelations WHERE poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$loggedUsername' AND isAdmin = 1))) ";
                                                   break;

                                                 //Gestisce tutte le dashboards
                                                 case "ToolAdmin":
                                                    $query = "SELECT count(*) AS qt FROM Dashboard.Config_dashboard WHERE status_dashboard = 1";
                                                    break;
                                            }
                                            
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               $dashboardsActiveQt = $row['qt'];
                                               echo $row['qt'];
                                            }
                                            else
                                            {
                                                $dashboardsActiveQt = "-";
                                                echo '-';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        active
                                    </div>
                                </div>
                                <div id="dashboardTotPermCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            switch($loggedRole)
                                            {
                                                //Gestisce solo le proprie dashboard
                                                case "Manager":
                                                    $query = "SELECT count(*) AS qt FROM Dashboard.Config_dashboard WHERE Config_dashboard.user = '$loggedUsername' AND visibility = 'public'";
                                                    break;

                                                //Gestisce le proprie dashboard e di quelle dei manager dei pools di cui è admin 
                                                case "AreaManager":
                                                   $query = "SELECT count(*) AS qt FROM Dashboard.Config_dashboard AS dashes " .
                                                            "WHERE (dashes.user = '$loggedUsername' AND dashes.visibility = 'public')" . //Proprie dashboard
                                                            "OR (dashes.user IN (SELECT username FROM Dashboard.UsersPoolsRelations WHERE poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$loggedUsername' AND isAdmin = 1))) ";
                                                   break;

                                                 //Gestisce tutte le dashboards
                                                 case "ToolAdmin":
                                                    $query = "SELECT count(*) AS qt FROM Dashboard.Config_dashboard WHERE visibility = 'public'";
                                                    break;
                                            }
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               $dashboardsPublicQt = $row['qt'];
                                               echo $row['qt'];
                                            }
                                            else
                                            {
                                                $dashboardsPublicQt = "-";
                                                echo '-';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        public
                                    </div>
                                </div>
                                <div id="dashboardLastCnt" class="col-md-6 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex lastDashName">
                                        <?php
                                            switch($loggedRole)
                                            {
                                                //Gestisce solo le proprie dashboard
                                                case "Manager":
                                                    $query = "SELECT * FROM Dashboard.Config_dashboard WHERE Config_dashboard.user = '$loggedUsername' ORDER BY creation_date DESC LIMIT 1";
                                                    break;

                                                //Gestisce le proprie dashboard e di quelle dei manager dei pools di cui è admin 
                                                case "AreaManager":
                                                   $query = "SELECT * FROM Dashboard.Config_dashboard AS dashes " .
                                                            "WHERE dashes.user = '$loggedUsername'" . //Proprie dashboard
                                                            "OR (dashes.user IN (SELECT username FROM Dashboard.UsersPoolsRelations WHERE poolId IN (SELECT poolId FROM Dashboard.UsersPoolsRelations WHERE username = '$loggedUsername' AND isAdmin = 1))) " .
                                                            "ORDER BY creation_date DESC LIMIT 1";
                                                   break;

                                                 //Gestisce tutte le dashboards
                                                 case "ToolAdmin":
                                                    $query = "SELECT * FROM Dashboard.Config_dashboard ORDER BY creation_date DESC LIMIT 1";
                                                    break;
                                            }
                                            
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               $lastDashId = $row['Id'];
                                               $lastDashTitle = $row['title_header'];
                                               $lastDashCreator = $row['user'];
                                               $lastDashDate = $row['creation_date'];
                                               echo '<a href="../view/index.php?iddasboard=' . base64_encode($lastDashId) . '" target="_blank">' . $lastDashTitle . '</a>';
                                            }
                                            else
                                            {
                                                echo '-';
                                                $lastDashId = null;
                                                $lastDashTitle = null;
                                                $lastDashCreator = null;
                                                $lastDashDate = null;
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        last created
                                    </div>
                                </div>
                            </div>
                            <div class="row mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">List</div>
                                <div class="col-xs-12 mainContentCellCnt">
                                    <table id="list_dashboard" class="table"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modale creazione dashboard -->
        <div class="modal fade" id="modalCreateDashboard" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <form id="form-setting-dashboard" class="form-horizontal" name="form-setting-dashboard" role="form" method="post" action="" data-toggle="validator" enctype="multipart/form-data">  
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Create dashboard
                </div>

                <div id="addDashboardModalBody" class="modal-body modalBody">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active"><a data-toggle="tab" href="#measuresTab">Measures</a></li>
                        <li><a data-toggle="tab" href="#headerTab">Header</a></li>
                        <li><a data-toggle="tab" href="#bodyTab">Body</a></li>
                        <li><a data-toggle="tab" href="#visibilityTab">Visibility</a></li>
                        <li><a data-toggle="tab" href="#embeddabilityTab">Embeddability</a></li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- Measures tab -->
                        <div id="measuresTab" class="tab-pane fade in active">
                            <div class="row">
                                <div class="col-xs-12 col-md-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <input id="inputWidthDashboard" name="inputWidthDashboard" data-slider-id="inputWidthDashboardSlider" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="10"/>
                                    </div>
                                    <div class="modalFieldLabelCnt">Width (cells)</div>
                                </div>
                                <div class="col-xs-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="pixelWidth" id="pixelWidth" disabled> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Pixel width</div>
                                </div>
                                <div class="col-xs-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="percentWidth" id="percentWidth" disabled> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Width (%) on your screen</div>
                                </div>
                            </div>
                        </div>
                        <!-- Header tab -->
                        <div id="headerTab" class="tab-pane fade">
                            <div class="row">
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="inputTitleDashboard" id="inputTitleDashboard" required> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Title</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="inputSubTitleDashboard" id="inputSubTitleDashboard"> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Subtitle</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <div class="input-group customColorChoice">
                                            <input type="text" class="modalInputTxt" id="inputColorDashboard" name="inputColorDashboard" value="#5367ce" required>
                                            <span class="input-group-addon"><i></i></span>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">Header color</div>
                                </div>
                                <div class="col-xs-12 col-md-2 col-md-offset-2 modalCell">
                                    <div class="modalFieldCnt">
                                        <input id="headerVisible" name="headerVisible" checked type="checkbox">
                                    </div>
                                    <div class="modalFieldLabelCnt">Show header</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input id="headerFontSize" name="headerFontSize" data-slider-id="headerFontSizeSlider" type="text" data-slider-min="1" data-slider-max="36" data-slider-step="1" data-slider-value="28"/>
                                    </div>
                                    <div class="modalFieldLabelCnt">Header font size</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <div class="input-group customColorChoice">
                                            <input type="text" class="modalInputTxt" id="headerFontColor" name="headerFontColor" value="#ffffff">
                                            <span class="input-group-addon"><i id="color_hf"></i></span>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">Header font color</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input id="dashboardLogoInput" name="dashboardLogoInput" type="file" class="filestyle modalInputTxt" data-badge="false" data-input ="true" data-size="nr" data-buttonName="btn-primary" data-buttonText="File">
                                    </div>
                                    <div class="modalFieldLabelCnt">Header logo</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" class="modalInputTxt" name="dashboardLogoLinkInput" id="dashboardLogoLinkInput"> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Header logo link</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Body tab -->
                        <div id="bodyTab" class="tab-pane fade">
                            <div class="row">
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <div class="input-group customColorChoice">
                                            <input type="text" class="modalInputTxt" id="inputColorBackgroundDashboard" name="inputColorBackgroundDashboard" value="#ffffff" required>
                                            <span class="input-group-addon"><i></i></span>
                                        </div> 
                                    </div>
                                    <div class="modalFieldLabelCnt">Widgets area color</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <div class="input-group customColorChoice">
                                            <input type="text" class="modalInputTxt" id="inputExternalColorDashboard" name="inputExternalColorDashboard" value="#ffffff" required>
                                            <span class="input-group-addon"><i></i></span>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">External frame color</div>
                                </div>
                                <div class="col-xs-12 col-md-4 col-md-offset-1 modalCell">
                                    <input id="widgetsBorders" name="widgetsBorders" checked type="checkbox">
                                    <div class="modalFieldLabelCnt">Widgets borders</div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-md-offset-1 modalCell">
                                    <div class="modalFieldCnt">
                                        <div class="input-group customColorChoice">
                                            <input type="text" class="modalInputTxt" id="inputWidgetsBordersColor" name="inputWidgetsBordersColor" value="#dddddd" required>
                                            <span class="input-group-addon"><i></i></span>
                                        </div>
                                    </div>
                                    <div class="modalFieldLabelCnt">Widgets borders color</div>
                                </div>
                            </div>    
                        </div>
                        
                        <!-- Visibility tab -->
                        <div id="visibilityTab" class="tab-pane fade">
                            <div class="row">
                                <div class="col-xs-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <select name="inputDashboardVisibility" class="modalInputTxt" id="inputDashboardVisibility" required>
                                            <option value="author">Dashboard author only</option>
                                            <option value="restrict">Author and selected users</option>
                                            <option value="public">Everybody (public)</option>
                                        </select>
                                    </div>
                                    <!--<div class="modalFieldLabelCnt">Permission type</div>-->
                                </div>
                                <div class="col-xs-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <table id="inputDashboardVisibilityUsersTable"></table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Embeddability tab -->
                        <div id="embeddabilityTab" class="tab-pane fade">
                            <div class="row">
                                <div class="col-xs-12 modalCell">
                                    <div class="modalFieldCnt">
                                        <table id="authorizedPagesTable">
                                            <thead>
                                                <th>Authorized pages</th>
                                                <th><i id="addAuthorizedPageBtn" class="fa fa-plus"></i></th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                    <input type="hidden" id="authorizedPagesJson" name="authorizedPagesJson" />
                                </div>
                            </div>
                        </div>
                    </div>
                <div id="addDashboardModalFooter" class="modal-footer">
                  <button type="button" id="addDashboardCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="submit" id="addDashboardConfirmBtn" name="addDashboard" class="btn confirmBtn internalLink">Confirm</button>
                </div>
              </div>
            </form>  
            </div>
        </div>
    </body>
</html>

<script type='text/javascript'>
    $(document).ready(function () 
    {
        var sessionEndTime = "<?php echo $_SESSION['sessionEndTime']; ?>";
        $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
        $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");
        
        setInterval(function(){
            var now = parseInt(new Date().getTime() / 1000);
            var difference = sessionEndTime - now;
            
            if(difference === 300)
            {
                $('#sessionExpiringPopupTime').html("5 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function(){
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function(){
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }
            
            if(difference === 120)
            {
                $('#sessionExpiringPopupTime').html("2 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function(){
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function(){
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }
            
            if((difference > 0)&&(difference <= 60))
            {
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                $('#sessionExpiringPopupTime').html(difference + " seconds");
            }
            
            if(difference <= 0)
            {
                location.href = "logout.php?sessionExpired=true";
            }
        }, 1000);
        
        $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        
        $(window).resize(function(){
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
            $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");
            
            if($(window).width() < 992)
            {
                $('#list_dashboard').bootstrapTable('hideColumn', 'user');
                $('#list_dashboard').bootstrapTable('hideColumn', 'status_dashboard');
            }
            else
            {
                $('#list_dashboard').bootstrapTable('showColumn', 'user');
                $('#list_dashboard').bootstrapTable('showColumn', 'status_dashboard');
            }
        });
        
        $('#dashboardsLink .mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt #dashboardsLink .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt #dashboardsLink .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        $('#inputWidthDashboard').bootstrapSlider({
            tooltip_position: 'top',
            tooltip: 'always'
        });
        
        var cols = parseInt($('#inputWidthDashboard').val());
        var px = parseInt(cols*78 + 10);
        var percent = parseInt(px/screen.width*100);
        $('#pixelWidth').val(px + " px");
        $('#percentWidth').val(percent + " %");
        
        $('#headerVisible').bootstrapToggle({
            on: 'Yes',
            off: 'No',
            onstyle: 'info',
            offstyle: 'default',
            size: 'normal'
        });
        
        $('#headerFontSize').bootstrapSlider({
            tooltip_position: 'top'
        });
        
        $('#widgetsBorders').bootstrapToggle({
            on: 'Yes',
            off: 'No',
            onstyle: 'info',
            offstyle: 'default',
            size: 'normal'
        });
        
        /*$('#embeddable').bootstrapToggle({
            on: 'Yes',
            off: 'No',
            onstyle: 'info',
            offstyle: 'default',
            size: 'normal'
        });*/
        
        //$('#dashboardLastCnt').height($('#dashboardTotNumberCnt').height());
        
        var loggedRole = "<?= $_SESSION['loggedRole'] ?>";
            var loggedType = "<?= $_SESSION['loggedType'] ?>";
            var usr = "<?= $_SESSION['loggedUsername'] ?>";
            var userVisibilitySet = null;
            var authorizedPages = [];
            var internalDest = false;
            var tableFirstLoad = true;
            
            $('.internalLink').on('mousedown', function(){
                internalDest = true;
            });

            /*$(window).on('beforeunload', function(){
                $.ajax({
                        url: "iframeProxy.php",
                        action: "notificatorRemoteLogout",
                        async: false,
                        success: function()
                        {
                            console.log("Remote logout from Notificator OK");
                        },
                        error: function(errorData)
                        {
                            console.log("Remote logout from Notificator failed");
                            console.log(JSON.stringify(errorData));
                        }
                    });
                if(internalDest === false)
                {
                    console.log("Logout notificatore");
                    $.ajax({
                        url: "iframeProxy.php",
                        action: "notificatorRemoteLogout",
                        async: false,
                        success: function()
                        {
                            console.log("Remote logout from Notificator OK");
                        },
                        error: function(errorData)
                        {
                            console.log("Remote logout from Notificator failed");
                            console.log(JSON.stringify(errorData));
                        }
                    });
                }
                else
                {
                    console.log("Navigazione interna");
                    return 'Are you sure you want to leave?';
                }
            });*/
            
            $('#authorizedPagesJson').val(JSON.stringify(authorizedPages));
            //$('label[for=authorizedPages]').hide();
            //$('#authorizedPagesTable').parent().hide();
            $('#color_hf').css("background-color", '#ffffff');
            
            /*$('#embeddable').change(function(){
                if($(this).prop('checked')) 
                {
                    $('label[for=authorizedPages]').show();
                    $('#authorizedPagesTable').parent().show();
                }
                else
                {
                    $('label[for=authorizedPages]').hide();
                    $('#authorizedPagesTable').parent().hide();
                    $('#authorizedPagesTable tbody').empty();
                    authorizedPages = [];
                    $('#authorizedPagesJson').val("");
                }
            });*/
            
            $('#addAuthorizedPageBtn').off("click");
            $('#addAuthorizedPageBtn').click(function(){
                 var row = $('<tr><td><a href="#" class="toBeEdited" data-type="text" data-mode="popup"></a></td><td><i class="fa fa-minus"></i></td></tr>');
                 $('#authorizedPagesTable tbody').append(row);
                 
                 var rowIndex = row.index();
                 
                 row.find('a').editable({
                    emptytext: "Empty",
                    display: function(value, response){
                        if(value.length > 35)
                        {
                            $(this).html(value.substring(0, 32) + "...");
                        }
                        else
                        {
                           $(this).html(value); 
                        }
                    }
                });
                
                authorizedPages[rowIndex] = null;
                $('#authorizedPagesJson').val(JSON.stringify(authorizedPages));
                
                row.find('i.fa-minus').off("click");
                row.find('i.fa-minus').click(function(){
                    var rowIndex = $(this).parents('tr').index();
                    $('#authorizedPagesTable tbody tr').eq(rowIndex).remove();
                    authorizedPages.splice(rowIndex, 1);
                    $('#authorizedPagesJson').val(JSON.stringify(authorizedPages));
                });
                
                row.find('a.toBeEdited').off("save");
                row.find('a.toBeEdited').on('save', function(e, params){
                    var rowIndex = $(this).parents('tr').index();
                    authorizedPages[rowIndex] = params.newValue;
                    $('#authorizedPagesJson').val(JSON.stringify(authorizedPages));
                });
            });
            
            setGlobals(loggedRole, usr, loggedType, userVisibilitySet);
            
            $("#logoutBtn").off("click");
            $("#logoutBtn").click(function(event)
            {
               event.preventDefault();
               location.href = "logout.php";
               /*$.ajax({
                    url: "iframeProxy.php",
                    action: "notificatorRemoteLogout",
                    async: true,
                    success: function()
                    {
                        
                    },
                    error: function(errorData)
                    {
                        console.log("Remote logout from Notificator failed");
                        console.log(JSON.stringify(errorData));
                    },
                    complete: function()
                    {
                        location.href = "logout.php";
                    }
                });*/
            });
            
            $('#inputDashboardVisibility').change(function(){
               if($(this).val() === 'restrict') 
               {
                   /*$('label[for="inputDashboardVisibilityUsersTable"]').show();
                   $('#inputDashboardVisibilityUsersTableContainer').show();*/
                   $('#inputDashboardVisibilityUsersTable').show();
               }
               else
               {
                   /*$('label[for="inputDashboardVisibilityUsersTable"]').hide();
                   $('#inputDashboardVisibilityUsersTableContainer').hide();*/
                   $('#inputDashboardVisibilityUsersTable').hide();
               }
            });
            
            $('#inputWidthDashboard').on('slide',function(e)
            {
                var cols = parseInt(e.value);
                var px = parseInt(cols*78 + 10);
                var percent = parseInt(px/screen.width*100);
                $('#pixelWidth').val(px + " px");
                $('#percentWidth').val(percent + " %");
            });
            
            $.ajax({
                url: "get_data.php",
                data: {
                    action: "get_dashboards"
                },
                type: "GET",
                async: true,
                dataType: 'json',
                success: function(data) 
                {
                    var creatorVisibile = true;
                    var detailView = true;
                    var statusVisibile = true;
                    
                    if($(window).width() < 992)
                    {
                        detailView = false;
                        creatorVisibile = false; 
                        statusVisibile = false;
                    }
                    
                    $('#list_dashboard').bootstrapTable({
                        columns: [
                        {
                            field: 'title_header',
                            title: 'Title',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                if(value !== null)
                                {
                                    if(value.length > 75)
                                    {
                                       return value.substr(0, 75) + " ...";
                                    }
                                    else
                                    {
                                       return value;
                                    } 
                                }
                            },
                            cellStyle: function(value, row, index, field) {
                                var fontSize = "1em"; 
                                if($(window).width() < 992)
                                {
                                    fontSize = "0.9em";
                                }
                                
                                if(index%2 !== 0)
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "color": "rgba(51, 64, 69, 1)", 
                                            "font-size": fontSize,
                                            "font-weight": "bold",
                                            "background-color": "rgb(230, 249, 255)",
                                            "border-top": "none"
                                        }
                                    };
                                }
                                else
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "color": "rgba(51, 64, 69, 1)", 
                                            "font-size": fontSize,
                                            "font-weight": "bold",
                                            "background-color": "white",
                                            "border-top": "none"
                                        }
                                    };
                                }
                            }
                        },
                        {
                            field: 'user',
                            title: 'Creator',
                            sortable: true,
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            visible: creatorVisibile,
                            formatter: function(value, row, index)
                            {
                                if(value !== null)
                                {
                                    if(value.length > 50)
                                    {
                                       return value.substr(0, 50) + " ...";
                                    }
                                    else
                                    {
                                       return value;
                                    } 
                                }
                            },
                            cellStyle: function(value, row, index, field) {
                                if(index%2 !== 0)
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "rgb(230, 249, 255)",
                                            "border-top": "none"
                                        }
                                    };
                                }
                                else
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "white",
                                            "border-top": "none"
                                        }
                                    };
                                }
                            }
                        },
                        {
                            field: 'status_dashboard',
                            title: "Status",
                            align: "center",
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            visible: statusVisibile,
                            formatter: function(value, row, index)
                            {
                                if((value === '0')||(value === 0))
                                {
                                    return '<input type="checkbox" data-toggle="toggle" class="changeDashboardStatus">';
                                }
                                else
                                {
                                    return '<input type="checkbox" checked data-toggle="toggle" class="changeDashboardStatus">';
                                }
                            },
                            cellStyle: function(value, row, index, field) {
                                if(index%2 !== 0)
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "rgb(230, 249, 255)",
                                            "border-top": "none"
                                        }
                                    };
                                }
                                else
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "white",
                                            "border-top": "none"
                                        }
                                    };
                                }
                            }
                        },
                        {
                            title: "",
                            align: "center",
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                return '<button type="button" class="editDashBtn">edit</button>';
                            },
                            cellStyle: function(value, row, index, field) {
                                if(index%2 !== 0)
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "rgb(230, 249, 255)",
                                            "border-top": "none"
                                        }
                                    };
                                }
                                else
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "white",
                                            "border-top": "none"
                                        }
                                    };
                                }
                            }
                        },
                        {
                            title: "",
                            align: "center",
                            valign: "middle",
                            align: "center",
                            halign: "center",
                            formatter: function(value, row, index)
                            {
                                return '<button type="button" class="viewDashBtn">view</button>';
                            },
                            cellStyle: function(value, row, index, field) {
                                if(index%2 !== 0)
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "rgb(230, 249, 255)",
                                            "border-top": "none"
                                        }
                                    };
                                }
                                else
                                {
                                    return {
                                        classes: null,
                                        css: {
                                            "background-color": "white",
                                            "border-top": "none"
                                        }
                                    };
                                }
                            }
                        }
                        ],
                        data: data,
                        search: true,
                        pagination: true,
                        pageSize: 10,
                        locale: 'en-US',
                        searchAlign: 'left',
                        uniqueId: "Id",
                        striped: false,
                        classes: "table table-hover table-no-bordered",
                        detailView: detailView,
                        detailFormatter: function(index, row, element) {
                            return 'Creation date: ' + data[index].creation_date + ' | Visibility: ' + data[index].visibility + " | Embeddable: " + data[index].embeddable;
                        },
                        searchTimeOut: 250,
                        onPostBody: function()
                        {
                            if(tableFirstLoad)
                            {
                                //Caso di primo caricamento della tabella
                                tableFirstLoad = false;
                                var addDasboardDiv = $('<div class="pull-right"><i id="link_add_dashboard" data-toggle="modal" data-target="#modal-add-metric" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                                $('div.fixed-table-toolbar').append(addDasboardDiv);
                                addDasboardDiv.css("margin-top", "10px");
                                addDasboardDiv.find('i.fa-plus-square').off('hover');
                                addDasboardDiv.find('i.fa-plus-square').hover(function(){
                                    $(this).css('color', '#e37777');
                                    $(this).css('cursor', 'pointer');
                                }, 
                                function(){
                                    $(this).css('color', '#ffcc00');
                                    $(this).css('cursor', 'normal');
                                });
                                
                                $('#link_add_dashboard').off('click');
                                $('#link_add_dashboard').click(function(){
                                    authorizedPages = [];
                                    $('#modalCreateDashboard').modal('show');
                                });
                                
                                $('#list_dashboard thead').css("background", "rgba(0, 162, 211, 1)");
                                $('#list_dashboard thead').css("color", "white");
                                $('#list_dashboard thead').css("font-size", "1.1em");
                            }
                            else
                            {
                                //Casi di cambio pagina
                            }

                            //Istruzioni da eseguire comunque
                            $('#list_dashboard tbody tr').each(function(i){
                                if(i%2 !== 0)
                                {
                                    $(this).find('td').eq(0).css("background-color", "rgb(230, 249, 255)");
                                    $(this).find('td').eq(0).css("border-top", "none");
                                }
                                else
                                {
                                    $(this).find('td').eq(0).css("background-color", "white");
                                    $(this).find('td').eq(0).css("border-top", "none");
                                }
                            });
                            
                            $('#list_dashboard').css("border-bottom", "none");
                            $('span.pagination-info').hide();

                            $('#list_dashboard button.editDashBtn').off('hover');
                            $('#list_dashboard button.editDashBtn').hover(function(){
                                $(this).css('background', '#ffcc00');
                                $(this).parents('tr').find('td').eq(1).css('background', '#ffcc00');
                            }, 
                            function(){
                                $(this).css('background', 'rgb(69, 183, 175)');
                                $(this).parents('tr').find('td').eq(1).css('background', $(this).parents('td').css('background'));
                            });
                            
                            $('#list_dashboard button.viewDashBtn').off('hover');
                            $('#list_dashboard button.viewDashBtn').hover(function(){
                                $(this).css('background', '#ffcc00');
                                $(this).parents('tr').find('td').eq(1).css('background', '#ffcc00');
                            }, 
                            function(){
                                $(this).css('background', 'rgba(0, 162, 211, 1)');
                                $(this).parents('tr').find('td').eq(1).css('background', $(this).parents('td').css('background'));
                            });
                            
                            $('#list_dashboard button.viewDashBtn').off('click');
                            $('#list_dashboard button.viewDashBtn').click(function () 
                            {
                                var dashboardId = $(this).parents('tr').attr("data-uniqueid");
                                window.open("../view/index.php?iddasboard=" + btoa(dashboardId));
                            });
                            
                            $('#list_dashboard input.changeDashboardStatus').bootstrapToggle({
                                on: "On",
                                off: "Off",
                                onstyle: "primary",
                                offstyle: "default",
                                size: "mini"
                            });

                            $('#list_dashboard tbody input.changeDashboardStatus').off('change');
                            $('#list_dashboard tbody input.changeDashboardStatus').change(function() {
                                if($(this).prop('checked') === false)
                                {
                                    var newStatus = 0;
                                }
                                else
                                {
                                    var newStatus = 1;
                                }

                                $.ajax({
                                    url: "process-form.php",
                                    data: {
                                        modify_status_dashboard: true,
                                        dashboardId: $(this).parents('tr').attr('data-uniqueid'),
                                        newStatus: newStatus
                                    },
                                    type: "POST",
                                    async: true,
                                    success: function(data)
                                    {
                                        if(data !== "Ok")
                                        {
                                            console.log("Error updating dashboard status");
                                            console.log(data);
                                            alert("Error updating dashboard status");
                                            location.reload();
                                        }
                                        else
                                        {
                                            if($('#dashboardTotActiveCnt .pageSingleDataCnt').html() !== "-")
                                            {
                                                if(newStatus === 0)
                                                {
                                                    $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) - 1);
                                                }
                                                else
                                                {
                                                    $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) + 1);
                                                }
                                            }
                                        }
                                    },
                                    error: function(errorData)
                                    {
                                        console.log("Error updating dashboard status");
                                        console.log(errorData);
                                        alert("Error updating dashboard status");
                                        location.reload();
                                    }
                                });
                            });

                            $('#list_dashboard tbody button.editDashBtn').off('click');
                            $('#list_dashboard tbody button.editDashBtn').click(function() 
                            {
                                var dashboardId = $(this).parents('tr').attr('data-uniqueid');
                                location.href = "process-form.php?openDashboardToEdit=true&dashboardId=" + dashboardId;
                            });
                        }
                    });
                },
                error: function(errorData)
                {
                    console.log("KO");
                    console.log(errorData);
                }
            });

            $('#dashboardLogoInput').change(function ()
            {
                $('#dashboardLogoLinkInput').removeAttr('disabled');
            });

            
            $('.customColorChoice').colorpicker({
                format: "rgba"
            });
            
            //Caricamento dell'insieme di visibilità per l'utente collegato
            $.ajax({
               url: "getUserVisibilitySet.php",
               type: "POST",
               async: true,
               dataType: 'JSON',
               cache: false, 
               success: function (data) 
               {
                   userVisibilitySet = data;

                   $("#inputDashboardVisibilityUsersTable").append('<tr><th class="selectCell">Select</th><th class="usernameCell">Username</th></tr>');

                   for(var i = 0; i < userVisibilitySet.length; i++)
                   {
                      $("#inputDashboardVisibilityUsersTable").append('<tr><td><input type="checkbox" name="selectedVisibilityUsers[]" value="' + userVisibilitySet[i] + '"/></td><td>' + userVisibilitySet[i] + '</td></tr>'); 
                   }

                   //Metodo apposito per settare/desettare gli attributi checked sulle checkbox
                   $('#inputDashboardVisibilityUsersTable input[type="checkbox"').off('click');
                   $('#inputDashboardVisibilityUsersTable input[type="checkbox"').click(function(){
                       if($(this).attr("checked") === "checked")
                       {
                           $(this).removeAttr("checked");
                       }
                       else
                       {
                           $(this).attr("checked", "true");
                       }
                   });
               },
               error: function (data) 
               {
                   //TBD
                   console.log("Error: " + JSON.stringify(data));
               }
           });
    });
</script>  