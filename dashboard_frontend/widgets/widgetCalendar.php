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

<script src="../js/d3/d3.js"></script>

<script type='text/javascript'>
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, isRestoringFromExternalContent) {
        <?php
        $link = mysqli_connect($host, $username, $password);
        if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
            eventLog("Returned the following ERROR in widgetCalendar.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
            exit();
        }
        ?>
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var showHeader = null;
        var addSampleToTrend = null;
        var metricData, metricType, series, styleParameters, timeRange, gridLineColor, chartAxesColor, chartType, index, highchartsChartType, chartSeriesObject, legendWidth, xAxisCategories, rowParameters, aggregationGetData, getDataFinishCount, xAxisType,
            dataLabelsRotation, dataLabelsAlign, dataLabelsVerticalAlign, dataLabelsY, legendItemClickValue, stackingOption, fontSize, fontColor, chartColor, dataLabelsFontSize, chartLabelsFontSize, dataLabelsFontColor, chartLabelsFontColor, appId, flowId, nrMetricType,
            widgetHeight, lineWidth, xAxisTitle, smField, widgetTitle, countdownRef, widgetParameters, thresholdsJson, infoJson, xAxisFormat, yAxisType, idMetric = null;
        var serviceUri = "";
        var editLabels = "";
        var valueUnit = null;
        var seriesDataArray = [];
        var utcOption = false;
        var rowParamLength = null;
        var dataOriginV = null;
        var upperTimeLimitISOTrimmed = null;
        var timeNavCount = 0;
        var fromGisExternalContentRangePrevious = null;
        var fromGisExternalContentServiceUriPrevious = null;
        var fromGisExternalContentFieldPrevious = null;
        var dataFut = null;
        var upLimit, upperTime = null;
        var now = new Date();
        var nowUTC = now.toUTCString();
        var isoDate = new Date(nowUTC).toISOString();

        var pattern = /Percentuale\//;
        var objName = null;
        var webSocket, openWs, manageIncomingWsMsg, openWsConn, wsClosed = null;

        console.log("Entrato in widgetCalendar --> " + widgetName);

        $(document).off('mouseOverTimeTrendFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverTimeTrendFromExternalContentGis_' + widgetName, function(event)
        {
            widgetOriginalBorderColor = $("#" + widgetName).css("border-color");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(event.widgetTitle);
            $("#" + widgetName).css("border-color", event.color1);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", event.color1);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "-webkit-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "-o-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "-moz-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "linear-gradient(to left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("color", "black");
        });

        $(document).off('mouseOutTimeTrendFromExternalContentGis_' + widgetName);
        $(document).on('mouseOutTimeTrendFromExternalContentGis_' + widgetName, function(event)
        {
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", widgetHeaderColor);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("color", widgetHeaderFontColor);
        });

        $(document).off('showTimeTrendFromExternalContentGis_' + widgetName);
        $(document).on('showTimeTrendFromExternalContentGis_' + widgetName, function(event)
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef);
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, event.range, event.marker, event.mapRef, event.fakeId, false, null, null, event.futureLastDate);
            }
        });

        $(document).off('restoreOriginalTimeTrendFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalTimeTrendFromExternalContentGis_' + widgetName, function(event)
        {
            isRestoringFromExternalContent = true;
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef);
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, "<?= sanitizeTitle($_REQUEST['title_w']) ?>", "<?= escapeForJS($_REQUEST['frame_color_w']) ?>", "<?= $_REQUEST['headerFontColor'] ?>", false, null, null, null, null, null, null, false, null);
            }
        });

        function truncateStackedSerie(serie, timeRange) {

            var truncatedSerie = [];
            var truncatedMillis = null;

            for (let n = 0; n < serie.length; n++) {
                truncatedMillis = moment(serie[n][0]).milliseconds(0).valueOf();
                truncatedSerie[n] = [truncatedMillis, serie[n][1]];
            }
            return truncatedSerie;

        }


        function resizeWidget() {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);

            if (infoJson !== "fromTracker" || fromGisExternalContent === true) {
                var titleDiv = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv');
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("color", widgetHeaderFontColor);
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton').css("color", widgetHeaderFontColor);
                titleDiv.css("width", "70%");

                if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 400) {
                    titleDiv.css("width", "65%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "19%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 480) {
                    titleDiv.css("width", "74%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "14%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 560) {
                    titleDiv.css("width", "75%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "15%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 700) {
                    titleDiv.css("width", "80%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "11%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 900) {
                    titleDiv.css("width", "84%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "9%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1000) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "8%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1050) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else {
                    titleDiv.css("width", "87%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                }
            }
        }

        function adjustData(groupData) {
        //    for (var n=0; n<groupData.length; n++) {
                let n = 0;
                let auxVal = null;
                let arrLength = groupData.length;
                for (var m=0; m<groupData[n].length; m++) {
                    let o = 0;
                //    for (o=0; o<groupData[n][m][1].length; o++) {
                    while (o<groupData[n][m][1].length) {
                        if (groupData[n][m][1][o][0].getUTCFullYear() == new Date().getUTCFullYear() - timeNavCount) {
                            // slice from original arrayù
                            auxVal = groupData[n][m][1][o];
                            groupData[n][m][1].splice(o,1);
                            if (groupData[arrLength] == null) {
                                groupData[arrLength] = [];
                                groupData[arrLength][0] = [];
                                groupData[arrLength][0][0] = groupData[n][m][0];
                                groupData[arrLength][0][1] = [];
                                groupData[arrLength][0][1][0] = auxVal;
                            //    groupData[arrLength].push(groupData[n][m][1][o][0]);
                            } else {
                                let i = 0;
                                for (i=0; i<groupData[arrLength].length; i++) {
                               // while (i<groupData[arrLength].length) {
                                    if(groupData[arrLength][i][0] == groupData[n][m][0]) {
                                        if (groupData[arrLength][i] == null) {
                                            groupData[arrLength][i] = [];
                                            groupData[arrLength][i][0] = groupData[n][m][0];
                                            groupData[arrLength][i][1] = [];
                                            groupData[arrLength][i][1][0] = auxVal;
                                        } else {
                                            groupData[arrLength][i][1].push(auxVal);
                                        }
                                    } else if (i == groupData[arrLength].length-1){
                                        groupData[arrLength][i+1] = [];
                                        groupData[arrLength][i+1][0] = groupData[n][m][0];
                                        groupData[arrLength][i+1][1] = [];
                                      //  groupData[arrLength][i+1][1][0] = auxVal;
                                      //  i++;
                                    }
                                   // groupData[arrLength][i] = groupData[n][m][1][o][0];
                                }
                            }
                        } else {
                            o++;
                        }
                    }
                }
        //    }
            return groupData;
        }

        function drawDiagram(timeDomain, xAxisFormat, yAxisFormat) {
            elToEmpty.empty();

            if (chartSeriesObject != null) {

                var calendarCellSize = 17;
                var calendarWidth = 0;
                var calendarHeight = 0;

                var timeSlots = ["00:00", "01:00", "02:00", "03:00", "04:00", "05:00", "06:00", "07:00", "08:00", "09:00", "10:00", "11:00",
                    "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00"];
                var monthArray = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
                var weekdayArray = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

                var yLabel, cornerTag = 'NULL';
                var countDay = 'NULL';

                var currentViewMode = styleParameters.calendarViewMode;

                var formatDate = d3.timeFormat("%d/%m/%Y");

                var formatDay = i => yLabel[i];
                var formatMonth = i => cornerTag[i];

                var dayFormat = d3.timeFormat("%e");
                var formatValue = d3.format(".2f");
                var monthFormat = d3.timeFormat("%m");

                var tIndex = 0;
                var calendarData = [];

                var dati = chartSeriesObject[tIndex].data.map(d => [new Date(d[0]), d[1]]);

                if (rowParameters[0].metricHighLevelType === "Sensor"  || rowParameters[0].metricHighLevelType == "IoT Device Variable" || rowParameters[0].metricHighLevelType == "Data Table Variable" || rowParameters[0].metricHighLevelType == "Mobile Device Variable"){
                    dati.reverse(); // se provengono da sensori li riordiniamo dal più vecchio al più recente
                }

                var groupedData = d3.groups(dati, d => d[0].getMonth()).map(function(d) {
                    if (currentViewMode === "monthly") {
                        return d3.groups(d[1], d => d[0].getDate()).map(function (d) {
                            return d3.groups(d[1], d => d[0].getHours());
                        });
                    } else if (currentViewMode === "yearly") {
                        return d3.groups(d[1], d => d[0].getDate());
                    } else {
                        console.warn("Nessuna vista selezionata.");
                        if ((timeRange === "4 Ore")||(timeRange === "12 Ore")||(timeRange === "Giornaliera")||(timeRange === "Settimanale")||(timeRange === "Mensile")) {
                            currentViewMode = "monthly";
                            return d3.groups(d[1], d => d[0].getDate()).map(function (d) {
                                return d3.groups(d[1], d => d[0].getHours());
                            });
                        } else if ((timeRange === "Semestrale")||(timeRange === "Annuale")) {
                            currentViewMode = "yearly";
                            return d3.groups(d[1], d => d[0].getDate());
                        } else {
                            console.error("Calendar: errore nella lettura del time range.");
                        }
                    }
                });

                if (currentViewMode === "yearly") {
                    groupedData = adjustData(groupedData);
                }

                groupedData.map(function(d){
                    d.map(function(d){
                        if (currentViewMode === "monthly") {
                            d.map(function (d) {
                                let singleHourObject = [];
                                let sommaValori = 0;
                                for (let k = 0; k < d[1].length; k++) {
                                    if (!isNaN(d[1][k][1])) {
                                        sommaValori += d[1][k][1];
                                    }
                                }
                                singleHourObject.push(d[1][0][0]);
                                if (styleParameters.meanSum === "mean") {
                                    singleHourObject.push(formatValue(sommaValori / (d[1].length)));
                                } else if (styleParameters.meanSum === "sum") {
                                    singleHourObject.push(Math.round(sommaValori));
                                } else {
                                    singleHourObject.push(formatValue(sommaValori / (d[1].length)));
                                }
                                calendarData.push(singleHourObject);
                            });
                        } else if (currentViewMode === "yearly") {
                            let singleDayObject = [];
                            let sommaValoriGiornalieri = 0;
                            for (let k = 0; k < d[1].length; k++) {
                                if (!isNaN(d[1][k][1])) {
                                    sommaValoriGiornalieri += d[1][k][1];
                                }
                            }
                            if (d[1][0] != null) {
                                singleDayObject.push(d[1][0][0]);
                                if (styleParameters.meanSum === "mean") {
                                    singleDayObject.push(formatValue(sommaValoriGiornalieri / (d[1].length)));
                                } else if (styleParameters.meanSum === "sum") {
                                    singleDayObject.push(Math.round(sommaValoriGiornalieri));
                                } else {
                                    singleDayObject.push(formatValue(sommaValoriGiornalieri / (d[1].length)));
                                }
                                calendarData.push(singleDayObject);
                            }
                        } else {
                            console.error("Viewmode not selected.");
                        }
                    });
                });

                // si potrebbe inserire l'impostazione del quantile nelle more options
                const color = d3.scaleSequential(styleParameters.barsColors[0] === undefined ? d3.interpolateBlues : d3.interpolateRgb("#ffffff", styleParameters.barsColors[0]))
                    .domain([0, +d3.quantile(calendarData.map(d => Math.abs(d[1])).sort(d3.ascending), 0.85)])
                    .unknown("#ffffff");

                if (currentViewMode === "monthly") {

                    calendarWidth = 579;
                    calendarHeight = calendarCellSize * 26;

                    yLabel = timeSlots;
                    cornerTag = monthArray;

                    countDay = i => i % 24;

                    const months = d3.groups(calendarData, d => d[0].getMonth()).reverse();

                    const svg = d3.select("#<?= $_REQUEST['name_w'] ?>_chartContainer")
                        .append("svg")
                        .attr("viewBox", [0, 0, calendarWidth, calendarHeight * months.length])
                        .attr("font-family", "sans-serif")
                        .attr("font-size", 10);

                    const month = svg.selectAll("g")
                        .data(months)
                        .join("g")
                        .attr("transform", (d, i) => `translate(40.5,${calendarHeight * i + calendarCellSize * 1.5})`);

                    month.append("text")
                        .attr("x", -5)
                        .attr("y", -5)
                        .attr("font-weight", "bold")
                        .attr("text-anchor", "end")
                        .style('fill', fontColor)
                        .text(([key]) => formatMonth(key));

                    month.append("g")
                        .attr("text-anchor", "end")
                        .selectAll("text")
                        .data(d3.range(yLabel.length))
                        .join("text")
                        .attr("x", -5)
                        .attr("y", i => (countDay(i) + 0.5) * calendarCellSize)
                        .attr("dy", "0.31em")
                        .style('fill', fontColor)
                        .text(formatDay);

                    month.append("g")
                        .selectAll("rect")
                        .data(([, values]) => values)
                        .join("rect")
                        .attr("width", calendarCellSize - 1)
                        .attr("height", calendarCellSize - 1)
                        .attr("x", d => d3.timeDay.count(d3.timeMonth(d[0]), d[0]) * calendarCellSize + 0.5)
                        .attr("y", d => countDay(d[0].getHours()) * calendarCellSize + 0.5)
                        .attr("fill", d => color(d[1]))
                        .append("title")
                    //    .text(d => `${rowParameters[0].metricName} - ${formatDate(d[0])}
                        .text(d => `${objName} - ${formatDate(d[0])}
                            Time: ${d[0].getHours()}:00 - ${(d[0].getHours() + 1)}:00
                            ${rowParameters[0].smField}: ${d[1]}`
                        );

                    const daysInMonth = month.append("g")
                        .selectAll("g")
                        .data(([, values]) => d3.timeDays(d3.timeDay(values[0][0]), values[values.length - 1][0]))
                        .join("g");

                    daysInMonth.append("text")
                        .attr("x", d => d3.timeDay.count(d3.timeMonth(d), d3.timeDay.ceil(d)) * calendarCellSize + 2)
                        .attr("y", -5)
                        .style('fill', fontColor)
                        .text(dayFormat);

                } else if (currentViewMode === "yearly") {

                    calendarWidth = 954;
                    calendarHeight = calendarCellSize * 9;

                    yLabel = weekdayArray;

                    countDay = i => (i + 6) % 7;

                    const years = d3.groups(calendarData, d => d[0].getFullYear()).reverse();

                    const svg = d3.select("#<?= $_REQUEST['name_w'] ?>_chartContainer")
                        .append("svg")
                        .attr("viewBox", [0, 0, calendarWidth, calendarHeight * years.length])
                        .attr("font-family", "sans-serif")
                        .attr("font-size", 10);

                    const year = svg.selectAll("g")
                        .data(years)
                        .join("g")
                        .attr("transform", (d, i) => `translate(40.5,${calendarHeight * i + calendarCellSize * 1.5})`);

                    year.append("text")
                        .attr("x", -5)
                        .attr("y", -5)
                        .attr("font-weight", "bold")
                        .attr("text-anchor", "end")
                        .style('fill', fontColor)
                        .text(([key]) => key);

                    year.append("g")
                        .attr("text-anchor", "end")
                        .selectAll("text")
                        .data(d3.range(7))
                        .join("text")
                        .attr("x", -5)
                        .attr("y", i => (countDay(i) + 0.5) * calendarCellSize)
                        .attr("dy", "0.31em")
                        .style('fill', fontColor)
                        .text(formatDay);

                    year.append("g")
                        .selectAll("rect")
                        .data(([, values]) => values)
                        .join("rect")
                        .attr("width", calendarCellSize - 1)
                        .attr("height", calendarCellSize - 1)
                        .attr("x", d => d3.timeMonday.count(d3.timeYear(d[0]), d[0]) * calendarCellSize + 0.5)
                        .attr("y", d => countDay(d[0].getDay()) * calendarCellSize + 0.5)
                        .attr("fill", d => color(d[1]))
                        .append("title")
                    //    .text(d => `${rowParameters[0].metricName} - ${formatDate(d[0])}
                        .text(d => `${objName} - ${formatDate(d[0])}

${rowParameters[0].smField}: ${d[1]}`);

                    function pathMonth(t) {
                        const n = 7;
                        const d = Math.max(0, Math.min(n, countDay(t.getDay())));
                        const w = d3.timeMonday.count(d3.timeYear(t), t);
                        return `${d === 0 ? `M${w * calendarCellSize},0`
                            : d === n ? `M${(w + 1) * calendarCellSize},0`
                                : `M${(w + 1) * calendarCellSize},0V${d * calendarCellSize}H${w * calendarCellSize}`}V${n * calendarCellSize}`;
                    }

                    const month = year.append("g")
                        .selectAll("g")
                        .data(([, values]) => d3.timeMonths(d3.timeMonth(values[0][0]), values[values.length - 1][0]))
                        .style('fill', fontColor)
                        .join("g");

                    month.filter((d, i) => i).append("path")
                        .attr("fill", "none")
                        .attr("stroke", "#ffffff")
                        .attr("stroke-width", 3)
                        .attr("d", pathMonth);

                    month.append("text")
                        .attr("x", d => d3.timeMonday.count(d3.timeYear(d), d3.timeMonday.ceil(d)) * calendarCellSize + 2)
                        .attr("y", -5)
                        .style('fill', fontColor)
                        .text(d => {
                            return monthArray[monthFormat(d) - 1];
                        });

                } else {
                    console.error("Calendar: errore nella lettura del viewMode.");
                }

            } else {

                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
            }
        }

        function getUpperTimeLimit(timeRange, timeCount) {
            let hours = 0;
            switch(timeRange) {
                case "Annuale":
                    hours = 365*24*timeCount;
                    break;

                case "Semestrale":
                    hours = 180*24*timeCount;
                    break;

                case "Mensile":
                    hours = 30*24*timeCount;
                    break;

                case "Settimanale":
                    hours = 7*24*timeCount;
                    break;

                case "Giornaliera":
                    hours = 24*timeCount;
                    break;

                case "12 Ore":
                    hours = 12*timeCount;
                    break;

                case "4 Ore":
                    hours = 4*timeCount;
                    break;
            }
            let now = new Date();
            let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
            let upperTimeLimit = now.setHours(now.getHours() - hours - timeZoneOffsetHours);
            let upperTimeLimitUTC = new Date(upperTimeLimit).toUTCString();
            let upperTimeLimitISO = new Date(upperTimeLimitUTC).toISOString();
            let upperTimeLimitISOTrim = upperTimeLimitISO.substring(0, isoDate.length - 5);
            return upperTimeLimitISOTrim;
            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
        }

        /*function convertFromMomentToTime(momentDate) {
            var momentDateTime = momentDate.format();
            //  momentDateTime = momentDateTime.replace("T", " ");
            var plusIndexLocal = momentDateTime.indexOf("+");
            momentDateTime = momentDateTime.substr(0, plusIndexLocal);
            var convertedDateTime = momentDateTime;
            return convertedDateTime;
        }*/

        function convertDataFromTimeNavToDm(originalData, field) {
            var singleOriginalData, singleData, convertedDate, futureDate = null;
            var convertedData = {
                data: []
            };

            var originalDataWithNoTime = 0;
            var originalDataNotNumeric = 0;

            if(originalData.hasOwnProperty("realtime"))
            {
                if(originalData.realtime.hasOwnProperty("results"))
                {
                    if(originalData.realtime.results.hasOwnProperty("bindings"))
                    {
                        if(originalData.realtime.results.bindings.length > 0)
                        {
                            for(var i = 0; i < originalData.realtime.results.bindings.length; i++)
                            {
                                singleData = {
                                    commit: {
                                        author: {
                                            IdMetric_data: null, //Si puÃ² lasciare null, non viene usato dal widget
                                            computationDate: null,
                                            futureDate: null,
                                            value_perc1: null, //Non lo useremo mai
                                            value: null,
                                            descrip: null, //Mettici il nome della metrica splittato
                                            threshold: null, //Si puÃ² lasciare null, non viene usato dal widget
                                            thresholdEval: null //Si puÃ² lasciare null, non viene usato dal widget
                                        },
                                        range_dates: 0//Si puÃ² lasciare null, non viene usato dal widget
                                    }
                                };

                                singleOriginalData = originalData.realtime.results.bindings[i];
                                if(singleOriginalData.hasOwnProperty("updating"))
                                {
                                    convertedDate = singleOriginalData.updating.value;
                                }
                                else
                                {
                                    if(singleOriginalData.hasOwnProperty("measuredTime"))
                                    {
                                        convertedDate = singleOriginalData.measuredTime.value;
                                    }
                                    else
                                    {
                                        if(singleOriginalData.hasOwnProperty("instantTime"))
                                        {
                                            convertedDate = singleOriginalData.instantTime.value;
                                        }
                                        else
                                        {
                                            originalDataWithNoTime++;
                                            continue;
                                        }
                                    }
                                }

                                // TIME-ZONE CONVERSION
                                var localTimeZone = moment.tz.guess();
                                var momentDateTime = moment(convertedDate);
                                var localDateTime = momentDateTime.tz(localTimeZone).format();
                                localDateTime = localDateTime.replace("T", " ");
                                var plusIndexLocal = localDateTime.indexOf("+");
                                localDateTime = localDateTime.substr(0, plusIndexLocal);

                                convertedDate = convertedDate.replace("T", " ");
                                var plusIndex = convertedDate.indexOf("+");
                                convertedDate = convertedDate.substr(0, plusIndex);
                                if (singleOriginalData[field].hasOwnProperty("valueDate")) {
                                    futureDate = singleOriginalData[field].valueDate.replace("T", " ");
                                    var plusIndexFuture = futureDate.indexOf("+");
                                    futureDate = futureDate.substr(0, plusIndexFuture);
                                    var momentDateTimeFuture = moment(futureDate);
                                    var localDateTimeFuture = momentDateTimeFuture.tz(localTimeZone).format();
                                    localDateTimeFuture = localDateTimeFuture.replace("T", " ");
                                    var plusIndexLocalFuture = localDateTimeFuture.indexOf("+");
                                    localDateTimeFuture = localDateTimeFuture.substr(0, plusIndexLocalFuture);
                                }
                                if (localDateTime == "") {
                                    singleData.commit.author.computationDate = convertedDate;
                                    singleData.commit.author.futureDate = futureDate;
                                } else {
                                    singleData.commit.author.computationDate = localDateTime;
                                    singleData.commit.author.futureDate = localDateTimeFuture;

                                }

                                if(singleOriginalData[field] !== undefined) {
                                    if (!isNaN(parseFloat(singleOriginalData[field].value))) {
                                        singleData.commit.author.value = parseFloat(singleOriginalData[field].value);
                                    } else {
                                        originalDataNotNumeric++;
                                        continue;
                                    }
                                } else {
                                    originalDataNotNumeric++;
                                    continue;
                                }

                                convertedData.data.push(singleData);
                            }

                            if (convertedData.data.length > 0) {
                                return convertedData;
                            } else {
                                convertedData.data.push(singleData)
                                return convertedData;
                            }
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        /*function compareSeriesData(a, b) {
            var x = a[0];
            var y = b[0];

            if(x < y)
            {
                return -1
            }
            else
            {
                if(x > y)
                {
                    return 1;
                }
                else
                {
                    return 0;
                }
            }
        }*/

        function buildSeriesFromAggregationData(timeRange) {
            var roundedVal, singleSeriesData, singleSample, sampleTime, seriesSingleObj = null;
            chartSeriesObject = [];

            for(var i = 0; i < aggregationGetData.length; i++)
            {
                singleSeriesData = [];

                switch(aggregationGetData[i].metricHighLevelType)
                {
                    case "KPI":
                        utcOption = true;
                        if((aggregationGetData[i].metricType === "Percentuale")||(pattern.test(aggregationGetData[i].metricType)))
                        {
                            for(var j = 0; j < aggregationGetData[i].data.length; j++)
                            {
                                roundedVal = parseFloat(aggregationGetData[i].data[j].value_perc1);
                                roundedVal = Number(roundedVal.toFixed(2));
                                sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime() + 7200000);
                                //  sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime());
                                singleSample = [sampleTime, roundedVal];
                                singleSeriesData.push(singleSample);
                            }
                        }
                        else
                        {
                            switch(aggregationGetData[i].metricType)
                            {
                                case "Intero":
                                    for(var j = 0; j < aggregationGetData[i].data.length; j++)
                                    {
                                        roundedVal = parseInt(aggregationGetData[i].data[j].value_num);
                                        sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime() + 7200000);
                                        //   sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime());
                                        singleSample = [sampleTime, roundedVal];
                                        singleSeriesData.push(singleSample);
                                    }
                                    break;

                                case "Float":
                                    for(var j = 0; j < aggregationGetData[i].data.length; j++)
                                    {
                                        roundedVal = parseFloat(aggregationGetData[i].data[j].value_num);
                                        roundedVal = Number(roundedVal.toFixed(2));
                                        sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime() + 7200000);
                                        //    sampleTime = parseInt(new Date(aggregationGetData[i].data[j].computationDate).getTime());
                                        singleSample = [sampleTime, roundedVal];
                                        singleSeriesData.push(singleSample);
                                    }
                                    break;

                                //I testuali NON li aggiungiamo al grafico
                                default:
                                    break;
                            }
                        }

                        seriesSingleObj = {
                            //showInLegend: true,
                            name: aggregationGetData[i].metricShortDesc,
                            data: singleSeriesData,
                            /*color: styleParameters.barsColors[i],
                            dataLabels: {
                                useHTML: false,
                                enabled: false,
                                inside: true,
                                rotation: dataLabelsRotation,
                                overflow: 'justify',
                                crop: true,
                                align: dataLabelsAlign,
                                verticalAlign: dataLabelsVerticalAlign,
                                y: dataLabelsY,
                                formatter: labelsFormat,
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.dataLabelsFontSize + "px",
                                    color: styleParameters.dataLabelsFontColor,
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)"
                                }
                            }*/
                        };

                        chartSeriesObject.push(seriesSingleObj);
                        break;

                    case "Dynamic":
                        //   utcOption = false;
                        let extractedData = {};
                        if (timeNavCount != 0) {
                            extractedData.values = aggregationGetData[i].data;
                        } else {
                            extractedData.values = rowParameters[i].values;
                        }
                        extractedData.metricType = rowParameters[i].metricType;
                        extractedData.metricId = rowParameters[i].metricId;
                        extractedData.metricName = rowParameters[i].metricName;
                        //     extractedData.measuredTime = rowParameters[i].measuredTime;
                        extractedData.metricValueUnit = rowParameters[i].metricValueUnit;

                        seriesDataArray.push(extractedData);

                        objName = null;
                        /*   if (editLabels != null) {
                               if (editLabels.length > 0) {
                                   objName = editLabels[i];
                               } else {
                                   objName = aggregationGetData[i].metricName;
                               }
                           } else {
                               objName = aggregationGetData[i].metricName;
                           }*/

                        if (aggregationGetData[i].label) {
                            objName = aggregationGetData[i].label;
                        } else {
                            objName = aggregationGetData[i].metricName;
                        }

                        /*     if (rowParameters.length === seriesDataArray.length) {
                                 // DO FINAL SERIALIZATION
                                 serializeAndDisplay(rowParameters, seriesDataArray, editLabels, groupByAttr)
                             }  */

                        let timeSlicedData = [];
                        let millisToSubtract = null;
                        switch (timeRange) {
                            case "4 Ore":
                                millisToSubtract = 4 * 60 * 60 * 1000;
                                break;
                            case "12 Ore":
                                millisToSubtract = 12 * 60 * 60 * 1000;
                                break;
                            case "Giornaliera":
                                millisToSubtract = 24 * 60 * 60 * 1000;
                                break;
                            case "Settimanale":
                                millisToSubtract = 7 * 24 * 60 * 60 * 1000;
                                break;
                            case "Mensile":
                                millisToSubtract = 30 * 24 * 60 * 60 * 1000;
                                break;
                            case "Semestrale":
                                millisToSubtract = 180 * 24 * 60 * 60 * 1000;
                                break;
                            case "Annuale":
                                millisToSubtract = 365 * 24 * 60 * 60 * 1000;
                                break;
                        }
                        let currDate = new Date();
                        let currMillis = currDate.getTime();
                        if (timeNavCount != 0) {
                            if (upperTime != null) {
                                currMillis = new Date(upperTime).getTime();
                            }
                        }

                        if (extractedData.values) {
                            if (xAxisFormat != "numeric") {
                                for (let n = 0; n < extractedData.values.length; n++) {
                                    let timestamp = extractedData.values[n][0];
                                    if (timestamp >= currMillis - millisToSubtract) {
                                        timeSlicedData.push(extractedData.values[n]);
                                    }
                                }
                            } else {
                                timeSlicedData = extractedData.values;
                            }
                        } else {
                            timeSlicedData = [];
                        }

                        if (timeSlicedData.length != 0) {
                            seriesSingleObj = {
                                //showInLegend: true,
                                name: objName,
                                //    data: extractedData.values,
                                data: timeSlicedData,
                                /*color: styleParameters.barsColors[i],
                                dataLabels: {
                                    useHTML: false,
                                    enabled: false,
                                    inside: true,
                                    rotation: dataLabelsRotation,
                                    overflow: 'justify',
                                    crop: true,
                                    align: dataLabelsAlign,
                                    verticalAlign: dataLabelsVerticalAlign,
                                    y: dataLabelsY,
                                    formatter: labelsFormat,
                                    style: {
                                        fontFamily: 'Montserrat',
                                        fontSize: styleParameters.dataLabelsFontSize + "px",
                                        color: styleParameters.dataLabelsFontColor,
                                        fontWeight: 'bold',
                                        fontStyle: 'italic',
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)"
                                    }
                                }*/
                            };
                        }

                        if (extractedData.metricValueUnit != null) {
                            chartSeriesObject.valueUnit = extractedData.metricValueUnit;
                        }

                        chartSeriesObject.push(seriesSingleObj);

                        //    }

                        break;

                    case "MyKPI":
                        utcOption = false;
                        var smPayload = aggregationGetData[i].data;
                        var smField = aggregationGetData[i].smField;
                        smPayload = JSON.parse(smPayload);

                        var resultsArray = smPayload;

                        objName = null;
                        /*    if (editLabels != null) {
                                if (editLabels.length > 0) {
                                    objName = editLabels[i];
                                } else {
                                    objName = aggregationGetData[i].metricName;
                                }
                            } else {
                                objName = aggregationGetData[i].metricName;
                            }*/

                        if (aggregationGetData[i].label) {
                            objName = aggregationGetData[i].label;
                        } else {
                            objName = aggregationGetData[i].metricName + " - " + smField;
                        }

                        for(var j = 0; j < resultsArray.length; j++)
                        {
                            newVal = resultsArray[j].value;
                            addSampleToTrend = true;
                            //    newTime = resultsArray[j].insertTime;
                            newTime = resultsArray[j].dataTime;
                            chartSeriesObject.valueUnit = "";

                            if((newVal.trim() !== '')&&(addSampleToTrend))
                            {
                                roundedVal = parseFloat(newVal);
                                roundedVal = Number(roundedVal.toFixed(2));
                                //sampleTime = parseInt(new Date(newTime).getTime() + 7200000);
                                sampleTime = parseInt(new Date(newTime).getTime());
                                singleSample = [sampleTime, roundedVal];
                                singleSeriesData.push(singleSample);
                            }
                        }

                        if (stackingOption === "normal") {
                            singleSeriesData = truncateStackedSerie(singleSeriesData, timeRange);
                        }

                        seriesSingleObj = {
                            //showInLegend: true,
                            name: objName,
                            data: singleSeriesData,
                            /*color: styleParameters.barsColors[i],
                            dataLabels: {
                                useHTML: false,
                                enabled: false,
                                inside: true,
                                rotation: dataLabelsRotation,
                                overflow: 'justify',
                                crop: true,
                                align: dataLabelsAlign,
                                verticalAlign: dataLabelsVerticalAlign,
                                y: dataLabelsY,
                                formatter: labelsFormat,
                                style: {
                                    fontFamily: 'Montserrat',
                                    fontSize: styleParameters.dataLabelsFontSize + "px",
                                    color: styleParameters.dataLabelsFontColor,
                                    fontWeight: 'bold',
                                    fontStyle: 'italic',
                                    "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)"
                                }
                            }*/
                        };

                        if (aggregationGetData[i].metricValueUnit != null) {
                            chartSeriesObject.valueUnit = aggregationGetData[i].metricValueUnit;
                        }

                        chartSeriesObject.push(seriesSingleObj);

                        break;

                    case "IoT Device Variable":
                    case "Data Table Variable":
                    case "Mobile Device Variable":
                    case "Sensor":
                        utcOption = false;
                        var smPayload = aggregationGetData[i].data;
                        var smField = aggregationGetData[i].smField;
                        smPayload = JSON.parse(smPayload);
                        chartSeriesObject.valueUnit = "";

                        objName = null;
                        /*    if (editLabels != null) {
                                if (editLabels.length > 0) {
                                    objName = editLabels[i];
                                } else {
                                    objName = aggregationGetData[i].metricName;
                                }
                            } else {
                                objName = aggregationGetData[i].metricName;
                            }*/

                        if (aggregationGetData[i].label) {
                            objName = aggregationGetData[i].label;
                        } else {
                            objName = aggregationGetData[i].metricName + " - " + smField;
                        }

                        if(smPayload.hasOwnProperty('trendis'))
                        {
                            var resultsArray = smPayload.predictions;
                            var newVal, newDay, newHour = null;

                            for(var j = 0; j < resultsArray.length; j++)
                            {

                                for(var key in resultsArray[j])
                                {
                                    if(key !== 'datePrediction')
                                    {
                                        newVal = resultsArray[j][key];
                                    }
                                }
                                newTime = resultsArray[j].datePrediction;

                                if(newVal.trim() !== '')
                                {
                                    roundedVal = parseFloat(newVal);
                                    roundedVal = Number(roundedVal.toFixed(2));
                                    //sampleTime = parseInt(new Date(newTime).getTime() + 7200000);
                                    sampleTime = parseInt(new Date(newTime).getTime());
                                    singleSample = [sampleTime, roundedVal];
                                    singleSeriesData.push(singleSample);
                                }
                            }

                            if (stackingOption === "normal") {
                                singleSeriesData = truncateStackedSerie(singleSeriesData, timeRange);
                            }

                            seriesSingleObj = {
                                //showInLegend: true,
                                //    name: aggregationGetData[i].metricName,
                                name: objName,
                                data: singleSeriesData,
                                /*color: styleParameters.barsColors[i],
                                dataLabels: {
                                    useHTML: false,
                                    enabled: false,
                                    inside: true,
                                    rotation: dataLabelsRotation,
                                    overflow: 'justify',
                                    crop: true,
                                    align: dataLabelsAlign,
                                    verticalAlign: dataLabelsVerticalAlign,
                                    y: dataLabelsY,
                                    formatter: labelsFormat,
                                    style: {
                                        fontFamily: 'Montserrat',
                                        fontSize: styleParameters.dataLabelsFontSize + "px",
                                        color: styleParameters.dataLabelsFontColor,
                                        fontWeight: 'bold',
                                        fontStyle: 'italic',
                                        "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)"
                                    }
                                }*/
                            };

                            chartSeriesObject.push(seriesSingleObj);
                        }
                        else
                        {
                            if(smPayload.hasOwnProperty('realtime'))
                            {
                                if(smPayload.realtime.hasOwnProperty('results'))
                                {
                                    var resultsArray = smPayload.realtime.results.bindings;
                                    var newVal, newTime = null;
                                    for(var j = 0; j < resultsArray.length; j++)
                                    {
                                        newVal = resultsArray[j][smField].value;
                                        addSampleToTrend = true;

                                        if(resultsArray[j].hasOwnProperty("updating"))
                                        {
                                            newTime = resultsArray[j].updating.value;
                                        }
                                        else
                                        {
                                            if(resultsArray[j].hasOwnProperty("measuredTime"))
                                            {
                                                newTime = resultsArray[j].measuredTime.value;
                                            }
                                            else
                                            {
                                                if(resultsArray[j].hasOwnProperty("instantTime"))
                                                {
                                                    newTime = resultsArray[j].instantTime.value;
                                                }
                                                else
                                                {
                                                    addSampleToTrend = false;
                                                }
                                            }
                                        }

                                        if((newVal.trim() !== '')&&(addSampleToTrend))
                                        {
                                            roundedVal = parseFloat(newVal);
                                            roundedVal = Number(roundedVal.toFixed(2));
                                            //sampleTime = parseInt(new Date(newTime).getTime() + 7200000);
                                            sampleTime = parseInt(new Date(newTime).getTime());
                                            singleSample = [sampleTime, roundedVal];
                                            singleSeriesData.push(singleSample);
                                        }
                                    }

                                    if (stackingOption === "normal") {
                                        singleSeriesData = truncateStackedSerie(singleSeriesData, timeRange);
                                    }

                                    seriesSingleObj = {
                                        //showInLegend: true,
                                        name: objName,
                                        data: singleSeriesData,
                                        /*color: styleParameters.barsColors[i],
                                        dataLabels: {
                                            useHTML: false,
                                            enabled: false,
                                            inside: true,
                                            rotation: dataLabelsRotation,
                                            overflow: 'justify',
                                            crop: true,
                                            align: dataLabelsAlign,
                                            verticalAlign: dataLabelsVerticalAlign,
                                            y: dataLabelsY,
                                            formatter: labelsFormat,
                                            style: {
                                                fontFamily: 'Montserrat',
                                                fontSize: styleParameters.dataLabelsFontSize + "px",
                                                color: styleParameters.dataLabelsFontColor,
                                                fontWeight: 'bold',
                                                fontStyle: 'italic',
                                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.10)"
                                            }
                                        }*/
                                    };

                                    chartSeriesObject.push(seriesSingleObj);
                                }

                            }
                        }

                        if (smPayload.Service != null) {
                            if (smPayload.Service.features[0].properties.realtimeAttributes[smField] != null) {
                                if (smPayload.Service.features[0].properties.realtimeAttributes[smField].value_unit != null) {
                                    chartSeriesObject.valueUnit = smPayload.Service.features[0].properties.realtimeAttributes[smField].value_unit;
                                }
                            }
                        } else if (smPayload.Sensor != null) {
                            if (smPayload.Sensor.features[0].properties.realtimeAttributes[smField] != null) {
                                if (smPayload.Sensor.features[0].properties.realtimeAttributes[smField].value_unit != null) {
                                    chartSeriesObject.valueUnit = smPayload.Sensor.features[0].properties.realtimeAttributes[smField].value_unit;
                                }
                            }
                        }

                        //console.log(aggregationGetData);
                        break;

                    //Poi si aggiungeranno altri casi
                    default:
                        console.log("Default");
                        break;
                }
            }
            return null;
        }

        function populateWidget(fromAggregate, localTimeRange, timeNavDirection, timeCount, dateInFuture, fromIotApp) {

            if(fromGisExternalContent)
            {
                // Reset Time Navigation
                if (fromGisExternalContentRangePrevious !== fromGisExternalContentRange || fromGisExternalContentFieldPrevious != fromGisExternalContentField || fromGisExternalContentServiceUriPrevious != fromGisExternalContentServiceUri) {
                    timeNavCount = 0;
                    timeCount = 0;
                    fromGisExternalContentRangePrevious = fromGisExternalContentRange;
                    fromGisExternalContentFieldPrevious = fromGisExternalContentField;
                    fromGisExternalContentServiceUriPrevious = fromGisExternalContentServiceUri;
                    dataFut = null;
                    upLimit = null;
                }

                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').hide();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').show();

                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').off('click');
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').click(function(){
                    if($(this).attr('data-onMap') === 'false')
                    {
                        if(fromGisMapRef.hasLayer(fromGisMarker))
                        {
                            fromGisMarker.fire('click');
                        }
                        else
                        {
                            fromGisMapRef.addLayer(fromGisMarker);
                            fromGisMarker.fire('click');
                        }
                        $(this).attr('data-onMap', 'true');
                        $(this).html('near_me');
                        $(this).css('color', 'white');
                        $(this).css('text-shadow', '2px 2px 4px black');
                    }
                    else
                    {
                        fromGisMapRef.removeLayer(fromGisMarker);
                        $(this).attr('data-onMap', 'false');
                        $(this).html('navigation');
                        $(this).css('color', '#337ab7');
                        $(this).css('text-shadow', 'none');
                    }
                });

                switch(fromGisExternalContentRange)
                {
                    case "4/HOUR":
                        localTimeRange = "4 Ore";
                        break;

                    case "1/DAY":
                        localTimeRange = "Giornaliera";
                        break;

                    case "7/DAY":
                        localTimeRange = "Settimanale";
                        break;

                    case "30/DAY":
                        localTimeRange = "Mensile";
                        break;

                    case "180/DAY":
                        localTimeRange = "Semestrale";
                        break;

                    case "365/DAY":
                        localTimeRange = "Annuale";
                        break;

                    default:
                        localTimeRange = "Annuale";
                        break;
                }

                rowParameters[0].metricId = fromGisExternalContentServiceUri;
                rowParameters[0].serviceUri = fromGisExternalContentServiceUri;
                rowParameters[0].smField = fromGisExternalContentField;

            }

            if(fromAggregate) {
                setupLoadingPanel(widgetName, widgetContentColor, firstLoad);

                aggregationGetData = [];
                getDataFinishCount = 0;

                if (rowParameters.length == null) {
                    rowParamLength = 0;
                } else {
                    rowParamLength = rowParameters.length;
                }

                for(let i = 0; i < rowParamLength; i++) {
                    aggregationGetData[i] = false;
                }

                for(let i = 0; i < rowParamLength; i++) {
                    upperTime = getUpperTimeLimit(localTimeRange, timeCount);
                    if (rowParamLength >= 1) {
                        dataOriginV = JSON.stringify(rowParameters[i]);
                    } else {
                        dataOriginV = JSON.stringify(rowParameters);
                    }
                    index = i;
                    $.ajax({
                        url: "../controllers/aggregationSeriesProxy.php",
                        type: "POST",
                        data:
                            {
                                dataOrigin: dataOriginV,
                                index: i,
                                timeRange: localTimeRange,
                                field: rowParameters[i].smField,
                                upperTime: upperTime
                            },
                        async: true,
                        dataType: 'json',
                        success: function(data)
                        {
                            aggregationGetData[data.index] = data;
                            getDataFinishCount++;
                            //var deviceLabels = [];
                            //var metricLabels = [];

                            //Popoliamo il widget quando sono arrivati tutti i dati
                            if(getDataFinishCount === rowParamLength)
                            {
                                widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);
                                legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                                editLabels = styleParameters.editDeviceLabels;
                                buildSeriesFromAggregationData(localTimeRange);

                                /*metricLabels = getMetricLabelsForBarSeries(rowParameters);
                                for (let n = 0; n < chartSeriesObject.length; n++) {
                                    if (chartSeriesObject[n] != null) {
                                        deviceLabels[n] = chartSeriesObject[n].name;
                                    }
                                }
                                series = serializeDataForSeries(metricLabels, deviceLabels);

                                if (styleParameters.xAxisLabel != null) {
                                    xAxisTitle = styleParameters.xAxisLabel;
                                }*/
                                showWidgetContent(widgetName);
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                $("#<?= $_REQUEST['name_w'] ?>_table").show();

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
                                        var stopFlag = 1;
                                    },
                                    error: function (errorData) {
                                        /*  metricData = null;
                                          console.log("Error in updating widgetBarSeries: <?= $_REQUEST['name_w'] ?>");
                                            console.log(JSON.stringify(errorData)); */
                                    }
                                });

                                let drawFlag = false;
                                for (let n = 0; n< chartSeriesObject.length; n++) {
                                    if (chartSeriesObject[n] != null) {
                                        if (chartSeriesObject[n].data.length > 0) {
                                            drawFlag = true;
                                        }
                                    }
                                }
                                if (drawFlag === true) {
                                    drawDiagram(true, xAxisFormat, yAxisType);
                                    if (!fromGisExternalContent) {
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').show();
                                        if (fromIotApp) {
                                            let dynamicTitle = widgetTitle + " - " + rowParameters[0].metricName + " - " + rowParameters[0].smField;
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(dynamicTitle);
                                        } else if (isRestoringFromExternalContent) {
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(widgetTitle);
                                            isRestoringFromExternalContent = false;
                                        }
                                    }
                                } else {
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                    $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                }
                                if (timeNavCount < 0) {
                                    if (moment(upperTime).isBefore(moment(dataFut))) {

                                    } else {
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                    }
                                }
                            }
                        },
                        error: function(errorData)
                        {
                            metricData = null;
                            console.log("Error in data retrieval");
                            console.log(JSON.stringify(errorData));
                            showWidgetContent(widgetName);
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                            $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                        }
                    });
                }
            }
            else {
                $.ajax({
                    url: getMetricDataUrl,
                    type: "GET",
                    data: {"IdMisura": ["<?= escapeForJS($_REQUEST['id_metric']) ?>"]},
                    async: true,
                    dataType: 'json',
                    success: function (data)
                    {
                        metricData = data;
                        $("#" + widgetName + "_loading").css("display", "none");

                        if(metricData.data.length !== 0)
                        {
                            metricType = metricData.data[0].commit.author.metricType;
                            series = JSON.parse(metricData.data[0].commit.author.series);

                            widgetHeight = parseInt($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height() + 25);

                            //Disegno del grafico
                            //chartSeriesObject = getChartSeriesObject(series);
                            /*legendWidth = $("#<?= $_REQUEST['name_w'] ?>_content").width();
                            xAxisCategories = getXAxisCategories(series, widgetHeight);

                            //Non trasposto
                            if(styleParameters.xAxisDataset === series.firstAxis.desc) {
                                xAxisTitle = series.firstAxis.desc;
                            }
                            else//Trasposto
                            {
                                xAxisTitle = series.secondAxis.desc;
                            }*/

                            if(firstLoad !== false) {
                                showWidgetContent(widgetName);
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                $("#<?= $_REQUEST['name_w'] ?>_table").show();
                            }
                            else {
                                elToEmpty.empty();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                $("#<?= $_REQUEST['name_w'] ?>_table").show();
                            }

                            drawDiagram(false, xAxisFormat, yAxisType);
                            if (timeNavCount < 0) {
                                if (moment(upperTime).isBefore(moment(dataFut))) {

                                } else {
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                }
                            }
                        }
                        else
                        {
                            showWidgetContent(widgetName);
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                            $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                        }
                    },
                    error: function()
                    {
                        metricData = null;
                        console.log("Error in data retrieval");
                        console.log(JSON.stringify(errorData));
                        showWidgetContent(widgetName);
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                        $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                    }
                });
            }
        }

        $("#" + widgetName + "_timeTrendPrevBtn").off("click").click(function () {
            //  alert("PREV Clicked!");
            timeNavCount++;
            if(timeNavCount === 0) {

                if (idMetric === 'AggregationSeries' || idMetric.includes("NR_")) {
                    populateWidget(true, timeRange, "minus", timeNavCount);
                } else {
                    populateWidget(false, null, "minus", timeNavCount);
                }

                for (let k = 0; k < rowParameters.length; k++) {
                    if (rowParameters[k].metricHighLevelType === "Sensor" || rowParameters[k].metricHighLevelType == "IoT Device Variable" || rowParameters[k].metricHighLevelType == "Data Table Variable" || rowParameters[k].metricHighLevelType == "Mobile Device Variable") {
                        let urlKBToBeCalled = "";
                        let field = "";
                        let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                        urlKBToBeCalled = "<?=$superServiceMapProxy?>" + "<?=$kbUrlSuperServiceMap?>" + "?serviceUri=" + rowParameters[k].serviceUri;
                        field = rowParameters[k].smField;
                        if (rowParameters != null) {
                            $.ajax({
                                url: urlKBToBeCalled,
                                type: "GET",
                                data: {},
                                async: true,
                                dataType: 'json',
                                success: function (originalData) {
                                    var stopFlag = 1;
                                    var convertedData = convertDataFromTimeNavToDm(originalData, field);
                                    if (convertedData) {
                                        if (convertedData.data.length > 0) {
                                            var localTimeZone = moment.tz.guess();
                                            var momentDateTime = moment();
                                            var localDateTime = momentDateTime.tz(localTimeZone).format();
                                            localDateTime = localDateTime.replace("T", " ");
                                            var plusIndexLocal = localDateTime.indexOf("+");
                                            localDateTime = localDateTime.substr(0, plusIndexLocal);
                                            var localTimeZoneString = "";
                                            if (localDateTime === "") {
                                                localTimeZoneString = "(not recognized) --> Europe/Rome"
                                            } else {
                                                localTimeZoneString = localTimeZone;
                                            }
                                            if (convertedData.data[0].commit.author.futureDate != null && convertedData.data[0].commit.author.futureDate !== undefined) {
                                                dataFut = (convertedData.data[0].commit.author.futureDate);
                                                if (moment(dataFut).isAfter(momentDateTime)) {
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                                } else {
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                                }
                                            } else {
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                            }
                                        } else {
                                            showWidgetContent(widgetName);
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            console.log("Dati non disponibili da Service Map");
                                        }
                                    } else {
                                        showWidgetContent(widgetName);
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Dati non disponibili da Service Map");
                                    }
                                },
                                error: function (data) {
                                    console.log("Errore in chiamata prima API");
                                    console.log(JSON.stringify(data));
                                }
                            });
                        } else {
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                        }
                    }
                }
            } else {
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                setupLoadingPanel(widgetName, widgetContentColor, true);
                if (idMetric === 'AggregationSeries' || idMetric.includes("NR_")) {
                    populateWidget(true, timeRange, "minus", timeNavCount);
                } else {
                    populateWidget(false, null, "minus", timeNavCount);
                }
            }
        });

        $("#" + widgetName + "_timeTrendNextBtn").off("click").click(function () {
            timeNavCount--;
            if(timeNavCount === 0) {

                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                if(idMetric === 'AggregationSeries' || idMetric.includes("NR_")) {
                    populateWidget(true, timeRange, "plus", timeNavCount);
                }
                else
                {
                    populateWidget(false, null, "plus", timeNavCount);
                }

                for (let k = 0; k < rowParameters.length; k++) {
                    if (rowParameters[k].metricHighLevelType === "Sensor" || rowParameters[k].metricHighLevelType == "IoT Device Variable" || rowParameters[k].metricHighLevelType == "Data Table Variable" || rowParameters[k].metricHighLevelType == "Mobile Device Variable") {
                        let urlKBToBeCalled = "";
                        let field = "";
                        let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                        urlKBToBeCalled = "<?=$superServiceMapProxy?>" + "<?=$kbUrlSuperServiceMap?>" + "?serviceUri=" + rowParameters[k].serviceUri;
                        field = rowParameters[k].smField;
                        if (rowParameters != null) {
                            $.ajax({
                                url: urlKBToBeCalled,
                                type: "GET",
                                data: {},
                                async: true,
                                dataType: 'json',
                                success: function (originalData) {
                                    var stopFlag = 1;
                                    var convertedData = convertDataFromTimeNavToDm(originalData, field);
                                    if (convertedData) {
                                        if (convertedData.data.length > 0) {
                                            var localTimeZone = moment.tz.guess();
                                            var momentDateTime = moment();
                                            var localDateTime = momentDateTime.tz(localTimeZone).format();
                                            localDateTime = localDateTime.replace("T", " ");
                                            var plusIndexLocal = localDateTime.indexOf("+");
                                            localDateTime = localDateTime.substr(0, plusIndexLocal);
                                            var localTimeZoneString = "";
                                            if (localDateTime === "") {
                                                localTimeZoneString = "(not recognized) --> Europe/Rome"
                                            } else {
                                                localTimeZoneString = localTimeZone;
                                            }
                                            if (convertedData.data[0].commit.author.futureDate != null && convertedData.data[0].commit.author.futureDate !== undefined) {
                                                dataFut = (convertedData.data[0].commit.author.futureDate);
                                                if (moment(dataFut).isAfter(momentDateTime)) {
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                                } else {
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                                }
                                            } else {
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                            }
                                        } else {
                                            showWidgetContent(widgetName);
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            console.log("Dati non disponibili da Service Map");
                                        }
                                    } else {
                                        showWidgetContent(widgetName);
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Dati non disponibili da Service Map");
                                    }
                                },
                                error: function (data) {
                                    console.log("Errore in chiamata prima API");
                                    console.log(JSON.stringify(data));
                                }
                            });
                        } else {
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                        }
                    }
                }
            } else {

                setupLoadingPanel(widgetName, widgetContentColor, true);
                if (idMetric === 'AggregationSeries' || idMetric.includes("NR_")) {
                    populateWidget(true, timeRange, "plus", timeNavCount);
                } else {
                    populateWidget(false, null, "plus", timeNavCount);
                }
            }
        });

        //Fine definizioni di funzione

        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function(event)
        {
            if((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef);
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null);
            }
        });

        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event)
        {
            showHeader = event.showHeader;
        });

        //Nuova versione
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
                infoJson = widgetData.params.infoJson;
                idMetric =  widgetData.params.id_metric;

                if (nrMetricType != null) {
                    openWs();
                }

                if (infoJson === "fromTracker" && fromGisExternalContent !== true) {
                    $("#" + widgetName + "_timeControlsContainer").hide();
                    $("#" + widgetName + "_titleDiv").css("width", "95%");
                } else {
                    $("#" + widgetName + "_timeControlsContainer").show();
                    $("#" + widgetName + "_titleDiv").css("width", "95%");
                }

                if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no"))) {
                    showHeader = false;
                }
                else {
                    showHeader = true;
                }

                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null)) {
                    metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
                    widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
                    widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
                    widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
                    rowParameters = widgetData.params.rowParameters;
                }
                else {
                    metricName = metricNameFromDriver;
                    widgetTitleFromDriver.replace(/_/g, " ");
                    widgetTitleFromDriver.replace(/\'/g, "&apos;");
                    widgetTitle = widgetTitleFromDriver;
                    $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
                    widgetHeaderColor = widgetHeaderColorFromDriver;
                    widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
                    rowParameters = widgetData.params.rowParameters;
                }

                setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
                $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
                $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

                if(firstLoad === false) {
                    showWidgetContent(widgetName);
                }
                else {
                    setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                }

                if((widgetData.params.styleParameters !== "")&&(widgetData.params.styleParameters !== "null")) {
                    styleParameters = JSON.parse(widgetData.params.styleParameters);
                    //xAxisFormat = styleParameters.xAxisFormat;
                    //yAxisType = styleParameters.yAxisType;
                }

                if(widgetData.params.parameters !== null) {
                    if(widgetData.params.parameters.length > 0) {
                        widgetParameters = JSON.parse(widgetData.params.parameters);
                        thresholdsJson = widgetParameters;
                    }
                }

                if((widgetData.params.infoJson !== 'null')&&(widgetData.params.infoJson !== '')) {
                    infoJson = JSON.parse(widgetData.params.infoJson);
                    infoJson = null;
                }

                //chartType = styleParameters.chartType;
                //lineWidth = styleParameters.lineWidth;

                /*switch(chartType) {
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

                if (timeRange === null || timeRange === undefined) {
                    timeRange = widgetData.params.temporal_range_w;
                }

                if(idMetric === 'AggregationSeries' || idMetric.includes("NR_")) {
                    rowParameters = JSON.parse(rowParameters);
                    timeRange = widgetData.params.temporal_range_w;
                    populateWidget(true, timeRange, null, timeNavCount);
                }
                else {
                    populateWidget(false, null, null, timeNavCount);
                }

                // Hide Next Button at first instantiation
                if(timeNavCount === 0) {
                    if (rowParameters != null) {
                        for (let k = 0; k < rowParameters.length; k++) {
                            if (rowParameters[k].metricHighLevelType === "Sensor" || rowParameters[k].metricHighLevelType == "IoT Device Variable" || rowParameters[k].metricHighLevelType == "Data Table Variable" || rowParameters[k].metricHighLevelType == "Mobile Device Variable") {
                                let urlKBToBeCalled = "";
                                let field = "";
                                let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                                urlKBToBeCalled = "<?=$superServiceMapProxy?>" + "<?=$kbUrlSuperServiceMap?>" + "?serviceUri=" + rowParameters[k].serviceUri;
                                field = rowParameters[k].smField;

                                $.ajax({
                                    url: urlKBToBeCalled,
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    dataType: 'json',
                                    success: function (originalData) {
                                        var stopFlag = 1;
                                        var convertedData = convertDataFromTimeNavToDm(originalData, field);
                                        if (convertedData) {
                                            if (convertedData.data.length > 0) {
                                                var localTimeZone = moment.tz.guess();
                                                var momentDateTime = moment();
                                                var localDateTime = momentDateTime.tz(localTimeZone).format();
                                                localDateTime = localDateTime.replace("T", " ");
                                                var plusIndexLocal = localDateTime.indexOf("+");
                                                localDateTime = localDateTime.substr(0, plusIndexLocal);
                                                var localTimeZoneString = "";
                                                if (localDateTime === "") {
                                                    localTimeZoneString = "(not recognized) --> Europe/Rome"
                                                } else {
                                                    localTimeZoneString = localTimeZone;
                                                }
                                                if (convertedData.data[0].commit.author.futureDate != null && convertedData.data[0].commit.author.futureDate !== undefined) {
                                                    dataFut = (convertedData.data[0].commit.author.futureDate);
                                                    if (moment(dataFut).isAfter(momentDateTime)) {
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                                                    } else {
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                                    }
                                                } else {
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                                }
                                            } else {
                                                showWidgetContent(widgetName);
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                console.log("Dati non disponibili da Service Map");
                                            }
                                        } else {
                                            showWidgetContent(widgetName);
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            console.log("Dati non disponibili da Service Map");
                                        }
                                    },
                                    error: function (data) {
                                        console.log("Errore in chiamata prima API");
                                        console.log(JSON.stringify(data));
                                    }
                                });
                            } else {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                            }
                        }
                    }
                }

                // Modify width to show newly implemented PREV and NEXT buttons
                var titleDiv = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv');
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("color", widgetHeaderFontColor);
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton').css("color", widgetHeaderFontColor);
                titleDiv.css("width", "70%");

                if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 400) {
                    titleDiv.css("width", "65%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "19%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 480) {
                    titleDiv.css("width", "74%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "14%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 560) {
                    titleDiv.css("width", "75%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "15%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 700) {
                    titleDiv.css("width", "80%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "11%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 900) {
                    titleDiv.css("width", "84%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "9%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1000) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "8%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else if ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header').width() < 1050) {
                    titleDiv.css("width", "85%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                } else {
                    titleDiv.css("width", "87%");
                    $("#" + widgetName + "_timeControlsContainer").css("width", "7%");
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_prevButton').css("padding-right", "0px");
                }

            },
            error: function(errorData)
            {
                console.log("Error in widget params retrieval");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                $("#<?= $_REQUEST['name_w'] ?>_table").hide();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
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
                        //    var point = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().series[0].points[0];
                        //    point.update(newValue);

                        rowParameters = newValue;
                        if(idMetric === 'AggregationSeries' || nrMetricType != null)
                        {
                            //    rowParameters = JSON.parse(rowParameters);
                            //    timeRange = widgetData.params.temporal_range_w;
                            populateWidget(true, timeRange, null, timeNavCount, null, true);
                        }
                        else
                        {
                            populateWidget(false, null, null, timeNavCount, null, true);
                        }

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

        $("#<?= $_REQUEST['name_w'] ?>").off('changeTimeRangeEvent');
        $("#<?= $_REQUEST['name_w'] ?>").on('changeTimeRangeEvent', function(event){
            timeRange = event.newTimeRange;
            populateWidget(true, event.newTimeRange, null, 0);
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
        //Fine del codice core del widget
    });
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
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert" class="noDataAlert">
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>
</div> 