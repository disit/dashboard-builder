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

  include_once('../config.php');

if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

include('process-form.php');
header("Cache-Control: private, max-age=$cacheControlMaxAge");

//session_start();
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
<html class="dark">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>organizationManager</title>
    
    

    <!-- Bootstrap Core CSS -->
    <link href="../css/s4c-css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../css/s4c-css/bootstrap/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles_gridster.css" type="text/css" />
    <link rel="stylesheet" href="../css/style_widgets.css?v=<?php
    echo time();
    ?>" type="text/css" />
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
    <?php include "theme-switcher.php"?>
    
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
                <?php
                if ($_SESSION['loggedRole'] == "RootAdmin") {
                    echo('<button type="button" class="btn btn-warning new_org" data-toggle="modal" data-target="#myModal_new" style="float:left; margin-right: 5px;">
                    <i class="fa fa-plus"></i> 
                    Create New Organization
                </button>');
                }
                ?>
                <!-- 	<div style="display:none;"> -->

                <!--</div>-->
            </div>
            <!-- -->
            <div id="table_div" style="margin-left: 5%; margin-right: 5%">
                <!-- -->
                <table id="value_table" class="table table-striped table-bordered" style="width: 100%">
                    <?php
                    if ($_SESSION['loggedRole'] == "RootAdmin") {
                        echo('<thead class="dashboardsTableHeader">
                                <th>Organizazion Name</th>
                                <!--<th>Link to Knowledge Base</th>
                                <th>Zoom level</th>
                                <th>Link to Drupal</th>
                                <th>Link to Welcome Page</th>
                                <th>Link to Org Page</th>-->
                                <th>Data Table/POI Users</th>
                                <th>Control</th>
                                </thead>');
                    } else {
                        echo('<thead class="dashboardsTableHeader">
                                <th>Organizazion Name</th>
                                <th>Link to Drupal</th>
                                <th>Link to Welcome Page</th>
                                </thead>');
                    }
                    ?>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Fine modal content -->
    <!-- Modal -->
    <div class="modal fade" id="myModal_new" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_new">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("New Organization") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon"><?= _("Set coordinates") ?>:</span>
                        <div id="Map1" style="height:200px"></div></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Latitude") ?>:</span><input id="new_lat" type="text" placeholder="" class="form-control" oninput="change_coord('mymap')"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Longitude") ?>:</span><input id="new_lng" type="text" placeholder=""  class="form-control" oninput="change_coord('mymap')"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Zoom level") ?>:</span><input id="new_zoom" type="text" placeholder=""  class="form-control" oninput="change_coord('mymap')"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Organizazion Name") ?>:</span><input id="new_name" type="text" placeholder=""  class="form-control"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Link to Knowledge Base") ?>:</span><input id="new_kb" type="text" placeholder="http://.../ServiceMap/api/v1/"  class="form-control"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Link to Drupal") ?>:</span><input id="new_drupal" type="text" placeholder="https://..."  class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Link to Welcome page") ?>:</span><input id="new_welcome" placeholder="https://..."  type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Link to Org page") ?>:</span><input id="new_orgpage" placeholder="https://..."  type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Broker") ?>:</span><input id="new_broker" type="text" placeholder=""  class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Orion IP") ?>:</span><input id="new_orionIP" type="text" placeholder="http://x.x.x.x:1026"  class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Orthomap Json") ?>:</span><textarea id="new_orthomapJson" placeholder="{}"  type="text" class="form-control" ></textarea></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("kb IP") ?>:</span><input id="new_kbIP" type="text" placeholder="http://x.x.x.x:8890"  class="form-control" ></div><br />
                   <!-- <div class="input-group"><span class="input-group-addon"><?= _("User") ?>:</span><input id="new_user" type="text" class="form-control" ></div><br />-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_new"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!--</div>-->
    <div class="modal fade" id="edit_org" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" id="button_conf_edit">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel"><?= _("Edit Organization") ?></h5>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon"><?= _("Set coordinates") ?>:</span>
                        <div id="Map2" style="height:200px"></div></div><br />
                    <input id="edit_id" type="text" class="form-control" readonly style="display: none">
                    <input id="edit_old_name" type="text" class="form-control" readonly style="display: none">
                    <div class="input-group"><span class="input-group-addon"><?= _("Latitude") ?>:</span><input id="edit_lat" type="text" class="form-control" oninput="change_coord('mymap2')"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Longitude") ?>:</span><input id="edit_lng" type="text" class="form-control" oninput="change_coord('mymap2')"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Zoom level") ?>:</span><input id="edit_zoom" type="text" class="form-control" oninput="change_coord('mymap2')"></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Organizazion Name") ?>:</span><input id="edit_name" type="text" class="form-control" readonly></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Link to Knowledge Base") ?>:</span><input id="edit_kb" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Link to Drupal") ?>:</span><input id="edit_drupal" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Link to Org page") ?>:</span><input id="edit_welcome" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Link to Welcome page") ?>:</span><input id="edit_orgpage" type="text" class="form-control" ></div><br />
                    <!-- NEW FIELD-->
                    <div class="input-group"><span class="input-group-addon"><?= _("Broker") ?>:</span><input id="edit_broker" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Orion IP") ?>:</span><input id="edit_orionIP" type="text" class="form-control" ></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("Orthomap Json") ?>:</span><textarea id="edit_orthomapJson" type="text" class="form-control" ></textarea></div><br />
                    <div class="input-group"><span class="input-group-addon"><?= _("kb IP") ?>:</span><input id="edit_kbIP" type="text" class="form-control" ></div><br />
                    <!--<div class="input-group"><span class="input-group-addon"><?= _("User") ?>:</span><input id="edit_user" type="text" class="form-control" ></div><br />-->
                    <!-- -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                    <button type="button" class="btn btn-primary" id="conf_edit"><?= _("Confirm") ?></button>
                </div>
            </div>
        </div>
    </div>
    <!--</div>-->
    <!-- Fine modale wizard -->
    <!-- DELETE -->
    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"><?= _("Delete Organization") ?></h4>
                </div>
                <form method="post" id="delete_element" accept-charset="UTF-8">
                    <div class="modal-body">
                        <?= _("Are you sure you want delete this Organization?") ?> 
                    </div>
                    <input id="delete_id" type="text" name="id" style="display: none;">
                    <input id="delete_name" type="text" name="name" style="display: none;">
                    <input id="table_delete" type="text" name="rable" style="display: none;">
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" data-dismiss="modal"><?= _("Cancel") ?></button>
                        <input type="button" id="delete_command" value="Confirm" class="btn confirmBtn">
                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- -->
    <!-- USERS MODAL -->
    <div class="modal fade" id="users-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"><?= _("Manage Users Organization List") ?></h4>
                </div>
                <form method="post" id="delete_element" accept-charset="UTF-8">
                    <div class="modal-body">
                        <div id="table_div" style="margin-left: 5%; margin-right: 5%">
                            <div class="panel panel-default">
                                <div class="panel-heading"><?= _("Add new User") ?></div>
                                <div class="input-group">
                                    <div class="panel-body">
                                        <input type="text" id="new_user_id" hidden />
                                        <input type="text" id="new_user_org" hidden/>
                                        <input type="text" class="form-control" placeholder="Insert username name" aria-label="Insert username name" id="new_user">
                                        <button type="button" id="user_button" class="btn btn-outline-secondary btn-warning new_org" data-toggle="modal" data-target="#" style="float:left; margin-right: 5px;">
                                            <i class="fa fa-plus"></i>
                                            <?= _("Add") ?>
                                        </button>
                                        <!-- -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br /><br />
                        <!-- -->
                        <div id="select_user"></div>
                        <!-- -->
                        <table id="users_table" class="table table-striped table-bordered" style="width: 100%">
                            <thead class="usersTableHeader">
                            <th> <?= _("Organization Users") ?></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" id="close_users" data-dismiss="modal"><?= _("Cancel") ?></button>
                        <!-- <input type="button" id="user_confirm" value="Confirm" class="btn confirmBtn">-->
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
    <!-- DataTable USERS MODAL -->
    <div class="modal fade" id="datatable-users-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"><?= _("Manage Data Table/POI Users List") ?></h4>
                </div>
                <form method="post" id="delete_element" accept-charset="UTF-8">
                    <div class="modal-body">
                        <!-- -->
                        <div id="select_user">
                        <input type="text" id="new_dtUser_org" style="display:none;"/> 
                        </div>
                        <!-- -->
                        <table id="dtUsers_table" class="table table-striped table-bordered" style="width: 100%">
                            <thead class="usersTableHeader">
                            <th> <?= _("Data Table/POI Users") ?></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" id="close_users" data-dismiss="modal"><?= _("Cancel") ?></button>
                        <input type="button" id="dtUser_confirm" value="Confirm" class="btn confirmBtn">
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
    <!-- -->
    <div class="modal fade" id="group-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"><?= _("Groups: ") ?><span id="org_name"><span></h4>
                </div>
                <form method="post" accept-charset="UTF-8">
                    <div class="modal-body">
                        <div id="table_grpou_div" style="margin-left: 5%; margin-right: 5%">
                            <div class="panel panel-default">
                                <div class="panel-heading"><?= _("Add new Group") ?></div>
                                <div class="input-group">
                                    <div class="panel-body">
                                        <input type="text" class="form-control" placeholder="Insert group name" aria-label="Insert group name" id="new_group">
                                        <button type="button" id="group_button" class="btn btn-outline-secondary btn-warning new_grp" data-toggle="modal" data-target="#" style="float:left; margin-right: 5px;">
                                            <i class="fa fa-plus"></i>
                                            <?= _("Add") ?>
                                        </button>
                                        <!-- -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br /><br />
                        <!-- -->
                        <div id="select_group"></div>
                        <!-- -->
                        <table id="group_table" class="table table-striped table-bordered" style="width: 100%">
                            <thead class="usersTableHeader">
                            <th> <?= _("Groups List") ?></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" id="close_users" data-dismiss="modal"><?= _("Cancel") ?></button>
                        <!-- <input type="button" id="user_confirm" value="Confirm" class="btn confirmBtn">-->
                    </div>
                </form>
            </div>
        </div>

    </div>
    <!--  -->
<script type='text/javascript'>
    ////*****////
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

        ////
        var role_session_active = "<?= $_SESSION['loggedRole']; ?>";
        ///////
        $.ajax({
            url: 'editorganization.php',
            data: {
                action: 'get_data'
            },
            type: "POST",
            async: true,
            success: function (data) {
                // console.log(data);
                var obj = JSON.parse(data);
                var l = obj.length;
                console.log(obj[0]['organizationName']);
                //var orthomap = JSON.stringify(obj[i]['orthomapJson']);
                var orthomap = '';
                for (var i = 0; i < l; i++) {
                    var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#edit_org" data-toggle="modal" value="' + obj[i]['org'] + '" onclick="edit_fun(\'' + obj[i]['org'] + '\')">EDIT</button>';
                    var button_del = '<button type="button" class="delDashBtn delete_file" data-target="#delete-modal" data-toggle="modal" value="' + obj[i]['org'] + '" onclick="delete_func(\'' + obj[i]['org'] + '\',\'' + obj[i]['org'] + '\')">DELETE</button>';
                    var button_users = '<button type="button" class="viewDashBtn addchild" data-target="#users-modal" data-toggle="modal" onclick="list_user(\'' + obj[i]['org'] + '\')">USERS</button>';
                    var button_groups = '<button type="button" class="viewDashBtn edit_group" data-target="#group-modal" data-toggle="modal" onclick="list_group(\'' + obj[i]['org'] + '\')">GROUP</button>';
                    var button_datatable = '<button type="button" class="viewDashBtn edit_datatable" data-target="#datatable-users-modal" data-toggle="modal" onclick="user_group(\'' + obj[i]['org'] + '\')">MANAGER</button>';
                    //button_users = "";
                    var dtUsers="";
                    if (obj[i]['dtUsers'] !== null){
                        dtUsers = obj[i]['dtUsers'];
                    }
                    //
                    if (role_session_active === 'RootAdmin') {
                        //$('#value_table').append('<tr><td>' + obj[i]['organizationName'] + '</td><td><a href="' + obj[i]['kbUrl'] + '" target="_blank">' + obj[i]['kbUrl'] + '</a></td><td>' + obj[i]['zoomLevel'] + '</td><td><a href="' + obj[i]['drupalUrl'] + '" target="_blank">' + obj[i]['drupalUrl'] + '</a></td><td><a href="' + obj[i]['orgUrl'] + '" target="_blank">' + obj[i]['orgUrl'] + '</a></td><td><a href="' + obj[i]['welcomeUrl'] + '" target="_blank">' + obj[i]['welcomeUrl'] + '</a></td><td>' + obj[i]['users'] + ' ' + button_users + '</td><td>' + button_edit + ' ' +button_groups+' ' + button_del + '</td></tr>');
                        $('#value_table').append('<tr><td>' + obj[i]['org']  + '</td><td><span id="'+obj[i]['org']+'_users" hidden>' + obj[i]['users'] + '</span><span id="'+obj[i]['org']+'_dtUsers">'+dtUsers+'</span>    '+button_datatable+'</td><td>' + button_edit + ' ' + button_users + ' ' +button_groups+' ' + button_del + '</td></tr>');
                        //
                    } else {
                        $('#value_table').append('<tr><td>' + obj[i]['org']  + '</td><td><span id="'+obj[i]['org']+'_users" hidden>' + obj[i]['users'] + '</span><span id="'+obj[i]['org']+'_dtUsers">'+dtUsers+'</span></td><td></td></tr>');
                       // $('#value_table').append('<tr><td>' + obj[i]['organizationName'] + '</td><td><a href="' + obj[i]['drupalUrl'] + '" target="_blank">' + obj[i]['drupalUrl'] + '</a></td><td><a href="' + obj[i]['orgUrl'] + '" target="_blank">' + obj[i]['orgUrl'] + '</a></td></tr>');
                    }

                }
                /////////////
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

                //////////
            }
        });


        ////////////
        mymap2.on('click',
                function (e) {
                    //var coord = e.latlng.toString().split(',');
                    //var lat = coord[0].split('(');
                    //var lng = coord[1].split(')');
                    var zoom = mymap2.getZoom();
                    //
                    $('#edit_lat').val(e.latlng.lat);
                    $('#edit_lng').val(e.latlng.lng);
                    $('#edit_zoom').val(zoom);
                    //
                    var latlng = e.latlng;
                    // mymap2.removeLayer();
                    var greyIcon = new L.Icon({
                        iconUrl: '../img/outputPngIcons/pin-generico.png',
                        //iconAnchor: [e.latlng.lat, e.latlng.lng]
                        iconAnchor: [16,32]
                    });
                    $(".leaflet-marker-icon").remove();
                    var marker = L.marker([e.latlng.lat, e.latlng.lng], {icon: greyIcon}).addTo(mymap2);
                    //var marker = L.marker(latlng, {icon: greyIcon}).addTo(mymap2);

                });
        /////////
        mymap.on('click',
                function (e) {
                    //var coord = e.latlng.toString().split(',');
                    ///var lat = coord[0].split('(');
                    ///var lng = coord[1].split(')');
                    var zoom = mymap.getZoom();
                    //
                    $('#new_lat').val(e.latlng.lat);
                    $('#new_lng').val(e.latlng.lng);
                    $('#new_zoom').val(zoom);
                    //
                    var latlng = e.latlng;
                    //
                    var greyIcon = new L.Icon({
                        iconUrl: '../img/outputPngIcons/pin-generico.png',
                       // iconAnchor: [e.latlng.lat, e.latlng.lng]
                       iconAnchor: [16,32]
                    });
                    //mymap.removeLayer();
                    $(".leaflet-marker-icon").remove();
                    var marker = L.marker([e.latlng.lat, e.latlng.lng], {icon: greyIcon}).addTo(mymap);
                  // var marker = L.marker(latlng, {icon: greyIcon}).addTo(mymap);
                });
        ////////////////
        $('#delete_command').click(function () {
            //
            var id = $('#delete_id').val();
            var name = $('#delete_name').val();
            var org_list = $('#'+name+'_users').text();
            //
            //alert('org_list: '+org_list);
            if (org_list ==""){
            $.ajax({
                url: 'editorganization.php',
                data: {
                    action: 'delete_data',
                    id: id,
                    name: name
                },
                type: "POST",
                async: true,
                success: function (data) {
                    if (data === 'ok') {
                        window.location.reload();
                    } else {
                        alert('Error during organization deletion');
                    }
                }
            });
          }else{
            alert('This organization cannot be deleted if there are users assigned to it');
          }
        });
        /////////
        $('#group_button').click(function () {  
        var new_group = $('#new_group').val();
        var org_name = $('#org_name').text();
        $.ajax({
            url: 'editorganization.php',
            data: {
                action: 'add_group',
                group: new_group,
                organization: org_name
            },
            type: "POST",
            async: false,
            success: function (data) {
                alert(data);
                window.location.reload();
                /////////
            }
        })
    });
        /////////
        $('#conf_edit').click(function () {
            //
            var id = $('#edit_id').val();
            var name = $('#edit_name').val();
            var kb = $('#edit_kb').val();
            var zoom = $('#edit_zoom').val();
            var drupal = $('#edit_drupal').val();
            var org = $('#edit_welcome').val();
            var welc = $('#edit_orgpage').val();
            var lat = $('#edit_lat').val();
            var lng = $('#edit_lng').val();
            var gps = lat + ',' + lng;
            var edit_old_name = $('#edit_old_name').val();
            ////
            var broker = $('#edit_broker').val();
            var orionIP = $('#edit_orionIP').val();
            var orthomapJson = $('#edit_orthomapJson').val();
            var kbIP = $('#edit_kbIP').val();

            /////////
            name = name.trim();
            kb = kb.trim();
            zoom = zoom.trim();
            drupal = drupal.trim();
            org = org.trim();
            welc = welc.trim();
            broker = broker.trim();
            orionIP = orionIP.trim();
            orthomapJson = orthomapJson.trim();
            kbIP = kbIP.trim();
            ////////////
            $.ajax({
                url: 'editorganization.php',
                data: {
                    action: 'edit_data',
                    id: id,
                    name: name,
                    kb: kb,
                    zoom: zoom,
                    drupal: drupal,
                    org: org,
                    welc: welc,
                    gps: gps,
                    old_name: edit_old_name,
                    broker: broker,
                    orionIP: orionIP,
                    orthomapJson: orthomapJson,
                    kbIP: kbIP
                },
                type: "POST",
                async: true,
                success: function (data) {
                    if (data === 'ok') {
                        window.location.reload();
                    } else {
                        alert('Error during editing organization');
                    }
                }
            });
        });
        ////////////////
        $('#button_conf_new').click(function () {
            $('#myModal_new').modal('hide');
        });
        //
        $('#button_conf_edit').click(function () {
            $('#edit_org').modal('hide');
        });
        //
        $('#user_button').click(function () {
            var user = $('#new_user').val();
            var id = $('#new_user_id').val();
            var org = $('#new_user_org').val();
            $.ajax({
                url: 'editorganization.php',
                data: {
                    action: 'add_user',
                    id: id,
                    user: user,
                    org: org
                },
                type: "POST",
                async: true,
                success: function (data) {
                    alert(data);
                    if (data === "User successfully added to organization") {
                        window.location.reload();
                    }
                    //
                }
            });
            //$('#edit_org').modal('hide');

            ////////////
        });
        //
        $('#close_users').click(function () {
            $("#users_table tbody tr").remove();
        });
        ///////
        $('#conf_new').click(function () {
            var name = $('#new_name').val();
            var kb = $('#new_kb').val();
            var zoom = $('#new_zoom').val();
            var drupal = $('#new_drupal').val();
            var org = $('#new_orgpage').val();
            var welc = $('#new_welcome').val();
            var lat = $('#new_lat').val();
            var lng = $('#new_lng').val();
            var gps = lat + ',' + lng;
            var broker = $('#new_broker').val();
            var orthomap = $('#new_orthomapJson').val();
            var orion =$('#new_orionIP').val();
            var kbIP = $('#new_kbIP').val();
            //////   
            name = name.trim();
            kb = kb.trim();
            zoom = zoom.trim();
            drupal = drupal.trim();
            org = org.trim();
            welc = welc.trim();
            lat = lat.trim();
            lng = lng.trim();
            /////
            $.ajax({
                url: 'editorganization.php',
                data: {
                    action: 'new_data',
                    name: name,
                    kb: kb,
                    zoom: zoom,
                    drupal: drupal,
                    org: org,
                    welc: welc,
                    gps: gps,
                    broker: broker,
                    orthomap: orthomap,
                    orion: orion,
                    kbIP: kbIP
                },
                type: "POST",
                async: true,
                success: function (data) {
                    if (data === 'ok') {
                        window.location.reload();
                    } else {
                        alert('Error during organization creation');
                    }
                }
            });
        });
        /////////////////
        $('#dtUser_confirm').click(function () {
            
            var checkboxesSelezionate = $('.dtUser:checked'); 
            //
            var org = $('#new_dtUser_org').val();
            var idArray = [];
            // Aggiungi gli ID all'array
            checkboxesSelezionate.each(function(){
            idArray.push($(this).attr('value'));
            });
            console.log(idArray);
            //AJACK MANAGER dtUSERS
            $.ajax({
                url: 'editorganization.php',
                data: {
                    action: 'edit_dtUsers',
                    users: idArray,
                    org: org
                },
                type: "POST",
                async: true,
                success: function (data) {
                    alert(data);
                    window.location.reload();
                    /*if (data === 'ok') {     
                        window.location.reload();
                    } else {
                        alert('Error during organization creation');
                    }*/
                }
            });
            //
        });

        /////////////////////
        setTimeout(mymap.invalidateSize.bind(mymap));
        setTimeout(mymap2.invalidateSize.bind(mymap2));
        ////////////////
    });
    ///////////////
    function change_coord(map) {

        if (map === 'mymap') {
            var lat = $('#new_lat').val();
            var lng = $('#new_lng').val();
            var zoom = $('#new_zoom').val();
            $(".leaflet-marker-icon").remove();
            mymap.setView([lat, lng], zoom);
            var greyIcon = new L.Icon({
                iconUrl: '../img/outputPngIcons/pin-generico.png',
                iconAnchor: [lat, lng]
            });
            var marker = L.marker([lat, lng], {icon: greyIcon}).addTo(mymap);

        }
        if (map === 'mymap2') {
            var lat = $('#edit_lat').val();
            var lng = $('#edit_lng').val();
            var zoom = $('#edit_zoom').val();
            $(".leaflet-marker-icon").remove();
            mymap2.setView([lat, lng], zoom);
            var greyIcon = new L.Icon({
                iconUrl: '../img/outputPngIcons/pin-generico.png',
                iconAnchor: [lat, lng]
            });
            var marker = L.marker([lat, lng], {icon: greyIcon}).addTo(mymap2);
        }
    }
    ////*****////
    function edit_fun(id) {
        //C:\Users\Bologna.DISIT\Desktop\dashboardSmartCity\img\outputPngIcons
        //mymap2.clearLayers();
            $.ajax({
                url: 'editorganization.php',
                data: {
                    action: 'select_data',
                    id: id
                },
                type: "POST",
                async: true,
                success: function (data) {
        //      
                var obj = JSON.parse(data);
                $('#edit_id').val(obj[0]['id']);
                $('#edit_name').val(obj[0]['organizationName']);
                $('#edit_kb').val(obj[0]['kbUrl']);
                $('#edit_zoom').val(obj[0]['zoomLevel']);
                $('#edit_drupal').val(obj[0]['drupalUrl']);
                $('#edit_welcome').val(obj[0]['welcomeUrl']);
                $('#edit_orgpage').val(obj[0]['orgUrl']);
                $('#edit_old_name').val(obj[0]['organizationName']);
                $('#edit_broker').val(obj[0]['broker']);
                $('#edit_orionIP').val(obj[0]['orionIP']);
                $('#edit_orthomapJson').val(obj[0]['orthomapJson']);
                $('#edit_kbIP').val(obj[0]['kbIP']);
        //
        var coord = obj[0]['gpsCentreLatLng'];
        var myArr = coord.split(",");
        var lat1 = myArr[0];
        console.log("lat1 " + lat1);
        var lng1 = myArr[1];
        console.log("lng1 " + lng1);
        $('#edit_lat').val(lat1);
        $('#edit_lng').val(lng1);
        // mymap2.removeLayer(markers);
        mymap2.setView([lat1, lng1], obj[0]['zoomLevel']);
        var greyIcon = new L.Icon({
            iconUrl: '../img/outputPngIcons/pin-generico.png',
            iconAnchor: [lat1, lng1]
        });
        var marker = L.marker([lat1, lng1], {icon: greyIcon}).addTo(mymap2);
        //
    }
    });
    }
    ///////
    function list_user(org) {
        $('#new_user_org').val(org);
        var users = $('#'+org+'_users').text();
        var array = JSON.parse(users);
        console.log(array);
        var len = array.length;
                $("#users_table tbody tr").remove();
                for (var i = 0; i < len; i++) {
                    var current = array[i];
                    //var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#" onclick="edit_user('+id+',\''+current+'\')" data-toggle="modal">EDIT</button>';
                    var button_del = '<button type="button" class="delDashBtn delete_file" data-target="#" onclick="delete_user(0,\'' + current + '\',\'' + org + '\')"  data-toggle="modal">DELETE</button>';
                    $("#users_table").append('<tr><td>' + current + ' ' + button_del + '</td></tr>');
                }
               
        /*$.ajax({
            url: 'editorganization.php',
            data: {
                action: 'user_list',
                id: id
            },
            type: "POST",
            async: false,
            success: function (data) {
                var obj = JSON.parse(data);
                var array = obj[0]['users'];
                var len = array.length;
                $("#users_table tbody tr").remove();
                for (var i = 0; i < len; i++) {
                    var current = array[i];
                    //var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#" onclick="edit_user('+id+',\''+current+'\')" data-toggle="modal">EDIT</button>';
                    var button_del = '<button type="button" class="delDashBtn delete_file" data-target="#" onclick="delete_user(' + id + ',\'' + current + '\',\'' + org + '\')"  data-toggle="modal">DELETE</button>';
                    $("#users_table").append('<tr><td>' + current + ' ' + button_del + '</td></tr>');
                }
                $('#new_user_id').val(id);
                $('#new_user_org').val(org);
                ///////
                /////////
            }
        });*/
    }
    ////
    function delete_func(id, name) {
        $('#delete_id').val(id);
        $('#delete_name').val(name);
    }



    function delete_user(id, user, org) {
        console.log(id);
        $.ajax({
            url: 'editorganization.php',
            data: {
                action: 'delete_list',
                id: id,
                user: user,
                org: org
            },
            type: "POST",
            async: false,
            success: function (data) {
                alert(data);
                window.location.reload();
                /////////
            }
        });
    }

    function delete_group(group, org) {
        //console.log(id);
        $.ajax({
            url: 'editorganization.php',
            data: {
                action: 'delete_group',
                group: group,
                org: org
            },
            type: "POST",
            async: false,
            success: function (data) {
                alert(data);
                window.location.reload();
                /////////
            }
        });
    }

    function list_group(org){
       // console.log(id);
        $('#org_name').text(org);
        //groupOfNames
        $.ajax({
            url: 'editorganization.php',
            data: {
                action: 'get_gropus',
                org: org
            },
            type: "POST",
            async: false,
            success: function (data) {
                //alert(data);
                var obj = JSON.parse(data);
                var array = obj;
                var len = array.length;
                //window.location.reload();
                $("#group_table tbody tr").remove();
                for (var i = 0; i < len; i++) {
                    var current = array[i];
                    //var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#" onclick="edit_user('+id+',\''+current+'\')" data-toggle="modal">EDIT</button>';
                    var button_del = '<button type="button" class="delDashBtn delete_file" data-target="#" onclick="delete_group(\'' + current + '\',\'' + org + '\')"  data-toggle="modal">DELETE</button>';
                    $("#group_table").append('<tr><td>' + current + ' ' + button_del + '</td></tr>');
                }
                /////////
            }
        });
    }

    function user_group(org){

        $('#new_dtUser_org').val(org);
        var users = $('#'+org+'_users').text();
        var dtUsers = $('#'+org+'_dtUsers').text();
    //var array = JSON.parse(users);
    var array2 = dtUsers.split(',');
    var array = JSON.parse(users);
        //console.log(JSON.parse(array2));
        var len = array.length;
                $("#dtUsers_table tbody tr").remove();
                for (var i = 0; i < len; i++) {
                    var current = array[i];
                    var checked = '';
                    //var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#" onclick="edit_user('+id+',\''+current+'\')" data-toggle="modal">EDIT</button>';
                   // var button_del = '<button type="button" class="delDashBtn delete_file" data-target="#" onclick="delete_user(0,\'' + current + '\',\'' + org + '\')"  data-toggle="modal">DELETE</button>';
                   for(var y=0; y< array2.length; y++){
                        //console.log(array2[y]);
                        if (array2[y] == current){
                            console.log(current);
                            checked = 'checked';
                           // $("#dtUsers_table").append('<tr><td>' + current + '     <input class="form-check-input dtUser" type="checkbox" value="' + current + '" checked></td></tr>');
                        }
                   }
                   $("#dtUsers_table").append('<tr><td>' + current + '     <input class="form-check-input dtUser" type="checkbox" value="' + current + '" '+checked+'></td></tr>');
                    
                }

    }

    $("#myModal_new").on('shown.bs.modal', function (e) {
        setTimeout(function () {
            mymap.invalidateSize();
        }, 0);
    });

    $("#edit_org").on('shown.bs.modal', function (e) {
        setTimeout(function () {
            mymap2.invalidateSize();
        }, 0);
    });

    $('#myModal_new').on('hidden.bs.modal', function () {
        $(".leaflet-marker-icon").remove();
        $(".leaflet-popup").remove();
    });

    $('#edit_org').on('hidden.bs.modal', function () {
        $(".leaflet-marker-icon").remove();
        $(".leaflet-popup").remove();
    });
</script>

</body>
</html>

<?php
} else {
   include('../s4c-legacy-management/organizationmanager.php');
}
?>