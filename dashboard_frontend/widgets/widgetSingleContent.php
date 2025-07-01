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
    $(document).ready(function <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w']))?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId)  
    {
        <?php
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetSingleContent.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>
                
        var headerHeight = 25;
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w']))?>";
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var embedWidget = <?= $_REQUEST['embedWidget']=='true' ? 'true' : 'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader = null;
        var wsRetryActive, wsRetryTime = null;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var styleParameters, metricType, originalMetricType, metricName, pattern, udm, udmPos, threshold, thresholdEval, appId, flowId, nrMetricType,
            delta, deltaPerc, sm_field, sizeRowsWidget, sm_based, rowParameters, fontSize, value, countdownRef, widgetTitle, metricData, widgetHeaderColor, 
            widgetHeaderFontColor, widgetOriginalBorderColor, urlToCall, geoJsonServiceData, showHeader, fontSizeRatio, realFontSize, 
            widgetParameters, webSocket, openWs, openWsConn, wsError, manageIncomingWsMsg, wsClosed, chartColor, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor = null;

        var needWebSocket = false;
        var rowParametersUrl = null;

        console.log("Widget Single Content: " + widgetName);
		/////////////
		$(document).off('showSingleContentFromExternalContent_' + widgetName);
        $(document).on('showSingleContentFromExternalContent_' + widgetName, function(event){
		        // console.log('showSingleContentFromExternalContent_AddCode!-CORRECT');
				if(encodeURIComponent(metricName) === encodeURIComponent(metricName))
                    {
                       var newWsValue = event.passedData;
						
						if (newWsValue.dataOperation){
							$("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").css("display", "none");
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").show()
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert").hide()
							$("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date").css("display", "none");
							$("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").css("height", "90%");
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").css("width", $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").width());
							$("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").html(newWsValue.dataOperation);
						}else{
							if (udm != null) {
                            udm = null;
                        }
                        
						//var cont = event.content;
						//sm_field = newWsValue.sm_field;
						useWebSocket = false;
						console.log('newWsValue:');
						console.log(newWsValue);
						

                        if (newWsValue.metricId != null) {
                            rowParameters = JSON.stringify(newWsValue);
                        } else {
                            rowParameters = newWsValue;
                            sm_based = 'no';
                            if (udm != null) {
                                udm = null;
                            }
                        }
                            // clearInterval(countdownRef);
                            // $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                            // <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, null, /*event.randomSingleGeoJsonIndex,*/ event.marker, event.mapRef, event.fakeId);
                            fromGisExternalContent = undefined
                            loadData();
						}

                    }
		});
		////////////
        
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
        
        $(document).off('mouseOverLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            widgetOriginalBorderColor = $("#" + widgetName).css("border-color");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(event.widgetTitle);
            $("#" + widgetName).css("border-color", event.color1);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", event.color1);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w']))?>_header").css("background", "-webkit-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "-o-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "-moz-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", "linear-gradient(to left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("color", "black");
        });
        
        $(document).off('mouseOutLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOutLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("background", widgetHeaderColor);
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_header").css("color", widgetHeaderFontColor);
        });
        
        $(document).off('showLastDataFromExternalContentGis_' + widgetName);
        $(document).on('showLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, null, /*event.randomSingleGeoJsonIndex,*/ event.marker, event.mapRef, event.fakeId);
            }
        });
        
        $(document).off('restoreOriginalLastDataFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content").hide();
                <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(true, metricName, "<?= sanitizeTitle($_REQUEST['title_w']) ?>", "<?= escapeForJS($_REQUEST['frame_color_w']) ?>", "<?= escapeForJS($_REQUEST['headerFontColor']) ?>", false, null, null, null, null, /*null,*/ null, null, null);
            }
        });

        $("#" + widgetName).hover(function()
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
                    var rowParametersUrl = null;

                    if(location.href.includes("index.php") && webLinkD != "" && webLinkD != "none" && webLinkD != null) {
                        $("#" + widgetName).css("cursor", "pointer");
                    }

                },
                error: function()
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

                        if(styleParameters != null) {
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
        
        //Specifiche per questo widget
        var flagNumeric = false;
        var alarmSet = false;
        var udm = "";
        var pattern = /Percentuale\//;
        
        //Definizioni di funzione specifiche del widget
        function populateWidget(showContentOnLoad)
        {
            if (showContentOnLoad != null && showContentOnLoad == "no") {
                return;
            }
            if(metricData !== null)
            {
                if(metricData.data[0] !== 'undefined')
                {
                    if(metricData.data.length > 0)
                    {
                        //Inizio eventuale codice ad hoc basato sui dati della metrica
                        if(firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').hide();
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loadErrorAlert").hide();
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").show();
                        }
                        else
                        {
                            $("#" + widgetName + "_value span").empty();
                            $("#" + widgetName + "_udm span").empty();
                        }
                        
                        metricType = metricData.data[0].commit.author.metricType;
                        threshold = metricData.data[0].commit.author.threshold;
                        thresholdEval = metricData.data[0].commit.author.thresholdEval;

                        if((metricType === "Percentuale") || (pattern.test(metricType)))
                        {
                            if((metricData.data[0].commit.author.value_perc1 !== null) && (metricData.data[0].commit.author.value_perc1 !== "") && (metricData.data[0].commit.author.value_perc1 !== "undefined"))
                            {
                                value = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                if(value > 100)
                                {
                                    value = 100;
                                }
                            }
                            flagNumeric = true;
                        }
                        else
                        {
                            switch(metricType)
                            {
                                case "Intero":
                                    if((metricData.data[0].commit.author.value_num !== null) && (metricData.data[0].commit.author.value_num !== "") && (typeof metricData.data[0].commit.author.value_num !== "undefined"))
                                    {
                                        value = parseInt(metricData.data[0].commit.author.value_num);
                                    }
                                    flagNumeric = true;
                                    break;

                                case "Float":
                                    if((metricData.data[0].commit.author.value_num !== null) && (metricData.data[0].commit.author.value_num !== "") && (typeof metricData.data[0].commit.author.value_num !== "undefined"))
                                    {
                                       value = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1));
                                    }
                                    flagNumeric = true;
                                    break;

                                case "Testuale":
                                    value = metricData.data[0].commit.author.value_text;
                                    break;
                            }
                        }
                        
                        if((metricType === "Testuale") && (value === "-"))
                        {
                            showWidgetContent(widgetName);
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                        }
                        else
                        {
                         //   if(udm !== null && udm != '' && udm != ' ')
                            dateHeight = 0
                            if(metricData.data[0].commit.author.date_time != undefined && styleParameters && styleParameters.showDateObserved == "yes"){
                                let date_time = moment(metricData.data[0].commit.author.date_time).format("L HH:mm:ssZ")
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date span").html(date_time);
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date").show();
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").css("overflow-y", "hidden");
                                sizeDateObserved = styleParameters.sizeDateObserved || fontSize/2
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date span").css('font-size', sizeDateObserved + 'px')
                                dateHeight = $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date").outerHeight()*100/$("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").height()
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date").css('height', dateHeight+"%")

                            }else{
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date").hide()
                            }
                            if(udm !== null && udm != '')
                            {
                               if(udmPos === 'next')
                               {   
                                  if((value !== null) && (value !== "") && (value !== undefined))
                                  {
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").show();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").show();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").hide();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").css("height", 100-dateHeight+"%");             
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").css("alignItems", "center"); 
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").html(value + udm);
                                  }
                                  else
                                  {
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").hide();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").hide();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date").hide();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                     $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                  }
                               }
                               else
                               {
                                  if((value !== null) && (value !== "") && (value !== undefined))
                                  {
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").show();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").show();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").show();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").css("height", 2*(100-dateHeight)/3+"%");
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").html(value);
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").css("height", (100-dateHeight)/3+"%");
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm span").html(udm);
                                  }
                                  else
                                  {
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").hide();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").hide();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date").hide();
                                     $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                     $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                  }
                               }
                            }
                            else
                            {
                                if((value !== null) && (value !== "") && (value !== undefined))
                                {
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").css("display", "none");
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").css("height", 100-dateHeight+"%");
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").html(value);
                                }
                                else
                                {
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                }
                            }
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value").css("color", fontColor);
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").css("color", fontColor);
                            
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value').textfill({
                                maxFontPixels: -20
                            });
                            
                            if(fontSize < parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', '')))
                            {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").css('font-size', fontSize + 'px');
                            }
                            else
                            {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").css('font-size', parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', ''))*0.8);
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date span").css('font-size', parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', ''))*0.4)
                            }
                            
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").css('font-size', parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', ''))*0.45);
                          
                            //Non cancellare, va riadattata appena aggiorneremo la gestione visiva degli allarmi
                            /*if(flagNumeric && (threshold !== null) && (thresholdEval !== null))
                            {
                                delta = Math.abs(value - threshold);
                                //Distinguiamo in base all'operatore di confronto
                                switch(thresholdEval)
                                {
                                   //Allarme attivo se il valore attuale è sotto la soglia
                                   case '<':
                                       if(value < threshold)
                                       {
                                          alarmSet = true;
                                       }
                                       break;

                                   //Allarme attivo se il valore attuale è sopra la soglia
                                   case '>':
                                       if(value > threshold)
                                       {
                                          alarmSet = true;
                                       }
                                       break;

                                   //Allarme attivo se il valore attuale è uguale alla soglia (errore sui float = 0.1% la distanza dalla soglia rispetto alla soglia stessa)
                                   case '=':
                                       deltaPerc = (delta / threshold)*100;
                                       if(deltaPerc < 0.01)
                                       {
                                           alarmSet = true;
                                       }
                                       break;    

                                   //Non gestiamo altri operatori 
                                   default:
                                       break;
                                }
                            }*/
                        }
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        if(firstLoad !== false)
                        {
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                        }
                    }
                }
                else
                {
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                    }
                } 
            }
            else
            {
                showWidgetContent(widgetName);
                if(firstLoad !== false)
                {
                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                }
            }

            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_titleDiv").html(widgetTitle);
        }
        
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value').textfill({
                maxFontPixels: -20
            });

            if(fontSize < parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', '')))
            {
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").css('font-size', fontSize + 'px');
            }
            else
            {
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").css('font-size', parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', ''))*0.8);
            }

            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").css('font-size', parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', ''))*0.45);
	}

	    function loadData() {
			
            if (rowParameters != null && rowParameters != '') {
                if (IsJsonString(rowParameters)) {
                    if (JSON.parse(rowParameters).metricHighLevelType == "Sensor") {
                        sm_based = "yes";
                    } else if (JSON.parse(rowParameters).metricHighLevelType == "MyKPI") {
                        sm_based = "myKPI";
                    } else if (JSON.parse(rowParameters).metricHighLevelType == "MyData") {
                        sm_based = "myData";
                    }
                    sm_field = JSON.parse(rowParameters).metricType;
                }
            }
			
            if (fromGisExternalContent) {
                urlToCall = "<?= $superServiceMapProxy; ?>api/v1/?serviceUri=" + encodeServiceUri(fromGisExternalContentServiceUri) + "&format=json";      // PANTALEO - DA METTERE SUPERSERVICEMAP ??

                $.ajax({
                    url: urlToCall,
                    type: "GET",
                    data: {},
                    async: true,
                    dataType: 'json',
                    success: function (geoJsonServiceData) {
                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').hide();
                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').show();

                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').off('click');
                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').click(function () {
                            if ($(this).attr('data-onMap') === 'false') {
                                if (fromGisMapRef.hasLayer(fromGisMarker)) {
                                    fromGisMarker.fire('click');
                                } else {
                                    fromGisMapRef.addLayer(fromGisMarker);
                                    fromGisMarker.fire('click');
                                }
                                $(this).attr('data-onMap', 'true');
                                $(this).html('near_me');
                                $(this).css('color', 'white');
                                $(this).css('text-shadow', '2px 2px 4px black');
                            } else {
                                fromGisMapRef.removeLayer(fromGisMarker);
                                $(this).attr('data-onMap', 'false');
                                $(this).html('navigation');
                                $(this).css('color', '#337ab7');
                                $(this).css('text-shadow', 'none');
                            }
                        });

                        metricData = {
                            "data": [
                                {
                                    "commit": {
                                        "author": {
                                            "IdMetric_data": fromGisExternalContentField,
                                            "computationDate": null,
                                            "value_num": null,
                                            "value_perc1": null,
                                            "value_perc2": null,
                                            "value_perc3": null,
                                            "value_text": null,
                                            "quant_perc1": null,
                                            "quant_perc2": null,
                                            "quant_perc3": null,
                                            "tot_perc1": null,
                                            "tot_perc2": null,
                                            "tot_perc3": null,
                                            "series": null,
                                            "descrip": fromGisExternalContentField,
                                            "metricType": null,
                                            "threshold": null,
                                            "thresholdEval": null,
                                            "field1Desc": null,
                                            "field2Desc": null,
                                            "field3Desc": null,
                                            "hasNegativeValues": "1",
                                            "date_time": null
                                        }
                                    }
                                }
                            ]
                        };

                        var fatherNode = null;
                        if (geoJsonServiceData.hasOwnProperty("BusStop")) {
                            fatherNode = geoJsonServiceData.BusStop;
                        } else {
                            if (geoJsonServiceData.hasOwnProperty("Sensor")) {
                                fatherNode = geoJsonServiceData.Sensor;
                            } else {
                                //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                fatherNode = geoJsonServiceData.Service;
                            }
                        }

                        var serviceProperties = fatherNode.features[0].properties;
                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");

                        var numberPattern = /^-?\d*\.?\d+$/;
                        var integerPattern = /^[+\-]?\d+$/;
                        if (numberPattern.test(geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value)) {
                            if (integerPattern.test(geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value)) {
                                metricData.data[0].commit.author.value_num = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                                metricData.data[0].commit.author.metricType = "Intero";
                            } else {
                                metricData.data[0].commit.author.value_num = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                                metricData.data[0].commit.author.metricType = "Float";
                            }
                        } else {
                            metricData.data[0].commit.author.value_text = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                            metricData.data[0].commit.author.metricType = "Testuale";
                        }
                        if (fromGisExternalContentField != "dateObserved" && geoJsonServiceData.realtime.results.bindings[0].dateObserved.value){
                            metricData.data[0].commit.author.date_time = geoJsonServiceData.realtime.results.bindings[0].dateObserved.value
                        }
                        if (serviceProperties.realtimeAttributes[fromGisExternalContentField].value_unit) {
                            udm = serviceProperties.realtimeAttributes[fromGisExternalContentField].value_unit;
                        }
                    },
                    error: function (errorData) {
                        console.log("Error in data retrieval");
                        console.log(JSON.stringify(errorData));
                    },
                    complete: function () {
                        populateWidget();
                    }
                });
            } else {
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').hide();
                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').show();

                if (rowParameters != null && rowParameters != '') {
                    if (IsJsonString(rowParameters)) {
                        if ((JSON.parse(rowParameters).metricId != null)) {
                            rowParametersUrl = encodeServiceUri(JSON.parse(rowParameters).metricId);
                        } else {
                            rowParametersUrl = encodeServiceUri(rowParameters);
                        }
                    } else {
                        rowParametersUrl = encodeServiceUri(rowParameters);
                    }
                } else {
                    rowParametersUrl = encodeServiceUri(rowParameters);
                }
		// console.log('sm_based: '+sm_based);
                switch (sm_based) {
                    case 'yes':
					
                        $.ajax({
                            url: "<?= $superServiceMapProxy?>" + rowParametersUrl,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function (data) {
                                if (data.Service) {
                                    var originalMetricType = data.Service.features[0].properties.realtimeAttributes[sm_field].data_type;
                                    if (udm == null || udm == '') {
                                        if (data.Service.features[0].properties.realtimeAttributes[sm_field] != null) {
                                            udm = data.Service.features[0].properties.realtimeAttributes[sm_field].value_unit;
                                        }
                                    }
                                } else if (data.Sensor) {
                                    var originalMetricType = data.Sensor.features[0].properties.realtimeAttributes[sm_field].data_type;
                                    if (udm == null || udm == '') {
                                        if (data.Sensor.features[0].properties.realtimeAttributes[sm_field] != null) {
                                            udm = data.Sensor.features[0].properties.realtimeAttributes[sm_field].value_unit;
                                        }
                                    }
                                }

                                metricData = {
                                    data: [
                                        {
                                            commit: {
                                                author: {
                                                    IdMetric_data: sm_field,
                                                    computationDate: null,
                                                    value_num: null,
                                                    value_perc1: null,
                                                    value_perc2: null,
                                                    value_perc3: null,
                                                    value_text: null,
                                                    quant_perc1: null,
                                                    quant_perc2: null,
                                                    quant_perc3: null,
                                                    tot_perc1: null,
                                                    tot_perc2: null,
                                                    tot_perc3: null,
                                                    series: null,
                                                    descrip: sm_field,
                                                    metricType: null,
                                                    threshold: null,
                                                    thresholdEval: null,
                                                    field1Desc: null,
                                                    field2Desc: null,
                                                    field3Desc: null,
                                                    hasNegativeValues: "1",
                                                    date_time: null
                                                }
                                            }
                                        }
                                    ]
                                };

                                switch (originalMetricType) {
                                    case "float":
                                        metricData.data[0].commit.author.metricType = "Float";
                                        metricData.data[0].commit.author.value_num = parseFloat(data.realtime.results.bindings[0][sm_field].value);
                                        break;

                                    case "integer":
                                        metricData.data[0].commit.author.metricType = "Intero";
                                        metricData.data[0].commit.author.value_num = parseInt(data.realtime.results.bindings[0][sm_field].value);
                                        break;

                                    default:
                                        metricData.data[0].commit.author.metricType = "Testuale";
                                        if (metricData.data[0].commit.author.value_text = data.realtime.results.bindings[0][sm_field] != null) {
                                            metricData.data[0].commit.author.value_text = data.realtime.results.bindings[0][sm_field].value;
                                        }
                                        break;
                                }

                                if(sm_field != "dateObserved" && data.realtime.results.bindings[0].dateObserved.value != ""){
                                    metricData.data[0].commit.author.date_time = data.realtime.results.bindings[0].dateObserved.value
                                }

                                $("#" + widgetName + "_loading").css("display", "none");
                                $("#" + widgetName + "_content").css("display", "block");
                                var key = getQueryString()["entityId"];
                                if (key == null) {
                                    populateWidget();
                                } else {
                                    populateWidget((styleParameters != null && styleParameters.showContentLoadM != null) ? styleParameters.showContentLoadM : null);
                                }
                            },
                            error: function (errorData) {
                                metricData = null;
                                console.log("Error in data retrieval");
                                console.log(JSON.stringify(errorData));
                                if (firstLoad !== false) {
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                }
                            }
                        });
                        break;

                    case 'no':
                        $.ajax({
                            url: getMetricDataUrl,
                            type: "GET",
                            data: {"IdMisura": ["<?= escapeForJS($_REQUEST['id_metric']) ?>"]},
                            async: true,
                            dataType: 'json',
                            success: function (data) {
                                metricData = data;
                                
                                if (metricData.data != null && metricData.data.length > 0) {
                                    needWebSocket = metricData.data[0].needWebSocket;
                                    if(metricData.data[0].commit.author.computationDate && metricData.data[0].commit.author.computationDate != "")
                                        metricData.data[0].commit.author.date_time = metricData.data[0].commit.author.computationDate
                                }
                                $("#" + widgetName + "_loading").css("display", "none");
                                $("#" + widgetName + "_content").css("display", "block");
                                populateWidget();

                            /*    if (needWebSocket) {
                                    if (webSocket == null) {
                                        openWs();
                                    }
                                }   */
                            },
                            error: function (errorData) {
                                metricData = null;
                                console.log("Error in data retrieval");
                                console.log(JSON.stringify(errorData));
                                if (firstLoad !== false) {
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                }
                            }
                        });
                        break;

                    case 'myPersonalData':
                        $.ajax({
                            url: "../controllers/myPersonalDataProxy.php?variableName=" + sm_field + "&last=1",
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function (data) {
                                if (data[0]) {
                                    if (parseFloat(data[0].variableValue) !== 'NaN') {
                                        originalMetricType = 'float';
                                    } else {
                                        if (parseInt(data[0].variableValue) !== 'NaN') {
                                            originalMetricType = 'integer';
                                        } else {
                                            originalMetricType = 'string';
                                        }
                                    }

                                        udm = data[0].variableUnit;

                                    metricData = {
                                        data: [
                                            {
                                                commit: {
                                                    author: {
                                                        IdMetric_data: sm_field,
                                                        computationDate: null,
                                                        value_num: null,
                                                        value_perc1: null,
                                                        value_perc2: null,
                                                        value_perc3: null,
                                                        value_text: null,
                                                        quant_perc1: null,
                                                        quant_perc2: null,
                                                        quant_perc3: null,
                                                        tot_perc1: null,
                                                        tot_perc2: null,
                                                        tot_perc3: null,
                                                        series: null,
                                                        descrip: sm_field,
                                                        metricType: null,
                                                        threshold: null,
                                                        thresholdEval: null,
                                                        field1Desc: null,
                                                        field2Desc: null,
                                                        field3Desc: null,
                                                        hasNegativeValues: "1"
                                                    }
                                                }
                                            }
                                        ]
                                    };

                                    switch (originalMetricType) {
                                        case "float":
                                            metricData.data[0].commit.author.metricType = "Float";
                                            metricData.data[0].commit.author.value_num = parseFloat(data[0].variableValue);
                                            break;

                                        case "integer":
                                            metricData.data[0].commit.author.metricType = "Intero";
                                            metricData.data[0].commit.author.value_num = parseInt(data[0].variableValue);
                                            break;

                                        default:
                                            metricData.data[0].commit.author.metricType = "Testuale";
                                            metricData.data[0].commit.author.value_text = data[0].variableValue;
                                            break;
                                    }

                                    $("#" + widgetName + "_loading").css("display", "none");
                                    $("#" + widgetName + "_content").css("display", "block");
                                    populateWidget();
                                } else {
                                    metricData = null;
                                    console.log("Error in data retrieval");
                                    if (firstLoad !== false) {
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                }
                            },
                            error: function (errorData) {
                                metricData = null;
                                console.log("Error in data retrieval");
                                console.log(JSON.stringify(errorData));
                                if (firstLoad !== false) {
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                }
                            }
                        });
                        break;

                    case 'myData':
                    case 'myKPI':
                        if (rowParametersUrl.includes("datamanager/api/v1/poidata/")) {
                            rowParametersUrl = rowParametersUrl.split("datamanager/api/v1/poidata/")[1];
                        }
                        $.ajax({
                            url: "../controllers/myKpiProxy.php?",
                            type: "GET",
                            data: {
                                myKpiId: rowParametersUrl,
                                action: "getValueUnit",
                                last: 1
                            },
                            async: true,
                            dataType: 'json',
                            success: function (data) {
                                if (data[0]) {
                                    if (parseFloat(data[0].value) !== 'NaN') {
                                        originalMetricType = 'float';
                                    } else {
                                        if (parseInt(data[0].value) !== 'NaN') {
                                            originalMetricType = 'integer';
                                        } else {
                                            originalMetricType = 'string';
                                        }
                                    }
                                    if (udm == null || udm == '') {
                                        if (data[0].valueUnit != null && data[0].valueUnit != "null") {
                                            udm = data[0].valueUnit;
                                        }
                                    }

                                    metricData = {
                                        data: [
                                            {
                                                commit: {
                                                    author: {
                                                        IdMetric_data: sm_field,
                                                        computationDate: null,
                                                        value_num: null,
                                                        value_perc1: null,
                                                        value_perc2: null,
                                                        value_perc3: null,
                                                        value_text: null,
                                                        quant_perc1: null,
                                                        quant_perc2: null,
                                                        quant_perc3: null,
                                                        tot_perc1: null,
                                                        tot_perc2: null,
                                                        tot_perc3: null,
                                                        series: null,
                                                        descrip: sm_field,
                                                        metricType: null,
                                                        threshold: null,
                                                        thresholdEval: null,
                                                        field1Desc: null,
                                                        field2Desc: null,
                                                        field3Desc: null,
                                                        hasNegativeValues: "1",
                                                        date_time: null
                                                    }
                                                }
                                            }
                                        ]
                                    };

                                    switch (originalMetricType) {
                                        case "float":
                                            metricData.data[0].commit.author.metricType = "Float";
                                            metricData.data[0].commit.author.value_num = parseFloat(data[0].value);
                                            break;

                                        case "integer":
                                            metricData.data[0].commit.author.metricType = "Intero";
                                            metricData.data[0].commit.author.value_num = parseInt(data[0].value);
                                            break;

                                        default:
                                            metricData.data[0].commit.author.metricType = "Testuale";
                                            metricData.data[0].commit.author.value_text = data[0].value;
                                            break;
                                    }
                                    if(data[0].date_time && data[0].date_time != ""){
                                        metricData.data[0].commit.author.date_time = data[0].date_time;
                                    } else if (data[0].dataTime && data[0].dataTime != "") {
                                        metricData.data[0].commit.author.date_time = data[0].dataTime;
                                    }
                                    $("#" + widgetName + "_loading").css("display", "none");
                                    $("#" + widgetName + "_content").css("display", "block");
                                    populateWidget();
                                } else {
                                    metricData = null;
                                    console.log("Error in data retrieval");
                                    if (firstLoad !== false) {
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                }
                            },
                            error: function (errorData) {
                                metricData = null;
                                console.log("Error in data retrieval");
                                console.log(JSON.stringify(errorData));
                                if (firstLoad !== false) {
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                    $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                }
                            }
                        });
                        break;
                }
            }
        }
        //Fine definizioni di funzione

        //Nuova versione
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
                sm_based = widgetData.params.sm_based;
                rowParameters = widgetData.params.rowParameters;
                sm_field = widgetData.params.sm_field;

                if (nrMetricType != null && webSocket == null) {
                    openWs(widgetName);
                }

                if (rowParameters != null && rowParameters != '') {
                    if (IsJsonString(rowParameters)) {
                        if (JSON.parse(rowParameters).metricHighLevelType == "Sensor") {
                            sm_based = "yes";
                        } else if (JSON.parse(rowParameters).metricHighLevelType == "MyKPI") {
                            sm_based = "myKPI";
                        }
                        sm_field = JSON.parse(rowParameters).metricType;
                    }
                }

                if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
                {
                    showHeader = false;
                }
                else
                {
                    showHeader = true;
                }

                if (widgetData.params.styleParameters != null && JSON.parse(widgetData.params.styleParameters) != null) {
                    styleParameters = JSON.parse(widgetData.params.styleParameters);
                }

                if(widgetData.params.parameters && JSON.parse(widgetData.params.parameters) != null) {
                    widgetParameters = JSON.parse(widgetData.params.parameters);
                }

                if (widgetData.params.udm != null) {
                    udm = widgetData.params.udm;
                }

                if(widgetData.params.udmPos) {
                    udmPos = widgetData.params.udmPos;
                }

                if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                {
                    metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
                    widgetTitle = widgetData.params.title_w;
                    widgetHeaderColor = widgetData.params.frame_color_w;
                    widgetHeaderFontColor = widgetData.params.headerFontColor;
                    udm = widgetData.params.udm;
                    udmPos = widgetData.params.udmPos;
                    sizeRowsWidget = parseInt(widgetData.params.size_rows);
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

                loadData();

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
                },
                error: function(errorData)
                {
                    
                }
        });
        
        
        //Web socket 
        openWs = function(widgetName)
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
                    echo 'wsRetryActive = "' . $wsRetryActive . '";'."\n";
                    echo 'wsRetryTime = ' . $wsRetryTime . ';'."\n";
                    echo 'wsUrl="' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '";'."\n";
                ?>
                //webSocket = new WebSocket(wsUrl);
                webSocket=null;
                initWebsocket(widgetName, wsUrl, null, wsRetryTime*1000, function(socket){
                 //   console.log('socket initialized!');
                    //do something with socket...
                    webSocket = socket;
                    openWsConn();
                }, function(){
                 //   console.log('init of socket on failed!');
                });                                          
                /*webSocket.addEventListener('open', openWsConn);
                webSocket.addEventListener('close', wsClosed);*/
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
                        if (udm != null) {
                            udm = null;
                        }
                        var newWsValue = msgObj.newValue;

                        if (newWsValue.metricId != null) {
                            rowParameters = JSON.stringify(newWsValue);
                        } else {
                            rowParameters = newWsValue;
                            sm_based = 'no';
                            if (udm != null) {
                                udm = null;
                            }
                        }
                        loadData();

                    /*    if (metricType === 'Float') {
                            newWsValue = parseFloat(newWsValue).toFixed(1);
                        }

                        if (udm !== null) {
                            if (udmPos === 'next') {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").html(newWsValue + udm);
                            } else {
                                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").html(newWsValue);
                            }
                        } else {
                            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").html(newWsValue);
                        }*/

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
                  webSocket.removeEventListener('open', openWsConn);
                  webSocket.removeEventListener('message', manageIncomingWsMsg);
                  webSocket.close();
                  webSocket = null;
              }, (timeToReload - 2)*1000);

            webSocket.addEventListener('message', manageIncomingWsMsg);
        };

        wsClosed = function(e)
        {
            if (webSocket != null) {
                webSocket.removeEventListener('close', wsClosed);
                webSocket.removeEventListener('open', openWsConn);
                webSocket.removeEventListener('message', manageIncomingWsMsg);
                webSocket = null;
                if (wsRetryActive === 'yes') {
                    setTimeout(openWs, parseInt(wsRetryTime * 1000));
                }
            }
        };
        
        //Per ora non usata
        wsError = function(e)
        {
            
        };
        
        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
        
        $(document).on('resizeHighchart_' + widgetName, function(event)
        {
            showHeader = event.showHeader;
        });
        
        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").off('updateFrequency');
        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('updateFrequency', function(event){
                clearInterval(countdownRef);
                timeToReload = event.newTimeToReload;
                countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);
        });
        
        countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);

        function initWebsocket(widget, url, existingWebsocket, retryTimeMs, success, failed) {
            if (!existingWebsocket || existingWebsocket.readyState != existingWebsocket.OPEN) {
                if (existingWebsocket) {
                    existingWebsocket.close();
                }
                var websocket = new WebSocket(url);
                websocket.widget = widget;
                console.log("store websocket for "+widget)
            //    Window.webSockets[widget] = websocket;
                websocket.onopen = function () {
                    console.info('websocket opened! url: ' + url);
                    success(websocket);
                };
                websocket.onclose = function () {
                    console.info('websocket closed! url: ' + url + " reconnect in "+retryTimeMs+"ms");
                    //reconnect after a retryTime
                    setTimeout(function(){
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
/*
  function initWebsocket(url, existingWebsocket, timeoutMs, numberOfRetries) {
    timeoutMs = timeoutMs ? timeoutMs : 1500;
    numberOfRetries = numberOfRetries ? numberOfRetries : 0;
    var hasReturned = false;
    var promise = new Promise((resolve, reject) => {
        setTimeout(function () {
            if(!hasReturned) {
                console.info('opening websocket timed out: ' + url);
                rejectInternal();
            }
        }, timeoutMs);
        if (!existingWebsocket || existingWebsocket.readyState != existingWebsocket.OPEN) {
            if (existingWebsocket) {
                existingWebsocket.close();
            }
            var websocket = new WebSocket(url);
            websocket.onopen = function () {
                if(hasReturned) {
                    websocket.close();
                } else {
                    console.info('websocket to opened! url: ' + url);
                    resolve(websocket);
                }
            };
            websocket.onclose = function () {
                console.info('websocket closed! url: ' + url);
                rejectInternal();
            };
            websocket.onerror = function () {
                console.info('websocket error! url: ' + url);
                rejectInternal();
            };
        } else {
            resolve(existingWebsocket);
        }

        function rejectInternal() {
            if(numberOfRetries <= 0) {
                reject();
            } else if(!hasReturned) {
                hasReturned = true;
                console.info('retrying connection to websocket! url: ' + url + ', remaining retries: ' + (numberOfRetries-1));
                initWebsocket(url, null, timeoutMs, numberOfRetries-1).then(resolve, reject);
            }
        }
    });
    promise.then(function () {hasReturned = true;}, function () {hasReturned = true;});
    return promise;
};     */
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
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer" class="chartContainer">
                <div id='<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_date' class="singleContentDate" style="text-align: center; padding-top: 5px;"><span></span></div>
                <div id='<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value' class="singleContentValue"><span></span></div> 
                <div id='<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm' class="singleContentUdm"><span></span></div>
            </div>
        </div>
    </div>	
</div> 
