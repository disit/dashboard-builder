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
        var timeFontSize, scroller, widgetProperties, styleParameters, icon, serviceUri, 
            eventName, eventType, newRow, newIcon, eventContentW, test, widgetTargetList, backgroundTitleClass, backgroundFieldsClass,
            background, originalHeaderColor, originalBorderColor, eventTitle, temp, day, month, hour, min, sec, eventStart, 
            eventName, serviceUri, eventLat, eventLng, eventTooltip, eventStartDate, eventStartTime, eventSeverityDesc,
            eventsNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx, eventContentWPerc, eventSubtype, dataContainer, middleContainer, subTypeContainer, subTypeFontSize,
            dateContainer, timeContainer, dateTimeFontSize, severityContainer, mapPtrContainer, pinContainer, pinMsgContainer, 
            fontSizePin, dateFontSize, mapPinImg, eventNameWithCase, eventSeverity, 
            typeId, subtypeId, lastPopup = null;    
    
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
        
        var eventsArray = [];
        var eventsOnMaps = {};
        var targetsArrayForNotify = [];
        
        var timeSortState = 0;
        
        if(url === "null")
        {
            url = null;
        }
   
        timeFontSize = parseInt(fontSize*1.6);
        dateFontSize = parseInt(fontSize*0.95);
        fontSizePin = parseInt(fontSize*0.95);
        
        $(document).on("esbEventAdded", function(event){
           if(event.generator !== "<?= $_GET['name'] ?>")
           {
              $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").each(function(i){
                 if($("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap") === "true")
                 {
                    for(widgetName in widgetTargetList)
                    {
                        $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap", "false");

                        $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                        $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).removeClass("onMapTrafficEventPinAnimated");
                        $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");

                        eventsOnMaps[widgetName].eventsNumber = 0; 
                        eventsOnMaps[widgetName].eventsPoints.splice(0);
                        eventsOnMaps[widgetName].mapRef = null;
                        
                        timeSortState = 0;
                        $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("img").attr("src", "../img/trafficIcons/time.png");
                        $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");
                     }
                  }
              });
           }
        });
        
        //Definizioni di funzione
        function loadDefaultMap(widgetName)
        {
            var mapdiv = widgetName + "_defaultMapDiv";
            var mapRef = L.map(mapdiv).setView([43.769789, 11.255694], 11);

            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
               maxZoom: 18
            }).addTo(mapRef);
            mapRef.attributionControl.setPrefix('');
        }
        
        function populateWidget(fromSort)
        {
           var i = 0;
           
           $('#<?= $_GET['name'] ?>_rollerContainer').empty();
           
           for(var key in eventsArray)
            {
             temp = eventsArray[key].event_time;
             eventStart = new Date(temp);

             day = eventStart.getDate();
             if(day < 10)
             {
                day = "0" + day.toString();
             }

             month = eventStart.getMonth() + 1;
             if(month < 10)
             {
                month = "0" + month.toString();
             }

             hour = eventStart.getHours();
             if(hour < 10)
             {
                hour = "0" + hour.toString();
             }

             min = eventStart.getMinutes();
             if(min < 10)
             {
                min = "0" + min.toString();
             }

             sec = eventStart.getSeconds();
             if(sec < 10)
             {
                sec = "0" + sec.toString();
             }

             eventStartDate = day + "/" + month + "/" + eventStart.getFullYear().toString(); 
             eventStartTime = hour + ":" + min + ":" + sec;
             eventNameWithCase = eventsArray[key].data_id.replace("urn:rixf:org.resolute-eu.simulator/resources/", "");
             eventName = eventsArray[key].data_id.replace("urn:rixf:org.resolute-eu.simulator/resources/", "").toLowerCase();
             eventLat = eventsArray[key].payload.locations[0].latitude;
             eventLng = eventsArray[key].payload.locations[0].longitude;

             newRow = $("<div></div>");
             
            backgroundTitleClass = "lowSeverityTitle";
            backgroundFieldsClass = "lowSeverity";//Giallo
            background = "#ffcc00"; 
            
            icon = $('<img src="../img/resourceIcons/metro.png" />');
            
             newRow.css("height", rowPercHeight + "%");
             eventTitle = $('<div class="eventTitle">' + eventName + '</div>');
             eventTitle.addClass(backgroundTitleClass);
             eventTitle.css("font-size", fontSize + "px");
             eventTitle.css("height", "30%");
             $('#<?= $_GET['name'] ?>_rollerContainer').append(newRow);

             newRow.append(eventTitle);

             dataContainer = $('<div class="trafficEventDataContainer"></div>');
             newRow.append(dataContainer);

             newIcon = $("<div class='trafficEventIcon centerWithFlex'></div>");
             newIcon.append(icon); 
             newIcon.addClass(backgroundFieldsClass);
             dataContainer.append(newIcon);

             dateContainer = $("<div class='resourceDateContainer centerWithFlex'></div>"); 
             dateContainer.css("font-size", dateFontSize + "px");
             dateContainer.addClass(backgroundFieldsClass);
             dateContainer.html(eventStartDate);
             dataContainer.append(dateContainer);

             timeContainer = $("<div class='resourceTimeContainer centerWithFlex'></div>"); 
             timeContainer.css("font-size", dateFontSize + "px");
             timeContainer.addClass(backgroundFieldsClass);
             timeContainer.html(eventStartTime);
             dataContainer.append(timeContainer);

             dateTimeFontSize = parseInt(fontSize) - 1;
             dateContainer.css("font-size", dateTimeFontSize + "px");
             timeContainer.css("font-size", dateTimeFontSize + "px");

             mapPtrContainer = $("<div class='trafficEventMapPtr'></div>"); 
             mapPtrContainer.addClass(backgroundFieldsClass);

             pinContainer = $("<div class='trafficEventPinContainer'><a class='trafficEventLink' data-eventstartdate='" + eventStartDate + "' data-eventstarttime='" + eventStartTime + "' data-eventname='" + eventNameWithCase + 
                                  "' data-eventlat='" + eventLat + "' data-eventlng='" + eventLng + "' data-background='" + background + "' data-onMap='false'><i class='material-icons' style='font-size:32px'>place</i></a></div>");
             mapPtrContainer.append(pinContainer);
             pinMsgContainer = $("<div class='trafficEventPinMsgContainer'></div>");
             pinMsgContainer.css("font-size", fontSizePin + "px");
             mapPtrContainer.append(pinMsgContainer);

             dataContainer.append(mapPtrContainer);

             if(i < (eventsNumber - 1))
             {
                newRow.css("margin-bottom", "4px");
             }
             else
             {
                newRow.css("margin-bottom", "0px");
             }

             $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop(0);
             
             //Interazione cross-widget
             pinContainer.find("a.trafficEventLink").hover(
                function() 
                {
                   var localBackground = $(this).attr("data-background");

                   originalHeaderColor = {};
                   originalBorderColor = {};

                   for(var index in widgetTargetList) 
                   {
                        originalHeaderColor[widgetTargetList[index]] = $("#" + widgetTargetList[index] + "_header").css("background-color");
                        originalBorderColor[widgetTargetList[index]] = $("#" + widgetTargetList[index]).css("border-color");
                        
                        if($(this).attr("data-onMap") === 'false')
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("show");
                        }
                        else
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("hide");
                        }

                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "white");

                        $("#" + widgetTargetList[index] + "_header").css("background", localBackground);
                        $("#" + widgetTargetList[index]).css("border-color", localBackground);
                   }
                }, 
                function() 
                {
                   for(var index in widgetTargetList) 
                   {
                        if($(this).attr("data-onMap") === 'false')
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                        }
                        else
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                        }
                        
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "black");

                        $("#" + widgetTargetList[index] + "_header").css("background", originalHeaderColor[widgetTargetList[index]]);
                        $("#" + widgetTargetList[index]).css("border-color", originalBorderColor[widgetTargetList[index]]);
                   }
                }
             );
             
             pinContainer.find("a.trafficEventLink").click(function()
             {  
                var goesOnMap = false;

                if($(this).attr("data-onMap") === 'false')
                {
                   $(this).attr("data-onMap", 'true');
                   goesOnMap = true;
                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                   $(this).addClass("onMapTrafficEventPinAnimated");
                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                }
                else
                {
                   $(this).attr("data-onMap", 'false');
                   goesOnMap = false;
                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                   $(this).removeClass("onMapTrafficEventPinAnimated");
                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                }
                
                targetsArrayForNotify = [];

                for(var index in widgetTargetList) 
                {
                    if(goesOnMap)
                    {
                       targetsArrayForNotify.push(widgetName);
                       addEventToMap($(this), widgetTargetList[index], index);
                    }
                    else
                    {
                       removeEventFromMap($(this), widgetTargetList[index], index);
                    }
                }
                
                //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
               $.event.trigger({
                   type: "esbEventAdded",
                   generator: "<?= $_GET['name'] ?>",
                   targetsArray: targetsArrayForNotify
               });
                
             });
             i++;

            }//Fine del for
            
            //Mappa vuota sui target
            if(fromSort !== true)
            {
               var wName = null;
               for(var index in widgetTargetList) 
               {
                  wName = widgetTargetList[index];
                  
                  if($("#" + wName + "_div").attr("data-emptymapshown") === "false")
                  {
                     $("#" + wName + "_wrapper").hide();
                     $("#" + wName + "_defaultMapDiv").show();
                     loadDefaultMap(wName);
                     $("#" + wName + "_div").attr("data-emptymapshown", "true");
                  }
               }
            }
            
            $('#<?= $_GET['name'] ?>_rollerContainer [data-toggle="tooltip"]').tooltip({
               html: true
            });
        }
        
        function removeAllEventsFromMap(eventType)
        {
            for(var widgetName in widgetTargetList) 
            {
               for(var key in widgetTargetList[widgetName]) 
               {
                    if(widgetTargetList[widgetName][key] === eventType)
                    {  
                        //Rimozione di tutti gli eventi dalla mappa
                        var marker = null;
                        for(var widgetName in eventsOnMaps)
                        {
                            for(var index in eventsOnMaps[widgetName].eventsPoints)
                            {
                               marker = eventsOnMaps[widgetName].eventsPoints[index][8];
                               eventsOnMaps[widgetName].mapRef.removeLayer(marker);
                            }

                            eventsOnMaps[widgetName].eventsPoints.splice(0);
                            eventsOnMaps[widgetName].eventsNumber = 0;
                            $("#" + widgetName + "_mapDiv").hide();
                            $("#" + widgetName + "_defaultMapDiv").show();
                        }
                     }
                  }
               }
               $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink[data-eventtype=" + eventType + "]").each(function(i){
                  $(this).attr("data-onMap", 'false');
                  $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                  $(this).removeClass("onMapTrafficEventPinAnimated");
                  $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
               });
               
            }
        
        function addEventToMap(eventLink, widgetName, widgetIndex)
        {
           var minLat, minLng, maxLat, maxLng, targetName, mapdiv, markerLocation, marker, popupText, pinIcon = null;
           var lat = eventLink.attr("data-eventlat");
           var lng = eventLink.attr("data-eventlng");
           var eventName = eventLink.attr("data-eventname");
           var eventStartDate = eventLink.attr("data-eventstartdate");
           var eventStartTime = eventLink.attr("data-eventstarttime");
           var coordsAndType = [];
           coordsAndType.push(lng);//OS vuole le coordinate alla rovescia
           coordsAndType.push(lat);
           coordsAndType.push(eventName);
           coordsAndType.push(eventStartDate);
           coordsAndType.push(eventStartTime);
           
           if(eventsOnMaps[widgetIndex]["noPointsUrl"] === null) 
           {
               targetName = widgetName + "_div";
               eventsOnMaps[widgetIndex]["noPointsUrl"] = $("#" + targetName).attr("data-nopointsurl");
            }
           
           eventsOnMaps[widgetIndex].eventsPoints.push(coordsAndType);
           eventsOnMaps[widgetIndex].eventsNumber++;
           
            //Leaflet
            $("#" + widgetName + "_wrapper").hide();
            $("#" + widgetName + "_defaultMapDiv").hide();
            mapdiv = widgetName + "_mapDiv";
            
            //Creazione della mappa
            if(eventsOnMaps[widgetIndex].mapRef !== null)
            {
               eventsOnMaps[widgetIndex].mapRef.off();
               eventsOnMaps[widgetIndex].mapRef.remove();
            }

            $("#" + widgetName + "_mapDiv").remove();
            $("#" + widgetName + "_content").append('<div id="' + widgetName + '_mapDiv" class="mapDiv"></div>');
            $("#" + widgetName + "_mapDiv").show();

            eventsOnMaps[widgetIndex].mapRef = L.map(mapdiv).setView([lat, lng], 17);

            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
               maxZoom: 18
            }).addTo(eventsOnMaps[widgetIndex].mapRef);
            eventsOnMaps[widgetIndex].mapRef.attributionControl.setPrefix('');
            
            minLat = +90;
            minLng = +180;
            maxLat = -90;
            maxLng = -180;
            
            for(var i = 0; i < eventsOnMaps[widgetIndex].eventsNumber; i++)
            {
               lat = eventsOnMaps[widgetIndex].eventsPoints[i][1];
               lng = eventsOnMaps[widgetIndex].eventsPoints[i][0];
               eventName = eventsOnMaps[widgetIndex].eventsPoints[i][2];
               eventStartDate  = eventsOnMaps[widgetIndex].eventsPoints[i][3];
               eventStartTime = eventsOnMaps[widgetIndex].eventsPoints[i][4];

               //console.log("Lat: " + lat + " - Lng: " + lng + " - Event type: " + eventType + " - Event name: " + eventName + " - Event severity: " + eventSeverity + " - Event start date: " + eventStartDate + " - Event start time: " + eventStartTime);

               mapPinImg = '../img/resourceIcons/metroMap.png';
            
               pinIcon = new L.DivIcon({
                   className: null,
                   html: '<img src="' + mapPinImg + '" class="leafletPin" />'
               });

               markerLocation = new L.LatLng(lat, lng);
               marker = new L.Marker(markerLocation, {icon: pinIcon});
               eventsOnMaps[widgetIndex].eventsPoints[i][5] = marker;
               popupText = "<span class='mapPopupTitle'>" + eventName.toUpperCase() + "</span>" + 
                           "<span class='mapPopupLine'>" + eventStartDate + " - " + eventStartTime + "</span>";

               eventsOnMaps[widgetIndex].mapRef.addLayer(marker);
               lastPopup = marker.bindPopup(popupText, {offset: [-5, -40]}).openPopup();
               
               //Calcolo del rettangolo di visualizzazione
               if(eventsOnMaps[widgetIndex].eventsPoints[i][0] < minLng)
               {
                  minLng = eventsOnMaps[widgetIndex].eventsPoints[i][0];
               }

               if(eventsOnMaps[widgetIndex].eventsPoints[i][0] > maxLng)
               {
                  maxLng = eventsOnMaps[widgetIndex].eventsPoints[i][0];
               }

               if(eventsOnMaps[widgetIndex].eventsPoints[i][1] < minLat)
               {
                  minLat = eventsOnMaps[widgetIndex].eventsPoints[i][1];
               }

               if(eventsOnMaps[widgetIndex].eventsPoints[i][1] > maxLat)
               {
                  maxLat = eventsOnMaps[widgetIndex].eventsPoints[i][1];
               }
            }
            
            if(eventsOnMaps[widgetIndex].eventsNumber > 1)
            {
               eventsOnMaps[widgetIndex].mapRef.fitBounds([
                  [minLat, minLng],
                  [maxLat, maxLng]
               ]);
            }
        }
        
        function removeAllEventsFromMaps(fromSort)
        {
           for(var index in eventsOnMaps)
           {
              if(eventsOnMaps[index].mapRef !== null)
              {
                 eventsOnMaps[index].mapRef.off();
                 eventsOnMaps[index].mapRef.remove();
                 eventsOnMaps[index].mapRef = null;
                 $("#" + widgetTargetList[index] + "_mapDiv").remove();
                 $('<div id="' + widgetTargetList[index] + '_mapDiv" class="mapDiv"></div>').insertBefore("#" + widgetTargetList[index] + "_wrapper");
            
                 if(fromSort)
                 {
                    $("#" + widgetTargetList[index] + "_mapDiv").show();
                 }
                 else
                 {
                    $("#" + widgetTargetList[index] + "_mapDiv").hide();
                    $("#" + widgetName + "_defaultMapDiv").show();
                 }
              }
              
              for(var index2 in eventsOnMaps[index].eventsPoints)
              {
                  eventsOnMaps[index].eventsPoints[index2][8] = null;
              }
               
              eventsOnMaps[index].eventsPoints.splice(0);
              eventsOnMaps[index].eventsNumber = 0; 
           }
        }
        
        function removeEventFromMap(eventLink, widgetName,widgetIndex)
        {
           var minLat, minLng, maxLat, maxLng, mapdiv, marker, pinIcon, index = null;
           var lat = eventLink.attr("data-eventlat");
           var lng = eventLink.attr("data-eventlng");
           var eventName = eventLink.attr("data-eventname");
           var eventStartDate = eventLink.attr("data-eventstartdate");
           var eventStartTime = eventLink.attr("data-eventstarttime");
           
           for(var j = 0; j < eventsOnMaps[widgetIndex].eventsPoints.length; j++)
           {
              if((eventsOnMaps[widgetIndex].eventsPoints[j][0] === lng)&&(eventsOnMaps[widgetIndex].eventsPoints[j][1] === lat)&&(eventsOnMaps[widgetIndex].eventsPoints[j][2] === eventName)&&(eventsOnMaps[widgetIndex].eventsPoints[j][3] === eventStartDate)&&(eventsOnMaps[widgetIndex].eventsPoints[j][4] === eventStartTime))
              {
                 index = j;
                 break;
              }
           }
           
           eventsOnMaps[widgetIndex].eventsPoints.splice(index, 1);
           eventsOnMaps[widgetIndex].eventsNumber--;
           
           if(lastPopup !== null)
           {
              lastPopup.closePopup();
           }
           
           //Leaflet
            $("#" + widgetName + "_wrapper").hide();
            $("#" + widgetName + "_defaultMapDiv").hide();
            mapdiv = widgetName + "_mapDiv";
            
            //Creazione della mappa
            if(eventsOnMaps[widgetIndex].mapRef !== null)
            {
               eventsOnMaps[widgetIndex].mapRef.off();
               eventsOnMaps[widgetIndex].mapRef.remove();
            }

            $("#" + widgetName + "_mapDiv").remove();
            $("#" + widgetName + "_content").append('<div id="' + widgetName + '_mapDiv" class="mapDiv"></div>');
            $("#" + widgetName + "_mapDiv").show();

            eventsOnMaps[widgetIndex].mapRef = L.map(mapdiv).setView([43.769805, 11.256064], 17);

            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
               maxZoom: 18
            }).addTo(eventsOnMaps[widgetIndex].mapRef);
            eventsOnMaps[widgetIndex].mapRef.attributionControl.setPrefix('');
            
            minLat = +90;
            minLng = +180;
            maxLat = -90;
            maxLng = -180;
            
            for(var i = 0; i < eventsOnMaps[widgetIndex].eventsNumber; i++)
            {
               lat = eventsOnMaps[widgetIndex].eventsPoints[i][1];
               lng = eventsOnMaps[widgetIndex].eventsPoints[i][0];
               eventName = eventsOnMaps[widgetIndex].eventsPoints[i][2];
               eventStartDate  = eventsOnMaps[widgetIndex].eventsPoints[i][3];
               eventStartTime = eventsOnMaps[widgetIndex].eventsPoints[i][4];

               //console.log("Lat: " + lat + " - Lng: " + lng + " - Event type: " + eventType + " - Event name: " + eventName + " - Event severity: " + eventSeverity + " - Event subtype: " + eventSubtype + " - Event start date: " + eventStartDate + " - Event start time: " + eventStartTime);

               switch(eventSeverity)
               {
                  case "MINOR":
                     mapPinImg = '../img/alarmIcons/' + alarmTypes[eventType].mapIconLow;
                     severityColor = "#ffcc00";
                     break;

                  case "MAJOR":
                     mapPinImg = '../img/alarmIcons/' + alarmTypes[eventType].mapIconMed;
                     severityColor = "#ff9900";
                     break;

                  case "CRITICAL":
                     mapPinImg = '../img/alarmIcons/' + alarmTypes[eventType].mapIconHigh;
                     severityColor = "#ff6666";
                     break;   
               } 

               //console.log("Map pin img: " + mapPinImg + " - Severity color: " + severityColor);

               mapPinImg = '../img/resourceIcons/metroMap.png';
            
               pinIcon = new L.DivIcon({
                   className: null,
                   html: '<img src="' + mapPinImg + '" class="leafletPin" />'
               });

               markerLocation = new L.LatLng(lat, lng);
               marker = new L.Marker(markerLocation, {icon: pinIcon});
               eventsOnMaps[widgetIndex].eventsPoints[i][5] = marker;
               popupText = "<span class='mapPopupTitle'>" + eventName.toUpperCase() + "</span>" + 
                           "<span class='mapPopupLine'>" + eventStartDate + " - " + eventStartTime + "</span>";

               eventsOnMaps[widgetIndex].mapRef.addLayer(marker);
               lastPopup = marker.bindPopup(popupText, {offset: [-5, -40]});
               
               //Calcolo del rettangolo di visualizzazione
               if(eventsOnMaps[widgetIndex].eventsPoints[i][0] < minLng)
               {
                  minLng = eventsOnMaps[widgetIndex].eventsPoints[i][0];
               }

               if(eventsOnMaps[widgetIndex].eventsPoints[i][0] > maxLng)
               {
                  maxLng = eventsOnMaps[widgetIndex].eventsPoints[i][0];
               }

               if(eventsOnMaps[widgetIndex].eventsPoints[i][1] < minLat)
               {
                  minLat = eventsOnMaps[widgetIndex].eventsPoints[i][1];
               }

               if(eventsOnMaps[widgetIndex].eventsPoints[i][1] > maxLat)
               {
                  maxLat = eventsOnMaps[widgetIndex].eventsPoints[i][1];
               }
            }
            
            if(eventsOnMaps[widgetIndex].eventsNumber > 1)
            {
               eventsOnMaps[widgetIndex].mapRef.fitBounds([
                  [minLat, minLng],
                  [maxLat, maxLng]
               ]);
            }
            else
            {
              if(eventsOnMaps[widgetIndex].eventsNumber === 1)
              {
                  eventsOnMaps[widgetIndex].mapRef.setView([eventsOnMaps[widgetIndex].eventsPoints[0][1], eventsOnMaps[widgetIndex].eventsPoints[0][0]], 17);
              }
              else
              {
                 $("#" + widgetName + "_mapDiv").hide();
                 $("#" + widgetName + "_defaultMapDiv").show();
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
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor);
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
                  noPointsUrl: null,
                  eventsNumber: 0,
                  eventsPoints: [],//Array indicizzato con le coordinate dei punti mostrati
                  mapRef: null
               };
            }
            
            $.ajax({
               url: "../widgets/esbDao.php",
               type: "POST",
               data: {
                  operation: "getResources"
               },
               async: true,
               dataType: 'json',
               success: function (data) 
               {
                  if(firstLoad !== false)
                  {
                      showWidgetContent(widgetName);
                  }
                  else
                  {
                      elToEmpty.empty();
                  }
                  
                  //console.log("OK");
                  //console.log(JSON.stringify(data));
                  
                  $("#<?= $_GET['name'] ?>_rollerContainer").height($("#<?= $_GET['name'] ?>_mainContainer").height() - 50); 
                
                  eventsNumber = Object.keys(data).length;
                  widgetWidth = $('#<?= $_GET['name'] ?>_div').width();
                  shownHeight = $("#<?= $_GET['name'] ?>_rollerContainer").prop("offsetHeight");
                  rowPercHeight =  75 * 100 / shownHeight;
                  contentHeightPx = eventsNumber * 100;
                  eventContentWPerc = null;
                  
                  if(contentHeightPx > shownHeight)
                  {
                      eventContentW = parseInt(widgetWidth - 45 - 22);
                  }
                  else
                  {
                      eventContentW = parseInt(widgetWidth - 45 - 5);
                  }
                  
                  eventContentWPerc = Math.floor(eventContentW / widgetWidth * 100);
                  
                  //Inserimento una tantum degli eventi nell'apposito array (per ordinamenti)
                  for(var key in data)
                  {
                    var localPayload = JSON.parse(data[key].payload);
                    data[key].payload = localPayload;
                    eventsArray.push(data[key]); 
                  }
                  
                  populateWidget(false);
               
                   $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).hover(
                        function()
                        {
                            $(this).css("cursor", "pointer");
                            $(this).css("color", "white");
                            $(this).find("img").attr("src", "../img/trafficIcons/timeWhite.png");
                            switch(timeSortState)
                            {
                                case 0://Crescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                    $(this).attr("title", "Sort by time - Ascending");
                                    break;
                                    
                                case 1://Decrescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                    $(this).attr("title", "Sort by time - Descending");
                                    break;
                                    
                                case 2://No sort
                                    $(this).find("div.trafficEventsButtonIndicator").html('no sort');
                                    $(this).attr("title", "Sort by time - None");
                                    break;
                            }
                        }, 
                        function()
                        {
                            $(this).css("cursor", "auto");
                            $(this).css("color", "black");
                            $(this).find("img").attr("src", "../img/trafficIcons/time.png");
                            
                            switch(timeSortState)
                            {
                                case 0://No sort
                                    $(this).find("div.trafficEventsButtonIndicator").html('');
                                    break;
                                    
                                case 1://Crescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                    break;
                                    
                                case 2://Decrescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                    break;
                            }
                        }
                   );
                   
                   $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).click(function(){
                        var localEventType = null;
                        
                        switch(timeSortState)
                        {
                            case 0:
                                eventsArray.sort(function(a,b) 
                                {
                                    var itemA = new Date(a.event_time); 
                                    var itemB = new Date(b.event_time);
                                    if (itemA > itemB)
                                    {
                                       return 1;
                                    }
                                    else
                                    {
                                       if (itemA < itemB)
                                       {
                                          return -1;
                                       }
                                       else
                                       {
                                          return 0; 
                                       }
                                    }
                                });
                                
                                populateWidget(true);
                                removeAllEventsFromMaps(true);
                                
                                //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                localEventType = $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                                $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                                
                                targetsArrayForNotify = [];
                                
                                for(var index in widgetTargetList) 
                                {
                                    targetsArrayForNotify.push(widgetTargetList[index]);
                                    $("#" + widgetTargetList[index] + "_wrapper").hide();
                                    $("#" + widgetTargetList[index] + "_defaultMapDiv").hide();
                                    $("#" + widgetTargetList[index] + "_mapDiv").show();
                                    addEventToMap($("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetTargetList[index], index);       
                                }
                                
                                 //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
                                 $.event.trigger({
                                     type: "esbEventAdded",
                                     generator: "<?= $_GET['name'] ?>",
                                     targetsArray: targetsArrayForNotify
                                 });
                                
                                timeSortState = 1;
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                break;

                            case 1:
                                eventsArray.sort(function(a,b) 
                                {
                                    var itemA = new Date(a.event_time); 
                                    var itemB = new Date(b.event_time);
                                    if (itemA < itemB)
                                    {
                                       return 1;
                                    }
                                    else
                                    {
                                       if (itemA > itemB)
                                       {
                                          return -1;
                                       }
                                       else
                                       {
                                          return 0; 
                                       }
                                    }
                                });
                                
                                populateWidget(true);
                                removeAllEventsFromMaps(true);
                                
                                //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                localEventType = $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                                $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                                
                                for(var index in widgetTargetList) 
                                {
                                    $("#" + widgetTargetList[index] + "_wrapper").hide();
                                    $("#" + widgetTargetList[index] + "_defaultMapDiv").hide();
                                    $("#" + widgetTargetList[index] + "_mapDiv").show();
                                    addEventToMap($("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetTargetList[index], index);       
                                }
                                
                                timeSortState = 2;
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                break;

                            case 2:
                                eventsArray.sort(function(a,b) {
                                    var itemA = a.data_id; 
                                    var itemB = b.data_id;
                                    if (itemA < itemB)
                                    {
                                       return 1;
                                    }
                                    else
                                    {
                                       if (itemA > itemB)
                                       {
                                          return -1;
                                       }
                                       else
                                       {
                                          return 0; 
                                       }
                                    }
                                });
                                
                                populateWidget(true);
                                removeAllEventsFromMaps(false);
                                
                                for(var index in widgetTargetList) 
                                {
                                    $("#" + widgetTargetList[index] + "_wrapper").hide();
                                    $("#" + widgetTargetList[index] + "_mapDiv").hide();
                                    $("#" + widgetTargetList[index] + "_defaultMapDiv").show();
                                }
                                
                                timeSortState = 0;
                                $(this).find("div.trafficEventsButtonIndicator").html('');
                                break;
                        }
                        $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html('');
                   });
 
               scroller = setInterval(stepDownInterval, speed);
               var timeToClearScroll = (timeToReload - 0.5) * 1000;
               
               setTimeout(function()
               {
                   clearInterval(scroller);
                   $("#<?= $_GET['name'] ?>_rollerContainer").off();
                   
                   //$(document).off("esbEventAdded");
                   
                   $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");

                   //Ripristino delle homepage native per gli widget targets al reload
                   /*for(var widgetName in widgetTargetList) 
                   {
                      if(eventsOnMaps[widgetName].eventsNumber > 0)
                      {
                         $("#" + widgetName + "_iFrame").attr("src", eventsOnMaps[widgetName].noPointsUrl);
                      }
                   }*/

               }, timeToClearScroll);


               $("#<?= $_GET['name'] ?>_rollerContainer").mouseenter(function() 
               {
                   clearInterval(scroller);
               });

               $("#<?= $_GET['name'] ?>_rollerContainer").mouseleave(function()
               {    
                   clearInterval(scroller);    
                   scroller = setInterval(stepDownInterval, speed);
               });
                  
               },
               error: function (data)
               {
                  console.log("Ko");
                  console.log(JSON.stringify(data));
                  
                  showWidgetContent(widgetName);
                  $("#<?= $_GET['name'] ?>_table").css("display", "none"); 
                  $("#<?= $_GET['name'] ?>_noDataAlert").css("display", "block");
               }
            });
        }
        else
        {
            alert("Error while loading widget properties");
        }
        
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, elToEmpty, "widgetResources", test, eventNames);
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
            <p id="<?= $_GET['name'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_GET['name'] ?>_mainContainer" class="chartContainer">
               <div id="<?= $_GET['name'] ?>_buttonsContainer" class="trafficEventsButtonsContainer centerWithFlex">
                   <div class="trafficEventsButtonContainer">
                       <div class="trafficEventsButtonIcon centerWithFlex">
                           <img src="../img/trafficIcons/time.png" />
                       </div>
                      <div class="trafficEventsButtonIndicator centerWithFlex"></div>                                           
                   </div>
               </div>
               <div id="<?= $_GET['name'] ?>_rollerContainer" class="trafficEventsRollerContainer"></div>
            </div>
        </div>
    </div>	
</div> 