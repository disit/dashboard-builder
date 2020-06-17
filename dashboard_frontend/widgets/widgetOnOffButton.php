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

            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetOnOffButton.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
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
            $useActuatorWS = $wsServerContent["wsServerActuator"][$env];?>
                
        var headerHeight = 25;
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= escapeForJS($_REQUEST['color_w']) ?>';
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var widgetProperties, styleParameters, metricType, metricName, widgetParameters, nrInputId,
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
        var updatedEverFlag = false;
        var updatedFlag = false;
        var lastValueOk = null;
        var useWebSocket = <?= $useActuatorWS ?>;
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

        function handleExtUpdate()
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
         //   updateRemoteValue();
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
            if (updatedEverFlag !== true) {
                setUpdatingMsgInterval = setInterval(function () {
                    if ((requestComplete === false) && (Math.abs(new Date() - updateRequestStartTime) > 1100)) {
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("display", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_display').css("display", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "block");
                        var loadingIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width() - $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').width()) / 2);
                        var loadingIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height() - $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').height()) / 2);
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("top", loadingIconMarginTop + "px");
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("left", loadingIconMarginLeft + "px");
                    }
                }, 250);
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
                        }
                        console.log(widgetName+" SEND ackReceived:"+webSocket.ackReceived)
                        if(webSocket.readyState==webSocket.OPEN) {
                            webSocket.send(JSON.stringify(data));
                            webSocket.timeout = setTimeout(function() {
                              if(!webSocket.ackReceived) {
                                console.log(widgetName+" ERR1 ackReceived:"+webSocket.ackReceived)
                                requestComplete = true;
                                clearInterval(setUpdatingMsgInterval);
                                showUpdateResult("API KO");
                                console.log("Update value KO");
                              }
                            },60000)
                        } else {
                            console.log(widgetName+" ERR1 socket not OPEN");
                            requestComplete = true;
                            clearInterval(setUpdatingMsgInterval);
                            showUpdateResult("API KO");
                            console.log("Update value KO");
                        }                      
                    } else {
                        $.ajax({
                            url: "../widgets/actuatorUpdateValuePersonalApps.php",
                            type: "POST",
                            data: {
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
                    }
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
        function resizeWidget() {
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
                        nrInputId = widgetProperties.param.nrInputId;
                        nodeRedInputName = widgetProperties.param.name;
                        dataType = widgetProperties.param.valueType;
                        offValue = widgetProperties.param.offValue;
                        onValue = widgetProperties.param.onValue;
                        username = widgetProperties.param.creator;
                        endPointHost = widgetProperties.param.endPointHost;
                        endPointPort = widgetProperties.param.endPointPort;
                        if(useWebSocket)
                          openWs(widgetName)
                    }
                    
                    switch(dataType)
                    {
                        case "Integer": case "integer":
                            currentValue = parseInt(widgetProperties.param.currentValue);
                            offValue = parseInt(offValue);
                            onValue = parseInt(onValue);
                            break;

                        case "Float": case "float":
                            currentValue = parseFloat(widgetProperties.param.currentValue);
                            offValue = parseFloat(offValue);
                            onValue = parseFloat(onValue);
                            break;

                        case "String": case "string":
                            currentValue = widgetProperties.param.currentValue;
                            break;

                        case "Boolean": case "boolean":
                            if((widgetProperties.param.currentValue === true)||(widgetProperties.param.currentValue === 'true')||(widgetProperties.param.currentValue === 'True'))
                            {
                                currentValue = true;
                            }
                            else
                            {
                                currentValue = false;   
                            }
                            
                            offValue = Boolean(offValue);
                            onValue = Boolean(onValue);
                            break;   
                            
                        default:
                            currentValue = widgetProperties.param.currentValue;
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
            showHeader = event.showHeader;
            populateWidget();
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
                 //   currentValue = lastValueOk;
                    lastValueOk = null;
                //    handleMouseDown();
                    handleExtUpdate();
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('click');
                    showUpdateResult("Device OK");
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
