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

<!-- START SNAP4D3 DEPENDENCIES -->
<script src="../js/d3/d3.js"></script> 
<script src="../js/d3require/d3-require.min.js"></script>
<!-- END SNAP4D3 DEPENDENCIES -->

<script type='text/javascript'>
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, isRestoringFromExternalContent) {
        <?php
        $link = mysqli_connect($host, $username, $password);
        if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
            eventLog("Returned the following ERROR in widgetSnap4D3.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
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

        const chartContainerName = "#<?= $_REQUEST['name_w'] ?>_chartContainer";
        let redraw=null;


        console.log("Entrato in widgetSnap4D3 --> " + widgetName);

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

        function resizeWidget() {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);           
           
           if(redraw){
               redraw()
           }
        }

        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        setupLoadingPanel(widgetName, widgetContentColor, firstLoad);

        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event) 
        {
            showHeader = event.showHeader;
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
       
        //<-- SNAP4D3 FUNCTIONS...


        function sendToNodeRed(data){           
            
            if(!webSocket || webSocket.readyState!=1){
                const message = "Cannot send data to node-red, because the websocket is not open.";
                console.error(message);
                alert(message);
                return;
            }

            const dataFromWidget = {
                msgType: "SendToEmitter",
                widgetUniqueName: widgetName,
                value: JSON.stringify({
                    widgetData:data,
                    widgetOperation: "SendToSnap4D3"
                }),                
                inputName: nodeRedInputName,
                dashboardId: dashboardId,
                username : $('#authForm #hiddenUsername').val(),
                nrInputId: nrInputId
            };            
            
            webSocket.send(JSON.stringify(dataFromWidget));
        }

        function populateWidget(d3,d3Configuration,d3Data,sendToNodeRed){

            const chartContainerJqueryElement = $(chartContainerName)

            const functionBody = `${d3Configuration}; return drawD3Chart;`;
            
            const d3NodePromise = (Function("d3","d3Data","width","height","sendToNodeRed",functionBody)()(d3,d3Data,chartContainerJqueryElement.width(),chartContainerJqueryElement.height(),sendToNodeRed))

            d3NodePromise
            .then(d3Node=>{
                $(chartContainerName).empty();
                chartContainerJqueryElement.append(d3Node)
            })
            .catch(error=>console.error("Unable to draw D3 chart due to: ",error));           
        }

        // SNAP4D3 FUNCTIONS... -->
             

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
                nodeRedInputName = widgetData.params.name;
                nrInputId = widgetData.params.nrInputId;

                if (nrMetricType != null) {
                    openWs();
                }
                $("#" + widgetName + "_titleDiv").css("width", "95%");                

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
                webSocket.addEventListener('message', manageIncomingWsMsg);
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
                    showWidgetContent(widgetName);                    
                    redraw = () => populateWidget(d3,msgObj.newValue.d3Configuration,msgObj.newValue.payload,sendToNodeRed)       
                    redraw();
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

        };

        wsClosed = function(e)
        {
            if(webSocket!=null){
                webSocket.removeEventListener('close', wsClosed);
                webSocket.removeEventListener('open', openWsConn);
                webSocket.removeEventListener('message', manageIncomingWsMsg);

                if(webSocket.readyState==webSocket.OPEN || webSocket.readyState==webSocket.CONNECTING){
                    try{
                        webSocket.close();
                    }catch(e){
                        console.error("Unable to close the WebSocket. reason: ",e);
                    }
                }

                webSocket = null;
            }
            if(wsRetryActive === 'yes')
            {
                const retryInterval = parseInt(wsRetryTime);
                const message = "Retry connection to websocket in "+retryInterval+"s"
                console.log(message);
                setTimeout(openWs, retryInterval*1000);

                alert(message);

            }
        };

        wsError = function(e)
        {
            console.error("WebSocketError",e);
        };

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