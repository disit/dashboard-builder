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
header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>

<script type='text/javascript'>
    var dataSet = [];
    var devices = [];
    var temp = {};
    var specificData = "";
    var count = devices.length;
    var prefix = "http://www.disit.org/km4city/resource/iot/orionUNIFI/DISIT/";
    var ordering = 1;

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
		//
		$(document).off('showWidgetEventFromExternalContent_' + widgetName);
        $(document).on('showWidgetEventFromExternalContent_' + widgetName, function(event){
		console.log('showWidgetEventFromExternalContent_AddCode!');
				if(encodeURIComponent(metricName) === encodeURIComponent(metricName))
                    {
                       var newValue = event.passedData;
						populateWidget();

                    }
		});
		//

        console.info(missingFieldsDevices);
        var test = {};
        console.info(test);

        if (Window.webSockets == undefined)
            Window.webSockets = {};

        console.log("Entrato in widgetEvent --> " + widgetName);

        function createTable() {
            table = $('#maintable').DataTable({
                data: dataSet,
                scrollResize: true,
                scrollY: 200,
                scrollCollapse: true,
                paging: false,
                "order": [[ordering, "desc"]],
                columns: [
                    {
                        "data": null, className: "expand-content all dt-center", orderable: false, title: "Icon",
                        "render": function (data, type, row, meta) {
                            if (icons[data.iconID.value] != null) {
                                return '<i style="font-size: 40px" class="' + icons[data.iconID.value] + '" aria-hidden="true"></i>'
                            } else {
                                return '<img style="width:40px" src="' + data.iconID.value + '"/>'
                            }

                        }
                    },
                    {title: "Device", "data": "device", className: columnsToShow["device"]},
                    {title: "Description", "data": "description.value", className: columnsToShow["description"]},
                    {title: "Severity", "data": "eventSeverity.value", className: columnsToShow["severity"]},
                    {title: "StartDate", "data": "dateStart.value", className: columnsToShow["startDate"]},
                    {title: "EndDate", "data": "dateEnd.value", className: columnsToShow["endDate"]},
                    {title: "DateObserved", "data": "dateObserved.value", className: columnsToShow["dateObserved"]},
                    {title: "Type", "data": "eventType.value", className: columnsToShow["type"]},
                    {title: "ColorStatus", "data": "colorStatus.value", className: columnsToShow["colorStatus"]},
                    {title: "DateStartShow", "data": "dateStartShow.value", className: columnsToShow["dateStartShow"]},
                    {title: "EventKind", "data": "eventKind.value", className: columnsToShow["eventKind"]},
                    {title: "ShownStatus", "data": "shownStatus.value", className: columnsToShow["shownStatus"]},
                    {title: "Status", "data": "status.value", className: columnsToShow["status"]},
                    {
                        title: "UniqueEventIdentifier",
                        "data": "uniqueEventIdentifier.value",
                        className: columnsToShow["uniqueEventIdentifier"]
                    },
                    {title: "Latitude", "data": "latitude", className: columnsToShow["latitude"]},
                    {title: "Longitude", "data": "longitude", className: columnsToShow["longitude"]},
                    {title: "SpecificData", "data": "specificData", className: columnsToShow["specificData"]},
                    {
                        "data": null, className: "all dt-center", orderable: false, title: "Actions",
                        "render": function (data, type, row, meta) {
                            var body = "";
                            for (let key in actions) {
                                if(key === "pin" && actions[key] === "show"){
                                    body += '<button id = "pin" class="btn actionButton" style="margin-left: 10px"><i style="font-size: 40px" class="fa fa-map-marker" aria-hidden="true"></i></button>';
                                }else{
                                    body += '<button id = "' + key + '" class="btn actionButton" style="margin-left: 10px"><img style="width:40px" src="' + actions[key] + '"/></button>';
                                }
                            }
                            return body;
                        }
                    }
                ],
                rowCallback: function (row, data, index) {
                    //console.log(data.iconID.value);
                    $(row).find('td:eq(0)').css('background-color', data.colorStatus.value);
                    $('.dataTables_scrollBody').css('overflow-x', 'hidden');
                }
            });

             $('.actionButton').click(function () {
                var data = table.row($(this).parents('tr')).data();
                var order = table.order();
                ordering = order[0][0];

                var dataToSend = {
                    device: data.device,
                    prefix: prefix,
                    ordering: (Object.keys(columnsToShow))[ordering],
                    action: this.id
                };

                stdSend(dataToSend);
				///ACTIVE SCRIPT///
				if((code !== null)&&(code !== '')){
					console.log('dataToSend');
					
					data = dataToSend;
					execute_<?= $_REQUEST['name_w'] ?>(data);
				}
            });


            if(isFirstLoad) {
                addButtonsListerners()
                isFirstLoad = 0;
            }

            showWidgetContent(widgetName);
            table.columns.adjust();
            $("#maintable_filter").find("label").css("color", "black");
        }

        function populateWidget() {
            dataSet = [];
            temp = {};
            specificData = "";
            count = devices.length;

            if (count !== 0) {
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                for (var i = 0; i < devices.length; i++) {
                    $.ajax({
                        url: "https://www.snap4city.org/superservicemap/api/v1/?serviceUri=" + prefix + devices[i] + "&format=json",
                        type: "GET",
                        data: {},
                        async: true,
                        timeout: 0,
                        dataType: 'json',

                        success: function (data) {
                            //console.log(data);
                            for (var i = 0; i < data.realtime.results.bindings.length; i++) {
                                temp = data.realtime.results.bindings[i];

                                var missing = Object
                                    .keys(template)
                                    .reduce(function (r, key) {
                                        if (!(key in temp)) r[key] = template[key]
                                        return r
                                    }, {})

                                if (!(jQuery.isEmptyObject(missing))) {
                                    if(missingFieldsDevices.missingFieldsPerDevice === null){
                                        missingFieldsDevices.missingFieldsPerDevice = {};
                                    }

                                    var missingString = "";
                                    for (var key in missing) {
                                        if (missing.hasOwnProperty(key)) {
                                            missingString = missingString.concat(key + "; ");
                                        }
                                    }

                                    missingFieldsDevices.missingFieldsPerDevice[(data.Service.features[0].properties.serviceUri).replace(prefix, "")] = missingString;

                                } else {
                                    var diff = Object
                                        .keys(temp)
                                        .reduce(function (r, key) {
                                            if (!(key in template)) r[key] = temp[key]
                                            return r
                                        }, {})

                                    for (var key in diff) {
                                        if (diff.hasOwnProperty(key)) {
                                            specificData = specificData.concat(key + ": " + diff[key].value + "; ");
                                        }
                                    }

                                    //console.log(temp);
                                    temp.device = data.Service.features[0].properties.name;
                                    temp.longitude = data.Service.features[0].geometry.coordinates[0];
                                    temp.latitude = data.Service.features[0].geometry.coordinates[1];
                                    temp.specificData = specificData;
                                    specificData = "";
                                    temp.dateStart.value = new Date(temp.dateStart.value).toLocaleString();
                                    temp.dateEnd.value = new Date(temp.dateEnd.value).toLocaleString();
                                    temp.dateStartShow.value = new Date(temp.dateStartShow.value).toLocaleString();
                                    temp.dateObserved.value = new Date(temp.dateObserved.value).toLocaleString();
                                    dataSet.push(temp);
                                }
                            }
                            count--;
                            if (count === 0) {
                                if(missingFieldsDevices.missingFieldsPerDevice === null){
                                    createTable();
                                }else{
                                    stdSend(missingFieldsDevices);
                                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                    showWidgetContent(widgetName);
                                }
                            }

                        },

                        error: function () {
                            count--;
                            if (count === 0) {
                                createTable();
                            }
                        }
                    });
                }
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
          /*  table.on( 'search.dt', function () {
                 var order = table.order();
                 ordering = order[0][0];

                 var dataToSend = {
                     device: "",
                     prefix: prefix,
                     ordering: (Object.keys(columnsToShow))[ordering],
                     action: "filter: " + table.search()
                 };

                 stdSend(dataToSend);
            });*/

            $('#maintable tbody').on('click', 'td.expand-content', function () {
                var data = table.row($(this).parents('tr')).data();
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var order = table.order();
                ordering = order[0][0];

                var action;
                if(!row.child.isShown()){
                    action = "closed";
                }else{
                    action = "expanded";
                }

                var dataToSend = {
                    device: data.device,
                    prefix: prefix,
                    ordering: (Object.keys(columnsToShow))[ordering],
                    action: action
                };

                stdSend(dataToSend);
            });

            $('#maintable').on( 'order.dt', function () {
                if(table !== null){
                 var order = table.order();
                    ordering = order[0][0];

                 var dataToSend = {
                    device: " ",
                    prefix: prefix,
                    ordering: (Object.keys(columnsToShow))[ordering],
                    action: "changedOrdering"
                 };

                 stdSend(dataToSend);
                }
            } );
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
				code = widgetData.params.code;
				//////lettura code
				if (widgetData.params.code != null && widgetData.params.code != "null") {
                        let code = widgetData.params.code;
                        var text_ck_area = document.createElement("text_ck_area");
                        text_ck_area.innerHTML = code;
                        var newInfoDecoded = text_ck_area.innerText;
                        newInfoDecoded = newInfoDecoded.replaceAll("function execute()","function execute_" + "<?= $_REQUEST['name_w'] ?>(param)");

                        var elem = document.createElement('script');
                        elem.type = 'text/javascript';
                        elem.innerHTML = newInfoDecoded;
                        $('#<?= $_REQUEST['name_w'] ?>_code').append(elem);

                        $('#<?= $_REQUEST['name_w'] ?>_code').css("display", "none");
						//
						
						//
                    }

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

                if (rowParameters !== null) {
                    var newValue = JSON.parse(rowParameters);

                    setupLoadingPanel(widgetName, widgetContentColor, true);

                    ordering = (Object.keys(columnsToShow)).indexOf(newValue.ordering);
                    prefix = newValue.prefix;
                    devices = newValue.devices;

                    var customActionNumber = 1;
                    for (let i = 0; i < newValue.actions.length; i++) {
                        if(actions[newValue.actions[i]] !== undefined){
                            actions[newValue.actions[i]] = "show";
                        }else{
                            actions["custom" + customActionNumber] = newValue.actions[i];
                            customActionNumber++;
                        }
                    }

                    for (let i = 0; i < newValue.columnsToShow.length; i++) {
                        columnsToShow[newValue.columnsToShow[i]] = "";
                    }
                }
                populateWidget();
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

                    if(newValue.devices && newValue.actions && newValue.prefix && newValue.ordering && newValue.columnsToShow) {
                        setupLoadingPanel(widgetName, widgetContentColor, true);

                        ordering = (Object.keys(columnsToShow)).indexOf(newValue.ordering);
                        prefix = newValue.prefix;
                        devices = newValue.devices;

                        actions = {pin: "hidden"};

                        var customActionNumber = 1;
                        for (let i = 0; i < newValue.actions.length; i++) {
                            if(actions[newValue.actions[i]] !== undefined){
                                actions[newValue.actions[i]] = "show";
                            }else{
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
                        populateWidget();
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

        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>No
                Data Available</p>
            <table id="maintable" class="table table-striped table-bordered display responsive" cellspacing="0"
                   style="width:100%"></table>
        </div>
    </div>
	<div id="<?= $_REQUEST['name_w'] ?>_code"></div>
</div>