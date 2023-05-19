<?php

/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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

    /* if(!isset($_SESSION)) {
       session_start();
    }   */
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);
    
    checkSession('Public');
?>

<!DOCTYPE html>
<html class="dark">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php include "mobMainMenuClaim.php" ?></title>
		
		<script type="text/javascript">
			const setTheme = (theme) => {
			document.documentElement.className = theme;
			localStorage.setItem('theme', theme);
			}
			const getTheme = () => {
			const theme = localStorage.getItem('theme');
			theme && setTheme(theme);
			}
			getTheme();
		</script>

        <!-- Bootstrap Core CSS -->
		<link href="../css/s4c-css/bootstrap/bootstrap.css" rel="stylesheet">
		<link href="../css/s4c-css/bootstrap/bootstrap-colorpicker.min.css" rel="stylesheet">

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
	   <link rel="stylesheet" href="../css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">
	   
        <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        
        <!-- Custom CSS -->
	   <link href="../css/s4c-css/s4c-dashboard.css?v=<?php echo time();?>" rel="stylesheet">
	   <link href="../css/s4c-css/s4c-dashboardList.css?v=<?php echo time();?>" rel="stylesheet">
	   <link href="../css/s4c-css/s4c-dashboardView.css?v=<?php echo time();?>" rel="stylesheet">
	   <link href="../css/s4c-css/s4c-addWidgetWizard2.css?v=<?php echo time();?>" rel="stylesheet">
	   <link href="../css/s4c-css/s4c-addDashboardTab.css?v=<?php echo time();?>" rel="stylesheet">
	   <link href="../css/s4c-css/s4c-dashboard_configdash.css?v=<?php echo time();?>" rel="stylesheet">
	   <link href="../css/s4c-css/s4c-iotApplications.css?v=a" rel="stylesheet">
        
        <!-- Custom scripts -->
        <script type="text/javascript" src="../js/dashboard_mng.js"></script>
        
        <!--<link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">-->
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
                        <div id="headerTitleCnt">
                            <script type="text/javascript">
                                <?php
                                if(isset($_GET['pageTitle']))
                                {?>
                                    document.write("<?php echo escapeForJS($_GET['pageTitle']); ?>");
                                <?php
                                }
                                ?>
                            </script>
                        </div>
						<div class="user-menu-container">
						  <?php include "loginPanel.php" ?>
						</div>
                        <div class="col-lg-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt">
                            <div class="row mainContentRow" id="iotApplicationsIframeRow">
                                <div class="col-xs-12 mainContentCellCnt" id="iotApplicationsIframeCnt">
                                    <iframe id="iotApplicationsIframe" allow="geolocation"></iframe>
                                </div>
                            </div>    
                            
                            
                            <div class="row mainContentRow" id="dashboardsListTableRow">
                                <div class="col-xs-12 mainContentCellCnt" >
									<div class="filterListBar">
										<div id="dashboardListsNewDashboard" class="dashboardsListMenuItem">
											<!--<div class="dashboardsListMenuItemTitle centerWithFlex col-xs-4">
												New<br>dashboard
											</div>-->
											<div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
												<button id="link_start_wizard" type="button" class="btn btn-new-dash"><?= _("New template")?></button>
											</div>
										</div>
										<button type="button" class="collapsible"><span></span></button>
										<div class="content">
                                    <div id="dashboardsListMenu" class="row">
                                        <!--<div id="dashboardListsViewMode" class="hidden-xs col-sm-6 col-md-2 dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                <input id="dashboardListsViewModeInput" type="checkbox">
                                            </div>
                                        </div>-->
                                        <div id="dashboardListsCardsSort" class="dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                <div class="col-xs-6 centerWithFlex">
                                                    <div class="dashboardsListSortBtnCnt">
                                                        <i class="fa fa-sort-alpha-asc dashboardsListSort"></i>
                                                    </div> 
                                                </div>
                                                <div class="col-xs-6 centerWithFlex">
                                                    <div class="dashboardsListSortBtnCnt">
                                                        <i class="fa fa-sort-alpha-desc dashboardsListSort"></i>
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if((@$_SESSION['loggedRole']) === 'RootAdmin')
                                        {
                                        ?>
                                        <div id="dashboardListsCardsOrgsSort" class="dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12 col-md-6">
                                                <div class="col-xs-6 centerWithFlex">
                                                    <script type="text/javascript">
                                                        if(location.href.includes("AllOrgs") != false) {
                                                            document.write('<div id="extServiceList" class="dashboardsListSortOrgsBtnCnt" data-toggle="tooltip" data-placement="bottom" title="All Organizations"></div>');
                                                        } else {
                                                            document.write('<div id="extServiceList" class="dashboardsListSortOrgsBtnCnt" data-toggle="tooltip" data-placement="bottom" title="My Organizations"></div>');
                                                        }
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div id="dashboardListsPages" class="dashboardsListMenuItem">
                                           <!--<div class="dashboardsListMenuItemTitle centerWithFlex col-xs-4">
                                                List<br>pages
                                            </div>-->
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                
                                            </div>
                                        </div>
                                        
                                        <div id="dashboardListsSearchFilter" class="dashboardsListMenuItem">
                                            <!--<div class="dashboardsListMenuItemTitle centerWithFlex col-xs-3">
                                                Search
                                            </div>-->
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                <div class="input-group">
                                                    <div class="input-group-btn">
                                                      <button type="button" id="searchDashboardBtn" class="btn"><i class="fa fa-search"></i></button>
                                                      <button type="button" id="resetSearchDashboardBtn" class="btn"><i class="fa fa-close"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
										
                                    </div>
									</div>
									</div>                                    
                                    
                                    <table id="list_dashboard" class="table">
                                        <thead class="dashboardsTableHeader">
                                            <tr>
                                                <th data-dynatable-column="title_header"><?= _("Title")?></th>
                                                <th data-dynatable-column="user"><?= _("Creator")?></th>
                                                <th data-dynatable-column="creation_date"><?= _("Creation date")?></th>
                                                <th data-dynatable-column="last_edit_date"><?= _("Last edit date")?></th>
                                                <th data-dynatable-column="status_dashboard"><?= _("Status")?></th>
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

<?php if(!$_SESSION["isPublic"]) { ?>

		<div class="modal fade" id="delegationsModal" tabindex="-1" role="dialog" aria-labelledby="modalAddWidgetTypeLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  <?= _("Management")?>
                </div>
                <form id="delegationsForm" class="form-horizontal" name="delegationsForm" role="form" method="post" action="" data-toggle="validator">
                    <div id="delegationsModalBody" class="modal-body modalBody">
                        <!-- Tabs -->
                        <ul id="delegationsTabsContainer" class="nav nav-tabs nav-justified">
                            <li id="ownershipTab" class="active"><a data-toggle="tab" href="#ownershipCnt" class="dashboardWizardTabTxt"><?= _("Ownership")?></a></li>
                            <li id="visibilityTab"><a data-toggle="tab" href="#visibilityCnt" class="dashboardWizardTabTxt"><?= _("Visibility")?></a></li>
                            <li id="delegationsTab"><a data-toggle="tab" href="#delegationsCnt" class="dashboardWizardTabTxt"><?= _("Delegations")?></a></li>
                            <!-- GP COMMENT TEMPORARY -->
                            <li id="groupDelegationsTab"><a data-toggle="tab" href="#groupDelegationsCnt" class="dashboardWizardTabTxt"><?= _("Group Delegations")?></a></li>
                        </ul> 
                        <!-- Fine tabs -->
                        
                        <div class="modal_wrapper">
                        <div id="delegationsModalLeftCnt" class="col-xs-12 col-sm-4">
                            <div class="col-xs-12 centerWithFlex delegationsModalTxt modalFirstLbl" id="delegationsDashboardTitle">
                            </div>
                            
                            <div id="delegationsDashPic" class="modalDelObjName col-xs-12 centerWithFlex"></div>
                        </div><!-- Fine delegationsModalLeftCnt-->    
                        
                        <div id="delegationsModalRightCnt" class="col-xs-12 col-sm-7 col-sm-offset-1">
                            <!-- Tab content -->
                            <div class="tab-content">
                                <!-- Ownership cnt -->
                                <div id="ownershipCnt" class="tab-pane fade in active">
                                    <div class="row" id="ownershipFormRow">
                                        <div class="col-xs-12 delegationsModalLbl modalFirstLbl" id="changeOwnershipLbl">
                                            <?= _("Change ownership")?>
                                        </div>
                                        <div class="col-xs-12" id="newOwnershipCnt">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="newOwner" placeholder="New owner username">
                                                <span class="input-group-btn">
                                                  <button type="button" id="newOwnershipConfirmBtn" class="btn confirmBtn disabled"><?= _("Confirm")?></button>
                                                </span>
                                            </div>
                                            <div class="col-xs-12 delegationsModalMsg" id="newOwnerMsg">
                                                <?= _("New owner username can't be empty")?>
                                            </div>    
                                        </div>
                                        <div class="col-xs-12 centerWithFlex" id="newOwnershipResultMsg">
                                            
                                        </div>    
                                    </div>    
                                </div>
                                <!-- Fine ownership cnt -->
                                
                                
                                <!-- Visibility cnt -->
                                <div id="visibilityCnt" class="tab-pane fade in">
                                    <div class="row" id="visibilityFormRow">
                                        <div class="col-xs-12 delegationsModalLbl modalFirstLbl" id="changeOwnershipLbl">
                                            <?= _("Change visibility")?>
                                        </div>
                                        <div class="col-xs-12" id="newVisibilityCnt">
                                            <div class="input-group">
                                                <select id="newVisibility" class="form-control">
                                                    <option value="public"><?= _("Public")?></option>
                                                    <option value="private"><?= _("Private")?></option>
                                                </select>
                                                <span class="input-group-btn">
                                                  <button type="button" id="newVisibilityConfirmBtn" class="btn confirmBtn"><?= _("Confirm")?></button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 centerWithFlex" id="newVisibilityResultMsg">
                                            
                                        </div>  
                                    </div>    
                                </div>
                                <!-- Fine visibility cnt -->
                                
                                <!-- Delegations cnt -->
                                <div id="delegationsCnt" class="tab-pane fade in">
                                    <div class="row centerWithFlex modalFirstLbl" id="delegationsNotAvailableRow">
                                        <?= _("Delegations are not possibile on a public template")?>
                                    </div>
                                    <div class="row" id="delegationsFormRow">
                                        <div class="col-xs-12 centerWithFlex modalFirstLbl" id="newDelegationLbl">
                                           <?= _("Add new delegation")?> 
                                        </div>
                                        <div class="col-xs-12" id="newDelegationCnt">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="newDelegation" placeholder="Delegated username">
                                                <span class="input-group-btn">
                                                  <button type="button" id="newDelegationConfirmBtn" class="btn confirmBtn disabled"><?= _("Confirm")?></button>
                                                </span>
                                            </div>
                                            <div class="col-xs-12 delegationsModalMsg" id="newDelegatedMsg">
                                                <?= _("Delegated username can't be empty")?>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 centerWithFlex" id="currentDelegationsLbl">
                                            <?= _("Current user delegations")?>
                                        </div>
                                        <div class="col-xs-12" id="delegationsTableCnt">
                                            <table id="delegationsTable">
                                                <thead>
                                                  <th><?= _("Delegated user")?></th>
                                                  <th><?= _("Remove")?></th>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Fine delegations cnt -->

                                <!-- Group Delegations cnt -->
                                <div id="groupDelegationsCnt" class="tab-pane fade in">
                                    <div class="row centerWithFlex modalFirstLbl" id="groupDelegationsNotAvailableRow">
                                        <?= _("Delegations are not possibile on a public template")?>
                                    </div>
                                    <div class="row" id="groupDelegationsFormRow">
                                        <div class="col-xs-12 centerWithFlex modalFirstLbl" id="newDelegationLbl">
                                            <?= _("Add new group delegation")?>
                                        </div>
                                        <div class="col-xs-12" id="newGroupDelegationCnt">
                                            <div class="col-xs-4">
                                                <select name="newDelegationOrganization" id="newDelegationOrganization" class="modalInputTxt"></select>
                                                  <!--  <option value="Antwerp">Antwerp</option></select>   -->
                                            </div>
                                            <div class="col-xs-4">
                                                <select name="newDelegationGroup" id="newDelegationGroup" class="modalInputTxt"></select>
                                            </div>
                                            <div class="col-xs-4">
                                                <span class="input-group-btn">
                                                    <button type="button" id="newGroupDelegationConfirmBtn" class="btn confirmBtn"><?= _("Confirm")?></button>
                                                </span>
                                            </div>
                                        <!--    <div class="input-group">
                                                <input type="text" class="form-control" id="newGroupDelegation" placeholder="Delegated group">
                                                <span class="input-group-btn">
                                                  <button type="button" id="newGroupDelegationConfirmBtn" class="btn confirmBtn disabled">Confirm</button>
                                                </span>
                                            </div>  -->
                                            <div class="col-xs-12 delegationsModalMsg" id="newGroupDelegatedMsg">
                                               <!-- Delegated group/organization name can't be empty    -->
                                            </div>
                                        </div>

                                        <div class="col-xs-12 centerWithFlex" id="currentGroupDelegationsLbl">
                                            <?= _("Current group delegations")?>
                                        </div>
                                        <div class="col-xs-12" id="groupDelegationsTableCnt">
                                            <table id="groupDelegationsTable">
                                                <thead>
                                                  <th><?= _("Delegated group")?></th>
                                                  <th><?= _("Remove")?></th>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Fine Group delegations cnt -->

                            </div>    
                            <!-- Fine tab content -->
                            <input type="hidden" id="delegationsDashId">
                        </div><!-- Fine delegationsModalRightCnt-->
						</div>
					</div>
                    <div id="delegationsModalFooter" class="modal-footer">
                      <button type="button" id="delegationsCancelBtn" class="btn cancelBtn" data-dismiss="modal" style="margin-top: 50px"><?= _("Close")?></button>
                    </div>
                </form>    
              </div>
            </div>
        </div>		
        <!-- Fine modale gestione deleghe dashboard -->
		
		<!-- Modale cancellazione dashboard -->
        <div class="modal fade" id="modalDelDash" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                  <?= _("Template deletion")?>
                </div>
                <input type="hidden" id="dashIdDelHidden" name="dashIdDelHidden" />
                <div id="delDashModalBody" class="modal-body modalBody">
                    <div class="row">
                        <div id="delDashNameMsg" class="col-xs-12 modalCell">
                            <div class="modalDelMsg col-xs-12 centerWithFlex">
                                <?= _("Do you want to delete the following template?")?>
                            </div>
                            <div id="dashToDelName" class="modalDelObjName col-xs-12 centerWithFlex"></div>
                            <div id="dashToDelPic" class="modalDelObjName col-xs-12 centerWithFlex"></div>
                        </div>
                    </div>
                    <div class="row" id="delDashRunningMsg">
                        <div class="col-xs-12 modalCell">
                            <div class="col-xs-12 centerWithFlex modalDelMsg"><?= _("Deleting template, please wait")?></div>
                            <div class="col-xs-12 centerWithFlex modalDelObjName"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px"></i></div>
                        </div>
                    </div>
                    <div class="row" id="delDashOkMsg">
                        <div class="col-xs-12 modalCell">
                            <div class="col-xs-12 centerWithFlex modalDelMsg"><?= _("Template deleted successfully")?></div>
                            <div class="col-xs-12 centerWithFlex modalDelObjName"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                        </div>
                    </div>
                    <div class="row" id="delDashKoMsg">
                        <div class="col-xs-12 modalCell">
                            <div id="delDashKoMsgTxt" class="col-xs-12 centerWithFlex modalDelMsg"><?= _("Error deleting template, please try again")?></div>
                            <div class="col-xs-12 centerWithFlex modalDelObjName"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                        </div>
                    </div>
                </div>
                <div id="delDashModalFooter" class="modal-footer">
                  <button type="button" id="delDashCancelBtn" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel")?></button>
                  <button type="button" id="delDashConfirmBtn" class="btn confirmBtn internalLink"><?= _("Confirm")?></button>
                </div>
              </div>
            </div>
        </div>
        <!-- Fine modale cancellazione dashboard -->    

<?php } ?>		

    </body>
</html>

<script type='text/javascript'>
    $(document).ready(function () 
    {
		
		var dashboardsList = null;
        var orgFilter = "<?php echo @$_SESSION['loggedOrganization']; ?>";
        var param = "";
        if (location.href.includes("AllOrgs")) {
            param = "AllOrgs";
        }
        var loggedRole = "<?php echo @$_SESSION['loggedRole']; ?>";
        var sessionEndTime = "<?php echo @$_SESSION['sessionEndTime']; ?>";
        $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
        $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");

        if(location.href.includes("AllOrgs") != false) {
            //   documnet.write("<i class="fa fa-cubes dashboardsListSort" data-active="false" ></i>");
            var divList = document.getElementById('extServiceList');
            var strToAppend = "<i class=\"fa fa-cubes dashboardsListSort\" data-active=\"false\" ></i>";
            //     divList.innerHtml(strToAppend);
            divList.insertAdjacentHTML('beforeend', strToAppend);
        } else  if (loggedRole == "RootAdmin") {
            var divList = document.getElementById('extServiceList');
            var strToAppend = "<i class=\"fa fa-cube dashboardsListSort\" data-active=\"false\" ></i>";
            //     divList.innerHtml(strToAppend);
            divList.insertAdjacentHTML('beforeend', strToAppend);
        }

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
        
        $('#mainMenuCnt .mainMenuLink[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>] div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt .mainMenuLink[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt .mainMenuLink[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        if($('div.mainMenuSubItemCnt').parents('a[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>]').length > 0)
        {
            var fatherMenuId = $('div.mainMenuSubItemCnt').parents('a[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>]').attr('data-fathermenuid');
            $("#" + fatherMenuId).attr('data-submenuVisible', 'true');
            $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + fatherMenuId + ']').show();
            $("#" + fatherMenuId).find('.submenuIndicator').removeClass('fa-caret-down');
            $("#" + fatherMenuId).find('.submenuIndicator').addClass('fa-caret-up');
            $('div.mainMenuSubItemCnt').parents('a[id=<?= escapeForJS(sanitizeGetString('linkId')) ?>]').find('div.mainMenuSubItemCnt').addClass("subMenuItemCntActive");
        }
        
        var loggedRole = "<?= ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']) ?>";
        var loggedType = "<?= @$_SESSION['loggedType'] ?: '' ?>";
        var usr = "<?= @$_SESSION['loggedUsername'] ?: '' ?>";
        var tableFirstLoad = true;
            
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

            var newRow = '<tr data-dashTitle="' + record.title_header + '" data-uniqueid="' + record.Id + '" data-authorName="' + record.user + '"><td class="' + cssClass + '" style="font-weight: bold">' + title + '</td><td class="' + cssClass + '">' + user + '</td><td class="' + cssClass + '">' + record.creation_date + '</td><td class="' + cssClass + '">' + record.last_edit_date + '</td><td class="' + cssClass + '">' + statusBtn + '</td><td class="' + cssClass + '"><button type="button" class="editDashBtn">edit</button></td><td class="' + cssClass + '"><button type="button" class="viewDashBtn">view</button></td></tr>';

            return newRow;
        }
    
        function myCardsWriter(rowIndex, record, columns, cellWriter)
        {
            var title = record.unique_name_id;

            if(title.length > 100)
            {
               title = title.substr(0, 100) + " ...";
            }

             var cardDiv = '<div data-uniqueid="' + record.id + '" data-title="' + title + '" data-url="' + record.parameters + '" data-icon="' + record.microAppExtServIcon + '" data-nature="'+record.nature+'" data-subnature="'+record.sub_nature+'" class="dashboardsListCardDiv col-xs-12 col-sm-6">' + 
			   '<div class="dashboardsListCardInnerDiv">' +
			   '<div class="dashboardsListCardOverlayDiv"></div>' +
				 '<div class="dashboardsListCardOverlayTxt"><i class="fa-solid fa-eye"></i><?= _("View")?></div>' +
				 '<div class="dashboardsListCardImgDiv" style="background-color:white;"></div>' + 
				  '<div class="cardLinkBtn"><button class="cardButton" style="font-size:8px;float: right;">New Tab</button></div>' +
			   //   '<div id="cardLinkBtn" style="font-size:8px;float: right;">New Tab</div>' +
				  '<div class="dashboardsListCardTitleDiv"><span class="dashboardListCardTitleSpan">' + title + '</span><span class="dashboardListCardTypeSpan" data-hasIotModal="true">' + record.high_level_type + " (" + record.nature + ': ' + record.sub_nature + ')</span></div>' +
				  
				  '<div class="dashboardsListCardVisibilityDiv">' + ( record.ownership == 'private' ? (record.user?(record.user == usr?'My own: Private':record.user+': Private'):'Private') + ' (' + record.organizations + ')' : (record.user?(record.user==usr?'My own: Public':record.user+': Public'):'Public')+ ' (' + record.organizations + ')' ) + '</div>' +
				  '<div class="dashboardsListCardClick2EditDiv">' + 
					  ( record.ownership == 'private' && ( usr == record.user || loggedRole == 'RootAdmin' ) ? '<span tooltip="<?= _("Edit")?>"><button type="button" class="dashBtnCard updSynTplBtnCard"><i class="fa-solid fa-pen"></i></button></span>' : '' ) + 
					  ( usr == record.user || loggedRole == 'RootAdmin' ? '<span tooltip="<?= _("Management")?>"><button type="button" class="dashBtnCard mgmtDashBtnCard"><i class="fa-solid fa-gear"></i></button></span>' : '') + 
					   '<span tooltip="<?= _("Instantiate")?>"><button type="button" class="dashBtnCard instSynTplBtnCard"><i class="fa-solid fa-arrows-split-up-and-left"></i></button></span>' + 	
					   ( false && record.ownership == 'private' && usr == record.user ?	'<span tooltip="<?= _("Delegate")?>"><button type="button" class="dashBtnCard delegateSynTplBtnCard"><i class="fa-solid fa-right-from-bracket"></i></button></span>' : '' ) + 
					   ( false && usr == record.user ?	'<span tooltip="<?= _("Chg Owner")?>"><button type="button" class="dashBtnCard chownSynTplBtnCard"  style="white-space:nowrap;"><i class="fa-solid fa-shuffle"></i></button></span>' : '' ) + 
					  ( false && record.ownership == 'private' && usr == record.user ? '<span tooltip="<?= _("Make Public")?>"><button type="button" class="dashBtnCard publSynTplBtnCard" style="white-space:nowrap;"><i class="fa-solid fa-globe"></i></button></span>' : '' ) + 
					  ( record.ownership == 'private' && ( usr == record.user || loggedRole == 'RootAdmin' ) ? '<span tooltip="<?= _("Delete")?>"><button type="button" class="dashBtnCard delSynTplBtnCard"><i class="fa-solid fa-trash"></i></button></span>' : '' ) + 
					  ( false && record.ownership == 'public' && usr == record.user ? '<span tooltip="<?= _("Make Private")?>"><button type="button" class="dashBtnCard mkPvtSynTplBtnCard" style="white-space:nowrap;"><i class="fa-solid fa-lock"></i></button></span>' : '' ) + 
				  '</div>' +  
			   '</div>' +
			'</div>';   

             return cardDiv;
        }
            
            //Nuova tabella
            $.ajax({
                url: "../controllers/getSynopticTemplates.php",
                data: {
                    orgFilter: "<?= @$_SESSION['loggedOrganization'] ?>",
                    param: param,
                    role: loggedRole
                },
                type: "GET",
                async: true,
                dataType: 'json',
                success: function(data) 
                {
                    dashboardsList = data.applications;
                    //Ricordati di metterlo PRIMA dell'istanziamento della tabella
                    $('#list_dashboard_cards').bind('dynatable:afterProcess', function(e, dynatable){
                        $('#dashboardsListTableRow').css('padding-top', '0px');
                        $('#dashboardsListTableRow').css('padding-bottom', '0px');
                        
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
                        
                        //Ricicliamolo come link CREATE NEW
                        $('#link_add_dashboard').off('click');
                        $('#link_add_dashboard').click(function(){                            
                        });
                        
                        $("#dynatable-pagination-links-list_dashboard_cards").appendTo("#dashboardListsPages div.dashboardsListMenuItemContent");
                        //$("#dynatable-pagination-links-list_dashboard_cards li").eq(0).remove();
                        $("#dynatable-pagination-links-list_dashboard_cards li").eq(0).remove();
                        //$("#dynatable-pagination-links-list_dashboard_cards li").eq($("#dynatable-pagination-links-list_dashboard_cards li").length - 1).remove();
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("font-weight", "bold");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("color", "white");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("font-weight", "bold");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("color", "white");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("-webkit-padding-start", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("-webkit-margin-before", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("-webkit-margin-after", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard_cards").css("padding", "0px");
                        
                        $("#dynatable-query-search-list_dashboard_cards").prependTo("#dashboardListsSearchFilter div.dashboardsListMenuItemContent div.input-group");
                        $('#dynatable-search-list_dashboard_cards').remove();
                        $("#dynatable-query-search-list_dashboard_cards").css("border", "none");
                        $("#dynatable-query-search-list_dashboard_cards").attr("placeholder", "Filter");
                        $("#dynatable-query-search-list_dashboard_cards").css("width", "100%");
                        $("#dynatable-query-search-list_dashboard_cards").addClass("form-control");
                        
                        $('#list_dashboard_cards div.dashboardsListCardDiv').each(function(i){
                            //$(this).find('div.dashboardsListCardImgDiv').css("background-image", "url(../img/synopticTemplates/" + $(this).attr('data-uniqueid') + "/" + $(this).attr('data-icon') + ")");
                            $(this).find('div.dashboardsListCardImgDiv').css("background-image", "url(../img/synopticTemplates/" + $(this).attr('data-icon') + ")");
                            if(!$(this).attr('data-icon').endsWith(".svg")) $(this).find('div.dashboardsListCardImgDiv').css("background-size", "150px");
                            //$(this).find('div.dashboardsListCardImgDiv').css("background-repeat", "no-repeat");
                            //$(this).find('div.dashboardsListCardImgDiv').css("background-position", "center center");
                            $(this).find('div.dashboardsListCardInnerDiv').css("width", "100%");
                            $(this).find('div.dashboardsListCardInnerDiv').css("height", $(this).height() + "px");
                            $(this).find('div.dashboardsListCardOverlayDiv').css("height", $(this).find('div.dashboardsListCardImgDiv').height() + "px");
                            $(this).find('div.dashboardsListCardOverlayTxt').css("height", $(this).find('div.dashboardsListCardImgDiv').height() + "px");
                            
                            $(this).find('.dashboardsListCardImgDiv').off('mouseenter');
                            $(this).find('.dashboardsListCardImgDiv').off('mouseleave');
                            
                            $(this).find('.dashboardsListCardOverlayTxt').hover(function(){
                                $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayTxt').css("opacity", "1");
                                $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayDiv').css("opacity", "0.8");
                                $(this).css("cursor", "pointer");
                            }, function(){
                                $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayTxt').css("opacity", "0");
                                $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayDiv').css("opacity", "0.05");
                                $(this).css("cursor", "normal");
                            });
                            
                            $(this).find('.dashboardsListCardOverlayTxt').off('click');
                            $(this).find('.dashboardsListCardOverlayTxt').click(function() 
                            {
                                var url = $(this).parents('div.dashboardsListCardDiv').attr('data-url');
								console.log(url);
                                
                                $('#dashboardsListTableRow').hide();
                                $('#iotApplicationsIframeRow').show();
                            //    var extContentHeaderTitleCnt = $('#headerTitleCnt')[0].firstChild.data + ": " + $(this)[0].parentNode.children[0].firstChild.innerText;
                                //var extContentHeaderTitleCnt = $('#headerTitleCnt')[0].innerText + ": " + $(this)[0].parentNode.children[1].firstChild.innerText;
								var extContentHeaderTitleCnt = "Synoptic Template: " + $(this)[0].parentNode.children[1].firstChild.innerText;
                                $('#headerTitleCnt').html(extContentHeaderTitleCnt);
                                $('#mainContentCnt').css('padding', '0px 0px 0px 0px');
                                $('#iotApplicationsIframeCnt').css('padding-left', '0px');
                                $('#iotApplicationsIframeCnt').css('padding-right', '0px');
                                $('#iotApplicationsIframe').attr('src', url);
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

                        $(this).find(".cardLinkBtn").click(function(){
                            var url = $(this).parents('div.dashboardsListCardDiv').attr('data-url');
                            window.open(url);
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
                      dynatable.sorts.add('sub_nature', 1); // 1=ASCENDING, -1=DESCENDING
                      dynatable.process();
                      
					  
                      $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                      $('#dashboardListsCardsSort i.dashboardsListSort').eq(0).click(function(){
                          var dynatable = $('#list_dashboard_cards').data('dynatable');
                          dynatable.sorts.clear();
							  dynatable.sorts.add('unique_name_id', 1); // 1=ASCENDING, -1=DESCENDING
                          dynatable.process();
                          $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(1).css('background-color', 'rgba(0, 162, 211, 1)');
                          $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                      });
                      
                      $('#dashboardListsCardsSort i.dashboardsListSort').eq(1).click(function(){
                          var dynatable = $('#list_dashboard_cards').data('dynatable');
                          dynatable.sorts.clear();
                          dynatable.sorts.add('sub_nature', -1); // 1=ASCENDING, -1=DESCENDING
                          dynatable.process();
                          $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(0).css('background-color', 'rgba(0, 162, 211, 1)');
                          $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(1).css('background-color', 'rgba(255, 204, 0, 1)');
                      });

                    // Toggle-Button for Filtering by Organization
                    if (loggedRole == "RootAdmin") {
                        if (!location.href.includes("AllOrgs")) {
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", true);
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                        } else {
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", false);
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).css('background-color', 'rgba(0, 162, 211, 1)');
                        }
                    }

                    $('#dashboardListsCardsOrgsSort i.dashboardsListSort').eq(0).click(function(){

                        if($('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active") === "true")
                        {
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", "false");
                            location.href = "../management/synopticTemplates.php?linkId=externalServicesList&fromSubmenu=false&sorts[title_header]=1&param=AllOrgs&pageTitle=" + $('#headerTitleCnt')[0].innerText;
                        }
                        else
                        {
                            $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", "true");
                            location.href = "../management/synopticTemplates.php?linkId=externalServicesList&fromSubmenu=false&sorts[title_header]=1&pageTitle=" + $('#headerTitleCnt')[0].innerText;
                        }
                    });				

					$('.updSynTplBtnCard').off('click');
					$('.updSynTplBtnCard').click(function() {
						var name = $(this).parents('div.dashboardsListCardDiv').attr('data-title');
						var nature = $(this).parents('div.dashboardsListCardDiv').attr('data-nature');
						var subnature = $(this).parents('div.dashboardsListCardDiv').attr('data-subnature');
						location.href = 'synopticTemplatesForm.php?name='+encodeURIComponent(name)+"&nature="+encodeURIComponent(nature)+"&subnature="+encodeURIComponent(subnature);
					});
					
					$('.publSynTplBtnCard').off('click');
					$('.publSynTplBtnCard').click(function() {
						if(confirm('Make '+$(this).parents('div.dashboardsListCardDiv').attr('data-title')+" public? Public templates cannot be edited.")) {
							var tplId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
							$.ajax({
								url: "../controllers/changeSynopticTplVisibility.php",
								data: {
									id: tplId,
									newVisibility: "public"
								},
								type: "GET",
								async: true,
								dataType: 'json',
								success: function(data) {
									if(data.detail == 'Ok') {
										location.reload();
									}
									else {
										alert("ERROR! The template could not be made public.");
									}
								},
								error: function(errorData) {
									alert("ERROR! The template could not be made public.");
								}
							});
						}
					});
					
					$('.mkPvtSynTplBtnCard').off('click');
					$('.mkPvtSynTplBtnCard').click(function() {						
						var tplId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
						var tplName = $(this).parents('div.dashboardsListCardDiv').attr('data-title');
						
						$.ajax({
							url: "../controllers/getSynoptics.php",
							data: {
								orgFilter: "<?= @$_SESSION['loggedOrganization'] ?>",
								param: param,
								role: loggedRole
							},
							type: "GET",
							async: true,
							dataType: 'json',
							success: function(data) 
							{
								var inUse = false;
								data.applications.forEach(function(synoptic){
									if(synoptic.low_level_type == tplName && synoptic.ownership == "public") {
										inUse = true;
									}
								});
								if(inUse) {
									alert("Public synoptics exist that use this template.\nPlease make them private first.");
								}
								else {		
									if(confirm("Make "+tplName+" private?")) {
										$.ajax({
											url: "../controllers/changeSynopticTplVisibility.php",
											data: {
												id: tplId,
												newVisibility: "private"
											},
											type: "GET",
											async: true,
											dataType: 'json',
											success: function(data) {
												if(data.detail == 'Ok') {
													location.reload();
												}
												else {
													alert("ERROR! The template could not be made public.");
												}
											},
											error: function(errorData) {
												alert("ERROR! The template could not be made public.");
											}
										});
									}
								}
							}
						});
					});					
					
					
					/*
					$('.delSynTplBtnCard').off('click');
					$('.delSynTplBtnCard').click(function() {
						var tplId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
						var tplTitle = $(this).parents('div.dashboardsListCardDiv').attr('data-title');						
						$.ajax({
							url: "../controllers/getSynoptics.php",
							data: {
								orgFilter: "<?= @$_SESSION['loggedOrganization'] ?>",
								param: param,
								role: loggedRole
							},
							type: "GET",
							async: true,
							dataType: 'json',
							success: function(data) 
							{
								var inUse = false;
								data.applications.forEach(function(synoptic){
									if(synoptic.low_level_type == tplTitle) {
										inUse = true;
									}
								});
								if(inUse) {
									alert("The template is in use and it cannot be deleted.\nPlease delete its instances first.");
								}
								else {									
									if(confirm('Delete '+tplTitle+"?")) {										
										$.ajax({
											url: "../controllers/deleteSynopticTemplate.php?id="+tplId,
											type: "GET",
											async: true,
											success: function(data) {
												if(data == 'Ok') {																							
													location.reload(); 									
												}
												else {
													alert("ERROR! The template could not be deleted.");
												}
											},
											error: function(errorData) {
												alert("ERROR! The template could not be deleted.");
											}
										});
									}						
								}
							}
						});						
					});	
					*/
					$('.delSynTplBtnCard').off('click');
					$('.delSynTplBtnCard').click(function() 
					{
						var dashboardId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
						var dashboardTitle = $(this).parents('div.dashboardsListCardDiv').attr('data-title');
						var dashboardUrl = $(this).parents('div.dashboardsListCardDiv').attr('data-url');
						
						$('#dashIdDelHidden').val(dashboardId);
						$('#dashToDelName').html(dashboardTitle);
						
						$('#dashToDelPic').css("background-image", "url("+dashboardUrl+")");
						$('#dashToDelPic').css("background-color", "white");
						$('#dashToDelPic').css("background-size", "auto auto");
						$('#dashToDelPic').css("background-repeat", "no-repeat");
						$('#dashToDelPic').css("background-position", "center center");
						$('#modalDelDash').modal('show');
					});
					
					$('.delegateSynTplBtnCard').off('click');
					$('.delegateSynTplBtnCard').click(function() {
						var synId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');		
						var synTitle = $(this).parents('div.dashboardsListCardDiv').attr('data-title');								
						var delegateUser = prompt("Grant/Revoke delegation for "+synTitle+" to (username):","");
						if(!delegateUser) return;						
						$.ajax({
							url: "../controllers/getSynopticTplDelegations.php?id="+synId,
							type: "GET",
							async: true,
							dataType: 'json',
							success: function(delegations) {								
								if(!delegations) {
									alert("DELEGATION ERROR");
									return;
								}
								var delegationId = null;
								delegations.forEach(function(delegation){
									if(delegation.delegatedUser == delegateUser) {
										delegationId = delegation.delegationId;
									}
								});
								if(delegationId) {
									$.ajax({
										url: "../controllers/delSynopticTplDelegation.php",
										data: {
											id: synId,
											delegationId: delegationId
										},
										type: "GET",
										async: true,
										dataType: 'json',
										success: function(response) {
											if(response.detail == "Ok") {
												alert("DELEGATION REVOKED\nTemplate: "+synTitle+"\nUser: "+delegateUser); 
											}
											else {
												alert("DELEGATION ERROR");
											}
										},
										error: function(err) {
											alert("DELEGATION ERROR");
										}
									});							
								}
								else {
									$.ajax({
										url: "../controllers/addSynopticTplDelegation.php?id="+synId+"&newDelegated="+delegateUser, 
										type: "GET",
										async: true,
										dataType: 'json',
										success: function(response) {
											if(response.detail == "Ok") {
												alert("DELEGATION GRANTED\nTemplate: "+synTitle+"\nUsername: "+delegateUser);
											}
											else {
												alert("DELEGATION ERROR");
											}
										},
										error: function(err) {
											alert("DELEGATION ERROR");
										}
									});	
								}
							},
							error: function(err) { 
								alert("DELEGATION ERROR");
							}
						});
						
					});
					
					$('.chownSynTplBtnCard').off('click');
					$('.chownSynTplBtnCard').click(function() {
						var tplId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');		
						var tplTitle = $(this).parents('div.dashboardsListCardDiv').attr('data-title');								
						var newOwner = prompt("The new owner of "+tplTitle+" is (username):","");
						if(!newOwner) return;						
						$.ajax({
							url: "../controllers/changeSynopticTplOwnership.php?id="+encodeURIComponent(tplId)+"&newOwner="+encodeURIComponent(newOwner)+"&tplTitle="+encodeURIComponent(tplTitle),
							type: "GET",
							async: true,
							dataType: 'json',
							success: function(response) {		
								if(response.detail == 'Ok') {
									alert("Ownership changed.");
									location.reload();
								}
								else {
									alert("ERROR! OWNERSHIP WAS NOT CHANGED.");
								}
							},
							error: function(err) {
								alert("ERROR! OWNERSHIP WAS NOT CHANGED.");
							}
						});	
					});				
					
					////

					$('.mgmtDashBtnCard').off('click');
					$('.mgmtDashBtnCard').click(function() 
					{
						console.log("entered");
						$('#delegationsTable tbody').empty();
						$('#groupDelegationsTable tbody').empty();
						var dashboardId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
						var dashboardTitle = $(this).parents('div.dashboardsListCardDiv').attr('data-title');
						var dashboardUrl = $(this).parents('div.dashboardsListCardDiv').attr('data-url');

						$('#delegationsDashId').val(dashboardId);
						$('#delegationsDashboardTitle').html(dashboardTitle);
						if($(this).parents('div.dashboardsListCardDiv').find('div.dashboardsListCardVisibilityDiv').html().includes('Public'))
						{
							$('#newVisibility').val('public');
							$('#delegationsFormRow').hide();
							$('#groupDelegationsFormRow').hide();
							$('#delegationsNotAvailableRow').show();
							$('#groupDelegationsNotAvailableRow').show();
						}
						else
						{
							$('#newVisibility').val('private');
							$('#delegationsNotAvailableRow').hide();
							$('#groupDelegationsNotAvailableRow').hide();
							$('#delegationsFormRow').show();
							$('#groupDelegationsFormRow').show();
						}
						
						$('#delegationsDashPic').css("background-image", "url("+dashboardUrl+")");
						$('#delegationsDashPic').css("background-color", "white");
						$('#delegationsDashPic').css("background-size", "100% auto");
						$('#delegationsDashPic').css("background-repeat", "no-repeat");
						$('#delegationsDashPic').css("background-position", "center center");
						
						$('#newOwner').val('');
						$('#newOwner').off('input');
						$('#newOwner').on('input',function(e)
						{
							if($(this).val().trim() === '')
							{
								$('#newOwnerMsg').css('color', '#f3cf58');
								$('#newOwnerMsg').html('New owner username can\'t be empty');
								$('#newOwnershipConfirmBtn').addClass('disabled');
							}
							else
							{
								if(($(this).val().trim() === "<?= @$_SESSION['loggedUsername'] ?: '' ?>")&&("<?= @$_SESSION['loggedRole'] ?>" !== "RootAdmin"))
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

						$('#newDelegation').val('');
						$('#newDelegation').off('input');
						$('#newDelegation').on('input',function(e)
						{
							if($(this).val().trim() === '')
							{
								$('#newDelegatedMsg').css('color', '#f3cf58');
								$('#newDelegatedMsg').html('Delegated username can\'t be empty');
								$('#newDelegationConfirmBtn').addClass('disabled');
							}
							else
							{
								$('#newDelegatedMsg').css('color', 'white');
								$('#newDelegatedMsg').html('User can be delegated');
								$('#newDelegationConfirmBtn').removeClass('disabled');
								
								$('#delegationsTable tbody tr').each(function(i)
								{
								   if($(this).attr('data-delegated').trim() === $('#newDelegation').val())
								   {
									   $('#newDelegatedMsg').css('color', '#f3cf58');
									   $('#newDelegatedMsg').html('User already delegated');
									   $('#newDelegationConfirmBtn').addClass('disabled');
								   }
								});

							}
						});
						
						$('#delegationsModal').modal('show');
						
						$.ajax({
							url: "../controllers/getSynopticTplDelegations.php",
							data: 
							{
								id: dashboardId
							},
							type: "GET",
							async: true,
							dataType: 'json',
							success: function(data) 
							{
								for(var i = 0; i < data.length; i++)
								{
									if (data[i].delegatedUser != null && data[i].delegatedUser != undefined) {
										$('#delegationsTable tbody').append('<tr class="delegationTableRow" data-delegationId="' + data[i].delegationId + '" data-delegated="' + data[i].delegatedUser + '"><td class="delegatedName">' + data[i].delegatedUser + '</td><td><i class="fa fa-remove removeDelegationBtn"></i></td></tr>');
									} else if (data[i].delegatedGroup != null && data[i].delegatedGroup != undefined) {
										$('#groupDelegationsTable tbody').append('<tr class="groupDelegationTableRow" data-delegationId="' + data[i].delegationId + '" data-delegated="' + data[i].delegatedGroup + '"><td class="delegatedName">' + data[i].delegatedGroup + '</td><td><i class="fa fa-remove removeDelegationBtn"></i></td></tr>');
									}
								}
								
								$('#delegationsTable tbody .removeDelegationBtn').click(function(){
									var rowToRemove = $(this).parents('tr');
									$.ajax({
										url: "../controllers/delSynopticTplDelegation.php",
										data: 
										{
											id: dashboardId,
											delegationId: $(this).parents('tr').attr('data-delegationId')
										},
										type: "POST",
										async: true,
										dataType: 'json',
										success: function(data) 
										{
											if(data.detail === 'Ok')
											{
												rowToRemove.remove();
											}
											else
											{
												//TBD
											}
										},
										error: function(errorData)
										{
										   //TBD
											console.log("Del Synoptic Template ko: " + errorData);
											console.log(JSON.stringify(errorData));
										}
									});
								});

								$('#groupDelegationsTable tbody .removeDelegationBtn').click(function(){
									document.body.style.cursor = "wait";
									var rowToRemove = $(this).parents('tr');
									$.ajax({
										url: "../controllers/delSynopticTplGroupDelegation.php",
										data:
											{
												id: dashboardId,
												delegationId: $(this).parents('tr').attr('data-delegationId')
											},
										type: "POST",
										async: true,
										dataType: 'json',
										success: function(data)
										{
											document.body.style.cursor = "default";
											if(data.detail === 'Ok')
											{
												rowToRemove.remove();
											}
											else
											{
												//TBD
											}
										},
										error: function(errorData)
										{
											//TBD
											document.body.style.cursor = "default";
											console.log("Del Synoptic Template Delegation ko: " + errorData);
											console.log(JSON.stringify(errorData));
										}
									});
								});

							},
							error: function(errorData)
							{
								console.log("Get Synoptic Template Delegation ko: " + errorData);
								console.log(JSON.stringify(errorData));
							}
						});
					});					

					$('#newOwnershipConfirmBtn').off('click');
					$('#newOwnershipConfirmBtn').click(function(){
						$.ajax({
							url: "../controllers/changeSynopticTplOwnership.php",
							data: 
							{
								id: $('#delegationsDashId').val(),
								tplTitle: $('#delegationsDashboardTitle').html(),
								newOwner: $('#newOwner').val().toLowerCase()
							},
							type: "POST",
							async: true,
							dataType: 'json',
							success: function(data) 
							{
								if(data.detail === 'Ok')
								{
									$('#newOwner').val('');
									$('#newOwner').addClass('disabled');
									$('#newOwnershipResultMsg').show();
									$('#newOwnershipResultMsg').html('New ownership set correctly');
									$('#newOwnershipResultMsg').css('color', 'white');
									$('#newOwnershipConfirmBtn').addClass('disabled');
									
									setTimeout(function()
									{
										location.reload();
									}, 1250);
								}
								else if (data.detail === 'checkUserKo')
								{
									$('#newOwner').addClass('disabled');
									$('#newOwnershipResultMsg').show();
									$('#newOwnershipResultMsg').html('Error: New owner does not exists or it is not a valid LDAP user');
									$('#newOwnershipResultMsg').css('color', '#f3cf58');
									$('#newOwnershipConfirmBtn').addClass('disabled');

									setTimeout(function()
									{
										$('#newOwner').removeClass('disabled');
										$('#newOwnershipResultMsg').html('');
										$('#newOwnershipResultMsg').hide();
									}, 1750);
								}
								else if (data.detail === 'ApiCallKo1')
								{
									$('#newOwner').addClass('disabled');
									$('#newOwnershipResultMsg').show();
									$('#newOwnershipResultMsg').html('Error: New owner has exceeded his limits for synoptic templates ownership');
									$('#newOwnershipResultMsg').css('color', '#f3cf58');
									$('#newOwnershipConfirmBtn').addClass('disabled');

									setTimeout(function()
									{
										$('#newOwner').removeClass('disabled');
										$('#newOwnershipResultMsg').html('');
										$('#newOwnershipResultMsg').hide();
									}, 1750);
								}
								else
								{
									$('#newOwner').addClass('disabled');
									$('#newOwnershipResultMsg').show();
									$('#newOwnershipResultMsg').html('Error setting new ownership: please try again');
									$('#newOwnershipResultMsg').css('color', '#f3cf58');
									$('#newOwnershipConfirmBtn').addClass('disabled');
									
									setTimeout(function()
									{
										$('#newOwner').removeClass('disabled');
										$('#newOwnershipResultMsg').html('');
										$('#newOwnershipResultMsg').hide();
									}, 1500);
								}
							},
							error: function(errorData)
							{
								$('#newOwner').addClass('disabled');
								$('#newOwnershipResultMsg').html('Error setting new ownership: please try again');
								$('#newOwnershipConfirmBtn').addClass('disabled');

								setTimeout(function()
								{
									$('#newOwner').removeClass('disabled');
									$('#newOwnershipResultMsg').html('');
									$('#newOwnershipResultMsg').hide();
								}, 1500);
							}
						});
					});					
					
					$('#newVisibilityConfirmBtn').off('click');
					$('#newVisibilityConfirmBtn').click(function(){
						var tplName = $('#delegationsDashboardTitle').html();							
						$.ajax({
							url: "../controllers/changeSynopticTplVisibility.php",
							data: 
							{
								id: $('#delegationsDashId').val(),
								tplTitle: $('#delegationsDashboardTitle').html(),
								newVisibility: $('#newVisibility').val()
							},
							type: "POST",
							async: true,
							dataType: 'json',
							success: function(data) 
							{
								if(data.detail === 'Ok')
								{
									$('#newVisibilityResultMsg').show();
									$('#newVisibilityResultMsg').html('New visibility set correctly');
									$('#newVisibilityConfirmBtn').addClass('disabled');
									
									setTimeout(function()
									{
										location.reload();
									}, 1250);
								}
								else
								{
									$('#newVisibilityResultMsg').show();
									$('#newVisibilityResultMsg').html('Error setting new visibility');
									$('#newVisibilityConfirmBtn').addClass('disabled');
									
									setTimeout(function()
									{
										$('#newVisibilityConfirmBtn').removeClass('disabled');
										$('#newVisibilityResultMsg').html('');
										$('#newVisibilityResultMsg').hide();
									}, 1500);
								}
							},
							error: function(errorData)
							{
								$('#newVisibilityResultMsg').show();
								$('#newVisibilityResultMsg').html('Error setting new visibility');
								$('#newVisibilityConfirmBtn').addClass('disabled');

								setTimeout(function()
								{
									$('#newVisibilityConfirmBtn').removeClass('disabled');
									$('#newVisibilityResultMsg').html('');
									$('#newVisibilityResultMsg').hide();
								}, 1500);
							}
						});
						
					});
					
					$('#newDelegationConfirmBtn').off('click');
					$('#newDelegationConfirmBtn').click(function(){
						$.ajax({
							url: "../controllers/addSynopticTplDelegation.php",
							data: 
							{
								id: $('#delegationsDashId').val(),
								newDelegated: $('#newDelegation').val()
							},
							type: "POST",
							async: true,
							dataType: 'json',
							success: function(data) 
							{
								if(data.detail === 'Ok')
								{
									$('#delegationsTable tbody').append('<tr class="delegationTableRow" data-delegationId="' + data.delegationId + '" data-delegated="' + $('#newDelegation').val() + '"><td class="delegatedName">' + $('#newDelegation').val() + '</td><td><i class="fa fa-remove removeDelegationBtn"></i></td></tr>');
									
									$('#delegationsTable tbody .removeDelegationBtn').off('click');
									$('#delegationsTable tbody .removeDelegationBtn').click(function(){
										var rowToRemove = $(this).parents('tr');
										$.ajax({
											url: "../controllers/delSynopticTplDelegation.php",
											data: 
											{
												id: $('#delegationsDashId').val(),
												delegationId: $(this).parents('tr').attr('data-delegationId')
											},
											type: "POST",
											async: true,
											dataType: 'json',
											success: function(data) 
											{
												if(data.detail === 'Ok')
												{
													rowToRemove.remove();
												}
												else
												{
													//TBD
												}
											},
											error: function(errorData)
											{
											   //TBD     
											}
										});
									});
									
									$('#newDelegation').val('');
									$('#newDelegation').addClass('disabled');
									$('#newDelegatedMsg').css('color', 'white');
									$('#newDelegatedMsg').html('New delegation added correctly');
									$('#newDelegationConfirmBtn').addClass('disabled');
									
									setTimeout(function()
									{
										$('#newDelegation').removeClass('disabled');
										$('#newDelegatedMsg').css('color', '#f3cf58');
										$('#newDelegatedMsg').html('Delegated username can\'t be empty');
									}, 1500);
								}
								else
								{
									var errorMsg = null;
									switch(data.detail)
									{
										case "RootAdmin":
											errorMsg = "You can\'t delegate a root admin";
											break;
											
										case "ApiCallKo":
											errorMsg = "Error calling Snap4City API";
											break;  
											
										case "QueryKo":
											errorMsg = "Database error";
											break;
											
										case "LdapKo":
											errorMsg = "LDAP error";
											break;

										case "Username_not_recognized":
											errorMsg = "Invalid Username (not recognized)";
											break;
									}
									
									$('#newDelegation').val('');
									$('#newDelegation').addClass('disabled');
									$('#newDelegatedMsg').css('color', '#f3cf58');
									$('#newDelegatedMsg').html(errorMsg);
									$('#newDelegationConfirmBtn').addClass('disabled');
									
									setTimeout(function()
									{
										$('#newDelegation').removeClass('disabled');
										$('#newDelegatedMsg').css('color', '#f3cf58');
										$('#newDelegatedMsg').html('Delegated username can\'t be empty');
									}, 2000);
								}
							},
							error: function(errorData)
							{
								var errorMsg = "Error calling internal API"; 
								$('#newDelegation').val('');
								$('#newDelegation').addClass('disabled');
								$('#newDelegatedMsg').css('color', '#f3cf58');
								$('#newDelegatedMsg').html(errorMsg);
								$('#newDelegationConfirmBtn').addClass('disabled');

								setTimeout(function()
								{
									$('#newDelegation').removeClass('disabled');
									$('#newDelegatedMsg').css('color', '#f3cf58');
									$('#newDelegatedMsg').html('Delegated username can\'t be empty');
								}, 2000);
							}
						});
					});

					$('#newGroupDelegationConfirmBtn').off('click');
					$('#newGroupDelegationConfirmBtn').click(function(){
						if (!$('#newGroupDelegationConfirmBtn').hasClass('disabled')) {
							var isPresentFlag = 0;
							$('#groupDelegationsTable tbody tr').each(function (i) {
								if ($(this).attr('data-delegated').trim() === $('#newDelegationOrganization').val() + " - " + $('#newDelegationGroup').val()) {
									$('#newGroupDelegatedMsg').css('color', '#f3cf58');
									$('#newGroupDelegatedMsg').html('Group already delegated');
									$('#newGroupDelegationConfirmBtn').addClass('disabled');
									isPresentFlag = 1;
								} else {
							   //     isPresentFlag = 0;
								}
							});

							if (isPresentFlag === 0) {
								var orgDel = $('#newDelegationOrganization').val();
								var groupDel = $('#newDelegationGroup').val();
								var newDelegatedString = "";
								if (orgDel != null && orgDel != undefined) {
									if (orgDel != "") {
										if (groupDel != null && groupDel != undefined) {
											if (groupDel != "" && groupDel != "All Groups"){
												newDelegatedString = "cn=" + groupDel + ",ou=" + orgDel + "," + "dc=ldap,dc=disit,dc=org";
											} else {
												newDelegatedString = "ou=" + orgDel + "," + "dc=ldap,dc=disit,dc=org";
											}
										}
									} else {
										// NON Dovrebbe essere gestito!
									}
								}
								$.ajax({
									url: "../controllers/addGroupSynopticTplDelegation.php",
									data:
										{
											id: $('#delegationsDashId').val(),
											newDelegated: newDelegatedString
										},
									type: "POST",
									async: true,
									dataType: 'json',
									success: function (data) {
										if (data.detail === 'Ok') {

											$('#groupDelegationsTable tbody').append('<tr class="groupDelegationTableRow" data-delegationId="' + data.delegationId + '" data-delegated="' + $('#newDelegationOrganization').val() + " - " + $('#newDelegationGroup').val() + '"><td class="delegatedName">' + $('#newDelegationOrganization').val() + " - " + $('#newDelegationGroup').val() + '</td><td><i class="fa fa-remove removeDelegationBtn"></i></td></tr>');

											$('#groupDelegationsTable tbody .removeDelegationBtn').off('click');
											$('#groupDelegationsTable tbody .removeDelegationBtn').click(function () {
												var rowToRemove = $(this).parents('tr');
												$.ajax({
													url: "../controllers/delSynopticTplGroupDelegation.php",
													data:
														{
															id: $('#delegationsDashId').val(),
															delegationId: $(this).parents('tr').attr('data-delegationId')
														},
													type: "POST",
													async: true,
													dataType: 'json',
													success: function (data) {
														if (data.detail === 'Ok') {
															rowToRemove.remove();
														}
														else {
															//TBD
														}
													},
													error: function (errorData) {
														//TBD
													}
												});
											});

											$('#newDelegation').val('');
											$('#newDelegation').addClass('disabled');
											$('#newGroupDelegatedMsg').css('color', 'white');
											$('#newGroupDelegatedMsg').html('New delegation added correctly');
											//   $('#newGroupDelegationConfirmBtn').addClass('disabled');

											/*    setTimeout(function()
												{
													$('#newDelegation').removeClass('disabled');
													$('#newGroupDelegatedMsg').css('color', '#f3cf58');
													$('#newGroupDelegatedMsg').html('Delegated username can\'t be empty');
												}, 1500);   */
										}
										else {
											var errorMsg = null;
											switch (data.detail) {
												case "RootAdmin":
													errorMsg = "You can\'t delegate a root admin";
													break;

												case "ApiCallKo":
													errorMsg = "Error calling Snap4City API";
													break;

												case "QueryKo":
													errorMsg = "Database error";
													break;

												case "LdapKo":
													errorMsg = "LDAP error";
													break;
											}

											$('#newDelegation').val('');
											$('#newDelegation').addClass('disabled');
											$('#newGroupDelegatedMsg').css('color', '#f3cf58');
											$('#newGroupDelegatedMsg').html(errorMsg);
											//   $('#newDelegationConfirmBtn').addClass('disabled');

											/*  setTimeout(function()
											  {
												  $('#newDelegation').removeClass('disabled');
												  $('#newGroupDelegatedMsg').css('color', '#f3cf58');
												  $('#newGroupDelegatedMsg').html('Delegated username can\'t be empty');
											  }, 2000);   */
										}
									},
									error: function (errorData) {
										var errorMsg = "Error calling internal API";
										$('#newDelegation').val('');
										$('#newDelegation').addClass('disabled');
										$('#newGroupDelegatedMsg').css('color', '#f3cf58');
										$('#newGroupDelegatedMsg').html(errorMsg);
										//    $('#newGroupDelegationConfirmBtn').addClass('disabled');

										/*  setTimeout(function()
										  {
											  $('#newDelegation').removeClass('disabled');
											  $('#newGroupDelegatedMsg').css('color', '#f3cf58');
											  $('#newGroupDelegatedMsg').html('Delegated username can\'t be empty');
										  }, 2000);   */
									}
								});
							}
						}
					});
					
					$('#delDashConfirmBtn').off("click");
					$('#delDashConfirmBtn').click(function(){
						$('#delDashNameMsg').parents('div.row').hide();
						$('#delDashCancelBtn').hide();
						$('#delDashConfirmBtn').hide();
						$('#delDashRunningMsg').show();
						
						var tplTitle = $('#dashToDelName').html();		
						$.ajax({
							url: "../controllers/getSynoptics.php",
							data: {
								orgFilter: "<?= @$_SESSION['loggedOrganization'] ?>",
								param: param,
								role: loggedRole
							},
							type: "GET",
							async: true,
							dataType: 'json',
							success: function(data) 
							{
								var inUse = false;
								data.applications.forEach(function(synoptic){
									if(synoptic.low_level_type == tplTitle) {
										inUse = true;
									}
								});
								if(inUse) {
									//alert("The template is in use and it cannot be deleted.\nPlease delete its instances first."); //
									$('#delDashRunningMsg').hide();
									$('#delDashKoMsgTxt').html("The template is in use. Please delete its instances first.");
									$('#delDashKoMsg').show();
									console.log("Del synoptic template ko: The template is in use and it cannot be deleted. Please delete its instances first.");
									setTimeout(function(){
										$('#modalDelWidget').modal('hide');
										setTimeout(function(){
											$('#delDashKoMsg').hide();
											$('#delDashKoMsgTxt').html("Error deleting template, please try again");
											$('#delDashNameMsg').parents('div.row').show();
											$('#delDashCancelBtn').show();
											$('#delDashConfirmBtn').show();
										}, 750);
									}, 2500);
								}
								else {

									$.ajax({
										url: "../controllers/deleteSynopticTemplate.php",
										data: {
											id: $('#dashIdDelHidden').val(),
											tplTitle: $('#dashToDelName').html()
										},
										async: true,
										success: function(successData)
										{
											$('#delDashRunningMsg').hide();

											if(successData !== 'Ok')
											{
												$('#delDashRunningMsg').hide();
												$('#delDashKoMsg').show();
												console.log("Del synoptic template ko: " + successData);
												setTimeout(function(){													
													$('#modalDelWidget').modal('hide');
													setTimeout(function(){
														$('#delDashKoMsg').hide();
														$('#delDashNameMsg').parents('div.row').show();
														$('#delDashCancelBtn').show();
														$('#delDashConfirmBtn').show();
													}, 750);
												}, 2500);
											}
											else
											{
												$('#delDashOkMsg').show();
												
												setTimeout(function(){
													location.reload();
												}, 1250);
												
												/*var keyToDel = null;
												for(var k = 0; k < dashboardsList.length; k++)
												{
													if(dashboardsList[k].Id === $('#dashIdDelHidden').val())
													{
														console.log(dashboardsList[k].title_header + " has been deleted: " + k);
														keyToDel = k;
													}
												}
												dashboardsList.splice(keyToDel, 1);
												$('.dashboardsListCardDiv[data-uniqueid=' + $('#dashIdDelHidden').val() + ']').remove();
												dynatable.process();*/
											}
										},
										error: function(errorData)
										{
											$('#delDashRunningMsg').hide();
											$('#delDashKoMsg').show();
											setTimeout(function(){
												$('#modalDelWidget').modal('hide');
												setTimeout(function(){
													$('#delDashKoMsg').hide();
													$('#delDashNameMsg').parents('div.row').show();
													$('#delDashCancelBtn').show();
													$('#delDashConfirmBtn').show();
												}, 750);
											}, 2500);
											console.log("Del synoptic template ko: " + errorData);
											console.log(JSON.stringify(errorData));
										}
									});
								}
							}
						});
					});
										
					function updateGroupList(ouname){
						$.ajax({
							url: "../api/ldap.php",
							data:{
								action: "get_group_for_ou",
								ou: ouname,
								token : "<?= @$_SESSION['refreshToken'] ?>"
							},
							type: "POST",
							async: true,
							success: function (data)
							{
								if(data["status"] === 'ko')
								{
									$('#newDelegatedMsgGroup').css('color', '#f3cf58');
									$('#newDelegatedMsgGroup').html(data["msg"]);
								}
								else if (data["status"] === 'ok')
								{
									if ($('#newGroupDelegationConfirmBtn').hasClass('disabled')) {
										$('#newGroupDelegationConfirmBtn').removeClass('disabled');
										$('#newGroupDelegatedMsg').html('')
									}
									var $dropdown = $("#newDelegationGroup");
									//remove old ones
									$dropdown.empty();
									//adding empty to rootadmin
									if (loggedRole=='RootAdmin') {
									  //  console.log("adding empty");
									  //  $dropdown.append($("<option />").val("").text(""));
										console.log("adding All Groups");
										$dropdown.append($("<option />").val("All Groups").text("All Groups"));
									}
									//add new ones
									$.each(data['content'], function() {
										$dropdown.append($("<option />").val(this).text(this));
									  //  $dropdown.append($("<option />").val(this).text(this)[0].innerHTML);
									});

								}
							},
							error: function (data)
							{
								$('#newDelegatedMsgGroup').css('color', '#f3cf58');
								$('#newDelegatedMsgGroup').html('Error calling internal API');
							}
						});
					}
		
					if (loggedRole=='RootAdmin') {
						console.log("Refresh Token"); console.log("<?= @$_SESSION['refreshToken'] ?>");
						$.ajax({
							url: "../api/ldap.php",
							data:{
								action: "get_all_ou",
								token : "<?= @$_SESSION['refreshToken'] ?>"
							},
							type: "POST",
							async: false,
							success: function (data)
							{
								console.log("Groups"); console.log(data); //!
								if(data["status"] === 'ko')
								{
									$('#newDelegatedMsgGroup').css('color', '#f3cf58');
									$('#newDelegatedMsgGroup').html(data["msg"]);
								}
								else if (data["status"] === 'ok')
								{
									var $dropdown = $("#newDelegationOrganization");
									$.each(data['content'], function() {
										$dropdown.append($("<option />").val(this).text(this));
									});
								}
							},
							error: function (data)
							{
								$('#newDelegatedMsgGroup').css('color', '#f3cf58');
								$('#newDelegatedMsgGroup').html('Error calling internal API');
							}
						});
					}
					//populate organization list with myorganization (otherwise)
					else {
						$.ajax({
							url: "../api/ldap.php",
							data:{
								action: "get_logged_ou",
								username: usr,
								token : "<?= @$_SESSION['refreshToken'] ?>"
							},
							type: "POST",
							async: false,
							success: function (data)
							{
								if(data["status"] === 'ko')
								{
									console.log("Error: "+data);
									//TODO: manage error
								}
								else if(data["status"] === 'ok')
								{
									var $dropdown = $("#newDelegationOrganization");
									$dropdown.append($("<option/>").val(data['content']).text(data['content']));
								}
							},
							error: function (data)
							{
								console.log("Error: " +  data);
								//TODO: manage error
							}
						});
					}

					// NEW TABS FOR GROPs/ORGANIZATIONs DELEGATION
					//populate group list with selected organization
					updateGroupList($("#newDelegationOrganization").val());

					//eventually update the group list
					$('#newDelegationOrganization').change( function() {
						$(this).find(":selected").each(function () {
							updateGroupList($(this).val());
						});
					});

					//eventually update the group list
					$('#newDelegationGroup').change( function() {
					   // $(this).find(":selected").each(function () {
							if ($('#newGroupDelegationConfirmBtn').hasClass('disabled')) {
								$('#newGroupDelegationConfirmBtn').removeClass('disabled');
								$('#newGroupDelegatedMsg').html('');
							}
					  //  });
					});
					
					/////
							
					$('#link_start_wizard').off('click');
					$('#link_start_wizard').click(function() {
						location.href = 'synopticTemplatesForm.php';
					});
					
					$('.instSynTplBtnCard').off('click');
					$('.instSynTplBtnCard').click(function() {
						var tplName = $(this).parents('div.dashboardsListCardDiv').attr('data-title');
						location.href = 'synopticsForm.php?template='+encodeURIComponent(tplName);
					});

                },
                error: function(errorData)
                {
                    
                }
				
            });
			
    });
</script>
<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
	this.classList.toggle("active");
	var content = this.nextElementSibling;
	if (content.style.display === "block") {
	  content.style.display = "none";
	} else {
	  content.style.display = "block";
	}
  });
}
</script>

<?php } else {
    include('../s4c-legacy-management/synopticTemplates.php');
}
?>