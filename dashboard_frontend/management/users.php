<?php
/* Dashboard Builder.
  Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

  This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */
   include('../config.php');
if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

include('process-form.php');
//session_start();

    checkSession('RootAdmin');
?>

<!DOCTYPE html>
<html class="dark">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php include "mobMainMenuClaim.php" ?></title>

        <!-- Bootstrap Core CSS -->
        <link href="../css/s4c-css/bootstrap/bootstrap.css" rel="stylesheet">
        <link href="../css/s4c-css/bootstrap/bootstrap-colorpicker.min.css" rel="stylesheet">

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
        <?php include "theme-switcher.php"?>
        <link href="../css/dashboard.css" rel="stylesheet">
        <!--<link href="../css/pageTemplate.css" rel="stylesheet">-->

        <!-- Custom scripts -->
        <script src="../js/usersManagement.js"></script>

        <!--<link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
        <style>
            #addUserGroupsTable thead th, #editUserGroupsTable thead th
{
    background: rgba(0, 162, 211, 1);
    color: white;
}

#addUserGroupsTable, #aaddUserGroupsTable tr, #addUserGroupsTable td, #editUserGroupsTable, #editUserGroupsTable tr, #editUserGroupsTable td /*#addUserExistingPoolsTable, #addUserExistingPoolsTable tr, 
#addUserExistingPoolsTable td, #addUserAddUsersToNewPoolTable, #addUserAddUsersToNewPoolTable tr, #addUserAddUsersToNewPoolTable td*/
{
    width: 100%;
    border: none;
}

#addUserGroupsTable tr:nth-child(odd), #editUserGroupsTable tr:nth-child(odd)/*, #addUserExistingPoolsTable tr:nth-child(odd), #addUserAddUsersToNewPoolTable tr:nth-child(odd)*/
{
    background-color: white;
}

#addUserGroupsTable tr:nth-child(even), #editUserGroupsTable tr:nth-child(even)/*#addUserExistingPoolsTable tr:nth-child(even), #addUserAddUsersToNewPoolTable tr:nth-child(even)*/
{
    background-color: rgb(230, 249, 255);
}

#addUserGroupsTable th, #editUserGroupsTable th
{
   text-align: center;
}

#addUserGroupsTable .addUserGroupsTableMakeAdminHeader, #addUserGroupsTable .addUserGroupsTableMakeAdminCheckbox,
#editUserGroupsTable .editUserGroupsTableMakeAdminHeader, #editUserGroupsTable .editUserGroupsTableMakeAdminCheckbox
{
   display: none;
}

#addUserGroupsTable td.checkboxCell, #addUserExistingGroupsTable td.checkboxCell,
#editUserGroupsTable td.checkboxCell, #editUserExistingGroupsTable td.checkboxCell  
{
    width: 20%;
    text-align: center;
}

#addUserGroupsTable td.poolNameCell, #editUserGroupsTable td.poolNameCell
{
    width: 60%;
    text-align: center;
}

#addUserGroupsRow{
    display: none;
}
.modalInputTxt{
    color: black !important;
}
#addUserDelOrgsTable thead th,
#editUserDelOrgsTable thead th {
  background: rgba(0, 162, 211, 1);
  color: white;
}

#addUserDelOrgsTable, 
#addUserDelOrgsTable tr, 
#addUserDelOrgsTable td,
#editUserDelOrgsTable, 
#editUserDelOrgsTable tr, 
#editUserDelOrgsTable td {
  width: 100%;
  border: none;
}

#addUserDelOrgsTable tr:nth-child(odd),
#editUserDelOrgsTable tr:nth-child(odd) {
  background-color: white;
}

#addUserDelOrgsTable tr:nth-child(even),
#editUserDelOrgsTable tr:nth-child(even) {
  background-color: rgb(230, 249, 255);
}

#addUserDelOrgsTable th,
#editUserDelOrgsTable th {
  text-align: center;
}

#addUserDelOrgsTable td.checkboxCell,
#editUserDelOrgsTable td.checkboxCell {
  width: 20%;
  text-align: center;
}

#addUserDelOrgsTable td.orgNameCell,
#editUserDelOrgsTable td.orgNameCell {
  width: 60%;
  text-align: center;
}

#addUserDelOrgsRow,
#editUserDelOrgsRowMod {
  display: none;
}


        </style>
    </head>
    <body class="guiPageBody">
    <?php include "../cookie_banner/cookie-banner.php"; ?>
        <div class="container-fluid">
            <?php include "sessionExpiringPopup.php" ?>
            <div class="mainContainer">
            <div class="menuFooter-container">
              <?php include "mainMenu.php" ?>
              <?php include "footer.php" ?>
            </div>
                <div class="col-xs-12 col-md-10" id="mainCnt">
                    <!-- MOBILE MENU -->
                    <!-- <div class="row hidden-md hidden-lg">
                        <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                            <?php include "mobMainMenuClaim.php" ?>
                        </div>
                        </div> -->
                    <div class="row header-container">
                      <div id="mobLogo"><?php include "logoS4cSVG.php"; ?></div>
                        <div id="headerTitleCnt" style="text-align:center">Users</div>
                        <div class="user-menu-container">
                          <?php include "loginPanel.php" ?>
                        </div>
                        <div class="col-lg-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <div class="row hidden-xs hidden-sm mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc">Synthesis</div>
                                <div id="dashboardTotNumberCnt" class="col-md-2 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">

                                    </div>
                                    <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                        users
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
                    <div>
                        <input type="text" id="del_org" class="modalInputTxt" style="display:none;"/>
                        <input type="text" id="del_role" class="modalInputTxt" style="display:none;"/>
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
                                        <input type="password" id="password" name="password" class="modalInputTxt" pattern="[A-Za-z0-9_]+" title="Numbers, letters and _ are admitted" value="" required>
                                        <div class="modalFieldLabelCnt">Password</div>
                                        <div id="passwordMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                </div>
                                <!--  -->
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="password" id="confirm_password" name="password" class="modalInputTxt" pattern="[A-Za-z0-9_]+" title="Numbers, letters and _ are admitted" value="" required>
                                        <div class="modalFieldLabelCnt">Confirm Password</div>
                                        <div id="conf_passwordMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                </div>
                                <!-- -->
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" id="email" name="email" class="modalInputTxt" pattern="[A-Za-z0-9_]+" title="Numbers, letters and _ are admitted" required>
                                        <div class="modalFieldLabelCnt">Email</div>
                                        <div id="passwordMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                </div>
                                <!-- -->
                                <div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <select id="userType" name="userType" class="modalInputTxt">
                                            <option value="Observer">Observer</option>
                                            <option value="Manager">Manager</option>
                                            <option value="AreaManager">Area manager</option>
                                            <option value="ToolAdmin">Tool admin</option>
                                            <option value="RootAdmin">Root admin</option>
                                        </select>
                                    </div>
                                    <div class="modalFieldLabelCnt">Account Role</div>
                                    <div id="usertypeMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                </div>
                                <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    CSBL Active? <input type="checkbox" id="CSBL_CheckC" name="CSBL" class=""> |
                                    Data-Ingestion Table Active? <input type="checkbox" id="Data-IngestionC" name="Data-Ingestion" class=""> |
                                    <!--Advisor Active? <input type="checkbox" id="AdvisorC" name="Advisor" class="">-->
                                    <hr style="margin-top: 5px; margin-bottom: 5px;" />   
                                    <div class="modalFieldLabelCnt">Authorizations</div>
                                </div>
                                </div>
                                <!--<div class="col-xs-12 col-md-6 modalCell">
                                    <div class="modalFieldCnt">
                                        <input type="text" id="SubitemsC" name="Subitems" class="modalInputTxt" pattern="^\d+(?:,\d+)*$" title="Enter menu numbers (comma-separated)">
                                    </div>
                                    <div class="modalFieldLabelCnt">Subitem menus numbers(comma-separated)</div>
                                </div>-->


                                <div class="row" id="addUserPoolsRow">
                                    <div class="col-xs-12" id="addUserPoolsOuterContainer">
                                        <div id="addUserPoolsContainer">
                                            <table id="addUserPoolsTable"><thead><tr><th>Make member</th><th class="addUserPoolsTableMakeAdminHeader">Make admin</th><th>Organization</th></tr></thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> 
                                </div>
                            <!-- MANAGE GROUPS -->
                            <div class="row" id="addUserGroupsRow">
                            <div class="col-xs-12" id="addUserGroupsOuterContainer">
                                        <div id="addUserGroupsContainer">
                                        <table id="addUserGroupsTable"><thead><tr><th>Make member</th><th class="addUserGroupsTableMakeAdminHeader">Make admin</th><th>Groups</th></tr></thead>
                                                <tbody>
                                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- END MANAGE GROUP -->
                            <!-- MANAGE USERSTATS DELEGATED ORGS -->
                            <?php if($userMonitoring == 'true'): ?>
                            <div class="row" id="addUserDelOrgsRow" style="display:none;">
                            <div class="col-xs-12">
                                <table id="addUserDelOrgsTable">
                                <thead>
                                    <tr>
                                    <th>Make member</th>
                                    <th>Delegated userstats orgs</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                </table>
                            </div>
                            </div>
                            <?php endif; ?>
                             <!-- END MANAGE USERSTATS -->
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
                        <!--<button type="button" id="addNewUserConfirmBtn" name="addWidgetType" class="btn confirmBtn internalLink" disabled="true">Confirm</button>-->
                        <button type="button" id="addNewUserConfirmBtn2" name="addWidgetType" class="btn confirmBtn internalLink">Confirm</button>
                    </div>
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

    <!-- MODAL DI MODIFCA DI UN UTENTE -->
    <div class="modal fade" id="editNewUserModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div id="editNewUserModalLabel" class="modalHeader centerWithFlex">
                    Edit User
                </div>
                <div id="editNewUserModalBody" class="modal-body modalBody">
                    <form id="NeweditNewUserForm" name="editUserForm" role="form" method="post" action="process-form.php" data-toggle="validator">
                        <div class="row">
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="password" id="passwordM" name="passwordM" class="modalInputTxt" pattern="[A-Za-z0-9_]+" title="Numbers, letters and _ are admitted">
                                    <input type="text" id="old_passwordM" name="old_passwordM" style="display:none;">
                                    <div class="modalFieldLabelCnt">New Password</div>
                                    <div id="passwordMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                </div>
                            </div>
                            <!----->
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="password" id="Confirm_passwordM" name="passwordM" class="modalInputTxt" pattern="[A-Za-z0-9_]+" title="Numbers, letters and _ are admitted">
                                    <div class="modalFieldLabelCnt">Confirm Password</div>
                                    <div id="confirm_passwordMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                </div>
                            </div>
                            <!-- -->
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" id="emailM" name="emailM" class="modalInputTxt" pattern="[A-Za-z0-9_]+" title="Numbers, letters and _ are admitted" required>
                                    <!-- -->
                                    <input type="text" id="old_emailM" name="old_emailM" style="display:none;">
                                    <div class="modalFieldLabelCnt">Email</div>
                                    <div id="mailMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                </div>
                            </div>
                            <!-- -->
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <select id="NewuserTypeM" name="userTypeM" class="modalInputTxt">
                                        <option value="Observer">Observer</option>
                                        <option value="Manager">Manager</option>
                                        <option value="AreaManager">AreaManager</option>
                                        <option value="ToolAdmin">ToolAdmin</option>
                                        <option value="RootAdmin">RootAdmin</option>
                                    </select>
                                </div>
                                <input type="text" id="old_roleM" name="old_roleM" style="display:none;">
                                <div class="modalFieldLabelCnt">Role</div>
                                <div id="NewusertypeMsgM" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    CSBL Active? <input type="checkbox" id="CSBL_Check" name="CSBL" class=""> |
                                    Data-Ingestion Table Active? <input type="checkbox" id="Data-Ingestion" name="Data-Ingestion" class=""> |
                                    <!--Advisor Active? <input type="checkbox" id="Advisor" name="Advisor" class="">-->
                                    <hr style="margin-top: 5px; margin-bottom: 5px;" />   
                                    <div class="modalFieldLabelCnt">Authorizations</div>
                                </div>
                            </div>
                            <!--<div class="col-xs-12 col-md-6 modalCell">
                                <div class="modalFieldCnt">
                                    <input type="text" id="Subitems" name="Subitems" class="modalInputTxt" pattern="^\d+(?:,\d+)*$" title="Enter menu numbers (comma-separated)">
                                </div>
                                <div class="modalFieldLabelCnt">Subitem menus numbers(comma-separated)</div>
                            </div>-->
                            <div class="col-xs-12 col-md-6 modalCell" style="display:none;">
                                <div class="modalFieldCnt">
                                    <input type="text" id="NeworganizationM" name="organizationM" class="modalInputTxt">
                                    <input type="text" id="old_organizationM" name="old_organizationM" class="modalInputTxt" style="display:none;">
                                </div>
                                <div class="modalFieldLabelCnt">Organization</div>
                                <div id="organizationMsgM" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <div class="col-xs-12 col-md-6 modalCell" style="display:none;">
                                <div class="modalFieldCnt">
                                    <input type="text" id="NewgroupM" name="NewgroupM" class="modalInputTxt">
                                    <input type="text" id="old_GroupsM" name="old_GroupsM" class="modalInputTxt" style="display:none;">
                                </div>
                                <div class="modalFieldLabelCnt">Groups</div>
                                <div id="groupMsgM" class="modalFieldMsgCnt">&nbsp;</div>
                            </div>
                            <!--<div class="col-xs-12 col-md-6 modalCell">-->
                            <!-- -->
                            <div class="row" id="addUserPoolsRowMod">
                                <div class="col-xs-12" id="addUserPoolsOuterContainer">
                                    <div id="editUserPoolsContainer">
                                        <table id="editUserPoolsTable">
                                            <thead><tr><th>Make member</th><th>Organization</th></tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> 
                            </div>
                            <!-- -->
                            <div class="row" id="addUserGroupsRowMod">
                                <div class="col-xs-12" id="addUserGroupsOuterContainer">
                                    <div id="editUserGroupsContainer">
                                        <table id="editUserGroupsTable">
                                            <thead><tr><th>Make member</th><th>Group</th></tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> 
                            </div>
                            <?php if($userMonitoring == 'true'): ?>
                            <div class="row" id="editUserDelOrgsRowMod" style="display:none;">
                            <div class="col-xs-12">
                                <table id="editUserDelOrgsTable">
                                <thead>
                                    <tr>
                                    <th>Make member</th>
                                    <th>Delegated userstats orgs</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                </table>
                            </div>
                            </div>
                            <?php endif; ?>
                            <!--</div>-->
                        </div>


                    </form>    
                </div>
                <div id="editUserModalFooter" class="modal-footer">
                    <button type="button" id="editUserCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                    <button type="button" id="editUserConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
                    <!--<button type="button" id="editUserConfirmBtn" class="btn confirmBtn internalLink" disabled="true">Confirm</button>-->
                </div>
            </div>
        </div>
    </div>


</body>
</html>

<script type='text/javascript'>
    $(document).ready(function () {
        var cachedOrgs = null;
        var cachedGroups = null;
        var get_list = null;


        var sessionEndTime = "<?php echo $_SESSION['sessionEndTime']; ?>";
        $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
        $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");

        setInterval(function () {
            var now = parseInt(new Date().getTime() / 1000);
            var difference = sessionEndTime - now;

            if (difference === 300)
            {
                $('#sessionExpiringPopupTime').html("5 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function () {
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function () {
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }

            if (difference === 120)
            {
                $('#sessionExpiringPopupTime').html("2 minutes");
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                setTimeout(function () {
                    $('#sessionExpiringPopup').css("opacity", "0");
                    setTimeout(function () {
                        $('#sessionExpiringPopup').hide();
                    }, 1000);
                }, 4000);
            }

            if ((difference > 0) && (difference <= 60))
            {
                $('#sessionExpiringPopup').show();
                $('#sessionExpiringPopup').css("opacity", "1");
                $('#sessionExpiringPopupTime').html(difference + " seconds");
            }

            if (difference <= 0)
            {
                location.href = "logout.php?sessionExpired=true";
            }
        }, 1000);

        $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());

        $(window).resize(function () {
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            if ($(window).width() < 992)
            {
                $('#usersTable').bootstrapTable('hideColumn', 'admin');
                $('#usersTable').bootstrapTable('hideColumn', 'status');
                $('#usersTable').bootstrapTable('hideColumn', 'name');
                $('#usersTable').bootstrapTable('hideColumn', 'surname');
                $('#usersTable').bootstrapTable('hideColumn', 'organization');
                $('#usersTable').bootstrapTable('hideColumn', 'email');
                $('#usersTable').bootstrapTable('hideColumn', 'reg_data');
            } else
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

        var admin = "<?= $_SESSION['loggedRole'] ?>";
        var existingPoolsJson = null;
        var tableFirstLoad = true;

        buildMainTable(false);

        //Settaggio dei globals per il file usersManagement.js
        setGlobals(admin, existingPoolsJson);

        /*** * ADD NEW USER CONFIRM * ***/
        $("#addNewUserConfirmBtn2").click(function () {
            //Chiamata API di inserimento nuovo utente
            var new_username = $('#username').val();
            var new_password = $('#password').val();
            var new_userType = $('#userType').val();
            var new_email = $('#email').val();
            var new_group = '';
            var csbl = $('#CSBL_CheckC')[0].checked 
            var data_ingestion = $('#Data-IngestionC')[0].checked 
            //var advisor = $('#AdvisorC')[0].checked
            //var subitems = $('#SubitemsC').val()
            //
            //
            //var addUserPoolsTable = $('.check_org:checked').val();
            var addUserPoolsTable = $('.check_org:checked').map(function() {
            return this.value;
            }).get();
           // new_group = $('.check_group:checked').val();
            if ($('#addUserDelOrgsTable').length) {
            var delegated_userstats_orgs = $('.check_delorg:checked').map(function() {
            return this.value;
            }).get();
            }
            new_group = $('.check_group:checked').map(function() {
                    return this.value;
                }).get();
            //
            $.ajax({
                url: "dashboardUserControllers.php",
                data: {action: 'add_user',
                    new_username: new_username,
                    new_password: new_password,
                    new_userType: new_userType,
                    new_email: new_email,
                    org: addUserPoolsTable,
                    group: new_group,
                    csbl: csbl,
                    data_ingestion: data_ingestion,
                    advisor: false,
                    subitems: "",
                    delegated_userstats_orgs: delegated_userstats_orgs,
                },
                type: "POST",
                async: true,
                success: function (data) {
                    var data_result = $.parseJSON(data);
                    var des = data_result["result"];
                    if (des === 'success') {
                        $('#addUserModal').modal('hide');
                        $("#addUserOkModalInnerDiv1").html('<h5>User <b>' + new_username + '</b> successfully registered</h5>');
                        $("#addUserOkModal").modal('show');
                        //
                        setTimeout(function ()
                        {
                            $("#addUserOkModal").modal('hide');
                            $('#addUserModal').modal('hide');
                            buildMainTable(true);
                        }, 2000);
                        //
                    } else if(des==='nousername') {
                        $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + new_username + '</b> couldn\'t be registered, username error, please try again</h5>');
                        $("#addUserKoModal").modal('show');
                    }else if(des==='nopassword') {
                        $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + new_username + '</b> couldn\'t be registered, password error, please try again</h5>');
                        $("#addUserKoModal").modal('show');
                    }else if(des==='noemail') {
                        $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + new_username + '</b> couldn\'t be registered, email error, please try again</h5>');
                        $("#addUserKoModal").modal('show');
                    }else if(des==='noorg') {
                        $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + new_username + '</b> couldn\'t be registered, organization error, please try again</h5>');
                        $("#addUserKoModal").modal('show');
                    }else{
                        $("#addUserKoModalInnerDiv1").html('<h5>User <b>' + new_username + '</b> couldn\'t be registered because of failure while inserting data, please try again</h5>');
                        $("#addUserKoModal").modal('show');
                    }
                    //alert(des);
                }
            });
        });
        /******/


        $('#editUserConfirmBtn').off("click");


        $("#addNewUserCancelBtn").off("click");
        $("#addNewUserCancelBtn").on('click', function () {
            $("#addUserForm").trigger("reset");
            $("#addUserAdminRoleChoiceOuterContainer").hide();
            $("#addUserAdminPoolsChoiceOuterContainer").hide();
            $("#addUserNewPoolNameOuterContainer").hide();
            $("#addUserAddUsersToNewPoolOuterContainer").hide();
            $("#addUserPoolsOuterContainer").show();
        });

        $("#addUserKoBackBtn").off("click");
        $("#addUserKoBackBtn").on('click', function () {
            $("#addUserKoModal").modal('hide');
            $("#addUserModal").modal('show');
        });

        $("#addUserKoConfirmBtn").off("click");
        $("#addUserKoConfirmBtn").on('click', function () {
            $("#addUserKoModal").modal('hide');
            $("#addUserForm").trigger("reset");
        });

        $("#editUserKoBackBtn").off("click");
        $("#editUserKoBackBtn").on('click', function () {
            $("#editUserKoModal").modal('hide');
            $("#editUserModal").modal('show');
        });

        $("#addUserKoConfirmBtn").off("click");
        $("#addUserKoConfirmBtn").on('click', function () {
            $("#editUserKoModal").modal('hide');
            $("#editUserForm").trigger("reset");
        });



        $("#userTypeM").change(function ()
        {
            $(".editUserPoolsTableMakeMemberCheckbox input").off("click");
            $(".editUserPoolsTableMakeAdminCheckbox input").off("click");

            switch ($(this).val())
            {
                case "Observer":
                case "Manager":
                    $(".editUserPoolsTableMakeAdminHeader").hide();
                    $(".editUserPoolsTableMakeAdminCheckbox").hide();
                    $("#editUserPoolsRow").show();
                    break;

                case "AreaManager":
                case "ToolAdmin":
                    $(".editUserPoolsTableMakeMemberCheckbox input").click(function () {
                        $(this).parent().parent().find(".editUserPoolsTableMakeAdminCheckbox input").prop("checked", false);
                    });

                    $(".editUserPoolsTableMakeAdminCheckbox input").click(function () {
                        $(this).parent().parent().find(".editUserPoolsTableMakeMemberCheckbox input").prop("checked", false);
                    });

                    $(".editUserPoolsTableMakeAdminHeader").show();
                    $(".editUserPoolsTableMakeAdminCheckbox").show();
                    $("#editUserPoolsRow").show();
                    break;

                case "RootAdmin":
                    $("#editUserPoolsRow").hide();
                    break;
            }
        });

        function updateAccountTimeout()
        {
            $("#editUserOkModal").modal('hide');
            setTimeout(function () {
                location.reload();
            }, 500);
        }

        function buildMainTable(destroyOld)
        {
            if (destroyOld)
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

            if ($(window).width() < 992)
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
                url: "dashboardUserControllers.php",
                data: {action: "get_list"},
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (mydata) {
                    var lun_json = mydata.length;
                    get_list = mydata;
                    $('#usersTable').bootstrapTable({
                        data: mydata,
                        search: true,
                        pagination: true,
                        sortable: true,
                        pageSize: 10,
                        locale: 'en-US',
                        searchAlign: 'left',
                        uniqueId: "IdUser",
                        striped: false,
                        searchTimeOut: 60,
                        classes: "table table-hover table-no-bordered",
                        columns: [{
                                //
                                field: 'username',
                                title: 'Username',
                                align: "center",
                                sortable: true,
                                halign: "center"
                            }, {
                                field: "admin",
                                title: 'Role',
                                align: "center",
                                sortable: true,
                                halign: "center"
                            }, {
                                field: "organization",
                                title: 'Organization',
                                align: "center",
                                sortable: true,
                                halign: "center"
                            }, {
                                field: "mail",
                                title: 'Email',
                                align: "center",
                                sortable: true,
                                halign: "center"
                            }, {
                                title: "",
                                align: "center",
                                valign: "middle",
                                sortable: false,
                                halign: "center",
                                formatter: function TableActionsEdit(value, row, index) {
                                    //return "<button type='button' class='editDashBtn editUser' data-toggle='modal' data-target='#editNewUserModal' >edit</button>";
                                    return "<button type='button' class='editDashBtn editUser' data-toggle='modal'>edit</button>";
                                    //
                                }
                            }, {
                                title: "",
                                align: "center",
                                valign: "middle",
                                sortable: false,
                                halign: "center",
                                formatter: function TableActionsDel(value, row, index) {
                                    return "<button type='button' class='delDashBtn delete_user'>del</button>";
                                }
                            }],
                        onPostBody: function () {
                            if (tableFirstLoad) {
                                //Caso di primo caricamento della tabella
                                tableFirstLoad = false;
                                var addUserDiv = $('<div class="pull-right"><i id="addUserBtn" class="fa fa-plus-square" style="font-size:36px; color: #ffcc00"></i></div>');

                                $('div.fixed-table-toolbar').append(addUserDiv);
                                addUserDiv.css("margin-top", "10px");
                                addUserDiv.find('i.fa-plus-square').off('hover');
                                addUserDiv.find('i.fa-plus-square').hover(function () {
                                    $(this).css('color', '#e37777');
                                    $(this).css('cursor', 'pointer');
                                },
                                        function () {
                                            $(this).css('color', '#ffcc00');
                                            $(this).css('cursor', 'normal');
                                        });
                                $("#addUserBtn").off("click");
                                $("#addUserBtn").click(showAddUserModal);
                                $('#usersTable thead').css("background", "rgba(0, 162, 211, 1)");
                                $('#usersTable thead').css("color", "white");
                                $('#usersTable thead').css("font-size", "1em");
                                $('#dashboardTotNumberCnt div.pageSingleDataCnt').text(lun_json);
                                //$('#dashboardTotActiveCnt div.pageSingleDataCnt').text(lun_json);
                            }
                        }
                    });

                    //new user
                    $('#addUserBtn').click(function () {
                        $('#addUserGroupsRow').show();
                        if (!cachedOrgs) {
                            $.ajax({
                            url: "dashboardUserControllers.php",
                            data: { action: "list_org" },
                            type: "GET",
                            dataType: "json",
                            success: function (jdata) {
                                cachedOrgs = jdata;                   // store organizations
                                populateOrgsTable(cachedOrgs);
                                populateDelOrgsTable(cachedOrgs);
                            }
                            });
                        } else {
                            populateOrgsTable(cachedOrgs);
                            populateDelOrgsTable(cachedOrgs);
                        }
                        if (!cachedGroups) {
                            $.ajax({
                            url: "dashboardUserControllers.php",
                            data: { action: "get_groups" },
                            type: "GET",
                            dataType: "json",
                            success: function (jdata) {
                                cachedGroups = jdata;                   // store groups
                                populateGroupsTable(cachedGroups);
                            }
                            });
                        } else {
                            populateGroupsTable(cachedGroups);
                        }
                        });

                        // helper to rebuild the table from an array of org names
                        function populateOrgsTable(orgs) {
                        var $tb = $('#addUserPoolsTable tbody').empty();
                        orgs.forEach(function (orgName) {
                            $tb.append(
                            '<tr>'
                            + '<td class="checkboxCell"><input type="checkbox" class="check_org" value="' + orgName + '"/></td>'
                            + '<td class="poolNameCell">' + orgName + '</td>'
                            + '</tr>'
                            );
                        });
                        }
                        function populateGroupsTable(groups) {
                        var $tb = $('#addUserGroupsTable tbody').empty();
                        groups.forEach(function (groupName) {
                            $tb.append(
                            '<tr>'
                            + '<td class="checkboxCell"><input type="checkbox" class="check_group" value="' + groupName + '"/></td>'
                            + '<td class="poolNameCell">' + groupName + '</td>'
                            + '</tr>'
                            );
                        });
                        }
                        function populateDelOrgsTable(orgs) {
                        if (! $('#addUserDelOrgsRow').length) return; // if userstats isn't active
                        var $tb = $('#addUserDelOrgsTable tbody').empty();
                        orgs.forEach(function(o){
                            $tb.append(
                            '<tr>' +
                                '<td class="checkboxCell">' +
                                '<input type="checkbox" class="check_delorg" value="' + o + '"/>' +
                                '</td>' +
                                '<td class="orgNameCell">' + o + '</td>' +
                            '</tr>'
                            );
                        });
                        $('#addUserDelOrgsRow').show();
                        }


                    $('#addNewUserCancelBtn').click(function () {
                        $('#addUserPoolsTable tbody').empty();
                        $('#addUserGroupsTable tbody').empty();
                        $('#addUserDelOrgsTable tbody').empty();
                    });

                    $('#usersTable thead').css("background", "rgba(0, 162, 211, 1)");
                    $('#usersTable thead').css("color", "white");
                    $('#usersTable thead').css("font-size", "1em");

                    $('#usersTable button.delDashBtn').off('hover');
                    $('#usersTable button.delDashBtn').hover(function () {
                        $(this).css('background', '#ffcc00');
                        $(this).parents('tr').find('td').eq(0).css('background', '#ffcc00');
                    },
                            function () {
                                $(this).css('background', '#e37777');
                                $(this).parents('tr').find('td').eq(0).css('background', $(this).parents('td').css('background'));
                            });

                    $('#usersTable button.delDashBtn').off('click');
                    $('#usersTable button.delDashBtn').click(function () {
                        var username = $(this).parents("tr").find("td").eq(0).html();
                        $("#deleteUserModal div.modal-body").html('<div class="modalBodyInnerDiv"><span data-username = "' + username + '">Do you want to confirm deletion of user <b>' + username + '</b>?</span></div>');
                        $("#deleteUserModal").modal('show');
                    });
                }
            });
        }

        //delDashBtn
        $(document).on('click', '.delete_user', function () {
            //$('#deleteUserModal').modal('show');
            var username = $(this).parents("tr").find("td").eq(0).html();
            var role = $(this).parents('tr').children('td:eq(1)').text();
            var org = $(this).parents('tr').children('td:eq(2)').text();
            $('#del_role').val(role);
            $('#del_org').val(org);
            $("#deleteUserModal div.modal-body").html('<div class="modalBodyInnerDiv"><span data-username = "' + username + '">Do you want to confirm deletion of user <b>' + username + '</b>?</span></div>');
            $("#deleteUserModal").modal('show');
        });


        // Edit User: open modal and render multi-org checkboxes
        $(document).on('click', '.editUser', function () {
            var $tr       = $(this).closest('tr'),
                username  = $tr.find('td').eq(0).text(),
                role      = $tr.find('td').eq(1).text(),
                orgText   = $tr.find('td').eq(2).text(),
                mail      = $tr.find('td').eq(3).text(),
                user      = get_list.find(u => u.username === username),
                userOrgs  = orgText.split(',').map(s => s.trim()).filter(Boolean),
                userGroups = user.groups || [];
            //pop fields
            console.log(user);
            $('#editNewUserModalLabel').text('Edit account - ' + username);
            $('#emailM').val(mail);
            $('#old_emailM').val(mail);
            $('#NewuserTypeM').val(role);
            $('#old_roleM').val(role);
            $('#old_organizationM').val(orgText);
            $('#passwordM, #Confirm_passwordM').val('');
            $('#Confirm_passwordM').siblings('.modalFieldLabelCnt').text('Confirm Password');
            $('#confirm_passwordMsg').text('');
            $('#CSBL_Check')[0].checked      = user.csbl;
            $('#Data-Ingestion')[0].checked  = user.data_table;
            $('#editUserPoolsTable tbody').empty();
            $('#editUserGroupsTable tbody').empty();
            //ou & gropus
            function renderOrgs(orgs) {
                var $tb = $('#editUserPoolsTable tbody').empty();
                orgs.forEach(function(o){
                var checked = userOrgs.indexOf(o) !== -1 ? 'checked' : '';
                $tb.append(
                    '<tr>' +
                    '<td class="checkboxCell">' +
                        '<input type="checkbox" class="check_editorg" value="' + o + '" ' + checked + ' />' +
                    '</td>' +
                    '<td class="poolNameCell">' + o + '</td>' +
                    '</tr>'
                );
                });
            }
            function renderGroups(groups) {
                var $tb = $('#editUserGroupsTable tbody').empty();
                groups.forEach(function(g){
                var checked = userGroups.indexOf(g) !== -1 ? 'checked' : '';
                $tb.append(
                    '<tr>' +
                    '<td class="checkboxCell">' +
                        '<input type="checkbox" class="check_editgroup" value="' + g + '" ' + checked + ' />' +
                    '</td>' +
                    '<td class="groupNameCell">' + g + '</td>' +
                    '</tr>'
                );
                });
            }
            function renderDelOrgs(orgs) {
            if (! $('#editUserDelOrgsRowMod').length) return;
            var $tb = $('#editUserDelOrgsTable tbody').empty();
            orgs.forEach(function(o){
                var checked = user.delegated_userstats_orgs && user.delegated_userstats_orgs.indexOf(o) !== -1
                            ? 'checked' : '';
                $tb.append(
                '<tr>' +
                    '<td class="checkboxCell">' +
                    '<input type="checkbox" class="check_editdelorg" value="' + o + '" ' + checked + '/>' +
                    '</td>' +
                    '<td class="orgNameCell">' + o + '</td>' +
                '</tr>'
                );
            });
            $('#editUserDelOrgsRowMod').show();
            }
            // fetch orgs+ groups
            if (!cachedOrgs) {
                $.getJSON('dashboardUserControllers.php', { action: 'list_org' })
                .done(function(orgs){
                cachedOrgs = orgs;
                renderOrgs(orgs);
                renderDelOrgs(orgs);
                });
            } else {
                renderOrgs(cachedOrgs);
                renderDelOrgs(cachedOrgs);
            }
            if (!cachedGroups) {
                $.getJSON('dashboardUserControllers.php', { action: 'get_groups' })
                .done(function(groups){
                cachedGroups = groups;
                renderGroups(groups);
                });
            } else {
                renderGroups(cachedGroups);
            }
            $('#editNewUserModal').modal('show');
            });

        $(document).on('click', '#editUserConfirmBtn', function () {
            var username = $('#editNewUserModalLabel').text()
                            .replace('Edit account - ', '');
            var user      = get_list.find(u => u.username === username);
            var mail     = $('#emailM').val();
            var role     = $('#NewuserTypeM').val();
            var password = $('#passwordM').val();
            var oldMail  = user.mail;
            var oldRole  = user.admin;
            var oldOrgs  = user.organization;

            var orgs       = $('.check_editorg:checked').map(function(){ return this.value; }).get();
            var groups     = $('.check_editgroup:checked').map(function(){ return this.value; }).get();
            var oldGroups  = user.groups || [];
            var csbl = $('#CSBL_Check')[0].checked 
            var data_ingestion = $('#Data-Ingestion')[0].checked 
            //var advisor = $('#Advisor')[0].checked
            //var subitems = $('#Subitems').val()
            if ($('#editUserDelOrgsTable').length) {
            var delegated_userstats_orgs = $('.check_editdelorg:checked').map(function() {
            return this.value;
            }).get();
            }
            var old_delegated_userstats_orgs = user.delegated_userstats_orgs || [];
            console.log($('#editUserDelOrgsTable').length)
            $.ajax({
                url: "dashboardUserControllers.php",
                dataType: "json",
                type: "POST",
                data: {
                    action:     'edit_user',
                    user:       username,
                    mail:       mail,
                    role:       role,
                    org:        orgs,
                    password:   password,
                    old_org:    oldOrgs,
                    old_mail:   oldMail,
                    conf_password:   $('#Confirm_passwordM').val(),
                    old_role:   oldRole,
                    group:      groups,
                    old_groups: oldGroups,
                    csbl: csbl,
                    data_ingestion: data_ingestion,
                    advisor: false,
                    subitems: "",
                    delegated_userstats_orgs: delegated_userstats_orgs,
                    old_delegated_userstats_orgs: old_delegated_userstats_orgs,
                },
                success: function (jdata) {
                    if (jdata.index === 1) {
                        $('#editUserOkModalInnerDiv1').text('User data successfully modified');
                        $('#editUserOkModal').modal('show');
                        $("#editNewUserModal").modal('hide');
                        setTimeout(function () { buildMainTable(true); }, 2000);
                    } else {
                        $('#editUserKoModal').modal('show');
                        $("#editNewUserModal").modal('hide');
                        var msg = '';
                        if (jdata.password === 'Error during password creation')      msg = 'Error during password updating.';
                        else if (jdata.role === 'error modify role')                  msg = 'Error updating Role.';
                        else if (jdata.role === 'error deleting old role')           msg = 'Error deleting old role.';
                        else if (jdata.org === 'error during updating new organization') msg = 'Error updating organization.';
                        else if (jdata.org === 'error during deleting old organization') msg = 'Error removing old organization.';
                        else if (jdata.mail === 'Mail yet used')                     msg = "This mail is already used.";
                        else if (jdata.password === 'Password not correct')          msg = "Password not correct.";
                        $('#editUserKoModalInnerDiv1').text(msg);
                    }
                }
            });
        });
        $(document).on('click', '#editUserCancelBtn', function () {
            $("#NeworganizationM").val('');
            $("#emailM").val('');
            $("#NewuserTypeM").val('');
            $("#editNewUserModalLabel").html("Edit account - ");
            $("#editUserPoolsTable tbody").empty();
            $('#CSBL_Check')[0].checked = false;
            $('#Data-Ingestion')[0].checked = false;
            //$('#Advisor')[0].checked = false;
        });
        $('#editNewUserModal').on('hidden.bs.modal', function () {
        // Mostra un alert quando il modal si chiude
            $("#NeworganizationM").val('');
            $("#emailM").val('');
            $("#NewuserTypeM").val('');
            $("#editNewUserModalLabel").html("Edit account - ");
            $("#editUserPoolsTable tbody").empty();
            $('#CSBL_Check')[0].checked = false;
            $('#Data-Ingestion')[0].checked = false;
            //$('#Advisor')[0].checked = false;
        });

        $(document).on('click', 'delete_user', function () {
            var role = $(this).parents('tr').children('td:eq(1)').text();
            var org = $(this).parents('tr').children('td:eq(2)').text();
            $('#del_org').val(org);
            $('#del_role').val(role);
        });

        $("#Confirm_passwordM").change(function () {
            var pass = $('#passwordM').val();
            var pass_c = $("#Confirm_passwordM").val();
            if ((pass_c !== pass)&&(pass_c !=="")) {
                $("#confirm_passwordMsg").css('color', 'red');
                $("#confirm_passwordMsg").text('Password not correct');
                $("#editUserConfirmBtn").prop("disabled", true);
            } else {
                $("#confirm_passwordMsg").css('color', 'green');
                $("#confirm_passwordMsg").text('Password correct');
                $("#editUserConfirmBtn").prop("disabled", false);
            }
            //
        });
        $("#confirm_password").change(function () {
            var pass = $('#password').val();
            var pass_c = $("#confirm_password").val();
            if (pass_c !== pass) {
                $("#conf_passwordMsg").css('color', 'red');
                $("#conf_passwordMsg").text('Password not correct');
                $("#addNewUserConfirmBtn2").prop("disabled", true);
            } else {
                $("#conf_passwordMsg").css('color', 'green');
                $("#conf_passwordMsg").text('Password correct');
                $("#addNewUserConfirmBtn2").prop("disabled", false);
            }
            //
        });
        //addNewUserCancelBtn
        $(document).on('click','#addNewUserCancelBtn', function () {
            $('#addUserGroupsRow').css('display','none');
        });

        
        $(document).on('click','#editUserKoConfirmBtn', function () {
            $('#editUserKoModal').modal('hide');
        });
        
        //editUserKoBackBtn
        $(document).on('click','#editUserKoBackBtn', function () {
            $('#editUserKoModal').modal('hide');
            $("#editNewUserModal").modal('show');
        });
        $(document).on('click', '#deleteUserConfirmBtn', function () {
            var username = $("#deleteUserModal span").attr("data-username");

            $("#deleteUserModal div.modal-body").html("");
            $("#deleteUserCancelBtn").hide();
            $("#deleteUserConfirmBtn").hide();
            $("#deleteUserModal div.modal-body").append('<div id="deleteUserModalInnerDiv1" class="modalBodyInnerDiv"><h5>User deletion in progress, please wait</h5></div>');
            $("#deleteUserModal div.modal-body").append('<div id="deleteUserModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px"></i></div>');
            //
            var role = $('#del_role').val();
            var org = $('#del_org').val();
            //Chiamata API di cancellazione utente
            $.ajax({
                url: "dashboardUserControllers.php",
                data: {
                    action: 'delete_user',
                    username: username,
                    org: org,
                    role: role
                },
                type: "POST",
                async: false,
                success: function (data)
                {
                    var jdata = $.parseJSON(data);
                    console.log(jdata);
                    if(jdata['dash'] === 'failure'){
                        $("#deleteUserModal div.modal-body").html("");
                        $("#deleteUserModalInnerDiv1").html('User &nbsp; <b>' + username + '</b> &nbsp; Group deletion failed, please try again');
                        $("#deleteUserModalInnerDiv2").html('<i class="fa fa-frown-o" style="font-size:42px"></i>');
                        }
                    if(jdata['org'] === 'failure'){
                        $("#deleteUserModal div.modal-body").html("");
                        $("#deleteUserModalInnerDiv1").html('User &nbsp; <b>' + username + '</b> &nbsp; Organization deletion failed, please try again');
                        $("#deleteUserModalInnerDiv2").html('<i class="fa fa-frown-o" style="font-size:42px"></i>');
                        }
                    if(jdata['role'] === 'failure'){
                        $("#deleteUserModal div.modal-body").html("");
                        $("#deleteUserModalInnerDiv1").html('User &nbsp; <b>' + username + '</b> &nbsp; Role failed, please try again');
                        $("#deleteUserModalInnerDiv2").html('<i class="fa fa-frown-o" style="font-size:42px"></i>');
                        }
                        
                    if (jdata['result'] === 'error')
                    {
                        $("#deleteUserModal div.modal-body").html("");
                        $("#deleteUserModalInnerDiv1").html('User &nbsp; <b>' + username + '</b> &nbsp; deletion failed, please try again');
                        $("#deleteUserModalInnerDiv2").html('<i class="fa fa-frown-o" style="font-size:42px"></i>');
                    } else if (jdata['result'] === 'success')
                    {
                        $("#deleteUserModalInnerDiv1").html('User &nbsp; <b>' + username + '</b> &nbsp;deleted successfully');
                        $("#deleteUserModalInnerDiv2").html('<i class="fa fa-check" style="font-size:42px"></i>');
                        setTimeout(function ()
                        {
                            buildMainTable(true);
                            $("#deleteUserModal").modal('hide');
                            $("#deleteUserCancelBtn").show();
                            $("#deleteUserConfirmBtn").show();
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
    });

</script>

<?php } else {
    include('../s4c-legacy-management/users.php');
}
?> 