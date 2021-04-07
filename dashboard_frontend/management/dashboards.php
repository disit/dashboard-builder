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
    include('../TourRepository.php');
if (!isset($_SESSION)) {
    session_start();
}

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

checkSession('Public');
if (isset($_REQUEST['linkId']) && ctype_alnum($_REQUEST['linkId']))
    $linkIdd = @$_REQUEST['linkId'];
else
    $linkIdd = 'invalid';

    $tourRepo = new TourRepository($host, $username, $password,$dbname);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php include "mobMainMenuClaim.php" ?></title>

        <!-- jQuery -->
        <script src="../js/jquery-1.10.1.min.js"></script>

        <!-- JQUERY UI -->
        <script src="../js/jqueryUi/jquery-ui.js"></script>

        <!-- Bootstrap Core CSS -->
        <link href="../css/bootstrap.css" rel="stylesheet">
        <!-- Bootstrap Core JavaScript -->
        <script src="../js/bootstrap.min.js"></script>

        <!-- Color pickers -->
        <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
        <script src="../js/bootstrap-colorpicker.min.js"></script>

        <!-- Bootstrap toggle button -->
        <link href="../bootstrapToggleButton/css/bootstrap-toggle.min.css" rel="stylesheet">
        <script src="../bootstrapToggleButton/js/bootstrap-toggle.min.js"></script>

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

        <!-- Bootstrap Multiselect -->
        <script src="../js/bootstrap-multiselect.js"></script>
        <link href="../css/bootstrap-multiselect.css" rel="stylesheet">

        <!-- DataTables -->
        <script type="text/javascript" charset="utf8" src="../js/DataTables/datatables.js"></script>
        <link rel="stylesheet" type="text/css" href="../js/DataTables/datatables.css">
        <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.bootstrap.min.js"></script>
        <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.responsive.min.js"></script>
        <script type="text/javascript" charset="utf8" src="../js/DataTables/responsive.bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/DataTables/dataTables.bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/DataTables/responsive.bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/DataTables/jquery.dataTables.min.css">  

        <!-- Leaflet -->
        <!-- Versione locale: 1.3.1 --> 
        <script src="../leafletCore/leaflet.js"></script> 
        <link rel="stylesheet" href="../leafletCore/leaflet.css" />

        <!-- Bootstrap slider -->
        <script src="../bootstrapSlider/bootstrap-slider.js"></script>
        <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet"/>

        <!-- Filestyle -->
        <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>

        <!-- Font awesome icons -->
        <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

        <!-- Custom CSS -->
        <link href="../css/dashboard.css?v=<?php echo time(); ?>" rel="stylesheet">
        <link href="../css/dashboardList.css?v=<?php echo time(); ?>" rel="stylesheet">
        <link href="../css/dashboardView.css?v=<?php echo time(); ?>" rel="stylesheet">
        <link href="../css/addWidgetWizard.css?v=<?php echo time(); ?>" rel="stylesheet">
        <link href="../css/addDashboardTab.css?v=<?php echo time(); ?>" rel="stylesheet">
        <link href="../css/dashboard_configdash.css?v=<?php echo time(); ?>" rel="stylesheet">

        <!--Highchart -->
        <script src="https://code.highcharts.com/stock/highstock.js"></script>
        <script src="https://code.highcharts.com/stock/modules/data.js"></script>
        <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/stock/modules/export-data.js"></script>
        <!-- Custom scripts -->
        <script type="text/javascript" src="../js/dashboard_mng.js"></script>
        <!-- Chat CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@8/dist/css/shepherd.min.css">
        <!--  <link rel="stylesheet" href="../css/shepherd.min.css">  -->
    <link href="../css/snapTour.css" rel="stylesheet">
</head>

    <style type="text/css">
        .left{
            float:left;
        }
        .right{
            float: right;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 82px;
            height: 20px;
        }

        .switch input {display:none;}

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #DBDBDB;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: blue;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(62px);
            -ms-transform: translateX(62px);
            transform: translateX(62px);
        }

        /*------ ADDED CSS ---------*/
        .fixMapon
        {
            display: none;
        }

        .fixMapon, .fixMapoff
        {
            color: white;
            position: absolute;
            transform: translate(-50%,-50%);
            top: 50%;
            left: 50%;
            font-size: 10px;
            font-family: Verdana, sans-serif;
        }

        input:checked+ .slider .on
        {display: block;}

        input:checked + .slider .off
        {display: none;}

        /*--------- END --------*/

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;}

        /****************/
        .previous {
            background-color: #f1f1f1;
            color: black;
            padding: 5px;
            margin: 5px;
            margin-top: 20px;
        }

        .next {
            background-color: #f1f1f1;
            color: black;
            padding: 5px;
            margin: 5px;
            margin-top: 20px;
        }

        .control_list{
            margin: 10px;
            margin-bottom: 0px;
        }
        
        .navbar-nav {
            flex: 1;
            margin: auto !important;
            display: flex;
            justify-content: space-between;
        }
        
        #control_minutes {
            margin: 10px;
        }
        
        #control_access{
            margin: 10px;
        }
        /***************/
        @media screen and (min-width: 768px) and (max-width: 1080px)  
        {
            .nav-tabs.nav-justified > li {
                display: inline-block;
                text-align:justify;
                float: left;
                max-width: 20%;
                width: auto;
                overflow: hidden;
                text-overflow: ellipsis
            }
        }
    </style>

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
                        <div class="col-xs-10 col-md-12 centerWithFlex" id="headerTitleCnt">
                            <script type="text/javascript">
<?php
if (isset($_GET['pageTitle'])) {
    ?>
                                    document.write("<?php echo escapeForJS($_GET['pageTitle']); ?>");
    <?php
    }
?>
                            </script>
                        </div>
                        <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <!--   <div class="row">
                           <div class="col-xs-10 col-md-12 centerWithFlex" id="headerSubTitleCnt">(My Own Organization)</div>
                           <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" style="background-color:lightblue"></div>
                       </div>  -->
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt" style='background-color: rgba(138, 159, 168, 1)'>
                            <div class="row mainContentRow" id="dashboardsListTableRow">
                                <!--<div class="col-xs-12 mainContentRowDesc">List</div>-->

                                <div class="col-xs-12 mainContentCellCnt" style='background-color: rgba(138, 159, 168, 1)'>
                                    <div id="dashboardsListMenu" class="row">

                                        <div id="dashboardListsViewMode" class="hidden-xs col-sm-6 col-md-1 dashboardsListMenuItem">
<?php
if (($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']) === 'RootAdmin') {
    ?>
                                                <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                    <input id="dashboardListsViewModeInput" type="checkbox">
                                                </div>
    <?php
}
?>
                                        </div>

                                        <div id="dashboardListsCardsSort" class="col-xs-12 col-sm-6 col-md-2 dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                <div class="col-xs-3 centerWithFlex">
                                                    <div class="dashboardsListSortBtnCnt" data-toggle="tooltip" data-placement="bottom" title="Sort ascending">
                                                        <i class="fa fa-sort-alpha-asc dashboardsListSort"></i>
                                                    </div> 
                                                </div>
                                                <div class="col-xs-3 centerWithFlex">
                                                    <div class="dashboardsListSortBtnCnt" data-toggle="tooltip" data-placement="bottom" title="Sort descending">
                                                        <i class="fa fa-sort-alpha-desc dashboardsListSort"></i>
                                                    </div>    
                                                </div>
<?php if (!$_SESSION['isPublic']) : ?>                                              
                                                    <div class="col-xs-3 centerWithFlex">
                                                        <div id="mySort" class="dashboardsListSortBtnCnt" data-toggle="tooltip" data-placement="bottom" title="My own dashboards">
                                                            <i id="myIcon" class="fa fa-user-secret dashboardsListSort" data-active="false"></i>
                                                        </div>    
                                                    </div>
                                                    <div class="col-xs-3 centerWithFlex">
                                                        <div id="publicSort" class="dashboardsListSortBtnCnt" data-toggle="tooltip" data-placement="bottom" title="Public dashboards">
                                                            <i id="publicIcon" class="fa fa-globe dashboardsListSort" data-active="false" ></i>
                                                        </div>    
                                                    </div>
                                                    <div class="col-xs-3 centerWithFlex">
                                                        <div id="delegatedBtn" class="dashboardsListSortBtnCnt" data-toggle="tooltip" data-placement="bottom" title="Delegated dashboards">
                                                            <i class="fa fa-handshake-o dashboardsListSort" data-active="false" ></i>
                                                        </div>
                                                    </div>
<?php endif; ?>                                              
                                            </div>
                                        </div>
                                        <!--    <div id="dashboardListsCardsOrgsSort" class="col-xs-6 col-sm-4 col-md-2 dashboardsListMenuItem">
                                                <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12 col-md-6">
                                                    <div class="col-xs-6 centerWithFlex">
                                                        <div class="dashboardsListSortOrgsBtnCnt" data-toggle="tooltip" data-placement="bottom" title="My Own / All Organizations">
                                                            <i class="fa fa-cube dashboardsListSort" data-active="false" ></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  -->
                                        <div id="dashboardListsPages" class="col-xs-12 col-sm-6 col-md-3 dashboardsListMenuItem">
                                            <!--<div class="dashboardsListMenuItemTitle centerWithFlex col-xs-4">
                                                 List<br>pages
                                             </div>-->
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">

                                            </div>
                                        </div>
                                        <!--    <div id="dashboardShowAllOrgsButton" class="col-xs-12 col-sm-6 col-md-1 dashboardsListMenuItem">
                                                <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                    <button id="all_organizations_public_dashboards" type="button" class="btn btn-warning">All Orgs</button>
                                                </div>
                                            </div>  -->

                                        <div id="dashboardListsSearchFilter" class="col-xs-12 col-sm-6 col-md-4 dashboardsListMenuItem">
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
<?php if (!$_SESSION['isPublic']) : ?>                                      
                                            <div id="dashboardListsNewDashboard" class="col-xs-12 col-sm-12 col-md-2 dashboardsListMenuItem">
                                                <!--<div class="dashboardsListMenuItemTitle centerWithFlex col-xs-4">
                                                    New<br>dashboard
                                                </div>-->
                                                <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                    <button id="link_start_wizard" type="button" class="btn btn-warning">New dashboard</button>
                                                </div>
                                            </div>
<?php endif; ?>                                      
                                    </div>

                                        <?php
                                        if (($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']) === 'RootAdmin') {
                                            ?>                
                                        <table id="list_dashboard" class="table">
                                            <thead class="dashboardsTableHeader">
                                                <tr>
                                                    <th data-dynatable-column="title_header">Title</th>
                                                    <th data-dynatable-column="user">Creator</th>
                                                    <th data-dynatable-column="creation_date">Creation date</th>
                                                    <th data-dynatable-column="last_edit_date">Last edit date</th>
                                                    <th data-dynatable-column="nAccessPerDay"># Access Today</th>
                                                    <th data-dynatable-column="nMinutesPerDay">Minutes Opened Today</th>
                                                    <th data-dynatable-column="status_dashboard">Status</th>
                                                    <th>Edit</th>
                                                    <th>View</th>
                                                    <th>Organizations</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
    <?php
}
?>   

                                    <div id="list_dashboard_cards" class="container-fluid">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fine del container-fluid -->

<?php if (!$_SESSION['isPublic']) : ?>
            <!-- Modale wizard -->
            <div class="modal fade" id="addWidgetWizard" tabindex="-1" role="dialog" aria-labelledby="addWidgetWizardLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modalContentWizardForm"> 
                        <div class="modalHeader centerWithFlex">
                            Wizard
                        </div>

                        <div id="addWidgetWizardLabelBody" class="modal-body modalBody">
    <?php /* include "addWidgetWizardInclusionCode.php" */ ?>
                        </div>

                        <div id="modalStartWizardFooter" class="modal-footer">
                            <div class="row">
                                <div class="col-xs-8 col-xs-offset-2 centerWithFlex">
                                    <button type="button" id="addWidgetWizardPrevBtn" name="addWidgetWizardPrevBtn" class="btn confirmBtn">Prev</button>
                                    <button type="button" id="addWidgetWizardNextBtn" name="addWidgetWizardNextBtn" class="btn confirmBtn">Next</button>
                                </div>    
                                <div class="col-xs-2">
                                    <button type="button" id="addWidgetWizardCancelBtn" class="btn cancelBtn" data-dismiss="modal">Close</button>
                                </div>   
                            </div>
                        </div>
                    </div>    <!-- Fine modal content -->
                </div> <!-- Fine modal dialog -->
            </div><!-- Fine modale -->
            <!-- Fine modale wizard -->

            <div class="modal fade" id="modalCheckDashLimits" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modalHeader centerWithFlex">
                            Add new Dashboard
                        </div>
                        <div id="checkDashLimitsModalBody" class="modal-body modalBody">
                            <div class="row" id="limitsDashKoMsg">
                                <div class="col-xs-12 modalCell">
                                    <div class="col-xs-12 centerWithFlex modalDelMsg">Exceeded limits for Dashboard Creation for user <?php echo @$_SESSION['loggedUsername'] ? : ''; ?></div>
                                    <div class="col-xs-12 centerWithFlex modalDelObjName"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                                </div>
                            </div>
                        </div>
                        <div id="checkDashLimitsModalFooter" class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modale cancellazione dashboard -->
            <div class="modal fade" id="modalDelDash" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modalHeader centerWithFlex">
                            Dashboard deletion
                        </div>
                        <input type="hidden" id="dashIdDelHidden" name="dashIdDelHidden" />
                        <div id="delDashModalBody" class="modal-body modalBody">
                            <div class="row">
                                <div id="delDashNameMsg" class="col-xs-12 modalCell">
                                    <div class="modalDelMsg col-xs-12 centerWithFlex">
                                        Do you want to delete the following dashboard?
                                    </div>
                                    <div id="dashToDelName" class="modalDelObjName col-xs-12 centerWithFlex"></div>
                                    <div id="dashToDelPic" class="modalDelObjName col-xs-12 centerWithFlex"></div>
                                </div>
                            </div>
                            <div class="row" id="delDashRunningMsg">
                                <div class="col-xs-12 modalCell">
                                    <div class="col-xs-12 centerWithFlex modalDelMsg">Deleting dashboard, please wait</div>
                                    <div class="col-xs-12 centerWithFlex modalDelObjName"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px"></i></div>
                                </div>
                            </div>
                            <div class="row" id="delDashOkMsg">
                                <div class="col-xs-12 modalCell">
                                    <div class="col-xs-12 centerWithFlex modalDelMsg">Dashboard deleted successfully</div>
                                    <div class="col-xs-12 centerWithFlex modalDelObjName"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                                </div>
                            </div>
                            <div class="row" id="delDashKoMsg">
                                <div class="col-xs-12 modalCell">
                                    <div class="col-xs-12 centerWithFlex modalDelMsg">Error deleting dashboard, please try again</div>
                                    <div class="col-xs-12 centerWithFlex modalDelObjName"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                                </div>
                            </div>
                        </div>
                        <div id="delDashModalFooter" class="modal-footer">
                            <button type="button" id="delDashCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                            <button type="button" id="delDashConfirmBtn" class="btn confirmBtn internalLink">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fine modale cancellazione dashboard -->    

            <!-- Modale gestione deleghe dashboard -->
            <div class="modal fade" id="delegationsModal" tabindex="-1" role="dialog" aria-labelledby="modalAddWidgetTypeLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modalHeader centerWithFlex">
                            Management
                        </div>
                        <form id="delegationsForm" class="form-horizontal" name="delegationsForm" role="form" method="post" action="" data-toggle="validator">
                            <div id="delegationsModalBody" class="modal-body modalBody">
                                <!-- Tabs -->
                                <ul id="delegationsTabsContainer" class="nav nav-tabs nav-justified">
                                <!--<ul id="delegationsTabsContainer" class="nav nav-tabs nav-fill flex-row-reverse">-->
                                    <li id="ownershipTab" class="nav-item active"><a data-toggle="tab" href="#ownershipCnt" class="nav-link dashboardWizardTabTxt">Ownership</a></li>
                                    <li id="visibilityTab" class="nav-item"><a data-toggle="tab" href="#visibilityCnt" class="nav-link dashboardWizardTabTxt">Visibility</a></li>
                                    <li id="delegationsTab" class="nav-item"><a data-toggle="tab" href="#delegationsCnt" class="nav-link dashboardWizardTabTxt">Delegations</a></li>
                                    <li id="groupDelegationsTab" class="nav-item"><a data-toggle="tab" href="#groupDelegationsCnt" class="nav-link dashboardWizardTabTxt">Group Delegations</a></li>
                                    <!-- Dashboard Trend -->
                                    <li id="trendTab" class="nav-item"><a data-toggle="tab" href="#groupTrendCnt" class="nav-link dashboardWizardTabTxt">Accesses Trends</a></li>
                                    <!-- GP COMMENT TEMPORARY -->
                                </ul> 
                                <!-- Fine tabs -->


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


                                        <!-- Visibility cnt -->
                                        <div id="visibilityCnt" class="tab-pane fade in">
                                            <div class="row" id="visibilityFormRow">
                                                <div class="col-xs-12 centerWithFlex delegationsModalLbl modalFirstLbl" id="changeOwnershipLbl">
                                                    Change visibility
                                                </div>
                                                <div class="col-xs-12" id="newVisibilityCnt">
                                                    <div class="input-group">
                                                        <select id="newVisibility" class="form-control">
                                                            <option value="public">Public</option>
                                                            <option value="private">Private</option>
                                                        </select>
                                                        <span class="input-group-btn">
                                                            <button type="button" id="newVisibilityConfirmBtn" class="btn confirmBtn">Confirm</button>
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
                                                Delegations are not possibile on a public dashboard
                                            </div>
                                            <div class="row" id="delegationsFormRow">
                                                <div class="col-xs-12 centerWithFlex modalFirstLbl" id="newDelegationLbl">
                                                    Add new delegation
                                                </div>
                                                <div class="col-xs-12" id="newDelegationCnt">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="newDelegation" placeholder="Delegated username">
                                                        <span class="input-group-btn">
                                                            <button type="button" id="newDelegationConfirmBtn" class="btn confirmBtn disabled">Confirm</button>
                                                        </span>
                                                    </div>
                                                    <div class="col-xs-12 centerWithFlex delegationsModalMsg" id="newDelegatedMsg">
                                                        Delegated username can't be empty
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 centerWithFlex" id="currentDelegationsLbl">
                                                    Current user delegations
                                                </div>
                                                <div class="col-xs-12" id="delegationsTableCnt">
                                                    <table id="delegationsTable">
                                                        <thead>
                                                        <th>Delegated user</th>
                                                        <th>Remove</th>
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
                                                Delegations are not possibile on a public dashboard
                                            </div>
                                            <div class="row" id="groupDelegationsFormRow">
                                                <div class="col-xs-12 centerWithFlex modalFirstLbl" id="newDelegationLbl">
                                                    Add new group delegation
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
                                                            <button type="button" id="newGroupDelegationConfirmBtn" class="btn confirmBtn">Confirm</button>
                                                        </span>
                                                    </div>
                                                    <!--    <div class="input-group">
                                                            <input type="text" class="form-control" id="newGroupDelegation" placeholder="Delegated group">
                                                            <span class="input-group-btn">
                                                              <button type="button" id="newGroupDelegationConfirmBtn" class="btn confirmBtn disabled">Confirm</button>
                                                            </span>
                                                        </div>  -->
                                                    <div class="col-xs-12 centerWithFlex delegationsModalMsg" id="newGroupDelegatedMsg">
                                                        <!-- Delegated group/organization name can't be empty    -->
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 centerWithFlex" id="currentGroupDelegationsLbl">
                                                    Current group delegations
                                                </div>
                                                <div class="col-xs-12" id="groupDelegationsTableCnt">
                                                    <table id="groupDelegationsTable">
                                                        <thead>
                                                        <th>Delegated group</th>
                                                        <th>Remove</th>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Fine Group delegations cnt -->

                                    </div>  
                                    <!--<input type="hidden" id="TrendDashId">-->
                                    <!-- Fine tab content -->
                                    <input type="hidden" id="delegationsDashId">
                                </div><!-- Fine delegationsModalRightCnt-->
                                <div id="trendmodal" hidden>
                                    <div class="col-xs-12 centerWithFlex delegationsModalTxt modalFirstLbl" id="trendDashboardTitle">
                                    </div>
                                    <div id="groupTrendCnt" class="tab-pane fade in">
                                        <div class="row" id="groupTrendFormRow" >
                                            <!-- Aggiungere dei valori di Trend della Dashboard -->

                                            <div id="control_access" class="control_list">

                                                <a href="#" class="previous" title="show to previous" onclick="change_trend('access', 'previous')">&laquo; Previous</a>
                                                <select id="menu_1" title="Show by ..." onchange="timetrendaccesses()">
                                                    <option value=" 24 HOUR ">Last Day</option>
                                                    <option value=" 1 WEEK ">Last Week</option>
                                                    <option value=" 1 MONTH ">Last Month</option>
                                                    <option value=" 6 MONTH ">Last Semester</option>
                                                    <option value=" 1 YEAR ">Last Year</option>
                                                </select>
                                                <a href="#" class="next" title="show to next" onclick="change_trend('access', 'next')">Next &raquo;</a>
                                            </div>
                                            From <input type="text" id="end_accss" readonly> To 
                                            <input type="text" id="start_accss" readonly>

                                            <div id="container_accesses" style="height: 200px">                                            
                                            </div><br />
                                            <div id="control_minutes" class="control_list">

                                                <a href="#" class="previous" title="show to previous" onclick="change_trend('minutes', 'previous')">&laquo; Previous</a>
                                                <select id="menu_2" title="Show by ..." onchange="timetrendminutes()">
                                                    <option value=" 24 HOUR ">Last Day</option>
                                                    <option value=" 1 WEEK ">Last Week</option>
                                                    <option value=" 1 MONTH ">Last Month</option>
                                                    <option value=" 6 MONTH ">Last Semester</option>
                                                    <option value=" 1 YEAR ">Last Year</option>
                                                </select>
                                                <a href="#" class="next" title="show to next" onclick="change_trend('minutes', 'next')">Next &raquo;</a>
                                            </div>
                                            From <input type="text" id="end_min" readonly> To 
                                            <input type="text" id="start_min" readonly>
                                            <div id="container_minutes" style="height: 200px">                                            
                                            </div>

                                            <!-- -->
                                        </div>
                                    </div>
                                    <!-- Fine Group Trend cnt -->
                                </div>
                            </div>
                            <div id="delegationsModalFooter" class="modal-footer">
                                <button type="button" id="delegationsCancelBtn" class="btn cancelBtn" data-dismiss="modal" style="margin-top: 50px">Close</button>
                            </div>
                        </form>    
                    </div>
                </div>
            </div>
            <!-- Fine modale gestione deleghe dashboard -->

            <!-- Modale duplicazione dashboard -->
            <div class="modal fade" id="cloneDashboardModal" tabindex="-1" role="dialog" aria-labelledby="modalAddWidgetTypeLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modalHeader centerWithFlex">
                            Dashboard duplication
                        </div>
                        <form id="cloneDashboardForm" class="form-horizontal" name="cloneDashboardForm" role="form" method="post" action="" data-toggle="validator">
                            <div id="cloneDashboardModalBody" class="modal-body modalBody">
                                <div class="row" id="cloningDashboardFormRow">
                                    <div class="col-xs-12 centerWithFlex" id="currentDashboardTitle">

                                    </div>

                                    <div id="dashToClonePic" class="modalDelObjName col-xs-12 centerWithFlex"></div>

                                    <div class="col-xs-12 centerWithFlex" style="font-weight: bold !important; margin-top: 6px; color: white">
                                        Cloned dashboard title
                                    </div>
                                    <div class="col-xs-12 centerWithFlex" id="newDashboardTitleCnt">
                                        <input type="text" class="form-control" id="newDashboardTitle" name="NameNewDashboard" required>
                                    </div>
                                    <div id="cloneDashboardTitleMsg" class="col-xs-12 centerWithFlex ok">
                                        New title OK
                                    </div>
                                    <input type="hidden" id="dashIdCloneHidden">
                                </div>
                                <div class="row" id="duplicateDashboardLoadingTitlesRow">
                                    <div class="col-xs-12 centerWithFlex">Retrieving current dashboards titles, please wait</div>
                                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                                </div>
                                <div class="row" id="duplicateDashboardLoadingTitlesKoRow">
                                    <div class="col-xs-12 centerWithFlex">Error retrieving current dashboards titles, please try again</div>
                                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                                </div>
                                <div class="row" id="duplicateDashboardLoadingRow">
                                    <div class="col-xs-12 centerWithFlex">Cloning dashboard, please wait</div>
                                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i></div>
                                </div>
                                <div class="row" id="duplicateDashboardOkRow">
                                    <div class="col-xs-12 centerWithFlex">Dashboard cloned successfully</div>
                                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-up" style="font-size:36px"></i></div>
                                </div>
                                <div class="row" id="duplicateDashboardWarningRow">
                                    <div class="col-xs-12 centerWithFlex"></div>
                                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-exclamation-triangle" style="font-size:36px"></i></div>
                                </div>
                                <div class="row" id="duplicateDashboardKoRow">
                                    <div class="col-xs-12 centerWithFlex">Error while cloning dashboard, please try again</div>
                                    <div class="col-xs-12 centerWithFlex"><i class="fa fa-thumbs-o-down" style="font-size:36px"></i></div>
                                </div>
                            </div>
                            <div id="cloneDashboardModalFooter" class="modal-footer">
                                <button type="button" id="duplicateDashboardCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                                <button type="button" id="duplicateDashboardBtn" class="btn confirmBtn internalLink">Confirm</button>
                            </div>
                        </form>    
                    </div>
                </div>
            </div>
            <!-- Fine modale duplicazione dashboard -->

            <!-- Modale gestione IOT Apps  dashboard -->
            <div class="modal fade" id="iotAppsModal" tabindex="-1" role="dialog" aria-labelledby="modalAddWidgetTypeLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modalHeader centerWithFlex">
                            Dashboard IOT apps 
                        </div>

                        <div id="iotAppsModalBody" class="modal-body modalBody">
                            <div class="row" id="delegationsFormRow">
                                <!-- Colonna sinistra -->
                                <div class="col-xs-2">
                                    <div class="col-xs-12 centerWithFlex iotAppsModalLbl" id="iotAppsDashboardTitle"></div>
                                    <div id="iotAppsDashPic" class="modalDelObjName col-xs-12 centerWithFlex"></div>

                                    <div class="col-xs-12 centerWithFlex iotAppsModalLbl">IOT applications list</div>
                                    <div class="col-xs-12" id="iotAppsNoAppsCnt">
                                        You have no access rights for Apps connected to this dashboard or these Apps may have been deleted
                                    </div>    
                                    <div class="col-xs-12" id="iotAppsTableCnt">
                                        <table id="iotAppsTable">
                                            <thead>
                                            <th>App</th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Fine colonna sinistra -->

                                <!-- Colonna destra -->
                                <div class="col-xs-10">
                                    <div class="col-xs-12 centerWithFlex iotAppsModalLbl">Flow designer</div>
                                    <div class="col-xs-12" id="iotAppsIframeCnt">
                                        <iframe id="iotAppsIframe"></iframe>
                                    </div>
                                </div>
                                <!-- Fine colonna destra -->
                                <input type="hidden" id="iotAppsDashId">
                            </div>
                        </div>
                        <div id="iotAppsModalFooter" class="modal-footer">
                            <button type="button" id="iotAppsCancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                        </div>   
                    </div>
                </div>
            </div>
            <!-- Fine modale gestione IOT Apps dashboard -->
<?php endif; ?>
    </body>
</html>

<script src="https://cdn.jsdelivr.net/npm/shepherd.js@8/dist/js/shepherd.min.js"></script>
<!-- <script src="../js/shepherd.min.js"></script> -->
<script src="../js/snapTour.js"></script>
<script type='text/javascript'>
    $(document).ready(function ()
    {
        $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        //   if (location.href.includes("[search]=My+own")) {
        if (location.href.includes("My+orgMy%3FlinkId") || location.href.includes("My+orgMy?linkId")) {
            //  $('#sessionExpiringPopup').show();
            // PARTIAL MOD TO BE COMMITTED
            //    $('#publicSort').hide();
        } else {
            $('#delegatedBtn').hide();
        }
        var dashboardsList, dashboardWizardChoice = null;
        var allDashboardsList = null;
        allGlobalDashboards = [];

        var orgFlag = "all";
<?php if (isset($_GET['param']) && !empty($_GET['param']) /* && ($_GET['param']=='My org' || $_GET['param']=='My orgMy?linkId') */) { ?>
            orgFlag = "<?php echo escapeForJS($_GET['param']); ?>";
<?php } ?>

        var sessionEndTime = "<?php echo $_SESSION['sessionEndTime']; ?>";
        $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
        $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");

        if (sessionEndTime > 0)
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
                    location.href = "logout.php";
                }
            }, 1000);

        // $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
        
        $(window).resize(function () {
            $('#mainContentCnt').height($('#mainMenuCnt').height() - $('#headerTitleCnt').height());
            $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
            $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");
        });

        $('#mainMenuCnt .mainMenuLink[id=<?= escapeForJS($linkIdd) ?>] div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt .mainMenuLink[id=<?= escapeForJS($linkIdd) ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt .mainMenuLink[id=<?= escapeForJS($linkIdd) ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");

        var loggedRole = "<?= ($_SESSION['isPublic'] ? 'Public' : $_SESSION['loggedRole']) ?>";
        var loggedType = "<?= @$_SESSION['loggedType'] ? : '' ?>";
        var usr = "<?= @$_SESSION['loggedUsername'] ? : '' ?>";
        var org = "<?= @$_SESSION['loggedOrganization'] ?>";
        var userVisibilitySet = null;
        var authorizedPages = [];

        // Solve the Firefox limit to 640k character issue for DynaTable (when displaying list of dashboards and cards)
        var dynaTablePushState = true;
        if (loggedRole === "RootAdmin") {
            dynaTablePushState = false;
        }

        $('#authorizedPagesJson').val(JSON.stringify(authorizedPages));
        $('#color_hf').css("background-color", '#ffffff');

        setGlobals(loggedRole, usr, loggedType, userVisibilitySet);

        $("#logoutBtn").off("click");
        $("#logoutBtn").click(function (event)
        {
            event.preventDefault();
            location.href = "logout.php";
        });

        function myRowWriter(rowIndex, record, columns, cellWriter)
        {
            var statusBtn, cssClass = null;
            var title = record.title_header;

            if (rowIndex % 2 !== 0)
            {
                cssClass = 'blueRow';
            } else
            {
                cssClass = 'whiteRow';
            }

            if (title.length > 75)
            {
                title = title.substr(0, 75) + " ...";
            }

            var user = record.user;
            var accessPerDay = 0;
            var orgs = record.organizations;

            /*if((record.nAccessPerDay === null)||(record.nAccessPerDay === 'null'))
             {
             record.nAccessPerDay = "0";
             //console.log("Entrato: " + record.nAccessPerDay);
             }*/

            //     var minsPerDay = 0;

            /*if((record.nMinutesPerDay === null)||(record.nMinutesPerDay === 'null'))
             {
             record.nMinutesPerDay = "0";
             }*/

            record.nAccessPerDay = parseInt(record.nAccessPerDay);
            record.nMinutesPerDay = parseInt(record.nMinutesPerDay);

            if (user.length > 75)
            {
                user = user.substr(0, 75) + " ...";
            }

            if ((record.status_dashboard === '0') || (record.status_dashboard === 0))
            {
                statusBtn = '<input type="checkbox" data-toggle="toggle" class="changeDashboardStatus">';
            } else
            {
                statusBtn = '<input type="checkbox" checked data-toggle="toggle" class="changeDashboardStatus">';
            }

            var titleEscapedPre = record.title_header.replace(/\"/g, '&quot;');
            var titleEscaped = titleEscapedPre.replace(/\'/g, '&apos;');
            var newRow = '<tr data-dashTitle="' + record.title_header + '" data-uniqueid="' + record.Id + '" data-authorName="' + record.user + '"><td class="' + cssClass + '" style="font-weight: bold">' + title + '</td><td class="' + cssClass + '">' + user + '</td><td class="' + cssClass + '">' + record.creation_date + '</td><td class="' + cssClass + '">' + record.last_edit_date + '</td><td class="' + cssClass + '">' + record.nAccessPerDay + '</td><td class="' + cssClass + '">' + record.nMinutesPerDay + '</td><td class="' + cssClass + '">' + statusBtn + '</td><td class="' + cssClass + '"><button type="button" class="editDashBtn">edit</button></td><td class="' + cssClass + '"><button type="button" class="viewDashBtn">view</button></td><td class="' + cssClass + '">' + record.organizations + '</td></tr>';

            return newRow;
        }

        function myCardsWriter(rowIndex, record, columns, cellWriter)
        {
            var title = record.title_header;

            if (title.length > 100)
            {
                title = title.substr(0, 100) + " ...";
            }

            var headerColor = record.color_header;

            if ((headerColor === '#ffffff') || (headerColor === 'rgb(255, 255, 255)') || (headerColor === 'rgba(255, 255, 255, 1)') || (headerColor === 'white') || (headerColor.includes(',0)')))
            {
                headerColor = '#e6f9ff';
            }

            var visibility = record.visibilityLbl;
            /*if(visibility.includes("MyOwn"))
             {
             visibility = "My own";
             }*/
            var managementLbl = record.managementLbl;
            var authorLbl = record.authorLbl;
            var rightsLbl = record.rightsLbl;
            var deleteLbl = record.deleteLbl;
            var editLbl = record.editLbl;
            var cloneLbl = record.cloneLbl;
            var brokerLbl = record.brokerLbl;
            var iotLbl = record.iotLbl;
            var organizations = record.organizations;

            var titleHTMLEscapedPre = record.title_header.replace(/\"/g, '&quot;');
            var titleHTMLEscaped = titleHTMLEscapedPre.replace(/\'/g, '&apos;');
            var cardDiv = '<div data-uniqueid="' + record.Id + '" data-screenshotFilename="' + record.screenshotFilename + '" data-deleted="' + record.deleted + '" data-dashTitle="' + record.title_header + '" data-headerColor = "' + headerColor + '" data-headerFontColor="' + record.headerFontColor + '" data-org="' + record.organizations + '" class="dashboardsListCardDiv col-xs-12 col-sm-6 col-md-3">' +
                    '<div class="dashboardsListCardInnerDiv">';

            if (brokerLbl && iotLbl)
            {
                cardDiv = cardDiv + '<div class="dashboardsListCardTitleDiv col-xs-12"><span class="dashboardListCardTitleSpan">' + title + '</span><span class="dashboardListCardTypeSpan" data-hasIotModal="<?= !$_SESSION['isPublic'] ? 'true' : 'false' ?>">IOT apps & broker</span>' + '</div>';
            } else
            {
                if ((!brokerLbl) && iotLbl)
                {
                    cardDiv = cardDiv + '<div class="dashboardsListCardTitleDiv col-xs-12"><span class="dashboardListCardTitleSpan">' + title + '</span><span class="dashboardListCardTypeSpan" data-hasIotModal="<?= !$_SESSION['isPublic'] ? 'true' : 'false' ?>">IOT apps</span>' + '</div>';
                } else
                {
                    if (brokerLbl && (!iotLbl))
                    {
                        cardDiv = cardDiv + '<div class="dashboardsListCardTitleDiv col-xs-12"><span class="dashboardListCardTitleSpan">' + title + '</span><span class="dashboardListCardTypeSpan" data-hasIotModal="false">Broker</span>' + '</div>';
                    } else
                    {
                        if ((!brokerLbl) && (!iotLbl))
                        {
                            cardDiv = cardDiv + '<div class="dashboardsListCardTitleDiv col-xs-12"><span class="dashboardListCardTitleSpan">' + title + '</span><span class="dashboardListCardTypeSpan" data-hasIotModal="false">Passive</span>' + '</div>';
                        }
                    }
                }
            }


            cardDiv = cardDiv + '<div class="dashboardsListCardOverlayDiv col-xs-12 centerWithFlex"></div>' +
                    '<div class="dashboardsListCardOverlayTxt col-xs-12 centerWithFlex">View</div>' +
                    '<div class="dashboardsListCardImgDiv"></div>';
            var appendStr = "";
            var appendStr1 = "";
            var appendStr2 = "";
            if (authorLbl === 'hide')
            {
                if (organizations == "None" || organizations == "none") {
                    if (loggedRole == "RootAdmin") {

                    } else {
                        organizations = "Other";
                    }
                }
                if (visibility.includes("to Group")) {
                    var appendStr1 = visibility.split("to Group")[0] + "(" + organizations + ")";
                    var appendStr2 = "to Group" + visibility.split("to Group")[1];
                    cardDiv = cardDiv + '<div class="dashboardsListCardVisibilityDiv col-xs-12 centerWithFlex">' + appendStr1 + '</div>';
                    cardDiv = cardDiv + '<div class="dashboardsListCardClick2EditDiv col-xs-12 centerWithFlex" style="background-color: inherit; color: white; font-size: 11px; font-weight: normal";>' + appendStr2;
                } else {
                    appendStr = visibility + " (" + organizations + ")";
                    cardDiv = cardDiv + '<div class="dashboardsListCardVisibilityDiv col-xs-12 centerWithFlex">' + appendStr + '</div>';
                    cardDiv = cardDiv + '<div class="dashboardsListCardClick2EditDiv col-xs-12 centerWithFlex" style="background-color: inherit; color: inherit">';
                }
                //  cardDiv = cardDiv + '<div class="dashboardsListCardOrganizationDiv col-xs-12 centerWithFlex">' + organizations + '</div>';
            } else
            {
                if (organizations == "None" || organizations == "none") {
                    if (loggedRole == "RootAdmin") {

                    } else {
                        organizations = "Other";
                    }
                }
                cardDiv = cardDiv + '<div class="dashboardsListCardVisibilityDiv col-xs-12 centerWithFlex">' + authorLbl + ": " + visibility + " - " + organizations + '</div>';
                //    cardDiv = cardDiv + '<div class="dashboardsListCardOrganizationDiv col-xs-12 centerWithFlex">' + authorLbl + ": " + visibility + '</div>';
                cardDiv = cardDiv + '<div class="dashboardsListCardClick2EditDiv col-xs-12 centerWithFlex" style="background-color: inherit; color: inherit">';
            }

<?php if (!$_SESSION['isPublic']) : ?>
                if (editLbl === 'show')
                {
                    cardDiv = cardDiv + '<button type="button" class="dashBtnCard editDashBtnCard">Edit</button>';
                }

                switch (managementLbl)
                {
                    case "show":
                        cardDiv = cardDiv + '<button type="button" class="dashBtnCard delegateDashBtnCard">Management</button>';
                        break;

                    default:
                        break;
                }

                if (cloneLbl === 'show')
                {
                    cardDiv = cardDiv + '<button type="button" class="dashBtnCard cloneDashBtnCard">Clone</button>';
                }

                if (deleteLbl === 'show')
                {
                    cardDiv = cardDiv + '<button type="button" class="dashBtnCard deleteDashBtnCard">Delete</button>';
                }
<?php endif; ?>
            cardDiv = cardDiv + '</div>' +
                    '</div>' +
                    '</div>';

            return cardDiv;
        }

        function updateGroupList(ouname) {
            $.ajax({
                url: "../api/ldap.php",
                data: {
                    action: "get_group_for_ou",
                    ou: ouname,
                    token: "<?= @$_SESSION['refreshToken'] ?>"
                },
                type: "POST",
                async: true,
                success: function (data)
                {
                    if (data["status"] === 'ko')
                    {
                        $('#newDelegatedMsgGroup').css('color', '#f3cf58');
                        $('#newDelegatedMsgGroup').html(data["msg"]);
                    } else if (data["status"] === 'ok')
                    {
                        if ($('#newGroupDelegationConfirmBtn').hasClass('disabled')) {
                            $('#newGroupDelegationConfirmBtn').removeClass('disabled');
                            $('#newGroupDelegatedMsg').html('')
                        }
                        var $dropdown = $("#newDelegationGroup");
                        //remove old ones
                        $dropdown.empty();
                        //adding empty to rootadmin
                        if (loggedRole == 'RootAdmin') {
                            //  console.log("adding empty");
                            //  $dropdown.append($("<option />").val("").text(""));
                            console.log("adding All Groups");
                            $dropdown.append($("<option />").val("All Groups").text("All Groups"));
                        }
                        //add new ones
                        $.each(data['content'], function () {
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

        //   if (orgFlag == "all") {
        function getAllDash(f) {
            $.ajax({
                url: "get_data.php",
                data: {
                    action: "get_all_dashboards",
                    param: ""
                },
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (data) {
                    var allDashboardsList = data;
                    f(allDashboardsList);
                },
                error: function (errorData) {
                }
            });
        }
        //    }

        function getAllGlobalDashboards() {
            $.ajax({
                url: "get_data.php",
                data: {
                    action: "get_all_dashboards",
                    param: ""
                },
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (data) {
                    allGlobalDashboards = data;
                },
                error: function (errorData) {
                }
            });
        }

        //Nuova tabella
        $.ajax({
            url: "get_data.php",
            data: {
                action: "get_dashboards",
                param: orgFlag
            },
            type: "GET",
            async: true,
            dataType: 'json',
            success: function (data)
            {
                dashboardsList = data;
                //Ricordati di metterlo PRIMA dell'istanziamento della tabella
                $('#list_dashboard_cards').bind('dynatable:afterProcess', function (e, dynatable) {
                    $('#dashboardsListTableRow').css('padding-top', '0px');
                    $('#dashboardsListTableRow').css('padding-bottom', '0px');

                    $('#dashboardListsViewModeInput').bootstrapToggle({
                        on: 'Table',
                        off: 'Cards',
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

<?php
if (@$_SESSION['loggedRole'] === 'RootAdmin') {
    ?>

                        $('#dashboardListsViewModeInput').change(function () {
                            if ($(this).prop('checked'))
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
                                $('#searchDashboardBtn').click(function () {
                                    var dynatable = $('#list_dashboard').data('dynatable');
                                    dynatable.queries.run();
                                });

                                $('#resetSearchDashboardBtn').off('click');
                                $('#resetSearchDashboardBtn').click(function () {
                                    var dynatable = $('#list_dashboard').data('dynatable');
                                    $("#dynatable-query-search-list_dashboard").val("");
                                    dynatable.queries.runSearch("");
                                });
                            } else
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
                                $('#searchDashboardBtn').click(function () {
                                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                                    dynatable.queries.run();
                                });

                                $('#resetSearchDashboardBtn').off('click');
                                $('#resetSearchDashboardBtn').click(function () {
                                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                                    $("#dynatable-query-search-list_dashboard_cards").val("");
                                    dynatable.queries.runSearch("");
                                });
                            }
                        });

    <?php
}
?>

                    $('#link_start_wizard').click(function () {
                        authorizedPages = [];
                        //$('#modalCreateDashboard').modal('show');
                        $.ajax({
                            url: "../controllers/checkDashboardLimits.php",
                            data:
                                    {
                                    },
                            type: "POST",
                            async: true,
                            dataType: 'json',
                            success: function (data) {
                                if (data.detail === 'DashboardLimitsOk') {
                                    var allDashList = [];
                                    allDashList = getAllDash(function (allDashList) {
                                        //   loadWizardModal(allDashList);
                                        $('#addWidgetWizard').modal('show');
                                    }
                                    );

                                    //  choosenWidgetIconName = null;
                                    //  widgetWizardSelectedRows = {};
                                    //  widgetWizardSelectedRowsTable.clear().draw(false);

                                } else {
                                    $('#modalCheckDashLimits').modal('show');
                                    $('#limitsDashKoMsg').show();
                                    console.log("Dashboard Limits Exceeded.");
                                    setTimeout(function () {
                                        $('#limitsDashKoMsg').show();
                                        $('#checkDashLimitsModalBody').modal('hide');
                                    }, 2500);
                                }
                            },
                            error: function (errorData) {
                                $('#modalCheckDashLimits').modal('show');
                                $('#limitsDashKoMsg').show();
                                console.log("Dashboard Limits Exceeded.");
                                setTimeout(function () {
                                    $('#limitsDashKoMsg').show();
                                    $('#checkDashLimitsModalBody').modal('hide');
                                }, 2500);
                            }
                        });

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
                    $("#dynatable-query-search-list_dashboard_cards").attr("placeholder", "Filter by dashboard title, author...");
                    $("#dynatable-query-search-list_dashboard_cards").css("width", "100%");
                    $("#dynatable-query-search-list_dashboard_cards").addClass("form-control");

                    $('#list_dashboard_cards div.dashboardsListCardDiv').each(function (i) {
                        $(this).find('div.dashboardsListCardImgDiv').css("background-image", "url(../img/dashScr/dashboard" + $(this).attr('data-uniqueid') + "/" + $(this).attr('data-screenshotFilename') + ")");
                        $(this).find('div.dashboardsListCardImgDiv').css("background-size", "100% auto");
                        $(this).find('div.dashboardsListCardImgDiv').css("background-repeat", "no-repeat");
                        $(this).find('div.dashboardsListCardImgDiv').css("background-position", "center top");
                        $(this).find('div.dashboardsListCardInnerDiv').css("width", "100%");
                        $(this).find('div.dashboardsListCardInnerDiv').css("height", $(this).height() + "px");
                        $(this).find('div.dashboardsListCardOverlayDiv').css("height", $(this).find('div.dashboardsListCardImgDiv').height() + "px");
                        $(this).find('div.dashboardsListCardOverlayTxt').css("height", $(this).find('div.dashboardsListCardImgDiv').height() + "px");

                        $(this).find('.dashboardsListCardImgDiv').off('mouseenter');
                        $(this).find('.dashboardsListCardImgDiv').off('mouseleave');

                        $(this).find('.dashboardsListCardOverlayTxt').hover(function () {
                            $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayTxt').css("opacity", "1");
                            $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayDiv').css("opacity", "0.8");
                            $(this).css("cursor", "pointer");
                        }, function () {
                            $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayTxt').css("opacity", "0");
                            $(this).parents('.dashboardsListCardDiv').find('div.dashboardsListCardOverlayDiv').css("opacity", "0.05");
                            $(this).css("cursor", "normal");
                        });

                        $(this).find('.cloneDashBtnCard').off('click');
                        $(this).find('.cloneDashBtnCard').click(function ()  // Add Ownership Registraion on Dashboard Clone
                        {
                            $('#cloneDashboardTitleMsg').html("New title OK");
                            $('#cloneDashboardTitleMsg').removeClass("error");
                            $('#cloneDashboardTitleMsg').addClass("ok");
                            $('#duplicateDashboardBtn').attr("disabled", false);
                            var dashboardId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
                            var dashboardTitle = $(this).parents('div.dashboardsListCardDiv').attr('data-dashTitle');
                            $.ajax({
                                url: "../controllers/checkDashboardLimits.php",
                                data:
                                        {
                                        },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function (data) {
                                    if (data.detail === 'DashboardLimitsOk') {

                                        $('#dashIdCloneHidden').val(dashboardId);
                                        $('#currentDashboardTitle').html(dashboardTitle);
                                        $('#newDashboardTitle').val(dashboardTitle + " - Cloned");

                                        $('#dashToClonePic').css("background-image", "url(../img/dashScr/dashboard" + dashboardId + "/lastDashboardScr.png)");
                                        $('#dashToClonePic').css("background-size", "100% auto");
                                        $('#dashToClonePic').css("background-repeat", "no-repeat");
                                        $('#dashToClonePic').css("background-position", "center top");
                                        $('#cloneDashboardModal').modal('show');

                                        $.ajax({
                                            url: "process-form.php",
                                            data: {
                                                getDashboardTitlesList: true,
                                            },
                                            type: "GET",
                                            async: true,
                                            dataType: 'json',
                                            success: function (data)
                                            {
                                                if (data.detail !== 'Ok')
                                                {
                                                    console.log("Error getting dashboards titles list");
                                                    console.log(data);
                                                    $('#duplicateDashboardLoadingTitlesRow').hide();
                                                    $('#duplicateDashboardLoadingTitlesKoRow').show();

                                                    setTimeout(function () {
                                                        $('#cloneDashboardModal').modal('hide');
                                                        setTimeout(function () {
                                                            $('#duplicateDashboardLoadingTitlesKoRow').hide();
                                                            $('#duplicateDashboardLoadingTitlesRow').show();
                                                        }, 300);
                                                    }, 3500);
                                                } else {
                                                    dashboardTitlesList = data.titles;
                                                    $('#duplicateDashboardLoadingTitlesRow').hide();
                                                    $('#cloningDashboardFormRow').show();
                                                    $('#cloneDashboardModalFooter').show();
                                                    dashboardTitlesList = data.titles;
                                                    if (dashboardTitlesList.indexOf($('#newDashboardTitle').val().trim()) > 0)
                                                    {
                                                        $('#cloneDashboardTitleMsg').html("Title already in use");
                                                        $('#cloneDashboardTitleMsg').removeClass("ok");
                                                        $('#cloneDashboardTitleMsg').addClass("error");
                                                        $('#duplicateDashboardBtn').attr("disabled", true);
                                                    }
                                                    // else
                                                    //  {
                                                    $('#newDashboardTitle').on('input', function () {
                                                        if ($('#newDashboardTitle').val().trim().length < 4) {
                                                            $('#cloneDashboardTitleMsg').html("Title can't be less than 4 characters long");
                                                            $('#cloneDashboardTitleMsg').removeClass("ok");
                                                            $('#cloneDashboardTitleMsg').addClass("error");
                                                            $('#duplicateDashboardBtn').attr("disabled", true);
                                                        } else {
                                                            if (dashboardTitlesList.indexOf($('#newDashboardTitle').val().trim()) > 0) {
                                                                $('#cloneDashboardTitleMsg').html("Title already in use");
                                                                $('#cloneDashboardTitleMsg').removeClass("ok");
                                                                $('#cloneDashboardTitleMsg').addClass("error");
                                                                $('#duplicateDashboardBtn').attr("disabled", true);
                                                            } else {
                                                                $('#cloneDashboardTitleMsg').html("New title OK");
                                                                $('#cloneDashboardTitleMsg').removeClass("error");
                                                                $('#cloneDashboardTitleMsg').addClass("ok");
                                                                $('#duplicateDashboardBtn').attr("disabled", false);
                                                            }
                                                        }
                                                    });
                                                    //  }
                                                }
                                            },
                                            error: function (errorData)
                                            {
                                                console.log("Error getting dashboards titles list");
                                                console.log(errorData);
                                                $('#duplicateDashboardLoadingTitlesRow').hide();
                                                $('#duplicateDashboardLoadingTitlesKoRow').show();

                                                setTimeout(function () {
                                                    $('#cloneDashboardModal').modal('hide');
                                                    setTimeout(function () {
                                                        $('#duplicateDashboardLoadingTitlesKoRow').hide();
                                                        $('#duplicateDashboardLoadingTitlesRow').show();
                                                    }, 300);
                                                }, 3500);
                                            }
                                        });
                                    } else {
                                        $('#modalCheckDashLimits').modal('show');
                                        $('#limitsDashKoMsg').show();
                                        console.log("Dashboard Limits Exceeded.");
                                        setTimeout(function () {
                                            $('#limitsDashKoMsg').show();
                                            $('#checkDashLimitsModalBody').modal('hide');
                                        }, 2500);
                                    }
                                },
                                error: function (errorData) {
                                    $('#modalCheckDashLimits').modal('show');
                                    $('#limitsDashKoMsg').show();
                                    console.log("Dashboard Limits Exceeded.");
                                    setTimeout(function () {
                                        $('#limitsDashKoMsg').show();
                                        $('#checkDashLimitsModalBody').modal('hide');
                                    }, 2500);
                                }
                            });

                        });

                        //Duplicazione della dashboard
                        $('#duplicateDashboardBtn').off("click");
                        $('#duplicateDashboardBtn').click(function ()
                        {
                            $('#cloningDashboardFormRow').hide();
                            $('#cloneDashboardModalFooter').hide();
                            $('#duplicateDashboardLoadingRow').show();

                            $.ajax({
                                url: "duplicate_dash.php",
                                data: {
                                    sourceDashboardId: $('#dashIdCloneHidden').val(),
                                    newDashboardTitle: $('#newDashboardTitle').val()
                                },
                                type: "POST",
                                async: true,
                                success: function (data)
                                {
                                    $('#duplicateDashboardLoadingRow').hide();
                                    switch (data)
                                    {
                                        case "Ok":
                                            $('#duplicateDashboardOkRow').show();
                                            setTimeout(function () {
                                                location.reload();
                                                /*$('#cloneDashboardModal').modal('hide');
                                                 setTimeout(function(){
                                                 $('#duplicateDashboardOkRow').hide();
                                                 $('#cloningDashboardFormRow').show();
                                                 $('#cloneDashboardModalFooter').show(); 
                                                 $('#newDashboardTitle').val("");
                                                 $('#cloneDashboardTitleMsg').val("Title can't be less than 4 characters long");
                                                 $('#duplicateDashboardBtn').attr("disabled", true);
                                                 }, 300);*/
                                            }, 1250);
                                            break;

                                        case "logoDirCreationKo":
                                        case "logoFileCopyKo":
                                            $('#duplicateDashboardWarningRow').show();
                                            setTimeout(function () {
                                                $('#duplicateDashboardWarningRow').hide();
                                                $('#cloningDashboardFormRow').show();
                                                $('#cloneDashboardModalFooter').show();
                                            }, 3000);
                                            console.log(data);
                                            break;

                                        case "Ko":
                                            $('#duplicateDashboardKoRow').show();
                                            setTimeout(function () {
                                                $('#duplicateDashboardKoRow').hide();
                                                $('#cloningDashboardFormRow').show();
                                                $('#cloneDashboardModalFooter').show();
                                            }, 3000);
                                            console.log(data);
                                            break;
                                    }
                                },
                                error: function (data)
                                {
                                    $('#duplicateDashboardLoadingRow').hide();
                                    $('#duplicateDashboardKoRow').show();
                                    setTimeout(function () {
                                        $('#duplicateDashboardKoRow').hide();
                                        $('#cloningDashboardFormRow').show();
                                        $('#cloneDashboardModalFooter').show();
                                    }, 3000);
                                    console.log(data);
                                }
                            });
                        });

                        $('#newOwnershipConfirmBtn').off('click');
                        $('#newOwnershipConfirmBtn').click(function () {
                            $.ajax({
                                url: "../controllers/changeDashboardOwnership.php",
                                data:
                                        {
                                            dashboardId: $('#delegationsDashId').val(),
                                            dashboardTitle: $('#delegationsDashboardTitle').html(),
                                            newOwner: $('#newOwner').val().toLowerCase()
                                        },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function (data)
                                {
                                    if (data.detail === 'Ok')
                                    {
                                        $('#newOwner').val('');
                                        $('#newOwner').addClass('disabled');
                                        $('#newOwnershipResultMsg').show();
                                        $('#newOwnershipResultMsg').html('New ownership set correctly');
                                        $('#newOwnershipResultMsg').css('color', 'white');
                                        $('#newOwnershipConfirmBtn').addClass('disabled');

                                        setTimeout(function ()
                                        {
                                            location.reload();
                                        }, 1250);
                                    } else if (data.detail === 'checkUserKo')
                                    {
                                        $('#newOwner').addClass('disabled');
                                        $('#newOwnershipResultMsg').show();
                                        $('#newOwnershipResultMsg').html('Error: New owner does not exists or it is not a valid LDAP user');
                                        $('#newOwnershipResultMsg').css('color', '#f3cf58');
                                        $('#newOwnershipConfirmBtn').addClass('disabled');

                                        setTimeout(function ()
                                        {
                                            $('#newOwner').removeClass('disabled');
                                            $('#newOwnershipResultMsg').html('');
                                            $('#newOwnershipResultMsg').hide();
                                        }, 1750);
                                    } else if (data.detail === 'ApiCallKo1')
                                    {
                                        $('#newOwner').addClass('disabled');
                                        $('#newOwnershipResultMsg').show();
                                        $('#newOwnershipResultMsg').html('Error: New owner has exceeded his limits for dashboard ownership');
                                        $('#newOwnershipResultMsg').css('color', '#f3cf58');
                                        $('#newOwnershipConfirmBtn').addClass('disabled');

                                        setTimeout(function ()
                                        {
                                            $('#newOwner').removeClass('disabled');
                                            $('#newOwnershipResultMsg').html('');
                                            $('#newOwnershipResultMsg').hide();
                                        }, 1750);
                                    } else
                                    {
                                        $('#newOwner').addClass('disabled');
                                        $('#newOwnershipResultMsg').show();
                                        $('#newOwnershipResultMsg').html('Error setting new ownership: please try again');
                                        $('#newOwnershipResultMsg').css('color', '#f3cf58');
                                        $('#newOwnershipConfirmBtn').addClass('disabled');

                                        setTimeout(function ()
                                        {
                                            $('#newOwner').removeClass('disabled');
                                            $('#newOwnershipResultMsg').html('');
                                            $('#newOwnershipResultMsg').hide();
                                        }, 1500);
                                    }
                                },
                                error: function (errorData)
                                {
                                    $('#newOwner').addClass('disabled');
                                    $('#newOwnershipResultMsg').html('Error setting new ownership: please try again');
                                    $('#newOwnershipConfirmBtn').addClass('disabled');

                                    setTimeout(function ()
                                    {
                                        $('#newOwner').removeClass('disabled');
                                        $('#newOwnershipResultMsg').html('');
                                        $('#newOwnershipResultMsg').hide();
                                    }, 1500);
                                }
                            });
                        });

                        $('#newVisibilityConfirmBtn').off('click');
                        $('#newVisibilityConfirmBtn').click(function () {
                            $.ajax({
                                url: "../controllers/changeDashboardVisibility.php",
                                data:
                                        {
                                            dashboardId: $('#delegationsDashId').val(),
                                            dashboardTitle: $('#delegationsDashboardTitle').html(),
                                            newVisibility: $('#newVisibility').val()
                                        },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function (data)
                                {
                                    if (data.detail === 'Ok')
                                    {
                                        $('#newVisibilityResultMsg').show();
                                        $('#newVisibilityResultMsg').html('New visibility set correctly');
                                        $('#newVisibilityConfirmBtn').addClass('disabled');

                                        setTimeout(function ()
                                        {
                                            location.reload();
                                        }, 1250);
                                    } else
                                    {
                                        $('#newVisibilityResultMsg').show();
                                        $('#newVisibilityResultMsg').html('Error setting new visibility');
                                        $('#newVisibilityConfirmBtn').addClass('disabled');

                                        setTimeout(function ()
                                        {
                                            $('#newVisibilityConfirmBtn').removeClass('disabled');
                                            $('#newVisibilityResultMsg').html('');
                                            $('#newVisibilityResultMsg').hide();
                                        }, 1500);
                                    }
                                },
                                error: function (errorData)
                                {
                                    $('#newVisibilityResultMsg').show();
                                    $('#newVisibilityResultMsg').html('Error setting new visibility');
                                    $('#newVisibilityConfirmBtn').addClass('disabled');

                                    setTimeout(function ()
                                    {
                                        $('#newVisibilityConfirmBtn').removeClass('disabled');
                                        $('#newVisibilityResultMsg').html('');
                                        $('#newVisibilityResultMsg').hide();
                                    }, 1500);
                                }
                            });
                        });

                        $('#newDelegationConfirmBtn').off('click');
                        $('#newDelegationConfirmBtn').click(function () {
                            $.ajax({
                                url: "../controllers/addDashboardDelegation.php",
                                data:
                                        {
                                            dashboardId: $('#delegationsDashId').val(),
                                            newDelegated: $('#newDelegation').val()
                                        },
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                success: function (data)
                                {
                                    if (data.detail === 'Ok')
                                    {
                                        $('#delegationsTable tbody').append('<tr class="delegationTableRow" data-delegationId="' + data.delegationId + '" data-delegated="' + $('#newDelegation').val() + '"><td class="delegatedName">' + $('#newDelegation').val() + '</td><td><i class="fa fa-remove removeDelegationBtn"></i></td></tr>');

                                        $('#delegationsTable tbody .removeDelegationBtn').off('click');
                                        $('#delegationsTable tbody .removeDelegationBtn').click(function () {
                                            var rowToRemove = $(this).parents('tr');
                                            $.ajax({
                                                url: "../controllers/delDashboardDelegation.php",
                                                data:
                                                        {
                                                            dashboardId: $('#delegationsDashId').val(),
                                                            delegationId: $(this).parents('tr').attr('data-delegationId')
                                                        },
                                                type: "POST",
                                                async: true,
                                                dataType: 'json',
                                                success: function (data)
                                                {
                                                    if (data.detail === 'Ok')
                                                    {
                                                        rowToRemove.remove();
                                                    } else
                                                    {
                                                        //TBD
                                                    }
                                                },
                                                error: function (errorData)
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

                                        setTimeout(function ()
                                        {
                                            $('#newDelegation').removeClass('disabled');
                                            $('#newDelegatedMsg').css('color', '#f3cf58');
                                            $('#newDelegatedMsg').html('Delegated username can\'t be empty');
                                        }, 1500);
                                    } else
                                    {
                                        var errorMsg = null;
                                        switch (data.detail)
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

                                        setTimeout(function ()
                                        {
                                            $('#newDelegation').removeClass('disabled');
                                            $('#newDelegatedMsg').css('color', '#f3cf58');
                                            $('#newDelegatedMsg').html('Delegated username can\'t be empty');
                                        }, 2000);
                                    }
                                },
                                error: function (errorData)
                                {
                                    var errorMsg = "Error calling internal API";
                                    $('#newDelegation').val('');
                                    $('#newDelegation').addClass('disabled');
                                    $('#newDelegatedMsg').css('color', '#f3cf58');
                                    $('#newDelegatedMsg').html(errorMsg);
                                    $('#newDelegationConfirmBtn').addClass('disabled');

                                    setTimeout(function ()
                                    {
                                        $('#newDelegation').removeClass('disabled');
                                        $('#newDelegatedMsg').css('color', '#f3cf58');
                                        $('#newDelegatedMsg').html('Delegated username can\'t be empty');
                                    }, 2000);
                                }
                            });
                        });

                        $('#newGroupDelegationConfirmBtn').off('click');
                        $('#newGroupDelegationConfirmBtn').click(function () {
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
                                                if (groupDel != "" && groupDel != "All Groups") {
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
                                        url: "../controllers/addGroupDashboardDelegation.php",
                                        data:
                                                {
                                                    dashboardId: $('#delegationsDashId').val(),
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
                                                        url: "../controllers/delDashboardGroupDelegation.php",
                                                        data:
                                                                {
                                                                    dashboardId: $('#delegationsDashId').val(),
                                                                    delegationId: $(this).parents('tr').attr('data-delegationId')
                                                                },
                                                        type: "POST",
                                                        async: true,
                                                        dataType: 'json',
                                                        success: function (data) {
                                                            if (data.detail === 'Ok') {
                                                                rowToRemove.remove();
                                                            } else {
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
                                            } else {
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

                        if ($(this).find('.delegateDashBtnCard').length > 0)
                        {
                            $(this).find('.delegateDashBtnCard').off('click');
                            $(this).find('.delegateDashBtnCard').click(function ()
                            {
                                $('#delegationsTable tbody').empty();
                                $('#groupDelegationsTable tbody').empty();
                                var dashboardId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
                                var dashboardTitle = $(this).parents('div.dashboardsListCardDiv').attr('data-dashTitle');

                                $('#delegationsDashId').val(dashboardId);
                                $('#delegationsDashboardTitle').html(dashboardTitle);
                                //
                                timetrend();
                                //
                                if ($(this).parents('div.dashboardsListCardDiv').find('div.dashboardsListCardVisibilityDiv').html().includes('Public'))
                                {
                                    $('#newVisibility').val('public');
                                    $('#delegationsFormRow').hide();
                                    $('#groupDelegationsFormRow').hide();
                                    $('#delegationsNotAvailableRow').show();
                                    $('#groupDelegationsNotAvailableRow').show();
                                } else
                                {
                                    $('#newVisibility').val('private');
                                    $('#delegationsNotAvailableRow').hide();
                                    $('#groupDelegationsNotAvailableRow').hide();
                                    $('#delegationsFormRow').show();
                                    $('#groupDelegationsFormRow').show();
                                }

                                $('#delegationsDashPic').css("background-image", "url(../img/dashScr/dashboard" + dashboardId + "/lastDashboardScr.png)");
                                $('#delegationsDashPic').css("background-size", "100% auto");
                                $('#delegationsDashPic').css("background-repeat", "no-repeat");
                                $('#delegationsDashPic').css("background-position", "center top");

                                $('#newOwner').val('');
                                $('#newOwner').off('input');
                                $('#newOwner').on('input', function (e)
                                {
                                    if ($(this).val().trim() === '')
                                    {
                                        $('#newOwnerMsg').css('color', '#f3cf58');
                                        $('#newOwnerMsg').html('New owner username can\'t be empty');
                                        $('#newOwnershipConfirmBtn').addClass('disabled');
                                    } else
                                    {
                                        if (($(this).val().trim() === "<?= @$_SESSION['loggedUsername'] ? : '' ?>") && ("<?= @$_SESSION['loggedRole'] ?>" !== "RootAdmin"))
                                        {
                                            $('#newOwnerMsg').css('color', '#f3cf58');
                                            $('#newOwnerMsg').html('New owner can\'t be you');
                                            $('#newOwnershipConfirmBtn').addClass('disabled');
                                        } else
                                        {
                                            $('#newOwnerMsg').css('color', 'white');
                                            $('#newOwnerMsg').html('User can be new owner');
                                            $('#newOwnershipConfirmBtn').removeClass('disabled');
                                        }
                                    }
                                });

                                $('#newDelegation').val('');
                                $('#newDelegation').off('input');
                                $('#newDelegation').on('input', function (e)
                                {
                                    if ($(this).val().trim() === '')
                                    {
                                        $('#newDelegatedMsg').css('color', '#f3cf58');
                                        $('#newDelegatedMsg').html('Delegated username can\'t be empty');
                                        $('#newDelegationConfirmBtn').addClass('disabled');
                                    } else
                                    {
                                        $('#newDelegatedMsg').css('color', 'white');
                                        $('#newDelegatedMsg').html('User can be delegated');
                                        $('#newDelegationConfirmBtn').removeClass('disabled');

                                        $('#delegationsTable tbody tr').each(function (i)
                                        {
                                            if ($(this).attr('data-delegated').trim() === $('#newDelegation').val())
                                            {
                                                $('#newDelegatedMsg').css('color', '#f3cf58');
                                                $('#newDelegatedMsg').html('User already delegated');
                                                $('#newDelegationConfirmBtn').addClass('disabled');
                                            }
                                        });

                                        /*      $('#groupDelegationsTable tbody tr').each(function(i)
                                         {
                                         if($(this).attr('data-delegated').trim() === $('#newDelegation').val())
                                         {
                                         $('#newGroupDelegatedMsg').css('color', '#f3cf58');
                                         $('#newGroupDelegatedMsg').html('User already delegated');
                                         //   $('#newDelegationConfirmBtn').addClass('disabled');
                                         }
                                         }); */
                                    }
                                });

                                $('#delegationsModal').modal('show');

                                $.ajax({
                                    url: "../controllers/getDashboardDelegations.php",
                                    data:
                                            {
                                                dashboardId: dashboardId
                                            },
                                    type: "GET",
                                    async: true,
                                    dataType: 'json',
                                    success: function (data)
                                    {
                                        for (var i = 0; i < data.length; i++)
                                        {
                                            if (data[i].delegatedUser != null && data[i].delegatedUser != undefined) {
                                                $('#delegationsTable tbody').append('<tr class="delegationTableRow" data-delegationId="' + data[i].delegationId + '" data-delegated="' + data[i].delegatedUser + '"><td class="delegatedName">' + data[i].delegatedUser + '</td><td><i class="fa fa-remove removeDelegationBtn"></i></td></tr>');
                                            } else if (data[i].delegatedGroup != null && data[i].delegatedGroup != undefined) {
                                                $('#groupDelegationsTable tbody').append('<tr class="groupDelegationTableRow" data-delegationId="' + data[i].delegationId + '" data-delegated="' + data[i].delegatedGroup + '"><td class="delegatedName">' + data[i].delegatedGroup + '</td><td><i class="fa fa-remove removeDelegationBtn"></i></td></tr>');
                                            }
                                        }

                                        $('#delegationsTable tbody .removeDelegationBtn').click(function () {
                                            var rowToRemove = $(this).parents('tr');
                                            $.ajax({
                                                url: "../controllers/delDashboardDelegation.php",
                                                data:
                                                        {
                                                            dashboardId: dashboardId,
                                                            delegationId: $(this).parents('tr').attr('data-delegationId')
                                                        },
                                                type: "POST",
                                                async: true,
                                                dataType: 'json',
                                                success: function (data)
                                                {
                                                    if (data.detail === 'Ok')
                                                    {
                                                        rowToRemove.remove();
                                                    } else
                                                    {
                                                        //TBD
                                                    }
                                                },
                                                error: function (errorData)
                                                {
                                                    //TBD
                                                    console.log("Del dashboard ko: " + errorData);
                                                    console.log(JSON.stringify(errorData));
                                                }
                                            });
                                        });

                                        $('#groupDelegationsTable tbody .removeDelegationBtn').click(function () {
                                            document.body.style.cursor = "wait";
                                            var rowToRemove = $(this).parents('tr');
                                            $.ajax({
                                                url: "../controllers/delDashboardDelegation.php",
                                                data:
                                                        {
                                                            dashboardId: dashboardId,
                                                            delegationId: $(this).parents('tr').attr('data-delegationId')
                                                        },
                                                type: "POST",
                                                async: true,
                                                dataType: 'json',
                                                success: function (data)
                                                {
                                                    document.body.style.cursor = "default";
                                                    if (data.detail === 'Ok')
                                                    {
                                                        rowToRemove.remove();
                                                    } else
                                                    {
                                                        //TBD
                                                    }
                                                },
                                                error: function (errorData)
                                                {
                                                    //TBD
                                                    document.body.style.cursor = "default";
                                                    console.log("Del dashboard ko: " + errorData);
                                                    console.log(JSON.stringify(errorData));
                                                }
                                            });
                                        });

                                    },
                                    error: function (errorData)
                                    {
                                        console.log("Get Dashboard Delegations dashboard ko: " + errorData);
                                        console.log(JSON.stringify(errorData));
                                    }
                                });
                            });
                        }



                        $(this).find('.deleteDashBtnCard').off('click');
                        $(this).find('.deleteDashBtnCard').click(function ()
                        {
                            var dashboardId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
                            var dashboardTitle = $(this).parents('div.dashboardsListCardDiv').attr('data-dashTitle');

                            $('#dashIdDelHidden').val(dashboardId);
                            $('#dashToDelName').html(dashboardTitle);

                            $('#dashToDelPic').css("background-image", "url(../img/dashScr/dashboard" + dashboardId + "/lastDashboardScr.png)");
                            $('#dashToDelPic').css("background-size", "100% auto");
                            $('#dashToDelPic').css("background-repeat", "no-repeat");
                            $('#dashToDelPic').css("background-position", "center top");
                            $('#modalDelDash').modal('show');
                        });

                        $('#delDashConfirmBtn').off("click");
                        $('#delDashConfirmBtn').click(function () {
                            $('#delDashNameMsg').parents('div.row').hide();
                            $('#delDashCancelBtn').hide();
                            $('#delDashConfirmBtn').hide();
                            $('#delDashRunningMsg').show();

                            $.ajax({
                                url: "../controllers/deleteDashboard.php",
                                data: {
                                    dashboardId: $('#dashIdDelHidden').val(),
                                    dashboardTitle: $('#dashToDelName').html()
                                },
                                async: true,
                                success: function (successData)
                                {
                                    $('#delDashRunningMsg').hide();

                                    if (successData !== 'Ok')
                                    {
                                        $('#delDashKoMsg').show();
                                        console.log("Del dashboard ko: " + successData);
                                        setTimeout(function () {
                                            $('#modalDelWidget').modal('hide');
                                            setTimeout(function () {
                                                $('#delDashKoMsg').hide();
                                                $('#delDashNameMsg').parents('div.row').show();
                                                $('#delDashCancelBtn').show();
                                                $('#delDashConfirmBtn').show();
                                            }, 750);
                                        }, 2500);
                                    } else
                                    {
                                        $('#delDashOkMsg').show();

                                        setTimeout(function () {
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
                                error: function (errorData)
                                {
                                    $('#delDashRunningMsg').hide();
                                    $('#delDashKoMsg').show();
                                    setTimeout(function () {
                                        $('#modalDelWidget').modal('hide');
                                        setTimeout(function () {
                                            $('#delDashKoMsg').hide();
                                            $('#delDashNameMsg').parents('div.row').show();
                                            $('#delDashCancelBtn').show();
                                            $('#delDashConfirmBtn').show();
                                        }, 750);
                                    }, 2500);
                                    console.log("Del dashboard ko: " + errorData);
                                    console.log(JSON.stringify(errorData));
                                }
                            });


                        });

                        $(this).find('.editDashBtnCard').off('click');
                        $(this).find('.editDashBtnCard').click(function ()
                        {
                            var dashboardId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
                            var dashboardTitle = $(this).parents('div.dashboardsListCardDiv').attr('data-dashTitle');

                            $.ajax({
                                url: "../controllers/getDashboardEditPermissions.php",
                                type: "GET",
                                data: {
                                    openDashboardToEdit: true,
                                    dashboardId: dashboardId
                                },
                                async: true,
                                dataType: 'json',
                                success: function (data)
                                {
                                    switch (data['detail'])
                                    {
                                        case "Ok":
                                            window.open("../management/dashboard_configdash.php?dashboardId=" + dashboardId + "&dashboardAuthorName=" + encodeURI(data.dashboardAuthorName) + "&dashboardEditorName=" + encodeURI("<?= @$_SESSION['loggedUsername']? : '' ?>" + "&dashboardTitle=" + encodeURI(dashboardTitle)));
                                            break;

                                        case "missingParam":
                                            //TBD modale
                                            break;

                                        case "unauthorized":
                                            //TBD modale
                                            break;

                                        default:
                                            //TBD modale
                                            break;
                                    }
                                },
                                error: function (errorData)
                                {
                                    console.log("Error editing dashboard");
                                    console.log(errorData);
                                }
                            });
                        });

                        $(this).find('.dashboardsListCardOverlayTxt').off('click');
                        $(this).find('.dashboardsListCardOverlayTxt').click(function ()
                        {
                            var dashboardId = $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid');
                            window.open("../view/index.php?iddasboard=" + btoa(dashboardId));
                        });
                    });

                    $('#searchDashboardBtn').off('click');
                    $('#searchDashboardBtn').click(function () {
                        var dynatable = $('#list_dashboard_cards').data('dynatable');
                        dynatable.queries.run();
                    });

                    $('#resetSearchDashboardBtn').off('click');
                    $('#resetSearchDashboardBtn').click(function () {
                        var dynatable = $('#list_dashboard_cards').data('dynatable');
                        $("#dynatable-query-search-list_dashboard_cards").val("");
                        dynatable.queries.runSearch("");
                    });
<?php if (!$_SESSION['isPublic']) : ?>
                        //Apertura modale elenco IOT apps
                        $('span.dashboardListCardTypeSpan[data-hasIotModal="true"]').off("click");
                        $('span.dashboardListCardTypeSpan[data-hasIotModal="true"]').click(function ()
                        {
                            $('#iotAppsDashboardTitle').html($(this).parents('div.dashboardsListCardDiv').attr('data-dashTitle'));
                            $('#iotAppsDashPic').css("background-image", "url(../img/dashScr/dashboard" + $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid') + "/lastDashboardScr.png)");
                            $('#iotAppsDashPic').css("background-size", "100% auto");
                            $('#iotAppsDashPic').css("background-repeat", "no-repeat");
                            $('#iotAppsDashPic').css("background-position", "center top");

                            $('#iotAppsModal').modal('show');

                            $.ajax({
                                url: "../controllers/getDashboardIotApps.php",
                                data:
                                        {
                                            dashboardId: $(this).parents('div.dashboardsListCardDiv').attr('data-uniqueid')
                                        },
                                type: "GET",
                                async: true,
                                dataType: 'json',
                                success: function (data)
                                {
                                    if (data.result === 'Ok')
                                    {
                                        var count = 0;
                                        $('#iotAppsTable tbody').empty();
                                        $('#iotAppsIframe').attr('src', 'about:blank');

                                        if ((data.appsFromOwnership === 0) && (data.appsFromQuery > 0))
                                        {
                                            $('#iotAppsTableCnt').hide();
                                            $('#iotAppsNoAppsCnt').show();
                                        } else
                                        {
                                            $('#iotAppsNoAppsCnt').hide();
                                            $('#iotAppsTableCnt').show();
                                            for (var appId in data.applications)
                                            {
                                                $('#iotAppsTable tbody').append('<tr class="delegationTableRow" data-url="' + data.applications[appId].url + '"><td class="delegatedName iotAppName">' + data.applications[appId].name + '</td></tr>');
                                                count++;
                                            }

                                            $('#iotAppsTable tbody .iotAppName').off('click');
                                            $('#iotAppsTable tbody .iotAppName').click(function () {
                                                $('#iotAppsIframe').attr('src', $(this).parents('tr').attr('data-url'));
                                            });
                                        }
                                    } else
                                    {
                                        //TBD
                                    }
                                },
                                error: function (errorData)
                                {
                                    //TBD
                                }
                            });
                        });
<?php endif; ?>
                });

                $('#list_dashboard_cards').dynatable({
                    table: {
                        bodyRowSelector: 'div'
                    },
                    dataset: {
                        records: data,
                        perPageDefault: 12,
                        perPageOptions: [4, 8, 12, 16, 20, 24, 28, 32]
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
                        search: true,
                        pushState: dynaTablePushState
                    }
                });

                var dynatable = $('#list_dashboard_cards').data('dynatable');

                //    $("#row mainRow").include('mainMenu.php');

                dynatable.sorts.clear();
                dynatable.sorts.add('title_header', 1); // 1=ASCENDING, -1=DESCENDING
                dynatable.process();
                // PARTIAL MOD TO BE COMMITTED
                /*  if (orgFlag.includes("My orgMy?linkId")) {
                 $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(255, 204, 0, 1)');
                 $('#myIcon').attr("data-active", "true");
                 } //else {
                 //  $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(255, 204, 0, 1)');
                 //  $('#publicIcon').attr("data-active", "true");
                 // }*/
                if (orgFlag.includes("My orgMy?linkId")) {
                    if (loggedRole != "RootAdmin") {
                        dynatable.queries.runSearch("");
                    } else {
                        //    dynatable.queries.runSearch("My own");
                        //    $('#dashboardListsCardsSort i.dashboardsListSort').eq(2).click();
                        if ($('#dashboardListsCardsSort i.dashboardsListSort').eq(2).attr("data-active") === "false")
                        {
                            $('#dashboardListsCardsSort i.dashboardsListSort').eq(2).attr("data-active", "true");
                            $('#dashboardListsCardsSort i.dashboardsListSort').eq(3).attr("data-active", "false");
                            dynatable.queries.runSearch("My own");
                            $('#dynatable-query-search-list_dashboard_cards').val("");
                            $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                            $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(255, 204, 0, 1)');
                            $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(4).css('background-color', 'rgba(0, 162, 211, 1)');
                        } else
                        {
                            $('#dashboardListsCardsSort i.dashboardsListSort').eq(2).attr("data-active", "false");
                            dynatable.queries.runSearch("");
                            $('#dynatable-query-search-list_dashboard_cards').val("");
                            $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                            $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(0, 162, 211, 1)');
                            $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(4).css('background-color', 'rgba(0, 162, 211, 1)');
                        }
                    }
                }

                $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                $('#dashboardListsCardsSort i.dashboardsListSort').eq(0).click(function () {
                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                    dynatable.sorts.clear();
                    dynatable.sorts.add('title_header', 1); // 1=ASCENDING, -1=DESCENDING
                    dynatable.process();

                    $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(1).css('background-color', 'rgba(0, 162, 211, 1)');
                    $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');

                    $('#dashboardListsCardsSort').eq(2).off('mouseover');
                    $('#dashboardListsCardsSort').eq(3).off('mouseover');
                    $('#dashboardListsCardsSort').eq(2).off('mouseout');
                    $('#dashboardListsCardsSort').eq(3).off('mouseout');

                    $('#dashboardListsCardsSort').eq(2).hover(function () {
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(255, 204, 0, 1)');
                    }, function () {
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(0, 162, 211, 1)');
                    });

                    $('#dashboardListsCardsSort').eq(3).hover(function () {
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(255, 204, 0, 1)');
                    }, function () {
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                    });
                });

                $('#dashboardListsCardsSort i.dashboardsListSort').eq(1).click(function () {
                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                    dynatable.sorts.clear();
                    dynatable.sorts.add('title_header', -1); // 1=ASCENDING, -1=DESCENDING
                    dynatable.process();
                    $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(0).css('background-color', 'rgba(0, 162, 211, 1)');
                    $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(1).css('background-color', 'rgba(255, 204, 0, 1)');

                    $('#dashboardListsCardsSort').eq(2).off('mouseover');
                    $('#dashboardListsCardsSort').eq(3).off('mouseover');
                    $('#dashboardListsCardsSort').eq(2).off('mouseout');
                    $('#dashboardListsCardsSort').eq(3).off('mouseout');

                    $('#dashboardListsCardsSort').eq(2).hover(function () {
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(255, 204, 0, 1)');
                    }, function () {
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(0, 162, 211, 1)');
                    });

                    $('#dashboardListsCardsSort').eq(3).hover(function () {
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(255, 204, 0, 1)');
                    }, function () {
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                    });
                });

                $('#dashboardListsCardsSort').eq(2).off('mouseover');
                $('#dashboardListsCardsSort').eq(3).off('mouseover');
                $('#dashboardListsCardsSort').eq(2).off('mouseout');
                $('#dashboardListsCardsSort').eq(3).off('mouseout');

                $('#dashboardListsCardsSort').eq(2).hover(function () {
                    $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(255, 204, 0, 1)');
                }, function () {
                    $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(0, 162, 211, 1)');
                });

                $('#dashboardListsCardsSort').eq(3).hover(function () {
                    $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(255, 204, 0, 1)');
                }, function () {
                    $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                });

                $('#dashboardListsCardsSort i.dashboardsListSort').eq(2).click(function () {
                    var dynatable = $('#list_dashboard_cards').data('dynatable');

                    if ($(this).attr("data-active") === "false")
                    {
                        $(this).attr("data-active", "true");
                        $('#dashboardListsCardsSort i.dashboardsListSort').eq(3).attr("data-active", "false");
                        dynatable.queries.runSearch("My own");
                        $('#dynatable-query-search-list_dashboard_cards').val("");
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(255, 204, 0, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(4).css('background-color', 'rgba(0, 162, 211, 1)');
                    } else
                    {
                        $(this).attr("data-active", "false");
                        dynatable.queries.runSearch("");
                        $('#dynatable-query-search-list_dashboard_cards').val("");
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(4).css('background-color', 'rgba(0, 162, 211, 1)');
                    }
                });

                $('#dashboardListsCardsSort i.dashboardsListSort').eq(3).click(function () {
                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                    if ($(this).attr("data-active") === "false")
                    {
                        $(this).attr("data-active", "true");
                        $('#dashboardListsCardsSort i.dashboardsListSort').eq(2).attr("data-active", "false");
                        dynatable.queries.runSearch("Public");
                        $('#dynatable-query-search-list_dashboard_cards').val("");
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(255, 204, 0, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(4).css('background-color', 'rgba(0, 162, 211, 1)');
                    } else
                    {
                        $(this).attr("data-active", "false");
                        dynatable.queries.runSearch("");
                        $('#dynatable-query-search-list_dashboard_cards').val("");
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(4).css('background-color', 'rgba(0, 162, 211, 1)');
                    }
                });

                $('#dashboardListsCardsSort i.dashboardsListSort').eq(4).click(function () {
                    var dynatable = $('#list_dashboard_cards').data('dynatable');
                    if ($(this).attr("data-active") === "false")
                    {
                        $(this).attr("data-active", "true");
                        $('#dashboardListsCardsSort i.dashboardsListSort').eq(2).attr("data-active", "false");
                        dynatable.queries.runSearch("Delegated by");
                        $('#dynatable-query-search-list_dashboard_cards').val("");
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(4).css('background-color', 'rgba(255, 204, 0, 1)');
                    } else
                    {
                        $(this).attr("data-active", "false");
                        dynatable.queries.runSearch("");
                        $('#dynatable-query-search-list_dashboard_cards').val("");
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(3).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(2).css('background-color', 'rgba(0, 162, 211, 1)');
                        $('#dashboardListsCardsSort div.dashboardsListSortBtnCnt').eq(4).css('background-color', 'rgba(0, 162, 211, 1)');
                    }
                });

                // Buttons for Filter by Organization

                /*      $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                 $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", true);
                 //      dynatable.queries.runSearch("My own");
                 //   dynatable.queries.runSearch(org + "__org");
                 //  dynatable.process();
                 
                 $('#dashboardListsCardsOrgsSort i.dashboardsListSort').eq(0).click(function(){
                 //   var dynatable = $('#list_dashboard_cards').data('dynatable');
                 
                 if($('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active") === "true")
                 {
                 $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", "false");
                 //      dynatable.queries.runSearch("");
                 //    dynatable.queries.remove("organizations");
                 //    dynatable.queries.runSearch("");
                 $('#dynatable-query-search-list_dashboard_cards').val("");
                 //   $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(1).css('background-color', 'rgba(0, 162, 211, 1)');
                 $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).css('background-color', 'rgba(0, 162, 211, 1)');
                 $('#headerSubTitleCnt').text("(All Organizations)");
                 }
                 else
                 {
                 $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).attr("data-active", "true");
                 //   dynatable.queries.runSearch(org + "__org");
                 $('#dynatable-query-search-list_dashboard_cards').val("");
                 //   $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(1).css('background-color', 'rgba(0, 162, 211, 1)');
                 $('#dashboardListsCardsOrgsSort div.dashboardsListSortOrgsBtnCnt').eq(0).css('background-color', 'rgba(255, 204, 0, 1)');
                 $('#headerSubTitleCnt').text("(My Organization)");
                 }
                 });*/

                $('.dashboardsListSortOrgsBtnCnt').change(function () {
                    console.log("Entra !!!!");
                    /* var value = $(this).val();
                     if (value === "") {
                     dynatable.queries.remove("model");
                     } else {
                     dynatable.queries.add("model",value);
                     }
                     dynatable.process();  */
                });

<?php
if (@$_SESSION['loggedRole'] === 'RootAdmin') {
    ?>

                    $('#list_dashboard').bind('dynatable:afterProcess', function (e, dynatable) {
                        $('span.dynatable-per-page-label').remove();

                        //$('#dynatable-per-page-list_dashboard').parents('span.dynatable-per-page').appendTo("#dashboardListsItemsPerPage div.dashboardsListMenuItemContent");
                        //$('#dynatable-per-page-list_dashboard').addClass('form-control');

                        $("#dynatable-pagination-links-list_dashboard").appendTo("#dashboardListsPages div.dashboardsListMenuItemContent");
                        $("#dynatable-pagination-links-list_dashboard li").eq(0).remove();
                        //$("#dynatable-pagination-links-list_dashboard li").eq(0).remove();
                        //$("#dynatable-pagination-links-list_dashboard li").eq($("#dynatable-pagination-links-list_dashboard li").length - 1).remove();
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("font-weight", "bold");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent').css("color", "white");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("font-family", "Montserrat");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("font-weight", "bold");
                        $('#dashboardListsPages div.dashboardsListMenuItemContent a').css("color", "white");
                        $("ul#dynatable-pagination-links-list_dashboard").css("-webkit-padding-start", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard").css("-webkit-margin-before", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard").css("-webkit-margin-after", "0px");
                        $("ul#dynatable-pagination-links-list_dashboard").css("padding", "0px");

                        $("#dynatable-query-search-list_dashboard").prependTo("#dashboardListsSearchFilter div.dashboardsListMenuItemContent div.input-group");
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

                        $('#list_dashboard tbody input.changeDashboardStatus').off('change');
                        $('#list_dashboard tbody input.changeDashboardStatus').change(function () {
                            if ($(this).prop('checked') === false)
                            {
                                var newStatus = 0;
                            } else
                            {
                                var newStatus = 1;
                            }

                            $.ajax({
                                url: "process-form.php",
                                data: {
                                    modify_status_dashboard: true,
                                    dashboardId: $(this).parents('tr').attr('data-uniqueid'),
                                    newStatus: newStatus
                                },
                                type: "POST",
                                async: true,
                                success: function (data)
                                {
                                    if (data !== "Ok")
                                    {
                                        console.log("Error updating dashboard status");
                                        console.log(data);
                                        alert("Error updating dashboard status");
                                        location.reload();
                                    } else
                                    {
                                        if ($('#dashboardTotActiveCnt .pageSingleDataCnt').html() !== "-")
                                        {
                                            if (newStatus === 0)
                                            {
                                                $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) - 1);
                                            } else
                                            {
                                                $('#dashboardTotActiveCnt .pageSingleDataCnt').html(parseInt($('#dashboardTotActiveCnt .pageSingleDataCnt').html()) + 1);
                                            }
                                        }
                                    }
                                },
                                error: function (errorData)
                                {
                                    console.log("Error updating dashboard status");
                                    console.log(errorData);
                                    alert("Error updating dashboard status");
                                    location.reload();
                                }
                            });
                        });

                        $('#list_dashboard button.editDashBtn').off('click');
                        $('#list_dashboard button.editDashBtn').click(function ()
                        {
                            var dashboardId = $(this).parents('tr').attr('data-uniqueid');
                            var dashboardTitle = $(this).parents('tr').attr('data-dashTitle');
                            var dashboardAuthorName = $(this).parents('tr').attr('data-authorName');

                            window.open("../management/dashboard_configdash.php?dashboardId=" + dashboardId + "&dashboardAuthorName=" + dashboardAuthorName + "&dashboardEditorName=" + encodeURI("<?= @$_SESSION['loggedUsername'] ? : '' ?>" + "&dashboardTitle=" + encodeURI(dashboardTitle)));
                        });

                        $('#list_dashboard button.viewDashBtn').off('click');
                        $('#list_dashboard button.viewDashBtn').click(function ()
                        {
                            var dashboardId = $(this).parents('tr').attr("data-uniqueid");
                            window.open("../view/index.php?iddasboard=" + btoa(dashboardId));
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
                            perPageSelect: false,
                            pushState: dynaTablePushState
                        },
                        inputs: {
                            perPagePlacement: 'after'
                        }
                    });
                    $("#dynatable-pagination-links-list_dashboard").hide();
                    $("#dynatable-query-search-list_dashboard").hide();

    <?php
}
?>
            },
            error: function (errorData)
            {
                console.log("Errore in caricamento dashboards");
                console.log(JSON.stringify(errorData));
            }
        });

        $('.customColorChoice').colorpicker({
            format: "rgba"
        });

        //NON CANCELLARE - Caricamento dell'insieme di visibilità per l'utente collegato
        /*$.ajax({
         url: "getUserVisibilitySet.php",
         type: "POST",
         async: true,
         dataType: 'JSON',
         cache: false, 
         success: function (data) 
         {
         userVisibilitySet = data;
         
         $("#inputDashboardVisibilityUsersTable").append('<tr><th class="selectCell">Select</th><th class="usernameCell">Username</th></tr>');
         
         for(var i = 0; i < userVisibilitySet.length; i++)
         {
         $("#inputDashboardVisibilityUsersTable").append('<tr><td><input type="checkbox" name="selectedVisibilityUsers[]" value="' + userVisibilitySet[i] + '"/></td><td>' + userVisibilitySet[i] + '</td></tr>'); 
         }
         
         //Metodo apposito per settare/desettare gli attributi checked sulle checkbox
         $('#inputDashboardVisibilityUsersTable input[type="checkbox"').off('click');
         $('#inputDashboardVisibilityUsersTable input[type="checkbox"').click(function(){
         if($(this).attr("checked") === "checked")
         {
         $(this).removeAttr("checked");
         }
         else
         {
         $(this).attr("checked", "true");
         }
         });
         },
         error: function (data) 
         {
         //TBD
         console.log("Error: " + JSON.stringify(data));
         }
         });*/

        //Apertura nuova dashboard quando appena creata
<?php
if (isset($_GET['newDashId']) && isset($_GET['newDashAuthor']) && isset($_GET['newDashTitle'])) {
    echo 'window.open("../management/dashboard_configdash.php?dashboardId=' . urlencode(filter_input(INPUT_GET, 'newDashId', FILTER_SANITIZE_NUMBER_INT)) . '&dashboardAuthorName=' . urlencode(filter_input(INPUT_GET, 'newDashAuthor', FILTER_SANITIZE_STRING)) . '&dashboardEditorName=' . urlencode(filter_input(INPUT_GET, 'newDashAuthor', FILTER_SANITIZE_STRING)) . '&dashboardTitle=' . urlencode(filter_input(INPUT_GET, 'newDashTitle', FILTER_SANITIZE_STRING)) . '");';
    echo 'history.replaceState(null, null, "dashboards.php");';
}
?>

        $('[data-toggle="tooltip"]').tooltip();





        if (loggedRole == 'RootAdmin') {
            $.ajax({
                url: "../api/ldap.php",
                data: {
                    action: "get_all_ou",
                    token: "<?= @$_SESSION['refreshToken'] ?>"
                },
                type: "POST",
                async: false,
                success: function (data)
                {
                    if (data["status"] === 'ko')
                    {
                        $('#newDelegatedMsgGroup').css('color', '#f3cf58');
                        $('#newDelegatedMsgGroup').html(data["msg"]);
                    } else if (data["status"] === 'ok')
                    {
                        var $dropdown = $("#newDelegationOrganization");
                        $.each(data['content'], function () {
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
                data: {
                    action: "get_logged_ou",
                    username: usr,
                    token: "<?= @$_SESSION['refreshToken'] ?>"
                },
                type: "POST",
                async: false,
                success: function (data)
                {
                    if (data["status"] === 'ko')
                    {
                        console.log("Error: " + data);
                        //TODO: manage error
                    } else if (data["status"] === 'ok')
                    {
                        var $dropdown = $("#newDelegationOrganization");
                        $dropdown.append($("<option/>").val(data['content']).text(data['content']));
                    }
                },
                error: function (data)
                {
                    console.log("Error: " + data);
                    //TODO: manage error
                }
            });
        }

        // NEW TABS FOR GROPs/ORGANIZATIONs DELEGATION
        //populate group list with selected organization
        updateGroupList($("#newDelegationOrganization").val());

        //eventually update the group list
        $('#newDelegationOrganization').change(function () {
            $(this).find(":selected").each(function () {
                updateGroupList($(this).val());
            });
        });

        //eventually update the group list
        $('#newDelegationGroup').change(function () {
            // $(this).find(":selected").each(function () {
            if ($('#newGroupDelegationConfirmBtn').hasClass('disabled')) {
                $('#newGroupDelegationConfirmBtn').removeClass('disabled');
                $('#newGroupDelegatedMsg').html('');
            }
            //  });
        });

        //   function loadWizardModal (allDashboardsList) {
<?php if (!$_SESSION['isPublic']) : ?>
            $("#addWidgetWizardLabelBody").load("addWidgetWizardInclusionCode.php", function () {

                if (!allDashboardsList) {
                    getAllGlobalDashboards();
                }

                $('#inputTitleDashboard').on('input', function (e) {
                    if ($(this).val().trim() === '') {
                        $('#modalAddDashboardWizardTitleAlreadyUsedMsg').css('color', '#f3cf58');
                        $('#modalAddDashboardWizardTitleAlreadyUsedMsg .centerWithFlex').html('Dashboard title can\'t be empty');
                        $('#inputTitleDashboardStatus').val('empty');
                    } else if ($(this).val().includes('"') || $(this).val().includes("'")) {
                        $('#modalAddDashboardWizardTitleAlreadyUsedMsg').css('color', '#f3cf58');
                        $('#modalAddDashboardWizardTitleAlreadyUsedMsg .centerWithFlex').html('Single or double quotes are not allowed in dashboard title.');
                        $('#inputTitleDashboardStatus').val('alreadyUsed');
                    } else {
                        if ($(this).val().length > 300) {
                            $('#modalAddDashboardWizardTitleAlreadyUsedMsg').css('color', '#f3cf58');
                            $('#modalAddDashboardWizardTitleAlreadyUsedMsg .centerWithFlex').html('Dashboard title can\'t be longer than 300 chars');
                            $('#inputTitleDashboardStatus').val('tooLong');
                        } else {
                            var ok = true;
                            /*   for (var i = 0; i < allDashboardsList.length; i++) {
                             if ($(this).val().trim().toLowerCase() === allDashboardsList[i].title_header.toLowerCase()) {
                             ok = false;
                             break;
                             }
                             }*/
                            for (var i = 0; i < allGlobalDashboards.length; i++) {
                                if ($(this).val().trim().toLowerCase() === allGlobalDashboards[i].title_header.toLowerCase()) {
                                    ok = false;
                                    break;
                                }
                            }
                            if (!ok) {
                                $('#modalAddDashboardWizardTitleAlreadyUsedMsg').css('color', '#f3cf58');
                                $('#modalAddDashboardWizardTitleAlreadyUsedMsg .centerWithFlex').html('Dashboard title already in use');
                                $('#inputTitleDashboardStatus').val('alreadyUsed');
                            } else {
                                $('#modalAddDashboardWizardTitleAlreadyUsedMsg').css('color', 'white');
                                $('#modalAddDashboardWizardTitleAlreadyUsedMsg .centerWithFlex').html('Dashboard title OK');
                                $('#inputTitleDashboardStatus').val('ok');
                            }
                        }
                    }

                    if (($('#dashboardTemplateStatus').val() === 'ok') && ($('#inputTitleDashboardStatus').val() === 'ok')) {
                        if ($('#dashboardDirectStatus').val() != "yes") {
                            $('#bTab a').attr("data-toggle", "tab");
                            $('#addWidgetWizardNextBtn').removeClass('disabled');
                        } else {
                            $('#addWidgetWizardNextBtn').removeClass('disabled');
                        }
                    } else {
                        $('#bTab a').attr("data-toggle", "no");
                        $('#addWidgetWizardNextBtn').addClass('disabled');
                    }
                });
            });
<?php endif; ?>
        //    }

        //$('#delegateDashBtnCard').click(function() {
        $('#trendTab').click(function () {
            //
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            //
            $('#start_accss').val(today);
            $('#start_min').val(today);
            //
             $('#menu_1').val(' 1 YEAR ');
             $('#menu_2').val(' 1 YEAR ');
            //
            //CAMBIO END
            var expr = $('#menu_1').val();
            switch (expr) {
                case ' 24 HOUR ':
                    //console.log('Oranges are $0.59 a pound.');
                    var today2 = new Date();
                    var dd2 = String(today2.getDate() - 1).padStart(2, '0');
                    var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy2 = today2.getFullYear();
                    today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                    $('#end_accss').val(today2);
                    $('#end_min').val(today2);
                    //
                    break;
                case ' 1 WEEK ':
                    //
                    var today2 = new Date();
                    var dd2 = String(today2.getDate() - 7).padStart(2, '0');
                    var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy2 = today2.getFullYear();
                    today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                    $('#end_accss').val(today2);
                    $('#end_min').val(today2);
                    //
                    break;
                case ' 1 MONTH ':
                    //console.log('Mangoes and papayas are $2.79 a pound.');
                    var today2 = new Date();
                    var dd2 = String(today2.getDate()).padStart(2, '0');
                    var mm2 = String(today2.getMonth()).padStart(2, '0'); //January is 0!
                    var yyyy2 = today2.getFullYear();
                    today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                    $('#end_accss').val(today2);
                    $('#end_min').val(today2);
                    //
                    // expected output: "Mangoes and papayas are $2.79 a pound."
                    break;
                case ' 6 MONTH ':
                    //console.log('Mangoes and papayas are $2.79 a pound.');
                    var today2 = new Date();
                    var dd2 = String(today2.getDate()).padStart(2, '0');
                    var mm2 = String(today2.getMonth() - 5).padStart(2, '0'); //January is 0!
                    var yyyy2 = today2.getFullYear();
                    today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                    $('#end_accss').val(today2);
                    $('#end_min').val(today2);
                    //
                    // expected output: "Mangoes and papayas are $2.79 a pound."
                    break;
                case ' 1 YEAR ':
                    //console.log('Mangoes and papayas are $2.79 a pound.');
                    var today2 = new Date();
                    var dd2 = String(today2.getDate()).padStart(2, '0');
                    var mm2 = String(today2.getMonth() - 1).padStart(2, '0'); //January is 0!
                    var yyyy2 = today2.getFullYear() - 1;
                    today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                    $('#end_accss').val(today2);
                    $('#end_min').val(today2);
                    //
                    // expected output: "Mangoes and papayas are $2.79 a pound."
                    break;
                default:
                    //
                    var today2 = new Date();
                    var dd2 = String(today2.getDate()).padStart(2, '0');
                    var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy2 = today2.getFullYear();
                    today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                    $('#end_accss').val(today2);
                    $('#end_min').val(today2);
                    //
                    //console.log(`Sorry, we are out of ${expr}.`);
            }


            //trendmodal
            $('#trendmodal').show();
            $('#delegationsModalRightCnt').hide();
            $('#delegationsModalLeftCnt').hide();
            //
            var del = $('#delegationsDashboardTitle').text();
            $('#trendDashboardTitle').text(del);
            //
            $('#delegationsCancelBtn').css('margin-top','10px');
            //
            var n_dash = $('#delegationsDashboardTitle').text();
            var chartRef = null;
            var menu_accesses = $('#menu_accesses').val();
            timetrend();
            timetrendaccesses();
            timetrendminutes();

        });
        //Manage other tabs
        $('.dashboardWizardTabTxt').click(function () {
            $('#trendmodal').hide();
            $('#delegationsModalRightCnt').show();
            $('#delegationsModalLeftCnt').show();
            $('#delegationsCancelBtn').css('margin-top','50px');
        });


        //
        $('#delegationsCancelBtn').click(function () {
            $('#container_accesses').empty();
            $('#container_minutes').empty();
            $('#trendmodal').hide();
            $('#delegationsModalRightCnt').show();
            $('#delegationsModalLeftCnt').show();
            //togliere actiive
             $('#menu_1').val(' 1 YEAR ');
             $('#menu_2').val(' 1 YEAR ');
             timetrend();
            $('#trendTab').removeClass('active');
            $('#ownershipTab').addClass('active');
            $('#delegationsCancelBtn').css('margin-top','50px');
            //
        });
        $('#groupDelegationsTab').click(function () {
            $('#container_accesses').empty();
            $('#container_minutes').empty();
            $('#trendmodal').hide();
            $('#delegationsModalRightCnt').show();
            $('#delegationsModalLeftCnt').show();
             //togliere actiive
             $('#menu_1').val(' 1 YEAR ');
             $('#menu_2').val(' 1 YEAR ');
            $('#trendTab').removeClass('active');
            $('#ownershipTab').addClass('active');
            $('#delegationsCancelBtn').css('margin-top','50px');
            //
        });
        $('#delegationsModal').on('hidden.bs.modal', function () {
           $('#container_accesses').empty();
            $('#container_minutes').empty();
            $('#trendmodal').hide();
            $('#delegationsModalRightCnt').show();
            $('#delegationsModalLeftCnt').show();
             //togliere actiive
             $('#menu_1').val(' 1 YEAR ');
             $('#menu_2').val(' 1 YEAR ');
             timetrend();
            $('#trendTab').removeClass('active');
            $('#ownershipTab').addClass('active');
            $('#delegationsCancelBtn').css('margin-top','50px');
            //
        });

    });


    function change_trend(type, direction) {
        //alert('TEST move');
        var n_dash = $('#delegationsDashboardTitle').text();
        var start_accss = $('#start_accss').val();
        var start_min = $('#start_min').val();
        //


        //
        if (type === 'access') {
            //
            var menu_accesses = $('#menu_1').val();
            var menu_1 = $('#menu_1').val();
            //calcolo di start end end;
            //
            var start_accss = $('#start_accss').val();
            var date_1 = new Date(start_accss);
            //
            //$('#start_accss').val();
            var end_accss = $('#end_accss').val();
            var date_2 = new Date(end_accss);

            //Calconi dati
            if (menu_accesses == ' 24 HOUR ') {
                if (direction == 'previous') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setDate(date_1.getDate() - 1);
                    end_accss = date_2.setDate(date_2.getDate() - 1);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setDate(date_1.getDate() + 1);
                    end_accss = date_2.setDate(date_2.getDate() + 1);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);
                } else {

                }
            } else if (menu_accesses == ' 1 WEEK ') {

                if (direction == 'previous') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setDate(date_1.getDate() - 7);
                    end_accss = date_2.setDate(date_2.getDate() - 7);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setDate(date_1.getDate() + 7);
                    end_accss = date_2.setDate(date_2.getDate() + 7);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);
                } else {

                }
            } else if (menu_accesses == ' 1 MONTH ') {
                //////
                if (direction == 'previous') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setMonth(date_1.getMonth() - 1);
                     end_accss = date_2.setMonth(date_2.getMonth() - 1);
                    //start_accss = date_1.setDate(date_1.getDate() - 30);
                   // end_accss = date_2.setDate(date_2.getDate() - 30);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setMonth(date_1.getMonth() + 1);
                    end_accss = date_2.setMonth(date_2.getMonth() + 1);
                    //start_accss = date_1.setDate(date_1.getDate() +30);
                  //end_accss = date_2.setDate(date_2.getDate() + 30);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);
                } else {

                }
                ///////
            } else if (menu_accesses == ' 6 MONTH ') {
                if (direction == 'previous') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setMonth(date_1.getMonth() - 6);
                    end_accss = date_2.setMonth(date_2.getMonth() - 6);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setMonth(date_1.getMonth() + 6);
                    end_accss = date_2.setMonth(date_2.getMonth() + 6);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);
                } else {

                }
            } else if (menu_accesses == ' 1 YEAR ') {
                if (direction == 'previous') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setFullYear(date_1.getFullYear() - 1);
                    end_accss = date_2.setFullYear(date_2.getFullYear() - 1);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    console.log(date_1);
                    console.log(date_2);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_1').val();
                    var menu_1 = $('#menu_1').val();
                    //calcolo di start end end;
                    //
                    var start_accss = $('#start_accss').val();
                    var date_1 = new Date(start_accss);
                    //
                    //$('#start_accss').val();
                    var end_accss = $('#end_accss').val();
                    var date_2 = new Date(end_accss);
                    start_accss = date_1.setFullYear(date_1.getFullYear() + 1);
                    end_accss = date_2.setFullYear(date_2.getFullYear() + 1);

                    var start_accss1 = new Date(start_accss);
                    var end_accss1 = new Date(end_accss);
                    //
                    start_accss = start_accss1.getFullYear() + "-" + (start_accss1.getMonth() + 1) + "-" + start_accss1.getDate();
                    end_accss = end_accss1.getFullYear() + "-" + (end_accss1.getMonth() + 1) + "-" + end_accss1.getDate();


                    //
                    $('#start_accss').val(start_accss);
                    $('#end_accss').val(end_accss);
                } else {

                }
            } else {

            }
            //
            //'#container_accesses').empty();

            $.ajax({
                url: "dashboard_trend.php",
                dataType: 'json',
                data: {
                    dashboard: n_dash,
                    interval: menu_accesses,
                    direction: direction,
                    current_date: start_accss,
                    end_date: end_accss
                },
                type: "GET",
                async: true,
                success: function (data) {
                    var dates = JSON.stringify(data.dates);
                    var nAccessPerDay = JSON.stringify(data.AccessPerDay);
                    var array_date = JSON.parse(dates);
                    var array_nAccessPerDay = JSON.parse(nAccessPerDay);
                    var nMinutesPerDay = JSON.stringify(data.MinutesPerDay);
                    var array_nMinutesPerDay = JSON.parse(nMinutesPerDay);
                    $('#container_accesses').highcharts({
                        title: {
                            text: 'Accessess over Time'
                        },
                        xAxis: {
                            text: 'datetime',
                            categories: array_date
                        },
                        yAxis: {
                            title: {
                                text: 'num. accesses'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        exporting: {
                            enabled: false
                        },
                        series: [{
                                type: 'area',
                                name: 'Accesses',
                                data: array_nAccessPerDay
                            }]
                    });
                    //

                }
            });
        } else if (type === 'minutes') {
            $('#container_minutes').empty();
            var chartRef = null;
            //
            //
            // var menu_1 = $('#menu_1').val();
            //
            var menu_accesses = $('#menu_2').val();
            var end_min = $('#end_min').val();
            var start_min = $('#start_min').val();
            //
            var date_1 = new Date(start_min);
            var date_2 = new Date(end_min);

            //
            if (menu_accesses == ' 24 HOUR ') {
                if (direction == 'previous') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setDate(date_1.getDate() - 1);
                    end_min = date_2.setDate(date_2.getDate() - 1);
                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth() + 1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth() + 1) + "-" + end_min1.getDate();


                    //
                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setDate(date_1.getDate() + 1);
                    end_min = date_2.setDate(date_2.getDate() + 1);

                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth() + 1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth() + 1) + "-" + end_min1.getDate();


                    //
                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);
                } else {

                }
            } else if (menu_accesses == ' 1 WEEK ') {

                if (direction == 'previous') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setDate(date_1.getDate() - 7);
                    end_min = date_2.setDate(date_2.getDate() - 7);
                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth() + 1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth() + 1) + "-" + end_min1.getDate();

                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setDate(date_1.getDate() + 7);
                    end_min = date_2.setDate(date_2.getDate() + 7);
                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth() + 1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth() + 1) + "-" + end_min1.getDate();
                    //
                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);
                } else {

                }
            } else if (menu_accesses == ' 1 MONTH ') {
                //////
                if (direction == 'previous') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setMonth(date_1.getMonth() - 1);
                    end_min = date_2.setMonth(date_2.getMonth() - 1);

                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth() + 1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth() + 1) + "-" + end_min1.getDate();


                    //
                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setMonth(date_1.getMonth() + 1);
                    end_min = date_2.setMonth(date_2.getMonth() + 1);

                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth() + 1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth() + 1) + "-" + end_min1.getDate();


                    //
                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);
                } else {

                }
                ///////
            } else if (menu_accesses == ' 6 MONTH ') {
                if (direction == 'previous') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setMonth(date_1.getMonth() - 6);
                    end_min = date_2.setMonth(date_2.getMonth() - 6);

                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth() + 1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth() + 1) + "-" + end_min1.getDate();


                    //
                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setMonth(date_1.getMonth() + 6);
                    end_min = date_2.setMonth(date_2.getMonth() + 6);

                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth() + 1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth() + 1) + "-" + end_min1.getDate();


                    //
                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);
                } else {

                }
            } else if (menu_accesses == ' 1 YEAR ') {
                if (direction == 'previous') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setFullYear(date_1.getFullYear() - 1);
                    end_min = date_2.setFullYear(date_2.getFullYear() - 1);

                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    //console.log(start_min1);
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth()+1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth()+1) + "-" + end_min1.getDate();


                    //
                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);

                } else if (direction == 'next') {
                    var menu_accesses = $('#menu_2').val();
                    var end_min = $('#end_min').val();
                    var start_min = $('#start_min').val();
                    //
                    var date_1 = new Date(start_min);
                    var date_2 = new Date(end_min);
                    start_min = date_1.setFullYear(date_1.getFullYear() + 1);
                    end_min = date_2.setFullYear(date_2.getFullYear() + 1);

                    var start_min1 = new Date(start_min);
                    var end_min1 = new Date(end_min);
                    //
                    start_min = start_min1.getFullYear() + "-" + (start_min1.getMonth() + 1) + "-" + start_min1.getDate();
                    end_min = end_min1.getFullYear() + "-" + (end_min1.getMonth() + 1) + "-" + end_min1.getDate();


                    //
                    $('#start_min').val(start_min);
                    $('#end_min').val(end_min);
                } else {

                }
            } else {

            }
            //
            //
            $.ajax({
                url: "dashboard_trend.php",
                dataType: 'json',
                data: {
                    dashboard: n_dash,
                    interval: menu_accesses,
                    direction: direction,
                    current_date: start_min,
                    end_date: end_min
                },
                type: "GET",
                async: true,
                success: function (data) {
                    var dates = JSON.stringify(data.dates);
                    var nAccessPerDay = JSON.stringify(data.AccessPerDay);
                    var array_date = JSON.parse(dates);
                    var array_nAccessPerDay = JSON.parse(nAccessPerDay);
                    var nMinutesPerDay = JSON.stringify(data.MinutesPerDay);
                    var array_nMinutesPerDay = JSON.parse(nMinutesPerDay);

                    //
                    $('#container_minutes').highcharts({
                        title: {
                            text: 'Minutes over Time'
                        },
                        xAxis: {
                            text: 'datetime',
                            categories: array_date
                        },
                        yAxis: {
                            title: {
                                text: 'num. Minutes'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        exporting: {
                            enabled: false
                        },
                        series: [{
                                type: 'area',
                                name: 'Minutes',
                                data: array_nMinutesPerDay
                            }]
                    });
                }

            });
        } else {
            //Nothing
        }
    }

    function timetrendaccesses() {
        var n_dash = $('#delegationsDashboardTitle').text();
        var chartRef = null;
        var menu_accesses = $('#menu_1').val();
        console.log('Accesses');
        $('#container_accesses').empty();
        //
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;
        $('#start_accss').val(today);
        //CAMBIO END
        var expr = $('#menu_1').val();
        switch (expr) {
            case ' 24 HOUR ':
                //console.log('Oranges are $0.59 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate() - 1).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                //
                break;
            case ' 1 WEEK ':
                //
                var today2 = new Date();
                var dd2 = String(today2.getDate() - 7).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                //
                break;
            case ' 1 MONTH ':
                //console.log('Mangoes and papayas are $2.79 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth()).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                //
                // expected output: "Mangoes and papayas are $2.79 a pound."
                break;
            case ' 6 MONTH ':
                //console.log('Mangoes and papayas are $2.79 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() - 5).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                //
                // expected output: "Mangoes and papayas are $2.79 a pound."
                break;
            case ' 1 YEAR ':
                //console.log('Mangoes and papayas are $2.79 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear() - 1;
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                //
                // expected output: "Mangoes and papayas are $2.79 a pound."
                break;
            default:
                //
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                //
                //console.log(`Sorry, we are out of ${expr}.`);
        }
        //

        $.ajax({
            url: "dashboard_trend.php",
            dataType: 'json',
            data: {
                dashboard: n_dash,
                interval: menu_accesses,
                end_date: today2
            },
            type: "GET",
            async: true,
            success: function (data) {
                var dates = JSON.stringify(data.dates);
                var nAccessPerDay = JSON.stringify(data.AccessPerDay);
                var array_date = JSON.parse(dates);
                var array_nAccessPerDay = JSON.parse(nAccessPerDay);
                var nMinutesPerDay = JSON.stringify(data.MinutesPerDay);
                var array_nMinutesPerDay = JSON.parse(nMinutesPerDay);
                $('#container_accesses').highcharts({
                    title: {
                        text: 'Accessess over Time'
                    },
                    xAxis: {
                        text: 'datetime',
                        categories: array_date
                    },
                    yAxis: {
                        title: {
                            text: 'num. accesses'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    series: [{
                            type: 'area',
                            name: 'Accesses',
                            data: array_nAccessPerDay
                        }]
                });
                //

            }
        });
    }
    function timetrendminutes() {
        var n_dash = $('#delegationsDashboardTitle').text();
        var chartRef = null;
        var menu_accesses = $('#menu_2').val();
        console.log('Minutes');
        $('#container_minutes').empty();
        //
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;
        $('#start_min').val(today);
        //CAMBIO END
        var expr = $('#menu_2').val();
        switch (expr) {
            case ' 24 HOUR ':
                //console.log('Oranges are $0.59 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate() - 1).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_min').val(today2);
                //
                break;
            case ' 1 WEEK ':
                //
                var today2 = new Date();
                var dd2 = String(today2.getDate() - 7).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_min').val(today2);
                //
                break;
            case ' 1 MONTH ':
                //console.log('Mangoes and papayas are $2.79 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth()).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_min').val(today2);
                //
                // expected output: "Mangoes and papayas are $2.79 a pound."
                break;
            case ' 6 MONTH ':
                //console.log('Mangoes and papayas are $2.79 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() - 5).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_min').val(today2);
                //
                // expected output: "Mangoes and papayas are $2.79 a pound."
                break;
            case ' 1 YEAR ':
                //console.log('Mangoes and papayas are $2.79 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear() - 1;
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_min').val(today2);
                //
                // expected output: "Mangoes and papayas are $2.79 a pound."
                break;
            default:
                //
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_min').val(today2);
                //
                //console.log(`Sorry, we are out of ${expr}.`);
        }
        //
        $.ajax({
            url: "dashboard_trend.php",
            dataType: 'json',
            data: {
                dashboard: n_dash,
                interval: menu_accesses,
                end_date: today2
            },
            type: "GET",
            async: true,
            success: function (data) {
                var dates = JSON.stringify(data.dates);
                var nAccessPerDay = JSON.stringify(data.AccessPerDay);
                var array_date = JSON.parse(dates);
                var array_nAccessPerDay = JSON.parse(nAccessPerDay);
                var nMinutesPerDay = JSON.stringify(data.MinutesPerDay);
                var array_nMinutesPerDay = JSON.parse(nMinutesPerDay);

                //
                $('#container_minutes').highcharts({
                    title: {
                        text: 'Minutes over Time'
                    },
                    xAxis: {
                        text: 'datetime',
                        categories: array_date
                    },
                    yAxis: {
                        title: {
                            text: 'num. Minutes'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    series: [{
                            type: 'area',
                            name: 'Minutes',
                            data: array_nMinutesPerDay
                        }]
                });
            }

        });
        //
    }

    function timetrend() {
        var n_dash = $('#delegationsDashboardTitle').text();
        var chartRef = null;
        var menu_accesses = $('#menu_accesses').val();
        //  menu_accesses = 'MONTH';
        $('#container_minutes').empty();
        $('#container_accesses').empty();
        //
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;
        $('#start_accss').val(today);
        $('#start_min').val(today);
        //CAMBIO END
        var expr = $('#menu_1').val();
        switch (expr) {
            case ' 24 HOUR ':
                //console.log('Oranges are $0.59 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear()-1;
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                $('#end_min').val(today2);
                //
                break;
            case ' 1 WEEK ':
                //
                var today2 = new Date();
                var dd2 = String(today2.getDate() - 7).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                $('#end_min').val(today2);
                //
                break;
            case ' 1 MONTH ':
                //console.log('Mangoes and papayas are $2.79 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth()).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                $('#end_min').val(today2);
                //
                // expected output: "Mangoes and papayas are $2.79 a pound."
                break;
            case ' 6 MONTH ':
                //console.log('Mangoes and papayas are $2.79 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() - 5).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                $('#end_min').val(today2);
                //
                // expected output: "Mangoes and papayas are $2.79 a pound."
                break;
            case ' 1 YEAR ':
                //console.log('Mangoes and papayas are $2.79 a pound.');
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() - 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear() - 1;
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                $('#end_min').val(today2);
                //
                // expected output: "Mangoes and papayas are $2.79 a pound."
                break;
            default:
                //
                var today2 = new Date();
                var dd2 = String(today2.getDate()).padStart(2, '0');
                var mm2 = String(today2.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy2 = today2.getFullYear();
                today2 = yyyy2 + '-' + mm2 + '-' + dd2;
                $('#end_accss').val(today2);
                $('#end_min').val(today2);
                //
                //console.log(`Sorry, we are out of ${expr}.`);
        }
        //
        $.ajax({
            url: "dashboard_trend.php",
            dataType: 'json',
            data: {
                dashboard: n_dash,
                interval: menu_accesses,
                end_date: ''
            },
            type: "GET",
            async: true,
            success: function (data) {
                var dates = JSON.stringify(data.dates);
                var nAccessPerDay = JSON.stringify(data.AccessPerDay);
                var array_date = JSON.parse(dates);
                var array_nAccessPerDay = JSON.parse(nAccessPerDay);
                var nMinutesPerDay = JSON.stringify(data.MinutesPerDay);
                var array_nMinutesPerDay = JSON.parse(nMinutesPerDay);
                $('#container_accesses').highcharts({
                    title: {
                        text: 'Accessess over Time'
                    },
                    xAxis: {
                        text: 'datetime',
                        categories: array_date
                    },
                    yAxis: {
                        title: {
                            text: 'num. accesses'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    series: [{
                            type: 'area',
                            name: 'Accesses',
                            data: array_nAccessPerDay
                        }]
                });
                //
                $('#container_minutes').highcharts({
                    title: {
                        text: 'Minutes over Time'
                    },
                    xAxis: {
                        text: 'datetime',
                        categories: array_date
                    },
                    yAxis: {
                        title: {
                            text: 'num. Minutes'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    series: [{
                            type: 'area',
                            name: 'Minutes',
                            data: array_nMinutesPerDay
                        }]
                });
            },
            error: function (data)
            {
                console.log("Error: " + data);
            }
        });
    }

</script>  

<script>
    $(function() {
        const steps = JSON.parse('<?= serializeToJsonString($tourRepo->getTourSteps("preRegisterTour")) ?>');
        const session = JSON.parse('<?= serializeToJsonString($_SESSION) ?>');
        SnapTour.init(steps, {
            isPublic: session.isPublic,
          //  resetTimeout: 1000 * 60 * 60 * 12 // 12 hour as ms. if left blank the default is 24h
            resetTimeout: 1000 * 60 * 5
        });
    });
</script>
