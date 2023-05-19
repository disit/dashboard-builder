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

if (!isset($_SESSION)) {
    session_start();
}

if ((!$_SESSION['isPublic'] && isset($_SESSION['newLayout']) && $_SESSION['newLayout'] === true) || ($_SESSION['isPublic'] && $_COOKIE['layout'] == "new_layout")) {

include('process-form.php');
header("Cache-Control: private, max-age=$cacheControlMaxAge");

//session_start();
checkSession('RootAdmin');

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

    <title>MenuManager</title>
    
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

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="../css/s4c-css/fontawesome-free-6.2.0-web/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="../css/s4c-css/s4c-dashboard.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/s4c-css/s4c-dashboardList.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/s4c-css/s4c-dashboardView.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/s4c-css/s4c-addWidgetWizard2.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/s4c-css/s4c-addDashboardTab.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/s4c-css/s4c-dashboard_configdash.css?v=<?php echo time();?>" rel="stylesheet">
    <link href="../css/s4c-css/s4c-iotApplications.css?v=a" rel="stylesheet">
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
        backbground-color: #337ab7;
        color: white;
    }

    .paginate_button{
        backbground-color: #337ab7;
    }

    #dropdownMenuButton{
        float: left
    }

    #value_table{
        height: 90%;
        width: 90%;
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
                <button type="button" class="btn btn-warning new_rule" data-toggle="modal" data-target="#myModal_new" style="float:left; margin-right: 5px;">
                    <i class="fa fa-plus"></i> 
                    Create New Text translation
                </button>
                <!-- 	<div style="display:none;"> -->
                <div style="display:none;">
                    <input id="checked_menu" type="text" value="select_all"></input>
                    <input id="checked_dom" type="text"  value="select_all"></input>
                </div>
                <!--</div>-->
                <!-- -->
                <button type="button" id="import_data" class="btn btn-warning import_menu" data-toggle="modal" data-target="#myModal_import" style="float:left; margin-right: 5px;">
                    <i class="fa fa-language"></i>
                    Import menu
                </button>
                <!---- ------->
                <div class="dropdown" style="display: inline; float:left; margin-left: 5px;">
                    <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Filter by language <span class="caret">
                        </span></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="domain_list">

                        <li><a href="#"><input class="select_check check_domain" type="checkbox" name="SelectAll" value="select_all" id="checkdom_select_all" onclick="filterLang('select_all')">Select all</a>
                        </li>
                        <li class="divider">

                        </li>
                        <?php
                        $obj = json_decode($localizations, true);
                        //echo ($localizations);
                        $languages = $obj['languages'];
                        $tot_leng = count($languages);
                        if ($tot_leng > 0) {
                            for ($i = 0; $i < $tot_leng; $i++) {
                                $lang = $obj['languages'][$i]['code'];
                                //echo ('<option value="' . $lang . '">' . $lang . '</option>');
                                echo('<li><a href="#">
                                <input class="select_check check_lang" type="checkbox" name="value" value="' . $lang . '" onclick="filterLang(\'' . $lang . '\')">' . $lang . '</a>
                        </li>');
                            }
                        }
                        ?>

                    </div>
                </div>
                <!--- --->
                <!--IMPORT --> 
                <button type="button" id="import_file" class="btn btn-warning import_file" data-toggle="modal" data-target="#import_file_modal" style="float:left; margin-left: 5px;">
                    <i class="fa fa-upload"></i>
                    Import file
                </button>
                <!--EXPORT -->
                <button type="button" id="export_file" class="btn btn-warning export_file" data-toggle="modal" data-target="#" style="float:left; margin-left: 5px;">
                    <i class="fa fa-download"></i>
                    Export file
                </button>
                <!-- -->
            </div>

            <!-- -->
            <div id="table_div" style="margin-left: 5%; margin-right: 5%">
                <!-- -->
                <table id="value_table" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="dashboardsTableHeader">
                        <tr>
                            <th>Id</th>
                            <th>Reference Text</th>
                            <th>Language</th>
                            <th>Translated Text</th>
                            <th>Edit</th> 
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Fine modal content -->
    <!-- Modal -->
    <div id="myModal_new" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add new translation</h4>
                </div>
                <div class="modal-body">

                    <div class="input-group">
                        <span class="input-group-addon" readonly>Reference Text:  </span>

                        <textarea  id="reference" class="form-control" aria-label="With textarea"></textarea>
                    </div><br />
                    <!--<div class="input-group edit_plu"><span class="input-group-addon">Reference Text:  </span><input id="reference" name="publicLinkUrl" type="textarea" class="form-control" /></div><br class='edit_plu'/>-->
                    <div class="input-group"><span class="input-group-addon">Language: </span>
                        <!--<input id="icon"  type="text" class="form-control" />-->
                        <select id="icon_e" name="select_mode" class="form-control">
                            <?php
                            $obj = json_decode($localizations, true);
                            echo ($localizations);
                            $languages = $obj['languages'];
                            $tot_leng = count($languages);
                            if ($tot_leng > 0) {
                                for ($i = 0; $i < $tot_leng; $i++) {
                                    $lang = $obj['languages'][$i]['code'];
                                    echo ('<option value="' . $lang . '">' . $lang . '</option>');
                                }
                            }
                            ?>
                        </select>
                        <!-- -->
                    </div><br /> 
                    <!--
                        <div class="input-group edit_plu"><span class="input-group-addon">Translated text: </span><input id="translate" name="publicLinkUrl" type="textarea" class="form-control" /></div><br class='edit_plu'/>
                    -->
                    <div class="input-group">
                        <span class="input-group-addon">Translated text: </span>

                        <textarea  id="translate" class="form-control" aria-label="With textarea"></textarea>
                    </div><br />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <!--<input type="buton" class="btn btn-primary" id="create_rule" value="Confirm" />-->
                    <input type="button" id="create_rule" value="Confirm" class="btn confirmBtn" />
                </div>
            </div>
        </div>
    </div>
    <!--</div> <!-- Fine modal dialog -->
    <!--</div><!-- Fine modale -->
    <!-- Fine modale wizard -->
    <div id="edit-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit translation</h4>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3" style="display: none;">
                        <input type="text" id="id_element" placeholder="id_element" class="form-control" aria-describedby="basic-addon1"><br />
                    </div>
                    <div class="input-group">

                        <span class="input-group-addon">Reference Text:  </span>

                        <textarea  id="reference_edit" class="form-control" aria-label="With textarea" readonly></textarea>
                    </div><br />
                    <!--<div class="input-group edit_plu"><span class="input-group-addon">Reference Text:  </span><input id="reference" name="publicLinkUrl" type="textarea" class="form-control" /></div><br class='edit_plu'/>-->
                    <div class="input-group"><span class="input-group-addon">Language: </span>
                        <!--<input id="icon"  type="text" class="form-control" />-->
                        <select id="icon_edit" name="select_mode" class="form-control">
                            <?php
                            $obj = json_decode($localizations, true);
                            echo ($localizations);
                            $languages = $obj['languages'];
                            $tot_leng = count($languages);
                            if ($tot_leng > 0) {
                                for ($i = 0; $i < $tot_leng; $i++) {
                                    $lang = $obj['languages'][$i]['code'];
                                    echo ('<option value="' . $lang . '">' . $lang . '</option>');
                                }
                            }
                            ?>
                        </select>
                        <!-- -->
                    </div><br /> 
                    <!--
                        <div class="input-group edit_plu"><span class="input-group-addon">Translated text: </span><input id="translate" name="publicLinkUrl" type="textarea" class="form-control" /></div><br class='edit_plu'/>
                    -->
                    <div class="input-group">
                        <span class="input-group-addon">Translated text: </span>

                        <textarea  id="translate_edit" class="form-control" aria-label="With textarea"></textarea>
                    </div><br />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="button" class="btn btn-primary" id="edit_rule" value="Confirm" />
                </div>
                <!--</form>	-->
            </div>

        </div>
    </div>
    <!-- DELETE -->
    <div class="modal fade fade bd-example-modal-lg" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" >Delete Element</h4>
                </div>
                <form method="post" id="delete_element"  accept-charset="UTF-8">
                    <div class="modal-body">
                        Are you sure you want delete this element from Menu?
                    </div>
                    <input id="delete_id" type="text" name="id" style="display: none;"></input>
                    <input id="table_delete" type="text" name="rable" style="display: none;"></input>
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                        <input type="button" id="delete_command" value="Confirm" class="btn confirmBtn"/>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="modal fade fade bd-example-modal-lg" id="confirm_copy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" >Confirm Copy Table</h4>
                </div>
                <form method="post" accept-charset="UTF-8">
                    <div class="modal-body">
                        Are you sure you want copy this table?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                        <input type="button" id="confirm_copy_command" value="Confirm" class="btn confirmBtn"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--  -->
    <div class="modal fade fade bd-example-modal-lg" id="result_copy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" >Result copy table</h4>
                </div>
                <form method="post" accept-charset="UTF-8">
                    <div class="modal-body" id="text_copy_table">
                        <div class="input-group edit_plu"><span class="input-group-addon">Reference Text;  </span><input id="reference" name="publicLinkUrl" type="text" class="form-control" /></div><br class='edit_plu'/>
                        <div class="input-group edit_plu"><span class="input-group-addon">Translated text: </span><input id="translate" name="publicLinkUrl" type="text" class="form-control" /></div><br class='edit_plu'/>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" data-dismiss="modal" id="close_copy">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- -->
    <!-- Modal -->
    <div id="myModal_import" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import menu</h4>
                </div>
                <div class="modal-body">
                    <div class="input-group"><span class="input-group-addon">Select menu type: </span>
                    <!--<input id="icon"  type="text" class="form-control" />-->
                        <select id="select_import" name="select_mode" class="form-control">
                            <option>MainMenu</option>
                            <option>MainMenuSubmenus</option>
                            <option>OrgMenu</option>
                            <option>OrgMenuSubmenus</option>
                            <option>MobMainMenu</option>
                            <option>MobMainMenuSubmenus</option>
                            <!-- ---->
                        </select>
                        <!-- -->
                    </div><br />
                    <div class="input-group"><span class="input-group-addon">Translate in language: </span>
                    <!--<input id="icon"  type="text" class="form-control" />-->
                        <select id="icon_import" name="select_mode" class="form-control">
                            <?php
                            $obj = json_decode($localizations, true);
                            echo ($localizations);
                            $languages = $obj['languages'];
                            $tot_leng = count($languages);
                            if ($tot_leng > 0) {
                                for ($i = 0; $i < $tot_leng; $i++) {
                                    $lang = $obj['languages'][$i]['code'];
                                    echo ('<option value="' . $lang . '">' . $lang . '</option>');
                                }
                            }
                            ?>
                        </select>
                        <!-- -->
                    </div><br />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <!--<input type="buton" class="btn btn-primary" id="create_rule" value="Confirm" />-->
                    <input type="button" id="import_rule" value="Confirm" class="btn confirmBtn" />
                </div>
            </div>
        </div>
    </div>
    <!--</div> <!-- Fine modal dialog -->
    <!-- IMPORT MENU-->
    <div class="modal fade fade bd-example-modal-lg" id="import_file_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" >Import File</h4>
                </div>
                <!-- <form action="translatemenu.php" method="post" enctype="multipart/form-data">-->
                <form  id="form_import" action="translationfileimport.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="input-group"><span class="input-group-addon">Language: </span>
                            <!--<input id="icon"  type="text" class="form-control" />-->
                            <select id="import_lang" name="select_mode" class="form-control">
                                <?php
                                $obj = json_decode($localizations, true);
                                echo ($localizations);
                                $languages = $obj['languages'];
                                $tot_leng = count($languages);
                                if ($tot_leng > 0) {
                                    for ($i = 0; $i < $tot_leng; $i++) {
                                        $lang = $obj['languages'][$i]['code'];
                                        echo ('<option value="' . $lang . '">' . $lang . '</option>');
                                    }
                                }
                                ?>
                            </select>
                            <!-- -->
                        </div><br /> 
                        <label for="formFileDisabled" class="form-label">Import an csv or xlsx file:</label>
                                                <div class="panel panel-default">
                                                    <div class="panel-body">The file must have two columns named <b>"Reference Text"</b> and <b>"Translated Text"</b> and the only accepted fromat are .csv and .xlsx</b></div></div>
                        <input class="form-control" type="file" name="formFileDisabled" id="formFileDisabled" accept=".csv, .xlsx"/>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                        <input type="button" name="button" value="Confirm" class="btn confirmBtn" id="Confirm_import"/>

                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- EXPORT MENU-->
    <div class="modal fade fade bd-example-modal-lg" id="export_file_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" >Export File</h4>
                </div>
                <div class="modal-body">
                    <form method="post" id="edit_export_menu"  accept-charset="UTF-8">
                        Are you sure you want to download a copy of the translation table?
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                    <a id="download_link" href="translatemenu.php?action=download"><input type="button" value="Download" class="btn confirmBtn" id="download_file" /></a>
                </div>
            </div>
        </div>

    </div>
    <!-- -->
    <script type='text/javascript'>
        //***//
        $(document).ready(function () {
            $.ajax({
                async: true,
                type: 'GET',
                url: 'translatemenu.php',
                data: {
                    action: 'get_data'
                            //lang: lang
                },
                success: function (data) {
                    //console.log(data);
                    //var edit_button = "";

                    var results = JSON.parse(data);
                    console.log(results);
                    var lun = results.length;
                    console.log(lun);
                    for (var i = 0; i < lun; i++) {
                        var id_ed = results[i]['id'];
                        var menuText_ed = results[i]['menuText'];
                        var language_ed = results[i]['language'];
                        var translatedText_ed = results[i]['translatedText'];
                        //
                        var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#" data-toggle="modal" value="' + results[i]['id'] + '" onclick=\'function_edit("' + id_ed + '")\'>EDIT</button> ';
                        $('#value_table tbody').append('<tr><td id=' + results[i]['id'] + '>' + results[i]['id'] + '</td><td>' + results[i]['menuText'] + '</td><td>' + results[i]['language'] + '</td><td>' + results[i]['translatedText'] + '</td><td>' + button_edit + '</td></tr>');
                    }
                    //////////////
                    var table = $('#value_table').DataTable({
                        "searching": true,
                        "paging": true,
                        "ordering": true,
                        "info": false,
                        "responsive": true,
                        "lengthMenu": [5, 10, 20, 50],
                        "iDisplayLength": 20,
                        "pagingType": "full_numbers",
                        "dom": '<"pull-left"l><"pull-right"f>tip',
                        "language": {"paginate": {
                                "first": "First",
                                "last": "Last",
                                "next": "Next >>",
                                "previous": "<< Prev"
                            },
                            "lengthMenu": "Show	_MENU_ ",
                        }
                    });
                    ////////////////
                }
            });

            ////////////////*************//////////
            //Add element create_rule
            $('#create_rule').click(function () {
                var reference = $('#reference').val();
                var icon_e = $('#icon_e').val();
                var translate = $('#translate').val();
                $.ajax({
                    async: true,
                    type: 'POST',
                    url: 'translatemenu.php',
                    data: {
                        action: 'add',
                        reference: reference,
                        icon_e: icon_e,
                        translate: translate
                                //lang: lang
                    },
                    success: function (data) {
                        var message = data;
                        //alert('Translation added successfully');
                        if (message == 'Error') {
                            alert('Error during element creation');
                        }
                        if (message == 'Duplicate') {
                            alert('Yet existing element');
                        } else {
                            location.reload();
                        }
                    }


                });
            });
            /////////////////////////////*********///
            /*$('#import_data').click(function () {
             $.ajax({
             async: true,
             type: 'POST',
             url: 'translatemenu.php',
             data: {
             action: 'menulist'
             //lang: lang
             },
             success: function (data) {
             console.log('ciao!');
             //alert('Translation modified successfully');
             //location.reload();
             }
             });
             });*/
            $('#export_file').click(function () {
                $('#export_file_modal').modal('show');
                /*$.ajax({
                    async: true,
                    type: 'POST',
                    url: 'translatemenu.php',
                    data: {
                        action: 'download',
                        lang: ''
                                //lang: lang
                    },
                    success: function (data) {

                        //alert('Translation modified successfully');
                        //$('#download_link').attr({target: '_blank',
                          //  href: 'file.csv', download: 'file.csv'});
                        
                        // location.reload();
                    }
            });*/
            });

            ////****///edit_rule
            $('#edit_rule').click(function () {
                //
                ///
                var reference_edit = $('#reference_edit').val();
                var icon_edit = $('#icon_edit').val();
                var translate_edit = $('#translate_edit').val();
                var id_element = $('#id_element').val();
                $.ajax({
                    async: true,
                    type: 'POST',
                    url: 'translatemenu.php',
                    data: {
                        action: 'edit',
                        reference: reference_edit,
                        icon_e: icon_edit,
                        translate: translate_edit,
                        id_element: id_element
                                //lang: lang
                    },
                    success: function (data) {
                        //alert('Translation modified successfully');
                        var message = data;
                        //alert('Translation added successfully');
                        if (message == 'Error') {
                            alert('Error during element creation ');
                        }
                        if (message == 'Duplicate') {
                            alert('Yet existing element');
                        } else {
                            location.reload();
                        }
                    }
                });
            });
            //***//
            //edit_file
            $(".edit_file").click(function () {
                //alert("Handler for .click() called.");
            });
            //////////create_rule
            $("#import_rule").click(function () {
                ////////////////alert("CIAO!");
                var icon_import = $('#icon_import').val();
                var select_import = $('#select_import').val();
                //
                $.ajax({
                    async: true,
                    type: 'POST',
                    url: 'translatemenu.php',
                    data: {
                        action: 'import',
                        lang: icon_import,
                        select: select_import
                    },
                    success: function (data) {
                        //alert('Translation modified successfully');
                        var message = data;
                        //alert('Translation added successfully');
                        if (message == 'Error') {
                            alert('Error during element creation');
                        }
                        if (message == 'Duplicate') {
                            alert('Yet existing element');
                        } else {
                            location.reload();
                        }
                    }
                });
                ///////////////
            });
            //***//
        });
//download_file
        $("#download_file").click(function () {
            /*$.ajax({
                async: true,
                type: 'POST',
                url: 'translatemenu.php',
                data: {
                    action: 'delete_downloadfile'
                },
                success: function (data) {
                    //alert('Translation modified successfully');
                    //location.reload();
                }
            });*/
        });
        $("#export_file_modal").on("hidden.bs.modal", function () {
            // put your default event here
           /* $.ajax({
                async: true,
                type: 'POST',
                url: 'translatemenu.php',
                data: {
                    action: 'delete_downloadfile'
                },
                success: function (data) {
                    //alert('Translation modified successfully');
                    //console.log('deleted');
                }
            });*/
        });

        $("#Confirm_import").click(function () {
            //alert('prova');
            var import_lang = $("#import_lang").val();
            //
            var myFile = $('#formFileDisabled').prop('files')[0];
            console.log(myFile);
            //
            var fd = new FormData();
            var formFileDisabled = $('#formFileDisabled')[0].files;
            fd.append('file', formFileDisabled[0]);
            //alert(myFile);
            // }
            // var fd = new FormData('#form_import');
            console.log(formFileDisabled);
            $.ajax({
                async: true,
                type: 'POST',
                url: 'translationfileimport.php?lang=' + import_lang,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                data: fd,
                success: function (data) {
                    //var obj = JSON.parse(data);
                    //console.log(data);
                    //alert(obj);
                    var message = data;
                    //alert('Translation added successfully');
                    if (message == 'Error') {
                        alert('Error during file upload');
                    } else if(message == 'Not valid keys'){
                        alert('Not valid column names in file');
                    }else if (message == 'not file') {
                        alert('Error during file upload');
                    }else if (message == 'correct'){                    
                            location.reload();
                    }else{
                        location.reload();
                    }
                }
            });
            //}
        });


        function function_edit(id) {
            //
            var yourArray = new Array();
            $("input:checkbox[name=type]:checked").each(function () {
                yourArray.push($(this).val());
            });
            //AGGIUNTA AJAX
            $.ajax({
                async: true,
                type: 'POST',
                url: 'translatemenu.php',
                data: {
                    action: 'get_data',
                    id_el: id
                },
                success: function (data) {
                    var results = JSON.parse(data);
                    console.log(results);
                    var lun = results.length;
                    console.log(lun);
                    for (var i = 0; i < lun; i++) {
                        var id_ed = results[i]['id'];
                        var menuText_ed = results[i]['menuText'];
                        var language_ed = results[i]['language'];
                        var translatedText_ed = results[i]['translatedText'];
                        //
                        $('#id_element').val(id_ed);
                        // var translatedText = $().attr
                        $('#reference_edit').text(menuText_ed);
                        $('#icon_edit').val(language_ed);
                        $('#translate_edit').val(translatedText_ed);
                        $('#edit-modal').modal('show');
                    }
                }
            });
            //
            //alert(id);   // The function returns the product of p1 and p2

        }//translatedText
        //
        //
        function filterLang(lang) {
            //array selected langs
            $('.select_check').removeAttr('checked');
            $("input[value='" + lang + "']").prop('checked', true);
            //$('.check_lang').removeAttr('checked');
            if (lang == "select_all") {
                lang = "";
            }

            //
            $('#value_table tbody').empty();
            $('#value_table').DataTable().clear().destroy();
            $.ajax({
                async: true,
                type: 'GET',
                url: 'translatemenu.php',
                data: {
                    action: 'get_data',
                    lang: lang
                },
                success: function (data) {
                    //console.log(data);
                    //var edit_button = "";

                    var results = JSON.parse(data);
                    console.log(results);
                    var lun = results.length;
                    console.log(lun);
                    for (var i = 0; i < lun; i++) {
                        var id_ed = results[i]['id'];
                        var menuText_ed = results[i]['menuText'];
                        var language_ed = results[i]['language'];
                        var translatedText_ed = results[i]['translatedText'];
                        //
                        var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#" data-toggle="modal" value="' + results[i]['id'] + '" onclick=\'function_edit("' + id_ed + '")\'>EDIT</button> ';
                        $('#value_table tbody').append('<tr><td id=' + results[i]['id'] + '>' + results[i]['id'] + '</td><td>' + results[i]['menuText'] + '</td><td>' + results[i]['language'] + '</td><td>' + results[i]['translatedText'] + '</td><td>' + button_edit + '</td></tr>');
                    }
                    //////////////
                    var table = $('#value_table').DataTable({
                        "searching": true,
                        "paging": true,
                        "ordering": true,
                        "info": false,
                        "responsive": true,
                        "lengthMenu": [5, 10, 20, 50],
                        "iDisplayLength": 20,
                        "pagingType": "full_numbers",
                        "dom": '<"pull-left"l><"pull-right"f>tip',
                        "language": {"paginate": {
                                "first": "First",
                                "last": "Last",
                                "next": "Next >>",
                                "previous": "<< Prev"
                            },
                            "lengthMenu": "Show	_MENU_ ",
                        }
                    });
                    ////////////////
                }
            });
        }
        /////
    </script>
</body>

</html>

<?php } else {
    include('../s4c-legacy-management/translationmanager.php');
}
?>