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
require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

session_start();

header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>
<style>
    table{
        table-layout: fixed;
        margin: 2%;
    }

    td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    td:hover {
        background-color: white;
    }

    th:hover {
        overflow: visible;
    }


    th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .content{
        overflow: auto;
    }
</style>
<script src="../js/DataTables/datatables.min.js" type="text/javascript"></script>
<script src="../js/DataTables/datatables.js" type="text/javascript"></script>
<script src="../js/DataTables/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="../js/DataTables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../js/DataTables/datatables.min.css">
<link rel="stylesheet" href="../js/DataTables/datatables.css">
<script type='text/javascript'>
    var dataSet = [];
    var devices = [];
    var temp = {};
    var specificData = "";
    var count = devices.length;
    var prefix = "http://www.disit.org/km4city/resource/iot/orionUNIFI/DISIT/";
    var ordering = 1;
    var sortOnValue = "";
    var icons = {
    car: "fa fa-car",
            velox: "fa fa-video-camera",
            earthquake: "fa fa-building",
            landslide: "fa fa-globe",
            theater: "fa fa-podcast",
            alarm: "fa fa-volume-up"
    }

    var template = {
    "colorStatus": {},
            "dateEnd": {},
            "dateObserved": {},
            "dateStart": {},
            "dateStartShow": {},
            "description": {},
            "eventKind": {},
            "eventSeverity": {},
            "eventType": {},
            "iconID": {},
            "measuredTime": {},
            "shownStatus": {},
            "status": {},
            "uniqueEventIdentifier": {},
    };

    var columnsToShow = {
    icon: "none",
            device: "none",
            description: "none",
            severity: "none",
            startDate: "none",
            endDate: "none",
            dateObserved: "none",
            type: "none",
            colorStatus: "none",
            dateStartShow: "none",
            eventKind: "none",
            shownStatus: "none",
            status: "none",
            uniqueEventIdentifier: "none",
            latitude: "none",
            longitude: "none",
            specificData: "none"
    }

    var actions = {
    pin: "hidden"
    }

    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) {
<?php
$link = mysqli_connect($host, $username, $password);
if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
    eventLog("Returned the following ERROR in widgetEvent.php for the widget " . escapeForHTML($_REQUEST['name_w']) . " is not instantiated or allowed in this dashboard.");
    exit();
        }?>

//ACCESS TOKEN 
    var access_token = '';
    var maintable_length = 5;
    var current_page = 0;
    var rowParameters_content = '';
    var n_rows = $('#n_rows').val();
    var sortOnValue = '';
    var order_sort = ':asc';
///
    $('#maintable thead').html("");
    $('#maintable tbody').html("");
    $('#paging_table').html("");
//


    var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
    var widgetName = "<?= $_REQUEST['name_w'] ?>";
    //   console.log("BarSeries: " + widgetName);
    var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
    var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
    var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
    var timeToReload = <?= sanitizeInt('frequency_w') ?>;
    var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
    var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_table");
    var metricData, metricType, series, index, styleParameters, chartSeriesObject, fontSize, fontColor, legendWidth,
            xAxisCategories, chartType, highchartsChartType, chartColor, dataLabelsFontColor,
            dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue,
            stackingOption, dataLabelsFontSize, chartLabelsFontSize,
            widgetHeight, metricName, aggregationGetData, getDataFinishCount, widgetTitle, countdownRef, chartRef,
            widgetParameters, infoJson, thresholdsJson, rowParameters, chartLabelsFontColor, appId, flowId,
            nrMetricType, groupByAttr, nrInputId, nodeRedInputName = null;
    var headerHeight = 25;
    var embedWidget = <?= $_REQUEST['embedWidget'] == 'true' ? 'true' : 'false' ?>;
    var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
    var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
    var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
    var showHeader = null;
    var pattern = /Percentuale\//;
    var seriesDataArray = [];
    var serviceUri = "";
    var flipFlag = false;
    var table = null;
    var dashboard_id = null;
    var isFirstLoad = 1;
    var missingFieldsDevices = {
    error: "Missing minimum fields in one or more devices",
            missingFieldsPerDevice: null
    };
//////////
    function reloadTable(start, end, current, mod){
    current_page = current;
    $('#maintable').DataTable().destroy();
    $('#maintable tbody').empty();
    maintable_length = $('#n_rows').val();
    //
    if (mod == 'mod2'){
    populateWidgetMod2(rowParameters_content);
    }
    if (mod == 'mod1'){
    populateWidgetMod1(rowParameters_content);
    }
    //
    }
/////////
    $('#searchlabel').on('keyup', function() {
    var searchlabel = $('#searchlabel').val();
    console.log(searchlabel);
    });
//
/////////
    function createTable(order, mod){
    $('#maintable').DataTable().destroy();
    var order = order;
    var order_type = $('#order').val();
    if (order_type == ""){
    order_type = 'desc';
    }
    if (mod == 2){
    $('.mod2').css('display', '');
    var table = $('#maintable').DataTable({
    "searching": false,
            "paging": false,
            "ordering": true,
            "autoWidth":true,
            "pageResize": true,
            "scrollCollapse": true,
            "order": [[order, order_type]],
            "info": false,
            "columnDefs": [	{ width: "20px", targets: "_all" } ],
            "responsive": false,
            "bAutoWidth": false,
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
    } else{
    $('.mod2').css('display', 'none');
    var table = $('#maintable').DataTable({
    "searching": true,
            "paging": true,
            "ordering": true,
            "autoWidth":true,
            "pageResize": true,
            "scrollCollapse": true,
            "order": [[order, order_type]],
            "info": false,
            "columnDefs": [	{ width: "20px", targets: "_all" } ],
            "responsive": true,
            "bAutoWidth": false,
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

    //$('#container').css( 'display', 'block' );
    //table.columns.adjust().draw();
    $('#maintable').DataTable().draw();
    ///
    $('.actionButton').click(function () {
    var data = table.row($(this).parents('tr')).data();
    var order = table.order();
    ordering = order[0][0];
    var data_pin = $(this).attr('value');
    var prefix = $(this).attr('prefix');
    var query = $(this).attr('query');
    //
    console.log(prefix);
    widgetName = data[0];
    //

    var dataToSend = {
    device: widgetName,
            prefix: prefix,
            query: query,
            ordering: order,
            action: data_pin
    };
    stdSend(dataToSend);
    });
    showWidgetContent(widgetName);
    table.columns.adjust();
    $("#maintable_filter").find("label").css("color", "black");
    ////

    }



    console.info(missingFieldsDevices);
    var test = {};
    //

    if (Window.webSockets == undefined)
            Window.webSockets = {};
    //

    function populateWidgetMod2(rowParameters_content) {
    var rowParameters = JSON.parse(rowParameters_content);
    //Execute API2
    var ordering = rowParameters.ordering;
    var order = 0;
    //	
    var sorting_desc = '';
    var sorting_asc = '';
    //
    sorting_asc = $('.sorting_asc').attr('value');
    if (sorting_asc != undefined){
    sortOnValue = sorting_asc + order_sort;
    $('#order_column').text(sortOnValue);
    $('#order').text('asc');
    }
    //
    sorting_desc = $('.sorting_desc').attr('value');
    if (sorting_desc != undefined){
    sortOnValue = sorting_desc + order_sort;
    $('#order_column').text(sortOnValue);
    $('#order').text('desc');
    }
    //
    if (access_token !== ''){
    var create_url = rowParameters.query + '&accessToken=' + access_token;
    } else{
    var create_url = rowParameters.query;
    }
    var columnsShow = rowParameters.columnsToShow;
    //MODALITA 2:
    //
    var fullCount = 0;
    var fromResult = '&fromResult=' + (current_page * maintable_length);
    var maxResults = '&maxResults=' + maintable_length;
    var sort_column = $('#order_column').val();
    var sort_order = $('#order').val();
    var order_column = '';
    if (sort_order != ''){
    order_column = ':' + sort_order;
    }
    var sortOnValue_content = '&sortOnValue=' + sort_column + order_column;
    create_url = create_url + fromResult + maxResults + sortOnValue_content;
    //
    dataSet = [];
    temp = {};
    specificData = "";
    $.ajax({
    url: create_url,
            type: "GET",
            data: {},
            async: false,
            timeout: 0,
            dataType: 'json',
            success: function (data) {

            var features = data.features;
            var Count = features.length;
            var fullCount = data.fullCount;
            if (Count > 0){
            var features = data.features;
            var values = features[0].properties.values;
            var keys = Object.keys(values);
            //
            var n_page = Math.ceil(fullCount / maintable_length);
            //
            if (maintable_length < Count){
            Count = maintable_length;
            }

            for (var i = 0; i < Count; i++) {
            //
            var row_content = '<td>' + features[i].properties.deviceName + '</td>';
            //					

            for (var z = 0; z < columnsShow.length; z++) {
            if (columnsShow[z] in values){
            var name = columnsShow[z];
            var c1 = values[name];
            if (sortOnValue == name){
            order = z + 1;
            }
            row_content = row_content + '<td>' + features[i].properties.values[name] + '</td>';
            } else{
            row_content = row_content + '<td></td>';
            }
            }

            if (rowParameters.actions != undefined){
            var acts = rowParameters.actions;
            var cont_icon = '';
            row_content = row_content + '<td>';
            for (var r = 0; r < acts.length; r++){

            if (acts[r] == "pin"){
            cont_icon = cont_icon + '<button class="btn actionButton pin" style="margin-left: 10px" value="pin" query="' + create_url + '"><i style="font-size: 40px" class="fa fa-map-marker"  aria-hidden="true"></i></button>';
            } else{
            cont_icon = cont_icon + '<button class="btn actionButton pin" style="margin-left: 10px" value="' + acts[r] + '" query="' + create_url + '"><img style="max-width:40px; width: auto;"  src="' + acts[r] + '"/></button>';
            }
            }
            row_content = row_content + cont_icon + '</td>';
            }
            //
            $('#maintable tbody').append('<tr>' + row_content + '</tr>');
            }
            ////
            console.log('order: ' + order);
            var mod = 2;
            var order = $('#current_order').val();
            createTable(order, mod);
            //
            }
            ///////
            var last_page = 0;
            last_page = (fullCount - maintable_length);
            var list_links = '';
            var first_int = 0;
            var end_int = 0;
            end_int = (first_int + maintable_length);
            for (var i = 0; i < n_page; i++){
            var n = i + 1;
            var active = '';
            if (i == current_page){
            active = 'active';
            } else{
            active = '';
            }
            list_links = list_links + '<li class="paginate_button ' + active + '" id="page_' + i + '" start="' + first_int + '" end="' + end_int + '"current=' + i + '><a href="#"  id="page_link_' + i + '" aria-controls="maintable" data-dt-idx="2" tabindex="0" start="' + first_int + '" end="' + end_int + '">' + n + '</a></li>';
            first_int = end_int;
            end_int = + maintable_length;
            }
            ///
            //
            ////
            var dis_next = "";
            console.log('current_page: ' + current_page);
            console.log('i: ' + i);
            if (current_page == i - 1){
            dis_next = "disabled";
            } else{
            dis_next = "";
            }
            //
            var dis_prev = "";
            if (current_page == 0){
            dis_prev = "disabled";
            } else{
            dis_prev = "";
            }
            //
            var next = current_page++;
            var prev = current_page - 1;
            $('#paging_table').html('<ul class="pagination"><li class="paginate_button' + dis_prev + ' first ' + dis_prev + '" id="maintable_first" tabindex="0" start="0" end="' + maintable_length + '" current="0"><a href="#" aria-controls="maintable" data-dt-idx="0" tabindex="0" start="0" end="' + maintable_length + '">First</a></li><li class="paginate_button' + dis_prev + ' previous ' + dis_prev + '" id="maintable_previous" start="' + first_int + '" end="' + end_int + '" current=' + (prev - 1) + '><a href="#" aria-controls="maintable" data-dt-idx="1" tabindex="0">&lt;&lt; Prev</a></li>' + list_links + '<li class="paginate_button' + dis_next + ' next ' + dis_next + '" id="maintable_next" start="' + first_int + '" end="' + end_int + '"current="' + (next + 1) + '"><a href="#" aria-controls="maintable" data-dt-idx="3" tabindex="0">Next &gt;&gt;</a></li><li class="paginate_button' + dis_next + ' last ' + dis_next + '" id="maintable_last" start="' + first_int + '" end="' + maintable_length + '"current=' + (n_page - 1) + '><a href="#" aria-controls="maintable" data-dt-idx="4" tabindex="0" start="' + first_int + '" end="' + end_int + '">Last</a></li></ul>');
            //
            }
    });
    /////
    $('.paginate_button').on('click', function () {
    var start = $(this).attr('start');
    var end = $(this).attr('end');
    var current = $(this).attr('current');
    reloadTable(start, end, current, 'mod2');
    });
    $('#n_rows').on('change', function() {
    maintable_length = $('#n_rows').val();
    reloadTable(0, maintable_length, 0, 'mod2');
    });
    /////////////
    $('.column_th').on('click', function() {
    $('#current_order').val($(this).attr('n_col'));
    var ml = $('#n_rows').val();
    if ($(this).attr('value') !== 'deviceName'){
    $('#order_column').val($(this).attr('value') + '.value');
    } else{
    $('#order_column').val($(this).attr('value'));
    }
    var clicked = $(this).attr('value');
    var type = jQuery.type(clicked);
    var order_cl = $('#order').val();
    if (order_cl == ""){
    $('#order').val('asc');
    }
    //	
    if (sorting_asc == $(this).attr('value')){
    $('#order').val('desc');
    order_cl = 'desc';
    }
    if (sorting_desc == $(this).attr('value')){
    $('#order').val('asc');
    order_cl = 'asc';
    }
    //					
    var check_val = $('#order_column').val();
    if (check_val == ""){
    }
    reloadTable(0, ml, 0, 'mod2');
    //////
    });
    //
    //
    }

    function populateWidgetMod1(rowParameters) {

    content_rp = JSON.parse(rowParameters);
    var columnsShow = content_rp.columnsToShow;
    var devices = content_rp.devices;
    var ordering = $('#order_column').attr('value');
    var sorting_desc = '';
    var sorting_asc = '';
    //
    sorting_asc = $('.sorting_asc').attr('value');
    if (sorting_asc != undefined){
    sortOnValue = sorting_asc + order_sort;
    $('#order_column').text(sortOnValue);
    $('#order').text('asc');
    }
    //
    sorting_desc = $('.sorting_desc').attr('value');
    if (sorting_desc != undefined){
    sortOnValue = sorting_desc + order_sort;
    $('#order_column').text(sortOnValue);
    $('#order').text('desc');
    }


    //MODALITA 1:
    var order = 0;
    dataSet = [];
    temp = {};
    specificData = "";
    count = devices.length;
    console.log(count);
    var n_page = Math.ceil(count / maintable_length);
    //
    var current_start = current_page * maintable_length;
    var current_end = current_start + maintable_length;
    if (count !== 0) {
    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
    for (var i = 0; i < devices.length; i++) {
    //for (var i = current_start; i < current_end; i++) {
    var current_device = devices[i];
    $.ajax({
    url: "https://www.snap4city.org/superservicemap/api/v1/?serviceUri=" + prefix + current_device + "&format=json",
            type: "GET",
            data: {},
            async: false,
            timeout: 0,
            dataType: 'json',
            success: function (data) {
            if (data.realtime.results !== undefined){
            for (var i = 0; i < data.realtime.results.bindings.length; i++) {
            temp = data.realtime.results.bindings[i];
            //
            var row_content = '';
            var device_name = data.Service.features[0].properties.name;
            row_content = '<td>' + device_name + '</td>';
            for (var z = 0; z < columnsShow.length; z++) {
            if (columnsShow[z] in temp){
            var name = columnsShow[z];
            if (ordering == name){
            order = z + 1;
            }
            var c1 = temp[name].value;
            row_content = row_content + '<td>' + c1 + '</td>';
            } else{
            row_content = row_content + '<td></td>';
            }
            }
            //
            if (content_rp.actions != undefined){
            var acts = content_rp.actions;
            var cont_icon = '';
            row_content = row_content + '<td>';
            for (var r = 0; r < acts.length; r++){
            if (acts[r] == "pin"){
            cont_icon = cont_icon + '<button class="btn actionButton" style="margin-left: 10px" value="pin" prefix="' + prefix + '"><i style="font-size: 40px" class="fa fa-map-marker"  aria-hidden="true"></i></button>';
            } else{
            cont_icon = cont_icon + '<button class="btn actionButton" style="margin-left: 10px" value="' + acts[r] + '" prefix="' + prefix + '"><img style="max-width:40px; width: auto;" src="' + acts[r] + '"/></button>';
            }
            }
            row_content = row_content + cont_icon + '</td>';
            }
            $('#maintable tbody').append('<tr>' + row_content + '</tr>');
            }
            }
            },
            error: function () {
            count--;
            }
    });
    ///			
    }


    /////////////////
    var mod = 1;
    createTable(order, mod);
    } else {
    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
    showWidgetContent(widgetName);
    }

    }

    //Definizioni di funzione specifiche del widget


    function resizeWidget() {
    setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
    //$('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
    }

    //Fine definizioni di funzione

    $(document).off('changeMetricFromButton_' + widgetName);
    $(document).on('changeMetricFromButton_' + widgetName, function (event) {
    if ((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange")) {
    clearInterval(countdownRef);
    $("#<?= $_REQUEST['name_w'] ?>_content").hide();
<?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null);
    }
    });
    $(document).off('resizeHighchart_' + widgetName);
    $(document).on('resizeHighchart_' + widgetName, function (event) {
    showHeader = event.showHeader;
    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
    });
    function addButtonsListerners(){
    table.on('search.dt', function () {
    var order = table.order();
    ordering = order[0][0];
    var dataToSend = {
    device: "",
            prefix: "",
            ordering: "",
            action: "filter: " + table.search()
    };
    stdSend(dataToSend);
    });
    $('#maintable tbody').on('click', 'td.expand-content', function () {
    var data = table.row($(this).parents('tr')).data();
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var order = table.order();
    ordering = order[0][0];
    var action;
    if (!row.child.isShown()){
    action = "closed";
    } else{
    action = "expanded";
    }

    var dataToSend = {
    device: "",
            prefix: "",
            ordering: "",
            action: action
    };
    stdSend(dataToSend);
    });
    $('#maintable').on('order.dt', function () {
    if (table !== null){
    var order = table.order();
    ordering = order[0][0];
    var dataToSend = {
    device: " ",
            prefix: "",
            ordering: "",
            action: "changedOrdering"
    };
    stdSend(dataToSend);
    }
    });
    }

    //Nuova versione
    $.ajax({
    url: "../controllers/getWidgetParams.php",
            type: "GET",
            data: {
            widgetName: "<?= $_REQUEST['name_w'] ?>"
            },
            async: true,
            dataType: 'json',
            success: function (widgetData) {
            showTitle = widgetData.params.showTitle;
            widgetContentColor = widgetData.params.color_w;
            fontSize = widgetData.params.fontSize;
            fontColor = widgetData.params.fontColor;
            timeToReload = widgetData.params.frequency_w;
            hasTimer = widgetData.params.hasTimer;
            chartColor = widgetData.params.chartColor;
            dataLabelsFontSize = widgetData.params.dataLabelsFontSize;
            dataLabelsFontColor = widgetData.params.dataLabelsFontColor;
            chartLabelsFontSize = widgetData.params.chartLabelsFontSize;
            chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
            appId = widgetData.params.appId;
            flowId = widgetData.params.flowId;
            nrMetricType = widgetData.params.nrMetricType;
            serviceUri = widgetData.params.serviceUri;
            nrInputId = widgetData.params.nrInputId;
            nodeRedInputName = widgetData.params.name;
            dashboard_id = widgetData.params.id_dashboard;
            //
            //console.log("widgetData.params:");
            //console.log(widgetData.params);

            if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
            showHeader = false;
            } else {
            showHeader = true;
            }

            if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) {
            metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
            widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
            widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
            widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
            rowParameters = widgetData.params.rowParameters;
            } else {
            metricName = metricNameFromDriver;
            widgetTitleFromDriver.replace(/_/g, " ");
            widgetTitleFromDriver.replace(/\'/g, "&apos;");
            widgetTitle = widgetTitleFromDriver;
            $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
            widgetHeaderColor = widgetHeaderColorFromDriver;
            widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
            }

            openWs(widgetName);
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            if ((rowParameters !== null) && (isJsonString(rowParameters))) {
            var newValue = JSON.parse(rowParameters);
            setupLoadingPanel(widgetName, widgetContentColor, true);
            ordering = (Object.keys(columnsToShow)).indexOf(newValue.ordering);
            prefix = newValue.prefix;
            devices = newValue.devices;
            var customActionNumber = 1;
            for (let i = 0; i < newValue.actions.length; i++) {
            if (actions[newValue.actions[i]] !== undefined){
            actions[newValue.actions[i]] = "show";
            } else{
            actions["custom" + customActionNumber] = newValue.actions[i];
            customActionNumber++;
            }
            }

            for (let i = 0; i < newValue.columnsToShow.length; i++) {
            columnsToShow[newValue.columnsToShow[i]] = "";
            }
            }


            if (isJsonString(rowParameters)){
            $("#maintable tbody").empty();
            $("#maintable thead").empty();
            var newValue = JSON.parse(rowParameters);
            var columnsShow = newValue.columnsToShow;
            var content_header = '';
            content_header = '<th class="column_th" value="deviceName" n_col=0>Device</th>';
            for (var y = 0; y < columnsShow.length; y++) {
            content_header = content_header + '<th class="column_th" n_col=' + (y + 1) + ' value="' + columnsShow[y] + '">' + columnsShow[y] + '</th>';
            }
            if (newValue.actions != undefined){
            var acts = newValue.actions;
            content_header = content_header + '<th n_col=' + (y + 1) + '>Actions</th>';
            }
            $('#maintable thead').append('<tr>' + content_header + '</tr>');
            content_rp = JSON.parse(rowParameters);
            ////
            rowParameters_content = rowParameters;
            if (content_rp.query !== undefined){
            populateWidgetMod2(rowParameters);
            } else{
            populateWidgetMod1(rowParameters);
            }
            $('.content').css('display', 'block');
            $('.loadingDiv').css('display', 'none');
            }

            //populateWidget(rowParameters);
            },
            error: function (errorData) {
            console.log("Error in widget params retrieval");
            console.log(JSON.stringify(errorData));
            showWidgetContent(widgetName);
            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
            }
    });
    $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function (event) {
    resizeWidget();
    });
    $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
    $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function (event) {
    clearInterval(countdownRef);
    timeToReload = event.newTimeToReload;
    countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
    });
    countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
    //Fine del codice core del widget


    var openWs = function (widget) // this one is the only function that is called from the above
    {
    try {
<?php
$genFileContent = parse_ini_file("../conf/environment.ini");
$wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
$wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$genFileContent['environment']['value']];
$wsServerPort = $wsServerContent["wsServerPort"][$genFileContent['environment']['value']];
$wsPath = $wsServerContent["wsServerPath"][$genFileContent['environment']['value']];
$wsProtocol = $wsServerContent["wsServerProtocol"][$genFileContent['environment']['value']];
$wsRetryActive = $wsServerContent["wsServerRetryActive"][$genFileContent['environment']['value']];
$wsRetryTime = $wsServerContent["wsServerRetryTime"][$genFileContent['environment']['value']];
echo 'wsRetryActive = "' . $wsRetryActive . '";' . "\n";
echo 'wsRetryTime = ' . $wsRetryTime . ';' . "\n";
echo 'wsUrl="' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '";' . "\n";
?>
    // console.log(wsUrl);
    initWebsocket(widget, wsUrl, null, wsRetryTime * 1000, function (socket) {
    // console.log('socket initialized!');
    openWsConn(widget);
    }, function () {
    console.log('Form Widget error: could not connect to this socket:');
    console.log(wsUrl);
    });
    } catch (e) {
    wsClosed();
    }
    };
    var manageIncomingWsMsg = function (msg) {
    var data = JSON.parse(msg.data);
    try {
    console.log("I have received:");
    console.log(msg);
    if (data.msgType === "SendToEmitter" && data.result === "Ok" && data.widgetUniqueName === "<?= $_REQUEST['name_w'] ?>") {
    accepted = data.msgId;
    // console.log("My message # "+accepted+" has been accepted by the socket server, that is now attempting to deliver it to the IoT App.");
    return;
    }

    if (data.msgType === "DataToEmitter" && data.widgetUniqueName === "<?= $_REQUEST['name_w'] ?>") {
    var newValue = JSON.parse(data.newValue);
    //console.info(newValue);

    if (newValue.devices && newValue.actions && newValue.prefix && newValue.ordering && newValue.columnsToShow) {
    setupLoadingPanel(widgetName, widgetContentColor, true);
    ordering = (Object.keys(columnsToShow)).indexOf(newValue.ordering);
    prefix = newValue.prefix;
    devices = newValue.devices;
    actions = {pin: "hidden"};
    var customActionNumber = 1;
    for (let i = 0; i < newValue.actions.length; i++) {
    if (actions[newValue.actions[i]] !== undefined){
    actions[newValue.actions[i]] = "show";
    } else{
    actions["custom" + customActionNumber] = newValue.actions[i];
    customActionNumber++;
    }
    }

    for (let key in columnsToShow) {
    columnsToShow[key] = "none";
    }

    for (let i = 0; i < newValue.columnsToShow.length; i++) {
    columnsToShow[newValue.columnsToShow[i]] = "";
    }

    if (table !== null) {
    table.clear();
    table.destroy();
    $("#maintable tbody").empty();
    $("#maintable thead").empty();
    table = null;
    }

    missingFieldsDevices.missingFieldsPerDevice = null;
    //CONTROLLO JSON
    rowParameters_content = rowParameters;
    if (isJsonString(rowParameters)){
    content_rp = JSON.parse(rowParameters);
    if (content_rp.query !== undefined){
    populateWidgetMod2(rowParameters);
    } else{
    populateWidgetMod1(rowParameters);
    }
    }

    }
    return;
    }

    if (data.msgType === "DataToEmitterAck" && data.msgId === accepted && data.widgetUniqueName === "<?= $_REQUEST['name_w'] ?>") {
    // console.log("My message # "+accepted+" has been acknowledged by the IoT App.");
    accepted = null;
    var webSocket = Window.webSockets[data.widgetUniqueName];
    if (!webSocket.ackReceived) {
    clearTimeout(webSocket.timeout);
    webSocket.ackReceived = true;
    // console.log(data.widgetUniqueName + " ACK ackReceived:" + webSocket.ackReceived)
    webSocket.onAck({result: "Ok", widgetName: data.widgetUniqueName});
    }
    $("#<?= $_REQUEST['name_w'] ?>_formContainer form").css("opacity", 1);
    return;
    }


    } catch (e) {
    console.log(e);
    }

    };
    timeToReload = 200;
    var openWsConn = function (widget) {
    var webSocket = Window.webSockets[widget];
    var wsRegistration = {
    msgType: "ClientWidgetRegistration",
            userType: "widgetInstance",
            widgetUniqueName: "<?= $_REQUEST['name_w'] ?>"
    };
    webSocket.send(JSON.stringify(wsRegistration));
    webSocket.addEventListener('message', manageIncomingWsMsg);
    };
    var wsClosed = function (e) {
    var webSocket = Window.webSockets["<?= $_REQUEST['name_w'] ?>"];
    webSocket.removeEventListener('message', manageIncomingWsMsg);
    if (wsRetryActive === 'yes') {
    setTimeout(openWs, parseInt(wsRetryTime * 1000));
    }
    };
    var stdSend = function (newValue) {
    var data = {
    "msgType": "SendToEmitter",
            "widgetUniqueName": widgetName,
            "value": JSON.stringify(newValue),
            "inputName": "",
            "dashboardId": dashboard_id,
            "username": $('#authForm #hiddenUsername').val(),
            "nrInputId": nrInputId
    };
    console.log("Sending...");
    console.log(data);
    var webSocket = Window.webSockets[widgetName];
    webSocket.ackReceived = false;
    webSocket.onAck = function (data) {
    // console.log(widgetName+" SUCCESS ackReceived:"+webSocket.ackReceived)
    //clearInterval(setUpdatingMsgInterval);
    switch (data.result) {
    case "insertQueryKo":
            showUpdateResult("DB KO");
    break;
    case "updateBlockKo":
            showUpdateResult("Device KO");
    break;
    case "updateBlockAndUpdateQueryKo":
            showUpdateResult("DB and device KO");
    break;
    case "updateQueryKo":
            showUpdateResult("DB KO");
    break;
    case "Ok":
            showUpdateResult("Device OK");
    break;
    }
    }
    //console.log(widgetName+" SEND ackReceived:"+webSocket.ackReceived)
    if (webSocket.readyState == webSocket.OPEN) {
    webSocket.send(JSON.stringify(data));
    console.log("I have sent:");
    console.log(JSON.stringify(data));
    webSocket.timeout = setTimeout(function () {
    if (!webSocket.ackReceived) {
    console.log(widgetName + " ERR1 ackReceived:" + webSocket.ackReceived)
            showUpdateResult("API KO");
    console.log("Update value KO");
    }
    }, 60000)
    } else {
    console.log(widgetName + " ERR1 socket not OPEN");
    showUpdateResult("API KO");
    }
    };
    function initWebsocket(widget, url, existingWebsocket, retryTimeMs, success, failed) {
    if (!existingWebsocket || existingWebsocket.readyState != existingWebsocket.OPEN) {
    if (existingWebsocket) {
    existingWebsocket.close();
    }
    var websocket = new WebSocket(url);
    websocket.widget = widget;
    // console.log("store websocket for "+widget)
    Window.webSockets[widget] = websocket;
    websocket.onopen = function () {
    // console.info('websocket opened! url: ' + url);
    success(websocket);
    };
    websocket.onclose = function () {
    // console.info('websocket closed! url: ' + url + " reconnect in "+retryTimeMs+"ms");
    //reconnect after a retryTime
    setTimeout(function () {
    initWebsocket(widget, url, existingWebsocket, retryTimeMs, success, failed);
    }, retryTimeMs);
    };
    websocket.onerror = function (e) {
    console.info('websocket error! url: ' + url);
    console.info(e);
    };
    } else {
    success(existingWebsocket);
    }

    return;
    };
    function showUpdateResult(msg) {

    }

    function isJsonString(str) {
    try {
    JSON.parse(str);
    } catch (e) {
    return false;
    }
    return true;
    }

    });
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
<?php include '../widgets/widgetHeader.php'; ?>
<?php include '../widgets/widgetCtxMenu.php'; ?>

        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data...</p>
            </div>
            <div class="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>

        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content" style="padding:1%">
<?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>No Data Available</p>
            <input type="text" id="order_column" value="deviceName" style="display: none;"/>
            <input type="text" id="order" value="desc" style="display: none;"/>
            <input type="text" id="current_order" value=0 style="display: none;"/>
            <label class="mod2">Show	<select id="n_rows" aria-controls="maintable" class="form-control input-sm"><option value="5">5</option><option value="10">10</option><option value="20">20</option><option value="50">50</option></select> </label>
            <div class="pull-right mod2"><div id="maintable_filter" class="dataTables_filter"><label style="color: rgb(0, 0, 0);">Search:<input type="search" class="form-control input-sm" placeholder="" aria-controls="maintable" id="searchlabel"></label></div></div>
            <table id="maintable" class="table table-striped table-bordered display responsive" style="width:100%;"><thead></thead><tbody></tbody></table>
            <div id="paging_table" class="mod2"></div>
        </div>
    </div>
</div>