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

    include('process-form.php');
    checkSession('RootAdmin');
    
    $link = mysqli_connect($host, $username, $password);
    mysqli_select_db($link, $dbname);    
?>

<!DOCTYPE html>
<html class="dark">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php include "mobMainMenuClaim.php" ?></title>
        
        

        <!-- Bootstrap Core CSS -->
          <link href="../css/s4c-css/bootstrap/bootstrap.css" rel="stylesheet">
          <link href="../css/s4c-css/bootstrap/bootstrap-colorpicker.min.css" rel="stylesheet">

        <!-- jQuery -->
        <script src="../js/jquery-1.10.1.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="../js/bootstrap.min.js"></script>
       
        <!-- DataTables -->
        <script type="text/javascript" charset="utf8" src="../js/DataTables/datatables.js"></script>
        <link rel="stylesheet" type="text/css" href="../js/DataTables/datatables.css">
        <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.bootstrap.min.js"></script>
        <script type="text/javascript" charset="utf8" src="../js/DataTables/dataTables.responsive.min.js"></script>
        <script type="text/javascript" charset="utf8" src="../js/DataTables/responsive.bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/DataTables/dataTables.bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/DataTables/responsive.bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/DataTables/jquery.dataTables.min.css"> 

       
         <!-- Custom CSS -->
         <?php include "theme-switcher.php"?>
        
        <!-- Custom scripts -->
        <script type="text/javascript" src="../js/dashboard_mng.js"></script>
        
        <!-- File js associato alla pagina -->
        <!--<script src="../js/metricsHttpManagement.js"></script>-->
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
                        <div id="headerTitleCnt">HTTP metrics</div>
                        <div class="user-menu-container">
                          <?php include "loginPanel.php" ?>
                        </div>
                        <div class="col-lg-2 hidden-md hidden-lg centerWithFlex" id="headerMenuCnt"><?php include "mobMainMenu.php" ?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="mainContentCnt"> 
                            <div class="row mainContentRow" id="dashboardsListTableRow" style="padding-top: 0px !important; padding-bottom: 0px !important">
                                <div class="col-xs-12 mainContentCellCnt">
                                    <div id="dashboardsListMenu" class="row">
                                        <div id="dashboardListsPages" class="dashboardsListMenuItem">
                                            <div class="dashboardsListMenuItemContent centerWithFlex col-xs-12">
                                                
                                            </div>
                                        </div>
                                        <div id="dashboardListsNewDashboard" class="dashboardsListMenuItem">
                                           <button type="button" class="btn btn-new-dash" id="addMetricBtn">New metric</button>
                                        </div>
                                    </div>
                                    
                                    <table id="list_metrics" class="table table-striped dt-responsive nowrap"> 
                                        <thead>
                                            <tr>
                                                <th data-cellTitle="id">id</th>
                                                <th data-cellTitle="IdMetric">Name</th>
                                                <th data-cellTitle="description">description</th>
                                                <th data-cellTitle="status">status</th>   
                                                <th data-cellTitle="query">query</th>      
                                                <th data-cellTitle="queryType">Query type</th>
                                                <th data-cellTitle="metricType">Metric type</th>
                                                <th data-cellTitle="frequency">Frequency</th>
                                                <th data-cellTitle="processType">processType</th>
                                                <th data-cellTitle="area">area</th>
                                                <th data-cellTitle="source">source</th>
                                                <th data-cellTitle="description_short">Short desc</th>
                                                <th data-cellTitle="dataSource">Data source</th>
                                                <th data-cellTitle="status_HTTPRetr">Status</th>
                                                <th data-cellTitle="username_HTTPRetr">username_HTTPRetr</th>
                                                <th data-cellTitle="password_HTTPRetr">password_HTTPRetr</th>
                                                <th data-cellTitle="Actions">Actions</th>
                                            </tr>  
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal di creazione di una metrica-->
    <div class="modal fade" id="modalAddMetric" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add new HTTP Metric</h4>
                </div>
                <div class="modal-body" style="overflow-y: auto;">
                    <div class="tabbable"> 
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1" data-toggle="tab">General</a></li>
                            <li><a href="#tab2" data-toggle="tab">Data acquisition</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="tab1">
                                <form id="formGeneralInsertMetric" class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="nameMetric" class="col-md-4 control-label">Name</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="nameMetric">                                 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="descriptionMetric" class="col-md-4 control-label">Description</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control textarea-metric" rows="3" name="descriptionMetric"></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="descriptionShortMetric" class="col-md-4 control-label">Short Description</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="descriptionShortMetric"> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="areaMetric" class="col-md-4 control-label">Data Area</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="areaMetric" id="areaMetric">
                                                    <option value = "Mobilità">Mobilità</option>
                                                    <option value = "Intrattenimento">Intrattenimento</option>
                                                    <option value = "Statistiche">Statistiche</option>
                                                    <option value = "Social Network">Social Network</option>
                                                    <option value = "Meteo">Meteo</option>
                                                    <option value = "Network">Network</option>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="sourceMetric" class="col-md-4 control-label">Source</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="sourceMetric"> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="frequencyMetric" class="col-md-4 control-label">Frequency of calculation (milliseconds)</label>
                                            <div class="col-md-6">
                                                <input type="number" class="form-control" name="frequencyMetric"> 
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                                
                            <!-- Tab Data Acquisition -->
                            <div class="tab-pane" id="tab2">
                                <form id="formConnectionInsertMetric" class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="URL">URL</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="URLInsertMetric" name="URL">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="username">Username</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="usernameInsertMetric" name="username">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="password">Password</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="passwordInsertMetric" name="password">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="rawDataType">Raw data type</label>
                                            <div class="col-md-3">
                                                <select class="form-control" name="rawDataType" id="rawDataTypeInsertMetric">
                                                    <option value ="XML">XML</option>
                                                    <option value ="XML_to_JSON">XML as JSON</option>
                                                    <option value="JSON">JSON</option>
                                                    <option value="Text">Text</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label">Raw data</label>
                                            <div class="col-md-9">
                                                <textarea id="rawDataInsertMetric" class="form-control" rows="8" readonly></textarea> 
                                            </div>
                                        </div>
                                        <div class="row flex-center">
                                            <p id="feedbackTestConn"></p>
                                        </div>
                                        <div class="row flex-center">
                                            <button type="submit" class="btn btn-primary">Test Connection</button>
                                        </div>
                                    </div>
                                </form>
                                <form id="formScriptInsertMetric" class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="dataType">Metric type</label>
                                            <div class="col-md-3">
                                                <select class="form-control" name="metricType" id="metricTypeInsertMetric">
                                                    <option value = "Intero">Intero</option>
                                                    <option value ="Float">Float</option>
                                                    <option value = "Testuale">Testuale</option>
                                                    <option value = "Percentuale">Percentuale</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="script">Script JS</label>
                                            <button id="buttonHintInsertScript" type="button" class="btn btn-link">Show Hint</button>
                                        </div>
                                        <div id="containerHintInsertScript" class="row flex-center">
                                            <div class="col-md-11 col-sm-12">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="row flex-center">
                                            <div class="col-md-11 col-sm-12">
                                                <textarea id="scriptInsertMetric" name="script" class="form-control textarea-metric" rows="10"></textarea> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label">Result data</label>
                                            <div class="col-md-9">
                                                <textarea id="resultScriptInsertMetric" class="form-control textarea-metric" rows="5" readonly></textarea> 
                                            </div>
                                        </div>
                                        <div class="row flex-center">
                                            <p id="feedbackTestScript"></p>
                                        </div>
                                        <div class="row flex-center">
                                            <button type="submit" class="btn btn-primary">Test Script</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row flex-center">
                        <p id="feedbackAddMetric"></p>
                    </div>
                    <div class="row flex-center">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="buttonInsertNewMetric"  class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal di modifica di una metrica-->
    <div class="modal fade" id="modalModifyMetric" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modify HTTP Metric</h4>
                </div>
                <div class="modal-body" style="overflow-y: auto;">
                    <div class="tabbable"> 
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tabModifyGeneral" data-toggle="tab">General</a></li>
                            <li><a href="#tabModifyData" data-toggle="tab">Data acquisition</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="tabModifyGeneral">
                                <form id="formGeneralModifyMetric" class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="nameMetric" class="col-md-4 control-label">Name</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="nameMetric">                                 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="descriptionMetric" class="col-md-4 control-label">Description</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control textarea-metric" rows="3" name="descriptionMetric"></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="descriptionShortMetric" class="col-md-4 control-label">Short Description</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="descriptionShortMetric"> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="areaMetric" class="col-md-4 control-label">Data Area</label>
                                            <div class="col-md-6">
                                                <select class="form-control" name="areaMetric" id="areaMetric">
                                                    <option value = "Mobilità">Mobilità</option>
                                                    <option value = "Intrattenimento">Intrattenimento</option>
                                                    <option value = "Statistiche">Statistiche</option>
                                                    <option value = "Social Network">Social Network</option>
                                                    <option value = "Meteo">Meteo</option>
                                                    <option value = "Network">Network</option>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="sourceMetric" class="col-md-4 control-label">Source</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="sourceMetric"> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="frequencyMetric" class="col-md-4 control-label">Frequency of calculation (milliseconds)</label>
                                            <div class="col-md-6">
                                                <input type="number" class="form-control" name="frequencyMetric"> 
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab Data Acquisition -->
                            <div class="tab-pane" id="tabModifyData">
                                <form id="formConnectionModifyMetric" class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="URL">URL</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="URLModifyMetric" name="URL">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="username">Username</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="usernameModifyMetric" name="username">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="password">Password</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="passwordModifyMetric" name="password">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="rawDataType">Raw data type</label>
                                            <div class="col-md-3">
                                                <select class="form-control" name="rawDataType" id="rawDataTypeModifyMetric">
                                                    <option value ="XML">XML</option>
                                                    <option value ="XML_to_JSON">XML as JSON</option>
                                                    <option value="JSON">JSON</option>
                                                    <option value="Text">Text</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label">Raw data</label>
                                            <div class="col-md-9">
                                                <textarea id="rawDataModifyMetric" class="form-control" rows="8" readonly></textarea> 
                                            </div>
                                        </div>
                                        <div class="row" align="center">
                                            <p id="feedbackTestConnModify"></p>
                                        </div>
                                        <div class="row flex-center">
                                            <button type="submit" class="btn btn-primary">Test Connection</button>
                                        </div>
                                    </div>
                                </form>
                                <form id="formScriptModifyMetric" class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="dataType">Metric type</label>
                                            <div class="col-md-3">
                                                <select class="form-control" name="metricType" id="metricTypeModifyMetric">
                                                    <option value = "Intero">Intero</option>
                                                    <option value ="Float">Float</option>
                                                    <option value = "Testuale">Testuale</option>
                                                    <option value = "Percentuale">Percentuale</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-3 control-label" for="script">Script JS</label>
                                            <button id="buttonHintModifyScript" type="button" class="btn btn-link">Show Hint</button>
                                        </div>
                                        <div id="containerHintModifyScript" class="row flex-center">
                                            <div class="col-md-11 col-sm-12">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="row flex-center">
                                            <div class="col-md-11 col-sm-12">
                                                <textarea id="scriptModifyMetric" name="script" class="form-control textarea-metric" rows="10"></textarea> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label class="col-md-3 control-label">Result data</label>
                                            <div class="col-md-9">
                                                <textarea id="resultScriptModifyMetric" class="form-control textarea-metric" rows="5" readonly></textarea> 
                                            </div>
                                        </div>
                                        <div class="row flex-center">
                                            <p id="feedbackTestScriptModify"></p>
                                        </div>
                                        <div class="row flex-center">
                                            <button type="submit" class="btn btn-primary">Test Script</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row flex-center">
                        <p id="feedbackModifyMetric"></p>
                    </div>
                    <div class="row flex-center">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="buttonModifyMetric"  class="btn btn-primary">Modify</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fine del menù di modifica della metrica -->    
    
    <!-- Modale cancellazione della metrica-->
    <div class="modal fade" id="modalDeleteMetric" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalTitleDeleteMetric"></h4>
                </div>
                <div class="modal-body">
                    <div class = "row">
                        <div class="col-sm-12"><p>After the confirmation will not be possible to restore the deleted metric. Are you sure you want to proceed?</p></div>
                    </div>
                    <div clas = "row flex-center">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="buttonDeleteMetric"  class="btn btn-primary">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FIne del menù di cancellazione della metrica -->
        
    </body>
<html class="dark">

<script type='text/javascript'>
    $(document).ready(function () 
    {
        var sessionEndTime = "<?php echo $_SESSION['sessionEndTime']; ?>";
        $('#sessionExpiringPopup').css("top", parseInt($('body').height() - $('#sessionExpiringPopup').height()) + "px");
        $('#sessionExpiringPopup').css("left", parseInt($('body').width() - $('#sessionExpiringPopup').width()) + "px");
        
        var webServerURL = 'http://localhost:8080/JavaWebApplication/services';
        var array_metrics = new Array();
        var metricToDelete, metricToModify, hintInsertScriptShowed, hintModifyScriptShowed, datatableRef = null;

        Array.prototype.contains = function (v) {
            for (var i = 0; i < this.length; i++) {
                if (this[i] === v)
                    return true;
            }
            return false;
        };

        Array.prototype.unique = function () {
            var arr = [];
            for (var i = 0; i < this.length; i++) {
                if (!arr.contains(this[i])) {
                    arr.push(this[i]);
                }
            }
            return arr;
        };
        
        //recupera le metriche dal database e le mostra nella tabella
        function findAllMetrics(){
            var old_tbody = $('#tableBody');
            var new_tbody = document.createElement('tbody');
            new_tbody.setAttribute('id', 'tableBody');

            var htmlBody = '';

            $.ajax({
                url: "get_data.php",
                data: {action: "getHTTPMetrics"},
                type: "GET",
                async: true,
                contentType: "charset=iso-8859-1",
                dataType: 'json',
                success: function (data) {
                    for (var i = 0; i < data.length; i++)
                    {
                        array_metrics[i] = {desc: data[i]['descMetric'],
                            descShort: data[i]['descShortMetric'],
                            id: data[i]['idMetric'],
                            type: data[i]['typeMetric'],
                            area: data[i]['areaMetric'],
                            source: data[i]['sourceMetric'],
                            frequency: data[i]['frequencyMetric'],
                            status: data[i]['statusMetric'],
                            queryType: data[i]['rawDataTypeMetric'],
                            url: data[i]['sourceURLMetric'],
                            metricType: data[i]['typeMetric'],
                            scriptJS: data[i]['scriptMetric'],
                            username: data[i]['usernameMetric'],
                            password: data[i]['password']};

                        //Lista delle metriche
                        if (array_metrics[i]['status'] == 'Attivo') {
                            htmlBody += '<tr>' +
                                        '<td class="name_met">' + array_metrics[i]['id'] + '</td>' +
                                        '<td>' + array_metrics[i]['desc'] + '</td>' +
                                        '<td>' + array_metrics[i]['area'] + '</td>' +
                                        '<td>' + array_metrics[i]['source'] + '</td>' +
                                        '<td><input type="checkbox" class="checkStato" name="stato" value=1 checked onchange="checkStatoCheckedChanged(this, ' + i +')"/></td>' +
                                        '<td><div class="icons-modify-metric"><a class="icon-cfg-metric" data-toggle="modal" data-target="#modalModifyMetric"  onclick="buttonModifyClick(' + i + ')" style="float:left;"><span class="glyphicon glyphicon-cog glyphicon-modify-metric" tabindex="-1" aria-hidden="true"></span></a></div><div class="icons-delete-metric"><a class="icon-delete-metric" data-toggle="modal" data-target="#modalDeleteMetric" onclick="buttonDeleteClick(' + i + ')"><span class="glyphicon glyphicon-remove glyphicon-delete-metric" aria-hidden="true" style="float:right;"></span></a></div></td>' +
                                        '</tr>';
                        } else {
                            htmlBody += '<tr>' +
                                        '<td class="name_met">' + array_metrics[i]['id'] + '</td>' +
                                        '<td>' + array_metrics[i]['desc'] + '</td>' +
                                        '<td>' + array_metrics[i]['area'] + '</td>' +
                                        '<td>' + array_metrics[i]['source'] + '</td>' +
                                        '<td><input type="checkbox" class="checkStato" name="stato" value=0 onchange="checkStatoCheckedChanged(this, ' + i +')"/></td><td><div class="icons-modify-metric"><a class="icon-cfg-metric" href="#" data-toggle="modal" data-target="#modalModifyMetric" onclick="buttonModifyClick(' + i + ')" style="float:left;"><span class="glyphicon glyphicon-cog glyphicon-modify-metric" tabindex="-1" aria-hidden="true"></span></a></div><div class="icons-delete-metric"><a class="icon-delete-metric" data-toggle="modal" data-target="#modalDeleteMetric" onclick="buttonDeleteClick(' + i + ')"><span class="glyphicon glyphicon-remove glyphicon-delete-metric" aria-hidden="true" style="float:right;"></span></a></div></td>' +
                                        '</tr>';
                        }    
                    }

                    $(new_tbody).html(htmlBody);
                    old_tbody.replaceWith(new_tbody);

                }
            });
        }

        //gestione del click bottone modifica metrica
        /*function buttonModifyClick(metricIndex){
            metricToModify = array_metrics[metricIndex];

            //riempio i campi del modal con i dati della metrica da modificare
            $scriptForm = $('#formScriptModifyMetric');
            $connectionForm = $('#formConnectionModifyMetric');
            $generalForm = $('#formGeneralModifyMetric');

            $generalForm.find('input[name="nameMetric"]').val(metricToModify.id);
            $generalForm.find('textarea[name="descriptionMetric"]').val(metricToModify.desc);
            $generalForm.find('input[name="descriptionShortMetric"]').val(metricToModify.descShort);
            $generalForm.find('select[name="areaMetric"]').val(metricToModify.area);
            $generalForm.find('input[name="sourceMetric"]').val(metricToModify.source);
            $generalForm.find('input[name="frequencyMetric"]').val(metricToModify.frequency);
            $connectionForm.find('input[name="URL"]').val(metricToModify.url);
            $connectionForm.find('input[name="username"]').val(metricToModify.username);
            $connectionForm.find('input[name="password"]').val(metricToModify.password);
            $connectionForm.find('select[name="rawDataType"]').val(metricToModify.queryType);
            $scriptForm.find('select[name="metricType"]').val(metricToModify.metricType),
            $scriptForm.find('textarea[name="script"]').val(metricToModify.scriptJS);
        }*/

        //gestione del click bottone cancellazione metrica
        function buttonDeleteClick(metricIndex){
            metricToDelete = array_metrics[metricIndex];

            $('#modalTitleDeleteMetric').text('Delete metric ' + metricToDelete.id);
        }

        function checkStatoCheckedChanged(checkBox, metricIndex){
            var metric = array_metrics[metricIndex];

            var jsonRequest = {
                metricId: metric.id,
                status: checkBox.checked
            };

            $.ajax({
                headers: { 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json' 
                },
                type: 'POST',
                url: webServerURL + '/webservices/statusMetric',
                data: JSON.stringify(jsonRequest),
                error: function(data){
                    alert('Server error');
                    $(checkBox).prop('checked', !checkBox.checked);
                },
                success: function(data) {
                    if(!data.success){
                        alert(data.error);
                        $(checkBox).prop('checked', !checkBox.checked);
                    }
                }
            });
        }

        function getHintByRawDataType(rawDataType){
            switch(rawDataType){
                case 'XML':
                    return 'Parse XML data with Java DOM methods. Document object is stored in variable \'doc\'. XML root element is stored in vairable \'rootElement\'. XML data as string are stored in variable \'data\'.';
                    break;
                default:
                    return 'Data are stored in variable \'data\'.';
                    break;
            }
        }

        //metodi per la validazione dei fields dei form
        function validateGeneralForm(form){
            $fieldName = $(form).find("input[name=nameMetric]");
            $fieldFrequency = $(form).find("input[name=frequencyMetric]");

            if($fieldName.hasClass('invalid-field')){
                $fieldName.removeClass('invalid-field');
            }
            if($fieldFrequency.hasClass('invalid-field')){
                $fieldFrequency.removeClass('invalid-field');
            }

            if($fieldName.val() == ''){
                $fieldName.addClass('invalid-field');
                $fieldName.focus();
                return false;
            }
            if($fieldFrequency.val() == ''|| $fieldFrequency.val() <= 0){
                $fieldFrequency.addClass('invalid-field');
                $fieldFrequency.focus();
                return false;
            }

            return true;
        }

        function validateConnectionForm(form){
            $fieldUrl = $(form).find("input[name=URL]");

            if($fieldUrl.hasClass('invalid-field')){
                $fieldUrl.removeClass('invalid-field');
            }

            if($fieldUrl.val() == ''){
                $fieldUrl.addClass('invalid-field');
                $fieldUrl.focus();
                return false;
            }

            return true;
        }

        function validateScriptForm(form){
            $fieldScript = $(form).find("input[name=script]");

            if($fieldScript.hasClass('invalid-field')){
                $fieldScript.removeClass('invalid-field');
            }

            if($fieldScript.val() == ''){
                $fieldScript.addClass('invalid-field');
                $fieldScript.focus();
                return false;
            }

            return true;
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
        
        $('#mainMenuCnt .mainMenuLink[id=<?= $_REQUEST['linkId'] ?>] div.mainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuPortraitCnt .mainMenuLink[id=<?= $_REQUEST['linkId'] ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        $('#mobMainMenuLandCnt .mainMenuLink[id=<?= $_REQUEST['linkId'] ?>] .mobMainMenuItemCnt').addClass("mainMenuItemCntActive");
        
        if($('div.mainMenuSubItemCnt').parents('a[id=<?= $_REQUEST['linkId'] ?>]').length > 0)
        {
            var fatherMenuId = $('div.mainMenuSubItemCnt').parents('a[id=<?= $_REQUEST['linkId'] ?>]').attr('data-fathermenuid');
            $("#" + fatherMenuId).attr('data-submenuVisible', 'true');
            $('#mainMenuCnt a.mainMenuSubItemLink[data-fatherMenuId=' + fatherMenuId + ']').show();
            $("#" + fatherMenuId).find('.submenuIndicator').removeClass('fa-caret-down');
            $("#" + fatherMenuId).find('.submenuIndicator').addClass('fa-caret-up');
            $('div.mainMenuSubItemCnt').parents('a[id=<?= $_REQUEST['linkId'] ?>]').find('div.mainMenuSubItemCnt').addClass("subMenuItemCntActive");
        }
            
        $('#color_hf').css("background-color", '#ffffff');
            
        $("#logoutBtn").off("click");
        $("#logoutBtn").click(function(event)
        {
           event.preventDefault();
           location.href = "logout.php";
        });
        
        //Parte Ravagli    
        datatableRef = $('#list_metrics').DataTable({
            "bLengthChange": false,
            "bInfo": false,
            "paging": true,
            "language": {search: ""},
            "pageLength": 8,
            aaSorting: [[0, 'desc']],
            "processing": true,
            "serverSide": true,
            "ajax": {
                async: true, 
                url: "../controllers/httpMetricsController.php?action=getMetricsList"
            },
            "createdRow": function (row, data, index) {
                $(row).attr('data-id', data[0]);
                $(row).attr('data-IdMetric', data[1]);
                
                $(row).find('.editDashBtn').click(function ()
                {
                    $('#modalModifyMetric').modal('show');
                    metricToModify = $(this).parents('tr').attr('data-IdMetric');
                    
                    $.ajax({
                        url: "../controllers/httpMetricsController.php?action=getSingleMetric&metricId=" + $(this).parents('tr').attr('data-id'),
                        type: "GET",
                        async: true,
                        dataType: 'json',
                        success: function (data) 
                        {
                            var scriptForm = $('#formScriptModifyMetric');
                            var connectionForm = $('#formConnectionModifyMetric');
                            var generalForm = $('#formGeneralModifyMetric');

                            generalForm.find('input[name="nameMetric"]').val(data.data[0][1]);
                            generalForm.find('textarea[name="descriptionMetric"]').val(data.data[0][2]);
                            generalForm.find('input[name="descriptionShortMetric"]').val(data.data[0][11]);
                            generalForm.find('select[name="areaMetric"]').val(data.data[0][9]);
                            generalForm.find('input[name="sourceMetric"]').val(data.data[0][10]);
                            generalForm.find('input[name="frequencyMetric"]').val(data.data[0][7]);
                            connectionForm.find('input[name="URL"]').val(data.data[0][12]);
                            connectionForm.find('input[name="username"]').val(data.data[0][14]);
                            connectionForm.find('input[name="password"]').val(data.data[0][15]);
                            connectionForm.find('select[name="rawDataType"]').val(data.data[0][5]);
                            scriptForm.find('select[name="metricType"]').val(data.data[0][6]),
                            scriptForm.find('textarea[name="script"]').val(data.data[0][4]);
                        },
                        error: function(errorData)
                        {

                        }
                    });
                });
                
                $(row).find('.delDashBtn').click(function ()
                {
                    metricToDelete = $(this).parents('tr').attr('data-IdMetric');
                    $('#modalDeleteMetric').modal('show');
                });
                
                $(row).find('.metricStatusChange').change(function ()
                {
                    var jsonRequest = {
                        metricId: $(this).parents('tr').attr('data-IdMetric'),
                        status: $(this).prop('checked')
                    };
                    
                    $.ajax({
                        headers: { 
                            'Accept': 'application/json',
                            'Content-Type': 'application/json' 
                        },
                        type: 'POST',
                        url: webServerURL + '/webservices/statusMetric',
                        data: JSON.stringify(jsonRequest),
                        error: function(data){
                            alert('Server error');
                            //$(this).prop('checked', !checkBox.checked);
                        },
                        success: function(data) {
                            if(!data.success){
                                alert(data.error);
                                //$(checkBox).prop('checked', !checkBox.checked);
                            }
                        },
                        complete: function()
                        {
                            datatableRef.ajax.reload();
                        }
                    });
                });
            },
            "columnDefs": [
                {
                    "targets": [0, 2, 3, 4, 8, 9, 10, 14, 15],
                    "visible": false
                },
                {
                    "targets": 13,
                    "searchable": true,
                    "render": function (data, type, row, meta) 
                    {
                        var checkbox = null;
                        if(row[13] === 'Attivo')
                        {
                            checkbox = '<input type="checkbox" class="metricStatusChange" checked></>';
                        }
                        else
                        {
                            checkbox = '<input type="checkbox" class="metricStatusChange"></>';
                        }
                        
                        return checkbox;
                    }
                },
                {
                    "targets": 16,
                    "searchable": false,
                    "render": function (data, type, row, meta) 
                    {
                        var actionBtns = '<button type="button" class="editDashBtn" style="background-color: rgb(69, 183, 175);">edit</button>&nbsp;<button type="button" class="delDashBtn" style="background-color: rgb(255, 204, 0);">del</button>';
                        return actionBtns;
                        
                    }
                },
            ]
        }); 
        
        $('#addMetricBtn').click(function()
        {
            $('#modalAddMetric').modal('show');
        });
        
        
        //visualizzazione del modal di inserimento di una metrica
        $('#modalAddMetric').on('shown.bs.modal', function () {
            //nascondo e resetto il suggerimento per lo script
            hintInsertScriptShowed = false;
            $('#buttonHintInsertScript').text('Show Hint');
            $('#containerHintInsertScript').hide();
            $('#containerHintInsertScript p').text('');

            //ripulisco gli input
            $('#modalAddMetric').find('input:text').val('');
            $('#modalAddMetric').find('textarea').val('');
            $('#modalAddMetric').find('select').prop('selectedIndex', 0);

            $fieldName = $('#formGeneralInsertMetric').find('input[name="nameMetric"]');
            if($fieldName.hasClass('invalid-field')){
                $fieldName.removeClass('invalid-field')
            }
            $fieldFrequency = $('#formGeneralInsertMetric').find('input[name="frequencyMetric"]');
            if($fieldFrequency.hasClass('invalid-field')){
                $fieldFrequency.removeClass('invalid-field')
            }
            $fieldUrl = $('#formConnectionInsertMetric').find('input[name="URL"]');
            if($fieldUrl.hasClass('invalid-field')){
                $fieldUrl.removeClass('invalid-field')
            }
            $fieldScript = $('#formScriptInsertMetric').find('input[name="script"]');
            if($fieldUrl.hasClass('invalid-field')){
                $fieldUrl.removeClass('invalid-field')
            }

            //ripulisco i campi di feedback
            $('#feedbackTestConn').text('');
            if($('#feedbackTestConn').hasClass('error-feedback')){
                $('#feedbackTestConn').removeClass('error-feedback');
            }
            $('#feedbackTestScript').text('');
            if($('#feedbackTestScript').hasClass('error-feedback')){
                $('#feedbackTestScript').removeClass('error-feedback');
            }
            $('#feedbackAddMetric').text('');
            if($('#feedbackAddMetric').hasClass('error-feedback')){
                $('#feedbackAddMetric').removeClass('error-feedback');
            }
            $('#rawDataInsertMetric').text('');
            $('#resultScriptInsertMetric').text('');
        });

        //cambio di selezione del tipo di dato raw (Inserimento metrica)
        $('#rawDataTypeInsertMetric').on('change', function(){
            if(hintInsertScriptShowed){
                var rawDataType = $(this).val();
                $('#containerHintInsertScript p').text(getHintByRawDataType(rawDataType));
            }
        });

        // submit del form di test della connessione (Inserimento metrica)
        $('#formConnectionInsertMetric').submit(function() {
            $connectionForm = $(this);

            $('#feedbackTestConn').text('');
            if($('#feedbackTestConn').hasClass('error-feedback')){
                $('#feedbackTestConn').removeClass('error-feedback');
            }

            //convalido il form
            if(validateConnectionForm(this)){
                //incapsulo i parametri in un JSON
                var jsonRequest = {
                    URL: $connectionForm.find('input[name="URL"]').val(),
                    username: $connectionForm.find('input[name="username"]').val(),
                    password: $connectionForm.find('input[name="password"]').val(),
                    typeRawData: $connectionForm.find('select[name="rawDataType"]').val()
                };

                $.ajax({
                    headers: { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json' 
                    },
                    type: 'POST',
                    url: webServerURL + '/webservices/testConnection',
                    data: JSON.stringify(jsonRequest),
                    error: function(data){
                        $('#feedbackTestConn').addClass('error-feedback');
                        $('#feedbackTestConn').text('Server error');
                        $('#rawDataInsertMetric').val('');
                    },
                    success: function(data) {
                        if(data.connected){
                            $('#feedbackTestConn').text('Successfully connected to the URL');
                            $('#rawDataInsertMetric').val(data.data);
                        }
                        else{
                            $('#feedbackTestConn').addClass('error-feedback');
                            $('#feedbackTestConn').text(data.error);
                            $('#rawDataInsertMetric').val('');
                        }
                    }
                });
            }
            else{
                $('#feedbackTestConn').addClass('error-feedback');
                $('#feedbackTestConn').text('Invalid input: check fields');
            }

            // prevent submitting again
            return false;
        });

        //click bottone suggerimento per script (Inserimento metrica)
        $('#buttonHintInsertScript').on('click', function () {
            if(hintInsertScriptShowed){
                hintInsertScriptShowed = false;
                $('#buttonHintInsertScript').text('Show Hint');
                $('#containerHintInsertScript').hide();
                $('#containerHintInsertScript p').text('');
            }
            else{
                hintInsertScriptShowed = true;
                $('#buttonHintInsertScript').text('Hide Hint');
                $('#containerHintInsertScript').show();
                var rawDataType = $('#formConnectionInsertMetric').find('select[name="rawDataType"]').val();
                $('#containerHintInsertScript p').text(getHintByRawDataType(rawDataType));
            }
        });

        // submit del form di test dello script (Inserimento metrica)
        $('#formScriptInsertMetric').submit(function() {
            $scriptForm = $(this);
            $connectionForm = $('#formConnectionInsertMetric');

            //ripulisco i campi di feedback
            $('#feedbackTestConn').text('');
            $('#feedbackTestScript').text('');
            if($('#feedbackTestScript').hasClass('error-feedback')){
                $('#feedbackTestScript').removeClass('error-feedback')
            }
            $('#rawDataInsertMetric').val('');
            $('#resultScriptInsertMetric').val('');

            //convalido il form con i parametri di connessione e quello con lo script
            if(validateConnectionForm($connectionForm) && validateScriptForm(this)){
                $('#feedbackTestScript').text('Testing script...');

                //incapsulo i parametri in un JSON
                var jsonRequest = {
                    URL: $connectionForm.find('input[name="URL"]').val(),
                    username: $connectionForm.find('input[name="username"]').val(),
                    password: $connectionForm.find('input[name="password"]').val(),
                    typeRawData: $connectionForm.find('select[name="rawDataType"]').val(),
                    typeMetric: $scriptForm.find('select[name="metricType"]').val(),
                    script: $scriptForm.find('textarea[name="script"]').val()
                };

                $.ajax({
                    headers: { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json' 
                    },
                    type: 'POST',
                    url: webServerURL + '/webservices/testScript',
                    data: JSON.stringify(jsonRequest),
                    error: function(data){
                        $('#feedbackTestScript').addClass('error-feedback');
                        $('#feedbackTestScript').text('Server error');
                        $('#rawDataInsertMetric').val('');
                        $('#resultScriptInsertMetric').val('');
                    },
                    success: function(data) {
                        if(data.success){
                            $('#feedbackTestScript').text('Script successfully executed: data extracted');
                            $('#rawDataInsertMetric').val(data.rawData);
                            $('#resultScriptInsertMetric').val(data.data);
                        }
                        else{
                            if(data.connected){
                                $('#feedbackTestScript').text(data.error);
                                $('#rawDataInsertMetric').val(data.rawData);
                                $('#resultScriptInsertMetric').val('');
                            }
                            else{
                                $('#feedbackTestScript').addClass('error-feedback');
                                $('#feedbackTestScript').text(data.error);
                                $('#rawDataInsertMetric').val('');
                                $('#resultScriptInsertMetric').val('');
                            }
                        }
                    }
                });
            }
            else{
                $('#feedbackTestScript').addClass('error-feedback');
                $('#feedbackTestScript').text('Invalid input: check fields');
            }

            // prevent submitting again
            return false;
        });

        //click bottone di inserimento metrica
        $('#buttonInsertNewMetric').on('click', function () {
            $scriptForm = $('#formScriptInsertMetric');
            $connectionForm = $('#formConnectionInsertMetric');
            $generalForm = $('#formGeneralInsertMetric');

            //ripulisco i campi di feedback
            $('#feedbackTestConn').text('');
            $('#feedbackTestScript').text('');
            $('#feedbackAddMetric').text('');
            if($('#feedbackAddMetric').hasClass('error-feedback')){
                $('#feedbackAddMetric').removeClass('error-feedback')
            }
            $('#rawDataInsertMetric').text('');
            $('#resultScriptInsertMetric').text('');

            //controllo che tutti i form siano stati compilati correttamente
            if(validateGeneralForm($generalForm) && validateConnectionForm($connectionForm) && validateScriptForm($scriptForm)){
                //incapsulo i parametri in un JSON
                var jsonRequest = {
                    metricName: $generalForm.find('input[name="nameMetric"]').val(),
                    metricDescription: $generalForm.find('textarea[name="descriptionMetric"]').val(),
                    metricDescriptionShort: $generalForm.find('input[name="descriptionShortMetric"]').val(),
                    metricArea: $generalForm.find('select[name="areaMetric"]').val(),
                    metricSource: $generalForm.find('input[name="sourceMetric"]').val(),
                    metricFrequency: $generalForm.find('input[name="frequencyMetric"]').val(),
                    URL: $connectionForm.find('input[name="URL"]').val(),
                    username: $connectionForm.find('input[name="username"]').val(),
                    password: $connectionForm.find('input[name="password"]').val(),
                    typeRawData: $connectionForm.find('select[name="rawDataType"]').val(),
                    typeMetric: $scriptForm.find('select[name="metricType"]').val(),
                    script: $scriptForm.find('textarea[name="script"]').val()
                };

                $.ajax({
                    headers: { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json' 
                    },
                    type: 'POST',
                    url: webServerURL + '/webservices/addMetric',
                    data: JSON.stringify(jsonRequest),
                    error: function(data){
                        $('#feedbackAddMetric').addClass('error-feedback');
                        $('#feedbackAddMetric').text('Server error');
                    },
                    success: function(data) 
                    {
                        if(data.success)
                        {
                            $('#modalAddMetric').modal('toggle');
                            alert("Metric successfully added");
                            datatableRef.ajax.reload();
                        }
                        else
                        {
                            $('#feedbackAddMetric').addClass('error-feedback');
                            $('#feedbackAddMetric').text(data.error);
                        }
                    }
                });
            }
            else
            {
                $('#feedbackAddMetric').addClass('error-feedback');
                $('#feedbackAddMetric').text('Invalid input: check fields');
            }
        });

        //visualizzazione del modal di modifica di una metrica
        $('#modalModifyMetric').on('shown.bs.modal', function () {
            //nascondo e resetto il suggerimento per lo script
            hintModifyScriptShowed = false;
            $('#buttonHintModifyScript').text('Show Hint');
            $('#containerHintModifyScript').hide();
            $('#containerHintModifyScript p').text('');


            //ripulisco i campi di input
            $fieldName = $('#formGeneralModifyMetric').find('input[name="nameMetric"]');
            if($fieldName.hasClass('invalid-field')){
                $fieldName.removeClass('invalid-field')
            }
            $fieldFrequency = $('#formGeneralModifyMetric').find('input[name="frequencyMetric"]');
            if($fieldFrequency.hasClass('invalid-field')){
                $fieldFrequency.removeClass('invalid-field')
            }
            $fieldUrl = $('#formConnectionModifyMetric').find('input[name="URL"]');
            if($fieldUrl.hasClass('invalid-field')){
                $fieldUrl.removeClass('invalid-field')
            }
            $fieldScript = $('#formScriptModifyMetric').find('input[name="script"]');
            if($fieldUrl.hasClass('invalid-field')){
                $fieldUrl.removeClass('invalid-field')
            }

            //ripulisco i campi di feedback
            $('#rawDataModifyMetric').val('');
            $('#resultScriptModifyMetric').val('');

            $('#feedbackTestConnModify').text('');
            if($('#feedbackTestConnModify').hasClass('error-feedback')){
                $('#feedbackTestConnModify').removeClass('error-feedback');
            }
            $('#feedbackTestScriptModify').text('');
            if($('#feedbackTestScriptModify').hasClass('error-feedback')){
                $('#feedbackTestScriptModify').removeClass('error-feedback');
            }
            $('#feedbackModifyMetric').text('');
            if($('#feedbackModifyMetric').hasClass('error-feedback')){
                $('#feedbackModifyMetric').removeClass('error-feedback');
            }
            $('#rawDataModifyMetric').text('');
            $('#resultScriptModifyMetric').text('');
        });

        //cambio di selezione del tipo di dato raw (Modifica metrica)
        $('#rawDataTypeModifyMetric').on('change', function(){
            if(hintModifyScriptShowed){
                var rawDataType = $(this).val();
                $('#containerHintModifyScript p').text(getHintByRawDataType(rawDataType));
            }
        });

        // submit del form di test della connessione (Modifica metrica)
        $('#formConnectionModifyMetric').submit(function() {
            $connectionForm = $(this);

            $('#feedbackTestConnModify').text('');
            if($('#feedbackTestConnModify').hasClass('error-feedback')){
                $('#feedbackTestConnModify').removeClass('error-feedback');
            }

            //convalido il form
            if(validateConnectionForm(this)){

                //incapsulo i parametri in un JSON
                var jsonRequest = {
                    URL: $connectionForm.find('input[name="URL"]').val(),
                    username: $connectionForm.find('input[name="username"]').val(),
                    password: $connectionForm.find('input[name="password"]').val(),
                    typeRawData: $connectionForm.find('select[name="rawDataType"]').val()
                };

                 $.ajax({
                    headers: { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json' 
                    },
                    type: 'POST',
                    url: webServerURL + '/webservices/testConnection',
                    data: JSON.stringify(jsonRequest),
                    error: function(data){
                        $('#feedbackTestConnModify').addClass('error-feedback');
                        $('#feedbackTestConnModify').text('Server error');
                        $('#rawDataModifyMetric').val('');
                    },
                    success: function(data) {
                        if(data.connected){
                           $('#feedbackTestConnModify').text('Successfully connected to the URL');
                           $('#rawDataModifyMetric').val(data.data);
                        }
                        else{
                           $('#feedbackTestConnModify').addClass('error-feedback');
                           $('#feedbackTestConnModify').text(data.error);
                           $('#rawDataModifyMetric').val('');
                        }
                    }
                 });
            }
            else{
                $('#feedbackTestConnModify').addClass('error-feedback');
                $('#feedbackTestConnModify').text('Invalid input: check fields');
            }

            // prevent submitting again
            return false;
        });

        //click bottone suggerimento per script (Modifica metrica)
        $('#buttonHintModifyScript').on('click', function () {
            if(hintModifyScriptShowed){
                hintModifyScriptShowed = false;
                $('#buttonHintModifyScript').text('Show Hint');
                $('#containerHintModifyScript').hide();
                $('#containerHintModifyScript p').text('');
            }
            else{
                hintModifyScriptShowed = true;
                $('#buttonHintModifyScript').text('Hide Hint');
                $('#containerHintModifyScript').show();
                var rawDataType = $('#formConnectionModifyMetric').find('select[name="rawDataType"]').val();
                $('#containerHintModifyScript p').text(getHintByRawDataType(rawDataType));
            }
        });

        // submit del form di test dello script (Modifica metrica)
        $('#formScriptModifyMetric').submit(function() {
            $scriptForm = $(this);
            $connectionForm = $('#formConnectionModifyMetric');

            //ripulisco i campi di feedback
            $('#feedbackTestConnModify').text('');
            $('#feedbackTestScriptModify').text('');
            if($('#feedbackTestScriptModify').hasClass('error-feedback')){
                $('#feedbackTestScriptModify').removeClass('error-feedback')
            }
            $('#rawDataModifyMetric').val('');
            $('#resultScriptModifyMetric').val('');

            //convalido il form con i parametri di connessione e quello con lo script
            if(validateConnectionForm($connectionForm) && validateScriptForm(this)){
                $('#feedbackTestScriptModify').text('Testing script...');

                //incapsulo i parametri in un JSON
                var jsonRequest = {
                    URL: $connectionForm.find('input[name="URL"]').val(),
                    username: $connectionForm.find('input[name="username"]').val(),
                    password: $connectionForm.find('input[name="password"]').val(),
                    typeRawData: $connectionForm.find('select[name="rawDataType"]').val(),
                    typeMetric: $scriptForm.find('select[name="metricType"]').val(),
                    script: $scriptForm.find('textarea[name="script"]').val()
                };

                $.ajax({
                    headers: { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json' 
                    },
                    type: 'POST',
                    url: webServerURL + '/webservices/testScript',
                    data: JSON.stringify(jsonRequest),
                    error: function(data){
                        $('#feedbackTestScriptModify').addClass('error-feedback');
                        $('#feedbackTestScriptModify').text('Server error');
                        $('#rawDataModifyMetric').val('');
                        $('#resultScriptModifyMetric').val('');
                    },
                    success: function(data) {
                        if(data.success){
                            $('#feedbackTestScriptModify').text('Script successfully executed: data extracted');
                            $('#rawDataModifyMetric').val(data.rawData);
                            $('#resultScriptModifyMetric').val(data.data);
                        }
                        else{
                            $('#feedbackTestScriptModify').addClass('error-feedback');
                            if(data.connected){
                                $('#feedbackTestScriptModify').text(data.error);
                                $('#rawDataModifyMetric').val(data.rawData);
                                $('#resultScriptModifyMetric').val('');
                            }
                            else{
                                $('#feedbackTestScriptModify').text(data.error);
                                $('#rawDataModifyMetric').val('');
                                $('#resultScriptModifyMetric').val('');
                            }
                        }
                    }
                });
            }
            else{
                $('#feedbackTestScriptModify').addClass('error-feedback');
                $('#feedbackTestScriptModify').text('Invalid input: check fields');
            }

            // prevent submitting again
            return false;
        });

        //click bottone di conferma modifica metrica
        $('#buttonModifyMetric').on('click', function () {
            $scriptForm = $('#formScriptModifyMetric');
            $connectionForm = $('#formConnectionModifyMetric');
            $generalForm = $('#formGeneralModifyMetric');

            //ripulisco i campi di feedback
            $('#feedbackTestConnModify').text('');
            $('#feedbackTestScriptModify').text('');
            $('#feedbackModifyMetric').text('');
            if($('#feedbackModifyMetric').hasClass('error-feedback')){
                $('#feedbackModifyMetric').removeClass('error-feedback')
            }
            $('#rawDataModifyMetric').val('');
            $('#resultScriptModifyMetric').val('');


            //controllo che tutti i form siano stati compilati correttamente
            if(validateGeneralForm($generalForm) && validateConnectionForm($connectionForm) && validateScriptForm($scriptForm)){
                //incapsulo i parametri in un JSON
                var jsonRequest = {
                    metricToModify: metricToModify,
                    metricName: $generalForm.find('input[name="nameMetric"]').val(),
                    metricDescription: $generalForm.find('textarea[name="descriptionMetric"]').val(),
                    metricDescriptionShort: $generalForm.find('input[name="descriptionShortMetric"]').val(),
                    metricArea: $generalForm.find('select[name="areaMetric"]').val(),
                    metricSource: $generalForm.find('input[name="sourceMetric"]').val(),
                    metricFrequency: $generalForm.find('input[name="frequencyMetric"]').val(),
                    URL: $connectionForm.find('input[name="URL"]').val(),
                    username: $connectionForm.find('input[name="username"]').val(),
                    password: $connectionForm.find('input[name="password"]').val(),
                    typeRawData: $connectionForm.find('select[name="rawDataType"]').val(),
                    typeMetric: $scriptForm.find('select[name="metricType"]').val(),
                    script: $scriptForm.find('textarea[name="script"]').val()
                };

                $.ajax({
                    headers: { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json' 
                    },
                    type: 'POST',
                    url: webServerURL + '/webservices/modifyMetric',
                    data: JSON.stringify(jsonRequest),
                    error: function(data){
                        $('#feedbackModifyMetric').addClass('error-feedback')
                        $('#feedbackModifyMetric').text('Server error');
                    },
                    success: function(data) {
                        if(data.success){
                            $('#modalModifyMetric').modal('toggle');
                            alert("Metric successfully modified");
                            //findAllMetrics();
                            datatableRef.ajax.reload();
                        }
                        else{
                            $('#feedbackModifyMetric').addClass('error-feedback')
                            $('#feedbackModifyMetric').text(data.error);
                        }
                    }
                });
            }
            else{
                $('#feedbackModifyMetric').addClass('error-feedback')
                $('#feedbackModifyMetric').text('Invalid input: check fields');
            }
        });

        //click bottone conferma cancellazione metrica
        $('#buttonDeleteMetric').on('click', function () {
            var jsonRequest = { metricId: metricToDelete };

             $.ajax({
                headers: { 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json' 
                },
                type: 'POST',
                url: webServerURL + '/webservices/deleteMetric',
                data: JSON.stringify(jsonRequest),
                error: function(data){
                    $('#modalDeleteMetric').modal('toggle');
                    alert('Server error');
                },
                success: function(data) {
                    $('#modalDeleteMetric').modal('toggle');
                    if(data.success){
                        alert('Metric deleted');
                        //findAllMetrics();
                        datatableRef.ajax.reload();
                    }
                    else{
                        alert(data.error);
                    }
                }
            });
        });
    });
</script>

<?php } else {
    include('../s4c-legacy-management/metricsHttpManagement.php');
}
?>