<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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

<link rel="stylesheet" href="../css/widgetKnob.css">

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
        
        var widgetProperties, styleParameters, metricType, metricName, pattern, udm, 
            sizeRowsWidget, fontSize, metricType, countdownRef, widgetTitle, widgetHeaderColor, 
            widgetHeaderFontColor, showHeader, minDim, offset, mouseDown, startAngle, endAngle, currentAngle = null;
    
        var rotationViolation = false;
        
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
        
        
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        elToEmpty.css("font-family", "Verdana");
        var url = "<?= $_GET['link_w'] ?>";
        
        //Specifiche per questo widget
        var flagNumeric = false;
        var alarmSet = false;
        var udm = "";
        var pattern = /Percentuale\//;
        
        //Definizioni di funzione specifiche del widget
        //Restituisce il JSON delle soglie se presente, altrimenti NULL
        function getThresholdsJson()
        {
            var thresholdsJson = null;
            if(jQuery.parseJSON(widgetProperties.param.parameters !== null))
            {
                thresholdsJson = widgetProperties.param.parameters; 
            }
            
            return thresholdsJson;
        }
        
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
            //Provvisorio
            showWidgetContent(widgetName);
            $('#<?= $_GET['name'] ?>_noDataAlert').hide();
            $("#<?= $_GET['name'] ?>_loadErrorAlert").hide();
            $("#<?= $_GET['name'] ?>_chartContainer").show();
            
            if($("#<?= $_GET['name'] ?>_chartContainer").width() > $("#<?= $_GET['name'] ?>_chartContainer").height())
            {
                minDim = $("#<?= $_GET['name'] ?>_chartContainer").height();
            }
            else
            {
                minDim = $("#<?= $_GET['name'] ?>_chartContainer").width();
            }
            
            $('#<?= $_GET['name'] ?>_chartContainer').css("position", "relative");
            
            $('#<?= $_GET['name'] ?>_knob').width(minDim*0.9);
            $('#<?= $_GET['name'] ?>_knob').height(minDim*0.9);
            $('#<?= $_GET['name'] ?>_knob').css("border-radius", minDim*0.45);
            
            $('#<?= $_GET['name'] ?>_halo').width(minDim*0.925);
            $('#<?= $_GET['name'] ?>_halo').height(minDim*0.925);
            $('#<?= $_GET['name'] ?>_halo').css("border-radius", minDim*0.475);
            
            var knobMarginLeft = ($('#<?= $_GET['name'] ?>_chartContainer').width() - $('#<?= $_GET['name'] ?>_knob').width()) / 2;
            var knobMarginTop = ($('#<?= $_GET['name'] ?>_chartContainer').height() - $('#<?= $_GET['name'] ?>_knob').height()) / 2;
            $('#<?= $_GET['name'] ?>_knob').css("margin-left", knobMarginLeft + "px");
            $('#<?= $_GET['name'] ?>_knob').css("margin-top", knobMarginTop + "px");
            
            $('#<?= $_GET['name'] ?>_knobIndicator').css("height", minDim*0.2 + "px");
            
            
            //Legge di rotazione
            offset = $('#<?= $_GET['name'] ?>_knob').offset();
            mouseDown = false;
            
            currentAngle = 20;
            
            $('#<?= $_GET['name'] ?>_knob').css('-moz-transform', 'rotate(' + currentAngle + 'deg)');
            $('#<?= $_GET['name'] ?>_knob').css('-webkit-transform', 'rotate(' + currentAngle + 'deg)');
            $('#<?= $_GET['name'] ?>_knob').css('-o-transform', 'rotate(' + currentAngle + 'deg)');
            $('#<?= $_GET['name'] ?>_knob').css('-ms-transform', 'rotate(' + currentAngle + 'deg)');
            $('#<?= $_GET['name'] ?>_knob').css('transform', 'rotate(' + currentAngle + 'deg)');
            
            function knobGrows(currentAngle, newAngle)
            {
                if(currentAngle <= newAngle)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            
            function rotateKnob(evt) 
            {
                if(mouseDown === true)
                {
                    var center_x = offset.left + ($('#<?= $_GET['name'] ?>_knob').width() / 2);
                    var center_y = offset.top + ($('#<?= $_GET['name'] ?>_knob').height() / 2);
                    var mouse_x = evt.pageX;
                    var mouse_y = evt.pageY;
                    var radians = Math.atan2(mouse_x - center_x, mouse_y - center_y);
                    var newAngle = (radians * (180 / Math.PI) * -1) + 180; //Lasciarci il + 180, sennò aggiunge un angolo indesiderato
                    
                    //Condizione ok ma con bug if((((newAngle >= 0)&&(newAngle <= endAngle))||((newAngle >= startAngle)&&(newAngle <= 360))))
                    if((((newAngle >= 0)&&(newAngle <= endAngle))||((newAngle >= startAngle)&&(newAngle <= 360))))
                    {
                        $('#<?= $_GET['name'] ?>_knob').css('-moz-transform', 'rotate(' + newAngle + 'deg)');
                        $('#<?= $_GET['name'] ?>_knob').css('-webkit-transform', 'rotate(' + newAngle + 'deg)');
                        $('#<?= $_GET['name'] ?>_knob').css('-o-transform', 'rotate(' + newAngle + 'deg)');
                        $('#<?= $_GET['name'] ?>_knob').css('-ms-transform', 'rotate(' + newAngle + 'deg)');
                        $('#<?= $_GET['name'] ?>_knob').css('transform', 'rotate(' + newAngle + 'deg)'); 
                        currentAngle = newAngle;
                        console.log("New current angle: " + currentAngle);
                    }
                    else
                    {
                        console.log("Altri casi");
                        //rotationViolation = true;
                    }
                }
            }

            $('#<?= $_GET['name'] ?>_knob').mousedown(function (e) 
            {
                mouseDown = true;
                $(document).mousemove(rotateKnob);
            });

            $(document).mouseup(function (e) 
            {
                mouseDown = false;
                rotationViolation = false;
            });
            
            
            /*if(metricData !== null)
            {
                if(metricData.data[0] !== 'undefined')
                {
                    if(metricData.data.length > 0)
                    {
                        //Inizio eventuale codice ad hoc basato sui dati della metrica
                        if(firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                            $('#<?= $_GET['name'] ?>_noDataAlert').hide();
                            $("#<?= $_GET['name'] ?>_loadErrorAlert").hide();
                            $("#<?= $_GET['name'] ?>_chartContainer").show();
                        }
                        else
                        {
                            $("#" + widgetName + "_value").empty();
                            $("#" + widgetName + "_udm").empty();
                        }
                        
                        metricType = metricData.data[0].commit.author.metricType;
                        threshold = metricData.data[0].commit.author.threshold;
                        thresholdEval = metricData.data[0].commit.author.thresholdEval;

                        if((metricType === "Percentuale") || (pattern.test(metricType)))
                        {
                            if((metricData.data[0].commit.author.value_perc1 !== null) && (metricData.data[0].commit.author.value_perc1 !== "") && (metricData.data[0].commit.author.value_perc1 !== "undefined"))
                            {
                                value = parseFloat(parseFloat(metricData.data[0].commit.author.value_perc1).toFixed(1));
                                if(value > 100)
                                {
                                    value = 100;
                                }
                            }
                            flagNumeric = true;
                        }
                        else
                        {
                            switch(metricType)
                            {
                                case "Intero":
                                    if((metricData.data[0].commit.author.value_num !== null) && (metricData.data[0].commit.author.value_num !== "") && (typeof metricData.data[0].commit.author.value_num !== "undefined"))
                                    {
                                        value = parseInt(metricData.data[0].commit.author.value_num);
                                    }
                                    flagNumeric = true;
                                    break;

                                case "Float":
                                    if((metricData.data[0].commit.author.value_num !== null) && (metricData.data[0].commit.author.value_num !== "") && (typeof metricData.data[0].commit.author.value_num !== "undefined"))
                                    {
                                       value = parseFloat(parseFloat(metricData.data[0].commit.author.value_num).toFixed(1)); 
                                    }
                                    flagNumeric = true;
                                    break;

                                case "Testuale":
                                    value = metricData.data[0].commit.author.value_text;
                                    break;
                            }
                        }
                        
                        var fontSizeUdm = parseInt(fontSize*0.6);
                        $("#<?= $_GET['name'] ?>_value").css("font-size", fontSize + "px");
                        $("#<?= $_GET['name'] ?>_udm").css("font-size", fontSizeUdm + "px");
                        
                        
                        if((metricType === "Testuale") && (value === "-"))
                        {
                            showWidgetContent(widgetName);
                            $("#<?= $_GET['name'] ?>_chartContainer").hide();
                            $('#<?= $_GET['name'] ?>_noDataAlert').show();
                        }
                        else
                        {
                            if(udm !== null)
                            {
                               if(udmPos === 'next')
                               {   
                                  if((value !== null) && (value !== "") && (value !== undefined))
                                  {
                                     $("#<?= $_GET['name'] ?>_chartContainer").show();
                                     $("#<?= $_GET['name'] ?>_value").show();
                                     $("#<?= $_GET['name'] ?>_udm").hide();
                                     $("#<?= $_GET['name'] ?>_value").css("height", "100%");             
                                     $("#<?= $_GET['name'] ?>_value").css("alignItems", "center"); 
                                     $("#<?= $_GET['name'] ?>_value").html(value + udm);
                                  }
                                  else
                                  {
                                     $("#<?= $_GET['name'] ?>_value").hide();
                                     $("#<?= $_GET['name'] ?>_udm").hide(); 
                                     $("#<?= $_GET['name'] ?>_chartContainer").hide();
                                     $('#<?= $_GET['name'] ?>_noDataAlert').show();
                                  }
                               }
                               else
                               {
                                  if((value !== null) && (value !== "") && (value !== undefined))
                                  {
                                     $("#<?= $_GET['name'] ?>_chartContainer").show();
                                     $("#<?= $_GET['name'] ?>_value").show();
                                     $("#<?= $_GET['name'] ?>_udm").show();
                                     $("#<?= $_GET['name'] ?>_value").css("height", "60%");
                                     $("#<?= $_GET['name'] ?>_value").html(value);
                                     $("#<?= $_GET['name'] ?>_udm").css("height", "40%");
                                     $("#<?= $_GET['name'] ?>_udm").html(udm);
                                  }
                                  else
                                  {
                                     $("#<?= $_GET['name'] ?>_value").hide();
                                     $("#<?= $_GET['name'] ?>_udm").hide();
                                     $("#<?= $_GET['name'] ?>_chartContainer").hide();
                                     $('#<?= $_GET['name'] ?>_noDataAlert').show();
                                  }
                               }
                            }
                            else
                            {
                                if((value !== null) && (value !== "") && (value !== undefined))
                                {
                                    $("#<?= $_GET['name'] ?>_udm").css("display", "none");
                                    $("#<?= $_GET['name'] ?>_value").css("height", "100%");
                                    $("#<?= $_GET['name'] ?>_value").html(value);
                                }
                                else
                                {
                                    $("#<?= $_GET['name'] ?>_chartContainer").hide();
                                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                                }
                            }

                            $("#<?= $_GET['name'] ?>_value").css("color", fontColor);
                            $("#<?= $_GET['name'] ?>_udm").css("color", fontColor);
                        }
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        if(firstLoad !== false)
                        {
                            $("#<?= $_GET['name'] ?>_chartContainer").hide();
                            $('#<?= $_GET['name'] ?>_noDataAlert').show();
                        }
                    }
                }
                else
                {
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                        $("#<?= $_GET['name'] ?>_chartContainer").hide();
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }
                } 
            }
            else
            {
                showWidgetContent(widgetName);
                if(firstLoad !== false)
                {
                    $("#<?= $_GET['name'] ?>_chartContainer").hide();
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            }*/ 
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight);
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        
        //addLink(widgetName, url, linkElement, divContainer);
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
                    styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
                    sizeRowsWidget = parseInt(widgetProperties.param.size_rows);
                    startAngle = styleParameters.startAngle;
                    endAngle = styleParameters.endAngle;

                    //Fine eventuale codice ad hoc basato sulle proprietà del widget

                    $('#<?= $_GET['name'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_GET['name'] ?>_infoButtonDiv a.info_source').show();
                    manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
                    //metricData = getMetricData(metricName);
                    populateWidget(); 
                    
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
                <div id="<?= $_GET['name'] ?>_knob" class="knob">
                    <div id="<?= $_GET['name'] ?>_knobIndicator" class="knobIndicator"></div>
                </div>
                <div id='<?= $_GET['name'] ?>_halo' class="knobHalo"></div>
            </div>
        </div>
    </div>	
</div> 