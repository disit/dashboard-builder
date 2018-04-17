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
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
        var showHeader = null;
        var wsRetryActive, wsRetryTime = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var widgetProperties, styleParameters, metricType, metricName, pattern, udm, udmPos, threshold, thresholdEval, 
            delta, deltaPerc, sizeRowsWidget, fontSize, value, countdownRef, widgetTitle, metricData, widgetHeaderColor, 
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
        
        //$('#<?= $_REQUEST['name_w'] ?>_countdownContainerDiv').hide();
            
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
            
            if($('#<?= $_REQUEST['name_w'] ?>_content').width() < $('#<?= $_REQUEST['name_w'] ?>_content').height())
            {
                signalDimension = $('#<?= $_REQUEST['name_w'] ?>_chartContainer').width()*0.9;
            }
            else
            {
                signalDimension = $('#<?= $_REQUEST['name_w'] ?>_chartContainer').height()*0.9;
            }

            $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').width(signalDimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').height(signalDimension + "px");
            signalMarginLeft = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width() - signalDimension)/2;
            signalMarginTop = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height() - signalDimension)/2;
            $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').css("margin-left", signalMarginLeft + "px");
            $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').css("margin-top", signalMarginTop + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_speedLimitInnerContainer').textfill({
                maxFontPixels: fontSize
            });
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
        
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);
        
        metricType = "<?= $_REQUEST['metricType'] ?>";
        
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
                    //XXX Inizio eventuale codice ad hoc basato sulle proprietà del widget
                    styleParameters = getStyleParameters();
                    fontSize = widgetProperties.param.fontSize;
                    
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').show();
                    
                    if(metricType !== 'SpeedLimit')
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

                                            $('#<?= $_REQUEST['name_w'] ?>_loading').hide();
                                            $('#<?= $_REQUEST['name_w'] ?>_content').show();

                                            if($('#<?= $_REQUEST['name_w'] ?>_content').width() < $('#<?= $_REQUEST['name_w'] ?>_content').height())
                                            {
                                                signalDimension = $('#<?= $_REQUEST['name_w'] ?>_chartContainer').width()*0.9;
                                            }
                                            else
                                            {
                                                signalDimension = $('#<?= $_REQUEST['name_w'] ?>_chartContainer').height()*0.9;
                                            }

                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').width(signalDimension + "px");
                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').height(signalDimension + "px");
                                            signalMarginLeft = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width() - signalDimension)/2;
                                            signalMarginTop = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height() - signalDimension)/2;
                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').css("margin-left", signalMarginLeft + "px");
                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').css("margin-top", signalMarginTop + "px");

                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitValueSpan').html(metricData.data[0].commit.author.value_num);
                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitValueSpan').css("font-size", fontSize + "px");
                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitInnerContainer').textfill({
                                                maxFontPixels: fontSize
                                            });
                                            
                                            countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);
                                            
                                            /*try 
                                            {
                                                <?php
                                                    $genFileContent = parse_ini_file("../conf/environment.ini");
                                                    $wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
                                                    $wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$genFileContent['environment']['value']];
                                                    $wsServerPort = $wsServerContent["wsServerPort"][$genFileContent['environment']['value']];
                                                    $wsPath = $wsServerContent["wsServerPath"][$genFileContent['environment']['value']];
                                                    $wsProtocol = $wsServerContent["wsServerProtocol"][$genFileContent['environment']['value']];
                                                    echo 'webSocket = new WebSocket("' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '");';
                                                ?>

                                                webSocket.onopen = function(msg) 
                                                { 
                                                    var wsRegistration = {
                                                      msgType: "ClientWidgetRegistration",
                                                      userType: "widgetInstance",
                                                      metricName: encodeURIComponent(metricName)
                                                    };
                                                    webSocket.send(JSON.stringify(wsRegistration));

                                                    setTimeout(function(){
                                                        webSocket.close();
                                                    }, (timeToReload - 2)*1000);
                                                };

                                                webSocket.onmessage = function(msg) 
                                                { 
                                                    var msgObj = JSON.parse(msg.data);

                                                    switch(msgObj.msgType)
                                                    {
                                                        case "newNRMetricData":
                                                            if(encodeURIComponent(msgObj.metricName) === encodeURIComponent(metricName))
                                                            {     
                                                                $('#<?= $_REQUEST['name_w'] ?>_speedLimitValueSpan').html(msgObj.newValue);
                                                            }
                                                            break;

                                                        default:
                                                            console.log("Received: " + msg.data);
                                                            break;
                                                    }

                                                };

                                                webSocket.onclose = function(msg) 
                                                { 
                                                    console.log("Disconnected - status " + msg); 
                                                };
                                            }
                                            catch(ex)
                                            { 
                                               console.log(ex); 
                                            }*/
    
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
                                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitValueSpan').html(msgObj.newValue);
                                                        }
                                                        break;

                                                    default:
                                                        console.log("Received: " + msg.data);
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
                                                      webSocket.close();
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
                                        }
                                        else
                                        {
                                            if(firstLoad !== false)
                                            {
                                               $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                               $("#<?= $_REQUEST['name_w'] ?>_loading").hide();
                                               $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if(firstLoad !== false)
                                        {
                                           $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                           $("#<?= $_REQUEST['name_w'] ?>_loading").hide();
                                           $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                        }
                                    }
                                }
                                else
                                {
                                    if(firstLoad !== false)
                                    {
                                       $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                       $("#<?= $_REQUEST['name_w'] ?>_loading").hide();
                                       $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
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
                                   $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                                   $("#<?= $_REQUEST['name_w'] ?>_loading").hide();
                                   $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                                }
                            }
                        });
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

                                $('#<?= $_REQUEST['name_w'] ?>_loading').hide();
                                $('#<?= $_REQUEST['name_w'] ?>_content').show();

                                if($('#<?= $_REQUEST['name_w'] ?>_content').width() < $('#<?= $_REQUEST['name_w'] ?>_content').height())
                                {
                                    signalDimension = $('#<?= $_REQUEST['name_w'] ?>_chartContainer').width()*0.9;
                                }
                                else
                                {
                                    signalDimension = $('#<?= $_REQUEST['name_w'] ?>_chartContainer').height()*0.9;
                                }

                                $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').width(signalDimension + "px");
                                $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').height(signalDimension + "px");
                                signalMarginLeft = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width() - signalDimension)/2;
                                signalMarginTop = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height() - signalDimension)/2;
                                $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').css("margin-left", signalMarginLeft + "px");
                                $('#<?= $_REQUEST['name_w'] ?>_speedLimitContainer').css("margin-top", signalMarginTop + "px");

                                $('#<?= $_REQUEST['name_w'] ?>_speedLimitValueSpan').html(currentStatus);
                                $('#<?= $_REQUEST['name_w'] ?>_speedLimitValueSpan').css("font-size", fontSize + "px");
                                $('#<?= $_REQUEST['name_w'] ?>_speedLimitInnerContainer').textfill({
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
                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitValueSpan').html(currentStatus);
                                            $('#<?= $_REQUEST['name_w'] ?>_speedLimitInnerContainer').textfill({
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
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
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
                  $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                  $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
               }
            }
        });
});//Fine document ready  ZZZ
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
                <div id="<?= $_REQUEST['name_w'] ?>_speedLimitContainer" class="speedLimitContainer">
                    <div id="<?= $_REQUEST['name_w'] ?>_speedLimitInnerContainer" class="speedLimitInnerContainer centerWithFlex">
                        <span id="<?= $_REQUEST['name_w'] ?>_speedLimitValueSpan" class="speedLimitValueSpan"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>	
</div> 
