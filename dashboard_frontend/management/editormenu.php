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

   include_once('../config.php');

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
    
    

    <!-- Bootstrap Core CSS -->
    <link href="../css/s4c-css/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="../css/s4c-css/bootstrap/bootstrap-colorpicker.min.css" rel="stylesheet">
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
    <?php include "theme-switcher.php" ?> 
    
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
                <button type="button" class="btn btn-warning new_rule" data-toggle="modal" data-target="#myModal_new" style="float:left; margin-right: 5px;" onclick="newData()">
                    <i class="fa fa-plus"></i> 
                    Create New Menu element
                </button>
                <!-- 	<div style="display:none;"> -->
                <div style="display:none;">
                    <input id="checked_menu" type="text" value="select_all"></input>
                    <input id="checked_dom" type="text"  value="select_all"></input>
                </div>
                <!--</div>-->
                <div class="dropdown" style="display: inline; float:left; ">
                    <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Filter by type of Menu <span class="caret">
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a href="#"><input class="select_check check_org" type="checkbox" name="SelectAll" value="all" onclick="filtroData('select_all')" id="select_all">Select all</a></li>
                        <li class="divider"></li>
                        <li><a href="#"><input class="select_check check_org" type="checkbox" name="nature" value="MainMenu" onclick="filtroData('sel_MainMenu')" id="sel_MainMenu">MainMenu</a></li>
                        <li><a href="#"><input class="select_check check_org" type="checkbox" name="value type" value="MainMenuSubmenus" onclick="filtroData('sel_MainMenuSubmenus')" id="sel_MainMenuSubmenus">MainMenuSubmenus</a></li>
                        <li class="divider"></li>
                        <li><a href="#"><input class="select_check check_org" type="checkbox" name="subnature" value="OrgMenu" onclick="filtroData('sel_OrgMenu')" id="sel_OrgMenu">OrgMenu</a></li>
                        <li><a href="#"><input class="select_check check_org" type="checkbox" name="value type" value="OrgMenuSubmenus" onclick="filtroData('sel_OrgMenuSubmenus')" id="sel_OrgMenuSubmenus">OrgMenuSubmenus</a></li>
                        <li class="divider"></li>
                        <li><a href="#"><input class="select_check check_org" type="checkbox" name="value type" value="MobMainMenu" onclick="filtroData('sel_MobMainMenu')" id="sel_MobMainMenu">MobMainMenu</a></li>
                        <li><a href="#"><input class="select_check check_org" type="checkbox" name="value type" value="MobMainMenuSubmenus" onclick="filtroData('sel_MobMainMenuSubmenus')" id="sel_MobMainMenuSubmenus">MobMainMenuSubmenus</a></li>
                    </div>
                </div>
                <div class="dropdown" style="display: inline; float:left; margin-left: 5px;">
                    <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Filter by Domain <span class="caret">
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="domain_list">

                    </div>
                </div>

                <button type="button" class="btn btn-new-dash" data-toggle="modal" data-target="#copy_table" style="float:left; margin-left: 5px;" onclick="copyTable()">
                    <i class="fa fa-copy"></i> 
                    Copy Table
                </button>

            </div>
            <!-- -->
            <div id="table_div" style="margin-left: 5%; margin-right: 5%">
                <!-- -->
                <table id="value_table" class="table table-striped table-bordered" style="width: 100%">
                    <thead class="dashboardsTableHeader">
                        <tr>
                            <th>Id</th>
                            <th>Domain</th>
                            <th>Text</th>
                            <th>Link Url </th>
                            <th>pageTitle</th>
                            <th>Controls</th> 
                            <th>Public Link Url</th>
                            <th>LinkId</th>
                            <th>Type of menu</th>
                            <th>Menu</th>
                            <th>Icon</th>
                            <th>color</th>

                            <th>Privileges</th>
                            <!--<th>Parent Id</th>-->

                            <th>externalApp</th>
                            <th>organizations</th>
                            <th>openMode</th>
                            <th>menuOrder</th>

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
                    <h4 class="modal-title">Create New menu element</h4>
                </div>

                <div class="modal-body">
                    <div class="input-group"><span class="input-group-addon">Link Url: </span><input id="url" name="url" type="text" class="form-control" required/></div><br />                    
                    <div class="input-group new_plu"><span class="input-group-addon">public Link Url: </span><input id="publicLinkUrl" name="publicLinkUrl" type="text" class="form-control" /></div><br class="new_plu" />
                    <div class="input-group"><span class="input-group-addon">Link Id: </span><input id="linkid" name="user_c" type="text" class="form-control" required/></div><br />
                    <div class="input-group"><span class="input-group-addon" required>Icon: </span>
                        <!--<input id="icon"  type="text" class="form-control" />-->
                        <select id="icon" name="select_mode" class="form-control" onchange="select_icon('icon')">

                        </select>
                        <!-- -->
                    </div><br />
                    <div id="icon_new"></div>
                    <div class="input-group"><span class="input-group-addon">Color: </span><input id="color"  type="text" class="form-control" required/></div><br />
                    <div class="input-group"><span class="input-group-addon">Text: </span><input id="text"  type="text" class="form-control" /></div><br />
                    <div class="input-group"><span class="input-group-addon">Page title: </span><input id="pagetitle"  type="text" class="form-control" /></div><br />
                    <div class="input-group"><span class="input-group-addon">External App: </span>
                        <select id="externalApp"  type="select_mode" class="form-control" />
                        <option value="yes">yes</option>
                        <option value="no">no</option>
                        </select></div><br />
                    <div class="input-group"><span class="input-group-addon">Menu Order: </span><input id="menuorder"  type="number" class="form-control" required/></div><br />
                    <div class="input-group"><span class="input-group-addon">Domain: </span>
                        <select id="domain" name="select_mode" class="form-control">


                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon">Open mode: </span>

                        <select id="select_mode" name="select_mode" class="form-control">
                            <option  value="iframe">iframe</option>
                            <option  value="submenu">submenu</option>
                            <option  value="samePage">samePage</option>
                            <option  value="newTab">newTab</option>
                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon">Type of Menu: </span>

                        <select id="select_type_creation" name="select_type_creation" class="form-control">  
                            <option  value="MainMenu">MainMenu</option>
                            <option  value="MainMenuSubmenus">MainMenuSubmenus</option>
                            <option  value="MobMainMenu">MobMainMenu</option>
                            <option  value="MobMainMenuSubmenus">MobMainMenuSubmenus</option>
                            <option value="OrgMenu">OrgMenu</option>
                            <option value="OrgMenuSubmenus">OrgMenuSubmenus</option>
                        </select>
                    </div>
                    <div id="menu_code"></div>
                    <br />
                    <div class="input-group"><span><b>Privileges: </b></span>
                        <br />
                        <input class="form-check-input rol_sel" type="checkbox" value="RootAdmin" id="rootadmin"><span>RootAdmin</span><br />
                        <input class="form-check-input rol_sel" type="checkbox" value="ToolAdmin" id="tooladmin"><span>ToolAdmin</span><br />
                        <input class="form-check-input rol_sel" type="checkbox" value="AreaManager" id="areamanager"><span>AreaManager</span><br />
                        <input class="form-check-input rol_sel" type="checkbox" value="Manager" id="manager"><span>Manager</span><br />
                        <input class="form-check-input rol_sel" type="checkbox" value="Observer" id="observer"><span>Observer</span><br />
                        <input class="form-check-input rol_sel" type="checkbox" value="Public" id="public"><span>Public</span><br />
                    </div>
                    <br /> <div class="input-group"><span ><b>Organization: </b></span>
                        <div id="org_list">
                            <input class="form-check-input org_sel" type="checkbox" value="*" id="all_e"></input><span>*</span><br />
                        </div><br />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <!--<input type="buton" class="btn btn-primary" id="create_rule" value="Confirm" />-->
                        <input type="button" id="create_rule" value="Confirm" class="btn confirmBtn" />
                    </div>
                    <!--</form>	-->
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
                    <h4 class="modal-title">Edit menu element</h4>
                </div>
                <div class="modal-body">
                    <div class="input-group" style="display:none;"><span class="input-group-addon">Id: </span><input id="id_e"  type="text" class="form-control" readonly/></div><br />
                    <div class="input-group"><span class="input-group-addon">Link Url: </span><input id="url_e"  type="text" class="form-control" /></div><br />
                    <div class="input-group edit_plu"><span class="input-group-addon">public Link Url: </span><input id="publicLinkUrl_e" name="publicLinkUrl" type="text" class="form-control" /></div><br class='edit_plu'/>
                    <div class="input-group"><span class="input-group-addon">Link Id: </span><input id="linkid_e" name="user_c" type="text" class="form-control" /></div><br />
                    <!--<div class="input-group"><span class="input-group-addon">Icon: </span><input id="icon_e"  type="text" class="form-control" /></div><br />-->
                    <div class="input-group"><span class="input-group-addon">Icon: </span>
                        <!--<input id="icon"  type="text" class="form-control" />-->
                        <select id="icon_e" name="select_mode" class="form-control" onchange="select_icon('icon_e')">

                        </select>
                        <!-- -->
                    </div><br />
                    <div id="icon_e_new"></div>
                    <!-- -->
                    <div class="input-group"><span class="input-group-addon">Color: </span><input id="color_e"  type="text" class="form-control" /></div><br />
                    <div class="input-group"><span class="input-group-addon">Text: </span><input id="text_e"  type="text" class="form-control" /></div><br />
                    <div class="input-group"><span class="input-group-addon">Page title: </span><input id="pagetitle_e"  type="text" class="form-control" /></div><br />
                    <div class="input-group"><span class="input-group-addon">External App: </span>
                        <select id="externalApp_e"  type="select_mode" class="form-control" >
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon">Open mode: </span>

                        <select id="select_mode_c" name="select_mode_c" class="form-control">
                            <option  value="iframe">iframe</option>
                            <option  value="submenu">submenu</option>
                            <option  value="samePage">samePage</option>
                            <option  value="newTab">newTab</option>
                        </select>
                    </div><br />
                    <div id="domain_div"  class="input-group"><span class="input-group-addon">Domain: </span>
                        <select id="domain_e" name="select_mode" class="form-control">


                        </select>
                        <br />
                    </div>
                    <div class="input-group"><span class="input-group-addon">Menu Order: </span><input id="menuorder_e"   type="number" class="form-control" /></div><br />
                    <div class="input-group" style="display:none;"><span class="input-group-addon">Type of Menu: </span>

                        <select id="select_type_edit" name="select_type_edit" class="form-control" style="display:none;">  
                            <option class="menu_c" value="MainMenu">MainMenu</option>
                            <option class="menu_c" value="MainMenuSubmenus">MainMenuSubmenus</option>
                            <option class="menu_c" value="MobMainMenu">MobMainMenu</option>
                            <option class="menu_c" value="MobMainMenuSubmenus">MobMainMenuSubmenusu</option>
                            <option class="menu_c"value="OrgMenu">OrgMenu</option>
                            <option class="menu_c" value="OrgMenuSubmenus">OrgMenuSubmenus</option>
                        </select>
                    </div>
                    <div id="mainmenu_c">

                    </div>
                    <div class="input-group"><span><b>Privileges: </b></span>
                        <br />
                        <input class="form-check-input rol_sel_c" type="checkbox" value="RootAdmin" id="rootadmin_e"><span>RootAdmin</span><br />
                        <input class="form-check-input rol_sel_c" type="checkbox" value="ToolAdmin" id="tooladmin_e"><span>ToolAdmin</span><br />
                        <input class="form-check-input rol_sel_c" type="checkbox" value="AreaManager" id="areamanager_e"><span>AreaManager</span><br />
                        <input class="form-check-input rol_sel_c" type="checkbox" value="Manager" id="manager_e"><span>Manager</span><br />
                        <input class="form-check-input rol_sel_c" type="checkbox" value="Observer" id="observer_e"><span>Observer</span><br />
                        <input class="form-check-input rol_sel_c" type="checkbox" value="Public" id="public_e"><span>Public</span><br />
                    </div>
                    <br /><div class="input-group"><span ><b>Organization: </b></span>
                        <div id="org_list_c">
                                 <input class="form-check-input org_sel_c" type="checkbox" value="*" id="all_c" /><span>*</span><br />
                        </div><br />
                    </div>
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
    <!-- -->
    <div class="modal fade fade bd-example-modal-lg" id="copy_table" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" >Copy Table</h4>
                </div>
                <form method="post" id="copy_table_element"  accept-charset="UTF-8">
                    <div class="modal-body">
                        <div class="input-group"><span class="input-group-addon">Original Menu: </span>

                            <select class="form-control" readonly>  
                                <option class="menu_copy_orig" value="MainMenu">MainMenu/MainMenuSubmenus</option>
                            </select>
                        </div>
                        <br />
                        <div class="input-group"><span class="input-group-addon">Destination Menu: </span>

                            <select id="select_type_edit_dest" name="select_type_edit_dest" class="form-control">  
                                <option class="menu_copy_dest" value="MobMainMenu">MobMainMenu/MobMainMenuSubmenus</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" data-dismiss="modal">Cancel</button>
                        <input type="button" id="copy_table_command" value="Confirm" class="btn confirmBtn"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- -->
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
                        <div id="main_t1"></div>
                        <div id="main_s1"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancelBtn" data-dismiss="modal" id="close_copy">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- -->
    <!-- DELETE  add-modal-->
    <div class="modal fade fade bd-example-modal-lg" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add child to menu element</h4>
                </div>

                <div class="modal-body">
                    <div class="input-group"><span class="input-group-addon">Menu id: </span><input id="add_id" name="add_id" type="text" class="form-control" readonly/></div><br />                    
                    <div class="input-group"><span class="input-group-addon">Link Url: </span><input id="add_url"  type="text" class="form-control" /></div><br />
                    <div class="input-group add_plu"><span class="input-group-addon">public Link Url: </span><input id="add_publicLinkUrl" name="publicLinkUrl" type="text" class="form-control" /></div><br class="add_plu" />
                    <div class="input-group"><span class="input-group-addon">Link Id: </span><input id="add_plinkid" name="user_c" type="text" class="form-control" /></div><br />
                    <!--<div class="input-group"><span class="input-group-addon">Icon: </span><input id="add_picon"  type="text" class="form-control" /></div><br />-->
                    <div class="input-group"><span class="input-group-addon">Icon: </span>
                        <!--<input id="icon"  type="text" class="form-control" />-->
                        <select id="add_icon" name="select_mode" class="form-control" onchange="select_icon('add_icon')">

                        </select>
                        <!-- -->
                    </div><br />
                    <div id="add_icon_new"></div>
                    <div class="input-group"><span class="input-group-addon">Color: </span><input id="add_pcolor"  type="text" class="form-control" /></div><br />
                    <div class="input-group"><span class="input-group-addon">Text: </span><input id="add_ptext"  type="text" class="form-control" /></div><br />
                    <div class="input-group"><span class="input-group-addon">Page title: </span><input id="add_ppagetitle"  type="text" class="form-control" /></div><br />
                    <div class="input-group"><span class="input-group-addon">Type of menu: </span><input id="add_type"  type="text" class="form-control" readonly/></div><br />
                    <!--<div class="input-group"><span class="input-group-addon">External App: </span><input id="add_externalApp"  type="text" class="form-control" /></div><br />-->
                    <div class="input-group"><span class="input-group-addon">External App: </span>
                        <select id="add_externalApp"  type="select_mode" class="form-control" >
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </div><br />
                    <div class="input-group"><span class="input-group-addon">Menu Order: </span><input id="add_menuorder"  type="number" class="form-control" /></div><br />
                    <div class="input-group"><span class="input-group-addon">Open mode: </span>

                        <select id="add_pselect_mode" name="add_pselect_mode" class="form-control">
                            <option  value="iframe">iframe</option>
                            <option  value="submenu">submenu</option>
                            <option  value="samePage">samePage</option>
                            <option  value="newTab">newTab</option>
                        </select>
                    </div><br />
                    <div id="menu_code"></div>
                    <br />
                    <div class="input-group"><span><b>Privileges: </b></span>
                        <br />
                        <input class="form-check-input rol_sel_add" type="checkbox" value="RootAdmin" id="rootadmin"><span>RootAdmin</span><br />
                        <input class="form-check-input rol_sel_add" type="checkbox" value="ToolAdmin" id="tooladmin"><span>ToolAdmin</span><br />
                        <input class="form-check-input rol_sel_add" type="checkbox" value="AreaManager" id="areamanager"><span>AreaManager</span><br />
                        <input class="form-check-input rol_sel_add" type="checkbox" value="Manager" id="manager"><span>Manager</span><br />
                        <input class="form-check-input rol_sel_add" type="checkbox" value="Observer" id="observer"><span>Observer</span><br />
                        <input class="form-check-input rol_sel_add" type="checkbox" value="Public" id="public"><span>Public</span><br />
                    </div>
                    <br /> <div class="input-group"><span ><b>Organization: </b></span>
                        <div id="org_list_add">
                             <input class="form-check-input org_sel_c" type="checkbox" value="*" id="all_add"></input><span>*</span><br />
                        </div><br />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <!--<input type="buton" class="btn btn-primary" id="create_rule" value="Confirm" />-->
                        <input type="button" id="create_child" value="Confirm" class="btn confirmBtn" />
                    </div>
                    <!--</form>	-->
                </div>

            </div>
        </div>

    </div>
    <!-- -->
    <script type='text/javascript'>
        if ($(window).width() < 1200) {
            $('#right').css('float', 'left');
        }
        if ($(window).width() < 1534) {
            var width = $(window).width() - 20;
            width = width + 'px';
            $('#widgetWizardTableContainer').css('width', width);
            $('#widgetWizardTable').css('width', width);
        }
        /*if ($(window).width() < 1200) {
         var margin = document.getElementById('DCTemp1_24_widgetTimeTrend6351_div').style.margin;
         var margin = margin.substring(0, margin.length - 2);
         var width = $(window).width() - (parseInt(margin) * 2);
         var headerwidth = width - 60.75;
         var widthpx = width + 'px';
         var widgetCtxMenuBtnCntLeft = widthpx - $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").width();
         $('#timetrend').css('width', widthpx);
         
         $('#DCTemp1_24_widgetTimeTrend6351_div').css('width', widthpx);
         
         $('#DCTemp1_24_widgetTimeTrend6351_header').css('width', widthpx);
         
         $('#DCTemp1_24_widgetTimeTrend6351_titleDiv').css('width', Math.floor(headerwidth / width * 100) + "%");
         $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").css("left", widthpx);
         }*/

        //fa-circle



        //});

        $(window).resize(function () {
            if ($(window).width() < 1200) {
                $('#right').css('float', 'left');
            }

            if ($(window).width() > 1200) {
                $('#right').css('float', 'right');
            }
            if ($(window).width() < 1534) {
                var width = $(window).width() - 20;
                width = width + 'px';
                $('#widgetWizardTableContainer').css('width', width);
                $('#widgetWizardTable').css('width', width);
            }
            /*if ($(window).width() < 1200) {
             var margin = document.getElementById('DCTemp1_24_widgetTimeTrend6351_div').style.margin;
             var margin = margin.substring(0, margin.length - 2);
             var width = $(window).width() - (parseInt(margin) * 2);
             var headerwidth = width - 60.75;
             var widthpx = width + 'px';
             var widgetCtxMenuBtnCntLeft = widthpx - $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").width();
             $('#timetrend').css('width', widthpx);
             
             $('#DCTemp1_24_widgetTimeTrend6351_div').css('width', widthpx);
             
             $('#DCTemp1_24_widgetTimeTrend6351_header').css('width', widthpx);
             
             $('#DCTemp1_24_widgetTimeTrend6351_titleDiv').css('width', Math.floor(headerwidth / width * 100) + "%");
             $("#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt").css("left", widthpx);
             }
             if ($(window).width() > 1200) {
             $('#timetrend').css('width', '1200px');
             $('#DCTemp1_24_widgetTimeTrend6351_div').css('width', '1200px');
             $('#DCTemp1_24_widgetTimeTrend6351_header').css('width', '1200px');
             $('#DCTemp1_24_widgetTimeTrend6351_titleDiv').css('width', '95%');
             $('#DCTemp1_24_widgetTimeTrend6351_widgetCtxMenuBtnCnt').css('left', '1200px');
             }
             if ($(window).width() > 1534) {
             $('#widgetWizardTableContainer').css('width', '1534px');
             $('#widgetWizardTable').css('width', '1534px');
             }
             */
        });
        $(document).ready(function () {

            $("#color").colorpicker();
            $("#color_e").colorpicker();
            $("#add_pcolor").colorpicker();
            ///
            var array_act0 = new Array();
            $.ajax({
                async: true,
                type: 'POST',
                //url: url_parameters,
                //dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'list_domains'

                },
                success: function (data) {
                    //console.log('OK');
                    console.log(data);
                    var data0 = $.parseJSON(data);
                    var checkdom_select_all = "'select_all'";
                    $('#domain_list').append('<li><a href="#"><input class="select_check check_domain" type="checkbox" name="SelectAll" value="select_all" onclick="filtroDom(' + checkdom_select_all + ')" id="checkdom_select_all">Select all</a></li><li class="divider"></li>');
                    for (var i = 0; i < data0.length; i++)
                    {
                        array_act0[i] = {
                            id: data0[i]['id'],
                            claim: data0[i]['claim']
                        }
                        //var idcheckdom="'checkdom_1_"+array_act0[i]['id']+"'";
                        $('#domain_list').append('<li><a href="#"><input class="select_check check_domain" type="checkbox" name="value type" id="checkdom_' + array_act0[i]['id'] + '" value="' + array_act0[i]['id'] + '" onclick="filtroDom(' + array_act0[i]['id'] + ')">' + array_act0[i]['claim'] + '</a></li>');
                        $('#domain').append('<option value="' + array_act0[i]['id'] + '">' + array_act0[i]['claim'] + '</option>');
                        $('#domain_e').append('<option  value="' + array_act0[i]['id'] + '">' + array_act0[i]['claim'] + '</option>');
                        $('#add_domain').append('<option  value="' + array_act0[i]['id'] + '">' + array_act0[i]['claim'] + '</option>');
                        //
                    }
                }
            });
            ///
            var array_act = new Array();
            $.ajax({
                async: true,
                type: 'POST',
                //url: url_parameters,
                dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'list_menu',
                    sel_MainMenu: false,
                    sel_MainMenuSubmenus: false,
                    sel_OrgMenu: false,
                    sel_OrgMenuSubmenus: false,
                    sel_MobMainMenuSubmenus: false,
                    sel_MobMainMenu: false,
                    select_all: true,
                    domain: 'select_all'
                },
                success: function (data) {
                    //console.log(data);
                    //value_table
                    for (var i = 0; i < data.length; i++)
                    {
                        array_act[i] = {
                            id: data[i]['id'],
                            linkUrl: data[i]['linkUrl'],
                            publicLinkUrl: data[i]['publicLinkUrl'],
                            linkId: data[i]['linkId'],
                            icon: data[i]['icon'],
                            text: data[i]['text'],
                            privileges: data[i]['privileges'],
                            userType: data[i]['userType'],
                            externalApp: data[i]['externalApp'],
                            openMode: data[i]['openMode'],
                            iconColor: data[i]['iconColor'],
                            pageTitle: data[i]['pageTitle'],
                            domain: data[i]['domain'],
                            menuOrder: data[i]['menuOrder'],
                            organizations: data[i]['organizations'],
                            typemenu: data[i]['typemenu'],
                            submenu: data[i]['submenu'],
                            claim: data[i]['claim']
                                    //
                        };
                        //});
                        //
                        var button_submenu = '';
                        if ((array_act[i]['linkUrl'] === 'submenu') && ((array_act[i]['typemenu'] === 'MainMenu') || (array_act[i]['typemenu'] === 'MobMainMenu') || (array_act[i]['typemenu'] === 'OrgMenu'))) {
                            button_submenu = '<button type="button" class="viewDashBtn addchild" data-target="#add-modal" data-toggle="modal" value="' + i + '" onclick=addSubmenu(' + array_act[i]['id'] + ',"' + array_act[i]['typemenu'] + '")>ADD</button> ';
                        } else {
                            button_submenu = '';
                        }
                        //var json_data = JSON.stringify(data.healthiness);
                        var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#edit-modal" data-toggle="modal" value="' + i + '" onclick=editData(' + escape(array_act[i]['id']) + ',"' + escape(array_act[i]['linkUrl']) + '","' + escape(array_act[i]['publicLinkUrl']) + '","' + escape(array_act[i]['linkId']) + '","' + escape(array_act[i]['iconColor']) + '","' + escape(array_act[i]['pageTitle']) + '","' + escape(array_act[i]['typemenu']) + '","' + escape(array_act[i]['text']) + '","' + escape(array_act[i]['icon']) + '","' + escape(array_act[i]['organizations']) + '","' + escape(array_act[i]['privileges']) + '","' + escape(array_act[i]['menuOrder']) + '","' + escape(array_act[i]['externalApp']) + '","' + escape(array_act[i]['openMode']) + '","' + escape(array_act[i]['submenu']) + '","' + escape(array_act[i]['domain']) + '")>EDIT</button> ';
                        var button_del = '<button type="button" class="delDashBtn delete_file" data-target="#delete-modal" data-toggle="modal" value="' + i + '" onclick=deleteData(' + array_act[i]['id'] + ',"' + array_act[i]['typemenu'] + '")>DELETE</button> ';
                        //console.log(lun);
                        var controls = button_edit + button_submenu + button_del;
                        $('#value_table tbody').append('<tr><td>' + array_act[i]['id'] + '</td><td>' + array_act[i]['claim'] + '</td><td>' + array_act[i]['text'] + '</td><td>' + array_act[i]['linkUrl'] + '</td><td>' + array_act[i]['pageTitle'] + '</td><td>' + controls + '</td><td>' + array_act[i]['publicLinkUrl'] + '</td><td>' + array_act[i]['linkId'] + '</td><td>' + array_act[i]['typemenu'] + '</td><td>' + array_act[i]['submenu'] + '</td><td><i class="' + array_act[i]['icon'] + '"></i></td><td><i class="fa fa-circle" aria-hidden="true" style="color:' + array_act[i]['iconColor'] + '"></i>' + array_act[i]['iconColor'] + '</td><td>' + array_act[i]['privileges'] + '</td><td>' + array_act[i]['externalApp'] + '</td><td>' + array_act[i]['organizations'] + '</td><td>' + array_act[i]['openMode'] + '</td><td>' + array_act[i]['menuOrder'] + '</td></tr>');
                    }
                    /****/
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
                            "lengthMenu": "Show	_MENU_ ",
                        }
                    });
                    /***/
                    //$('.paginate_button').css('background-color','#337ab7');
                    /***/
                }
                //
            });
            $('#myModal_new').on('hidden.bs.modal', function (e) {
                //*$('#org_list').empty();
                $(".org_sel").attr("checked", false);
                $(".rol_sel").attr("checked", false);
                //
                //$('#id_e').val();
                $('#url').val();
                $('#publicLinkUrl').val();
                $('#icon').val();
                $('#color').val();
                $('#text').val();
                $('#pagetitle').val();
                $('#externalApp').val();
                $('#select_mode').val();
                $('#menuorder').val();
                $('#select_type_creation').val();
                $('#icon_new').empty();
                //
            });
            $('#edit-modal').on('hidden.bs.modal', function (e) {
                //$('#org_list_c').empty();
                $(".org_sel_c").attr("checked", false);
                $(".rol_sel_c").attr("checked", false);
                //
                $('#id_e').val();
                $('#url_e').val();
                $('#publicLinkUrl_e').val();
                $('#icon_e').val();
                $('#color_e').val();
                $('#text_e').val();
                $('#pagetitle_e').val();
                $('#externalApp_e').val();
                $('#select_mode_c').val();
                $('#menuorder_e').val();
                $('#select_type_edit').val();
                $('#icon_e_new').empty();
                //
            });
            //btn btn-warning new_rule
            //btn btn-warning new_rule
            // });
            // org_sel_c"
            // add-modal
            $('#add-modal').on('hidden.bs.modal', function (e) {
                //$('#org_list_c').empty();
                $(".org_sel_c").attr("checked", false);
                $(".rol_sel_add").attr("checked", false);
                $('#add_id').val();
                $('#add_url').val();
                $('#add_publicLinkUrl').val();
                $('#add_plinkid').val();
                $('#add_icon').val();
                $('#add_pcolor').val();
                $('#add_ptext').val();
                $('#add_ppagetitle').val();
                $('#add_type').val();
                $('#add_externalApp').val();
                $('#add_menuorder').val();
                $('#add_pselect_mode').val();
                $('#add_icon_new_text').empty();
            });
            //
            var array_act02 = new Array();
            $.ajax({
                async: true,
                type: 'GET',
                //url: url_parameters,
                //dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'list_orgs'
                },
                success: function (data) {
                    console.log(data);
                    data = data.replaceAll('"', '');
                    data = data.replaceAll('"', '');
                    data = data.replaceAll("'", "");
                    data = data.replace("[", "");
                    data = data.replace("]", "");
                    var data1 = data.split(/[,.]/);
                    //value_table
                    //var data1 = JSON.parse(data);
                    //console.log(data1);
                    for (var i = 0; i < data1.length; i++)
                    {
                        //console.log(data1[i])
                        $('#org_list').append('<input class="form-check-input org_sel n_ast_new" type="checkbox" value="' + data1[i] + '" id="' + data1[i] + '_e" /><span>' + data1[i] + '</span><br />');
                        $('#org_list_c').append('<input class="form-check-input org_sel_c n_ast" type="checkbox" value="' + data1[i] + '" id="' + data1[i] + '_c" /><span>' + data1[i] + '</span><br />');
                        $('#org_list_add').append('<input class="form-check-input org_sel_c n_ast_add" type="checkbox" value="' + data1[i] + '" id="' + data1[i] + '_c" /><span>' + data1[i] + '</span><br />');
                        /***/
                        /***/
                    }
                     $(".n_ast_new").click(function() {
                        console.log('clicked n_ast_new');
                                $("#all_e").prop( "checked", false );
                     });
                     $(".n_ast_add").click(function() {
                        console.log('clicked');
                        $('#all_add').prop( "checked", false );
                     });
                     $(".n_ast").click(function() {
                        console.log('clicked n_ast');
                                $("#all_c").prop( "checked", false );
                        //
                     });
                    //
                }
            });
            //$('#new_rule').click(function () {
            var array_act03 = new Array();
            $.ajax({
                async: true,
                type: 'GET',
                //url: url_parameters,
                //dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'list_icons'
                },
                success: function (data) {
                    console.log('ICONs');
                    //console.log(data);
                    data = data.replaceAll('"', '');
                    data = data.replaceAll('"', '');
                    data = data.replaceAll("'", "");
                    data = data.replace("[", "");
                    data = data.replace("]", "");
                    var data1 = data.split(/[,.]/);
                    for (var i = 0; i < data1.length; i++)
                    {
                        $('#icon').append('<option  value="' + data1[i] + '"><i class="' + data1[i] + '" style="color:black;"></i>   ' + data1[i] + '</option>');
                        $('#icon_e').append('<option  value="' + data1[i] + '"><i class="' + data1[i] + '" style="color:black;"></i>   ' + data1[i] + '</option>');
                        $('#add_icon').append('<option  value="' + data1[i] + '"><i class="' + data1[i] + '" style="color:black;"></i>   ' + data1[i] + '</option>');
                    }
                    $('#icon').append('<option  value="other">Other...</option>');
                    $('#icon_e').append('<option  value="other">Other...</option>');
                    $('#add_icon').append('<option  value="other">Other...</option>');
                }
            });
            
                    $("#all_c").click(function() {
                        console.log('clicked');
                                if ( $("#all_c").is(":checked")){
                                    $(".n_ast").prop( "checked", false );
                                    $("#all_c").prop( "checked", true );
                                }
                     });
                      /*$(".n_ast").click(function() {
                        console.log('clicked');
                                $("#all_c").prop( "checked", false );
                        //
                     });*/
                     
                     $("#all_add").click(function() {
                        console.log('clicked');
                                if ( $("#all_add").is(":checked")){
                                    $(".n_ast_add").prop( "checked", false );
                                    $("#all_add").prop( "checked", true );
                                }
                     });
                      /*$(".n_ast_add").click(function() {
                        console.log('clicked');
                        $('#all_add').prop( "checked", false );
                     });*/
                     
                      $("#all_e").click(function() {
                        console.log('clicked');
                                if ( $("#all_e").is(":checked")){
                                    $(".n_ast_new").prop( "checked", false );
                                    $("#all_e").prop( "checked", true );
                                    }
                             });
                     /*$(".n_ast_new").click(function() {
                        console.log('clicked n_ast_new');
                          //if ( $("#all_e").is(":checked")){
                                $("#all_e").prop( "checked", false );
                           // }
                        //
                     });*/

        });
        function newData() {
            console.log('NEW RULE');
        }

        //$(document).on('click', '.edit_file', function () {
        $('.edit_file').click(function () {


            //
            var row = $(this).parent().parent().first().children().html();
            var url_e = $(this).parent().parent().first().children().html();
            var linkid_e = $(this).parent().parent().children().eq(2).html();
            var text_e = $(this).parent().parent().children().eq(7).html();
            var pagetitle_e = $(this).parent().parent().children().eq(9).html();
            var icon_e = $(this).parent().parent().children().eq(5).children().eq(0).attr('class');
            var org_e = $(this).parent().parent().children().eq(10).html();
            var color_e = $(this).parent().parent().children().eq(6).children().eq(0).attr('style');
            color_e = color_e.replace('color:', '');
            ///*Check*///
            var role_e = $(this).parent().parent().children().eq(7).html();
            role_e = role_e.replaceAll("'", "");
            role_e = role_e.replaceAll("'", "");
            role_e = role_e.replaceAll(" ", "");
            role_e = role_e.replace("[", "");
            role_e = role_e.replace("]", "");
            var arr_role = role_e.split(/[,.]/);
            var org_e = $(this).parent().parent().children().eq(9).html();
            org_e = org_e.replaceAll("'", "");
            org_e = org_e.replaceAll("'", "");
            org_e = org_e.replaceAll(" ", "");
            org_e = org_e.replace("[", "");
            org_e = org_e.replace("]", "");
            org_e = org_e.replace("GardaLake", "Garda Lake");
            var arr_org = org_e.split(/[,.]/);
            $('#url_e').val(url_e);
            $('#linkid_e').val(linkid_e);
            $('#text_e').val(text_e);
            $('#pagetitle_e').val(pagetitle_e);
            $('#color_e').val(color_e);
            $('#icon_e').val(icon_e);
            var lr = arr_role.length;
            for (var y = 0; y < lr; y++) {
                if (document.getElementById(arr_role[y].toLowerCase() + '_e')) {
                    document.getElementById(arr_role[y].toLowerCase() + '_e').checked = true;
                }
                //$('#'+arr_role[y].toLowerCase()+'_e').checked = true;
            }

            var lo = arr_org.length;
            for (var z = 0; z < lo; z++) {
                if (document.getElementById(arr_org[z] + '_c')) {
                    document.getElementById(arr_org[z] + '_c').checked = true;
                    //Firenze_e
                    console.log("'" + arr_org[z] + "_c'");
                } else {
                    console.log("Not found: " + arr_org[z]);
                }
                //$('#'+arr_org[z]+'_e').checked = true;
            }
            //console.log(arr_role);
            //console.log(arr_org);
            //
        });
        $('#edit_rule').click(function () {
            //
            var id_e = $('#id_e').val();
            var url_e = $('#url_e').val();
            var linkid_e = $('#linkid_e').val();
            var text_e = $('#text_e').val();
            var pagetitle_e = $('#pagetitle_e').val();
            var color_e = $('#color_e').val();
            var icon_e = $('#icon_e').val();
            var select_type_creation = $('#select_type_edit').val();
            var externalApp_e = $('#externalApp_e').val();
            var menuorder_e = $('#menuorder_e').val();
            var openmomde = $('#select_mode_c').val();
            ///////
            var org = '';
            var role = '';
            if (icon_e === 'other') {
                icon_e = $('#icon_e_new_text').val();
            }

            var main_menu = '';
            if ($("#main_menu_id").length) {
                main_menu = $('#main_menu_id').val();
            } else {
                main_menu = '';
            }

            var domain = '';
            if ($("#domain_e").length) {
                domain = $('#domain_e').val();
            } else {
                domain = '';
            }
            /////////////
            //select_check
            var rol_arr = '[';
            var i = 0;
            $(".rol_sel_c").each(function () {
                if ($(this).is(":checked")) {
                    //console.log($(this).val());
                    rol_arr += "'" + $(this).val() + "', ";
                    // i++;
                }
            });
            rol_arr = rol_arr + ']';
            rol_arr = rol_arr.replace(", ]", "]");
            console.log(rol_arr);
            ////////////
            var org_arr = '[';
            /////org_sel
            var i2 = 0;
            $(".org_sel_c").each(function () {
                if ($(this).is(":checked")) {
                    org_arr += "'" + $(this).val() + "', ";
                    i2++;
                }
            })
            org_arr = org_arr + ']';
            org_arr = org_arr.replace(", ]", "]");
            ;
            //
            console.log('COMANDO DI EDIT');
            $.ajax({
                async: true,
                type: 'POST',
                //url: url_parameters,
                //dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'edit_menu',
                    id: id_e,
                    url_e: url_e,
                    linkid_e: linkid_e,
                    text_e: text_e,
                    pagetitle_e: pagetitle_e,
                    color_e: color_e,
                    table: select_type_creation,
                    icon_e: icon_e,
                    org_arr: org_arr,
                    rol_arr: rol_arr,
                    externalApp: externalApp_e,
                    menuOrder: menuorder_e,
                    openmomde: openmomde,
                    main_menu: main_menu,
                    domain: domain
                },
                success: function (data) {
                    //console.log('OK');
                    console.log(data);
                    //$('#table_delete').val();
                    //$('#table_delete').val();
                    location.reload();
                    //
                }
            });
            //
        });
        $('#delete_command').click(function () {
            var id = $('#delete_id').val();
            var table = $('#table_delete').val();
            console.log('ID:' + id + ', TABLE: ' + table);
            $.ajax({
                async: true,
                type: 'POST',
                //url: url_parameters,
                //dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'delete_menu',
                    id: id,
                    table: table
                },
                success: function (data) {
                    console.log('OK');
                    $('#table_delete').val();
                    $('#table_delete').val();
                    location.reload();
                    //
                }
            });
        });
        $('#create_rule').click(function () {
            //console.log("CLICK BUTTON");
            var url = $('#url').val();
            var linkid = $('#linkid').val();
            var text = $('#text').val();
            var pagetitle = $('#pagetitle').val();
            var color = $('#color').val();
            var icon = $('#icon').val();
            //
            if (icon === 'other') {
                icon = $('#icon_new_text').val();
            }
            //
            var publicLinkUrl = $('#publicLinkUrl').val();
            var select_type_creation = $('#select_type_creation').val();
            var menu = '';
            if ((select_type_creation === "MainMenuSubmenus") || (select_type_creation === "MobMainMenuSubmenus") || (select_type_creation === "OrgMenuSubmenus")) {
                menu = $('#menu').val();
            }
            var org = '';
            var role = '';
            var select_mode = $('#select_mode').val();
            /////////////
            //select_check
            var rol_arr = '[';
            var i = 0;
            $(".rol_sel").each(function () {
                if ($(this).is(":checked")) {
                    //console.log($(this).val());
                    rol_arr += "'" + $(this).val() + "', ";
                    // i++;
                }
            });
            rol_arr = rol_arr + ']';
            rol_arr = rol_arr.replace(", ]", "]");
            ////////////
            var org_arr = '[';
            /////org_sel
            var i2 = 0;
            $(".org_sel").each(function () {
                if ($(this).is(":checked")) {
                    org_arr += "'" + $(this).val() + "', ";
                    i2++;
                }
            })
            org_arr = org_arr + ']';
            org_arr = org_arr.replace(", ]", "]");
            var menuOrder = $('#menuorder').val();
            var externalApp = $('#externalApp').val();
            //
            var domain = '';
            domain = $('#domain').val();
            $.ajax({
                async: true,
                type: 'POST',
                //url: url_parameters,
                //dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'create_menu',
                    url: url,
                    linkid: linkid,
                    text: text,
                    pagetitle: pagetitle,
                    publicLinkUrl: publicLinkUrl,
                    color: color,
                    icon: icon,
                    select_type_creation: select_type_creation,
                    menu: menu,
                    role: rol_arr,
                    org: org_arr,
                    select_mode: select_mode,
                    menuOrder: menuOrder,
                    externalApp: externalApp,
                    domain: domain
                },
                success: function (data) {
                    location.reload();
                    //
                }
            });
        });

        $('#copy_table_command').click(function () {

            console.log('COPY');
            $('#confirm_copy').modal({
                show: 'true'
            });
            //APRI CONFERMA

//close_copy
            $('#close_copy').click(function () {
                location.reload();
            });

            $('#confirm_copy_command').click(function () {
                var select_type_edit_dest = $('#select_type_edit_dest').val();
                console.log(select_type_edit_dest);
                $.ajax({
                    async: true,
                    type: 'POST',
                    url: 'geteditormenu.php',
                    dataType: 'json',
                    data: {
                        service: 'copy_table',
                        table: select_type_edit_dest
                    },
                    success: function (data) {

                        if (data['main']['result'] === 'success') {
                            $('#main_t1').html('Table <b>' + data['main']['origin'] + '</b> successfully copied in table <b>' + data['main']['destination']+'</b>. Number of copied Rows: '+ data['main']['Copied Rows']);
                        } else {
                            $('#main_t1').html('Table <b>' + data['main']['origin'] + '</b> Not successfully copied in table <b>' + data['main']['destination']+'</b><br />');
                        }
                        if (data['submenu']['result'] === 'success') {
                            $('#main_s1').html('Table <b>' + data['submenu']['origin'] + '</b> successfully copied in table <b>' + data['submenu']['destination']+'</b>. Number of copied Rows: '+ data['submenu']['Copied Rows']);
                        } else {
                            $('#main_s1').html('Table <b>' + data['submenu']['origin'] + '</b> Not successfully copied in table <b>' + data['submenu']['destination']+'</b>');
                        }
                        $('#result_copy').modal({
                            show: 'true'
                        });

                    }
                });
                //
                //
            });
        });

        $('#create_child').click(function () {


            var url = $('#url').val();
            var linkid = $('#add_plinkid').val();
            var text = $('#add_ptext').val();
            var pagetitle = $('#add_ppagetitle').val();
            var color = $('#add_pcolor').val();
            var icon = $('#add_icon').val();
            //
            //
            if (icon === 'other') {
                icon = $('#add_icon_new_text').val();
            }
            //
            var publicLinkUrl = $('#add_publicLinkUrl').val();
            var select_type_creation = $('#add_pselect_mode').val();
            var add_id = $('#add_id').val();
            var add_url = $('#add_url').val();
            var org = '';
            var role = '';
            var select_mode = $('#select_mode').val();
            /////////////
            //select_check
            var rol_arr = '[';
            var i = 0;
            $(".rol_sel_add").each(function () {
                if ($(this).is(":checked")) {
                    //console.log($(this).val());
                    rol_arr += "'" + $(this).val() + "', ";
                    // i++;
                }
            });
            rol_arr = rol_arr + ']';
            rol_arr = rol_arr.replace(", ]", "]");
            ;
            ////////////
            var org_arr = '[';
            /////org_sel
            var i2 = 0;
            $(".org_sel_c").each(function () {
                if ($(this).is(":checked")) {
                    org_arr += "'" + $(this).val() + "', ";
                    i2++;
                }
            })
            org_arr = org_arr + ']';
            org_arr = org_arr.replace(", ]", "]");
            var add_externalApp = $('#add_externalApp').val();
            var add_menuorder = $('#add_menuorder').val();
            var add_type = $('#add_type').val();
            $.ajax({
                async: true,
                type: 'POST',
                //url: url_parameters,
                //dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'create_menu',
                    url: add_url,
                    linkid: linkid,
                    text: text,
                    pagetitle: pagetitle,
                    publicLinkUrl: publicLinkUrl,
                    color: color,
                    icon: icon,
                    select_type_creation: add_type,
                    menu: add_id,
                    role: rol_arr,
                    org: org_arr,
                    select_mode: select_mode,
                    externalApp: add_externalApp,
                    menuOrder: add_menuorder,
                    domain: ''
                },
                success: function (data) {
                    console.log(data);
                    //alert('create_child');
                    location.reload();
                    //
                }
            });
        });
        $('#select_type_creation').change(function () {
            var select = $('#select_type_creation').val();
            //ajax lista dei submenu
            var list_arr = '';
           var list_c =  $('#select_type_creation').val();
            //AJAX
             $.ajax({
                async: false,
                type: 'POST',
                //url: url_parameters,
                //dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'list_menu_id',
                    type: list_c
                },
                success: function (data) {
                    var obj = JSON.parse(data);
                    var lr = obj.length;
                    for (var y = 0; y < lr; y++) {
                        list_arr= list_arr + '<option value="'+obj[y]['id']+'">'+obj[y]['id']+'</option>';
                    }
                    //alert('create_child');
                    //location.reload();
                    //
                }
            });
            //
            //
            if ((select === "MainMenuSubmenus") || (select === "MobMainMenuSubmenus") || (select === "OrgMenuSubmenus")) {
                $('.new_plu').hide();
                if (select === "OrgMenuSubmenus"){
                    $('.new_plu').show();
                }
                //$('#menu_code').html('<br /><div class="input-group"><span class="input-group-addon">Menu id: </span><input id="menu" name="menu_c" type="text" class="form-control" /></div><br />');
                //
                $('#menu_code').html('<br/><div class="input-group"><span class="input-group-addon">Main Menu Id: </span><select id="menu" name="menu_c" class="form-control">'+list_arr+'</select></div><br/>');
                console.log(select);
            } else {
                $('#menu').val('');
                $('#menu_code').empty();
                $('.new_plu').show();
            }
        });
        function editData(id, linkUrl, publicLinkUrl, linkId, color, pageTitle, typemenu, text, icon, organizations, roles, menuOrder, externalApp, openmomde, submenu, domain) {
            console.log('EDIT MENU');
            //submenu
            $('#id_e').val(unescape(id));
            $('#url_e').val(unescape(linkUrl));
            $('#publicLinkUrl_e').val(unescape(publicLinkUrl));
            $('#linkid_e').val(unescape(linkId));
            $('#icon_e').val(unescape(icon));
            if ((typemenu === 'MainMenu') || (typemenu === 'MobMainMenu') || (typemenu === 'OrgMenu')) {
                $('#domain_div').show();
                $('#domain_e').val(domain);
                $('.edit_plu').show();
                //publicLinkUrl_e
            } else if(typemenu === 'OrgMenuSubmenus') {
                $('#domain_div').hide();
                $('#domain_e').val('');
                $('.edit_plu').show();
                //publicLinkUrl_e
            }else{
                $('#domain_div').hide();
                $('#domain_e').val('');
                $('.edit_plu').hide();
                //publicLinkUrl_e
            }
            //
            console.log('ICON_E:    ' + unescape(icon));
            //CHECK ICON
            if (submenu !== '') {
                //CREA MENU//
                var list_menu = '';
                $.ajax({
                async: false,
                type: 'POST',
                //url: url_parameters,
                //dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'list_menu_id',
                    type: typemenu
                },
                success: function (data) {
                    var obj = JSON.parse(data);
                    var lr = obj.length;
                    for (var y = 0; y < lr; y++) {
                        list_menu= list_menu + '<option value="'+obj[y]['id']+'">'+obj[y]['id']+'</option>';
                    }
                    //alert('create_child');
                    //location.reload();
                    //
                }
            });
                //$('#mainmenu_c').html('<div class="input-group"><span class="input-group-addon">Main Menu Id: </span><input id="main_menu_id"  type="text" class="form-control" /></div><br />');
                $('#mainmenu_c').html('<div class="input-group"><span class="input-group-addon">Main Menu Id: </span><select id="main_menu_id"  type="text" class="form-control">'+list_menu+'</select></div><br />');
                $('#main_menu_id').val(submenu);
                //
            } else {
                $('#mainmenu_c').empty();
            }
            var c_icon = $('#icon_e').val();
            if (c_icon !== unescape(icon)) {
                $('#icon_e').val('other');
                //$('#icon_e_new').html('');
                $('#icon_e_new').html('<div class="input-group"><span class="input-group-addon">Add new Icon: </span><input id="icon_e_new_text"  type="text" class="form-control" /></div><br />');
                $('#icon_e_new_text').val(unescape(icon));
            } else {
                $('#icon_e_new').empty();
            }
            //
            $('#color_e').val(unescape(color));
            $('#text_e').val(unescape(text));
            $('#pagetitle_e').val(unescape(pageTitle));
            console.log('unescape: ' + unescape(menuOrder));
            $('#select_type_edit').val(unescape(typemenu));
            $('#menuorder_e').val(unescape(menuOrder));
            $('#externalApp_e').val(unescape(externalApp));
            $('#select_mode_c').val(unescape(openmomde));
            //
            var org_e = unescape(organizations);
            org_e = org_e.replaceAll("'", "");
            org_e = org_e.replaceAll("'", "");
            org_e = org_e.replaceAll(" ", "");
            org_e = org_e.replace("[", "");
            org_e = org_e.replace("]", "");
            org_e = org_e.replace("GardaLake", "Garda Lake");
            var arr_org = org_e.split(/[,.]/);
            ///////////////
            var role_e = unescape(roles);
            role_e = role_e.replaceAll("'", "");
            role_e = role_e.replaceAll("'", "");
            role_e = role_e.replaceAll(" ", "");
            role_e = role_e.replace("[", "");
            role_e = role_e.replace("]", "");
            var arr_role = role_e.split(/[,.]/);
            /////////////
            var lr = arr_role.length;
            for (var y = 0; y < lr; y++) {
                if (document.getElementById(arr_role[y].toLowerCase() + '_e')) {
                    document.getElementById(arr_role[y].toLowerCase() + '_e').checked = true;
                }
                //$('#'+arr_role[y].toLowerCase()+'_e').checked = true;
            }

            /////////////////
            var lo = arr_org.length;
            for (var z = 0; z < lo; z++) {
                if (document.getElementById(arr_org[z] + '_c')) {
                    document.getElementById(arr_org[z] + '_c').checked = true;
                    //Firenze_e
                    console.log("'" + arr_org[z] + "_c'");
                } else {
                    console.log("Not found: " + arr_org[z]);
                    if (arr_org[z] == '*'){
                        document.getElementById('all_c').checked = true;
                    }
                }
                //$('#'+arr_org[z]+'_e').checked = true;
            }

        }
        //
        function deleteData(id, table) {
            //$("#delete-Data").modal();
            console.log('ID:' + id + ', Table:' + table);
            $('#delete_id').val(id);
            $('#table_delete').val(table);
            //
        }

        function copyTable() {
            console.log('COPY TABLE');
            // $('#confirm_copy').show();
            var select_type_edit_dest = $('#select_type_edit_dest').val();
            console.log('select_type_edit_dest:' + select_type_edit_dest);
        }

        function filtroDom(id) {

            $('.check_domain').attr("checked", false);
            var id2 = 'checkdom_' + id;
            $('#checked_dom').val(id);
            var checked_menu = $('#checked_dom').val();
            var sel_MainMenu = $('#sel_MainMenu').prop("checked");
            var sel_MainMenuSubmenus = $('#sel_MainMenuSubmenus').prop("checked");
            var sel_OrgMenu = $('#sel_OrgMenu').prop("checked");
            var sel_OrgMenuSubmenus = $('#sel_OrgMenuSubmenus').prop("checked");
            var sel_MobMainMenuSubmenus = $('#sel_MobMainMenuSubmenus').prop("checked");
            var sel_MobMainMenu = $('#sel_MobMainMenu').prop("checked");
            var select_all = $('#select_all').prop("checked");
            $('#checked_menu').val(id);
            var checked_dom = $('#checked_dom').val();
            var check_butt = $('#checkdom_' + id).prop("checked");
            var check_checkdom_select_all = $('#checkdom_select_all').prop("checked");
            //
            //
            //var checked_dom = 'select_all';
            var prop = $("#checkdom_" + id).prop("checked");
            if (prop == false) {
                $("#checkdom_" + id).prop('checked', true);
            } else if (prop == true) {
                $("#checkdom_" + id).prop('checked', false);
                $('#checked_dom').val('select_all');
            } else {
                $("#checkdom_" + id).prop('checked', false);
                $('#checked_dom').val('select_all');
            }
            //
            //
            $('input[class="check_domain"]:checked').each(function () {      // $(':checkbox:checked')
                var i1 = $(this).val();
                console.log('i1: ' + i1);
            });
            //
            var table = $('#value_table').DataTable();
            //
            var array_act = new Array();
            table.destroy();
            $('#value_table tbody').empty();
            if ((sel_MainMenu == false) && (sel_MainMenuSubmenus == false) && (sel_OrgMenu == false) && (sel_OrgMenuSubmenus == false) && (sel_MobMainMenuSubmenus == false) && (sel_MobMainMenu == false) && (select_all == false)) {
                select_all = true;
            }
            $.ajax({
                async: true,
                type: 'POST',
                //url: url_parameters,
                dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'list_menu',
                    sel_MainMenu: sel_MainMenu,
                    sel_MainMenuSubmenus: sel_MainMenuSubmenus,
                    sel_OrgMenu: sel_OrgMenu,
                    sel_OrgMenuSubmenus: sel_OrgMenuSubmenus,
                    sel_MobMainMenuSubmenus: sel_MobMainMenuSubmenus,
                    sel_MobMainMenu: sel_MobMainMenu,
                    select_all: select_all,
                    domain: checked_dom
                },
                success: function (data) {
                    //console.log(data);
                    //value_table
                    for (var i = 0; i < data.length; i++)
                    {
                        array_act[i] = {
                            id: data[i]['id'],
                            linkUrl: data[i]['linkUrl'],
                            publicLinkUrl: data[i]['publicLinkUrl'],
                            linkId: data[i]['linkId'],
                            icon: data[i]['icon'],
                            text: data[i]['text'],
                            privileges: data[i]['privileges'],
                            userType: data[i]['userType'],
                            externalApp: data[i]['externalApp'],
                            openMode: data[i]['openMode'],
                            iconColor: data[i]['iconColor'],
                            pageTitle: data[i]['pageTitle'],
                            domain: data[i]['domain'],
                            menuOrder: data[i]['menuOrder'],
                            organizations: data[i]['organizations'],
                            typemenu: data[i]['typemenu'],
                            submenu: data[i]['submenu'],
                            claim: data[i]['claim']
                                    //
                        };
                        //});
                        //
                        var button_submenu = '';
                        if ((array_act[i]['linkUrl'] === 'submenu') && ((array_act[i]['typemenu'] === 'MainMenu') || (array_act[i]['typemenu'] === 'MobMainMenu') || (array_act[i]['typemenu'] === 'OrgMenu'))) {
                            button_submenu = '<button type="button" class="viewDashBtn addchild" data-target="#add-modal" data-toggle="modal" value="' + i + '" onclick=addSubmenu(' + array_act[i]['id'] + ',"' + array_act[i]['typemenu'] + '")>ADD</button> ';
                        } else {
                            button_submenu = '';
                        }
                        //var json_data = JSON.stringify(data.healthiness);
                        var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#edit-modal" data-toggle="modal" value="' + i + '" onclick=editData(' + escape(array_act[i]['id']) + ',"' + escape(array_act[i]['linkUrl']) + '","' + escape(array_act[i]['publicLinkUrl']) + '","' + escape(array_act[i]['linkId']) + '","' + escape(array_act[i]['iconColor']) + '","' + escape(array_act[i]['pageTitle']) + '","' + escape(array_act[i]['typemenu']) + '","' + escape(array_act[i]['text']) + '","' + escape(array_act[i]['icon']) + '","' + escape(array_act[i]['organizations']) + '","' + escape(array_act[i]['privileges']) + '","' + escape(array_act[i]['menuOrder']) + '","' + escape(array_act[i]['externalApp']) + '","' + escape(array_act[i]['openMode']) + '","' + escape(array_act[i]['submenu']) + '","' + escape(array_act[i]['domain']) + '")>EDIT</button> ';
                        var button_del = '<button type="button" class="delDashBtn delete_file" data-target="#delete-modal" data-toggle="modal" value="' + i + '" onclick=deleteData(' + array_act[i]['id'] + ',"' + array_act[i]['typemenu'] + '")>DELETE</button> ';
                        //console.log(lun);
                        var controls = button_edit + button_submenu + button_del;
                        $('#value_table tbody').append('<tr><td>' + array_act[i]['id'] + '</td><td>' + array_act[i]['claim'] + '</td><td>' + array_act[i]['text'] + '</td><td>' + array_act[i]['linkUrl'] + '</td><td>' + array_act[i]['pageTitle'] + '</td><td>' + controls + '</td><td>' + array_act[i]['publicLinkUrl'] + '</td><td>' + array_act[i]['linkId'] + '</td><td>' + array_act[i]['typemenu'] + '</td><td>' + array_act[i]['submenu'] + '</td><td><i class="' + array_act[i]['icon'] + '"></i></td><td><i class="fa fa-circle" aria-hidden="true" style="color:' + array_act[i]['iconColor'] + '"></i>' + array_act[i]['iconColor'] + '</td><td>' + array_act[i]['privileges'] + '</td><td>' + array_act[i]['externalApp'] + '</td><td>' + array_act[i]['organizations'] + '</td><td>' + array_act[i]['openMode'] + '</td><td>' + array_act[i]['menuOrder'] + '</td></tr>');
                    }
                    /****/
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
                            "lengthMenu": "Show	_MENU_ ",
                        }
                    });
                    /***/
                    //$('.paginate_button').css('background-color','#337ab7');
                    /***/
                }
                //
            });
            //document.getElementById(id2).checked = true;
            //<input id="checked_menu" type="text" value="select_all"></input>
            //<input id="checked_dom" type="text"  value="select_all"></input>

        }

        function filtroData(id) {
            //
            $('.check_org').attr("checked", false);
            document.getElementById(id).checked = true;
            //
            var sel_MainMenu = $('#sel_MainMenu').prop("checked");
            var sel_MainMenuSubmenus = $('#sel_MainMenuSubmenus').prop("checked");
            var sel_OrgMenu = $('#sel_OrgMenu').prop("checked");
            var sel_OrgMenuSubmenus = $('#sel_OrgMenuSubmenus').prop("checked");
            var sel_MobMainMenuSubmenus = $('#sel_MobMainMenuSubmenus').prop("checked");
            var sel_MobMainMenu = $('#sel_MobMainMenu').prop("checked");
            var select_all = $('#select_all').prop("checked");
            $('#checked_menu').val(id);
            var checked_dom = $('#checked_dom').val();
            //
            $('input[class="check_domain"]:checked').each(function () {      // $(':checkbox:checked')
                var i1 = $(this).val();
                console.log('i1: ' + i1);
            });
            //
            var table = $('#value_table').DataTable();
            //
            var array_act = new Array();
            table.destroy();
            $('#value_table tbody').empty();
            $.ajax({
                async: true,
                type: 'POST',
                //url: url_parameters,
                dataType: 'json',
                url: 'geteditormenu.php',
                data: {
                    service: 'list_menu',
                    sel_MainMenu: sel_MainMenu,
                    sel_MainMenuSubmenus: sel_MainMenuSubmenus,
                    sel_OrgMenu: sel_OrgMenu,
                    sel_OrgMenuSubmenus: sel_OrgMenuSubmenus,
                    sel_MobMainMenuSubmenus: sel_MobMainMenuSubmenus,
                    sel_MobMainMenu: sel_MobMainMenu,
                    select_all: select_all,
                    domain: checked_dom
                },
                success: function (data) {
                    //console.log(data);
                    //value_table
                    for (var i = 0; i < data.length; i++)
                    {
                        array_act[i] = {
                            id: data[i]['id'],
                            linkUrl: data[i]['linkUrl'],
                            publicLinkUrl: data[i]['publicLinkUrl'],
                            linkId: data[i]['linkId'],
                            icon: data[i]['icon'],
                            text: data[i]['text'],
                            privileges: data[i]['privileges'],
                            userType: data[i]['userType'],
                            externalApp: data[i]['externalApp'],
                            openMode: data[i]['openMode'],
                            iconColor: data[i]['iconColor'],
                            pageTitle: data[i]['pageTitle'],
                            domain: data[i]['domain'],
                            menuOrder: data[i]['menuOrder'],
                            organizations: data[i]['organizations'],
                            typemenu: data[i]['typemenu'],
                            submenu: data[i]['submenu'],
                            claim: data[i]['claim']
                                    //
                        };
                        //});
                        //
                        var button_submenu = '';
                        if ((array_act[i]['linkUrl'] === 'submenu') && ((array_act[i]['typemenu'] === 'MainMenu') || (array_act[i]['typemenu'] === 'MobMainMenu') || (array_act[i]['typemenu'] === 'OrgMenu'))) {
                            button_submenu = '<button type="button" class="viewDashBtn addchild" data-target="#add-modal" data-toggle="modal" value="' + i + '" onclick=addSubmenu(' + array_act[i]['id'] + ',"' + array_act[i]['typemenu'] + '")>ADD</button> ';
                        } else {
                            button_submenu = '';
                        }
                        //var json_data = JSON.stringify(data.healthiness);
                        var button_edit = '<button type="button" class="editDashBtn edit_file" data-target="#edit-modal" data-toggle="modal" value="' + i + '" onclick=editData(' + escape(array_act[i]['id']) + ',"' + escape(array_act[i]['linkUrl']) + '","' + escape(array_act[i]['publicLinkUrl']) + '","' + escape(array_act[i]['linkId']) + '","' + escape(array_act[i]['iconColor']) + '","' + escape(array_act[i]['pageTitle']) + '","' + escape(array_act[i]['typemenu']) + '","' + escape(array_act[i]['text']) + '","' + escape(array_act[i]['icon']) + '","' + escape(array_act[i]['organizations']) + '","' + escape(array_act[i]['privileges']) + '","' + escape(array_act[i]['menuOrder']) + '","' + escape(array_act[i]['externalApp']) + '","' + escape(array_act[i]['openMode']) + '","' + escape(array_act[i]['submenu']) + '","' + escape(array_act[i]['domain']) + '")>EDIT</button> ';
                        var button_del = '<button type="button" class="delDashBtn delete_file" data-target="#delete-modal" data-toggle="modal" value="' + i + '" onclick=deleteData(' + array_act[i]['id'] + ',"' + array_act[i]['typemenu'] + '")>DELETE</button> ';
                        //console.log(lun);
                        var controls = button_edit + button_submenu + button_del;
                        $('#value_table tbody').append('<tr><td>' + array_act[i]['id'] + '</td><td>' + array_act[i]['claim'] + '</td><td>' + array_act[i]['text'] + '</td><td>' + array_act[i]['linkUrl'] + '</td><td>' + array_act[i]['pageTitle'] + '</td><td>' + controls + '</td><td>' + array_act[i]['publicLinkUrl'] + '</td><td>' + array_act[i]['linkId'] + '</td><td>' + array_act[i]['typemenu'] + '</td><td>' + array_act[i]['submenu'] + '</td><td><i class="' + array_act[i]['icon'] + '"></i></td><td><i class="fa fa-circle" aria-hidden="true" style="color:' + array_act[i]['iconColor'] + '"></i>' + array_act[i]['iconColor'] + '</td><td>' + array_act[i]['privileges'] + '</td><td>' + array_act[i]['externalApp'] + '</td><td>' + array_act[i]['organizations'] + '</td><td>' + array_act[i]['openMode'] + '</td><td>' + array_act[i]['menuOrder'] + '</td></tr>');
                    }
                    /****/
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
                            "lengthMenu": "Show	_MENU_ ",
                        }
                    });
                    /***/
                    //$('.paginate_button').css('background-color','#337ab7');
                    /***/
                }
                //
            });
        }

        function select_icon(icon) {
            var icon_val = document.getElementById(icon).value;
            var icon_new = icon + '_new';
            if (icon_val === 'other') {

                $('#' + icon_new).html('<div class="input-group"><span class="input-group-addon">Add new Icon: </span><input id="' + icon_new + '_text"  type="text" class="form-control" /></div><br />');
            } else {
                $('#' + icon_new).empty();
                console.log('DELETE ICON NEW');
            }
        }

        function addSubmenu(id, type) {
            //addSubmenu
            $('#add_id').val(id);
            console.log(type);
            if (type === 'MainMenu') {
                $('#add_type').val('MainMenuSubmenus');
                $('.add_plu').hide();
                //
            } else if (type === 'MobMainMenu') {
                $('#add_type').val('MobMainMenuSubmenus');
                $('.add_plu').hide();
                //
            } else if (type === 'OrgMenu') {
                $('#add_type').val('OrgMenuSubmenus');
                $('.add_plu').show();
                //
            } else {
                $('#add_type').val(type);
                $('.add_plu').show();
                //
            }

        }

        function filtroAllData() {
            console.log('OK');
        }
        
    </script>
</body>

</html>

<?php } else {
    include('../s4c-legacy-management/editormenu.php');
}
?>