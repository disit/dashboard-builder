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
       
       <!-- Bootstrap slider -->
        <script src="../bootstrapSlider/bootstrap-slider.js"></script>
        <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet"/>

       <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

        <!-- Custom CSS -->
        <link href="../css/dashboard.css" rel="stylesheet">
        
        <!-- Custom scripts -->
        <script type="text/javascript" src="../js/dashboard_mng.js"></script>
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
                        <div class="col-xs-10 col-md-12 centerWithFlex"  id="headerTitleCnt">Application setup</div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <!--<div class="row hidden-xs hidden-sm mainContentRow">
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
                            </div>-->
                            <div class="row mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">List</div>
                                <div class="col-xs-12 mainContentCellCnt">
                                    <table id="filesTable" class="table"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
        
        <!-- Modale modifica modulo standard -->
        <div class="modal fade" id="modalEditModuleStd" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  
                </div>
                <form id="editModuleForm" name="editModuleForm" role="form" method="post" action="" data-toggle="validator">    
                    <input type="hidden" id="moduleIdToEdit" >  
                    <div id="editModuleModalBody" class="modal-body modalBody">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="active"><a data-toggle="tab" href="#devTab">Development</a></li>
                            <li><a data-toggle="tab" href="#testTab">Test</a></li>
                            <li><a data-toggle="tab" href="#prodTab">Production</a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="devTab" class="tab-pane fade in active">
                                <div class="row">

                                </div>
                            </div>
                            <div id="testTab" class="tab-pane fade in">
                                <div class="row">

                                </div>
                            </div>
                            <div id="prodTab" class="tab-pane fade in">
                                <div class="row">

                                </div>
                            </div>
                        </div>
                        <div class="row" id="editModuleLoadingMsg">
                            <div class="col-xs-12 centerWithFlex"></div>
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                        </div>
                        <div class="row" id="editModuleOkMsg">
                            <div class="col-xs-12 centerWithFlex">Module updated successfully</div>
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                        </div>
                        <div class="row" id="editModuleKoMsg">
                            <div class="col-xs-12 centerWithFlex">Error while updating module</div>
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                        </div>
                    </div>
                    <div id="editModuleModalFooter" class="modal-footer">
                      <button type="button" id="editModuleCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                      <button type="button" id="editModuleConfirmBtn" name="addDs" class="btn confirmBtn internalLink">Confirm</button>
                    </div>
                </form>    
              </div>
            </div>
        </div>
        
        <!-- Modale di modifica modulo environment -->
        <div class="modal fade" id="modalEditModuleEnv" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  
                </div>
                <form id="editModuleEnvForm" name="editModuleEnvForm" role="form" method="post" action="" data-toggle="validator"> 
                    <div id="editModuleEnvModalBody" class="modal-body modalBody">
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2 modalCell">
                                <div class="modalFieldCnt">
                                    <input id="activeEnv" name="activeEnv" data-slider-id="activeEnvSlider" type="text" data-slider-step="1"/>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="editModuleEnvLabel centerWithFlex modalFieldLabelCnt">
                                    Developement
                                </div>
                                <div class="editModuleEnvLabel centerWithFlex modalFieldLabelCnt">
                                    Test
                                </div>
                                <div class="editModuleEnvLabel centerWithFlex modalFieldLabelCnt">
                                    Production
                                </div>
                            </div>
                        </div>
                        <div class="row" id="editModuleEnvLoadingMsg">
                            <div class="col-xs-12 centerWithFlex"></div>
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                        </div>
                        <div class="row" id="editModuleEnvOkMsg">
                            <div class="col-xs-12 centerWithFlex">Module updated successfully</div>
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                        </div>
                        <div class="row" id="editModuleEnvKoMsg">
                            <div class="col-xs-12 centerWithFlex">Error while updating module</div>
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                        </div>
                    </div>
                    <div id="editModuleEnvModalFooter" class="modal-footer">
                      <button type="button" id="editModuleEnvCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                      <button type="button" id="editModuleEnvConfirmBtn" name="addDs" class="btn confirmBtn internalLink">Confirm</button>
                    </div>
                </form>    
              </div>
            </div>
        </div>
        
        <!-- Modale cancellazione modulo -->
        <div class="modal fade" id="modalDelModule" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Delete setup module
                </div>
                <input type="hidden" id="moduleIdToDelete" />
                <div id="delModuleModalBody" class="modal-body modalBody">
                    <div class="row">
                        <div class="col-xs-12 modalCell">
                            <div class="modalDelMsg col-xs-12 centerWithFlex">
                                Do you want to confirm cancellation of the following setup module?
                            </div>
                            <div class="modalDelObjName col-xs-12 centerWithFlex" id="delModuleName"></div> 
                        </div>
                    </div>
                    <div class="row" id="delModuleLoadingMsg">
                        <div class="col-xs-12 centerWithFlex">Deleting setup module, please wait</div>
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                    </div>
                    <div class="row" id="delModuleOkMsg">
                        <div class="col-xs-12 centerWithFlex">Setup module deleted successfully</div>
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="delModuleKoMsg">
                        <div class="col-xs-12 centerWithFlex">Error deleting setup module</div>
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                    </div>
                </div>
                <div id="delModuleModalFooter" class="modal-footer">
                  <button type="button" id="delModuleCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="delModuleConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
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
                    }, 2000);
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
                    }, 2000);
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
        });
        
        $('#setupLink .mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt #setupLink .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt #setupLink .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        $("#editModuleEnvForm #activeEnv").bootstrapSlider({
            ticks: [1, 2, 3],
            tooltip: 'hide'
        });
        
        var loggedRole = "<?= $_SESSION['loggedRole'] ?>";
        var loggedType = "<?= $_SESSION['loggedType'] ?>";
        var usr = "<?= $_SESSION['loggedUsername'] ?>";
        var userVisibilitySet = null;
        var authorizedPages = null;
        var internalDest = false;
        var tableFirstLoad = true;
            
        $('.internalLink').on('mousedown', function(){
            internalDest = true;
        });
        
        buildMainTable(false);
        
        $('#modalEditModuleStd #editModuleConfirmBtn').click(function(){
            $('#modalEditModuleStd ul.nav').hide();
            $('#modalEditModuleStd div.tab-content').hide();
            $('#modalEditModuleStd #editModuleModalFooter').hide();
            $('#modalEditModuleStd #editModuleLoadingMsg').show();
            
            var formData =  $('#modalEditModuleStd #editModuleForm').serializeArray(); 
            
            $('#modalEditModuleStd #editModuleForm input[type=checkbox]').each(function(i)
            {
                if($(this).prop("checked"))
                {
                    for(var i = 0; i < formData.length; i++)
                    {
                        if((formData[i].name === $(this).attr("name")) && (formData[i].value === "on"))
                        {
                            formData[i].value = "yes";
                        }
                    }
                }
                else
                {
                    formData.push({name: $(this).attr("name"), value: "no"});
                }
            });
            
            $.ajax({
                url: "process-form.php",
                data: {
                    updateConfigFile: true,
                    fileName: $('#modalEditModuleStd #moduleIdToEdit').val(),
                    data: JSON.stringify(formData)
                },
                type: "POST",
                async: true,
                //datatype: 'json',
                success: function(data)
                {
                    switch(data)
                    {
                        case "Ok":
                            $('#modalEditModuleStd #editModuleLoadingMsg').hide();
                            $('#modalEditModuleStd #editModuleOkMsg').show();
                            setTimeout(function(){
                                $('#modalEditModuleStd').modal('hide');
                                setTimeout(function(){
                                    $('#modalEditModuleStd #editModuleOkMsg').hide();
                                    $('#modalEditModuleStd ul.nav').show();
                                    $('#modalEditModuleStd ul.nav li').removeClass("active");
                                    $('#modalEditModuleStd ul.nav li').eq(0).addClass("active");
                                    $('#modalEditModuleStd div.tab-content').show();
                                    $('#modalEditModuleStd #editModuleModalFooter').show();
                                    //C'è un bug al reload dei toggle button, non carica il valore giusto finché non clicchi su un tab, per ora patch tramite reload
                                    location.reload();
                                }, 300);
                            }, 3000);
                            break;
                            
                        default:
                            console.log("Edit setup module KO:");
                            console.log(data);
                            $('#modalEditModuleStd #editModuleLoadingMsg').hide();
                            $('#modalEditModuleStd #editModuleKoMsg').show();
                            setTimeout(function(){
                                $('#modalEditModuleStd #editModuleKoMsg').hide();
                                $('#modalEditModuleStd ul.nav').show();
                                $('#modalEditModuleStd div.tab-content').show();
                                $('#modalEditModuleStd #editModuleModalFooter').show();
                            }, 3000);
                            break;
                    }
                },
                error: function(data)
                {
                    console.log("Edit setup module KO:");
                    console.log(data);
                    $('#modalEditModuleStd #editModuleLoadingMsg').hide();
                    $('#modalEditModuleStd #editModuleKoMsg').show();
                    setTimeout(function(){
                        $('#modalEditModuleStd #editModuleKoMsg').hide();
                        $('#modalEditModuleStd ul.nav').show();
                        $('#modalEditModuleStd div.tab-content').show();
                        $('#modalEditModuleStd #editModuleModalFooter').show();
                    }, 3000);
                }
            });
        });
        
        $('#modalEditModuleEnv #editModuleEnvConfirmBtn').click(function(){
            $('#editModuleEnvModalBody .row').eq(0).hide();
            $('#editModuleEnvModalFooter').hide();
            $('#editModuleEnvLoadingMsg').show();
            
            var activeEnvironment = null;
            switch($("#editModuleEnvForm #activeEnv").bootstrapSlider('getValue'))
            {
                case 1:
                    activeEnvironment = "dev";
                    break;

                case 2:
                    activeEnvironment = "test";
                    break;    

                case 3:
                    activeEnvironment = "prod";
                    break;    
            }
            
            $.ajax({
                url: "process-form.php",
                data: {
                    updateConfigFile: true,
                    fileName: "environment.ini",
                    activeEnvironment: activeEnvironment
                },
                type: "POST",
                async: true,
                //datatype: 'json',
                success: function(data)
                {
                    switch(data)
                    {
                        case "Ok":
                            $('#editModuleEnvLoadingMsg').hide();
                            $('#editModuleEnvOkMsg').show();
                            setTimeout(function(){
                                $('#modalEditModuleEnv').modal('hide');
                                setTimeout(function(){
                                    $('#editModuleEnvOkMsg').hide();
                                    $('#editModuleEnvModalBody .row').eq(0).show();
                                    $('#editModuleEnvModalFooter').show();
                                }, 300);
                            }, 3000);
                            break;
                            
                        default:
                            console.log("Edit setup module KO:");
                            console.log(data);
                            $('#editModuleEnvLoadingMsg').hide();
                            $('#editModuleKoMsg').show();
                            setTimeout(function(){
                                $('#editModuleEnvKoMsg').hide();
                                $('#editModuleEnvModalBody .row').eq(0).show();
                                $('#editModuleEnvModalFooter').show();
                            }, 3000);
                            break;
                    }
                },
                error: function(data)
                {
                    console.log("Edit setup module KO:");
                    console.log(data);
                    $('#editModuleEnvLoadingMsg').hide();
                    $('#editModuleEnvKoMsg').show();
                    setTimeout(function(){
                        $('#editModuleEnvKoMsg').hide();
                        $('#editModuleEnvModalBody .row').eq(0).show();
                        $('#editModuleEnvModalFooter').show();
                    }, 3000);
                }
            });   
        });
        
        $('#delModuleConfirmBtn').click(function(){
            $('#delModuleModalBody div.row').eq(0).hide();
            $('#delModuleModalFooter').hide();
            $('#delModuleLoadingMsg').show();
            
            $.ajax({
                url: "process-form.php",
                data: {
                    deleteConfigFile: true,
                    fileName: $('#moduleIdToDelete').val()
                },
                type: "POST",
                async: true,
                //datatype: 'json',
                success: function(data)
                {
                    switch(data)
                    {
                        case "Ok":
                            $('#delModuleLoadingMsg').hide();
                            $('#delModuleOkMsg').show();
                            setTimeout(function(){
                                $('#modalDelModule').modal('hide');
                                setTimeout(function(){
                                    $('#delModuleOkMsg').hide();
                                    $('#delModuleModalBody .row').eq(0).show();
                                    $('#delModuleModalFooter').show();
                                    location.reload();
                                }, 300);
                            }, 3000);
                            break;
                            
                        default:
                            console.log("Delete setup module KO:");
                            console.log(data);
                            $('#delModuleLoadingMsg').hide();
                            $('#delModuleKoMsg').show();
                            setTimeout(function(){
                                $('#delModuleKoMsg').hide();
                                $('#delModuleModalBody .row').eq(0).show();
                                $('#delModuleModalFooter').show();
                            }, 3000);
                            break;
                    }
                },
                error: function(data)
                {
                    console.log("Delete setup module KO:");
                    console.log(data);
                    $('#delModuleLoadingMsg').hide();
                    $('#delModuleKoMsg').show();
                    setTimeout(function(){
                        $('#delModuleKoMsg').hide();
                        $('#delModuleModalBody .row').eq(0).show();
                        $('#delModuleModalFooter').show();
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

            $.ajax({
                url: "get_data.php",
                data: {
                    action: "getConfigurationFilesList"
                },
                type: "GET",
                async: true,
                datatype: 'json',
                success: function (data)
                {
                    $('#filesTable').bootstrapTable({
                            columns: [{
                                field: 'fileDesc',
                                title: 'Module name',
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
                                    if(data[index].fileDeletable === "true")
                                    {
                                        return '<button type="button" class="delDashBtn">del</button>';
                                    }
                                    else
                                    {
                                        return '<button type="button" class="undeletableDashBtn">unerasable</button>';
                                    }
                                }
                            }],
                            data: data,
                            search: true,
                            pagination: true,
                            pageSize: 10,
                            locale: 'en-US',
                            searchAlign: 'left',
                            uniqueId: "fileName",
                            striped: false,
                            searchTimeOut: 250,
                            classes: "table table-hover table-no-bordered",
                            rowAttributes: function(row, index){
                            return {
                                "data-customForm": row.customForm
                            };},
                            onPostBody: function()
                            {
                                if(tableFirstLoad)
                                {
                                    //Caso di primo caricamento della tabella
                                    tableFirstLoad = false;
                                    /*var addModuleDiv = $('<div class="pull-right"><i id="addModuleBtn" data-toggle="modal" data-target="#modalAddModuleType" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                                    $('div.fixed-table-toolbar').append(addModuleDiv);
                                    addModuleDiv.css("margin-top", "10px");
                                    addModuleDiv.find('i.fa-plus-square').off('hover');
                                    addModuleDiv.find('i.fa-plus-square').hover(function(){
                                        $(this).css('color', '#e37777');
                                        $(this).css('cursor', 'pointer');
                                    }, 
                                    function(){
                                        $(this).css('color', '#ffcc00');
                                        $(this).css('cursor', 'normal');
                                    });*/
                                    
                                    $('#filesTable thead').css("background", "rgba(0, 162, 211, 1)");
                                    $('#filesTable thead').css("color", "white");
                                    $('#filesTable thead').css("font-size", "1em");
                                }
                                else
                                {
                                    //Casi di cambio pagina
                                }

                                //Istruzioni da eseguire comunque
                                $('#filesTable').css("border-bottom", "none");
                                $('span.pagination-info').hide();
                                $('#filesTable button.editDashBtn').off('hover');
                                $('#filesTable button.editDashBtn').hover(function(){
                                    $(this).css('background', '#ffcc00');
                                    $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                                }, 
                                function(){
                                    $(this).css('background', 'rgb(69, 183, 175)');
                                    $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                                });

                                $('#filesTable button.editDashBtn').off('click');
                                $('#filesTable button.editDashBtn').click(function(){
                                    if($(this).parents("tr").attr("data-customForm") === "false")
                                    {
                                        $('#modalEditModuleStd div.modalHeader').html($(this).parents("tr").find("td").eq(0).html());
                                        $('#modalEditModuleStd #moduleIdToEdit').val($(this).parents('tr').attr("data-uniqueid"));
                                        $('#modalEditModuleStd ul.nav').hide; 
                                        $('#modalEditModuleStd div.tab-content').hide();
                                        $('#modalEditModuleStd #editModuleModalFooter').hide();
                                        $('#modalEditModuleStd #editModuleLoadingMsg div.col-xs-12').eq(0).html('Retrieving module data, please wait');
                                        $('#modalEditModuleStd #editModuleLoadingMsg').show();
                                        $('#modalEditModuleStd').modal('show');

                                        $.ajax({
                                            url: "get_data.php",
                                            data: {
                                                action: "getSingleModuleData",
                                                fileName: $(this).parents('tr').attr('data-uniqueid')
                                            },
                                            type: "GET",
                                            async: true,
                                            datatype: 'json',
                                            success: function(data)
                                            {
                                                var fileField, formField = null;
                                                $('#modalEditModuleStd #editModuleLoadingMsg').hide();
                                                $('#modalEditModuleStd ul.nav').show(); 
                                                $('#modalEditModuleStd div.tab-content').show();
                                                $('#modalEditModuleStd #devTab div.row').empty();
                                                $('#modalEditModuleStd #testTab div.row').empty();
                                                $('#modalEditModuleStd #prodTab div.row').empty();
                                                $('#modalEditModuleStd #editModuleModalFooter').show();
                                                
                                                for(var key in data) 
                                                {
                                                    if((key !== "fileDesc")&&(key !== "customForm")&&(key !== "fileDeletable"))
                                                    {
                                                        if(data[key].dev === "yes")
                                                        {
                                                            formField = $('<div class="col-xs-12 modalCell">' +
                                                                            '<div class="modalFieldCnt">' +
                                                                                '<input type="checkbox" data-toggle="toggle" id="' + key + '[dev]" name="' + key + '[dev]" checked>' +
                                                                            '</div>' +
                                                                            '<div class="modalFieldLabelCnt">' + data[key].desc + '</div>' +
                                                                        '</div>');
                                                                
                                                            formField.find('input[type=checkbox]').bootstrapToggle({
                                                                    on: 'Yes',
                                                                    off: 'No',
                                                                    onstyle: 'info',
                                                                    offstyle: 'default',
                                                                    size: 'normal'
                                                                });    
                                                        }
                                                        else
                                                        {
                                                            if(data[key].dev === "no")
                                                            {
                                                                formField = $('<div class="col-xs-12 modalCell">' +
                                                                            '<div class="modalFieldCnt">' +
                                                                                '<input type="checkbox" data-toggle="toggle" id="' + key + '[dev]" name="' + key + '[dev]">' +
                                                                            '</div>' +
                                                                            '<div class="modalFieldLabelCnt">' + data[key].desc + '</div>' +
                                                                        '</div>');
                                                                
                                                                formField.find('input[type=checkbox]').bootstrapToggle({
                                                                    on: 'Yes',
                                                                    off: 'No',
                                                                    onstyle: 'info',
                                                                    offstyle: 'default',
                                                                    size: 'normal'
                                                                });
                                                            }
                                                            else
                                                            {
                                                                formField = $('<div class="col-xs-12 modalCell">' +
                                                                            '<div class="modalFieldCnt">' +
                                                                                '<input type="text" id="' + key + '[dev]" name="' + key + '[dev]" value="' + data[key].dev + '" class="modalInputTxt" required>' +
                                                                            '</div>' +
                                                                            '<div class="modalFieldLabelCnt">' + data[key].desc + '</div>' +
                                                                        '</div>');
                                                            }
                                                        }
                                                        
                                                        $('#modalEditModuleStd #devTab div.row').append(formField);  
                                                        
                                                        if(data[key].test === "yes")
                                                        {
                                                            formField = $('<div class="col-xs-12 modalCell">' +
                                                                            '<div class="modalFieldCnt">' +
                                                                                '<input type="checkbox" data-toggle="toggle" id="' + key + '[test]" name="' + key + '[test]" checked>' +
                                                                            '</div>' +
                                                                            '<div class="modalFieldLabelCnt">' + data[key].desc + '</div>' +
                                                                        '</div>');
                                                                
                                                            formField.find('input[type=checkbox]').bootstrapToggle({
                                                                    on: 'Yes',
                                                                    off: 'No',
                                                                    onstyle: 'info',
                                                                    offstyle: 'default',
                                                                    size: 'normal'
                                                                });    
                                                        }
                                                        else
                                                        {
                                                            if(data[key].test === "no")
                                                            {
                                                                formField = $('<div class="col-xs-12 modalCell">' +
                                                                            '<div class="modalFieldCnt">' +
                                                                                '<input type="checkbox" data-toggle="toggle" id="' + key + '[test]" name="' + key + '[test]">' +
                                                                            '</div>' +
                                                                            '<div class="modalFieldLabelCnt">' + data[key].desc + '</div>' +
                                                                        '</div>');
                                                                
                                                                formField.find('input[type=checkbox]').bootstrapToggle({
                                                                    on: 'Yes',
                                                                    off: 'No',
                                                                    onstyle: 'info',
                                                                    offstyle: 'default',
                                                                    size: 'normal'
                                                                });
                                                            }
                                                            else
                                                            {
                                                                formField = $('<div class="col-xs-12 modalCell">' +
                                                                            '<div class="modalFieldCnt">' +
                                                                                '<input type="text" id="' + key + '[test]" name="' + key + '[test]" value="' + data[key].test + '" class="modalInputTxt" required>' +
                                                                            '</div>' +
                                                                            '<div class="modalFieldLabelCnt">' + data[key].desc + '</div>' +
                                                                        '</div>');
                                                            }
                                                        }
                                                        
                                                        $('#modalEditModuleStd #testTab div.row').append(formField);
                                                        
                                                        if(data[key].prod === "yes")
                                                        {
                                                            formField = $('<div class="col-xs-12 modalCell">' +
                                                                            '<div class="modalFieldCnt">' +
                                                                                '<input type="checkbox" data-toggle="toggle" id="' + key + '[prod]" name="' + key + '[prod]" checked>' +
                                                                            '</div>' +
                                                                            '<div class="modalFieldLabelCnt">' + data[key].desc + '</div>' +
                                                                        '</div>');
                                                                
                                                            formField.find('input[type=checkbox]').bootstrapToggle({
                                                                on: 'Yes',
                                                                off: 'No',
                                                                onstyle: 'info',
                                                                offstyle: 'default',
                                                                size: 'normal'
                                                            });    
                                                        }
                                                        else
                                                        {
                                                            if(data[key].prod === "no")
                                                            {
                                                                formField = $('<div class="col-xs-12 modalCell">' +
                                                                            '<div class="modalFieldCnt">' +
                                                                                '<input type="checkbox" data-toggle="toggle" id="' + key + '[prod]" name="' + key + '[prod]">' +
                                                                            '</div>' +
                                                                            '<div class="modalFieldLabelCnt">' + data[key].desc + '</div>' +
                                                                        '</div>');
                                                                
                                                                formField.find('input[type=checkbox]').bootstrapToggle({
                                                                    on: 'Yes',
                                                                    off: 'No',
                                                                    onstyle: 'info',
                                                                    offstyle: 'default',
                                                                    size: 'normal'
                                                                });
                                                            }
                                                            else
                                                            {
                                                                formField = $('<div class="col-xs-12 modalCell">' +
                                                                            '<div class="modalFieldCnt">' +
                                                                                '<input type="text" id="' + key + '[prod]" name="' + key + '[prod]" value="' + data[key].prod + '" class="modalInputTxt" required>' +
                                                                            '</div>' +
                                                                            '<div class="modalFieldLabelCnt">' + data[key].desc + '</div>' +
                                                                        '</div>');
                                                            }
                                                        }
                                                        $('#modalEditModuleStd #prodTab div.row').append(formField);
                                                    }
                                                }
                                            },
                                            error: function(errorData)
                                            {
                                                //TBD
                                            }
                                        });
                                    }
                                    else
                                    {
                                        switch($(this).parents('tr').attr('data-uniqueid'))
                                        {
                                            case "environment.ini":
                                                $('#modalEditModuleEnv div.modalHeader').html($(this).parents("tr").find("td").eq(0).html());
                                                $('#modalEditModuleEnv #moduleIdToEdit').val($(this).parents('tr').attr("data-uniqueid"));
                                                $('#modalEditModuleEnv #editModuleEnvModalBody div.row').hide; 
                                                $('#modalEditModuleEnv #editModuleModalFooter').hide();
                                                $('#modalEditModuleEnv #editModuleLoadingMsg div.col-xs-12').eq(0).html('Retrieving module data, please wait');
                                                $('#modalEditModuleEnv #editModuleLoadingMsg').show();
                                                $('#modalEditModuleEnv').modal('show');

                                                $.ajax({
                                                    url: "get_data.php",
                                                    data: {
                                                        action: "getSingleModuleData",
                                                        fileName: $(this).parents('tr').attr('data-uniqueid')
                                                    },
                                                    type: "GET",
                                                    async: true,
                                                    datatype: 'json',
                                                    success: function(data)
                                                    {
                                                        var fileField, formField = null;
                                                        $('#modalEditModuleEnv #editModuleLoadingMsg').hide();
                                                        $('#modalEditModuleEnv ul.nav').show(); 
                                                        $('#modalEditModuleEnv #editModuleModalFooter').show();
                                                        switch(data.environment.value)
                                                        {
                                                            case "dev":
                                                                $("#editModuleEnvForm #activeEnv").bootstrapSlider('setValue', 1);
                                                                break;

                                                            case "test":
                                                                $("#editModuleEnvForm #activeEnv").bootstrapSlider('setValue', 2);
                                                                break;    

                                                            case "prod":
                                                                $("#editModuleEnvForm #activeEnv").bootstrapSlider('setValue', 3);
                                                                break;    
                                                        }
                                                    },
                                                    error: function(errorData)
                                                    {
                                                        //TBD
                                                    }
                                                });
                                                break;
                                        } 
                                    }
                                });

                                $('#filesTable button.delDashBtn').off('hover');
                                $('#filesTable button.delDashBtn').hover(function(){
                                    $(this).css('background', '#ffcc00');
                                    $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                                }, 
                                function(){
                                    $(this).css('background', '#e37777');
                                    $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                                });

                                $('#filesTable button.delDashBtn').off('click');
                                $('#filesTable button.delDashBtn').click(function(){
                                    $('#moduleIdToDelete').val($(this).parents('tr').attr("data-uniqueid"));
                                    $('#delModuleName').html($(this).parents('tr').find('td').eq(0).text());
                                    $('#modalDelModule').modal('show');
                                });
                            }
                        });
                    }
            });
        }
    });
</script>  