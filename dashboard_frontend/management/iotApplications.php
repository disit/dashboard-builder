<?php
/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
if (!isset($_SESSION)) {
  session_start();
}

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

checkSession('Manager');
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php include "mobMainMenuClaim.php" ?></title>

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

    <!-- Bootstrap editable tables -->
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>-->

    <link href="../bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
    <script src="../bootstrap3-editable/js/bootstrap-editable.js"></script>

    <!-- Bootstrap table -->
    <link rel="stylesheet" href="../boostrapTable/dist/bootstrap-table.css">
    <script src="../boostrapTable/dist/bootstrap-table.js"></script>
    <!-- Questa inclusione viene sempre DOPO bootstrap-table.js -->
    <script src="../boostrapTable/dist/locale/bootstrap-table-en-US.js"></script>

    <!-- Dynatable -->
    <link rel="stylesheet" href="../dynatable/jquery.dynatable.css">
    <script src="../dynatable/jquery.dynatable.js"></script>

    <!-- Bootstrap slider -->
    <script src="../bootstrapSlider/bootstrap-slider.js"></script>
    <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet"/>

    <!-- Filestyle -->
    <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/dashboardList.css" rel="stylesheet">
    <link href="../css/iotApplications.css?v=a" rel="stylesheet">

    <!-- Custom scripts -->
    <script type="text/javascript" src="../js/dashboard_mng.js"></script>

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
              <?php include "mobMainMenuClaim.php" ?>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-10 col-md-12 centerWithFlex" id="headerTitleCnt">IOT Applications</div>
            <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
          </div>
          <div class="row">
            <div class="col-xs-12" id="mainContentCnt" style='background-color: rgba(138, 159, 168, 1)'>
              <div class="row mainContentRow" id="iotApplicationsIframeRow">
                <div class="col-xs-12 mainContentCellCnt" id="iotApplicationsIframeCnt">
                  <iframe id="iotApplicationsIframe"></iframe>
                </div>
              </div>    


              <div class="row mainContentRow" id="iotAppsListTableRow">
                <div class="col-xs-12 mainContentCellCnt" style='background-color: rgba(138, 159, 168, 1)'>
                  <div id="iotAppsListMenu" class="row">
                    <!--<div id="dashboardListsViewMode" class="hidden-xs col-sm-6 col-md-2 iotAppsListMenuItem">
                        <div class="iotAppsListMenuItemContent centerWithFlex col-xs-12">
                            <input id="dashboardListsViewModeInput" type="checkbox">
                        </div>
                    </div>-->
                    <div id="dashboardListsCardsSort" class="col-xs-12 col-sm-6 col-md-1 col-md-offset-2 iotAppsListMenuItem">
                      <div class="iotAppsListMenuItemContent centerWithFlex col-xs-12">
                        <div class="col-xs-6 centerWithFlex">
                          <div class="iotAppsListSortBtnCnt">
                            <i class="fa fa-sort-alpha-asc iotAppsListSort"></i>
                          </div> 
                        </div>
                        <div class="col-xs-6 centerWithFlex">
                          <div class="iotAppsListSortBtnCnt">
                            <i class="fa fa-sort-alpha-desc iotAppsListSort"></i>
                          </div>    
                        </div>
                      </div>
                    </div>
                    <div id="dashboardListsPages" class="col-xs-12 col-sm-6 col-md-3 iotAppsListMenuItem">
                      <!--<div class="iotAppsListMenuItemTitle centerWithFlex col-xs-4">
                           List<br>pages
                       </div>-->
                      <div class="iotAppsListMenuItemContent centerWithFlex col-xs-12">

                      </div>
                    </div>

                    <div id="dashboardListsSearchFilter" class="col-xs-12 col-sm-6 col-md-4 iotAppsListMenuItem">
                      <!--<div class="iotAppsListMenuItemTitle centerWithFlex col-xs-3">
                          Search
                      </div>-->
                      <div class="iotAppsListMenuItemContent centerWithFlex col-xs-12">
                        <div class="input-group">
                          <div class="input-group-btn">
                            <button type="button" id="searchDashboardBtn" class="btn"><i class="fa fa-search"></i></button>
                            <button type="button" id="resetSearchDashboardBtn" class="btn"><i class="fa fa-close"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="dashboardListsNewDashboard" class="col-xs-12 col-sm-6 col-md-2 iotAppsListMenuItem">
                      <!--<div class="iotAppsListMenuItemTitle centerWithFlex col-xs-4">
                          New<br>dashboard
                      </div>-->
                      <div class="iotAppsListMenuItemContent centerWithFlex col-xs-12">
                        <button id="link_add_iotapp" data-toggle="modal" type="button" class="btn btn-warning">Create new</button>
                      </div>
                    </div>
                  </div>


                  <table id="list_dashboard" class="table">
                    <thead class="iotAppsTableHeader">
                      <tr>
                        <th data-dynatable-column="title_header">Title</th>
                        <th data-dynatable-column="user">Creator</th>
                        <th data-dynatable-column="creation_date">Creation date</th>
                        <th data-dynatable-column="last_edit_date">Last edit date</th>
                        <th data-dynatable-column="status_dashboard">Status</th>
                        <th>Edit</th>
                        <th>View</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>

                  <div id="list_dashboard_cards" class="container-fluid">

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modale creazione app -->
    <div class="modal fade" id="modalAddIoTApp" tabindex="-1" role="dialog" aria-labelledby="modalAddIoTApp" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content modalContentWizardForm" style="top:150px;">
          <div class="modalHeader centerWithFlex" style="background-color: rgba(51, 64, 69, 1) !important">
            Create IoT Application
          </div>

          <div id="modalAddIoTAppBody" class="modal-body modalBody" style="background-color: rgba(108, 135, 147, 1) !important">   
            <div class="row iotAppModalRow">
              <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                Application name:
              </div>
              <div class="col-xs-8">
                <input type="text" name="inputTitleIoTApp" id="inputTitleIoTApp" value="" class="form-control" style="width: 100%;" required> 
              </div>
            </div>
<?php if($_SESSION['loggedRole']!='Manager') : ?>
            <div class="row iotAppModalRow">
              <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                Application type:
              </div>
              <div class="col-xs-8">
                <select id="applicationType" class="form-control">
                    <option value="basic">Basic</option>
                    <option value="advanced">Advanced</option>
                </select>    
              </div>
            </div>  
<?php endif; ?>
              <div class="row">
                <div class="col-xs-12" style="font-weight: bold !important; margin-top: 25px !important; color: white;">
                  By pressing the <b>Confirm</b> button you agree to the <a style="color:#aedaff" href="https://www.snap4city.org/drupal/node/47" target="_blank">Terms and Conditions</a> and <a href="https://www.snap4city.org/drupal/node/49" style="color:#aedaff" target="_blank">Privacy Policy</a>.
                </div>
              </div>                           
            </div>
            <div id="modalAddIoTAppFooter" class="modal-footer">
              <button type="button" id="modalAddIoTAppCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
              <button type="button" id="modalAddIoTAppConfirmBtn" name="addDashboardWizardConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
            </div>
          </div>    <!-- Fine modal content -->
        </div> <!-- Fine modal dialog -->
      </div><!-- Fine modale -->
    <!-- Modale creazione app -->
    <div class="modal fade" id="modalEditIoTApp" tabindex="-1" role="dialog" aria-labelledby="modalEditIoTApp" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content modalContentWizardForm" style="top:150px;">
          <div class="modalHeader centerWithFlex" style="background-color: rgba(51, 64, 69, 1) !important">
            IoT Application Management
          </div>

          <div id="modalEditIoTAppBody" class="modal-body modalBody" style="background-color: rgba(108, 135, 147, 1) !important">   
            <!-- Tabs -->
            <ul id="iotappTabsContainer" class="nav nav-tabs nav-justified">
                <li id="propertiesTab" class="active"><a data-toggle="tab" href="#propertiesCnt">Properties</a></li>
                <li id="controlTab"><a data-toggle="tab" href="#controlCnt" class="dashboardWizardTabTxt">Control</a></li>
                <li id="ownershipTab"><a data-toggle="tab" href="#ownershipCnt" class="dashboardWizardTabTxt">Ownership</a></li>
            </ul> 
            <!-- Fine tabs -->
            <div class="tab-content" style="height:200px">
              <!-- Ownership cnt -->
              <div id="ownershipCnt" class="tab-pane fade in">
                  <div class="row" id="ownershipFormRow">
                      <div class="col-xs-12 centerWithFlex delegationsModalLbl modalFirstLbl" id="changeOwnershipLbl">
                          Change ownership
                      </div>
                      <div class="col-xs-12" id="newOwnershipCnt">
                          <div class="input-group">
                              <input type="text" class="form-control" id="newOwner" placeholder="New owner username">
                              <span class="input-group-btn">
                                <button type="button" id="newOwnershipConfirmBtn" class="btn confirmBtn disabled">Confirm</button>
                              </span>
                          </div>
                          <div class="col-xs-12 centerWithFlex delegationsModalMsg" id="newOwnerMsg">
                              New owner username can't be empty
                          </div>    
                      </div>
                      <div class="col-xs-12 centerWithFlex" id="newOwnershipResultMsg">

                      </div>    
                  </div>    
              </div>
              <!-- Fine ownership cnt -->
              <div id="propertiesCnt" class="tab-pane fade in active">
                <input type="hidden" id="iotAppIdHidden" name="iotAppIdHidden" />
                <input type="hidden" id="iotAppNameHidden" name="iotAppNameHidden" />
                <div class="row iotAppModalRow">
                  <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                    Application name:
                  </div>
                  <div class="col-xs-8">
                    <input type="text" name="inputTitleEditIoTApp" id="inputTitleEditIoTApp" value="" class="form-control" style="width: 100%;" required> 
                  </div>
                </div>
    <?php if($_SESSION['loggedRole']!='Manager') : ?>
                <div class="row iotAppModalRow">
                  <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                    Application type:
                  </div>
                  <div class="col-xs-8">
                    <select id="appTypeEditIoTApp" class="form-control" disabled>
                        <option value="basic">Basic</option>
                        <option value="advanced">Advanced</option>
                        <option value="plumber">Data analytic</option>
                    </select>    
                  </div>     
                </div>
    <?php endif; ?>
                <div class="row iotAppModalRow">
                  <div class="col-xs-4 centerWithFlex iotAppModalLbl">
                    Created:
                  </div>
                  <div class="col-xs-8">
                    <input type="text" readonly name="createdEditIoTApp" id="createdEditIoTApp" value="10 min" class="form-control">   
                  </div>     
                </div>
                <div class="row iotAppModalRow">
                  <div class="col-xs-12 centerWithFlex">
                    <button type="button" id="modalEditIoTAppConfirmBtn" name="addDashboardWizardConfirmBtn" class="btn confirmBtn internalLink">Update</button>
                  </div>     
                </div>
              </div>
              <div id="controlCnt" class="tab-pane fade in">
                <div class="row iotAppModalRow" style="padding-top:85px">
                  <div class="col-xs-12 centerWithFlex">
                    <button type="button" id="modalEditIoTAppDeleteBtn" class="btn deleteBtn" data-dismiss="modal">Delete application...</button>
                    <button type="button" id="modalEditIoTAppRestartBtn" class="btn restartBtn" data-dismiss="modal">Restart application...</button>
                  </div>     
                </div>
              </div>
            </div>
            <div id="modalAddIoTAppFooter" class="modal-footer">
              <button type="button" id="modalEditIoTAppCancelBtn" class="btn cancelBtn" data-dismiss="modal">Close</button>
            </div>          
        </div>
      </div>
  </body>
</html>

<script type='text/javascript'>
    var iotAppsList = [];
    var iotAppsHealthiness = {};
    
    function checkIotApp(appId) {
      var d=new Date(iotAppsHealthiness[appId].created);
      var frequency;
      if((Date.now()-d)/60000<5)
        frequency = 1000;
      else 
        frequency = 60000;
      $.ajax({
          url: "../controllers/statusIotApplication.php",
          data: {
            id: appId
          },
          type: "GET",
          async: true,
          dataType: 'json',
          success: function(data) {
            //console.log(data);
            if((data.detail=="Ok" && data.result.healthiness!==undefined) || appId.length>10) {
              if(appId.length>10 && !appId.startsWith("pl"))
                iotAppsHealthiness[appId].healthiness = null;
              else {
                iotAppsHealthiness[appId].healthiness = data.result.healthiness;
                iotAppsHealthiness[appId].timeout = setTimeout('checkIotApp("'+appId+'")',frequency);
              }
              if(iotAppsHealthiness[appId].healthiness) {
                $("#health_"+appId).css("background-color","lightgreen");
                //$("#iotapp_"+appId+" .iotAppsListCardOverlayTxt").removeClass("wait");
                $("#iotapp_"+appId+" div.iotAppsListCardOverlayDiv").css("opacity", "0.05");
              } else if(iotAppsHealthiness[appId].healthiness === null) {
                $("#health_"+appId).css("background-color","lightgray");
                //$("#iotapp_"+appId+" .iotAppsListCardOverlayTxt").removeClass("wait");
                $("#iotapp_"+appId+" div.iotAppsListCardOverlayDiv").css("opacity", "0.05");
              } else {
                $("#health_"+appId).css("background-color","red");
                //$("#iotapp_"+appId+" .iotAppsListCardOverlayTxt").addClass("wait");
                $("#iotapp_"+appId+" div.iotAppsListCardOverlayDiv").css("opacity", "0.8");
              }
            } else {
              iotAppsHealthiness[appId]="?";
              console.log("iotapp status "+appId+" unknown")
            }
          },
          error: function(errorData) {
            console.log(errorData);
          }
      });
    }
   
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
        $('#iotApplicationsIframeCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        
        $(window).resize(function(){
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            $('#iotApplicationsIframeCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
            $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");
        });
        
        $('#mainMenuCnt .mainMenuLink[id=<?= $_REQUEST['linkId'] ?>] div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt .mainMenuLink[id=<?= $_REQUEST['linkId'] ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt .mainMenuLink[id=<?= $_REQUEST['linkId'] ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        if($('div.mainMenuSubItemCnt').parents('a[id=<?= $_REQUEST['linkId'] ?>]').length > 0)
        {
            var fatherMenuId = $('div.mainMenuSubItemCnt').parents('a[id=<?= $_REQUEST['linkId'] ?>]').attr('data-fathermenuid');
            $("#" + fatherMenuId).attr('data-submenuVisible', 'true');
            $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + fatherMenuId + ']').show();
            $("#" + fatherMenuId).find('.submenuIndicator').removeClass('fa-caret-down');
            $("#" + fatherMenuId).find('.submenuIndicator').addClass('fa-caret-up');
            $('div.mainMenuSubItemCnt').parents('a[id=<?= $_REQUEST['linkId'] ?>]').find('div.mainMenuSubItemCnt').addClass("subMenuItemCntActive");
        }
        
        var usr = "<?= $_SESSION['loggedUsername'] ?>";
            
        $('#color_hf').css("background-color", '#ffffff');
            
        $("#logoutBtn").off("click");
        $("#logoutBtn").click(function(event)
        {
           event.preventDefault();
           location.href = "logout.php";
        });
            
        function myRowWriter(rowIndex, record, columns, cellWriter)
        {
            var statusBtn, cssClass = null;
            var title = record.title_header;

            if(rowIndex%2 !== 0)
            {
                cssClass = 'blueRow';
            }
            else
            {
                cssClass = 'whiteRow';
            }

            if(title.length > 75)
            {
               title = title.substr(0, 75) + " ...";
            }

            var user = record.user;
            if(user.length > 75)
            {
               user = user.substr(0, 75) + " ...";
            }

            if((record.status_dashboard === '0')||(record.status_dashboard === 0))
            {
                statusBtn = '<input type="checkbox" data-toggle="toggle" class="changeDashboardStatus">';
            }
            else
            {
                statusBtn = '<input type="checkbox" checked data-toggle="toggle" class="changeDashboardStatus">';
            }

            var newRow = '<tr data-dashTitle="' + record.title_header + '" data-uniqueid="' + record.Id + '" data-authorName="' + record.user + '"><td class="' + cssClass + '" style="font-weight: bold">' + title + '</td><td class="' + cssClass + '">' + user + '</td><td class="' + cssClass + '">' + record.creation_date + '</td><td class="' + cssClass + '">' + record.last_edit_date + '</td><td class="' + cssClass + '">' + statusBtn + '</td><td class="' + cssClass + '"><button type="button" class="dashBtnCard editDashBtn">edit</button></td><td class="' + cssClass + '"><button type="button" class="viewDashBtn">view</button></td></tr>';

            return newRow;
        }
    
        function myCardsWriter(rowIndex, record, columns, cellWriter)
        {
            var title = record.title;

            if(title.length > 100)
            {
               title = title.substr(0, 100) + " ...";
            }
            var owner = "";
            if(record.username!=usr)
              owner ='owner: '+record.username;
            else
              owner ='My own';
            
            var dashboards = "";
            //dashboards+='<a href="'+record.url+'/ui/" target="_blank" title="node-red dashboard" class="red-dash"></a>';
            if(record.type!="plumber") {
              for(var d of record.dashboards) {
                var eurl="",vurl="";
                <?php if(isset($_SESSION['loggedRole']) &&  $_SESSION['loggedRole']!='RootAdmin') echo "if(d.dashboardAuthor==usr)"; ?>
                  eurl = 'dashboard_configdash.php?dashboardId='+d.dashboardId+'&dashboardAuthorName='+d.dashboardAuthor+'&dashboardEditorName='+usr+'&dashboardTitle='+encodeURIComponent(d.dashboardName);
                vurl = '../view/?iddasboard='+btoa(d.dashboardId);
                if(eurl) {
                  dashboards+='<a href="'+vurl+'" target="_blank" title="view '+d.dashboardName+'" class="white-dash1"></a>'
                  dashboards+='<a href="'+eurl+'" target="_blank" title="edit '+d.dashboardName+'" class="white-dash2"></a>'
                } else {
                  dashboards+='<a href="'+vurl+'" target="_blank" title="view '+d.dashboardName+'" class="white-dash"></a>'
                }
              }
              for(var i=0;i<5-record.dashboards.length; i++) {
                dashboards+='<div title="no connected dashboard" class="gray-dash"></div>';
              }
            }
            var healthStyle="lightgray";
            if(iotAppsHealthiness.hasOwnProperty(record.id)) {
              if(iotAppsHealthiness[record.id].healthiness)
                healthStyle="lightgreen";
              else if(iotAppsHealthiness[record.id].healthiness===null)
                healthStyle="lightgray";
              else
                healthStyle="red";                
            } else {
              iotAppsHealthiness[record.id] = {"created":record.created, "healthiness": null};
            }

            var cardDiv = '<div id="iotapp_'+record.id+'" data-uniqueid="' + record.id + '" data-title="' + title + '" data-url="' + record.url + '" data-type="' + record.type + '" data-icon="' + record.icon + '" data-iotapps="' + record.iotapps + '" data-privileges="' + record.privileges + '" data-created="' + record.created + '" data-username="' + record.username + '" data-edgetype="' + record.edgetype + '" class="iotAppsListCardDiv col-xs-12 col-sm-6 col-md-3">' + 
                               '<div id="iotapp_'+record.id+'" class="iotAppsListCardInnerDiv">' +
                                  '<div class="iotAppsListCardTitleDiv col-xs-12 centerWithFlex"><div id="health_'+record.id+'" class="iotAppHealth" style="background-color:'+healthStyle+'">&nbsp;</div>' + title + '</div>' + 
                                  '<div class="iotAppsListCardOverlayDiv col-xs-12 centerWithFlex"></div>' +
                                  '<a href="'+record.url+'" class="iotAppsListCardOverlayTxt col-xs-12 centerWithFlex" style="opacity:1;" onclick="return false"></a>' +
                                  '<div class="iotAppsListCardImgDiv" style="height:150px;margin-bottom:3px;"></div>' + 
                                  '<div class="iotAppsListCardVisibilityDiv col-xs-12 centerWithFlex">'+owner+'</div>'+
                                  '<div class="iotAppsListCardClick2EditDiv col-xs-12" style="background-color: inherit; color: inherit">' + 
                                  '<div style="float:left;width: 135px;height: 25px;overflow: auto;" id="dashboardsListCardDashs">'+dashboards+'</div>' +
                                  '<button type="button" class="dashBtnCard propertiesIoTAppBtnCard" style="float:right;" >Management</button></div>' + 
                                  '</div>' +  
                               '</div>' +
                            '</div>';
             if(!iotAppsHealthiness[record.id].timeout) {
               checkIotApp(record.id);
             }
             return cardDiv;
        }
            
            //Nuova tabella
            $.ajax({
                url: "../controllers/getIotApplications.php",
                data: {
                },
                type: "GET",
                async: true,
                dataType: 'json',
                success: function(data) 
                {
                    iotAppsList = data.applications;
                    iotAppsHealtyiness = {};
                    //Ricordati di metterlo PRIMA dell'istanziamento della tabella
                    $('#list_dashboard_cards').bind('dynatable:afterProcess', function(e, dynatable){
                        $('#iotAppsListTableRow').css('padding-top', '0px');
                        $('#iotAppsListTableRow').css('padding-bottom', '0px');
                        
                        $('#dashboardListsViewModeInput').bootstrapToggle({
                            on: 'View as table',
                            off: 'View as cards',
                            onstyle: 'default',
                            offstyle: 'info',
                            size: 'normal'
                        });
                        
                        $('label.toggle-off').css("background-color", "rgba(0, 162, 211, 1)");
                        $('label.toggle-off').css("font-weight", "bold");
                        $('label.toggle-off').css("padding-left", "18px");
                        $('label.toggle-on').css("background-color", "rgba(255, 204, 0, 1)");
                        $('label.toggle-on').css("color", "rgba(255, 255, 255, 1)");
                        $('label.toggle-on').css("font-weight", "bold");
                        $('label.toggle-on').css("padding-right", "24px");
                        
                        $('#dashboardListsViewModeInput').change(function() {
                            if($(this).prop('checked'))
                            {
                                //Visione a tabella
                                $('#list_dashboard_cards').hide();
                                $('#list_dashboard').show();
                                $("#dynatable-pagination-links-list_dashboard_cards").hide();
                                $("#dynatable-query-search-list_dashboard_cards").hide();
                                $('#dashboardListsCardsSort').hide();
                                $('#dashboardListsPages').removeClass('col-md-3');
                                $('#dashboardListsPages').addClass('col-md-4');
                                $("#dashboardListsItemsPerPage").show();
                                $("#dynatable-pagination-links-list_dashboard").show();
                                $("#dynatable-query-search-list_dashboard").show();
                                
                                $('#searchDashboardBtn').off('click');
                                $('#searchDashboardBtn').click(function(){
                                    var dynatable = $('#list_dashboard').data('dynatable');
                                    dynatable.queries.run();
                                }); 

                                $('#resetSearchDashboardBtn').off('click');
                                $('#resetSearchDashboardBtn').click(function(){
                                    var dynatable = $('#list_dashboard').data('dynatable');
                                    $("#dynatable-query-search-list_dashboard").val("");
                                    dynatable.queries.runSearch("");
                                }); 
                            }
                            else
                            {
                                //Visione a cards
                                $('#list_dashboard').hide();
                                $('#list_dashboard_cards').show();
                                $("#dynatable-pagination-links-list_dashboard").hide();
                                $("#dynatable-query-search-list_dashboard").hide();
                                $("#dashboardListsItemsPerPage").hide();
                                $('#dashboardListsCardsSort').show();
                                $('#dashboardListsPages').removeClass('col-md-4');
                                $('#dashboardListsPages').addClass('col-md-3');
                                $("#dynatable-query-search-list_dashboard_cards").show();
                                $("#dynatable-pagination-links-list_dashboard_cards").show();
                                
                                $('#searchDashboardBtn').off('click');
                                $('#searchDashboardBtn').click(function(){
                                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                                    dynatable.queries.run();
                                }); 

                                $('#resetSearchDashboardBtn').off('click');
                                $('#resetSearchDashboardBtn').click(function(){
                                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                                    $("#dynatable-query-search-list_dashboard_cards").val("");
                                    dynatable.queries.runSearch("");
                                }); 
                            }
                        });


                        //Ricicliamolo come link CREATE NEW
                        $('#link_add_iotapp').off('click').click(function(){                            
                            $('#modalAddIoTApp').modal('show');                            
                            $('#modalAddIoTApp #modalAddIoTAppConfirmBtn').off('click').click(function() {
                              var appName = $('#inputTitleIoTApp').val().trim();
                              if(appName=='')
                                alert("Please provide a name")
                              else {
                                var appType = $('#applicationType').val();
                                $.ajax({
                                  url: "../controllers/createIotApplication.php",
                                  data: {"name": appName, "type":appType},
                                  type: "GET",
                                  async: true,
                                  dataType: 'JSON',
                                  cache: false, 
                                  success: function (data) {
                                    if(data.detail=="Ok") {
                                      //alert("Application created");
                                      location.reload();
                                    } else {
                                      alert("Error: "+data.error);
                                    }
                                    console.log(data);
                                    $('#modalAddIoTApp').modal('hide');
                                  },
                                  error: function (error) {
                                    alert("An error occured");
                                    console.log(error);
                                    $('#modalAddIoTApp').modal('hide');
                                  }
                              });
                              }
                            });
                        });
                        $(this).find('.propertiesIoTAppBtnCard').off('click').click(function() {
                            var appId = $(this).parents('div.iotAppsListCardDiv').attr('data-uniqueid');
                            var appName = $(this).parents('div.iotAppsListCardDiv').attr('data-title');
                            var appType = $(this).parents('div.iotAppsListCardDiv').attr('data-type');
                            var appCreated = $(this).parents('div.iotAppsListCardDiv').attr('data-created');
                            appCreated = new Date(appCreated);
                            
                            $('#iotAppIdHidden').val(appId);
                            $('#iotAppNameHidden').val(appName);
                            $('#inputTitleEditIoTApp').val(appName);
                            $('#appTypeEditIoTApp').val(appType);
                            $('#createdEditIoTApp').val(appCreated.toLocaleString())
                            
                            $('#modalEditIoTApp').modal('show');
                            $('#modalEditIoTAppConfirmBtn').off('click').click(function() {
                              var appName = $('#inputTitleEditIoTApp').val().trim();
                              var appId = $('#iotAppIdHidden').val();
                              
                              if(appName=='')
                                alert("Please provide a name")
                              else {
                                $.ajax({
                                  url: "../controllers/editIotApplication.php",
                                  data: {"name": appName, "id":appId},
                                  type: "GET",
                                  async: true,
                                  dataType: 'JSON',
                                  cache: false, 
                                  success: function (data) {
                                    if(data.detail=="Ok") {
                                      location.reload();
                                    } else {
                                      alert("Error: "+data.error);
                                    }
                                    console.log(data);
                                    $('#modalAddIoTApp').modal('hide');
                                  },
                                  error: function (error) {
                                    alert("An error occured");
                                    console.log(error);
                                    $('#modalAddIoTApp').modal('hide');
                                  }
                              });
                              }
                            });
                            $('#modalEditIoTApp .deleteBtn').off('click').click(function() {
                              var appId = $('#iotAppIdHidden').val();

                              if(confirm("Do you really want to DELETE the application?")) {
                                $.ajax({
                                  url: "../controllers/deleteIotApplication.php",
                                  data: {"id":appId},
                                  type: "GET",
                                  async: true,
                                  dataType: 'JSON',
                                  cache: false, 
                                  success: function (data) {
                                    if(data.detail=="Ok") {
                                      location.reload();
                                    } else {
                                      alert("Error: "+data.error);
                                    }
                                    console.log(data);
                                    $('#modalAddIoTApp').modal('hide');
                                  },
                                  error: function (error) {
                                    alert("An error occured");
                                    console.log(error);
                                    $('#modalAddIoTApp').modal('hide');
                                  }
                              });
                              }
                            });
                            $('#modalEditIoTApp .restartBtn').off('click').click(function() {
                              var appId = $('#iotAppIdHidden').val();

                              if(confirm("Do you really want to RESTART the application?")) {
                                $.ajax({
                                  url: "../controllers/restartIotApplication.php",
                                  data: {"id":appId},
                                  type: "GET",
                                  async: true,
                                  dataType: 'JSON',
                                  cache: false, 
                                  success: function (data) {
                                    if(data.detail=="Ok") {
                                      location.reload();
                                    } else {
                                      alert("Error: "+data.error);
                                    }
                                    console.log(data);
                                    $('#modalAddIoTApp').modal('hide');
                                  },
                                  error: function (error) {
                                    alert("An error occured");
                                    console.log(error);
                                    $('#modalAddIoTApp').modal('hide');
                                  }
                              });
                              }
                            });
                        });
                        $('#newOwner').val('').off('input').on('input',function(e)
                        {
                            if($(this).val().trim() === '')
                            {
                                $('#newOwnerMsg').css('color', '#f3cf58');
                                $('#newOwnerMsg').html('New owner username can\'t be empty');
                                $('#newOwnershipConfirmBtn').addClass('disabled');
                            }
                            else
                            {
                                if(($(this).val().trim() === "<?= $_SESSION['loggedUsername'] ?>")&&("<?= $_SESSION['loggedRole'] ?>" !== "RootAdmin"))
                                {
                                    $('#newOwnerMsg').css('color', '#f3cf58');
                                    $('#newOwnerMsg').html('New owner can\'t be you');
                                    $('#newOwnershipConfirmBtn').addClass('disabled');
                                }
                                else
                                {
                                    $('#newOwnerMsg').css('color', 'white');
                                    $('#newOwnerMsg').html('User can be new owner');
                                    $('#newOwnershipConfirmBtn').removeClass('disabled');
                                }
                            }
                        });                        
                        $('#newOwnershipConfirmBtn').off('click').click(function(){
                            $.ajax({
                                url: "../controllers/changeIotApplicationOwnership.php",
                                data: 
                                {
                                    appId: $('#iotAppIdHidden').val(),
                                    newOwner: $('#newOwner').val(),
                                    appName: $('#iotAppNameHidden').val(),
                                },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function(data) {
                                    if(data.detail === 'Ok') {
                                        $('#newOwner').val('');
                                        $('#newOwner').addClass('disabled');
                                        $('#newOwnershipResultMsg').show();
                                        $('#newOwnershipResultMsg').html('New ownership set correctly');
                                        $('#newOwnershipConfirmBtn').addClass('disabled');
                                        
                                        setTimeout(function() {
                                            location.reload();
                                        }, 1250);
                                    } else {
                                        $('#newOwner').addClass('disabled');
                                        $('#newOwnershipResultMsg').show();
                                        $('#newOwnershipResultMsg').html('Error setting new ownership: please try again');
                                        $('#newOwnershipConfirmBtn').addClass('disabled');                                        
                                        setTimeout(function() {
                                            $('#newOwner').removeClass('disabled');
                                            $('#newOwnershipResultMsg').html('');
                                            $('#newOwnershipResultMsg').hide();
                                        }, 5000);
                                    }
                                },
                                error: function(errorData) {
                                    console.log(errorData);
                                    $('#newOwner').addClass('disabled');
                                    $('#newOwnershipResultMsg').show();
                                    $('#newOwnershipResultMsg').html('Error setting new ownership: please try again');
                                    $('#newOwnershipConfirmBtn').addClass('disabled');

                                    setTimeout(function() {
                                        $('#newOwner').removeClass('disabled');
                                        $('#newOwnershipResultMsg').html('');
                                        $('#newOwnershipResultMsg').hide();
                                    }, 5000);
                                }
                            });
                        });
                                                
                        $("#dynatable-pagination-links-list_dashboard_cards").appendTo("#dashboardListsPages div.iotAppsListMenuItemContent");
                        //$("#dynatable-pagination-links-list_dashboard_cards li").eq(0).remove();
                        $("#dynatable-pagination-links-list_dashboard_cards li").eq(0).remove();
                        //$("#dynatable-pagination-links-list_dashboard_cards li").eq($("#dynatable-pagination-links-list_dashboard_cards li").length - 1).remove();
                        $('#dashboardListsPages div.iotAppsListMenuItemContent').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent').css("font-weight", "bold");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent').css("color", "white");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent a').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent a').css("font-weight", "bold");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent a').css("color", "white");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("-webkit-padding-start", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("-webkit-margin-before", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("-webkit-margin-after", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("padding", "0px");
                        
                        $("#dynatable-query-search-list_dashboard_cards").prependTo("#dashboardListsSearchFilter div.iotAppsListMenuItemContent div.input-group");
                        $('#dynatable-search-list_dashboard_cards').remove();
                        $("#dynatable-query-search-list_dashboard_cards").css("border", "none");
                        $("#dynatable-query-search-list_dashboard_cards").attr("placeholder", "Filter");
                        $("#dynatable-query-search-list_dashboard_cards").css("width", "100%");
                        $("#dynatable-query-search-list_dashboard_cards").addClass("form-control");
                        
                        $('#list_dashboard_cards div.iotAppsListCardDiv').each(function(i){
                            $(this).find('div.iotAppsListCardImgDiv').css("background-image", "url(../img/iotApplications/" + $(this).attr('data-icon'));
                            $(this).find('div.iotAppsListCardImgDiv').css("background-size", "contain");
                            $(this).find('div.iotAppsListCardImgDiv').css("background-repeat", "no-repeat");
                            $(this).find('div.iotAppsListCardImgDiv').css("background-position", "center center");
                            $(this).find('div.iotAppsListCardInnerDiv').css("width", "100%");
                            $(this).find('div.iotAppsListCardInnerDiv').css("height", $(this).height() + "px");
                            $(this).find('div.iotAppsListCardOverlayDiv').css("height", $(this).find('div.iotAppsListCardImgDiv').height() + "px");
                            $(this).find('.iotAppsListCardOverlayTxt').css("height", $(this).find('div.iotAppsListCardImgDiv').height() + "px");
                            
                            $(this).find('.iotAppsListCardImgDiv').off('mouseenter');
                            $(this).find('.iotAppsListCardImgDiv').off('mouseleave');
                            
                            $(this).find('.iotAppsListCardOverlayTxt').hover(function(){
                                $(this).parents('.iotAppsListCardDiv').find('.iotAppsListCardOverlayTxt').css("opacity", "1");
                                $(this).parents('.iotAppsListCardDiv').find('div.iotAppsListCardOverlayDiv').css("opacity", "0.8");
                                $(this).css("cursor", "pointer");
                                var created = $(this).parents('div.iotAppsListCardDiv').attr('data-created');
                                var id = $(this).parents('div.iotAppsListCardDiv').attr('data-uniqueid');
                                if(iotAppsHealthiness[id].healthiness===false) {
                                  $(this).text("please wait...")
                                  //$(this).addClass("wait");
                                } else {
                                  $(this).text("Open")
                                }
                            }, function(){
                                //$(this).parents('.iotAppsListCardDiv').find('div.iotAppsListCardOverlayTxt').css("opacity", "0");
                                $(this).css("cursor", "normal");
                                var created = $(this).parents('div.iotAppsListCardDiv').attr('data-created');
                                var id = $(this).parents('div.iotAppsListCardDiv').attr('data-uniqueid');
                                if(iotAppsHealthiness[id].healthiness ===false) {
                                  $(this).parents('.iotAppsListCardDiv').find('div.iotAppsListCardOverlayDiv').css("opacity", "0.8");
                                  //$(this).addClass("wait");
                                } else {
                                  $(this).parents('.iotAppsListCardDiv').find('div.iotAppsListCardOverlayDiv').css("opacity", "0.05");
                                  $(this).text("")
                                  //$(this).removeClass("wait");
                                }
                            });
                            
                            $(this).find('.iotAppsListCardOverlayTxt').off('click').click(function() 
                            {
                                var url = $(this).parents('div.iotAppsListCardDiv').attr('data-url');
                                var created = $(this).parents('div.iotAppsListCardDiv').attr('data-created');
                                var title = $(this).parents('div.iotAppsListCardDiv').attr('data-title');
                                var id = $(this).parents('div.iotAppsListCardDiv').attr('data-uniqueid');
                                var edgetype = $(this).parents('div.iotAppsListCardDiv').attr('data-edgetype');
                                var type = $(this).parents('div.iotAppsListCardDiv').attr('data-type');
                                var iotapps = $(this).parents('div.iotAppsListCardDiv').attr('data-iotapps');
                                if(iotAppsHealthiness[id].healthiness===false) {
                                  $(this).text("please wait...")
                                } else {
                                  $(this).text("Open")
                                  var c = true
                                  if(edgetype) {
                                    c = confirm("This EDGE application can be accessible only in the local network at address "+url+"\nDo you want to open it?");
                                  } else if(type=="plumber") {
                                    c=false;
                                    alert("This process of Data Analytic is:\n  exploited by IOT Applications: "+iotapps+"\n  named as Block: "+title+"\n  created: "+created)
                                  }
                                  if(c) {
                                    $('#headerTitleCnt').text(title);
                                    $('#iotAppsListTableRow').hide();
                                    $('#iotApplicationsIframeRow').show();
                                    $('#mainContentCnt').css('padding', '0px 0px 0px 0px');
                                    $('#iotApplicationsIframeCnt').css('padding-left', '0px');
                                    $('#iotApplicationsIframeCnt').css('padding-right', '0px');
                                    $('#iotApplicationsIframe').attr('src', url);
                                  }
                                }
                            });
                        });
                        
                        $('#dashboardListsViewMode').hide();
                        
                        $('#searchDashboardBtn').off('click');
                        $('#searchDashboardBtn').click(function(){
                            var dynatable = $('#list_dashboard_cards').data('dynatable');
                            dynatable.queries.run();
                        }); 
                        
                        $('#resetSearchDashboardBtn').off('click');
                        $('#resetSearchDashboardBtn').click(function(){
                            var dynatable = $('#list_dashboard_cards').data('dynatable');
                            $("#dynatable-query-search-list_dashboard_cards").val("");
                            dynatable.queries.runSearch("");
                        }); 
                        
                      });
                                        
                    $('#list_dashboard_cards').dynatable({
                        table: {
                            bodyRowSelector: 'div'
                          },
                        dataset: {
                          records: data.applications,
                          perPageDefault: 12,
                          perPageOptions: [4, 8, 12]
                        },
                        writers: {
                            _rowWriter: myCardsWriter
                        },
                        inputs: {
                            paginationLinkPlacement: 'before'
                        },
                        features: {
                            recordCount: false,
                            perPageSelect: false,
                            search: true
                        }
                      });
                      
                      var dynatable = $('#list_dashboard_cards').data('dynatable');
                      dynatable.sorts.clear();
                      dynatable.sorts.add('title', 1); // 1=ASCENDING, -1=DESCENDING
                      dynatable.process();
                      
                      $('#dashboardListsCardsSort div.iotAppsListSortBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                      $('#dashboardListsCardsSort i.iotAppsListSort').eq(0).click(function(){
                          var dynatable = $('#list_dashboard_cards').data('dynatable');
                          dynatable.sorts.clear();
                          dynatable.sorts.add('title', 1); // 1=ASCENDING, -1=DESCENDING
                          dynatable.process();
                          $('#dashboardListsCardsSort div.iotAppsListSortBtnCnt').eq(1).css('background-color', 'rgba(0, 162, 211, 1)');
                          $('#dashboardListsCardsSort div.iotAppsListSortBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                      });
                      
                      $('#dashboardListsCardsSort i.iotAppsListSort').eq(1).click(function(){
                          var dynatable = $('#list_dashboard_cards').data('dynatable');
                          dynatable.sorts.clear();
                          dynatable.sorts.add('title', -1); // 1=ASCENDING, -1=DESCENDING
                          dynatable.process();
                          $('#dashboardListsCardsSort div.iotAppsListSortBtnCnt').eq(0).css('background-color', 'rgba(0, 162, 211, 1)');
                          $('#dashboardListsCardsSort div.iotAppsListSortBtnCnt').eq(1).css('background-color', 'rgba(255, 204, 0, 1)');
                      });
                    
                    $('#list_dashboard').bind('dynatable:afterProcess', function(e, dynatable){
                        $('span.dynatable-per-page-label').remove();
                        
                        //$('#dynatable-per-page-list_dashboard').parents('span.dynatable-per-page').appendTo("#dashboardListsItemsPerPage div.iotAppsListMenuItemContent");
                        //$('#dynatable-per-page-list_dashboard').addClass('form-control');
                        
                        $("#dynatable-pagination-links-list_dashboard").appendTo("#dashboardListsPages div.iotAppsListMenuItemContent");
                        $("#dynatable-pagination-links-list_dashboard li").eq(0).remove();
                        //$("#dynatable-pagination-links-list_dashboard li").eq(0).remove();
                        //$("#dynatable-pagination-links-list_dashboard li").eq($("#dynatable-pagination-links-list_dashboard li").length - 1).remove();
                        $('#dashboardListsPages div.iotAppsListMenuItemContent').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent').css("font-weight", "bold");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent').css("color", "white");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent a').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent a').css("font-weight", "bold");
                        $('#dashboardListsPages div.iotAppsListMenuItemContent a').css("color", "white");
                        $("ul#dynatable-pagination-links-list_dashboard").css("-webkit-padding-start", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard").css("-webkit-margin-before", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard").css("-webkit-margin-after", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard").css("padding", "0px");
                        
                        $("#dynatable-query-search-list_dashboard").prependTo("#dashboardListsSearchFilter div.iotAppsListMenuItemContent div.input-group");
                        $('#dynatable-search-list_dashboard').remove();
                        $("#dynatable-query-search-list_dashboard").css("border", "none");
                        $("#dynatable-query-search-list_dashboard").attr("placeholder", "Filter by dashboard title, author...");
                        $("#dynatable-query-search-list_dashboard").css("width", "100%");
                        $("#dynatable-query-search-list_dashboard").addClass("form-control");
                        
                        $('#list_dashboard input.changeDashboardStatus').bootstrapToggle({
                            on: "On",
                            off: "Off",
                            onstyle: "primary",
                            offstyle: "default",
                            size: "mini"
                        });
                                                
                        $('#list_dashboard button.editDashBtn').off('click');
                        $('#list_dashboard button.editDashBtn').click(function() 
                        {
                            var dashboardId = $(this).parents('tr').attr('data-uniqueid');
                            var dashboardTitle = $(this).parents('tr').attr('data-dashTitle');
                            var dashboardAuthorName = $(this).parents('tr').attr('data-authorName');
                            alert('edit ');
                        });
                        
                    });
                    
                    
                      $('#list_dashboard').dynatable({
                        dataset: {
                          records: data,
                          perPageDefault: 20,
                          perPageOptions: [5, 10, 20, 30, 40]
                        },
                        writers: {
                            _rowWriter: myRowWriter
                        },
                        features: {
                            recordCount: false,
                            perPageSelect: false
                        },
                        inputs: {
                            perPagePlacement: 'after'
                        }
                      });
                      $("#dynatable-pagination-links-list_dashboard").hide();
                      $("#dynatable-query-search-list_dashboard").hide();
                      
                      /*$('#dynatable-per-page-list_dashboard option').each(function(i){
                            $(this).text($(this).text() + " rows");
                        });*/
                },
                error: function(errorData)
                {
                  console.log(errorData);
                }
            });            
    });
</script>  
