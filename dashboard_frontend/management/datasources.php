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

        <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        
        <!-- Custom CSS -->
        <link href="../css/dashboard.css" rel="stylesheet">
        <!--<link href="../css/pageTemplate.css" rel="stylesheet">-->
        
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
                        <div class="col-xs-10 col-md-12 centerWithFlex"  id="headerTitleCnt">Data sources</div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <div class="row hidden-xs hidden-sm mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">Synthesis</div>
                                <div id="dashboardTotNumberCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT count(*) AS qt FROM Dashboard.DataSource";
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
                                        sources
                                    </div>
                                </div>
                                <div id="dashboardTotActiveCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT Descriptions.dataSource, count(*) FROM Dashboard.Descriptions " .
                                                     "WHERE Descriptions.dataSource <> 'none' " .
                                                     "GROUP BY Descriptions.dataSource " .
                                                     "ORDER BY count(*) DESC LIMIT 1";
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               echo $row['dataSource'];
                                            }
                                            else
                                            {
                                                echo '-';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        most used
                                    </div>
                                </div>
                                <div id="dashboardTotPermCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT Descriptions.dataSource, count(*) FROM Dashboard.Descriptions " .
                                                     "WHERE Descriptions.dataSource <> 'none' " .
                                                     "GROUP BY Descriptions.dataSource " .
                                                     "ORDER BY count(*) ASC LIMIT 1";
                                            $result = mysqli_query($link, $query);
                                            
                                            if($result)
                                            {
                                               $row = $result->fetch_assoc();
                                               echo $row['dataSource'];
                                            }
                                            else
                                            {
                                                echo '-';
                                            }
                                        ?>
                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        least used
                                    </div>
                                </div>
                            </div>
                            <div class="row mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">List</div>
                                <div class="col-xs-12 mainContentCellCnt">
                                    <table id="dataSourcesTable" class="table"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modale aggiunta datasource -->
        <div class="modal fade" id="modalAddDs" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Add data source
                </div>

                <div id="addDsModalBody" class="modal-body modalBody">
                    <div class="row">
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" id="dsName" name="dsName" class="modalInputTxt" required>
                            </div>
                            <div class="modalFieldLabelCnt">Name</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsUrl" id="dsUrl" required>
                            </div>
                            <div class="modalFieldLabelCnt">URL</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsDbType" id="dsDbType" required> 
                            </div>
                            <div class="modalFieldLabelCnt">Database type</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsDbName" id="dsDbName" required> 
                            </div>
                            <div class="modalFieldLabelCnt">Database name</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsDbUsr" id="dsDbUsr"> 
                            </div>
                            <div class="modalFieldLabelCnt">Database username</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsDbPwd" id="dsDbPwd"> 
                            </div>
                            <div class="modalFieldLabelCnt">Database password</div>
                        </div>
                    </div>
                    <div class="row" id="addDsLoadingMsg">
                        <div class="col-xs-12 centerWithFlex">Adding data source, please wait</div>
                    </div>
                    <div class="row" id="addDsLoadingIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                    </div>
                    <div class="row" id="addDsOkMsg">
                        <div class="col-xs-12 centerWithFlex">Data source added successfully</div>
                    </div>
                    <div class="row" id="addDsOkIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="addDsKoMsg">
                        <div class="col-xs-12 centerWithFlex">Error adding data source</div>
                    </div>
                    <div class="row" id="addDsKoIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                    </div>
                </div>
                <div id="addDsModalFooter" class="modal-footer">
                  <button type="button" id="addDsCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="addDsConfirmBtn" name="addDsConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
                </div>
              </div>
            </div>
        </div>
        
        <!-- Modale modifica datasource -->
        <div class="modal fade" id="modalEditDs" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Update data source
                </div>
                <input type="hidden" id="dsIdToEdit" >  
                <div id="editDsModalBody" class="modal-body modalBody">
                    <div class="row">
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" id="dsNameM" name="dsNameM" class="modalInputTxt" required>
                            </div>
                            <div class="modalFieldLabelCnt">Name</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsUrlM" id="dsUrlM" required>
                            </div>
                            <div class="modalFieldLabelCnt">URL</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsDbTypeM" id="dsDbTypeM" required> 
                            </div>
                            <div class="modalFieldLabelCnt">Database type</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsDbNameM" id="dsDbNameM" required> 
                            </div>
                            <div class="modalFieldLabelCnt">Database name</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsDbUsrM" id="dsDbUsrM"> 
                            </div>
                            <div class="modalFieldLabelCnt">Database username</div>
                        </div>
                        <div class="col-xs-12 col-md-6 modalCell">
                            <div class="modalFieldCnt">
                                <input type="text" class="modalInputTxt" name="dsDbPwdM" id="dsDbPwdM"> 
                            </div>
                            <div class="modalFieldLabelCnt">Database password</div>
                        </div>
                    </div>
                    <div class="row" id="editDsLoadingMsg">
                        <div class="col-xs-12 centerWithFlex"></div>
                    </div>
                    <div class="row" id="editDsLoadingIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                    </div>
                    <div class="row" id="editDsOkMsg">
                        <div class="col-xs-12 centerWithFlex">Datasource updated successfully</div>
                    </div>
                    <div class="row" id="editDsOkIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="editDsKoMsg">
                        <div class="col-xs-12 centerWithFlex"></div>
                    </div>
                    <div class="row" id="editDsKoIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                    </div>
                </div>
                <div id="editDsModalFooter" class="modal-footer">
                  <button type="button" id="editDsCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="editDsConfirmBtn" name="editDsConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
                </div>
              </div>
            </div>
        </div>
        
        <!-- Modale cancellazione datasource -->
        <div class="modal fade" id="modalDelDs" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Delete data source
                </div>
                <input type="hidden" id="dsIdToDelete" />
                <div id="delDsModalBody" class="modal-body modalBody">
                    <div class="row">
                        <div class="col-xs-12 modalCell">
                            <div class="modalDelMsg col-xs-12 centerWithFlex">
                                Do you want to confirm cancellation of the following data source?
                            </div>
                            <div class="modalDelObjName col-xs-12 centerWithFlex" id="delDsName"></div> 
                        </div>
                    </div>
                    <div class="row" id="delDsLoadingMsg">
                        <div class="col-xs-12 centerWithFlex">Deleting data source, please wait</div>
                    </div>
                    <div class="row" id="delDsLoadingIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                    </div>
                    <div class="row" id="delDsOkMsg">
                        <div class="col-xs-12 centerWithFlex">Data source deleted successfully</div>
                    </div>
                    <div class="row" id="delDsOkIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                    </div>
                    <div class="row" id="delDsKoMsg">
                        <div class="col-xs-12 centerWithFlex">Error deleting data source</div>
                    </div>
                    <div class="row" id="delDsKoIcon">
                        <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                    </div>
                </div>
                <div id="delDsModalFooter" class="modal-footer">
                  <button type="button" id="delDsCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="delDsConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
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
                //console.log("Logout");
                location.href = "logout.php?sessionExpired=true";
            }
            /*else
            {
                console.log("Keep in");
            }*/
        }, 1000);
        
        
        $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        
        $(window).resize(function(){
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            
            if($(window).width() < 992)
            {
                $('#dataSourcesTable').bootstrapTable('hideColumn', 'database');
                $('#dataSourcesTable').bootstrapTable('hideColumn', 'databaseType');
                $('#dataSourcesTable').bootstrapTable('hideColumn', 'url');
            }
            else
            {
                $('#dataSourcesTable').bootstrapTable('showColumn', 'database');
                $('#dataSourcesTable').bootstrapTable('showColumn', 'databaseType');
                $('#dataSourcesTable').bootstrapTable('showColumn', 'url');
            }
        });
        
        $('#link_sources_mng .mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt #link_sources_mng .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt #link_sources_mng .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        var tableFirstLoad = true;
        buildMainTable(false);
        
        $('#addDsConfirmBtn').off("click");
        $('#addDsConfirmBtn').click(function(){
            $('#modalAddDs div.modalCell').hide();
            $('#addDsModalFooter').hide();
            $('#addDsLoadingMsg').show();
            $('#addDsLoadingIcon').show();

            $.ajax({
                url: "process-form.php",
                data: {
                    addDs: true,
                    name: $('#dsName').val(),
                    url: $('#dsUrl').val(),
                    dbType: $('#dsDbType').val(),
                    dbName: $('#dsDbName').val(),
                    dbUsr: $('#dsDbUsr').val(),
                    dbPwd: $('#dsDbPwd').val()
                },
                type: "POST",
                async: true,
                success: function(data)
                {
                    if(data !== 'Ok')
                    {
                        console.log("Error adding data source");
                        console.log(data);
                        $('#addDsLoadingMsg').hide();
                        $('#addDsLoadingIcon').hide();
                        $('#addDsKoMsg').show();
                        $('#addDsKoIcon').show();
                        setTimeout(function(){
                            $('#addDsKoMsg').hide();
                            $('#addDsKoIcon').hide();
                            $('#modalAddDs div.modalCell').show();
                            $('#addDsModalFooter').show();
                        }, 3000);
                    }
                    else
                    {
                        $('#addDsLoadingMsg').hide();
                        $('#addDsLoadingIcon').hide();
                        $('#addDsOkMsg').show();
                        $('#addDsOkIcon').show();

                        setTimeout(function(){
                            $('#modalAddDs').modal('hide');
                            buildMainTable(true);

                            setTimeout(function(){
                                $('#addDsOkMsg').hide();
                                $('#addDsOkIcon').hide();
                                $('#dsName').val("");
                                $('#dsUrl').val("");
                                $('#dsDbType').val("");
                                $('#dsDbName').val("");
                                $('#dsDbUsr').val("");
                                $('#dsDbPwd').val("");
                                $('#modalAddDs div.modalCell').show();
                                $('#addDsModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                },
                error: function(errorData)
                {
                    $('#addDsLoadingMsg').hide();
                    $('#addDsLoadingIcon').hide();
                    $('#addDsKoMsg').show();
                    $('#addDsKoIcon').show();
                    setTimeout(function(){
                        $('#addDsKoMsg').hide();
                        $('#addDsKoIcon').hide();
                        $('#modalAddDs div.modalCell').show();
                        $('#addDsModalFooter').show();
                    }, 3000);
                    console.log("Error adding widget type");
                    console.log(errorData);
                }
            });  
        });

        $('#editDsConfirmBtn').off("click");
        $('#editDsConfirmBtn').click(function(){
            $('#modalEditDs div.modalCell').hide();
            $('#editDsModalFooter').hide();
            $('#editDsLoadingMsg div.col-xs-12').html("Saving data, please wait");
            $('#editDsLoadingMsg').show();
            $('#editDsLoadingIcon').show();

            $.ajax({
                url: "process-form.php",
                data: {
                    updateDs: true,
                    id: $('#dsIdToEdit').val(),
                    name: $('#dsNameM').val(),
                    url: $('#dsUrlM').val(),
                    dbType: $('#dsDbTypeM').val(),
                    dbName: $('#dsDbNameM').val(),
                    dbUsr: $('#dsDbUsrM').val(),
                    dbPwd: $('#dsDbPwdM').val()
                },
                type: "POST",
                datatype: 'json',
                async: true,
                success: function(data)
                {
                    if(data !== 'Ok')
                    {
                        console.log("Error updating data source");
                        console.log(data);
                        $('#editDsLoadingMsg').hide();
                        $('#editDsLoadingIcon').hide();
                        $('#editDsKoMsg div.col-xs-12').html("Error updating data source");
                        $('#editDsKoMsg').show();
                        $('#editDsKoIcon').show();
                        setTimeout(function(){
                            $('#editDsKoMsg').hide();
                            $('#editDsKoIcon').hide();
                            $('#modalEditDs div.modalCell').show();
                            $('#editDsModalFooter').show();
                        }, 3000);
                    }
                    else
                    {
                        $('#editDsLoadingMsg').hide();
                        $('#editDsLoadingIcon').hide();
                        $('#editDsOkMsg').show();
                        $('#editDsOkIcon').show();

                        setTimeout(function(){
                            $('#modalEditDs').modal('hide');
                            buildMainTable(true);

                            setTimeout(function(){
                                $('#dsNameM').val();
                                $('#dsUrlM').val();
                                $('#dsDbTypeM').val();
                                $('#dsDbNameM').val();
                                $('#dsDbUsrM').val();
                                $('#dsDbUsrM').val();
                                $('#editDsOkMsg').hide();
                                $('#editDsOkIcon').hide();
                                $('#modalEditDs div.modalCell').show();
                                $('#editDsModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                },
                error: function(errorData)
                {
                    console.log("Error updating data source");
                    console.log(errorData);
                    $('#editDsLoadingMsg').hide();
                    $('#editDsLoadingIcon').hide();
                    $('#editDsKoMsg div.col-xs-12').html("Error updating data source");
                    $('#editDsKoMsg').show();
                    $('#editDsKoIcon').show();
                    setTimeout(function(){
                        $('#editDsKoMsg').hide();
                        $('#editDsKoIcon').hide();
                        $('#modalEditDs div.modalCell').show();
                        $('#editDsModalFooter').show();
                    }, 3000);
                }
            });  
        });
        
        $('#delDsConfirmBtn').off("click");
        $('#delDsConfirmBtn').click(function(){
            $('#modalDelDs div.modalCell').hide();
            $('#delDsModalFooter').hide();
            $('#delDsLoadingMsg').show();
            $('#delDsLoadingIcon').show();

            $.ajax({
                url: "process-form.php",
                data: {
                    delDs: true,
                    id: $('#dsIdToDelete').val()
                },
                type: "POST",
                async: true,
                success:function(data)
                {
                    if(data !== 'Ok')
                    {
                        console.log("Error deleting datasource");
                        console.log(data);
                        $('#delDsLoadingMsg').hide();
                        $('#delDsLoadingIcon').hide();
                        $('#delDsKoMsg').show();
                        $('#delDsKoIcon').show();

                        setTimeout(function(){
                            $('#modalDelDs').modal('hide');
                            setTimeout(function(){
                                $('#delDsKoMsg').hide();
                                $('#delDsKoIcon').hide();
                                $('#modalDelDs div.modalCell').show();
                                $('#delDsModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                    else
                    {
                        $('#delDsLoadingMsg').hide();
                        $('#delDsLoadingIcon').hide();
                        $('#delDsOkMsg').show();
                        $('#delDsOkIcon').show();

                        setTimeout(function(){
                            $('#modalDelDs').modal('hide');
                            buildMainTable(true);

                            setTimeout(function(){
                                $('#delDsOkMsg').hide();
                                $('#delDsOkIcon').hide();
                                $('#modalDelDs div.modalCell').show();
                                $('#delDsModalFooter').show();
                            }, 500);
                        }, 3000);
                    }
                },
                error: function(errorData)
                {
                    console.log("Error deleting datasource");
                    console.log(errorData);
                    $('#delDsLoadingMsg').hide();
                    $('#delDsLoadingIcon').hide();
                    $('#delDsKoMsg').show();
                    $('#delDsKoIcon').show();

                    setTimeout(function(){
                        $('#modalDelDs').modal('hide');
                        setTimeout(function(){
                            $('#delDsKoMsg').hide();
                            $('#delDsKoIcon').hide();
                            $('#modalDelDs div.modalCell').show();
                            $('#delDsModalFooter').show();
                        }, 500);
                    }, 3000);
                }
            });  
        });

        function buildMainTable(destroyOld)
        {
            if(destroyOld)
            {
                $('#dataSourcesTable').bootstrapTable('destroy');
                tableFirstLoad = true;
            }
            
            var dbVisibile = true;
            var dbTypeVisible = true;
            var urlVisible = true;

            if($(window).width() < 992)
            {
                dbVisibile = false;
                dbTypeVisible = false; 
                urlVisible = false;
            }

            $.ajax({
                url: "get_data.php",
                data: {action: "getDataSources"},
                type: "GET",
                async: true,
                datatype: 'json',
                success: function (data)
                {
                    $('#dataSourcesTable').bootstrapTable({
                            columns: [{
                                field: 'Id',
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
                                field: 'database',
                                title: 'Database',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: dbVisibile,
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
                                field: 'databaseType',
                                title: 'Database type',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: dbTypeVisible,
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
                                field: 'url',
                                title: 'Url',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: urlVisible,
                                formatter: function(value, row, index)
                                {
                                    if(value !== null)
                                    {
                                        if(value.length > 90)
                                        {
                                           return value.substr(0, 90) + " ...";
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
                                title: "",
                                align: "center",
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                formatter: function(value, row, index)
                                {
                                    //return '<span class="glyphicon glyphicon-cog"></span>'; 
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
                                    return '<button type="button" class="delDashBtn">del</button>';
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
                            }],
                            data: data,
                            search: true,
                            pagination: true,
                            pageSize: 10,
                            locale: 'en-US',
                            searchAlign: 'left',
                            uniqueId: "intId",
                            striped: false,
                            searchTimeOut: 250,
                            classes: "table table-hover table-no-bordered",
                            onPostBody: function()
                            {
                                if(tableFirstLoad)
                                {
                                    //Caso di primo caricamento della tabella
                                    tableFirstLoad = false;
                                    var addDsDiv = $('<div class="pull-right"><i id="addDsBtn" data-toggle="modal" data-target="#modalAddDs" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                                    $('div.fixed-table-toolbar').append(addDsDiv);
                                    addDsDiv.css("margin-top", "10px");
                                    addDsDiv.find('i.fa-plus-square').off('hover');
                                    addDsDiv.find('i.fa-plus-square').hover(function(){
                                        $(this).css('color', '#e37777');
                                        $(this).css('cursor', 'pointer');
                                    }, 
                                    function(){
                                        $(this).css('color', '#ffcc00');
                                        $(this).css('cursor', 'normal');
                                    });
                                    
                                    $('#dataSourcesTable thead').css("background", "rgba(0, 162, 211, 1)");
                                    $('#dataSourcesTable thead').css("color", "white");
                                    $('#dataSourcesTable thead').css("font-size", "1em");
                                }
                                else
                                {
                                    //Casi di cambio pagina
                                }

                                //Istruzioni da eseguire comunque
                                $('#dataSourcesTable').css("border-bottom", "none");
                                 $('span.pagination-info').hide();

                                $('#dataSourcesTable button.editDashBtn').off('hover');
                                $('#dataSourcesTable button.editDashBtn').hover(function(){
                                    $(this).css('background', '#ffcc00');
                                    $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                                }, 
                                function(){
                                    $(this).css('background', 'rgb(69, 183, 175)');
                                    $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                                });

                                $('#dataSourcesTable button.editDashBtn').off('click');
                                $('#dataSourcesTable button.editDashBtn').click(function(){
                                    $('#dsIdToEdit').val($(this).parents('tr').attr("data-uniqueid"));
                                    $('div.editDsDataRow').hide();
                                    $('#editDsConfirmBtn').hide();
                                    $('#editDsLoadingMsg div.col-xs-12').html('Retrieving datasource details, please wait');
                                    $('#editDsLoadingMsg').show();
                                    $('#editDsLoadingIcon').show();
                                    $('#modalEditDs').modal('show');

                                    $.ajax({
                                        url: "get_data.php",
                                        data: {
                                            action: "getSingleDataSource",
                                            id: $(this).parents('tr').attr('data-uniqueid')
                                        },
                                        type: "GET",
                                        async: true,
                                        datatype: 'json',
                                        success: function(data)
                                        {
                                            $('#editDsLoadingMsg').hide();
                                            $('#editDsLoadingIcon').hide();

                                            if(data.result !== "Ok")
                                            {
                                                console.log("Error getting datasource details");
                                                console.log(data);
                                                $('#editDsConfirmBtn').show();
                                                $('#editDsModalFooter').hide();
                                                $('#editDsKoMsg').show();
                                                $('#editDsKoMsg div.col-xs-12').html('Error retrieving datasource details');
                                                $('#editDsKoIcon').show();

                                                setTimeout(function(){
                                                    $('#modalEditDs').modal('hide');

                                                    setTimeout(function(){
                                                        $('#editDsKoMsg').hide();
                                                        $('#editDsKoIcon').hide();
                                                        $('div.editDsDataRow').show();
                                                        $('#editDsModalFooter').show();
                                                    }, 500);
                                                }, 3000);
                                            }
                                            else
                                            {
                                                $('div.editDsDataRow').show();
                                                $('#editDsConfirmBtn').show();
                                                $('#editDsModalFooter').show();

                                                $('#dsNameM').val(data.data.Id);
                                                $('#dsUrlM').val(data.data.url);
                                                $('#dsDbTypeM').val(data.data.databaseType);
                                                $('#dsDbNameM').val(data.data.database);
                                                $('#dsDbUsrM').val(data.data.username);
                                                $('#dsDbPwdM').val(data.data.password);
                                            }
                                        },
                                        error: function(errorData)
                                        {
                                            console.log("Error getting datasource details");
                                            console.log(data);
                                            $('#editDsConfirmBtn').show();
                                            $('#editDsModalFooter').hide();
                                            $('#editDsKoMsg').show();
                                            $('#editDsKoMsg div.col-xs-12').html('Error retrieving datasource details');
                                            $('#editDsKoIcon').show();

                                            setTimeout(function(){
                                                $('#modalEditDs').modal('hide');

                                                setTimeout(function(){
                                                    $('#editDsKoMsg').hide();
                                                    $('#editDsKoIcon').hide();
                                                    $('div.editDsDataRow').show();
                                                    $('#editDsModalFooter').show();
                                                }, 500);
                                            }, 3000);
                                        }
                                    });
                                });
                                
                                $('#dataSourcesTable button.delDashBtn').off('hover');
                                $('#dataSourcesTable button.delDashBtn').hover(function(){
                                    $(this).css('background', '#ffcc00');
                                    $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                                }, 
                                function(){
                                    $(this).css('background', '#e37777');
                                    $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                                });

                                $('#dataSourcesTable button.delDashBtn').off('click');
                                $('#dataSourcesTable button.delDashBtn').click(function(){
                                    $('#dsIdToDelete').val($(this).parents('tr').attr("data-uniqueid"));
                                    $('#delDsName').html($(this).parents('tr').find('td').eq(0).text());
                                    $('#modalDelDs').modal('show');
                                });
                            }
                        });
                    }
            });
        }
        
    });
</script>  