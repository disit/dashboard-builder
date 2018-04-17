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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId)  
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_REQUEST['title_w'];
        ?>
                
        var headerHeight = 25;
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= $_REQUEST['color_w'] ?>';
        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var fontColor = "<?= $_REQUEST['fontColor'] ?>";
        var timeToReload = <?= $_REQUEST['frequency_w'] ?>;
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
        var showHeader = null;
        var wsRetryActive, wsRetryTime = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var widgetProperties, styleParameters, metricType, metricName, pattern, udm, udmPos, threshold, thresholdEval, 
            delta, deltaPerc, sizeRowsWidget, fontSize, value, metricType, countdownRef, widgetTitle, metricData, widgetHeaderColor, 
            widgetHeaderFontColor, widgetOriginalBorderColor, urlToCall, geoJsonServiceData, showHeader, fontSizeRatio, realFontSize, 
            widgetParameters, webSocket, openWs, openWsConn, wsError, manageIncomingWsMsg, wsClosed = null;
        
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
            metricName = "<?= $_REQUEST['id_metric'] ?>";
            widgetTitle = "<?= preg_replace($titlePatterns, $replacements, $title) ?>";
            widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
            widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>"; 
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
        
        $(document).off('changeMetricFromButton_' + widgetName);
        $(document).on('changeMetricFromButton_' + widgetName, function(event) 
        {
            if((event.targetWidget === widgetName) && (event.newMetricName !== "noMetricChange"))
            {
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, event.newMetricName, event.newTargetTitle, event.newHeaderAndBorderColor, event.newHeaderFontColor, false, null, null, null, null, null, null);
            }
        });
        
        $(document).off('mouseOverLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOverLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            widgetOriginalBorderColor = $("#" + widgetName).css("border-color");
            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(event.widgetTitle);
            $("#" + widgetName).css("border-color", event.color1);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", event.color1);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "-webkit-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "-o-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "-moz-linear-gradient(left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", "linear-gradient(to left, " + event.color1 + ", " + event.color2 + ")");
            $("#<?= $_REQUEST['name_w'] ?>_header").css("color", "black");
        });
        
        $(document).off('mouseOutLastDataFromExternalContentGis_' + widgetName);
        $(document).on('mouseOutLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);
            $("#" + widgetName).css("border-color", widgetOriginalBorderColor);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("background", widgetHeaderColor);
            $("#<?= $_REQUEST['name_w'] ?>_header").css("color", widgetHeaderFontColor);
        });
        
        $(document).off('showLastDataFromExternalContentGis_' + widgetName);
        $(document).on('showLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, metricName, event.widgetTitle, event.color1, "black", true, event.serviceUri, event.field, null, /*event.randomSingleGeoJsonIndex,*/ event.marker, event.mapRef, event.fakeId);
            }
        });
        
        $(document).off('restoreOriginalLastDataFromExternalContentGis_' + widgetName);
        $(document).on('restoreOriginalLastDataFromExternalContentGis_' + widgetName, function(event) 
        {
            if(event.targetWidget === widgetName)
            {
                clearInterval(countdownRef); 
                $("#<?= $_REQUEST['name_w'] ?>_content").hide();
                <?= $_REQUEST['name_w'] ?>(true, metricName, "<?= preg_replace($titlePatterns, $replacements, $title) ?>", "<?= $_REQUEST['frame_color_w'] ?>", "<?= $_REQUEST['headerFontColor'] ?>", false, null, null, null, null, /*null,*/ null, null, null);
            }
        });
        
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        elToEmpty.css("font-family", "Verdana");
        var url = "<?= $_REQUEST['link_w'] ?>";
        
        //Specifiche per questo widget
        var flagNumeric = false;
        var alarmSet = false;
        var udm = "";
        var pattern = /Percentuale\//;
        
        //Definizioni di funzione specifiche del widget
        //Restituisce il JSON delle soglie se presente, altrimenti NULL
        function getThresholdsJson()
        {
            var thresholdsJson = null;
            if(jQuery.parseJSON(widgetProperties.param.parameters !== null))
            {
                thresholdsJson = widgetProperties.param.parameters; 
            }
            
            return thresholdsJson;
        }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getInfoJson()
        {
            var infoJson = null;
            if(jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson); 
            }
            
            return infoJson;
        }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getStyleParameters()
        {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        
        function populateWidget()
        {
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
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
                            $("#<?= $_REQUEST['name_w'] ?>_loadErrorAlert").hide();
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
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
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                        }
                        else
                        {
                            if(udm !== null)
                            {
                               if(udmPos === 'next')
                               {   
                                  if((value !== null) && (value !== "") && (value !== undefined))
                                  {
                                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                     $("#<?= $_REQUEST['name_w'] ?>_value").show();
                                     $("#<?= $_REQUEST['name_w'] ?>_udm").hide();
                                     $("#<?= $_REQUEST['name_w'] ?>_value").css("height", "100%");             
                                     $("#<?= $_REQUEST['name_w'] ?>_value").css("alignItems", "center"); 
                                     $("#<?= $_REQUEST['name_w'] ?>_value span").html(value + udm);
                                  }
                                  else
                                  {
                                     $("#<?= $_REQUEST['name_w'] ?>_value").hide();
                                     $("#<?= $_REQUEST['name_w'] ?>_udm").hide(); 
                                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                     $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                  }
                               }
                               else
                               {
                                  if((value !== null) && (value !== "") && (value !== undefined))
                                  {
                                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
                                     $("#<?= $_REQUEST['name_w'] ?>_value").show();
                                     $("#<?= $_REQUEST['name_w'] ?>_udm").show();
                                     $("#<?= $_REQUEST['name_w'] ?>_value").css("height", "60%");
                                     $("#<?= $_REQUEST['name_w'] ?>_value span").html(value);
                                     $("#<?= $_REQUEST['name_w'] ?>_udm").css("height", "40%");
                                     $("#<?= $_REQUEST['name_w'] ?>_udm span").html(udm);
                                  }
                                  else
                                  {
                                     $("#<?= $_REQUEST['name_w'] ?>_value").hide();
                                     $("#<?= $_REQUEST['name_w'] ?>_udm").hide();
                                     $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                     $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                  }
                               }
                            }
                            else
                            {
                                if((value !== null) && (value !== "") && (value !== undefined))
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_udm").css("display", "none");
                                    $("#<?= $_REQUEST['name_w'] ?>_value").css("height", "100%");
                                    $("#<?= $_REQUEST['name_w'] ?>_value span").html(value);
                                }
                                else
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                }
                            }

                            $("#<?= $_REQUEST['name_w'] ?>_value").css("color", fontColor);
                            $("#<?= $_REQUEST['name_w'] ?>_udm").css("color", fontColor);
                            
                            $('#<?= $_REQUEST['name_w'] ?>_value').textfill({
                                maxFontPixels: -20
                            });
                            
                            if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_value span').css('font-size').replace('px', '')))
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_value span").css('font-size', fontSize + 'px');
                            }
                            else
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_value span").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_value span').css('font-size').replace('px', ''))*0.8);
                            }
                            
                            $("#<?= $_REQUEST['name_w'] ?>_udm").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_value span').css('font-size').replace('px', ''))*0.45);
                          
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
                            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                        }
                    }
                }
                else
                {
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    }
                } 
            }
            else
            {
                showWidgetContent(widgetName);
                if(firstLoad !== false)
                {
                    $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                }
            } 
        }
        
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            $('#<?= $_REQUEST['name_w'] ?>_value').textfill({
                maxFontPixels: -20
            });

            if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_value span').css('font-size').replace('px', '')))
            {
                $("#<?= $_REQUEST['name_w'] ?>_value span").css('font-size', fontSize + 'px');
            }
            else
            {
                $("#<?= $_REQUEST['name_w'] ?>_value span").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_value span').css('font-size').replace('px', ''))*0.8);
            }

            $("#<?= $_REQUEST['name_w'] ?>_udm").css('font-size', parseInt($('#<?= $_REQUEST['name_w'] ?>_value span').css('font-size').replace('px', ''))*0.45);
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
        
        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);
        
        //Nuova versione
        if(('<?= $_REQUEST['styleParameters'] ?>' !== "")&&('<?= $_REQUEST['styleParameters'] ?>' !== "null"))
        {
            styleParameters = JSON.parse('<?= $_REQUEST['styleParameters'] ?>');
        }
        
        if('<?= $_REQUEST['parameters'] ?>'.length > 0)
        {
            widgetParameters = JSON.parse('<?= $_REQUEST['parameters'] ?>');
        }
        
        udm = "<?= $_REQUEST['udm'] ?>";
        udmPos = "<?= $_REQUEST['udmPos'] ?>";
        sizeRowsWidget = parseInt("<?= $_REQUEST['size_rows'] ?>");
        
        if(fromGisExternalContent)
        {
            if((fromGisFakeId !== null) && (fromGisFakeId !== 'null') && (fromGisFakeId !== undefined))
            {
                urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + fromGisFakeId;
            }
            else
            {
                urlToCall = "<?php echo $serviceMapUrlPrefix; ?>api/v1/?serviceUri=" + fromGisExternalContentServiceUri + "&format=json";
            }

            $.ajax({
                url: urlToCall,
                type: "GET",
                data: {},
                async: true,
                dataType: 'json',
                success: function(geoJsonServiceData) 
                {
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').show();

                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').off('click');
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').click(function(){
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

                    metricData = {  
                        "data":[  
                           {  
                              "commit":{  
                                 "author":{  
                                    "IdMetric_data": fromGisExternalContentField,
                                    "computationDate": null,
                                    "value_num":null,
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
                                    "threshold":null,
                                    "thresholdEval":null,
                                    "field1Desc": null,
                                    "field2Desc": null,
                                    "field3Desc": null,
                                    "hasNegativeValues": "1"
                                 }
                              }
                           }
                        ]
                    };

                    var fatherNode = null;
                    if(geoJsonServiceData.hasOwnProperty("BusStop"))
                    {
                        fatherNode = geoJsonServiceData.BusStop;
                    }
                    else
                    {
                        if(geoJsonServiceData.hasOwnProperty("Sensor"))
                        {
                            fatherNode = geoJsonServiceData.Sensor;
                        }
                        else
                        {
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
                    if(numberPattern.test(geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value))
                    {
                        if(integerPattern.test(geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value))
                        {
                            metricData.data[0].commit.author.value_num = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                            metricData.data[0].commit.author.metricType = "Intero"; 
                        }
                        else
                        {
                            metricData.data[0].commit.author.value_num = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                            metricData.data[0].commit.author.metricType = "Float"; 
                        }
                    }
                    else
                    {
                        metricData.data[0].commit.author.value_text = geoJsonServiceData.realtime.results.bindings[0][fromGisExternalContentField].value;
                        metricData.data[0].commit.author.metricType = "Testuale";
                    }
                },
                error: function(errorData)
                {
                    console.log("Error in data retrieval");
                    console.log(JSON.stringify(errorData));
                },
                complete: function()
                {
                    populateWidget(); 
                }
            });
        }
        else
        {
            $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').hide();
            $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').show();
            
            $.ajax({
                url: getMetricDataUrl,
                type: "GET",
                data: {"IdMisura": ["<?= $_REQUEST['id_metric'] ?>"]},
                async: true,
                dataType: 'json',
                success: function (data) 
                {
                    metricData = data;
                    $("#" + widgetName + "_loading").css("display", "none");
                    $("#" + widgetName + "_content").css("display", "block");
                    populateWidget();
                },
                error: function()
                {
                    metricData = null;
                    console.log("Error in data retrieval");
                    console.log(JSON.stringify(errorData));
                    if(firstLoad !== false)
                    {
                       $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_loading").hide();
                       $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    }
                }
            });
        }
        
        //Web socket 
        openWs = function(e)
        {
            console.log("Widget " + widgetTitle + " is trying to open WebSocket");
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
            }
            catch(e)
            {
                console.log("Widget " + widgetTitle + " could not connect to WebSocket");
                wsClosed();
            }
        };
        
        manageIncomingWsMsg = function(msg)
        {
            console.log("Widget " + widgetTitle + " got new data from WebSocket: \n" + msg.data);
            var msgObj = JSON.parse(msg.data);

            switch(msgObj.msgType)
            {
                case "newNRMetricData":
                    if(encodeURIComponent(msgObj.metricName) === encodeURIComponent(metricName))
                    {
                        var newWsValue = msgObj.newValue;

                        if(metricType === 'Float')
                        {
                            newWsValue = parseFloat(newWsValue).toFixed(1);
                        }

                        if(udm !== null)
                        {
                           if(udmPos === 'next')
                           {   
                              $("#<?= $_REQUEST['name_w'] ?>_value span").html(newWsValue + udm);
                           }
                           else
                           {
                              $("#<?= $_REQUEST['name_w'] ?>_value span").html(newWsValue);
                           }
                        }
                        else
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_value span").html(newWsValue);
                        }
                    }
                    break;

                default:
                    break;
            }
        };
        
        openWsConn = function(e)
        {
            console.log("Widget " + widgetTitle + " connected successfully to WebSocket");
            var wsRegistration = {
                msgType: "ClientWidgetRegistration",
                userType: "widgetInstance",
                metricName: encodeURIComponent(metricName)
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
            console.log("Widget " + widgetTitle + " got WebSocket closed");
            
            webSocket.removeEventListener('close', wsClosed);
            webSocket.removeEventListener('open', openWsConn);
            webSocket.removeEventListener('message', manageIncomingWsMsg);
            webSocket = null;
            if(wsRetryActive === 'yes')
            {
                console.log("Widget " + widgetTitle + " will retry WebSocket reconnection in " + parseInt(wsRetryTime) + "s");
                setTimeout(openWs, parseInt(wsRetryTime*1000));
            }	
        };
        
        //Per ora non usata
        wsError = function(e)
        {
            console.log("Widget " + widgetTitle + " got WebSocket error: " + e);
        };
        
        openWs();
        
        /*try
        {
            openWs();
        }
        catch(e)
        {
            console.log("Widget " + widgetTitle + " got main exception connecting to WebSocket");
        }*/
        
        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);
          
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
            <div id="<?= $_REQUEST['name_w'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer">
                <div id='<?= $_REQUEST['name_w'] ?>_value' class="singleContentValue"><span></span></div>
                <div id='<?= $_REQUEST['name_w'] ?>_udm' class="singleContentUdm"><span></span></div>
            </div>
        </div>
    </div>	
</div> 
