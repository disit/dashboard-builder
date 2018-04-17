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

<link rel="stylesheet" href="../css/widgetOnOffButton.css">

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
            dataType, displayColor, currentValue, fontFamily, 
            onOffButtonPercentWidth, onOffButtonPercentHeight, onOffButtonRadius, onButtonColor, textOnNeonEffect, textOffNeonEffect,
            offButtonColor, offValue, onValue, updateRequestStartTime, symbolOnColor, symbolOffColor, textOnColor, textOffColor,
            symbolOnNeonEffect, symbolOffNeonEffect, symbolOnNeonEffectSetting, viewMode, textFontSize, displayFontSize,
            displayOffColor, displayOnColor, displayRadius, displayColor, displayWidth, displayHeight, displayOffNeonEffect, 
            displayOnNeonEffect, actuatorTarget, username, endPointHost, endPointPort, nodeRedInputName = null;
        
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
            
            if(currentValue === onValue)
            {
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", onButtonColor);
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').addClass('onOffButtonActive');
                
                switch(viewMode)
                {
                    case "emptyButton":

                        break;

                    case "iconOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                        break;

                    case "textOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOnNeonEffect);
                        break;

                    case "displayOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                        break;    

                    case "iconAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                        break;    

                    case "iconAndDisplay":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                        break;
                        
                    case "displayAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                        break;

                    case "all":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                        break;        
                }
            }
            else
            {
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", offButtonColor);
                
                switch(viewMode)
                {
                    case "emptyButton":

                        break;

                    case "iconOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                        break;

                    case "textOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOffNeonEffect);
                        break;

                    case "displayOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                        break;    

                    case "iconAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                        break;    

                    case "iconAndDisplay":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                        break;
                        
                    case "displayAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                        break;    

                    case "all":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                        break;        
                }
            }
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('click');
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').click(handleMouseDown);
        }
        
        function handleMouseDown()
        {
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').toggleClass('onOffButtonActive');
            
            if(currentValue === onValue)
            {
                currentValue = offValue;
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", offButtonColor);
                switch(viewMode)
                {
                    case "emptyButton":

                        break;

                    case "iconOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                        break;

                    case "textOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOffNeonEffect);
                        break;

                    case "displayOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                        break;    

                    case "iconAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                        break;    

                    case "iconAndDisplay":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                        break;
                        
                    case "displayAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                        break;    

                    case "all":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                        break;        
                }
            }
            else
            {
                if(currentValue === offValue)
                {
                    currentValue = onValue;
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", onButtonColor);
                    switch(viewMode)
                    {
                        case "emptyButton":

                            break;

                        case "iconOnly":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                            break;

                        case "textOnly":
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOnNeonEffect);
                            break;

                        case "displayOnly":
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                            break;    

                        case "iconAndText":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                            break;    

                        case "iconAndDisplay":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                            break;
                            
                        case "displayAndText":
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                            break;    

                        case "all":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                            break;        
                    }
                }
            }
            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
            updateRemoteValue();
        }
        
        function updateRemoteValue()
        {
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('click', handleMouseDown);
            
            var requestComplete = false;
            updateRequestStartTime = new Date();
            setUpdatingMsgInterval = setInterval(function(){
                if((requestComplete === false)&&(Math.abs(new Date() - updateRequestStartTime) > 1100))
                {
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "block");
                    var loadingIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').width())/2);
                    var loadingIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').height())/2);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("top", loadingIconMarginTop + "px");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("left", loadingIconMarginLeft + "px");
                }
            }, 250);
            
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
                            clearInterval(setUpdatingMsgInterval);
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
                            requestComplete = true;
                            clearInterval(setUpdatingMsgInterval);
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
            //msg = "Pippo";
            if(msg !== "Device OK")
            {
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");
                
                setTimeout(function(){
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').toggleClass('onOffButtonActive');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");
                    
                    if(currentValue === onValue)
                    {
                        currentValue = offValue;
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", offButtonColor);
                        
                        switch(viewMode)
                        {
                            case "emptyButton":

                                break;

                            case "iconOnly":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                                break;

                            case "textOnly":
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOffNeonEffect);
                                break;

                            case "displayOnly":
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display').css("color", displayOffColor);
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                                break;    

                            case "iconAndText":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                                break;    

                            case "iconAndDisplay":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display').css("color", displayOffColor);
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                    maxFontPixels: -20
                                });

                                if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                }
                                $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                                break;
                                
                            case "displayAndText":
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display').css("color", displayOffColor);
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                                break;    

                            case "all":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOnColor);
                                $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                                break;        
                        }
                    }
                    else
                    {
                        if(currentValue === offValue)
                        {
                            currentValue = onValue;
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", onButtonColor);
                            switch(viewMode)
                            {
                                case "emptyButton":

                                    break;

                                case "iconOnly":
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                                    break;

                                case "textOnly":
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOnNeonEffect);
                                    break;

                                case "displayOnly":
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css("color", displayOnColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                                    break;    

                                case "iconAndText":
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");   
                                    break;    

                                case "iconAndDisplay":
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOnColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css("color", displayOnColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                        maxFontPixels: -20
                                    });

                                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                    {
                                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                    }
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                                    break;
                                    
                                case "displayAndText":
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css("color", displayOnColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOnColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none"); 
                                    break;    

                                case "all":
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolOffColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayOffColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textOffColor);
                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                                    break;        
                            }
                        }
                    }
                    
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').click(handleMouseDown);
                }, 1500);
            }
            else
            {
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').click(handleMouseDown);
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
                    offButtonColor = styleParameters.offColor;
                    onButtonColor = styleParameters.onColor;
                    fontFamily = widgetProperties.param.fontFamily;
                    actuatorTarget = widgetProperties.param.actuatorTarget;
                    if(actuatorTarget === 'broker')
                    {
                        entityJson = widgetProperties.param.entityJson;
                        attributeName = widgetProperties.param.attributeName;
                        dataType = JSON.parse(entityJson)[attributeName].type;
                        offValue = widgetParameters.offValue;
                        onValue = widgetParameters.onValue;
                    }
                    else
                    {
                        nodeRedInputName = widgetProperties.param.name;
                        dataType = widgetProperties.param.valueType;
                        offValue = widgetProperties.param.offValue;
                        onValue = widgetProperties.param.onValue;
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

                        case "String":
                            currentValue = widgetProperties.param.currentValue;
                            break;

                        case "Boolean":
                            if((widgetProperties.param.currentValue === true)||(widgetProperties.param.currentValue === 'true')||(widgetProperties.param.currentValue === 'True'))
                            {
                                currentValue = true;
                            }
                            else
                            {
                                currentValue = false;   
                            }
                            break;    
                    }
                    
                    viewMode = styleParameters.viewMode;
                    symbolOnColor = styleParameters.symbolOnColor;
                    symbolOffColor = styleParameters.symbolOffColor;
                    textOnColor = styleParameters.textOnColor;
                    textOffColor = styleParameters.textOffColor;
                    textFontSize = styleParameters.textFontSize;
                    displayFontSize = styleParameters.displayFontSize;
                    displayFontSize = styleParameters.displayFontSize;
                    displayOffColor = styleParameters.displayOffColor; 
                    displayOnColor = styleParameters.displayOnColor;
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
                            //symbolOnNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + symbolOnColor + ", 0 0 14px " + symbolOnColor + ", 0 0 20px " + symbolOnColor + ", 0 0 24px " + symbolOnColor + ", 0 0 28px " + symbolOnColor;
                            symbolOnNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolOnColor + ", 0 0 8px " + symbolOnColor + ", 0 0 14px " + symbolOnColor + ", 0 0 18px ";
                            symbolOffNeonEffect = "none";
                            textOnNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textOnColor + ", 0 0 14px " + textOnColor + ", 0 0 20px " + textOnColor + ", 0 0 24px " + textOnColor + ", 0 0 28px " + textOnColor;
                            textOffNeonEffect = "none"; 
                            displayOnNeonEffect = displayOnColor + " 2px 2px 16px, " + displayOnColor + " 2px -2px 16px, " + displayOnColor + " -2px 2px 16px, " + displayOnColor + " -2px -2px 16px";
                            displayOffNeonEffect = "none";
                            break;    
                            
                        case "offStatus":
                            symbolOnNeonEffect = "none";
                            //symbolOffNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + symbolOffColor + ", 0 0 14px " + symbolOffColor + ", 0 0 20px " + symbolOffColor + ", 0 0 24px " + symbolOffColor + ", 0 0 28px " + symbolOffColor;
                            symbolOffNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolOffColor + ", 0 0 8px " + symbolOffColor + ", 0 0 14px " + symbolOffColor + ", 0 0 18px ";
                            textOnNeonEffect = "none";
                            textOffNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textOffColor + ", 0 0 14px " + textOffColor + ", 0 0 20px " + textOffColor + ", 0 0 24px " + textOffColor + ", 0 0 28px " + textOffColor; 
                            displayOnNeonEffect = "none";
                            displayOffNeonEffect = displayOffColor + " 2px 2px 16px, " + displayOffColor + " 2px -2px 16px, " + displayOffColor + " -2px 2px 16px, " + displayOffColor + " -2px -2px 16px";
                            break;
                            
                        case "always":
                            //symbolOnNeonEffect = "0 0 0px #fff, 0 0 0px #fff, 0 0 0px #fff, 0 0 8px " + symbolOnColor + ", 0 0 14px " + symbolOnColor + ", 0 0 20px " + symbolOnColor + ", 0 0 24px " + symbolOnColor + ", 0 0 28px " + symbolOnColor;
                            symbolOnNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolOnColor + ", 0 0 8px " + symbolOnColor + ", 0 0 14px " + symbolOnColor + ", 0 0 18px ";
                            //symbolOffNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + symbolOffColor + ", 0 0 14px " + symbolOffColor + ", 0 0 20px " + symbolOffColor + ", 0 0 24px " + symbolOffColor + ", 0 0 28px " + symbolOffColor;
                            symbolOffNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolOffColor + ", 0 0 8px " + symbolOffColor + ", 0 0 14px " + symbolOffColor + ", 0 0 18px ";
                            textOnNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textOnColor + ", 0 0 14px " + textOnColor + ", 0 0 20px " + textOnColor + ", 0 0 24px " + textOnColor + ", 0 0 28px " + textOnColor;
                            textOffNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textOffColor + ", 0 0 14px " + textOffColor + ", 0 0 20px " + textOffColor + ", 0 0 24px " + textOffColor + ", 0 0 28px " + textOffColor; 
                            displayOnNeonEffect = displayOnColor + " 2px 2px 16px, " + displayOnColor + " 2px -2px 16px, " + displayOnColor + " -2px 2px 16px, " + displayOnColor + " -2px -2px 16px";
                            displayOffNeonEffect = displayOffColor + " 2px 2px 16px, " + displayOffColor + " 2px -2px 16px, " + displayOffColor + " -2px 2px 16px, " + displayOffColor + " -2px -2px 16px";
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