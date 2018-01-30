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

    include('process-form.php');
    include('../config.php');
    session_start();
?>

<html lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard management system</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Custom Core JavaScript -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>
    
    <!-- Bootstrap table -->
    <link rel="stylesheet" href="../boostrapTable/dist/bootstrap-table.css">
    <script src="../boostrapTable/dist/bootstrap-table.js"></script>
    <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
    <script src="../boostrapTable/dist/locale/bootstrap-table-en-US.js"></script>
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    
    <!-- Scripts file -->
    <script src="../js/usersManagement.js"></script>
    
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
</head>

<body>
    <?php
        if(!isset($_SESSION['loggedRole']))
        {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "unauthorizedUser.php";';
            echo '</script>';
        }
        else if($_SESSION['loggedRole'] != "ToolAdmin")
        {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "unauthorizedUser.php";';
            echo '</script>';
        }
    ?>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Dashboard Management System</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <?php
                    if(isset($_SESSION['loggedUsername']))
                    {
                        echo '<li><a href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>' . $_SESSION["loggedUsername"] . '</a></li>';
                        echo '<li><a href="logout.php">Logout</a></li>';
                    }
                ?>
            </ul>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul id="navbarLinks" class="nav navbar-nav side-nav">
                    <li><a class="internalLink" href="../management/dashboard_mng.php">Dashboards management</a></li>
                    <?php
                       if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
                        {     
                           if($_SESSION['loggedType'] == "local")
                           {
                              echo '<li><a class="internalLink" href="../management/accountManagement.php" id="accountManagementLink">Account management</a></li>';
                           }
                           
                           if($_SESSION['loggedRole'] == "ToolAdmin")
                           {
                                echo '<li><a class="internalLink" href="../management/metrics_mng.php" id="link_metric_mng">Metrics management</a></li>';
                                echo '<li><a class="internalLink" href="../management/widgets_mng.php" id="link_widgets_mng">Widgets management</a></li>';
                                echo '<li><a class="internalLink" href="../management/dataSources_mng.php" id="link_sources_mng">Data sources management</a></li>';
                                echo '<li class="active"><a class="internalLink" href="../management/usersManagement.php" id="link_user_register">Users management</a></li>';
                           }
                           
                           if(($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager"))
                           {
                              echo '<li><a class="internalLink" href="../management/poolsManagement.php?showManagementTab=false&selectedPoolId=-1" id="link_pools_management">Users pools management</a></li>';
                           }
                        }
                    ?>
                    <li>
                        <a href="<?php echo $notificatorLink?>" target="blank"> Notificator</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row" style="margin-top: 50px">
                    <div class="col-xs-12 centerWithFlex mainPageTitleContainer">
                        Users
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12">
                        <table id="usersTable"></table>
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
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addUserModalLabel">Add new user</h5>
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
            <div id="addUserModalBody" class="modal-body">
                <form id="addUserForm" name="addUserForm" role="form" method="post" action="process-form.php" data-toggle="validator">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="addUserFormSubfieldContainer">Username</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" id="username" name="username" pattern="[A-Za-z0-9_]+" title="Numbers, letters and _ are admitted" required>
                            </div>
                            <div id="usernameMsg" class="addUserFormSubfieldContainer">&nbsp;</div>    
                        </div>
                       <div class="col-md-4">
                            <div class="addUserFormSubfieldContainer">User type</div>
                            <div class="addUserFormSubfieldContainer">
                                <select id="userType" name="userType">
                                    <option value="Observer">Observer</option>
                                    <option value="Manager">Manager</option>
                                    <option value="AreaManager">Area manager</option>
                                    <option value="ToolAdmin">Tool admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="addUserFormSubfieldContainer">E-Mail</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div id="emailMsg" class="addUserFormSubfieldContainer">&nbsp;</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="addUserFormSubfieldContainer">First name</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" id="firstName" name="firstName">
                            </div>
                            <div id="firstNameMsg" class="addUserFormSubfieldContainer">&nbsp;</div>  
                        </div>
                        <div class="col-md-4">
                            <div class="addUserFormSubfieldContainer">Last name</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" id="lastName" name="lastName">
                            </div>
                            <div id="lastNameMsg" class="addUserFormSubfieldContainer">&nbsp;</div>
                        </div>
                        <div class="col-md-4">
                            <div class="addUserFormSubfieldContainer">Organization</div>
                            <div class="addUserFormSubfieldContainer">
                                <input type="text" id="organization" name="organization">
                            </div>
                            <div id="organizationMsg" class="addUserFormSubfieldContainer">&nbsp;</div>
                        </div>
                    </div> 
                    <div class="row" id="addUserPoolsRow">
                        <div class="col-md-8 col-md-offset-2" id="addUserPoolsOuterContainer">
                            <div class="addUserFormSubfieldContainer">Users pools</div>
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
                                                    echo '<table id="addUserPoolsTable">';
                                                    echo '<tr><th>Make member</th><th class="addUserPoolsTableMakeAdminHeader">Make admin</th><th>Pool name</th></tr>';
                                                    
                                                    while ($row = $result->fetch_assoc()) 
                                                    {
                                                        echo '<tr><td class="checkboxCell addUserPoolsTableMakeMemberCheckbox"><input data-poolId="' . $row["poolId"] . '" type="checkbox" /></td><td class="checkboxCell addUserPoolsTableMakeAdminCheckbox"><input data-poolId="' . $row["poolId"] . '" type="checkbox" /></td><td class="poolNameCell">' . $row["poolName"] . '</td>';
                                                    }
                                                    
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
            </div>
            <div id="addUserModalFooter" class="modal-footer">
              <button type="button" id="addNewUserCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id="addNewUserConfirmBtn" class="btn btn-primary internalLink" disabled="true">Confirm</button>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di notifica inserimento utente avvenuto con successo -->
    <div class="modal fade" id="addUserOkModal" tabindex="-1" role="dialog" aria-labelledby="addUserOkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addUserOkModalLabel">Add new user</h5>
            </div>
            <div class="modal-body">
                <div id="addUserOkModalInnerDiv1" class="modalBodyInnerDiv"></div>
                <div id="addUserOkModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:36px"></i></div>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di notifica inserimento utente fallito -->
    <div class="modal fade" id="addUserKoModal" tabindex="-1" role="dialog" aria-labelledby="addUserKoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addUserKoModalLabel">Add new user</h5>
            </div>
            <div class="modal-body">
                <div id="addUserKoModalInnerDiv1" class="modalBodyInnerDiv"></div>
                <div id="addUserKoModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:36px"></i></div>
            </div>
            <div class="modal-footer">
              <button type="button" id="addUserKoBackBtn" class="btn btn-primary">Go back to new user form</button>
              <button type="button" id="addUserKoConfirmBtn" class="btn btn-primary">Go back to users page</button>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di modifica account utente -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editUserModalLabel"></h5>
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
            <div class="modal-body" id="editUserModalBody">
                <form id="editUserForm" name="editUserForm" role="form" method="post" action="process-form.php" data-toggle="validator">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="editUserFormSubfieldContainer">First name</div>
                            <div class="editUserFormSubfieldContainer">
                                <input type="text" id="firstNameM" name="firstNameM">
                            </div>
                            <div id="firstNameMsgM" class="editUserFormSubfieldContainer">&nbsp;</div>  
                        </div>
                        <div class="col-md-4">
                            <div class="editUserFormSubfieldContainer">Last name</div>
                            <div class="editUserFormSubfieldContainer">
                                <input type="text" id="lastNameM" name="lastName">
                            </div>
                            <div id="lastNameMsgM" class="editUserFormSubfieldContainer">&nbsp;</div>
                        </div>
                        <div class="col-md-4">
                            <div class="editUserFormSubfieldContainer">Organization</div>
                            <div class="editUserFormSubfieldContainer">
                                <input type="text" id="organizationM" name="organizationM">
                            </div>
                            <div id="organizationMsgM" class="editUserFormSubfieldContainer">&nbsp;</div>
                        </div>
                    </div>    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="editUserFormSubfieldContainer">User type</div>
                            <div class="editUserFormSubfieldContainer">
                                <select id="userTypeM" name="userTypeM">
                                    <option value="Observer">Observer</option>
                                    <option value="Manager">Manager</option>
                                    <option value="AreaManager">Area manager</option>
                                    <option value="ToolAdmin">Tool admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="editUserFormSubfieldContainer">Status</div>
                            <div class="editUserFormSubfieldContainer">
                                <select id="userStatusM" name="userStatusM">
                                    <option value="1">Active</option>
                                    <option value="0">Not active</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="editUserFormSubfieldContainer">E-Mail</div>
                            <div class="editUserFormSubfieldContainer">
                                <input type="email" id="emailM" name="emailM" required>
                            </div>
                            <div id="emailMsgM" class="editUserFormSubfieldContainer">&nbsp;</div>
                        </div>
                    </div>
                    <div class="row" id="editUserPoolsRow">
                        <div class="col-md-8 col-md-offset-2" id="editUserPoolsOuterContainer">
                            <div class="editUserFormSubfieldContainer">Users pools</div>
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
              <button type="button" id="editUserCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id="editUserConfirmBtn" class="btn btn-primary internalLink" disabled="true">Confirm</button>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di notifica edit account utente avvenuto con successo -->
    <div class="modal fade" id="editUserOkModal" tabindex="-1" role="dialog" aria-labelledby="editUserOkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editUserOkModalLabel">Edit account</h5>
            </div>
            <div class="modal-body">
                <div id="editUserOkModalInnerDiv1" class="modalBodyInnerDiv"></div>
                <div id="editUserOkModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:36px"></i></div>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di notifica edit account utente fallito -->
    <div class="modal fade" id="editUserKoModal" tabindex="-1" role="dialog" aria-labelledby="editUserKoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editUserKoModalLabel">Edit account</h5>
            </div>
            <div class="modal-body">
                <div id="editUserKoModalInnerDiv1" class="modalBodyInnerDiv"></div>
                <div id="edituserKoModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:36px"></i></div>
            </div>
            <div class="modal-footer">
              <button type="button" id="editUserKoBackBtn" class="btn btn-primary">Go back to edit account form</button>
              <button type="button" id="editUserKoConfirmBtn" class="btn btn-primary" data-dismiss='modal'>Go back to users page</button>
            </div>
          </div>
        </div>
    </div>
    
<script type='text/javascript'>
    $(document).ready(function () 
    {
        var admin = "<?= $_SESSION['loggedRole'] ?>";
        var existingPoolsJson = null;
        var internalDest = false;
        var tableFirstLoad = true;
        
        buildMainTable(false);
        
        //Settaggio dei globals per il file usersManagement.js
        setGlobals(admin, existingPoolsJson);
        
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

             //Chiamata API di inserimento nuovo utente
             $.ajax({
                 url: "editUser.php",
                 data:{operation: "updateAccount", accountJson: JSON.stringify(accountJson)},
                 type: "POST",
                 async: true,
                 success: function (data) 
                 {
                    console.log("Ok");
                    console.log(JSON.stringify(data));
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
        
        $("#addNewUserCancelBtn").on('click', function(){
            $("#addUserForm").trigger("reset");
            $("#addUserAdminRoleChoiceOuterContainer").hide();
            $("#addUserAdminPoolsChoiceOuterContainer").hide();
            $("#addUserNewPoolNameOuterContainer").hide();
            $("#addUserAddUsersToNewPoolOuterContainer").hide();
            $("#addUserPoolsOuterContainer").show();
        });
        
        $("#addUserKoBackBtn").on('click', function(){
            $("#addUserKoModal").modal('hide');
            $("#addUserModal").modal('show');
        });
        
        $("#addUserKoConfirmBtn").on('click', function(){
            $("#addUserKoModal").modal('hide');
            $("#addUserForm").trigger("reset");
        });
        
        $("#editUserKoBackBtn").on('click', function(){
            $("#editUserKoModal").modal('hide');
            $("#editUserModal").modal('show');
        });
        
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
                                }
                            }, {
                                field: 'name',
                                title: 'First name',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
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
                                }
                            },
                            {
                                field: 'surname',
                                title: 'Last name',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
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
                                }
                            },
                            {
                                field: 'organization',
                                title: 'Organization',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
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
                                }
                            },
                            {
                                field: 'admin',
                                title: 'Account',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
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
                                }
                            },
                            {
                                field: 'email',
                                title: 'E-Mail',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                formatter: function(value, row, index)
                                {
                                    return value;
                                }
                            },
                            {
                                field: 'reg_data',
                                title: 'Registration date',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                formatter: function(value, row, index)
                                {
                                    return value;
                                }
                            },
                            {
                                field: 'status',
                                title: 'Status',
                                sortable: true,
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                formatter: function(value, row, index)
                                {
                                    if(value === 0)
                                    {
                                        return "Not active";
                                    }
                                    else 
                                    {
                                        return "Active";
                                    }
                                }
                            },
                            {
                                title: "Edit",
                                align: "center",
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                formatter: function(value, row, index)
                                {
                                    return '<span class="glyphicon glyphicon-cog"></span>'; 
                                }
                            },
                            {
                                title: "Delete",
                                align: "center",
                                valign: "middle",
                                align: "center",
                                halign: "center",
                                formatter: function(value, row, index)
                                {
                                    return '<span class="glyphicon glyphicon-remove"></span>'; 
                                }
                            }],
                            data: data,
                            search: true,
                            pagination: true,
                            pageSize: 10,
                            locale: 'en-US',
                            searchAlign: 'left',
                            uniqueId: "IdUser",
                            striped: true,
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
                                        $(this).css('color', 'red');
                                        $(this).css('cursor', 'pointer');
                                    }, 
                                    function(){
                                        $(this).css('color', '#ffcc00');
                                        $(this).css('cursor', 'normal');
                                    });
                                    $("#addUserBtn").click(showAddUserModal);
                                }
                                else
                                {
                                    //Casi di cambio pagina
                                }

                                //Istruzioni da eseguire comunque
                                $('#usersTable span.glyphicon-cog').css('color', '#337ab7');
                                $('#usersTable span.glyphicon-cog').css('font-size', '20px');

                                $('#usersTable span.glyphicon-cog').off('hover');
                                $('#usersTable span.glyphicon-cog').hover(function(){
                                    $(this).css('color', '#ffcc00');
                                    $(this).css('cursor', 'pointer');
                                }, 
                                function(){
                                    $(this).css('color', '#337ab7');
                                    $(this).css('cursor', 'normal');
                                });

                                $('#usersTable span.glyphicon-cog').off('click');
                                $('#usersTable span.glyphicon-cog').click(function(){
                                    $("#editUserModalUpdating").hide();
                                    $("#editUserModalBody").show();
                                    $("#editUserModalFooter").show();
                                    $("#editUserModal").modal('show');
                                    $("#editUserModalLabel").html("Edit account - " + $(this).parent().parent().find("td").eq(0).html());
                                    $("#usernameM").val($(this).parent().parent().find("td").eq(0).html());
                                    $("#firstNameM").val($(this).parent().parent().find("td").eq(1).html());
                                    $("#lastNameM").val($(this).parent().parent().find("td").eq(2).html());
                                    $("#organizationM").val($(this).parent().parent().find("td").eq(3).html());
                                    if($(this).parent().parent().find("td").eq(7).html() === "Active")
                                    {
                                       $("#userStatusM").val(1);
                                    }
                                    else
                                    {
                                       $("#userStatusM").val(0);
                                    }
                                    $("#emailM").val($(this).parent().parent().find("td").eq(5).html());

                                    var role = $(this).parent().parent().find("td").eq(4).html();

                                    $.ajax({
                                        url: "editUser.php",
                                        data: {operation: "getUserPoolMemberships", username: $(this).parent().parent().find("td").eq(0).html()},
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
                                                $("#userTypeM").val("Observer");
                                                break;

                                             case "Manager":   
                                                $("#editUserPoolsRow").show();
                                                $(".editUserPoolsTableMakeAdminHeader").hide();
                                                $(".editUserPoolsTableMakeAdminCheckbox").hide();
                                                $("#userTypeM").val("Manager");
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
                                                $("#userTypeM").val("AreaManager");
                                                break;

                                             case "Tool admin":
                                                $("#editUserPoolsRow").hide();
                                                $("#userTypeM").val("ToolAdmin");
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
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    /*$('#dsIdToEdit').val($(this).parents('tr').attr("data-uniqueid"));
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
                                    });*/
                                });

                                $('#usersTable span.glyphicon-remove').css('color', 'red');
                                $('#usersTable span.glyphicon-remove').css('font-size', '20px');

                                $('#usersTable span.glyphicon-remove').off('hover');
                                $('#usersTable span.glyphicon-remove').hover(function(){
                                    $(this).css('color', '#ffcc00');
                                    $(this).css('cursor', 'pointer');
                                }, 
                                function(){
                                    $(this).css('color', 'red');
                                    $(this).css('cursor', 'normal');
                                });

                                $('#usersTable span.glyphicon-remove').off('click');
                                $('#usersTable span.glyphicon-remove').click(function(){
                                    var username = $(this).parents("tr").find("td").eq(0).html();
                                    $("#deleteUserModal div.modal-body").html('<div class="modalBodyInnerDiv"><span data-username = "' + username + '">Do you want to confirm deletion of user <b>' + username + '</b>?</span></div>');
                                    $("#deleteUserModal").modal('show');
                                });
                            }
                        });
                    }
            });
        }
        
        /*$("#usersTable i.fa-cog").on('click', function()
        {
            $("#editUserModalUpdating").hide();
            $("#editUserModalBody").show();
            $("#editUserModalFooter").show();
            $("#editUserModal").modal('show');
            $("#editUserModalLabel").html("Edit account - " + $(this).parent().parent().find("td").eq(0).html());
            $("#usernameM").val($(this).parent().parent().find("td").eq(0).html());
            $("#firstNameM").val($(this).parent().parent().find("td").eq(1).html());
            $("#lastNameM").val($(this).parent().parent().find("td").eq(2).html());
            $("#organizationM").val($(this).parent().parent().find("td").eq(3).html());
            if($(this).parent().parent().find("td").eq(7).html() === "Active")
            {
               $("#userStatusM").val(1);
            }
            else
            {
               $("#userStatusM").val(0);
            }
            $("#emailM").val($(this).parent().parent().find("td").eq(5).html());
            
            var role = $(this).parent().parent().find("td").eq(4).html();
            
            $.ajax({
                url: "editUser.php",
                data: {operation: "getUserPoolMemberships", username: $(this).parent().parent().find("td").eq(0).html()},
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
                        $("#userTypeM").val("Observer");
                        break;
                        
                     case "Manager":   
                        $("#editUserPoolsRow").show();
                        $(".editUserPoolsTableMakeAdminHeader").hide();
                        $(".editUserPoolsTableMakeAdminCheckbox").hide();
                        $("#userTypeM").val("Manager");
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
                        $("#userTypeM").val("AreaManager");
                        break;

                     case "Tool admin":
                        $("#editUserPoolsRow").hide();
                        $("#userTypeM").val("ToolAdmin");
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
        
        
        */

    });//Fine document ready
</script>
</body>
</html>