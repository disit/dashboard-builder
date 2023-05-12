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
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>
<script src="../datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
 var <?= $_REQUEST['name_w'] ?>_loaded = false;   
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000'
    };
    
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) {
        <?php
        $link = mysqli_connect($host, $username, $password);
        if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
            eventLog("Returned the following ERROR in widgetPieChart.php for the widget " . escapeForHTML($_REQUEST['name_w']) . " is not instantiated or allowed in this dashboard.");
            exit();
        }
        ?>
     //   var defaultColorsArray = ['#ffcc00', '#ff9933', '#ff3300', '#ff3399', '#6666ff', '#0066ff', '#00ccff', '#00ffff', '#00ff00', '#009900'];
        var defaultColorsArray = ['#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#a16f37', '#7cb5ec', '#434348'];
        var defaultColorsArray2 = ['#dafdff', '#9dfaff', '#3ef5ff', '#02bcc7', '#07838a', '#013f42'];
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= escapeForJS($_REQUEST['color_w']) ?>';
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType,
            metricData, nrMetricType, appId, flowId, gridLineColor, chartAxesColor, chartColor, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor,
            pattern, totValues, shownValues, descriptions, udm, threshold, thresholdEval, stopsArray,
            delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength, metricName, widgetTitle, countdownRef,
            innerRadius1, widgetParameters, thresholdsJson = null;
        var colors = [];
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var wsRetryActive, wsRetryTime = null;
        var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var url = "<?= escapeForJS($_REQUEST['link_w']) ?>";
        var embedWidget = <?= $_REQUEST['embedWidget'] == 'true' ? 'true' : 'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader = null;
        var seriesDataArray = [];
        var serviceUri = "";
        var widgetParameters, series, rowParameters, editLabels, thresholdsJson, infoJson, xAxisCategories,
            chartSeriesObject, startAngle, endAngle, groupByAttr = null;
        var flipFlag, emptyLegendFlagFromWs = false;
        var webSocket, openWs, manageIncomingWsMsg, openWsConn, wsClosed = null;
        var code, clickedVar, clickedVarType = null;
        var selectedDataJson = [];
        var fromCode = null;
        var showLegendFlag = false;
		//////ADD CODE//////
				//
        $(document).off('showPieChartFromExternalContent_' + widgetName);
        $(document).on('showPieChartFromExternalContent_' + widgetName, function(event){
            
            var newValue = event.passedData;
            rowParameters = newValue;
            
            if(localStorage.getItem("widgets") == null){
                var widgets = [];
                widgets.push(widgetName);
                localStorage.setItem("widgets", JSON.stringify(widgets));
            }
            else{
                var widgets = JSON.parse(localStorage.getItem("widgets"));
                if(!widgets.includes(widgetName)){
                    widgets.push(widgetName);
                    localStorage.setItem("widgets", JSON.stringify(widgets));
                }
            }
            clearInterval(countdownRef);
            //$("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
            //<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, event.range, event.marker, event.mapRef);
            if(encodeURIComponent(metricName) === encodeURIComponent(metricName))
            {
                //    <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);
                //metricName = 'AggregationSeries';
                // console.log(metricName);
                var newValue = event.passedData;
                //console.log(newValue);
                // console.log('RECEIVED PASSED DATA');
                var point = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().series[0].points[0];
                   // point.update(newValue);


                rowParameters = newValue;
                // console.log(rowParameters);

                emptyLegendFlagFromWs = true;
                $("#" + widgetName + "_loading").css("display", "block");
                
                var test = localStorage.getItem("passedData");
                if(localStorage.getItem("passedData") == null || localStorage.getItem("passedData") == "[object Object]"){
                    var init = [];
                    var firstEl = {};
                    firstEl.passedData = event.passedData;
                    firstEl.name = widgetName;
                    if (localStorage.getItem("events") == null) {
                        firstEl.eventIndex = 0;
                    } else {
                        firstEl.eventIndex = JSON.parse(localStorage.getItem("events")).length - 1;
                    }
                    init.push(firstEl);
                    localStorage.setItem("passedData", JSON.stringify(init));
                }
                else{
                    var newEl = {};
                    newEl.passedData = event.passedData;
                    newEl.name = widgetName;
                    if (localStorage.getItem("events") == null) {
                        newEl.eventIndex = 0;
                    } else {
                        newEl.eventIndex = JSON.parse(localStorage.getItem("events")).length - 1;
                    }
                    var oldElement = JSON.parse(localStorage.getItem("passedData"));
                    oldElement.push(newEl);
                    localStorage.setItem("passedData", JSON.stringify(oldElement));
                }
                populateWidget();
            }

        });
    /////
    	 $(document).off('reloadPreviousContent_' + widgetName);
        $(document).on('reloadPreviousContent_' + widgetName, function(event){
            var passedData = JSON.parse(localStorage.getItem("passedData"));
            var j = 0;
            var t = -1;
            while(passedData[j].eventIndex <= event.index && j < passedData.length - 1){
                if(passedData[j].name === widgetName){
                    t = j;
                }
                j = j+1;
            }
            if(t == -1){
                $('body').trigger({
                    type: "resetContent_"+widgetName
                });
            }
            else{
                rowParameters = passedData[t].passedData;
                populateWidget();
            }
        });
	
	$('#<?= $_REQUEST['name_w'] ?>_datetimepicker').datetimepicker({
            showTodayButton: true,
            widgetPositioning:{
                horizontal: 'auto',
                vertical: 'bottom'
            }
        })
		
		

        console.log("Entrato in widgetPieChart --> " + widgetName);

        if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
            showHeader = false;
        } else {
            showHeader = true;
        }

        if (url === "null") {
            url = null;
        }

        if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) {
            metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
            widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
            widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
            widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        } else {
            metricName = metricNameFromDriver;
            widgetTitleFromDriver.replace(/_/g, " ");
            widgetTitleFromDriver.replace(/\'/g, "&apos;");
            widgetTitle = widgetTitleFromDriver;
            $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
            widgetHeaderColor = widgetHeaderColorFromDriver;
            widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
        }

        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function (event) {
            if ((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange")) {
                $("#" + widgetName + "_legendContainer1").empty();
                $("#" + widgetName + "_legendContainer2").empty();
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
        //Definizioni di funzione specifiche del widget

        //Restituisce il JSON delle soglie se presente, altrimenti NULL
        function getThresholdsJson() {
            var thresholdsJson = null;
            if (jQuery.parseJSON(widgetProperties.param.parameters !== null)) {
                thresholdsJson = widgetProperties.param.parameters;
            }

            return thresholdsJson;
        }

        function serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr, fromCode) {

            var deviceLabels = [];
            var metricLabels = [];
            var auxLabels = [];
            let mappedSeriesDataArray = [];

            /*  let series = JSON.parse(metricData.data[0].commit.author.series);
              legendLength = series.secondAxis.labels.length;
              seriesObj = getChartSeriesObject(series);*/

            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css('height', '86%');

            metricLabels = getMetricLabelsForBarSeries(rowParameters);
            deviceLabels = getDeviceLabelsForBarSeries(rowParameters);
            if (groupByAttr != null) {
                //if (groupByAttr == "metrics") {
                //    if (groupByAttr == "value type") {
                if (groupByAttr == "value name") {
                    flipFlag = false;
                    //    } else if (groupByAttr == "device") {
                    //    } else if (groupByAttr == "value name") {
                } else if (groupByAttr == "value type") {
                    flipFlag = true;
                }
            } else {
                flipFlag = false;
            }
            if (flipFlag !== true) {
                mappedSeriesDataArray = buildBarSeriesArrayMap(seriesDataArray);
            } else {
                mappedSeriesDataArray = buildBarSeriesArrayMap2(seriesDataArray);
                auxLabels = metricLabels;
                metricLabels = deviceLabels;
                deviceLabels = auxLabels;
            }
            /*  if (editLabels) {
                  // in case of custom labels edited by user
                  series = serializeSensorDataForBarSeries(mappedSeriesDataArray, metricLabels, deviceLabels, flipFlag, editLabels);
              } else {*/
            series = serializeSensorDataForBarSeries(mappedSeriesDataArray, metricLabels, deviceLabels, flipFlag);
            //    }

            xAxisCategories = metricLabels.slice();

            widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);

            chartSeriesObject = getChartSeriesObject(series, editLabels, fromCode);
            legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
            //    xAxisCategories = getXAxisCategories(series, widgetHeight);

            if (firstLoad !== false) {
                showWidgetContent(widgetName);
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                $("#<?= $_REQUEST['name_w'] ?>_table").show();
            } else {
                elToEmpty.empty();
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                $("#<?= $_REQUEST['name_w'] ?>_table").show();
            }

            //    if (!serviceUri) {
            $.ajax({
                url: "../widgets/updateBarSeriesParameters.php",
                type: "GET",
                data: {
                    widgetName: "<?= $_REQUEST['name_w'] ?>",
                    series: series
                },
                async: true,
                dataType: 'json',
                success: function (widgetData) {

                },
                error: function (errorData) {
                /*    metricData = null;
                    console.log("Error in updating widgetBarSeries: <?= $_REQUEST['name_w'] ?>");
                    console.log(JSON.stringify(errorData));*/
                }
            });
            //    }
            drawDiagram("#<?= $_REQUEST['name_w'] ?>_chartContainer", chartSeriesObject, pieObj, fromCode);

        }

        function getXAxisCategories(series, widgetHeight) {
            var finalLabels, label, newLabel, id, singleInfo, dropClass, legendHeight = null;
            var isSimpleLabel = true;

            finalLabels = [];

            if (series !== null) {
                for (var i = 0; i < series.firstAxis.labels.length; i++) {
                    if (infoJson !== null) {
                        label = series.firstAxis.labels[i];
                        id = label.replace(/\s/g, '_');

                        singleInfo = infoJson.firstAxis[id];

                        //Aggiunta pulsante info
                        if ((singleInfo !== '') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                            //Aggiunta legenda sulle soglie
                            if ((thresholdsJson !== null) && (thresholdsJson !== undefined) && (thresholdsJson !== 'undefined')) {
                                if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) {
                                    if (thresholdsJson.thresholdArray.length > 0) {
                                        newLabel = '<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: ' + styleParameters.rowsLabelsFontSize + 'px; color: ' + styleParameters.rowsLabelsFontColor + '"></i>  ' +
                                            '<div style="display: inline" class="thrLegend">' +
                                            '<a href="#" data-toggle="dropdown" style="text-decoration: none; font-size: ' + styleParameters.rowsLabelsFontSize + ' ; color: ' + styleParameters.rowsLabelsFontColor + ';" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' +
                                            '<ul class="dropdown-menu thrLegend">' +
                                            '</ul>' +
                                            '</div>';
                                    } else {
                                        newLabel = '<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: ' + styleParameters.rowsLabelsFontSize + 'px; color: ' + styleParameters.rowsLabelsFontColor + '"></i> <span>' + label + '</span>';
                                    }
                                } else {
                                    newLabel = '<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: ' + styleParameters.rowsLabelsFontSize + 'px; color: ' + styleParameters.rowsLabelsFontColor + '"></i> <span>' + label + '</span>';
                                }
                            } else {
                                newLabel = '<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: ' + styleParameters.rowsLabelsFontSize + 'px; color: ' + styleParameters.rowsLabelsFontColor + '"></i> <span>' + label + '</span>';
                            }
                        } else {
                            //Aggiunta legenda sulle soglie
                            if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) {
                                if ((thresholdsJson !== null) && (thresholdsJson !== undefined) && (thresholdsJson !== 'undefined')) {
                                    if (thresholdsJson.thresholdArray.length > 0) {
                                        newLabel = '<div style="display: inline" class="thrLegend">' +
                                            '<a href="#" data-toggle="dropdown" style="text-decoration: none; font-size: ' + styleParameters.rowsLabelsFontSize + ' ; color: ' + styleParameters.rowsLabelsFontColor + ';" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' +
                                            '<ul class="dropdown-menu">' +
                                            '</ul>' +
                                            '</div>';
                                    } else {
                                        newLabel = label;
                                    }
                                } else {
                                    newLabel = label;
                                }
                            } else {
                                newLabel = label;
                            }
                        }

                        //Aggiunta nuova label al vettore delle labels
                        finalLabels[i] = newLabel;
                    } else {
                        finalLabels[i] = series.firstAxis.labels[i];
                    }
                }
            }
            return finalLabels;
        }

        function populateWidget(fromAggregate, fromCode) {

            seriesDataArray = [];
			var fromDate = null;
			if ((fromAggregate != null)&&(fromAggregate != '')){
				date = new Date(fromAggregate);
				 var y = date.getFullYear();
				 var m = date.getMonth() + 1; 
					if (m < 10){
						m = '0' +m;
					}				 
				 var d = date.getDate();
				 if (d < 10){
						d = '0' +d;
					}	
				 var h = date.getHours();
				 if (h < 10){
						h = '0' +h;
					}
				 var s = date.getMinutes();
				 if (s < 10){
						s = '0' +s;
					}
				 fromDate = y +'-'+m+'-'+d+'T'+h+':'+s+':00';
				 // console.log(fromDate);
			}

            let aggregationFlag = false;
            if (rowParameters != null) {
                if (rowParameters[0].metricHighLevelType == "Sensor" || rowParameters[0].metricHighLevelType == "MyKPI" || rowParameters[0].metricHighLevelType == "IoT Device Variable" || rowParameters[0].metricHighLevelType == "Data Table Variable" || rowParameters[0].metricHighLevelType == "Mobile Device Variable") {
                    aggregationFlag = true;
                }
            }

            if (metricName === 'AggregationSeries' || aggregationFlag === true || nrMetricType != null) {
            //    rowParameters = JSON.parse(rowParameters);
                aggregationGetData = [];
                getDataFinishCount = 0;
                editLabels = styleParameters.editDeviceLabels;

                for (var i = 0; i < rowParameters.length; i++) {
                    aggregationGetData[i] = false;
                }

                var startAngle = 90 - parseInt(styleParameters.startAngle);
                var endAngle = 90 - parseInt(styleParameters.endAngle);
                if (startAngle > endAngle) {
                    var temp = startAngle;
                    startAngle = endAngle;
                    endAngle = temp;
                }

                var centerY = 100 - parseInt(styleParameters.centerY);

            /*    if (fromCode) {
                    showLegendFlag = true;
                }   */
                pieObj = {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    showInLegend: showLegendFlag,
                    startAngle: startAngle,
                    endAngle: endAngle,
                    center: ['50%', centerY + '%']
                };

                for (var i = 0; i < rowParameters.length; i++) {
                    let dataOrigin = rowParameters[i].metricHighLevelType;
                    switch (dataOrigin) {
                        case "KPI":
                            index = i;
                            $.ajax({
                                url: "../controllers/aggregationSeriesProxy.php",
                                type: "POST",
                                data:
                                    {
                                        dataOrigin: JSON.stringify(rowParameters[i]),
                                        index: i
                                    },
                                async: true,
                                dataType: 'json',
                                success: function (data) {
                                    aggregationGetData[data.index] = data;
                                    getDataFinishCount++;

                                    //Popoliamo il widget quando sono arrivati tutti i dati
                                    if (getDataFinishCount === rowParameters.length) {
                                        series = buildSeriesFromAggregationData();

                                        widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);

                                        chartSeriesObject = getChartSeriesObject(series);
                                        legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                                        xAxisCategories = getXAxisCategories(series, widgetHeight);

                                        if (firstLoad !== false) {
                                            showWidgetContent(widgetName);
                                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                            $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                        } else {
                                            elToEmpty.empty();
                                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                            $("#<?= $_REQUEST['name_w'] ?>_table").show();
                                        }

                                        drawDiagram("#<?= $_REQUEST['name_w'] ?>_chartContainer", chartSeriesObjecteriesObj, pieObj);
                                    }
                                },
                                error: function (errorData) {
                                    metricData = null;
                                    console.log("Error in data retrieval");
                                    console.log(JSON.stringify(errorData));
                                    showWidgetContent(widgetName);
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                }
                            });
                            break;

                        case "IoT Device Variable":
                        case "Data Table Variable":
                        case "Mobile Device Variable":
                        case "Sensor":
                            var timeRange = null;
                            var urlToCall = "";
                            var xlabels = [];
                            let smUrl = "";
							var fromDate_url = '';
							if (fromDate != null){
								fromDate_url = '&toTime='+fromDate;
							}
                            if (rowParameters[i].metricId.split("serviceUri=").length > 1) {
                                smUrl = "<?= $superServiceMapProxy ?>/api/v1/?serviceUri=" + rowParameters[i].metricId.split("serviceUri=")[1]+fromDate_url;
                            } else {
                                smUrl = "<?= $superServiceMapProxy ?>/api/v1/?serviceUri=" + rowParameters[i].metricId+fromDate_url;
                            }
                            //    metricType = "Float";

                            if ("<?= $_REQUEST['timeRange']?>") {
                                if ("<?= $_REQUEST['timeRange'] ?>" != 'last' && "<?= $_REQUEST['timeRange'] ?>" != "") {
                                    /*  switch("<?= $_REQUEST['timeRange'] ?>") {
                                                case "4 Ore":
                                                    timeRange = "fromTime=4-hour";
                                                    break;

                                                case "12 Ore":
                                                    timeRange = "fromTime=12-hour";
                                                    break;

                                                case "Giornaliera":
                                                    timeRange = "fromTime=1-day";
                                                    break;

                                                case "Settimanale":
                                                    timeRange = "fromTime=7-day";
                                                    break;

                                                case "Mensile":
                                                    timeRange = "fromTime=30-day";
                                                    break;

                                                case "Annuale":
                                                    timeRange = "fromTime=365-day";
                                                    break;
                                            }   */

                                    urlToCall = smUrl + "&" + timeRange;
                                } else {
                                    urlToCall = smUrl;
                                }
                            } else {
                                urlToCall = smUrl;
                            }

                            getSmartCitySensorValues(rowParameters, i, smUrl, null, true, function (extractedData) {

                                if (extractedData) {
                                    seriesDataArray.push(extractedData);
                                } else {
                                    console.log("Dati Smart City non presenti");
                                    seriesDataArray.push(undefined);
                                }
                                //if (endFlag === true) {
                                // Alla fine quando si arriva all'ultimo record ottenuto dalle varie chiamate asincrone
                                if (rowParameters.length === seriesDataArray.length) {
                                    // DO FINAL SERIALIZATION
                                    serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr, fromCode);
                                }

                            });
                            break;

                        case "Dynamic":
                            let extractedData = {};
                            extractedData.value = rowParameters[i].value;
                            extractedData.metricType = rowParameters[i].metricType;
                            extractedData.metricId = rowParameters[i].metricId;
                            extractedData.metricName = rowParameters[i].metricName;
                            extractedData.measuredTime = rowParameters[i].measuredTime;
                            extractedData.metricValueUnit = rowParameters[i].metricValueUnit;

                            seriesDataArray.push(extractedData);

                            if (rowParameters.length === seriesDataArray.length) {
                                // DO FINAL SERIALIZATION
                                serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr, fromCode)
                            }

                            break;

                        case "MyKPI":

                            //    var convertedData = getMyKPIValues(rowParameters[i].metricId);
							var fromDate_url = null;
							if (fromDate != null){
								fromDate_url = '&to='+fromDate;
							}
							//
                            let aggregationCell = [];
                            var xlabels = [];
                            let kpiMetricName = rowParameters[i].metricName;
                            let kpiMetricType = rowParameters[i].metricType;
                            if (rowParameters[i].metricId.includes("datamanager/api/v1/poidata/")) {
                                rowParameters[i].metricId = rowParameters[i].metricId.split("datamanager/api/v1/poidata/")[1];
                            }
                            //getMyKPIValues(rowParameters, i, null, 1, function (extractedData) {
							getMyKPIValues(rowParameters, i, fromDate_url, 1, function (extractedData) {
                                let countEmpty = 0;
                                if (extractedData) {
                                    if (Object.keys(extractedData).length > 0) {
                                        seriesDataArray.push(extractedData);
                                    } else {
                                        countEmpty++;
                                    }
                                } else {
                                    console.log("Dati Smart City non presenti");
                                    seriesDataArray.push(undefined);
                                }
                                //if (endFlag === true) {
                                // Alla fine quando si arriva all'ultimo record ottenuto dalle varie chiamate asincrone
                                if (rowParameters.length === seriesDataArray.length) {
                                    // DO FINAL SERIALIZATION
                                    serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr, fromCode)
                                }

                            });
                            break;

                    }
                }
            } else {
                $.ajax({
                    url: getMetricDataUrl,
                    type: "GET",
                    data: {"IdMisura": ["<?= escapeForJS($_REQUEST['id_metric']) ?>"]},
                    async: true,
                    dataType: 'json',
                    success: function (metricData) {
                        metricType = metricData.data[0].commit.author.metricType;

                        shownValues = [];
                        descriptions = [];
                        totValues = [];
                        dataObj = [];
                        seriesObj = [];

                        var startAngle = 90 - parseInt(styleParameters.startAngle);
                        var endAngle = 90 - parseInt(styleParameters.endAngle);

                        if (startAngle > endAngle) {
                            var temp = startAngle;
                            startAngle = endAngle;
                            endAngle = temp;
                        }

                        var centerY = 100 - parseInt(styleParameters.centerY);

                        if (metricType.indexOf('Percentuale') >= 0) {
                            //Diagramma sui valori value_perc1, value_perc2, value_perc3
                            udm = "%";

                            if (metricData.data[0].commit.author.value_perc1 !== null) {
                                if ("<?= escapeForJS($_REQUEST['id_metric']) ?>" === 'SmartDS_Process') {
                                    shownValues[0] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1 * 100).toFixed(1));
                                } else {
                                    shownValues[0] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(2));
                                }
                                descriptions[0] = metricData.data[0].commit.author.field1Desc;
                            }

                            if (metricData.data[0].commit.author.value_perc2 !== null) {
                                if ("<?= escapeForJS($_REQUEST['id_metric']) ?>" === 'SmartDS_Process') {
                                    shownValues[1] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc2 * 100).toFixed(1));
                                } else {
                                    shownValues[1] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc2).toFixed(2));
                                }
                                descriptions[1] = metricData.data[0].commit.author.field2Desc;
                            }

                            if (metricData.data[0].commit.author.value_perc3 !== null) {
                                if ("<?= escapeForJS($_REQUEST['id_metric']) ?>" === 'SmartDS_Process') {
                                    shownValues[2] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc3 * 100).toFixed(1));
                                } else {
                                    shownValues[2] = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc3).toFixed(2));
                                }
                                descriptions[2] = metricData.data[0].commit.author.field3Desc;
                            }

                            pieObj = {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                startAngle: startAngle,
                                endAngle: endAngle,
                                center: ['50%', centerY + '%']
                            };

                            if (shownValues.length === 1) {
                                shownValues[1] = parseFloat(parseFloat(100 - shownValues[0]).toFixed(2));
                                //descriptions[1] = 'Complementary';
                                var color0, color1, color, desc = null;

                                if ((styleParameters.colorsSelect1 === 'manual') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                    color0 = styleParameters.colors1[0];
                                    color1 = styleParameters.colors1[1];
                                } else {
                                    color0 = defaultColorsArray[0];
                                    color1 = defaultColorsArray[1];
                                }

                                dataObj[0] = {
                                    name: descriptions[0],
                                    color: color0,
                                    y: shownValues[0]
                                };
                                dataObj[1] = {
                                    //name: descriptions[1],
                                    color: color1,
                                    y: shownValues[1]
                                };
                            } else {
                                for (var i = 0; i < shownValues.length; i++) {
                                    if ((styleParameters.colorsSelect1 === 'manual') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                        color = styleParameters.colors1[i];
                                    } else {
                                        color = defaultColorsArray[i % 10];
                                    }

                                    desc = descriptions[i];

                                    dataObj[i] = {
                                        name: desc,
                                        color: color,
                                        y: shownValues[i]
                                    };
                                }
                            }

                            var dataLabelsDistance = parseInt(styleParameters.dataLabelsDistance);

                            if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) {
                                innerRadius1 = parseInt(styleParameters.innerRadius1);
                            } else {
                                innerRadius1 = 25;
                            }

                            if (innerRadius1 > 100) {
                                innerRadius1 = 100;
                            }
                            if (innerRadius1 < 0) {
                                innerRadius1 = 0;
                            }

                            seriesObj.push({
                                data: dataObj,
                                dataLabels: {
                                    formatter: function () {
                                        return this.y + " " + udm;
                                    },
                                    distance: dataLabelsDistance,
                                    style: {
                                        fontFamily: 'Verdana',
                                        fontSize: styleParameters.dataLabelsFontSize + "px",
                                        color: styleParameters.dataLabelsFontColor,
                                        fontWeight: 'bold',
                                        fontStyle: 'italic',
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)",
                                        "textOutline": "1px 1px contrast"
                                    }
                                },
                                size: "100%",
                                innerSize: innerRadius1 + "%",
                                tooltip: {
                                    headerFormat: null,
                                    backgroundColor: {
                                        linearGradient: [0, 0, 0, 60],
                                        stops: [
                                            [0, '#FFFFFF'],
                                            [1, '#E0E0E0']
                                        ]
                                    },
                                    pointFormatter: function () {
                                        var field = this.series.name;
                                        var temp, thresholdObject, desc, min, max, color, label, index, message = null;
                                        var rangeOnThisField = false;
                                        var dataStringInPopup = "";
                                        var dateMessage = "";
                                        var valueUnitInPopup = "";

                                        for (var n = 0; n < seriesDataArray.length; n++) {
                                            if (seriesDataArray[n].metricType == this.name) {
                                                dataStringInPopup = seriesDataArray[n].measuredTime;
                                                //  if(seriesDataArray[n].metricValueUnit != null) {
                                                valueUnitInPopup = seriesDataArray[n].metricValueUnit;
                                                //  }
                                            } else if (flipFlag == true && seriesDataArray[n].metricName == this.name) {
                                                dataStringInPopup = seriesDataArray[n].measuredTime;
                                                valueUnitInPopup = seriesDataArray[n].metricValueUnit;
                                            }
                                        }

                                        if ((thresholdsJson !== null) && (thresholdsJson !== 'undefined') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                            temp = JSON.parse(thresholdsJson);
                                            thresholdObject = temp.thresholdObject.fields;

                                            if (thresholdObject.length > 0) {
                                                label = this.name;

                                                if (label === "Complementary") {
                                                    rangeOnThisField = false;
                                                    message = "No range defined on this field";
                                                } else {
                                                    for (var i in thresholdObject) {
                                                        if (label === thresholdObject[i].fieldName) {
                                                            if (thresholdObject[i].thrSeries.length > 0) {
                                                                for (var j in thresholdObject[i].thrSeries) {
                                                                    if ((parseFloat(this.y) >= thresholdObject[i].thrSeries[j].min) && (parseFloat(this.y) < thresholdObject[i].thrSeries[j].max)) {
                                                                        desc = thresholdObject[i].thrSeries[j].desc;
                                                                        min = thresholdObject[i].thrSeries[j].min;
                                                                        max = thresholdObject[i].thrSeries[j].max;
                                                                        color = thresholdObject[i].thrSeries[j].color;
                                                                        rangeOnThisField = true;
                                                                    } else {
                                                                        message = "This value doesn't belong to any of the defined ranges";
                                                                    }
                                                                }
                                                            } else {
                                                                rangeOnThisField = false;
                                                                message = "No range defined on this field";
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                rangeOnThisField = false;
                                                message = "No range defined on this field";
                                            }
                                        } else {
                                            rangeOnThisField = false;
                                            message = "No range defined on this field";
                                        }


                                        if (rangeOnThisField) {
                                            if ((desc !== null) && (desc !== '')) {
                                                return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                            } else {
                                                return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                            }
                                        } else {
                                            //    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                                            //        '<span style="color:' + this.color + '">\u25CF</span> ' + message + '<br/>' +
                                            //        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                            //        '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                            return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                                                '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                                '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                        }
                                    }
                                }
                            });

                            //Per il caso semplice basta una sola riga per la legenda
                            $('#<?= $_REQUEST['name_w'] ?>_legendContainer2').hide();
                            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css('height', '93%');

                        } else if (metricType === 'Series') {
                            //Caso di pie sulle serie
                            series = JSON.parse(metricData.data[0].commit.author.series);
                            legendLength = series.secondAxis.labels.length;
                            seriesObj = getChartSeriesObject(series);

                            pieObj = {
                                point: {
                                    events: {
                                        legendItemClick: function () {
                                        //    alert("click");
                                        }
                                    }
                                },
                                allowPointSelect: true,
                                cursor: 'pointer',
                                showInLegend: true,
                                startAngle: startAngle,
                                endAngle: endAngle,
                                center: ['50%', centerY + '%']
                            };

                            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css('height', '86%');
                        }

                        if (firstLoad !== false) {
                            showWidgetContent(widgetName);
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                            $("#<?= $_REQUEST['name_w'] ?>_legendContainer1").show();
                            $("#<?= $_REQUEST['name_w'] ?>_legendContainer2").show();
                        } else {
                            elToEmpty.empty();
                            $("#" + widgetName + "_legendContainer1").empty();
                            $("#" + widgetName + "_legendContainer2").empty();
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                            $("#<?= $_REQUEST['name_w'] ?>_legendContainer1").show();
                            $("#<?= $_REQUEST['name_w'] ?>_legendContainer2").show();
                        }
                        drawDiagram("#<?= $_REQUEST['name_w'] ?>_chartContainer", seriesObj, pieObj);

                    },
                    error: function () {
                        metricData = null;
                        console.log("Error in data retrieval");
                        console.log(JSON.stringify(errorData));
                        showWidgetContent(widgetName);
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                        $("#<?= $_REQUEST['name_w'] ?>_legendContainer1").hide();
                        $("#<?= $_REQUEST['name_w'] ?>_legendContainer2").hide();
                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    }
                });

            }
        }

        function drawDiagram (id, seriesObj, pieObj, fromCode){
			//
            $(id).highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                    backgroundColor: 'transparent',
                    options3d: {
                        enabled: false,
                        alpha: 45,
                        beta: 0
                    },
                    events: {
                        load: onDraw(fromCode)
                    }
                },
                title: {
                    text: ''
                },
                plotOptions: {
                    pie: pieObj,
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    if(code){
                                        selectedDataJson = [];
                                        selectedDataJson.event = "click";
                                        clickedVar = this.options.name;
                                        var param1 = this.options.y;
                                        clickedVarType = this.series.name;
                                        var dataString = "";
                                        var vUnit = null;
                                        if (clickedVarType == 'value name') {
                                            for (var n = 0; n < seriesDataArray.length; n++) {
                                                if (seriesDataArray[n].metricName == clickedVar) {
                                                    selectedDataJson.push(seriesDataArray[n]);
                                                }
                                            }
                                        } else if (clickedVarType == 'value type') {
                                            for (var n = 0; n < seriesDataArray.length; n++) {
                                                if (seriesDataArray[n].metricType == clickedVar && Math.floor(seriesDataArray[n].value) == Math.floor(this.y)) {   
                                                    selectedDataJson.push(seriesDataArray[n]);
                                                }
                                            }
                                        }
                                        let j=1;
                                        if(localStorage.getItem("events") == null){

                                            var events = [];
                                            events.push("PieChartClick1");
                                            localStorage.setItem("events", JSON.stringify(events));
                                        }
                                        else{
                                            var events = JSON.parse(localStorage.getItem("events"));
                                            for(var e in events){
                                                if(events[e].slice(0,13) == "PieChartClick")
                                                    j = j+1;
                                            }
                                            events.push("PieChartClick" + j);
                                            localStorage.setItem("events", JSON.stringify(events));
                                        }

                                        let newId = "PieChartClick"+j;
                                        $('#BIMenuCnt').append('<div id="'+newId+'" class="row" data-selected="false"></div>');
                                        $('#'+newId).append('<div class="col-md-12 orgMenuSubItemCnt">'+newId+'</div>' );
                                        $('#'+newId).on( "click", function() {
                                            var widgets = JSON.parse(localStorage.getItem("widgets"));
                                            var index = JSON.parse(localStorage.getItem("events")).indexOf(newId);
                                            for(var w in widgets){
                                                if(widgets[w] != null){
                                                    $('body').trigger({
                                                        type: "reloadPreviousContent_"+widgets[w],
                                                        index: index
                                                    });
                                                }
                                            }
                                                
                                        });
                                        $( '#'+newId ).mouseover(function() {
                                            $('#'+newId).css('cursor', 'pointer');
                                        });
                                        if(code) {
                                            try {
                                                execute_<?= $_REQUEST['name_w'] ?>(selectedDataJson);
                                            } catch (e) {
                                                console.log("Error in JS function from pie click on " + widgetName);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                },
                series: seriesObj,
                legend: {
                    enabled: true
                },
                exporting: {
                    enabled: false
                },
                credits: {
                    enabled: false
                }
            });
        }

        function labelsFormat()
        {
            var format, test = null;

            switch(styleParameters.dataLabels)
            {
                case "no":
                    format = "";
                    break;

                case "value":
                    format = this.y;
                    break;

                case "full":
                    format = this.series.name + ': ' + this.y;
                    break;

                default:
                    format = this.y;
                    break;
            }

            return format;
        }

        function getChartSeriesObject(series, xAxisLabelsEdit, fromCode)
        {
            var totals, chartSeriesObject, singleObject, seriesName, seriesValue, seriesValues, seriesArray, zonesObject, zonesArray, inf, sup, i, innerSize, outerSize, numberOfCircs, chartWidth, increment, color = null;

            if(series !== null)
            {
                chartSeriesObject = [];
                numberOfCircs = series.secondAxis.series.length;
                chartWidth = ($('#<?= $_REQUEST['name_w'] ?>_div').height() - 40)*0.86;

                //Primo cerchio con le categorie del secondo asse
                seriesName = series.secondAxis.desc;
                seriesValues = [];

                totals = [];

                for(var i = 0; i < series.secondAxis.labels.length; i++)
                {
                    totals[i] = 0;
                    for(var j = 0; j < series.secondAxis.series[i].length; j++)
                    {
                        if (!isNaN(parseFloat(series.secondAxis.series[i][j]))) {
                            totals[i] = parseFloat(totals[i]) + parseFloat(series.secondAxis.series[i][j]);
                        }
                    }
                }

                for(var i = 0; i < series.secondAxis.labels.length; i++)
                {
                    if((styleParameters.colorsSelect1 === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                    {
                        color = styleParameters.colors1[i];
                    }
                    else
                    {
                        color = defaultColorsArray[i%10];
                    }

                    let seriesValuesName = "";
                    if (xAxisLabelsEdit != null) {
                        if (!flipFlag) {
                            if (xAxisLabelsEdit.length == series.secondAxis.labels.length && !fromCode) {
                                seriesValuesName = xAxisLabelsEdit[i];
                            } else {
                                seriesValuesName = series.secondAxis.labels[i];
                            }
                        } else {
                            // flipped case
                            seriesValuesName = series.secondAxis.labels[i];
                        }
                    } else {
                        seriesValuesName = series.secondAxis.labels[i];
                    }

                    seriesValues.push({
                        name: seriesValuesName,
                        color: color,
                        y: totals[i]
                    });
                }

                //Calcolo dei diametri delle circonferenze
                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    innerSize = chartWidth * parseFloat(parseFloat(styleParameters.innerRadius1)/100);
                    outerSize = chartWidth * parseFloat(parseFloat(styleParameters.outerRadius1)/100);
                }
                else
                {
                    innerSize = chartWidth * parseFloat(0.2);
                    outerSize = chartWidth * parseFloat(0.5);
                }

                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    dataLabelsDistance1 = parseInt(styleParameters.dataLabelsDistance1);
                }
                else
                {
                    dataLabelsDistance1 = -30;
                }

                singleObject = {
                    type: 'pie',
                    name: seriesName,
                    data: seriesValues,
                    size: outerSize,
                    innerSize: innerSize,
                    showInLegend: showLegendFlag,
                    borderWidth: 1,
                    tooltip: {
                        headerFormat: null,
                        pointFormat: "<span style='color:{point.color}'>\u25CF</span> {series.name}: <b>{point.name}</b><br/>"
                    },
                    dataLabels: {
                        useHTML: false,
                        enabled: true,
                        inside: true,
                        distance: dataLabelsDistance1,
                        formatter: function(){
                            switch(styleParameters.dataLabels)
                            {
                                case 'no':
                                    return null;
                                    break;

                                case 'value':
                                    return this.point.name;
                                    break;

                                case 'full':
                                    return this.point.name;
                                    break;

                                default:
                                    return this.point.name;
                                    break;
                            }
                        },
                        style: {
                            fontFamily: 'Verdana',
                            fontSize: styleParameters.dataLabelsFontSize + "px",
                            color: styleParameters.dataLabelsFontColor,
                            fontWeight: 'bold',
                            fontStyle: 'italic',
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)",
                            "textOutline": "1px 1px contrast"
                        }
                    }
                };

                //Workaround temporaneo per far vedere pie tradizionali con più di 3 fette
                if(seriesValues.length > 0)
                {
                    chartSeriesObject.push(singleObject);
                }

                //Secondo cerchio con le subcategorie prese dal primo asse
                seriesName = series.firstAxis.desc;
                seriesValues = [];

                for(var i = 0; i < series.secondAxis.series.length; i++)
                {
                    for(var j = 0; j < series.secondAxis.series[i].length; j++)
                    {
                        if((styleParameters.colorsSelect2 === 'manual')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                        {
                            color = styleParameters.colors2[j];
                        }
                        else
                        {
                            color = defaultColorsArray2[j%5];
                        }

                        seriesValues.push({
                            name: series.firstAxis.labels[j],
                            color: color,
                            y: series.secondAxis.series[i][j]
                        });
                    }
                }

                //Calcolo dei diametri delle circonferenze
                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    innerSize = chartWidth * parseFloat(parseFloat(styleParameters.innerRadius2)/100);
                }
                else
                {
                    innerSize = chartWidth * parseFloat(0.5);
                }

                outerSize = parseFloat(chartWidth * 1);

                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    dataLabelsDistance2 = parseInt(styleParameters.dataLabelsDistance2);
                }
                else
                {
                    dataLabelsDistance2 = -30;
                }

                singleObject = {
                    type: 'pie',
                    name: seriesName,
                    data: seriesValues,
                    size: outerSize,
                    innerSize: innerSize,
                    showInLegend: showLegendFlag,
                    tooltip: {
                        style: {
                            fontFamily: 'Verdana',
                            fontSize: 12 + "px",
                            color: 'black',
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.15)",
                            "z-index": 5
                        },
                        backgroundColor: {
                            linearGradient: [0, 0, 0, 60],
                            stops: [
                                [0, '#FFFFFF'],
                                [1, '#E0E0E0']
                            ]
                        },
                        //headerFormat: '<span style="font-size: 10px">{point.key}</span><br/>'
                        headerFormat: null,
                        pointFormatter: function()
                        {
                            var field = this.series.name;
                            var temp, thresholdObject, desc, min, max, color, label, index, message = null;
                            var rangeOnThisField = false;
                            var dataStringInPopup = "";
                            var dateMessage = "";
                            var valueUnitInPopup = "";

                            for (var n = 0; n < seriesDataArray.length; n++) {
                                if (seriesDataArray[n].metricType == this.name) {
                                    dataStringInPopup = seriesDataArray[n].measuredTime;
                                    //  if(seriesDataArray[n].metricValueUnit != null) {
                                    valueUnitInPopup = seriesDataArray[n].metricValueUnit;
                                    //  }
                                } else if (flipFlag == true && seriesDataArray[n].metricName == this.name) {
                                    dataStringInPopup = seriesDataArray[n].measuredTime;
                                    valueUnitInPopup = seriesDataArray[n].metricValueUnit;
                                }
                            }

                            if((thresholdsJson !== null)&&(thresholdsJson !== 'undefined')&&((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)))
                            {
                                temp = JSON.parse(thresholdsJson);
                                thresholdObject = temp.thresholdObject.firstAxis.fields;

                                if(thresholdObject.length > 0)
                                {
                                    label = this.name;

                                    for(var i in thresholdObject)
                                    {
                                        if(label === thresholdObject[i].fieldName)
                                        {
                                            if(thresholdObject[i].thrSeries.length > 0)
                                            {
                                                for(var j in thresholdObject[i].thrSeries)
                                                {
                                                    if((parseFloat(this.y) >= thresholdObject[i].thrSeries[j].min)&&(parseFloat(this.y) < thresholdObject[i].thrSeries[j].max))
                                                    {
                                                        desc = thresholdObject[i].thrSeries[j].desc;
                                                        min = thresholdObject[i].thrSeries[j].min;
                                                        max = thresholdObject[i].thrSeries[j].max;
                                                        color = thresholdObject[i].thrSeries[j].color;
                                                        rangeOnThisField = true;
                                                    }
                                                    else
                                                    {
                                                        message = "This value doesn't belong to any of the defined ranges";
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                rangeOnThisField = false;
                                                message = "No range defined on this field";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    rangeOnThisField = false;
                                    message = "No range defined on this field";
                                }
                            }
                            else
                            {
                                rangeOnThisField = false;
                                message = "No range defined on this field";
                            }

                            if(rangeOnThisField)
                            {
                                if((desc !== null)&&(desc !== ''))
                                {
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Classification: <b>' + desc + '</b>' +
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                }
                                else
                                {
                                    return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Range: between <b>' + min + '</b> and <b>' + max + '</b><br/>' +
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                           '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                }
                            }
                            else
                            {
                             //   return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                             //          '<span style="color:' + this.color + '">\u25CF</span> ' + message + '<br/>' +
                             //          '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                             //          '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                                return '<span style="color:' + this.color + '">\u25CF</span><b> ' + this.name + '</b>: <b>' + this.y + '</b><br/>' +
                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Value Unit: <b>' + valueUnitInPopup + '</b><br/>' +
                                    '<span style="color:' + this.color + '">\u25CF</span> ' + 'Date: <b>' + dataStringInPopup + '</b><br/>';
                            }
                        }
                    },
                    dataLabels: {
                        useHTML: false,
                        enabled: true,
                        inside: true,
                        distance: dataLabelsDistance2,
                        formatter: function(){
                            switch(styleParameters.dataLabels)
                            {
                                case 'no':
                                    return null;
                                    break;

                                case 'value':
                                    return this.y;
                                    break;

                                case 'full':
                                    return this.point.name + ": " + this.y;
                                    break;

                                default:
                                    return this.y;
                                    break;
                            }
                        },
                        style: {
                            fontFamily: 'Verdana',
                            fontSize: styleParameters.dataLabelsFontSize + "px",
                            color: styleParameters.dataLabelsFontColor,
                            fontWeight: 'bold',
                            fontStyle: 'italic',
                            "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)",
                            "textOutline": "1px 1px contrast"
                        }
                    }
                };

                chartSeriesObject.push(singleObject);
            }
            return chartSeriesObject;
        }

        function showModalFieldsInfoFirstAxis()
        {
            var label = $(this).attr("data-label");
            var id = label.replace(/\s/g, '_');
            var info = infoJson.firstAxis[id];

            $('#modalWidgetFieldsInfoTitle').html("Detailed info for field <b>" + label + "</b>");
            $('#modalWidgetFieldsInfoContent').html(info);

            $('#modalWidgetFieldsInfo').css({
                'vertical-align': 'middle',
                'position': 'absolute',
                'top': '10%'
            });

            $('#modalWidgetFieldsInfo').modal('show');
        }

        function showModalFieldsInfoSecondAxis()
        {
            var label = $(this).attr("data-label");
            var id = label.replace(/\s/g, '_');
            var info = infoJson.secondAxis[id];

            $('#modalWidgetFieldsInfoTitle').html("Detailed info for field <b>" + label + "</b>");
            $('#modalWidgetFieldsInfoContent').html(info);

            $('#modalWidgetFieldsInfo').css({
                'vertical-align': 'middle',
                'position': 'absolute',
                'top': '10%'
            });

            $('#modalWidgetFieldsInfo').modal('show');
        }

        //Disegno ad hoc della legenda, con inserimento dei pulsanti info e dei menu a comparsa delle legende sulle soglie nel caso delle serie.
        function onDraw(fromCode) {
        //    if (!fromCode) {
                var colorContainer, labelContainer, infoContainer, infoIcon, label, id, singleInfo, item,
                    thresholdObject,
                    dropDownElement = null;

                if ((thresholdsJson !== null) && (thresholdsJson !== 'undefined')) {
                    thresholdObject = JSON.parse(thresholdsJson);
                }

                if (emptyLegendFlagFromWs == true) {
                    $("#" + widgetName + "_legendContainer1").empty();
                    $("#" + widgetName + "_legendContainer2").empty();
                    emptyLegendFlagFromWs = false;
                }

                if (metricType != null) {
                    if (metricType.indexOf('Percentuale') >= 0) {
                        for (var i = 0; i < descriptions.length; i++) {
                            label = descriptions[i];

                            if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) {
                                colorContainer = $('<div class="legendColorContainer" style="background-color: ' + styleParameters.colors1[i] + '"></div>');
                            } else {
                                colorContainer = $('<div class="legendColorContainer" style="background-color: ' + defaultColorsArray[i] + '"></div>');
                            }

                            //Aggiunta degli eventuali caret per i menu a comparsa per le legende sulle soglie
                            if ((thresholdsJson !== null) && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                if (thresholdObject.thresholdObject.fields[i] !== undefined) {
                                    if (thresholdObject.thresholdObject.fields[i].thrSeries.length > 0) {
                                        labelContainer = $('<div class="legendLabelContainer thrLegend dropup">' +
                                            '<a href="#" data-toggle="dropdown" style="text-decoration: none;" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' +
                                            '<ul class="dropdown-menu thrLegend">' +
                                            '</ul>' +
                                            '</div>');

                                        thresholdObject.thresholdObject.fields[i].thrSeries.forEach(function (range) {
                                            if (range.desc !== '') {
                                                dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                            } else {
                                                dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                            }
                                        });
                                    } else {
                                        labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                                    }
                                } else {
                                    labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                                }
                            } else {
                                labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                            }
                            labelContainer.css("font-size", styleParameters.legendFontSize + "px");
                            item = $('<div class="legendSingleContainer"></div>');
                            item.append(colorContainer);
                            item.append(labelContainer);
                            item.css("color", styleParameters.legendFontColor);
                            item.find('a').css("color", styleParameters.legendFontColor);
                            item.find('a.thrLegendElement').css("color", "black");
                            if (i < shownValues.length - 1) {
                                item.css("margin-right", "10px");
                            }

                            item.css("display", "block");
                            $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').append(item);

                            var parentLegendElement = $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').find("div.legendSingleContainer").eq(i);
                            var elementLeftPosition = parentLegendElement.position().left;
                            var widgetWidth = $("#<?= $_REQUEST['name_w'] ?>_div").width();
                            var legendMargin = null;

                            if (elementLeftPosition > (widgetWidth / 2)) {
                                legendMargin = 200;
                            } else {
                                legendMargin = 0;
                            }

                            $("#<?= $_REQUEST['name_w'] ?>_legendContainer1 .legendSingleContainer").eq(i).find("div.thrLegend ul").css("left", "-" + legendMargin + "%");

                        }
                    } else if (metricType === 'Series') {
                        for (var i = 0; i < series.secondAxis.labels.length; i++) {
                            label = series.secondAxis.labels[i];

                            if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) {
                                colorContainer = $('<div class="legendColorContainer" style="background-color: ' + styleParameters.colors1[i] + '"></div>');
                            } else {
                                colorContainer = $('<div class="legendColorContainer" style="background-color: ' + defaultColorsArray[i] + '"></div>');
                            }

                            //Aggiunta degli eventuali caret per i menu a comparsa per le legende sulle soglie - Qui per ora è inutile, non esistono soglie sull'anello più interno
                            if ((thresholdsJson !== null) && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                if (thresholdObject.thresholdObject.secondAxis.fields[i].thrSeries.length > 0) {
                                    labelContainer = $('<div class="legendLabelContainer thrLegend dropup">' +
                                        '<a href="#" data-toggle="dropdown" style="text-decoration: none;" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' +
                                        '<ul class="dropdown-menu thrLegend">' +
                                        '</ul>' +
                                        '</div>');

                                    thresholdObject.thresholdObject.secondAxis.fields[i].thrSeries.forEach(function (range) {
                                        if (range.desc !== '') {
                                            dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                        } else {
                                            dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                        }
                                        dropDownElement.css("font", "bold 10px Verdana");
                                        dropDownElement.find("i").css("font-size", "12px");
                                        labelContainer.find("ul").append(dropDownElement);
                                    });
                                } else {
                                    labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                                }
                            } else {
                                labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                            }

                            item = $('<div class="legendSingleContainer"></div>');

                            if (('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== 'null') && ('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== '')) {
                                id = label.replace(/\s/g, '_');
                                singleInfo = infoJson.secondAxis[id];

                                if ((singleInfo !== '') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                    infoIcon = $('<i class="fa fa-info-circle handPointer" data-axis="y" data-label="' + label + '" style="font-size: 12px; margin-left: 3px"></i>');
                                    infoIcon.css("color", styleParameters.legendFontColor);
                                    infoContainer = $('<div class="legendInfoContainer"></div>');
                                    infoContainer.append(infoIcon);
                                    item.append(colorContainer);
                                    item.append(infoContainer);
                                    item.append(labelContainer);
                                    infoIcon.on("click", showModalFieldsInfoSecondAxis);
                                } else {
                                    item.append(colorContainer);
                                    item.append(labelContainer);
                                }
                            } else {
                                item.append(colorContainer);
                                item.append(labelContainer);
                            }

                            item.css("color", styleParameters.legendFontColor);
                            item.find('a').css("color", styleParameters.legendFontColor);
                            item.find('a.thrLegendElement').css("color", "black");
                            item.find('i.fa-info-circl').css("color", styleParameters.legendFontColor);

                            if (i < series.secondAxis.labels.length - 1) {
                                item.css("margin-right", "10px");
                            }

                            item.css("display", "block");

                            //Workaround temporaneo per far vedere pie tradizionali con più di 3 fette
                            if (series.secondAxis.labels.length > 0) {
                                $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').append(item);

                                var parentLegendElement = $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').find("div.legendSingleContainer").eq(i);
                                var elementLeftPosition = parentLegendElement.position().left;
                                var widgetWidth = $("#<?= $_REQUEST['name_w'] ?>_div").width();
                                var legendMargin = null;

                                if (elementLeftPosition > (widgetWidth / 2)) {
                                    legendMargin = 200;
                                } else {
                                    legendMargin = 0;
                                }

                                $("#<?= $_REQUEST['name_w'] ?>_legendContainer1 .legendSingleContainer").eq(i).find("div.thrLegend ul").css("left", "-" + legendMargin + "%");
                            } else {
                                $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').hide();
                                $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("height", "93%");
                            }
                        }

                        for (var i = 0; i < series.firstAxis.labels.length; i++) {
                            label = series.firstAxis.labels[i];

                            if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) {
                                colorContainer = $('<div class="legendColorContainer" style="background-color: ' + styleParameters.colors2[i] + '"></div>');
                            } else {
                                colorContainer = $('<div class="legendColorContainer" style="background-color: ' + defaultColorsArray[i] + '"></div>');
                            }

                            //Aggiunta degli eventuali caret per i menu a comparsa per le legende sulle soglie
                            if ((thresholdsJson !== null) && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                if (thresholdObject.thresholdObject.firstAxis.fields[i].thrSeries.length > 0) {
                                    labelContainer = $('<div class="legendLabelContainer thrLegend dropup">' +
                                        '<a href="#" data-toggle="dropdown" style="text-decoration: none;" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' +
                                        '<ul class="dropdown-menu thrLegend">' +
                                        '</ul>' +
                                        '</div>');

                                    thresholdObject.thresholdObject.firstAxis.fields[i].thrSeries.forEach(function (range) {
                                        if (range.desc !== '') {
                                            dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                        } else {
                                            dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                        }
                                        dropDownElement.css("font", "bold 10px Verdana");
                                        dropDownElement.find("i").css("font-size", "12px");
                                        labelContainer.find("ul").append(dropDownElement);
                                    });
                                } else {
                                    labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                                }
                            } else {
                                labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                            }

                            item = $('<div class="legendSingleContainer"></div>');

                            if (('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== 'null') && ('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== '')) {
                                id = label.replace(/\s/g, '_');
                                singleInfo = infoJson.firstAxis[id];

                                if ((singleInfo !== '') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                    infoIcon = $('<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: 12px; color: black; margin-left: 3px"></i>'); //data-axis="y" data-label="' + $(this).html() + '" vanno rimessi?
                                    infoIcon.css("color", styleParameters.legendFontColor);
                                    infoContainer = $('<div class="legendInfoContainer"></div>');
                                    infoContainer.append(infoIcon);
                                    item.append(colorContainer);
                                    item.append(infoContainer);
                                    item.append(labelContainer);
                                    infoIcon.on("click", showModalFieldsInfoFirstAxis);
                                } else {
                                    item.append(colorContainer);
                                    item.append(labelContainer);
                                }
                            } else {
                                item.append(colorContainer);
                                item.append(labelContainer);
                            }

                            item.css("color", styleParameters.legendFontColor);
                            item.find('a').css("color", styleParameters.legendFontColor);
                            item.find('a.thrLegendElement').css("color", "black");
                            item.find('i.fa-info-circle').css("color", styleParameters.legendFontColor);

                            if (i < series.secondAxis.labels.length - 1) {
                                item.css("margin-right", "10px");
                            }

                            item.css("display", "block");

                            $('#<?= $_REQUEST['name_w'] ?>_legendContainer2').append(item);

                            var parentLegendElement = $('#<?= $_REQUEST['name_w'] ?>_legendContainer2').find("div.legendSingleContainer").eq(i);
                            var elementLeftPosition = parentLegendElement.position().left;
                            var widgetWidth = $("#<?= $_REQUEST['name_w'] ?>_div").width();
                            var legendMargin = null;

                            if (elementLeftPosition > (widgetWidth / 2)) {
                                legendMargin = 200;
                            } else {
                                legendMargin = 0;
                            }

                            $("#<?= $_REQUEST['name_w'] ?>_legendContainer2 .legendSingleContainer").eq(i).find("div.thrLegend ul").css("left", "-" + legendMargin + "%");
                        }
                    }
                } else {
                    for (var i = 0; i < series.secondAxis.labels.length; i++) {

                        if (editLabels != null) {
                            if (!flipFlag) {
                                if (editLabels.length == series.secondAxis.labels.length && !fromCode) {
                                    label = editLabels[i];
                                } else {
                                    label = series.secondAxis.labels[i];
                                }
                            } else {
                                // flipped case
                                label = series.secondAxis.labels[i];
                            }
                        } else {
                            label = series.secondAxis.labels[i];
                        }

                        //    label = series.secondAxis.labels[i];

                        if (((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) && styleParameters.colorsSelect1 == "manual") {
                            colorContainer = $('<div class="legendColorContainer" style="background-color: ' + styleParameters.colors1[i] + '"></div>');
                        } else {
                            colorContainer = $('<div class="legendColorContainer" style="background-color: ' + defaultColorsArray[i % 10] + '"></div>');
                        }

                        //Aggiunta degli eventuali caret per i menu a comparsa per le legende sulle soglie - Qui per ora è inutile, non esistono soglie sull'anello più interno
                        if ((thresholdsJson !== null) && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                            if (thresholdObject.thresholdObject.secondAxis.fields[i].thrSeries.length > 0) {
                                labelContainer = $('<div class="legendLabelContainer thrLegend dropup">' +
                                    '<a href="#" data-toggle="dropdown" style="text-decoration: none;" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' +
                                    '<ul class="dropdown-menu thrLegend">' +
                                    '</ul>' +
                                    '</div>');

                                thresholdObject.thresholdObject.secondAxis.fields[i].thrSeries.forEach(function (range) {
                                    if (range.desc !== '') {
                                        dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                    } else {
                                        dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                    }
                                    dropDownElement.css("font", "bold 10px Verdana");
                                    dropDownElement.find("i").css("font-size", "12px");
                                    labelContainer.find("ul").append(dropDownElement);
                                });
                            } else {
                                labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                            }
                        } else {
                            labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                        }

                        item = $('<div class="legendSingleContainer"></div>');

                        if (('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== 'null') && ('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== '')) {
                            id = label.replace(/\s/g, '_');
                            singleInfo = infoJson.secondAxis[id];

                            if ((singleInfo !== '') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                infoIcon = $('<i class="fa fa-info-circle handPointer" data-axis="y" data-label="' + label + '" style="font-size: 12px; margin-left: 3px"></i>');
                                infoIcon.css("color", styleParameters.legendFontColor);
                                infoContainer = $('<div class="legendInfoContainer"></div>');
                                infoContainer.append(infoIcon);
                                item.append(colorContainer);
                                item.append(infoContainer);
                                item.append(labelContainer);
                                infoIcon.on("click", showModalFieldsInfoSecondAxis);
                            } else {
                                item.append(colorContainer);
                                item.append(labelContainer);
                            }
                        } else {
                            item.append(colorContainer);
                            item.append(labelContainer);
                        }

                        item.css("color", styleParameters.legendFontColor);
                        item.find('a').css("color", styleParameters.legendFontColor);
                        item.find('a.thrLegendElement').css("color", "black");
                        item.find('i.fa-info-circl').css("color", styleParameters.legendFontColor);

                        if (i < series.secondAxis.labels.length - 1) {
                            item.css("margin-right", "10px");
                        }

                        item.css("display", "block");

                        //Workaround temporaneo per far vedere pie tradizionali con più di 3 fette
                        if (series.secondAxis.labels.length > 0) {

                            let legendSameItemFlag = false;
                            for (let n = 0; n < $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').children().length; n++) {
                                if ($('#<?= $_REQUEST['name_w'] ?>_legendContainer1').children()[n].textContent == item[0].textContent) {
                                    legendSameItemFlag = true;
                                }
                            }

                            if (!legendSameItemFlag) {
                                $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').append(item);
                            }

                            var parentLegendElement = $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').find("div.legendSingleContainer").eq(i);
                            var elementLeftPosition = parentLegendElement.position().left;
                            var widgetWidth = $("#<?= $_REQUEST['name_w'] ?>_div").width();
                            var legendMargin = null;

                            if (elementLeftPosition > (widgetWidth / 2)) {
                                legendMargin = 200;
                            } else {
                                legendMargin = 0;
                            }

                            $("#<?= $_REQUEST['name_w'] ?>_legendContainer1 .legendSingleContainer").eq(i).find("div.thrLegend ul").css("left", "-" + legendMargin + "%");
                        } else {
                            $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').hide();
                            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("height", "93%");
                        }
                    }

                    for (var i = 0; i < series.firstAxis.labels.length; i++) {
                        if (series.firstAxis.desc == "value name") {
                            if (editLabels != null) {
                                if (editLabels[i] != series.firstAxis.labels[i]) {
                                    label = editLabels[i];
                                } else {
                                    label = series.firstAxis.labels[i];
                                }
                            } else {
                                label = series.firstAxis.labels[i];
                            }
                        } else {
                            label = series.firstAxis.labels[i];
                        }

                        if (((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null)) && styleParameters.colorsSelect2 == "manual") {
                            colorContainer = $('<div class="legendColorContainer" style="background-color: ' + styleParameters.colors2[i] + '"></div>');
                        } else {
                            colorContainer = $('<div class="legendColorContainer" style="background-color: ' + defaultColorsArray2[i % 5] + '"></div>');
                        }

                        //Aggiunta degli eventuali caret per i menu a comparsa per le legende sulle soglie
                        if ((thresholdsJson !== null) && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                            if (thresholdObject.thresholdObject.firstAxis.fields[i].thrSeries.length > 0) {
                                labelContainer = $('<div class="legendLabelContainer thrLegend dropup">' +
                                    '<a href="#" data-toggle="dropdown" style="text-decoration: none;" class="dropdown-toggle"><span class="inline">' + label + '</span><b class="caret"></b></a>' +
                                    '<ul class="dropdown-menu thrLegend">' +
                                    '</ul>' +
                                    '</div>');

                                thresholdObject.thresholdObject.firstAxis.fields[i].thrSeries.forEach(function (range) {
                                    if (range.desc !== '') {
                                        dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '&nbsp;&nbsp;<b>' + range.desc + '</b></a></li>');
                                    } else {
                                        dropDownElement = $('<li><a href="#" class="thrLegendElement"><div style="width: 15px; height: 15px; border: none; float: left; background-color: ' + range.color + '"></div>&nbsp;&nbsp;' + range.min + ' <i class="fa fa-arrows-h"></i> ' + range.max + '</a></li>');
                                    }
                                    dropDownElement.css("font", "bold 10px Verdana");
                                    dropDownElement.find("i").css("font-size", "12px");
                                    labelContainer.find("ul").append(dropDownElement);
                                });
                            } else {
                                labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                            }
                        } else {
                            labelContainer = $('<div class="legendLabelContainer">' + label + '</div>');
                        }

                        item = $('<div class="legendSingleContainer"></div>');

                        if (('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== 'null') && ('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== '')) {
                            id = label.replace(/\s/g, '_');
                            singleInfo = infoJson.firstAxis[id];

                            if ((singleInfo !== '') && ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))) {
                                infoIcon = $('<i class="fa fa-info-circle handPointer" data-axis="x" data-label="' + label + '" style="font-size: 12px; color: black; margin-left: 3px"></i>'); //data-axis="y" data-label="' + $(this).html() + '" vanno rimessi?
                                infoIcon.css("color", styleParameters.legendFontColor);
                                infoContainer = $('<div class="legendInfoContainer"></div>');
                                infoContainer.append(infoIcon);
                                item.append(colorContainer);
                                item.append(infoContainer);
                                item.append(labelContainer);
                                infoIcon.on("click", showModalFieldsInfoFirstAxis);
                            } else {
                                item.append(colorContainer);
                                item.append(labelContainer);
                            }
                        } else {
                            item.append(colorContainer);
                            item.append(labelContainer);
                        }

                        item.css("color", styleParameters.legendFontColor);
                        item.find('a').css("color", styleParameters.legendFontColor);
                        item.find('a.thrLegendElement').css("color", "black");
                        item.find('i.fa-info-circle').css("color", styleParameters.legendFontColor);

                        if (i < series.secondAxis.labels.length - 1) {
                            item.css("margin-right", "10px");
                        }

                        //item.css("display", "block");	// Comment after BI

                        let legendSameItemFlag = false;
                        for (let n = 0; n < $('#<?= $_REQUEST['name_w'] ?>_legendContainer2').children().length; n++) {
                            if ($('#<?= $_REQUEST['name_w'] ?>_legendContainer2').children()[n].textContent == item[0].textContent) {
                                legendSameItemFlag = true;
                            }
                        }

                        if (!legendSameItemFlag) {
                            $('#<?= $_REQUEST['name_w'] ?>_legendContainer2').append(item);
                        }

                        var parentLegendElement = $('#<?= $_REQUEST['name_w'] ?>_legendContainer2').find("div.legendSingleContainer").eq(i);
                        var elementLeftPosition = parentLegendElement.position().left;
                        var widgetWidth = $("#<?= $_REQUEST['name_w'] ?>_div").width();
                        var legendMargin = null;

                        if (elementLeftPosition > (widgetWidth / 2)) {
                            legendMargin = 200;
                        } else {
                            legendMargin = 0;
                        }

                        $("#<?= $_REQUEST['name_w'] ?>_legendContainer2 .legendSingleContainer").eq(i).find("div.thrLegend ul").css("left", "-" + legendMargin + "%");
                    }
                }
        /*    } else {
                $('#<?= $_REQUEST['name_w'] ?>_legendContainer1').hide();
                $('#<?= $_REQUEST['name_w'] ?>_legendContainer2').hide();
            }   */
        }

        function buildSeriesFromAggregationData()
        {
            var seriesObj = {
                firstAxis:{
                    //desc:"Metrics",
                    desc:"value type",
                    labels:[]
                },
                secondAxis:{
                    //   "desc":"Values",
                    "desc":"value name",
                    "labels":[],
                    "series":[]
                }
            };

            for(var i = 0; i < aggregationGetData.length; i++)
            {
                switch(aggregationGetData[i].metricHighLevelType)
                {
                    case "KPI":
                        seriesObj.secondAxis.labels[i] = aggregationGetData[i].metricShortDesc;
                        var roundedVal = null;
                        if((aggregationGetData[i].metricType === "Percentuale")||(pattern.test(aggregationGetData[i].metricType)))
                        {
                            roundedVal = parseFloat(JSON.parse(aggregationGetData[i].data).value_perc1);
                            roundedVal = Number(roundedVal.toFixed(2));
                            seriesObj.secondAxis.series[i] = [roundedVal];
                        }
                        else
                        {
                            switch(aggregationGetData[i].metricType)
                            {
                                case "Intero":
                                    seriesObj.secondAxis.series[i] = [parseInt(JSON.parse(aggregationGetData[i].data).value_num)];
                                    break;

                                case "Float":
                                    roundedVal = parseFloat(JSON.parse(aggregationGetData[i].data).value_num);
                                    roundedVal = Number(roundedVal.toFixed(2));
                                    seriesObj.secondAxis.series[i] = [roundedVal];
                                    break;

                                //I testuali NON li aggiungiamo al grafico
                                default:
                                    break;
                            }
                        }
                        break;

                    //Poi si aggiungeranno altri casi
                    default:
                        break;
                }
            }
            return seriesObj;
        }

        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);

            var bodyHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - widgetHeaderHeight);
            $("#" + widgetName + "_loading").css("height", bodyHeight + "px");
            $("#" + widgetName + "_content").css("height", bodyHeight + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').highcharts().reflow();
	}
        //Fine definizioni di funzione

        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        //addLink(widgetName, url, linkElement, divContainer, null);

        //Nuova versione
        if(('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "")&&('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "null"))
        {
            styleParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>');
        }

        if('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>'.length > 0)
        {
            widgetParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>');
        }

        if(widgetParameters !== null && widgetParameters !== undefined)
        {
            if(widgetParameters.hasOwnProperty("thresholdObject"))
            {
              thresholdObject = JSON.parse(widgetParameters.thresholdObject);
            }
        }

        if(('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== 'null')&&('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>' !== ''))
        {
            infoJson = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['infoJson']) ?>');
        }

        $.ajax({
            url: "../controllers/getWidgetParams.php",
            type: "GET",
            data: {
                widgetName: "<?= $_REQUEST['name_w'] ?>"
            },
            async: true,
            dataType: 'json',
            success: function(widgetData)
            {
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
                gridLineColor = widgetData.params.chartPlaneColor;
                chartAxesColor = widgetData.params.chartAxesColor;
                serviceUri = widgetData.params.serviceUri;
				code = widgetData.params.code;
				//
				//parameters = widgetData.params.parameters;
				//
				//
				
                if (nrMetricType != null) {
                    openWs();
                }

                if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
                {
                    showHeader = false;
                }
                else
                {
                    showHeader = true;
                }

                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
                    widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
                    widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
                    widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
                    rowParameters = widgetData.params.rowParameters;
                }
                else
                {
                    metricName = metricNameFromDriver;
                    widgetTitleFromDriver.replace(/_/g, " ");
                    widgetTitleFromDriver.replace(/\'/g, "&apos;");
                    widgetTitle = widgetTitleFromDriver;
                    $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
                    widgetHeaderColor = widgetHeaderColorFromDriver;
                    widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
                }

                setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
                $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
                $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

                if(firstLoad === false)
                {
                    showWidgetContent(widgetName);
                }
                else
                {
                    setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                }

                if((widgetData.params.styleParameters !== "")&&(widgetData.params.styleParameters !== "null"))
                {
                    styleParameters = JSON.parse(widgetData.params.styleParameters);
                    groupByAttr = styleParameters['groupByAttr'];
                }

                if(widgetData.params.parameters !== null)
                {
                    if(widgetData.params.parameters.length > 0)
                    {
						if (widgetData.params.parameters !=='{"mode": "ckeditor"}'){
                        widgetParameters = JSON.parse(widgetData.params.parameters);
                        thresholdsJson = widgetParameters;
						} else {
							// console.log('CKEDITOR!!!');
						}
						
                    }
                }

                if((widgetData.params.infoJson !== 'null')&&(widgetData.params.infoJson !== ''))
                {
                    infoJson = JSON.parse(widgetData.params.infoJson);
                    //Patch per il resize, non mostriamo i pulsanti info per ora
                    infoJson = null;
                }

                chartType = styleParameters.chartType;
                lineWidth = styleParameters.lineWidth;

                /*     switch(chartType)
                     {
                         case 'lines':
                             stackingOption = null;
                             highchartsChartType = 'spline';
                             dataLabelsAlign = 'center';
                             dataLabelsVerticalAlign = 'middle';
                             dataLabelsY = 0;
                             break;

                         case 'area':
                             stackingOption = null;
                             highchartsChartType = 'areaspline';
                             dataLabelsAlign = 'center';
                             dataLabelsVerticalAlign = 'middle';
                             dataLabelsY = 0;
                             break;

                         case 'stacked':
                             stackingOption = 'normal';
                             highchartsChartType = 'areaspline';
                             dataLabelsAlign = 'center';
                             dataLabelsVerticalAlign = 'middle';
                             dataLabelsY = 0;
                             break;

                         default:
                             stackingOption = null;
                             highchartsChartType = 'spline';
                             dataLabelsAlign = 'center';
                             break;
                     }*/

                if(typeof rowParameters === 'string')
                    rowParameters = JSON.parse(rowParameters);
				////////////lettura code
				if (widgetData.params.code != null && widgetData.params.code != "null") {
                        let code = widgetData.params.code;
                        var text_ck_area = document.createElement("text_ck_area");
                        text_ck_area.innerHTML = code;
                        var newInfoDecoded = text_ck_area.innerText;
                        newInfoDecoded = newInfoDecoded.replaceAll("function execute()","function execute_" + "<?= $_REQUEST['name_w'] ?>(param)");

                        var elem = document.createElement('script');
                        elem.type = 'text/javascript';
                        elem.innerHTML = newInfoDecoded;
                        try {
                            $('#<?= $_REQUEST['name_w'] ?>_code').append(elem);

                            $('#<?= $_REQUEST['name_w'] ?>_code').css("display", "none");
                        } catch(e) {
                            console.log("Error in appending JS function to DOM on " + widgetName);
                        }
						//
						
						//
                    }
				//////////////
                populateWidget(null);

            },
            error: function(errorData)
            {
                console.log("Error in widget params retrieval");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
            }
        });

        //Web socket
        openWs = function(e)
        {
            try
            {
                <?php
                $genFileContent = parse_ini_file("../conf/environment.ini");
                $wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
                $wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$genFileContent['environment']['value']];
                $wsServerPort = $wsServerContent["wsServerPort"][$genFileContent['environment']['value']];
                $wsPath = $wsServerContent["wsServerPath"][$genFileContent['environment']['value']];
                $wsProtocol = $wsServerContent["wsServerProtocol"][$genFileContent['environment']['value']];
                $wsRetryActive = $wsServerContent["wsServerRetryActive"][$genFileContent['environment']['value']];
                $wsRetryTime = $wsServerContent["wsServerRetryTime"][$genFileContent['environment']['value']];
                echo 'wsRetryActive = "' . $wsRetryActive . '";';
                echo 'wsRetryTime = ' . $wsRetryTime . ';';
                echo 'webSocket = new WebSocket("' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '");';
                ?>

                webSocket.addEventListener('open', openWsConn);
                webSocket.addEventListener('close', wsClosed);

                setTimeout(function(){
                    webSocket.removeEventListener('close', wsClosed);
                    webSocket.removeEventListener('open', openWsConn);
                    webSocket.removeEventListener('message', manageIncomingWsMsg);
                    webSocket.close();
                    webSocket = null;
                }, (timeToReload - 2)*1000);
            }
            catch(e)
            {
                wsClosed();
            }
        };

        manageIncomingWsMsg = function(msg)
        {
            var msgObj = JSON.parse(msg.data);

            switch(msgObj.msgType)
            {
                case "newNRMetricData":
                    if(encodeURIComponent(msgObj.metricName) === encodeURIComponent(metricName))
                    {
                        //    <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);

                        var newValue = msgObj.newValue;
                        var point = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().series[0].points[0];
                        //    point.update(newValue);

                        rowParameters = newValue;
                        emptyLegendFlagFromWs = true;
                        populateWidget(null);

                    }
                    break;

                default:
                    break;
            }
        };

        openWsConn = function(e)
        {
            var wsRegistration = {
                msgType: "ClientWidgetRegistration",
                userType: "widgetInstance",
                metricName: encodeURIComponent(metricName),
                widgetUniqueName: "<?= $_REQUEST['name_w'] ?>"
            };
            webSocket.send(JSON.stringify(wsRegistration));

            setTimeout(function(){
                webSocket.removeEventListener('close', wsClosed);
                webSocket.close();
            }, (timeToReload - 2)*1000);

            webSocket.addEventListener('message', manageIncomingWsMsg);
        };

        wsClosed = function(e)
        {
            webSocket.removeEventListener('close', wsClosed);
            webSocket.removeEventListener('open', openWsConn);
            webSocket.removeEventListener('message', manageIncomingWsMsg);
            webSocket = null;
            if(wsRetryActive === 'yes')
            {
                setTimeout(openWs, parseInt(wsRetryTime*1000));
            }
        };

        //Per ora non usata
        wsError = function(e)
        {

        };
		$(document).off('resetContent_' + widgetName);
        $(document).on('resetContent_' + widgetName, function(){
            $.ajax({
                url: "../controllers/getWidgetParams.php",
                type: "GET",
                data: {
                    widgetName: "<?= $_REQUEST['name_w'] ?>"
                },
                async: true,
                dataType: 'json',
                success: function(widgetData)
                {
                    rowParameters = JSON.parse(widgetData.params.rowParameters);
                    populateWidget();
                    
                },
                error: function(errorData)
                {
                    console.log("Error in widget params retrieval");
                    console.log(JSON.stringify(errorData));
                    showWidgetContent(widgetName);
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                    $("#<?= $_REQUEST['name_w'] ?>_table").hide(); 
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                }
            });
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
        $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function(event){
                clearInterval(countdownRef);
                timeToReload = event.newTimeToReload;
                countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        });
        
        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
    function clear(){
                dateChoice = null;
                $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').val='';
            }

            $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').datetimepicker().on('dp.show',function(){
                $('.media').css({'overflow':'visible', 'z-index':'1000000'});
            }).on('dp.hide',function(){
                $('.media').css({'overflow':'hidden'});
            })

            $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').datetimepicker().on('dp.change', function (e) {  
                var date = $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").date();
                dateChoice = date;
                timeNavCount = 0;
                    //populateWidget(true, timeRange, null, 0);
					var timeRange = dateChoice;
					populateWidget(date);
                    //loadHyperCube();
                    //drawDiagram(true, xAxisFormat, yAxisType);
            });
            $('#<?= $_REQUEST['name_w'] ?>_datetimepicker').data("DateTimePicker").clear()

                if (<?= $_REQUEST['name_w'] ?>_loaded==false){

                document.getElementById('<?= $_REQUEST['name_w'] ?>_droptitle').addEventListener('click', function (e) {
                  const dropdown = e.currentTarget.parentNode;
                  const menu = dropdown.querySelector('.menu');

                  toggleClass(menu,'hide');
               });

                document.getElementById('<?= $_REQUEST['name_w'] ?>_droptitle').addEventListener('change', function (e) {
                    <?= $_REQUEST['name_w'] ?>_select = e.target.textContent.trimEnd();
                    //populateWidget(true, timeRange, "minus", timeNavCount);
                    //loadHyperCube();
                    //drawDiagram(true, xAxisFormat, yAxisType);
                });
                <?= $_REQUEST['name_w'] ?>_loaded = true;
            }

		///////////////////
	});//Fine document ready

</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        
        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainerPie"></div>
            <div id="<?= $_REQUEST['name_w'] ?>_legendContainer1" class="legendContainer1"></div>
            <div id="<?= $_REQUEST['name_w'] ?>_legendContainer2" class="legendContainer2"></div>
        </div>
		<!-- -->
		<div style="position: relative;">
		 <div class="widget-dropbdown" style="position: absolute;">
                                <div class='dropdown' style="float: left;width: 20%; padding-left: 5%">
                            
                            <div id='<?= $_REQUEST['name_w'] ?>_droptitle' class='dropdown-title title pointerCursor'></div>
                            
                            <div id='<?= $_REQUEST['name_w'] ?>_options' class='menu pointerCursor hide'></div>

                        </div>
                                <div class ="form-group" style="float: left;width:30%; ">  
                                <div class ='input-group date' id='<?= $_REQUEST['name_w'] ?>_datetimepicker'>  
                                  <input type ='text' class="form-control" /> 
                                  <span class ="input-group-addon">  
                                    <span class ="glyphicon glyphicon-calendar"></span>  
                                  </span>  
                                </div> 
                              </div>
                              
                         <!--
                            <button id='<?= $_REQUEST['name_w'] ?>_cut' style="float: left;width:25%; padding:0.6em 0em;">Toggle Time Slice</button>
                            <button id='<?= $_REQUEST['name_w'] ?>_stream' style="float: left;width:25%; padding:0.6em 0em;">Toggle Stream Graph</button>
							-->
                        
                 </div>
			</div>
		<!-- -->
    </div>
		<div id="<?= $_REQUEST['name_w'] ?>_code"></div>
</div> 

