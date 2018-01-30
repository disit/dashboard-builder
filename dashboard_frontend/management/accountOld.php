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

    <title>Dashboard Management System</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Custom Core JavaScript -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>
    
    <!-- Bootstrap editable tables -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    
    <!-- Scripts file -->
    <script src="../js/accountManagement.js"></script>
    
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
                    <li><a href="../management/dashboard_mng.php" class="internalLink">Dashboards management</a></li>
                    <?php
                        if(isset($_SESSION['loggedRole'])&&isset($_SESSION['loggedType']))
                        {     
                           if($_SESSION['loggedType'] == "local")
                           {
                              echo '<li class="active"><a class="internalLink" href="../management/accountManagement.php" id="accountManagementLink">Account management</a></li>';
                           }
                           
                           if($_SESSION['loggedRole'] == "ToolAdmin")
                           {
                                echo '<li><a class="internalLink" href="../management/metrics_mng.php" id="link_metric_mng">Metrics management</a></li>';
                                echo '<li><a class="internalLink" href="../management/widgets_mng.php" id="link_widgets_mng">Widgets management</a></li>';
                                echo '<li><a class="internalLink" href="../management/dataSources_mng.php" id="link_sources_mng">Data sources management</a></li>';
                                echo '<li><a class="internalLink" href="../management/usersManagement.php" id="link_user_register">Users management</a></li>';
                                
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
                        Account
                    </div>
                </div>
                           <?php
                               if(isset($_SESSION['loggedRole']))
                               {
                                 $link = mysqli_connect($host, $username, $password) or die();
                                 mysqli_select_db($link, $dbname);
                                 $username = $_SESSION['loggedUsername'];
                                 $query = "SELECT * FROM Dashboard.Users WHERE username = '$username'";
                                 $result = mysqli_query($link, $query) or die(mysqli_error($link));

                                 if($result)
                                 {
                                    if($result->num_rows > 0) 
                                    {
                                       $row = $result->fetch_assoc();
                                       $password = $row["password"];
                                       $firstName = $row["name"];
                                       $lastName = $row["surname"];
                                       $organization = $row["organization"];
                                       $email = $row["email"];
                                    }
                                 }
                               }
                           ?>
                           
                           <div class="row">
                              <div class="col-md-4">
                                 <div class="accountEditSubfieldContainer">First name</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="text" id="accountFirstName" name="accountFirstName" value="<?php echo $firstName ?>" data-originalvalue="<?php echo $firstName ?>">
                                 </div>
                                 <div id="accountFirstNameMsg" class="accountEditSubfieldContainer"></div>    
                             </div>
                             <div class="col-md-4">
                                 <div class="accountEditSubfieldContainer">Last name</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="text" id="accountLastName" name="accountLastName" value="<?php echo $lastName ?>" data-originalvalue="<?php echo $lastName ?>">
                                 </div>
                                 <div id="accountLastNameMsg" class="accountEditSubfieldContainer"></div>    
                             </div> 
                             <div class="col-md-4">
                                 <div class="accountEditSubfieldContainer">Organization</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="text" id="accountOrganization" name="accountOrganization" value="<?php echo $organization ?>" data-originalvalue="<?php echo $organization ?>">
                                 </div>
                                 <div id="accountOrganizationMsg" class="accountEditSubfieldContainer"></div>    
                             </div>  
                           </div>
                           <div class="row">
                              <div class="col-md-4">
                                 <div class="accountEditSubfieldContainer">E-Mail</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="email" id="accountEmail" name="accountEmail" value="<?php echo $email ?>" data-originalvalue="<?php echo $email ?>">
                                 </div>
                                 <div id="accountEmailMsg" class="accountEditSubfieldContainer"></div>    
                             </div>
                             <div class="col-md-4">
                                 <div class="accountEditSubfieldContainer">Password</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="password" id="accountPassword" name="accountPassword">
                                 </div>
                                 <div id="accountPasswordMsg" class="accountEditSubfieldContainer"></div>    
                             </div> 
                             <div class="col-md-4">
                                 <div class="accountEditSubfieldContainer">Password confirmation</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="password" id="accountPasswordConfirmation" name="accountPasswordConfirmation">
                                 </div>
                                 <div id="accountPasswordConfirmationMsg" class="accountEditSubfieldContainer"></div>    
                             </div>  
                           </div> 
                           <div class="row">
                              <button type="button" id="editAccountConfirmBtn" class="btn btn-primary pull-right internalLink" disabled="true">Apply changes</button>
                              <button type="button" id="editAccountCancelBtn" class="btn btn-secondary pull-right" data-dismiss="modal">Undo changes</button>
                           </div>
            </div>
        </div>
    </div>
   
   <!-- Modale di notifica aggiornamento account avvenuto con successo -->
    <div class="modal fade" id="editAccountOkModal" tabindex="-1" role="dialog" aria-labelledby="editAccountOkModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editAccountOkModalLabel">Account update</h5>
            </div>
            <div class="modal-body">
                <div id="editAccountOkModalInnerDiv1" class="modalBodyInnerDiv">Account successfully updated</div>
                <div id="editAccountOkModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-check" style="font-size:42px"></i></div>
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di notifica aggiornamento account fallito -->
    <div class="modal fade" id="editAccountKoModal" tabindex="-1" role="dialog" aria-labelledby="editAccountKoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editAccountKoModalLabel">Account update</h5>
            </div>
            <div class="modal-body">
                <div id="addUserKoModalInnerDiv1" class="modalBodyInnerDiv">Account update failed, please try again</div>
                <div id="addUserKoModalInnerDiv2" class="modalBodyInnerDiv"><i class="fa fa-frown-o" style="font-size:42px"></i></div>
            </div>
          </div>
        </div>
    </div>
   
<script type='text/javascript'>
    $(document).ready(function () 
    {
       editAccountPageSetup();
       
       $("#editAccountCancelBtn").click(function(){
          $("#accountFirstName").val($("#accountFirstName").attr("data-originalvalue"));
          $("#accountLastName").val($("#accountLastName").attr("data-originalvalue"));
          $("#accountOrganization").val($("#accountOrganization").attr("data-originalvalue"));
          $("#accountEmail").val($("#accountEmail").attr("data-originalvalue"));
          $("#accountPassword").val($("#accountPassword").attr("data-originalvalue"));
          $("#accountPasswordConfirmation").val("");
          $("#editAccountConfirmBtn").attr("disabled", true);
          editAccountPageSetup();
       });
       
       $("#editAccountConfirmBtn").click(function(){
          editAccount("<?php echo $_SESSION['loggedUsername'] ?>");
       });
       
    });//Fine document ready
</script>
</body>
</html>