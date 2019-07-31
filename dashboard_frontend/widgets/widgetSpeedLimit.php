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
<link rel="stylesheet" href="../css/widgetSpeedLimit.css">
<script type='text/javascript'>
    $(document).ready(function <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId)  
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
                eventLog("Returned the following ERROR in widgetSpeedLimit.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?>

        var headerHeight = 25;
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>";
        var divContainer = $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content");
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var nome_wid = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div";
        var linkElement = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_link_w');
        var color = '<?= $_REQUEST['color_w'] ?>';
        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var fontColor = "<?= $_REQUEST['fontColor'] ?>";
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
        var showHeader = null;
        var wsRetryActive, wsRetryTime = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var widgetProperties, styleParameters, metricType, metricName, pattern, udm, udmPos, threshold, thresholdEval, 
            delta, deltaPerc, sizeRowsWidget, sm_based, rowParameters, sm_field, fontSize, value, countdownRef, widgetTitle, metricData, widgetHeaderColor, 
            widgetHeaderFontColor, widgetOriginalBorderColor, urlToCall, geoJsonServiceData, showHeader, fontSizeRatio, 
            realFontSize, entityJson, entityId, oldStatus, currentStatus, attributeName, updateTime, signalDimension, 
            signalMarginLeft, signalMarginTop, webSocket, timeToReload, openWs, manageIncomingWsMsg, openWsConn, wsClosed = null;
        
        timeToReload = <?= $_REQUEST['frequency_w'] ?>;
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
		{
				showHeader = false;
		}
		else
		{
			showHeader = true;
		} 
        
        //$('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_countdownContainerDiv').hide();
            
        if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
        {
            metricName = "<?= $_REQUEST['id_metric'] ?>";
            widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
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
        
        //Specifiche per questo widget
        
        //Definizioni di funzione specifiche del widget
        function getStyleParameters()
        {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            
            if($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').width() < $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').height())
            {
                signalDimension = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').width()*0.9;
            }
            else
            {
                signalDimension = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height()*0.9;
            }

            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').width(signalDimension + "px");
            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').height(signalDimension + "px");
            signalMarginLeft = ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').width() - signalDimension)/2;
            signalMarginTop = ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height() - signalDimension)/2;
            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').css("margin-left", signalMarginLeft + "px");
            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').css("margin-top", signalMarginTop + "px");
            
            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitInnerContainer').textfill({
                maxFontPixels: fontSize
            });
	}
        //Fine definizioni di funzione 
        
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
        
        metricType = "<?= $_REQUEST['id_metric'] ?>";
        
        $.ajax({
            url: getParametersWidgetUrl,
            type: "GET",
            data: {"nomeWidget": [widgetName]},
            async: true,
            dataType: 'json',
            success: function (data) 
            {
                widgetProperties = data;
                if((widgetProperties !== null) && (widgetProperties !== ''))
                {
                    //Inizio eventuale codice ad hoc basato sulle proprietà del widget
                    styleParameters = getStyleParameters();
                    fontSize = widgetProperties.param.fontSize;
                    
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').show();
                    
                    if(metricType !== 'SpeedLimit')
                    {
                        if(/*sm_based === 'yes'*/false)
                        {
                            $.ajax({
                                url: rowParameters,
                                type: "GET",
                                data: {},
                                async: true,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    var originalMetricType = data.Service.features[0].properties.realtimeAttributes[sm_field].data_type;
                                    udm = data.Service.features[0].properties.realtimeAttributes[sm_field].value_unit;
                                    
                                    metricData = {  
                                        data:[  
                                           {  
                                              commit:{  
                                                 author:{  
                                                    IdMetric_data: sm_field,
                                                    computationDate: null,
                                                    value_num:null,
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
                                                    threshold:null,
                                                    thresholdEval:null,
                                                    field1Desc: null,
                                                    field2Desc: null,
                                                    field3Desc: null,
                                                    hasNegativeValues: "0"
                                                 }
                                              }
                                           }
                                        ]
                                    };
                                    
                                    switch(originalMetricType)
                                    {
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
                                            metricData.data[0].commit.author.value_text = data.realtime.results.bindings[0][sm_field].value;
                                            break;    
                                    }
                                    
                                    $("#" + widgetName + "_loading").css("display", "none");
                                    $("#" + widgetName + "_content").css("display", "block");
                                    if(metricData !== null)
                                    {
                                        if(metricData.data[0] !== 'undefined')
                                        {
                                            if(metricData.data.length > 0)
                                            {
                                                $("#" + widgetName + "_loading").css("display", "none");
                                                $("#" + widgetName + "_content").css("display", "block");

                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading').hide();
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').show();

                                                if($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').width() < $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').height())
                                                {
                                                    signalDimension = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').width()*0.9;
                                                }
                                                else
                                                {
                                                    signalDimension = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height()*0.9;
                                                }

                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').width(signalDimension + "px");
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').height(signalDimension + "px");
                                                signalMarginLeft = ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').width() - signalDimension)/2;
                                                signalMarginTop = ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height() - signalDimension)/2;
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').css("margin-left", signalMarginLeft + "px");
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').css("margin-top", signalMarginTop + "px");

                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan').html(metricData.data[0].commit.author.value_num);
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan').css("font-size", fontSize + "px");
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitInnerContainer').textfill({
                                                    maxFontPixels: fontSize
                                                });

                                                countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);

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
                                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan').html(msgObj.newValue);
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

                                                openWs();
                                            }
                                            else
                                            {
                                                if(firstLoad !== false)
                                                {
                                                   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                                   $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if(firstLoad !== false)
                                            {
                                               $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                               $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                               $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if(firstLoad !== false)
                                        {
                                           $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                           $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                           $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        }
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
                            });//FINE NUOVA AJAX
                        }
                        else
                        {
                            $.ajax({
                                url: getMetricDataUrl,
                                type: "GET",
                                data: {"IdMisura": ["<?= $_REQUEST['id_metric'] ?>"]},
                                async: true,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    metricData = data;

                                    if(metricData !== null)
                                    {
                                        if(metricData.data[0] !== 'undefined')
                                        {
                                            if(metricData.data.length > 0)
                                            {
                                                $("#" + widgetName + "_loading").css("display", "none");
                                                $("#" + widgetName + "_content").css("display", "block");

                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading').hide();
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').show();

                                                if($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').width() < $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').height())
                                                {
                                                    signalDimension = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').width()*0.9;
                                                }
                                                else
                                                {
                                                    signalDimension = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height()*0.9;
                                                }

                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').width(signalDimension + "px");
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').height(signalDimension + "px");
                                                signalMarginLeft = ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').width() - signalDimension)/2;
                                                signalMarginTop = ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height() - signalDimension)/2;
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').css("margin-left", signalMarginLeft + "px");
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').css("margin-top", signalMarginTop + "px");

                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan').html(metricData.data[0].commit.author.value_num);
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan').css("font-size", fontSize + "px");
                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitInnerContainer').textfill({
                                                    maxFontPixels: fontSize
                                                });

                                                countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);

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
                                                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan').html(msgObj.newValue);
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
                                                        metricName: encodeURIComponent(metricName)
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

                                                openWs();
                                            }
                                            else
                                            {
                                                if(firstLoad !== false)
                                                {
                                                   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                                   $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                                   $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if(firstLoad !== false)
                                            {
                                               $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                               $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                               $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if(firstLoad !== false)
                                        {
                                           $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                           $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                           $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                        }
                                    }
                                },
                                error: function()
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
                        }
                        
                        
                    }
                    else
                    {
                        entityId = JSON.parse(widgetProperties.param.parameters).entityId;
                        attributeName = JSON.parse(widgetProperties.param.parameters).attributeName;
                        updateTime = JSON.parse(widgetProperties.param.parameters).updateTime;
                        
                        $.ajax({
                            url: "../management/iframeProxy.php?action=getOrionEntityStatus&entityId=" + entityId,
                            type: "GET",
                            data: {},
                            async: true,
                            dataType: 'json',
                            success: function (data) 
                            {
                                entityJson = JSON.parse(data);
                                currentStatus = entityJson[attributeName].value;
                                oldStatus = currentStatus;

                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading').hide();
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').show();

                                if($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').width() < $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content').height())
                                {
                                    signalDimension = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').width()*0.9;
                                }
                                else
                                {
                                    signalDimension = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height()*0.9;
                                }

                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').width(signalDimension + "px");
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').height(signalDimension + "px");
                                signalMarginLeft = ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').width() - signalDimension)/2;
                                signalMarginTop = ($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer').height() - signalDimension)/2;
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').css("margin-left", signalMarginLeft + "px");
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer').css("margin-top", signalMarginTop + "px");

                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan').html(currentStatus);
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan').css("font-size", fontSize + "px");
                                $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitInnerContainer').textfill({
                                    maxFontPixels: fontSize
                                });

                                setInterval(function(){
                                    oldStatus = currentStatus;
                                    $.ajax({
                                        url: "../management/iframeProxy.php?action=getOrionEntityStatus&entityId=" + entityId,
                                        type: "GET",
                                        data: {},
                                        async: true,
                                        dataType: 'json',
                                        success: function(data) 
                                        {
                                            entityJson = JSON.parse(data);
                                            currentStatus = entityJson[attributeName].value;
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan').html(currentStatus);
                                            $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitInnerContainer').textfill({
                                                maxFontPixels: fontSize
                                            });
                                        }
                                    });
                                }, updateTime*1000);
                            },
                            error: function (data) 
                            {
                                console.log("Ko");
                            }
                        });
                    }
                }
                else
                {
                    console.log("Errore in caricamento proprietà widget");
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                    }
                }
            },
            error: function(errorData)
            {
               console.log("Errore in caricamento proprietà widget");
               console.log(JSON.stringify(errorData));
               showWidgetContent(widgetName);
               if(firstLoad !== false)
               {
                  $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                  $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
               }
            }
        });
        
        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
        
        $(document).on('resizeHighchart_' + widgetName, function(event)
        {
            showHeader = event.showHeader;
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
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer" class="chartContainer">
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitContainer" class="speedLimitContainer">
                    <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitInnerContainer" class="speedLimitInnerContainer centerWithFlex">
                        <span id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_speedLimitValueSpan" class="speedLimitValueSpan"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>	
</div> 
