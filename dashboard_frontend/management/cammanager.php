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
checkSession('RootAdmin');
//$_SESSION['loggedRole'] = "RootAdmin";
/*
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);
error_reporting(E_ERROR);*/

$lastUsedColors = null;

?>
<!DOCTYPE HTML>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cam Manager</title>

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
</style>

<body style="overflow-y: hidden !important">

    <!-- Inizio dei modali -->
    <!-- Modale wizard -->
    <div class="modal-content modalContentWizardForm">
        <!--   <div class="modalHeader centerWithFlex">
              &nbsp;&nbsp;&nbsp;
            </div>  -->

        <div id="addWidgetWizardLabelBody" class="body">
            <?php
            //include "addWidgetWizardInclusionCode2.php";
            ?>
            <!-- -->
            <div id="select_element_type" style="margin-left: 5%; margin: 2%;  float: left">
                <!--<div id="buotton_files" style="width: 75%; padding-bottom:50px;">-->
                <!-- -->
                <button type="button" id="new_cam_modal" class="btn btn-warning new_org" data-toggle="modal" data-target="#myModal_new" style="float:left; margin-right: 5px;">
                    <i class="fa fa-plus"></i> 
                    Add new Cam
                </button>
                <!-- 	<div style="display:none;"> -->

                <!--</div>-->
            </div>
            <!-- -->
            <div id="table_div" style="margin-left: 5%; margin-right: 5%">
                <!-- -->
                <table id="value_table" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="dashboardsTableHeader">
                    <th>Name</th>
                    <th>Description</th>
                    <th>Subnature</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>VideoSource</th>
                    <th>Username</th>
                    <th>ServiceURI</th>
                    <th>Organization</th>
                    <th>Contextbroker</th>
                    <th>Model</th>
                    <th>Controls</th>
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
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("New Cam") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="input-group"><span class="input-group-addon"><?= _("Name") ?>:</span><input id="new_name" type="text" class="form-control"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Description") ?>:</span><input id="new_description" type="text" class="form-control" ></div><br />
                     <!--<div class="input-group"><span class="input-group-addon"><?= _("Nature") ?>:</span>
                       <select id="new_nature" name="select_mode" class="form-control">
                        </select>
                    </div><br />-->
                    <!--<div class="input-group"><span class="input-group-addon"><?= _("Subnature") ?>:</span>
                        <select id="new_subnature" name="select_mode" class="form-control">
                        </select>
                    </div><br />-->
                    <div class="input-group"><span class="input-group-addon"><?= _("Model") ?>:</span>
                        <select id="new_model" name="select_mode" class="form-control">
                        </select>
                    </div><br />
                    <div class="input-group">
                    <span class="input-group-addon"><?= _("Set coordinates") ?>:</span>
                    <div id="Map1" style="height:200px">
                    </div></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Latitude") ?>:</span><input id="new_latitude" type="text" class="form-control" oninput="new_coords()" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Longitude") ?>:</span><input id="new_longitude" type="text" class="form-control" oninput="new_coords()"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("VideoSource") ?>:</span><input id="new_videosource" type="text" class="form-control"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Username") ?>:</span><input id="new_username" type="text" class="form-control"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Password") ?>:</span><input id="new_password" type="text" placeholder="" autocomplete="off" class="form-control pw"></div><br />
                    <!--<div class="input-group"><span class="input-group-addon"><?= _("Organizations") ?>:</span>
                        <select id="new_org" name="select_mode" class="form-control">
                        </select>
                    </div>-->
                </div><br />                    
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_new"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
    <div class="modal fade" id="edit_cam" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("Edit Cam") ?></h5>
                </div>
                <div class="modal-body">
                    <input id="edit_id" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="edit_broker" type="text" class="form-control" readonly style="display:none;"/>
                    <div class="input-group"><span class="input-group-addon"><?= _("Name") ?>:</span><input id="edit_name" type="text" class="form-control" readonly></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Description") ?>:</span><input id="edit_description" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Model") ?>:</span>
                        <select id="edit_model" name="select_mode" class="form-control">
                        </select>
                    </div><br />
                   <!-- <div class="input-group"><span class="input-group-addon"><?= _("Nature") ?>:</span>
                        <select id="edit_nature" name="select_mode" class="form-control">
                        </select>
                    </div><br />-->
                    <div class="input-group"><span class="input-group-addon"><?= _("Subnature") ?>:</span>
                        <!--<select id="edit_subnature" name="select_mode" class="form-control">
                        </select>-->
                        <input id="edit_subnature" type="text" class="form-control" readonly>
                    </div><br />
                    <div class="input-group">
                        <span class="input-group-addon"><?= _("Set coordinates") ?>:</span>
                        <div id="Map2" style="height:200px"></div></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Latitude") ?>:</span><input id="edit_latitude" type="text" class="form-control" oninput="edit_coords()" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Longitude") ?>:</span><input id="edit_longitude" type="text" class="form-control" oninput="edit_coords()"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("VideoSource") ?>:</span><input id="edit_videosource" type="text" class="form-control"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Username") ?>:</span><input id="edit_username" type="text" class="form-control"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Password") ?>:</span><input id="edit_password" type="text" placeholder="" autocomplete="off" class="form-control pw"></div><br />
                    <!-- <div class="input-group"><span class="input-group-addon"><?= _("Organizations") ?>:</span>
                       <select id="edit_org" name="select_mode" class="form-control">
                        </select>
                    </div><br />-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_edit"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->
    <div class="modal fade" id="delete_cam" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("Delete Cam") ?></h5>
                </div>
                <div class="modal-body">
                    <input id="delete_id" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="delete_name" type="text" class="form-control" readonly style="display:none;"/>
                    <input id="delete_broker" type="text" class="form-control" readonly style="display:none;"/>
                    <?= _("Are you sure do you want to delete this cam- from database?") ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_del"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
        <!--  -->
    <div class="modal fade" id="delegate_cam" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("Delegate User") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="row centerWithFlex modalFirstLbl" id="delegationsNotAvailableRow">
                                            </div>
                                            <div class="row" id="delegationsFormRow">
                                                <div class="col-xs-12" id="newDelegationCnt">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="delegationsDashId" readonly style="display:none;">
                                                        <input type="text" class="form-control" id="delegationsDashOrg" readonly style="display:none;">
                                                        <input type="text" class="form-control" id="delegationsDashCb" readonly style="display:none;">
                                                    </div>
                                                    <div class="input-group"><span class="input-group-addon"><?= _("Add new delegation")?>:</span><input type="text" class="form-control" id="newDelegation" placeholder="Delegated username"></div><br />
                                                    <div class="col-xs-12 centerWithFlex delegationsModalMsg" id="newDelegatedMsg">
                                                        <?= _("Delegated username can't be empty")?>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 centerWithFlex" id="currentDelegationsLbl">
                                                    <?= _("Current user delegations")?>
                                                </div>
                                            </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                   <button type="button" id="newDelegationConfirmBtn" class="btn btn-primary"><?= _("Confirm")?></button>
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
        
        $.ajax({
            async: true,
            type: 'GET',
            dataType: 'json',
            url: 'getcamdata.php',
            data: {
                action: 'get_cam'
            },
            success: function (data) {
                //var json_data = JSON.parse(data);

                if (data['code'] === '200'){
                    var data = data['data'];
                var len = data.length;
                //
                //

                var link_rstp = '<?php echo _($rstp_link); ?>';
                for (var i = 0; i < len; i++) {
                    var nature = '';
                    var subnature = '';
                    var organizations = '';
                    var model = '';
                    var id = data[i]['id'];
                    var name = data[i]['name'];
                    var description = data[i]['description'];
                    if ((data[i]['nature'] === null) || (data[i]['nature'] === 'null') || (data[i]['nature'] === undefined)) {
                        nature = '';
                    } else {
                        nature = data[i]['nature'];
                    }
                    if ((data[i]['subnature'] === null) || (data[i]['subnature'] === 'null') || (data[i]['subnature'] === undefined)) {
                        subnature = '';
                    } else {
                        subnature = data[i]['subnature'];
                    }
                    var latitude = data[i]['latitude'];
                    var longitude = data[i]['longitude'];
                    var videosource = data[i]['videosource'];
                    var username = data[i]['username'];
                    var password = data[i]['password'];
                    var serviceURI = data[i]['serviceURI'];
                    organizations = data[i]['organizations'];
                    var broker = data[i]['contextbroker'];
                    var model = data[i]['model'];
                    //
                    var view_button='<a href="'+link_rstp+'/?src='+serviceURI+'" target="_blank"><button type="button" class="viewDashBtn">VIEW</button></a>';
                    var delegate_button='<button type="button" class="editDashBtn" onclick="func_delegation(\'' + name + '\',\'' + broker + '\',\'' + organizations + '\')" data-target="#delegate_cam" data-toggle="modal">DELEGATE</button>';
                    
                    $('#value_table tbody').append('<tr><td>' + name + '</td><td>' + description + '</td><td>' + subnature + '</td><td>' + latitude + '</td><td>' + longitude + '</td><td><a href="' + videosource + '" target="_blank">' + videosource + '</a></td><td>' + username + '</td><td><a href="' + serviceURI + '" target="_blank">' + serviceURI + '</a></td><td>' + organizations + '</td><td>'+broker+'</td><td>'+model+'</td><td>'+view_button+' <button type="button" class="editDashBtn edit_file" data-target="#edit_cam" data-toggle="modal" onclick="func_edit(\'' + id + '\', \'' + name + '\', \'' + description + '\', \'' + nature + '\',  \'' + subnature + '\', \'' + latitude + '\', \'' + longitude + '\', \'' + videosource + '\', \'' + username + '\', \'' + password + '\', \'' + serviceURI + '\', \'' + organizations + '\', \'' + model + '\', \'' + broker + '\')" value="' + id + '">EDIT</button>  '+delegate_button+'  <button type="button" class="delDashBtn delete_file" data-target="#delete_cam" data-toggle="modal" onclick="func_del(' + id + ', \'' + name + '\',\'' + broker + '\')">DELETE</button></td></tr>');
                }

                }else{
                    console.log(data['message']);
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
                        "lengthMenu": "Show	_MENU_ "
                    }
                });
              }
        });
        //API LIST
        $('#button_conf_new').click(function () {
            $('#myModal_new').modal('hide');
        });
        //
        $('#new_cam_modal').click(function () {
            $.ajax({
                async: true,
                type: 'GET',
                dataType: 'json',
                url: 'getcamdata.php',
                data: {
                    action: 'get_models'
                },
                success: function (data) {
                    $('#new_org').empty();
                    $('#new_nature').empty();
                    $('#new_subnature').empty();
                    $('#new_model').empty();
                    var lun = data.length;

                    for (var i = 0; i < lun; i++) {
                        $('#new_model').append('<option value="' + data[i] + '">' + data[i] + '</option>');
                    }
                   
                }
            });
        });
        //
        $('#conf_new').click(function () {
            var new_name = $('#new_name').val();
            var new_description = $('#new_description').val();
            var new_latitude = $('#new_latitude').val();
            var new_longitude = $('#new_longitude').val();
            var new_videosource = $('#new_videosource').val();
            var new_username = $('#new_username').val();
            var new_password = $('#new_password').val();
            //var new_org = $('#new_org').val();
            var model = $('#new_model').val();
            //
            $('#loading_div').modal('show');
            //
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                url: 'getcamdata.php',
                data: {
                    action: 'new_cam',
                    name: new_name,
                    description: new_description,
                    latitude: new_latitude,
                    longitude: new_longitude,
                    videosource: new_videosource,
                    username: new_username,
                    password: new_password,
                    model: model
                    
                },
                success: function (data) {
                    $('#loading_div').modal('hide');
                    window.location.reload();
                }
            });
        });
        //
        $('#conf_edit').click(function () {
            var id = $('#edit_id').val();
            var name = $('#edit_name').val();
            var new_description = $('#edit_description').val();
            var new_latitude = $('#edit_latitude').val();
            var new_longitude = $('#edit_longitude').val();
            var new_videosource = $('#edit_videosource').val();
            var new_username = $('#edit_username').val();
            var new_password = $('#edit_password').val();
            var serviceURI = $('#edit_serviceURI').val();
            //
            $('#loading_div').modal('show');

            var model = $('#edit_model').val();
            var edit_broker = $('#edit_broker').val();
            //
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                url: 'getcamdata.php',
                data: {
                    action: 'edit_cam',
                    id: id,
                    name: name,
                    description: new_description,
                    latitude: new_latitude,
                    longitude: new_longitude,
                    videosource: new_videosource,
                    username: new_username,
                    password: new_password,
                    serviceURI: serviceURI,
                    model: model,

                    broker: edit_broker
  
                },
                success: function (data) {
                    $('#loading_div').modal('hide');
                    window.location.reload();
                }
            });
        });

        $('#conf_del').click(function () {
            var id = $('#delete_id').val();
            var name = $('#delete_name').val();
            var broker = $('#delete_broker').val();
            $('#loading_div').modal('show');
            //
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                url: 'getcamdata.php',
                data: {
                    action: 'delete_cam',
                    id: id,
                    name: name,
                    broker: broker
                },
                success: function (data) {
                    $('#loading_div').modal('hide');
                    window.location.reload();
                }
            });
        });
        //////////////////
        $('#newDelegationConfirmBtn').click(function () {
            var newDelegation = $('#newDelegation').val();
            var delegationsDashId = $('#delegationsDashId').val();
            var delegationsDashOrg = $('#delegationsDashOrg').val();
            var delegationsDashCb = $('#delegationsDashCb').val();
            $('#loading_div').modal('show');
            /////////////////////////////
            //
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                url: 'getcamdata.php',
                data: {
                    action: 'delegate_user',
                    id: delegationsDashId,
                    cb: delegationsDashOrg,
                    org: delegationsDashCb,
                    usernameDelegated: newDelegation,
                    usernameDelegator: ''
                    
                },
                success: function (data) {
                    $('#loading_div').modal('hide');
                    window.location.reload();
                }
            });
         
            ////////////////////////////
        });
        ////////////
        mymap2.on('click',
                function (e) {
                    var zoom = mymap2.getZoom();
                    //
                    $('#edit_latitude').val(e.latlng.lat);
                    $('#edit_longitude').val(e.latlng.lng);
                    //
                    var greyIcon = new L.Icon({
                        iconUrl: '../img/outputPngIcons/pin-generico.png',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32]
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
                        iconUrl: '../img/outputPngIcons/pin-generico.png',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32]
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

    function func_del(id, name, broker) {
        //var value = this.attr('value');
        $('#delete_id').val(id);
        $('#delete_name').val(name);
        $('#delete_broker').val(broker);
    }
    ;
    
    function func_delegation (name, org, cb){
        $('#delegationsDashId').val(name);
        $('#delegationsDashOrg').val(org);
        $('#delegationsDashCb').val(cb);
    }
    
    function edit_coords (){
        ///////////////////////////////////
        //var zoom = mymap2.getZoom();
                    //
                    var lat =$('#edit_latitude').val();
                    var lng = $('#edit_longitude').val();
                    //
                    var greyIcon = new L.Icon({
                        iconUrl: '../img/outputPngIcons/pin-generico.png',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32]
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
                        iconUrl: '../img/outputPngIcons/pin-generico.png',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32]
                        //iconAnchor: [e.latlng.lat, e.latlng.lng]
                    });
                    //mymap.removeLayer();
                    $(".leaflet-marker-icon").remove();
                    mymap.setView([lat, lng], 9);
                    var marker = L.marker([lat, lng], {icon: greyIcon}).addTo(mymap);
                    setTimeout(mymap.invalidateSize.bind(mymap));

        ////////
    }

    function func_edit(id, name, description, nature, subnature, latitude, longitude, videosource, username, password, serviceURI, org, model, broker) {

        $.ajax({
            async: true,
            type: 'GET',
            dataType: 'json',
            url: 'getcamdata.php',
            data: {
                 action: 'get_models'
            },
            success: function (data) {
 
                var orgs = data['organizations'];
                var natu = data['nature'];
                var subn = data['subnature'];
                //var lun = orgs.length;
        /////
        $(".leaflet-marker-icon").remove();
            mymap2.setView([latitude, longitude], 10);
            var greyIcon = new L.Icon({
                iconUrl: '../img/outputPngIcons/pin-generico.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
                //iconAnchor: [latitude, longitude]
            });
            var marker = L.marker([latitude, longitude], {icon: greyIcon}).addTo(mymap2);
                //
                $('#edit_model').empty();
                    var lun = data.length;
                    for (var i = 0; i < lun; i++) {
                        $('#edit_model').append('<option value="' + data[i] + '">' + data[i] + '</option>');
                    }
                $('#edit_name').val(name);
                $('#edit_description').val(description);
                $('#edit_nature').val(nature);
                $('#edit_subnature').val(subnature);
                $('#edit_latitude').val(latitude);
                $('#edit_longitude').val(longitude);
                $('#edit_videosource').val(videosource);
                $('#edit_username').val(username);
                $('#edit_password').val(password);
                $('#edit_id').val(id);
                $('#edit_serviceURI').val(serviceURI);
                $('#edit_org').val(org).prop('selected', true);
                $('#edit_broker').val(broker);
            }
        });

    }
    ;
    
    $("#myModal_new").on('shown.bs.modal', function (e) {
        setTimeout(function () {
            mymap.invalidateSize();
        }, 0);
    });

    $("#edit_cam").on('shown.bs.modal', function (e) {
        setTimeout(function () {
            mymap2.invalidateSize();
        }, 0);
    });
</script>
</body>
</html>