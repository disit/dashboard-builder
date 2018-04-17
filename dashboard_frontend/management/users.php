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

        <title>Snap4City</title>

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
        <script src="../js/usersManagement.js"></script>
        
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
                            Snap4City
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-10 col-md-12 centerWithFlex"  id="headerTitleCnt">Users</div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <div class="row hidden-xs hidden-sm mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">Synthesis</div>
                                <div id="dashboardTotNumberCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT count(*) AS qt FROM Dashboard.Users";
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
                                        users
                                    </div>
                                </div>
                                <div id="dashboardTotActiveCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                            $query = "SELECT count(*) AS qt FROM Dashboard.Users WHERE status = 1";
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
                            </div>
                            <div class="row mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">List</div>
                                <div class="col-xs-12 mainContentCellCnt">
                                    <table id="usersTable" class="table"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modale di conferma cancellazione utente -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="deleteUserModalLabel">User deletion</h5>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                  <button type="button" id="deleteUserCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="button" id="deleteUserConfirmBtn" class="btn btn-primary">Confirm</button>
                </div>
              </div>
            </div>
        </div>

        <!-- Modale di registrazione nuovo utente -->
        <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Add new user
                </div>
                <div id="addUserModalCreating" class="modal-body container-fluid">
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                Creating account, please wait
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                        </div> 
                    </div>
                </div>  
                <div id="addUserModalBody" class="modal-body modalBody">
                    <form id="addUserForm" name="addUserForm" role="form" method="post" action="process-form.php" data-toggle="validator">
                        <div class="row">
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" id="username" name="username" class="modalInputTxt" pattern="[A-Za-z0-9_]+" title="Numbers, letters and _ are admitted" required>
                                </div>
                                <div class="modalFieldLabelCnt">Username</div>
                                <div id="usernameMsg" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <select id="userType" name="userType" class="modalInputTxt">
                                        <option value="Observer">Observer</option>
                                        <option value="Manager">Manager</option>
                                        <option value="AreaManager">Area manager</option>
                                        <option value="ToolAdmin">Tool admin</option>
                                    </select>
                                </div>
                                <div class="modalFieldLabelCnt">Account type</div>
                                <div id="usertypeMsg" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" id="firstName" name="firstName" class="modalInputTxt">
                                </div>
                                <div class="modalFieldLabelCnt">First name</div>
                                <div id="firstNameMsg" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" id="lastName" name="lastName" class="modalInputTxt">
                                </div>
                                <div class="modalFieldLabelCnt">Last name</div>
                                <div id="lastNameMsg" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" id="organization" name="organization" class="modalInputTxt">
                                </div>
                                <div class="modalFieldLabelCnt">Organization</div>
                                <div id="organizationMsg" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="email" id="email" name="email" class="modalInputTxt" required>
                                </div>
                                <div class="modalFieldLabelCnt">E-Mail</div>
                                <div id="emailMsg" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                        </div>
                        <div class="row" id="addUserPoolsRow">
                            <div class="col-xs-12" id="addUserPoolsOuterContainer">
                                <div id="addUserPoolsContainer">
                                    <?php
                                        if(isset($_SESSION['loggedRole']))
                                        {
                                            if($_SESSION['loggedRole'] == "ToolAdmin")
                                            {
                                                //Reperimento elenco dei pool
                                                $link = mysqli_connect($host, $username, $password) or die();
                                                mysqli_select_db($link, $dbname);
                                                $poolsQuery = "SELECT * FROM Dashboard.UsersPools";
                                                $result = mysqli_query($link, $poolsQuery) or die(mysqli_error($link));

                                                if($result)
                                                {
                                                    if($result->num_rows > 0) 
                                                    {
                                                        echo '<table id="addUserPoolsTable"><thead><tr><th>Make member</th><th class="addUserPoolsTableMakeAdminHeader">Make admin</th><th>Pool name</th></tr></thead>';
                                                        echo '<tbody>';

                                                        while ($row = $result->fetch_assoc()) 
                                                        {
                                                            echo '<tr><td class="checkboxCell addUserPoolsTableMakeMemberCheckbox"><input data-poolId="' . $row["poolId"] . '" type="checkbox" /></td><td class="checkboxCell addUserPoolsTableMakeAdminCheckbox"><input data-poolId="' . $row["poolId"] . '" type="checkbox" /></td><td class="poolNameCell">' . $row["poolName"] . '</td>';
                                                        }
                                                        
                                                        echo '</tbody>';
                                                        echo '</table>';
                                                    }
                                                    else 
                                                    {
                                                        //Nessun pool associabile
                                                        echo 'No pools available';
                                                    }
                                                }
                                                else 
                                                {
                                                    //Nessun pool associabile
                                                    echo 'No pools available';
                                                }
                                            }
                                        }
                                    ?>
                                </div>
                            </div> 
                        </div>
                    </form>    
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
                <div id="addUserModalFooter" class="modal-footer">
                  <button type="button" id="addNewUserCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="addNewUserConfirmBtn" name="addWidgetType" class="btn confirmBtn internalLink" disabled="true">Confirm</button>
                </div>
              </div>
            </div>
        </div>

        <!-- Modale di notifica inserimento utente avvenuto con successo -->
        <div class="modal fade" id="addUserOkModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Add new user
                </div>
                <input type="hidden" id="widgetIdToDelete" />
                <div id="delWidgetTypeModalBody" class="modal-body modalBody">
                    <div class="row">
                        <div class="col-xs-12 modalCell">
                            <div id="addUserOkModalInnerDiv1" class="modalDelMsg col-xs-12 centerWithFlex">
                                
                            </div>
                            <div class="modalDelObjName col-xs-12 centerWithFlex" id="addUserOkModalInnerDiv2"><i class="fa fa-check" style="font-size:36px"></i></div> 
                        </div>
                    </div>
                </div>
                <!--<div class="modal-footer">
                  
                </div>-->
              </div>
            </div>
        </div>
        
        <!-- Modale di notifica inserimento utente fallito -->
        <div class="modal fade" id="addUserKoModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Add new user
                </div>
                <input type="hidden" id="widgetIdToDelete" />
                <div id="delWidgetTypeModalBody" class="modal-body modalBody">
                    <div class="row">
                        <div class="col-xs-12 modalCell">
                            <div id="addUserKoModalInnerDiv1" class="modalDelMsg col-xs-12 centerWithFlex">
                                
                            </div>
                            <div class="modalDelObjName col-xs-12 centerWithFlex" id="addUserKoModalInnerDiv2"><i class="fa fa-frown-o" style="font-size:36px"></i></div> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" id="addUserKoBackBtn" class="btn cancelBtn">Go back to new user form</button>
                  <button type="button" id="addUserKoConfirmBtn" class="btn confirmBtn">Go back to users page</button>
                </div>
              </div>
            </div>
        </div>

        <!-- Modale di modifica account utente -->
        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div id="editUserModalLabel" class="modalHeader centerWithFlex">
                  
                </div>
                <div id="editUserModalLoading" class="modal-body container-fluid">
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                            Loading user's data, please wait
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                            <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                        </div> 
                    </div>
                </div>
                <div id="editUserModalUpdating" class="modal-body container-fluid">
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                Updating user's data, please wait
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 centerWithFlex">
                                <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                        </div> 
                    </div>
                </div>  
                <div id="editUserModalBody" class="modal-body modalBody">
                    <form id="editUserForm" name="editUserForm" role="form" method="post" action="process-form.php" data-toggle="validator">
                        <div class="row">
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" id="firstNameM" name="firstNameM" class="modalInputTxt">
                                </div>
                                <div class="modalFieldLabelCnt">First name</div>
                                <div id="firstNameMsgM" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" id="lastNameM" name="lastNameM" class="modalInputTxt">
                                </div>
                                <div class="modalFieldLabelCnt">Last name</div>
                                <div id="lastNameMsgM" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <select id="userTypeM" name="userTypeM" class="modalInputTxt">
                                        <option value="Observer">Observer</option>
                                        <option value="Manager">Manager</option>
                                        <option value="AreaManager">Area manager</option>
                                        <option value="ToolAdmin">Tool admin</option>
                                    </select>
                                </div>
                                <div class="modalFieldLabelCnt">Account type</div>
                                <div id="usertypeMsgM" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <select id="userStatusM" name="userStatusM" class="modalInputTxt">
                                        <option value="1">Active</option>
                                        <option value="0">Not active</option>
                                    </select>
                                </div>
                                <div class="modalFieldLabelCnt">Status</div>
                                <div id="userstatusMsgM" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" id="organizationM" name="organizationM" class="modalInputTxt">
                                </div>
                                <div class="modalFieldLabelCnt">Organization</div>
                                <div id="organizationMsgM" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="email" id="emailM" name="emailM" class="modalInputTxt" required>
                                </div>
                                <div class="modalFieldLabelCnt">E-Mail</div>
                                <div id="emailMsgM" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                        </div>
                        <div class="row" id="editUserPoolsRow" style="margin-top: 15px">
                            <div class="col-xs-12" id="editUserPoolsOuterContainer">
                                <div id="editUserPoolsContainer">
                                   <table id="editUserPoolsTable">
                                        <thead>
                                           <tr><th>Make member</th><th class="editUserPoolsTableMakeAdminHeader">Make admin</th><th>Pool name</th></tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                   </table>
                                </div>
                            </div> 
                        </div>
                       <input type="hidden" id="usernameM" name="usernameM"/>
                    </form>    
                </div>
                <div id="editUserModalFooter" class="modal-footer">
                  <button type="button" id="editUserCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                  <button type="button" id="editUserConfirmBtn" class="btn confirmBtn internalLink" disabled="true">Confirm</button>
                </div>
              </div>
            </div>
        </div>
        
        <!-- Modale di notifica edit account utente avvenuto con successo -->
        <div class="modal fade" id="editUserOkModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Update account
                </div>
                <div class="modal-body modalBody">
                    <div class="row">
                        <div class="col-xs-12 modalCell">
                            <div id="editUserOkModalInnerDiv1" class="modalDelMsg col-xs-12 centerWithFlex">
                                
                            </div>
                            <div class="modalDelObjName col-xs-12 centerWithFlex" id="editUserOkModalInnerDiv2"><i class="fa fa-check" style="font-size:36px"></i></div> 
                        </div>
                    </div>
                </div>
                <!--<div class="modal-footer">
                  
                </div>-->
              </div>
            </div>
        </div>
        
        <!-- Modale di notifica edit account utente fallito -->
        <div class="modal fade" id="editUserKoModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  Update account
                </div>
                <div id="delWidgetTypeModalBody" class="modal-body modalBody">
                    <div class="row">
                        <div class="col-xs-12 modalCell">
                            <div id="editUserKoModalInnerDiv1" class="modalDelMsg col-xs-12 centerWithFlex">
                                
                            </div>
                            <div class="modalDelObjName col-xs-12 centerWithFlex" id="edituserKoModalInnerDiv2"><i class="fa fa-frown-o" style="font-size:36px"></i></div> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" id="editUserKoBackBtn" class="btn cancelBtn">Go back to edit account form</button>
                  <button type="button" id="editUserKoConfirmBtn" class="btn confirmBtn">Go back to users page</button>
                </div>
              </div>
            </div>
        </div>

        
        
    </body>
</html>

<script type='text/javascript'>
    $(document).ready(function () 
    {
        $('#mainMenuCnt a.mainMenuSubItemLink[data-fathermenuid=mainSetupLink]').show();
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
                $('#usersTable').bootstrapTable('hideColumn', 'admin');
                $('#usersTable').bootstrapTable('hideColumn', 'status');
                $('#usersTable').bootstrapTable('hideColumn', 'name');
                $('#usersTable').bootstrapTable('hideColumn', 'surname');
                $('#usersTable').bootstrapTable('hideColumn', 'organization');
                $('#usersTable').bootstrapTable('hideColumn', 'email');
                $('#usersTable').bootstrapTable('hideColumn', 'reg_data');
            }
            else
            {
                $('#usersTable').bootstrapTable('showColumn', 'admin');
                $('#usersTable').bootstrapTable('showColumn', 'status');
                $('#usersTable').bootstrapTable('showColumn', 'name');
                $('#usersTable').bootstrapTable('showColumn', 'surname');
                $('#usersTable').bootstrapTable('showColumn', 'organization');
                $('#usersTable').bootstrapTable('showColumn', 'email');
                $('#usersTable').bootstrapTable('showColumn', 'reg_data');
            }
        });
        
        $('#link_user_register .mainMenuSubItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt #link_user_register .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt #link_user_register .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        var admin = "<?= $_SESSION['loggedRole'] ?>";
        var existingPoolsJson = null;
        var internalDest = false;
        var tableFirstLoad = true;
        
        buildMainTable(false);
        
        //Settaggio dei globals per il file usersManagement.js
        setGlobals(admin, existingPoolsJson);
        
        $('#addNewUserConfirmBtn').off("click");
        $("#addNewUserConfirmBtn").click(function(){
            $("#addUserModalBody").hide();
            $("#addUserModalFooter").hide();
            $("#addUserModalCreating").show();

             newUserJson = {
                 username: $("#addUserForm #username").val(),
                 firstName: $("#addUserForm #firstName").val(),
                 lastName: $("#addUserForm #lastName").val(),
                 organization: $("#addUserForm #organization").val(),
                 userType: $("#addUserForm #userType").val(),
                 email: $("#addUserForm #email").val(),
                 pools: []
             };

             switch(newUserJson.userType)
             {
                 case 'Observer': case 'Manager':
                     $("#addUserPoolsTable tr").each(function(i){
                         if($(this).find(".addUserPoolsTableMakeMemberCheckbox input").prop("checked"))
                         {
                             var poolItem = {
                                poolId: $(this).find(".addUserPoolsTableMakeMemberCheckbox input").attr("data-poolid"),
                                makeAdmin: false
                             };
                             newUserJson.pools.push(poolItem);
                         }
                     });
                     break;

                 case 'AreaManager':
                     $("#addUserPoolsTable tr").each(function(){
                         if($(this).find(".addUserPoolsTableMakeMemberCheckbox input").prop("checked"))
                         {
                             var poolItem = {
                                poolId: $(this).find(".addUserPoolsTableMakeMemberCheckbox input").attr("data-poolid"),
                                makeAdmin: false
                             };
                             newUserJson.pools.push(poolItem);
                         }

                         if($(this).find(".addUserPoolsTableMakeAdminCheckbox input").prop("checked"))
                         {
                            var poolItem = {
                                poolId: $(this).find(".addUserPoolsTableMakeMemberCheckbox input").attr("data-poolid"),
                                makeAdmin: true
                             };
                             newUserJson.pools.push(poolItem);
                         }
                     });
                     break;

                 default://Se superadmin non si fa niente di specifico su GUI - I superadmin non vengono più scritti come admin dei pool su DB
                     break;
             }

             //Chiamata API di inserimento nuovo utente
             $.ajax({
                 url: "addUser.php",
                 data:{newUserJson: JSON.stringify(newUserJson)},
                 type: "POST",
                 async: true,
                 success: function (data) 
                 {
                     switch(data)
                     {
                         case '0':
                             $("#addUserModal").modal('hide');
                             $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + newUserJson.username + '</b> couldn\'t be registered because of a database failure while inserting data, please try again</h5>');
                             $("#addUserKoModal").modal('show');
                             $("#addUserModalCreating").hide();
                             $("#addUserModalBody").show();
                             $("#addUserModalFooter").show();
                             break;

                         case '1':
                             $("#addUserModal").modal('hide');
                             buildMainTable(true);
                             $("#addUserOkModalInnerDiv1").html('<h5>User <b>' + newUserJson.username + '</b> successfully registered</h5>');
                             $("#addUserOkModal").modal('show');
                             $("#addUserModalCreating").hide();
                             $("#addUserModalBody").show();
                             $("#addUserModalFooter").show();
                             setTimeout(function(){
                                 $("#addUserOkModal").modal('hide');
                             }, 2000);
                             break;

                         case '2':
                             $("#addUserModal").modal('hide');
                             $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + newUserJson.username + '</b> couldn\'t be registered because of a database failure while checking for existing usernames, please try again</h5>');
                             $("#addUserKoModal").modal('show');
                             $("#addUserModalCreating").hide();
                             $("#addUserModalBody").show();
                             $("#addUserModalFooter").show();
                             break;

                         case '3':
                             $("#addUserModal").modal('hide');
                             $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + newUserJson.username + '</b> couldn\'t be registered: this username is already in use, please change it and try again</h5>');
                             $("#addUserKoModal").modal('show');
                             $("#addUserModalCreating").hide();
                             $("#addUserModalBody").show();
                             $("#addUserModalFooter").show();
                             break;

                         case '4':
                             $("#addUserModal").modal('hide');
                             $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + newUserJson.username + '</b> couldn\'t be registered: password is less than 8 chars long and/or doesn\'t have at least 1 char and 1 digit, please change it and try again</h5>');
                             $("#addUserKoModal").modal('show');
                             $("#addUserModalCreating").hide();
                             $("#addUserModalBody").show();
                             $("#addUserModalFooter").show();
                             break;

                         case '5':
                             $("#addUserModal").modal('hide');
                             $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + newUserJson.username + '</b> couldn\'t be registered: password and password confirmation don\'t match, please correct and try again</h5>');
                             $("#addUserKoModal").modal('show');
                             $("#addUserModalCreating").hide();
                             $("#addUserModalBody").show();
                             $("#addUserModalFooter").show();
                             break;

                         case '6':
                             $("#addUserModal").modal('hide');
                             $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + newUserJson.username + '</b> couldn\'t be registered: one between (first name - last name) and organization must be given, please correct and try again</h5>');
                             $("#addUserKoModal").modal('show');
                             $("#addUserModalCreating").hide();
                             $("#addUserModalBody").show();
                             $("#addUserModalFooter").show();
                             break;

                         case '7':
                             $("#addUserModal").modal('hide');
                             $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + newUserJson.username + '</b> couldn\'t be registered: e-mail address doesn\'t respect mailbox@domain.ext pattern, please correct and try again</h5>');
                             $("#addUserKoModal").modal('show');
                             $("#addUserModalCreating").hide();
                             $("#addUserModalBody").show();
                             $("#addUserModalFooter").show();
                             break;

                         default:
                             break;
                     }
                 },
                 error: function (data) 
                 {
                     console.log("Ko result: " + data);
                     $("#addUserModal").modal('hide');
                     $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + newUserJson.username + '</b> couldn\'t be registered because of an API call failure, please try again</h5>');
                     $("#addUserKoModal").modal('show');
                     $("#addUserModalCreating").hide();
                     $("#addUserModalBody").show();
                     $("#addUserModalFooter").show();
                 }
             });
        });
        
        $('#deleteUserConfirmBtn').off("click");
        $("#deleteUserConfirmBtn").click(function(){
            var username = $("#deleteUserModal span").attr("data-username");
    
            $("#deleteUserModal div.modal-body").html("");
            $("#deleteUserCancelBtn").hide();
            $("#deleteUserConfirmBtn").hide();
            $("#deleteUserModal div.modal-body").append('<div id="deleteUserModalInnerDiv1" class="modalBodyInnerDiv"><h5>User deletion in progress, please wait</h5></div>');
            $("#deleteUserModal div.modal-body").append('<div id="deleteUserModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px"></i></div>');

             //Chiamata API di cancellazione utente
            $.ajax({
                url: "deleteUser.php",
                data:{username: username},
                type: "POST",
                async: false,
                success: function (data) 
                {
                    if(data === '0')
                    {
                        $("#deleteUserModalInnerDiv1").html('User &nbsp; <b>' + username + '</b> &nbsp; deletion failed, please try again');
                        $("#deleteUserModalInnerDiv2").html('<i class="fa fa-frown-o" style="font-size:42px"></i>');
                    }
                    else if( data === '1')
                    {
                        $("#deleteUserModalInnerDiv1").html('User &nbsp; <b>' + username + '</b> &nbsp;deleted successfully');
                        $("#deleteUserModalInnerDiv2").html('<i class="fa fa-check" style="font-size:42px"></i>');
                        setTimeout(function()
                        {
                            buildMainTable(true);
                            $("#deleteUserModal").modal('hide');
                            setTimeout(function(){
                                $("#deleteUserCancelBtn").show();
                                $("#deleteUserConfirmBtn").show();
                            }, 500);
                        }, 2000);
                    }
                },
                error: function (data) 
                {
                    $("#deleteUserModalInnerDiv1").html('User &nbsp; <b>' + username + '</b> &nbsp; deletion failed, please try again');
                    $("#deleteUserModalInnerDiv2").html('<i class="fa fa-frown-o" style="font-size:42px"></i>');
                }
            });
        });
        
        $('#editUserConfirmBtn').off("click");
        $("#editUserConfirmBtn").click(function(){
            $("#editUserModalBody").hide();
            $("#editUserModalFooter").hide();
            $("#editUserModalUpdating").show();

             accountJson = {
                 username: $("#editUserForm #usernameM").val(),
                 firstName: $("#editUserForm #firstNameM").val(),
                 lastName: $("#editUserForm #lastNameM").val(),
                 organization: $("#editUserForm #organizationM").val(),
                 userType: $("#editUserForm #userTypeM").val(),
                 userStatus: $("#editUserForm #userStatusM").val(),
                 email: $("#editUserForm #emailM").val(),
                 pools: []
             };

             switch(accountJson.userType)
             {
                 case 'Observer': case 'Manager':
                     $("#editUserPoolsTable tr").each(function(i){
                         if($(this).find(".editUserPoolsTableMakeMemberCheckbox input").prop("checked"))
                         {
                             var poolItem = {
                                poolId: $(this).find(".editUserPoolsTableMakeMemberCheckbox input").attr("data-poolid"),
                                makeAdmin: false
                             };
                             accountJson.pools.push(poolItem);
                         }
                     });
                     break;

                 case 'AreaManager':
                     $("#editUserPoolsTable tr").each(function(){
                         if($(this).find(".editUserPoolsTableMakeMemberCheckbox input").prop("checked"))
                         {
                             var poolItem = {
                                poolId: $(this).find(".editUserPoolsTableMakeMemberCheckbox input").attr("data-poolid"),
                                makeAdmin: false
                             };
                             accountJson.pools.push(poolItem);
                         }

                         if($(this).find(".editUserPoolsTableMakeAdminCheckbox input").prop("checked"))
                         {
                            var poolItem = {
                                poolId: $(this).find(".editUserPoolsTableMakeMemberCheckbox input").attr("data-poolid"),
                                makeAdmin: true
                             };
                             accountJson.pools.push(poolItem);
                         }
                     });
                     break;

                 default://Se superadmin non si fa niente di specifico su GUI - I superadmin non vengono più scritti come admin dei pool su DB
                     break;
             }

             console.log(JSON.stringify(accountJson));

             //Chiamata API di aggiornamento account utente
             $.ajax({
                 url: "editUser.php",
                 data:{operation: "updateAccount", accountJson: JSON.stringify(accountJson)},
                 type: "POST",
                 async: true,
                 success: function (data) 
                 {
                     switch(data)
                     {
                         case '0':
                             $("#editUserModal").modal('hide');
                             $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated because of a database failure while inserting data, please try again</h5>');
                             $("#editUserKoModal").modal('show');
                             $("#editUserModalUpdating").hide();
                             $("#editUserModalBody").show();
                             $("#editUserModalFooter").show();
                             break;

                         case '1':
                             $("#editUserModal").modal('hide');
                             $("#editUserOkModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> successfully updated</h5>');
                             $("#editUserOkModal").modal('show');
                             setTimeout(updateAccountTimeout, 2000);
                             break;

                         case '4':
                             $("#editUserModal").modal('hide');
                             $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated: password is less than 8 chars long and/or doesn\'t have at least 1 char and 1 digit, please change it and try again</h5>');
                             $("#editUserKoModal").modal('show');
                             $("#editUserModalUpdating").hide();
                             $("#editUserModalBody").show();
                             $("#editUserModalFooter").show();
                             break;

                         case '5':
                             $("#editUserModal").modal('hide');
                             $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated: password and password confirmation don\'t match, please fix and try again</h5>');
                             $("#editUserKoModal").modal('show');
                             $("#editUserModalUpdating").hide();
                             $("#editUserModalBody").show();
                             $("#editUserModalFooter").show();
                             break;

                         case '6':
                             $("#editUserModal").modal('hide');
                             $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated: one between (first name - last name) and organization must be given, please fix and try again</h5>');
                             $("#editUserKoModal").modal('show');
                             $("#editUserModalUpdating").hide();
                             $("#editUserModalBody").show();
                             $("#editUserModalFooter").show();
                             break;

                         case '7':
                             $("#editUserModal").modal('hide');
                             $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated: e-mail address doesn\'t respect mailbox@domain.ext pattern, please fix and try again</h5>');
                             $("#editUserKoModal").modal('show');
                             $("#editUserModalUpdating").hide();
                             $("#editUserModalBody").show();
                             $("#editUserModalFooter").show();
                             break;

                         default:
                             break;
                     }
                 },
                 error: function (data) 
                 {
                     console.log("Ko result: " + data);
                     $("#editUserModal").modal('hide');
                     $("#editUserKoModalInnerDiv1").html('<h5>Account <b>' + accountJson.username + '</b> couldn\'t be updated because of an API call failure, please try again</h5>');
                     $("#editUserKoModal").modal('show');
                     $("#editUserModalUpdating").hide();
                     $("#editUserModalBody").show();
                     $("#editUserModalFooter").show();
                 }
             });
        });
        
        $("#addNewUserCancelBtn").off("click");
        $("#addNewUserCancelBtn").on('click', function(){
            $("#addUserForm").trigger("reset");
            $("#addUserAdminRoleChoiceOuterContainer").hide();
            $("#addUserAdminPoolsChoiceOuterContainer").hide();
            $("#addUserNewPoolNameOuterContainer").hide();
            $("#addUserAddUsersToNewPoolOuterContainer").hide();
            $("#addUserPoolsOuterContainer").show();
        });
        
        $("#addUserKoBackBtn").off("click");
        $("#addUserKoBackBtn").on('click', function(){
            $("#addUserKoModal").modal('hide');
            $("#addUserModal").modal('show');
        });
        
        $("#addUserKoConfirmBtn").off("click");
        $("#addUserKoConfirmBtn").on('click', function(){
            $("#addUserKoModal").modal('hide');
            $("#addUserForm").trigger("reset");
        });
        
        $("#editUserKoBackBtn").off("click");
        $("#editUserKoBackBtn").on('click', function(){
            $("#editUserKoModal").modal('hide');
            $("#editUserModal").modal('show');
        });
        
        $("#addUserKoConfirmBtn").off("click");
        $("#addUserKoConfirmBtn").on('click', function(){
            $("#editUserKoModal").modal('hide');
            $("#editUserForm").trigger("reset");
        });
        
        $("#userType").change(function()
        {
           $(".addUserPoolsTableMakeMemberCheckbox input").off("click");
           $(".addUserPoolsTableMakeAdminCheckbox input").off("click");
           
           switch($(this).val())
           {
                case "Observer": case "Manager":
                   $(".addUserPoolsTableMakeAdminHeader").hide();
                   $(".addUserPoolsTableMakeAdminCheckbox").hide();
                   $("#addUserPoolsRow").show();
                   break;
                   
                case "AreaManager":
                   $(".addUserPoolsTableMakeMemberCheckbox input").click(function(){
                     $(this).parent().parent().find(".addUserPoolsTableMakeAdminCheckbox input").prop("checked", false);
                   });
                   
                   $(".addUserPoolsTableMakeAdminCheckbox input").click(function(){
                     $(this).parent().parent().find(".addUserPoolsTableMakeMemberCheckbox input").prop("checked", false);
                   });
                   
                   $(".addUserPoolsTableMakeAdminHeader").show();
                   $(".addUserPoolsTableMakeAdminCheckbox").show();
                   $("#addUserPoolsRow").show();
                   break;   
                    
                case "ToolAdmin":
                    $("#addUserPoolsRow").hide();
                    break;
           }
        });
        
        $("#userTypeM").change(function()
        {
           $(".editUserPoolsTableMakeMemberCheckbox input").off("click");
           $(".editUserPoolsTableMakeAdminCheckbox input").off("click");
           
           switch($(this).val())
           {
                case "Observer": case "Manager":
                   $(".editUserPoolsTableMakeAdminHeader").hide();
                   $(".editUserPoolsTableMakeAdminCheckbox").hide();
                   $("#editUserPoolsRow").show();
                   break;
                   
                case "AreaManager":
                   $(".editUserPoolsTableMakeMemberCheckbox input").click(function(){
                     $(this).parent().parent().find(".editUserPoolsTableMakeAdminCheckbox input").prop("checked", false);
                   });
                   
                   $(".editUserPoolsTableMakeAdminCheckbox input").click(function(){
                     $(this).parent().parent().find(".editUserPoolsTableMakeMemberCheckbox input").prop("checked", false);
                   });
                   
                   $(".editUserPoolsTableMakeAdminHeader").show();
                   $(".editUserPoolsTableMakeAdminCheckbox").show();
                   $("#editUserPoolsRow").show();
                   break;   
                    
                case "ToolAdmin":
                    $("#editUserPoolsRow").hide();
                    break;
           }
        });
        
        function updateAccountTimeout()
        {
            $("#editUserOkModal").modal('hide');
            setTimeout(function(){
               location.reload();
            }, 500);
        }
        
        function buildMainTable(destroyOld)
        {
            if(destroyOld)
            {
                $('#usersTable').bootstrapTable('destroy');
                tableFirstLoad = true;
            }
            
            var accountVisibile = true;
            var statusVisible = true;
            var firstNameVisibile = true;
            var lastNameVisibile = true;
            var orgVisibile = true;
            var emailVisibile = true;
            var regDateVisibile = true;

            if($(window).width() < 992)
            {
                accountVisibile = false;
                statusVisible = false; 
                firstNameVisibile = false;
                lastNameVisibile = false;
                orgVisibile = false;
                emailVisibile = false;
                regDateVisibile = false;
            }
            

            $.ajax({
                url: "get_data.php",
                data: {action: "getLocalUsers"},
                type: "GET",
                async: true,
                datatype: 'json',
                success: function (data)
                {
                    $('#usersTable').bootstrapTable({
                            columns: [{
                                field: 'username',
                                title: 'Username',
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
                                field: 'admin',
                                title: 'Account',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: accountVisibile,
                                formatter: function(value, row, index)
                                {
                                    switch(value)
                                    {
                                       case "ToolAdmin":
                                          return "Tool admin";
                                          break;

                                       case "AreaManager":
                                          return "Area manager";
                                          break;

                                       case "Manager":
                                          return "Manager";
                                          break;
                                          
                                       default:
                                          return value;
                                          break;   
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
                                field: 'status',
                                title: 'Status',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: statusVisible,
                                formatter: function(value, row, index)
                                {
                                    if(value === '0')
                                    {
                                        return "Not active";
                                    }
                                    else 
                                    {
                                        return "Active";
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
                                field: 'name',
                                title: 'First name',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: firstNameVisibile,
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
                                field: 'surname',
                                title: 'Last name',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: lastNameVisibile,
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
                                field: 'organization',
                                title: 'Organization',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: orgVisibile,
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
                                field: 'email',
                                title: 'E-Mail',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: emailVisibile,
                                formatter: function(value, row, index)
                                {
                                    return value;
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
                                field: 'reg_data',
                                title: 'Registration date',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                visible: regDateVisibile,
                                formatter: function(value, row, index)
                                {
                                    return value;
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
                                    //return '<span class="glyphicon glyphicon-remove"></span>'; 
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
                            uniqueId: "IdUser",
                            striped: false,
                            searchTimeOut: 60,
                            classes: "table table-hover table-no-bordered",
                            rowAttributes: function(row, index){
                            return {
                                "data-username": row.username,
                                "data-admin": row.admin,
                                "data-status": row.status,
                                "data-name": row.name,
                                "data-surname": row.surname,
                                "data-organization": row.organization,
                                "data-email": row.email,
                                "data-reg_data": row.reg_data
                            };},
                            onPostBody: function()
                            {
                                if(tableFirstLoad)
                                {
                                    //Caso di primo caricamento della tabella
                                    tableFirstLoad = false;
                                    var addUserDiv = $('<div class="pull-right"><i id="addUserBtn" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');
                                    
                                    $('div.fixed-table-toolbar').append(addUserDiv);
                                    addUserDiv.css("margin-top", "10px");
                                    addUserDiv.find('i.fa-plus-square').off('hover');
                                    addUserDiv.find('i.fa-plus-square').hover(function(){
                                        $(this).css('color', '#e37777');
                                        $(this).css('cursor', 'pointer');
                                    }, 
                                    function(){
                                        $(this).css('color', '#ffcc00');
                                        $(this).css('cursor', 'normal');
                                    });
                                    $("#addUserBtn").off("click");
                                    $("#addUserBtn").click(showAddUserModal);
                                    $('#usersTable thead').css("background", "rgba(0, 162, 211, 1)");
                                    $('#usersTable thead').css("color", "white");
                                    $('#usersTable thead').css("font-size", "1em");
                                }
                                else
                                {
                                    //Casi di cambio pagina
                                }

                                //Istruzioni da eseguire comunque
                                $('#usersTable').css("border-bottom", "none");
                                $('span.pagination-info').hide();

                                $('#usersTable button.editDashBtn').off('hover');
                                $('#usersTable button.editDashBtn').hover(function(){
                                    $(this).css('background', '#ffcc00');
                                    $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                                }, 
                                function(){
                                    $(this).css('background', 'rgb(69, 183, 175)');
                                    $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                                });

                                $('#usersTable button.editDashBtn').off('click');
                                $('#usersTable button.editDashBtn').click(function(){
                                    $("#editUserModalUpdating").hide();
                                    $("#editUserModalBody").show();
                                    $("#editUserModalFooter").show();
                                    $("#editUserModal").modal('show');
                                    $("#editUserModalLabel").html("Edit account - " + $(this).parents('tr').attr("data-username"));
                                    $("#usernameM").val($(this).parents('tr').attr("data-username"));
                                    $("#firstNameM").val($(this).parents('tr').attr("data-name"));
                                    $("#lastNameM").val($(this).parents('tr').attr("data-surname"));
                                    $("#organizationM").val($(this).parents('tr').attr("data-organization"));
                                    $("#userStatusM").val($(this).parents('tr').attr("data-status"));
                                    $("#emailM").val($(this).parents('tr').attr("data-email"));
                                    var role = $(this).parents('tr').attr("data-admin");
                                    $("#userTypeM").val(role);

                                    $.ajax({
                                        url: "editUser.php",
                                        data: {operation: "getUserPoolMemberships", username: $(this).parents('tr').attr("data-username")},
                                        type: "GET",
                                        async: true,
                                        dataType: 'json',
                                        success: function (data) 
                                        {
                                          var row = null;

                                          $("#editUserPoolsTable tbody").empty();
                                          for(var i = 0; i < data.length; i++)
                                          {
                                             row = $('<tr><td class="checkboxCell editUserPoolsTableMakeMemberCheckbox"><input data-poolId="' + data[i].poolId + '" type="checkbox" /></td><td class="checkboxCell editUserPoolsTableMakeAdminCheckbox"><input data-poolId="' +  data[i].poolId + '" type="checkbox" /></td><td class="poolNameCell">' + data[i].poolName + '</td>');

                                             switch(role)
                                             {
                                                case "Observer": case "Manager":
                                                   if(data[i].username !== null)
                                                   {
                                                      row.find(".editUserPoolsTableMakeMemberCheckbox input").attr("checked", true);
                                                   }
                                                   $(".editUserPoolsTableMakeAdminHeader").hide();
                                                   $(".editUserPoolsTableMakeAdminCheckbox").hide();
                                                   break;

                                                case "Area manager":
                                                   if(data[i].username !== null)
                                                   {
                                                      if(data[i].isAdmin === "1")
                                                      {
                                                         row.find(".editUserPoolsTableMakeAdminCheckbox input").attr("checked", true);
                                                      }
                                                      else
                                                      {
                                                         row.find(".editUserPoolsTableMakeMemberCheckbox input").attr("checked", true);
                                                      }
                                                   }

                                                   $(".editUserPoolsTableMakeAdminHeader").show();
                                                   $(".editUserPoolsTableMakeAdminCheckbox").show();
                                                   break;

                                                case "Tool admin":
                                                   break;   
                                             }

                                             $("#editUserPoolsTable").append(row);
                                          }

                                          switch(role)
                                          {
                                             case "Observer": 
                                                $("#editUserPoolsRow").show();
                                                $(".editUserPoolsTableMakeAdminHeader").hide();
                                                $(".editUserPoolsTableMakeAdminCheckbox").hide();
                                                break;

                                             case "Manager":   
                                                $("#editUserPoolsRow").show();
                                                $(".editUserPoolsTableMakeAdminHeader").hide();
                                                $(".editUserPoolsTableMakeAdminCheckbox").hide();
                                                break;

                                             case "Area manager":
                                                $(".editUserPoolsTableMakeMemberCheckbox input").click(function(){
                                                   $(this).parent().parent().find(".editUserPoolsTableMakeAdminCheckbox input").prop("checked", false);
                                                });

                                                $(".editUserPoolsTableMakeAdminCheckbox input").click(function(){
                                                   $(this).parent().parent().find(".editUserPoolsTableMakeMemberCheckbox input").prop("checked", false);
                                                });

                                                $("#editUserPoolsRow").show();
                                                $(".editUserPoolsTableMakeAdminHeader").show();
                                                $(".editUserPoolsTableMakeAdminCheckbox").show();
                                                break;

                                             case "Tool admin":
                                                $("#editUserPoolsRow").hide();
                                                break;   
                                          }

                                           $("#editUserModalLoading").hide();

                                           showEditUserModalBody();
                                        },
                                        error: function (data)
                                        {
                                           console.log("Get user pool memberships KO");
                                           console.log(data);
                                        }
                                    });
                                });

                                $('#usersTable button.delDashBtn').off('hover');
                                $('#usersTable button.delDashBtn').hover(function(){
                                    $(this).css('background', '#ffcc00');
                                    $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                                }, 
                                function(){
                                    $(this).css('background', '#e37777');
                                    $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                                });

                                $('#usersTable button.delDashBtn').off('click');
                                $('#usersTable button.delDashBtn').click(function(){
                                    var username = $(this).parents("tr").find("td").eq(0).html();
                                    $("#deleteUserModal div.modal-body").html('<div class="modalBodyInnerDiv"><span data-username = "' + username + '">Do you want to confirm deletion of user <b>' + username + '</b>?</span></div>');
                                    $("#deleteUserModal").modal('show');
                                });
                            }
                        });
                    }
            });
        }
    });
</script>  