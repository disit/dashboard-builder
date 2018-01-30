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
    
    /*if(!isset($_SESSION['loggedRole']))
    {
        header("location: unauthorizedUser.php");
    }
    else if($_SESSION['loggedRole'] != "ToolAdmin")
    {
        header("location: unauthorizedUser.php");
    }*/
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
        
        <!-- Custom scripts -->
        <script src="../js/accountManagement.js"></script>
        
        <!--<link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
    </head>
    <body class="guiPageBody">
        <div class="container-fluid">
            <?php include "sessionExpiringPopup.php" ?>
            <div class="row">
                <?php include "mainMenu.php" ?>
                <div class="col-xs-12 col-md-10" id="mainCnt">
                    <div class="row hidden-md hidden-lg">
                        <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                            Dashboard Management System
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-10 col-md-12 centerWithFlex"  id="headerTitleCnt">Account</div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
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
                            
                            <div class="row accountEditRow">
                              <div class="col-xs-12 col-md-3 accountEditFieldContainer">
                                 <div class="accountEditIconContainer"><i class="fa fa-address-card-o" aria-hidden="true"></i></div> 
                                 <div class="accountEditDescContainer">First name</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="text" id="accountFirstName" name="accountFirstName" value="<?php echo $firstName ?>" data-originalvalue="<?php echo $firstName ?>">
                                 </div>
                                 <div id="accountFirstNameMsg" class="accountEditSubfieldContainer"></div>    
                             </div>
                             <div class="col-xs-12 col-md-3 col-md-offset-1 accountEditFieldContainer">
                                 <div class="accountEditIconContainer"><i class="fa fa-address-card-o" aria-hidden="true"></i></div>
                                 <div class="accountEditDescContainer">Last name</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="text" id="accountLastName" name="accountLastName" value="<?php echo $lastName ?>" data-originalvalue="<?php echo $lastName ?>">
                                 </div>
                                 <div id="accountLastNameMsg" class="accountEditSubfieldContainer"></div>    
                             </div> 
                             <div class="col-xs-12 col-md-3 col-md-offset-1 accountEditFieldContainer">
                                 <div class="accountEditIconContainer"><i class="fa fa-building-o"></i></div>
                                 <div class="accountEditDescContainer">Organization</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="text" id="accountOrganization" name="accountOrganization" value="<?php echo $organization ?>" data-originalvalue="<?php echo $organization ?>">
                                 </div>
                                 <div id="accountOrganizationMsg" class="accountEditSubfieldContainer"></div>    
                             </div>  
                           </div>
                           <div class="row accountEditRow">
                              <div class="col-xs-12 col-md-3 accountEditFieldContainer">
                                  <div class="accountEditIconContainer"><i class="fa fa-at"></i></div>
                                 <div class="accountEditDescContainer">E-Mail</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="email" id="accountEmail" name="accountEmail" value="<?php echo $email ?>" data-originalvalue="<?php echo $email ?>">
                                 </div>
                                 <div id="accountEmailMsg" class="accountEditSubfieldContainer"></div>    
                             </div>
                             <div class="col-xs-12 col-md-3 col-md-offset-1 accountEditFieldContainer">
                                 <div class="accountEditIconContainer"><i class="fa fa-key"></i></div>
                                 <div class="accountEditDescContainer">Password</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="password" id="accountPassword" name="accountPassword">
                                 </div>
                                 <div id="accountPasswordMsg" class="accountEditSubfieldContainer"></div>    
                             </div> 
                             <div class="col-xs-12 col-md-3 col-md-offset-1 accountEditFieldContainer">
                                 <div class="accountEditIconContainer"><i class="fa fa-key"></i></div>
                                 <div class="accountEditDescContainer">Password confirmation</div>
                                 <div class="accountEditSubfieldContainer">
                                    <input type="password" id="accountPasswordConfirmation" name="accountPasswordConfirmation">
                                 </div>
                                 <div id="accountPasswordConfirmationMsg" class="accountEditSubfieldContainer"></div>    
                             </div>  
                           </div> 
                           <div class="row accountEditRow" id="editAccountBtnRow">
                              <button type="button" id="editAccountConfirmBtn" class="btn pull-left internalLink" disabled="true" style="margin-right: 15px; background-color: rgba(0, 162, 211, 1); color: white; font-weight: bold">Apply changes</button>
                              <button type="button" id="editAccountCancelBtn" class="btn pull-left" data-dismiss="modal" style="background-color: #f3cf58; color: white; font-weight: bold">Undo changes</button>
                           </div>
                        </div>
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
        });
        
        $('#accountManagementLink .mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt #accountManagementLink .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt #accountManagementLink .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        
       editAccountPageSetup();
       
       $('#editAccountCancelBtn').off("click");
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
       
       $('#editAccountConfirmBtn').off("click");
       $("#editAccountConfirmBtn").click(function(){
          editAccount("<?php echo $_SESSION['loggedUsername'] ?>");
       });
       
    });//Fine document ready
</script>