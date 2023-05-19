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
//checkSession('RootAdmin');
//$_SESSION['loggedRole'] = "RootAdmin";

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);
error_reporting(E_ERROR);

$lastUsedColors = null;
/*    $dashId = $_REQUEST['dashboardId'];
  $q = "SELECT * FROM Dashboard.Config_dashboard WHERE Id = '$dashId'";
  $r = mysqli_query($link, $q);

  if($r)
  {
  data[i] = mysqli_fetch_assoc($r);

  if(data[i]['deleted:== 'yes')
  {
  header("Location: ../view/dashboardNotAvailable.php");
  exit();
  }
  else
  {
  $lastUsedColors = json_decode(data[i]['lastUsedColors']);
  }
  } */
?>
<!DOCTYPE HTML>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bim Manager</title>

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
    <script src="../js/bootstrap-ckeditor-.js?v=<?php
          echo time();
    ?>" type="text/javascript" charset="utf-8"></script>

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
</style>

<body style="overflow-y: hidden !important">
  <?php include "../cookie_banner/cookie-banner.php"; ?>

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
                <button type="button" id="new_bim_modal" class="btn btn-warning new_org" data-toggle="modal" data-target="#myModal_new" style="float:left; margin-right: 5px;">
                    <i class="fa fa-plus"></i> 
                    Add new BIM
                </button>
                <!-- 	<div style="display:none;"> -->

                <!--</div>-->
            </div>
            <!-- -->
            <div id="table_div" style="margin-left: 5%; margin-right: 5%">
                <!-- -->
                <table id="value_table" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="dashboardsTableHeader">
                    <th>Id</th>
                    <th>Description</th>
                    <th>Nature</th>
                    <th>Subnature</th>
                    <th>Link to Pin</th>
                    <th>Link to Project</th>
                    <th>Organizations</th>
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
                    <button type="button" class="close" id="button_conf_new">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("New BIM") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="input-group"><span class="input-group-addon"><?= _("Description") ?>:</span><input id="new_description" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Nature") ?>:</span>
                      <!--  <input id="new_nature" type="text" class="form-control" >-->
                        <select id="new_nature" name="select_mode" class="form-control">
                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Subnature") ?>:</span>
                       <!-- <input id="new_subnature" type="text" class="form-control" >-->
                        <select id="new_subnature" name="select_mode" class="form-control">
                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Project Id") ?>:</span><input id="new_iod" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Organizations") ?>:</span>
                        <!--<input id="new_org" type="text" class="form-control" >-->
                        <select id="new_org" name="select_mode" class="form-control">
                        </select>
                    </div></div><br />
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_new"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
    <div class="modal fade" id="edit_bim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("Edit BIM") ?></h5>
                </div>
                <div class="modal-body">
                    <input id="edit_id" type="text" class="form-control" readonly style="display:none;"/>
                    <div class="input-group"><span class="input-group-addon"><?= _("Description") ?>:</span><input id="edit_description" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Nature") ?>:</span>
                        <!--<input id="edit_nature" type="text" class="form-control" >-->
                        <select id="edit_nature" name="select_mode" class="form-control">
                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Subnature") ?>:</span>
                      <!--  <input id="edit_subnature" type="text" class="form-control" >-->
                        <select id="edit_subnature" name="select_mode" class="form-control">
                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Project Id") ?>:</span><input id="edit_iod" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Organizations") ?>:</span>
                        <select id="edit_org" name="select_mode" class="form-control">
                        </select>
                 <!--<input id="edit_org" type="text" class="form-control" >-->
                    </div><br />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_edit"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->
    <div class="modal fade" id="delete_bim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("Delete BIM") ?></h5>
                </div>
                <div class="modal-body">
                    <input id="delete_id" type="text" class="form-control" readonly style="display:none;"/>
                    <?= _("Are you sure do you want to delete this bim project from database?") ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_del"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->
</div>
<script type='text/javascript'>
    $(document).ready(function () {
        var BIMDeviceApi = "<?php echo $BIMDeviceApi; ?>";
        var BIMLinkProject = "<?php echo $BIMLinkProject; ?>";
        
        $.ajax({
            async: true,
            type: 'GET',
            dataType: 'json',
            url: 'getbimdata.php',
            data: {
                action: 'get_bim'
            },
            success: function (data) {
                //var json_data = JSON.parse(data);
                var len = data.length;
                //
                //console.log(json_data['response']['result']);


                for (var i = 0; i < len; i++) {
                    var nature = '';
                    var subnature = '';
                    var organizations = '';
                    var current_id = data[i]['id'];
                    var current_name = data[i]['project_name'];
                    if ((data[i]['nature'] === null) || (data[i]['nature'] === 'null') || (data[i]['nature'] === undefined)) {
                        nature = '';
                    } else {
                        nature = data[i]['nature'];
                    }
                    if ((data[i]['sub_nature'] === null) || (data[i]['sub_nature'] === 'null') || (data[i]['sub_nature'] === undefined)) {
                        subnature = '';
                    } else {
                        subnature = data[i]['sub_nature'];
                    }
                    organizations = data[i]['organizations'];

                    var oid = data[i]['poid'];
                    var project_link = BIMLinkProject+'?poid=' + oid;
                    var pin_link = BIMDeviceApi + oid;
                    // $('#value_table tbody').append('<tr><td>'+current_id+'</td><td>' + current_name + '</td><td><a href="'+pin_link+'" target="_blank">'+pin_link+'</a></td><td><a href="'+project_link+'" target="_blank">'+project_link+'</a></td><td><button type="button" class="editDashBtn edit_file" data-target="#edit_org" data-toggle="modal" value="'+current_id+'">EDIT</button>    <button type="button" class="delDashBtn delete_file" data-target="#delete-modal" data-toggle="modal" value="'+current_id+'">DELETE</button></td></tr>');
                    $('#value_table tbody').append('<tr><td>' + current_id + '</td><td>' + current_name + '</td><td>' + nature + '</td><td>' + subnature + '</td><td><a href="' + pin_link + '" target="_blank">' + pin_link + '</a></td><td><a href="' + project_link + '" target="_blank">' + project_link + '</a></td><td>' + organizations + '</td><td><button type="button" class="editDashBtn edit_file" data-target="#edit_bim" data-toggle="modal" onclick="func_edit(' + current_id + ', \'' + current_name + '\', \'' + nature + '\',  \'' + subnature + '\', \'' + oid + '\',\'' + organizations + '\')" value="' + current_id + '">EDIT</button>    <button type="button" class="delDashBtn delete_file" data-target="#delete_bim" data-toggle="modal" onclick="func_del(' + current_id + ')">DELETE</button></td></tr>');
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
        $('#new_bim_modal').click(function () {
            $.ajax({
                async: true,
                type: 'GET',
                dataType: 'json',
                url: 'getbimdata.php',
                data: {
                    action: 'get_parameters'
                },
                success: function (data) {
                    // console.log(data);
                    //var json_data = JSON.parse(data);
                    $('#new_org').empty();
                    $('#new_nature').empty();
                    $('#new_subnature').empty();
                    var orgs = data['organizations'];
                    var natu = data['nature'];
                    var subn = data['subnature'];
                    var lun = orgs.length;
                    var lun_natu = natu.length;
                    var lun_subn = subn.length;
                    console.log(orgs);
                    for (var i = 0; i < lun; i++) {
                        $('#new_org').append('<option value="' + orgs[i] + '">' + orgs[i] + '</option>');

                    }
                    for (var y = 0; y < lun_natu; y++) {
                        $('#new_nature').append('<option value="' + natu[y]['value'] + '">' + natu[y]['value'] + '</option>');
                    }
                    for (var j = 0; j < lun_subn; j++) {
                        $('#new_subnature').append('<option value="' + subn[j]['value'] + '">' + subn[j]['value'] + '</option>');
                    }
                    // window.location.reload();
                }
            });
        });
        //
        $('#conf_new').click(function () {
            var new_description = $('#new_description').val();
            var new_nature = $('#new_nature').val();
            var new_subnature = $('#new_subnature').val();
            var new_iod = $('#new_iod').val();
            var new_org = $('#new_org').val();
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                url: 'getbimdata.php',
                data: {
                    action: 'new_bim',
                    name: new_description,
                    nature: new_nature,
                    subnature: new_subnature,
                    iod: new_iod,
                    org: new_org
                },
                success: function (data) {
                    window.location.reload();
                }
            });
        });
        //
        $('#conf_edit').click(function () {
            var id = $('#edit_id').val();
            var new_description = $('#edit_description').val();
            var new_nature = $('#edit_nature').val();
            var new_subnature = $('#edit_subnature').val();
            var new_iod = $('#edit_iod').val();
            var new_org = $('#edit_org').val();
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                url: 'getbimdata.php',
                data: {
                    action: 'edit_bim',
                    id: id,
                    name: new_description,
                    nature: new_nature,
                    subnature: new_subnature,
                    iod: new_iod,
                    org: new_org
                },
                success: function (data) {
                    window.location.reload();
                }
            });
        });

        $('#conf_del').click(function () {
            var id = $('#delete_id').val();
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'json',
                url: 'getbimdata.php',
                data: {
                    action: 'delete_bim',
                    id: id
                },
                success: function (data) {
                    window.location.reload();
                }
            });
        });

        //
    });

    function func_del(id) {
        //var value = this.attr('value');
        console.log(id);
        $('#delete_id').val(id);
    }
    ;

    function func_edit(id, project_name, nature, sub_nature, oid, org) {

        $.ajax({
            async: true,
            type: 'GET',
            dataType: 'json',
            url: 'getbimdata.php',
            data: {
                action: 'get_parameters'
            },
            success: function (data) {
                // console.log(data);
                //var json_data = JSON.parse(data);
                $('#edit_org').empty();
                $('#edit_nature').empty();
                $('#edit_subnature').empty();
                var orgs = data['organizations'];
                var natu = data['nature'];
                var subn = data['subnature'];
                var lun = orgs.length;
                var lun_natu = natu.length;
                var lun_subn = subn.length;

                for (var i = 0; i < lun; i++) {
                    $('#edit_org').append('<option value="' + orgs[i] + '">' + orgs[i] + '</option>');
                }
                for (var y = 0; y < lun_natu; y++) {
                    $('#edit_nature').append('<option value="' + natu[y]['value'] + '">' + natu[y]['value'] + '</option>');
                }
                for (var j = 0; j < lun_subn; j++) {
                    $('#edit_subnature').append('<option value="' + subn[j]['value'] + '">' + subn[j]['value'] + '</option>');
                }
                // window.location.reload();
                $('#edit_description').val(project_name);
                $('#edit_nature').val(nature);
                $('#edit_subnature').val(sub_nature);
                $('#edit_iod').val(oid);
                $('#edit_id').val(id);
                $('#edit_org').val(org).prop('selected', true);
            }
        });

    }
    ;
</script>
</body>
</html>