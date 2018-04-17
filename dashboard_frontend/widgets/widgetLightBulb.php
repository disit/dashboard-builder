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
<link rel="stylesheet" href="../css/widgetLightBulb.css">
<script type='text/javascript'>
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId)  
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_GET['title'];
        ?>
                
        var headerHeight = 25;
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_content");
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= $_GET['color'] ?>';
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_GET['showTitle'] ?>";
		var showHeader = null;
		var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        
        var widgetProperties, styleParameters, metricType, metricName, pattern, udm, udmPos, threshold, thresholdEval, 
            delta, deltaPerc, sizeRowsWidget, fontSize, value, metricType, countdownRef, widgetTitle, metricData, widgetHeaderColor, 
            widgetHeaderFontColor, widgetOriginalBorderColor, urlToCall, geoJsonServiceData, showHeader, fontSizeRatio, 
            realFontSize, entityJson, entityId, oldStatus, currentStatus = null;
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
	{
            showHeader = false;
	}
	else
	{
	    showHeader = true;
	} 
            
        if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
        {
            metricName = "<?= $_GET['metric'] ?>";
            widgetTitle = "<?= preg_replace($titlePatterns, $replacements, $title) ?>";
            widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
            widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>"; 
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
            $('#<?= $_GET['name'] ?>_loading').hide();
            $('#<?= $_GET['name'] ?>_content').show();
            
            for(var key in entityJson)
            {
                if((key !== 'id')&&(key !== 'type')&&(key !== "actuatorCanceller")&&(key !== 'actuatorDeleted')&&(key !== 'actuatorDeletionDate')&&(key !== 'creationDate')&&(key !== 'entityCreator')&&(key !== 'entityDesc'))
                {
                    currentStatus = entityJson[key].value;
                    oldStatus = currentStatus;
                }
            }
            
            currentStatus = parseFloat(currentStatus/100);
            
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-top').css("background-color", "rgba(255, 238, 51, " + currentStatus + ")");
            if($('#<?= $_GET['name'] ?>_chartContainer').width() < $('#<?= $_GET['name'] ?>_chartContainer div.trafficlight').height())
            {
                var dimension = $('#<?= $_GET['name'] ?>_chartContainer').width();
                var lampScaleFactor = dimension/532;
            }
            else
            {
                var dimension = $('#<?= $_GET['name'] ?>_chartContainer').height()*0.8;
                var lampScaleFactor = $('#<?= $_GET['name'] ?>_chartContainer').height()/532;
            }
            
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-top').css("width", dimension*0.8 + "px");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-top').css("height", dimension*0.8 + "px");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-middle-1').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/18) + "px auto 0 auto");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-middle-2').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/24.545) + "px auto 0 auto");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-middle-3').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/27) + "px auto 0 auto");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-bottom').css("height", parseFloat($('#<?= $_GET['name'] ?>_chartContainer').height()/7.92) + "px");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-bottom').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/67.5) + "px auto 0 auto");
            $('#<?= $_GET['name'] ?>_chartContainer .screw-top').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/3) + "px auto -4px auto");
            $('#<?= $_GET['name'] ?>_chartContainer .screw-a').css("height", parseFloat($('#<?= $_GET['name'] ?>_chartContainer').height()/24.44) + "px");
            $('#<?= $_GET['name'] ?>_chartContainer .screw-b').css("height", parseFloat($('#<?= $_GET['name'] ?>_chartContainer').height()/24.44) + "px");
            
            $('#<?= $_GET['name'] ?>_chartContainer').css('-ms-transform', 'scale(' + lampScaleFactor + ', ' + lampScaleFactor + ')');
            $('#<?= $_GET['name'] ?>_chartContainer').css('-webkit-transform', 'scale(' + lampScaleFactor + ', ' + lampScaleFactor + ')');
            $('#<?= $_GET['name'] ?>_chartContainer').css('transform', 'scale(' + lampScaleFactor + ', ' + lampScaleFactor + ')');
        }
        
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeigh, hasTimer);
            
            if($('#<?= $_GET['name'] ?>_chartContainer').width() < $('#<?= $_GET['name'] ?>_chartContainer div.trafficlight').height())
            {
                var dimension = $('#<?= $_GET['name'] ?>_chartContainer').width();
                var lampScaleFactor = dimension/532;
            }
            else
            {
                var dimension = $('#<?= $_GET['name'] ?>_chartContainer').height()*0.8;
                var lampScaleFactor = $('#<?= $_GET['name'] ?>_chartContainer').height()/532;
            }
            
            $('#<?= $_GET['name'] ?>_chartContainer').css('-ms-transform', 'scale(' + lampScaleFactor + ', ' + lampScaleFactor + ')');
            $('#<?= $_GET['name'] ?>_chartContainer').css('-webkit-transform', 'scale(' + lampScaleFactor + ', ' + lampScaleFactor + ')');
            $('#<?= $_GET['name'] ?>_chartContainer').css('transform', 'scale(' + lampScaleFactor + ', ' + lampScaleFactor + ')');
            
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-top').css("width", dimension*0.8 + "px");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-top').css("height", dimension*0.8 + "px");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-middle-1').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/18) + "px auto 0 auto");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-middle-2').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/24.545) + "px auto 0 auto");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-middle-3').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/20) + "px auto 0 auto");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-bottom').css("height", parseFloat($('#<?= $_GET['name'] ?>_chartContainer').height()/7.92) + "px");
            $('#<?= $_GET['name'] ?>_chartContainer .bulb-bottom').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/67.5) + "px auto 0 auto");
            $('#<?= $_GET['name'] ?>_chartContainer .screw-top').css("margin", "-" + parseFloat($('#<?= $_GET['name'] ?>_chartContainer').width()/3) + "px auto -4px auto");
            $('#<?= $_GET['name'] ?>_chartContainer .screw-a').css("height", parseFloat($('#<?= $_GET['name'] ?>_chartContainer').height()/34.44) + "px");
            $('#<?= $_GET['name'] ?>_chartContainer .screw-b').css("height", parseFloat($('#<?= $_GET['name'] ?>_chartContainer').height()/34.44) + "px");
	}
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        
        $('#<?= $_GET['name'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_GET['name'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        
        $("#<?= $_GET['name'] ?>_titleDiv").html(widgetTitle);
        
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

                    $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_GET['name'] ?>_infoButtonDiv a.info_source').show();
                    
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
                                        for(var key in entityJson)
                                        {
                                            if((key !== 'id')&&(key !== 'type')&&(key !== "actuatorCanceller")&&(key !== 'actuatorDeleted')&&(key !== 'actuatorDeletionDate')&&(key !== 'creationDate')&&(key !== 'entityCreator')&&(key !== 'entityDesc'))
                                            {
                                                currentStatus = entityJson[key].value;
                                            }
                                        }
                                        
                                        currentStatus = parseFloat(currentStatus/100);
                                        $('#<?= $_GET['name'] ?>_chartContainer .bulb-top').css("background-color", "rgba(255, 238, 51, " + currentStatus + ")");
                                        //$('#light').css("opacity", currentStatus);
                                    }
                                });
                            }, 750);
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
                        $("#<?= $_GET['name'] ?>_chartContainer").hide();
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
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
                  $("#<?= $_GET['name'] ?>_chartContainer").hide();
                  $('#<?= $_GET['name'] ?>_noDataAlert').show();
               }
            },
            complete: function()
            {
                //countdownRef = startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId);
            }
        });
        
        
});//Fine document ready 
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
               <i class="material-icons gisDriverPin" data-onMap="false">navigation</i>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            </div>
            <div id="<?= $_GET['name'] ?>_countdownContainerDiv" class="countdownContainer">
                <div id="<?= $_GET['name'] ?>_countdownDiv" class="countdown"></div> 
            </div>   
        </div>
        
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_GET['name'] ?>_content" class="content">
            <div id="<?= $_GET['name'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_GET['name'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_GET['name'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer">
                <div id="<?= $_GET['name'] ?>_bulb" class="bulb">
                  <div class="bulb-top">
                    <!--<div class="reflection"></div>-->
                  </div>
                  <div class="bulb-middle-1"></div>
                  <div class="bulb-middle-2"></div>
                  <div class="bulb-middle-3"></div>
                  <div class="bulb-bottom"></div>
                </div>

                <div id="<?= $_GET['name'] ?>_base" class="base">
                  <div class="screw-top"></div>
                  <div class="screw-a"></div>
                  <div class="screw-b"></div>
                  <div class="screw-a"></div>
                  <div class="screw-b"></div>
                  <div class="screw-a"></div>
                  <div class="screw-b"></div>
                  <div class="screw-c"></div>
                  <!--<div class="screw-d"></div>-->
                </div>
            </div>
        </div>
    </div>	
</div> 