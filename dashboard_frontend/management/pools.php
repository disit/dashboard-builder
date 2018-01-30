<?php

/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

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
    
    function ldapCheckRole($connection, $userDn, $role) {
      $result = ldap_search(
              $connection, 'dc=ldap,dc=disit,dc=org', 
              '(&(objectClass=organizationalRole)(cn=' . $role . ')(roleOccupant=' . $userDn . '))'
      );
      $entries = ldap_get_entries($connection, $result);
      //echo var_dump($entries);
      foreach ($entries as $key => $value) {
          if (is_numeric($key)) {
              if ($value["cn"]["0"] == $role) 
              {
                  return true;
              }
          }
      }
      return false;
  }
    
    if(!isset($_SESSION['loggedRole']))
    {
        header("location: unauthorizedUser.php");
    }
    else if(($_SESSION['loggedRole'] != "ToolAdmin")&&($_SESSION['loggedRole'] != "AreaManager"))
    {
        header("location: unauthorizedUser.php");
    }
    
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
    <!--<link href="../css/pageTemplate.css" rel="stylesheet">-->
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="../js/jquery-1.10.1.min.js"></script>
    
    <!-- JQUERY UI -->
    <script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>
    
    <!-- Bootstrap editable tables -->
    <link href="https:/cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">
    
    <!-- Scripts file -->
    <script src="../js/poolsManagement.js"></script>
    
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
                    <div class="col-xs-10 col-md-12 centerWithFlex"  id="headerTitleCnt">Users pools</div>
                    <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                </div>
                <div class="row">
                    <div class="col-xs-12" id="mainContentCnt"> 
                        <div class="row hidden-xs hidden-sm mainContentRow">
                            <div class="col-xs-12 mainContentRowDesc">Synthesis</div>
                            <div id="dashboardTotNumberCnt" class="col-md-2 mainContentCellCnt">
                                <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                    <?php
                                        $query = "SELECT count(*) AS qt FROM Dashboard.UsersPools";
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
                                    pools
                                </div>
                            </div>
                            <div id="dashboardTotActiveCnt" class="col-md-2 mainContentCellCnt">
                                <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                    <?php
                                        $query = "SELECT UsersPools.poolName, subquery.qt FROM Dashboard.UsersPools " .
                                                  "LEFT JOIN (SELECT count(*) AS qt, poolId FROM Dashboard.UsersPoolsRelations GROUP BY poolId) AS subquery " .
                                                  "ON UsersPools.poolId = subquery.poolId " .
                                                  "ORDER BY subquery.qt DESC LIMIT 1";
                                        $result = mysqli_query($link, $query);

                                        if($result)
                                        {
                                           $row = $result->fetch_assoc();
                                           echo $row['poolName'];
                                        }
                                        else
                                        {
                                            $dashboardsActiveQt = "-";
                                            echo '-';
                                        }
                                    ?>
                                </div>
                                <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                    biggest
                                </div>
                            </div>
                            <div id="dashboardTotPermCnt" class="col-md-2 mainContentCellCnt">
                                <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                    <?php
                                        $query = "SELECT UsersPools.poolName, subquery.qt FROM Dashboard.UsersPools " .
                                                  "LEFT JOIN (SELECT count(*) AS qt, poolId FROM Dashboard.UsersPoolsRelations GROUP BY poolId) AS subquery " .
                                                  "ON UsersPools.poolId = subquery.poolId " .
                                                  "ORDER BY subquery.qt ASC LIMIT 1";
                                        $result = mysqli_query($link, $query);

                                        if($result)
                                        {
                                           $row = $result->fetch_assoc();
                                           echo $row['poolName'];
                                        }
                                        else
                                        {
                                            $dashboardsActiveQt = "-";
                                            echo '-';
                                        }
                                    ?>
                                </div>
                                <div class="col-md-12 centerWithFlex pageSingleDataLabel">
                                    smallest
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mainContentRow">
                            <div class="col-xs-12 mainContentRowDesc">Management</div> 
                                <ul class="nav nav-tabs">
                                    <li role="presentation" id="addPoolTab" class="active"><a href="#">Add new pool</a></li>
                                    <li role="presentation" id="managePoolsTab"><a href="#">Manage existing pools</a></li>
                                </ul>
                                <div id="addPoolMainContainer" class="container-fluid">
                                    <form id="addNewPoolForm" name="addNewPoolForm" role="form" method="post" data-toggle="validator">
                                        
                                        <div class="col-xs-12 col-md-4 modalCell">
                                            <div class="modalFieldCnt">
                                                <input type="text" id="addPoolNewPoolName" name="addPoolNewPoolName" class="modalInputTxt" required>
                                            </div>
                                            <div class="modalFieldLabelCnt">New pool name</div>
                                            <div id="addPoolNewPoolNameMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                        </div>
                                        
                                        <div class="col-xs-12 col-md-12 modalCell" style="margin-top: 20px">
                                            <div class="modalFieldLabelCnt">Add users to new pool</div>
                                            <div id="addPoolNewPoolUsersContainer">
                                                <?php
                                                    if(isset($_SESSION['loggedRole']))
                                                    {
                                                        if(($_SESSION['loggedRole'] == "ToolAdmin")||($_SESSION['loggedRole'] == "AreaManager"))
                                                        {
                                                           //Reperimento elenco utenti LDAP
                                                           $temp = [];
                                                           $users = [];

                                                           $ds = ldap_connect($ldapServer, $ldapPort);
                                                           ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                                                           $bind = ldap_bind($ds);

                                                           $result = ldap_search(
                                                                   $ds, 'dc=ldap,dc=disit,dc=org', 
                                                                   '(cn=Dashboard)'
                                                           );
                                                           $entries = ldap_get_entries($ds, $result);
                                                           foreach ($entries as $key => $value) 
                                                           {
                                                              for($index = 0; $index < (count($value["memberuid"]) - 1); $index++)
                                                              { 
                                                                 $usr = $value["memberuid"][$index];
                                                                 array_push($temp, $usr);
                                                              }
                                                           }

                                                           ldap_close();

                                                           $ds = ldap_connect($ldapServer, $ldapPort);
                                                           ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                                                           $bind = ldap_bind($ds);

                                                           for($i = 0; $i < count($temp); $i++)
                                                           {
                                                              if(!ldapCheckRole($ds, $temp[$i], "ToolAdmin"))
                                                              {
                                                                 $name = str_replace("cn=", "", $temp[$i]);
                                                                 $name = str_replace(",dc=ldap,dc=disit,dc=org", "", $name);
                                                                 if(ldapCheckRole($ds, $temp[$i], "Observer"))
                                                                 {
                                                                    $singleUser = [$name, "ldap", "Observer"];
                                                                 }
                                                                 else
                                                                 {
                                                                   if(ldapCheckRole($ds, $temp[$i], "Manager"))
                                                                   {
                                                                      $singleUser = [$name, "ldap", "Manager"];
                                                                   }
                                                                   else
                                                                   {
                                                                      if(ldapCheckRole($ds, $temp[$i], "AreaManager"))
                                                                      {
                                                                         $singleUser = [$name, "ldap", "AreaManager"];
                                                                      }
                                                                   }
                                                                 }

                                                                 array_push($users, $singleUser);
                                                              }
                                                           }

                                                            //Reperimento elenco utenti locali
                                                            $link = mysqli_connect($host, $username, $password) or die();
                                                            mysqli_select_db($link, $dbname);
                                                            $query = "SELECT username, admin FROM Dashboard.Users WHERE admin <> 'ToolAdmin'";
                                                            $result = mysqli_query($link, $query) or die(mysqli_error($link));

                                                            if($result)
                                                            {
                                                               if($result->num_rows > 0) 
                                                               {
                                                                  while ($row = $result->fetch_assoc()) 
                                                                  {
                                                                     $singleUser = [$row["username"], "local", $row["admin"]];
                                                                     array_push($users, $singleUser);
                                                                  }
                                                               }
                                                            }

                                                            if(count($users) > 0)
                                                            {
                                                               echo '<table id="addPoolNewPoolUsersTable">';
                                                               echo '<tr><th class="smallCell">Select user</th><th class="smallCell">Make admin</th><th class="smallCell">Username</th><th class="smallCell">User type</th><th class="addPoolNewUserHideMobile">User origin</th></tr>';
                                                               for($j = 0; $j < count($users); $j++)
                                                               {
                                                                   echo '<tr><td class="smallCell"><input type="checkbox" data-username="' . $users[$j][0] . '" data-usersource="' . $users[$j][1] . '" data-usertype="' . $users[$j][2] . '" /></td><td class="smallCell"><input type="checkbox" disabled="disabled" /><td class="smallCell addUserToPoolUsername">' . $users[$j][0] . '</td><td class="smallCell">' . $users[$j][2] . '</td><td class="addPoolNewUserHideMobile">' . $users[$j][1] . '</td>';
                                                               }


                                                               echo '</table>';
                                                            }
                                                            else
                                                            {
                                                               //Nessun utente associabile
                                                               echo 'No users available';
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </div>
                                        </div>    
                                        <div id="addNewPoolButtonsContainer">
                                            <button type="button" id="addNewPoolCancelBtn" class="btn cancelBtn" style='margin-left: 15px'>Reset form</button>
                                            <button type="button" id="addNewPoolConfirmBtn" class="btn confirmBtn internalLink" disabled="true">Confirm</button>
                                        </div>
                                    </form>    
                                </div>
                                <div id="delPoolMainContainer" class="container-fluid">
                                    <div class="row">
                                        <div class="modalFieldLabelCnt" style="margin-left: 15px; margin-top: 15px">Pools list</div>
                                        <div class="col-xs-12" id="delPoolMainSubContainer">
                                            <?php
                                                if(isset($_SESSION['loggedRole']))
                                                {
                                                    if(($_SESSION['loggedRole'] == "ToolAdmin")||($_SESSION['loggedRole'] == "AreaManager"))
                                                    {
                                                        //Reperimento elenco pool
                                                        $link = mysqli_connect($host, $username, $password) or die();
                                                        mysqli_select_db($link, $dbname);
                                                        $query = "SELECT * FROM Dashboard.UsersPools";
                                                        $result = mysqli_query($link, $query) or die(mysqli_error($link));

                                                        if($result)
                                                        {
                                                            if($result->num_rows > 0) 
                                                            {
                                                                echo '<table id="delPoolTable">';
                                                                echo '<tr><th class="smallCell">Select</th><th class="bigCell">Pool name</th><th class="smallCell">Delete</th></tr>';

                                                                while ($row = $result->fetch_assoc()) 
                                                                {
                                                                    echo '<tr data-poolId="' . $row["poolId"] . '"><td class="smallCell"><input type="radio" name="pool" value="' . $row["poolId"] . '"></td><td class="bigCell">' . $row["poolName"] . '<td class="smallCell"><i data-poolId="' . $row["poolId"] . '" data-poolName="' . $row["poolName"] . '" class="fa fa-remove" style="font-size:24px;color:red"></i></td>';
                                                                }
                                                                echo '</table>';
                                                            }
                                                            else
                                                            {
                                                                //Nessun pool disponibile
                                                                echo 'No pools available';
                                                            }
                                                        }
                                                        else
                                                        {
                                                            //Nessun pool disponibile
                                                            echo 'No pools available';
                                                        }
                                                    }
                                                }
                                            ?>    
                                        </div>
                                        <div class="col-md-2"></div> <!-- Celle vuote di utilità -->
                                    </div>
                                    <div id="editPoolsNamesButtonsContainer" class="row">
                                        <button type="button" id="editPoolsNamesDiscardBtn" class="btn cancelBtn">Undo changes</button>
                                        <button type="button" id="editPoolsNamesBtn" class="btn confirmBtn internalLink">Save pool names</button>
                                    </div>
                                    <div id ="poolManagementRow" class="row">
                                        <div class="col-xs-12 col-md-5">
                                            <div id="outerUsersLabelContainer" class="modalFieldLabelCnt">
                                                Users not in the pool
                                            </div>
                                            <div id="outerUsersTableContainer"></div>
                                        </div>
                                        <div id="buttonsContainer" class="col-xs-12 col-md-2">
                                            <div>
                                                <i id="addUsersToPoolBtn" class="fa" style="font-size:36px"></i>
                                            </div>
                                            <div>
                                                <i id="delUsersFromPoolBtn" class="fa" style="font-size:36px"></i>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-5">
                                            <div id="innerUsersLabelContainer" class="modalFieldLabelCnt">
                                                Users in the pool
                                            </div>
                                            <div id="innerUsersTableContainer"></div>
                                        </div>
                                    </div>
                                    <div id="editPoolsButtonsContainer" class="row">
                                        <button type="button" id="editPoolsDiscardBtn" class="btn cancelBtn">Undo changes</button>
                                        <button type="button" id="editPoolsBtn" class="btn confirmBtn">Save pools compositions</button>
                                    </div>
                                </div>
                            </div>
                        </div>    
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modale di notifica inserimento pool avvenuto con successo -->
     <div class="modal fade" id="addPoolOkModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Add new pool
            </div>
            <div id="delMetricModalBody" class="modal-body modalBody">
                <div class="row">
                    <div class="col-xs-12 modalCell">
                        <div class="modalDelMsg col-xs-12 centerWithFlex" id="addPoolOkModalInnerDiv1">
                        </div>
                        <div id="addPoolOkModalInnerDiv2"  class="modalDelObjName col-xs-12 centerWithFlex"><i class="fa fa-check" style="font-size:36px"></i></div> 
                    </div>
                </div>    
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di notifica inserimento pool fallito -->
    <div class="modal fade" id="addPoolKoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Add new pool
            </div>
            <div id="delMetricModalBody" class="modal-body modalBody">
                <div class="row">
                    <div class="col-xs-12 modalCell">
                        <div class="modalDelMsg col-xs-12 centerWithFlex" id="addPoolKoModalInnerDiv1">
                        </div>
                        <div id="addPoolKoModalInnerDiv2"  class="modalDelObjName col-xs-12 centerWithFlex"><i class="fa fa-frown-o" style="font-size:36px"></i></div> 
                    </div>
                </div>    
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di conferma cancellazione pool -->
    <div class="modal fade" id="deletePoolModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Pool deletion
            </div>
            <div class="modal-body modalBody">   
            </div>
            <div class="modal-footer">
              <button type="button" id="deletePoolCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
              <button type="button" id="deletePoolConfirmBtn" class="btn confirmBtn">Confirm</button>
            </div>  
          </div>
        </div>
    </div>
    
    <!-- Modale di notifica cancellazione pool avvenuta con successo -->
    <div class="modal fade" id="delPoolOkModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Pool deletion
            </div>
            <div class="modal-body modalBody">
                <div class="row">
                    <div class="col-xs-12 modalCell">
                        <div class="modalDelMsg col-xs-12 centerWithFlex" id="delPoolOkModalInnerDiv1">
                        </div>
                        <div id="delPoolOkModalInnerDiv2"  class="modalDelObjName col-xs-12 centerWithFlex"><i class="fa fa-check" style="font-size:36px"></i></div> 
                    </div>
                </div>    
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di notifica cancellazione pool fallita -->
    <div class="modal fade" id="delPoolKoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Pool deletion
            </div>
            <div class="modal-body modalBody">
                <div class="row">
                    <div class="col-xs-12 modalCell">
                        <div class="modalDelMsg col-xs-12 centerWithFlex" id="delPoolKoModalInnerDiv1">
                        </div>
                        <div id="delPoolKoModalInnerDiv2"  class="modalDelObjName col-xs-12 centerWithFlex"><i class="fa fa-check" style="font-size:36px"></i></div> 
                    </div>
                </div>    
            </div>
          </div>
        </div>
    </div>
    
    <!-- Modale di conferma edit dei pools -->
    <div class="modal fade" id="editPoolsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Pools edit
            </div>
            <div class="modal-body modalBody centerWithFlex">
                Do you want to save edited pools compositions?
            </div>
            <div class="modal-footer">
              <button type="button" id="editPoolsConfirmCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
              <button type="button" id="editPoolsConfirmBtn" class="btn confirmBtn">Confirm</button>
            </div>  
          </div>
        </div>
    </div>
    
    <!-- Modale di conferma undo edit dei pools -->
    <div class="modal fade" id="editPoolsUndoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Pools edit
            </div>
            <div class="modal-body modalBody centerWithFlex">
                Do you want to discard changes made to pools compositions?
            </div>
            <div class="modal-footer">
              <button type="button" id="editPoolsCancelUndoBtn" class="btn cancelBtn" data-dismiss="modal">No</button>
              <button type="button" id="editPoolsConfirmUndoBtn" class="btn confirmBtn">Yes</button>
            </div>  
          </div>
        </div>
    </div>
    
    <!-- Modale di conferma cambio pagina senza aver salvato le modifiche ai pools -->
    <div class="modal fade" id="editPoolsLeavePageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Pools edit
            </div>
            <div class="modal-body modalBody centerWithFlex">
                There are unsaved changes to users pools: if you leave the page changes will be discarded, do you want to confirm?
            </div>
            <div class="modal-footer">
              <button type="button" id="editPoolsLeavePageCancelBtn" class="btn cancelBtn" data-dismiss="modal">No</button>
              <button type="button" id="editPoolsLeavePageConfirmBtn" class="btn confirmBtn">Yes</button>
            </div>  
          </div>
        </div>
    </div>
    
    <!-- Modale di conferma cambio nomi dei pools -->
    <div class="modal fade" id="editPoolsNamesModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="editPoolsNamesModal">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Pools edit
            </div>
            <div class="modal-body modalBody centerWithFlex">
                Do you want to save edited pools names?
            </div>
            <div class="modal-footer">
              <button type="button" id="editPoolsNamesCancelBtn" class="btn cancelBtn" data-dismiss="modal">No</button>
              <button type="button" id="editPoolsNamesConfirmBtn" class="btn confirmBtn">Yes</button>
            </div>  
          </div>
        </div>
    </div>
    
    <!-- Modale di conferma undo edit dei nomi dei pools -->
    <div class="modal fade" id="editPoolsNamesUndoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="editPoolsNamesModal">
          <div class="modal-content">
            <div class="modalHeader centerWithFlex">
              Pools edit
            </div>
            <div class="modal-body modalBody centerWithFlex">
                Do you want to discard changes made to pools names?
            </div>
            <div class="modal-footer">
              <button type="button" id="editPoolsNamesUndoCancelBtn" class="btn cancelBtn" data-dismiss="modal">No</button>
              <button type="button" id="editPoolsNamesUndoConfirmBtn" class="btn confirmBtn">Yes</button>
            </div>  
          </div>
        </div>
    </div>
    
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
        
        if($(window).width() < 992)
        {
            $('#addUsersToPoolBtn').addClass("fa-arrow-circle-down");
            $('#delUsersFromPoolBtn').addClass("fa-arrow-circle-up");
        }
        else
        {
            $('#addUsersToPoolBtn').addClass("fa-arrow-circle-right");
            $('#delUsersFromPoolBtn').addClass("fa-arrow-circle-left");
        }
        
        var admin = "<?= $_SESSION['loggedRole'] ?>";
        var showManagementTab = "<?= $_GET['showManagementTab'] ?>";
        var selectedPoolId = "<?= $_GET['selectedPoolId'] ?>";
        var internalDest = false;
        
        $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        
        $(window).resize(function(){
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            if($(window).width() < 992)
            {
                $('#addUsersToPoolBtn').removeClass("fa-arrow-circle-right");
                $('#addUsersToPoolBtn').addClass("fa-arrow-circle-down");
                $('#delUsersFromPoolBtn').removeClass("fa-arrow-circle-left");
                $('#delUsersFromPoolBtn').addClass("fa-arrow-circle-up");
            }
            else
            {
                $('#addUsersToPoolBtn').removeClass("fa-arrow-circle-down");
                $('#addUsersToPoolBtn').addClass("fa-arrow-circle-right");
                $('#delUsersFromPoolBtn').removeClass("fa-arrow-circle-up");
                $('#delUsersFromPoolBtn').addClass("fa-arrow-circle-left");
            }    
        });
        
        $('#link_pools_management .mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt #link_pools_management .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt #link_pools_management .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        //Settaggio dei globals per il file poolsManagement.js
        setGlobals(admin);
        getPoolsCompositions();
        
        $("#delPoolTable tr").each(function(i) 
        {
            $(this).find("td").each(function(j)
            { 
                if(j === 1)
                {
                    var value = $(this).html();
                    $(this).html('<a href="#" class="toBeEdited" data-type="text" data-mode="inline" data-title="Pool name">' + value + '</a>');
                    $(this).find('a').editable({
                        //Lo rende obbligatorio
                        validate: function(value) {
                           if($.trim(value) === '') return 'This field is required';
                        }
                    });
                }
            });
        });
        
        $("#delPoolTable a").on('save', function(){
            $(this).parents("tr").attr("data-edited", true);
            disableMainLinks();
        });
        
        $("#addPoolNewPoolUsersTable input").click(function(){
            if($(this).attr("checked") === "checked")
            {
                $(this).removeAttr("checked");
            }
            else
            {
                $(this).attr("checked", "true");
            }
        });
        
        $("#delPoolTable input[type=radio]").click(function(){
            $("#delPoolTable input[type=radio]").attr("data-selected", "false");
            $(this).attr("data-selected", "true");
        });
        
        //Inizialmente rendiamo visibile il tab di aggiunta nuovo pool
        $("#addPoolMainContainer").show();
        $("#delPoolMainContainer").hide();
        $("#poolsUsersMainContainer").hide();
        
        //Controllo accettabilità pool name
        $("#addPoolNewPoolName").on('input', checkNewPoolName);
        $("#addPoolNewPoolName").on('input', checkAddNewPoolConditions);
        checkNewPoolName();
        
        //Controllo accettabilità pool name
        $("#addPoolNewPoolName").on('input', checkNewPoolName);
            
        //Controllo per abilitare/disabilitare pulsante di conferma inserimento nuovo pool
        $("#addPoolNewPoolName").on('input', checkAddNewPoolConditions);

        $("#addNewPoolConfirmBtn").on('click', addNewPool);
        
        $("#addPoolNewPoolUsersTable tr").each(function(){
            $(this).find("td").eq(0).find("input").click(function(){
               if(($(this).attr("data-usertype") !== "Observer")&&($(this).attr("data-usertype") !== "Manager"))
               {
                  if($(this).attr("checked") === "checked")
                  {
                      $(this).parent().parent().find("td").eq(1).find("input").attr("disabled", false);
                  }
                  else
                  {
                      $(this).parent().parent().find("td").eq(1).find("input").attr("disabled", true);
                  }
               }
            });
        });
        
        $("#delPoolTable i.fa-remove").click(function(){
            setPoolIdToDelete($(this).attr("data-poolId"), $(this).attr("data-poolName"));
            $("#deletePoolModal div.modal-body").html("Do you want to confirm pool <b>&nbsp;" + $(this).attr("data-poolName") + "&nbsp;</b> deletion?");
            $("#deletePoolModal").modal('show');
        });
        
        //Gestori pulsanti dei tab
        $("#addPoolTab").click(function() 
        {
            $("#addPoolTab").attr("class", "active");
            $("#managePoolsTab").attr("class", "");
            
            $("#addPoolMainContainer").show();
            $("#delPoolMainContainer").hide();
        });
        
        $("#managePoolsTab").click(function() 
        {
            $("#addPoolTab").attr("class", "");
            $("#managePoolsTab").attr("class", "active");
            
            $("#addPoolMainContainer").hide();
            $("#delPoolMainContainer").show();
        });
        
        //Reset add pool form
        $("#addNewPoolCancelBtn").click(function(){
            $("#addNewPoolForm").trigger("reset");
            $("#addPoolNewPoolUsersTable input").removeAttr("checked");
        });
        
        $("#editPoolsBtn").click(function(){
            $("#editPoolsModal").modal('show');
        });
        
        $("#editPoolsDiscardBtn").click(function(){
            $("#editPoolsUndoModal").modal('show');
        });
        
        $("#editPoolsConfirmUndoBtn").click(function(){
            $("#editPoolsUndoModal").modal('hide');
            getPoolsCompositions();
            buildPoolCompositionTables(); 
        });
        
        $('input[type=radio][name=pool]').change(buildPoolCompositionTables);
        $("#deletePoolConfirmBtn").click(deletePool);
        $('#addUsersToPoolBtn').click(addUsersToPool);
        $('#delUsersFromPoolBtn').click(delUsersFromPool);
        $('#editPoolsConfirmBtn').click(savePoolsCompositions);
        
        $("#editPoolsLeavePageConfirmBtn").click(confirmPageChange); 
        
        $("#editPoolsNamesBtn").click(function(){
            $("#editPoolsNamesModal").modal('show');
        });
        
        $("#editPoolsNamesConfirmBtn").click(savePoolsNames);
        
        $("#editPoolsNamesDiscardBtn").click(function(){
            $("#editPoolsNamesUndoModal").modal('show');
        });
        
        $("#editPoolsNamesUndoConfirmBtn").click(function(){
            var index = window.location.href.indexOf("?");
            var locationBase = window.location.href.substring(0, index);
            var selectedPoolIdLocal = null;
            
            if($("#delPoolTable input[data-selected=true]").length > 0)
            {
                selectedPoolIdLocal = $("#delPoolTable input[data-selected=true]").val();
            }
            else
            {
                selectedPoolIdLocal = -1;
            }
            
            $("#editPoolsNamesUndoModal div.modal-body").empty();
            $("#editPoolsNamesUndoModal div.modal-footer").hide();
            $("#editPoolsNamesUndoModal div.modal-body").removeClass("centerWithFlex");
            $("#editPoolsNamesUndoModal div.modal-body").append('<div class="poolsManagementSubfieldContainer">Pools names changes discarded</div>');
            $("#editPoolsNamesUndoModal div.modal-body").append('<div class="poolsManagementSubfieldContainer"><i class="fa fa-check" style="font-size:42px"></i></div>');
            setTimeout(function(){
                window.location.href = locationBase + "?showManagementTab=true&selectedPoolId=" + selectedPoolIdLocal;
            }, 1500);
        });
        
        if(showManagementTab === "true")
        {
            $("#managePoolsTab").trigger('click');
            if(selectedPoolId !== '-1')
            {
                $("#delPoolTable input[value=" + selectedPoolId + "]").trigger('click');
            }
        }
    });//Fine document ready
</script>
</body>
</html>