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
         
        var intervalRef, timeFontSize, scroller, widgetProperties, styleParameters, icon, serviceUri, 
            planName, newRow, eventContentW, test, widgetTargetList, backgroundTitleClass, backgroundFieldsClass,
            background, originalHeaderColor, originalBorderColor, planTitle, temp, day, month, hour, min, sec, planStart, 
            planName, serviceUri, planStartDate, planStartTime,
            plansNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx, eventContentWPerc, dataContainer,
            dateContainer, dateTimeFontSize, mapPtrContainer, pinContainer, pinMsgContainer, 
            fontSizePin, dateFontSize, mapPinImg, planId, pathsContainer, paths, statusContainer, status, detailsContainer, decisionContainer, 
            decisionPtrContainer, decisionMsgContainer, statusOnPin, lastPopup = null;    
    
        var planNames = new Array();
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
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        
        var plansObj = {};
        var plansOrderedIds = [];
        var plansOnMaps = {};
        var targetsArrayForNotify = [];
        
        var timeSortState = 0;
        var decisionSortState = 0;
        
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
   
        timeFontSize = parseInt(fontSize*1.6);
        dateFontSize = parseInt(fontSize*0.95);
        fontSizePin = parseInt(fontSize*0.95);
        
        $(document).on("esbEventAdded", function(event){
           if(event.generator !== "<?= $_GET['name'] ?>")
           {
              $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").each(function(i){
                 if($("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap") === "true")
                 {
                    var evtType = null;
                    for(widgetName in widgetTargetList)
                    {
                       evtType = $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-eventtype");
                       if($.inArray(evtType, widgetTargetList[widgetName]))
                       {
                          $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap", "false");
                          
                          $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                          $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).removeClass("onMapTrafficEventPinAnimated");
                          $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                          
                          plansOnMaps[widgetName].shownPolyGroup = null;
                          plansOnMaps[widgetName].mapRef = null;
                          
                          decisionSortState = 0;
                          timeSortState = 0;
                          
                          $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("img").attr("src", "../img/trafficIcons/time.png");
                          $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");
                          $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("img").attr("src", "../img/planIcons/decision.png");
                          $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html("");
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
        
        function populateWidget(fromSort)
        {
           var i = 0;
           
           $('#<?= $_GET['name'] ?>_rollerContainer').empty();
           
            for(var z = 0; z < plansOrderedIds.length; z++)
            {
             key = plansOrderedIds[z];
             temp = plansObj[key].event_time;
             planStart = new Date(temp);

             day = planStart.getDate();
             if(day < 10)
             {
                day = "0" + day.toString();
             }

             month = planStart.getMonth() + 1;
             if(month < 10)
             {
                month = "0" + month.toString();
             }

             hour = planStart.getHours();
             if(hour < 10)
             {
                hour = "0" + hour.toString();
             }

             min = planStart.getMinutes();
             if(min < 10)
             {
                min = "0" + min.toString();
             }

             sec = planStart.getSeconds();
             if(sec < 10)
             {
                sec = "0" + sec.toString();
             }

             planStartDate = day + "/" + month + "/" + planStart.getFullYear().toString(); 
             planStartTime = hour + ":" + min + ":" + sec;
             
             planName = "PLAN N." + plansObj[key].data_id.replace("urn:rixf:gr.certh/evacuationplan_ID/", "").toLowerCase();
             
             if((plansObj[key].lastStatus === "") || (plansObj[key].lastStatus === "null") || (plansObj[key].lastStatus === null) || (plansObj[key].lastStatus === undefined))
             {
                plansObj[key].lastStatus = "PROPOSED";
             }
             
             status = plansObj[key].lastStatus;
             
             paths = plansObj[key].payload.evacuation_paths.length;
             planId = key;
             
             newRow = $("<div></div>");
             
             switch(status)
             {
                case "PROPOSED":
                   backgroundTitleClass = "planProposedTitle";
                   backgroundFieldsClass = "planProposed";//Giallo
                   background = "#ffcc00";
                   break;
                   
                case "IN_PROGRESS":
                   backgroundTitleClass = "planProgressTitle";
                   backgroundFieldsClass = "planProgress";//Arancio
                   background = "#ff9900";
                   break;
                   
                case "APPROVED":
                   backgroundTitleClass = "planApprovedTitle";
                   backgroundFieldsClass = "planApproved";//Verde
                   background = "#80ff80";
                   break;
                   
                case "REJECTED":
                   backgroundTitleClass = "planRejectedTitle";
                   backgroundFieldsClass = "planRejected";//Rosso
                   background = "#ff6666";
                   break;
                   
                case "CLOSED":
                   backgroundTitleClass = "planClosedTitle";
                   backgroundFieldsClass = "planClosed";//Grigio
                   background = "#e6e6e6";
                   break;  
             }
             
             

             newRow.css("height", rowPercHeight + "%");
             planTitle = $('<div class="eventTitle"><p class="eventTitlePar">' + planName + '</p></div>');
             planTitle.addClass(backgroundTitleClass);
             planTitle.css("font-size", fontSize + "px");
             planTitle.css("height", "30%");
             $('#<?= $_GET['name'] ?>_rollerContainer').append(newRow);

             newRow.append(planTitle);

             dataContainer = $('<div class="trafficEventDataContainer"></div>');
             newRow.append(dataContainer);
             
             detailsContainer = $('<div class="planDetailsContainer"></div>');
             
             statusContainer = $("<div class='planStatusContainer centerWithFlex'></div>"); 
             statusContainer.css("font-size", dateFontSize + "px");
             statusContainer.addClass(backgroundFieldsClass);
             statusContainer.html(status);
             detailsContainer.append(statusContainer);

             dateContainer = $("<div class='planDateContainer centerWithFlex'></div>"); 
             dateContainer.css("font-size", dateFontSize + "px");
             dateContainer.addClass(backgroundFieldsClass);
             dateContainer.html(planStartDate + " - " + planStartTime);
             detailsContainer.append(dateContainer);
             
             pathsContainer = $("<div class='planDateContainer centerWithFlex'></div>"); 
             pathsContainer.css("font-size", dateFontSize + "px");
             pathsContainer.addClass(backgroundFieldsClass);
             pathsContainer.html("PATHS: " + paths);
             detailsContainer.append(pathsContainer);
             
             dataContainer.append(detailsContainer);
             
             dateTimeFontSize = parseInt(fontSize) - 1;
             dateContainer.css("font-size", dateTimeFontSize + "px");

             mapPtrContainer = $("<div class='trafficEventMapPtr'></div>"); 
             mapPtrContainer.addClass(backgroundFieldsClass);
             
             if(status.includes("progress"))
             {
                statusOnPin = "inProgress";
             }
             else
             {
                statusOnPin = status.toLowerCase();
             }
             

             pinContainer = $("<div class='trafficEventPinContainer'><a class='trafficEventLink' data-eventType='" + statusOnPin + "' data-eventstartdate='" + planStartDate + "' data-eventstarttime='" + planStartTime + "'" + 
                                "data-background='" + background + "' data-onMap='false' data-planid='" + planId + "'><i class='material-icons' style='font-size:32px'>place</i></a></div>");
             mapPtrContainer.append(pinContainer);
             pinMsgContainer = $("<div class='trafficEventPinMsgContainer'></div>");
             pinMsgContainer.css("font-size", fontSizePin + "px");
             mapPtrContainer.append(pinMsgContainer);

             dataContainer.append(mapPtrContainer);
             
             
             decisionPtrContainer = $("<div class='trafficEventMapPtr'></div>"); 
             decisionPtrContainer.addClass(backgroundFieldsClass);
             decisionContainer = $("<div class='planDecisionContainer'><a class='planDecisionLink' data-background='" + background + "' data-planstatus='" + status + "' data-planid='" + planId + "'><img src='../img/planIcons/decision.png'/></a></div>");
             decisionPtrContainer.append(decisionContainer);
             decisionMsgContainer = $("<div class='decisionPinMsgContainer'></div>");
             decisionMsgContainer.css("font-size", fontSizePin + "px");
             decisionPtrContainer.append(decisionMsgContainer);

             dataContainer.append(decisionPtrContainer);

             if(i < (plansNumber - 1))
             {
                newRow.css("margin-bottom", "4px");
             }
             else
             {
                newRow.css("margin-bottom", "0px");
             }

             $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop(0);
             
             
             decisionContainer.find("a.planDecisionLink").hover(
                function(){
                  if(status !== "CLOSED")
                  {
                     $(this).css("cursor", "pointer");
                     $(this).find("img").attr("src", "../img/planIcons/decisionWhite.png");
                     $(this).parent().parent().find("div.decisionPinMsgContainer").html("decide");
                     $(this).parent().parent().find("div.decisionPinMsgContainer").css("color", "white");
                  }
               },
               function(){
                  if(status !== "CLOSED")
                  {
                     $(this).css("cursor", "auto");
                     $(this).find("img").attr("src", "../img/planIcons/decision.png");
                     $(this).parent().parent().find("div.decisionPinMsgContainer").html("");
                     $(this).parent().parent().find("div.decisionPinMsgContainer").css("color", "black");
                  }
               }
             );
     
            decisionContainer.find("a.planDecisionLink").click(
                function(){
                  if($(this).attr("data-planstatus") !== "CLOSED")
                  {
                     //Freeze del widget, per evitare problemi quando il refresh intercorre prima di chiudere il modale
                     clearInterval(intervalRef);
                     clearInterval(scroller);
                     $("#<?= $_GET['name'] ?>_rollerContainer").off();
                     
                     $("#modalChangePlanStatusPlanId").val($(this).attr("data-planid"));
                     $("#modalChangePlanStatusCurrentStatus").val($(this).attr("data-planstatus"));
                     $("#modalChangePlanStatusTitle").html($(this).attr("data-planid"));
                     $("#modalChangePlanStatusStatus").html($(this).attr("data-planstatus")); 
                     $("#modalChangePlanStatusStatus").css("background-color", $(this).attr("data-background"));
                     
                     $("#modalChangePlanStatusSelect").empty();
                     
                     switch($(this).attr("data-planstatus"))
                     {
                        case "PROPOSED":
                           $("#modalChangePlanStatusSelect").append('<option value="IN_PROGRESS">in progress</option>');
                           $("#modalChangePlanStatusSelect").append('<option value="APPROVED">approved</option>');
                           $("#modalChangePlanStatusSelect").append('<option value="REJECTED">rejected</option>');
                           break;
                           
                        case "IN_PROGRESS":
                           $("#modalChangePlanStatusSelect").append('<option value="APPROVED">approved</option>');
                           $("#modalChangePlanStatusSelect").append('<option value="REJECTED">rejected</option>');
                           break;
                           
                        case "APPROVED": case "REJECTED":
                           $("#modalChangePlanStatusSelect").append('<option value="CLOSED">closed</option>');
                           break;   
                     }
                     $("#modalChangePlanStatusOk").hide();
                     $("#modalChangePlanStatusKo").hide();
                     $("#modalChangePlanStatusMain").show();
                     $("#modalChangePlanStatusFooter").show();
                     $("#modalChangePlanStatus").modal('show');
                  }
               }
            );
    
             //Interazione cross-widget
             pinContainer.find("a.trafficEventLink").hover(
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
                                if($(this).attr("data-onMap") === 'false')
                                {
                                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("show");
                                }
                                else
                                {
                                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("hide");
                                }

                                if($(this).attr("data-eventType") === localEventType)
                                {
                                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                                   $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                                }

                                $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "white");

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
                   var localEventType = $(this).attr("data-eventType");
                   for(var widgetName in widgetTargetList) 
                   {
                        for(var key in widgetTargetList[widgetName]) 
                        {
                            if(widgetTargetList[widgetName][key] === localEventType)
                            {
                                if($(this).attr("data-onMap") === 'false')
                                {
                                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                                }
                                else
                                {
                                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                                }

                                if($(this).attr("data-eventType") === localEventType)
                                {
                                   $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                                   $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                                }

                                $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "black");

                                $("#" + widgetName + "_header").css("background", originalHeaderColor[widgetName]);
                                $("#" + widgetName).css("border-color", originalBorderColor[widgetName]);
                            }
                        }
                   }
                }
             );
             
             pinContainer.find("a.trafficEventLink").click(function()
             {  
                var localEventType = $(this).attr("data-eventType");
                
                targetsArrayForNotify = [];
                
                for(var widgetName in widgetTargetList) 
                {
                   for(var key in widgetTargetList[widgetName]) 
                   {
                        if(widgetTargetList[widgetName][key] === localEventType)
                        {
                            if($(this).attr("data-onMap") === 'false')
                            {
                                $("#<?= $_GET['name'] ?>_div div.trafficEventPinMsgContainer").each(function(){
                                   $(this).html("");
                                });
                                
                                $("#<?= $_GET['name'] ?>_div a.trafficEventLink").each(function(){
                                   $(this).removeClass("onMapTrafficEventPinAnimated");
                                   $(this).attr("data-onMap", 'false');
                                });
                                        
                                $("#<?= $_GET['name'] ?>_div div.trafficEventPinMsgContainer").each(function(){
                                   $(this).removeClass("onMapTrafficEventPinAnimated");
                                });
                                
                                $(this).attr("data-onMap", 'true');
                                $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                                $(this).addClass("onMapTrafficEventPinAnimated");
                                $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                                
                                targetsArrayForNotify.push(widgetName);
                                
                                addEventToMap($(this), widgetName);
                            }
                            else
                            {
                               $(this).attr("data-onMap", 'false');
                               $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                               $(this).removeClass("onMapTrafficEventPinAnimated");
                               $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                               
                               removeEventFromMap(widgetName);
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
             i++;
             
            //Mappa vuota sui target - Commentata il 21/09/2017, la deve caricare da solo il widgetExternalContent solo se il suo link è valorizzato a "map" 
            /*if(fromSort !== true)
            {
               for(var widgetName in widgetTargetList) 
               {
                  if($("#" + widgetName + "_div").attr("data-emptymapshown") === "false")
                  {
                     $("#" + widgetName + "_wrapper").hide();
                     $("#" + widgetName + "_defaultMapDiv").show();
                     loadDefaultMap(widgetName);
                     $("#" + widgetName + "_div").attr("data-emptymapshown", "true");
                  }
               }
            }*/

            }//Fine del for 
        }
        
        function removeAllEventsFromMaps(fromSort)
        {
           for(var widgetName in plansOnMaps)
           {
              if(plansOnMaps[widgetName].mapRef !== null)
              {
                 plansOnMaps[widgetName].mapRef.off();
                 plansOnMaps[widgetName].mapRef.remove();
                 plansOnMaps[widgetName].mapRef = null;
                 $("#" + widgetName + "_mapDiv").remove();
                 $('<div id="' + widgetName + '_mapDiv" class="mapDiv"></div>').insertBefore("#" + widgetName + "_wrapper");
                 
                 $("#" + widgetName + "_wrapper").hide();
                 
                 if(fromSort)
                 {
                    $("#" + widgetName + "_mapDiv").show();
                 }
                 else
                 {
                    $("#" + widgetName + "_driverWidgetType").val("");
                    $("#" + widgetName + "_netAnalysisServiceMapUrl").val("");
                    $("#" + widgetName + "_buttonUrl").val("");
                    $("#" + widgetName + "_recreativeEventsUrl").val(""); 
                    $("#" + widgetName + "_mapDiv").hide();
                    $("#" + widgetName + "_defaultMapDiv").show();
                 }
              }
              
              plansOnMaps[widgetName].shownPolyGroup = null;
           }
        }
        
        function updateFullscreenPointsList(widgetNameLocal, polyIndex, poly, pathsQt)
        {
            var temp = null;
            $("#" + widgetNameLocal + "_driverWidgetType").val("evacuationPlans");
            for(var i = 0; i < poly._latlngs.length; i++)
            {  
              if($('#' + widgetNameLocal + '_fullscreenEvent_' + i).length <= 0)
              {
                temp = $('<input type="hidden" class="fullscreenEventPoint" data-eventType="evacuationPlan" data-pathsQt="' + pathsQt + '"data-polyColor="' + poly.options.color + '" data-polyIndex="' + polyIndex + '" id="<?= $_GET['name'] ?>_fullscreenEvent_' + i + '"/>');
                temp.val(JSON.stringify(poly._latlngs[i]));
                $('#' + widgetNameLocal + '_modalLinkOpen div.modalLinkOpenBody').append(temp);
              }
            }
        }
        
        function addEventToMap(eventLink, widgetName)
        {
           var planId = eventLink.attr("data-planid");
           var targetName, point, mapdiv, modalMapRef, path, polyline = null;
           var colors = ['red', 'blue', 'orange', 'green', 'yellow', 'black']; 
           
           if(plansOnMaps[widgetName]["noPointsUrl"] === null) 
           {
               targetName = widgetName + "_div";
               plansOnMaps[widgetName]["noPointsUrl"] = $("#" + targetName).attr("data-nopointsurl");
           }
           
           //Leaflet
           $("#" + widgetName + "_defaultMapDiv").hide();
           $("#" + widgetName + "_wrapper").hide();
           mapdiv = widgetName + "_mapDiv";
           
           //Creazione della mappa
            if(plansOnMaps[widgetName].mapRef !== null)
            {
               plansOnMaps[widgetName].mapRef.off();
               plansOnMaps[widgetName].mapRef.remove();
            }

            $("#" + widgetName + "_mapDiv").remove();
            $("#" + widgetName + "_content").append('<div id="' + widgetName + '_mapDiv" class="mapDiv"></div>');
            $("#" + widgetName + "_mapDiv").show();

            plansOnMaps[widgetName].mapRef = L.map(mapdiv).setView([43.769728, 11.255552], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
               maxZoom: 18
            }).addTo(plansOnMaps[widgetName].mapRef);
            plansOnMaps[widgetName].mapRef.attributionControl.setPrefix('');
            
            plansOnMaps[widgetName].shownPolyGroup = null;
            plansOnMaps[widgetName].shownPolyGroup = L.featureGroup(); 
           
           //Aggiungerla sempre qui
           $('#' + widgetName + '_modalLinkOpen input.fullscreenEventPoint').remove();
           
           for(var j = 0; j < plansObj[planId].payload.evacuation_paths.length; j++)
           {
               path = [];
               
               for(var i = 0; i < plansObj[planId].payload.evacuation_paths[j].coords.length; i++)
               {
                  point = [];
                  point[0] = plansObj[planId].payload.evacuation_paths[j].coords[i].latitude;
                  point[1] = plansObj[planId].payload.evacuation_paths[j].coords[i].longitude;
                  path.push(point);
               }
               polyline = L.polyline(path, {color: colors[j%6]});
               plansOnMaps[widgetName].shownPolyGroup.addLayer(polyline);
               
               updateFullscreenPointsList(widgetName, j, polyline, plansObj[planId].payload.evacuation_paths.length);
           }
           
           plansOnMaps[widgetName].shownPolyGroup.addTo(plansOnMaps[widgetName].mapRef);
           plansOnMaps[widgetName].mapRef.fitBounds(plansOnMaps[widgetName].shownPolyGroup.getBounds());
        }
        
        function removeEventFromMap(widgetName)
        {
           plansOnMaps[widgetName].mapRef.removeLayer(plansOnMaps[widgetName].shownPolyGroup);
           plansOnMaps[widgetName].shownPolyGroup = null;
           $("#" + widgetName + "_driverWidgetType").val("");
           $("#" + widgetName + "_netAnalysisServiceMapUrl").val("");
           $("#" + widgetName + "_buttonUrl").val("");
           $("#" + widgetName + "_recreativeEventsUrl").val("");
           $("#" + widgetName + "_wrapper").hide();
           $("#" + widgetName + "_mapDiv").hide();
           $("#" + widgetName + "_defaultMapDiv").show();
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
        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            
            widgetTargetList = JSON.parse(widgetProperties.param.parameters);
            manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
            
            for(var name in widgetTargetList) 
            {
               plansOnMaps[name] = {
                  noPointsUrl: null,
                  shownPolyGroup: null,
                  mapRef: null
               };
            }
            
            $.ajax({
               url: "../widgets/esbDao.php",
               type: "POST",
               data: {
                  operation: "getEvacuationPlans"
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
                  
                  $("#<?= $_GET['name'] ?>_noDataAlert").hide();
                  $('#<?= $_GET['name'] ?>_buttonsContainer').show();
                  $("#<?= $_GET['name'] ?>_rollerContainer").show();
                  
                  $("#<?= $_GET['name'] ?>_rollerContainer").height($("#<?= $_GET['name'] ?>_mainContainer").height() - 50); 
                  
                  plansNumber = Object.keys(data).length;
                  
                  if(plansNumber === 0)
                  {
                     $("#<?= $_GET['name'] ?>_table").css("display", "none"); 
                     $("#<?= $_GET['name'] ?>_noDataAlert").css("display", "block");
                  }
                  else
                  {
                     widgetWidth = $('#<?= $_GET['name'] ?>_div').width();
                     shownHeight = $("#<?= $_GET['name'] ?>_rollerContainer").prop("offsetHeight");
                     rowPercHeight =  75 * 100 / shownHeight;
                     contentHeightPx = plansNumber * 100;
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

                     //Inserimento una tantum degli eventi nell'apposito oggetto
                     var dataId = null;
                     for(var key in data)
                     {
                       var localPayload = JSON.parse(data[key].originalPayload);
                       data[key].payload = localPayload;
                       delete data[key].originalPayload;

                       dataId = data[key].data_id.replace("urn:rixf:gr.certh/evacuationplan_ID/", "");
                       
                       if((data[key].lastStatus === "") || (data[key].lastStatus === "null") || (data[key].lastStatus === null) || (data[key].lastStatus === undefined))
                       {
                          data[key].lastStatus = "PROPOSED";
                       }
                       
                       plansObj[dataId] = data[key];
                       
                       plansOrderedIds.push(dataId);
                     }
                     
                     plansOrderedIds.sort(function(a,b){
                        var itemA = new Date(plansObj[a].event_time); 
                        var itemB = new Date(plansObj[b].event_time);
                        if (itemA > itemB)
                        {
                           return -1;
                        }
                        else
                        {
                           if (itemA < itemB)
                           {
                              return 1;
                           }
                           else
                           {
                              return 0; 
                           }
                        }
                    });

                     populateWidget(false);
                     
                     //Abort evacuation plan status update
                     $("#modalChangePlanStatusCloseBtn").click(function(){
                        $("#modalChangePlanStatusConfirmBtn").off("click");
                        $("#modalChangePlanStatusCancelBtn").off("click");
                        $("#modalChangePlanStatusCloseBtn").off("click");
                        <?= $_GET['name'] ?>(false);
                        $("#modalChangePlanStatus").modal('hide');
                     });
                     
                     $("#modalChangePlanStatusCancelBtn").click(function(){
                        $("#modalChangePlanStatusConfirmBtn").off("click");
                        $("#modalChangePlanStatusCancelBtn").off("click");
                        $("#modalChangePlanStatusCloseBtn").off("click");
                        <?= $_GET['name'] ?>(false);
                        $("#modalChangePlanStatus").modal('hide');
                     });
                     
                     //Update evacuation plan status
                     $("#modalChangePlanStatusConfirmBtn").off("click");
                     $("#modalChangePlanStatusConfirmBtn").click(function(){
                        $("#modalChangePlanStatusMain").hide();
                        $("#modalChangePlanStatusFooter").hide();
                        $("#modalChangePlanStatusWait").show();
                        
                        var newStatus = $("#modalChangePlanStatusSelect").val();
                        var planId = $("#modalChangePlanStatusPlanId").val();
                        
                        $.ajax({
                           url: "https://www.resolute-eu.org/cxf/resolute/commands/plan/status/?consumerId=urn:rixf:org.disit/dashboard_manager&evacuationPlanId=urn:rixf:gr.certh/evacuationplan_ID/" + planId + "&status=" + newStatus,
                           type: "POST",
                           contentType: 'application/json', 
                           async: true,
                           success: function (result) 
                           {
                              clearInterval(intervalRef);
                              clearInterval(scroller);
                              $("#<?= $_GET['name'] ?>_rollerContainer").off();
                              $("#modalChangePlanStatusWait").hide();
                              $("#modalChangePlanStatusOk").show();
                              
                              setTimeout(function(){
                                $("#modalChangePlanStatus").modal('hide');
                                $("#modalChangePlanStatusConfirmBtn").off("click");
                                $("#modalChangePlanStatusCancelBtn").off("click");
                                $("#modalChangePlanStatusCloseBtn").off("click");
                                 
                               <?= $_GET['name'] ?>(false);
                              }, 2000);
                           },
                           error: function(result)
                           {
                              console.log("KO");
                              console.log(JSON.stringify(result));
                              
                              $("#modalChangePlanStatusWait").hide();
                              $("#modalChangePlanStatusKo").show();
                              
                              setTimeout(function(){
                                 $("#modalChangePlanStatus").modal('hide');
                                 <?= $_GET['name'] ?>(false);
                              }, 3000);
                           }
                        });
                        
                     });

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

                        $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).hover(
                           function()
                           {
                               $(this).css("cursor", "pointer");
                               $(this).css("color", "white");
                               $(this).find("img").attr("src", "../img/planIcons/decisionWhite.png");
                               switch(decisionSortState)
                               {
                                   case 0://Crescente verso il basso
                                       $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                       $(this).attr("title", "Sort by status - Ascending");
                                       break;

                                   case 1://Decrescente verso il basso
                                       $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                       $(this).attr("title", "Sort by status - Descending");
                                       break;

                                   case 2://No sort
                                       $(this).find("div.trafficEventsButtonIndicator").html('no sort');
                                       $(this).attr("title", "Sort by status - None");
                                       break;
                               }
                           }, 
                           function()
                           {
                               $(this).css("cursor", "auto");
                               $(this).css("color", "black");
                               $(this).find("img").attr("src", "../img/planIcons/decision.png");

                               switch(decisionSortState)
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
                                   timeSortState = 1;
                                   plansOrderedIds.sort(function(a,b) {
                                       var itemA = new Date(plansObj[a].event_time); 
                                       var itemB = new Date(plansObj[b].event_time);
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
                                    
                                    targetsArrayForNotify = [];
                                    
                                    //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                    localEventType = $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                    $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                    $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                                    $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                    $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                                    for(var widgetName in widgetTargetList) 
                                     {
                                        for(var key in widgetTargetList[widgetName]) 
                                        {
                                           if(widgetTargetList[widgetName][key] === localEventType)
                                           {
                                              targetsArrayForNotify.push(widgetName);
                                              $("#" + widgetName + "_wrapper").hide();
                                              $("#" + widgetName + "_mapDiv").show();
                                              addEventToMap($("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetName);
                                           }
                                        }
                                     }
                                     
                                    //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
                                    $.event.trigger({
                                        type: "esbEventAdded",
                                        generator: "<?= $_GET['name'] ?>",
                                        targetsArray: targetsArrayForNotify
                                    }); 
                                   
                                   $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                   break;

                               case 1:
                                   timeSortState = 2;
                                   plansOrderedIds.sort(function(a,b){
                                       var itemA = new Date(plansObj[a].event_time); 
                                       var itemB = new Date(plansObj[b].event_time);
                                       if (itemA > itemB)
                                       {
                                          return -1;
                                       }
                                       else
                                       {
                                          if (itemA < itemB)
                                          {
                                             return 1;
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

                                    for(var widgetName in widgetTargetList) 
                                     {
                                        for(var key in widgetTargetList[widgetName]) 
                                        {
                                           if(widgetTargetList[widgetName][key] === localEventType)
                                           {
                                              $("#" + widgetName + "_wrapper").hide();
                                              $("#" + widgetName + "_mapDiv").show();
                                              addEventToMap($("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetName);
                                           }
                                        }
                                     }
                                   
                                   $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                   break;

                               case 2:
                                   timeSortState = 0;
                                   plansOrderedIds.sort(function(a,b){
                                       var itemA = new Date(plansObj[a].event_time); 
                                       var itemB = new Date(plansObj[b].event_time);
                                       if (itemA > itemB)
                                       {
                                          return -1;
                                       }
                                       else
                                       {
                                          if (itemA < itemB)
                                          {
                                             return 1;
                                          }
                                          else
                                          {
                                             return 0; 
                                          }
                                       }
                                   });
                                   
                                   populateWidget(true);
                                   removeAllEventsFromMaps(false);
                                   
                                   $(this).find("div.trafficEventsButtonIndicator").html('');
                                   break;
                           }
                           
                           $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html('');
                           decisionSortState = 0;
                      });

                      $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).click(function(){
                           var localEventType = null;
                           
                           switch(decisionSortState)
                           {
                               case 0:
                                  decisionSortState = 1;
                                   plansOrderedIds.sort(function(a,b) {
                                       var itemA = plansObj[a].lastStatus; 
                                       var itemB = plansObj[b].lastStatus;
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
                                       return parseInt(a.id) - parseInt(b.id);
                                   });
                                   
                                   populateWidget(true);
                                   removeAllEventsFromMaps(true);
                                   
                                   targetsArrayForNotify = [];

                                    //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                    localEventType = $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                    $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                    $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                                    $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                    $("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                                    for(var widgetName in widgetTargetList) 
                                     {
                                        for(var key in widgetTargetList[widgetName]) 
                                        {
                                           if(widgetTargetList[widgetName][key] === localEventType)
                                           {
                                              targetsArrayForNotify.push(widgetName);
                                              $("#" + widgetName + "_wrapper").hide();
                                              $("#" + widgetName + "_mapDiv").show();
                                              addEventToMap($("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetName);
                                           }
                                        }
                                     }
                                     
                                     //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
                                    $.event.trigger({
                                        type: "esbEventAdded",
                                        generator: "<?= $_GET['name'] ?>",
                                        targetsArray: targetsArrayForNotify
                                    });
                                   
                                   $("#<?= $_GET['name'] ?>_decisionSortMsg").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                   break;

                               case 1:
                                   decisionSortState = 2;
                                   plansOrderedIds.sort(function(a,b){
                                       var itemA = plansObj[a].lastStatus; 
                                       var itemB = plansObj[b].lastStatus;
                                       if (itemA > itemB)
                                       {
                                          return -1;
                                       }
                                       else
                                       {
                                          if (itemA < itemB)
                                          {
                                             return 1;
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

                                    for(var widgetName in widgetTargetList) 
                                     {
                                        for(var key in widgetTargetList[widgetName]) 
                                        {
                                           if(widgetTargetList[widgetName][key] === localEventType)
                                           {
                                              $("#" + widgetName + "_wrapper").hide();
                                              $("#" + widgetName + "_mapDiv").show();
                                              addEventToMap($("#<?= $_GET['name'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetName);
                                           }
                                        }
                                     }
                                   
                                   $("#<?= $_GET['name'] ?>_decisionSortMsg").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                   break;

                               case 2:
                                   decisionSortState = 0;
                                   plansOrderedIds.sort(function(a,b){
                                       var itemA = parseInt(plansObj[a].id); 
                                       var itemB = parseInt(plansObj[b].id);
                                       return itemA - itemB;
                                   });
                                   
                                   populateWidget(true);
                                   removeAllEventsFromMaps(false);
                                   
                                   $("#<?= $_GET['name'] ?>_decisionSortMsg").html('');
                                   break;
                           }
                           
                           $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html('');
                           timeSortState = 0;
                      });
                        
                        scroller = setInterval(stepDownInterval, speed);
                        var timeToClearScroll = (timeToReload - 0.5) * 1000;

                        setTimeout(function()
                        {
                            clearInterval(scroller);
                            $("#<?= $_GET['name'] ?>_rollerContainer").off();
                            
                            //$(document).off("esbEventAdded");
                            
                            $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");
                            $("#<?= $_GET['name'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html("");
                            
                            //Ripristino delle homepage native per gli widget targets al reload, se pilotati per ultimi da questo widget
                            for(var widgetName in widgetTargetList) 
                            {
                               if($("#" + widgetName + "_driverWidgetType").val() === 'evacuationPlans')
                               {
                                   loadDefaultMap(widgetName);
                               }
                               else
                               {
                                   //console.log("Attualmente non pilotato da evacuationPlans");
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
        
        intervalRef = startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        
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
               <div id="<?= $_GET['name'] ?>_buttonsContainer" class="trafficEventsButtonsContainer centerWithFlex">
                   <div class="trafficEventsButtonContainer">
                       <div class="trafficEventsButtonIcon centerWithFlex">
                          <img src="../img/trafficIcons/time.png" />
                       </div>
                      <div class="trafficEventsButtonIndicator centerWithFlex"></div>                                         
                   </div>
                   <div class="trafficEventsButtonContainer">
                       <div class="trafficEventsButtonIcon centerWithFlex">
                           <img src="../img/planIcons/decision.png" />
                       </div>
                      <div id="<?= $_GET['name'] ?>_decisionSortMsg" class="trafficEventsButtonIndicator centerWithFlex"></div>                                           
                   </div>
               </div>
               <div id="<?= $_GET['name'] ?>_rollerContainer" class="trafficEventsRollerContainer"></div>
            </div>
        </div>
    </div>	
</div> 