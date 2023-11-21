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

  include '../config.php';
  session_start();
  require '../sso/autoload.php';
  
  use Jumbojett\OpenIDConnectClient;
  
  header('Access-Control-Allow-Origin: *');
  error_reporting(E_ERROR);
  //checkSession('RootAdmin');

  //
?>


<!DOCTYPE HTML>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ttt Manager</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" href="../css/style_widgets.css?v=<?php echo time(); ?>" type="text/css" />
    <link href="../css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/chat.css" type="text/css" />

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
    <script src="../js/jqueryUi/jquery-ui.js"></script>

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

.form-check-label {
    margin: 0.5em;
    margin-right: 1.1em;
}

#container {
            width: auto; /* Imposta la larghezza del container al 100% del genitore */
            max-width: 100%;
        }




</style>
<script src="../datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
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
                <button type="button" id="new_ttt_modal" class="btn btn-warning new_org" data-toggle="modal" data-target="#new_ttt" style="float:left; margin-right: 5px;">
                    <i class="fa fa-plus"></i> 
                    Add new Typical time trend
                </button>
                <!-- 	<div style="display:none;"> -->

                <!--</div>-->
            </div>
            <!-- -->
            <div id="table_div" style="margin-left: 5%; margin-right: 5%">
                <!-- -->
                <table id="value_table" class="table table-striped table-bordered display responsive no-wrap dataTable no-footer dtr-inline" style="width: 100%">
                    <thead class="dashboardsTableHeader">
                    <th>Name</th>               
                    <th>Value Type</th>
                    <th>Value Unit</th>
                    <th>Data Type</th>
                    <th>Max Value</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Kind</th>
                    <th>Kind Details</th>
                    <th>View Values</th>
                    <th>Delete</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- -->
    <!-- Modal -->
    <div class="modal fade" id="new_ttt" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("New Typical Time Trend") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="input-group"><span class="input-group-addon"><?= _("Name") ?>:</span><input id="new_name" type="text" class="form-control"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Value Type") ?>:</span><select  id="value_type" class="form-control"></select></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Value Unit") ?>:</span><select  id="value_unit" class="form-control"></select></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Data Type") ?>:</span><select  id="data_type" class="form-control"></select></div><br />             
                    <div class="input-group"><span class="input-group-addon"><?= _("From Date") ?>:</span><input id="from_date" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("To Date") ?>:</span><input id="to_date" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Reference To") ?>:</span><input id="referenceTo" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Kind") ?>:</span><select class="form-control" id="kind">
                        <option value="day">Day</option>
                        <option value="week">Week</option>
                        <option value="holydays">Holydays</option>
                    </select></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("KindDetails") ?>:</span><select class="form-control" id="kindDetails">
                        <option value="Monday" class="day">Monday</option>
                        <option value="Tuesday" class="day">Tuesday</option>
                        <option value="Wednesday" class="day">Wednesday</option>
                        <option value="Thursday" class="day">Thursday</option>
                        <option value="Friday" class="day">Friday</option>
                        <option value="Saturnday" class="day">Saturnday</option>
                        <option value="Sunday" class="day">Sunday</option>
                        <option value="All Week" class="week" hidden>All Week</option>

                        <option value="Holyday" class="fh" hidden>Holyday</option>
                        <option value="Pre-holyday" class="fh" hidden>Pre-Holyday</option>
                        <option value="Workday" class="fh" hidden>Workday</option>
                    </select>
                </div>
                <br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Value List") ?>:</span><textarea class="form-control resizeable-textarea" id="valuelist" rows="4"></textarea></div><br />
                </div><br />                    
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_new"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
    <!-- Fine modal content -->
    <div class="modal fade" id="view-modal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="viewModalLabel"><?= _("View Typical Time Trend") ?></h5>
                </div>
                <div class="modal-body">
                     <div id="container" style="width:560px; height: 300px;">
                    </div>
                </div>
               </div>
        </div>
    </div>
    <!-- -->
    <div class="modal fade" id="list-modal" tabindex="-1" role="dialog" aria-labelledby="listModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="listModalLabel"><?= _("View Typical Time Trend as a list") ?></h5>
                </div>
                <div class="modal-body">
                <div id="table_div" style="margin-left: 5%; margin-right: 5%">
                <!-- -->
                <table id="list_table" class="table table-striped table-bordered display responsive no-wrap dataTable no-footer dtr-inline" style="width: 100%">
                    <thead class="dashboardsTableHeader">
                    <th>Hour</th>               
                    <th>Value</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
               </div>
        </div>
    </div>
    </div>
    <!-- -->
    <!-- Fine modal content -->
    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="deleteModalLabel"><?= _("Delete Typical Time Trend") ?></h5>
                </div>
                <div class="modal-body">
                     <div id="containerDelete">
                     <?= _("Are you sure you want delete this Typical Time Trend?") ?>
                     </div>
                     <input id="delete_name" type="text" class="form-control" style="display:none;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_delete"><?= _("Confirm") ?></button>
                </div>
               </div>
        </div>
    </div>
    <!-- -->
    <!-- Modal -->
    <!-- -->
    <script type='text/javascript'>
    $(document).ready(function () {
        $('#from_date').datetimepicker({
            showTodayButton: true,
            widgetPositioning:{
                horizontal: 'auto',
                vertical: 'bottom'
            }
        });

        $('#to_date').datetimepicker({
            showTodayButton: true,
            widgetPositioning:{
                horizontal: 'auto',
                vertical: 'bottom'
            }
        });

////////////////////LOADING PAGE//////////////
///
var accessToken = '';
var modelCall = '<?= $modelCall ?>';
var uri ='';
var maxResults=200;
var language='en';
var serviceMapUrlForTrendApi = '<?= $serviceMapUrlForTrendApi ?>';
var processloader_uri_filemanager = '<?= $processloader_uri_filemanager ?>';
console.log('processloader_uri_filemanager: '+processloader_uri_filemanager);

if ("<?= $_SESSION['refreshToken'] ?>" != null && "<?= $_SESSION['refreshToken'] ?>" != "") {
$.ajax({
url: "../controllers/getAccessToken.php",
data: {
refresh_token: "<?= $_SESSION['refreshToken'] ?>"
},

type: "GET",
async: false,
dataType: 'json',
success: function (dataSso) {
accessToken = dataSso.accessToken;
}
});
}

var url_devices = serviceMapUrlForTrendApi + '?selection=43.76871;11.25137&categories=&maxResults=100&maxDists=200&format=json&lang=&geometry=&model=TTT-Model&appID=iotapp&accessToken='+accessToken;
$.ajax({
    url: url_devices,
    data: { 

    },
		type: "GET",
		async: true,
		dataType: 'json',
		success: function (data) {
            console.log(data);	
            if (data.hasOwnProperty('Services')){
                        var services = data.Services;
                        var  features= services.features;
                        for (var i=0; i < features.length; i++){ 
                            var properties = services.features[i].properties;
                            var serviceUri = properties.serviceUri;   
                            var id_dev = services.features[i].id_dev;                          
                                //console.log(properties.name);
                                ////
                                //
                                $.ajax({
                                        url: serviceMapUrlForTrendApi + "?serviceUri="+serviceUri+"&format=json&accessToken="+accessToken,
                                        data: { },
                                        type: "GET",
                                        async: false,
                                        dataType: 'json',
                                        success: function (data1) {	
                                            //console.log(data1);
                                            //
                                            var rt = '';
                                            var bind = '';
                                            var valueType ='';
                                            var valueUnit='';
                                            var dataType ='';
                                            var maxValue ='';
                                            var fromDate = '';
                                            var toDate = '';
                                            var days = '';
                                            var values = '';
                                            var kind = '';
                                            var kindDetails = '';
                                            if (data1.hasOwnProperty('realtime')){
                                                    rt = data1.realtime;
                                                    if (rt.hasOwnProperty('results')){
                                                        var results = rt.results.bindings;
                                                            bind = results[0];
                                                            if(bind.valueType != undefined){
                                                            valueType = bind.valueType.value;
                                                            }

                                                            if(bind.valueUnit != undefined){
                                                            valueUnit = bind.valueUnit.value;
                                                            }

                                                            if(bind.dataType != undefined){
                                                            dataType = bind.dataType.value;
                                                            }

                                                            if(bind.maxValue != undefined){
                                                            maxValue = bind.maxValue.value;
                                                            }

                                                            if(bind.fromDate != undefined){
                                                            fromDate = bind.fromDate.value;
                                                            }

                                                            if(bind.toDate != undefined){
                                                            toDate = bind.toDate.value;
                                                            }

                                                            if(bind.daysOfTheWeek != undefined){
                                                            days = bind.daysOfTheWeek.value;
                                                            }

                                                            if(bind.values != undefined){
                                                            values = bind.values.value;
                                                            }

                                                            if(bind.kind != undefined){
                                                                kind = bind.kind.value;
                                                            }

                                                            if(bind.kindDetails != undefined){
                                                                kindDetails = bind.kindDetails.value;
                                                            }
                                                        }
                                                        ///
                                                        $('#value_table tbody').append('<tr><td>'+properties.name+'</td><td>'+valueType+'</td><td>'+valueUnit+'</td><td>'+dataType+'</td><td>'+maxValue+'</td><td>'+fromDate+'</td><td>'+toDate+'</td><td>'+kind+'</td><td>'+kindDetails+'</td><td> <button type="button" class="viewDashBtn viewList" data-target="#view-modal" data-toggle="modal" value="'+values+'">GRAPH</button>   <button type="button" class="editDashBtn editList" data-target="#list-modal" data-toggle="modal" value="'+values+'">LIST</button></td><td><button type="button" class="delDashBtn delete_file" data-target="#delete-modal" data-toggle="modal" onclick="delete_value(\''+properties.name+'\')" value="'+properties.name+'">DELETE</button></td></tr>');
                                                        ////
                                                    }
                                            
                                        }
                                });
                                ////
                        }

                        var table = $('#value_table').DataTable({
                                        "searching": true,
                                        "paging": true,
                                        "ordering": true,
                                        "info": false,
                                        "responsive": true,
                                        "lengthMenu": [5, 10, 20],
                                        "iDisplayLength": 10,
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
                                    ////

                                    $('.editList').on( "click", function() {
                                                var list = $(this).val();
                                                //console.log(list);
                                                $('#list_table tbody').empty();
                                                        list = list.replace('[','');
                                                        list = list.replace(']','');
                                                        var array_list = list.split(',');
                                                        for(var i=0; i < array_list.length; i++){
                                                        array_list[i] = parseFloat(array_list[i]);
                                                        $('#list_table tbody').append('<tr><td>'+i+':00</td><td>'+array_list[i]+'</td></tr>');
                                                        }  
                                            });

                                    $('.delDashBtn').on("click", function() {
                                        var list = $(this).val();
                                        $('#delete_name').val(list);

                                    });
                                    ////////////////WQ
                                    $('.viewDashBtn').on( "click", function() {
           // alert('OPEN');
            // Seleziona l'elemento HTML in cui visualizzare il grafico
            var container = document.getElementById('container');
            var list = $(this).val();
            list = list.replace('[','');
            list = list.replace(']','');
            var array_list = list.split(',');
            for(var i=0; i < array_list.length; i++){
            array_list[i] = parseFloat(array_list[i]);
            }
            console.log(array_list);
            $('#container').highcharts({
                title: {
                    text: null
                },
                exporting: {
                    enabled: false
                },
                legend: {
                    enabled: false 
                },
                chart: {
                    type: 'line'
                },
                yAxis: {
                    title: {
                        text: 'Values'
                    }
                },
                xAxis: {
                    title: {
                        text: 'Hours'
                    }
                },
                series: [{
                    data: array_list
                }]
            });
            /////

        });
                                    /////
            }else{
                console.log('NO DATA');
            }
        }
});

$('#kind').change(function() {
 var current_kind = $('#kind').val();
 if (current_kind == 'day'){
            $('.day').show();
            $('.week').hide();
            $('.fh').hide();
         }
if (current_kind == 'week'){
            $('.day').hide();
            $('.week').show();
            $('.fh').hide();
    }
if (current_kind =='holydays'){
            $('.day').hide();
            $('.week').hide();
            $('.fh').show();
}
});

//////////////////////////////////////////////
        $('#value_type').change(function() {
            
            var id =$('#value_type').val();
            $.ajax({
                            url: processloader_uri_filemanager+"/?id="+id,
							data: { },
							type: "GET",
							async: true,
							dataType: 'json',
							success: function (data) {	
                                      $('#data_type').empty();
                                      $('#value_unit').empty();
                                    var content = data.content;
                                    for (var i =0; i < content.length; i++){
                                        var value_data_type_id = content[i].data_type_id;
                                        var value_children_id = content[i].children_id;
                                        for (var y =0; y < value_data_type_id.length; y++){
                                            $('#data_type').append('<option value="'+content[i].data_type_value[y]+'">'+content[i].data_type_value[y]+'</option>');
                                            //$('#value_unit').append('<option value="'+value_data_type_id[y]+'">'+content[i].data_type_value[y]+'</option>');
                                        }
                                        for (var z =0; z < value_children_id.length; z++){
                                         $('#value_unit').append('<option value="'+content[i].children_value[z]+'">'+content[i].children_value[z]+'</option>');
                                        //$('#data_type').append('<option value="'+value_children_id[y]+'">'+content[i].children_value[z]+'</option>');
                                        }
                                    }
								}
							});
        });


        $('#new_ttt_modal').on( "click", function() {
              
                            $.ajax({
                            url: processloader_uri_filemanager+"/?type=value type",
							data: { },
							type: "GET",
							async: true,
							dataType: 'json',
							success: function (data) {	
                                    var content = data.content;
                                    for (var i =0; i < content.length; i++){
                                        var value_u = content[i].id;
                                        $('#value_type').append('<option value="'+value_u+'">'+content[i].label+'</option>');
                                    }
								}
							});
                            $("#new_ttt").show();               
        });


        $('#conf_delete').on( "click", function() {
            var delete_name = $("#delete_name").val();
            //////////////
            $.ajax({
                url: "get_typicaltimetrend.php",
				data: { 
                    'action': 'delete',
                    'name': delete_name
                },
				type: "POST",
				async: true,
				//dataType: 'json',
				success: function (data) { 
                   /* if (data == "ok"){
                        alert("Typical Time Trend Successfully deleted");
                        location.reload();
                    } 
                    if (data == "ko"){
                        alert("Error during deletion");
                        location.reload();
                    } */
                    setTimeout(function() {
                location.reload();
            }, 1000); 
                   
                 }
                });
            /////////
            
        });

        
        $('#conf_new').on( "click", function() {
                        var new_name = $(".form-control").filter(function() {
                                                if ($(this).val() === '' || $(this).val() === null) {
                                                           $(this).css('border','1px solid #ff0000');
                                                }
                            });
                        new_name.each(function() { 
                            $(this).addClass("vuoto");
                        });
                        //
                        var value_unit = $('#value_unit').val();
                        var data_type = $('#data_type').val();
                        //
                        var value_list0 = $('#valuelist').val();
                        var value_list = value_list0.replace(' ', '').replace('"', '').replace("'", "");              
                        var array_list = [];
                        if (data_type =='float'){
                            array_list = value_list.split(';').map(number => parseFloat(number.replace(",", ".")));
                        }else if (data_type =='integer'){
                            array_list = value_list.split(';').map(number => parseInt(number.replace(",", ".")));
                        }else if (data_type =='string'){
                            array_list = value_list.split(';').map(number => parseFloat(number.replace(",", ".")));
                        }else{
                            array_list = value_list.split(';').map(number => parseFloat(number.replace(",", ".")));
                        }
                        
                        //alert(array_list.length);
                        //
                        if (array_list.length != 24){
                            $('#valuelist').css('color','red');
                            $('#valuelist').css('border','1px solid #ff0000');
                            $('#conf_new').prop('disabled', true);
                                alert('Not valid list');
                        }else{
                            //INSERT API//
                            //////////
                            var from_date = $('#from_date').val();
                            var f_date = new Date(from_date);
                            var f_anno = f_date.getFullYear();
                            var f_mese = String(f_date.getMonth() + 1).padStart(2, '0'); // Aggiunge uno per il mese (0-11)
                            var f_giorno = String(f_date.getDate()).padStart(2, '0');
                            var f_ora = String(f_date.getHours()).padStart(2, '0');
                            var f_minuti = String(f_date.getMinutes()).padStart(2, '0');
                            var f_secondi = String(f_date.getSeconds()).padStart(2, '0');
                            var from_date_value = f_anno +'-'+f_mese+'-'+f_giorno+'T'+f_ora+':'+f_minuti+':'+f_secondi+'.000Z';

                            var to_date = $('#to_date').val();
                            var t_date = new Date(to_date);
                            var t_anno = t_date.getFullYear();
                            var t_mese = String(t_date.getMonth() + 1).padStart(2, '0'); // Aggiunge uno per il mese (0-11)
                            var t_giorno = String(t_date.getDate()).padStart(2, '0');
                            var t_ora = String(t_date.getHours()).padStart(2, '0');
                            var t_minuti = String(t_date.getMinutes()).padStart(2, '0');
                            var t_secondi = String(t_date.getSeconds()).padStart(2, '0');
                            var to_date_value = t_anno +'-'+t_mese+'-'+t_giorno+'T'+t_ora+':'+t_minuti+':'+t_secondi+'.000Z';
                            //////
                            var max_value = 0;
                            if (data_type !=='string'){
                                max_value = Math.max(...array_list);
                            }          
                            //var data_type = $('#data_type').val();
                            //var value_unit = $('#value_unit').val();
                            var device_name = $('#new_name').val();
                            var referenceTo = $('#referenceTo').val();
                            var kind = $('#kind').val();
                            var kindDetails = $('#kindDetails').val();
                            var values_string = array_list.join(',');
                            values_string = '['+values_string+']';
                           // var value_type = $('#value_type').val();
                            var selectElement = document.getElementById('value_type');
                            var selectedOption = selectElement.options[selectElement.selectedIndex];
                            var value_type = selectedOption.text;
                            console.log(value_type);
                            

                           
                           $.ajax({
                            url: "get_typicaltimetrend.php",
							data: { 
                                'action': 'insert',
                                'name': device_name,
                                'value_type': value_type,
                                'data_type': data_type,
                                'value_unit': value_unit,
                                'max_value': max_value,
                                'from_date': from_date_value,
                                'to_date': to_date_value,
                                //'day_weeks': jsonDay,
                                'kind': kind,
                                'kindDetails':kindDetails,
                                'values': values_string,
                                'referenceTo': referenceTo
                            },
							type: "POST",
							async: true,
							//dataType: 'json',
							success: function (data) {
                                alert('Device Successfully created');
                                location.reload();    
                                   },
                            error: function(data){
                                alert('Error during device creation'); 
                                location.reload();
                            }
                            });
                            
                        }
                        
                        //
        });

        $('#valuelist').on("keypress", function(){
            $('#valuelist').css('color','black');
            $('#valuelist').css('border','1px solid #ccc');
            $('#conf_new').prop('disabled', false);
        });
        $('.form-control').on("keypress", function(){
            $(this).css('color','black');
            $(this).css('border','1px solid #ccc');
            $('#conf_new').prop('disabled', false);
            //////
        });
        $('.form-control').on("change", function(){
            $(this).css('color','black');
            $(this).css('border','1px solid #ccc');
            $('#conf_new').prop('disabled', false);
            //////
        });





/*$("#delete-modal").on('shown.bs.modal', function (e) {
    alert('LIST');
});*/
///
    });

    function delete_value (value){
    //alert(value);
    $('#delete_name').val(value);
}
    </script>
    </body>
</html>