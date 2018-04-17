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

<script type='text/javascript'>
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef)  
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
        var timeFontSize, scroller, widgetProperties, styleParameters, icon, serviceUri, 
            eventName, newRow, newIcon, eventContentW, test, widgetTargetList, backgroundTitleClass, backgroundFieldsClass,
            background, originalHeaderColor, originalBorderColor, eventTitle,
            eventName, serviceUri, eventTooltip, widgetParameters,
            eventsNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx, eventContentWPerc, dataContainer, middleContainer,
             mapPtrContainer, pinContainer, pinMsgContainer, 
            fontSizePin, dateFontSize, globalPayload, nameContainer, valueContainer = null;    
    
        var eventNames = new Array();
        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var speed = 50;
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_mainContainer");
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
        var widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var timeToReload = <?= $_REQUEST['frequency_w'] ?>;
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer");
        var url = "<?= $_REQUEST['link_w'] ?>";
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
		var showHeader = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var eventsOnMaps = {};
        var widgetTargetListFlags = [];
        var targetsArrayForNotify = [];
        
        if(url === "null")
        {
            url = null;
        }
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
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
           if(event.generator !== "<?= $_REQUEST['name_w'] ?>")
           {
              $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").each(function(i){
                 if($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap") === "true")
                 {
                    for(widgetName in widgetTargetList)
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap", "false");

                        $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                        $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).removeClass("onMapTrafficEventPinAnimated");
                        $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");

                        eventsOnMaps[widgetName].eventsNumber = 0; 
                        eventsOnMaps[widgetName].eventsPoints.splice(0);
                        eventsOnMaps[widgetName].mapRef = null;
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
               $('#' + widgetName + '_wrapper').hide();
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
           var name, value = null;
           $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').empty();
           
           var edgeMeasureName = globalPayload.edge_measures[0].name;
           var edgeMeasureDesc = globalPayload.edge_measures[0].description;
           var criticalEdges = globalPayload.edge_measures[0].critical_edges;
           var nodeMeasureName1 = globalPayload.node_measures[0].name;
           var nodeMeasureDesc1 = globalPayload.node_measures[0].description;
           var criticalNodes1 = globalPayload.node_measures[0].critical_nodes;
           var nodeMeasureName2 = globalPayload.node_measures[1].name;
           var nodeMeasureDesc2 = globalPayload.node_measures[1].description;
           var criticalNodes2 = globalPayload.node_measures[1].critical_nodes;
           
           //Nodi 2
           for(var index in criticalNodes2)
            {
               name = criticalNodes2[index].element_id;
               value = criticalNodes2[index].value;
               
               newRow = $('<div class="trafficEventRow"></div>');
               
               if(parseFloat(value) < 0.333)
               {
                  backgroundTitleClass = "lowSeverityTitle";
                  backgroundFieldsClass = "lowSeverity";//Giallo
                  background = "#ffcc00"; 
               }
               else
               {
                  if((parseFloat(value) < 0.666) && (parseFloat(value) >= 0.333))
                  {
                     backgroundTitleClass = "medSeverityTitle";
                     backgroundFieldsClass = "medSeverity";//Arancio
                     background = "#ff9900";
                  }
                  else
                  {
                      backgroundTitleClass = "highSeverityTitle";
                      backgroundFieldsClass = "highSeverity";//Rosso
                      background = "#ff6666";  
                  }
               }
               
             icon = $('<img src="../img/networkIcons/node.png "/>');
             
             newRow.css("height", rowPercHeight + "%");
             eventTitle = $('<div class="eventTitle centerWithFlex"><span>' + name + '</span></div>');
             eventTitle.addClass(backgroundTitleClass);
             eventTitle.css("font-weight", "bold");
             eventTitle.css("height", "30%");
             $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').append(newRow);

             newRow.append(eventTitle);

             dataContainer = $('<div class="trafficEventDataContainer"></div>');
             newRow.append(dataContainer);

             newIcon = $("<div class='trafficEventIcon centerWithFlex' data-toggle='tooltip' data-placement='top' title='Node'></div>");
             newIcon.append(icon); 
             newIcon.addClass(backgroundFieldsClass);
             dataContainer.append(newIcon);

             middleContainer = $("<div class='trafficEventMiddleContainer'></div>");
             
             nameContainer = $("<div class='networkNameContainer centerWithFlex' data-toggle='tooltip' data-placement='top' title='" + nodeMeasureDesc2 + "'>" + nodeMeasureName2.toLowerCase().replace("(normalized)", "") + "</div>"); 
             nameContainer.addClass(backgroundTitleClass);
             middleContainer.append(nameContainer);
             
             valueContainer = $("<div class='networkValueContainer centerWithFlex'>" + value + "</div>"); 
             valueContainer.addClass(backgroundTitleClass);
             middleContainer.append(valueContainer);

             dataContainer.append(middleContainer);

             mapPtrContainer = $("<div class='trafficEventMapPtr'></div>"); 
             mapPtrContainer.addClass(backgroundFieldsClass);

             pinContainer = $("<div class='trafficEventPinContainer'><a class='trafficEventLink' data-infotype='node2' data-background='" + background + "' data-serviceid='" + name + "' data-onMap='false'><i class='material-icons'>place</i></a></div>");
             mapPtrContainer.append(pinContainer);
             pinMsgContainer = $("<div class='trafficEventPinMsgContainer'></div>");
             mapPtrContainer.append(pinMsgContainer);

             dataContainer.append(mapPtrContainer);

             if(index < (eventsNumber - 1))
             {
                newRow.css("margin-bottom", "4px");
             }
             else
             {
                newRow.css("margin-bottom", "0px");
             }

             $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);
             
             //Interazione cross-widget
             pinContainer.find("a.trafficEventLink[data-infotype=node2]").hover(
                function() 
                {
                   var localBackground = $(this).attr("data-background");
                   originalHeaderColor = {};
                   originalBorderColor = {};

                   for(var index2 in widgetTargetList) 
                   {
                        originalHeaderColor[widgetTargetList[index2]] = $("#" + widgetTargetList[index2] + "_header").css("background-color");
                        originalBorderColor[widgetTargetList[index2]] = $("#" + widgetTargetList[index2]).css("border-color");
                        
                        if($(this).attr("data-onMap") === 'false')
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("show");
                        }
                        else
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("hide");
                        }

                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                        $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                        
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "white");

                        $("#" + widgetTargetList[index2] + "_header").css("background", localBackground);
                        $("#" + widgetTargetList[index2]).css("border-color", localBackground);
                   }
                }, 
                function() 
                {
                   for(var index3 in widgetTargetList) 
                   { 
                     if($(this).attr("data-onMap") === 'false')
                     {
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                     }
                     else
                     {
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                     }

                     $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                     $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                     
                     $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "black");

                     $("#" + widgetTargetList[index3] + "_header").css("background", originalHeaderColor[widgetTargetList[index3]]);
                     $("#" + widgetTargetList[index3]).css("border-color", originalBorderColor[widgetTargetList[index3]]);
                   }
                }
             );
             
             pinContainer.find("a.trafficEventLink[data-infotype=node2]").click(function()
             {  
                var goesOnMap = false;

                if($(this).attr("data-onMap") === 'false')
                {
                  $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventPinMsgContainer").each(function(){
                     $(this).html("");
                  });

                  $("#<?= $_REQUEST['name_w'] ?>_div a.trafficEventLink").each(function(){
                     $(this).removeClass("onMapTrafficEventPinAnimated");
                     $(this).attr("data-onMap", 'false');
                  });

                  $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventPinMsgContainer").each(function(){
                     $(this).removeClass("onMapTrafficEventPinAnimated");
                  });
                   
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

                for(var index4 in widgetTargetList) 
                {
                  if(goesOnMap)
                  {
                     targetsArrayForNotify.push(widgetTargetList[index4]);
                     addEventToMap($(this), widgetTargetList[index4], index4);
                  }
                  else
                  {
                     removeEventFromMap($(this), widgetTargetList[index4], index4);
                  } 
                }
                
                //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
               $.event.trigger({
                   type: "esbEventAdded",
                   generator: "<?= $_REQUEST['name_w'] ?>",
                   targetsArray: targetsArrayForNotify
               });
                
                
             });
            }//Fine del for per nodi 2
           
           //Nodi 1 
           for(var index in criticalNodes1)
            {
               name = criticalNodes1[index].element_id;
               value = criticalNodes1[index].value;
               
               newRow = $('<div class="trafficEventRow"></div>');
               
               backgroundTitleClass = "unclassifiedNodeTitle";
               backgroundFieldsClass = "unclassifiedNode";//Grigio
               background = "#d9d9d9"; 
               
               
             icon = $('<img src="../img/networkIcons/node.png "/>');
             eventTooltip = nodeMeasureDesc1;  
             
             newRow.css("height", rowPercHeight + "%");
             eventTitle = $('<div class="eventTitle centerWithFlex"><span>' + name + '</span></div>');
             eventTitle.addClass(backgroundTitleClass);
             eventTitle.css("font-weight", "bold");
             eventTitle.css("height", "30%");
             $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').append(newRow);

             newRow.append(eventTitle);

             dataContainer = $('<div class="trafficEventDataContainer"></div>');
             newRow.append(dataContainer);

             newIcon = $("<div class='trafficEventIcon centerWithFlex' data-toggle='tooltip' data-placement='top' title='Node'></div>");
             newIcon.append(icon); 
             newIcon.addClass(backgroundFieldsClass);
             dataContainer.append(newIcon);

             middleContainer = $("<div class='trafficEventMiddleContainer'></div>");
             
             nameContainer = $("<div class='networkNameContainer centerWithFlex' data-toggle='tooltip' data-placement='top' title='" + nodeMeasureDesc1 + "'>" + nodeMeasureName1.toLowerCase().replace("(normalized)", "") + "</div>"); 
             nameContainer.addClass(backgroundTitleClass);
             middleContainer.append(nameContainer);
             
             valueContainer = $("<div class='networkValueContainer centerWithFlex'>" + value + "</div>"); 
             valueContainer.addClass(backgroundTitleClass);
             middleContainer.append(valueContainer);

             dataContainer.append(middleContainer);

             mapPtrContainer = $("<div class='trafficEventMapPtr'></div>"); 
             mapPtrContainer.addClass(backgroundFieldsClass);

             pinContainer = $("<div class='trafficEventPinContainer'><a class='trafficEventLink' data-infotype='node1' data-background='" + background + "' data-serviceid='" + name + "' data-onMap='false'><i class='material-icons'>place</i></a></div>");
             mapPtrContainer.append(pinContainer);
             pinMsgContainer = $("<div class='trafficEventPinMsgContainer'></div>");
             mapPtrContainer.append(pinMsgContainer);

             dataContainer.append(mapPtrContainer);

             if(index < (eventsNumber - 1))
             {
                newRow.css("margin-bottom", "4px");
             }
             else
             {
                newRow.css("margin-bottom", "0px");
             }

             $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);
             
             //Interazione cross-widget
             pinContainer.find("a.trafficEventLink[data-infotype=node1]").hover(
                function() 
                {
                   var localBackground = $(this).attr("data-background");
                   originalHeaderColor = {};
                   originalBorderColor = {};

                   for(var index2 in widgetTargetList) 
                   {
                        originalHeaderColor[widgetTargetList[index2]] = $("#" + widgetTargetList[index2] + "_header").css("background-color");
                        originalBorderColor[widgetTargetList[index2]] = $("#" + widgetTargetList[index2]).css("border-color");
                        
                        if($(this).attr("data-onMap") === 'false')
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("show");
                        }
                        else
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("hide");
                        }

                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                        $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                        
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "white");

                        $("#" + widgetTargetList[index2] + "_header").css("background", localBackground);
                        $("#" + widgetTargetList[index2]).css("border-color", localBackground);
                   }
                }, 
                function() 
                {
                   for(var index3 in widgetTargetList) 
                   { 
                     if($(this).attr("data-onMap") === 'false')
                     {
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                     }
                     else
                     {
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                     }

                     $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                     $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                     
                     $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "black");

                     $("#" + widgetTargetList[index3] + "_header").css("background", originalHeaderColor[widgetTargetList[index3]]);
                     $("#" + widgetTargetList[index3]).css("border-color", originalBorderColor[widgetTargetList[index3]]);
                   }
                }
             );
             
             pinContainer.find("a.trafficEventLink[data-infotype=node1]").click(function()
             {  
                var goesOnMap = false;

                if($(this).attr("data-onMap") === 'false')
                {
                  $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventPinMsgContainer").each(function(){
                     $(this).html("");
                  });

                  $("#<?= $_REQUEST['name_w'] ?>_div a.trafficEventLink").each(function(){
                     $(this).removeClass("onMapTrafficEventPinAnimated");
                     $(this).attr("data-onMap", 'false');
                  });

                  $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventPinMsgContainer").each(function(){
                     $(this).removeClass("onMapTrafficEventPinAnimated");
                  });
                   
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

                for(var index4 in widgetTargetList) 
                {
                  if(goesOnMap)
                  {
                     targetsArrayForNotify.push(widgetTargetList[index4]);
                     addEventToMap($(this), widgetTargetList[index4], index4);
                  }
                  else
                  {
                     removeEventFromMap($(this), widgetTargetList[index4], index4);
                  } 
                }
                
                //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
               $.event.trigger({
                   type: "esbEventAdded",
                   generator: "<?= $_REQUEST['name_w'] ?>",
                   targetsArray: targetsArrayForNotify
               });
               
             });
            }  //Fine del for per nodi 1
           
           
           //Archi 
           for(var index in criticalEdges)
            {
               name = criticalEdges[index].element_id;
               value = criticalEdges[index].value;
               
               newRow = $('<div class="trafficEventRow"></div>');
               
               if(parseFloat(value) < 0.333)
               {
                  backgroundTitleClass = "lowSeverityTitle";
                  backgroundFieldsClass = "lowSeverity";//Giallo
                  background = "#ffcc00"; 
               }
               else
               {
                  if((parseFloat(value) < 0.666) && (parseFloat(value) >= 0.333))
                  {
                     backgroundTitleClass = "medSeverityTitle";
                     backgroundFieldsClass = "medSeverity";//Arancio
                     background = "#ff9900";
                  }
                  else
                  {
                      backgroundTitleClass = "highSeverityTitle";
                      backgroundFieldsClass = "highSeverity";//Rosso
                      background = "#ff6666";  
                  }
               }
               
             icon = $('<img src="../img/networkIcons/edge.png "/>');
             eventTooltip = edgeMeasureDesc;  
             
             newRow.css("height", rowPercHeight + "%");
             eventTitle = $('<div class="eventTitle centerWithFlex"><span>' + name + '</span></div>');
             eventTitle.addClass(backgroundTitleClass);
             eventTitle.css("font-weight", "bold");
             eventTitle.css("height", "30%");
             $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').append(newRow);

             newRow.append(eventTitle);

             dataContainer = $('<div class="trafficEventDataContainer"></div>');
             newRow.append(dataContainer);

             newIcon = $("<div class='trafficEventIcon centerWithFlex' data-toggle='tooltip' data-placement='top' title='Edge'></div>");
             newIcon.append(icon); 
             newIcon.addClass(backgroundFieldsClass);
             dataContainer.append(newIcon);

             middleContainer = $("<div class='trafficEventMiddleContainer'></div>");
             
             nameContainer = $("<div class='networkNameContainer centerWithFlex' data-toggle='tooltip' data-placement='top' title='" + edgeMeasureDesc + "'>" + edgeMeasureName.toLowerCase().replace("(normalized)", "") + "</div>"); 
             nameContainer.addClass(backgroundTitleClass);
             middleContainer.append(nameContainer);
             
             valueContainer = $("<div class='networkValueContainer centerWithFlex'>" + value + "</div>"); 
             valueContainer.addClass(backgroundTitleClass);
             middleContainer.append(valueContainer);

             dataContainer.append(middleContainer);

             mapPtrContainer = $("<div class='trafficEventMapPtr'></div>"); 
             mapPtrContainer.addClass(backgroundFieldsClass);

             pinContainer = $("<div class='trafficEventPinContainer'><a class='trafficEventLink' data-infotype='edge' data-background='" + background + "' data-serviceid='" + name + "' data-onMap='false'><i class='material-icons'>place</i></a></div>");
             mapPtrContainer.append(pinContainer);
             pinMsgContainer = $("<div class='trafficEventPinMsgContainer'></div>");
             mapPtrContainer.append(pinMsgContainer);

             dataContainer.append(mapPtrContainer);

             if(index < (eventsNumber - 1))
             {
                newRow.css("margin-bottom", "4px");
             }
             else
             {
                newRow.css("margin-bottom", "0px");
             }

             $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);
             
             //Interazione cross-widget
             pinContainer.find("a.trafficEventLink[data-infotype=edge]").hover(
                function() 
                {
                   var localBackground = $(this).attr("data-background");
                   originalHeaderColor = {};
                   originalBorderColor = {};

                   for(var index2 in widgetTargetList) 
                   {
                        originalHeaderColor[widgetTargetList[index2]] = $("#" + widgetTargetList[index2] + "_header").css("background-color");
                        originalBorderColor[widgetTargetList[index2]] = $("#" + widgetTargetList[index2]).css("border-color");
                        
                        if($(this).attr("data-onMap") === 'false')
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("show");
                        }
                        else
                        {
                           $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("hide");
                        }

                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                        $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                        
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "white");

                        $("#" + widgetTargetList[index2] + "_header").css("background", localBackground);
                        $("#" + widgetTargetList[index2]).css("border-color", localBackground);
                   }
                }, 
                function() 
                {
                   for(var index3 in widgetTargetList) 
                   { 
                     if($(this).attr("data-onMap") === 'false')
                     {
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                     }
                     else
                     {
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("on map");
                     }

                     $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                     $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                     
                     $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "black");

                     $("#" + widgetTargetList[index3] + "_header").css("background", originalHeaderColor[widgetTargetList[index3]]);
                     $("#" + widgetTargetList[index3]).css("border-color", originalBorderColor[widgetTargetList[index3]]);
                   }
                }
             );
             
             pinContainer.find("a.trafficEventLink[data-infotype=edge]").click(function()
             {  
                var goesOnMap = false;

                if($(this).attr("data-onMap") === 'false')
                {
                  $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventPinMsgContainer").each(function(){
                     $(this).html("");
                  });

                  $("#<?= $_REQUEST['name_w'] ?>_div a.trafficEventLink").each(function(){
                     $(this).removeClass("onMapTrafficEventPinAnimated");
                     $(this).attr("data-onMap", 'false');
                  });

                  $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventPinMsgContainer").each(function(){
                     $(this).removeClass("onMapTrafficEventPinAnimated");
                  });
                   
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

                for(var index4 in widgetTargetList) 
                {
                  if(goesOnMap)
                  {
                     targetsArrayForNotify.push(widgetTargetList[index4]);
                     addEventToMap($(this), widgetTargetList[index4], index4);
                  }
                  else
                  {
                     removeEventFromMap($(this), widgetTargetList[index4], index4);
                  } 
                }
                
                //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
               $.event.trigger({
                   type: "esbEventAdded",
                   generator: "<?= $_REQUEST['name_w'] ?>",
                   targetsArray: targetsArrayForNotify
               });
             });

            }//Fine del for per gli archi
            
            var maxTitleFontSize = $('div.eventTitle').height()*0.75;

            if(maxTitleFontSize > fontSize)
            {
                maxTitleFontSize = fontSize;
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer p.eventTitlePar span').css("font-size", maxTitleFontSize + "px");
            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .networkNameContainer').eq(0).width()*0.07;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .networkNameContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .networkValueContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventLink i').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width()/1.5) + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinMsgContainer').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width()/2.846) + "px");
            
            var btnIndicatorFontSize = $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").eq(0).width()/48.4375;
            $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").css("font-size", btnIndicatorFontSize + "em");
            
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer [data-toggle="tooltip"]').tooltip({
               html: true
            });
        }
        
        function addEventToMap(eventLink, widgetName, index)
        {
           var serviceId = eventLink.attr("data-serviceid");
           var serviceMapUrl = "<?php echo $serviceMapUrlPrefix; ?>" + "api/v1/?format=html&maxDists=0.03&categories=BusStop;PublicTransportLine&selection=https://www.disit.org/km4city/resource/" + serviceId;
           
           $("#" + widgetName + "_mapDiv").hide();
           $("#" + widgetName + "_defaultMapDiv").hide();
           $("#" + widgetName + "_wrapper").show();
           $("#" + widgetName + "_iFrame").attr("src", serviceMapUrl);
           $("#" + widgetName + "_driverWidgetType").val("newtworkAnalysis");
           $("#" + widgetName + "_netAnalysisServiceMapUrl").val(serviceMapUrl + "&controls=show");
           widgetTargetListFlags[index] = true;
        }
        
        function removeEventFromMap(eventLink, widgetName, index)
        {
           $("#" + widgetName + "_wrapper").hide();
           widgetTargetListFlags[index] = false;
           $("#" + widgetName + "_driverWidgetType").val("");
           $("#" + widgetName + "_netAnalysisServiceMapUrl").val("");
           $("#" + widgetName + "_buttonUrl").val("");
           $("#" + widgetName + "_recreativeEventsUrl").val("");
           $("#" + widgetName + "_defaultMapDiv").show();
           $("#" + widgetName + "_driverWidgetType").val("newtworkAnalysis");
           $("#" + widgetName + "_netAnalysisServiceMapUrl").val("");
        }
        
        function stepDownInterval()
        {
            var oldPos = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop();
            var newPos = oldPos + 1;
            
            var oldScrollTop = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop();
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(newPos);
            var newScrollTop = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop();
            
            if(oldScrollTop === newScrollTop)
            {
               $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);
            }
        }
        
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            
            var btnIndicatorFontSize = $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").eq(0).width()/48.4375;
            $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").css("font-size", btnIndicatorFontSize + "em");
            
            shownHeight = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").prop("offsetHeight");
            rowPercHeight =  75 * 100 / shownHeight;
            
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer div.trafficEventRow').css("height", rowPercHeight + "%");
            var maxTitleFontSize = $('div.eventTitle').height()*0.75;
            
            if(maxTitleFontSize > fontSize)
            {
                maxTitleFontSize = fontSize;
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer p.eventTitlePar span').css("font-size", maxTitleFontSize + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer p.eventTitlePar span').css("font-size", maxTitleFontSize + "px");
            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .networkNameContainer').eq(0).width()*0.07;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .networkNameContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .networkValueContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventLink i').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width()/1.5) + "px");
            
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinMsgContainer').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width()/2.846) + "px");
            
            clearInterval(scroller);    
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);
            scroller = setInterval(stepDownInterval, speed);
        }
		
		$(document).off('resizeHighchart_' + widgetName);
		$(document).on('resizeHighchart_' + widgetName, function(event) 
		{
			var newHeight = null;
			if($('#<?= $_REQUEST['name_w'] ?>_header').is(':visible'))
			{
				newHeight = $('#<?= $_REQUEST['name_w'] ?>').height() - $('#<?= $_REQUEST['name_w'] ?>_header').height();
			}
			else
			{
				newHeight = $('#<?= $_REQUEST['name_w'] ?>').height();
			}
			
			$('#<?= $_REQUEST['name_w'] ?>_rollerContainer').css('height', newHeight + 'px');
		});
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer").css("background-color", $("#<?= $_REQUEST['name_w'] ?>_header").css("background-color"));
        
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        
        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_REQUEST['name_w'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
        
        //Fine eventuale codice ad hoc basato sulle proprietà del widget
        
        //Nuova versione
        if(('<?= $_REQUEST['styleParameters'] ?>' !== "")&&('<?= $_REQUEST['styleParameters'] ?>' !== "null"))
        {
            styleParameters = JSON.parse('<?= $_REQUEST['styleParameters'] ?>');
        }
        
        if('<?= $_REQUEST['parameters'] ?>'.length > 0)
        {
            widgetParameters = JSON.parse('<?= $_REQUEST['parameters'] ?>');
        }

        widgetTargetList = widgetParameters;
        if((widgetTargetList !== null)&&(widgetTargetList !== undefined))
        {
           for(var i = 0; i < widgetTargetList.length; i++)
           {
              widgetTargetListFlags[i] = false;   
           }
        }

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
              operation: "getNetworkAnalysis"
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

              //Inserimento una tantum degli eventi nell'apposito array (per ordinamenti)
              if(data.length === 0)
              {
                  $('#<?= $_REQUEST['name_w'] ?>_buttonsContainer').hide();
                  $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").hide(); 
                  $("#<?= $_REQUEST['name_w'] ?>_noDataAlert").show();
              }
              else
              {
                globalPayload = JSON.parse(data.payload); 
                eventsNumber = globalPayload.edge_measures[0].critical_edges.length + globalPayload.node_measures[0].critical_nodes.length + globalPayload.node_measures[1].critical_nodes.length;
                $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').show();
                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").css("height", "100%");
                widgetWidth = $('#<?= $_REQUEST['name_w'] ?>_div').width();
                shownHeight = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").prop("offsetHeight");
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

                populateWidget();

                scroller = setInterval(stepDownInterval, speed);
                var timeToClearScroll = (timeToReload - 0.5) * 1000;

                setTimeout(function()
                {
                      clearInterval(scroller);
                      $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").off();

                      //$(document).off("esbEventAdded");

                      //Ripristino delle mappe native per gli widget targets al reload
                      var wName = null;
                      for(var i in widgetTargetList) 
                      {
                         /*wName = widgetTargetList[i];
                         if(widgetTargetListFlags[i] === true)
                         {
                            $("#" + wName + "_iFrame").attr("src", $("#" + wName + "_iFrame").attr("data-oldsrc"));
                         }*/

                        if($("#" + widgetTargetList[i] + "_driverWidgetType").val() === 'newtworkAnalysis')
                        {
                            loadDefaultMap(widgetTargetList[i]);
                        }
                        else
                        {
                            //console.log("Attualmente non pilotato da newtworkAnalysis");
                        }
                      }

                }, timeToClearScroll);


               $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").mouseenter(function() 
               {
                   clearInterval(scroller);
               });

               $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").mouseleave(function()
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
              $("#<?= $_REQUEST['name_w'] ?>_table").css("display", "none"); 
              $("#<?= $_REQUEST['name_w'] ?>_noDataAlert").css("display", "block");
           }
        });
        
        
        startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
    });//Fine document ready
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
	    <?php include '../widgets/widgetHeader.php'; ?>
		<?php include '../widgets/widgetCtxMenu.php'; ?>
        <!--<div id='<?= $_REQUEST['name_w'] ?>_header' class="widgetHeader">
            <div id="<?= $_REQUEST['name_w'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_REQUEST['name_w'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
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
            <div id="<?= $_REQUEST['name_w'] ?>_mainContainer" class="chartContainer">
               <div id="<?= $_REQUEST['name_w'] ?>_rollerContainer" class="trafficEventsRollerContainer"></div>
            </div>
        </div>
    </div>	
</div> 