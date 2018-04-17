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

<link rel="stylesheet" href="../css/widgetNumericKeyboard.css">

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
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var widgetProperties, styleParameters, metricType, metricName, widgetParameters, 
            sizeRowsWidget, widgetTitle, widgetHeaderColor, 
            widgetHeaderFontColor, showHeader, minDim, minDimCells, minDimName, offset, dashboardId,
            widgetWidthCells, widgetHeightCells,
            entityJson, attributeName, updateMsgFontSize, setUpdatingMsgIndex, setUpdatingMsgInterval, 
            dataType, displayColor, currentValue, fontFamily, targetCurrentStatus, oldValue,
            onOffButtonPercentWidth, onOffButtonPercentHeight, onOffButtonRadius, buttonClickColor, textOnNeonEffect, textOffNeonEffect,
            buttonColor, offValue, impulseValue, updateRequestStartTime, symbolColor, symbolClickColor, textClickColor, textColor,
            symbolOnNeonEffect, symbolOffNeonEffect, symbolOnNeonEffectSetting, viewMode, textFontSize, displayFontSize,
            displayFontColor, displayFontClickColor, displayRadius, displayColor, displayWidth, displayHeight, displayOffNeonEffect, 
            displayOnNeonEffect, impulseMode, targetEntity, targetEntityAttribute, baseValue, sequenceEntityUpdateInterval, sentValue,
            actuatorTarget, username, endPointHost, endPointPort, nodeRedInputName, btnColor, btnFontColor, displayColor, displayFontColor = null;
        
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
        
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        elToEmpty.css("font-family", "Verdana");
        var url = "<?= $_REQUEST['link_w'] ?>";
        $('#<?= $_REQUEST['name_w'] ?>_countdownDiv').hide();
        
        //Definizioni di funzione specifiche del widget
        
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
            currentValue = $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text();
            sentValue = 'None';
            showWidgetContent(widgetName);
            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
            $("#<?= $_REQUEST['name_w'] ?>_loadErrorAlert").hide();
            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
            
            if($("#<?= $_REQUEST['name_w'] ?>_chartContainer").width() > $("#<?= $_REQUEST['name_w'] ?>_chartContainer").height())
            {
                minDim = $("#<?= $_REQUEST['name_w'] ?>_chartContainer").height();
                minDimCells = widgetHeightCells;
                minDimName = "height";
            }
            else
            {
                minDim = $("#<?= $_REQUEST['name_w'] ?>_chartContainer").width();
                minDimCells = widgetWidthCells;
                minDimName = "width";
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("position", "relative");
            
            $('#<?= $_REQUEST['name_w'] ?>_lastContainer').css('background-color', displayColor);
            $('#<?= $_REQUEST['name_w'] ?>_lastContainer').css('color', displayFontColor);
            $('#<?= $_REQUEST['name_w'] ?>_sentContainer').css('background-color', displayColor);
            $('#<?= $_REQUEST['name_w'] ?>_sentContainer').css('color', displayFontColor);
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').css('color', btnFontColor);
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').css('background-color', btnColor);
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').css('color', btnFontColor);
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardEnterBtn').css('background-color', btnColor);
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardEnterBtn').css('color', btnFontColor);
            
            var normFontSize = $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').height()*0.7;
            var normFontDisplayLabelSize = $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').height()*0.3;
            var normFontDisplayValSize = $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').height()*0.5;
            
            $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayLabel').css('font-size', normFontDisplayLabelSize + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_sentContainer span.displayLabel').css('font-size', normFontDisplayLabelSize + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').css('font-size', normFontDisplayValSize + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_sentContainer span.displayVal').css('font-size', normFontDisplayValSize + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').css('font-size', normFontSize + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardEnterBtn').css('font-size', normFontSize + 'px');
            
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').click(newKeyboardInput);
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardEnterBtn').click(newKeyboardInput);
        }
        
        function newKeyboardInput(event)
        {
            currentValue = $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text();
            
            console.log("new value: " + event.target.getAttribute("value"));
            
            switch(event.target.getAttribute("value"))
            {
                case "back":
                    $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text($('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text().substring(0, $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text().length - 1));
                    break;
                    
                case "enter":
                    updateRemoteValue();
                    break;    
                
                case "comma":
                    if(!currentValue.includes('.'))
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text(currentValue + ".");
                    }
                    break;
                
                default:
                    $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text(currentValue + event.target.getAttribute("value"));
                    break;
            }
        }
        
        function updateRemoteValue()
        {
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').off('click', newKeyboardInput);
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardEnterBtn').off('click', newKeyboardInput);
            
            var requestComplete = false;
            $('#<?= $_REQUEST['name_w'] ?>_sentContainer span.displayVal').text('Updating');  
            
            switch(actuatorTarget)
            {
                case 'broker':
                    $.ajax({
                        url: "../widgets/actuatorUpdateValue.php",
                        type: "POST",
                        data: {
                            "dashboardId": dashboardId,
                            "entityId": "<?= $_REQUEST['name_w'] ?>",
                            "entityJson": entityJson,
                            "attributeName": attributeName,
                            "attributeType": JSON.parse(entityJson)[attributeName].type,
                            "value": currentValue,
                            "dashboardUsername": $('#authForm #hiddenUsername').val()
                        },
                        async: true,
                        dataType: 'json',
                        success: function(data) 
                        {
                            requestComplete = true;
                            clearInterval(setUpdatingMsgInterval);
                            switch(data.result)
                            {
                                case "insertQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "updateEntityKo":
                                    showUpdateResult("Device KO");
                                    break;

                                case "updateEntityAndUpdateQueryKo":
                                    showUpdateResult("DB and device KO");
                                    break;

                                case "updateQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "Ok":
                                    showUpdateResult("Device OK");
                                    break;    
                            }
                        },
                        error: function(errorData)
                        {
                            requestComplete = true;
                            showUpdateResult("API KO");
                            console.log("Update value KO");
                            console.log(JSON.stringify(errorData));
                        }
                    });
                    break;
                
                case 'app':
                    $.ajax({
                        url: "../widgets/actuatorUpdateValuePersonalApps.php",
                        type: "POST",
                        data: {
                            "inputName": nodeRedInputName,
                            "dashboardId": dashboardId,
                            "widgetName": "<?= $_REQUEST['name_w'] ?>",
                            "username" : $('#authForm #hiddenUsername').val(),
                            "value": currentValue,
                            "endPointPort": "<?= $_REQUEST['endPointPort'] ?>",
                            "httpRoot": "<?= $_REQUEST['httpRoot'] ?>"
                        },
                        async: true,
                        dataType: 'json',
                        success: function(data) 
                        {
                            requestComplete = true;
                            //clearInterval(setUpdatingMsgInterval);
                            switch(data.result)
                            {
                                case "insertQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "updateBlockKo":
                                    showUpdateResult("Device KO");
                                    break;

                                case "updateBlockAndUpdateQueryKo":
                                    showUpdateResult("DB and device KO");
                                    break;

                                case "updateQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "Ok":
                                    showUpdateResult("Device OK");
                                    break;    
                            }
                        },
                        error: function(errorData)
                        {
                            requestComplete = true;
                            //clearInterval(setUpdatingMsgInterval);
                            showUpdateResult("API KO");
                            console.log("Update value KO");
                            console.log(JSON.stringify(errorData));
                        }
                    });
                    break;  
            }
            
        }
        
        function showUpdateResult(msg)
        {
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').click(newKeyboardInput);
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardEnterBtn').click(newKeyboardInput);
            
            if(msg !== "Device OK")
            {
                $('#<?= $_REQUEST['name_w'] ?>_sentContainer span.displayVal').text('KO');
                setTimeout(function(){
                    $('#<?= $_REQUEST['name_w'] ?>_sentContainer span.displayVal').text(sentValue); 
                }, 1500);
            }
            else
            {
                sentValue = currentValue;
                $('#<?= $_REQUEST['name_w'] ?>_sentContainer span.displayVal').text(sentValue); 
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text('');
            currentValue = $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text();
        }
        
        function resizeWidget()
	{
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            
            var normFontSize = $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').height()*0.7;
            
            $('#<?= $_REQUEST['name_w'] ?>_lastContainer').css('font-size', normFontSize + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardBtn').css('font-size', normFontSize + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_keyboard button.numericKeyboardEnterBtn').css('font-size', normFontSize + 'px');
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
                    dashboardId = widgetProperties.param.id_dashboard;
                    styleParameters = getStyleParameters();
                    widgetParameters = JSON.parse(widgetProperties.param.parameters);
                    sizeRowsWidget = parseInt(widgetProperties.param.size_rows);
                    widgetWidthCells = parseInt(widgetProperties.param.size_columns);
                    widgetHeightCells = parseInt(widgetProperties.param.size_rows);
                    fontFamily = widgetProperties.param.fontFamily;
                    actuatorTarget = widgetProperties.param.actuatorTarget;
                    if(actuatorTarget === 'broker')
                    {
                        /*entityJson = widgetProperties.param.entityJson;
                        attributeName = widgetProperties.param.attributeName;
                        dataType = JSON.parse(entityJson)[attributeName].type;
                        targetEntity = widgetParameters.targetEntity;
                        targetEntityAttribute = widgetParameters.targetEntityAttribute;
                        impulseValue = widgetParameters.impulseValue;
                        baseValue = widgetParameters.baseValue;*/
                    }
                    else
                    {
                        nodeRedInputName = widgetProperties.param.name;
                        dataType = widgetProperties.param.valueType;
                        /*baseValue = widgetProperties.param.offValue;
                        impulseValue = widgetProperties.param.onValue;*/
                        username = widgetProperties.param.creator;
                        endPointHost = widgetProperties.param.endPointHost;
                        endPointPort = widgetProperties.param.endPointPort;
                    }
                    
                    switch(dataType)
                    {
                        case "Integer":
                            currentValue = parseInt(widgetProperties.param.currentValue);
                            break;

                        case "Float":
                            currentValue = parseFloat(widgetProperties.param.currentValue);
                            break;
                    }
                    
                    displayColor = styleParameters.displayColor;
                    displayFontColor = styleParameters.displayFontColor;
                    btnColor = styleParameters.btnColor;
                    btnFontColor = styleParameters.btnFontColor;
                    
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').show();
                    
                    populateWidget();
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
                
            }
        });
        
        
});//Fine document ready 
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
	    <?php include '../widgets/widgetHeader.php'; ?>
		<?php include '../widgets/widgetCtxMenu.php'; ?>
        <!--<div id='<?= $_REQUEST['name_w'] ?>_header' class="widgetHeader">
            <div id="<?= $_REQUEST['name_w'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id="info_modal" href="#" class="info_source"><i id="source_<?= $_REQUEST['name_w'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
               <i class="material-icons gisDriverPin" data-onMap="false">navigation</i>
            </div>    
            <div id="<?= $_REQUEST['name_w'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_REQUEST['name_w'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_countdownContainerDiv" class="countdownContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_countdownDiv" class="countdown"></div> 
            </div>   
        </div>-->
        
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
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" ondragstart="return false;" ondrop="return false;" class="chartContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_keyboard" class="numericKeyboardContainer">
                    <div class="row numericKeyboardDisplayRow">
                        <div id="<?= $_REQUEST['name_w'] ?>_lastContainer" class="col-xs-12 numericKeyboardValueContainer">
                            <span class="displayLabel centerWithFlex">New</span>
                            <span class="displayVal centerWithFlex"></span>
                        </div>
                        <div id="<?= $_REQUEST['name_w'] ?>_sentContainer" class="col-xs-12 numericKeyboardValueContainer">
                            <span class="displayLabel centerWithFlex">Last confirmed</span>
                            <span class="displayVal centerWithFlex">None</span>
                        </div>      
                    </div>
                    
                    <div class="row numericKeyboardRow">
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_7btn" value="7" class="numericKeyboardBtn centerWithFlex">7</button>
                        </div>
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_8btn" value="8" class="numericKeyboardBtn centerWithFlex">8</button>
                        </div>
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_9btn" value="9" class="numericKeyboardBtn centerWithFlex">9</button>
                        </div>
                    </div>
                    <div class="row numericKeyboardRow">
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_4btn" value="4" class="numericKeyboardBtn centerWithFlex">4</button>
                        </div>
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_5btn" value="5" class="numericKeyboardBtn centerWithFlex">5</button>
                        </div>
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_6btn" value="6" class="numericKeyboardBtn centerWithFlex">6</button>
                        </div>
                    </div>
                    <div class="row numericKeyboardRow">
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_1btn" value="1" class="numericKeyboardBtn centerWithFlex">1</button>
                        </div>
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_2btn" value="2" class="numericKeyboardBtn centerWithFlex">2</button>
                        </div>
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_3btn" value="3" class="numericKeyboardBtn centerWithFlex">3</button>
                        </div>
                    </div>
                    <div class="row numericKeyboardRow">
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_0btn" value="0" class="numericKeyboardBtn centerWithFlex">0</button>
                        </div>
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_commabtn" value="comma" class="numericKeyboardBtn centerWithFlex">.</button>
                        </div>
                        <div class="col-xs-4 numericKeyboardBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_backbtn" value="back" class="numericKeyboardBtn centerWithFlex">Canc</button>
                        </div>
                    </div>
                    <div class="row numericKeyboardRow">
                        <div class="col-xs-12 numericKeyboardEnterBtnContainer">
                            <button type="button" id="<?= $_REQUEST['name_w'] ?>_enterbtn" value="enter" class="numericKeyboardEnterBtn centerWithFlex">Confirm</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>	
</div> 