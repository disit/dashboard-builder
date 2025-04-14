<?php
/* Snap4City: IoT-Directory
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

session_start();
checkSession("RootAdmin");

///// SHOW FRAME PARAMETER /////
if (isset($_REQUEST['showFrame'])) {
    if ($_REQUEST['showFrame'] == 'false') {
        $hide_menu = "hide";
    } else {
        $hide_menu = "";
    }
} else
    $hide_menu = "";
//// SHOW FRAME PARAMETER  ////

if (!isset($_GET['pageTitle'])) {
    $default_title = "API manager";
} else {
    $default_title = "";
}

if (isset($_REQUEST['redirect'])) {
    $access_denied = "denied";
} else {
    $access_denied = "";
}

$link = mysqli_connect($dbhost, $dbuser, $dbpassword);
mysqli_select_db($link, $dbapimanagername);

$accessToken = "";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Snap4City IoT Directory</title>

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
    <link href="../bootstrapToggleButton/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="../bootstrapToggleButton/js/bootstrap-toggle.min.js"></script>

    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="../js/DataTables/datatables.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/DataTables/datatables.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/responsive.bootstrap.min.js"></script>


    <link rel="stylesheet" type="text/css" href="../css/DataTables/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/jquery.dataTables.min.css">



    <!-- Bootstrap slider -->
    <script src="../bootstrapSlider/bootstrap-slider.js"></script>
    <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet" />

    <!-- select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


    <!-- Filestyle -->
    <script src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

    <!-- Custom CSS -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <style>
        .btn-round {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        #mainMenuCnt {
            background-color: rgba(51, 64, 69, 1);
            color: white;
            height: 100vh;
            <?php if ($hide_menu == "hide") echo "display:none"; //MM201218 
            ?>
        }
    </style>

    <script>
        var loggedRole = "<?php echo $_SESSION['loggedRole']; ?>";
        var loggedUser = "<?php echo $_SESSION['loggedUsername']; ?>";
        var admin = "<?php echo $_SESSION['loggedRole']; ?>";
        var organization = "<?php echo $_SESSION['organization']; ?>";
        var kbUrl = "<?php echo $_SESSION['kbUrl']; ?>";
        var gpsCentreLatLng = "<?php echo $_SESSION['gpsCentreLatLng']; ?>";
        var zoomLevel = "<?php echo $_SESSION['zoomLevel']; ?>";
        var titolo_default = "<?php echo $default_title; ?>";
        var access_denied = "<?php echo $access_denied; ?>";
        var nascondi = "<?php echo $hide_menu; ?>";
        var sessionEndTime = "<?php echo $_SESSION['sessionEndTime']; ?>";
        var sessionToken = "<?php if (isset($_SESSION['refreshToken'])) echo $_SESSION['refreshToken'];
                            else echo ""; ?>";
        var mypage = location.pathname.split("/").slice(-1)[0];
        var functionality = [];
        var currentDictionaryStaticAttribAdd = [];
        var currentDictionaryStaticAttribEdit = [];

    </script>

    <!-- Custom scripts 
    <script src="../legacy/management/js/devices.js"></script>
    <script src="../legacy/management/js/devicesManagement.js"></script>
    <script src="../legacy/management/js/fieldsManagement.js"></script>
    <script src="../legacy/management/js/devicesEditManagement.js"></script>
    <script src="../js/dashboard_mng.js"></script>
    <script src="../legacy/management/js/common.js"></script>
	-->
    <!-- leaflet scripts -->

</head>

    <body class="guiPageBody IOTdevices">
        <div class="container-fluid">
                <?php include "sessionExpiringPopup.php" ?> 
            <div class="row mainRow"> 
                <div 
                <?php //MM201218
                if (($hide_menu == "hide")) {
                    ?>
                        class="col-xs-12 col-md-12" 
                    <?php } else { ?>
                        class="col-xs-12 col-md-10" 
<?php } //MM201218 FINE ?>
                    id="mainCnt">
                    <div class="row hidden-md hidden-lg">
                        <div id="mobHeaderClaimCnt" class="col-xs-12 hidden-md hidden-lg centerWithFlex">
                            Snap4City
                        </div>
                    </div>
<?php //MM201218
if (($hide_menu != "hide")) {
    ?>
                        <div class="row" id="title_row">
                            <div class="col-xs-10 col-md-12 centerWithFlex" id="headerTitleCnt">Experimental API Manager</div>
                            <div class="col-xs-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"></div> 
                        </div>
<?php } //MM201218 FINE  ?>

                    <div class="row">
                        <div class="col-xs-12" id="mainContentCntIot">
                            <div id="synthesis" class="row hidden-xs hidden-sm mainContentRow">
                                <div id="dashboardTotNumberCnt" class="col-md-3 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                        $query = "SELECT count(*) AS qt FROM operative_apitable where apideletiondate = 0";
                                        $result = mysqli_query($link, $query);
                                        if ($result) {
                                            $row = $result->fetch_assoc();
                                            echo $row['qt'] . ' total API';
                                        } else {
                                            echo '-' . ' total API';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div id="dashboardTotActiveCnt" class="col-md-3 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                        //MM
                                        $query = "SELECT count(*) AS qt FROM operative_apitable where apistatus = 'active' and apideletiondate = 0";
                                        $result = mysqli_query($link, $query);
                                        if ($result) {
                                            $row = $result->fetch_assoc();
                                            echo $row['qt'] . ' active API';
                                        } else {
                                            echo '-' . ' active API';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div id="dashboardTotPermCnt" class="col-md-3 mainContentCellCnt">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                        //MM
                                        $query = "select count(*) as qt from ratelimit";
                                        $result = mysqli_query($link, $query);
                                        if ($result) {
                                            $row = $result->fetch_assoc();
                                            echo $row['qt'] . ' rules';
                                        } else {
                                            echo '-' . ' rules';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div id="dashboardTotPrivateCnt" class="col-md-3 mainContentCellCnt" style="background: blue;">
                                    <div class="col-md-12 centerWithFlex pageSingleDataCnt">
                                        <?php
                                        $query = "select (select count(*) from operative_apitable left join ratelimit on ratelimit.resource=operative_apitable.idapi where apideletiondate = 0)-(select count(*) from operative_apitable left join ratelimit on ratelimit.resource=operative_apitable.idapi where kind_of_limit is not null and apideletiondate = 0) as result";
                                        $result = mysqli_query($link, $query);
                                        if ($result) {
                                            $row = $result->fetch_assoc();
                                            echo $row['result'] . ' API without rules';
                                        } else {
                                            echo '-' . ' API without rules';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>



                            <div id="displayAllAPIRow" class="row mainContentRow">
                                <div class="col-xs-12 mainContentRowDesc "></div>
                                <div class="col-xs-12 mainContentCellCnt ">
                                    <div >
                                        <table id="APIsTable" class="addWidgetWizardTable table table-striped dt-responsive nowrap dataTable no-footer dtr-inline collapsed" >
                                            <thead class="dataTableHeadColTitle">
                                                <tr>
                                                    <th data-cellTitle="id">Element ID</th>
                                                    <th data-cellTitle="name">Element Name</th>
                                                    <th data-cellTitle="elementType">Element Type</th>
                                                    <th data-cellTitle="elementInfo">Element Info</th>
                                                    <th data-cellTitle="internal">Internal Url</th>
                                                    <th data-cellTitle="external">External Url</th>
                                                    <th data-cellTitle="status">Status</th>
                                                    <th data-cellTitle="edit">Edit</th>
                                                    <th data-cellTitle="rules">Rules</th>
                                                    <th data-cellTitle="delete">Delete</th>
                                                    <th data-cellTitle="additionalinfo">Additional Info</th>
                                                </tr>
                                            </thead>
					    <tbody>
						<?php
							$query = 'select idapi, apiname, apikind, apiinfo, apiinternalurl, apiexternalurl, apistatus, "Edit", "Rules", "Delete", apiadditionalinfo  from operative_apitable where apideletiondate = 0;';
							$result = mysqli_query($link, $query);
							if ($result) {
								$fields = mysqli_fetch_fields($result);
    								$columns = [];
							    	foreach ($fields as $field) {
        								$columns[] = $field->name; // Store column names for reference
    								}
								while ($row = $result->fetch_assoc()) {
        								echo '<tr>';
        								foreach ($columns as $col) {
            									$value = htmlspecialchars($row[$col]);
								            	if (stripos($col, 'url') !== false) {
                									$value = '<a href="' . $value . '" target="_blank">' . $value . '</a>';
            									} elseif (stripos($col, 'edit') !== false) {
                									$value = "<button class='editDashBtn editbuttonmodal' data-toggle='modal' data-target='#modalfourth' data-id=".$row['idapi']." data-name='".$row['apiname']."' data-kind='".$row['apikind']."' data-info='".$row['apiinfo']."' data-apiinternalurl='".$row['apiinternalurl']."' data-apiexternalurl='".$row['apiexternalurl']."' data-status='".$row['apistatus']."'>edit</button>";
            									} elseif (stripos($col, 'delete') !== false) {
                									$value = "<button class='delDashBtn deleteapibuttonmodal' data-toggle='modal' data-target='#modalDeleteApi' data-id=".$row['idapi']." data-name=".$row['apiname'].">delete</button>";
            									} elseif (stripos($col, 'view') !== false) {
                									$value = "<button class='viewDashBtn'>view</button>";
            									} elseif (stripos($col, 'Rules') !== false) {
                									$value = "<button class='viewDashBtn searchRule' data-toggle='modal' data-target='#modalthird' ruleitem='".$row['idapi']."'>rules</button>";
            									} else {
									                $value = nl2br($value); // Converts newlines to <br>
            									}
            									echo '<td>' . $value . '</td>';
        								}
        								echo "</tr>\r\n";
    								}
							}
							else {echo "Error reading data";}
						?>
					    </tbody>
                                        </table>
										<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#modalfirst">Add new API</button>
										</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adding a new api -->
		<div class="modal fade" id="modalfirst" tabindex="-1" role="dialog" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modalHeader centerWithFlex">
                        Add a new API
                    </div>

                    <div id="addAPIModalBody">

                        <div class="tab-content">

                            <!-- Info tab -->
                            <div id="addInfoTabAPI" class="tab-pane fade in active">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <div class="modalFieldCnt">
                                            <input type="text" class="modalInputTxt" name="inputNameAPI" id="inputNameAPI"> 
                                        </div>
                                        <div class="modalFieldLabelCnt">API Identifier</div>
                                        <div id="inputNameAPIMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <div class="modalFieldCnt">
                                          <select id="selectAPIkind" name="selectAPIkind" class="modalInputTxt" onchange="generateAPIForm()">
												<option disabled selected>Select an option</option>
                                                <option value="ClearMLStable">ClearML Stable</option>
                                                <option value="ClearMLSporadic">ClearML Sporadic</option>
                                                <option value="SUMOAPI">SUMO API</option>
                                                <option value="GraphHopper">GraphHopper</option>
                                                <option value="Generic">Generic</option>
                                                <option value="Other">Other</option>
                                                 
                                          </select>

                                        </div>
                                        <div class="modalFieldLabelCnt">API Kind</div>
                                        <div id="inputModelAPIMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <div class="modalFieldCnt">
                                            <input type="text" class="modalInputTxt" name="inputInternalAPIUrl" id="inputInternalAPIUrl"> 
                                        </div>
                                        <div class="modalFieldLabelCnt">Internal API URL</div>
                                        <div id="inputInternalAPIUrlMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>

                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <div class="modalFieldCnt">
                                            <input type="text" class="modalInputTxt" name="inputExternalAPIUrl" id="inputExternalAPIUrl"> 
                                        </div>
                                        <div class="modalFieldLabelCnt">External API URL</div>
                                        <div id="inputExternalAPIUrlMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-md-12 modalCell">
                                        <div class="modalFieldCnt">
                                            <input type="text" class="modalInputTxt" name="inputAPIInfo" id="inputAPIInfo"> 
                                        </div>
                                        <div class="modalFieldLabelCnt">API Info</div>
                                        <div id="inputAPIInfoMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                </div>
								<div class="row" id="additionalInfoOptionalDataAPI" id="additionalInfoOptionalDataAPI">
                                    
                                </div>
                                
                            </div>

                        </div>

                    </div> 	


                    <div id="addAPIModalFooter" class="modal-footer">
                        <div class="row">

                            <div align="left">
                                <div id="addAPICheckExternalLoadingIcon" style="display:none;">
                                    <i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i> <i>checking...</i> </div>
                            </div>
                            <div  align="right">
                                <button type="text" id="addNewAPICancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                                <button type="text" id="addNewAPIConfirmBtn" name="addNewAPIConfirmBtn" class="btn confirmBtn internalLink" onClick="createAPI()">Confirm</button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<!-- Adding a new rulev2 -->
		<div class="modal fade" id="modalsecondsecond" tabindex="-1" role="dialog" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modalHeader centerWithFlex">
                        Add a new rule
                    </div>

                    <div id="addRuleModalBodySecond">

                        <div class="tab-content">

                            <!-- Info tab -->
                            <div id="addInfoTabRuleSecond" class="tab-pane fade in active">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <input type="text" class="modalInputTxt" name="addRuleUserFieldSecond" id="addRuleUserFieldSecond">
                                        <div class="modalFieldLabelCnt">Select User</div>
                                        <div id="addRuleUserFieldSecondMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <input type="text" class="modalInputTxt" name="addRuleResourceFieldSecond" id="addRuleResourceFieldSecond" disabled>
                                        <div class="modalFieldLabelCnt">Selected Resource</div>
                                        <div id="addRuleResourceFieldSecondMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                </div>
								
								<div class="row">
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <input type="datetime-local" id="addRuleStartingOfValidity" class="form-control">
                                        <div class="modalFieldLabelCnt">Rule is valid from</div>
                                        <div id="addRuleStartingOfValidityMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <input type="datetime-local" id="addRuleEndingOfValidity" class="form-control">
                                        <div class="modalFieldLabelCnt">Rule is valid until</div>
                                        <div id="addRuleEndingOfValidityMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>

                                </div>
								
                                <div class="row">
                                    <div class="col-xs-12 col-md-12 modalCell">
                                        <select id="selectRuleKindSecond" name="selectRuleKind" class="modalInputTxt" onClick="generateRuleFormSecond()">
											<option disabled selected value="invalid">Select an option</option>
											<option value="ContemporaryAccess">Contemporary access</option>
											<option value="AccessesOverTime">Accesses over time</option>
											<option value="TotalAccesses">Total Accesses</option>
										</select>
                                    </div>
                                </div>
								
								<div class="row" id="createRuleDivSecond">
                                </div>
                                
                            </div>

                        </div>

                    </div> 	


                    <div id="addRuleModalFooterSecond" class="modal-footer">
                        <div class="row">

                            <div align="left">
                                <div id="addRuleCheckExternalLoadingIcon" style="display:none;">
                                    <i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i> <i>checking...</i> </div>
                            </div>
                            <div  align="right">
                                <button type="text" id="addNewRuleCancelBtnSecond" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                                <button type="text" id="addNewRuleConfirmBtnSecond" name="addNewRuleConfirmBtn" class="btn confirmBtn internalLink" onclick="createRuleSecond()">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<!-- View Accesses -->
		<div class="modal fade" id="modalviewaccess" tabindex="-1" role="dialog" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modalHeader centerWithFlex">
                        View accesses
                    </div>

                    <div id="addRuleModalBody">
						<div id="showAccessesdiv"> 
						</div>
                        
                    </div> 	


                    <div id="viewAccessModalFooter" class="modal-footer">
                        <div class="row">
                            <div align="right">
                                <button type="text" id="viewAccessCloseBtn" class="btn cancelBtn" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<!-- View Rules-->
		<div class="modal fade" id="modalthird" tabindex="-1" role="dialog" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modalHeader centerWithFlex">
                        View rules
                    </div>

                    <div id="addRuleModalBody">
						<div id="showRulesdiv"> 
						</div>
                        
                    </div> 	


                    <div id="addRuleModalFooter" class="modal-footer">
                        <div class="row">
                            <div align="right">
								<button id="openModalCreateRuleButton" data-dismiss="modal" data-api="" type="button" idapi="" class="btn btn-info btn-lg" data-toggle="modal" data-target="#modalsecondsecond" onClick="setupNewRuleForm(this)">Add new Rule</button>
                                <button type="text" id="addNewRuleCancelBtn" class="btn cancelBtn" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<!-- Confirm delete api-->
		<div class="modal fade" id="modalDeleteApi" tabindex="-1" role="dialog" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modalHeader centerWithFlex">
                        Delete Api
                    </div>

                    <div id="deleteApiBody">
						<div id="deleteApiText" style="text-align: center"> 
						</div>
                        
                    </div> 	


                    <div id="deleteApiModalFooter" class="modal-footer">
                        <div class="row">
                            <div align="right">
								<button type="text" id="deleteApiConfirmBtn" name="addNewRuleConfirmBtn" class="btn confirmBtn internalLink" onclick="deleteapi()">Delete API</button>
                                <button type="text" id="deleteApiCancelBtn" class="btn cancelBtn" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<!-- edit api -->
		<div class="modal fade" id="modalfourth" tabindex="-1" role="dialog" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modalHeader centerWithFlex">
                        Edit API
                    </div>

                    <div id="addAPIModalBody">

                        <div class="tab-content">

                            <!-- Info tab -->
                            <div id="addInfoTabAPI" class="tab-pane fade in active">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <div class="modalFieldCnt">
                                            <input type="text" class="modalInputTxt" name="editinputNameAPI" id="editinputNameAPI" required disabled> 
                                        </div>
                                        <div class="modalFieldLabelCnt">API Identifier</div>
                                        <div id="editinputNameAPIMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
									
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <div class="modalFieldCnt">
                                            <select id="editselectAPIkind" name="editselectAPIkind" class="modalInputTxt" disabled>
												<option disabled selected >Select an option</option>
                                                <option value="ClearMLStable">ClearML Stable</option>
                                                <option value="ClearMLSporadic">ClearML Sporadic</option>
                                                <option value="SUMOAPI">SUMO API</option>
                                                <option value="GraphHopper">GraphHopper</option>
                                                <option value="Generic">Generic</option>
                                                <option value="Other">Other</option>
                                                 
                                            </select>

                                        </div>
                                        <div class="modalFieldLabelCnt">API Kind</div>
                                        <div id="editinputModelAPIMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>

                                </div>
								<div class="row">
									<div class="col-xs-12">
										The above fields cannot be edited for internal consistency reasons.
									</div>
								</div>
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <div class="modalFieldCnt">
                                            <input type="text" class="modalInputTxt" name="editInternalAPIUrl" id="editInternalAPIUrl"> 
                                        </div>
                                        <div class="modalFieldLabelCnt">Internal API URL</div>
                                        <div id="editInternalAPIUrlMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>

                                    <div class="col-xs-12 col-md-6 modalCell">
                                        <div class="modalFieldCnt">
                                            <input type="text" class="modalInputTxt" name="editExternalAPIUrl" id="editExternalAPIUrl"> 
                                        </div>
                                        <div class="modalFieldLabelCnt">External API URL</div>
                                        <div id="editExternalAPIUrlMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="row">
									
                                    <div class="col-xs-12 col-md-9 modalCell">
                                        <div class="modalFieldCnt">
                                            <input type="text" class="modalInputTxt" name="editAPIInfo" id="editAPIInfo"> 
                                        </div>
                                        <div class="modalFieldLabelCnt">API Information</div>
                                        <div id="editAPIInfoMsg" class="modalFieldMsgCnt">&nbsp;</div>
                                    </div>
									<div class="col-xs-12 col-md-2 modalCell">
										<input class="form-check-input" type="checkbox" value="" id="isAPIActive">
										<label class="form-check-label" for="isAPIActive">
											Is active?
										</label>
									</div>
                                </div>
                                <input type="hidden" class="modalInputTxt" name="editAPIID" id="editAPIID">
                            </div>

                        </div>

                    </div> 	


                    <div id="addAPIModalFooter" class="modal-footer">
                        <div class="row">

                            <div align="left">
                                <div id="addAPICheckExternalLoadingIcon" style="display:none;">
                                    <i class="fa fa-circle-o-notch fa-spin" style="font-size:36px;"></i> <i>checking...</i> </div>
                            </div>
                            <div  align="right">
                                <button type="text" id="editAPICancelBtn" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                                <button type="text" id="editAPIConfirmBtn" name="editAPIConfirmBtn" class="btn confirmBtn internalLink" onclick="editAPI()">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		

	<script>
		//populates edit modal
		const btns = document.querySelectorAll('.editbuttonmodal');
		btns.forEach(btn => {

			btn.addEventListener('click', event => {
				document.getElementById("editinputNameAPI").value=event.target.getAttribute('data-name');
				document.getElementById("editselectAPIkind").value=event.target.getAttribute('data-kind');
				document.getElementById("editAPIInfo").value=event.target.getAttribute('data-info');
				document.getElementById("editInternalAPIUrl").value=event.target.getAttribute('data-apiinternalurl');
				document.getElementById("editExternalAPIUrl").value=event.target.getAttribute('data-apiexternalurl');
				document.getElementById("editAPIID").value=event.target.getAttribute('data-id');
				if (event.target.getAttribute('data-status')=='inactive') {
					document.getElementById("isAPIActive").checked=false;
				}
				else if (event.target.getAttribute('data-status')=='active' || event.target.getAttribute('data-status')=='ready') {
					document.getElementById("isAPIActive").checked=true;
				}
				else {
					alert("Api doesn't have an expected status");
					document.getElementById("isAPIActive").checked=false;
				}
			});

		});
		
		const deletebtns = document.querySelectorAll('.deleteapibuttonmodal');
		deletebtns.forEach(btn => {
			btn.addEventListener('click', event => {
				document.getElementById("deleteApiText").innerHTML="<h3>Are you sure you want to delete the API " + event.target.getAttribute('data-name') + "? This will also delete any related rule.</h3>";
				document.getElementById("viewAccessCloseBtn").setAttribute("data-id",event.target.getAttribute('data-id'));
			});
		});
		
		
		//go read the rules for a given resource
		document.querySelectorAll(".searchRule").forEach(button => {
			button.addEventListener("click", function () {
				const searchValue = this.getAttribute("ruleitem");
				
				if (!searchValue.trim()) {
					alert("Please enter a search term.");
					return;
				}
				
				fetch("./api-dashboard-back.php", {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify({ query: searchValue, action: "getRules" })
				})
				.then(response => response.json())
				.then(data => {
					if (data.error) {
						console.error("Error:", data.error);
						alert("An error occurred while fetching results: " + data.error);
						return;
					}
					document.getElementById('openModalCreateRuleButton').setAttribute('api-data',searchValue);
					document.getElementById('openModalCreateRuleButton').setAttribute('idapi',searchValue);
					const existingTable = document.getElementById("rulesTable");
					if (existingTable) {
						$("#rulesTable").DataTable().destroy();
						existingTable.remove();
					}
					const table = document.createElement("table");
					table.id = "rulesTable";
					const thead = document.createElement("thead");
					const tbody = document.createElement("tbody");

					const columns = ["Resource name", "User", "Kind of rule", "Valid from", "Valid to", "Details of rules", "Delete", "View Accesses"];

					const headerRow = document.createElement("tr");
					columns.forEach(key => {
						const th = document.createElement("th");
						th.textContent = key;
						headerRow.appendChild(th);
					});
					thead.appendChild(headerRow);
					data.results.forEach(item => {
						const row = document.createElement("tr");
						["Resource name", "User", "Kind of rule", "Valid from", "Valid to", "Details of rules"].forEach(key => {
							const cell = document.createElement("td");
							cell.textContent = item[key];
							row.appendChild(cell);
						});
						const deleteCell = document.createElement("td");
						const deleteButton = document.createElement("button");
						deleteButton.classList.add('delDashBtn');
						deleteButton.textContent = 'Delete rule';
						deleteButton.setAttribute('data-deletion-id',item['Resource id']);
						deleteButton.setAttribute('data-deletion-user',item['User']);
						deleteButton.setAttribute('onClick','callDelete(this)');
						deleteCell.appendChild(deleteButton);
						row.appendChild(deleteCell);
						const viewCell = document.createElement("td");
						const viewButton = document.createElement("button");
						viewButton.classList.add('viewDashBtn');
						viewButton.textContent = 'View Accesses';
						viewButton.setAttribute('data-view-id',item['Resource id']);
						viewButton.setAttribute('data-view-user',item['User']);
						viewButton.setAttribute('data-toggle',"modal");
						viewButton.setAttribute('data-target',"#modalviewaccess");
						viewButton.setAttribute('data-dismiss',"modal");
						viewButton.addEventListener("click", showAccesses, false);
						viewCell.appendChild(viewButton);
						row.appendChild(viewCell);
						tbody.appendChild(row);
					});

					table.appendChild(thead);
					table.appendChild(tbody);
					document.getElementById("showRulesdiv").appendChild(table);
					$(document).ready(function () {
						$("#rulesTable").DataTable();
					});
						})
				.catch(error => {
					console.error("Fetch error:", error);
					alert("Failed to retrieve data.");
				});
			});
		});
		
		//make the main table a datatable
		$('#APIsTable').DataTable();
		function callDelete(caller) {
			const deletionid = caller.getAttribute("data-deletion-id");
			const deletionuser = caller.getAttribute("data-deletion-user");
			if (!deletionid.trim() || !deletionuser.trim()) {
				alert("It seems something is missing?");
				return;
			}
			
			fetch("./api-dashboard-back.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({ deletionid: deletionid, deletionuser: deletionuser, action: "deleteRule" })
			})
			.then(response => response.json())
			.then(data => {
				if (data.error) {
					console.error("Error:", data.error);
					alert("An error occurred while fetching results: " + data.error);
					return;
				}
				alert("Deletion successful");
				window.location.reload();
			});
		}
		
		function getUsers() {
			fetch("./api-dashboard-back.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({  action: "getUsers" })
			})
			.then(response => response.json())
			.then(data => {
				const selectElement = document.getElementById("selectUserRule");

				if (data.result && Array.isArray(data.result)) {
					// Clear existing options
					selectElement.innerHTML = "";

					// Add default option
					const defaultOption = document.createElement("option");
					defaultOption.value = "";
					defaultOption.textContent = "Select a user";
					defaultOption.disabled = true;
					defaultOption.selected = true;
					selectElement.appendChild(defaultOption);

					// Populate new options
					data.result.forEach(username => {
						const option = document.createElement("option");
						option.value = username;
						option.textContent = username;
						selectElement.appendChild(option);
					});
				} else if (data.error) {
					// Show error alert
					alert(data.error);
				} else {
					// Fallback error message
					alert("An unknown error occurred.");
				}
				
				});
		}
		
		function showAccesses(evt) {
				
				fetch("./api-dashboard-back.php", {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify({ user: evt.currentTarget.dataset.viewUser, resource: evt.currentTarget.dataset.viewId, action: "getAccesses" })
				})
				.then(response => response.json())
				.then(data => {
					if (data.error) {
						console.error("Error:", data.error);
						alert("An error occurred while fetching results: " + data.error);
						return;
					}
					const existingTable = document.getElementById("accessesTable");
					if (existingTable) {
						$("#accessesTable").DataTable().destroy();
						existingTable.remove();
					}
					const table = document.createElement("table");
					table.id = "accessesTable";
					const thead = document.createElement("thead");
					const tbody = document.createElement("tbody");

					const columns = ["User", "Api Name", "Begin Access", "End Access"];

					const headerRow = document.createElement("tr");
					columns.forEach(key => {
						const th = document.createElement("th");
						th.textContent = key;
						headerRow.appendChild(th);
					});
					thead.appendChild(headerRow);
					data.results.forEach(item => {
						const row = document.createElement("tr");
						["User", "Api Name", "Begin Access", "End Access"].forEach(key => {
							const cell = document.createElement("td");
							if (item[key]) {
								cell.textContent = item[key];
							}
							else {
								cell.textContent = "End of connection not set";
							}
							row.appendChild(cell);
						});
						
						tbody.appendChild(row);
					});

					table.appendChild(thead);
					table.appendChild(tbody);
					document.getElementById("showAccessesdiv").appendChild(table);
					$(document).ready(function () {
						$("#accessesTable").DataTable();
					});
						})
				.catch(error => {
					console.error("Fetch error:", error);
					alert("Failed to retrieve data.");
				});
			};
		function deleteapi() {
			fetch("./api-dashboard-back.php", {
					method: "POST",
					headers: {
						"Content-Type": "application/json"
					},
					body: JSON.stringify({ apiID: document.getElementById("viewAccessCloseBtn").getAttribute("data-id"), action: "deleteAPI" })
				})
				.then(response => response.json())
				.then(data => {
					if (data.error) {
						console.error("Error:", data.error);
						alert("An error occurred while fetching results: " + data.error);
						return;
					}
					alert("Deletion successful");
					window.location.reload();
				});
		};
		/*
		function generateRuleForm() {
			var a = document.getElementById('selectRuleKind');
			document.getElementById('createRuleDiv').innerHTML = "";
			if (a.value == 'ContemporaryAccess') {
				document.getElementById('createRuleDiv').innerHTML = `
				<div class="col-xs-12 col-md-12 modalCell">
					<div class="modalFieldCnt">
						<input type="number" min="1" class="modalInputTxt" name="createRuleAmount" id="createRuleAmount" required> 
					</div>
					<div class="modalFieldLabelCnt">Amount of contemporary accesses allowed</div>
					<div id="inputRule1Msg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				`;
			} else if (a.value == 'AccessesOverTime') {
				document.getElementById('createRuleDiv').innerHTML = `
				<div class="col-xs-12 col-md-6 modalCell">
					<div class="modalFieldCnt">
						<input type="number" min="1" class="modalInputTxt" name="createRuleAmount" id="createRuleAmount" required> 
					</div>
					<div class="modalFieldLabelCnt">Amount of accesses</div>
					<div id="inputRule1Msg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				<div class="col-xs-12 col-md-6 modalCell">
					<div class="modalFieldCnt">
						<select id="selectRuleTimePeriod" name="selectRuleTimePeriod" class="modalInputTxt">
							<option disabled selected>Select an option</option>
							<option value="0">Each Day</option>
							<option value="1">Each Week</option>
							<option value="2">Each Month</option>
							<option value="3">Each Year</option>
						</select>
					</div>
					<div class="modalFieldLabelCnt">Frequency of limit renewal</div>
					<div id="selectRuleTimePeriodMsg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				`;
			} else if (a.value == "TotalAccesses") {
				document.getElementById('createRuleDiv').innerHTML = `
				<div class="col-xs-12 col-md-12 modalCell">
					<div class="modalFieldCnt">
						<input type="number" min="1" class="modalInputTxt" name="createRuleAmount" id="createRuleAmount" required> 
					</div>
					<div class="modalFieldLabelCnt">Amount of total accesses allowed</div>
					<div id="inputRule1Msg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				`;
			} else if (a.value == "invalid") {
				return;
			} else { alert("invalid choice for limit");}
			
		};
		*/
		
		function generateRuleFormSecond() {
			var a = document.getElementById('selectRuleKindSecond');
			document.getElementById('createRuleDivSecond').innerHTML = "";
			if (a.value == 'ContemporaryAccess') {
				document.getElementById('createRuleDivSecond').innerHTML = `
				<div class="col-xs-12 col-md-12 modalCell">
					<div class="modalFieldCnt">
						<input type="number" min="1" class="modalInputTxt" name="createRuleAmount" id="createRuleAmount" required> 
					</div>
					<div class="modalFieldLabelCnt">Amount of contemporary accesses allowed</div>
					<div id="inputRule1Msg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				`;
			} else if (a.value == 'AccessesOverTime') {
				document.getElementById('createRuleDivSecond').innerHTML = `
				<div class="col-xs-12 col-md-6 modalCell">
					<div class="modalFieldCnt">
						<input type="number" min="1" class="modalInputTxt" name="createRuleAmount" id="createRuleAmount" required> 
					</div>
					<div class="modalFieldLabelCnt">Amount of accesses</div>
					<div id="inputRule1Msg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				<div class="col-xs-12 col-md-6 modalCell">
					<div class="modalFieldCnt">
						<select id="selectRuleTimePeriod" name="selectRuleTimePeriod" class="modalInputTxt">
							<option disabled selected>Select an option</option>
							<option value="0">Each Day</option>
							<option value="1">Each Week</option>
							<option value="2">Each Month</option>
							<option value="3">Each Year</option>
						</select>
					</div>
					<div class="modalFieldLabelCnt">Frequency of limit renewal</div>
					<div id="selectRuleTimePeriodMsg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				`;
			} else if (a.value == "TotalAccesses") {
				document.getElementById('createRuleDivSecond').innerHTML = `
				<div class="col-xs-12 col-md-12 modalCell">
					<div class="modalFieldCnt">
						<input type="number" min="1" class="modalInputTxt" name="createRuleAmount" id="createRuleAmount" required> 
					</div>
					<div class="modalFieldLabelCnt">Amount of total accesses allowed</div>
					<div id="inputRule1Msg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				`;
			} else if (a.value == "invalid") {
				return;
			} else { alert("invalid choice for limit");}
			
		};
		
		
		function generateAPIForm() {
			var a = document.getElementById('selectAPIkind');
			document.getElementById('additionalInfoOptionalDataAPI').innerHTML = "";
			if (a.value == 'ClearMLStable' || a.value == 'ClearMLSporadic') {
				document.getElementById('additionalInfoOptionalDataAPI').innerHTML = `
				<div class="col-xs-12 col-md-12 modalCell">
					<div class="modalFieldCnt">
						<input class="modalInputTxt" name="createAPICMLData" id="createAPICMLData" required> 
					</div>
					<div class="modalFieldLabelCnt">Additional data for ClearML</div>
					<div id="inputRule2Msg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				`;
			} else if (a.value == 'Other') {
				document.getElementById('additionalInfoOptionalDataAPI').innerHTML = `
				<div class="col-xs-12 col-md-12 modalCell">
					<div class="modalFieldCnt">
						<input  class="modalInputTxt" name="createAPIGenericData" id="createAPIGenericData" required> 
					</div>
					<div class="modalFieldLabelCnt">Additional Data</div>
					<div id="inputRule2Msg" class="modalFieldMsgCnt">&nbsp;</div>
				</div>
				`;
			} 			
		};
		
		
		
		
		function createAPI() {
			const container = document.getElementById("modalfirst");
			if (!container) return null;

			const values = {};
			let hasInvalidOrEmpty = false;

			// Get all input and select elements inside the div
			container.querySelectorAll('input, select').forEach(element => {
				const value = element.value.trim(); // Trim to remove unnecessary spaces
				values[element.name || element.id] = value;

				// Check for "invalid" or empty values
				if (value === "invalid" || value === "") {
					hasInvalidOrEmpty = true;
				}
			});

			if (hasInvalidOrEmpty) {
				alert("Invalid or empty value detected!");
				return null;
			}
			fetch("./api-dashboard-back.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({ ...values, action: "makeApi" })
			})
			.then(response => response.json())
			.then(data => {
				if (data.error) {
					console.error("Error:", data.error);
					alert("An error occurred while fetching results: " + data.error);
					return;
				}
				alert("API added successfully");
				window.location.reload();
			});
		};
		function editAPI() {
			const container = document.getElementById("modalfourth");
			if (!container) return null;

			const values = {};
			let hasInvalidOrEmpty = false;

			// Get all input and select elements inside the div
			container.querySelectorAll('input, select').forEach(element => {
				let value;
				
				if (element.type === "checkbox") {
					value = element.checked; // Store boolean for checkboxes
				} else {
					value = element.value.trim(); // Trim spaces for text inputs and selects
				}
				values[element.name || element.id] = value;

				// Check for "invalid" or empty values
				if (value === "invalid" || value === "") {
					hasInvalidOrEmpty = true;
				}
			});

			if (hasInvalidOrEmpty) {
				alert("Invalid or empty value detected!");
				return null;
			}
			fetch("./api-dashboard-back.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({ ...values, action: "editApi" })
			})
			.then(response => response.json())
			.then(data => {
				if (data.error) {
					console.error("Error:", data.error);
					alert("An error occurred while fetching results: "+data.error);
					return;
				}
				alert("API edited successfully");
				window.location.reload();
			});
		};
		
		function setupNewRuleForm(caller) {
			document.getElementById('addRuleResourceFieldSecond').value=caller.getAttribute('api-data');
			let now = new Date();
            let nextDay = new Date(now);
            nextDay.setDate(nextDay.getDate() + 1);
            
            let formatDateTime = (date) => date.toISOString().slice(0, 16);
            
            document.getElementById("addRuleStartingOfValidity").value = formatDateTime(now);
            document.getElementById("addRuleEndingOfValidity").value = formatDateTime(nextDay);
            
            document.getElementById("addRuleStartingOfValidity").addEventListener("change", enforceDateOrder);
            document.getElementById("addRuleEndingOfValidity").addEventListener("change", enforceDateOrder);
			
        
		};
		
		function enforceDateOrder() {
            let addRuleStartingOfValidity = new Date(document.getElementById("addRuleStartingOfValidity").value);
            let addRuleEndingOfValidity = new Date(document.getElementById("addRuleEndingOfValidity").value);
            
            if (addRuleEndingOfValidity <= addRuleStartingOfValidity) {
                addRuleEndingOfValidity = new Date(addRuleStartingOfValidity);
                addRuleEndingOfValidity.setDate(addRuleEndingOfValidity.getDate() + 1);
                document.getElementById("addRuleEndingOfValidity").value = addRuleEndingOfValidity.toISOString().slice(0, 16);
            }
        }
		
		function createRuleSecond() {
			const container = document.getElementById("modalsecondsecond");
			if (!container) return null;

			const values = {};
			let hasInvalidOrEmpty = false;

			// Get all input and select elements inside the div
			container.querySelectorAll('input, select').forEach(element => {
				const value = element.value.trim(); // Trim to remove unnecessary spaces
				values[element.name || element.id] = value;

				// Check for "invalid" or empty values
				if (value === "invalid" || value === "") {
					hasInvalidOrEmpty = true;
				}
			});

			if (hasInvalidOrEmpty) {
				alert("Invalid or empty value detected!");
				return null;
			}
			fetch("./api-dashboard-back.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({ ...values, action: "makeRule" })
			})
			.then(response => response.json())
			.then(data => {
				if (data.error) {
					console.error("Error:", data.error);
					alert("An error occurred while fetching results: " + data.error);
					return;
				}
				alert("Rule added successfully");
				window.location.reload();
			});
		};
	</script>

    </body>

</html>