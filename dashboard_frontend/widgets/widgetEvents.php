<?php

/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

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
?>
<script type='text/javascript'>
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad)  
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
        var fontSizeSmall, scroller, scrollBottom, widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters,  
            metricType, metricData, pattern, descriptions, widgetParameters, freeEvent, address, feeIcon, icon, eventId, serviceUri, description, endDate, 
            startDate, eventName, eventType, newRow, newIcon, newContent, eventContentW, test = null;    
        var eventNames = new Array();
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var speed = 50;
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_chartContainer");
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
        var widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= $_GET['color'] ?>';
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var fontColor = "<?= $_GET['fontColor'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var metricId = "<?= $_GET['metric'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        
        if(url === "null")
        {
            url = null;
        }
        
        if(fontSize <= 16)
        {
            fontSizeSmall = parseInt(parseInt(fontSize) - 2); 
        }
        else
        {
            fontSizeSmall = 14;
        }
        
        //Definizioni di funzione specifiche del widget
        /*Restituisce il JSON delle soglie se presente, altrimenti NULL*/
        function getThresholdsJson()
        {
            var thresholdsJson = null;
            if(jQuery.parseJSON(widgetProperties.param.parameters !== null))
            {
                thresholdsJson = widgetProperties.param.parameters; 
            }
            
            return thresholdsJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getInfoJson()
        {
            var infoJson = null;
            if(jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson); 
            }
            
            return infoJson;
        }
        
        /*Restituisce il JSON delle info se presente, altrimenti NULL*/
        function getStyleParameters()
        {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        
        function stepDownInterval()
        {
            var pos = $("#<?= $_GET['name'] ?>_chartContainer").scrollTop();
            if(pos < scrollBottom)
            {
                pos++;
            }
            else
            {
                pos = 0;
            }
            $("#<?= $_GET['name'] ?>_chartContainer").scrollTop(pos);
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor);
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_GET['name'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            
            $.ajax({
                url: "../widgets/curlProxy.php?url=<?=$internalServiceMapUrlPrefix?>api/v1/events/?range=day",
                type: "GET",
                async: true,
                dataType: 'json',
                success: function (msg) 
                {
                    if(firstLoad !== false)
                    {
                        showWidgetContent(widgetName);
                    }
                    else
                    {
                        /*console.log("EventNames l pre svuotamento: " + eventNames.length);
                        eventNames.splice(0, eventNames.length);
                        console.log("EventNames l post svuotamento: " + eventNames.length);*/
                        $("#" + widgetName + "_chartContainer").off();
                        $("#" + widgetName + "_chartContainer").empty();
                    }
        
                    var rowColor = "viola";
                    var rowGrad = "violaGrad";
                    
                    var eventsNumber = msg.contents.Event.features.length;
                    var contentWidth = $('#<?= $_GET['name'] ?>_div').prop("offsetWidth") - 17;//Indifferente variarla, perchè?
                    var shownHeight = $("#<?= $_GET['name'] ?>_chartContainer").prop("offsetHeight");
                    var rowPercHeight =  75 * 100 / shownHeight;
                    var rowPercBottomMargin =  5 * 100 / shownHeight;
                    var iconPercWidth = Math.ceil(45 * 100 / contentWidth);
                    var contentPercWidth = 100 - rowPercBottomMargin - iconPercWidth;
                    var rowPxHeight = rowPercHeight*shownHeight/100 + rowPercBottomMargin*shownHeight/100;
                    var contentHeight = rowPxHeight * eventsNumber;
                    scrollBottom = Math.floor(contentHeight - shownHeight) - 3;

                    if($('#<?= $_GET['name'] ?>_chartContainer').height() < shownHeight)
                    {
                        eventContentW = parseInt($('#<?= $_GET['name'] ?>_div').width() - 45 - 22);
                    }
                    else
                    {
                        eventContentW = parseInt($('#<?= $_GET['name'] ?>_div').width() - 45 - 5);
                    }

                    for (var i = 0; i < eventsNumber; i++)
                    {
                        eventType = msg.contents.Event.features[i].properties.categoryIT;
                        eventName = msg.contents.Event.features[i].properties.name.toLowerCase();
                        if(eventNames.indexOf(eventName) < 0)
                        {
                            eventNames.push(eventName);
                            if(eventName.length > 77)
                            {
                                eventName = eventName.substring(0, 70) + "...";
                            }
                            startDate = msg.contents.Event.features[i].properties.startDate;
                            endDate = msg.contents.Event.features[i].properties.endDate;
                            description = msg.contents.Event.features[i].properties.descriptionIT;
                            serviceUri = msg.contents.Event.features[i].properties.serviceUri;
                            address = msg.contents.Event.features[i].properties.address + " " + msg.contents.Event.features[i].properties.civic;
                            freeEvent = msg.contents.Event.features[i].properties.freeEvent;
                            var index = serviceUri.indexOf("Event");
                            eventId = serviceUri.substring(index);

                            newRow = $("<div class='eventsRow'></div>");
                            newIcon = $("<div class='eventIcon'></div>");
                            var newIconUp = $("<div class='eventIconUp'></div>");
                            var newIconDown = $("<div class='eventIconDown'></div>");
                            newIcon.append(newIconUp);
                            newIcon.append(newIconDown);
                            newContent = $("<div class='eventContent turcheseGrad'></div>");
                            newContent.css("width", eventContentW + "px");
                            newContent.html("<div class='eventName'><p>" + eventName + "</p></div>" + "<div class='eventTime'><i class='fa fa-calendar' style='font-size:13px'></i>&nbsp;&nbsp;" + startDate + " to " + endDate + "</div>" + "<div class='eventAddress'><a href='http://servicemap.disit.org/WebAppGrafo/api/v1/?serviceUri=http://www.disit.org/km4city/resource/" + eventId + "&format=html' class='eventLink' target='_blank'><i class='material-icons' style='font-size:16px'>place</i>&nbsp;" + address + "</a></div>");
                            $('#<?= $_GET['name'] ?>_chartContainer .eventName').css("font-size", fontSize + "px");
                            $('#<?= $_GET['name'] ?>_chartContainer .eventTime').css("font-size", fontSizeSmall + "px");
                            $('#<?= $_GET['name'] ?>_chartContainer .eventAddress').css("font-size", fontSizeSmall + "px");

                            switch (eventType)
                            {
                                case "Mostre":
                                    icon= $("<i class='fa fa-bank'></i>");
                                    newIconUp.append(icon);
                                    break;

                                case "News":
                                    icon= $("<i class='fa fa-newspaper-o'></i>");
                                    newIconUp.append(icon);
                                    break;

                                case "Aperture straordinarie, visite guidate":
                                    icon= $("<i class='material-icons' style='font-size:36px'>group</i>");
                                    newIconUp.append(icon);
                                    break;    

                                default:
                                    icon= $("<i class='fa fa-calendar-check-o'></i>");
                                    newIconUp.append(icon);
                                    break;
                            }

                            if(freeEvent === 'NO')
                            {
                                feeIcon = $("<i class='fa fa-euro'></i>");
                                newIconDown.append(feeIcon); 
                            }
                            else
                            {
                                newIconDown.html("free");
                            }
                            
                            switch(rowColor)
                            {
                                case "turchese":
                                    rowColor = "arancio";
                                    rowGrad = "arancioGrad";
                                    break;

                                case "arancio":
                                    rowColor = "viola";
                                    rowGrad = "violaGrad";
                                    break;

                                case "viola":
                                    rowColor = "turchese";
                                    rowGrad = "turcheseGrad";
                                    break;   
                                    
                                default:
                                    rowColor = "turchese";
                                    rowGrad = "turcheseGrad";
                                    break;
                            }
                            
                            newIcon.addClass(rowColor);
                            newContent.addClass(rowGrad);

                            newRow.append(newIcon);
                            newRow.append(newContent);
                            newRow.css("background-color", "<?= $_GET['color'] ?>");

                            if(i === (eventsNumber - 1))
                            {
                                newRow.css("margin-bottom", "0px");
                                newRow.find(".eventName").css("font-size", fontSize + "px");
                                newRow.find(".eventTime").css("font-size", fontSizeSmall + "px");
                                newRow.find(".eventAddress").css("font-size", fontSizeSmall + "px");
                            }
                            $('#<?= $_GET['name'] ?>_chartContainer').append(newRow);
                            $("#<?= $_GET['name'] ?>_chartContainer").scrollTop(0);
                        }
                    }
                    

                    $('#<?= $_GET['name'] ?>_chartContainer .eventsRow').css("width", "100%");
                    $('#<?= $_GET['name'] ?>_chartContainer .eventsRow').css("height", rowPercHeight + "%");
                    $('#<?= $_GET['name'] ?>_chartContainer .eventsRow').css("margin-bottom", rowPercBottomMargin + "%");
                    $('#<?= $_GET['name'] ?>_chartContainer .eventIcon').css("width", iconPercWidth + "%");
                    $('#<?= $_GET['name'] ?>_chartContainer .eventIcon').css("height", "100%");
                    $('#<?= $_GET['name'] ?>_chartContainer .eventIcon').css("margin-right", rowPercBottomMargin + "%");
                    $('#<?= $_GET['name'] ?>_chartContainer .eventContent ').css("width", contentPercWidth + "%");

                    scroller = setInterval(stepDownInterval, speed);
                    var timeToClearScroll = (timeToReload - 0.5) * 1000;
                    setTimeout(function()
                    {
                        clearInterval(scroller);
                        $("#<?= $_GET['name'] ?>_chartContainer").off("scroll");
                    }, timeToClearScroll);
                    

                    $("#<?= $_GET['name'] ?>_chartContainer").mouseenter(function() 
                    {
                        clearInterval(scroller);
                    });

                    $("#<?= $_GET['name'] ?>_chartContainer").mouseleave(function()
                    {    
                        clearInterval(scroller);    
                        scroller = setInterval(stepDownInterval, speed);
                    });
                },
                error: function (jqXHR, textStatus, errorThrow) 
                {
                    showWidgetContent(widgetName);
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            });  
        }
        else
        {
            alert("Error while loading widget properties");
        }
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, elToEmpty, "widgetEvents", test, eventNames);
    });//Fine document ready
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
                <a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a>
                <a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a>
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
            <p id="<?= $_GET['name'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer event_data"></div>
        </div>
    </div>	
</div> 