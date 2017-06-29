<?php

/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

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
        var fontSizeSmall, scroller, widgetProperties, styleParameters, freeEvent, address, feeIcon, icon, eventId, serviceUri, description, endDate, 
            startDate, eventName, eventType, newRow, newIcon, eventContentW, test, widgetTargetList, backgroundTitleClass, backgroundFieldsClass,
            background, originalHeaderColor, originalBorderColor = null;    
        var eventNames = new Array();
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var speed = 50;
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_chartContainer");
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
        var widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= $_GET['link_w'] ?>";
        
        var eventsOnMaps = {};
        
        if(url === "null")
        {
            url = null;
        }
        
        if(fontSize <= 16)
        {
            fontSizeSmall = parseInt(parseInt(fontSize) - 1); 
        }
        else
        {
            fontSizeSmall = 15;
        }
        
        //Definizioni di funzione
        function addEventToMap(eventLink, widgetName)
        {
           var minLat, minLng, maxLat, maxLng = null;
           var coords = JSON.parse(eventLink.attr("data-coords"));
           var serviceUri = eventLink.attr("data-serviceUri");
           
           if(eventsOnMaps[widgetName]["noPointsUrl"] === null) 
           {
               var targetName = widgetName + "_div";
               eventsOnMaps[widgetName]["noPointsUrl"] = $("#" + targetName).attr("data-nopointsurl");
            }
           
           eventsOnMaps[widgetName].eventsPoints[serviceUri] = coords;
           eventsOnMaps[widgetName].eventsNumber++;
           
           if(eventsOnMaps[widgetName].eventsNumber > 1)
           {
              //Chiamata a service map per mostrare più punti
              minLat = +90;
              minLng = +180;
              maxLat = -90;
              maxLng = -180;
              
              for(var key in eventsOnMaps[widgetName].eventsPoints)
              {
                 if(eventsOnMaps[widgetName].eventsPoints[key][0] < minLat)
                 {
                    minLat = eventsOnMaps[widgetName].eventsPoints[key][0];
                 }
                 
                 if(eventsOnMaps[widgetName].eventsPoints[key][0] > maxLat)
                 {
                    maxLat = eventsOnMaps[widgetName].eventsPoints[key][0];
                 }
                 
                 if(eventsOnMaps[widgetName].eventsPoints[key][1] < minLng)
                 {
                    minLng = eventsOnMaps[widgetName].eventsPoints[key][1];
                 }
                 
                 if(eventsOnMaps[widgetName].eventsPoints[key][1] > maxLng)
                 {
                    maxLng = eventsOnMaps[widgetName].eventsPoints[key][1];
                 }
              }
         
              minLat = minLat - (minLat*0.0000002);
              maxLat = maxLat + (maxLat*0.0000002);
              minLng = minLng - (minLng*0.0000002);
              maxLng = maxLng + (maxLng*0.0000002);
              
              $("#" + widgetName + "_iFrame").attr("src", "<?= $serviceMapUrlPrefix ?>api/v1/?selection=" + minLat + ";" + minLng + ";" + maxLat + ";" + maxLng + "&categories=Events&maxResults=0&lang=it&format=html");
           }
           else
           {
              //Chiamata a service map per mostrare un solo punto
              $("#" + widgetName + "_iFrame").attr("src", "<?= $serviceMapUrlPrefix ?>" + "api/v1/?serviceUri=" + serviceUri + "&format=html");
           }
        }
        
        function removeEventFromMap(eventLink, widgetName)
        {
           var minLat, minLng, maxLat, maxLng = null;
           var serviceUri = eventLink.attr("data-serviceUri");
           
           delete eventsOnMaps[widgetName].eventsPoints[serviceUri];
           eventsOnMaps[widgetName].eventsNumber--;
           
           if(eventsOnMaps[widgetName].eventsNumber > 1)
           {
              //Chiamata a service map per mostrare più punti
              minLat = +90;
              minLng = +180;
              maxLat = -90;
              maxLng = -180;
              
              for(var key in eventsOnMaps[widgetName].eventsPoints)
              {
                 if(eventsOnMaps[widgetName].eventsPoints[key][0] < minLat)
                 {
                    minLat = eventsOnMaps[widgetName].eventsPoints[key][0];
                 }
                 
                 if(eventsOnMaps[widgetName].eventsPoints[key][0] > maxLat)
                 {
                    maxLat = eventsOnMaps[widgetName].eventsPoints[key][0];
                 }
                 
                 if(eventsOnMaps[widgetName].eventsPoints[key][1] < minLng)
                 {
                    minLng = eventsOnMaps[widgetName].eventsPoints[key][1];
                 }
                 
                 if(eventsOnMaps[widgetName].eventsPoints[key][1] > maxLng)
                 {
                    maxLng = eventsOnMaps[widgetName].eventsPoints[key][1];
                 }
              }
              
              minLat = minLat - (minLat*0.0000002);
              maxLat = maxLat + (maxLat*0.0000002);
              minLng = minLng - (minLng*0.0000002);
              maxLng = maxLng + (maxLng*0.0000002);
              
              $("#" + widgetName + "_iFrame").attr("src", "<?= $serviceMapUrlPrefix ?>api/v1/?selection=" + minLat + ";" + minLng + ";" + maxLat + ";" + maxLng + "&categories=Events&maxResults=0&lang=it&format=html");
           }
           else
           {
              if(eventsOnMaps[widgetName].eventsNumber === 1)
              {
                 for(var key in eventsOnMaps[widgetName].eventsPoints)
                 {
                    //Chiamata a service map per mostrare un solo punto
                    $("#" + widgetName + "_iFrame").attr("src", "<?= $serviceMapUrlPrefix ?>" + "api/v1/?serviceUri=" + key + "&format=html");
                 }
              }
              else
              {
                 $("#" + widgetName + "_iFrame").attr("src", eventsOnMaps[widgetName].noPointsUrl);
              }
           }
        }
        
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
            var oldPos = $("#<?= $_GET['name'] ?>_chartContainer").scrollTop();
            var newPos = oldPos + 1;
            
            var oldScrollTop = $("#<?= $_GET['name'] ?>_chartContainer").scrollTop();
            $("#<?= $_GET['name'] ?>_chartContainer").scrollTop(newPos);
            var newScrollTop = $("#<?= $_GET['name'] ?>_chartContainer").scrollTop();
            
            if(oldScrollTop === newScrollTop)
            {
               $("#<?= $_GET['name'] ?>_chartContainer").scrollTop(0);
            }
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
            
            widgetTargetList = JSON.parse(widgetProperties.param.parameters);
            var targetName = null;
            
            for(var name in widgetTargetList) 
            {
               targetName = name + "_div";
               eventsOnMaps[name] = {
                  noPointsUrl: null,//$("#" + targetName).attr("data-nopointsurl"),
                  eventsNumber: 0,
                  eventsPoints: {}//Array associativo le cui chiavi sono i serviceUri
               };
            }
            
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
                        $("#" + widgetName + "_chartContainer").off();
                        $("#" + widgetName + "_chartContainer").empty();
                    }
                   
                    //console.log(JSON.stringify(msg));
                    
                    var eventsNumber = msg.contents.Event.features.length;
                    var widgetWidth = $('#<?= $_GET['name'] ?>_div').width();
                    var shownHeight = $("#<?= $_GET['name'] ?>_chartContainer").prop("offsetHeight");
                    var rowPercHeight =  100 * 100 / shownHeight;
                    var contentHeightPx = eventsNumber * 100;
                    var eventContentWPerc = null;

                    if(contentHeightPx > shownHeight)
                    {
                        eventContentW = parseInt(widgetWidth - 45 - 22);
                    }
                    else
                    {
                        eventContentW = parseInt(widgetWidth - 45 - 5);
                    }
                    
                    eventContentWPerc = Math.floor(eventContentW / widgetWidth * 100);

                    for (var i = 0; i < eventsNumber; i++)
                    {
                        eventType = msg.contents.Event.features[i].properties.categoryIT;
                        eventName = msg.contents.Event.features[i].properties.name.toLowerCase();
                        if(eventNames.indexOf(eventName) < 0)
                        {
                            eventNames.push(eventName);
                            var coords = msg.contents.Event.features[i].geometry.coordinates;//Da service map arriva prima la longitudine e poi la latitudine, quindi le invertiamo
                            var temp = coords[0];
                            coords[0] = coords[1];
                            coords[1] = temp;
                            
                            startDate = msg.contents.Event.features[i].properties.startDate;
                            endDate = msg.contents.Event.features[i].properties.endDate;
                            var startTime = msg.contents.Event.features[i].properties.startTime;
                            description = msg.contents.Event.features[i].properties.descriptionIT;
                            serviceUri = msg.contents.Event.features[i].properties.serviceUri;
                            //var place = msg.contents.Event.features[i].properties.place;
                            address = msg.contents.Event.features[i].properties.address + " " + msg.contents.Event.features[i].properties.civic;
                            freeEvent = msg.contents.Event.features[i].properties.freeEvent;
                            var price = msg.contents.Event.features[i].properties.price;
                            var index = serviceUri.indexOf("Event");
                            eventId = serviceUri.substring(index);
                            
                            //Per l'interazione cross-widget
                            var serviceMapUrl = "<?= $serviceMapUrlPrefix ?>" + "api/v1/?serviceUri=" + serviceUri + "&format=html";

                            newRow = $("<div></div>");
                            
                            switch (eventType)
                            {
                                 case "Altri eventi":
                                    icon= $("<i class='fa fa-calendar-check-o'></i>");
                                    backgroundTitleClass = "altriEventiTitle";
                                    backgroundFieldsClass = "altriEventi";//Grigio
                                    background = "#d9d9d9";
                                    break;
                                    
                                 case "Aperture straordinarie, visite guidate":
                                    icon= $("<i class='material-icons' style='font-size:36px'>group</i>");
                                    backgroundTitleClass = "apertureStraordinarieTitle";
                                    backgroundFieldsClass = "apertureStraordinarie";//Giallo
                                    background = "#ffcc00";
                                    break;
                                    
                                 case "Estate Fiorentina":
                                    icon= $("<i class='fa fa-sun-o'></i>");
                                    backgroundTitleClass = "estateFiorentinaTitle";
                                    backgroundFieldsClass = "estateFiorentina";//Verde chiaro
                                    background = "#00b300";
                                    break;   
                                    
                                 case "Fiere, mercati":
                                    icon= $("<i class='fa fa-shopping-cart'></i>");
                                    backgroundTitleClass = "fiereTitle";
                                    backgroundFieldsClass = "fiere";//Rosa
                                    background = "#ff66cc";
                                    break;   
                                    
                                 case "Film festival":
                                    icon= $("<i class='fa fa-film'></i>"); 
                                    backgroundTitleClass = "filmTitle";
                                    backgroundFieldsClass = "film";//Arancio
                                    background = "#ff9900";
                                    break;   
      
                                 case "Mostre":
                                    icon= $("<i class='fa fa-bank'></i>");
                                    backgroundTitleClass = "mostreTitle";
                                    backgroundFieldsClass = "mostre";//Turchese
                                    background = "#70dbc4";
                                    break;
                                    
                                 case "Musica classica, opera e balletto":
                                    icon= $("<i class='fa fa-music'></i>");
                                    backgroundTitleClass = "musicaClassicaTitle";
                                    backgroundFieldsClass = "musicaClassica";//Rosso scuro
                                    background = "#e6004c";
                                    break;
                                    
                                 case "Musica rock, jazz, pop, contemporanea":
                                    icon= $("<i class='fa fa-music'></i>");
                                    backgroundTitleClass = "musicaRockTitle";
                                    backgroundFieldsClass = "musicaRock";//Viola
                                    background = "#bab3ff";
                                    break;   

                                case "News":
                                    icon= $("<i class='fa fa-newspaper-o'></i>");
                                    backgroundFieldsClass = "news";//Ruggine
                                    backgroundTitleClass = "newsTitle";
                                    background = "#ff531a";
                                    break;
                                    
                                 case "Readings, Conferenze, Convegni":
                                    icon= $("<i class='fa fa-group'></i>");
                                    backgroundTitleClass = "convegniTitle";
                                    backgroundFieldsClass = "convegni";//Violetto chiaro
                                    background = "#ffb3ff";
                                    break;   
                                    
                                 case "Readings, incontri letterari, conferenze":
                                    icon= $("<i class='fa fa-book'></i>");
                                    backgroundTitleClass = "incontriLetterariTitle";
                                    backgroundFieldsClass = "incontriLetterari";//Azzurro
                                    background = "#33ccff";
                                    break;   
                                    
                                 case "Sport":
                                    icon= $("<i class='fa fa-futbol-o'></i>");
                                    backgroundTitleClass = "sportTitle";
                                    backgroundFieldsClass = "sport";//Rosso chiaro
                                    background = "#ff6666";
                                    break;
                                    
                                 case "Teatro":
                                    icon= $("<i class='fa fa-ticket'></i>");
                                    backgroundTitleClass = "teatroTitle";
                                    backgroundFieldsClass = "teatro";//Ocra
                                    background = "#d9b38c";
                                    break;
                                    
                                 case "Tradizioni popolari":
                                    icon= $("<i class='fa fa-spoon'></i>");
                                    backgroundTitleClass = "tradizioniPopolariTitle";
                                    backgroundFieldsClass = "tradizioniPopolari";//Grigio chiaro
                                    background = "#e6e6e6";
                                    break;
                                    
                                 case "Walking":
                                    icon= $("<i class='fa fa-male'></i>");
                                    backgroundTitleClass = "walkingTitle";
                                    backgroundFieldsClass = "walking";//Verde scuro
                                    background = "#39ac39";
                                    break;      
                                    
                                default:
                                    icon= $("<i class='fa fa-calendar-check-o'></i>");
                                    backgroundTitleClass = "altriEventiTitle";
                                    backgroundFieldsClass = "altriEventi";//Grigio
                                    background = "#d9d9d9";
                                    break;
                            }
                            
                            newRow.css("height", rowPercHeight + "%");
                            //var eventTitle = $('<div class="eventTitle centerWithFlex" data-toggle="tooltip" data-placement="top" title="' + description + '">' + eventName + '</div>');
                            var eventTitle = $('<div class="eventTitle centerWithFlex">' + eventName + '</div>');
                            eventTitle.addClass(backgroundTitleClass);
                            eventTitle.css("font-size", fontSize + "px");
                            eventTitle.css("height", "30%");
                            $('#<?= $_GET['name'] ?>_chartContainer').append(newRow);
                            
                            eventTitle.dotdotdot({
                              /*	The text to add as ellipsis. */
                              ellipsis	: '...',

                              /*	How to cut off the text/html: 'word'/'letter'/'children' */
                              wrap: 'word',

                              /*	Wrap-option fallback to 'letter' for long words */
                              fallbackToLetter: true,

                              /*	jQuery-selector for the element to keep and put after the ellipsis. */
                              after: null,

                              /*	Whether to update the ellipsis: true/'window' */
                              watch: true,

                              /*	Optionally set a max-height, can be a number or function.
                                 If null, the height will be measured. */
                              height: 34,

                              /*	Deviation for the height-option. */
                              tolerance: 0,

                              /*	Callback function that is fired after the ellipsis is added,
                                 receives two parameters: isTruncated(boolean), orgContent(string). */
                              callback	: function( isTruncated, orgContent ) {
                              },

                              lastCharacter	: {

                                 /*	Remove these characters from the end of the truncated text. */
                                 remove		: [ ' ', ',', ';', '.', '!', '?' ],

                                 /*	Don't add an ellipsis if this array contains 
                                    the last character of the truncated text. */
                                 noEllipsis	: []
                              }
                           });
                           
                           newRow.append(eventTitle);
                           
                           var dataContainer = $('<div class="eventDataContainer"></div>');
                           dataContainer.addClass(backgroundFieldsClass);
                            
                           newIcon = $("<div class='eventIcon'></div>");
                           var newIconUp = $("<div class='eventIconUp' data-toggle='tooltip' data-placement='top' title='" + eventType + "'></div>");
                            
                           newIconUp.append(icon);
                           var newIconDown = $("<div class='eventIconDown' data-toggle='tooltip' data-placement='top'></div>");
                           
                           if(freeEvent === 'NO')
                           {
                              feeIcon = $("<i class='fa fa-euro'></i>");
                              newIconDown.append(feeIcon); 
                              newIconDown.attr("title", price);
                           }
                           else
                           {
                              newIconDown.html("free");
                              newIconDown.attr("title", "This event is free");
                           }
                           
                           newIcon.append(newIconUp);
                           newIcon.append(newIconDown);  
                           newIcon.addClass(backgroundFieldsClass);
                           dataContainer.append(newIcon);
                           
                           //var dateTooltip = "Start date: " + startDate + "<br>End date: " + endDate + "<br>Hours: " + startTime + "";
                           //var dateContainer = $("<div class='eventTime' data-toggle='tooltip' data-placement='top' title='" + dateTooltip + "'><i class='fa fa-calendar' style='font-size:13px'></i>&nbsp;&nbsp;" + startDate + " to " + endDate + "</div>"); 
                           var dateContainer = $("<div class='eventTime'><i class='fa fa-calendar' style='font-size:13px'></i>&nbsp;&nbsp;" + startDate + " to " + endDate + "</div>"); 
                           dateContainer.addClass(backgroundFieldsClass);
                           dataContainer.append(dateContainer);
                           
                           //var addressTooltip = "<strong>CLICK THIS PIN TO SHOW OR HIDE THE PLACE ON THE HIGHLIGHTED MAP(S)</strong>";
                           //var eventAddress = $("<div class='eventAddress' data-toggle='tooltip' data-placement='bottom' title='" + addressTooltip + "'><a class='eventLink' data-serviceMapUrl='" + serviceMapUrl + "' data-eventType='" + eventType + "' data-background='" + background + "' data-onMap='false' data-coords='" + JSON.stringify(coords) + "'><i class='material-icons' style='font-size:16px'>place</i>&nbsp;" + address + "<span class='onMapSignal'> <i class='fa fa-caret-right' style='font-size:16px'></i> ON MAP</span></a></div>");
                           var eventAddress = $("<div class='eventAddress'><a class='eventLink' data-serviceUri='" + serviceUri + "' data-serviceMapUrl='" + serviceMapUrl + "' data-eventType='" + eventType + "' data-background='" + background + "' data-onMap='false' data-coords='" + JSON.stringify(coords) + "'><i class='material-icons' style='font-size:16px'>place</i>&nbsp;" + address + "<span class='onMapSignal'> <i class='fa fa-caret-right' style='font-size:16px'></i> ON MAP</span></a></div>");
                           eventAddress.addClass(backgroundFieldsClass);
                           dataContainer.append(eventAddress);
                           
                           newRow.append(dataContainer);
                           
                           dateContainer.css("font-size", fontSizeSmall + "px");
                           eventAddress.css("font-size", fontSizeSmall + "px");
                           
                           if(i < (eventsNumber -1))
                           {
                              newRow.css("margin-bottom", "4px");
                           }
                           else
                           {
                              newRow.css("margin-bottom", "0px");
                           }
                           
                           $('[data-toggle="tooltip"]').tooltip({
                              html: true
                           });
                           
                           $("#<?= $_GET['name'] ?>_chartContainer").scrollTop(0);
                            
                           //Interazione cross-widget
                           
                           eventAddress.find("a.eventLink").hover(
                              function() 
                              {
                                 var localEventType = $(this).attr("data-eventType");
                                 var localBackground = $(this).attr("data-background");
                                 
                                 originalHeaderColor = {};
                                 originalBorderColor = {};
                                 
                                 for(var widgetName in widgetTargetList) 
                                 {
                                    originalHeaderColor[widgetName] = $("#" + widgetName + "_header").css("background-color");
                                    originalBorderColor[widgetName] = $("#" + widgetName).css("border-color");
                                    
                                    for(var key in widgetTargetList[widgetName]) 
                                    {
                                       if(widgetTargetList[widgetName][key] === localEventType)
                                       {
                                          $("#" + widgetName + "_header").css("background", localBackground);
                                          $("#" + widgetName).css("border-color", localBackground);
                                       }
                                    }
                                 }
                              }, 
                              function() 
                              {
                                var localEventType = $(this).attr("data-eventType");
                                 for(var widgetName in widgetTargetList) 
                                 {
                                    for(var key in widgetTargetList[widgetName]) 
                                    {
                                       if(widgetTargetList[widgetName][key] === localEventType)
                                       {
                                          $("#" + widgetName + "_header").css("background", originalHeaderColor[widgetName]);
                                          $("#" + widgetName).css("border-color", originalBorderColor[widgetName]);
                                       }
                                    }
                                 }
                              }
                           );
                           
                           eventAddress.find("a.eventLink").click(function()
                           {  
                              var localEventType = $(this).attr("data-eventType");
                              var goesOnMap = false;
                              
                              if($(this).attr("data-onMap") === 'false')
                              {
                                 $(this).attr("data-onMap", 'true');
                                 goesOnMap = true;
                                 $(this).find("i.material-icons").addClass("onMapPinAnimated");
                                 $(this).find("span.onMapSignal").show();
                              }
                              else
                              {
                                 $(this).attr("data-onMap", 'false');
                                 goesOnMap = false;
                                 $(this).find("i.material-icons").removeClass("onMapPinAnimated");
                                 $(this).find("span.onMapSignal").hide();
                              }
                              
                              for(var widgetName in widgetTargetList) 
                              {
                                 for(var key in widgetTargetList[widgetName]) 
                                 {
                                    if(widgetTargetList[widgetName][key] === localEventType)
                                    {
                                       if(goesOnMap)
                                       {
                                          addEventToMap($(this), widgetName);
                                       }
                                       else
                                       {
                                          removeEventFromMap($(this), widgetName);
                                       }
                                    }
                                 }
                              }
                           });
                        }
                    }
                    
                    scroller = setInterval(stepDownInterval, speed);
                    var timeToClearScroll = (timeToReload - 0.5) * 1000;
                    setTimeout(function()
                    {
                        clearInterval(scroller);
                        $("#<?= $_GET['name'] ?>_chartContainer").off("scroll");
                        
                        //Ripristino delle homepage native per gli widget targets al reload
                        for(var widgetName in widgetTargetList) 
                        {
                           if(eventsOnMaps[widgetName].eventsNumber > 0)
                           {
                              $("#" + widgetName + "_iFrame").attr("src", eventsOnMaps[widgetName].noPointsUrl);
                           }
                        }
                        
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
                <!--<a id ="info_modal" href="#" class="info_source"><img id="source_<?= $_GET['name'] ?>" src="../management/img/info.png" class="source_button"></a>-->
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
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