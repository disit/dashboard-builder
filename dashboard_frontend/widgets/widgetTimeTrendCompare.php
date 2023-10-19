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

<script type='text/javascript'>
    $(document).ready(function <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromTrackerFlag, fromTrackerDay, fromTrackerParams, fromCsbl)
    {
<?php
$titlePatterns = array();
$titlePatterns[0] = '/_/';
$titlePatterns[1] = '/\'/';
$replacements = array();
$replacements[0] = ' ';
$replacements[1] = '&apos;';
$title = $_REQUEST['title_w'];
$link = mysqli_connect($host, $username, $password);
if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
    eventLog("Returned the following ERROR in widgetTimeTrendCompare.php for the widget " . escapeForHTML($_REQUEST['name_w']) . " is not instantiated or allowed in this dashboard.");
    exit();
}
?>

        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>";
        var widgetContentColor, chartColor, showTitle, hasTimer, showHeader, chartRef, widgetHeaderColor, widgetHeaderFontColor, fontSize, fontColor, timeToReload, widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, pattern, totValues, shownValues,
                descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor,
                rangeMin, rangeMax, widgetParameters, sizeRowsWidget, desc, plotLinesArray, chartAxesColor, value, day, dayParts, timeParts, date, maxValue1, maxValue2, nInterval,
                valueAtt, valuePrec, gridLineColor, alarmSet, sm_based, rowParameters, sm_field, plotLineObj, metricName, widgetTitle, countdownRef, timeRange = null;
        var elToEmpty = $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer");
        var range = "<?= escapeForJS($_REQUEST['time']) ?>";
        var seriesData1 = [];
        var valuesData1 = [];
        var seriesData2 = [];
        var valuesData2 = [];
        var timeNavCount = 0;
        var udmFromUserOptions = null;
        var upperTimeLimitISOTrimmed = null;
        var udm = null;
        var now = new Date();
        var nowUTC = now.toUTCString();
        var isoDate = new Date(nowUTC).toISOString();
        var dayTracker = fromTrackerDay;
        var flagTracker = fromTrackerFlag;
        var dataFut = null;
        var compareval = 0;
        var myKPITimeRangeCompare;
        var upLimit = null;
        var singleOriginalData, singleData, convertedDate = null;
        var convertedData = {
            data: []
        };
        var originalDataWithNoTime = 0;
        var originalDataNotNumeric = 0;
        var timeRangeCompare = null;
        var upperTimeLimitCompareISOTrim = 0;
        var convertedData2 = null;
        var embedWidget = <?= $_REQUEST['embedWidget'] == 'true' ? 'true' : 'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var headerHeight = 25;
        var fromGisExternalContentRangePrevious = null;
        var fromGisExternalContentFieldPrevious = null;
        var fromGisExternalContentServiceUriPrevious = null;
        console.log("Widget Time Trend Compare: " + widgetName);
        var unitsWidget = [['millisecond', // unit name
                [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] // allowed multiples
            ], [
                'second',
                [1, 2, 5, 10, 15, 30]
            ], [
                'minute',
                [1, 2, 5, 10, 15, 30]
            ], [
                'hour',
                [1, 2, 3, 4, 6, 8, 12]
            ], [
                'day',
                [1]
            ], [
                'week',
                [1]
            ], [
                'month',
                [1, 3, 4, 6, 8, 10, 12]
            ], [
                'year',
                null
            ]];

        $(document).off('showTimeTrendCompareFromExternalContent_' + widgetName);
        $(document).on('showTimeTrendCompareFromExternalContent_' + widgetName, function(event)
        {
            if(event.targetWidget === widgetName)
            {

                clearInterval(countdownRef);
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();

                rowParameters = event.passedData[0].serviceUri;
                sm_field = event.passedData[0].smField;

                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, event.passedData[0].metricName, (event.passedData[0].title != null ? event.passedData[0].title : widgetTitle), null, (event.passedData[0].headerColor != null ? event.passedData[0].headerColor : widgetHeaderFontColor), true, rowParameters, sm_field, event.passedData[0].timeRange, null, null, false, null, null, true);

                //populateWidget(event.passedData[0].timeRange, null, null, 0, null, udmFromUserOptions);

            }
        });

        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function (event)
        {
            if ((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef);
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, /*null,*/ null, null);
            }
        });
        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function (event)
        {
            showHeader = event.showHeader;
            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().reflow();
        });
        $("#" + widgetName).hover(function ()
        {
            $.ajax({
                url: "../controllers/getWidgetParams.php",
                type: "GET",
                data: {
                    widgetName: "<?= $_REQUEST['name_w'] ?>"
                },
                async: true,
                dataType: 'json',
                success: function (widgetData) {
                    var widgetNameD = widgetData.params.name_w;
                    var showTitleD = widgetData.params.showTitle;
                    var widgetContentColorD = widgetData.params.color_w;
                    var fontSizeD = widgetData.params.fontSize;
                    var fontColorD = widgetData.params.fontColor;
                    var timeToReloadD = widgetData.params.frequency_w;
                    var hasTimerD = widgetData.params.hasTimer;
                    var chartColorD = widgetData.params.chartColor;
                    var dataLabelsFontSizeD = widgetData.params.dataLabelsFontSize;
                    var dataLabelsFontColorD = widgetData.params.dataLabelsFontColor;
                    var chartLabelsFontSizeD = widgetData.params.chartLabelsFontSize;
                    var chartLabelsFontColorD = widgetData.params.chartLabelsFontColor;
                    var appIdD = widgetData.params.appId;
                    var flowIdD = widgetData.params.flowId;
                    var nrMetricTypeD = widgetData.params.nrMetricType;
                    var webLinkD = widgetData.params.link_w;
                    if (location.href.includes("index.php") && webLinkD != "" && webLinkD != "none" && webLinkD != null) {
                        $("#" + widgetName).css("cursor", "pointer");
                    }

                },
                error: function ()
                {

                }
            });
        });
        $("#" + widgetName).click(function ()
        {
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
                    var styleParametersString = widgetData.params.styleParameters;
                    styleParameters = jQuery.parseJSON(styleParametersString);
                    webLink = widgetData.params.link_w;
                    if (location.href.includes("index.php") && webLink != "" && webLink != "none") {
                        if (styleParameters != null) {
                            if (styleParameters['openNewTab'] === "yes") {
                                var newTab = window.open(webLink);
                                if (newTab) {
                                    newTab.focus();
                                } else {
                                    alert('Please allow popups for this website');
                                }
                            } else {
                                window.location.href = webLink;
                            }
                        } else {
                            var newTab = window.open(webLink);
                            if (newTab) {
                                newTab.focus();
                            } else {
                                alert('Please allow popups for this website');
                            }
                        }
                    }

                },
                error: function ()
                {
                    console.log("Error in opening web link.");
                }
            });
        });
        //Definizioni di funzione specifiche del widget
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            var bodyHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - widgetHeaderHeight);
            $("#" + widgetName + "_loading").css("height", bodyHeight + "px");
            $("#" + widgetName + "_content").css("height", bodyHeight + "px");
            if (infoJson != "fromTracker" || fromGisExternalContent === true) {
                var titleDiv = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv');
                //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv').css("width", "3.5%");
                //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_countdownContainerDiv').css("width", "3%");
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
        
        function getUpperTimeLimit(hours) {
            let now = new Date();
            let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
            let upperTimeLimit = now.setHours(now.getHours() - hours - timeZoneOffsetHours);
            let upperTimeLimitUTC = new Date(upperTimeLimit).toUTCString();
            let upperTimeLimitISO = new Date(upperTimeLimitUTC).toISOString();
            let upperTimeLimitISOTrim = upperTimeLimitISO.substring(0, isoDate.length - 5);
            return upperTimeLimitISOTrim;
            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
        }

        function getUpperTimeLimitCompare(hours) {
            let now = new Date();
            let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
            let upperTimeLimitCompare = now.setHours(now.getHours() - hours - timeZoneOffsetHours);
            let upperTimeLimitCompareUTC = new Date(upperTimeLimitCompare).toUTCString();
            let upperTimeLimitCompareISO = new Date(upperTimeLimitCompareUTC).toISOString();
            let upperTimeLimitCompareISOTrim = upperTimeLimitCompareISO.substring(0, isoDate.length - 5);
            return upperTimeLimitCompareISOTrim;
            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
        }
        
        function getUpperTimeLimitMCompare(months) {
            let now = new Date();
            let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
            let upperTimeLimitCompare = now.setMonth(now.getMonth() - months);
            upperTimeLimitCompare = now.setHours(now.getHours() - timeZoneOffsetHours);
            let upperTimeLimitCompareUTC = new Date(upperTimeLimitCompare).toUTCString();
            let upperTimeLimitCompareISO = new Date(upperTimeLimitCompareUTC).toISOString();
            let upperTimeLimitCompareISOTrim = upperTimeLimitCompareISO.substring(0, isoDate.length - 5);
            return upperTimeLimitCompareISOTrim;
            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
        }
        
        function getUpperTimeLimitCompareSetToMonday(hours) {
            let now = new Date();
            let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
            let upperTimeLimitCompare = now.setHours(now.getHours() - hours - timeZoneOffsetHours);
            let upperTimeLimitCompareDate = new Date(upperTimeLimitCompare)
            var day = upperTimeLimitCompareDate.getDay() || 7;
            if (day !== 1)
                upperTimeLimitCompareDate.setHours(-24 * (day - 1));
            let upperTimeLimitCompareISO = new Date(upperTimeLimitCompareDate).toISOString();
            let upperTimeLimitCompareISOTrim = upperTimeLimitCompareISO.substring(0, isoDate.length - 5);
            return upperTimeLimitCompareISOTrim;

        }

        function getUpperTimeLimitCompareSetTo1Day(hours) {
            let now = new Date();
            let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
            let upperTimeLimitCompare = now.setHours(now.getHours() - hours - timeZoneOffsetHours);
            upperTimeLimitCompare = new Date(upperTimeLimitCompare);
            let upperTimeLimitCompareDate = new Date(upperTimeLimitCompare.getFullYear(), upperTimeLimitCompare.getMonth(), 1)
            let upperTimeLimitCompareISO = new Date(upperTimeLimitCompareDate).toISOString();
            let upperTimeLimitCompareISOTrim = upperTimeLimitCompareISO.substring(0, isoDate.length - 5);
            return upperTimeLimitCompareISOTrim;

        }
        function getUpperTimeLimitCompareSetTo1Day1Month(hours) {
            let maxmonth = 0;
            let now = new Date();
            var upperTimeLimitCompareDate;
            let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
            let upperTimeLimitCompare = now.setHours(now.getHours() - hours - timeZoneOffsetHours);
            upperTimeLimitCompare = new Date(upperTimeLimitCompare);
            if (upperTimeLimitCompare.getMonth() >= 0 && upperTimeLimitCompare.getMonth() <= 5) {
                maxmonth = 6;
                upperTimeLimitCompareDate = new Date(upperTimeLimitCompare.getFullYear(), 6, 1);
            } else {
                maxmonth = 0;
                upperTimeLimitCompareDate = new Date(upperTimeLimitCompare.getFullYear() + 1, 0, 1);
            }
            //let upperTimeLimitCompareDate = new Date(upperTimeLimitCompare.getFullYear(), maxmonth , 1);
            let upperTimeLimitCompareISO = new Date(upperTimeLimitCompareDate).toISOString();
            let upperTimeLimitCompareISOTrim = upperTimeLimitCompareISO.substring(0, isoDate.length - 5);
            return upperTimeLimitCompareISOTrim;

        }

        function getUpperTimeLimitCompareSetToLastDayYear(hours) {
            let maxmonth = 0;
            let now = new Date();
            var upperTimeLimitCompareDate;
            let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
            let upperTimeLimitCompare = now.setHours(now.getHours() - hours - timeZoneOffsetHours);
            upperTimeLimitCompare = new Date(upperTimeLimitCompare);
            upperTimeLimitCompareDate = new Date(upperTimeLimitCompare.getFullYear() + 1, 0, 1);
            let upperTimeLimitCompareISO = new Date(upperTimeLimitCompareDate).toISOString();
            let upperTimeLimitCompareISOTrim = upperTimeLimitCompareISO.substring(0, isoDate.length - 5);
            return upperTimeLimitCompareISOTrim;

        }


        function ComparePeriodCalc(timeRangeCompare, timeCount) {
            switch (timeRangeCompare)
            {
                // 4 hours
                case "4 previous hours":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare(4 + (4 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(4, 'hours');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "4 hours day before":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare(24 + (4 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(4, 'hours');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                    // 12 hours
                case "12 previous hours":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare(12 + (12 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(12, 'hours');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "12 hours day before":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare(24 + (12 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(12, 'hours');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                    // day
                case "previous day":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare(24 + (24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(24, 'hours');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "same day previous week":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare((7 * 24) + (24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(24, 'hours');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "same day previous month":
                    //upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare((30 * 24) + (30 * 24 * timeCount));
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitMCompare(1 + timeCount);
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(24, 'hours');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                    // week 
                case "previous week":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare((7 * 24) + (7 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(7, 'days');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "previous week starting Monday":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompareSetToMonday((0 * 24) + (7 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(7, 'days');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                    // month
                case "previous month":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitMCompare(1 + timeCount);
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(1, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "previous month - day 1":
                    // ultimo giorno del mese prima
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompareSetTo1Day((0 * 24) + (30 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(1, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "same month prev year - day 1":
                    // ultimo giorno del mese prima
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompareSetTo1Day((335 * 24) + (30 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(1, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                    // 6 month
                case "previous 6 months":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitMCompare(6 + (6 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(6, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "previous 6 months - day 1 of prev 6 months":
                    // ultimo giorno del mese prima
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompareSetTo1Day((150 * 24) + (180 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(6, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "same 6 months of prev year - day 1":
                    // ultimo giorno del mese prima
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompareSetTo1Day((335 * 24) + (180 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(6, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "same 6 months of prev year - day 1 month 1 or 7":
                    // ultimo giorno del mese prima
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompareSetTo1Day1Month((365 * 24) + (180 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(6, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                    // year
                case "previous year":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare((365 * 24) + (365 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(12, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "previous year - day 1 of prev year":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompareSetTo1Day((335 * 24) + (365 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(12, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "previous year - month 1":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompareSetToLastDayYear((365 * 24) + (365 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(12, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "previous 2 years":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare((2 * 365 * 24) + (2 * 365 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(24, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                case "previous 10 years":
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare((10 * 365 * 24) + (10 * 365 * 24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(120, 'months');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
                default:
                    serviceMapTimeRange = "fromTime=1-day";
                    upperTimeLimitCompareISOTrim = getUpperTimeLimitCompare(24 + (24 * timeCount));
                    if (flagTracker === true) {
                        myKPITimeRangeCompare = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                    } else {
                        var myKPIFromTimeRangeNew = moment(upperTimeLimitCompareISOTrim).subtract(1, 'days');
                        var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                        myKPITimeRangeCompare = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitCompareISOTrim;
                    }
                    break;
            }

        }

        function convertDataCompareFromMyKpiToDm(originalData, compareval, timeCount, notFirstInst)
        {
            if ((timeCount != 0 && compareval == 0) || (notFirstInst === '1' && compareval == 0)) {
                singleOriginalData, singleData, convertedDate = null;
                convertedData = {
                    data: []
                };
            }


            for (var i = 0; i < originalData.length; i++)
            {
                singleData = {
                    commit: {
                        author: {
                            IdMetric_data: null, //Si può lasciare null, non viene usato dal widget
                            computationDate: null,
                            value_perc1: null, //Non lo useremo mai
                            value: null,
                            descrip: null, //Mettici il nome della metrica splittato
                            threshold: null, //Si può lasciare null, non viene usato dal widget
                            thresholdEval: null //Si può lasciare null, non viene usato dal widget
                        },
                        range_dates: compareval//Si può lasciare null, non viene usato dal widget
                    }
                };

                singleOriginalData = originalData[i];

                convertedDate = new Date(singleOriginalData.dataTime); //2001-11-23 03:08:46
                convertedDate = convertedDate.getFullYear() + "-" + parseInt(convertedDate.getMonth() + 1) + "-" + convertedDate.getDate() + " " + convertedDate.getHours() + ":" + convertedDate.getMinutes() + ":" + convertedDate.getSeconds();

                singleData.commit.author.computationDate = convertedDate;

                if (!isNaN(parseFloat(singleOriginalData.value)))
                {
                    singleData.commit.author.value = parseFloat(singleOriginalData.value);
                } else
                {
                    singleData.commit.author.value = singleOriginalData.value;
                }
                if (singleData.commit.author.value !== undefined) {

                    convertedData.data.push(singleData);
                }
            }

            return convertedData;
        }

        function convertFromMomentToTime(momentDate) {
            var momentDateTime = momentDate.format();
            //  momentDateTime = momentDateTime.replace("T", " ");
            var plusIndexLocal = momentDateTime.indexOf("+");
            momentDateTime = momentDateTime.substr(0, plusIndexLocal);
            var convertedDateTime = momentDateTime;
            return convertedDateTime;
        }

        function convertDataFromTimeNavToDm(originalData, field, udmFromUserOptions)
        {
            var singleOriginalData, singleData, convertedDate, futureDate = null;
            var convertedData = {
                data: []
            };
            var originalDataWithNoTime = 0;
            var originalDataNotNumeric = 0;
            if (originalData.hasOwnProperty("realtime"))
            {
                if (originalData.realtime.hasOwnProperty("results"))
                {
                    if (originalData.realtime.results.hasOwnProperty("bindings"))
                    {
                        if (originalData.realtime.results.bindings.length > 0)
                        {
                            let propertyJson = "";
                            if (originalData.hasOwnProperty("BusStop"))
                            {
                                propertyJson = originalData.BusStop;
                            } else
                            {
                                if (originalData.hasOwnProperty("Sensor"))
                                {
                                    propertyJson = originalData.Sensor;
                                } else
                                {
                                    if (originalData.hasOwnProperty("Service"))
                                    {
                                        propertyJson = originalData.Service;
                                    } else
                                    {
                                        propertyJson = originalData.Services;
                                    }
                                }
                            }
                            if (udmFromUserOptions != null) {
                                udm = udmFromUserOptions;
                            } else {
                                if (propertyJson.features[0].properties.realtimeAttributes[field].value_unit != null) {
                                    udm = propertyJson.features[0].properties.realtimeAttributes[field].value_unit;
                                }
                            }
                            for (var i = 0; i < originalData.realtime.results.bindings.length; i++)
                            {
                                singleData = {
                                    commit: {
                                        author: {
                                            IdMetric_data: null, //Si può lasciare null, non viene usato dal widget
                                            computationDate: null,
                                            futureDate: null,
                                            value_perc1: null, //Non lo useremo mai
                                            value: null,
                                            descrip: null, //Mettici il nome della metrica splittato
                                            threshold: null, //Si può lasciare null, non viene usato dal widget
                                            thresholdEval: null //Si può lasciare null, non viene usato dal widget
                                        },
                                        range_dates: 0//Si può lasciare null, non viene usato dal widget
                                    }
                                };
                                singleOriginalData = originalData.realtime.results.bindings[i];
                                if (singleOriginalData.hasOwnProperty("updating"))
                                {
                                    convertedDate = singleOriginalData.updating.value;
                                } else
                                {
                                    if (singleOriginalData.hasOwnProperty("measuredTime"))
                                    {
                                        convertedDate = singleOriginalData.measuredTime.value;
                                    } else
                                    {
                                        if (singleOriginalData.hasOwnProperty("instantTime"))
                                        {
                                            convertedDate = singleOriginalData.instantTime.value;
                                        } else
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

                                if (singleOriginalData[field] !== undefined) {
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
                        } else
                        {
                            return false;
                        }
                    } else
                    {
                        return false;
                    }
                } else
                {
                    return false;
                }
            } else
            {
                return false;
            }
        }

        function convertDataFromSmToDm(originalData, field, udmFromUserOptions, compareval, timeCount, notFirstInst)
        {
            if ((timeCount != 0 && compareval == 0) || (notFirstInst === '1' && compareval == 0)) {
                singleOriginalData, singleData, convertedDate = null;
                convertedData = {
                    data: []
                };

                originalDataWithNoTime = 0;
                originalDataNotNumeric = 0;

            }


            if (originalData.hasOwnProperty("realtime"))
            {
                if (originalData.realtime.hasOwnProperty("results"))
                {
                    if (originalData.realtime.results.hasOwnProperty("bindings"))
                    {
                        if (originalData.realtime.results.bindings.length > 0)
                        {
                            let propertyJson = "";
                            if (originalData.hasOwnProperty("BusStop"))
                            {
                                propertyJson = originalData.BusStop;
                            } else
                            {
                                if (originalData.hasOwnProperty("Sensor"))
                                {
                                    propertyJson = originalData.Sensor;
                                } else
                                {
                                    if (originalData.hasOwnProperty("Service"))
                                    {
                                        propertyJson = originalData.Service;
                                    } else
                                    {
                                        propertyJson = originalData.Services;
                                    }
                                }
                            }
                            if (udmFromUserOptions != null) {
                                udm = udmFromUserOptions;
                            } else {
                                if (propertyJson.features[0].properties.realtimeAttributes[field].value_unit != null) {
                                    udm = propertyJson.features[0].properties.realtimeAttributes[field].value_unit;
                                }
                            }
                            for (var i = 0; i < originalData.realtime.results.bindings.length; i++)
                            {
                                singleData = {
                                    commit: {
                                        author: {
                                            IdMetric_data: null, //Si può lasciare null, non viene usato dal widget
                                            computationDate: null,
                                            value_perc1: null, //Non lo useremo mai
                                            value: null,
                                            descrip: null, //Mettici il nome della metrica splittato
                                            threshold: null, //Si può lasciare null, non viene usato dal widget
                                            thresholdEval: null //Si può lasciare null, non viene usato dal widget
                                        },
                                        range_dates: compareval

                                    }
                                };
                                singleOriginalData = originalData.realtime.results.bindings[i];
                                if (singleOriginalData.hasOwnProperty("updating"))
                                {
                                    convertedDate = singleOriginalData.updating.value;
                                } else
                                {
                                    if (singleOriginalData.hasOwnProperty("measuredTime"))
                                    {
                                        convertedDate = singleOriginalData.measuredTime.value;
                                    } else
                                    {
                                        if (singleOriginalData.hasOwnProperty("instantTime"))
                                        {
                                            convertedDate = singleOriginalData.instantTime.value;
                                        } else
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
                                if (localDateTime == "") {
                                    singleData.commit.author.computationDate = convertedDate;
                                } else {
                                    singleData.commit.author.computationDate = localDateTime;
                                }

                                if (singleOriginalData[field] !== undefined) {
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

                            return convertedData;
                        } else
                        {
                            return false;
                        }
                    } else
                    {
                        return false;
                    }
                } else
                {
                    if (timeRange != "10 Anni") {
                        return false;
                    } else {
                        return convertedData;
                    }
                }
            } else
            {
                return false;
            }

        }

        function populateWidget(localTimeRange, kpiTracker, timeNavDirection, timeCount, dateInFuture, udmFromUserOptions, notFirstInst, showContentOnLoad, fromCsbl)
        {
            if ((showContentOnLoad != null && showContentOnLoad == "no") && !fromCsbl) {
                return;
            }
            if (fromGisExternalContent)
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
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').click(function () {
                    if ($(this).attr('data-onMap') === 'false')
                    {
                        if (fromGisMapRef.hasLayer(fromGisMarker))
                        {
                            fromGisMarker.fire('click');
                        } else
                        {
                            fromGisMapRef.addLayer(fromGisMarker);
                            fromGisMarker.fire('click');
                        }
                        $(this).attr('data-onMap', 'true');
                        $(this).html('near_me');
                        $(this).css('color', 'white');
                        $(this).css('text-shadow', '2px 2px 4px black');
                    } else
                    {
                        fromGisMapRef.removeLayer(fromGisMarker);
                        $(this).attr('data-onMap', 'false');
                        $(this).html('navigation');
                        $(this).css('color', '#337ab7');
                        $(this).css('text-shadow', 'none');
                    }
                });
                switch (fromGisExternalContentRange)
                {
                    case "4/HOUR":
                        serviceMapTimeRange = "fromTime=4-hour";
                        //    var deltaT = 4 + parseInt(timeCount) * 4;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-hour";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(4 * timeCount);
                        break;
                    case "1/DAY":
                        serviceMapTimeRange = "fromTime=1-day";
                        //    var deltaT = 1 + parseInt(timeCount);
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(24 * timeCount);
                        break;
                    case "7/DAY":
                        serviceMapTimeRange = "fromTime=7-day";
                        //    var deltaT = 7 + parseInt(timeCount) * 7;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(7 * 24 * timeCount);
                        break;
                    case "30/DAY":
                        serviceMapTimeRange = "fromTime=30-day";
                        //    var deltaT = 30 + parseInt(timeCount) * 30;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(30 * 24 * timeCount);
                        break;
                    default:
                        serviceMapTimeRange = "fromTime=1-day";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(24 * timeCount);
                        break;
                }

                $.ajax({
                    url: "<?= $superServiceMapProxy ?>api/v1/?serviceUri=" + encodeURI(fromGisExternalContentServiceUri) + "&" + serviceMapTimeRange + "&toTime=" + upperTimeLimitISOTrimmed + "&valueName=" + fromGisExternalContentField,
                    type: "GET",
                    data: {},
                    async: true,
                    dataType: 'json',
                    success: function (originalData)
                    {
                        compareval = 0;
                        var convertedData = convertDataFromSmToDm(originalData, fromGisExternalContentField, udm, compareval, timeCount);
                        if (convertedData)
                        {
                            if (convertedData.data.length > 0)
                            {
                                var localTimeZone = moment.tz.guess();
                                var momentDateTime = moment();
                                var localDateTime = momentDateTime.tz(localTimeZone).format();
                                localDateTime = localDateTime.replace("T", " ");
                                var plusIndexLocal = localDateTime.indexOf("+");
                                localDateTime = localDateTime.substr(0, plusIndexLocal);
                                var localTimeZoneString = "";
                                if (localDateTime == "") {
                                    localTimeZoneString = "(not recognized) --> Europe/Rome"
                                } else {
                                    localTimeZoneString = localTimeZone;
                                }
                                ComparePeriodCalc(timeRangeCompare, timeCount);

                                $.ajax({
                                    //    url: rowParameters + "&" + serviceMapTimeRange + "&valueName=" + sm_field,
                                    url: "<?= $superServiceMapProxy ?>api/v1/?serviceUri=" + encodeURI(fromGisExternalContentServiceUri) + "&" + serviceMapTimeRange + "&toTime=" + upperTimeLimitCompareISOTrim + "&valueName=" + fromGisExternalContentField,
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    dataType: 'json',
                                    success: function (originalData)
                                    {
                                        compareval = 1;
                                        convertedData = convertDataFromSmToDm(originalData, fromGisExternalContentField, udm, compareval, timeCount, notFirstInst);
                                        if (convertedData)
                                        {
                                            if (convertedData.data.length > 0)
                                            {
                                                var localTimeZone = moment.tz.guess();
                                                var momentDateTime = moment();
                                                var localDateTime = momentDateTime.tz(localTimeZone).format();
                                                localDateTime = localDateTime.replace("T", " ");
                                                var plusIndexLocal = localDateTime.indexOf("+");
                                                localDateTime = localDateTime.substr(0, plusIndexLocal);
                                                var localTimeZoneString = "";
                                                if (localDateTime == "") {
                                                    localTimeZoneString = "(not recognized) --> Europe/Rome"
                                                } else {
                                                    localTimeZoneString = localTimeZone;
                                                }
                                                //convertedData = convertedData + convertedData2;
                                                drawDiagram(convertedData, fromGisExternalContentRange, fromGisExternalContentField, true, localTimeZoneString, udm);
                                                upLimit = convertedData.data[0].commit.author.computationDate;
                                                if (timeNavCount < 0) {
                                                    if (moment(upLimit).isBefore(moment(dateInFuture))) {

                                                    } else {
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                                    }
                                                }

                                            } else
                                            {
                                                showWidgetContent(widgetName);
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                console.log("Dati non disponibili da Service Map");
                                            }
                                        } else
                                        {
                                            showWidgetContent(widgetName);
                                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            console.log("Dati non disponibili da Service Map");
                                        }
                                    },
                                    error: function (data)
                                    {
                                        showWidgetContent(widgetName);
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Errore in scaricamento dati da Service Map");
                                        console.log(JSON.stringify(data));
                                    }
                                });

                            } else
                            {
                                showWidgetContent(widgetName);
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                console.log("Dati non disponibili da Service Map");
                            }
                        } else
                        {
                            showWidgetContent(widgetName);
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                            console.log("Dati non disponibili da Service Map");
                        }
                    },
                    error: function (data)
                    {
                        showWidgetContent(widgetName);
                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                        console.log("Errore in scaricamento dati da Service Map");
                        console.log(JSON.stringify(data));
                    }
                });

            } else
            {
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').hide();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').show();
                switch (localTimeRange)
                {
                    case "4 Ore":
                        serviceMapTimeRange = "fromTime=4-hour";
                        //    var deltaT = 4 + parseInt(timeCount) * 4;
                        //    serviceMapTimeRange = "fromTime=" + deltaT.toString() + "-day";
                        globalDiagramRange = "4/HOUR";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(4 * timeCount);
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 4);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //   myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(4, 'hours');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                    case "12 Ore":
                        serviceMapTimeRange = "fromTime=12-hour";
                        //    var deltaT = 12 + parseInt(timeCount) * 12;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-hour";
                        globalDiagramRange = "12/HOUR";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(12 * timeCount);
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 12);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //   myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(12, 'hours');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                    case "Giornaliera":
                        serviceMapTimeRange = "fromTime=1-day";
                        //    var deltaT = 1 + parseInt(timeCount);
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";
                        globalDiagramRange = "1/DAY";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(24 * timeCount);
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 24);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(24, 'hours');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                    case "Settimanale":
                        serviceMapTimeRange = "fromTime=7-day";
                        //    var deltaT = 7 + parseInt(timeCount) * 7;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";
                        globalDiagramRange = "7/DAY";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(7 * 24 * timeCount);
                        if (timeRangeCompare === 'previous week starting Monday')
                        {
                            // domenica della settimana prima
                            upperTimeLimitISOTrimmed = getUpperTimeLimitCompareSetToMonday((-7 * 24) + (7 * 24 * timeCount));
                        }
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 168);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(7, 'days');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                    case "Mensile":
                        serviceMapTimeRange = "fromTime=30-day";
                        //    var deltaT = 30 + parseInt(timeCount) * 30;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";
                        globalDiagramRange = "30/DAY";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(30 * 24 * timeCount);
                        if (timeRangeCompare === 'previous month - day 1')
                        {
                            // ultimo giorno del mese prima
                            upperTimeLimitISOTrimmed = getUpperTimeLimitCompareSetTo1Day((-31 * 24) + (30 * 24 * timeCount));
                        }
                        if (timeRangeCompare === 'same month prev year - day 1')
                        {
                            // ultimo giorno del mese prima 
                            upperTimeLimitISOTrimmed = getUpperTimeLimitCompareSetTo1Day(((-31) * 24) + (30 * 24 * timeCount));
                        }
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 720);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //   myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(1, 'month');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                    case "Semestrale":
                        serviceMapTimeRange = "fromTime=180-day";
                        //    var deltaT = 365 + parseInt(timeCount) * 365;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";
                        globalDiagramRange = "180/DAY";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(180 * 24 * timeCount);
                        if (timeRangeCompare === 'previous 6 months - day 1 of prev 6 months')
                        {
                            // ultimo giorno del mese prima 
                            upperTimeLimitISOTrimmed = getUpperTimeLimitCompareSetTo1Day((-31 * 24) + (180 * 24 * timeCount));
                        }
                        if (timeRangeCompare === 'same 6 months of prev year - day 1')
                        {
                            // ultimo giorno del mese prima 
                            upperTimeLimitISOTrimmed = getUpperTimeLimitCompareSetTo1Day((-31 * 24) + (180 * 24 * timeCount));
                        }
                        if (timeRangeCompare === 'same 6 months of prev year - day 1 month 1 or 7')
                        {
                            upperTimeLimitISOTrimmed = getUpperTimeLimitCompareSetTo1Day1Month((0 * 24) + (180 * 24 * timeCount));
                        }
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 4320);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(6, 'month');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                    case "Annuale":
                        serviceMapTimeRange = "fromTime=365-day";
                        //    var deltaT = 365 + parseInt(timeCount) * 365;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";
                        globalDiagramRange = "365/DAY";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(365 * 24 * timeCount);
                        if (timeRangeCompare === 'previous year - day 1 of prev year')
                        {
                            // ultimo giorno del mese prima 
                            upperTimeLimitISOTrimmed = getUpperTimeLimitCompareSetTo1Day((-31 * 24) + (365 * 24 * timeCount));
                        }
                        if (timeRangeCompare === 'previous year - month 1')
                        {
                            upperTimeLimitISOTrimmed = getUpperTimeLimitCompareSetToLastDayYear((0 * 24) + (365 * 24 * timeCount));
                        }
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 8760);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(1, 'year');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                    case "2 Anni":
                        serviceMapTimeRange = "fromTime=730-day";
                        //    var deltaT = 365 + parseInt(timeCount) * 365;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";
                        globalDiagramRange = "730/DAY";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(2 * 365 * 24 * timeCount);
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 17520);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(2, 'year');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                    case "10 Anni":
                        serviceMapTimeRange = "fromTime=3650-day";
                        //    var deltaT = 365 + parseInt(timeCount) * 365;
                        //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";
                        globalDiagramRange = "3650/DAY";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(10 * 365 * 24 * timeCount);
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 87600);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(10, 'year');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                    default:
                        serviceMapTimeRange = "fromTime=1-day";
                        globalDiagramRange = "1/DAY";
                        upperTimeLimitISOTrimmed = getUpperTimeLimit(24 * timeCount);
                        if (flagTracker === true) {
                            myKPITimeRange = "&from=" + dayTracker + "T00:00:00&to=" + dayTracker + "T23:59:59";
                        } else {
                            var now = new Date();
                            myKPIFromTimeRange = now.setHours(now.getHours() - 24);
                            var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);
                            //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
                            var myKPIFromTimeRangeNew = moment(upperTimeLimitISOTrimmed).subtract(24, 'hours');
                            var myKPIFromTimeRangeNewTrimmed = convertFromMomentToTime(myKPIFromTimeRangeNew);
                            myKPITimeRange = "&from=" + myKPIFromTimeRangeNewTrimmed + "&to=" + upperTimeLimitISOTrimmed;
                        }
                        break;
                }
                switch (sm_based)
                {
                    case 'yes':
                        var serviceMapTimeRangeCompare = serviceMapTimeRange;
                        $.ajax({
                            //    url: rowParameters + "&" + serviceMapTimeRange + "&valueName=" + sm_field,
                            url: "<?= $superServiceMapProxy ?>" + encodeServiceUri(rowParameters) + "&" + serviceMapTimeRange + "&toTime=" + upperTimeLimitISOTrimmed + "&valueName=" + sm_field,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function (originalData)
                            {
                                compareval = 0;
                                var convertedData = convertDataFromSmToDm(originalData, sm_field, udm, compareval, timeCount, notFirstInst);
                                if (convertedData)
                                {
                                    if (convertedData.data.length > 0)
                                    {
                                        var localTimeZone = moment.tz.guess();
                                        var momentDateTime = moment();
                                        var localDateTime = momentDateTime.tz(localTimeZone).format();
                                        localDateTime = localDateTime.replace("T", " ");
                                        var plusIndexLocal = localDateTime.indexOf("+");
                                        localDateTime = localDateTime.substr(0, plusIndexLocal);
                                        var localTimeZoneString = "";
                                        if (localDateTime == "") {
                                            localTimeZoneString = "(not recognized) --> Europe/Rome"
                                        } else {
                                            localTimeZoneString = localTimeZone;
                                        }
                                        ComparePeriodCalc(timeRangeCompare, timeCount);

                                        $.ajax({
                                            //    url: rowParameters + "&" + serviceMapTimeRange + "&valueName=" + sm_field,
                                            url: "<?= $superServiceMapProxy ?>" + encodeServiceUri(rowParameters) + "&" + serviceMapTimeRangeCompare + "&toTime=" + upperTimeLimitCompareISOTrim + "&valueName=" + sm_field,
                                            type: "GET",
                                            data: {},
                                            async: true,
                                            dataType: 'json',
                                            success: function (originalData)
                                            {
                                                compareval = 1;
                                                convertedData = convertDataFromSmToDm(originalData, sm_field, udm, compareval, timeCount, notFirstInst);
                                                if (convertedData)
                                                {
                                                    if (convertedData.data.length > 0)
                                                    {
                                                        var localTimeZone = moment.tz.guess();
                                                        var momentDateTime = moment();
                                                        var localDateTime = momentDateTime.tz(localTimeZone).format();
                                                        localDateTime = localDateTime.replace("T", " ");
                                                        var plusIndexLocal = localDateTime.indexOf("+");
                                                        localDateTime = localDateTime.substr(0, plusIndexLocal);
                                                        var localTimeZoneString = "";
                                                        if (localDateTime == "") {
                                                            localTimeZoneString = "(not recognized) --> Europe/Rome"
                                                        } else {
                                                            localTimeZoneString = localTimeZone;
                                                        }
                                                        //convertedData = convertedData + convertedData2;
                                                        drawDiagram(convertedData, fromGisExternalContentRange, fromGisExternalContentField, true, localTimeZoneString, udm);
                                                        upLimit = convertedData.data[0].commit.author.computationDate;
                                                        if (timeNavCount < 0) {
                                                            if (moment(upLimit).isBefore(moment(dateInFuture))) {

                                                            } else {
                                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                                            }
                                                        }

                                                    } else
                                                    {
                                                        showWidgetContent(widgetName);
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                        console.log("Dati non disponibili da Service Map");
                                                    }
                                                } else
                                                {
                                                    showWidgetContent(widgetName);
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                    console.log("Dati non disponibili da Service Map");
                                                }
                                            },
                                            error: function (data)
                                            {
                                                showWidgetContent(widgetName);
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                console.log("Errore in scaricamento dati da Service Map");
                                                console.log(JSON.stringify(data));
                                            }
                                        });

                                    } else
                                    {
                                        showWidgetContent(widgetName);
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Dati non disponibili da Service Map");
                                    }
                                } else
                                {
                                    showWidgetContent(widgetName);
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    console.log("Dati non disponibili da Service Map");
                                }
                            },
                            error: function (data)
                            {
                                showWidgetContent(widgetName);
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                console.log("Errore in scaricamento dati da Service Map");
                                console.log(JSON.stringify(data));
                            }
                        });
                        break;
                    case 'no':
                        $.ajax({
                            url: "../widgets/getDataMetricsForTimeTrend.php",
                            data: {"IdMisura": [metricName], "time": "<?= escapeForJS($_REQUEST['time']) ?>", "compare": 1, "compareperiod": [timeRangeCompare], "timeCount": timeCount},
                            type: "GET",
                            async: true,
                            dataType: 'json',
                            success: function (metricData)
                            {
                                var localTimeZone = moment.tz.guess();
                                var momentDateTime = moment();
                                var localDateTime = momentDateTime.tz(localTimeZone).format();
                                localDateTime = localDateTime.replace("T", " ");
                                var plusIndexLocal = localDateTime.indexOf("+");
                                localDateTime = localDateTime.substr(0, plusIndexLocal);
                                var localTimeZoneString = "";
                                if (localDateTime == "") {
                                    localTimeZoneString = "(not recognized) --> Europe/Rome"
                                } else {
                                    localTimeZoneString = localTimeZone;
                                }
                                drawDiagram(metricData, fromGisExternalContentRange, fromGisExternalContentField, true, localTimeZoneString, udm);
                            },
                            error: function (errorData)
                            {
                                showWidgetContent(widgetName);
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                console.log("Errore in chiamata di getDataMetricsForTimeTrend.php.");
                                console.log(JSON.stringify(errorData));
                            }
                        });
                        break;
                    case 'myPersonalData':
                    case 'myData':
                    case 'myKPI':
                        if (fromTrackerParams != null && fromTrackerParams != undefined) {
                            rowParameters = fromTrackerParams;
                        } else {
                            if (rowParameters.includes("datamanager/api/v1/poidata/")) {
                                rowParameters = rowParameters.split("datamanager/api/v1/poidata/")[1];
                            }
                        }

                        $.ajax({
                            url: "../controllers/myKpiProxy.php",
                            type: "GET",
                            data: {
                                myKpiId: rowParameters,
                                timeRange: myKPITimeRange,
                                action: "getValueUnitForTrend"
                            },
                            async: true,
                            dataType: 'json',
                            success: function (data) {
                                var stopFlag = 1;
                                // convertDataFromMyKpiToDm è in js/widgetcommonfunction.js
                                compareval = 0;
                                var convertedData = convertDataCompareFromMyKpiToDm(data, compareval, timeCount, notFirstInst);
                                if (convertedData)
                                {
                                    if (convertedData.data.length > 1)
                                    {
                                        if (convertedData.data[0].commit.author.value != null){
                                            
                                        if (udmFromUserOptions != null) {
                                            udm = udmFromUserOptions;
                                        } else {
                                            if (data[0].variableUnit != null) {
                                                udm = data[0].variableUnit;
                                            } else if (data[0].valueUnit != null) {
                                                udm = data[0].valueUnit;
                                            }
                                        }
                                        var localTimeZone = moment.tz.guess();
                                        var momentDateTime = moment();
                                        var localDateTime = momentDateTime.tz(localTimeZone).format();
                                        localDateTime = localDateTime.replace("T", " ");
                                        var plusIndexLocal = localDateTime.indexOf("+");
                                        localDateTime = localDateTime.substr(0, plusIndexLocal);
                                        var localTimeZoneString = "";
                                        if (localDateTime == "") {
                                            localTimeZoneString = "(not recognized) --> Europe/Rome"
                                        } else {
                                            localTimeZoneString = localTimeZone;
                                        }

                                        ComparePeriodCalc(timeRangeCompare, timeCount);

                                        $.ajax({
                                            url: "../controllers/myKpiProxy.php",
                                            type: "GET",
                                            data: {
                                                myKpiId: rowParameters,
                                                timeRange: myKPITimeRangeCompare,
                                                action: "getValueUnitForTrend"
                                            },
                                            async: true,
                                            dataType: 'json',
                                            success: function (data) {
                                                var stopFlag = 1;
                                                // convertDataFromMyKpiToDm è in js/widgetcommonfunction.js
                                                compareval = 1;
                                                convertedData = convertDataCompareFromMyKpiToDm(data, compareval, timeCount, notFirstInst);
                                                if (convertedData)
                                                {
                                                    if (convertedData.data.length > 1 )
                                                    {
                                                        if (convertedData.data[0].commit.author.value != null){
                                                        if (udmFromUserOptions != null) {
                                                            udm = udmFromUserOptions;
                                                        } else {
                                                            if (data[0].variableUnit != null) {
                                                                udm = data[0].variableUnit;
                                                            } else if (data[0].valueUnit != null) {
                                                                udm = data[0].valueUnit;
                                                            }
                                                        }
                                                        var localTimeZone = moment.tz.guess();
                                                        var momentDateTime = moment();
                                                        var localDateTime = momentDateTime.tz(localTimeZone).format();
                                                        localDateTime = localDateTime.replace("T", " ");
                                                        var plusIndexLocal = localDateTime.indexOf("+");
                                                        localDateTime = localDateTime.substr(0, plusIndexLocal);
                                                        var localTimeZoneString = "";
                                                        if (localDateTime == "") {
                                                            localTimeZoneString = "(not recognized) --> Europe/Rome"
                                                        } else {
                                                            localTimeZoneString = localTimeZone;
                                                        }
                                                        drawDiagram(convertedData, globalDiagramRange, sm_field, true, localTimeZoneString, udm);

                                                    }
                                                }
                                                    //else if (convertedData.data[0].commit.author.value == null)
                                                    else
                                                    {
                                                        showWidgetContent(widgetName);
                                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                        console.log("Dati MyKPI non presenti");
                                                    }
                                                } else
                                                {
                                                    showWidgetContent(widgetName);
                                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                    console.log("Dati MyKPI non presenti");
                                                }
                                            },
                                            error: function (data) {
                                                showWidgetContent(widgetName);
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                console.log("Errore!");
                                                console.log(JSON.stringify(data));
                                            }
                                        });
                                    }
                                    }
                                    //else if (convertedData.data[0].commit.author.value == null)
                                    else
                                    {
                                        showWidgetContent(widgetName);
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Dati MyKPI non presenti");
                                    }
                                } else
                                {
                                    showWidgetContent(widgetName);
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    console.log("Dati MyKPI non presenti");
                                }
                            },
                            error: function (data) {
                                showWidgetContent(widgetName);
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                console.log("Errore!");
                                console.log(JSON.stringify(data));
                            }
                        });
                        break;
                }

                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(widgetTitle);
            }
        }

        function drawDiagram(metricData, timeRange, seriesName, fromSelector, timeZone, udm)
        {
            /* if ($("#" + widgetName + "_loading").css("display") == "block") {
             $("#" + widgetName + "_loading").css("display", "none");
             }*/
            if (metricData.data.length > 0)
            {
                seriesData1 = [];
                valuesData1 = [];
                seriesData2 = [];
                valuesData2 = [];
                for (var i = 0; i < metricData.data.length; i++)
                {
                    day = metricData.data[i].commit.author.computationDate;
                    if ((metricData.data[i].commit.author.value !== null) && (metricData.data[i].commit.author.value !== ""))
                    {
                        value = parseFloat(parseFloat(metricData.data[i].commit.author.value).toFixed(1));
                        flagNumeric = true;
                    } else if ((metricData.data[i].commit.author.value_perc1 !== null) && (metricData.data[i].commit.author.value_perc1 !== ""))
                    {
                        if (value >= 100)
                        {
                            value = parseFloat(parseFloat(metricData.data[i].commit.author.value_perc1).toFixed(0));
                        } else
                        {
                            value = parseFloat(parseFloat(metricData.data[i].commit.author.value_perc1).toFixed(1));
                        }
                        flagNumeric = true;
                    }

                    dayParts = day.substring(0, day.indexOf(' ')).split('-');
                    timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                    if (metricData.data[i].commit.range_dates === 0)
                    {
                        if (range === '1/DAY' || range.split("/")[1] === "HOUR" || range === "7/DAY" || range === "30/DAY")
                        {
                            seriesData1.push([Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2], timeParts[0], timeParts[1]), value]);
                        } else
                        {
                            seriesData1.push([Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2]), value]);
                        }

                        valuesData1.push(value);
                    } else
                    {
                        if (range === '30/DAY')
                        {
                            if (timeRangeCompare === "same month prev year - day 1") {
                                //seriesData2.push([Date.UTC(parseInt(dayParts[0]) + 1, parseInt(dayParts[1]) - 1, dayParts[2]), value]);
                                seriesData2.push([Date.UTC(parseInt(dayParts[0]) + 1, parseInt(dayParts[1]) - 1, dayParts[2], timeParts[0], timeParts[1]), value]);
                            } else {
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1], dayParts[2], timeParts[0], timeParts[1]), value]);
                            }
                        }
                        if (range === '180/DAY')  
                        {
                            if ((timeRangeCompare === "same 6 months of prev year - day 1" || timeRangeCompare === "same 6 months of prev year - day 1 month 1 or 7") && (sm_based === 'no' || sm_based === 'yes')) {
                                seriesData2.push([Date.UTC(parseInt(dayParts[0]) + 1, parseInt(dayParts[1]) - 1, dayParts[2]), value]);
                                
                            } else {
                                seriesData2.push([Date.UTC(dayParts[0], parseInt(dayParts[1]) - 1, parseInt(dayParts[2]) + 180), value]);
                            }
                            
                        } /*else if (range === '7/DAY')
                         {
                         seriesData2.push([Date.UTC(dayParts[0], parseInt(dayParts[1]) - 1, parseInt(dayParts[2]) + 7), value]);
                         }*/ else if (range === '365/DAY')
                        {
                            seriesData2.push([Date.UTC(parseInt(dayParts[0]) + 1, dayParts[1] - 1, dayParts[2]), value]);
                        } else if (range === '730/DAY')
                        {
                            seriesData2.push([Date.UTC(parseInt(dayParts[0]) + 2, dayParts[1] - 1, dayParts[2]), value]);
                        } else if (range === '3650/DAY')
                        {
                            seriesData2.push([Date.UTC(parseInt(dayParts[0]) + 10, dayParts[1] - 1, dayParts[2]), value]);
                        } else if (range === '1/DAY')
                        {
                            if (timeRangeCompare === "same day previous week"){
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1] - 1, parseInt(dayParts[2]) + 7, timeParts[0], timeParts[1]), value]);
                            } else if (timeRangeCompare === "same day previous month"){ 
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1] , parseInt(dayParts[2]), timeParts[0], timeParts[1]), value]);
                            } else {    
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1] - 1, parseInt(dayParts[2]) + 1, timeParts[0], timeParts[1]), value]);
                            } 
                        } else if (range === '7/DAY')
                        {
                            seriesData2.push([Date.UTC(dayParts[0], dayParts[1] - 1, parseInt(dayParts[2]) + 7, timeParts[0], timeParts[1]), value]);
                        } else if (range === '4/HOUR')
                        {   
                            if (timeRangeCompare === "4 hours day before"){
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1] - 1, parseInt(dayParts[2]) + 1, parseInt(timeParts[0]) , timeParts[1]), value]);
                            }else{    
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2], parseInt(timeParts[0]) + 4, timeParts[1]), value]);
                            }
                        }else if (range === '12/HOUR')
                        {
                            if (timeRangeCompare === "12 hours day before"){
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1] - 1, parseInt(dayParts[2]) + 1, parseInt(timeParts[0]) , timeParts[1]), value]);
                            }else{ 
                                seriesData2.push([Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2], parseInt(timeParts[0]) + 12 , timeParts[1]), value]);
                            }
                        }

                        valuesData2.push(value);
                    }
                }

                maxValue1 = parseFloat((Math.max.apply(Math, valuesData1)).toFixed(1));
                maxValue2 = parseFloat((Math.max.apply(Math, valuesData2)).toFixed(1));
                nInterval = parseFloat((Math.max(maxValue1, maxValue2) / 4).toFixed(1));
                if (flagNumeric && (thresholdObject !== null))
                {
                    plotLinesArray = [];
                    var op, op1, op2 = null;
                    for (var i in thresholdObject)
                    {
                        //Semiretta sinistra
                        if ((thresholdObject[i].op === "less") || (thresholdObject[i].op === "lessEqual"))
                        {
                            if (thresholdObject[i].op === "less")
                            {
                                op = "<";
                            } else
                            {
                                op = "<=";
                            }

                            plotLineObj = {
                                color: thresholdObject[i].color,
                                dashStyle: 'shortdash',
                                value: parseFloat(thresholdObject[i].thr1),
                                width: 1,
                                zIndex: 5,
                                label: {
                                    text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1,
                                    y: 12
                                }
                            };
                            plotLinesArray.push(plotLineObj);
                        } else
                        {
                            //Semiretta destra
                            if ((thresholdObject[i].op === "greater") || (thresholdObject[i].op === "greaterEqual"))
                            {
                                if (thresholdObject[i].op === "greater")
                                {
                                    op = ">";
                                } else
                                {
                                    op = ">=";
                                }

                                //Semiretta destra
                                plotLineObj = {
                                    color: thresholdObject[i].color,
                                    dashStyle: 'shortdash',
                                    value: parseFloat(thresholdObject[i].thr1),
                                    width: 1,
                                    zIndex: 5,
                                    label: {
                                        text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1
                                    }
                                };
                                plotLinesArray.push(plotLineObj);
                            } else
                            {
                                //Valore uguale a
                                if (thresholdObject[i].op === "equal")
                                {
                                    op = "=";
                                    plotLineObj = {
                                        color: thresholdObject[i].color,
                                        dashStyle: 'shortdash',
                                        value: parseFloat(thresholdObject[i].thr1),
                                        width: 1,
                                        zIndex: 5,
                                        label: {
                                            text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1
                                        }
                                    };
                                    plotLinesArray.push(plotLineObj);
                                } else
                                {
                                    //Valore diverso da
                                    if (thresholdObject[i].op === "notEqual")
                                    {
                                        op = "!=";
                                        plotLineObj = {
                                            color: thresholdObject[i].color,
                                            dashStyle: 'shortdash',
                                            value: parseFloat(thresholdObject[i].thr1),
                                            width: 1,
                                            zIndex: 5,
                                            label: {
                                                text: thresholdObject[i].desc + " " + op + " " + thresholdObject[i].thr1
                                            }
                                        };
                                        plotLinesArray.push(plotLineObj);
                                    } else
                                    {
                                        //Intervallo bi-limitato
                                        switch (thresholdObject[i].op)
                                        {
                                            case "intervalOpen":
                                                op1 = ">";
                                                op2 = "<";
                                                break;
                                            case "intervalClosed":
                                                op1 = ">=";
                                                op2 = "<=";
                                                break;
                                            case "intervalLeftOpen":
                                                op1 = ">";
                                                op2 = "<=";
                                                break;
                                            case "intervalRightOpen":
                                                op1 = ">=";
                                                op2 = "<";
                                                break;
                                        }

                                        plotLineObj = {
                                            color: thresholdObject[i].color,
                                            dashStyle: 'shortdash',
                                            value: parseFloat(thresholdObject[i].thr1),
                                            width: 1,
                                            zIndex: 5,
                                            label: {
                                                text: thresholdObject[i].desc + " " + op1 + " " + thresholdObject[i].thr1
                                            }
                                        };
                                        plotLinesArray.push(plotLineObj);
                                        plotLineObj = {
                                            color: thresholdObject[i].color,
                                            dashStyle: 'shortdash',
                                            value: parseFloat(thresholdObject[i].thr2),
                                            width: 1,
                                            zIndex: 5,
                                            label: {
                                                text: thresholdObject[i].desc + " " + op2 + " " + thresholdObject[i].thr2,
                                                y: 12
                                            }
                                        };
                                        plotLinesArray.push(plotLineObj);
                                    }
                                }
                            }
                        }
                    }


                }

                if (firstLoad !== false)
                {
                    showWidgetContent(widgetName);
                } else
                {
                    elToEmpty.empty();
                }
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").show();
                //Disegno del diagramma
                chartRef = Highcharts.chart('<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer', {
                    credits: {
                        enabled: false
                    },
                    chart: {
                        zoomType: 'x',
                        backgroundColor: 'transparent',
                        type: 'areaspline',
                        style: {
                            fontFamily: 'Montserrat'
                        },
                        events: {
                            load: function () {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartColorMenuItem").trigger('chartCreated');
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartPlaneColorMenuItem").trigger('chartCreated');
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartLabelsColorMenuItem").trigger('chartCreated');
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartAxesColorMenuItem").trigger('chartCreated');
                            }
                        }
                    },
                    exporting: {
                        enabled: false
                    },
                    title: {
                        text: ''

                    },
                    xAxis: {
                        type: 'datetime',
                        units: unitsWidget,
                        lineColor: chartAxesColor,
                        labels: {
                            enabled: true,
                            useHTML: true,
                            style: {
                                fontFamily: 'Montserrat',
                                color: chartLabelsFontColor,
                                fontSize: fontSize + "px",
                                /*"text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                 "textOutline": "1px 1px contrast"*/
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: ''
                        },
                        min: 0,
                        max: Math.max(maxValue1, maxValue2),
                        tickInterval: nInterval,
                        plotLines: plotLinesArray,
                        lineWidth: 1,
                        gridLineColor: gridLineColor,
                        lineColor: chartAxesColor,
                        labels: {
                            enabled: true,
                            style: {
                                fontFamily: 'Montserrat',
                                color: chartLabelsFontColor,
                                fontSize: fontSize + "px",
                                /*"text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                 "textOutline": "1px 1px contrast"*/
                            }
                        }
                    },
                    tooltip: {
                        //valueSuffix: 'Valori',
                        formatter: function () {
                            var val2 = Highcharts.dateFormat('%a %d %b %H:%M:%S', this.x);
                            var valX = new Date(this.x);
                            var nameSerie = this.series.name;
                            var mod = range;
                            if (nameSerie === 'Previous')
                            {
                                if (mod === '1/DAY')
                                {
                                    valueAtt = valX.getDate();
                                    valuePrec = valX.getDate() - 1;
                                    valX.setDate(valuePrec);
                                } else if (mod === '30/DAY')
                                {
                                   if (timeRangeCompare === "same month prev year - day 1") {
                                       valueAtt = valX.getFullYear();
                                       valuePrec = valX.getFullYear() - 1;
                                       valX.setYear(valuePrec);
                                   }else {
                                       valueAtt = valX.getMonth();
                                       valuePrec = valX.getMonth() - 1;
                                       valX.setMonth(valuePrec);
                                   }
                                    
                                } else if (mod === '7/DAY')
                                {
                                    valueAtt = valX.getDate();
                                    valuePrec = valX.getDate() - 7;
                                    valX.setDate(valuePrec);
                                } else if (mod === '180/DAY')
                                {
                                    if ((timeRangeCompare === "same 6 months of prev year - day 1" || timeRangeCompare === "same 6 months of prev year - day 1 month 1 or 7") && sm_based=='no') {
                                       valueAtt = valX.getFullYear();
                                       valuePrec = valX.getFullYear() - 1;
                                       valX.setYear(valuePrec);
                                   }else {
                                       valueAtt = valX.getDate();
                                       valuePrec = valX.getDate() - 180;
                                       valX.setDate(valuePrec);
                                   }
                                    
                                } else if (mod === '365/DAY')
                                {
                                    valueAtt = valX.getFullYear();
                                    valuePrec = valX.getFullYear() - 1;
                                    valX.setYear(valuePrec);
                                } else if (mod === '730/DAY')
                                {
                                    valueAtt = valX.getFullYear();
                                    valuePrec = valX.getFullYear() - 2;
                                    valX.setYear(valuePrec);
                                } else if (mod === '3650/DAY')
                                {
                                    valueAtt = valX.getFullYear();
                                    valuePrec = valX.getFullYear() - 10;
                                    valX.setYear(valuePrec);
                                } else if (mod === '4/HOUR')
                                {
                                    valueAtt = valX.getHours();
                                    if (timeRangeCompare === "4 hours day before"){
                                        valuePrec = valX.getHours() - 24;
                                    }else{
                                        valuePrec = valX.getHours() - 4;
                                    }
                                    valX.setHours(valuePrec);
                                } else if (mod === '12/HOUR')
                                {
                                    valueAtt = valX.getHours();
                                    if (timeRangeCompare === "12 hours day before"){
                                        valuePrec = valX.getHours() - 24;
                                    }else{
                                        valuePrec = valX.getHours() - 12;
                                    }
                                    valX.setHours(valuePrec);
                                }
                            }
                            return Highcharts.dateFormat('%A, %b %d %Y, %H:%M', valX.getTime()) + '<br><span style="color:' + this.color + '">\u25CF</span> ' + nameSerie + ': <b>' + this.y + '</b>';
                        },
                        headerFormat: '<b>{series.name}</b><br>',
                        pointFormat: '{point.x:%e. %b}: {point.y:.2f}'
                    },
                    plotOptions: {
                        series: {
                            events:
                                    {
                                        legendItemClick: function () {
                                            return false;
                                        }
                                    },
                            lineWidth: 2
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0,
                        margin: 2,
                        itemStyle: {
                            fontFamily: 'Montserrat',
                            color: chartLabelsFontColor,
                            fontSize: fontSize + "px",
                            /*"text-shadow": "1px 1px 1px rgba(0,0,0,0.35)",
                             "textOutline": "1px 1px contrast"*/
                        }
                    },
                    series: [{
                            showInLegend: true,
                            name: "Current",
                            data: seriesData1,
                            color: chartColor,
                            fillColor: {
                                linearGradient: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 0,
                                    y2: 1
                                },
                                stops: [
                                    [0, Highcharts.Color(chartColor).setOpacity(0.5).get('rgba')],
                                    [1, Highcharts.Color(chartColor).setOpacity(0).get('rgba')]
                                ]
                            },
                            zIndex: 999999
                        },
                        {
                            showInLegend: true,
                            name: "Previous",
                            data: seriesData2,
                            color: "#CECECE",
                            zIndex: 999999,
                            fillColor: {
                                linearGradient: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 0,
                                    y2: 1
                                },
                                stops: [
                                    [0, Highcharts.Color("#CECECE").setOpacity(0).get('rgba')],
                                    [1, Highcharts.Color("#CECECE").setOpacity(0).get('rgba')]
                                ]
                            },
                        }]
                });
            } else
            {
                console.log("Chiamata di getDataMetricsForTimeTrend.php OK ma nessun dato restituito.");
                showWidgetContent(widgetName);
                if (firstLoad !== false)
                {
                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                }
            }

            /*   if ($("#" + widgetName + "_content").css("display") == "none") {
             $("#" + widgetName + "_content").css("display", "block");
             }*/
            showWidgetContent(widgetName);
        }
        //Fine definizioni di funzione 

        $("#" + widgetName + "_timeTrendPrevBtn").off("click").click(function () {
            //  alert("PREV Clicked!");
            if (timeNavCount == 0) {
                //   if (widgetData.params.sm_based == "yes" || fromGisExternalContent === true) {
                let urlKBToBeCalled = "";
                let field = "";
                let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                if (fromGisExternalContent) {
                    // urlKBToBeCalled = dashboardOrgKbUrl + "?serviceUri=" + encodeURI(fromGisExternalContentServiceUri) + "&valueName=" + fromGisExternalContentField;
                    urlKBToBeCalled = dashboardOrgKbUrl + "?serviceUri=" + encodeURI(fromGisExternalContentServiceUri);
                    field = fromGisExternalContentField;
                } else {
                    //  urlKBToBeCalled = rowParameters + "&" + "&valueName=" + sm_field;
                    urlKBToBeCalled = rowParameters;
                    field = sm_field;
                }
                if (rowParameters != null) {
                    if (rowParameters.includes("https:")) {
                        $.ajax({
                            url: "<?= $superServiceMapProxy ?>" + urlKBToBeCalled,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function (originalData) {
                                var stopFlag = 1;
                                var convertedData = convertDataFromTimeNavToDm(originalData, field, udm);
                                if (convertedData) {
                                    if (convertedData.data.length > 0) {
                                        var localTimeZone = moment.tz.guess();
                                        var momentDateTime = moment();
                                        var localDateTime = momentDateTime.tz(localTimeZone).format();
                                        localDateTime = localDateTime.replace("T", " ");
                                        var plusIndexLocal = localDateTime.indexOf("+");
                                        localDateTime = localDateTime.substr(0, plusIndexLocal);
                                        var localTimeZoneString = "";
                                        if (localDateTime == "") {
                                            localTimeZoneString = "(not recognized) --> Europe/Rome"
                                        } else {
                                            localTimeZoneString = localTimeZone;
                                        }
                                        if (convertedData.data[0].commit.author.futureDate != null && convertedData.data[0].commit.author.futureDate != undefined) {
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
                                //  showWidgetContent(widgetName);
                                //  $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                //  $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                console.log("Errore in chiamata prima API");
                                console.log(JSON.stringify(data));
                            }
                        });
                    } else {
                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                    }
                } else {
                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
                }

            } else if (timeNavCount < 0 && $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").is(":hidden")) {
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").show();
            }
            timeNavCount++;
            setupLoadingPanel(widgetName, widgetContentColor, true);
            //    populateWidget(timeRange, null, "minus", timeNavCount, null, udmFromUserOptions);
            populateWidget(timeRange, null, "minus", timeNavCount, null, udm);
        });
        $("#" + widgetName + "_timeTrendNextBtn").off("click").click(function () {
            //   alert("NEXT Clicked!");
            timeNavCount--;
            if (timeNavCount == 0) {
                //    if (widgetData.params.sm_based == "yes" || fromGisExternalContent === true) {
                var notFirstInst = '1';
                let urlKBToBeCalled = "";
                let field = "";
                let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                if (fromGisExternalContent) {
                    // urlKBToBeCalled = dashboardOrgKbUrl + "?serviceUri=" + encodeURI(fromGisExternalContentServiceUri) + "&valueName=" + fromGisExternalContentField;
                    urlKBToBeCalled = dashboardOrgKbUrl + "?serviceUri=" + encodeURI(fromGisExternalContentServiceUri);
                    field = fromGisExternalContentField;
                } else {
                    //  urlKBToBeCalled = rowParameters + "&" + "&valueName=" + sm_field;
                    urlKBToBeCalled = rowParameters;
                    field = sm_field;
                }
                if (rowParameters != null) {
                    if (rowParameters.includes("https:")) {
                        $.ajax({
                            url: "<?= $superServiceMapProxy ?>" + urlKBToBeCalled,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function (originalData) {
                                var stopFlag = 1;
                                var convertedData = convertDataFromTimeNavToDm(originalData, field, udm);
                                if (convertedData) {
                                    if (convertedData.data.length > 0) {
                                        var localTimeZone = moment.tz.guess();
                                        var momentDateTime = moment();
                                        var localDateTime = momentDateTime.tz(localTimeZone).format();
                                        localDateTime = localDateTime.replace("T", " ");
                                        var plusIndexLocal = localDateTime.indexOf("+");
                                        localDateTime = localDateTime.substr(0, plusIndexLocal);
                                        var localTimeZoneString = "";
                                        if (localDateTime == "") {
                                            localTimeZoneString = "(not recognized) --> Europe/Rome"
                                        } else {
                                            localTimeZoneString = localTimeZone;
                                        }
                                        if (convertedData.data[0].commit.author.futureDate != null && convertedData.data[0].commit.author.futureDate != undefined) {
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
                                //  showWidgetContent(widgetName);
                                //  $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                //  $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                console.log("Errore in chiamata prima API");
                                console.log(JSON.stringify(data));
                            }
                        });
                    } else {
                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                    }
                } else {
                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                }
            }

            setupLoadingPanel(widgetName, widgetContentColor, true);
            //    populateWidget(timeRange, null, "plus", timeNavCount, dataFut, udmFromUserOptions);
            populateWidget(timeRange, null, "plus", timeNavCount, dataFut, udm, notFirstInst);
        });
        $.ajax({
            url: "../controllers/getWidgetParams.php",
            type: "GET",
            data: {
                widgetName: "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>"
            },
            async: true,
            dataType: 'json',
            success: function (widgetData)
            {
                if (timeNavCount == 0) {
                    if (widgetData.params.sm_based == "yes" || fromGisExternalContent === true) {
                        let urlKBToBeCalled = "";
                        let field = "";
                        let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                        //let dashboardOrgKbUrl = "https://servicemap.disit.org/WebAppGrafo/api/v1/";
                        if (fromGisExternalContent) {
                            // urlKBToBeCalled = dashboardOrgKbUrl + "?serviceUri=" + encodeURI(fromGisExternalContentServiceUri) + "&valueName=" + fromGisExternalContentField;
                            urlKBToBeCalled = "<?= $superServiceMapProxy ?>" + dashboardOrgKbUrl + "?serviceUri=" + encodeURI(fromGisExternalContentServiceUri);
                            field = fromGisExternalContentField;
                        } else {
                            //  urlKBToBeCalled = rowParameters + "&" + "&valueName=" + sm_field;
                            urlKBToBeCalled = "<?= $superServiceMapProxy ?>" + widgetData.params.rowParameters;
                            field = widgetData.params.sm_field;
                        }
                        $.ajax({
                            url: urlKBToBeCalled,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function (originalData) {
                                var stopFlag = 1;
                                var convertedData = convertDataFromTimeNavToDm(originalData, field, udmFromUserOptions);
                                if (convertedData) {
                                    if (convertedData.data.length > 0) {
                                        var localTimeZone = moment.tz.guess();
                                        var momentDateTime = moment();
                                        var localDateTime = momentDateTime.tz(localTimeZone).format();
                                        localDateTime = localDateTime.replace("T", " ");
                                        var plusIndexLocal = localDateTime.indexOf("+");
                                        localDateTime = localDateTime.substr(0, plusIndexLocal);
                                        var localTimeZoneString = "";
                                        if (localDateTime == "") {
                                            localTimeZoneString = "(not recognized) --> Europe/Rome"
                                        } else {
                                            localTimeZoneString = localTimeZone;
                                        }
                                        if (convertedData.data[0].commit.author.futureDate != null && convertedData.data[0].commit.author.futureDate != undefined) {
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
                                //  showWidgetContent(widgetName);
                                //  $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                //  $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                console.log("Errore in chiamata prima API");
                                console.log(JSON.stringify(data));
                            }
                        });
                    } else {
                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                    }
                }

                showTitle = widgetData.params.showTitle;
                widgetContentColor = widgetData.params.color_w;
                fontSize = widgetData.params.fontSize;
                timeToReload = widgetData.params.frequency_w;
                hasTimer = widgetData.params.hasTimer;
                widgetTitle = widgetData.params.title_w;
                widgetHeaderColor = widgetData.params.frame_color_w;
                widgetHeaderFontColor = widgetData.params.headerFontColor;
                chartColor = widgetData.params.chartColor;
                dataLabelsFontSize = widgetData.params.dataLabelsFontSize;
                dataLabelsFontColor = widgetData.params.dataLabelsFontColor;
                chartLabelsFontSize = widgetData.params.chartLabelsFontSize;
                chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
                sm_based = widgetData.params.sm_based;
                rowParameters = widgetData.params.rowParameters;
                sm_field = widgetData.params.sm_field;
                gridLineColor = widgetData.params.chartPlaneColor;
                chartAxesColor = widgetData.params.chartAxesColor;
                if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no")))
                {
                    showHeader = false;
                } else
                {
                    showHeader = true;
                }

                if ((metricNameFromDriver === "undefined") || (metricNameFromDriver === undefined) || (metricNameFromDriver === "null") || (metricNameFromDriver === null))
                {
                    metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
                    widgetTitle = widgetData.params.title_w;
                    widgetHeaderColor = widgetData.params.frame_color_w;
                    widgetHeaderFontColor = widgetData.params.headerFontColor;
                    timeRange = widgetData.params.temporal_range_w;
                    timeRangeCompare = widgetData.params.temporal_compare_w;
                } else
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
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').off('resizeWidgets');
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
                if (firstLoad === false)
                {
                    showWidgetContent(widgetName);
                } else
                {
                    setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                }


                //Nuova versione
                if (('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "") && ('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "null"))
                {
                    styleParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>');
                }

                if ('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>'.length > 0)
                {
                    widgetParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>');
                }

                if (widgetParameters !== null && widgetParameters !== undefined)
                {
                    if (widgetParameters.hasOwnProperty("thresholdObject"))
                    {
                        thresholdObject = widgetParameters.thresholdObject;
                    }
                }

                if (widgetParameters !== null && widgetParameters !== undefined)
                {
                    if (widgetParameters.hasOwnProperty("thresholdObject"))
                    {
                        thresholdObject = widgetParameters.thresholdObject;
                    }
                }

                sizeRowsWidget = parseInt('<?= escapeForJS($_REQUEST['size_rows']) ?>');
                if (timeRange == null || timeRange == undefined) {
                    timeRange = widgetData.params.temporal_range_w;
                }
                if (timeRangeCompare == null || timeRangeCompare == undefined) {
                    timeRangeCompare = widgetData.params.temporal_compare_w;
                }
                var key = getQueryString()["entityId"];
                if (key == null) {
                    populateWidget(timeRange, null, null, timeNavCount, null, udmFromUserOptions);
                } else {
                    if (styleParameters != null && styleParameters.showContentLoadM != null) {
                        populateWidget(timeRange, null, null, timeNavCount, null, udmFromUserOptions, null, styleParameters.showContentLoadM, fromCsbl);
                    } else {
                        populateWidget(timeRange, null, null, timeNavCount, null, udmFromUserOptions, null, null, fromCsbl);
                    }
                }
                if (infoJson != "fromTracker" || fromGisExternalContent === true) {
                    var titleDiv = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv');
                    //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv').css("width", "3.5%");
                    //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_countdownContainerDiv').css("width", "3%");
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

                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('customResizeEvent', function (event) {
                    resizeWidget();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().reflow();
                });
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").off('updateFrequency');
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('updateFrequency', function (event) {
                    cclearInterval(countdownRef);
                    timeToReload = event.newTimeToReload;
                    countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef);
                });

                $("#<?= $_REQUEST['name_w'] ?>").off('changeTimeRangeCompareEvent');
                $("#<?= $_REQUEST['name_w'] ?>").on('changeTimeRangeCompareEvent', function (event)
                {
                    //    currentWidth = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height();
                    //   $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').hide();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading').show();
                    //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').css('height', currentWidth);
                    timeRangeCompare = event.newTimeRangeCompare;
                    populateWidget(event.newTimeRangeCompare, null, null, 0, null, udmFromUserOptions);
                });

                countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef);
            },
            error: function (errorData)
            {

            }
        });
    }); //Fine document ready   

</script>

<div class="widget" id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>

        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>

        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>	
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert" class="noDataAlert">
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText" class="noDataAlertText">
                    No Data Available in the Selected Time Range
                </div>
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 