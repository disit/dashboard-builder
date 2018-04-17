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

<link rel="stylesheet" href="../css/widgetImpulseButton.css">

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
            displayOnNeonEffect, impulseMode, targetEntity, targetEntityAttribute, baseValue, sequenceEntityUpdateInterval,
            actuatorTarget, username, endPointHost, endPointPort, nodeRedInputName = null;
        
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
            
            if(showHeader)
            {
                if((2*widgetWidthCells) === widgetHeightCells)
                {
                    onOffButtonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height() - 15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').height();
                    onOffButtonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').width();
                }
                else
                {
                    onOffButtonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').height();
                    onOffButtonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').width();
                }
            }
            else
            {
                onOffButtonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').height();
                onOffButtonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').width();
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("width", onOffButtonPercentWidth + "%");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("height", onOffButtonPercentHeight + "%");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("left", parseFloat(100 - onOffButtonPercentWidth)/2 + "%");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("top", parseFloat(100 - onOffButtonPercentHeight)/2 + "%");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("font-size", minDim*0.3 + "px");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("border-radius", minDim*onOffButtonRadius/200);
            
            switch(viewMode)
            {
                case "emptyButton":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'none');
                    break;
                    
                case "iconOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').addClass('centerWithFlex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'none');
                    break;
                    
                case "textOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("width", "100%");
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("height", "100%");
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-size", textFontSize + "px");
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer span').html(widgetTitle);
                    if(fontFamily !== 'Auto')
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-family", fontFamily);
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                        maxFontPixels: -20
                    });

                    if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                    }
                    break;
                    
                case "displayOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('width', displayWidth + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('height', displayHeight + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-left', parseInt((100 - displayWidth)/2) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height() - $('#<?= $_REQUEST['name_w'] ?>_display').height())/2) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('border-radius', parseInt(minDim*displayRadius/100) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('background-color', displayColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                    $('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size', displayFontSize + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                        maxFontPixels: -20
                    });

                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                    }
                    break;    
                    
                case "iconAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('height', '60%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-left-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-right-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('float', 'left');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').addClass('centerWithFlex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('height', '40%');
                    if(fontFamily !== 'Auto')
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-family", fontFamily);
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer span').html(widgetTitle);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                        maxFontPixels: -20
                    });

                    if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                    }
                    break;    
                    
                case "iconAndDisplay":
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('height', parseInt(100 - displayHeight - 10) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-left-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-right-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('float', 'left');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').addClass('centerWithFlex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('width', displayWidth + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('height', displayHeight + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-left', parseInt((100 - displayWidth)/2) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', '2%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('border-radius', parseInt(minDim*displayRadius/100) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('background-color', displayColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                        maxFontPixels: -20
                    });

                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                    }
                    break;
                    
                case "displayAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer span').html(widgetTitle);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                        maxFontPixels: -20
                    });

                    if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                    }
                    
                    if(fontFamily !== 'Auto')
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-family", fontFamily);
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('width', displayWidth + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('height', displayHeight + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-left', parseInt((100 - displayWidth)/2) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', '15%');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('height', parseInt(100 - displayHeight - 15) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('border-radius', parseInt(minDim*displayRadius/100) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                        maxFontPixels: -20
                    });

                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('background-color', displayColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').insertBefore('#<?= $_REQUEST['name_w'] ?>_txtContainer');
                    break;
                    
                case "all":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('margin-top', '6%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-left-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-right-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('float', 'left');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').addClass('centerWithFlex');
                    
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer span').html(widgetTitle);
                    if(fontFamily !== 'Auto')
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-family", fontFamily);
                    }
                    
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('width', displayWidth + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('height', displayHeight + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-left', parseInt((100 - displayWidth)/2) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', '9%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-bottom', '3%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('border-radius', parseInt(minDim*displayRadius/100) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                        maxFontPixels: -20
                    });

                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('background-color', displayColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').insertBefore('#<?= $_REQUEST['name_w'] ?>_txtContainer');
                    
                    var txtContainerHeight = $('#<?= $_REQUEST['name_w'] ?>_onOffButton').height() - ($('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').height() + $('#<?= $_REQUEST['name_w'] ?>_display').height() + 0.18*($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()));
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('height', txtContainerHeight + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                        maxFontPixels: -20
                    });

                    if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                    }
                    break;        
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", buttonColor);
                
            switch(viewMode)
            {
                case "emptyButton":
                    break;

                case "iconOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                    break;

                case "textOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOffNeonEffect);
                    break;

                case "displayOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    break;    

                case "iconAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;    

                case "iconAndDisplay":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    break;

                case "displayAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;    

                case "all":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;        
            }
            
			
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('mousedown');
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('mouseup');
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').mousedown(handleMouseDown);
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').mouseup(handleMouseUp);
        }
        
        function handleMouseDown()
        {
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').addClass('onOffButtonActive');
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", buttonClickColor);
            
            switch(viewMode)
            {
                case "emptyButton":

                    break;

                case "iconOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                    break;

                case "textOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOnNeonEffect);
                    break;

                case "displayOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                    break;    

                case "iconAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;    

                case "iconAndDisplay":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                    break;

                case "displayAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;    

                case "all":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;        
            }
        }
        
        function handleMouseUp()
        {
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').removeClass('onOffButtonActive');
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", buttonColor);
            
            switch(viewMode)
            {
                case "emptyButton":

                    break;

                case "iconOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                    break;

                case "textOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOffNeonEffect);
                    break;

                case "displayOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    break;    

                case "iconAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;    

                case "iconAndDisplay":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    break;

                case "displayAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;    

                case "all":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;        
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");
            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("display", "none");
            $('#<?= $_REQUEST['name_w'] ?>_display').css("display", "none");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "block");
            var loadingIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').width())/2);
            var loadingIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').height())/2);
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("top", loadingIconMarginTop + "px");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("left", loadingIconMarginLeft + "px");

            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('mousedown', handleMouseDown);
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('mouseup', handleMouseUp);
                    
            var valueToSend = null;
            
            function sendCurrentPositionError(obj)
            {
                console.log("Get current position KO: " + obj.message);
            }
            
            if(navigator.geolocation) 
            {
                navigator.geolocation.getCurrentPosition(sendCurrentPosition, sendCurrentPositionError);
            }
            else
            {
                valueToSend = 'Geolocator not available';
            }
            
            function sendCurrentPosition(position)
            {
                console.log("sendCurrentPosition OK");

                $.ajax({
                    url: "../widgets/actuatorUpdateValuePersonalApps.php",
                    type: "POST",
                    data: {
                        "inputName": nodeRedInputName,
                        "dashboardId": dashboardId,
                        "widgetName": "<?= $_REQUEST['name_w'] ?>",
                        "username" : "publicDashboard",
                        "value": JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy:  position.coords.accuracy,
                            altitude:  position.coords.altitude,
                            altitudeAccuracy:  position.coords.altitudeAccuracy,
                            heading:  position.coords.heading,
                            speed:  position.coords.speed
                        }),
                        "endPointPort": "<?= $_REQUEST['endPointPort'] ?>",
                        "httpRoot": "<?= $_REQUEST['httpRoot'] ?>"
                    },
                    async: true,
                    dataType: 'json',
                    success: function(data) 
                    {
                        switch(data.result)
                        {
                            case "Ok":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').mousedown(handleMouseDown);
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').mouseup(handleMouseUp);
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");

                                switch(viewMode)
                                {
                                    case "emptyButton":
                                        break;

                                    case "iconOnly":
                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                        break;

                                    case "textOnly":
                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                        break;

                                    case "displayOnly":
                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                        break;    

                                    case "iconAndText":
                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');    
                                        break;    

                                    case "iconAndDisplay":
                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                            maxFontPixels: -20
                                        });

                                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                        {
                                            $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                        }
                                        break;

                                    case "displayAndText":
                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                        break;    

                                    case "all":
                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                        break;        
                                }
                                break;    

                            default:
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                                var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                                var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

                                setTimeout(function(){
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                                    currentValue = baseValue;

                                    switch(viewMode)
                                    {
                                        case "emptyButton":

                                            break;

                                        case "iconOnly":
                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                            break;

                                        case "textOnly":
                                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                            break;

                                        case "displayOnly":
                                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                            break;    

                                        case "iconAndText":
                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                            break;    

                                        case "iconAndDisplay":
                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                            $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                                maxFontPixels: -20
                                            });

                                            if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                            {
                                                $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                            }
                                            break;

                                        case "displayAndText":
                                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                            break;    

                                        case "all":
                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                            break;        
                                    }

                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').mousedown(handleMouseDown);
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').mouseup(handleMouseUp);
                                }, 1500);
                                break;
                        }
                    },
                    error: function(data)
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                        var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                        var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

                        setTimeout(function(){
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                            currentValue = baseValue;

                            switch(viewMode)
                            {
                                case "emptyButton":

                                    break;

                                case "iconOnly":
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                    break;

                                case "textOnly":
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                    break;

                                case "displayOnly":
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                    break;    

                                case "iconAndText":
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                    break;    

                                case "iconAndDisplay":
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                        maxFontPixels: -20
                                    });

                                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                    {
                                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                    }
                                    break;

                                case "displayAndText":
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                    break;    

                                case "all":
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                    break;        
                            }

                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').mousedown(handleMouseDown);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').mouseup(handleMouseUp);
                        }, 1500);
                        console.log("Impulse ko:");
                        console.log(data);
                    }
                }); 
            
            }
        }
        
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);

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
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("border-radius", minDim*onOffButtonRadius/200);
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("font-size", minDim*0.3 + "px");

            switch(viewMode)
            {
                case "emptyButton":
                      break;

                case "iconOnly":

                        break;

                case "textOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                                maxFontPixels: -20
                        });

                        if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                        {
                                $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                        }
                        break;

                case "displayOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height() - $('#<?= $_REQUEST['name_w'] ?>_display').height())/2) + 'px');
                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                maxFontPixels: -20
                        });

                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                        {
                                $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                        }
                        break;    

                case "iconAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                                maxFontPixels: -20
                        });

                        if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                        {
                                $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                        }
                        break;    

                case "iconAndDisplay":
                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                maxFontPixels: -20
                        });

                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                        {
                                $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                        }
                        break;

                case "displayAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                maxFontPixels: -20
                        });

                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                        {
                                $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                        }

                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                                maxFontPixels: -20
                        });

                        if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                        {
                                $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                        }
                        break;

                case "all":
                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                maxFontPixels: -20
                        });

                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                        {
                                $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                        }

                        var txtContainerHeight = $('#<?= $_REQUEST['name_w'] ?>_onOffButton').height() - ($('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').height() + $('#<?= $_REQUEST['name_w'] ?>_display').height() + 0.18*($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()));
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('height', txtContainerHeight + 'px');

                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                                maxFontPixels: -20
                        });

                        if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                        {
                                $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                        }
                        break;        
                    }
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
                    onOffButtonRadius = parseInt(styleParameters.buttonRadius);
                    fontFamily = widgetProperties.param.fontFamily;
                    buttonColor = styleParameters.color;
                    buttonClickColor = styleParameters.clickColor;
                    actuatorTarget = widgetProperties.param.actuatorTarget;
                    if(actuatorTarget === 'broker')
                    {
                        entityJson = widgetProperties.param.entityJson;
                        attributeName = widgetProperties.param.attributeName;
                        dataType = JSON.parse(entityJson)[attributeName].type;
                        targetEntity = widgetParameters.targetEntity;
                        targetEntityAttribute = widgetParameters.targetEntityAttribute;
                        impulseValue = widgetParameters.impulseValue;
                        baseValue = widgetParameters.baseValue;
                    }
                    else
                    {
                        nodeRedInputName = widgetProperties.param.name;
                        dataType = widgetProperties.param.valueType;
                        baseValue = widgetProperties.param.offValue;
                        impulseValue = widgetProperties.param.onValue;
                        username = widgetProperties.param.creator;
                        endPointHost = widgetProperties.param.endPointHost;
                        endPointPort = widgetProperties.param.endPointPort;
                    }
                    
                    currentValue = "Off";
                    
                    impulseMode = widgetParameters.impulseMode;
                    viewMode = styleParameters.viewMode;
                    symbolColor = styleParameters.symbolColor;
                    symbolClickColor = styleParameters.symbolClickColor;
                    textClickColor = styleParameters.textClickColor;
                    textColor = styleParameters.textColor;
                    textFontSize = styleParameters.textFontSize;
                    displayFontSize = styleParameters.displayFontSize;
                    displayFontColor = styleParameters.displayFontColor; 
                    displayFontClickColor = styleParameters.displayFontClickColor;
                    displayRadius = styleParameters.displayRadius; 
                    displayColor = styleParameters.displayColor;
                    displayWidth = styleParameters.displayWidth;
                    displayHeight = styleParameters.displayHeight;
                    symbolOnNeonEffectSetting = styleParameters.neonEffect;
                    
                    switch(symbolOnNeonEffectSetting)
                    {
                        case "never":
                            symbolOnNeonEffect = "none";
                            symbolOffNeonEffect = "none";
                            textOnNeonEffect = "none";
                            textOffNeonEffect = "none";
                            displayOffNeonEffect = "none";
                            displayOnNeonEffect = "none";
                            break;
                            
                        case "onStatus":
                            symbolOnNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolColor + ", 0 0 8px " + symbolColor + ", 0 0 14px " + symbolColor + ", 0 0 18px ";
                            symbolOffNeonEffect = "none";
                            textOnNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textClickColor + ", 0 0 14px " + textClickColor + ", 0 0 20px " + textClickColor + ", 0 0 24px " + textClickColor + ", 0 0 28px " + textClickColor;
                            textOffNeonEffect = "none"; 
                            displayOnNeonEffect = displayFontClickColor + " 2px 2px 16px, " + displayFontClickColor + " 2px -2px 16px, " + displayFontClickColor + " -2px 2px 16px, " + displayFontClickColor + " -2px -2px 16px";
                            displayOffNeonEffect = "none";
                            break;    
                            
                        case "offStatus":
                            symbolOnNeonEffect = "none";
                            symbolOffNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolClickColor + ", 0 0 8px " + symbolClickColor + ", 0 0 14px " + symbolClickColor + ", 0 0 18px ";
                            textOnNeonEffect = "none";
                            textOffNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textColor + ", 0 0 14px " + textColor + ", 0 0 20px " + textColor + ", 0 0 24px " + textColor + ", 0 0 28px " + textColor; 
                            displayOnNeonEffect = "none";
                            displayOffNeonEffect = displayFontColor + " 2px 2px 16px, " + displayFontColor + " 2px -2px 16px, " + displayFontColor + " -2px 2px 16px, " + displayFontColor + " -2px -2px 16px";
                            break;
                            
                        case "always":
                            symbolOnNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolColor + ", 0 0 8px " + symbolColor + ", 0 0 14px " + symbolColor + ", 0 0 18px ";
                            symbolOffNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolClickColor + ", 0 0 8px " + symbolClickColor + ", 0 0 14px " + symbolClickColor + ", 0 0 18px ";
                            textOnNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textClickColor + ", 0 0 14px " + textClickColor + ", 0 0 20px " + textClickColor + ", 0 0 24px " + textClickColor + ", 0 0 28px " + textClickColor;
                            textOffNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textColor + ", 0 0 14px " + textColor + ", 0 0 20px " + textColor + ", 0 0 24px " + textColor + ", 0 0 28px " + textColor; 
                            displayOnNeonEffect = displayFontClickColor + " 2px 2px 16px, " + displayFontClickColor + " 2px -2px 16px, " + displayFontClickColor + " -2px 2px 16px, " + displayFontClickColor + " -2px -2px 16px";
                            displayOffNeonEffect = displayFontColor + " 2px 2px 16px, " + displayFontColor + " 2px -2px 16px, " + displayFontColor + " -2px 2px 16px, " + displayFontColor + " -2px -2px 16px";
                            break;    
                    }
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
        
        $(document).off('resizeHighchart_' + widgetName);
		$(document).on('resizeHighchart_' + widgetName, function(event) 
		{
			populateWidget();
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
            <div id="<?= $_REQUEST['name_w'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" ondragstart="return false;" ondrop="return false;" class="chartContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_onOffButton" class="onOffButton">
                    <div id="<?= $_REQUEST['name_w'] ?>_onOffButtonBefore" class="onOffButtonBefore"></div>
                    <i class="fa fa-power-off"></i>
                    <i class="fa fa-refresh fa-spin"></i>
                    <i class="fa fa-times-circle-o"></i>
                    <div id="<?= $_REQUEST['name_w'] ?>_txtContainer" class="onOffButtonTxtContainer centerWithFlex">
                        <span></span>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_display" class="onOffButtonDisplay centerWithFlex">
                        <span></span>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_onOffButtonAfter" class="onOffButtonAfter"></div>
                </div>
            </div>
        </div>
    </div>	
</div> 