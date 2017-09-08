<?php

/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery -->
    <!--<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>-->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <!--<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>-->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Custom Core JavaScript -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>
    
    <!-- Bootstrap editable tables -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    
    <!-- Scripts file -->
    <script src="../js/usersManagement.js"></script>
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
                <a class="navbar-brand" href="index.html">Dashboard management system</a>
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
                    <li><a href="../management/dashboard_mng.php">Dashboards management</a></li>
                    <?php
                       if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
                        {     
                           if($_SESSION['loggedType'] == "local")
                           {
                              echo '<li><a href="../management/accountManagement.php" id="accountManagementLink">Account management</a></li>';
                           }
                           
                           if($_SESSION['loggedRole'] == "ToolAdmin")
                           {
                                echo '<li><a href="../management/metrics_mng.php" id="link_metric_mng">Metrics management</a></li>';
                                echo '<li><a href="../management/widgets_mng.php" id="link_widgets_mng">Widgets management</a></li>';
                                echo '<li><a href="../management/dataSources_mng.php" id="link_sources_mng">Data sources management</a></li>';
                                echo '<li><a href="../management/usersManagement.php" id="link_user_register">Users management</a></li>';
                                
                           }
                           
                           if(($_SESSION['loggedRole'] == "ToolAdmin") || ($_SESSION['loggedRole'] == "AreaManager"))
                           {
                              echo '<li><a href="../management/poolsManagement.php?showManagementTab=false&selectedPoolId=-1" id="link_pools_management">Users pools management</a></li>';
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
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            <br/>Users management
                        </h1>
                        <?php
                            if(isset($_SESSION['loggedRole']))
                            {
                                if($_SESSION['loggedRole'] == "ToolAdmin")
                                {       
                                    echo '<nav class="navbar navbar-default floatLeft">' . 
                                         '<div class="collapse navbar-collapse">' .
                                         '<ul class="nav navbar-nav">' .
                                         '<li>' .
                                         '<a id="addNewUserBtn" href="#" data-toggle="modal">' . 
                                         '<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>' .
                                         'Add new user' .
                                         '<span class="sr-only">(current)</span></a></li>' .  
                                         '</ul>' .
                                         '</div>' .
                                         '</nav>'; 
                                }
                            }
                        ?>    
                           
                    </div>
                </div>
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Registered users</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="usersTable2" class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>First name</th>
                                            <th>Last name</th>
                                            <th>Organization</th>
                                            <th>User type</th>
                                            <th>E-Mail</th>
                                            <th>Registration date</th>
                                            <th>Status</th>
                                            <th>Edit account</th>
                                            <th>Delete account</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if(isset($_SESSION['loggedRole']))
                                            {
                                                $link = mysqli_connect($host, $username, $password);
                                                mysqli_select_db($link, $dbname);
                                                if($_SESSION['loggedRole'] == "ToolAdmin")
                                                {
                                                    //Il super admin amministra tutti gli utenti e vede anche le password
                                                    $usersQuery = "SELECT * FROM Dashboard.Users";
                                                    $result = mysqli_query($link, $usersQuery) or die(mysqli_error($link));

                                                    if($result->num_rows > 0) 
                                                    {
                                                        while ($row = $result->fetch_assoc()) 
                                                        {
                                                            $user = $row["username"];//Lascia i nomi abbreviati, sennò va in conflitto con le variabili usate in config.php
                                                            $name = $row["name"];
                                                            $surname = $row["surname"];
                                                            $organization = $row["organization"];
                                                            $userType = $row["admin"];
                                                            $email = $row["email"];
                                                            $regDate = $row["reg_data"];
                                                            
                                                            if($row["status"] == 0)
                                                            {
                                                                $status = "Not active";
                                                            }
                                                            else if($row["status"] == 1)
                                                            {
                                                                $status = "Active";
                                                            }
                                                            
                                                            switch($userType)
                                                            {
                                                               case "ToolAdmin":
                                                                  $userType = "Tool admin";
                                                                  break;
                                                               
                                                               case "AreaManager":
                                                                  $userType = "Area manager";
                                                                  break;
                                                               
                                                               case "Manager":
                                                                  $userType = "Manager";
                                                                  break;
                                                            }

                                                            echo '<tr>';
                                                                echo '<td>' . $user . '</td>';
                                                                echo '<td>' . $name . '</td>';
                                                                echo '<td>' . $surname . '</td>';
                                                                echo '<td>' . $organization . '</td>';
                                                                echo '<td>' . $userType . '</td>';
                                                                echo '<td>' . $email . '</td>';
                                                                echo '<td>' . $regDate . '</td>';
                                                                echo '<td>' . $status . '</td>';
                                                                echo '<td><i class="fa fa-cog" style="font-size:24px;color:#337ab7"></i></td>';
                                                                echo '<td><i class="fa fa-remove" style="font-size:24px;color:red"></i></td>';
                                                            echo '</tr>';
                                                        }
                                                    }
                                                }
                                                mysqli_close($link);
                                            }
                                        ?>
                                    </tbody>
                                </table>
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
    
    <!-- Modale di conferma cambio pagina senza aver salvato i cambiamenti ai dati degli utenti -->
    <div class="modal fade" id="pageChangeModal" tabindex="-1" role="dialog" aria-labelledby="pageChangeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="pageChangeModalLabel">Alert</h5>
            </div>
            <div class="modal-body">
                <div class="modalBodyInnerDiv">There are unsaved changes to users data: if you leave the page changes will be discarded, do you want to confirm?</div>
            </div>
            <div class="modal-footer">
              <button type="button" id="pageChangeCancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id="pageChangeConfirmBtn" class="btn btn-primary">Confirm</button>
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
                       <i class="fa fa-spinner fa-spin" style="font-size:72px"></i>
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
              <button type="button" id="addNewUserConfirmBtn" class="btn btn-primary" disabled="true">Confirm</button>
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
                <div id="addUserOkModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>
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
                <div id="addUserKoModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
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
                       <i class="fa fa-spinner fa-spin" style="font-size:72px"></i>
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
                       <i class="fa fa-spinner fa-spin" style="font-size:72px"></i>
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
              <button type="button" id="editUserConfirmBtn" class="btn btn-primary" disabled="true">Confirm</button>
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
                <div id="editUserOkModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>
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
                <div id="edituserKoModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
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
        
        //Settaggio dei globals per il file usersManagement.js
        setGlobals(admin, existingPoolsJson);
        
        $("#usersTable2 i.fa-remove").on('click', function(){
            var username = $(this).parents("tr").find("td").eq(0).html();
            $("#deleteUserModal div.modal-body").html('<div class="modalBodyInnerDiv"><span data-username = "' + username + '">Do you want to confirm deletion of user <b>' + username + '</b>?</span></div>');
            $("#deleteUserModal").modal('show');
        });
        
        $("#usersTable2 i.fa-cog").on('click', function()
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
        
        $("#deleteUserConfirmBtn").on('click', deleteUser);
        $("#pageChangeConfirmBtn").on('click', confirmPageChange);
        $("#addNewUserBtn").click(showAddUserModal);
        $("#addNewUserConfirmBtn").on('click', addNewUser);
        $("#editUserConfirmBtn").on('click', updateAccount);
        
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
    });//Fine document ready
</script>
</body>
</html>