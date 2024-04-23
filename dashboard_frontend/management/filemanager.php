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

include('process-form.php');
header("Cache-Control: private, max-age=$cacheControlMaxAge");

session_start();
//checkSession('AreaManager');
checkSession('Manager');

$lastUsedColors = null;

?>
<!DOCTYPE HTML>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>File Manager</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" href="../css/style_widgets.css?v=<?php
    echo time();
    ?>" type="text/css" />
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/chat.css" type="text/css" />

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700|Catamaran|Varela+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

    <!-- jQuery -->

    <script src="../js/jquery-1.10.1.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Bootstrap Multiselect -->
    <script src="../js/bootstrap-multiselect_1.js"></script>
    <link href="../css/bootstrap-multiselect_1.css" rel="stylesheet">

    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="../js/DataTables/datatables.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/DataTables/datatables.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../js/DataTables/responsive.bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/DataTables/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/DataTables/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="../js/DataTables/Select-1.2.5/js/dataTables.select.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/DataTables/Select-1.2.5/css/select.dataTables.min.css">

    <!-- Select2-->
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.min.js"></script>  -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">

    <!-- Gridster -->
    <link rel="stylesheet" type="text/css" href="../css/jquery.gridster.css">
    <script src="../js/jquery.gridsterMod.js" type="text/javascript" charset="utf-8"></script>

    <!-- New Gridster -->
    <!--<link rel="stylesheet" type="text/css" href="../newGridster/dist/jquery.gridster.css">
    <script src="../newGridster/dist/jquery.gridster.js" type="text/javascript" charset="utf-8"></script>-->

    <!-- CKEditor -->
    <script src="../js/ckeditor/ckeditor.js"></script>
    <link rel="stylesheet" href="../js/ckeditor/skins/moono/editor.css">

    <!-- Filestyle -->
    <script type="text/javascript" src="../js/filestyle/src/bootstrap-filestyle.min.js"></script>

    <!-- JQUERY UI -->
    <!--<script src="../js/jqueryUi/jquery-ui.js"></script>

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../js/fontAwesome/css/font-awesome.min.css">

    <!-- Bootstrap colorpicker -->
    <script src="../js/bootstrap-colorpicker.min.js"></script>

    <!-- Modernizr -->
    <script src="../js/modernizr-custom.js"></script>

    <!-- Color pickers -->
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <script src="../js/bootstrap-colorpicker.min.js"></script>

    <!-- Highcharts -->
    <script src="../js/highcharts/code/highcharts.js"></script>
    <script src="../js/highcharts/code/modules/exporting.js"></script>
    <script src="../js/highcharts/code/highcharts-more.js"></script>
    <script src="../js/highcharts/code/modules/solid-gauge.js"></script>
    <script src="../js/highcharts/code/highcharts-3d.js"></script>

    <!-- Bootstrap editable tables -->
    <link href="../bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
    <script src="../bootstrap3-editable/js/bootstrap-editable.js"></script>

    <!-- TinyColors -->
    <script src="../js/tinyColor.js" type="text/javascript" charset="utf-8"></script>

    <!-- Bootstrap select -->
    <link href="../bootstrapSelect/css/bootstrap-select.css" rel="stylesheet" />
    <script src="../bootstrapSelect/js/bootstrap-select.js"></script>

    <!-- Moment -->
    <script type="text/javascript" src="../moment/moment.js"></script>

    <!-- Bootstrap datetimepicker -->
    <script src="../datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="../datetimepicker/build/css/bootstrap-datetimepicker.min.css">

    <!-- Bootstrap toggle button -->
    <link href="../bootstrapToggleButton/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="../bootstrapToggleButton/js/bootstrap-toggle.min.js"></script>

    <!-- html2canvas -->
    <script type="text/javascript" src="../js/html2canvas.js"></script>

    <!-- Leaflet -->
    <!-- Versione locale: 1.3.1 -->
    <link rel="stylesheet" href="../leafletCore/leaflet.css" />
    <script src="../leafletCore/leaflet.js"></script>

    <!-- Leaflet Wicket: libreria per parsare i file WKT -->
    <script src="../wicket/wicket.js"></script>
    <script src="../wicket/wicket-leaflet.js"></script>

    <!-- Leaflet Zoom Display -->
    <script src="../js/leaflet.zoomdisplay-src.js"></script>
    <link href="../css/leaflet.zoomdisplay.css" rel="stylesheet" />

    <!-- Dot dot dot -->
    <script src="../dotdotdot/jquery.dotdotdot.js" type="text/javascript"></script>

    <!-- Bootstrap slider -->
    <script src="../bootstrapSlider/bootstrap-slider.js"></script>
    <link href="../bootstrapSlider/css/bootstrap-slider.css" rel="stylesheet" />

    <!-- Weather icons -->
    <link rel="stylesheet" href="../img/meteoIcons/singleColor/css/weather-icons.css?v=<?php
    echo time();
    ?>">

    <!-- Text fill -->
    <script src="../js/jquery.textfill.min.js"></script>

    <!-- Custom CSS -->
    <link href="../css/dashboard.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <link href="../css/dashboardView.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <link href="../css/addWidgetWizard2.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <link href="../css/addDashboardTab.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <link href="../css/dashboard_configdash.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <link href="../css/widgetCtxMenu_1.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <link href="../css/widgetDimControls_1.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <link href="../css/widgetHeader_1.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <script src="../js/widgetsCommonFunctions.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../js/dashboard_configdash.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/trafficEventsTypes.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/alarmTypes.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <script src="../widgets/fakeGeoJsons.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>
    <link href="../css/chat.css?v=<?php
    echo time();
    ?>" rel="stylesheet">
    <!--<script src="../js/bootstrap-ckeditor-.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>-->

</head>
<style type="text/css">
    .left {
        float: left;
    }

    .right {
        float: right;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 82px;
        height: 20px;
    }

    .switch input {
        display: none;
    }

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

    .fixMapon {
        display: none;
    }

    .fixMapon,
    .fixMapoff {
        color: white;
        position: absolute;
        transform: translate(-50%, -50%);
        top: 50%;
        left: 50%;
        font-size: 10px;
        font-family: Verdana, sans-serif;
    }

    input:checked+ .slider .on {
        display: block;
    }

    input:checked + .slider .off {
        display: none;
    }
    /*--------- END --------*/
    /* Rounded sliders */

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .dropdown1 {

        overflow:scroll;
        height: 200px;
    }
    .dashboardsTableHeader{
        background-color: #337ab7;
        color: white;
    }

    .usersTableHeader{
        background-color: #337ab7;
        color: white;
    }

    #dropdownMenuButton{
        float: left
    }

    #value_table{
        height: 90%;
        width: 90%;
    }

    #Map1 {
        max-height: 100%;
        width: 100%;
    }

    #Map2 {
        max-height: 100%;
        width: 100%;
    }

    .my-leaflet-map-container img {
        max-height: none;
    }

    input.pw {
  -webkit-text-security: disc;
}
.publicBtn, .delegatedBtn
{
    height: 20px;
    background-color: white;
    border: none;
    color: rgba(0, 162, 211, 1);
    font-family: 'Montserrat';
    font-weight: bold;
    text-transform: uppercase;
}

</style>

<body style="overflow-y: hidden !important">

    <!-- Inizio dei modali -->
    <!-- Modale wizard -->
    <div class="modal-content modalContentWizardForm">
        <!--   <div class="modalHeader centerWithFlex">
              &nbsp;&nbsp;&nbsp;
            </div>  -->

        <div id="addWidgetWizardLabelBody" class="body">
            <!-- -->
            <div id="select_element_type" style="margin-left: 5%; margin: 2%;  float: left">
                <!-- -->
                <button type="button" id="new_file_modal" class="btn btn-warning new_org" data-toggle="modal" data-target="#myModal_new" style="float:left; margin-right: 5px;">
                    <i class="fa fa-plus"></i>
                    Add new File
                </button>
            </div>
            <!-- -->
            <div id="table_div" style="margin-left: 5%; margin-right: 5%">
                <!-- -->
                <table id="value_table" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="dashboardsTableHeader">
                    <th>Name</th>
                    <th>Description</th>
                    <th>Subnature</th>
                    <th>Language</th>
                    <th>Size</th>
                    <th>Date</th>
                    <th>Organization</th>
                    <th>Location</th>
                    <th>Edit</th>
                    <th>View</th>
                    <th>Delete</th>
                    <th>Ownership</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Fine modal content -->
    <!-- Modal -->
    <!--</div> <!-- Fine modal dialog -->
    <!--</div><!-- Fine modale -->
    <!-- Fine modale wizard -->
    <!-- Modal -->
    <div class="modal fade" id="myModal_new" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("New File") ?></h5>
                </div>
                <form id="formElement" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="input-group"><span class="input-group-addon"><?= _("Description") ?>:</span><input id="new_description" name="description" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Language") ?>:</span>
                       <select id="new_language" name="language" class="form-control">
                            <option value="English">English</option>
                            <option value="Italian">Italian</option>
                            <option value="French">French</option>
                            <option value="Spanish">Spanish</option>
                            <option value="German">German</option>
                            <option value="Finnish">Finnish</option>
                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Subnature") ?>:</span>
                        <select id="new_subnature" name="subnature" class="form-control">
                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("File type") ?>:</span>
                        <select id="new_filetype" name="filetype" class="form-control">
                        </select>
                    </div><br />
                    <div class="input-group">
                    <span class="input-group-addon"><?= _("Set coordinates") ?>:</span>
                    <div id="Map1" style="height:200px">
                    </div></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Latitude") ?>:</span><input id="new_latitude" name="latitude" type="text" class="form-control" oninput="new_coords()" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Longitude") ?>:</span><input id="new_longitude" name="longitude" type="text" class="form-control" oninput="new_coords()"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Select a File") ?>:</span><input id="new_file" type="file" class="form-control" name="new_file" onchange="return fileValidation()"></div><br />

                </div><br />
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_new"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
    <div class="modal fade" id="edit_file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("Edit File Metadata") ?></h5>
                </div>
                <div class="modal-body">
                    <input id="edit_id" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="edit_filename" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="edit_language" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="edit_filesize" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="edit_filetype" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="edit_date" type="text" class="form-control" readonly style="display:none;"/>
                                        <input id="edit_newfileid" type="text" class="form-control" readonly style="display:none;"/>
                    <div class="input-group"><span class="input-group-addon"><?= _("Description") ?>:</span><input id="edit_description" name="description" type="text" class="form-control" ></div><br />


                    <div class="input-group"><span class="input-group-addon"><?= _("Subnature") ?>:</span>
                        <select id="edit_subnature" name="subnature" class="form-control">
                        </select>
                    </div><br />

                    <div class="input-group">
                        <span class="input-group-addon"><?= _("Set coordinates") ?>:</span>
                        <div id="Map2" style="height:200px"></div></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Latitude") ?>:</span><input id="edit_latitude" name="latitude" type="text" class="form-control" oninput="edit_coords()" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Longitude") ?>:</span><input id="edit_longitude" name="longitude" type="text" class="form-control" oninput="edit_coords()"></div><br />

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_edit"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->
    <div class="modal fade" id="delete_file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("Delete File") ?></h5>
                </div>
                <div class="modal-body">
                    <input id="delete_id" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="delete_broker" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="delete_fileid" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="delete_filetype" type="text" class="form-control" readonly style="display:none;"/>
                    <?= _("Are you sure do you want to delete this file- from database?") ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_del"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
    <div class="modal fade" id="view_file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("View File") ?></h5>
                </div>
                <div class="modal-body">
                    <input id="view_fileid" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="view_filetype" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="view_filename" type="text" class="form-control" readonly style="display:none;"/>
                    <?= _("Are you sure do you want to read/download this file?") ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_view"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
        <!--  -->


    <div class="modal fade" id="delegationsModal" tabindex="-1" role="dialog" aria-labelledby="modalAddWidgetTypeLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div id="delegationHeadModalLabel"  class="modalHeader centerWithFlex">

        </div>
                <form class="form-horizontal">

                <div id="delegationsModalBody" class="modal-body modalBody">
                        <!-- Tabs -->
                        <ul id="delegationsTabsContainer" class="nav nav-tabs nav-justified">
                            <li id="ownershipTab" class="active"><a data-toggle="tab" href="#ownershipCnt" class="dashboardWizardTabTxt" aria-expanded="false">Ownership</a></li>
                            <li id="visibilityTab"><a data-toggle="tab" href="#visibilityCnt" class="dashboardWizardTabTxt">Visibility</a></li>
                            <li id="delegationsTab"><a data-toggle="tab" href="#delegationsCnt" class="dashboardWizardTabTxt">Delegations</a></li>
                            <li id="delegationsTabGroup"><a data-toggle="tab" href="#delegationsCntGroup" class="dashboardWizardTabTxt">Group Delegations</a></li>
                        </ul>
                        <!-- Fine tabs -->

                        <!-- Tab content -->
                        <div class="tab-content">

                            <!-- Visibility cnt -->
                            <div id="visibilityCnt" class="tab-pane fade in">
                            <div class="row" id="visibilityFormRow">
                                <legend><div class="col-xs-12 centerWithFlex delegationsModalLbl modalFirstLbl" id="changeOwnershipLbl">
                                        Change visibility
                                </div> </legend>
                                <div class="row" class="col-xs-12 col-md-6">
                                <!--<div class="col-xs-12" id="newVisibilityCnt"> -->
                                <div class="col-xs-12 col-md-2" id="newVisibilityCnt">

                                    <div id="visID"></div>
                                </div>
                                <div class="col-xs-12 col-md-6" id="newVisibilityCnt">
                                        <div  class="row">

                                            <button type="button" id="newVisibilityPublicBtn" class="btn pull-right confirmBtn">Make It Public</button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                            <button type="button" id="newVisibilityPrivateBtn" class="btn pull-right confirmBtn">Make It Private</button>

                                        </div>

                                </div>
                                <!-- <div class="col-xs-12 centerWithFlex" id="newVisibilityResultMsg"> -->
                                <div class="col-xs-12 col-md-4" id="newVisibilityResultMsg">

                                </div>

                                </div>
                            </div>
                            </div>

                            <!-- Ownership cnt -->
                            <div id="ownershipCnt" class="tab-pane fade in active">
                            <div class="row" id="ownershipFormRow">
                                <legend><div class="col-xs-12 centerWithFlex delegationsModalLbl modalFirstLbl" id="changeOwnershipLbl">
                                        Change ownership
                                </div> </legend>
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

                            <!-- Delegation cnt -->
                            <div id="delegationsCnt" class="tab-pane fade in">
                            <div class="row" id="delegationsFormRow">
                                <legend><div class="col-xs-12 centerWithFlex modalFirstLbl" id="newDelegationLbl">
                                        Add new delegation
                                </div></legend>
                                <div class="col-xs-12" id="newDelegationCnt">
                                <div class="input-group">
                                        <input type="text" class="form-control" name="newDelegation" id="newDelegation" placeholder="Delegated username">
                                        <span class="input-group-btn">
                                        <button type="button" id="newDelegationConfirmBtn" class="btn confirmBtn disabled">Confirm</button>
                                        </span>
                                </div>
                                <div class="col-xs-12 centerWithFlex delegationsModalMsg" id="newDelegatedMsg">
                                        Delegated username can't be empty
                                </div>
                                </div>

                                <legend><div class="col-xs-12 centerWithFlex" id="currentDelegationsLbl">
                                        Current delegations
                                </div></legend>
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

                            <!-- Delegation Group cnt -->
                            <div id="delegationsCntGroup" class="tab-pane fade in">
                                    <div class="row" id="delegationsFormRowGroup">
                                        <legend><div class="col-xs-12 centerWithFlex modalFirstLbl" id="newDelegationLblGroup">
                                                Add new Group delegation
                                            </div></legend>
                                        <div class="col-xs-12"  class="input-group">
                                            <div id="newDelegationCntGroup">
                                                <div class="col-xs-6">
                                                    <select name="newDelegationOrganization" id="newDelegationOrganization" class="modalInputTxt">
                                                    </select>
                                                </div>
                                                <div class="col-xs-6">
                                                    <select name="newDelegationGroup" id="newDelegationGroup" class="modalInputTxt">
                                                    </select>
                                                </div>
                                                <span class="col-xs-12 input-group-btn" style="width:100%">
                                                    <button type="button" id="newDelegationConfirmBtnGroup" class="btn confirmBtn" style="margin:10px 0;width:100%">Confirm</button>
                                                </span>
                                                <div class="col-xs-12 centerWithFlex delegationsModalMsg" id="newDelegatedMsgGroup">
                                                </div>
                                            </div>
                                        </div>
                                        <legend><div class="col-xs-12 centerWithFlex" id="currentDelegationsLblGroup">
                                                Current Group delegations
                                            </div></legend>
                                        <div class="col-xs-12" id="delegationsTableCntGroup">
                                            <table id="delegationsTableGroup" style="width:100%">
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




                        </div>
                </div>
                <div id="delegationsModalFooter" class="modal-footer">
                    <button type="button" id="delegationsCancelBtn" class="btn cancelBtn" data-dismiss="modal">Close</button>
                </div>
                </form>
        </div>
        </div>
    </div>

    <div class="modal fade" id="addMapShow" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modalHeader centerWithFlex">
                                        File Location on Map
                </div>
                                <div class="form-row iot-directory-form-row">
                                        <link rel="stylesheet" href="http://dashboard/iot2/css/leaflet.css" />
                                                <link rel="stylesheet" href="http://dashboard/iot2/css/leaflet.draw.css" />
                                                <div id="addDeviceMapModalBodyShow" style="width: 100%; height: 400px" class="modal-body modalBody">
                                        </div>
                                </div>
                                <div class="modal-footer">
                  <button type="button" id="cancelMapBtn" class="btn cancelBtn"  data-dismiss="modal">Cancel</button>
                </div>
              </div>
            </div>
        </div>



    <!-- -->
    <div class="modal fade" id="loading_div" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <center>
                    <span><?= _("Loading") ?>...</span>
                    <br />
                   <i class="fa fa-circle-o-notch fa-spin" style="font-size:48px; color: #337ab7"></i>
                   <center />
                </div>
            </div>
        </div>
    </div>
    <!-- -->
<script type='text/javascript'>
    var loggedRole = "<?php echo $_SESSION['loggedRole']; ?>";
    var loggedUser = "<?php echo $_SESSION['loggedUsername']; ?>";
    console.log(loggedUser);
        //*********MAPPA*********//
    var mymap = new L.Map('Map1');
    mymap.setView([43.769789, 11.255694], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
        maxZoom: 18,
        minZoom: 1,
        center: [43.769789, 11.255694],
        closePopupOnClick: false
    }).addTo(mymap);
    mymap.attributionControl.setPrefix('');
    setTimeout(mymap.invalidateSize.bind(mymap));
    ////////////
    var mymap2 = new L.Map('Map2');
    mymap2.setView([43.769789, 11.255694], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
        maxZoom: 18,
        minZoom: 3,
        center: [43.769789, 11.255694],
        closePopupOnClick: false
    }).addTo(mymap2);
    mymap2.attributionControl.setPrefix('');
    setTimeout(mymap2.invalidateSize.bind(mymap2));
//******FINE MAPPA******//
    $(document).ready(function () {
        console.log('filemanager ready');

        $.ajax({
            async: true,
            type: 'GET',
            dataType: 'json',
            url: 'getfiledata.php',
            data: {
                action: 'get_my_files'
                //              action: 'list_files'
            },
            error: function (data) { console.log(data);},
            success: function (data) {
                console.log('RESULTS:   ');
                                console.log(data);
                if (data['code'] === '200'){
                    var data = data['message'];
                    var len = data.length;

                for (var i = 0; i < len; i++) {
                    var subnature = '';
                    var newfileid = data[i]['newfileid']; //unique id
                    var deviceid = data[i]['deviceid'];
                    var filename = data[i]['filename']; //originalfilename
                    var description = data[i]['description'];
                    if ((data[i]['subnature'] === null) || (data[i]['subnature'] === 'null') || (data[i]['subnature'] === undefined)) {
                        subnature = '';
                    } else {
                        subnature = data[i]['subnature'];
                    }
                    var language = data[i]['language'];
                    var filesize = data[i]['filesize'];
                    var latitude = data[i]['latitude'];
                    var longitude = data[i]['longitude'];
                    var date = data[i]['date'];
                    var filetype = data[i]['filetype'];
                    var broker = data[i]['contextbroker'];
                    var organization = data[i]['organization'];
                    var visibility = data[i]['visibility'];
                    var k1=data[i]['k1'];
                    var k2=[i]['k2'];
                    if (loggedRole==='RootAdmin' || visibility ==='MyOwnPublic' || visibility === 'MyOwnPrivate'){
                        var edit_button = '<button type="button" id="edit_btn" class="editDashBtn edit_file" data-target="#edit_file" data-toggle="modal" onclick="func_edit(\'' + deviceid + '\', \'' + description + '\',  \'' + subnature + '\',  \'' + filename + '\', \'' + language + '\', \'' + filesize + '\', \'' + filetype + '\', \'' + date + '\', \'' + latitude + '\', \'' + longitude + '\',\''+newfileid+'\')">EDIT</button>  ';
                        var delete_button = '<button type="button" class="delDashBtn delete_file" data-target="#delete_file" data-toggle="modal" onclick="func_del(\'' + deviceid + '\',\'' + broker + '\',\'' +newfileid + '\',\'' + filetype + '\')">DELETE</button>';
                    }
                    else{
                        var edit_button ='&nbsp;';
                        var delete_button = '&nbsp;';
                    }
                    if (visibility === 'MyOwnPrivate') {
                        var management_button = '<button type="button"  class="delDashBtn" onclick="func_management(\'' + filename + '\',\'' + deviceid + '\',\'' + broker + '\',\'' + visibility + '\',\'' + k1 + '\',\'' + k2 + '\')">' + visibility + '</button>';
                    } else if (visibility === 'MyOwnPublic') {
                        var management_button = '<button type="button"  class="editDashBtn" onclick="func_management(\'' + filename + '\',\'' + deviceid + '\',\'' + broker + '\',\'' + visibility + '\',\'' + k1 + '\',\'' + k2 + '\')">' + visibility + '</button>';
                    } else if (visibility === 'public')
                    {
                        var management_button = '<div  class="publicBtn" >' + visibility + '</div>';
                    } else // value is private
                    {
                        var management_button =  '<div class= "delegatedBtn" >' + visibility + "</div>";
                    }
                    var position_button = '<div class="addMapBtn"><i  data-toggle="modal" data-target="#addMapShow" onclick="drawMap(\''+ latitude + '\',\'' + longitude + '\', \'' + filename + '\', \'' + 'addDeviceMapModalBodyShow' + '\')\" class="fa fa-globe"  style=\"font-size:36px; color: #0000ff\"></i></div>';

                    if(loggedRole==='RootAdmin' || visibility==='MyOwnPublic' || visibility==='MyOwnPrivate' || visibility==='public' || visibility==='delegated'){
                    var view_button='<button type="button" class="viewDashBtn" onclick="func_view(\'' + newfileid + '\',\'' + filetype + '\',\'' + filename + '\')" data-target="#view_file" data-toggle="modal">VIEW</button>';
                    }
                    else{
                        var view_button='&nbsp;';
                    }

                    $('#value_table tbody').append('<tr><td>' + filename + '</td><td>' + description + '</td><td>' + subnature + '</td><td>' + language + '</td><td>' + filesize + '</td><td>' + date + '</td><td>' + organization + '</td><td>' + position_button + '</td><td>' + edit_button + '</td><td>'+ view_button + '</td><td>' + delete_button + '</td><td>' + management_button + '</td></tr>');
                }

                }else{
                    console.log(data['message']);
                                        alert(data['message']);
                }
                var table = $('#value_table').DataTable({
                    "searching": true,
                    "paging": true,
                    "ordering": true,
                    "info": false,
                    "responsive": true,
                    "lengthMenu": [5, 10, 20, 50],
                    "iDisplayLength": 5,
                    "pagingType": "full_numbers",
                    "dom": '<"pull-left"l><"pull-right"f>tip',
                    "language": {"paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next >>",
                            "previous": "<< Prev"
                        },
                        "lengthMenu": "Show     _MENU_ "
                    }
                });
              }
        });
        //API LIST
        $('#button_conf_new').click(function () {
            $('#myModal_new').modal('hide');
        });
        //
        $('#new_file_modal').click(function () {
            $.ajax({
                async: true,
                type: 'GET',
                dataType: 'json',
                url: 'getfiledata.php',
                data: {
                    action: 'get_extensions'
                },
                success: function (data) {
                    $('#new_filetype').empty();
                    var lun = data.length;

                    for (var i = 0; i < lun; i++) {
                        $('#new_filetype').append('<option value="' + data[i] + '">' + data[i] + '</option>');
                    }

                }
            });
            $.ajax({
                async: true,
                type: 'GET',
                dataType: 'json',
                url: 'getfiledata.php',
                data: {
                    action: 'get_subnature'
                },
                success: function (data) {
                    var array_subnature = data['content'];
                    $('#new_subnature').empty();
                    var lun = array_subnature.length;

                    for (var i = 0; i < lun; i++) {
                        $('#new_subnature').append('<option value="' + array_subnature[i]['value'] + '">' + array_subnature[i]['value'] + '</option>');
                    }

                }
            });


        });
        //
        $('#conf_new').click(function () {
            console.log('conf_new');
            var form = document.getElementById('formElement');
            var form_data = new FormData(form);
            form_data.append('action', 'upload_file');
            $('#loading_div').modal('show');
            //
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                url: 'getfiledata.php',
                data:  form_data,
                success: function (data) {
                    console.log(data['message']);
                                        $('#loading_div').modal('hide');
                                        var code = data['code'];
                                        if (code == '200'){
                                                window.location.reload();
                                        }else{
                                                alert(data['message']);
                                        }

                }
            });
        });
        //
        $('#conf_edit').click(function () {
            var id = $('#edit_id').val();
            var new_subnature = $('#edit_subnature').val();
            var new_description = $('#edit_description').val();
            var new_latitude = $('#edit_latitude').val();
            var new_longitude = $('#edit_longitude').val();
            var filename = $('#edit_filename').val();
            var language = $('#edit_language').val();
            var filesize = $('#edit_filesize').val();
            var filetype = $('#edit_filetype').val();
            var date = $('#edit_date').val();
                        var newfileid =$('#edit_newfileid').val();
            //
            $('#loading_div').modal('show');
            //
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                url: 'getfiledata.php',
                data: {
                    action: 'edit_file',
                    id: id,
                    description: new_description,
                    subnature: new_subnature,
                    latitude: new_latitude,
                    longitude: new_longitude,
                    filename: filename,
                    language: language,
                    filesize: filesize,
                    filetype: filetype,
                    date: date,
                                        newfileid: newfileid

                },
                success: function (data) {
                    console.log(data['message']);
                    $('#loading_div').modal('hide');
                    window.location.reload();
                }
            });
        });

        $('#conf_del').click(function () {
            var deviceid = $('#delete_id').val();
            var broker = $('#delete_broker').val();
            var fileid = $('#delete_fileid').val();
            var filetype = $('#delete_filetype').val();
            $('#loading_div').modal('show');
            //
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                url: 'getfiledata.php',
                data: {
                    action: 'delete_file',
                    id: deviceid,
                    broker: broker,
                    fileid: fileid,
                    filetype: filetype
                },
                success: function (data) {
                    console.log(data['message']);
                    $('#loading_div').modal('hide');
                    window.location.reload();
                }
            });
        });

                ////////////////////
                /*function download(filename, text) {
                  var element = document.createElement('a');
                  element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
                  element.setAttribute('download', filename);

                  element.style.display = 'none';
                  document.body.appendChild(element);

                  element.click();

                  document.body.removeChild(element);
                }*/
                ///////////////

        $('#conf_view').click(function () {
            var fileid = $('#view_fileid').val();
            var filetype = $('#view_filetype').val();
            var filename = $('#view_filename').val();
            var mimetype = get_mimetype(filetype);
            $('#loading_div').modal('show');
            //
                        var url_file='getfiledata.php?action=view_file&fileid='+fileid+'&filetype='+filetype;
                        console.log(url_file);
                        window.open(url_file, 'download');
                        $('#loading_div').modal('hide');
                        //
           /* $.ajax({
                async: true,
                type: 'GET',
                url: 'getfiledata.php',
                datatype: 'binary',
                headers: {
                    Accept: 'application/octet-stream'
                },
                data: {
                    action: 'view_file',
                    fileid: fileid,
                    filetype: filetype
                },
                success: function (data) {

                    //console.log(data);
                                        //var obj = JSON.parse(data);
                                        //
                                        //e.preventDefault();  //stop the browser from following
                                        //window.location.href = obj.content;
                                        //window.open(obj.content, 'download');
                                        //window.location.open(obj.content, 'download');
                                        //var file = fileid+"."+filetype;
                                        //download(file,obj.content);
                    /*const a = document.createElement('a');
                    a.style = 'display: none';
                    document.body.appendChild(a);
                    const blob = new Blob([data], {type: 'application/octet-stream'});
                    const url = window.URL.createObjectURL(blob);
                    a.href = url;
                    a.download = filename;
                    a.click();
                    URL.revokeObjectURL(url);
                    $('#loading_div').modal('hide');
                }
            });*/
        });
        //////////////////

        mymap2.on('click',
                function (e) {
                    var zoom = mymap2.getZoom();
                    //
                    $('#edit_latitude').val(e.latlng.lat);
                    $('#edit_longitude').val(e.latlng.lng);
                    //
                    var greyIcon = new L.Icon({
                        iconUrl: 'https://www.snap4city.org/dashboardSmartCity/leafletCore/images/marker-icon.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41]
                        //iconAnchor: [e.latlng.lat, e.latlng.lng]
                    });
                    $(".leaflet-marker-icon").remove();
                    var marker = L.marker([e.latlng.lat, e.latlng.lng], {icon: greyIcon}).addTo(mymap2);
                });
        /////////
        mymap.on('click',
                function (e) {
                    var zoom = mymap.getZoom();
                    //
                    $('#new_latitude').val(e.latlng.lat);
                    $('#new_longitude').val(e.latlng.lng);
                    //
                    var greyIcon = new L.Icon({
                        iconUrl: 'https://www.snap4city.org/dashboardSmartCity/leafletCore/images/marker-icon.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41]
                        //iconAnchor: [e.latlng.lat, e.latlng.lng]
                    });
                    //mymap.removeLayer();
                    $(".leaflet-marker-icon").remove();
                    var marker = L.marker([e.latlng.lat, e.latlng.lng], {icon: greyIcon}).addTo(mymap);
                });
        ////////////////
        setTimeout(mymap.invalidateSize.bind(mymap));
        setTimeout(mymap2.invalidateSize.bind(mymap2));
        //
    });

    //map showing file location
    function drawMap(latitude,longitude, filename, divName){

     if (typeof map === 'undefined' || !map) {
             map = L.map(divName).setView([latitude,longitude], 10);
             L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                 attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
             }).addTo(map);

             window.node_input_map = map;
         }

         map.setView([latitude,longitude], 10);

     if (typeof theMarker !== 'undefined') {
             map.removeLayer(theMarker);
            }
     theMarker= L.marker([latitude,longitude]).addTo(map).bindPopup(filename);
     setTimeout(function(){
             map.invalidateSize();}, 400);
  }

    function fileValidation() {
        var fileInput = document.getElementById('new_file');

        var chosenExtension =  $('#new_filetype').val();//extension chosen in the form by the user
        var fileExtension = getFileExtension(fileInput.value);


        if (chosenExtension !== fileExtension) {
            alert('Invalid file type');
            fileInput.value = '';
            return false;
        }
    }

    function getFileExtension(fname){
        return fname.slice((fname.lastIndexOf(".") - 1 >>> 0) + 2);
    }

    function func_del(id, broker, fileid, filetype) {
        $('#delete_id').val(id);
        $('#delete_broker').val(broker);
        $('#delete_fileid').val(fileid);
        $('#delete_filetype').val(filetype);
    }

    function func_view(fileid, filetype, filename) {
        $('#view_fileid').val(fileid);
        $('#view_filetype').val(filetype);
        $('#view_filename').val(filename);
    }


    function edit_coords (){
        ///////////////////////////////////
        //var zoom = mymap2.getZoom();
                    //
                    var lat =$('#edit_latitude').val();
                    var lng = $('#edit_longitude').val();
                    //
                    var greyIcon = new L.Icon({
                        iconUrl: 'https://www.snap4city.org/dashboardSmartCity/leafletCore/images/marker-icon.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41]
                        //iconAnchor: [e.latlng.lat, e.latlng.lng]
                    });
                    $(".leaflet-marker-icon").remove();
                    mymap2.setView([lat, lng], 9);
                    var marker = L.marker([lat, lng], {icon: greyIcon}).addTo(mymap2);
                    setTimeout(mymap2.invalidateSize.bind(mymap2));

        //////////////////////////////////
    }

    function new_coords (){
        ////////
                   var lat = $('#new_latitude').val();
                   var lng = $('#new_longitude').val();
                    //
                    var greyIcon = new L.Icon({
                        iconUrl: 'https://www.snap4city.org/dashboardSmartCity/leafletCore/images/marker-icon.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41]
                        //iconAnchor: [e.latlng.lat, e.latlng.lng]
                    });
                    //mymap.removeLayer();
                    $(".leaflet-marker-icon").remove();
                    mymap.setView([lat, lng], 9);
                    var marker = L.marker([lat, lng], {icon: greyIcon}).addTo(mymap);
                    setTimeout(mymap.invalidateSize.bind(mymap));

        ////////
    }

    function func_edit(deviceid, description, subnature, filename, language, filesize, filetype, date, latitude, longitude, newfileid) {
        $(".leaflet-marker-icon").remove();
            mymap2.setView([latitude, longitude], 10);
            var greyIcon = new L.Icon({
                iconUrl: 'https://www.snap4city.org/dashboardSmartCity/leafletCore/images/marker-icon.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
                //iconAnchor: [latitude, longitude]
            });
            var marker = L.marker([latitude, longitude], {icon: greyIcon}).addTo(mymap2);
                //

                $('#edit_description').val(description);
                $('#edit_filename').val(filename);
                $('#edit_language').val(language);
                $('#edit_filesize').val(filesize);
                $('#edit_filetype').val(filetype);
                $('#edit_date').val(date);
                $('#edit_latitude').val(latitude);
                $('#edit_longitude').val(longitude);
                $('#edit_id').val(deviceid);
                                $('#edit_newfileid').val(newfileid);
        $.ajax({
                async: true,
                type: 'GET',
                dataType: 'json',
                url: 'getfiledata.php',
                data: {
                    action: 'get_subnature'
                },
                success: function (data) {
                    var array_subnature = data['content'];
                    $('#edit_subnature').empty();
                    var lun = array_subnature.length;

                    for (var i = 0; i < lun; i++) {
                        if(array_subnature[i]['value'] === subnature){
                            $('#edit_subnature').append('<option value="' + array_subnature[i]['value'] + '"selected>' + array_subnature[i]['value'] + '</option>');
                        }
                        else{
                        $('#edit_subnature').append('<option value="' + array_subnature[i]['value'] + '">' + array_subnature[i]['value'] + '</option>');
                        }
                    }

                }
            });

    };


    $("#myModal_new").on('shown.bs.modal', function (e) {
        setTimeout(function () {
            mymap.invalidateSize();
        }, 0);
    });

    $("#edit_file").on('shown.bs.modal', function (e) {
        setTimeout(function () {
            mymap2.invalidateSize();
        }, 0);
    });

//   START TO CHANGE THE VISIBILITY  & OWNERSHIP

    function func_management(name, deviceid, contextbroker, visibility, k1, k2) {
        $("#delegationsModal").modal('show');
        $("#delegationHeadModalLabel").html("File - " + name);
        var newVisibility = '';
        if(visibility==='MyOwnPrivate'){
                newVisibility = 'public';
                $('#visID').css('color', '#f3cf58');
                $("#visID").html("Visibility - Private");
                document.getElementById('newVisibilityPrivateBtn').style.visibility = 'hidden';
                document.getElementById('newVisibilityPublicBtn').style.visibility = 'show';

        } else //(visibility=='MyOwnPublic'){
        {
                newVisibility = 'private';
                $('#visID').css('color', '#f3cf58');
                $("#visID").html("Visibility - Public");
                document.getElementById('newVisibilityPrivateBtn').style.visibility = 'show';
                document.getElementById('newVisibilityPublicBtn').style.visibility = 'hidden';
        }
        // To Change from Private to Public
        $(document).on("click", "#newVisibilityPublicBtn", function(event){
                $.ajax({
                url: 'getfiledata.php',
                type: "POST",
                async: true,
                dataType: 'json',
                data:
                    {
                        action: "change_visibility",
                        id: deviceid,
                        contextbroker: contextbroker,
                        visibility: newVisibility
                    },

                success: function(data)
                    {
                        if (data["status"] === 'ok')
                        {
                            $('#newVisibilityResultMsg').show();
                            $("#visID").html("");
                            $('#visID').css('color', '#f3cf58');
                            $("#visID").html("Visibility - Private");
                            $('#newVisibilityResultMsg').html('New visibility set to Public');

                            $('#newVisibilityPublicBtn').addClass('disabled');

                            setTimeout(function()
                                                {
                                                        $('#devicesTable').DataTable().destroy();
                                                        //fetch_data(true);
                                                        location.reload();
                                                }, 3000);
                        }
                        else if (data["status"] === 'ko')
                        {   console.log(data['msg']);
                            $('#newVisibilityResultMsg').show();
                            $('#newVisibilityResultMsg').html('Error setting new visibility');
                            $('#newVisibilityPublicBtn').addClass('disabled');

                            setTimeout(function()
                                                {
                                                        $('#newVisibilityPublicBtn').removeClass('disabled');
                                                        $('#newVisibilityResultMsg').html('');
                                                        $('#newVisibilityResultMsg').hide();
                                                }, 3000);
                        }
                        else {console.log(data);}
                    },
                error: function(errorData)
                                {
                                        $('#newVisibilityResultMsg').show();
                                        $('#newVisibilityResultMsg').html('Error setting new visibility');
                                        $('#newVisibilityPublicBtn').addClass('disabled');

                                        setTimeout(function()
                                        {
                                                $('#newVisibilityPublicBtn').removeClass('disabled');
                                                $('#newVisibilityResultMsg').html('');
                                                $('#newVisibilityResultMsg').hide();
                                        }, 3000);
                                }
                        });
                });


        // To Change from Public to Private
        $(document).on("click", "#newVisibilityPrivateBtn", function(event){
                $.ajax({
                        url: 'getfiledata.php',
                        data:
                            {
                                action: "change_visibility",
                                id: deviceid,
                                contextbroker: contextbroker,
                                visibility: newVisibility
                            },
                        type: "POST",
                        async: true,
                        dataType: 'json',
                        success: function(data)
                                {
                                if (data["status"] === 'ok')
                                    {
                                                $('#newVisibilityResultMsg').show();
                                                $('#newVisibilityResultMsg').html('New visibility set Private');
                                                //$('#newVisibilityPrivateBtn').addClass('disabled');
                                                //document.getElementById('newVisibilityPrivateBtn').style.visibility = 'hidden';
                                                $('#newVisibilityPrivateBtn').addClass('disabled');
                                                //document.getElementById('CurrentVisiblityTxt').value = "Current Visiblity: " + newVisibility;
                                                //document.getElementById('newVisibilityPublicBtn').style.visibility = 'show';
                                                setTimeout(function()
                                                {
                                                        $('#devicesTable').DataTable().destroy();
                                                        //fetch_data(true);
                                                        location.reload();
                                                }, 3000);
                                        }
                                        else if (data["status"] === 'ko')
                                        {
                                                $('#newVisibilityResultMsg').show();
                                                $('#newVisibilityResultMsg').html('Error setting new visibility');
                                                $('#newVisibilityPrivateBtn').addClass('disabled');

                                                setTimeout(function()
                                                {
                                                        $('#newVisibilityPrivateBtn').removeClass('disabled');
                                                        $('#newVisibilityResultMsg').html('');
                                                        $('#newVisibilityResultMsg').hide();
                                                }, 3000);
                                        }
                                        else {console.log(data);}
                                },
                        error: function(errorData)
                                {
                                        $('#newVisibilityResultMsg').show();
                                        $('#newVisibilityResultMsg').html('Error setting new visibility');
                                        $('#newVisibilityPrivateBtn').addClass('disabled');

                                        setTimeout(function()
                                        {
                                                $('#newVisibilityPrivateBtn').removeClass('disabled');
                                                $('#newVisibilityResultMsg').html('');
                                                $('#newVisibilityResultMsg').hide();
                                        }, 3000);
                                }
                        });
                });


        $(document).on("click", "#newOwnershipConfirmBtn", function(event){
                        // I generate a new pair of keys for the new owner
                var k1new = generateUUID();
                var k2new = generateUUID();
                $.ajax({
                                 url: 'getfiledata.php',
                                 data:{
                                         action: "change_owner",
                                         id: deviceid,
                                         contextbroker: contextbroker,
                                         newOwner:  $('#newOwner').val(),
                                         k1: k1new,
                                         k2: k2new
                         },
                        type: "POST",
                        async: true,
                        dataType: 'json',
                        success: function(data)
                        {
                                if (data["status"] === 'ok')
                                {
                                        $('#newOwner').val('');
                                        $('#newOwner').addClass('disabled');
                                        $('#newOwnershipResultMsg').show();
                                        $('#newOwnershipResultMsg').html('New ownership set correctly');
                                        $('#newOwnershipConfirmBtn').addClass('disabled');


                                        setTimeout(function()
                                        {
                                                $('#devicesTable').DataTable().destroy();
                                                //fetch_data(true);
                                                location.reload();
                                        }, 3000);
                                }
                                else if (data["status"] === 'ko')
                                {
                                        $('#newOwner').addClass('disabled');
                                        $('#newOwnershipResultMsg').html('Error setting new ownership: please try again');
                                        $('#newOwnershipConfirmBtn').addClass('disabled');

                                        setTimeout(function()
                                        {
                                                $('#newOwner').removeClass('disabled');
                                                $('#newOwnershipResultMsg').html('');
                                                $('#newOwnershipResultMsg').hide();
                                        }, 3000);
                                }
                                else {console.log(data);}
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
                                }, 3000);
                        }
                });
        });



        $("#delegationsCancelBtn").off("click");
        $("#delegationsCancelBtn").on('click', function(){
                $('#newDelegation').val("");
                $('#newDelegationGroup').val("");
                $('#newDelegationOrganization').val("");
                $('#newOwner').val("");
                  $("#newVisibilityResultMsg").html("");
                  $("#newOwnershipResultMsg").html("");
                   location.reload();
                  $('#delegationsModal').modal('hide');
        });



       //populate the beginning of the tables and listen about the removal
       $.ajax({

           url: 'getfiledata.php',   //Checking the delegation table
           data:
           {
               action: "get_delegations",  // check the action and to be specified
               id: deviceid,
               contextbroker: contextbroker
           },
           type: "POST",
           async: true,
           dataType: 'json',
           success: function(data)
           {
                    if (data["status"]==='ok')
                    {

                        delegations = data['message']["delegation"];
                        $('#delegationsTable tbody').html("");
                        $('#delegationsTableGroup tbody').html("");
                        for(var i = 0; i < delegations.length; i++)
                        {
                            if ((delegations[i].userDelegated !=="ANONYMOUS")&&(delegations[i].userDelegated!==null)) {
                                              $('#delegationsTable tbody').append('<tr class="delegationTableRow" data-delegationId="' + delegations[i].delegationId + '" data-delegated="' + delegations[i].userDelegated + '"><td class="delegatedName">' + delegations[i].userDelegated + '</td><td><i class="fa fa-remove removeDelegationBtn"></i></td></tr>');

                            }
                            else  if (delegations[i].groupDelegated !==null){

                                                                               //extract cn and ou
                                    var startindex=delegations[i].groupDelegated.indexOf("cn=");
                                    if (startindex===-1)
                                    {

                                        gr="All groups";
                                        var endindex_ou=delegations[i].groupDelegated.indexOf(",");
                                        var ou=delegations[i].groupDelegated.substring(3, endindex_ou);
                                    }
                                    else{
                                        var endindex_gr= delegations[i].groupDelegated.indexOf(",");
                                        var gr=delegations[i].groupDelegated.substring(3, endindex_gr);
                                        var endindex_ou=delegations[i].groupDelegated.indexOf(",", endindex_gr+1);
                                        var ou=delegations[i].groupDelegated.substring(endindex_gr+4, endindex_ou);
                                    }

                                    var DN=ou+","+gr;

                                    $('#delegationsTableGroup tbody').append('<tr class="delegationTableRowGroup" data-delegationId="' + delegations[i].delegationId + '" data-delegated="' + ou + "," +gr+ '"><td class="delegatedName">' + DN + '</td><td><i class="fa fa-remove removeDelegationBtnGroup"></i></td></tr>');
                            }

                        }
                        $('#delegationsTable tbody').on("click","i.removeDelegationBtn",function(){
                                                    var rowToRemove = $(this).parents('tr');
                                                    $.ajax({
                                                        url: 'getfiledata.php',
                                                        data:
                                                        {
                                                                action: "remove_delegation",
                                                                delegationId: $(this).parents('tr').attr('data-delegationId'),
                                                                userDelegated: $(this).parents('tr').attr('data-delegated'),
                                                                id: deviceid,
                                                                contextbroker: contextbroker
                                                        },
                                                        type: "POST",
                                                        async: true,
                                                        dataType: 'json',
                                                        success: function(data)
                                                        {
                                                           if (data["status"] === 'ok')
                                                           {    console.log(data['message']);
                                                                rowToRemove.remove();
                                                                //console.log("ermoving a row from the table");
                                                           }
                                                            else
                                                            {console.log(data['message']);
                                                                //TBD insert a message of error
                                                            }
                                                        },
                                                        error: function(errorData)
                                                        {console.log(data['message']);
                                                           //TBD  insert a message of error
                                                        }
                                                    });
                                                });

                        $('#delegationsTableGroup tbody').on("click","i.removeDelegationBtnGroup",function(){
                                                   var rowToRemove = $(this).parents('tr');
                                                   $.ajax({
                                                            url: 'getfiledata.php',
                                                            data:
                                                                 {
                                                                  action: "remove_delegation",
                                                                  delegationId: $(this).parents('tr').attr('data-delegationId'),
                                                                  groupDelegated: $(this).parents('tr').attr('data-delegated'),
                                                                  id: deviceid,
                                                                  contextbroker: contextbroker
                                                            },
                                                            type: "POST",
                                                            async: true,
                                                            dataType: 'json',
                                                            success: function(data)
                                                                     {
                                                                        if (data["status"] === 'ok')
                                                                        {console.log(data['message']);
                                                                            rowToRemove.remove();
                                                                        }
                                                                        else
                                                                        {console.log(data['message']);
                                                                             //TBD insert a message of error
                                                                        }
                                                                      },
                                                            error: function(errorData)
                                                                     {console.log(data['message']);
                                                                             //TBD  insert a message of error
                                                                      }
                                                            });
                        });




                                            }
                                            else
                                            {
                                              // hangling situation of error
                                                console.log(json_encode(data));

                                            }

                                            },
                                            error: function(errorData)
                                           {
                                               //TBD  insert a message of error
                                            }
                                        });



       //listen about the confimation
       $(document).on("click", "#newDelegationConfirmBtn", function(event){
               var newDelegation = document.getElementById('newDelegation').value;
                var newk1 = generateUUID();
                var newk2 = generateUUID();
                $.ajax({
                                                        url: 'getfiledata.php',       //which api to use
                                                        data:
                                                        {
                                                            action: "add_delegation",
                                                                contextbroker: contextbroker,
                                                            id:deviceid,
                                                            delegated_user: newDelegation,
                                                                k1: newk1,
                                                            k2: newk2
                                                        },
                                                        type: "POST",
                                                        async: true,
                                                        dataType: 'json',
                                                        success: function(data)
                                                        {
                                                                if (data["status"] === 'ok')
                                                               {        console.log(data['message']);
                                                                        $('#delegationsTable tbody').append('<tr class="delegationTableRow" data-delegationId="' + data["delegationId"] + '" data-delegated="' + $('#newDelegation').val() + '"><td class="delegatedName">' + $('#newDelegation').val() + '</td><td><i class="fa fa-remove removeDelegationBtn"></i></td></tr>');


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
                                                                {   console.log(data['message']);
                                                                        var errorMsg = null;


                                                                        $('#newDelegation').val('');
                                                                        $('#newDelegation').addClass('disabled');
                                                                        $('#newDelegatedMsg').css('color', '#f3cf58');
                                                                        $('#newDelegatedMsg').html(data["msg"]);
                                                                        $('#newDelegationConfirmBtn').addClass('disabled');

                                                                        setTimeout(function()
                                                                        {
                                                                                $('#newDelegation').removeClass('disabled');
                                                                                $('#newDelegatedMsg').css('color', '#f3cf58');
                                                                                $('#newDelegatedMsg').html('Delegated username can\'t be empty');
                                                                        }, 3000);
                                                                }
                                                        },
                                                        error: function(errorData)
                                                        {       console.log(data['message']);
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
                                                                }, 3000);
                                                        }
               });

       });//single delegation -end

       //group delegation -start------------------------------------------------------------------------------------------------------------
        $(document).on("click", "#newDelegationConfirmBtnGroup", function(event){
               var delegatedDN="";
               var e = document.getElementById("newDelegationGroup");
               if ((typeof e.options[e.selectedIndex] !== 'undefined')&&(e.options[e.selectedIndex].text!=='All groups')){
                       delegatedDN = "cn="+e.options[e.selectedIndex].text+",";
               }
                var e2 = document.getElementById("newDelegationOrganization");
               delegatedDN=delegatedDN+"ou="+e2.options[e2.selectedIndex].text;

                var newk1 = generateUUID();
                var newk2 = generateUUID();
                $.ajax({
                       url: 'getfiledata.php',
                                                                                               data:
                                                                                               {
                                                                                                       action: "add_delegation",
                                                                                                       contextbroker: contextbroker,
                                                                                                       id:deviceid,
                                                                                                       delegated_group: delegatedDN,
                                                                                                       k1: newk1,
                                                                                                       k2: newk2
                                                                                               },
                                                                                               type: "POST",
                                                                                               async: true,
                                                                                               dataType: 'json',
                                                                                               success: function(data)
                                                                                               {
                                                                                                       if (data["status"] === 'ok')
                                                                                                       {
                                                                                                               var toadd= $('#newDelegationOrganization').val();
                                                                                                               if ( document.getElementById("newDelegationGroup").options[e.selectedIndex].text!==''){
                                                                                                                       toadd=toadd+","+$('#newDelegationGroup').val();
                                                                                                               }

                                                                                                               $('#delegationsTableGroup tbody').append('<tr class="delegationTableRowGroup" data-delegationId="' + data["delegationId"] + '" data-delegated="' + toadd+ '"><td class="delegatedNameGroup">' +toadd + '</td><td><i class="fa fa-remove removeDelegationBtnGroup"></i></td></tr>');
                                                                                                               $('#newDelegatedMsgGroup').css('color', 'white');
                                                                                                               $('#newDelegatedMsgGroup').html('New delegation added correctly');

                                                                                                               setTimeout(function()
                                                                                                               {
                                                                                                                       $('#newDelegatedMsgGroup').css('color', '#f3cf58');
                                                                                                                       $('#newDelegatedMsgGroup').html('Delegated groupname can\'t be empty');
                                                                                                               }, 1500);
                                                                                                       }
                                                                                                       else
                                                                                                       {
                                                                                                               var errorMsg = null;
                                                                                                               $('#newDelegatedMsgGroup').css('color', '#f3cf58');
                                                                                                               $('#newDelegatedMsgGroup').html(data["msg"]);

                                                                                                               setTimeout(function()
                                                                                                               {
                                                                                                                       $('#newDelegationGroup').removeClass('disabled');
                                                                                                                       $('#newDelegationOrganization').removeClass('disabled');
                                                                                                                       $('#newDelegatedMsgGroup').css('color', '#f3cf58');
                                                                                                                       $('#newDelegatedMsgGroup').html('Delegated groupname can\'t be empty');
                                                                                                               }, 2000);
                                                                                                       }
                                                                                               },
                                                                                               error: function(errorData)
                                                                                               {
                                                                                                       var errorMsg = "Error calling internal API";
                                                                                                       $('#newDelegatedMsgGroup').css('color', '#f3cf58');
                                                                                                       $('#newDelegatedMsgGroup').html(errorMsg);

                                                                                                       setTimeout(function()
                                                                                                       {
                                                                                                               $('#newDelegatedMsgGroup').css('color', '#f3cf58');
                                                                                                               $('#newDelegatedMsgGroup').html('Delegated groupname can\'t be empty');
                                                                                                       }, 2000);
                                                                                               }
               });
       });     //group delegation -end

        }

        //Validation of the name of the new owner during typing
    $('#newOwner').on('input', function (e)
    {

        if ($(this).val().trim() === '')
        {
            $('#newOwnerMsg').css('color', '#f3cf58');
            $('#newOwnerMsg').html('New owner username can\'t be empty');
            $('#newOwnershipConfirmBtn').addClass('disabled');
        } else
        {

            if (($(this).val().trim() === loggedUser))

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

    // DELEGATIONS
    function updateGroupList(ouname) {
        $.ajax({
            url: "getfiledata.php",
            dataType: "json",
            data: {
                action: "get_group_for_ou",
                ou: ouname
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
                    var $dropdown = $("#newDelegationGroup");
                    //remove old ones
                    $dropdown.empty();
                    //adding empty to rootadmin
                    if ((loggedRole == 'RootAdmin') || (loggedRole == 'ToolAdmin')) {
                        //console.log("adding empty");
                        $dropdown.append($("<option />").val("All groups").text("All groups"));
                    }
                    //add new ones
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

    //populate organization list with any possibile value (if rootAdmin)
    if ((loggedRole == 'RootAdmin') || (loggedRole == 'ToolAdmin')) {
        $.ajax({
            url: "getfiledata.php",
            dataType: "json",
            data: {
                action: "get_all_ou"
            },
            type: "POST",
            async: false,
            datatype: 'json',
            success: function (data)
            {
                if (data["status"] === 'ko')
                {
                    $('#newDelegatedMsgGroup').css('color', '#f3cf58');
                    $('#newDelegatedMsgGroup').html(data["mesage"]);
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
            url: "getfiledata.php",
            dataType: "json",
            data: {
                action: "get_logged_ou"
            },
            type: "POST",
            async: false,
            success: function (data)
            {
                if (data["status"] === 'ko')
                {
                    console.log("Error: " + data);
                    alert("An error occured when reading the data. <br/> Get in touch with the Snap4City Administrator. <br/>" + data["error_msg"]);
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

    //populate group list with selected organization
    updateGroupList($("#newDelegationOrganization").val());
    //eventually update the group list
    $('#newDelegationOrganization').change(function () {
        $(this).find(":selected").each(function () {
            updateGroupList($(this).val());
        });
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
        }
    });
    $('#valuesTable thead').css("background", "rgba(0, 162, 211, 1)");
    $('#valuesTable thead').css("color", "white");
    $('#valuesTable thead').css("font-size", "1em");
    $('#valuesTable tbody tr').each(function (i) {
        if (i % 2 !== 0)
        {
            $(this).find('td').eq(0).css("background-color", "rgb(230, 249, 255)");
            $(this).find('td').eq(0).css("border-top", "none");
        } else
        {
            $(this).find('td').eq(0).css("background-color", "white");
            $(this).find('td').eq(0).css("border-top", "none");
        }
    });
    $('#delegationsModal').on('hidden.bs.modal', function (e)
    {
        $(this).removeData();
    });

// END TO CHANGE THE VISIBILITY

function generateUUID() { // Public Domain/MIT
    var d = new Date().getTime();
    if (typeof performance !== 'undefined' && typeof performance.now === 'function'){
                        d += performance.now(); //use high-precision timer if available
    }
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = (d + Math.random() * 16) % 16 | 0;
        d = Math.floor(d / 16);
        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                });
        }
function get_mimetype(filetype){
    var mimetype = "";
    switch(filetype){
        case 'jpg':
            mimetype = 'image/jpeg';
            break;
        case 'png':
            mimetype = 'image/png';
            break;
        case 'pdf':
            mimetype = 'application/pdf';
            break;
        case 'doc':
            mimetype = 'application/msword';
            break;
        case 'docx':
            mimetype = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            break;
        }
    return mimetype;
}

</script>
</body>
</html>