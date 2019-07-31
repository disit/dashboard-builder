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
<link rel="stylesheet" href="../css/widgetTrafficLight.css">
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
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetTrafficLight.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
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
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var widgetProperties, styleParameters, metricType, metricName, pattern, udm, udmPos, threshold, thresholdEval, 
            delta, deltaPerc, sizeRowsWidget, fontSize, value, metricType, countdownRef, widgetTitle, metricData, widgetHeaderColor, 
            widgetHeaderFontColor, widgetOriginalBorderColor, urlToCall, geoJsonServiceData, showHeader, fontSizeRatio, 
            realFontSize, entityJson, entityId, oldStatus, currentStatus, attributeName, updateTime = null;
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
		{
				showHeader = false;
		}
		else
		{
			showHeader = true;
		} 
        
        $('#<?= $_REQUEST['name_w'] ?>_countdownContainerDiv').hide();
            
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
        
        function populateWidget()
        {
            $('#<?= $_REQUEST['name_w'] ?>_loading').hide();
            $('#<?= $_REQUEST['name_w'] ?>_content').show();
            
            if($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() < $('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').height()*0.33)
            {
                var dimension = $('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width()*0.9;
            }
            else
            {
                var dimension = Math.ceil($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').height()*0.3);
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("width", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("height", dimension + "px");
            
            var verticalLightDist = parseFloat($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').height() - 20 - 3*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height())/2;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("top", "20px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width())/2 + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("width", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("height", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("top", parseInt(20 + $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height() + verticalLightDist) + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').width())/2 + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("width", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("height", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("top", parseInt(20 + 2*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height() + verticalLightDist) + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').width())/2 + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("margin-top", verticalLightDist + "px");
            
            /*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("width", "60%");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("height", $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width() + "px");
            
            var verticalLightDist = parseFloat($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').height() - 20 - 3*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height())/2;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("top", "20px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width())/2 + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("width", "60%");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("height", $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width() + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("top", parseInt(20 + $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height() + verticalLightDist) + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').width())/2 + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("width", "60%");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("height", $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width() + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("top", parseInt(20 + 2*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height() + verticalLightDist) + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').width())/2 + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("margin-top", verticalLightDist + "px");*/
            
            /*for(var key in entityJson)
            {
                if((key !== 'id')&&(key !== 'type')&&(key !== "actuatorCanceller")&&(key !== 'actuatorDeleted')&&(key !== 'actuatorDeletionDate')&&(key !== 'creationDate')&&(key !== 'entityCreator')&&(key !== 'entityDesc'))
                {
                    currentStatus = entityJson[key].value;
                    oldStatus = currentStatus;
                }
            }*/
            
            oldStatus = currentStatus;
            currentStatus = entityJson[attributeName].value;
            
            switch(currentStatus)
            {
                case "Red":
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("opacity", "0");
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("opacity", "1");
                    break;
                    
                case "Green":
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("opacity", "0");
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("opacity", "1");
                    break;
            }
        }
        
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            
            if($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() < $('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').height()*0.33)
            {
                var dimension = $('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width()*0.8;
            }
            else
            {
                var dimension = $('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').height()*0.3;
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("width", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("height", dimension + "px");
            
            var verticalLightDist = parseFloat($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').height() - 20 - 3*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height())/2;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("top", "10px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width())/2 + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("width", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("height", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("top", parseInt(10 + $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height() + verticalLightDist) + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').width())/2 + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("width", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("height", dimension + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("top", parseInt(10 + 2*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height() + verticalLightDist) + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').width())/2 + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("margin-top", verticalLightDist + "px");
            
            /*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("height", $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width() + "px");
            var verticalLightDist = parseFloat($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').height() - 20 - 3*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height())/2;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("top", "20px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width())/2 + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("height", $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width() + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("top", parseInt(20 + $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height() + verticalLightDist) + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').width())/2 + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("height", $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').width() + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("top", parseInt(20 + 2*$('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').height() + verticalLightDist) + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("left", ($('#<?= $_REQUEST['name_w'] ?>_chartContainer div.trafficlight').width() - $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').width())/2 + "px");
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("margin-top", verticalLightDist + "px");*/
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
        
        //$("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);
        
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
                    entityId = JSON.parse(widgetProperties.param.parameters).entityId;
                    attributeName = JSON.parse(widgetProperties.param.parameters).attributeName;
                    updateTime = JSON.parse(widgetProperties.param.parameters).updateTime;

                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').show();
                    
                    $.ajax({
                        url: "../management/iframeProxy.php?action=getOrionEntityStatus&entityId=" + entityId,
                        type: "GET",
                        data: {},
                        async: true,
                        dataType: 'json',
                        success: function (data) 
                        {
                            entityJson = JSON.parse(data);
                            populateWidget(); 
                            
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
                                        /*for(var key in entityJson)
                                        {
                                            if((key !== 'id')&&(key !== 'type')&&(key !== "actuatorCanceller")&&(key !== 'actuatorDeleted')&&(key !== 'actuatorDeletionDate')&&(key !== 'creationDate')&&(key !== 'entityCreator')&&(key !== 'entityDesc'))
                                            {
                                                currentStatus = entityJson[key].value;
                                            }
                                        }*/
        
                                        currentStatus = entityJson[attributeName].value;
                                        
                                        switch(currentStatus)
                                        {
                                            case "Red":
                                                $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("opacity", "0");
                                                if(oldStatus === 'Green')
                                                {
                                                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("opacity", "1");
                                                        setTimeout(function(){
                                                        $('#<?= $_REQUEST['name_w'] ?>_chartContainer .yellow').css("opacity", "0");
                                                        $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("opacity", "1");
                                                    }, 2000);
                                                }
                                                break;

                                            case "Green":
                                                $('#<?= $_REQUEST['name_w'] ?>_chartContainer .red').css("opacity", "0");
                                                $('#<?= $_REQUEST['name_w'] ?>_chartContainer .green').css("opacity", "1");
                                                break;
                                        }
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
            },
            complete: function()
            {
                $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
                    resizeWidget();
                });
                
                $(document).on('resizeHighchart_' + widgetName, function(event)
                {
                    showHeader = event.showHeader;
                });
                //countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);
            }
        });
        
        
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
            <div id="<?= $_REQUEST['name_w'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" class="chartContainer">
                <div class="trafficlight">
                    <!--<div class="protector"></div>
                    <div class="protector"></div>
                    <div class="protector"></div>-->
                    <div class="red"></div>
                    <div class="yellow"></div>
                    <div class="green"></div>
                </div>
            </div>
        </div>
    </div>	
</div> 