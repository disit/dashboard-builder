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

<link rel="stylesheet" href="../css/widgetKnob.css">

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
                eventLog("Returned the following ERROR in widgetKnob.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }

            $genFileContent = parse_ini_file("../conf/environment.ini");
            $wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
            $env = $genFileContent['environment']['value'];
            $wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$env];
            $wsServerPort = $wsServerContent["wsServerPort"][$env];
            $wsPath = $wsServerContent["wsServerPath"][$env];
            $wsProtocol = $wsServerContent["wsServerProtocol"][$env];
            $wsRetryActive = $wsServerContent["wsServerRetryActive"][$env];
            $wsRetryTime = $wsServerContent["wsServerRetryTime"][$env];
            $useActuatorWS = $wsServerContent["wsServerActuator"][$env];
        ?>
                
        var headerHeight = 25;
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader = null;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        
        var widgetProperties, styleParameters, metricType, metricName, pattern, udm, widgetParameters, nrInputId,
            sizeRowsWidget, fontSize, metricType, countdownRef, widgetTitle, widgetHeaderColor, 
            widgetHeaderFontColor, showHeader, minDim, minDimCells, minDimName, offset, mouseDown, startAngle, endAngle, dashboardId,
            currentAngle, currentNormAngle, domainType, minValue, maxValue, currentValue, convFactor, valueRange, angleRange,
            indicatorRadius, displayRadius, continuousRanges, ticksNumber, widgetWidthCells, widgetHeightCells, tickDeltaAngle,
            activeTicks, entityJson, attributeName, updateMsgFontSize, setUpdatingMsgIndex, setUpdatingMsgInterval, 
            oldValue, oldAngle, restoredValue, restoredAngle, dataType, dataPrecision, displayColor, ticksColor, 
            knobPercentDim, knobPercentRadius, tickPxDim, tickPxHeight, labelsFontSize, labelsFontColor, labelsFontFamily,
            newTickValueLeft, hoverIndicatorColor, hoverRotationColor, tickInnerTopPx, ticksDensity,
            incrementControlDim, incrementControlAngle, incrementControlDeltaValue, knobHeight, knobWidth, actuatorTarget,
            nodeRedInputName, username, endPointHost, endPointPort = null;
        var useWebSocket = <?= $useActuatorWS ?>;
        var updatedEverFlag = false;
        var updatedFlag = false;
        var lastValueOk = null;
        var code = null;
		//
		$(document).off('showKnobFromExternalContent_' + widgetName);
        $(document).on('showKnobFromExternalContent_' + widgetName, function(event){
		        // console.log('showSingleContentFromExternalContent_AddCode!-CORRECT');
				if(encodeURIComponent(metricName) === encodeURIComponent(metricName))
                    {
						console.log(event);
                       var newWsValue = event.passedData;
						if (newWsValue.dataOperation){
							console.log('lastValueOk:' +lastValueOk);
							lastValueOk = newWsValue.dataOperation;
							if (lastValueOk !== null) {
								currentValue = lastValueOk;
								lastValueOk = null;
								showUpdateResult("Device OK");
										//   currentValue = currentNormAngle*convFactor + minValue;
										currentNormAngle = (currentValue - minValue)/convFactor;
										var newAngle = currentNormAngle;
										if ((currentNormAngle >= 0) && (currentNormAngle <= 360 - startAngle)) {
											newAngle = currentNormAngle + startAngle;
										} else if ((currentNormAngle >= 360 - startAngle) && (currentNormAngle <= 360 + endAngle - startAngle)) {
											newAngle = currentNormAngle + startAngle - 360;
										}

										switch(dataType)
										{
											case "Float": case "float":
											currentValue = parseFloat(currentValue).toFixed(dataPrecision);
											break;

											case "Integer": case "integer":
											currentValue = parseInt(currentValue);
											break;
										}
										$('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
										$('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
										$('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
										$('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
										$('#<?= $_REQUEST['name_w'] ?>_knob').css('transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
										$('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html(currentValue);
										$('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({ maxFontPixels: -20});

										if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
										{
											$("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
										}

										if(domainType === 'continuous')
										{
											if(continuousRanges !== null)
											{
												for(var i in continuousRanges)
												{
													if((currentValue >= parseFloat(continuousRanges[i].min))&&(currentValue <= parseFloat(continuousRanges[i].max)))
													{
														$('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-moz-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
														$('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-webkit-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
														$('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
														hoverRotationColor = continuousRanges[i].color;
													}
												}
											}
											else
											{
												hoverRotationColor = "rgba(51, 204, 255, 1)";
											}

											$('#<?= $_REQUEST['name_w'] ?>_knobIndicator').off("hover");
											$('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-moz-box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
											$('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-webkit-box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
											$('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
										}
										stopFlag = 1;
							}
							//////////////////////////////////////
						}else{

						}
                    }
		});
		
		
		///
        if(Window.webSockets == undefined)
          Window.webSockets = {};
        
        console.log("<?= $_REQUEST['name_w'] ?>");
        
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
            widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
            widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
            widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>"; 
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
        
        $('#<?= $_REQUEST['name_w'] ?>_countdownDiv').hide();
        
        //Specifiche per questo widget
        var pattern = /Percentuale\//;
        
        //Definizioni di funzione specifiche del widget
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
            showWidgetContent(widgetName);
            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
            $("#<?= $_REQUEST['name_w'] ?>_loadErrorAlert").hide();
            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("position", "relative");
            
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
            
            //Nuova versione box model
            knobHeight = minDim - 60;
            knobWidth = knobHeight;
            
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('width', knobWidth + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('height', knobHeight + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('border-radius', '100%');
            
            var knobMarginLeft = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width() - $('#<?= $_REQUEST['name_w'] ?>_knob').width()) / 2;
            var knobMarginTop = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height() - $('#<?= $_REQUEST['name_w'] ?>_knob').height()) / 2;
            $('#<?= $_REQUEST['name_w'] ?>_knob').css("margin-left", knobMarginLeft + "px");
            $('#<?= $_REQUEST['name_w'] ?>_knob').css("margin-top", knobMarginTop + "px");
            
            tickPxDim = knobHeight + 25;
            tickPxHeight = 12;
            tickInnerTopPx = tickPxHeight;
            incrementControlDim = 18;
            
            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("height", parseFloat(minDim*indicatorRadius/100) + "px");
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html(currentValue);
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').width(minDim*displayRadius/100);
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').height(minDim*displayRadius/100);
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("border-radius", '100%');
            $("#<?= $_REQUEST['name_w'] ?>_innerCircle").css('font-size', fontSize + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                maxFontPixels: -20
            });

            if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
            {
                $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("color", fontColor);
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("background-color", displayColor);
            
            $('#<?= $_REQUEST['name_w'] ?>_ticks').css("width", "100%");
            $('#<?= $_REQUEST['name_w'] ?>_ticks').css("height", "100%");
            
            tickDeltaAngle = parseFloat(angleRange / ticksNumber);
            
            var lastTickAngle, newTick, lastTickNormAngle, lastTickDataValue, lastTickRangeIndex, lastTickActiveColor, tickValueCompAngle, tickValueContainer = null;
            
            lastTickNormAngle = 0;
            lastTickDataValue = convFactor*lastTickNormAngle + minValue;
            
            //Tacca indicatrice del minimo
            lastTickAngle = startAngle;
            newTick = $('<div class="tick" data-angle="' + lastTickNormAngle + '" data-value="' + lastTickDataValue + '" data-rangeIndex="' + lastTickRangeIndex + '" data-activeColor="' + lastTickActiveColor + '"><div class="tickInner" ondragstart="return false;" ondrop="return false;"></div><div class="tickValue" ondragstart="return false;" ondrop="return false;"></div></div>');
            $('#<?= $_REQUEST['name_w'] ?>_ticks').append(newTick);
            newTick.width(tickPxDim);
            newTick.height(tickPxDim);
            newTick.css("border-radius", '100%');
            newTick.css("-webkit-transform", "rotate(" + lastTickAngle + "deg)");
            newTick.css("-moz-transform", "rotate(" + lastTickAngle + "deg)");
            newTick.css("-o-transform", "rotate(" + lastTickAngle + "deg)");
            newTick.css("-ms-transform", "rotate(" + lastTickAngle + "deg)");
            newTick.css("transform", "rotate(" + lastTickAngle + "deg)");
            newTick.find(".tickInner").css("top", "-" + tickInnerTopPx + "px");
            tickValueCompAngle = 360 - lastTickAngle;
            newTick.find('.tickValue').html(minValue.toFixed(0));
            newTick.find('.tickValue').css("-webkit-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.tickValue').css("-moz-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.tickValue').css("-o-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.tickValue').css("-ms-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.tickValue').css("transform", "rotate(" + tickValueCompAngle + "deg)");

            newTick.find('.tickValue').css("font-size", labelsFontSize + "px");
            newTick.find('.tickValue').css("color", labelsFontColor);
            if(labelsFontFamily !== "Auto")
            {
                newTick.find('.tickValue').css("font-family", labelsFontFamily);
            }
            else
            {
                newTick.find('.tickValue').css("font-family", "Verdana, sans-serif");
            }

            newTickValueLeft = Math.ceil((newTick.width() - newTick.find('.tickValue').width())/2);
            newTick.find('.tickValue').css("left", newTickValueLeft + "px");
            newTick.find('.tickValue').css("top", "-" + parseInt(tickPxHeight + 4 + parseInt(newTick.find('.tickValue').height())) + "px");
            
            //Tacca indicatrice del massimo
            lastTickAngle = endAngle;
            newTick = $('<div class="tick" data-angle="' + lastTickNormAngle + '" data-value="' + lastTickDataValue + '" data-rangeIndex="' + lastTickRangeIndex + '" data-activeColor="' + lastTickActiveColor + '"><div class="tickInner" ondragstart="return false;" ondrop="return false;"></div><div class="tickValue" ondragstart="return false;" ondrop="return false;"></div></div>');
            $('#<?= $_REQUEST['name_w'] ?>_ticks').append(newTick);
            newTick.width(tickPxDim);
            newTick.height(tickPxDim);
            newTick.css("border-radius", '100%');
            newTick.css("-webkit-transform", "rotate(" + lastTickAngle + "deg)");
            newTick.css("-moz-transform", "rotate(" + lastTickAngle + "deg)");
            newTick.css("-o-transform", "rotate(" + lastTickAngle + "deg)");
            newTick.css("-ms-transform", "rotate(" + lastTickAngle + "deg)");
            newTick.css("transform", "rotate(" + lastTickAngle + "deg)");
            newTick.find(".tickInner").css("top", "-" + tickInnerTopPx + "px");
            tickValueCompAngle = 360 - lastTickAngle;
            newTick.find('.tickValue').html(maxValue.toFixed(0));
            newTick.find('.tickValue').css("-webkit-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.tickValue').css("-moz-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.tickValue').css("-o-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.tickValue').css("-ms-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.tickValue').css("transform", "rotate(" + tickValueCompAngle + "deg)");

            newTick.find('.tickValue').css("font-size", labelsFontSize + "px");
            newTick.find('.tickValue').css("color", labelsFontColor);
            if(labelsFontFamily !== "Auto")
            {
                newTick.find('.tickValue').css("font-family", labelsFontFamily);
            }
            else
            {
                newTick.find('.tickValue').css("font-family", "Verdana, sans-serif");
            }

            newTickValueLeft = Math.ceil((newTick.width() - newTick.find('.tickValue').width())/2);
            newTick.find('.tickValue').css("left", newTickValueLeft + "px");
            newTick.find('.tickValue').css("top", "-" + parseInt(tickPxHeight + 4 + parseInt(newTick.find('.tickValue').height())) + "px");
            
            //Pulsante -
            incrementControlAngle = startAngle - 30*(152/minDim);
            
            newTick = $('<div class="tick"><div id="<?= $_REQUEST['name_w'] ?>_minusControl" class="incrementControlInner centerWithFlex">-</div></div>');
            $('#<?= $_REQUEST['name_w'] ?>_ticks').append(newTick);
            
            $('#<?= $_REQUEST['name_w'] ?>_minusControl').hover(function(){
                $(this).css("color", hoverIndicatorColor);
                $(this).css("box-shadow", "0px 0px 2px 2px #737373, 0px 0px 4px 4px " + hoverIndicatorColor + ", inset 0px 0px 1px 1px #FFFFFF");
            }, function(){
                $(this).css("color", "black");
                $(this).css("box-shadow", "0px 0px 2px 2px #737373, inset 0px 0px 1px 1px #FFFFFF");
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_minusControl').click(function(){
                changeValueByControl("minus");
                updatedFlag = false;
            });
            
            newTick.width(tickPxDim);
            newTick.height(tickPxDim);
            newTick.css("border-radius", '100%');
            newTick.find('.incrementControlInner').css("width", incrementControlDim + "px");
            newTick.find('.incrementControlInner').css("height", incrementControlDim + "px");
            newTick.find('.incrementControlInner').css("border-radius", parseInt(incrementControlDim/2) + "px");
            newTick.find('.incrementControlInner').css("background-color", displayColor);
            newTick.find('.incrementControlInner').css("font-family", "Digital");
            newTick.find('.incrementControlInner').css("font-size", "48px");
            newTick.css("-webkit-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-moz-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-o-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-ms-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("transform", "rotate(" + incrementControlAngle + "deg)");
            
            tickValueCompAngle = 360 - incrementControlAngle;
            newTick.find('.incrementControlInner').css("-webkit-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-moz-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-o-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-ms-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("transform", "rotate(" + tickValueCompAngle + "deg)");

            newTickValueLeft = Math.ceil((newTick.width() - newTick.find('.incrementControlInner').width())/2);
            newTick.find('.incrementControlInner').css("left", newTickValueLeft + "px");
            newTick.find('.incrementControlInner').css("top", "-" + incrementControlDim + "px");
            
            //Pulsante +
            incrementControlAngle = endAngle + 30*(152/minDim);
            
            newTick = $('<div class="tick"><div id="<?= $_REQUEST['name_w'] ?>_plusControl" class="incrementControlInner centerWithFlex">+</div></div>');
            $('#<?= $_REQUEST['name_w'] ?>_ticks').append(newTick);
            
            $('#<?= $_REQUEST['name_w'] ?>_plusControl').hover(function(){
                $(this).css("color", hoverIndicatorColor);
                $(this).css("box-shadow", "0px 0px 2px 2px #737373, 0px 0px 4px 4px " + hoverIndicatorColor + ", inset 0px 0px 1px 1px #FFFFFF");
            }, function(){
                $(this).css("color", "black");
                $(this).css("box-shadow", "0px 0px 2px 2px #737373, inset 0px 0px 1px 1px #FFFFFF");
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_plusControl').click(function(){
                changeValueByControl("plus");
                updatedFlag = false;
            });
            
            newTick.width(tickPxDim);
            newTick.height(tickPxDim);
            newTick.css("border-radius", '100%');
            newTick.find('.incrementControlInner').css("width", incrementControlDim + "px");
            newTick.find('.incrementControlInner').css("height", incrementControlDim + "px");
            newTick.find('.incrementControlInner').css("border-radius", parseInt(incrementControlDim/2) + "px");
            newTick.find('.incrementControlInner').css("background-color", displayColor);
            newTick.find('.incrementControlInner').css("font-family", "Digital");
            newTick.find('.incrementControlInner').css("font-size", "48px");
            newTick.css("-webkit-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-moz-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-o-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-ms-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("transform", "rotate(" + incrementControlAngle + "deg)");
            
            tickValueCompAngle = 360 - incrementControlAngle;
            newTick.find('.incrementControlInner').css("-webkit-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-moz-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-o-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-ms-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("transform", "rotate(" + tickValueCompAngle + "deg)");

            newTickValueLeft = Math.ceil((newTick.width() - newTick.find('.incrementControlInner').width())/2);
            newTick.find('.incrementControlInner').css("left", newTickValueLeft + "px");
            newTick.find('.incrementControlInner').css("top", "-" + incrementControlDim + "px");
            
            //Scala dei valori
            $('#<?= $_REQUEST['name_w'] ?>_gradCanvas').width($("#<?= $_REQUEST['name_w'] ?>_chartContainer").width());
            $('#<?= $_REQUEST['name_w'] ?>_gradCanvas').height($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height());
            if(showHeader)
            {
                $("#<?= $_REQUEST['name_w'] ?>_gradCanvas").css("margin-top", "25px");
            }
            
            if(domainType === 'continuous')
            {
                if(continuousRanges !== null)
                {
                    var rangeMinValue, rangeMaxValue, rangeMinAngle, rangeMinNormAngle, rangeMaxAngle, rangeMaxNormAngle, newFilter, newPath = null;
                    for(var k in continuousRanges)
                    {
                        rangeMinValue = parseInt(continuousRanges[k].min);
                        rangeMinNormAngle = (rangeMinValue - minValue)/convFactor;
                        rangeMinAngle = Math.ceil(startAngle + rangeMinNormAngle);
                        if(rangeMinAngle > 360)
                        {
                            rangeMinAngle = rangeMinAngle - 360;
                        }
                        
                        if((rangeMinAngle >= startAngle)&&(rangeMinAngle <= 360))
                        {
                            rangeMinAngle = rangeMinAngle - 360;
                        }
                        
                        rangeMaxValue = parseInt(continuousRanges[k].max);
                        rangeMaxNormAngle = (rangeMaxValue - minValue)/convFactor;
                        rangeMaxAngle = Math.ceil(startAngle + rangeMaxNormAngle);
                        
                        //Costruzione della tacca del valore massimo di quell'intervallo
                        lastTickAngle = rangeMaxAngle;
                        newTick = $('<div class="tick" ><div class="tickInner" ondragstart="return false;" ondrop="return false;"></div><div class="tickValue" ondragstart="return false;" ondrop="return false;"></div></div>');
                        $('#<?= $_REQUEST['name_w'] ?>_ticks').append(newTick);
                        newTick.width(tickPxDim);
                        newTick.height(tickPxDim);
                        newTick.css("border-radius", '100%');
                        newTick.css("-webkit-transform", "rotate(" + lastTickAngle + "deg)");
                        newTick.css("-moz-transform", "rotate(" + lastTickAngle + "deg)");
                        newTick.css("-o-transform", "rotate(" + lastTickAngle + "deg)");
                        newTick.css("-ms-transform", "rotate(" + lastTickAngle + "deg)");
                        newTick.css("transform", "rotate(" + lastTickAngle + "deg)");
                        newTick.find(".tickInner").css("top", "-" + tickInnerTopPx + "px");
                        tickValueCompAngle = 360 - lastTickAngle;
                        newTick.find('.tickValue').html(rangeMaxValue.toFixed(0));
                        newTick.find('.tickValue').css("-webkit-transform", "rotate(" + tickValueCompAngle + "deg)");
                        newTick.find('.tickValue').css("-moz-transform", "rotate(" + tickValueCompAngle + "deg)");
                        newTick.find('.tickValue').css("-o-transform", "rotate(" + tickValueCompAngle + "deg)");
                        newTick.find('.tickValue').css("-ms-transform", "rotate(" + tickValueCompAngle + "deg)");
                        newTick.find('.tickValue').css("transform", "rotate(" + tickValueCompAngle + "deg)");

                        newTick.find('.tickValue').css("font-size", labelsFontSize + "px");
                        newTick.find('.tickValue').css("color", labelsFontColor);
                        if(labelsFontFamily !== "Auto")
                        {
                            newTick.find('.tickValue').css("font-family", labelsFontFamily);
                        }
                        else
                        {
                            newTick.find('.tickValue').css("font-family", "Verdana, sans-serif");
                        }

                        newTickValueLeft = Math.ceil((newTick.width() - newTick.find('.tickValue').width())/2);
                        newTick.find('.tickValue').css("left", newTickValueLeft + "px");
                        newTick.find('.tickValue').css("top", "-" + parseInt(tickPxHeight + 4 + parseInt(newTick.find('.tickValue').height())) + "px");
                        
                        if(rangeMaxAngle > 360)
                        {
                            rangeMaxAngle = rangeMaxAngle - 360;
                        }
                        
                        if((rangeMaxAngle >= startAngle)&&(rangeMaxAngle <= 360))
                        {
                            rangeMaxAngle = rangeMaxAngle - 360;
                        }
                        
                        newPath = document.createElementNS("http://www.w3.org/2000/svg", 'path');
                        newPath.setAttribute("id", "range" + k);
                        newPath.setAttribute("d", describeArc(($("#<?= $_REQUEST['name_w'] ?>_gradCanvas").width() / 2), ($("#<?= $_REQUEST['name_w'] ?>_gradCanvas").height() / 2), parseInt(knobHeight/2 + 15), rangeMinAngle, rangeMaxAngle));
                        newPath.setAttribute("fill", "transparent");
                        newPath.setAttribute("stroke-width", "2");
                        newPath.setAttribute("stroke", continuousRanges[k].color);
                        newPath.setAttribute("stroke-linecap", "round");
                        document.getElementById('<?= $_REQUEST['name_w'] ?>_gradCanvas').appendChild(newPath);
                    }
                }
                else
                {
                    rangeMinAngle = startAngle;
                    rangeMaxAngle = endAngle;
                    
                    if(rangeMinAngle > 360)
                    {
                        rangeMinAngle = rangeMinAngle - 360;
                    }

                    if((rangeMinAngle >= startAngle)&&(rangeMinAngle <= 360))
                    {
                        rangeMinAngle = rangeMinAngle - 360;
                    }

                    if(rangeMaxAngle > 360)
                    {
                        rangeMaxAngle = rangeMaxAngle - 360;
                    }

                    if((rangeMaxAngle >= startAngle)&&(rangeMaxAngle <= 360))
                    {
                        rangeMaxAngle = rangeMaxAngle - 360;
                    }
                
                    newPath = document.createElementNS("http://www.w3.org/2000/svg", 'path');
                    newPath.setAttribute("id", "range");
                    newPath.setAttribute("d", describeArc(($("#<?= $_REQUEST['name_w'] ?>_gradCanvas").width() / 2), ($("#<?= $_REQUEST['name_w'] ?>_gradCanvas").height() / 2), parseInt(knobHeight/2 + 15), rangeMinAngle, rangeMaxAngle));
                    newPath.setAttribute("fill", "transparent");
                    newPath.setAttribute("stroke-width", "2");
                    newPath.setAttribute("stroke", "rgba(51, 204, 255, 1)");
                    newPath.setAttribute("stroke-linecap", "round");
                    document.getElementById('<?= $_REQUEST['name_w'] ?>_gradCanvas').appendChild(newPath);
                }
            }
            
            
            
            
            //Vecchio codice tacche troppo bloccanti: mantenerlo per rileggere concetto
            /*for(var i = 0; i <= ticksNumber; i++)
            {
                newTick = $('<div class="tick" data-angle="' + lastTickNormAngle + '" data-value="' + lastTickDataValue + '" data-rangeIndex="' + lastTickRangeIndex + '" data-activeColor="' + lastTickActiveColor + '"><div class="tickInner" ondragstart="return false;" ondrop="return false;"></div><div class="tickValue" ondragstart="return false;" ondrop="return false;"></div></div>');
                $('#<?= $_REQUEST['name_w'] ?>_ticks').append(newTick);
                newTick.width(minDim*tickPxDim);
                newTick.height(minDim*tickPxDim);
                newTick.css("border-radius", minDim*tickPxDim/2);
                newTick.css("-webkit-transform", "rotate(" + lastTickAngle + "deg)");
                newTick.css("-moz-transform", "rotate(" + lastTickAngle + "deg)");
                newTick.css("-o-transform", "rotate(" + lastTickAngle + "deg)");
                newTick.css("-ms-transform", "rotate(" + lastTickAngle + "deg)");
                newTick.css("transform", "rotate(" + lastTickAngle + "deg)");
                newTick.find(".tickInner").css("top", "-" + tickInnerTopPx + "px");
                
                if((i === 0)||(i === ticksNumber))
                {
                    tickValueCompAngle = 360 - lastTickAngle;
                    newTick.find('.tickValue').text(lastTickDataValue.toFixed(0));
                    newTick.find('.tickValue').css("-webkit-transform", "rotate(" + tickValueCompAngle + "deg)");
                    newTick.find('.tickValue').css("-moz-transform", "rotate(" + tickValueCompAngle + "deg)");
                    newTick.find('.tickValue').css("-o-transform", "rotate(" + tickValueCompAngle + "deg)");
                    newTick.find('.tickValue').css("-ms-transform", "rotate(" + tickValueCompAngle + "deg)");
                    newTick.find('.tickValue').css("transform", "rotate(" + tickValueCompAngle + "deg)");
                    
                    newTick.find('.tickValue').css("font-size", labelsFontSize + "px");
                    newTick.find('.tickValue').css("color", labelsFontColor);
                    if(labelsFontFamily !== "Auto")
                    {
                        newTick.find('.tickValue').css("font-family", labelsFontFamily);
                    }
                    else
                    {
                        newTick.find('.tickValue').css("font-family", "Verdana, sans-serif");
                    }
                    
                    newTickValueLeft = Math.ceil((newTick.width() - newTick.find('.tickValue').width())/2);
                    newTick.find('.tickValue').css("left", newTickValueLeft + "px");
                    newTick.find('.tickValue').css("top", "-" + parseInt(tickPxHeight + 4 + parseInt(newTick.find('.tickValue').height())) + "px");
                    
                }
                
                lastTickAngle = lastTickAngle + tickDeltaAngle;
                lastTickNormAngle = lastTickNormAngle + tickDeltaAngle;
                lastTickDataValue = convFactor*lastTickNormAngle + minValue;
                
                if(domainType === 'continuous')
                {
                    if(continuousRanges !== null)
                    {
                        for(var k in continuousRanges)
                        {
                            if((lastTickDataValue >= parseFloat(continuousRanges[k].min))&&(lastTickDataValue <= parseFloat(continuousRanges[k].max)))
                            {
                                lastTickRangeIndex = k;
                                lastTickActiveColor = continuousRanges[k].color;
                            }
                        }
                    }
                }
            }*/
            
            $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("background-color", "transparent");
            $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("height", tickPxHeight + "px");
            
            var tickMarginLeft = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width() - $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').eq(0).width()) / 2;
            var tickMarginTop = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height() - $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').eq(0).height()) / 2;
            
            $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').css("margin-top", tickMarginTop + "px");
            $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').css("margin-left", tickMarginLeft + "px");
            
            offset = $('#<?= $_REQUEST['name_w'] ?>_knob').offset();
            mouseDown = false;
            
            currentNormAngle = (currentValue - minValue)/convFactor;
            currentAngle = startAngle + currentNormAngle;
            if(currentAngle > 360)
            {
                currentAngle = currentAngle - 360;
            }
            
            oldAngle = currentAngle;
            
            if(domainType === 'continuous')
            {
                activeTicks = (Math.round(currentNormAngle / tickDeltaAngle) + 1);
                
                if(continuousRanges !== null)
                {
                    for(var i in continuousRanges)
                    {
                        if((currentValue >= parseFloat(continuousRanges[i].min))&&(currentValue <= parseFloat(continuousRanges[i].max)))
                        {
                            /*$('#<?= $_REQUEST['name_w'] ?>_halo').css("background", continuousRanges[i].color);
                            $('#<?= $_REQUEST['name_w'] ?>_halo').css("-moz-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));
                            $('#<?= $_REQUEST['name_w'] ?>_halo').css("-webkit-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));
                            $('#<?= $_REQUEST['name_w'] ?>_halo').css("box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));*/
                            
                            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-moz-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-webkit-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                            
                            hoverIndicatorColor = continuousRanges[i].color;
                        }
                        
                        /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').each(function(i){
                            if(currentValue >= parseFloat($(this).attr('data-value')))
                            {
                                var thisTickActiveColor = $(this).attr('data-activeColor');
                                $(this).addClass('activetick');
                                $(this).find('.tickInner').css("background-color", thisTickActiveColor);
                                $(this).find('.tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                $(this).find('.tickInner').css("-moz-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                $(this).find('.tickInner').css("box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                            }
                        });*/
                    }
                }
                else
                {
                    /*$('#<?= $_REQUEST['name_w'] ?>_halo').css("background", "rgba(0, 157, 220, 1)");
                    $('#<?= $_REQUEST['name_w'] ?>_halo').css("-moz-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px rgba(0, 157, 220, 1), 0 0 25px rgba(0, 157, 220, 1)");
                    $('#<?= $_REQUEST['name_w'] ?>_halo').css("-webkit-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px rgba(0, 157, 220, 1) 0 0 25px rgba(0, 157, 220, 1)");
                    $('#<?= $_REQUEST['name_w'] ?>_halo').css("box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px rgba(0, 157, 220, 1), 0 0 25px rgba(0, 157, 220, 1)");*/
                    
                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-moz-box-shadow", "2px 2px 4px rgba(51, 204, 255, 1), 2px -2px 4px rgba(51, 204, 255, 1), -2px 2px 4px rgba(51, 204, 255, 1), -2px -2px 4px rgba(51, 204, 255, 1)");
                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-webkit-box-shadow", "2px 2px 4px rgba(51, 204, 255, 1), 2px -2px 4px rgba(51, 204, 255, 1), -2px 2px 4px rgba(51, 204, 255, 1), -2px -2px 4px rgba(51, 204, 255, 1)");
                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("box-shadow", "2px 2px 4px rgba(51, 204, 255, 1), 2px -2px 4px rgba(51, 204, 255, 1), -2px 2px 4px rgba(51, 204, 255, 1), -2px -2px 4px rgba(51, 204, 255, 1)");
                    
                    hoverIndicatorColor = "rgba(51, 204, 255, 1)";
                    
                    /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').slice(0, activeTicks).addClass('activetick');
                    $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("background-color", "rgba(51, 204, 255, 1)");
                    $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                    $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-moz-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                    $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");*/
                }
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transform', 'rotate(' + currentAngle + 'deg)');
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transform', 'rotate(' + currentAngle + 'deg)');
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transform', 'rotate(' + currentAngle + 'deg)');
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transform', 'rotate(' + currentAngle + 'deg)');
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('transform', 'rotate(' + currentAngle + 'deg)');
            
            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').mousedown(handleMouseDown);
        }
        
        function polarToCartesian(centerX, centerY, radius, angleInDegrees) 
        {
            var angleInRadians = (angleInDegrees-90) * Math.PI / 180.0;

            return {
              x: centerX + (radius * Math.cos(angleInRadians)),
              y: centerY + (radius * Math.sin(angleInRadians))
            };
        }
        
        function describeArc(x, y, radius, startAngle, endAngle)
        {
            var start = polarToCartesian(x, y, radius, endAngle);
            var end = polarToCartesian(x, y, radius, startAngle);

            var largeArcFlag = Math.abs(endAngle - startAngle) <= 180 ? "0" : "1";
            var sweepFlag = 0;

            var d = [
                "M", start.x, start.y, 
                "A", radius, radius, 0, largeArcFlag, sweepFlag, end.x, end.y
            ].join(" ");

            return d;       
        }
        
        function rotateKnob(evt)   
        {
            var newValue = null;
            var clockCrossRotation = false;
            var anticlockCrossRotation = false;
            if((evt === "plus")||(evt === "minus"))
            {
                switch(evt)
                {
                    case "plus":
                        switch(dataType)
                        {
                            case "Float": case "float":
                                newValue = (parseFloat(currentValue) + parseFloat(incrementControlDeltaValue)).toFixed(dataPrecision);
                                break;

                            case "Integer": case "integer":
                                newValue = parseInt(currentValue) + incrementControlDeltaValue;
                                break;
                        }
                        
                        if(newValue > maxValue)
                        {
                            newValue = maxValue;
                        }
                        break;
                        
                    case "minus":
                        switch(dataType)
                        {
                            case "Float": case "float":
                                newValue = (parseFloat(currentValue) - parseFloat(incrementControlDeltaValue)).toFixed(dataPrecision);
                                break;

                            case "Integer": case "integer":
                                newValue = parseInt(currentValue) - incrementControlDeltaValue;
                                break;
                        }
                        if(newValue < minValue)
                        {
                            newValue = minValue;
                        }
                        break;
                }
                
                oldAngle = currentAngle;
                currentNormAngle = (newValue - minValue)/convFactor;
                currentAngle = startAngle + currentNormAngle;
                
                if(currentAngle > 360)
                {
                    currentAngle = currentAngle - 360;
                }
                
                restoredAngle = currentAngle;
                currentValue = newValue;

                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transition', 'transform 500ms ease');
                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transition', 'transform 500ms ease');
                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transition', 'transform 500ms ease');
                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transition', 'transform 500ms ease');
                $('#<?= $_REQUEST['name_w'] ?>_knob').css('transition', 'transform 500ms ease');
                
                if((oldAngle >= 0)&&(oldAngle <= endAngle)&&(currentAngle >= startAngle)&&(currentAngle <= 360))
                {
                    //Rotazione antioraria - OK ma bug: dopo la cross, se fai ancora - fa un giro orario
                    restoredAngle = -Math.abs(currentAngle - 360);
                    anticlockCrossRotation = true;
                }
                else
                {
                    if((oldAngle >= startAngle)&&(oldAngle <= 360)&&(currentAngle >= 0)&&(currentAngle <= endAngle))
                    {
                        //Rotazione oraria
                        restoredAngle = Math.abs(360 + currentAngle);
                        clockCrossRotation = true;
                    }
                    else
                    {
                        restoredAngle = Math.abs(currentAngle);
                    }
                }
                
                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)');
                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)');
                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)');
                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)');
                $('#<?= $_REQUEST['name_w'] ?>_knob').css('transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)');
                
                setTimeout(function(){
                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transition', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transition', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transition', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transition', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('transition', 'none');
                    
                    if(anticlockCrossRotation||clockCrossRotation)
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transform', 'rotate3d(0, 0, 1, ' + Math.abs(currentAngle) + 'deg)');
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transform', 'rotate3d(0, 0, 1, ' + Math.abs(currentAngle) + 'deg)');
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transform', 'rotate3d(0, 0, 1, ' + Math.abs(currentAngle) + 'deg)');
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transform', 'rotate3d(0, 0, 1, ' + Math.abs(currentAngle) + 'deg)');
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('transform', 'rotate3d(0, 0, 1, ' + Math.abs(currentAngle) + 'deg)');
                    }
                }, 500);
                
                $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html(currentValue);
                
                $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                    maxFontPixels: -20
                });

                if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
                {
                    $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
                }


                /*activeTicks = (Math.round(currentNormAngle / tickDeltaAngle) + 1);
                $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').slice(0, activeTicks).addClass('activetick');*/

                if(domainType === 'continuous')
                {
                    if(continuousRanges !== null)
                    {
                        for(var i in continuousRanges)
                        {
                            if((currentValue >= parseFloat(continuousRanges[i].min))&&(currentValue <= parseFloat(continuousRanges[i].max)))
                            {
                                /*$('#<?= $_REQUEST['name_w'] ?>_halo').css("background", continuousRanges[i].color);
                                $('#<?= $_REQUEST['name_w'] ?>_halo').css("-moz-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));
                                $('#<?= $_REQUEST['name_w'] ?>_halo').css("-webkit-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));
                                $('#<?= $_REQUEST['name_w'] ?>_halo').css("box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));*/

                                $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-moz-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-webkit-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                hoverRotationColor = continuousRanges[i].color;
                            }

                           /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                            $('#<?= $_REQUEST['name_w'] ?>_ticks .tick .tickInner').css("background-color", ticksColor);
                            $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-webkit-box-shadow", "none");
                            $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-moz-box-shadow", "none");
                            $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("box-shadow", "none");*/

                            /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').each(function(i){    
                                if(currentValue >= parseFloat($(this).attr('data-value')))
                                {
                                    var thisTickActiveColor = $(this).attr('data-activeColor');
                                    $(this).addClass('activetick');
                                    $(this).find('.tickInner').css("background-color", thisTickActiveColor);
                                    $(this).find('.tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                    $(this).find('.tickInner').css("-moz-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                    $(this).find('.tickInner').css("box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                }
                            });*/
                        }
                    }
                    else
                    {
                        /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tick .tickInner').css("background-color", ticksColor);
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-webkit-box-shadow", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-moz-box-shadow", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("box-shadow", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').slice(0, activeTicks).addClass('activetick');
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("background-color", "rgba(51, 204, 255, 1)");
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-moz-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");*/
                        hoverRotationColor = "rgba(51, 204, 255, 1)";
                    }

                    $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').off("hover");
                    $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-moz-box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
                    $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-webkit-box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
                    $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("box-shadow", "0px 0px 2px 2px " + hoverRotationColor); 
                }
            }
            else
            {
                if(mouseDown === true)
                {
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("cursor", "move");
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("cursor", "grabbing");
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("cursor", "-moz-grabbing");
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("cursor", "-webkit-grabbing");
                    $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("cursor", "-ms-grabbing");

                    var center_x = offset.left + ($('#<?= $_REQUEST['name_w'] ?>_knob').width() / 2);
                    var center_y = offset.top + ($('#<?= $_REQUEST['name_w'] ?>_knob').height() / 2);
                    var mouse_x = evt.pageX;
                    var mouse_y = evt.pageY;
                    var rad = Math.atan2(mouse_x - center_x, mouse_y - center_y);
                    var newAngle = (rad * (180 / Math.PI) * -1) + 180; //Lasciarci il + 180, sennò aggiunge un angolo indesiderato

                    if((newAngle >= 0)&&(newAngle <= endAngle)&&(parseFloat(newAngle/endAngle).toFixed(4) > 0.99)&&(parseFloat(newAngle/endAngle).toFixed(4) <= 1))
                    {
                        newAngle = endAngle;
                    }
                    else
                    {
                        if((newAngle >= startAngle)&&(newAngle <= 360)&&(parseFloat(newAngle/startAngle).toFixed(4) > 0.99)&&(parseFloat(newAngle/startAngle).toFixed(4) <= 1.01))
                        {
                            newAngle = startAngle;
                        }
                    }

                    //Condizione ok ma con bug if((((newAngle >= 0)&&(newAngle <= endAngle))||((newAngle >= startAngle)&&(newAngle <= 360))))
                    if((((newAngle >= 0)&&(newAngle <= endAngle))||((newAngle >= startAngle)&&(newAngle <= 360))))
                    {
                        currentAngle = newAngle;
                        if((currentAngle >= startAngle)&&(currentAngle <= 360))
                        {
                            currentNormAngle = currentAngle - startAngle;
                        }
                        else
                        {
                            if((currentAngle >= 0)&&(currentAngle <= endAngle))
                            {
                                currentNormAngle = 360 - startAngle + currentAngle;
                            }
                        }

                        currentValue = currentNormAngle*convFactor + minValue;
                        switch(dataType)
                        {
                            case "Float": case "float":
                                currentValue = parseFloat(currentValue).toFixed(dataPrecision);
                                break;

                            case "Integer": case "integer":
                                currentValue = parseInt(currentValue);
                                break;
                        }
                        
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
                        $('#<?= $_REQUEST['name_w'] ?>_knob').css('transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)'); 
                        
                        $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html(currentValue);
                        
                        $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                            maxFontPixels: -20
                        });

                        if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
                        }


                        /*activeTicks = (Math.round(currentNormAngle / tickDeltaAngle) + 1);
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').slice(0, activeTicks).addClass('activetick');*/

                        if(domainType === 'continuous')
                        {
                            if(continuousRanges !== null)
                            {
                                for(var i in continuousRanges)
                                {
                                    if((currentValue >= parseFloat(continuousRanges[i].min))&&(currentValue <= parseFloat(continuousRanges[i].max)))
                                    {
                                        /*$('#<?= $_REQUEST['name_w'] ?>_halo').css("background", continuousRanges[i].color);
                                        $('#<?= $_REQUEST['name_w'] ?>_halo').css("-moz-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));
                                        $('#<?= $_REQUEST['name_w'] ?>_halo').css("-webkit-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));
                                        $('#<?= $_REQUEST['name_w'] ?>_halo').css("box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));*/

                                        $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-moz-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                        $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-webkit-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                        $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                        hoverRotationColor = continuousRanges[i].color;
                                    }

                                   /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tick .tickInner').css("background-color", ticksColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-webkit-box-shadow", "none");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-moz-box-shadow", "none");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("box-shadow", "none");*/

                                    /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').each(function(i){    
                                        if(currentValue >= parseFloat($(this).attr('data-value')))
                                        {
                                            var thisTickActiveColor = $(this).attr('data-activeColor');
                                            $(this).addClass('activetick');
                                            $(this).find('.tickInner').css("background-color", thisTickActiveColor);
                                            $(this).find('.tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                            $(this).find('.tickInner').css("-moz-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                            $(this).find('.tickInner').css("box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                        }
                                    });*/
                                }
                            }
                            else
                            {
                                /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tick .tickInner').css("background-color", ticksColor);
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-webkit-box-shadow", "none");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-moz-box-shadow", "none");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("box-shadow", "none");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').slice(0, activeTicks).addClass('activetick');
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("background-color", "rgba(51, 204, 255, 1)");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-moz-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");*/
                                hoverRotationColor = "rgba(51, 204, 255, 1)";
                            }

                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').off("hover");
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-moz-box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-webkit-box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("box-shadow", "0px 0px 2px 2px " + hoverRotationColor); 
                        }

                    }
					console.log('newAngle: '+newAngle);
					
					//EVENTO//
					if (widgetProperties.param.code != null && widgetProperties.param.code != '') {
									//execute();
							var displayVal = newAngle;
							var functionName = "execute_" + "<?= $_REQUEST['name_w'] ?>";
							console.log(functionName);
							window[functionName](displayVal);
					}
					
					//
                }
            }

        }
        
        function handleMouseDown()
        {
            mouseDown = true;
            $('#<?= $_REQUEST['name_w'] ?>_minusControl').off('click');
            $('#<?= $_REQUEST['name_w'] ?>_plusControl').off('click');
            $(document).mousemove(rotateKnob);
            $(document).mouseup(knobRotationEnd);
        }
        
        function knobRotationEnd()
        {
            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-moz-box-shadow", "none");
            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-webkit-box-shadow", "none");
            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("box-shadow", "none");
            mouseDown = false;
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("cursor", "default");
            $(document).off('mouseup', knobRotationEnd);
            updateRemoteValue();
            updatedFlag = false;
        }
        
        function setUpdatingMsg()
        {
            switch(setUpdatingMsgIndex)
            {
                case 0:
                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html("UPDATE");
                    break;
                    
                case 1:
                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html("UPDATE.");
                    break;
                    
                case 2:
                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html("UPDATE..");
                    break;
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                maxFontPixels: -20
            });

            if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
            {
                $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
            }
            
            
            setUpdatingMsgIndex = (setUpdatingMsgIndex + 1)%3;
        }
        
        function updateRemoteValue()
        {
            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').off('mousedown', handleMouseDown);
            
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html("UPDATE");
            
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                maxFontPixels: -20
            });

            if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
            {
                $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
            }
            
            setUpdatingMsgIndex = 0;
            if (updatedEverFlag !== true) {
                setUpdatingMsgInterval = setInterval(setUpdatingMsg, 500);
            }
            
            switch(actuatorTarget)
            {
                case 'broker':
                    $.ajax({
                        url: "../widgets/actuatorUpdateValue.php",
                        type: "POST",
                        data: {
                            "dashboardId": dashboardId,
                            "entityId": JSON.parse(entityJson).id,
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
                            showUpdateResult("API KO");
                            console.log("Update value KO");
                            console.log(JSON.stringify(errorData));
                        }
                    });
                    break;
                    
                case 'app':
                    if(useWebSocket) {
                        var data = {
                              "msgType": "SendToEmitter",
                              "widgetUniqueName": widgetName,
                              "value": currentValue,
                              "inputName": nodeRedInputName,
                              "dashboardId": dashboardId,
                              "username" : $('#authForm #hiddenUsername').val(),
                              "nrInputId": nrInputId
                        };
                        var webSocket = Window.webSockets[widgetName];
                        webSocket.ackReceived=false;
                        webSocket.onAck = function(data) {
                            console.log(widgetName+" SUCCESS ackReceived:"+webSocket.ackReceived)
                            clearInterval(setUpdatingMsgInterval);
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
                        }
                        console.log(widgetName+" SEND ackReceived:"+webSocket.ackReceived)
                        if(webSocket.readyState==webSocket.OPEN) {
                            webSocket.send(JSON.stringify(data));
                            webSocket.timeout = setTimeout(function() {
                              if(!webSocket.ackReceived) {
                                console.log(widgetName+" ERR1 ackReceived:"+webSocket.ackReceived)
                                showUpdateResult("API KO");
                                console.log("Update value KO");
                              }
                            },60000)
                        } else {
                            console.log(widgetName+" ERR1 socket not OPEN");
                            showUpdateResult("API KO");
                        }                      
                    } else {
                        $.ajax({
                            url: "../widgets/actuatorUpdateValuePersonalApps.php",
                            type: "POST",
                            data: {
                                //"endPointHost": endPointHost,
                                //"endPointPort": endPointPort,
                                "inputName": nodeRedInputName,
                                "dashboardId": dashboardId,
                                "widgetName": "<?= $_REQUEST['name_w'] ?>",
                                "username" : $('#authForm #hiddenUsername').val(),
                                "value": currentValue,
                                "endPointPort": "<?= escapeForJS($_REQUEST['endPointPort']) ?>",
                                "httpRoot": "<?= escapeForJS($_REQUEST['httpRoot']) ?>",
                                "nrInputId": nrInputId
                            },
                            async: true,
                            dataType: 'json',
                            success: function(data) 
                            {
                                clearInterval(setUpdatingMsgInterval);
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
                                showUpdateResult("API KO");
                                console.log("Update value KO");
                                console.log(JSON.stringify(errorData));
                            }
                        });
                    }
                    break;
            }
        }
        
        function showUpdateResult(msg)
        {
            clearInterval(setUpdatingMsgInterval);
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css("opacity", 0);
            setTimeout(function(){
                $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html(msg);
            
                $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                    maxFontPixels: -20
                });

                if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
                {
                    $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
                }
                
                $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css("opacity", 1);
                
                setTimeout(function(){
                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css("opacity", 0);
                    setTimeout(function(){
                        $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css("opacity", 1);
                        $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html(currentValue);
            
                        $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                            maxFontPixels: -20
                        });

                        if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
                        }
                               
                        if(msg !== "Device OK")
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transition', 'transform 500ms ease');
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transition', 'transform 500ms ease');
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transition', 'transform 500ms ease');
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transition', 'transform 500ms ease');
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('transition', 'transform 500ms ease');
                            
                            if((oldAngle >= 0)&&(oldAngle <= endAngle)&&(currentAngle >= startAngle)&&(currentAngle <= 360))
                            {
                                //Ripristino orario
                                restoredAngle = currentAngle + Math.abs(oldAngle + 360 - currentAngle);
                            }
                            else
                            {
                                if((oldAngle >= startAngle)&&(oldAngle <= 360)&&(currentAngle >= 0)&&(currentAngle <= endAngle))
                                {
                                    //Ripristino antiorario
                                    restoredAngle = oldAngle - 360;
                                }
                                else
                                {
                                    restoredAngle = oldAngle;
                                }
                            }
                            
                            currentValue = oldValue;
                            currentAngle = oldAngle;
                            
                            if((currentAngle >= startAngle)&&(currentAngle <= 360))
                            {
                                currentNormAngle = currentAngle - startAngle;
                            }
                            else
                            {
                                if((currentAngle >= 0)&&(currentAngle <= endAngle))
                                {
                                   currentNormAngle = 360 - startAngle + currentAngle;
                                }
                            }
                            
                            activeTicks = (Math.round(currentNormAngle / tickDeltaAngle) + 1);
                            
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)');
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)');
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)');
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)');
                            $('#<?= $_REQUEST['name_w'] ?>_knob').css('transform', 'rotate3d(0, 0, 1, ' + restoredAngle + 'deg)'); 
                            
                            $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html(currentValue);
                            
                            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                                maxFontPixels: -20
                            });

                            if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
                            }
                            
                            setTimeout(function(){
                                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transition', 'none');
                                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transition', 'none');
                                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transition', 'none');
                                $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transition', 'none');
                                $('#<?= $_REQUEST['name_w'] ?>_knob').css('transition', 'none');

                                if (updatedFlag !== true) {
                                    $('#<?= $_REQUEST['name_w'] ?>_minusControl').click(function () {
                                        changeValueByControl("minus");
                                        updatedFlag = false;
                                    });

                                    $('#<?= $_REQUEST['name_w'] ?>_plusControl').click(function () {
                                        changeValueByControl("plus");
                                        updatedFlag = false;
                                    });
                                }
                                
                            }, 520);
                            
                            if(domainType === 'continuous')
                            {
                                if(continuousRanges !== null)
                                {
                                    for(var i in continuousRanges)
                                    {
                                        if((currentValue >= parseFloat(continuousRanges[i].min))&&(currentValue <= parseFloat(continuousRanges[i].max)))
                                        {
                                            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-moz-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-webkit-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                            hoverIndicatorColor = continuousRanges[i].color;
                                        }

                                        /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tick .tickInner').css("background-color", ticksColor);
                                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-webkit-box-shadow", "none");
                                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-moz-box-shadow", "none");
                                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("box-shadow", "none");

                                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').each(function(i){    
                                            if(currentValue >= parseFloat($(this).attr('data-value')))
                                            {
                                                var thisTickActiveColor = $(this).attr('data-activeColor');
                                                $(this).addClass('activetick');
                                                $(this).find('.tickInner').css("background-color", thisTickActiveColor);
                                                $(this).find('.tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                                $(this).find('.tickInner').css("-moz-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                                $(this).find('.tickInner').css("box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                            }
                                        });*/
                                    }
                                }
                                else
                                {
                                    /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tick .tickInner').css("background-color", ticksColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-webkit-box-shadow", "none");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-moz-box-shadow", "none");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("box-shadow", "none");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').slice(0, activeTicks).addClass('activetick');
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("background-color", "rgba(51, 204, 255, 1)");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-moz-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");*/
                                }
                            }
                            else
                            {
                                hoverIndicatorColor = "rgba(51, 204, 255, 1)";
                            }
                            
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').mousedown(handleMouseDown);
                        }
                        else
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').mousedown(handleMouseDown);
                            if(domainType === 'continuous')
                            {
                                if(continuousRanges !== null)
                                {
                                    for(var i in continuousRanges)
                                    {
                                        if((currentValue >= parseFloat(continuousRanges[i].min))&&(currentValue <= parseFloat(continuousRanges[i].max)))
                                        {
                                            hoverIndicatorColor = continuousRanges[i].color;
                                        }
                                    }
                                }
                                else
                                {
                                    hoverIndicatorColor = "rgba(51, 204, 255, 1)";
                                }
                            }
                            if (updatedFlag !== true) {
                                $('#<?= $_REQUEST['name_w'] ?>_minusControl').click(function () {
                                    changeValueByControl("minus");
                                    updatedFlag = false;
                                });

                                $('#<?= $_REQUEST['name_w'] ?>_plusControl').click(function () {
                                    changeValueByControl("plus");
                                    updatedFlag = false;
                                });
                            }
                        }
                        
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').off("hover");
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').hover(function(){
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-moz-box-shadow", "0px 0px 2px 2px " + hoverIndicatorColor);
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-webkit-box-shadow", "0px 0px 2px 2px " + hoverIndicatorColor);
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("box-shadow", "0px 0px 2px 2px " + hoverIndicatorColor); 
                        }, function(){
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-moz-box-shadow", "none");
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-webkit-box-shadow", "none");
                            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("box-shadow", "none"); 
                        });
                        
                    }, 180);
                }, 900);
            }, 180);
        }
        
        function changeValueByControl(control)
        {
            $('#<?= $_REQUEST['name_w'] ?>_minusControl').off('click');
            $('#<?= $_REQUEST['name_w'] ?>_plusControl').off('click');
            rotateKnob(control);
            updateRemoteValue();
        }
        
        function resizeWidget() {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            
            //Manopola
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
            
            knobHeight = minDim - 60;
            knobWidth = knobHeight;
            tickPxDim = knobHeight + 25;
            
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('width', knobWidth + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('height', knobHeight + 'px');
            $('#<?= $_REQUEST['name_w'] ?>_knob').css('border-radius', '100%');
            
            var knobMarginLeft = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width() - $('#<?= $_REQUEST['name_w'] ?>_knob').width()) / 2;
            var knobMarginTop = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height() - $('#<?= $_REQUEST['name_w'] ?>_knob').height()) / 2;
            $('#<?= $_REQUEST['name_w'] ?>_knob').css("margin-left", knobMarginLeft + "px");
            $('#<?= $_REQUEST['name_w'] ?>_knob').css("margin-top", knobMarginTop + "px");
            
            //Display centrale
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').width(minDim*displayRadius/100);
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').height(minDim*displayRadius/100);
            
            $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                maxFontPixels: -20
            });

            if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
            {
                $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
            }
            
            //Tacca indicatrice
            $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("height", parseFloat(minDim*indicatorRadius/100) + "px");
            
            //Scala dei valori
            $('#<?= $_REQUEST['name_w'] ?>_gradCanvas').width($("#<?= $_REQUEST['name_w'] ?>_chartContainer").width());
            $('#<?= $_REQUEST['name_w'] ?>_gradCanvas').height($("#<?= $_REQUEST['name_w'] ?>_chartContainer").height());
            if(showHeader)
            {
                $("#<?= $_REQUEST['name_w'] ?>_gradCanvas").css("margin-top", "25px");
            }
            
            if(domainType === 'continuous')
            {
                if(continuousRanges !== null)
                {
                    var rangeMinValue, rangeMaxValue, rangeMinAngle, rangeMinNormAngle, rangeMaxAngle, rangeMaxNormAngle, newFilter, newPath = null;
                    for(var k in continuousRanges)
                    {
                        rangeMinValue = parseInt(continuousRanges[k].min);
                        rangeMinNormAngle = (rangeMinValue - minValue)/convFactor;
                        rangeMinAngle = Math.ceil(startAngle + rangeMinNormAngle);
                        
                        if(rangeMinAngle > 360)
                        {
                            rangeMinAngle = rangeMinAngle - 360;
                        }
                        
                        if((rangeMinAngle >= startAngle)&&(rangeMinAngle <= 360))
                        {
                            rangeMinAngle = rangeMinAngle - 360;
                        }
                        
                        rangeMaxValue = parseInt(continuousRanges[k].max);
                        rangeMaxNormAngle = (rangeMaxValue - minValue)/convFactor;
                        rangeMaxAngle = Math.ceil(startAngle + rangeMaxNormAngle);
                        
                        if(rangeMaxAngle > 360)
                        {
                            rangeMaxAngle = rangeMaxAngle - 360;
                        }
                        
                        if((rangeMaxAngle >= startAngle)&&(rangeMaxAngle <= 360))
                        {
                            rangeMaxAngle = rangeMaxAngle - 360;
                        }
                        
                        document.getElementById('<?= $_REQUEST['name_w'] ?>_gradCanvas').getElementById('range' + k).setAttribute("d", describeArc(($("#<?= $_REQUEST['name_w'] ?>_gradCanvas").width() / 2), ($("#<?= $_REQUEST['name_w'] ?>_gradCanvas").height() / 2), parseInt(knobHeight/2 + 15), rangeMinAngle, rangeMaxAngle));
                    }
                }
                else
                {
                    rangeMinAngle = startAngle;
                    rangeMaxAngle = endAngle;
                    
                    if(rangeMinAngle > 360)
                    {
                        rangeMinAngle = rangeMinAngle - 360;
                    }

                    if((rangeMinAngle >= startAngle)&&(rangeMinAngle <= 360))
                    {
                        rangeMinAngle = rangeMinAngle - 360;
                    }

                    if(rangeMaxAngle > 360)
                    {
                        rangeMaxAngle = rangeMaxAngle - 360;
                    }

                    if((rangeMaxAngle >= startAngle)&&(rangeMaxAngle <= 360))
                    {
                        rangeMaxAngle = rangeMaxAngle - 360;
                    }
                    
                    document.getElementById('<?= $_REQUEST['name_w'] ?>_gradCanvas').getElementById('range').setAttribute("d", describeArc(($("#<?= $_REQUEST['name_w'] ?>_gradCanvas").width() / 2), ($("#<?= $_REQUEST['name_w'] ?>_gradCanvas").height() / 2), parseInt(knobHeight/2 + 15), rangeMinAngle, rangeMaxAngle));
                }
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_minusControl').remove();
            $('#<?= $_REQUEST['name_w'] ?>_plusControl').remove();
            
            $('#<?= $_REQUEST['name_w'] ?>_ticks div.tick').each(function(i){
                $(this).width(tickPxDim);
                $(this).height(tickPxDim);
                $(this).find(".tickInner").css("top", "-" + tickInnerTopPx + "px");
                newTickValueLeft = Math.ceil(($(this).width() - $(this).find('.tickValue').width())/2);
                $(this).find('.tickValue').css("left", newTickValueLeft + "px");
                $(this).find('.tickValue').css("top", "-" + parseInt(tickPxHeight + 4 + parseInt($(this).find('.tickValue').height())) + "px");
            }); 
            
            //Pulsante -
            var newTick = $('<div class="tick"><div id="<?= $_REQUEST['name_w'] ?>_minusControl" class="incrementControlInner centerWithFlex">-</div></div>');
            $('#<?= $_REQUEST['name_w'] ?>_ticks').append(newTick);
            
            $('#<?= $_REQUEST['name_w'] ?>_minusControl').off();
            
            $('#<?= $_REQUEST['name_w'] ?>_minusControl').hover(function(){
                $(this).css("color", hoverIndicatorColor);
                $(this).css("box-shadow", "0px 0px 2px 2px #737373, 0px 0px 4px 4px " + hoverIndicatorColor + ", inset 0px 0px 1px 1px #FFFFFF");
            }, function(){
                $(this).css("color", "black");
                $(this).css("box-shadow", "0px 0px 2px 2px #737373, inset 0px 0px 1px 1px #FFFFFF");
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_minusControl').click(function(){
                changeValueByControl("minus");
            //    updatedFlag = false;
            });
            
            newTick.width(tickPxDim);
            newTick.height(tickPxDim);
            newTick.css("left", ($('#<?= $_REQUEST['name_w'] ?>_ticks').width() - tickPxDim)/2 + "px");
            newTick.css("top", ($('#<?= $_REQUEST['name_w'] ?>_ticks').height() - tickPxDim)/2 + "px");
            newTick.css("border-radius", '100%');
            newTick.find('.incrementControlInner').css("width", incrementControlDim + "px");
            newTick.find('.incrementControlInner').css("height", incrementControlDim + "px");
            newTick.find('.incrementControlInner').css("border-radius", parseInt(incrementControlDim/2) + "px");
            newTick.find('.incrementControlInner').css("background-color", displayColor);
            newTick.find('.incrementControlInner').css("font-family", "Digital");
            newTick.find('.incrementControlInner').css("font-size", "48px");
            
            incrementControlAngle = startAngle - 30*(152/minDim);
            
            newTick.css("-webkit-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-moz-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-o-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-ms-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("transform", "rotate(" + incrementControlAngle + "deg)");
            
            var tickValueCompAngle = 360 - incrementControlAngle;
            newTick.find('.incrementControlInner').css("-webkit-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-moz-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-o-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-ms-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("transform", "rotate(" + tickValueCompAngle + "deg)");

            newTickValueLeft = Math.ceil((newTick.width() - newTick.find('.incrementControlInner').width())/2);
            newTick.find('.incrementControlInner').css("left", newTickValueLeft + "px");
            newTick.find('.incrementControlInner').css("top", "-" + incrementControlDim + "px");
            
            //Pulsante +
            newTick = $('<div class="tick"><div id="<?= $_REQUEST['name_w'] ?>_plusControl" class="incrementControlInner centerWithFlex">+</div></div>');
            $('#<?= $_REQUEST['name_w'] ?>_ticks').append(newTick);
            
            $('#<?= $_REQUEST['name_w'] ?>_plusControl').off();
            
            $('#<?= $_REQUEST['name_w'] ?>_plusControl').hover(function(){
                $(this).css("color", hoverIndicatorColor);
                $(this).css("box-shadow", "0px 0px 2px 2px #737373, 0px 0px 4px 4px " + hoverIndicatorColor + ", inset 0px 0px 1px 1px #FFFFFF");
            }, function(){
                $(this).css("color", "black");
                $(this).css("box-shadow", "0px 0px 2px 2px #737373, inset 0px 0px 1px 1px #FFFFFF");
            });
            
            $('#<?= $_REQUEST['name_w'] ?>_plusControl').click(function(){
                changeValueByControl("plus");
             //   updatedFlag = false;
            });
            
            incrementControlAngle = endAngle + 30*(152/minDim);
            
            newTick.width(tickPxDim);
            newTick.height(tickPxDim);
            newTick.css("left", ($('#<?= $_REQUEST['name_w'] ?>_ticks').width() - tickPxDim)/2 + "px");
            newTick.css("top", ($('#<?= $_REQUEST['name_w'] ?>_ticks').height() - tickPxDim)/2 + "px");
            newTick.css("border-radius", '100%');
            newTick.find('.incrementControlInner').css("width", incrementControlDim + "px");
            newTick.find('.incrementControlInner').css("height", incrementControlDim + "px");
            newTick.find('.incrementControlInner').css("border-radius", parseInt(incrementControlDim/2) + "px");
            newTick.find('.incrementControlInner').css("background-color", displayColor);
            newTick.find('.incrementControlInner').css("font-family", "Digital");
            newTick.find('.incrementControlInner').css("font-size", "48px");
            newTick.css("-webkit-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-moz-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-o-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("-ms-transform", "rotate(" + incrementControlAngle + "deg)");
            newTick.css("transform", "rotate(" + incrementControlAngle + "deg)");
            
            tickValueCompAngle = 360 - incrementControlAngle;
            newTick.find('.incrementControlInner').css("-webkit-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-moz-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-o-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("-ms-transform", "rotate(" + tickValueCompAngle + "deg)");
            newTick.find('.incrementControlInner').css("transform", "rotate(" + tickValueCompAngle + "deg)");

            newTickValueLeft = Math.ceil((newTick.width() - newTick.find('.incrementControlInner').width())/2);
            newTick.find('.incrementControlInner').css("left", newTickValueLeft + "px");
            newTick.find('.incrementControlInner').css("top", "-" + incrementControlDim + "px");
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
        
        $.ajax({
            url: getParametersWidgetUrl,
            type: "GET",
            data: {"nomeWidget": [widgetName]},
            async: true,
            dataType: 'json',
            success: function (data) 
            {
                widgetProperties = data;
                
                console.log(widgetProperties);
                
                if((widgetProperties !== null) && (widgetProperties !== ''))
                {
                    dashboardId = widgetProperties.param.id_dashboard;
                    styleParameters = getStyleParameters();
                    widgetParameters = JSON.parse(widgetProperties.param.parameters);
                    sizeRowsWidget = parseInt(widgetProperties.param.size_rows);
                    widgetWidthCells = parseInt(widgetProperties.param.size_columns);
                    widgetHeightCells = parseInt(widgetProperties.param.size_rows);
                    startAngle = parseFloat(styleParameters.startAngle);
                    endAngle = parseFloat(styleParameters.endAngle);
                    indicatorRadius = parseInt(styleParameters.indicatorRadius);
                    displayRadius = parseInt(styleParameters.displayRadius);
                    displayColor = styleParameters.displayColor;
                    ticksColor = styleParameters.ticksColor;
                    domainType = widgetParameters.domainType;
                    labelsFontSize = styleParameters.labelsFontSize; 
                    labelsFontColor = styleParameters.labelsFontColor;
                    labelsFontFamily= widgetProperties.param.fontFamily; 
                    actuatorTarget = widgetProperties.param.actuatorTarget;
					code = widgetProperties.param.code;
                    
                    if(actuatorTarget === 'broker')
                    {
                        entityJson = widgetProperties.param.entityJson;
                        attributeName = widgetProperties.param.attributeName;
                        if (entityJson && attributeName)
                            dataType = JSON.parse(entityJson)[attributeName].type;
                        minValue = parseFloat(widgetParameters.minValue);
                        maxValue = parseFloat(widgetParameters.maxValue);
                    }
                    else
                    {
                        nrInputId = widgetProperties.param.nrInputId;
                        nodeRedInputName = widgetProperties.param.name;
                        dataType = widgetProperties.param.valueType;
                        username = widgetProperties.param.creator;
                        endPointHost = widgetProperties.param.endPointHost;
                        endPointPort = widgetProperties.param.endPointPort;
                        if (widgetParameters.minValue == null) {
                            minValue = parseFloat(widgetProperties.param.minValue);
                            maxValue = parseFloat(widgetProperties.param.maxValue);
                        } else {
                            minValue = parseFloat(widgetParameters.minValue);
                            maxValue = parseFloat(widgetParameters.maxValue);
                        }
                        if(useWebSocket)
                            openWs(widgetName);
                    }
                    
                    valueRange = maxValue - minValue;
                    angleRange = endAngle + 360 - startAngle;
                    convFactor = parseFloat(valueRange / angleRange);
                    continuousRanges = widgetParameters.continuousRanges;
                    
                    dataPrecision = widgetParameters.dataPrecision;
                    incrementControlDeltaValue = parseInt(styleParameters.increaseValue);
                    
                    switch(dataType)
                    {
                        case "Float": case "float":
                            currentValue = parseFloat(widgetProperties.param.currentValue).toFixed(dataPrecision);
                            break;
                            
                        case "Integer": case "integer":
                            currentValue = parseInt(widgetProperties.param.currentValue);
                            break;
                    }
                    
                    oldValue = currentValue;
                    
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').show();
					///////////////////////////////
					if (widgetProperties.param.code != null && widgetProperties.param.code != "null") {
                        code = widgetProperties.param.code;
                        var text_ck_area = document.createElement("text_ck_area");
                        text_ck_area.innerHTML = code;
                        var newInfoDecoded = text_ck_area.innerText;
						var displayVal  = $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text();
						var displayVal ="";
                        newInfoDecoded = newInfoDecoded.replaceAll("function execute()","function execute_" + "<?= $_REQUEST['name_w'] ?>(param)");
						
                        var elem = document.createElement('script');
                        elem.type = 'text/javascript';
                        elem.innerHTML = newInfoDecoded;
                        $('#<?= $_REQUEST['name_w'] ?>_code').append(elem);
                        $('#<?= $_REQUEST['name_w'] ?>_code').css("display", "none");
                    }
                    ////////////////////////////
                    populateWidget(); 
                    
                    $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').off("hover");
                    $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').hover(function(){
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-moz-box-shadow", "0px 0px 2px 2px " + hoverIndicatorColor);
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-webkit-box-shadow", "0px 0px 2px 2px " + hoverIndicatorColor);
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("box-shadow", "0px 0px 2px 2px " + hoverIndicatorColor); 
                    }, function(){
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-moz-box-shadow", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-webkit-box-shadow", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("box-shadow", "none"); 
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
            }
        });
		
        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event) 
        {
            showHeader = event.showHeader;
            resizeWidget();
        });
        
        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });

        //Web socket 
        
        var openWs = function(widget)
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
                initWebsocket(widget, wsUrl, null, wsRetryTime*1000, function(socket){
                    console.log('socket initialized!');
                    //do something with socket...
                    //Window.webSockets["<?= $_REQUEST['name_w'] ?>"] = socket;
                    openWsConn(widget);
                }, function(){
                    console.log('init of socket failed!');
                });                                          
                /*webSocket.addEventListener('open', openWsConn);
                webSocket.addEventListener('close', wsClosed);*/
            }
            catch(e)
            {
                wsClosed();
            }
        };
        
        var manageIncomingWsMsg = function(msg)
        {
            var msgObj = JSON.parse(msg.data);
            console.log(msgObj);
            if (msgObj.msgType == "DataToEmitter") {
                if (currentValue != msgObj.newValue) {
                    updatedEverFlag = true;
                    updatedFlag = true;
                    lastValueOk = msgObj.newValue;
                  //  showUpdateResult("Device OK");
                }
            }
            if(msgObj.msgType=="DataToEmitterAck") {
                if (lastValueOk !== null) {
                    currentValue = lastValueOk;
                    lastValueOk = null;
                    showUpdateResult("Device OK");
                 //   currentValue = currentNormAngle*convFactor + minValue;
                    currentNormAngle = (currentValue - minValue)/convFactor;
                    var newAngle = currentNormAngle;
                //    var newAngle = null;
                    if ((currentNormAngle >= 0) && (currentNormAngle <= 360 - startAngle)) {
                        newAngle = currentNormAngle + startAngle;
                    } else if ((currentNormAngle >= 360 - startAngle) && (currentNormAngle <= 360 + endAngle - startAngle)) {
                        newAngle = currentNormAngle + startAngle - 360;
                    }

                    switch(dataType)
                    {
                        case "Float": case "float":
                        currentValue = parseFloat(currentValue).toFixed(dataPrecision);
                        break;

                        case "Integer": case "integer":
                        currentValue = parseInt(currentValue);
                        break;
                    }

                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('-webkit-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('-moz-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('-o-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('-ms-transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');
                    $('#<?= $_REQUEST['name_w'] ?>_knob').css('transform', 'rotate3d(0, 0, 1, ' + newAngle + 'deg)');

                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle span').html(currentValue);

                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle').textfill({
                        maxFontPixels: -20
                    });

                    if(fontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_innerCircle span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_innerCircle span").css('font-size', fontSize + 'px');
                    }


                    /*activeTicks = (Math.round(currentNormAngle / tickDeltaAngle) + 1);
                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                        $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').slice(0, activeTicks).addClass('activetick');*/

                    if(domainType === 'continuous')
                    {
                        if(continuousRanges !== null)
                        {
                            for(var i in continuousRanges)
                            {
                                if((currentValue >= parseFloat(continuousRanges[i].min))&&(currentValue <= parseFloat(continuousRanges[i].max)))
                                {
                                    /*$('#<?= $_REQUEST['name_w'] ?>_halo').css("background", continuousRanges[i].color);
                                        $('#<?= $_REQUEST['name_w'] ?>_halo').css("-moz-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));
                                        $('#<?= $_REQUEST['name_w'] ?>_halo').css("-webkit-box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));
                                        $('#<?= $_REQUEST['name_w'] ?>_halo').css("box-shadow", "3px 5px 8px rgba(255, 255, 255, .5) inset, 0 0 10px " + continuousRanges[i].color + ", 0 0 25px " + continuousRanges[i].color.replace(",1)", ", 0.6"));*/

                                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-moz-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("-webkit-box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                    $('#<?= $_REQUEST['name_w'] ?>_innerCircle').css("box-shadow", "2px 2px 4px " + continuousRanges[i].color + ", 2px -2px 4px " + continuousRanges[i].color + ", -2px 2px 4px " + continuousRanges[i].color + ", -2px -2px 4px " + continuousRanges[i].color);
                                    hoverRotationColor = continuousRanges[i].color;
                                }

                                /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tick .tickInner').css("background-color", ticksColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-webkit-box-shadow", "none");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-moz-box-shadow", "none");
                                    $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("box-shadow", "none");*/

                                /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').each(function(i){
                                        if(currentValue >= parseFloat($(this).attr('data-value')))
                                        {
                                            var thisTickActiveColor = $(this).attr('data-activeColor');
                                            $(this).addClass('activetick');
                                            $(this).find('.tickInner').css("background-color", thisTickActiveColor);
                                            $(this).find('.tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                            $(this).find('.tickInner').css("-moz-box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                            $(this).find('.tickInner').css("box-shadow", "0px 0px 0px 1px " + thisTickActiveColor.replace(",1)", ", 0.5"));
                                        }
                                    });*/
                            }
                        }
                        else
                        {
                            /*$('#<?= $_REQUEST['name_w'] ?>_ticks .tick').removeClass('activetick');
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tick .tickInner').css("background-color", ticksColor);
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-webkit-box-shadow", "none");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("-moz-box-shadow", "none");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tickInner').css("box-shadow", "none");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .tick').slice(0, activeTicks).addClass('activetick');
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("background-color", "rgba(51, 204, 255, 1)");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-webkit-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("-moz-box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");
                                $('#<?= $_REQUEST['name_w'] ?>_ticks .activetick .tickInner').css("box-shadow", "0px 0px 0px 1px rgba(51, 204, 255, 0.5)");*/
                            hoverRotationColor = "rgba(51, 204, 255, 1)";
                        }

                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').off("hover");
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-moz-box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("-webkit-box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
                        $('#<?= $_REQUEST['name_w'] ?>_knobIndicator').css("box-shadow", "0px 0px 2px 2px " + hoverRotationColor);
                    }

                    stopFlag = 1;
                } else {
                    var webSocket = Window.webSockets[msgObj.widgetUniqueName];
                    if (!webSocket.ackReceived) {
                        clearTimeout(webSocket.timeout);
                        webSocket.ackReceived = true;
                        console.log(msgObj.widgetUniqueName + " ACK ackReceived:" + webSocket.ackReceived)
                        webSocket.onAck({result: "Ok", widgetName: msgObj.widgetUniqueName});
                    }
                }
            }
			//TEST INSERT
			if (widgetProperties.param.code != null && widgetProperties.param.code != '') {
                //execute();
				///////////
				console.log("execute_" + "<?= $_REQUEST['name_w'] ?>");
				//var displayVal  = $('#<?= $_REQUEST['name_w'] ?>_lastContainer span.displayVal').text();
				var displayVal  = 50;
                var functionName = "execute_" + "<?= $_REQUEST['name_w'] ?>";
				console.log(functionName);
                window[functionName](displayVal);
            }
			
			//
        };
        
        timeToReload=200;
        var openWsConn = function(widget) {
            var webSocket = Window.webSockets[widget];
            var wsRegistration = {
                msgType: "ClientWidgetRegistration",
                userType: "widgetInstance",
             //   metricName: encodeURIComponent(metricName),
                widgetUniqueName: "<?= $_REQUEST['name_w'] ?>"
            };
            webSocket.send(JSON.stringify(wsRegistration));
            /*setTimeout(function(){
                var webSocket = Window.webSockets[widget];
                webSocket.removeEventListener('message', manageIncomingWsMsg);
                webSocket.close();
            }, (timeToReload - 2)*1000);*/
              
            webSocket.addEventListener('message', manageIncomingWsMsg);
        };
        
        var wsClosed = function(e)
        {
            var webSocket = Window.webSockets["<?= $_REQUEST['name_w'] ?>"];
            webSocket.removeEventListener('message', manageIncomingWsMsg);
            if(wsRetryActive === 'yes')
            {
                setTimeout(openWs, parseInt(wsRetryTime*1000));
            }	
        };

        function initWebsocket(widget, url, existingWebsocket, retryTimeMs, success, failed) {
          if (!existingWebsocket || existingWebsocket.readyState != existingWebsocket.OPEN) {
              if (existingWebsocket) {
                  existingWebsocket.close();
              }
              var websocket = new WebSocket(url);
              websocket.widget = widget;
              console.log("store websocket for "+widget)
              Window.webSockets[widget] = websocket;
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
            <svg id="<?= $_REQUEST['name_w'] ?>_gradCanvas" class="gradCanvas">
                <defs>
                    <filter id="basicBlur" x="0" y="0">
                        <feGaussianBlur in="SourceGraphic" stdDeviation="0.5" />
                    </filter>
                
                
                    <filter id="<?= $_REQUEST['name_w'] ?>_sofGlow" height="300%" width="300%" x="-75%" y="-75%">
                        <!-- Inspessimento elemento originale -->
                        <feMorphology operator="dilate" radius="0.95" in="SourceAlpha" result="thicken" />

                        <!-- Filtro gaussiano per effetto blur -->
                        <feGaussianBlur in="thicken" stdDeviation="4" result="blurred" />

                        <!-- Cambio di colore: SOSTITUIRCI LA VERSIONE CON MENO OPACITA' DEL COLORE IN USO -->
                        <feFlood id="<?= $_REQUEST['name_w'] ?>_floodColor" flood-color="rgb(0,186,255)" result="glowColor" />

                        <!-- Colore in effetto glow -->
                        <feComposite in="glowColor" in2="blurred" operator="in" result="softGlow_colored" />

                        <!-- impacchettamento effetti -->
                        <feMerge>
                            <feMergeNode in="softGlow_colored"/>
                            <feMergeNode in="SourceGraphic"/>
                        </feMerge>
                    </filter>
                </defs>
            </svg>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" ondragstart="return false;" ondrop="return false;" class="chartContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_knob" class="knob">
                    <div id="<?= $_REQUEST['name_w'] ?>_knobIndicator" class="knobIndicator"></div>
                </div>
                <!--<div id='<?= $_REQUEST['name_w'] ?>_halo' class="knobHalo"></div>-->
                <div id='<?= $_REQUEST['name_w'] ?>_innerCircle' class="knobInnerCircle centerWithFlex"><span unselectable="on"></span></div>
                <div id='<?= $_REQUEST['name_w'] ?>_ticks' class="ticks">
                </div>
            </div>
        </div>
    </div>
		<div id="<?= $_REQUEST['name_w'] ?>_code"></div>
</div> 
