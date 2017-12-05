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



<script type='text/javascript'>
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef)    
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
        var scroller, widgetProperties, styleParameters, icon, serviceUri, 
            eventName, eventType, newRow, newIcon, eventContentW, widgetTargetList, backgroundTitleClass, backgroundFieldsClass,
            background, originalHeaderColor, originalBorderColor, eventTitle, eventName, serviceUri, eventLat, eventLng,
            eventsNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx, eventContentWPerc, dataContainer, 
            dateContainer, lastPopup, startDate, endDate, startTime, description, address, freeEvent, price, 
            eventTitle, dataContainer, feeIcon, dateContainer, countdownRef, eventAddress, widgetMode, fontSizeSmall, localEventType, localBackground, goesOnMap, 
            eventProperties = null;    
    
        var eventNames = new Array();
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var speed = 50;
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_mainContainer");
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
        var widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var timeToReload = <?= $_GET['freq'] ?>;
        var elToEmpty = $("#<?= $_GET['name'] ?>_rollerContainer");
        var url = "<?= $_GET['link_w'] ?>";
        
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';
        var showTitle = "<?= $_GET['showTitle'] ?>";
        var showHeader = null;        
        var headerHeight = 25;
        
        var eventsArray = [];
        var eventsOnMaps = {};
        var targetsArrayForNotify = [];
        
        if(url === "null")
        {
            url = null;
        }
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
        {
            showHeader = false;
        }
        else
        {
            showHeader = true;
        }
   
        if(fontSize <= 16)
        {
            fontSizeSmall = parseInt(parseInt(fontSize) - 1); 
        }
        else
        {
            fontSizeSmall = 15;
        }
        
        $(document).on("esbEventAdded", function(event){
           if(event.generator !== "<?= $_GET['name'] ?>")
           {
              $("#<?= $_GET['name'] ?>_rollerContainer a.eventLink").each(function(i){
                 if($(this).attr("data-onmap") === "true")
                 {
                    var evtType = null;
                    
                    for(widgetName in widgetTargetList)
                    {
                       evtType = $(this).attr("data-eventType");
                       
                       for(var index in widgetTargetList[widgetName])
                       {
                          if(evtType === widgetTargetList[widgetName][index])
                          {
                             $(this).attr("data-onMap", 'false');
                             /*$(this).find("i.material-icons").removeClass("onMapPinAnimated");
                             $(this).find("span.onMapSignal").hide();*/
                        
                             $(this).find("i.material-icons").css("color", "black");
                             $(this).find("i.material-icons").html("navigation");
                             $(this).find("i.material-icons").css("text-shadow", "none");

                             eventsOnMaps[widgetName].eventsNumber = 0; 
                             eventsOnMaps[widgetName].eventsPoints.splice(0);
                             eventsOnMaps[widgetName].mapRef = null;
                          }
                       }
                    }
                 }
              });
           }
        });
        
        //Definizioni di funzione
        function loadDefaultMap(widgetName)
        {
            if($('#' + widgetName + '_defaultMapDiv div.leaflet-map-pane').length > 0)
            {
                //Basta nasconderla, tanto viene distrutta e ricreata ad ogni utilizzo (per ora).
               $('#' + widgetName + '_mapDiv').hide();
               $('#' + widgetName + '_defaultMapDiv').show();
            }
            else
            {
                var mapdiv = widgetName + "_defaultMapDiv";
                var mapRef = L.map(mapdiv).setView([43.769789, 11.255694], 11);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                   attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                   maxZoom: 18
                }).addTo(mapRef);
                mapRef.attributionControl.setPrefix('');
            }
        }
        
        function populateWidget()
        {
           var i = 0;
           var mapIconName = null;
           
           $('#<?= $_GET['name'] ?>_rollerContainer').empty();

            if(contentHeightPx > shownHeight)
            {
                eventContentW = parseInt(widgetWidth - 20);
            }
            else
            {
                eventContentW = parseInt(widgetWidth);
            }

            eventContentWPerc = Math.floor(eventContentW / widgetWidth * 100);
           
           for(var i = 0; i < eventsNumber; i++)
           {
               eventProperties = eventsArray[i].properties;
               eventType = eventProperties.categoryIT;
               eventName = eventProperties.name;
               
               if(eventName.includes('?'))
               {
                   eventName = eventName.replace(/\?/g, "'");
               }
               
               eventLat = eventsArray[i].geometry.coordinates[1];
               eventLng = eventsArray[i].geometry.coordinates[0];
               
               if(eventNames.indexOf(eventName) < 0)
               {
                   eventNames.push(eventName);

                   startDate = eventsArray[i].properties.startDate;
                   endDate = eventsArray[i].properties.endDate;
                   startTime = eventsArray[i].properties.startTime;
                   description = eventsArray[i].properties.descriptionIT;
                   
                   if(description.includes('?'))
                   {
                       description = description.replace(/\?/g, "'");
                   }
                   
                   serviceUri = eventsArray[i].properties.serviceUri;
                   address = eventsArray[i].properties.address + " " + eventsArray[i].properties.civic;
                   
                   if(address.includes('?'))
                   {
                       address = address.replace(/\?/g, "'");
                   }
                   
                   freeEvent = eventsArray[i].properties.freeEvent;
                   price = eventsArray[i].properties.price;
                   
                   newRow = $("<div></div>");
                   
                    switch(eventType)
                    {
                         case "Altri eventi":
                            icon= $("<i class='fa fa-calendar-check-o'></i>");
                            backgroundTitleClass = "altriEventiTitle";
                            backgroundFieldsClass = "altriEventi";//Grigio
                            background = "#d9d9d9";
                            mapIconName = "altriEventi";
                            break;

                         case "Aperture straordinarie, visite guidate":
                            icon= $("<i class='material-icons' style='font-size:36px'>group</i>");
                            backgroundTitleClass = "apertureStraordinarieTitle";
                            backgroundFieldsClass = "apertureStraordinarie";//Giallo
                            background = "#ffdb4d";
                            mapIconName = "apertureStraordinarie";
                            break;

                         case "Estate Fiorentina":
                            icon= $("<i class='fa fa-sun-o'></i>");
                            backgroundTitleClass = "estateFiorentinaTitle";
                            backgroundFieldsClass = "estateFiorentina";//Verde chiaro
                            background = "#00b300";
                            mapIconName = "estateFiorentina";
                            break;   

                         case "Fiere, mercati":
                            icon= $("<i class='fa fa-shopping-cart'></i>");
                            backgroundTitleClass = "fiereTitle";
                            backgroundFieldsClass = "fiere";//Rosa
                            background = "#ff80d5";
                            mapIconName = "fiere";
                            break;   

                         case "Film festival":
                            icon= $("<i class='fa fa-film'></i>"); 
                            backgroundTitleClass = "filmTitle";
                            backgroundFieldsClass = "film";//Arancio
                            background = "#ffad33";
                            mapIconName = "film";
                            break;   

                         case "Mostre":
                            icon= $("<i class='fa fa-bank'></i>");
                            backgroundTitleClass = "mostreTitle";
                            backgroundFieldsClass = "mostre";//Turchese
                            background = "#66d9ff";
                            mapIconName = "mostre";
                            break;

                         case "Musica classica, opera e balletto":
                            icon= $("<i class='fa fa-music'></i>");
                            backgroundTitleClass = "musicaClassicaTitle";
                            backgroundFieldsClass = "musicaClassica";//Rosso scuro
                            background = "#ff6699";
                            mapIconName = "musicaClassica";
                            break;

                         case "Musica rock, jazz, pop, contemporanea":
                            icon= $("<i class='fa fa-music'></i>");
                            backgroundTitleClass = "musicaRockTitle";
                            backgroundFieldsClass = "musicaRock";//Viola
                            background = "#ff6699";
                            mapIconName = "musicaRock";
                            break;   

                        case "News":
                            icon= $("<i class='fa fa-newspaper-o'></i>");
                            backgroundFieldsClass = "news";//Ruggine
                            backgroundTitleClass = "newsTitle";
                            background = "#ff794d";
                            mapIconName = "news";
                            break;

                         case "Readings, Conferenze, Convegni":
                            icon= $("<i class='fa fa-group'></i>");
                            backgroundTitleClass = "convegniTitle";
                            backgroundFieldsClass = "convegni";//Violetto chiaro
                            background = "#ffb3ff";
                            mapIconName = "convegni";
                            break;   

                         case "Readings, incontri letterari, conferenze":
                            icon= $("<i class='fa fa-book'></i>");
                            backgroundTitleClass = "incontriLetterariTitle";
                            backgroundFieldsClass = "incontriLetterari";//Azzurro
                            background = "#80dfff";
                            mapIconName = "incontriLetterari";
                            break;   

                         case "Sport":
                            icon= $("<i class='fa fa-futbol-o'></i>");
                            backgroundTitleClass = "sportTitle";
                            backgroundFieldsClass = "sport";//Rosso chiaro
                            background = "#ff8080";
                            mapIconName = "sport";
                            break;

                         case "Teatro":
                            icon= $("<i class='fa fa-ticket'></i>");
                            backgroundTitleClass = "teatroTitle";
                            backgroundFieldsClass = "teatro";//Ocra
                            background = "#d9b38c";
                            mapIconName = "teatro";
                            break;

                         case "Tradizioni popolari":
                            icon= $("<i class='fa fa-spoon'></i>");
                            backgroundTitleClass = "tradizioniPopolariTitle";
                            backgroundFieldsClass = "tradizioniPopolari";//Grigio chiaro
                            background = "#e6e6e6";
                            mapIconName = "tradizioniPopolari";
                            break;

                         case "Walking":
                            icon= $("<i class='fa fa-male'></i>");
                            backgroundTitleClass = "walkingTitle";
                            backgroundFieldsClass = "walking";//Verde scuro
                            background = "#66cc66";
                            mapIconName = "walking";
                            break;      

                        default:
                            icon= $("<i class='fa fa-calendar-check-o'></i>");
                            backgroundTitleClass = "altriEventiTitle";
                            backgroundFieldsClass = "altriEventi";//Grigio
                            background = "#d9d9d9";
                            mapIconName = "altriEventi";
                            break;
                    }
                   
                    newRow.css("height", rowPercHeight + "%");
                    newRow.css("margin-bottom", "4px");
                    eventTitle = $('<div class="eventTitle"><p class="recreativeEventTitlePar">' + eventName.toUpperCase() + '</p></div>');
                    eventTitle.addClass(backgroundTitleClass);
                    eventTitle.css("font-size", fontSize + "px");
                    eventTitle.css("height", "60%");
                    $('#<?= $_GET['name'] ?>_rollerContainer').append(newRow);
                    
                    newRow.append(eventTitle);
                    
                    dataContainer = $('<div class="eventDataContainer"></div>');
                    newRow.append(dataContainer);
                    
                    //Categoria
                    newIcon = $("<div class='eventIcon " + backgroundFieldsClass + "' data-toggle='tooltip' data-placement='top' title='" + eventType + "'></div>"); 
                    newIcon.css("width", "33.3333333%");
                    newIcon.append(icon);
                    newIcon.find("i").css("font-size", "16px");
                    newIcon.css("display", "flex");
                    newIcon.css("align-items", "center");
                    newIcon.css("justify-content", "center");
                    newIcon.css("text-align", "center");
                    dataContainer.append(newIcon);
                    newIcon.tooltip(); 
                    
                    //Prezzo
                    newIcon = $("<div class='eventIcon " + backgroundFieldsClass + "' data-toggle='tooltip' data-placement='top'></div>"); 
                    newIcon.css("width", "33.3333333%");
                    if(freeEvent === 'NO')
                    {
                       feeIcon = $("<i class='fa fa-euro'></i>");
                       newIcon.append(feeIcon); 
                       newIcon.attr("title", price);
                    }
                    else
                    {
                       newIcon.html("free");
                       newIcon.attr("title", "This event is free");
                    }
                    
                    newIcon.find("i").css("font-size", "16px");
                    newIcon.css("display", "flex");
                    newIcon.css("align-items", "center");
                    newIcon.css("justify-content", "center");
                    newIcon.css("text-align", "center");
                    dataContainer.append(newIcon);
                    newIcon.tooltip();
                    
                    eventAddress = $('<div class="eventAddress eventIcon ' + backgroundFieldsClass + '"><span class="recreativeEventPinShowMsg">show</span><span class="recreativeEventPinHideMsg">hide</span><a class="eventLink" data-eventlat="' + eventLat + '" data-eventlng="' + eventLng + '" data-eventType="' + eventType + '" data-colorClass="' + backgroundFieldsClass + '" data-background="' + background + '" data-mapIconName="' + mapIconName + '" data-onMap="false"><i class="material-icons">navigation</i></a></div>'); 
                    eventAddress.css("width", "33.3333333%");
                    eventAddress.find("i").css("font-size", "20px");
                    eventAddress.css("display", "flex");
                    eventAddress.css("align-items", "center");
                    eventAddress.css("justify-content", "center");
                    eventAddress.css("text-align", "center");
                    dataContainer.append(eventAddress);
                    
                    /*dataContainer.addClass(backgroundFieldsClass);

                    newIcon = $("<div class='eventIcon'></div>");
                    var iconWidth = parseFloat(parseFloat(30 / eventContentW)*100); 
                    newIcon.css("width", iconWidth + "%");
                    
                    newIconUp = $("<div class='eventIconUp' data-toggle='tooltip' data-placement='top' title='" + eventType + "'></div>");

                    newIconUp.append(icon);
                    newIconDown = $("<div class='eventIconDown' data-toggle='tooltip' data-placement='top'></div>");

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
                    
                    var rightDataContainer = $('<div class="eventDataRightContainer"></div>');
                    
                    rightDataContainer.css("width", parseFloat(100 - iconWidth) + "%");
                    rightDataContainer.addClass(backgroundTitleClass);
                    
                    dateContainer = $("<div class='eventTime'><i class='fa fa-calendar' style='font-size:13px'></i>&nbsp;&nbsp;" + startDate + " to " + endDate + "</div>"); 
                    rightDataContainer.append(dateContainer);

                    eventAddress = $("<div class='eventAddress'><a class='eventLink' data-eventlat='" + eventLat + "' data-eventlng='" + eventLng + "' data-eventType='" + eventType + "' data-colorClass='" + backgroundFieldsClass + "' data-background='" + background + "' data-mapIconName='" + mapIconName + "' data-onMap='false'><i class='material-icons' style='font-size:16px'>place</i>&nbsp;" + address + "<span class='onMapSignal'> <i class='fa fa-caret-right' style='font-size:16px'></i> ON MAP</span></a></div>");
                    rightDataContainer.append(eventAddress);
                    
                    dataContainer.append(rightDataContainer);*/
                    
                    if(eventProperties.hasOwnProperty('categoryIT'))
                    {
                        if((eventProperties.categoryIT !== '')&&(eventProperties.categoryIT !== undefined)&&(eventProperties.categoryIT !== 'undefined')&&(eventProperties.categoryIT !== null)&&(eventProperties.categoryIT !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-categoryIT", eventProperties.categoryIT);
                        }
                    }
                    
                    if(eventProperties.hasOwnProperty('name'))
                    {
                        if((eventProperties.name !== '')&&(eventProperties.name !== undefined)&&(eventProperties.name !== 'undefined')&&(eventProperties.name !== null)&&(eventProperties.name !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-name", eventProperties.name);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-name", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-name", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('place'))
                    {
                        if((eventProperties.place !== '')&&(eventProperties.place !== undefined)&&(eventProperties.place !== 'undefined')&&(eventProperties.place !== null)&&(eventProperties.place !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-place", eventProperties.place);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-place", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-place", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('startDate'))
                    {
                        if((eventProperties.startDate !== '')&&(eventProperties.startDate !== undefined)&&(eventProperties.startDate !== 'undefined')&&(eventProperties.startDate !== null)&&(eventProperties.startDate !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-startDate", eventProperties.startDate);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-startDate", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-startDate", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('endDate'))
                    {
                        if((eventProperties.endDate !== '')&&(eventProperties.endDate !== undefined)&&(eventProperties.endDate !== 'undefined')&&(eventProperties.endDate !== null)&&(eventProperties.endDate !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-endDate", eventProperties.endDate);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-endDate", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-endDate", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('startTime'))
                    {
                        if((eventProperties.startTime !== '')&&(eventProperties.startTime !== undefined)&&(eventProperties.startTime !== 'undefined')&&(eventProperties.startTime !== null)&&(eventProperties.startTime !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-startTime", eventProperties.startTime);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-startTime", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-startTime", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('freeEvent'))
                    {
                        if((eventProperties.freeEvent !== '')&&(eventProperties.freeEvent !== undefined)&&(eventProperties.freeEvent !== 'undefined')&&(eventProperties.freeEvent !== null)&&(eventProperties.freeEvent !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-freeEvent", eventProperties.freeEvent);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-freeEvent", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-freeEvent", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('address'))
                    {
                        if((eventProperties.address !== '')&&(eventProperties.address !== undefined)&&(eventProperties.address !== 'undefined')&&(eventProperties.address !== null)&&(eventProperties.address !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-address", eventProperties.address);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-address", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-address", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('civic'))
                    {
                        if((eventProperties.civic !== '')&&(eventProperties.civic !== undefined)&&(eventProperties.civic !== 'undefined')&&(eventProperties.civic !== null)&&(eventProperties.civic !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-civic", eventProperties.civic);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-civic", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-civic", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('price'))
                    {
                        if((eventProperties.price !== '')&&(eventProperties.price !== undefined)&&(eventProperties.price !== 'undefined')&&(eventProperties.price !== null)&&(eventProperties.price !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-price", eventProperties.price);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-price", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-price", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('phone'))
                    {
                        if((eventProperties.phone !== '')&&(eventProperties.phone !== undefined)&&(eventProperties.phone !== 'undefined')&&(eventProperties.phone !== null)&&(eventProperties.phone !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-phone", eventProperties.phone);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-phone", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-phone", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('descriptionIT'))
                    {
                        if((eventProperties.descriptionIT !== '')&&(eventProperties.descriptionIT !== undefined)&&(eventProperties.descriptionIT !== 'undefined')&&(eventProperties.descriptionIT !== null)&&(eventProperties.descriptionIT !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-descriptionIT", eventProperties.descriptionIT);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-descriptionIT", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-descriptionIT", "undefined");
                    }
                    
                    if(eventProperties.hasOwnProperty('website'))
                    {
                        if((eventProperties.website !== '')&&(eventProperties.website !== undefined)&&(eventProperties.website !== 'undefined')&&(eventProperties.website !== null)&&(eventProperties.website !== 'null'))
                        {
                            eventAddress.find("a.eventLink").attr("data-website", eventProperties.website);
                        }
                        else
                        {
                            eventAddress.find("a.eventLink").attr("data-website", "undefined");
                        }
                    }
                    else
                    {
                        eventAddress.find("a.eventLink").attr("data-website", "undefined");
                    }

                    newRow.append(dataContainer);

                    //dateContainer.css("font-size", fontSizeSmall + "px");
                    //eventAddress.css("font-size", fontSizeSmall + "px");
                    
                    $("#<?= $_GET['name'] ?>_chartContainer").scrollTop(0);
                    
                    //Interazione cross-widget - Hover
                    eventAddress.find("a.eventLink").hover(
                        function() 
                        {
                           localEventType = $(this).attr("data-eventType");
                           localBackground = $(this).attr("data-background");

                           originalHeaderColor = {};
                           originalBorderColor = {};
                           
                           if($(this).attr("data-onMap") === "false")
                           {
                               $(this).find("i.material-icons").css("color", "white");
                           }
                           else
                           {
                               $(this).find("i.material-icons").css("color", "black");
                           }
                           
                           for(var widgetName in widgetTargetList) 
                           {
                                originalHeaderColor[widgetName] = $("#" + widgetName + "_header").css("background-color");
                                originalBorderColor[widgetName] = $("#" + widgetName).css("border-color");

                                for(var key in widgetTargetList[widgetName]) 
                                {
                                    if(widgetTargetList[widgetName][key] === localEventType)
                                    {
                                        if($(this).attr("data-eventType") === localEventType)
                                        {
                                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                                           //$(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                                        }

                                        //$(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "white");

                                        $("#" + widgetName + "_header").css("background", localBackground);
                                        $("#" + widgetName).css("border-color", localBackground);
                                    }
                                    else
                                    {
                                        //Caso in cui non ci sono target per l'evento disponibile: per ora ok così, poi raffineremo il comportamento
                                    }
                                }
                           }
                        }, 
                        function() 
                        {
                           localEventType = $(this).attr("data-eventType");
                           
                           if($(this).attr("data-onMap") === "false")
                           {
                               $(this).find("i.material-icons").css("color", "black");
                           }
                           else
                           {
                               $(this).find("i.material-icons").css("color", "white");
                           }
                           
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
                     );//Fine interazione cross widget - Hover
                    
                     //Interazione cross widget - Click
                     eventAddress.find("a.eventLink").click(function()
                     {  
                        localEventType = $(this).attr("data-eventType");
                        goesOnMap = false;

                        if($(this).attr("data-onMap") === 'false')
                        {
                           $(this).attr("data-onMap", 'true');
                           goesOnMap = true;
                           $(this).find("i.material-icons").html("near_me");
                           $(this).find("i.material-icons").css("color", "white");
                           $(this).find("i.material-icons").css("text-shadow", "black 1px 1px 2px");
                           //$(this).find("i.material-icons").addClass("onMapPinAnimated");
                           //$(this).find("span.onMapSignal").show();
                        }
                        else
                        {
                           $(this).attr("data-onMap", 'false');
                           goesOnMap = false;
                           $(this).find("i.material-icons").html("navigation");
                           $(this).find("i.material-icons").css("color", "black");
                           $(this).find("i.material-icons").css("text-shadow", "white 1px 1px 2px");
                           //$(this).find("i.material-icons").removeClass("onMapPinAnimated");
                           //$(this).find("span.onMapSignal").hide();
                        }

                       targetsArrayForNotify = [];

                       for(var widgetName in widgetTargetList) 
                       {
                          for(var key in widgetTargetList[widgetName]) 
                          {
                               if(widgetTargetList[widgetName][key] === localEventType)
                               {
                                   if(goesOnMap)
                                   {
                                      targetsArrayForNotify.push(widgetName);
                                      addEventToMap($(this), widgetName);
                                   }
                                   else
                                   {
                                      removeEventFromMap($(this), widgetName);
                                   }
                               }
                          }
                       }

                      //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
                      $.event.trigger({
                          type: "esbEventAdded",
                          generator: "<?= $_GET['name'] ?>",
                          targetsArray: targetsArrayForNotify
                      });

                    });
               }
    
            }//Fine del for 
            
            $('#<?= $_GET['name'] ?>_rollerContainer [data-toggle="tooltip"]').tooltip({
               html: true
            });
        }
        
        function updateFullscreenPointsList(widgetNameLocal, eventsPointsLocal)
        {
            var temp = null;
            $("#" + widgetNameLocal + "_driverWidgetType").val("recreativeEvents");
            $('#' + widgetNameLocal + '_modalLinkOpen input.fullscreenEventPoint').remove();
            
            for(var i = 0; i < eventsPointsLocal.length; i++)
            {  
              if($('#' + widgetNameLocal + '_fullscreenEvent_' + i).length <= 0)
              {
                temp = $('<input type="hidden" class="fullscreenEventPoint" data-eventType="recreativeEvents" id="<?= $_GET['name'] ?>_fullscreenEvent_' + i + '"/>');
                temp.val(eventsPointsLocal[i].join("||"));
                $('#' + widgetNameLocal + '_modalLinkOpen div.modalLinkOpenBody').append(temp);
              }
            }
        }
        
        function addEventToMap(eventLink, widgetName)
        {
           var minLat, minLng, maxLat, maxLng, targetName, mapdiv, markerLocation, marker, popupText, 
               pinIcon, categoryIT, name, place, startDate, endDate, startTime, freeEvent, address, civic, 
               price, phone, descriptionIT, website, colorClass, mapIconName = null;
           var lat = eventLink.attr("data-eventlat");
           var lng = eventLink.attr("data-eventlng");
           
           var coordsAndType = [];
           coordsAndType.push(lng);//OS vuole le coordinate alla rovescia
           coordsAndType.push(lat);
           coordsAndType.push(eventLink.attr("data-categoryIT"));
           coordsAndType.push(eventLink.attr("data-name"));
           coordsAndType.push(eventLink.attr("data-place"));
           coordsAndType.push(eventLink.attr("data-startDate"));
           coordsAndType.push(eventLink.attr("data-endDate"));
           coordsAndType.push(eventLink.attr("data-startTime"));
           coordsAndType.push(eventLink.attr("data-freeEvent"));
           coordsAndType.push(eventLink.attr("data-address"));
           coordsAndType.push(eventLink.attr("data-civic"));
           coordsAndType.push(eventLink.attr("data-price"));
           coordsAndType.push(eventLink.attr("data-phone"));
           coordsAndType.push(eventLink.attr("data-descriptionIT"));
           coordsAndType.push(eventLink.attr("data-website"));
           coordsAndType.push(eventLink.attr("data-colorClass"));
           coordsAndType.push(eventLink.attr("data-mapIconName"));
           
           if(eventsOnMaps[widgetName]["noPointsUrl"] === null) 
           {
               targetName = widgetName + "_div";
               eventsOnMaps[widgetName]["noPointsUrl"] = $("#" + targetName).attr("data-nopointsurl");
            }
           
           eventsOnMaps[widgetName].eventsPoints.push(coordsAndType);
           eventsOnMaps[widgetName].eventsNumber++;
           
           updateFullscreenPointsList(widgetName, eventsOnMaps[widgetName].eventsPoints);
           
            //Leaflet
            $("#" + widgetName + "_wrapper").hide();
            $("#" + widgetName + "_defaultMapDiv").hide();
            mapdiv = widgetName + "_mapDiv";
            
            //Creazione della mappa, con distruzione preventiva
            if(eventsOnMaps[widgetName].mapRef !== null)
            {
               eventsOnMaps[widgetName].mapRef.off();
               eventsOnMaps[widgetName].mapRef.remove();
            }

            $("#" + widgetName + "_mapDiv").remove();
            $("#" + widgetName + "_content").append('<div id="' + widgetName + '_mapDiv" class="mapDiv"></div>');
            $("#" + widgetName + "_mapDiv").show();

            eventsOnMaps[widgetName].mapRef = L.map(mapdiv).setView([lat, lng], 17);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
               maxZoom: 18
            }).addTo(eventsOnMaps[widgetName].mapRef);
            eventsOnMaps[widgetName].mapRef.attributionControl.setPrefix('');
            
            minLat = +90;
            minLng = +180;
            maxLat = -90;
            maxLng = -180;
            
            for(var i = 0; i < eventsOnMaps[widgetName].eventsNumber; i++)
            {
               lat = eventsOnMaps[widgetName].eventsPoints[i][1];
               lng = eventsOnMaps[widgetName].eventsPoints[i][0];
               categoryIT = eventsOnMaps[widgetName].eventsPoints[i][2];
               name = eventsOnMaps[widgetName].eventsPoints[i][3];
               
               if(name.includes('?'))
               {
                  name = name.replace(/\?/g, "'");
               }
               
               place = eventsOnMaps[widgetName].eventsPoints[i][4];
               
               if(place.includes('?'))
               {
                  place = place.replace(/\?/g, "'");
               }	
               
               startDate = eventsOnMaps[widgetName].eventsPoints[i][5];
               endDate = eventsOnMaps[widgetName].eventsPoints[i][6];
               startTime = eventsOnMaps[widgetName].eventsPoints[i][7];    
               freeEvent = eventsOnMaps[widgetName].eventsPoints[i][8];
               address = eventsOnMaps[widgetName].eventsPoints[i][9];
               
               if(address.includes('?'))
               {
                  address = address.replace(/\?/g, "'");
               }
               
               civic = eventsOnMaps[widgetName].eventsPoints[i][10];
               price = eventsOnMaps[widgetName].eventsPoints[i][11];
               phone = eventsOnMaps[widgetName].eventsPoints[i][12];
               descriptionIT = eventsOnMaps[widgetName].eventsPoints[i][13];
               
               if(descriptionIT.includes('?'))
               {
                  descriptionIT = descriptionIT.replace(/\?/g, "'");
               }
               
               website = eventsOnMaps[widgetName].eventsPoints[i][14];
               colorClass = eventsOnMaps[widgetName].eventsPoints[i][15];
               mapIconName = eventsOnMaps[widgetName].eventsPoints[i][16];
               
               mapPinImg = '../img/eventsIcons/' + mapIconName + '.png';

               pinIcon = new L.DivIcon({
                   className: null,
                   html: '<img src="' + mapPinImg + '" class="leafletPin" />'
               });

               markerLocation = new L.LatLng(lat, lng);
               marker = new L.Marker(markerLocation, {icon: pinIcon});
               eventsOnMaps[widgetName].eventsPoints[i][17] = marker;
               popupText = '<h3 class="' + colorClass + ' recreativeEventMapTitle">' + name + '</h3>';
               popupText += '<div class="recreativeEventMapBtnContainer"><button class="recreativeEventMapDetailsBtn recreativeEventMapBtn ' + colorClass + ' recreativeEventMapBtnActive" type="button">Details</button><button class="recreativeEventMapDescriptionBtn recreativeEventMapBtn ' + colorClass + '" type="button">Description</button><button class="recreativeEventMapTimingBtn recreativeEventMapBtn ' + colorClass + '" type="button">Timing</button><button class="recreativeEventMapContactsBtn recreativeEventMapBtn ' + colorClass + '" type="button">Contacts</button></div>';
               
               popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer">';
               if((place !== 'undefined')||(address !== 'undefined'))
               {
                    if(categoryIT !== 'undefined')
                    {
                        popupText += '<b>Category: </b>' + categoryIT;
                    }
                   
                   if(place !== 'undefined')
                   {
                       popupText += '<br/>';
                       popupText += '<b>Location: </b>' + place;
                   }
                   
                   if(address !== 'undefined')
                   {
                       popupText += '<br/>';
                       popupText += '<b>Address: </b>' + address;
                       if(civic !== 'undefined')
                       {
                           popupText += ' ' + civic;
                       }
                   }
                   
                    if(freeEvent !== 'undefined')
                    {
                        popupText += '<br/>';
                        if((freeEvent !== 'yes')&&(freeEvent !== 'YES')&&(freeEvent !== 'Yes'))
                        {
                            if(price !== 'undefined')
                            {
                                popupText += '<b>Price (€) : </b>' + price + "<br>";
                            }
                            else
                            {
                                popupText += '<b>Price (€) : </b>N/A<br>';
                            }
                        }
                        else
                        {
                            popupText += '<b>Free event: </b>' + freeEvent + '<br>';
                        }
                    }
               }
               else
               {
                   popupText += 'No further details available';
               }
               
               popupText += '</div>';
               
               popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';
               if(descriptionIT !== 'undefined')
               {
                   popupText += descriptionIT;
               }
               else
               {
                   popupText += 'No description available';
               }
               popupText += '</div>';
               
               popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapTimingContainer">';
               if((startDate !== 'undefined')||(endDate !== 'undefined')||(startTime !== 'undefined'))
               {
                   popupText += '<b>From: </b>';
                   if(startDate !== 'undefined')
                   {
                       popupText += startDate;
                   }
                   else
                   {
                       popupText += 'N/A';
                   }
                   popupText += '<br/>';
                   
                   popupText += '<b>To: </b>';
                   if(endDate !== 'undefined')
                   {
                       popupText += endDate;
                   }
                   else
                   {
                       popupText += 'N/A';
                   }
                   popupText += '<br/>';
                   
                   if(startTime !== 'undefined')
                   {
                       popupText += '<b>Times: </b>' + startTime + '<br/>';
                   }
                   else
                   {
                       popupText += '<b>Times: </b>N/A<br/>';
                   }
                   
               }
               else
               {
                   popupText += 'No timings info available';
               }
               popupText += '</div>';
                
               popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';
               if((phone !== 'undefined')||(website !== 'undefined'))
               {
                    if(phone !== 'undefined')
                    {
                        popupText += '<b>Phone: </b>' + phone + '<br/>';
                    }
                    else
                    {
                        popupText += '<b>Phone: </b>N/A<br/>';
                    }
                    
                    if(website !== 'undefined')
                    {
                        if(website.includes('http')||website.includes('https'))
                        {
                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                        }
                        else
                        {
                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                        }
                    }
                    else
                    {
                        popupText += '<b>Website: </b>N/A';
                    }
               }
               else
               {
                   popupText += 'No contacts info available';
               }
               popupText += '</div>'; 

               eventsOnMaps[widgetName].mapRef.addLayer(marker);
               lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth : 300});
               
               lastPopup.on('popupopen', function(){
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapDetailsBtn').off('click');
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapDetailsBtn').click(function(){
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDataContainer').hide();
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDetailsContainer').show();
                        $('#' + widgetName + '_mapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                        $(this).addClass('recreativeEventMapBtnActive');
                    });

                    $('#' + widgetName + '_mapDiv button.recreativeEventMapDescriptionBtn').off('click');
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapDescriptionBtn').click(function(){
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDataContainer').hide();
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDescContainer').show();
                        $('#' + widgetName + '_mapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                        $(this).addClass('recreativeEventMapBtnActive');
                    });

                    $('#' + widgetName + '_mapDiv button.recreativeEventMapTimingBtn').off('click');
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapTimingBtn').click(function(){
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDataContainer').hide();
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapTimingContainer').show();
                        $('#' + widgetName + '_mapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                        $(this).addClass('recreativeEventMapBtnActive');
                    });

                    $('#' + widgetName + '_mapDiv button.recreativeEventMapContactsBtn').off('click');
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapContactsBtn').click(function(){
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDataContainer').hide();
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapContactsContainer').show();
                        $('#' + widgetName + '_mapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                        $(this).addClass('recreativeEventMapBtnActive');
                    }); 
                });
                
                lastPopup.openPopup();
               
               //Calcolo del rettangolo di visualizzazione
               if(eventsOnMaps[widgetName].eventsPoints[i][0] < minLng)
               {
                  minLng = eventsOnMaps[widgetName].eventsPoints[i][0];
               }

               if(eventsOnMaps[widgetName].eventsPoints[i][0] > maxLng)
               {
                  maxLng = eventsOnMaps[widgetName].eventsPoints[i][0];
               }

               if(eventsOnMaps[widgetName].eventsPoints[i][1] < minLat)
               {
                  minLat = eventsOnMaps[widgetName].eventsPoints[i][1];
               }

               if(eventsOnMaps[widgetName].eventsPoints[i][1] > maxLat)
               {
                  maxLat = eventsOnMaps[widgetName].eventsPoints[i][1];
               }
            }
            
            if(eventsOnMaps[widgetName].eventsNumber > 1)
            {
               eventsOnMaps[widgetName].mapRef.fitBounds([
                  [minLat, minLng],
                  [maxLat, maxLng]
               ]);
            }
        }
        
        function removeEventFromMap(eventLink, widgetName)
        {
           var minLat, minLng, maxLat, maxLng, targetName, mapdiv, markerLocation, marker, popupText, 
               pinIcon, categoryIT, name, place, startDate, endDate, startTime, freeEvent, address, civic, 
               price, phone, descriptionIT, website, colorClass, mapIconName = null;
           var lat = eventLink.attr("data-eventlat");
           var lng = eventLink.attr("data-eventlng");
           
           for(var j = 0; j < eventsOnMaps[widgetName].eventsPoints.length; j++)
           {
              if((eventsOnMaps[widgetName].eventsPoints[j][0] === lng)&&(eventsOnMaps[widgetName].eventsPoints[j][1] === lat))
              {
                 index = j;
                 break;
              }
           }
           
           eventsOnMaps[widgetName].eventsPoints.splice(index, 1);
           eventsOnMaps[widgetName].eventsNumber--;
           
           $("#" + widgetName + "_wrapper").hide();
           updateFullscreenPointsList(widgetName, eventsOnMaps[widgetName].eventsPoints);
           
           if(lastPopup !== null)
           {
              lastPopup.closePopup();
           }
           
           //Leaflet
            $("#" + widgetName + "_wrapper").hide();
            mapdiv = widgetName + "_mapDiv";
            
            //Creazione della mappa
            if(eventsOnMaps[widgetName].mapRef !== null)
            {
               eventsOnMaps[widgetName].mapRef.off();
               eventsOnMaps[widgetName].mapRef.remove();
            }

            $("#" + widgetName + "_mapDiv").remove();
            $("#" + widgetName + "_content").append('<div id="' + widgetName + '_mapDiv" class="mapDiv"></div>');
            $("#" + widgetName + "_mapDiv").show();

            eventsOnMaps[widgetName].mapRef = L.map(mapdiv).setView([43.769805, 11.256064], 17);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
               maxZoom: 18
            }).addTo(eventsOnMaps[widgetName].mapRef);
            eventsOnMaps[widgetName].mapRef.attributionControl.setPrefix('');
            
            minLat = +90;
            minLng = +180;
            maxLat = -90;
            maxLng = -180;
            
            for(var i = 0; i < eventsOnMaps[widgetName].eventsNumber; i++)
            {
               lat = eventsOnMaps[widgetName].eventsPoints[i][1];
               lng = eventsOnMaps[widgetName].eventsPoints[i][0];
               categoryIT = eventsOnMaps[widgetName].eventsPoints[i][2];
               name = eventsOnMaps[widgetName].eventsPoints[i][3];
               
               if(name.includes('?'))
               {
                  name = name.replace(/\?/g, "'");
               }
               
               place = eventsOnMaps[widgetName].eventsPoints[i][4];
               
               if(place.includes('?'))
               {
                  place = place.replace(/\?/g, "'");
               }
               
               startDate = eventsOnMaps[widgetName].eventsPoints[i][5];
               endDate = eventsOnMaps[widgetName].eventsPoints[i][6];
               startTime = eventsOnMaps[widgetName].eventsPoints[i][7];    
               freeEvent = eventsOnMaps[widgetName].eventsPoints[i][8];
               address = eventsOnMaps[widgetName].eventsPoints[i][9];
               
               if(address.includes('?'))
               {
                  address = address.replace(/\?/g, "'");
               }
               
               civic = eventsOnMaps[widgetName].eventsPoints[i][10];
               price = eventsOnMaps[widgetName].eventsPoints[i][11];
               phone = eventsOnMaps[widgetName].eventsPoints[i][12];
               descriptionIT = eventsOnMaps[widgetName].eventsPoints[i][13];
               
               if(descriptionIT.includes('?'))
               {
                  descriptionIT = descriptionIT.replace(/\?/g, "'");
               }
               
               website = eventsOnMaps[widgetName].eventsPoints[i][14];
               colorClass = eventsOnMaps[widgetName].eventsPoints[i][15];
               mapIconName = eventsOnMaps[widgetName].eventsPoints[i][16];

               mapPinImg = '../img/eventsIcons/' + mapIconName + '.png';

               pinIcon = new L.DivIcon({
                   className: null,
                   html: '<img src="' + mapPinImg + '" class="leafletPin" />'
               });

               markerLocation = new L.LatLng(lat, lng);
               marker = new L.Marker(markerLocation, {icon: pinIcon});
               eventsOnMaps[widgetName].eventsPoints[i][17] = marker;
               popupText = '<h3 class="' + colorClass + ' recreativeEventMapTitle">' + name + '</h3>';
               popupText += '<div class="recreativeEventMapBtnContainer"><button class="recreativeEventMapDetailsBtn recreativeEventMapBtn ' + colorClass + ' recreativeEventMapBtnActive" type="button">Details</button><button class="recreativeEventMapDescriptionBtn recreativeEventMapBtn ' + colorClass + '" type="button">Description</button><button class="recreativeEventMapTimingBtn recreativeEventMapBtn ' + colorClass + '" type="button">Timing</button><button class="recreativeEventMapContactsBtn recreativeEventMapBtn ' + colorClass + '" type="button">Contacts</button></div>';
               
               popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer">';
               if((place !== 'undefined')||(address !== 'undefined'))
               {
                    if(categoryIT !== 'undefined')
                    {
                        popupText += '<b>Category: </b>' + categoryIT;
                    }
                   
                   if(place !== 'undefined')
                   {
                       popupText += '<br/>';
                       popupText += '<b>Location: </b>' + place;
                   }
                   
                   if(address !== 'undefined')
                   {
                       popupText += '<br/>';
                       popupText += '<b>Address: </b>' + address;
                       if(civic !== 'undefined')
                       {
                           popupText += ' ' + civic;
                       }
                   }
                   
                    if(freeEvent !== 'undefined')
                    {
                        popupText += '<br/>';
                        if((freeEvent !== 'yes')&&(freeEvent !== 'YES')&&(freeEvent !== 'Yes'))
                        {
                            if(price !== 'undefined')
                            {
                                popupText += '<b>Price (€) : </b>' + price + "<br>";
                            }
                            else
                            {
                                popupText += '<b>Price (€) : </b>N/A<br>';
                            }
                        }
                        else
                        {
                            popupText += '<b>Free event: </b>' + freeEvent + '<br>';
                        }
                    }
               }
               else
               {
                   popupText += 'No further details available';
               }
               
               popupText += '</div>';
               
               popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';
               if(descriptionIT !== 'undefined')
               {
                   popupText += descriptionIT;
               }
               else
               {
                   popupText += 'No description available';
               }
               popupText += '</div>';
               
               popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapTimingContainer">';
               if((startDate !== 'undefined')||(endDate !== 'undefined')||(startTime !== 'undefined'))
               {
                   popupText += '<b>From: </b>';
                   if(startDate !== 'undefined')
                   {
                       popupText += startDate;
                   }
                   else
                   {
                       popupText += 'N/A';
                   }
                   popupText += '<br/>';
                   
                   popupText += '<b>To: </b>';
                   if(endDate !== 'undefined')
                   {
                       popupText += endDate;
                   }
                   else
                   {
                       popupText += 'N/A';
                   }
                   popupText += '<br/>';
                   
                   if(startTime !== 'undefined')
                   {
                       popupText += '<b>Times: </b>' + startTime + '<br/>';
                   }
                   else
                   {
                       popupText += '<b>Times: </b>N/A<br/>';
                   }
                   
               }
               else
               {
                   popupText += 'No timings info available';
               }
               popupText += '</div>';
                
               popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';
               if((phone !== 'undefined')||(website !== 'undefined'))
               {
                    if(phone !== 'undefined')
                    {
                        popupText += '<b>Phone: </b>' + phone + '<br/>';
                    }
                    else
                    {
                        popupText += '<b>Phone: </b>N/A<br/>';
                    }
                    
                    if(website !== 'undefined')
                    {
                        if(website.includes('http')||website.includes('https'))
                        {
                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                        }
                        else
                        {
                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                        }
                    }
                    else
                    {
                        popupText += '<b>Website: </b>N/A';
                    }
               }
               else
               {
                   popupText += 'No contacts info available';
               }
               popupText += '</div>'; 

               eventsOnMaps[widgetName].mapRef.addLayer(marker);
               lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth : 300});
               
               lastPopup.on('popupopen', function(){
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapDetailsBtn').off('click');
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapDetailsBtn').click(function(){
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDataContainer').hide();
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDetailsContainer').show();
                        $('#' + widgetName + '_mapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                        $(this).addClass('recreativeEventMapBtnActive');
                    });

                    $('#' + widgetName + '_mapDiv button.recreativeEventMapDescriptionBtn').off('click');
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapDescriptionBtn').click(function(){
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDataContainer').hide();
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDescContainer').show();
                        $('#' + widgetName + '_mapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                        $(this).addClass('recreativeEventMapBtnActive');
                    });

                    $('#' + widgetName + '_mapDiv button.recreativeEventMapTimingBtn').off('click');
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapTimingBtn').click(function(){
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDataContainer').hide();
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapTimingContainer').show();
                        $('#' + widgetName + '_mapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                        $(this).addClass('recreativeEventMapBtnActive');
                    });

                    $('#' + widgetName + '_mapDiv button.recreativeEventMapContactsBtn').off('click');
                    $('#' + widgetName + '_mapDiv button.recreativeEventMapContactsBtn').click(function(){
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapDataContainer').hide();
                        $('#' + widgetName + '_mapDiv div.recreativeEventMapContactsContainer').show();
                        $('#' + widgetName + '_mapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                        $(this).addClass('recreativeEventMapBtnActive');
                    }); 
                });
                
                //lastPopup.openPopup();
               
               //Calcolo del rettangolo di visualizzazione
               if(eventsOnMaps[widgetName].eventsPoints[i][0] < minLng)
               {
                  minLng = eventsOnMaps[widgetName].eventsPoints[i][0];
               }

               if(eventsOnMaps[widgetName].eventsPoints[i][0] > maxLng)
               {
                  maxLng = eventsOnMaps[widgetName].eventsPoints[i][0];
               }

               if(eventsOnMaps[widgetName].eventsPoints[i][1] < minLat)
               {
                  minLat = eventsOnMaps[widgetName].eventsPoints[i][1];
               }

               if(eventsOnMaps[widgetName].eventsPoints[i][1] > maxLat)
               {
                  maxLat = eventsOnMaps[widgetName].eventsPoints[i][1];
               }
            }
            
            if(eventsOnMaps[widgetName].eventsNumber > 1)
            {
               eventsOnMaps[widgetName].mapRef.fitBounds([
                  [minLat, minLng],
                  [maxLat, maxLng]
               ]);
            }
            else
            {
              if(eventsOnMaps[widgetName].eventsNumber === 1)
              {
                  eventsOnMaps[widgetName].mapRef.setView([eventsOnMaps[widgetName].eventsPoints[0][1], eventsOnMaps[widgetName].eventsPoints[0][0]], 17);
              }
              else
              {
                 $("#" + widgetName + "_driverWidgetType").val("");
                 $("#" + widgetName + "_netAnalysisServiceMapUrl").val("");
                 $("#" + widgetName + "_buttonUrl").val("");
                 $("#" + widgetName + "_recreativeEventsUrl").val("");
                 $("#" + widgetName + "_wrapper").hide(); 
                 $("#" + widgetName + "_mapDiv").hide();
                 $("#" + widgetName + "_defaultMapDiv").show();
              }
            }
        }
        
        /*function removeAllEventsFromMaps()
        {
           for(var widgetName in eventsOnMaps)
           {
              if(eventsOnMaps[widgetName].mapRef !== null)
              {
                eventsOnMaps[widgetName].mapRef.off();
                eventsOnMaps[widgetName].mapRef.remove();
                eventsOnMaps[widgetName].mapRef = null;
                $("#" + widgetName + "_mapDiv").remove();
                $('<div id="' + widgetName + '_mapDiv" class="mapDiv"></div>').insertBefore("#" + widgetName + "_wrapper");
                 
                $("#" + widgetName + "_driverWidgetType").val("");
                $("#" + widgetName + "_netAnalysisServiceMapUrl").val("");
                $("#" + widgetName + "_buttonUrl").val("");
                $("#" + widgetName + "_recreativeEventsUrl").val(""); 
                $("#" + widgetName + "_wrapper").hide();
                $("#" + widgetName + "_mapDiv").hide();
                $("#" + widgetName + "_defaultMapDiv").show();
              }
              
              for(var index in eventsOnMaps[widgetName].eventsPoints)
              {
                  eventsOnMaps[widgetName].eventsPoints[index][8] = null;
              }
               
              eventsOnMaps[widgetName].eventsPoints.splice(0);
              eventsOnMaps[widgetName].eventsNumber = 0; 
              
              updateFullscreenPointsList(widgetName, eventsOnMaps[widgetName].eventsPoints);
           }
        }*/
        
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
        
        function stepDownInterval()
        {
            var oldPos = $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop();
            var newPos = oldPos + 1;
            
            var oldScrollTop = $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop();
            $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop(newPos);
            var newScrollTop = $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop();
            
            if(oldScrollTop === newScrollTop)
            {
               $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop(0);
            }
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight);
        $("#<?= $_GET['name'] ?>_buttonsContainer").css("background-color", $("#<?= $_GET['name'] ?>_header").css("background-color"));
        
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
        //widgetProperties = getWidgetProperties(widgetName);
        
        $.ajax({
            url: getParametersWidgetUrl,
            type: "GET",
            data: {"nomeWidget": [widgetName]},
            async: true,
            dataType: 'json',
            success: function(data) 
            {
                widgetProperties = data;
                if((widgetProperties !== null) && (widgetProperties !== undefined))
                {
                    //Inizio eventuale codice ad hoc basato sulle proprietà del widget
                    styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
                    //Fine eventuale codice ad hoc basato sulle proprietà del widget
                    manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));

                    widgetTargetList = JSON.parse(widgetProperties.param.parameters);
                    var targetName = null;
                    widgetMode = widgetProperties.param.viewMode;

                    for(var name in widgetTargetList) 
                    {
                       targetName = name + "_div";
                       eventsOnMaps[name] = {
                          noPointsUrl: null,
                          eventsNumber: 0,
                          eventsPoints: [],//Array indicizzato con le coordinate dei punti mostrati
                          mapRef: null
                       };
                    }

                    $.ajax({
                       //url: "../widgets/curlProxy.php?url=<?=$serviceMapUrlPrefix?>api/v1/events/?range=month",
                       url: "<?=$serviceMapUrlPrefix?>api/v1/nextPOS/?categories=Event&range=month",
                       type: "GET",
                       async: true,
                       dataType: 'json',
                       success: function(data) 
                       {
                          if(firstLoad !== false)
                          {
                              showWidgetContent(widgetName);
                          }
                          else
                          {
                              elToEmpty.empty();
                          }

                          //eventsNumber = data.contents.Event.features.length;
                          eventsNumber = data.features.length;
                          widgetWidth = $('#<?= $_GET['name'] ?>_div').width();
                          var timeToClearScroll = (timeToReload - 0.5) * 1000;
                          
                          switch(widgetMode)
                          {
                              case "list":
                                    if(eventsNumber === 0)
                                    {
                                        $('#<?= $_GET['name'] ?>_buttonsContainer').hide();
                                        //$("#<?= $_GET['name'] ?>_searchFilterContainer").hide();
                                        //$("#<?= $_GET['name'] ?>_rollerContainer").hide(); 
                                        $("#<?= $_GET['name'] ?>_noDataAlert").show();
                                    }
                                    else
                                    {
                                      $("#<?= $_GET['name'] ?>_noDataAlert").hide();
                                      $('#<?= $_GET['name'] ?>_buttonsContainer').show();
                                      $("#<?= $_GET['name'] ?>_rollerContainer").show(); 
                                      $("#<?= $_GET['name'] ?>_rollerContainer").height($("#<?= $_GET['name'] ?>_mainContainer").height()); 

                                      shownHeight = $("#<?= $_GET['name'] ?>_rollerContainer").prop("offsetHeight");
                                      rowPercHeight =  60 * 100 / shownHeight;
                                      contentHeightPx = eventsNumber * 100;
                                      eventContentWPerc = null;

                                      if(contentHeightPx > shownHeight)
                                      {
                                          eventContentW = parseInt(widgetWidth - 20);
                                      }
                                      else
                                      {
                                          eventContentW = parseInt(widgetWidth);
                                      }

                                      eventContentWPerc = Math.floor(eventContentW / widgetWidth * 100);

                                      //Inserimento una tantum degli eventi nell'apposito array
                                      //eventsArray = data.contents.Event.features;
                                      eventsArray = data.features;

                                      populateWidget();

                                       scroller = setInterval(stepDownInterval, speed);
                                       
                                       setTimeout(function()
                                       {
                                           clearInterval(scroller);
                                           $("#<?= $_GET['name'] ?>_rollerContainer").off();

                                           //$(document).off("esbEventAdded");

                                           //Ripristino delle homepage native per gli widget targets al reload, se pilotati per ultimi da questo widget
                                           for(var widgetName in widgetTargetList) 
                                           {
                                              if(($("#" + widgetName + "_driverWidgetType").val() === 'recreativeEvents')&&(eventsOnMaps[widgetName].eventsNumber > 0))
                                              {
                                                  loadDefaultMap(widgetName);
                                              }
                                           }
                                       }, timeToClearScroll);

                                       $("#<?= $_GET['name'] ?>_rollerContainer").mouseenter(function() 
                                       {
                                          clearInterval(scroller);
                                       });

                                       $("#<?= $_GET['name'] ?>_rollerContainer").mouseleave(function()
                                       {    
                                           scroller = setInterval(stepDownInterval, speed);
                                       });
                                   }
                                  break;
                                  
                              case "searchAndList":
                                  $("#<?= $_GET['name'] ?>_searchFilterContainer").show();
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterLang').selectpicker({ 
                                     actionsBox: false, 
                                     noneSelectedText: "Lang",
                                     width: "100%",
                                     selectedTextFormat: "values",
                                     title: "Lang"
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterLang').off('loaded.bs.select');
                                  $('#<?= $_GET['name'] ?>_searchFilterLang').on('loaded.bs.select', function (e) 
                                  {
                                      $(this).parent().find('button.btn').css("border-radius", "0px");
                                      $(this).parent().find('button.btn').css("padding", "0px");
                                      $(this).parent().find('button.btn').css("font-size", fontSize + "px");
                                      $(this).parent().find('button.btn').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).parent().find('span.text').css("font-size", fontSize + "px");
                                      $(this).parent().find('span.text').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterType').off('loaded.bs.select');
                                  $('#<?= $_GET['name'] ?>_searchFilterType').selectpicker({
                                     actionsBox: false, 
                                     noneSelectedText: "Types",
                                     selectAllText: "All",
                                     deselectAllText: "None",
                                     width: "100%",
                                     selectedTextFormat: "static",
                                     title: "Type"
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterType').off('loaded.bs.select');
                                  $('#<?= $_GET['name'] ?>_searchFilterType').on('loaded.bs.select', function (e) 
                                  {
                                      $(this).parent().find('button.btn').css("border-radius", "0px");
                                      $(this).parent().find('button.btn').css("padding", "0px");
                                      $(this).parent().find('button.btn').css("font-size", fontSize + "px");
                                      $(this).parent().find('button.btn').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).parent().find('span.text').css("font-size", fontSize + "px");
                                      $(this).parent().find('span.text').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterCost').selectpicker({
                                     actionsBox: false, 
                                     noneSelectedText: "Cost",
                                     width: "100%",
                                     selectedTextFormat: "values",
                                     title: "Cost"
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterCost').off('loaded.bs.select');
                                  $('#<?= $_GET['name'] ?>_searchFilterCost').on('loaded.bs.select', function (e) 
                                  {
                                      $(this).parent().find('button.btn').css("border-radius", "0px");
                                      $(this).parent().find('button.btn').css("padding", "0px");
                                      $(this).parent().find('button.btn').css("font-size", fontSize + "px");
                                      $(this).parent().find('button.btn').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).parent().find('span.text').css("font-size", fontSize + "px");
                                      $(this).parent().find('span.text').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterStart').datetimepicker({
                                     format: 'YYYY-MM-DD',
                                     widgetPositioning: {
                                        horizontal: 'auto',
                                        vertical: 'auto'
                                     },
                                     defaultDate: new Date(),
                                     debug: true
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterStart').find('input[type=text]').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                  $('#<?= $_GET['name'] ?>_searchFilterStart').find('input[type=text]').css('font-size', fontSize + "px");
                                  $('#<?= $_GET['name'] ?>_searchFilterStart').find('input[type=text]').css('height', "auto");
                                  $('#<?= $_GET['name'] ?>_searchFilterStart').find('input[type=text]').css('padding', "0px");
                                  //input-group-addon
                                  
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterStart').off('dp.show');
                                  $('#<?= $_GET['name'] ?>_searchFilterStart').on('dp.show', function(){
                                      $(this).find('div.dropdown-menu').css('font-size', fontSize + "px");
                                      $(this).find('div.dropdown-menu').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).find('input[type=text]').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).find('input[type=text]').css('font-size', fontSize + "px");
                                      $(this).find('input[type=text]').css('height', "auto");
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterStart').off('dp.change');
                                  $('#<?= $_GET['name'] ?>_searchFilterStart').on('dp.change', function(){
                                      $(this).find('div.dropdown-menu').css('font-size', fontSize + "px");
                                      $(this).find('div.dropdown-menu').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).find('input[type=text]').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).find('input[type=text]').css('font-size', fontSize + "px");
                                      $(this).find('input[type=text]').css('height', "auto");
                                  });
                                  
                                  var now = new Date();
                                  now.setDate(parseInt(now.getDate() + 7));
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterStop').datetimepicker({
                                     format: 'YYYY-MM-DD',
                                     widgetPositioning: {
                                        horizontal: 'right',
                                        vertical: 'auto'
                                     },
                                     defaultDate: now,
                                     debug: true
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterStop').find('input[type=text]').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                  $('#<?= $_GET['name'] ?>_searchFilterStop').find('input[type=text]').css('font-size', fontSize + "px");
                                  $('#<?= $_GET['name'] ?>_searchFilterStop').find('input[type=text]').css('height', "auto");
                                  $('#<?= $_GET['name'] ?>_searchFilterStop').find('input[type=text]').css('padding', "0px");
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterStop').off('dp.show');
                                  $('#<?= $_GET['name'] ?>_searchFilterStop').on('dp.show', function(){
                                      $(this).find('div.dropdown-menu').css('font-size', fontSize + "px");
                                      $(this).find('div.dropdown-menu').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).find('input[type=text]').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).find('input[type=text]').css('font-size', fontSize + "px");
                                      $(this).find('input[type=text]').css('height', "auto");
                                  });
                                  
                                  $('#<?= $_GET['name'] ?>_searchFilterStop').off('dp.change');
                                  $('#<?= $_GET['name'] ?>_searchFilterStop').on('dp.change', function(){
                                      $(this).find('div.dropdown-menu').css('font-size', fontSize + "px");
                                      $(this).find('div.dropdown-menu').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).find('input[type=text]').css("font-family", '"Helvetica Neue", Helvetica, Arial, sans-serif');
                                      $(this).find('input[type=text]').css('font-size', fontSize + "px");
                                      $(this).find('input[type=text]').css('height', "auto");
                                  });
                                  
                                  /*if(eventsNumber === 0)
                                  {
                                    $('#<?= $_GET['name'] ?>_buttonsContainer').hide();
                                    $("#<?= $_GET['name'] ?>_searchFilterContainer").hide();
                                    $("#<?= $_GET['name'] ?>_rollerContainer").hide(); 
                                    $("#<?= $_GET['name'] ?>_noDataAlert").show();
                                  }
                                  else
                                  {

                                  }*/
                                    /*setTimeout(function()
                                    {
                                        clearInterval(scroller);
                                        $("#<?= $_GET['name'] ?>_rollerContainer").off();

                                        //$(document).off("esbEventAdded");

                                        //Ripristino delle homepage native per gli widget targets al reload, se pilotati per ultimi da questo widget
                                        for(var widgetName in widgetTargetList) 
                                        {
                                           if(($("#" + widgetName + "_driverWidgetType").val() === 'recreativeEvents')&&(eventsOnMaps[widgetName].eventsNumber > 0))
                                           {
                                               loadDefaultMap(widgetName);
                                           }
                                        }
                                    }, timeToClearScroll);*/
                                  break;    
                                  
                              case "searchAndMap":
                                  
                                /*setTimeout(function()
                                {
                                    clearInterval(scroller);
                                    $("#<?= $_GET['name'] ?>_rollerContainer").off();

                                    //$(document).off("esbEventAdded");

                                    //Ripristino delle homepage native per gli widget targets al reload, se pilotati per ultimi da questo widget
                                    for(var widgetName in widgetTargetList) 
                                    {
                                       if(($("#" + widgetName + "_driverWidgetType").val() === 'recreativeEvents')&&(eventsOnMaps[widgetName].eventsNumber > 0))
                                       {
                                           loadDefaultMap(widgetName);
                                       }
                                    }
                                }, timeToClearScroll);*/
                                  break;
                                  
                              case "searchAndListAndMap":
                                  /*setTimeout(function()
                                {
                                    clearInterval(scroller);
                                    $("#<?= $_GET['name'] ?>_rollerContainer").off();

                                    //$(document).off("esbEventAdded");

                                    //Ripristino delle homepage native per gli widget targets al reload, se pilotati per ultimi da questo widget
                                    for(var widgetName in widgetTargetList) 
                                    {
                                       if(($("#" + widgetName + "_driverWidgetType").val() === 'recreativeEvents')&&(eventsOnMaps[widgetName].eventsNumber > 0))
                                       {
                                           loadDefaultMap(widgetName);
                                       }
                                    }
                                }, timeToClearScroll);*/
                                  break;    
                          }
                       },
                       error: function (data)
                       {
                          console.log("Ko");
                          console.log(JSON.stringify(data));

                          showWidgetContent(widgetName);
                          $('#<?= $_GET['name'] ?>_buttonsContainer').hide();
                          $("#<?= $_GET['name'] ?>_rollerContainer").hide(); 
                          $("#<?= $_GET['name'] ?>_noDataAlert").show();
                       }
                    });
                }
                else
                {
                    console.log("Errore in caricamento proprietà widget");
                    $('#<?= $_GET['name'] ?>_buttonsContainer').hide();
                    $("#<?= $_GET['name'] ?>_rollerContainer").hide(); 
                    $("#<?= $_GET['name'] ?>_noDataAlert").show();
                }
                
            },
            error: function()
            {
                console.log("Errore in caricamento proprietà widget");
                $('#<?= $_GET['name'] ?>_buttonsContainer').hide();
                $("#<?= $_GET['name'] ?>_rollerContainer").hide(); 
                $("#<?= $_GET['name'] ?>_noDataAlert").show();
            },
            complete: function()
            {
                countdownRef = startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, null);
            }
        });
    });//Fine document ready
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
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
            <div id="<?= $_GET['name'] ?>_mainContainer" class="chartContainer">
               <div id="<?= $_GET['name'] ?>_noDataAlert" class="noDataAlert">
                    <div id="<?= $_GET['name'] ?>_noDataAlertText" class="noDataAlertText">
                        No data available
                    </div>
                    <div id="<?= $_GET['name'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                        <i class="fa fa-times"></i>
                    </div>
               </div>
               <div id="<?= $_GET['name'] ?>_searchFilterContainer" class="trafficEventsSearchFilterContainer">
                   <div class="row">
                       <!--<div class="col-xs-6 centerWithFlex">Language</div>-->
                       <div class="col-xs-4 centerWithFlex">
                           <select id="<?= $_GET['name'] ?>_searchFilterLang" class="form-control">
                               <option value="english">English</option>
                               <option value="italiano">Italiano</option>
                           </select>
                       </div>
                       <div class="col-xs-4 centerWithFlex">
                           <select id="<?= $_GET['name'] ?>_searchFilterType" class="form-control" multiple>
                               <option value="a">Pippo</option>
                               <option value="b">Pluto</option>
                           </select>
                       </div>
                       <div class="col-xs-4 centerWithFlex">
                           <select id="<?= $_GET['name'] ?>_searchFilterCost" class="form-control">
                               <option value="all">All</option>
                               <option value="free">Free</option>
                               <option value="pay">On pay</option>
                           </select>
                       </div>
                   </div>
                   <div class="row">
                     <div class="col-xs-6 centerWithFlex"> 
                        <div class="form-group">
                          <div class="input-group date" id="<?= $_GET['name'] ?>_searchFilterStart">
                             <input data-toggle="tooltip" data-container="body" title="" type="text" class="form-control" />
                             <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                             </span>
                          </div>
                       </div>
                     </div>
                     <div class="col-xs-6 centerWithFlex"> 
                        <div class="form-group">
                          <div class="input-group date" id="<?= $_GET['name'] ?>_searchFilterStop">
                             <input data-toggle="tooltip" data-container="body" title="" type="text" class="form-control" />
                             <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                             </span>
                          </div>
                       </div>
                     </div>
                   </div>
               </div>
               <div id="<?= $_GET['name'] ?>_rollerContainer" class="trafficEventsRollerContainer"></div>
            </div>
        </div>
    </div>	
</div>