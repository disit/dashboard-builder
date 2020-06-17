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
    if(!isset($_SESSION))
    {
        session_start();
    }
    checkSession('Public');

?>

<!-- <script type="text/javascript" src="../js/moment-timezone-with-data.js"></script> -->
<script type='text/javascript'> 
    $(document).ready(function <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId, fromTrackerFlag, fromTrackerDay, fromTrackerParams, futureLastDate)
    {
        <?php
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetTimeTrend.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?> 
        //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)
        var widgetName = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>";
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var wsRetryActive, wsRetryTime = null;
        var thresholdObject, chartColor, chartRef, styleParameters, metricType, pattern, totValues, shownValues, showTitle, showHeader, hasTimer, timeRange, globalDiagramRange, myKPITimeRange,
            threshold, thresholdEval, delta, deltaPerc, originalMetricType, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, fontSize, fontColor, timeToReload, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor,
            widgetParameters, sizeRowsWidget, desc, plotLinesArray, sm_based, rowParameters, sm_field, value, day, dayParts, timeParts, date, maxValue, minValue, nInterval, alarmSet, plotLineObj, metricName,
            widgetTitle, countdownRef,widgetOriginalBorderColor, serviceMapTimeRange, unitsWidget, webSocket, openWs, manageIncomingWsMsg, openWsConn, wsClosed, gridLineColor, chartAxesColor, infoJson = null;
        var elToEmpty = $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer");
        var seriesData = [];
        var valuesData = [];
        var embedWidget = <?= $_REQUEST['embedWidget']=='true' ? 'true' : 'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';	
        var headerHeight = 25;
        var needWebSocket = false;
    //    var loggedRole = "<?php echo $_SESSION['loggedRole'] ?>";
        var loggedOrg = "<?php echo $_SESSION['loggedOrganization'] ?>";
        var orgKbUrl = "<?php echo $_SESSION['orgKbUrl'] ?>";
        var orgCentreGpsCoords = "<?php echo $_SESSION['orgGpsCentreLatLng'] ?>";
        var now = new Date();
        var nowUTC = now.toUTCString();
        var isoDate = new Date(nowUTC).toISOString();
        var isoDateTrimmed = now.getFullYear()+"-"+(101+now.getMonth()+"").slice(-2)+"-"+(100+now.getDate()+"").slice(-2)+"T"+(100+now.getHours()+"").slice(-2)+":"+(100+now.getMinutes()+"").slice(-2);
        var myKPIFromTimeRange = "";
        var refreshToken = "<?= $_SESSION['refreshToken'] ?>";
        var accessToken = "<?= $_SESSION['accessToken'] ?>";
        var dayTracker = fromTrackerDay;
        var flagTracker = fromTrackerFlag;
        var upperTimeLimitISOTrimmed = null;
    //    this["timeNavCount_"+widgetName] = 0;
        var timeNavCount = 0;
        var fromGisExternalContentRangePrevious = null;
        var fromGisExternalContentServiceUriPrevious = null;
        var fromGisExternalContentFieldPrevious = null;
        var dataFut = null;
        var upLimit = null;
        var currentWidth = null;

        console.log("Entrato in widgetTimeTrend --> " + widgetName);

        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function(event) 
        {
            if((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef); 
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, null, null, null, null);
            }
        });
        
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
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, "<?= sanitizeTitle($_REQUEST['title_w']) ?>", "<?= escapeForJS($_REQUEST['frame_color_w']) ?>", "<?= $_REQUEST['headerFontColor'] ?>", false, null, null, null, null, null, null, false, null);
            }
        });

        $(document).off('mouseOverTimeTrendFromTracker_' + widgetName);
        $(document).on('mouseOverTimeTrendFromTracker_' + widgetName, function(event)
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

        $(document).off('mouseOutTimeTrendFromTracker_' + widgetName);
        $(document).on('mouseOutTimeTrendFromTracker_' + widgetName, function(event)
        {
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", widgetHeaderColor);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("color", widgetHeaderFontColor);
        });

        $(document).off('showTimeTrendFromTracker_' + widgetName);
        $(document).on('showTimeTrendFromTracker_' + widgetName, function(event)
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef);
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, event.widgetTitle, event.color1, "black", false, event.serviceUri, event.field, event.range, event.marker, event.mapRef, event.fakeId, true, event.day, event.rowParams);
            }
        });
		
	$(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event) 
        {
            showHeader = event.showHeader;
            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().reflow();
        });
        
        //Definizioni di funzione specifiche del widget
        function compareSeriesData(a, b)
        {
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
        }


        $("#" + widgetName + "_content").hover(function()
        {
            $.ajax({
                url: "../controllers/getWidgetParams.php",
                type: "GET",
                data: {
                    widgetName: "<?= $_REQUEST['name_w'] ?>"
                },
                async: true,
                dataType: 'json',
                success: function(widgetData) {
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

                    if(location.href.includes("index.php") && webLinkD != "" && webLinkD != "none" && webLinkD != null) {
                        $("#" + widgetName).css("cursor", "pointer");
                    }

                },
                error: function()
                {

                }
            });
        });

        $("#" + widgetName + "_content").off("click").click(function ()
        {
            $.ajax({
                url: "../controllers/getWidgetParams.php",
                type: "GET",
                data: {
                    widgetName: "<?= $_REQUEST['name_w'] ?>"
                },
                async: true,
                dataType: 'json',
                success: function(widgetData) {
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

                    if(location.href.includes("index.php")  && webLink != "" && webLink != "none") {

                        if (styleParameters != null) {
                            if (styleParameters['openNewTab'] === "yes") {
                                var newTab = window.open(webLink);
                                if (newTab) {
                                    newTab.focus();
                                }
                                else {
                                    alert('Please allow popups for this website');
                                }
                            } else {
                                window.location.href = webLink;
                            }
                        } else {
                            var newTab = window.open(webLink);
                            if (newTab) {
                                newTab.focus();
                            }
                            else {
                                alert('Please allow popups for this website');
                            }
                        }
                    }

                },
                error: function()
                {
                    console.log("Error in opening web link.");
                }
            });

        });



        function drawDiagram(metricData, timeRange, seriesName, fromSelector, timeZone)
        {
           /* if ($("#" + widgetName + "_loading").css("display") == "block") {
                $("#" + widgetName + "_loading").css("display", "none");
            }*/
            if(metricData.data.length > 0)
            {
                desc = metricData.data[0].commit.author.descrip;
                metricType = '<?= escapeForJS($_REQUEST['id_metric']) ?>';
                seriesData = [];
                valuesData = [];
                for(var i = 0; i < metricData.data.length; i++) 
                {
                    day = metricData.data[i].commit.author.computationDate;

                    if((metricData.data[i].commit.author.value !== null) && (metricData.data[i].commit.author.value !== "")) 
                    {
                    /*    var e = 1;
                        while (Math.round(metricData.data[i].commit.author.value * e) / e !== metricData.data[i].commit.author.value) e *= 10;
                        var precision = Math.log(e) / Math.LN10;    */
                        value = parseFloat(parseFloat(metricData.data[i].commit.author.value).toFixed(2));
                        flagNumeric = true;
                    } 
                    else if((metricData.data[i].commit.author.value_perc1 !== null) && (metricData.data[i].commit.author.value_perc1 !== "")) 
                    {
                        if(value >= 100) 
                        {
                            value = parseFloat(parseFloat(metricData.data[i].commit.author.value_perc1).toFixed(0));
                        } 
                        else 
                        {
                            value = parseFloat(parseFloat(metricData.data[i].commit.author.value_perc1).toFixed(1));
                        }
                        flagNumeric = true;
                    }

                    dayParts = day.substring(0, day.indexOf(' ')).split('-');
                    
                    if(fromSelector)
                    {
                        timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                        
                        if((timeRange === '1/DAY') || (timeRange.includes("HOUR"))) 
                        {
                            unitsWidget = [['millisecond', 
                            [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] 
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
                                [1]
                                //[1, 3, 4, 6, 8, 10, 12]
                            ], [
                                'year',
                                null
                            ]];
                            date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                        }
                        else 
                        {
                            unitsWidget = [['millisecond',  
                                [1] 
                            ], [
                                'second',
                                [1, 30]
                            ], [
                                'minute',
                                [1, 30]
                            ], [
                                'hour',
                                [1, 6]
                            ], [
                                'day',
                                [1]
                            ], [
                                'week',
                                [1]
                            ], [
                                'month',
                                [1]
                            ], [
                                'year',
                                [1]
                            ]];
                            date = Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2], timeParts[0]);
                        }
                        timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                        date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                    }
                    else
                    {
                        unitsWidget = [['millisecond', 
                            [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] 
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
                            [1]
                            //[1, 3, 4, 6, 8, 10, 12]
                        ], [
                            'year',
                            null
                        ]];
                        if((timeRange === '1/DAY') || (timeRange.includes("HOUR"))) 
                        {
                            timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                            date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                        }
                        else 
                        {
                            date = Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2]);
                        }
                    }
                    
                    seriesData.push([date, value]);
                    valuesData.push(value);
                }
                
                seriesData.sort(compareSeriesData);

                maxValue = Math.max.apply(Math, valuesData);
                minValue = Math.min.apply(Math, valuesData);
                nInterval = parseFloat((Math.abs(maxValue - minValue) / 4).toFixed(2));

                if(flagNumeric && (thresholdObject!== null))
                {
                   plotLinesArray = []; 
                   var op, op1, op2 = null;        

                   for(var i in thresholdObject) 
                   {
                      //Semiretta sinistra
                      if((thresholdObject[i].op === "less")||(thresholdObject[i].op === "lessEqual"))
                      {
                         if(thresholdObject[i].op === "less")
                         {
                            op = "<";
                         }
                         else
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
                      }
                      else
                      {
                         //Semiretta destra
                         if((thresholdObject[i].op === "greater")||(thresholdObject[i].op === "greaterEqual"))
                         {
                            if(thresholdObject[i].op === "greater")
                            {
                               op = ">";
                            }
                            else
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
                         }
                         else
                         {
                            //Valore uguale a
                            if(thresholdObject[i].op === "equal")
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
                            }
                            else
                            {
                               //Valore diverso da
                               if(thresholdObject[i].op === "notEqual")
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
                               }
                               else
                               {
                                  //Intervallo bi-limitato
                                  switch(thresholdObject[i].op)
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

                    //Non cancellare, da recuperare quando ripristini il blink in caso di allarme
                    /*delta = Math.abs(value - threshold);

                    //Distinguiamo in base all'operatore di confronto
                    switch(thresholdEval)
                    {
                       //Allarme attivo se il valore attuale è sotto la soglia
                       case '<':
                           if(value < threshold)
                           {
                              //alarmSet = true;
                           }
                           break;

                       //Allarme attivo se il valore attuale è sopra la soglia
                       case '>':
                           if(value > threshold)
                           {
                              //alarmSet = true;
                           }
                           break;

                       //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                       case '=':
                           deltaPerc = (delta / threshold)*100;
                           if(deltaPerc < 0.01)
                           {
                               //alarmSet = true;
                           }
                           break;    

                       //Non gestiamo altri operatori 
                       default:
                           break;
                    }*/
                }

                if(firstLoad !== false)
                {
                    showWidgetContent(widgetName);
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").show();
                }
                else
                {
                    elToEmpty.empty();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").show();
                }
                
                if(metricType === "isAlive") 
                {
                    //Calcolo del vettore delle zones
                    var myZonesArray = [];
                    
                    var newZoneItem = null;
                    var areaColor = null;
                    for(var i=1; i < seriesData.length; i++)
                    {
                        
                        switch(seriesData[i-1][1]){
                            case 2:
                                areaColor='#ff0000'; 
                                break;
                                
                             case 4:
                                 areaColor='#f96f06';
                                 break;
                                 
                             case 6:
                                 areaColor='#ffcc00';
                                 break;
                            
                            case 8:
                                areaColor='#00cc00';
                                break;
                
                       }   
                       if(i < seriesData.length-1)
                        {                                            
                            newZoneItem = {
                                value: seriesData[i][0],
                                color: areaColor
                            };
                        }
                        else
                        {
                            newZoneItem = {
                                color: areaColor
                            };
                        }
                        
                        myZonesArray.push(newZoneItem);
                    }
                  
                    //Disegno del diagramma
                    chartRef = Highcharts.chart('<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer', {
                        credits: {
                            enabled: false
                        },
                        chart: {
                            backgroundColor: 'transparent',
                            type: 'areaspline',
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
                        /*    title:
                                {
                                    enabled: true,
                                    text: "Time - zone: " + timeZone,
                                    style: {
                                        fontFamily: 'Montserrat',
                                        color: chartLabelsFontColor,
                                        fontSize: fontSize + "px"
                                    }
                                },  */
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
                            min: minValue,
                            max: 8,
                        //    tickInterval: nInterval,
                            plotLines: plotLinesArray,
                            gridLineColor: gridLineColor,
                            lineWidth: 1,
                            labels: {
                                enabled: true,
                                style: {
                                    fontFamily: 'Montserrat',
                                    color: chartLabelsFontColor,
                                    fontSize: fontSize + "px",
                                    /*"text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                    "textOutline": "1px 1px contrast"*/
                                },
                                formatter: function () {
                                    switch (this.value)
                                    {
                                        case 2:
                                            return "Time out";
                                            break;

                                        case 4:
                                            return "Error";
                                            break;

                                        case 6:
                                            return "Token not found";
                                            break;
                                        case 8:
                                            return "Ok";
                                            break;

                                        default:
                                            return null;
                                            break;
                                    }
                                    return this.value;
                                }

                            }
                        },
                        tooltip: {
                            xDateFormat: '%A, %e %b %Y, %H:%M',
                            valueSuffix: ''
                        },
                         
                        series: [{
                                showInLegend: false,
                                name: seriesName,
                                data: seriesData,
                                step: 'left',
                                zoneAxis: 'x',
                                zones: myZonesArray,
                                color: chartColor,
                                fillColor: {
                                    linearGradient: {
                                        x1: 0,
                                        y1: 0,
                                        x2: 0,
                                        y2: 1
                                    },
                                    stops: [
                                        [0, Highcharts.Color(chartColor).setOpacity(0.75).get('rgba')],
                                        [1, Highcharts.Color(chartColor).setOpacity(0.25).get('rgba')]
                                    ]
                                }
                            }]
                   
                    });
                } 
                else 
                {
                    //Disegno del diagramma
                //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts({
                    chartRef = Highcharts.chart('<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer', {
                        credits: {
                            enabled: false
                        },
                        chart: {
                            backgroundColor: 'transparent',
                            type: 'areaspline',
                            events: {
                                load: function () {
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartColorMenuItem").trigger('chartCreated');
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartPlaneColorMenuItem").trigger('chartCreated');
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartLabelsColorMenuItem").trigger('chartCreated');
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartAxesColorMenuItem").trigger('chartCreated');
                                }
                            }
                        },
                        plotOptions: {
                            spline: {
                                
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
                            className: 'timeTrendXAxis',
                            lineColor: chartAxesColor,
                         /*   title:
                                {
                                    enabled: true,
                                    text: "Time - zone: " + timeZone,
                                    style: {
                                        fontFamily: 'Montserrat',
                                        color: chartLabelsFontColor,
                                        fontSize: fontSize + "px"
                                    }
                                },  */
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
                        yAxis: {
                            title: {
                                text: ''
                            },
                            min: minValue,
                            max: maxValue,
                        //    tickInterval: nInterval,
                            plotLines: plotLinesArray,
                            lineColor: chartAxesColor,
                            lineWidth: 1,
                            className: 'timeTrendYAxis',
                            gridLineColor: gridLineColor,
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
                        tooltip: 
                        {
                            xDateFormat: '%A, %e %b %Y, %H:%M',
                            valueSuffix: ''
                        },
                        series: [{
                                showInLegend: false,
                                name: seriesName,
                                data: seriesData,
                                color: chartColor,
                                fillColor: {
                                    linearGradient: {
                                        x1: 0,
                                        y1: 0,
                                        x2: 0,
                                        y2: 1
                                    },
                                    stops: [
                                        [0, Highcharts.Color(chartColor).setOpacity(0.75).get('rgba')],
                                        [1, Highcharts.Color(chartColor).setOpacity(0.25).get('rgba')]
                                    ]
                                }
                            }]
                    });
                }
            }
            else
            {
                showWidgetContent(widgetName);
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
            }

         /*   if ($("#" + widgetName + "_content").css("display") == "none") {
                $("#" + widgetName + "_content").css("display", "block");
            }*/
            showWidgetContent(widgetName);

        }
        
        function convertDataFromSmToDm(originalData, field)
        {
            var singleOriginalData, singleData, convertedDate = null;
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
                                            IdMetric_data: null, //Si può lasciare null, non viene usato dal widget
                                            computationDate: null,
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
                                if (localDateTime == "") {
                                    singleData.commit.author.computationDate = convertedDate;
                                } else {
                                    singleData.commit.author.computationDate = localDateTime;
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

                            return convertedData;
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
        
        function convertDataFromPersonalDataToDm(originalData)
        {
            var singleOriginalData, singleData, convertedDate = null;
            var convertedData = {
                data: []
            };

            for(var i = 0; i < originalData.length; i++)
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
                        range_dates: 0//Si può lasciare null, non viene usato dal widget
                    }
                };

                singleOriginalData = originalData[i];

                convertedDate = new Date(singleOriginalData.dataTime); //2001-11-23 03:08:46
                convertedDate = convertedDate.getFullYear() + "-" + parseInt(convertedDate.getMonth() + 1) + "-" + convertedDate.getDate() + " " + convertedDate.getHours() + ":" + convertedDate.getMinutes() + ":" + convertedDate.getSeconds();

                singleData.commit.author.computationDate = convertedDate;

                if(!isNaN(parseFloat(singleOriginalData.variableValue)))
                {
                    singleData.commit.author.value = parseFloat(singleOriginalData.variableValue);
                }
                else
                {
                    singleData.commit.author.value = singleOriginalData.variableValue;
                }

                convertedData.data.push(singleData);
            }

            return convertedData;
        }

        function convertDataFromTimeNavToDm(originalData, field)
        {
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

        function zeroSpanDateAndHour(number) {

        }

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

        function convertFromMomentToTime(momentDate) {
            var momentDateTime = momentDate.format();
          //  momentDateTime = momentDateTime.replace("T", " ");
            var plusIndexLocal = momentDateTime.indexOf("+");
            momentDateTime = momentDateTime.substr(0, plusIndexLocal);
            var convertedDateTime = momentDateTime;
            return convertedDateTime;
        }
        
        function populateWidget(localTimeRange, kpiTracker, timeNavDirection, timeCount, dateInFuture)
        {
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
                        serviceMapTimeRange = "fromTime=4-hour";
                    //    var deltaT = 4 + parseInt(timeCount) * 4;
                    //    serviceMapTimeRange = "fromTime=" + deltaT + "-hour";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(4*timeCount);

                        break;

                    case "1/DAY":
                        serviceMapTimeRange = "fromTime=1-day";
                    //    var deltaT = 1 + parseInt(timeCount);
                    //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(24*timeCount);

                        break;

                    case "7/DAY":
                        serviceMapTimeRange = "fromTime=7-day";
                    //    var deltaT = 7 + parseInt(timeCount) * 7;
                    //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(7*24*timeCount);

                        break;

                    case "30/DAY":
                        serviceMapTimeRange = "fromTime=30-day";
                    //    var deltaT = 30 + parseInt(timeCount) * 30;
                    //    serviceMapTimeRange = "fromTime=" + deltaT + "-day";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(30*24*timeCount);

                        break;     

                    default:
                        serviceMapTimeRange = "fromTime=1-day";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(24*timeCount);

                        break;
                }

                $.ajax({
                    url: "<?= $superServiceMapProxy ?>api/v1/?serviceUri=" + encodeURI(fromGisExternalContentServiceUri) + "&" + serviceMapTimeRange + "&toTime=" + upperTimeLimitISOTrimmed + "&valueName=" + fromGisExternalContentField,
                    type: "GET",
                    data: {},
                    async: true,
                    dataType: 'json',
                    success: function(originalData) 
                    {
                        var convertedData = convertDataFromSmToDm(originalData, fromGisExternalContentField);
                        if(convertedData)
                        {
                            if(convertedData.data.length > 0)
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
                                drawDiagram(convertedData, fromGisExternalContentRange, fromGisExternalContentField, true, localTimeZoneString);
                                upLimit = convertedData.data[0].commit.author.computationDate;
                                if (timeNavCount < 0) {
                                    if (moment(upLimit).isBefore(moment(dateInFuture))) {

                                    } else {
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                    }
                                }
                            }
                            else
                            {
                                showWidgetContent(widgetName);
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                console.log("Dati non disponibili da Service Map");
                            }
                        }
                        else
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
            }
            else
            {
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').hide();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').show();
                
                switch(localTimeRange)
                {
                    case "4 Ore":
                        serviceMapTimeRange = "fromTime=4-hour";
                    //    var deltaT = 4 + parseInt(timeCount) * 4;
                    //    serviceMapTimeRange = "fromTime=" + deltaT.toString() + "-day";
                        globalDiagramRange = "4/HOUR";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(4*timeCount);

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

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(12*timeCount);

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

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(24*timeCount);

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

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(7*24*timeCount);

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

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(30*24*timeCount);

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

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(180*24*timeCount);

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

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(365*24*timeCount);

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

                    default:
                        serviceMapTimeRange = "fromTime=1-day";
                        globalDiagramRange = "1/DAY";

                        upperTimeLimitISOTrimmed = getUpperTimeLimit(24*timeCount);

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
                
                switch(sm_based)
                {
                    case 'yes':

                        $.ajax({
                        //    url: rowParameters + "&" + serviceMapTimeRange + "&valueName=" + sm_field,
                            url: "<?= $superServiceMapProxy ?>" + rowParameters + "&" + serviceMapTimeRange + "&toTime=" + upperTimeLimitISOTrimmed + "&valueName=" + sm_field,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function(originalData)
                            {
                                var convertedData = convertDataFromSmToDm(originalData, sm_field);
                                if(convertedData)
                                {
                                    if(convertedData.data.length > 0)
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
                                        drawDiagram(convertedData, globalDiagramRange, sm_field, true, localTimeZoneString);
                                        upLimit = convertedData.data[0].commit.author.computationDate;
                                        if (timeNavCount < 0) {
                                            if (moment(upLimit).isBefore(moment(dateInFuture))) {

                                            } else {
                                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_nextButton").hide();
                                            }
                                        }
                                    }
                                    else
                                    {
                                        showWidgetContent(widgetName);
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Dati non disponibili da Service Map");
                                    }
                                }
                                else
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
                            data: {
                                "IdMisura": ['<?= escapeForJS($_REQUEST['id_metric']) ?>'], 
                                "time": globalDiagramRange, 
                                "compare": 0,
                                "lowerDateTime": myKPIFromTimeRangeNewTrimmed.replace("T", " "),
                                "upperDateTime": upperTimeLimitISOTrimmed.replace("T", " ")
                            },
                            type: "GET",
                            async: true,
                            dataType: 'json',
                            success: function(metricData) 
                            {   
                                if(metricData.metricType === 'personal')
                                {
                                    needWebSocket = true;
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
                                drawDiagram(metricData, globalDiagramRange, '<?= escapeForJS($_REQUEST['id_metric']) ?>', false, localTimeZoneString);
                                
                                if(needWebSocket)
                                {
                                    openWs();
                                }
                            },
                            error: function(errorData)
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
                        $.ajax({
                            url: "../controllers/myPersonalDataProxy.php?variableName=" + sm_field + "&last=0&" + serviceMapTimeRange,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function (data) 
                            {
                                var convertedData = convertDataFromPersonalDataToDm(data);
                                if(convertedData)
                                {
                                    if(convertedData.data.length > 0)
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
                                        drawDiagram(convertedData, globalDiagramRange, sm_field, true, localTimeZoneString);
                                    }
                                    else
                                    {
                                        showWidgetContent(widgetName);
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Dati non disponibili da Service Map");
                                    }
                                }
                                else
                                {
                                    showWidgetContent(widgetName);
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    console.log("Dati non disponibili da Service Map");
                                }
                            },
                            error: function(errorData)
                            {
                                metricData = null;
                                console.log("Error in data retrieval");
                                console.log(JSON.stringify(errorData));
                                if(firstLoad !== false)
                                {
                                   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                   $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                }
                            }
                        });
                        break;

                    case 'myData':
                    case 'myKPI':
                     //   console.log("KPI Api Call.");

                        if(fromTrackerParams != null && fromTrackerParams != undefined) {
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
                                timeRange: myKPITimeRange
                            },
                            async: true,
                            dataType: 'json',
                            success: function(data) {
                                var stopFlag = 1;
                                var convertedData = convertDataFromMyKpiToDm(data);
                                if(convertedData)
                                {
                                    if(convertedData.data.length > 0)
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
                                        drawDiagram(convertedData, globalDiagramRange, sm_field, true, localTimeZoneString);
                                    }
                                    else
                                    {
                                        showWidgetContent(widgetName);
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        console.log("Dati MyKPI non presenti");
                                    }
                                }
                                else
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
        //Fine definizioni di funzione

        $("#" + widgetName + "_timeTrendPrevBtn").off("click").click(function () {
          //  alert("PREV Clicked!");
            if(timeNavCount == 0) {
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
                    if(rowParameters != null) {
                        if (rowParameters.includes("https:")) {
                            $.ajax({
                                url: "<?=$superServiceMapProxy?>" + urlKBToBeCalled,
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
            populateWidget(timeRange, null, "minus", timeNavCount);
        });

        $("#" + widgetName + "_timeTrendNextBtn").off("click").click(function () {
         //   alert("NEXT Clicked!");
            timeNavCount--;
            if(timeNavCount == 0) {
            //    if (widgetData.params.sm_based == "yes" || fromGisExternalContent === true) {
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
                    if(rowParameters != null) {
                        if (rowParameters.includes("https:")) {
                            $.ajax({
                                url: "<?=$superServiceMapProxy?>" + urlKBToBeCalled,
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
            populateWidget(timeRange, null, "plus", timeNavCount, dataFut);

        });

        $.ajax({
            url: "../controllers/getWidgetParams.php",
            type: "GET",
            data: {
                widgetName: "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>"
            },
            async: true,
            dataType: 'json',
            success: function(widgetData)
            {

                // Hide Next Button at first instantiation
                if(timeNavCount == 0) {
                    if (widgetData.params.sm_based == "yes" || fromGisExternalContent === true) {
                        let urlKBToBeCalled = "";
                        let field = "";
                        let dashboardOrgKbUrl = "<?= $superServiceMapUrlPrefix ?>api/v1/";
                        //let dashboardOrgKbUrl = "https://servicemap.disit.org/WebAppGrafo/api/v1/";
                        if (fromGisExternalContent) {
                            // urlKBToBeCalled = dashboardOrgKbUrl + "?serviceUri=" + encodeURI(fromGisExternalContentServiceUri) + "&valueName=" + fromGisExternalContentField;
                            urlKBToBeCalled = "<?=$superServiceMapProxy?>" + dashboardOrgKbUrl + "?serviceUri=" + encodeURI(fromGisExternalContentServiceUri);
                            field = fromGisExternalContentField;
                        } else {
                            //  urlKBToBeCalled = rowParameters + "&" + "&valueName=" + sm_field;
                            urlKBToBeCalled = "<?=$superServiceMapProxy?>" + widgetData.params.rowParameters;
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
                infoJson = widgetData.params.infoJson;
                
                sm_based = widgetData.params.sm_based;
            //    if ((sm_based === "myKPI" || sm_based === "no") && fromGisExternalContent != true) {
            //    if ((sm_based === "no" || infoJson === "fromTracker") && fromGisExternalContent != true) {
                if (infoJson === "fromTracker" && fromGisExternalContent != true) {
                    $("#" + widgetName + "_timeControlsContainer").hide();
                    $("#" + widgetName + "_titleDiv").css("width", "95%");
                } else {
                    $("#" + widgetName + "_timeControlsContainer").show();
                    $("#" + widgetName + "_titleDiv").css("width", "95%");
                }
                rowParameters = widgetData.params.rowParameters;
                sm_field = widgetData.params.sm_field;
                gridLineColor = widgetData.params.chartPlaneColor;
                chartAxesColor = widgetData.params.chartAxesColor;
                
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
                    widgetTitle = widgetData.params.title_w;
                    widgetHeaderColor = widgetData.params.frame_color_w;
                    widgetHeaderFontColor = widgetData.params.headerFontColor;
                    timeRange = widgetData.params.temporal_range_w;
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
                 //   $("#" + widgetName + "_titleDiv").html(widgetTitle + " On Day: " + dayTracker);
                }
                
                setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);

                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').off('resizeWidgets');
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

                if(firstLoad === false)
                {
                    showWidgetContent(widgetName);
                }
                else
                {
                    setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                }

                //Nuova versione
                if(('<?= sanitizeJsonRelaxed2('styleParameters') ?>' !== "")&&('<?= sanitizeJsonRelaxed2('styleParameters') ?>' !== "null"))
                {
                    styleParameters = JSON.parse('<?= sanitizeJsonRelaxed2('styleParameters') ?>');
                }

                if('<?= sanitizeJsonRelaxed2('parameters') ?>'.length > 0)
                {
                    widgetParameters = JSON.parse('<?= sanitizeJsonRelaxed2('parameters') ?>');
                }

                if(widgetParameters !== null && widgetParameters !== undefined)
                {
                    if(widgetParameters.hasOwnProperty("thresholdObject"))
                    {
                       thresholdObject = widgetParameters.thresholdObject;
                    }
                }

                sizeRowsWidget = parseInt('<?= escapeForJS($_REQUEST['size_rows']) ?>');

                if (timeRange == null || timeRange == undefined) {
                    timeRange = widgetData.params.temporal_range_w;
                }
                populateWidget(timeRange, null, null, timeNavCount);

                // Modify width to show newly implemented PREV and NEXT buttons
            //    if ((sm_based != "myKPI" && sm_based != "no") || fromGisExternalContent === true) {
            //    if ((sm_based != "no" &&  infoJson != "fromTracker") || fromGisExternalContent === true) {
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
                        //console.log("Widget " + widgetTitle + " could not connect to WebSocket");
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
                             /*   webSocket.close();
                                clearInterval(countdownRef);
                                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);*/
                                var newValue = msgObj.newValue;

                                // TIME-ZONE CONVERSION and ADJUSTMENT TO ADD DIRECTLY INTO HIGHCHART SERIES (DEFAULT-UTC HIGHCHARTS MODE)
                                var localTimeZone = moment.tz.guess();
                                var momentDateTime = moment(Date.now());
                                var offset = momentDateTime.utcOffset();
                                var localDteTimeAdj = momentDateTime.tz(localTimeZone).valueOf() + 60000 * offset;

                                chartRef.series[0].addPoint([localDteTimeAdj, newValue], true);

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

                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('customResizeEvent', function(event){
                    resizeWidget();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').highcharts().reflow();
                });

                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").off('updateFrequency');
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('updateFrequency', function(event){
                    clearInterval(countdownRef);
                    timeToReload = event.newTimeToReload;
                    countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId); 
                });
                
                $("#<?= $_REQUEST['name_w'] ?>").off('changeTimeRangeEvent');
                $("#<?= $_REQUEST['name_w'] ?>").on('changeTimeRangeEvent', function(event)
                {
                //    currentWidth = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height();
                 //   $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').hide();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading').show();
                //    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').css('height', currentWidth);
                    timeRange = event.newTimeRange;
                    populateWidget(event.newTimeRange, null, null, 0);
                });

                countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId); 
            },
            error: function(errorData)
            {
        
            }
        });
    });//Fine document ready

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
                    No data available
                </div>
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 
