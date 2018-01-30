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
    
    if(!isset($_SESSION['loggedRole']))
    {
        header("location: unauthorizedUser.php");
    }
    else if($_SESSION['loggedRole'] != "ToolAdmin")
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

       <!-- Bootstrap table -->
       <link rel="stylesheet" href="../boostrapTable/dist/bootstrap-table.css">
       <script src="../boostrapTable/dist/bootstrap-table.js"></script>
       <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
       <script src="../boostrapTable/dist/locale/bootstrap-table-en-US.js"></script>

       <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
        
        <!-- Bootstrap slider -->
        <script src="../bootstrapSlider/bootstrap-slider.js"></script>
        <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet"/>

        <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        
        <!-- Custom CSS -->
        <link href="../css/dashboard.css" rel="stylesheet">
        <!--<link href="../css/pageTemplate.css" rel="stylesheet">-->
        
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
                        <div class="col-xs-10 col-md-12 centerWithFlex"  id="headerTitleCnt">Widgets</div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <div class="row hidden-xs hidden-sm mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">Synthesis</div>
                                <div id="dashboardTotNumberCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT count(*) AS qt FROM Dashboard.Widgets";
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
                                        widgets
                                    </div>
                                </div>
                                <div id="dashboardTotActiveCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT Config_widget_dashboard.type_w, count(*) AS cnt FROM Dashboard.Config_widget_dashboard " .
                                                     "GROUP BY Config_widget_dashboard.type_w " .
                                                     "ORDER BY count(*) DESC LIMIT 1";
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               echo str_replace('widget', '', $row['type_w']);
                                            }
                                            else
                                            {
                                                echo '-';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        most instances - <?php echo $row['cnt']; ?>
                                    </div>
                                </div>
                                <div id="dashboardTotPermCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT Config_widget_dashboard.type_w, count(*) AS cnt FROM Dashboard.Config_widget_dashboard " .
                                                     "GROUP BY Config_widget_dashboard.type_w " .
                                                     "ORDER BY count(*) ASC LIMIT 1";
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               echo str_replace('widget', '', $row['type_w']);
                                            }
                                            else
                                            {
                                                echo '-';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        least instances - <?php echo $row['cnt']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">List</div>
                                <div class="col-xs-12 mainContentCellCnt">
                                    <table id="list_widgets" class="table"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- Modale aggiunta tipo di widget -->
    <div class="modal fade" id="modalAddWidgetType" tabindex="-1" role="dialog" aria-labelledby="modalAddWidgetTypeLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Add widget type
            </div>
            
            <div id="addWidgetTypeModalBody" class="modal-body modalBody">
                <div class="row">
                    <div class="col-xs-12 col-md-6 col-md-offset-3 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" id="widgetName" name="widgetName" class="modalInputTxt" required>
                        </div>
                        <div class="modalFieldLabelCnt">Widget name</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" class="modalInputTxt" name="phpFilename" id="phpFilename" required> 
                        </div>
                        <div class="modalFieldLabelCnt">PHP filename</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" class="modalInputTxt" name="metricsNumber" id="metricsNumber" value="1" required> 
                        </div>
                        <div class="modalFieldLabelCnt">N.of managed metrics</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" class="modalInputTxt" name="metricType" id="metricType" required> 
                        </div>
                        <div class="modalFieldLabelCnt">Metric type(s)</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" class="modalInputTxt" name="uniqueMetric" id="uniqueMetric"> 
                        </div>
                        <div class="modalFieldLabelCnt">Unique metric managed</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input id="minWidth" name="minWidth" data-slider-id="minWidthSlider" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="1"/>
                        </div>
                        <div class="modalFieldLabelCnt">Min width</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input id="maxWidth" name="maxWidth" data-slider-id="maxWidthSlider" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="50"/>
                        </div>
                        <div class="modalFieldLabelCnt">Max width</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input id="minHeight" name="minHeight" data-slider-id="minHeightSlider" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="2"/>
                        </div>
                        <div class="modalFieldLabelCnt">Min height</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input id="maxHeight" name="maxHeight" data-slider-id="maxHeightSlider" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="50"/>
                        </div>
                        <div class="modalFieldLabelCnt">Max height</div>
                    </div>
                </div>
                <div class="row" id="addWidgetTypeLoadingMsg">
                    <div class="col-xs-12 centerWithFlex">Adding widget type, please wait</div>
                </div>
                <div class="row" id="addWidgetTypeLoadingIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                </div>
                <div class="row" id="addWidgetTypeOkMsg">
                    <div class="col-xs-12 centerWithFlex">Widget type added successfully</div>
                </div>
                <div class="row" id="addWidgetTypeOkIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                </div>
                <div class="row" id="addWidgetTypeKoMsg">
                    <div class="col-xs-12 centerWithFlex">Error adding widget type</div>
                </div>
                <div class="row" id="addWidgetTypeKoIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                </div>
            </div>
            <div id="addWidgetTypeModalFooter" class="modal-footer">
              <button type="button" id="addWidgetTypeCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
              <button type="button" id="addWidgetTypeConfirmBtn" name="addWidgetType" class="btn confirmBtn internalLink">Confirm</button>
            </div>
          </div>
        </div>
    </div>
            
    <!-- Modale modifica tipo di widget -->
    <div class="modal fade" id="modalEditWidgetType" tabindex="-1" role="dialog" aria-labelledby="modalEditWidgetTypeLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Edit widget type
            </div>
            <input type="hidden" id="widgetIdToEdit" />
            <div id="editWidgetTypeModalBody" class="modal-body modalBody">
                <div class="row">
                    <div class="col-xs-12 col-md-6 col-md-offset-3 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" id="widgetNameM" name="widgetNameM" class="modalInputTxt" required>
                        </div>
                        <div class="modalFieldLabelCnt">Widget name</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" class="modalInputTxt" name="phpFilenameM" id="phpFilenameM" required> 
                        </div>
                        <div class="modalFieldLabelCnt">PHP filename</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" class="modalInputTxt" name="metricsNumberM" id="metricsNumberM" value="1" required> 
                        </div>
                        <div class="modalFieldLabelCnt">N.of managed metrics</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" class="modalInputTxt" name="metricTypeM" id="metricTypeM" required> 
                        </div>
                        <div class="modalFieldLabelCnt">Metric type(s)</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input type="text" class="modalInputTxt" name="uniqueMetricM" id="uniqueMetricM"> 
                        </div>
                        <div class="modalFieldLabelCnt">Unique metric managed</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input id="minWidthM" name="minWidthM" data-slider-id="minWidthSliderM" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="1"/>
                        </div>
                        <div class="modalFieldLabelCnt">Min width</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input id="maxWidthM" name="maxWidthM" data-slider-id="maxWidthSliderM" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="50"/>
                        </div>
                        <div class="modalFieldLabelCnt">Max width</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input id="minHeightM" name="minHeightM" data-slider-id="minHeightSliderM" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="2"/>
                        </div>
                        <div class="modalFieldLabelCnt">Min height</div>
                    </div>
                    <div class="col-xs-12 col-md-6 modalCell">
                        <div class="modalFieldCnt">
                            <input id="maxHeightM" name="maxHeightM" data-slider-id="maxHeightSliderM" type="text" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="50"/>
                        </div>
                        <div class="modalFieldLabelCnt">Max height</div>
                    </div>
                </div>
                <div class="row" id="editWidgetTypeLoadingMsg">
                    <div class="col-xs-12 centerWithFlex">Updating widget type, please wait</div>
                </div>
                <div class="row" id="editWidgetTypeLoadingIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                </div>
                <div class="row" id="editWidgetTypeOkMsg">
                    <div class="col-xs-12 centerWithFlex">Widget type updated successfully</div>
                </div>
                <div class="row" id="editWidgetTypeOkIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                </div>
                <div class="row" id="editWidgetTypeKoMsg">
                    <div class="col-xs-12 centerWithFlex">Error updating widget type</div>
                </div>
                <div class="row" id="editWidgetTypeKoIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                </div>
            </div>
            <div id="editWidgetTypeModalFooter" class="modal-footer">
              <button type="button" id="editWidgetTypeCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
              <button type="button" id="editWidgetTypeConfirmBtn" name="editWidgetType" class="btn confirmBtn internalLink">Confirm</button>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale cancellazione tipo di widget -->
    <div class="modal fade" id="modalDelWidgetType" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Delete widget type
            </div>
            <input type="hidden" id="widgetIdToDelete" />
            <div id="delWidgetTypeModalBody" class="modal-body modalBody">
                <div class="row">
                    <div class="col-xs-12 modalCell">
                        <div class="modalDelMsg col-xs-12 centerWithFlex">
                            Do you want to confirm cancellation of the following widget type?
                        </div>
                        <div class="modalDelObjName col-xs-12 centerWithFlex" id="delWidgetName"></div> 
                    </div>
                </div>
                <div class="row" id="delWidgetTypeLoadingMsg">
                    <div class="col-xs-12 centerWithFlex">Deleting widget type, please wait</div>
                </div>
                <div class="row" id="delWidgetTypeLoadingIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                </div>
                <div class="row" id="delWidgetTypeOkMsg">
                    <div class="col-xs-12 centerWithFlex">Widget type deleted successfully</div>
                </div>
                <div class="row" id="delWidgetTypeOkIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                </div>
                <div class="row" id="delWidgetTypeKoMsg">
                    <div class="col-xs-12 centerWithFlex">Error deleting widget type</div>
                </div>
                <div class="row" id="delWidgetTypeKoIcon">
                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                </div>
            </div>
            <div id="delWidgetTypeModalFooter" class="modal-footer">
              <button type="button" id="delWidgetTypeCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
              <button type="button" id="delWidgetTypeConfirmBtn" name="editWidgetType" class="btn confirmBtn internalLink">Confirm</button>
            </div>
          </div>
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
            
            if($(window).width() < 992)
            {
                $('#list_widgets').bootstrapTable('hideColumn', 'widgetType');
                $('#list_widgets').bootstrapTable('hideColumn', 'min_row');
                $('#list_widgets').bootstrapTable('hideColumn', 'max_row');
                $('#list_widgets').bootstrapTable('hideColumn', 'min_col');
                $('#list_widgets').bootstrapTable('hideColumn', 'max_col');
            }
            else
            {
                $('#list_widgets').bootstrapTable('showColumn', 'widgetType');
                $('#list_widgets').bootstrapTable('showColumn', 'min_row');
                $('#list_widgets').bootstrapTable('showColumn', 'max_row');
                $('#list_widgets').bootstrapTable('showColumn', 'min_col');
                $('#list_widgets').bootstrapTable('showColumn', 'max_col');
            }
        });
        
        $('#link_widgets_mng .mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt #link_widgets_mng .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt #link_widgets_mng .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        var tableFirstLoad = true;
            
        buildMainTable(false);

        $('#minWidth').bootstrapSlider({
            tooltip_position:'left'
        });

        $('#maxWidth').bootstrapSlider({
            tooltip_position: 'left'
        });

        $('#minHeight').bootstrapSlider({
            tooltip_position:'left'
        });

        $('#maxHeight').bootstrapSlider({
            tooltip_position: 'left'
        });

        $('#minWidthM').bootstrapSlider({
            tooltip_position:'left'
        });

        $('#maxWidthM').bootstrapSlider({
            tooltip_position: 'left'
        });

        $('#minHeightM').bootstrapSlider({
            tooltip_position:'left'
        });

        $('#maxHeightM').bootstrapSlider({
            tooltip_position: 'left'
        });

        $('#addWidgetTypeConfirmBtn').click(function(){
            $('#modalAddWidgetType div.modalCell').hide();
            $('#addWidgetTypeModalFooter').hide();
            $('#addWidgetTypeLoadingMsg').show();
            $('#addWidgetTypeLoadingIcon').show();

            $.ajax({
                url: "process-form.php",
                data: {
                    addWidgetType: true,
                    widgetName: $('#widgetName').val(),
                    phpFilename: $('#phpFilename').val(),
                    metricsNumber: $('#metricsNumber').val(),
                    metricType: $('#metricType').val(),
                    uniqueMetric: $('#uniqueMetric').val(),
                    minWidth: $('#minWidth').bootstrapSlider('getValue'),
                    maxWidth: $('#maxWidth').bootstrapSlider('getValue'),
                    minHeight: $('#minHeight').bootstrapSlider('getValue'),
                    maxHeight: $('#maxHeight').bootstrapSlider('getValue')
                },
                type: "POST",
                async: true,
                success: function(data)
                {
                    if(data !== 'Ok')
                    {
                        console.log("Error adding widget type");
                        console.log(data);
                        $('#addWidgetTypeLoadingMsg').hide();
                        $('#addWidgetTypeLoadingIcon').hide();
                        $('#addWidgetTypeKoMsg').show();
                        $('#addWidgetTypeKoIcon').show();
                        setTimeout(function(){
                            $('#addWidgetTypeKoMsg').hide();
                            $('#addWidgetTypeKoIcon').hide();
                            $('#modalAddWidgetType div.modalCell').show();
                            $('#addWidgetTypeModalFooter').show();
                        }, 3000);
                    }
                    else
                    {
                        $('#addWidgetTypeLoadingMsg').hide();
                        $('#addWidgetTypeLoadingIcon').hide();
                        $('#addWidgetTypeOkMsg').show();
                        $('#addWidgetTypeOkIcon').show();

                        setTimeout(function(){
                            $('#modalAddWidgetType').modal('hide');
                            buildMainTable(true);

                            setTimeout(function(){
                                $('#addWidgetTypeOkMsg').hide();
                                $('#addWidgetTypeOkIcon').hide();
                                $('#widgetName').val("");
                                $('#phpFilename').val("");
                                $('#metricsNumber').val("");
                                $('#metricType').val("");
                                $('#uniqueMetric').val("");
                                $('#minWidth').bootstrapSlider('setValue', 1);
                                $('#maxWidth').bootstrapSlider('setValue', 50);
                                $('#minHeight').bootstrapSlider('setValue', 2);
                                $('#maxHeight').bootstrapSlider('setValue', 50);
                                $('#modalAddWidgetType div.modalCell').show();
                                $('#addWidgetTypeModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                },
                error: function(errorData)
                {
                    $('#addWidgetTypeLoadingMsg').hide();
                    $('#addWidgetTypeLoadingIcon').hide();
                    $('#addWidgetTypeKoMsg').show();
                    $('#addWidgetTypeKoIcon').show();
                    setTimeout(function(){
                        $('#addWidgetTypeKoMsg').hide();
                        $('#addWidgetTypeKoIcon').hide();
                        $('#modalAddWidgetType div.modalCell').show();
                        $('#addWidgetTypeModalFooter').show();
                    }, 3000);
                    console.log("Error adding widget type");
                    console.log(errorData);
                }
            });  
        });
        
        $('#editWidgetTypeConfirmBtn').off("click");
        $('#editWidgetTypeConfirmBtn').click(function(){
            $('#modalEditWidgetType div.modalCell').hide();
            $('#editWidgetTypeModalFooter').hide();
            $('#editWidgetTypeLoadingMsg div.col-xs-12').html("Saving data, please wait");
            $('#editWidgetTypeLoadingMsg').show();
            $('#editWidgetTypeLoadingIcon').show();

            $.ajax({
                url: "process-form.php",
                data: {
                    editWidgetType: true,
                    id: $('#widgetIdToEdit').val(),
                    widgetName: $('#widgetNameM').val(),
                    phpFilename: $('#phpFilenameM').val(),
                    metricsNumber: $('#metricsNumberM').val(),
                    metricType: $('#metricTypeM').val(),
                    uniqueMetric: $('#uniqueMetricM').val(),
                    minWidth: $('#minWidthM').bootstrapSlider('getValue'),
                    maxWidth: $('#maxWidthM').bootstrapSlider('getValue'),
                    minHeight: $('#minHeightM').bootstrapSlider('getValue'),
                    maxHeight: $('#maxHeightM').bootstrapSlider('getValue')
                },
                type: "POST",
                datatype: 'json',
                async: true,
                success: function(data)
                {
                    if(data !== 'Ok')
                    {
                        console.log("Error updating widget type");
                        console.log(data);
                        $('#editWidgetTypeLoadingMsg').hide();
                        $('#editWidgetTypeLoadingIcon').hide();
                        $('#editWidgetTypeKoMsg div.col-xs-12').html("Error updating widget type");
                        $('#editWidgetTypeKoMsg').show();
                        $('#editWidgetTypeKoIcon').show();
                        setTimeout(function(){
                            $('#editWidgetTypeKoMsg').hide();
                            $('#editWidgetTypeKoIcon').hide();
                            $('#modalEditWidgetType div.modalCell').show();
                            $('#editWidgetTypeModalFooter').show();
                        }, 3000);
                    }
                    else
                    {
                        $('#editWidgetTypeLoadingMsg').hide();
                        $('#editWidgetTypeLoadingIcon').hide();
                        $('#editWidgetTypeOkMsg').show();
                        $('#editWidgetTypeOkIcon').show();

                        setTimeout(function(){
                            $('#modalEditWidgetType').modal('hide');
                            buildMainTable(true);

                            setTimeout(function(){
                                $('#editWidgetTypeOkMsg').hide();
                                $('#editWidgetTypeOkIcon').hide();
                                $('#widgetNameM').val("");
                                $('#phpFilenameM').val("");
                                $('#metricsNumberM').val("");
                                $('#metricTypeM').val("");
                                $('#uniqueMetricM').val("");
                                $('#minWidthM').bootstrapSlider('setValue', 1);
                                $('#maxWidthM').bootstrapSlider('setValue', 50);
                                $('#minHeightM').bootstrapSlider('setValue', 2);
                                $('#maxHeightM').bootstrapSlider('setValue', 50);
                                $('#modalEditWidgetType div.modalCell').show();
                                $('#editWidgetTypeModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                },
                error: function(errorData)
                {
                    console.log("Error updating widget type");
                    console.log(errorData);
                    $('#editWidgetTypeLoadingMsg').hide();
                    $('#editWidgetTypeLoadingIcon').hide();
                    $('#editWidgetTypeKoMsg div.col-xs-12').html("Error updating widget type");
                    $('#editWidgetTypeKoMsg').show();
                    $('#editWidgetTypeKoIcon').show();
                    setTimeout(function(){
                        $('#editWidgetTypeKoMsg').hide();
                        $('#editWidgetTypeKoIcon').hide();
                        $('#modalEditWidgetType div.modalCell').show();
                        $('#editWidgetTypeModalFooter').show();
                    }, 3000);
                }
            });  
        });
        
        $('#delWidgetTypeConfirmBtn').off("click");
        $('#delWidgetTypeConfirmBtn').click(function(){
            $('#modalDelWidgetType div.modalCell').hide();
            $('#delWidgetTypeModalFooter').hide();
            $('#delWidgetTypeLoadingMsg').show();
            $('#delWidgetTypeLoadingIcon').show();

            $.ajax({
                url: "process-form.php",
                data: {
                    delWidgetType: true,
                    id: $('#widgetIdToDelete').val()
                },
                type: "POST",
                async: true,
                success: function(data)
                {
                    if(data !== 'Ok')
                    {
                        console.log("Error deleting widget type");
                        console.log(data);
                        $('#delWidgetTypeLoadingMsg').hide();
                        $('#delWidgetTypeLoadingIcon').hide();
                        $('#delWidgetTypeKoMsg').show();
                        $('#delWidgetTypeKoIcon').show();

                        setTimeout(function(){
                            $('#modalDelWidgetType').modal('hide');
                            setTimeout(function(){
                                $('#delWidgetTypeKoMsg').hide();
                                $('#delWidgetTypeKoIcon').hide();
                                $('#modalDelWidgetType div.modalCell').show();
                                $('#delWidgetTypeModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                    else
                    {
                        $('#delWidgetTypeLoadingMsg').hide();
                        $('#delWidgetTypeLoadingIcon').hide();
                        $('#delWidgetTypeOkMsg').show();
                        $('#delWidgetTypeOkIcon').show();

                        setTimeout(function(){
                            $('#modalDelWidgetType').modal('hide');
                            buildMainTable(true);

                            setTimeout(function(){
                                $('#delWidgetTypeOkMsg').hide();
                                $('#delWidgetTypeOkIcon').hide();
                                $('#modalDelWidgetType div.modalCell').show();
                                $('#delWidgetTypeModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                },
                error: function(errorData)
                {
                    console.log("Error deleting widget type");
                    console.log(errorData);
                    $('#delWidgetTypeLoadingMsg').hide();
                    $('#delWidgetTypeLoadingIcon').hide();
                    $('#delWidgetTypeKoMsg').show();
                    $('#delWidgetTypeKoIcon').show();

                    setTimeout(function(){
                        $('#modalDelWidgetType').modal('hide');
                        setTimeout(function(){
                            $('#delWidgetTypeKoMsg').hide();
                            $('#delWidgetTypeKoIcon').hide();
                            $('#modalDelWidgetType div.modalCell').show();
                            $('#delWidgetTypeModalFooter').show();
                        }, 500);
                    }, 3000);
                }
            });  
        });

        function buildMainTable(destroyOld)
        {
            if(destroyOld)
            {
                $('#list_widgets').bootstrapTable('destroy');
                tableFirstLoad = true;
            }
            
            var widgetTypeVisibile = true;
            var minHeightVisibile = true;
            var maxHeightVisibile = true;
            var minWidthVisibile = true;
            var maxWidthVisibile = true;

            if($(window).width() < 992)
            {
                widgetTypeVisibile = false;
                minHeightVisibile = false; 
                maxHeightVisibile = false;
                minWidthVisibile = false;
                maxWidthVisibile = false;
            }

            $.ajax({
                url: "get_data.php",
                data: {action: "get_widget_types"},
                type: "GET",
                async: true,
                datatype: 'json',
                success: function (data)
                {
                    $('#list_widgets').bootstrapTable({
                            columns: [{
                                field: 'id_type_widget',
                                title: 'Name',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                formatter: function(value, row, index)
                                {
                                    var maxL = 50;
                                    if($(window).width() < 992)
                                    {
                                        maxL = 15;
                                    }

                                    if(value !== null)
                                    {
                                        if(value.length > maxL)
                                        {
                                           return value.substr(0, maxL) + " ...";
                                        }
                                        else
                                        {
                                           return value;
                                        } 
                                    }
                                },
                                cellStyle: function(value, row, index, field){
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
                                field: 'widgetType',
                                title: "Data type(s)",
                                align: "center",
                                sortable: true,
                                align: "center",
                                visible: widgetTypeVisibile,
                                halign: "center",
                                valign: "middle",
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
                                field: 'min_row',
                                title: 'Min height',
                                sortable: true,
                                align: "center",
                                halign: "center",
                                valign: "middle",
                                visible: minHeightVisibile,
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
                                field: 'max_row',
                                title: 'Max height',
                                sortable: true,
                                align: "center",
                                halign: "center",
                                valign: "middle",
                                visible: maxHeightVisibile,
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
                                field: 'min_col',
                                title: 'Min width',
                                sortable: true,
                                align: "center",
                                halign: "center",
                                valign: "middle",
                                visible: minWidthVisibile,
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
                                field: 'max_col',
                                title: 'Max width',
                                sortable: true,
                                align: "center",
                                halign: "center",
                                valign: "middle",
                                visible: maxWidthVisibile,
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
                                },
                                formatter: function(value, row, index)
                                {
                                    return '<button type="button" class="editDashBtn">edit</button>';
                                }
                            },
                            {
                                title: "",
                                align: "center",
                                valign: "middle",
                                align: "center",
                                halign: "center",
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
                                },
                                formatter: function(value, row, index)
                                {
                                    //return '<span class="glyphicon glyphicon-remove"></span>'; 
                                    return '<button type="button" class="delDashBtn">del</button>';
                                }
                            }],
                            data: data,
                            search: true,
                            pagination: true,
                            pageSize: 10,
                            locale: 'en-US',
                            searchAlign: 'left',
                            uniqueId: "id",
                            striped: false,
                            searchTimeOut: 250,
                            classes: "table table-hover table-no-bordered",
                            onPostBody: function()
                            {
                                if(tableFirstLoad)
                                {
                                    //Caso di primo caricamento della tabella
                                    tableFirstLoad = false;
                                    var addWidgetDiv = $('<div class="pull-right"><i id="addWidgetTypeBtn" data-toggle="modal" data-target="#modalAddWidgetType" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                                    $('div.fixed-table-toolbar').append(addWidgetDiv);
                                    addWidgetDiv.css("margin-top", "10px");
                                    addWidgetDiv.find('i.fa-plus-square').off('hover');
                                    addWidgetDiv.find('i.fa-plus-square').hover(function(){
                                        $(this).css('color', '#e37777');
                                        $(this).css('cursor', 'pointer');
                                    }, 
                                    function(){
                                        $(this).css('color', '#ffcc00');
                                        $(this).css('cursor', 'normal');
                                    });
                                    
                                    $('#list_widgets thead').css("background", "rgba(0, 162, 211, 1)");
                                    $('#list_widgets thead').css("color", "white");
                                    $('#list_widgets thead').css("font-size", "1em");
                                }
                                else
                                {
                                    //Casi di cambio pagina
                                }

                                //Istruzioni da eseguire comunque
                                $('#list_widgets').css("border-bottom", "none");
                                $('span.pagination-info').hide();
                                $('#list_widgets button.editDashBtn').off('hover');
                                $('#list_widgets button.editDashBtn').hover(function(){
                                    $(this).css('background', '#ffcc00');
                                    $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                                }, 
                                function(){
                                    $(this).css('background', 'rgb(69, 183, 175)');
                                    $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                                });

                                $('#list_widgets button.editDashBtn').off('click');
                                $('#list_widgets button.editDashBtn').click(function(){
                                    $('#widgetIdToEdit').val($(this).parents('tr').attr("data-uniqueid"));
                                    $('div.editWidgetTypeDataRow').hide();
                                    $('#editWidgetTypeConfirmBtn').hide();
                                    $('#editWidgetTypeLoadingMsg div.col-xs-12').html('Retrieving widget type data, please wait');
                                    $('#editWidgetTypeLoadingMsg').show();
                                    $('#editWidgetTypeLoadingIcon').show();
                                    $('#modalEditWidgetType').modal('show');

                                    $.ajax({
                                        url: "get_data.php",
                                        data: {
                                            action: "get_single_widget_type",
                                            id: $(this).parents('tr').attr('data-uniqueid')
                                        },
                                        type: "GET",
                                        async: true,
                                        datatype: 'json',
                                        success: function(data)
                                        {
                                            $('#editWidgetTypeLoadingMsg').hide();
                                            $('#editWidgetTypeLoadingIcon').hide();

                                            if(data.result !== "Ok")
                                            {
                                                console.log("Error getting widget type data");
                                                console.log(data);
                                                $('#editWidgetTypeConfirmBtn').show();
                                                $('#editWidgetTypeModalFooter').hide();
                                                $('#editWidgetTypeKoMsg').show();
                                                $('#editWidgetTypeKoMsg div.col-xs-12').html('Error retrieving widget type data');
                                                $('#editWidgetTypeKoIcon').show();

                                                setTimeout(function(){
                                                    $('#modalEditWidgetType').modal('hide');

                                                    setTimeout(function(){
                                                        $('#editWidgetTypeKoMsg').hide();
                                                        $('#editWidgetTypeKoIcon').hide();
                                                        $('div.editWidgetTypeDataRow').show();
                                                        $('#editWidgetTypeModalFooter').show();
                                                    }, 500);
                                                }, 3000);
                                            }
                                            else
                                            {
                                                $('div.editWidgetTypeDataRow').show();
                                                $('#editWidgetTypeConfirmBtn').show();
                                                $('#editWidgetTypeModalFooter').show();

                                                $('#widgetNameM').val(data.data.id_type_widget);
                                                $('#phpFilenameM').val(data.data.source_php_widget);
                                                $('#metricsNumberM').val(data.data.number_metrics_widget);
                                                $('#metricTypeM').val(data.data.widgetType);
                                                $('#uniqueMetricM').val(data.data.unique_metric);
                                                $('#minWidthM').bootstrapSlider('setValue', data.data.min_col);
                                                $('#maxWidthM').bootstrapSlider('setValue', data.data.max_col);
                                                $('#minHeightM').bootstrapSlider('setValue', data.data.min_row);
                                                $('#maxHeightM').bootstrapSlider('setValue', data.data.max_row);
                                            }
                                        },
                                        error: function(errorData)
                                        {
                                            console.log("Error getting widget type data");
                                            console.log(data);
                                            $('#editWidgetTypeLoadingMsg').hide();
                                            $('#editWidgetTypeLoadingIcon').hide();
                                            $('#editWidgetTypeConfirmBtn').show();
                                            $('#editWidgetTypeModalFooter').hide();
                                            $('#editWidgetTypeKoMsg').show();
                                            $('#editWidgetTypeKoMsg div.col-xs-12').html('Error retrieving widget type data');
                                            $('#editWidgetTypeKoIcon').show();

                                            setTimeout(function(){
                                                $('#modalEditWidgetType').modal('hide');

                                                setTimeout(function(){
                                                    $('#editWidgetTypeKoMsg').hide();
                                                    $('#editWidgetTypeKoIcon').hide();
                                                    $('div.editWidgetTypeDataRow').show();
                                                    $('#editWidgetTypeModalFooter').show();
                                                }, 500);
                                            }, 3000);
                                        }
                                    });

                                });

                                $('#list_widgets button.delDashBtn').off('hover');
                                $('#list_widgets button.delDashBtn').hover(function(){
                                    $(this).css('background', '#ffcc00');
                                    $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                                }, 
                                function(){
                                    $(this).css('background', '#e37777');
                                    $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                                });

                                $('#list_widgets button.delDashBtn').off('click');
                                $('#list_widgets button.delDashBtn').click(function(){
                                    $('#widgetIdToDelete').val($(this).parents('tr').attr("data-uniqueid"));
                                    $('#delWidgetName').html($(this).parents('tr').find('td').eq(0).text());
                                    $('#modalDelWidgetType').modal('show');
                                });
                            }
                        });
                    }
            });
        }
    });
</script>  