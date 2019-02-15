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
<script type="text/javascript" src="../js/heatmap/heatmap.js"></script>
<script type="text/javascript" src="../js/heatmap/leaflet-heatmap.js"></script>
<script src="../trafficRTDetails/js/leaflet.awesome-markers.min.js"></script>
<script src="../trafficRTDetails/js/jquery.dialogextend.js"></script>
<script src="../trafficRTDetails/js/leaflet-gps.js"></script>
<script src="../trafficRTDetails/js/wicket.js"></script>
<script src="../trafficRTDetails/js/wicket-leaflet.js"></script>
<script src="../trafficRTDetails/js/date.format.js"></script>
<script src="../trafficRTDetails/js/zoomHandler.js"></script>
<!-- jQuery -->
<script src="ol/ol.js"></script>
<link rel="stylesheet" href="ol/ol.css" />
<!-- layerSwitcher -->
<!--<script src="node_modules/ol-layerswitcher/dist/ol-layerswitcher.js"></script>-->
<!--<link rel="stylesheet" href="node_modules/ol-layerswitcher/src/ol-layerswitcher.css" />-->
<!-- Style -->
<style>
   /***/
   .ol-popup {
   min-width: 200px;
   background-color: white;
   -webkit-filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
   filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
   border: 1px solid #ccc;
   bottom: 12px;
   left: -200px;
   }
   /***/      
   .ol-popup:after, .ol-popup:before {
   top: 100%;
   border: solid transparent;
   content: " ";
   height: 0;
   width: 0;
   position: absolute;
   pointer-events: none;
   }
   .ol-popup:after {
   border-top-color: white;
   border-width: 10px;
   left: 218px;
   margin-left: -10px;
   }
   .ol-popup:before {
   border-top-color: #cccccc;
   border-width: 11px;
   left: 218px;
   margin-left: -11px;
   }
   .ol-popup-closer {
   text-decoration: none;
   position: absolute;
   top: 2px;
   right: 8px;
   color: #c3c3c3;
   }

   .ol-popup-closer:after {
   content: "✖";
   font-weight: bold;
   color: #c3c3c3;
   }
 
   .ol-popup-closer:hover {     
   text-decoration: none;
   }
        
   .layer{
   font-family: Arial,Helvetica Neue,Helvetica,sans-serif; 
   }	
</style>
<!-- -->
<script type='text/javascript'>
   $(document).ready(function iframe(firstLoad) 
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
               
       var hostFile = "<?= $_REQUEST['hostFile'] ?>";
       var widgetName = "<?= $_REQUEST['name_w'] ?>";
       var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
       var widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
       var widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
       var currentZoom = "<?= $_REQUEST['zoomFactor'] ?>";
       var showTitle = "<?= $_REQUEST['showTitle'] ?>";
       var controlsVisibility = "<?= $_REQUEST['controlsVisibility'] ?>";
       var wrapperW = $('#<?= $_REQUEST['name_w'] ?>_div').outerWidth();
       var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
       var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';	
       var actuatorAttribute = '<?= $_REQUEST['actuatorAttribute'] ?>';
       var headerHeight = 25;
       var wsRetryActive, wsRetryTime = null;
       var showTitle = "<?= $_REQUEST['showTitle'] ?>";
       var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
       var showHeader = null;
       var widgetProperties, styleParameters, topWrapper, height, zoomDisplayTimeout, wrapperH, titleWidth, sourceMapDivCopy, sourceIfram, 
           fullscreenMapRef, fullscreenDefaultMapRef, minLat, minLng, maxLat, maxLng, lat, lng, eventType, eventName, eventStartDate, eventStartTime, eventSeverity,
           mapPinImg, severityColor, pinIcon, markerLocation, marker, popupText, lastPopup, dataArray, eventSubtype, evtTypeForMaxZoom, pathsQt, gisMapRef,
           gisFullscreenMapRef, gisFullscreenMapCenter, gisFullscreenMapStartZoom, gisFullscreenMapStartBounds, widgetParameters, newpopup,
           serviceMapTimeRange, coordsCollectionUri, webSocket, openWs, manageIncomingWsMsg, openWsConn, wsClosed, metricName, defaultMapRef, eventsMapRef, webSocketReconnectionInterval, hiddenDiv = null;
   
       var gisLayersOnMap = {};
       var gisGeometryLayersOnMap = {};
       //var gisGeometryServiceUriToShowFullscreen = {};
       var markersCache = {};
       var stopGeometryAjax = {};
       var gisGeometryTankForFullscreen = {};
       var checkTankInterval = null;
       
       if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
       {
           showHeader = false;
       }
       else
       {
           showHeader = true;
       }
       
       //Definizioni di funzione specifiche del widgetfunction setAutoFontSize(container)
       
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
       
       //Va aggiornata con showWidgetContent
       function iframeLoaded(event)
       {
           $('#<?= $_REQUEST['name_w'] ?>_loading').css("display", "none");
           $('#<?= $_REQUEST['name_w'] ?>_wrapper').css("width", "100%");
           
           if($('#<?= $_REQUEST['name_w'] ?>_header').is(':visible'))
    {
               $("#<?= $_REQUEST['name_w'] ?>_wrapper").css("height", parseInt($("#<?= $_REQUEST['name_w'] ?>").height() - $('#<?= $_REQUEST['name_w'] ?>_header').height()) + "px");
           }
           else
           {
               $("#<?= $_REQUEST['name_w'] ?>_wrapper").css("height", "100%");
           }
           
           if((controlsVisibility === 'alwaysVisible') && (hostFile === 'config'))
           {
               $('#<?= $_REQUEST['name_w'] ?>_zoomControls').css("display", "block");
           }
           var target = document.getElementById('<?= $_REQUEST['name_w'] ?>_iFrame');
           target.contentWindow.postMessage(currentZoom, '*');
           
           $("#<?= $_REQUEST['name_w'] ?>_content").contents().find("body").css("transform-origin", "0% 0%");
           
           if(firstLoad !== false)
           {
               $('#<?= $_REQUEST['name_w'] ?>_loading').css("display", "none");
               $('#<?= $_REQUEST['name_w'] ?>_wrapper').css("display", "block");
           }
       }
       
       function createFullscreenModal()
       {
           var fullscreenModal = $('<div class="modal fade" tabindex="-1" id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen" class="modalLinkOpen" role="dialog" aria-labelledby="myModalLabel">' +
               '<div class="modal-dialog" role="document">' +  
                   '<div class="modal-content">' +
                       '<div class="modal-header centerWithFlex">' +
                           '<h4 class="modal-title"></h4>' +
                       '</div>' +
                       '<div class="modal-body">' +
                           '<div class="modalLinkOpenBody">' + 
                               '<iframe class="modalLinkOpenBodyIframe"></iframe>' +
                               '<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyMap" class="modalLinkOpenBodyMap" data-mapRef="null"></div>' +
                               '<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap" class="modalLinkOpenBodyDefaultMap" data-mapRef="null"></div>' +
                               //'<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisMap" class="modalLinkOpenGisMap" data-mapRef="null"></div>' +
                                '<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisMap" class="modalLinkOpenGisMap leaflet-container leaflet-touch leaflet-fade-anim leaflet-grab leaflet-touch-drag leaflet-touch-zoom" data-mapRef="null"></div>' +
                               //modalLinkOpenGisMap leaflet-container leaflet-touch leaflet-fade-anim leaflet-grab leaflet-touch-drag leaflet-touch-zoom
                               '<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend" class="modalLinkOpenGisTimeTrend"></div>' +
                           '</div>' +
                       '</div>' +
                       '<div class="modal-footer">' +
                           '<button type="button" id="<?= $_REQUEST['name_w'] ?>_modalLinkOpenCloseBtn" class="btn btn-primary">Back to dashboard</button>' +
                       '</div>' +
                   '</div>' +
               '</div>' +
           '</div>');
           //AGGIUNGERE IL OPENLAYER//
           /////
           fullscreenModal.insertAfter("#newTabLinkOpenImpossibile");
           fullscreenModal.css("font-family", "Verdana");
           fullscreenModal.find("div.modal-dialog").css("width", "95vw");
           fullscreenModal.find("div.modalLinkOpenBody").css("height", "80vh");
           
           fullscreenModal.find("iframe").css("width", "100%");
           fullscreenModal.find("iframe").css("height", "100%");
           fullscreenModal.find("iframe").hide();
           
           fullscreenModal.find("div.modalLinkOpenBodyMap").css("width", "100%");
           fullscreenModal.find("div.modalLinkOpenBodyMap").css("height", "100%");
           fullscreenModal.find("div.modalLinkOpenBodyMap").hide();
           
           fullscreenModal.find("div.modalLinkOpenBodyDefaultMap").css("width", "100%");
           fullscreenModal.find("div.modalLinkOpenBodyDefaultMap").css("height", "100%");
           fullscreenModal.find("div.modalLinkOpenBodyDefaultMap").hide();
           
           fullscreenModal.find("div.modalLinkOpenGisMap").css("width", "100%");
           fullscreenModal.find("div.modalLinkOpenGisMap").css("height", "100%");
           fullscreenModal.find("div.modalLinkOpenGisMap").css("-webkit-transition", "height 0.5s");
           fullscreenModal.find("div.modalLinkOpenGisMap").css("transition", "height 0.5s");
           fullscreenModal.find("div.modalLinkOpenGisMap").hide();
           
           fullscreenModal.find("div.modalLinkOpenGisTimeTrend").css("width", "100%");
           fullscreenModal.find("div.modalLinkOpenGisTimeTrend").css("height", "0vh");
           fullscreenModal.find("div.modalLinkOpenGisTimeTrend").css("background", "white");
           fullscreenModal.find("div.modalLinkOpenGisTimeTrend").hide();
           
           $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenCloseBtn").off();
           $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenCloseBtn").click(function(){
               if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").is(":visible"))
               {
                   fullscreenDefaultMapRef.off();
                   fullscreenDefaultMapRef.remove(); 
               }
       
               if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").is(":visible"))
               {
                   fullscreenMapRef.off();
                   fullscreenMapRef.remove(); 
               }
               
               if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").is(":visible"))
               {
                   for(var key in gisGeometryTankForFullscreen)
                   {
                       gisGeometryTankForFullscreen[key].shown = false;
                       gisGeometryTankForFullscreen[key].lastConsumedIndex = 0;
                   }
                   
                   clearInterval(checkTankInterval);
                   gisFullscreenMapRef.off();
                   gisFullscreenMapRef.remove();
               }
               
               fullscreenModal.find("div.modalLinkOpenGisMap").css("height", "80vh");
               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("height", "0vh");
               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").hide();
               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
               
               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen").modal('hide');
               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").modal('hide');
               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").modal('hide');
           });
       }
       
       //Creazione della mappa di default per il widget, non per il suo popup fullscreen
       function loadDefaultMap()
       {
           var mapDivLocal = "<?= $_REQUEST['name_w'] ?>_defaultMapDiv";
   /*
           defaultMapRef = L.map(mapDivLocal).setView([43.769789, 11.255694], 11);
   
           L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
              maxZoom: 18
           }).addTo(defaultMapRef);
           defaultMapRef.attributionControl.setPrefix('');*/
           /*MAPPA DI DEFAULT OL*/            
                       
                      var raster = new ol.layer.Group({
   			layers:[
                                                               new ol.layer.Tile({
                                                               source: new ol.source.OSM({
                                                                       imagerySet: 'Aerial',
                                                                        type: 'base',
                                                                })
                                                       })]
                                    });          
   var map = new ol.Map({
   		layers: [raster],
   		target: document.getElementById('<?= $_REQUEST['name_w'] ?>_defaultMapDiv'),
   		view: new ol.View({
   			center: ol.proj.transform( [11.255694, 43.769789] , 'EPSG:4326', 'EPSG:3857'),
                                                       //center: ol.proj.transform(OLlatLng,'EPSG:4326', 'EPSG:3857'),
                                                       //
   			maxZoom: 18,
                                                       zoom: 18
   		})
   	});
           /**/
       }
       
       //Creazione della mappa vuota per il widget in modalità GIS target, non per il suo popup fullscreen
       
       //Funzione eseguita per ciascuna feature della mappa GIS dopo che le feature vengano aggiunte - Per ora non usata
       
       //NON USATA MA NON CANCELLARLA (UTILE COME SOLUZIONE DI BACKUP SE LA REGEX FUNZIONA MALE), FILTRAGGIO FATTO A MONTE IN CHIAMATA A SERVICE MAP - Funzione che decide se una singola feature dev'essere aggiunta (ritorna true) o no (ritorna false) alla mappa
       function gisFilterOutOfBoundsPoints(feature)
       {
           var lng = feature.geometry.coordinates[0];
           var lat = feature.geometry.coordinates[1];
           var mapBounds = gisMapRef.getBounds();
           var minLat = parseFloat(mapBounds["_southWest"].lat);
           var maxLat = parseFloat(mapBounds["_northEast"].lat);
           var minLng = parseFloat(mapBounds["_southWest"].lng);
           var maxLng = parseFloat(mapBounds["_northEast"].lng);
           
           if((lat >= minLat)&&(lat <= maxLat)&&(lng >= minLng)&&(lng <= maxLng))
           {
               return true;
           }
           else
           {
               console.log("MinLat: " + minLat + " - MaxLat: " + maxLat + "MinLng: " + minLng + " - MaxLng: " + maxLng);
               console.log("Feature scartata: " + feature.properties.name + " - Lat: " + lat + " - Lng: " + lng);
               return false;
           }
       }
       
       
       
       function resizeWidget()
       {
           setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
           
           //Settaggio header
           if((hostFile === "index") && (showHeader === false))
           {
               $('#<?= $_REQUEST['name_w'] ?>_header').css("display", "none");
               height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
               wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight());
               topWrapper = "0px";
           }
           else
           {
               if((hostFile === "index")&&(showTitle === "no")&&(showHeader === true))
               {
                   $('#<?= $_REQUEST['name_w'] ?>_header').css("display", "none");
                   height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
                   wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight());
                   topWrapper = "0px";
               }
               else
               {
                   if(showHeader)
                   {
                       $('#<?= $_REQUEST['name_w'] ?>_header').css("display", "block");
                       height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight") - 25);
                       wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight() - 25);
                       topWrapper = "25px";
                   }
                   else
                   {
                       $('#<?= $_REQUEST['name_w'] ?>_header').css("display", "none");
                       height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
                       wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight());
                       topWrapper = "0px";
                   }
                   $("#" + widgetName + "_buttonsDiv").css("display", "none");
                   $("#" + widgetName + "_buttonsDiv").css("height", "100%");
                   $("#" + widgetName + "_buttonsDiv").css("float", "left");
                   $("#" + widgetName + "_buttonsDiv").css("display", "none");
                   $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).css("font-size", "20px");
                   $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).hover(function(){$(this).find("span").css("color", "red");}, function(){$(this).find("span").css("color", widgetHeaderFontColor);});
                   $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).css("font-size", "20px");
                   $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).hover(function(){$(this).find("span").css("color", "red");}, function(){$(this).find("span").css("color", widgetHeaderFontColor);});
   
                   if(hostFile === "config")
                   {
                       if((widgetProperties.param.enableFullscreenModal === 'yes')&&(widgetProperties.param.enableFullscreenTab === 'yes'))
                       {
                          $("#" + widgetName + "_buttonsDiv").css("width", "50px");
                          titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 25 - 2));
                          $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                          $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                           $("#" + widgetName + "_buttonsDiv").css("display", "none");
                       }
                       else
                       {
                          if((widgetProperties.param.enableFullscreenModal === 'yes')&&(widgetProperties.param.enableFullscreenTab === 'no'))
                          {
                              $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                              titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 25 - 2));
                              $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                              $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();
                              $("#" + widgetName + "_buttonsDiv").css("display", "none");
                          }
                          else
                          {
                             if((widgetProperties.param.enableFullscreenModal === 'no')&&(widgetProperties.param.enableFullscreenTab === 'yes')) 
                             {
                               $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                               titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 25 - 2));
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                               $("#" + widgetName + "_buttonsDiv").css("display", "none");
                             }
                             else
                             {
                               $("#" + widgetName + "_buttonsDiv").css("width", "0px");
                               titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 0 - 25 - 2));
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();
                               $("#" + widgetName + "_buttonsDiv").hide();
                             }
                          }
                       }
                   }
                   else
                   {
                      if((widgetProperties.param.enableFullscreenTab === 'yes')&&(widgetProperties.param.enableFullscreenModal === 'yes'))
                       {
                          $("#" + widgetName + "_buttonsDiv").css("width", "50px");
                          titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 2));
                          $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                          $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                          $("#" + widgetName + "_buttonsDiv").css("display", "none");
                       }
                       else
                       {
                          if((widgetProperties.param.enableFullscreenTab === 'yes')&&(widgetProperties.param.enableFullscreenModal === 'no'))
                          {
                              $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                              titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                              $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                              $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                              $("#" + widgetName + "_buttonsDiv").css("display", "none");
                          }
                          else
                          {
                             if((widgetProperties.param.enableFullscreenTab === 'no')&&(widgetProperties.param.enableFullscreenModal === 'yes')) 
                             {
                                 $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                 titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                                 $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                                 $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();
                                 $("#" + widgetName + "_buttonsDiv").css("display", "none");
                             }
                             else
                             {
                                 $("#" + widgetName + "_buttonsDiv").hide();
                                 titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 2));
                             }
                          }
                       }
                   } 
               }
               
               $("#" + widgetName + "_titleDiv").css("width", titleWidth + "px");
           }
           
           //Modalità Web Link: settaggio iframe
           if(showHeader === false)
           {
               wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight());
           }
           else
           {
               wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight() - $('#<?= $_REQUEST['name_w'] ?>_header').outerHeight());
           }
           
           wrapperW = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerWidth());
           $('#<?= $_REQUEST['name_w'] ?>_content').width(wrapperW);
           $('#<?= $_REQUEST['name_w'] ?>_content').height(wrapperH);
           $('#<?= $_REQUEST['name_w'] ?>_wrapper').width(wrapperW);
           $('#<?= $_REQUEST['name_w'] ?>_wrapper').height(wrapperH);
           $('#<?= $_REQUEST['name_w'] ?>_iFrame').width(wrapperW);
           $('#<?= $_REQUEST['name_w'] ?>_iFrame').height(wrapperH);
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
       //$("#<?= $_REQUEST['name_w'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
       widgetProperties = getWidgetProperties(widgetName);
       
       createFullscreenModal();
       
       if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
       {
           //Inizio eventuale codice ad hoc basato sulle proprietà del widget
           url = widgetProperties.param.link_w;
           //console.log('url');
           //console.log(url);
           //console.log('fine url');
           $("a.iconFullscreenModal").tooltip();
           $("a.iconFullscreenTab").tooltip();
           
           styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
           //Fine eventuale codice ad hoc basato sulle proprietà del widget
           
           if(url.includes("selectorWebTarget"))
           {
               $('#<?= $_REQUEST['name_w'] ?>_div').attr("data-noPointsUrl", JSON.parse(url).homepage);
               $('#<?= $_REQUEST['name_w'] ?>_div').attr("data-role", "selectorWebTarget");
           }
           else
           {
               if((url === 'map')||(url === 'gisTarget'))
               {
                   $('#<?= $_REQUEST['name_w'] ?>_div').attr("data-role", url);
                   $('#<?= $_REQUEST['name_w'] ?>_div').attr("data-noPointsUrl", url);
               }
               else
               {
                   if(url === 'none')
                   {
                       $('#<?= $_REQUEST['name_w'] ?>_div').attr("data-role", "none");
                       $('#<?= $_REQUEST['name_w'] ?>_div').attr("data-noPointsUrl", "about:blank");
                   }
                   else
                   {
                       $('#<?= $_REQUEST['name_w'] ?>_div').attr("data-role", "link");
                       $('#<?= $_REQUEST['name_w'] ?>_div').attr("data-noPointsUrl", url);
                   }
               }
           }
           
           //Inizio eventuale codice ad hoc basato sui dati della metrica
           //showTitle è dalle impostazioni su singolo widget, show header dalle impostazioni su embed dashboard
           if((hostFile === "index") && (showHeader === false))
           {
               $('#<?= $_REQUEST['name_w'] ?>_header').css("display", "none");
               height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
               wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight());
               topWrapper = "0px";
           }
           else
           {
               if((hostFile === "index")&&(showTitle === "no")&&(showHeader === true))
               {
                   $('#<?= $_REQUEST['name_w'] ?>_header').css("display", "none");
                   height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
                   wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight());
                   topWrapper = "0px";
               }
               else
               {
                   if(showHeader)
                   {
                       $('#<?= $_REQUEST['name_w'] ?>_header').css("display", "block");
                       height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight") - 25);
                       wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight() - 25);
                       topWrapper = "25px";
                   }
                   else
                   {
                       $('#<?= $_REQUEST['name_w'] ?>_header').css("display", "none");
                       height = parseInt($("#<?= $_REQUEST['name_w'] ?>_div").prop("offsetHeight"));
                       wrapperH = parseInt($('#<?= $_REQUEST['name_w'] ?>_div').outerHeight());
                       topWrapper = "0px";
                   }
                   
                   $("#" + widgetName + "_buttonsDiv").css("height", "100%");
                   $("#" + widgetName + "_buttonsDiv").css("float", "left");
   
                   $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).css("font-size", "20px");
                   $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).hover(function(){$(this).find("span").css("color", "red");}, function(){$(this).find("span").css("color", widgetHeaderFontColor);});
                   $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).css("font-size", "20px");
                   $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).hover(function(){$(this).find("span").css("color", "red");}, function(){$(this).find("span").css("color", widgetHeaderFontColor);});
   
                   if(hostFile === "config")
                   {
                       if((widgetProperties.param.enableFullscreenModal === 'yes')&&(widgetProperties.param.enableFullscreenTab === 'yes'))
                       {
                          $("#" + widgetName + "_buttonsDiv").css("width", "50px");
                          titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 25 - 2));
                          $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                          $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                       }
                       else
                       {
                          if((widgetProperties.param.enableFullscreenModal === 'yes')&&(widgetProperties.param.enableFullscreenTab === 'no'))
                          {
                              $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                              titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 25 - 2));
                              $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                              $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();
                          }
                          else
                          {
                             if((widgetProperties.param.enableFullscreenModal === 'no')&&(widgetProperties.param.enableFullscreenTab === 'yes')) 
                             {
                               $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                               titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 25 - 2));
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();   
                             }
                             else
                             {
                               $("#" + widgetName + "_buttonsDiv").css("width", "0px");
   				$("#" + widgetName + "_buttonsDiv").hide();
                               titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 0 - 25 - 2));
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                             }
                          }
                       }
                   }
                   else
                   {
                      if((widgetProperties.param.enableFullscreenTab === 'yes')&&(widgetProperties.param.enableFullscreenModal === 'yes'))
                       {
                          $("#" + widgetName + "_buttonsDiv").css("width", "50px");
                          titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 2));
                          $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                          $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                       }
                       else
                       {
                          if((widgetProperties.param.enableFullscreenTab === 'yes')&&(widgetProperties.param.enableFullscreenModal === 'no'))
                          {
                              $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                              titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                              $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                              $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                          }
                          else
                          {
                             if((widgetProperties.param.enableFullscreenTab === 'no')&&(widgetProperties.param.enableFullscreenModal === 'yes')) 
                             {
                                 $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                 titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                                 $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                                 $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();
                             }
                             else
                             {
                                 $("#" + widgetName + "_buttonsDiv").hide();
                                 titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 2));
                             }
                          }
                       }
                   } 
               }
               
               $("#" + widgetName + "_titleDiv").css("width", titleWidth + "px");
           }
   
           $('#<?= $_REQUEST['name_w'] ?>_content').css("width", wrapperW + "px");
           $('#<?= $_REQUEST['name_w'] ?>_content').css("height", wrapperH + "px");
           
           if(firstLoad !== false)
           {
               showWidgetContent(widgetName);
           }
           else
           {
               elToEmpty.empty();
           }
           
           $("#<?= $_REQUEST['name_w'] ?>_iFrame").load(iframeLoaded);
           
           if(url.includes("selectorWebTarget"))
           {
               $("#<?= $_REQUEST['name_w'] ?>_defaultMapDiv").hide();
               $("#<?= $_REQUEST['name_w'] ?>_mapDiv").hide();
               $("#<?= $_REQUEST['name_w'] ?>_gisMapDiv").hide();
               $("#<?= $_REQUEST['name_w'] ?>_wrapper").show(); 
               $("#<?= $_REQUEST['name_w'] ?>_iFrame").attr("src", JSON.parse(url).homepage);
               
               $(document).off('showLinkFromWebSelector_' + widgetName);
               $(document).on('showLinkFromWebSelector_' + widgetName, function(event) 
               {
                    $("#<?= $_REQUEST['name_w'] ?>_mapDiv").hide();
                    $("#<?= $_REQUEST['name_w'] ?>_defaultMapDiv").hide(); 
                    $("#<?= $_REQUEST['name_w'] ?>_gisMapDiv").hide();
                    $("#<?= $_REQUEST['name_w'] ?>_wrapper").show();
                    $('#<?= $_REQUEST['name_w'] ?>_iFrame').attr("src", event.link);
               });
   
               $(document).off('hideLinkFromWebSelector_' + widgetName);
               $(document).on('hideLinkFromWebSelector_' + widgetName, function(event) 
               {
                   if(event.link === $("#<?= $_REQUEST['name_w'] ?>_iFrame").attr("src"))
                   {
                       $("#<?= $_REQUEST['name_w'] ?>_defaultMapDiv").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_mapDiv").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_gisMapDiv").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_wrapper").show(); 
                       $("#<?= $_REQUEST['name_w'] ?>_iFrame").attr("src", JSON.parse(url).homepage);
                   }
               });
           }
           else
           {
               switch(url)
               {
                   case "map": 
                      $("#<?= $_REQUEST['name_w'] ?>_wrapper").hide(); 
                      $("#<?= $_REQUEST['name_w'] ?>_mapDiv").hide();
                      $("#<?= $_REQUEST['name_w'] ?>_gisMapDiv").hide();
                      $("#<?= $_REQUEST['name_w'] ?>_defaultMapDiv").show(); 
                      loadDefaultMap(); 
                      
                      $(document).off('updateEventsMapRef_' + widgetName);
                      $(document).on('updateEventsMapRef_' + widgetName, function(event) 
                      {
                          eventsMapRef = event.mapRef;
                      });
                      break;  
   
                   case "gisTarget":
                       $("#<?= $_REQUEST['name_w'] ?>_wrapper").hide(); 
                       $("#<?= $_REQUEST['name_w'] ?>_mapDiv").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_defaultMapDiv").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_gisMapDiv").show();
                       widgetParameters = JSON.parse(widgetProperties.param.parameters);
                       //coordsCollectionUri = widgetParameters.coordsCollectionUri;
                       //loadGisMap();
                       //console.log('widgetParameters.latLng:  '+widgetParameters.latLng);
                       //console.log('widgetParameters.Zoom:  '+widgetParameters.zoom);
                       //var mapPinImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                       var OLlatLng= widgetParameters.latLng;
                       var OLZoom= widgetParameters.zoom;
                      ////////////

                      var raster = new ol.layer.Group({
   			layers:[
                                                               new ol.layer.Tile({
                                                               source: new ol.source.OSM({
                                                                       imagerySet: 'Aerial',
                                                                        type: 'base',
                                                                })
                                                       })]
                                    });          
   var map = new ol.Map({
   		layers: [raster],
   		target: document.getElementById('<?= $_REQUEST['name_w'] ?>_gisMapDiv'),
   		view: new ol.View({
   			//center: ol.proj.transform( [12.335848, 45.438025] , 'EPSG:4326', 'EPSG:3857'),
                                                       center: ol.proj.transform(OLlatLng,'EPSG:4326', 'EPSG:3857'),
                                                       //
   			maxZoom: 19,
                                                       zoom: OLZoom
   		})
   	});
                      /*PREZZO DI TEST*/
                      ///////
                       
                       $(document).off('addLayerFromGis_' + widgetName);
                       $(document).on('addLayerFromGis_' + widgetName, function(event) 
                       {
                           //var mapBounds = gisMapRef.getBounds();
                           var query, targets = null;
                           var eventGenerator = event.eventGenerator;
                           var color1 = event.color1;
                           var color2 = event.color2;
                           var queryType = event.queryType;
                           //console.log('eventGenerator:' + eventGenerator);
                           //alert("Query type: " + queryType);
                           
                           var loadingDiv = $('<div class="gisMapLoadingDiv"></div>');
   
                           if($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length > 0)
                           {
                               loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').last());
                           }
                           else
                           {
                               loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_gisMapDiv'));
                           }
   
                           loadingDiv.css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - ($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length * loadingDiv.height())) + "px");
                           loadingDiv.css("left", ($('#<?= $_REQUEST['name_w'] ?>_div').width() - loadingDiv.width()) + "px");
   
                           var loadingText = $('<p class="gisMapLoadingDivTextPar">adding <b>' + event.desc.toLowerCase() + '</b> to map<br><i class="fa fa-circle-o-notch fa-spin" style="font-size: 30px"></i></p>');
                           var loadOkText = $('<p class="gisMapLoadingDivTextPar"><b>' + event.desc.toLowerCase() + '</b> added to map<br><i class="fa fa-check" style="font-size: 30px"></i></p>');
                           var loadKoText = $('<p class="gisMapLoadingDivTextPar">error adding <b>' + event.desc.toLowerCase() + '</b> to map<br><i class="fa fa-close" style="font-size: 30px"></i></p>');
   
                           loadingDiv.css("background", event.color1);
                           loadingDiv.css("background", "-webkit-linear-gradient(left top, " + event.color1 + ", " + event.color2 + ")");
                           loadingDiv.css("background", "-o-linear-gradient(bottom right, " + event.color1 + ", " + event.color2 + ")");
                           loadingDiv.css("background", "-moz-linear-gradient(bottom right, " + event.color1 + ", " + event.color2 + ")");
                           loadingDiv.css("background", "linear-gradient(to bottom right, " + event.color1 + ", " + event.color2 + ")");
                           loadingDiv.show();
   
                           loadingDiv.append(loadingText);
                           loadingDiv.css("opacity", 1);
   
                           var parHeight = loadingText.height();
                           var parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                           loadingText.css("margin-top", parMarginTop + "px");
   
                           var re1 = '(selection)';	// Word 1
                           var re2 = '(=)';	// Any Single Character 1
                           var re3 = '([+-]?\\d*\\.\\d+)(?![-+0-9\\.])';	// Float 1
                           var re4 = '(;|%3B)';	// Any Single Character 2
                           var re5 = '([+-]?\\d*\\.\\d+)(?![-+0-9\\.])';	// Float 2
                           var re6 = '(;|%3B)?';	// Any Single Character 3
                           var re7 = '([+-]?\\d*\\.\\d+)?(?![-+0-9\\.])?';	// Float 3
                           var re8 = '(;|%3B)?';	// Any Single Character 4
                           var re9 = '([+-]?\\d*\\.\\d+)?(?![-+0-9\\.])?';	// Float 4
   
                           var pattern = new RegExp(re1+re2+re3+re4+re5+re6+re7+re8+re9, ["i"]);
                           var new_level='';
   /****PEZZO DA SOSTITUIRE****/
               //
               query = event.query;
               var desc = event.desc;
               //**//
               var query_text =query.toLowerCase();
               //if(query_text.includes('service=wfs')){
                  //
                  console.log('WFS Incluso in '+ desc);
               
               //ESTRARRE IL NOME DELLA FEATURE//
               if (query.includes("typeNames=")){
                  var res = query.split("typeNames="); 
               }else{
               var res = query.split("typeName=");
             }
               //**//
               var query=query+'&srsname=EPSG:3857';
               //
   
                 var vectorSource = new ol.source.Vector({
                               url: '../widgets/proxyGisWFS.php?url='+query,
                               format: new ol.format.WFS({
                               rsName: 'EPSG:4326'
                             }),
                                projection: 'EPSG:4326'
                           });
               
              ////// AGGIUNTA PER I PIN ///          
                    var mapPinImg = '../img/gisMapIcons/' + res[1] + '.png';
                   //////
                   function doesFileExist(urlToFile) {
                       //
                          $.ajax({
                           type: 'HEAD',
                           //url: urlToFile,
                           url: urlToFile,
                           complete: function (xhr){
                             if ((xhr.status === '404')||(xhr.responsetext=='Error')){
                               return false; // Not found
                             }else{
                                return true; 
                             }
                                }
                         });
                }
                                   
                    function doesURLcorrect(urlToFile,desc) {
                        var urlLow = urlToFile.toLowerCase();
                        var controlForm = urlLow.includes('getfeature');
                        if(controlForm){
                                $.ajax({
                                   type: 'HEAD',
                                   //url: urlToFile,
                                   url: "../widgets/proxyGisWFS.php?url="+urlToFile,
                                   complete: function (xhr){
                                     if (xhr.status === 404){
                                       alert('Error during Selector: "'+desc+'" Execution. Check if url is correct');
                                       loadingDiv.empty();
                                       loadingDiv.append(loadKoText);
                                       return false; // Not found
                                     }else{
                                         //
                                        var patt1 = /version=[0-9].[0-9].[0-9]/;
                                        var cerca = query.match(patt1);
                                        var matched= cerca[0];
                                        if (matched !== "version=1.1.0"){
                                            alert('Error during Selector: "'+desc+'" Execution. WFS Version must be "1.1.0"');
                                            loadingDiv.empty();
                                            loadingDiv.append(loadKoText);
                                            return false;
                                        //var query = query.replace(matched, "version=1.1.0");
                                         }else{
                                         var xmlHttp = null;
                                         xmlHttp = new XMLHttpRequest();
                                               //xmlHttp.open( "GET", urlToFile, false );
                                               xmlHttp.open("GET", "../widgets/proxyGisWFS.php?url="+urlToFile, false );
                                               xmlHttp.send( null );
                                               var type = xmlHttp.responseXML;
                                               if(type === null){
                                                   //alert('Error during Selector: "'+desc+'" Execution. WFS API Response is not correct.');
                                                   return false;
                                               }else{
                                               var content = xmlHttp.responseXML.childNodes[0].nodeName;
                                               if ((content.includes('FeatureCollection') == false)){
                                                   alert('Error during Selector: "'+desc+'" Execution. Projection type not supported by GisWFS Widget.');
                                                   loadingDiv.empty();
                                                   loadingDiv.append(loadKoText);
                                                   return false;
                                               }else{
                                                  return true; 
                                               }
                                                //ExceptionReport
                                               //console.log(xmlHttp.responseText);
                                            }
                                         //
                                         
                                        }
                                     //
                                     }
                                   }
                               });
                           }else{
                              alert('Error during Selector: "'+desc+'" Execution. WFS API is not correct.');
                              loadingDiv.empty();
                              loadingDiv.append(loadKoText);
                              return false; 
                           }  
                          }
                                                  
                   //////
                   if(query_text.includes('service=wfs')){
                    //var url_icon = res[1];
                    var controlUrl=doesURLcorrect(query,desc);
                    var style1 =doesFileExist(mapPinImg);
                    
                    //
                    var style_pin ="";
                    $.get(mapPinImg)
                           .done(function() {
                               var style1='true';
                               return(style1);
                           }).fail(function() { 
                               var style1='false';
                               return(style1);
                           });
              //
              style1 = false;
              
              //
              if (style1){
                  var new_level = new ol.layer.Vector({
                       source: vectorSource,
                       title: res[1],
                       style: [ 
                           //
                            new ol.style.Style({
                                         image: new ol.style.Icon(({
                                                 anchor: [0.5, 46],
                                                 anchorXUnits: 'fraction',
                                                 anchorYUnits: 'pixels',
                                                 src: mapPinImg
                                               }))
                                        }),
                           //
                           new ol.style.Style({
                                       fill: new ol.style.Fill({
                                       color: color2
                                  }),
                                      radius: 5,
                                      stroke: new ol.style.Stroke({
                                                color: color1,
                                                width: 1
                                            })
                                  })]
                   });
              }else{
                  var new_level = new ol.layer.Vector({
                       source: vectorSource,
                       title: res[1],
                       style: [ 
                           //
                            //
                           new ol.style.Style({
                             image: new ol.style.Circle({
                             fill: new ol.style.Fill({
                                     color: color2
                                   }),
                              radius: 5,
                              stroke: new ol.style.Stroke({
                                      color:  color1,
                                      width: 1
                                      })
                                   })
                              }),
                           //
                           new ol.style.Style({
                                       fill: new ol.style.Fill({
                                       color: color2
                                  }),
                                      radius: 5,
                                      stroke: new ol.style.Stroke({
                                                color: color1,
                                                width: 1
                                            })
                                  })]
                               });
              }
              ///FINE AGGIUNTA PER I PIN///
            map.addLayer(new_level);
            //
            }else if (query_text.includes('wms')){              
                  var str_liv = query.split('?service=wms');
                  var url = str_liv[0];
                  //var layer = query.split('layers=Snap4City%3A');
                  var layer = query.split('layers=');
                  //
                  var layer1 =layer[1].split('&');                   
                  var time = query.split('time=');
                  var time1 =time[1].split('&');
                  var wmsDatasetName =layer1[0].replace("%3A", ":");
                  var timestampISO =time1[0].replace("%3A", ":"); 
                  var new_level = new ol.layer.Tile({
                                    title: res[1],
                                    source: new ol.source.TileWMS({
                                    url: url,
                                    params: {
                                            'LAYERS':  wmsDatasetName,
                                            'VERSION': '1.3.0',
                                            'FORMAT': 'image/png'
                                        }
                                    })
                   });
                   map.addLayer(new_level);
                   //wmsGroup.getLayers().push(new_level);
                   loadingDiv.empty();
                   loadingDiv.append(loadOkText);  
                  //
               }else if (query_text.includes('servicemap.disit.org')){
                        //var mapPinImg = '../img/gisMapIcons/Accommodation_Hotel.png';
                        //console.log(feature.properties.serviceType);
                            $.ajax({
                                type: 'GET',
                                url: '../widgets/proxyGisWFS.php?url='+query, 
                                success: function(result){
                                            var data = JSON.parse(result);
                                            var dati_query = data.Services;	
                                            var json_data = JSON.stringify(dati_query);
                                            /////
                                            var esempio = data.features[0];
                                            var type_image = esempio.properties.serviceType;
                                            //console.log(esempio.properties.serviceType);
                                            var mapPinImg = '../img/gisMapIcons/'+type_image+'.png';
                                            ////
                                            var source_v = new ol.source.Vector({
   				url: '../widgets/proxyGisWFS.php?url='+query,
   				format: new ol.format.GeoJSON()
   				});
                                                var vector = new ol.layer.Vector({
   				source: source_v,
                                                        style: [ 
                                                            new ol.style.Style({
                                                                         image: new ol.style.Icon(({
                                                                                 anchor: [0.5, 46],
                                                                                 anchorXUnits: 'fraction',
                                                                                 anchorYUnits: 'pixels',
                                                                                 src: mapPinImg
                                                                               }))
                                                                        }),
                            //
                                                                new ol.style.Style({
                                                                            fill: new ol.style.Fill({
                                                                            color: color2
                                                                       }),
                                                                           radius: 5,
                                                                           stroke: new ol.style.Stroke({
                                                                                     color: color1,
                                                                                     width: 1
                                                                                 })
                                                                       })]
                                                   });               
                                 map.addLayer(vector);
                                 //wfsGroup.getLayers().push(vector);
                                 loadingDiv.empty();
                                 loadingDiv.append(loadOkText);
     }
                        });
   //map.getZoom();
                   
                   //
               }else{
                    //alert('Error during Selector: "'+desc+'" Execution. Check Selector URI Correctness.');
                    loadingDiv.empty();
                    loadingDiv.append(loadKoText);
                        
               }
   
   
   
   //console.log(map);
   //overlayGroup.getLayers().push(new_level);
   //RIMUOVI PROVA
   //**************//
   setTimeout(function(){
                                       loadingDiv.css("opacity", 0);
                                       setTimeout(function(){
                                           loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function(i){
                                               $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                           });
                                           loadingDiv.remove();
                                       }, 350);
                                   }, 1000);
   //*************//
   //map.removeLayer(new_level);
   //************//
   eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").hide();
         eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("font-weight", "bold");
         eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("color", eventGenerator.attr("data-activeFontColor"));
         if(eventGenerator.parents("div.gisMapPtrContainer").find('a.gisPinLink').attr("data-symbolMode") === 'auto'){
                eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").html("near_me");
                eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("color", "white");
                eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("text-shadow", "2px 2px 4px black");
         }else{
                eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").show();
                eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
         }
        eventGenerator.show();
   //***********//
                       });
   
                       $(document).off('removeLayerFromGis_' + widgetName);
                       $(document).on('removeLayerFromGis_' + widgetName, function(event) {
                           var queryType = event.query;
                           //
                           console.log('queryType:  ');
                           console.log(queryType);
                           //var query_split = queryType.split('&typeNames=');
                           if (queryType.includes("wms")) {
                                            var query_split = queryType;
                                            //var map_ex = map.getLayerGroup();
                                            var group1 = map.getLayers();
                                            var arr = group1.array_;
                                            var lun = arr.length;
                                            ///////////
                                            for (var y = 0; y < lun; y++) {
                                                var type = arr[y].type;
                                                var tx = group1.array_[y];
                                                if(type == 'TILE'){
                                                     var tx2 = tx.values_.source.params_;
                                                            var tx3 = tx2.LAYERS;
                                                            var tx4 = tx3.split('Snap4City:');
                                                            var tx5 = tx4[0];
                                                            if (queryType.includes(tx5)) {
                                                                    map.removeLayer(tx);
                                                            }
                                                }
                                            }
                           }else{
                           ///
                           if (queryType.includes("typeNames=")){
                                    var query_split = queryType.split('&typeNames='); 
                                 }else{
                                     var query_split = queryType.split('&typeName=');
                              }
                           ///
                           var title_layer = query_split[1];
                           var map_ex = map.getLayerGroup();
                           var group1 = map_ex.getLayers();
                           var arr = group1.array_;
                           var lun = arr.length;
                           for(var y=1; y<lun;y++){
                               var tx2='';
                               var tx=group1.array_[y];
                               var tx2=tx.values_.source.url_;
                               var tipo = tx.type;
                               console.log(tipo);
                               if(tipo == 'TILE'){
                                   console.log('Error_TILE');
                               }else{
                                    if (tx2.includes(queryType)){
                                             map.removeLayer(tx);
                                         }
                               }
                               
                              }
                       //CLICK
                   }
                           ///////
                       });
                           
                       $(document).off('mouseOverFromGis_' + widgetName);
                       $(document).on('mouseOverFromGis_' + widgetName, function(evt) 
                       {
                           /*if(gisMapRef.hasLayer(gisLayersOnMap[evt.desc]))
                           {
                               gisLayersOnMap[evt.desc].eachLayer(function(marker) {
                                   marker.fire("mouseover");
                               });
                           }*/
                       });
   
                       $(document).off('mouseOutFromGis_' + widgetName);
                       $(document).on('mouseOutFromGis_' + widgetName, function(evt) 
                       {
                          /* if(gisMapRef.hasLayer(gisLayersOnMap[evt.desc]))
                           {
                               gisLayersOnMap[evt.desc].eachLayer(function(marker) {
                                   marker.fire("mouseout");
                               });
                           }*/
                       });
                       metricName = "<?= $_REQUEST['id_metric'] ?>";
                       break;
   
                   case "none":
                      $("#<?= $_REQUEST['name_w'] ?>_mapDiv").hide();
                      $("#<?= $_REQUEST['name_w'] ?>_defaultMapDiv").hide(); 
                      $("#<?= $_REQUEST['name_w'] ?>_gisMapDiv").hide();
                      $("#<?= $_REQUEST['name_w'] ?>_wrapper").show(); 
                      break;
   
                  default:
                      $("#<?= $_REQUEST['name_w'] ?>_mapDiv").hide();
                      $("#<?= $_REQUEST['name_w'] ?>_defaultMapDiv").hide(); 
                      $("#<?= $_REQUEST['name_w'] ?>_gisMapDiv").hide();
                      $("#<?= $_REQUEST['name_w'] ?>_wrapper").show();
                      $('#<?= $_REQUEST['name_w'] ?>_iFrame').attr("src", url);
                      $('#<?= $_REQUEST['name_w'] ?>_iFrame').attr("data-oldsrc", url);
   	   
                      metricName = "<?= $_REQUEST['id_metric'] ?>"; 	
                      break;
               }
           }
           
           $(document).off('centerMapForOperatorEvent_' + widgetName);
           switch(url)
           {
               case "map": 
                   /*if($('#<?= $_REQUEST['name_w'] ?>_defaultMapDiv').is(':visible'))
                   {
                       $(document).on('centerMapForOperatorEvent_' + widgetName, function(evt) 
                       {
                           var newLat = parseFloat(evt.lat);
                           var newLng = parseFloat(evt.lng);
                           defaultMapRef.panTo(new L.LatLng(newLat, newLng));
                           console.log("Ricentraggio mappa di default");
                       });
                   }
                   else
                   {
                       $(document).on('centerMapForOperatorEvent_' + widgetName, function(evt) 
                       {
                           console.log("Ricentraggio mappa eventi");
                           var newLat = parseFloat(evt.lat);
                           var newLng = parseFloat(evt.lng);
                           eventsMapRef.panTo(new L.LatLng(newLat, newLng));
                       });
                   }*/
                   break;
   
               case "gisTarget":
                   $(document).on('centerMapForOperatorEvent_' + widgetName, function(evt) 
                   {
                       var newLat = parseFloat(evt.lat);
                       var newLng = parseFloat(evt.lng);
                       gisMapRef.panTo(new L.LatLng(newLat, newLng));
                   });
                   break;
           }
           
           $('#<?= $_REQUEST['name_w'] ?>_buttonsDiv a.iconFullscreenModal').click(function()
           {
               switch($('#<?= $_REQUEST['name_w'] ?>_div').attr("data-role"))
               {
                   case "map": 
                     $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_REQUEST['name_w'] ?>_titleDiv").html());  
                     $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").hide(); 
                     
                     if($("#<?= $_REQUEST['name_w'] ?>_defaultMapDiv").is(":visible"))
                     {
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").show();
                       
                       //Creazione mappa
                       setTimeout(function(){
                           var mapdiv = "<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap";
                           fullscreenDefaultMapRef = L.map(mapdiv).setView([43.769789, 11.255694], 11);
   
                           L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                              attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                              maxZoom: 18
                           }).addTo(fullscreenDefaultMapRef);
                           fullscreenDefaultMapRef.attributionControl.setPrefix('');
                       }, 250);
                     }
                     else
                     {
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                       
                       if(($("#<?= $_REQUEST['name_w'] ?>_driverWidgetType").val() !== 'newtworkAnalysis')&&($("#<?= $_REQUEST['name_w'] ?>_driverWidgetType").val() !== 'button')/*&&($("#<?= $_REQUEST['name_w'] ?>_driverWidgetType").val() !== 'recreativeEvents')*/)
                       {   
                           $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").show();
                       
                           setTimeout(function(){
                               //Creazione mappa
                               var mapdiv = "<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyMap";
                               fullscreenMapRef = L.map(mapdiv).setView([43.769789, 11.255694], 11);
   
                               L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                  attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                  maxZoom: 18
                               }).addTo(fullscreenMapRef);
                               fullscreenMapRef.attributionControl.setPrefix('');
   
                               //Popolamento mappa (se ci sono eventi su mappa originaria)
                               if($('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen input.fullscreenEventPoint').length > 0)
                               {
                                   switch($('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen input.fullscreenEventPoint').eq(0).attr("data-eventType"))
                                   {
                                       case "recreativeEvents":
                                           minLat = +90;
                                           minLng = +180;
                                           maxLat = -90;
                                           maxLng = -180;
                                           
                                           var categoryIT, name, place, startDate, endDate, startTime, freeEvent, address, civic, price, phone,
                                               descriptionIT, website, colorClass, mapIconName = null;
   
                                           $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen input.fullscreenEventPoint").each(function(i)
                                           {
                                               dataArray = $(this).val().split('||');
                                               evtTypeForMaxZoom = $(this).attr("data-eventType");
                                               
                                               lat = dataArray[1];
                                               lng = dataArray[0];
                                               categoryIT = dataArray[2];
                                               name = dataArray[3];
                                               
                                               if(name.includes('?'))
                                               {
                                                  name = name.replace(/\?/g, "'");
                                               }
                                               
                                               place = dataArray[4];
                                               
                                               if(place.includes('?'))
                                               {
                                                  place = place.replace(/\?/g, "'");
                                               }
                                               
                                               startDate = dataArray[5];
                                               endDate = dataArray[6];
                                               startTime = dataArray[7];    
                                               freeEvent = dataArray[8];
                                               address = dataArray[9];
                                               
                                               if(address.includes('?'))
                                               {
                                                  address = address.replace(/\?/g, "'");
                                               }
                                               
                                               civic = dataArray[10];
                                               price = dataArray[11];
                                               phone = dataArray[12];
                                               descriptionIT = dataArray[13];
                                               
                                               if(descriptionIT.includes('?'))
                                               {
                                                  descriptionIT = descriptionIT.replace(/\?/g, "'");
                                               }
                                               
                                               website = dataArray[14];
                                               colorClass = dataArray[15];
                                               mapIconName = dataArray[16];
                                               
                                               mapPinImg = '../img/eventsIcons/' + mapIconName + '.png';
                                               
                                               pinIcon = new L.DivIcon({
                                                   className: null,
                                                   html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                                   iconAnchor: [18, 36]
                                               });
   
                                               markerLocation = new L.LatLng(lat, lng);
                                               marker = new L.Marker(markerLocation, {icon: pinIcon});
                                               dataArray[17] = marker;
                                               
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
                                                            popupText += '<b><a href="https://' + website + '" target="_blank">Website</a></b><br>';
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
   
                                               fullscreenMapRef.addLayer(marker);
                                               lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth : 300});
                                               
                                               lastPopup.on('popupopen', function(){
                                                   $('button.recreativeEventMapDetailsBtn').off('click');
                                                   $('button.recreativeEventMapDetailsBtn').click(function(){
                                                       $('div.recreativeEventMapDataContainer').hide();
                                                       $('div.recreativeEventMapDetailsContainer').show();
                                                       $('button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                                       $(this).addClass('recreativeEventMapBtnActive');
                                                   });
   
                                                   $('button.recreativeEventMapDescriptionBtn').off('click');
                                                   $('button.recreativeEventMapDescriptionBtn').click(function(){
                                                       $('div.recreativeEventMapDataContainer').hide();
                                                       $('div.recreativeEventMapDescContainer').show();
                                                       $('button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                                       $(this).addClass('recreativeEventMapBtnActive');
                                                   });
   
                                                   $('button.recreativeEventMapTimingBtn').off('click');
                                                   $('button.recreativeEventMapTimingBtn').click(function(){
                                                       $('div.recreativeEventMapDataContainer').hide();
                                                       $('div.recreativeEventMapTimingContainer').show();
                                                       $('button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                                       $(this).addClass('recreativeEventMapBtnActive');
                                                   });
   
                                                   $('button.recreativeEventMapContactsBtn').off('click');
                                                   $('button.recreativeEventMapContactsBtn').click(function(){
                                                       $('div.recreativeEventMapDataContainer').hide();
                                                       $('div.recreativeEventMapContactsContainer').show();
                                                       $('button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                                       $(this).addClass('recreativeEventMapBtnActive');
                                                   }); 
                                               });
                                               
                                               //Calcolo del rettangolo di visualizzazione
                                               if(dataArray[0] < minLng)
                                               {
                                                  minLng = dataArray[0];
                                               }
   
                                               if(dataArray[0] > maxLng)
                                               {
                                                  maxLng = dataArray[0];
                                               }
   
                                               if(dataArray[1] < minLat)
                                               {
                                                  minLat = dataArray[1];
                                               }
   
                                               if(dataArray[1] > maxLat)
                                               {
                                                  maxLat = dataArray[1];
                                               }
                                           });
   
                                           if(dataArray.length > 0)
                                           {
                                               fullscreenMapRef.fitBounds([
                                                 [minLat, minLng],
                                                 [maxLat, maxLng]
                                               ]);
                                           } 
                                           break;
                                       
                                       case "resource":
                                           minLat = +90;
                                           minLng = +180;
                                           maxLat = -90;
                                           maxLng = -180;
   
                                           $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen input.fullscreenEventPoint").each(function(i)
                                           {
                                               dataArray = $(this).val().split('||');
                                               evtTypeForMaxZoom = $(this).attr("data-eventType");
                                               
                                               lat = dataArray[1];
                                               lng = dataArray[0];
                                               eventName = dataArray[2];
                                               eventStartDate  = dataArray[3];
                                               eventStartTime = dataArray[4];
   
                                               mapPinImg = '../img/resourceIcons/metroMap.png';
   
                                               pinIcon = new L.DivIcon({
                                                   className: null,
                                                   html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                                   iconAnchor: [18, 36]
                                               });
   
                                               markerLocation = new L.LatLng(lat, lng);
                                               marker = new L.Marker(markerLocation, {icon: pinIcon});
                                               
                                               dataArray[5] = marker;
                                               popupText = "<span class='mapPopupTitle'>" + eventName.toUpperCase() + "</span>" + 
                                                           "<span class='mapPopupLine'>" + eventStartDate + " - " + eventStartTime + "</span>";
   
                                               fullscreenMapRef.addLayer(marker);
                                               lastPopup = marker.bindPopup(popupText, {offset: [-5, -40]}).openPopup();
   
                                               //Calcolo del rettangolo di visualizzazione
                                               if(dataArray[0] < minLng)
                                               {
                                                  minLng = dataArray[0];
                                               }
   
                                               if(dataArray[0] > maxLng)
                                               {
                                                  maxLng = dataArray[0];
                                               }
   
                                               if(dataArray[1] < minLat)
                                               {
                                                  minLat = dataArray[1];
                                               }
   
                                               if(dataArray[1] > maxLat)
                                               {
                                                  maxLat = dataArray[1];
                                               }
                                           });
   
                                           if(dataArray.length > 0)
                                           {
                                               fullscreenMapRef.fitBounds([
                                                 [minLat, minLng],
                                                 [maxLat, maxLng]
                                               ]);
                                           } 
                                           break;
                                       
                                       case "alarm":
                                           minLat = +90;
                                           minLng = +180;
                                           maxLat = -90;
                                           maxLng = -180;
   
                                           $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen input.fullscreenEventPoint").each(function(i)
                                           {
                                               dataArray = $(this).val().split('||');
                                               evtTypeForMaxZoom = $(this).attr("data-eventType");
   
                                               lat = dataArray[1];
                                               lng = dataArray[0];
                                               eventType = dataArray[2];
                                               eventName = dataArray[3];
                                               eventStartDate  = dataArray[4];
                                               eventStartTime = dataArray[5];
                                               eventSeverity = dataArray[6];
   
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
   
                                               pinIcon = new L.DivIcon({
                                                   className: null,
                                                   html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                                   iconAnchor: [18, 36]
                                               });
   
                                               markerLocation = new L.LatLng(lat, lng);
                                               marker = new L.Marker(markerLocation, {icon: pinIcon});
                                               dataArray[8] = marker;
                                               popupText = "<span class='mapPopupTitle'>" + eventName + "</span>" + 
                                                           "<span class='mapPopupLine'><i>Start date: </i>" + eventStartDate + " - " + eventStartTime + "</span>" + 
                                                           "<span class='mapPopupLine'><i>Event type: </i>" + alarmTypes[eventType].desc.toUpperCase() + "</span>" +
                                                           "<span class='mapPopupLine'><i>Event severity: <i/><span style='background-color: " + severityColor + "'>" + eventSeverity.toUpperCase() + "</span></span>";
   
                                               fullscreenMapRef.addLayer(marker);
                                               lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth : 600}).openPopup();
   
                                               //Calcolo del rettangolo di visualizzazione
                                               if(dataArray[0] < minLng)
                                               {
                                                  minLng = dataArray[0];
                                               }
   
                                               if(dataArray[0] > maxLng)
                                               {
                                                  maxLng = dataArray[0];
                                               }
   
                                               if(dataArray[1] < minLat)
                                               {
                                                  minLat = dataArray[1];
                                               }
   
                                               if(dataArray[1] > maxLat)
                                               {
                                                  maxLat = dataArray[1];
                                               }
                                           });
   
                                           if(dataArray.length > 0)
                                           {
                                               fullscreenMapRef.fitBounds([
                                                 [minLat, minLng],
                                                 [maxLat, maxLng]
                                               ]);
                                           } 
                                           break;
   
                                       case "trafficEvent":
                                           minLat = +90;
                                           minLng = +180;
                                           maxLat = -90;
                                           maxLng = -180;
   
                                           $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen input.fullscreenEventPoint").each(function(i)
                                           {
                                               dataArray = $(this).val().split('||');
                                               evtTypeForMaxZoom = $(this).attr("data-eventType");
                                               
                                               lat = dataArray[1];
                                               lng = dataArray[0];
                                               eventType = dataArray[2];
                                               eventName = dataArray[3];
                                               eventSeverity = dataArray[4];
                                               eventSubtype = dataArray[5];
                                               eventStartDate = dataArray[6];
                                               eventStartTime = dataArray[7];
   
                                               switch(eventSeverity)
                                               {
                                                  case "Low":
                                                     mapPinImg = '../img/trafficIcons/' + trafficEventTypes["type" + eventType].mapIconLow;
                                                     severityColor = "#ffcc00";
                                                     break;
   
                                                  case "Med":
                                                     mapPinImg = '../img/trafficIcons/' + trafficEventTypes["type" + eventType].mapIconMed;
                                                     severityColor = "#ff9900";
                                                     break;
   
                                                  case "High":
                                                     mapPinImg = '../img/trafficIcons/' + trafficEventTypes["type" + eventType].mapIconHigh;
                                                     severityColor = "#ff6666";
                                                     break;   
                                               } 
   
                                               pinIcon = new L.DivIcon({
                                                   className: null,
                                                   html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                                   iconAnchor: [18, 36]
                                               });
   
                                               markerLocation = new L.LatLng(lat, lng);
                                               marker = new L.Marker(markerLocation, {icon: pinIcon});
                                               dataArray[8] = marker;
                                               popupText = "<span class='mapPopupTitle'>" + eventName + "</span>" + 
                                                           "<span class='mapPopupLine'><i>Start date: </i>" + eventStartDate + " - " + eventStartTime + "</span>" + 
                                                           "<span class='mapPopupLine'><i>Event type: </i>" + trafficEventTypes["type" + eventType].desc.toUpperCase() + "</span>" +
                                                           "<span class='mapPopupLine'><i>Event subtype: </i>" + trafficEventSubTypes["subType" + eventSubtype].toUpperCase() + "</span>" +
                                                           "<span class='mapPopupLine'><i>Event severity: </i>" + dataArray[9] + " - <span style='background-color: " + severityColor + "'>" + eventSeverity.toUpperCase() + "</span></span>";
   
                                               fullscreenMapRef.addLayer(marker);
                                               lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth : 600}).openPopup();
   
                                               //Calcolo del rettangolo di visualizzazione
                                               if(dataArray[0] < minLng)
                                               {
                                                  minLng = dataArray[0];
                                               }
   
                                               if(dataArray[0] > maxLng)
                                               {
                                                  maxLng = dataArray[0];
                                               }
   
                                               if(dataArray[1] < minLat)
                                               {
                                                  minLat = dataArray[1];
                                               }
   
                                               if(dataArray[1] > maxLat)
                                               {
                                                  maxLat = dataArray[1];
                                               }
                                           });
   
                                           if(dataArray.length > 0)
                                           {
                                               fullscreenMapRef.fitBounds([
                                                 [minLat, minLng],
                                                 [maxLat, maxLng]
                                               ]);
                                           } 
                                           break;
   
                                       case "evacuationPlan":
                                           pathsQt = $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen input.fullscreenEventPoint').eq(0).attr("data-pathsQt");    
                                           var polyline, polyColor = null; 
                                           var polyGroup = L.featureGroup();
                                           //Algoritmo di popolamento mappa
                                           for(var i = 0; i < pathsQt; i++)
                                           {
                                               var path = [];
   
                                               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen input.fullscreenEventPoint[data-polyIndex=" + i + "]").each(function(j)
                                               {
                                                   if(j === 0)
                                                   {
                                                      polyColor = $(this).attr("data-polyColor"); 
                                                   }
                                                   var coords = JSON.parse($(this).val());
                                                   var point = [];
                                                   point[0] = coords.lat;
                                                   point[1] = coords.lng;
                                                   path.push(point);
                                               });
   
                                               polyline = L.polyline(path, {color: polyColor});
                                               polyGroup.addLayer(polyline);
   
                                               polyGroup.addTo(fullscreenMapRef);
                                               fullscreenMapRef.fitBounds(polyGroup.getBounds()); 
                                           }
                                           break;
                                   }
                               }
                           }, 250);
                       }    
                       else
                       {
                           switch($("#<?= $_REQUEST['name_w'] ?>_driverWidgetType").val())
                           {
                               case "newtworkAnalysis":
                                   $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                                   $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                                   $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                                   $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").show();  
                                   $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").attr("src", $("#<?= $_REQUEST['name_w'] ?>_netAnalysisServiceMapUrl").val()); 
                                   break;
                           }
                       }
                     }
                     
                     $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen").modal('show');
                     break;
   
                  case "none":
                       switch($("#<?= $_REQUEST['name_w'] ?>_driverWidgetType").val())
                       {
                           case "button":
                               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_REQUEST['name_w'] ?>_titleDiv").html()); 
                               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").show();  
                               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").attr("src", $("#<?= $_REQUEST['name_w'] ?>_buttonUrl").val()); 
                               $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen").modal('show');
                               break;
                               
                           default:
                               $("#newTabLinkOpenImpossibileMsg").html("No external link is set for this widget: please change it and try again.");
                               $("#newTabLinkOpenImpossibile").modal('show');
                               setTimeout(function(){
                                   $("#newTabLinkOpenImpossibile").modal('hide');
                               }, 4000);
                               break;
                       } 
                     break;
                     
                   case "gisTarget":
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_REQUEST['name_w'] ?>_titleDiv").html());  
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").hide();  
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").show();
                       /*
                       setTimeout(function(){
                           gisFullscreenMapCenter = gisMapRef.getCenter();
                           gisFullscreenMapStartZoom = gisMapRef.getZoom() + 1;
                           gisFullscreenMapStartBounds = gisMapRef.getBounds();
                           
                           var gisFullscreenMapDiv = "<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisMap";
                           gisFullscreenMapRef = L.map(gisFullscreenMapDiv).setView(gisFullscreenMapCenter, gisFullscreenMapStartZoom);
   
                           L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                              attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                              maxZoom: 18
                           }).addTo(gisFullscreenMapRef);
                           gisFullscreenMapRef.attributionControl.setPrefix('');
                           
                           for(var layerName in gisLayersOnMap)
                           {
                               var copyLayer = gisLayersOnMap[layerName].toGeoJSON();
                               
                               L.geoJSON(copyLayer, {
                                   pointToLayer: gisPrepareCustomMarkerForFullscreen
                               }).addTo(gisFullscreenMapRef);
                           }
                           
                           checkTankInterval = setInterval(function(){
                               console.log("Check tank consumer");
                               for(var key in gisGeometryTankForFullscreen)
                               {
                                   for(var k = gisGeometryTankForFullscreen[key].lastConsumedIndex; k < gisGeometryTankForFullscreen[key].tank.length; k++)
                                   {
                                       L.geoJSON(gisGeometryTankForFullscreen[key].tank[k], {
                                           //pointToLayer: gisPrepareCustomMarkerForFullscreen
                                       }).addTo(gisFullscreenMapRef);
                                   }
                                   gisGeometryTankForFullscreen[key].lastConsumedIndex = gisGeometryTankForFullscreen[key].tank.length - 1;
                               }
                           }, 750);
                       }, 250);*/
                       
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen").modal('show');
                      
                       break;
                       
                  case "link":
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_REQUEST['name_w'] ?>_titleDiv").html());  
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").show();  
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").attr("src", $('#<?= $_REQUEST['name_w'] ?>_iFrame').attr('src'));
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen").modal('show'); 
                     break;
                     
                  case "selectorWebTarget":
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_REQUEST['name_w'] ?>_titleDiv").html());  
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").show();
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").attr("src", $('#<?= $_REQUEST['name_w'] ?>_iFrame').attr('src')); 
                       $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen").modal('show');
                     break;
                     
                  default:
                      break;
               }
           });
           
           $('#<?= $_REQUEST['name_w'] ?>_buttonsDiv a.iconFullscreenTab').click(function()
           {
               switch($('#<?= $_REQUEST['name_w'] ?>_div').attr("data-role"))
               {
                  case "map": case "gisTarget":
                     //$("#newTabLinkOpenImpossibileMsg").html("It's not possibile to open an embedded map in an external page: please use the popup fullscreen option.");
                     $("#newTabLinkOpenImpossibile").modal('show');
                     setTimeout(function(){
                         $("#newTabLinkOpenImpossibile").modal('hide');
                     }, 4000);
                     break;
   
                  case "none":
                     switch($("#<?= $_REQUEST['name_w'] ?>_driverWidgetType").val())
                       {
                           case "button":
                               window.open($("#<?= $_REQUEST['name_w'] ?>_buttonUrl").val(), '_blank');
                               break;
                               
                           default:
                               $("#newTabLinkOpenImpossibileMsg").html("No external link is set for this widget: please change it and try again.");
                               $("#newTabLinkOpenImpossibile").modal('show');
                               setTimeout(function(){
                                   $("#newTabLinkOpenImpossibile").modal('hide');
                               }, 4000);
                               break;
                       }
                     break;
                     
                  case "link": case "selectorWebTarget":
                     window.open($('#<?= $_REQUEST['name_w'] ?>_iFrame').attr('src'), '_blank');
                     break;
                 }
           });
           
           $(document).off('showDefaultMapPicDiv_' + widgetName);
           $(document).on('showDefaultMapPicDiv_' + widgetName, function(evt) 
           {
               console.log("Colto evento show default pic");
               if($('#<?= $_REQUEST['name_w'] ?>_mapDiv').is(':visible'))
               {
                   hiddenDiv = "mapDiv";
                   $('#<?= $_REQUEST['name_w'] ?>_mapDiv').hide();
                   $('#<?= $_REQUEST['name_w'] ?>_defaultMapPicDiv').show();
               }
               else
               {
                   if($('#<?= $_REQUEST['name_w'] ?>_gisMapDiv').is(':visible'))
                   {
                       hiddenDiv = "gisMapDiv";
                       $('#<?= $_REQUEST['name_w'] ?>_gisMapDiv').hide();
                       $('#<?= $_REQUEST['name_w'] ?>_defaultMapPicDiv').show();
                   }
                   else
                   {
                       if($('#<?= $_REQUEST['name_w'] ?>_defaultMapDiv').is(':visible'))
                       {
                           hiddenDiv = "defaultMapDiv";
                           $('#<?= $_REQUEST['name_w'] ?>_defaultMapDiv').hide();
                           $('#<?= $_REQUEST['name_w'] ?>_defaultMapPicDiv').show();
                       }
                   }
               }
           });
           
           $(document).off('hideDefaultMapPicDiv_' + widgetName);
           $(document).on('hideDefaultMapPicDiv_' + widgetName, function(evt) 
           {
               if(hiddenDiv === "mapDiv")
               {
                   $('#<?= $_REQUEST['name_w'] ?>_defaultMapPicDiv').hide();
                   $('#<?= $_REQUEST['name_w'] ?>_mapDiv').show();
               }
               else
               {
                   if(hiddenDiv === "gisMapDiv")
                   {
                       $('#<?= $_REQUEST['name_w'] ?>_defaultMapPicDiv').hide();
                       $('#<?= $_REQUEST['name_w'] ?>_gisMapDiv').show();
                   }
                   else
                   {
                       if(hiddenDiv === "defaultMapDiv")
                       {
                           $('#<?= $_REQUEST['name_w'] ?>_defaultMapPicDiv').hide();
                           $('#<?= $_REQUEST['name_w'] ?>_defaultMapDiv').show();
                       }
                   }
               }
           });
           
           $(document).off('resizeHighchart_' + widgetName);
           $(document).on('resizeHighchart_' + widgetName, function(event) 
           {
               showHeader = event.showHeader;
               
               if($('#<?= $_REQUEST['name_w'] ?>_header').is(':visible'))
               {
                   $("#<?= $_REQUEST['name_w'] ?>_wrapper").css("height", parseInt($("#<?= $_REQUEST['name_w'] ?>").height() - $('#<?= $_REQUEST['name_w'] ?>_header').height()) + "px");
               }
               else
               {
                   $("#<?= $_REQUEST['name_w'] ?>_wrapper").css("height", "100%");
               }
           });
           
           //Web socket 
           openWs = function(e)
           {
               try
               {
                   <?php
      $genFileContent = parse_ini_file("../conf/environment.ini");
      $wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
      $wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$genFileContent['environment']['value']];
      $wsServerPort = $wsServerContent["wsServerPort"][$genFileContent['environment']['value']];
      $wsPath = $wsServerContent["wsServerPath"][$genFileContent['environment']['value']];
      $wsProtocol = $wsServerContent["wsServerProtocol"][$genFileContent['environment']['value']];
      $wsRetryActive = $wsServerContent["wsServerRetryActive"][$genFileContent['environment']['value']];
      $wsRetryTime = $wsServerContent["wsServerRetryTime"][$genFileContent['environment']['value']];
      echo 'wsRetryActive = "' . $wsRetryActive . '";';
      echo 'wsRetryTime = ' . $wsRetryTime . ';';
      echo 'webSocket = new WebSocket("' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '");';
      ?>
   
                   webSocket.addEventListener('open', openWsConn);
                   webSocket.addEventListener('close', wsClosed);
               }
               catch(e)
               {
                   wsClosed();
               }
           };
   
           manageIncomingWsMsg = function(msg)
           {
               var msgObj = JSON.parse(msg.data);
   
               switch(msgObj.msgType)
               {
                    case "newNRMetricData":
                        if((encodeURIComponent(msgObj.metricName) === encodeURIComponent(metricName))&&(msgObj.newValue !== 'Off'))
                        {
                           if($('#' + widgetName + '_wrapper').is(':visible'))
                           {
                               $("#<?= $_REQUEST['name_w'] ?>_iFrame").attr("src", msgObj.newValue);
                           }
                           
                           if($('#' + widgetName + '_mapDiv').is(':visible'))
                           {
                               //Aggiungi GeoJSON a _mapDiv - TBD
                           }
                           
                           if($('#' + widgetName + '_gisMapDiv').is(':visible')&&(msgObj.newValue.appId === actuatorAttribute))
                           {
                               //Aggiungi GeoJSON a _gisMapDiv
                               var layer = L.geoJSON(msgObj.newValue.geoJson, {
                               }).addTo(gisMapRef);
                               
                               gisMapRef.fitBounds(layer.getBounds());
                           }
                        }
                        break;
   
                    default:
                        break;
               }
           };
   
           openWsConn = function(e)
           {
               var wsRegistration = {
                   msgType: "ClientWidgetRegistration",
                   userType: "widgetInstance",
                   metricName: encodeURIComponent(metricName),
                   widgetUniqueName: "<?= $_REQUEST['name_w'] ?>"
                 };
                 
               webSocket.send(JSON.stringify(wsRegistration));
               webSocket.addEventListener('message', manageIncomingWsMsg);
           };
   
           wsClosed = function(e)
           {
               webSocket.removeEventListener('close', wsClosed);
               webSocket.removeEventListener('open', openWsConn);
               webSocket.removeEventListener('message', manageIncomingWsMsg);
               webSocket = null;
               if(wsRetryActive === 'yes')
               {
                   setTimeout(openWs, parseInt(wsRetryTime*1000));
               }
           };
   
           openWs();
           
           $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
               resizeWidget();
           });
       }    
       else
       {
           console.log("Errore in caricamento proprietà widget");
       }
   
   
   $("#" + widgetName + "_buttonsDiv").css("display", "none");
   //
   //click eve
   
   /**/
   var dt = $('#<?= $_REQUEST['name_w'] ?>_div').attr("data-role");
   if (dt == "gisTarget"){
   /**	Popup	**/		
   var id_name='<?= $_REQUEST['name_w'] ?>_popup';
   var container = document.getElementById('<?= $_REQUEST['name_w'] ?>_popup');
   var content_element = document.getElementById('<?= $_REQUEST['name_w'] ?>_popup-content');
   var closer = document.getElementById('<?= $_REQUEST['name_w'] ?>_ol-popup-closer');
   closer.onclick = function() {
   	overlay.setPosition(undefined);
   	closer.blur();
   	return false;
   };
   var overlay = new ol.Overlay({
   	element: container,
   	autoPan: true,
   	offset: [0, -10]
   });
   map.addOverlay(overlay);
   var fullscreen = new ol.control.FullScreen();
   map.addControl(fullscreen);
   map.on('click', function(evt){
   	var feature = map.forEachFeatureAtPixel(evt.pixel,
   	  function(feature) {
   		return feature;
   	  });
                                       //
                                       //
                                       var id_f = feature.getId();
                                       var layers_sel=id_f;
                                       var layer = map.forEachLayerAtPixel(evt.pixel,
   	  function(layer) {
   		return layer;
   	  });
                                         var source = layer.getSource();
                                         var urlSource=source.url_;
                                       var dataquery = $("[data-query]");
                                       //var l = dataquery.length;
                                       var c1 = '#ccc';
                                       var c2= '#eee';
                                       for (l = 0; l < dataquery.length; l++){
                                                   var actual_l = dataquery[l];
                                                   var father=actual_l.parentNode.parentNode.parentNode.parentNode;
                                                   var father2= $(father).attr('id');
                                                   //console.log(layers_sel);
                                                   var actual_l2=$(actual_l).attr('data-query');
                                                   var actual_Id=$(actual_l).attr('map-target');
                                                   var n_lay = actual_l2.includes(layers_sel);
                                                   var name_w = <?= $_REQUEST['name_w'] ?>;
                                                   var name_w2 =$(name_w).attr('data-widgetid');
                                                   var n_targ = actual_Id.includes(name_w2);
                                                  /* if((n_lay)&&(n_targ)){
                                                       c1 = $(actual_l).attr('data-color1');
                                                       c2 = $(actual_l).attr('data-color2');
                                                   } */
                                                   var controlUrl = urlSource.includes(actual_l2);
                                                   if ((controlUrl)&&(n_targ)){
                                                       c1 = $(actual_l).attr('data-color1');
                                                       c2 = $(actual_l).attr('data-color2');
                                                   }
                                       }
   	if (feature) {
   		var geometry = feature.getGeometry();
                                               //var stili0 = geometry.getLayout();
                                               //var stili0 = feature.get('type'),'layer:',feature.get('layer'));                                                
                                               //
   		var coord = geometry.getCoordinates();
   		//
                                               if ((evt.color1 == null)||(evt.color1 =='')||(evt.color1 == 'undefined')){
                                                   evt.color1 = c1;
                                               }
                                               if ((evt.color2 == null)||(evt.color2 =='')||(evt.color2 == 'undefined')){
                                                   evt.color2 = c2;
                                               }
                                               //              
                                               //
            var latLngId = evt.coordinate[1]+""+evt.coordinate[0];
                        latLngId = latLngId.replace(".", "");
                        latLngId = latLngId.replace(".", "");
                       //
                       var proprieta=feature.getProperties();
                       console.log("layer");
                       console.log(layer.getSource().url_);
                       var uri_source = layer.getSource().url_;
                       var urlLow = uri_source.toLowerCase();
                       console.log('uri_source:  '+uri_source);
                                    if (urlLow.includes('getfeature')){
                                        console.log('Include: '+ urlLow);
                                    var content = '<div class="leaflet-popup-content" style="width: 436px;"><h3 class="recreativeEventMapTitle" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">' + '</h3>';
                                                            //Popup nuovo stile uguali a quelli degli eventi ricreativi
                                    if (feature.get('descr')){
                                             content = '<div class="leaflet-popup-content" style="width: 436px;"><h3 class="recreativeEventMapTitle" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">' + feature.get('descr') + '</h3>';
                                    }
                                    if (feature.get('descrizion')){
                                            content = '<div class="leaflet-popup-content" style="width: 436px;"><h3 class="recreativeEventMapTitle" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">' + feature.get('descrizion') + '</h3>';
                                     }
                                    if (feature.get('name')){
                                             content = '<div class="leaflet-popup-content" style="width: 436px;"><h3 class="recreativeEventMapTitle" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">' + feature.get('name') + '</h3>';
                                    }
                                    content += '<div class="recreativeEventMapBtnContainer"><button  class="recreativeEventMapDetailsBtn recreativeEventMapBtn recreativeEventMapBtnActive"  type="button" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');" onclick="myFunctionDet('+id_name+');" id="'+id_name+'_detailBtn" >Details</button><button data-id="' + latLngId + '" class="recreativeEventMapDescriptionBtn recreativeEventMapBtn" type="button" data-role="button" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');" onclick="myFunctionDecr('+id_name+');" id="'+id_name+'_dscrBtn">Description</button></div>';
                                    content += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer ol_detail" id="'+id_name+'_ol_detail">';
                                    content += '<table class="gisPopupGeneralDataTable">';
                                    //Intestazione
                                    content += '<thead>';
                                    content += '<th style="background: ' + evt.color2 + '">Description</th>';
                                    content += '<th style="background: ' + evt.color2 + '">Value</th>';
                                    content += '</thead>';
                                    content += '<tbody>';    
                       jQuery.each(proprieta, function(i, val) {
                                   var inc = i.includes('db_');
                                   var inc_shape = i.includes('hape');
                                   var inc_dsc = i.includes('des');
                                   if ((val === undefined)||(val === '')||(val === null)){
                                       val = '-';
                                   }
                                   if ((inc === false)&&(inc_shape === false)&&(inc_dsc === false)){
                                   content +='<tr><td>'+i+'</td><td>'+val+'</td></tr>';
                                   }
                        });
                                content += '</tbody>';
                               content += '</table>';
                               content += '</div>';
                               content += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer ol_descr" id="'+id_name+'_ol_descr">';
   
                               if (feature.get('descriz')){
                                           content  += feature.get('descriz')+'<br>';
                                }else{
                                       content += "No description available";
                                   }
                               content += '</div>';
                                //
                                content += '</div>';
                        
                        content_element.innerHTML = content;
                                       if (Array.isArray(coord[0])){
                                                       var poin = coord[0].length;
                                                       var lun = poin/2;
                                                       if (!(coord[0][lun])){
                                                               overlay.setPosition(coord[0][0][0]);
                                                       }else{
                                                               overlay.setPosition(coord[0][lun]);
                                                           }
                                                       }else{
   				overlay.setPosition(coord);
                                               		}
                        
                        }else{
                        //console.log('NON Include');
                        //////notihng///
                        //console.log('proprieta:');
                        var uriToLoad = 'https://www.disit.org/superservicemap/api/v1/?serviceUri='+proprieta.serviceUri+'&format=json';
                        //console.log(uriToLoad);
                        /////////////////
                        /*
                        var mapPinImg = '../img/gisMapIcons/over/' + proprieta.serviceType + '_over.png';
                        feature.setStyle(new ol.style.Style({
                                                image: new ol.style.Icon(({
                                                       anchor: [0.5, 46],
                                                       anchorXUnits: 'fraction',
                                                       anchorYUnits: 'pixels',
                                                       src: mapPinImg
                                                        }))
                                          }));
                                          console.log(mapPinImg);*/
                        /////////////////////
                        $.ajax({
                                    url: uriToLoad,
                                    type: "GET",
                                    data: {},
                                    async: false,
                                    dataType: 'json',
                                    success: function(geoJsonServiceData) 
                                    {
                                            var fatherNode = null;
                                                if(geoJsonServiceData.hasOwnProperty("BusStop"))
                                                {
                                                    fatherNode = geoJsonServiceData.BusStop;
                                                }
                                                else
                                                {
                                                    if(geoJsonServiceData.hasOwnProperty("Sensor"))
                                                    {
                                                        fatherNode = geoJsonServiceData.Sensor;
                                                    }
                                                    else
                                                    {
                                                        //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                                        fatherNode = geoJsonServiceData.Service;
                                                    }
                                                }
                                                var serviceProperties = fatherNode.features[0].properties;
                                                console.log('serviceProperties:');
                                                console.log(serviceProperties);
                                                //////////////////////****************************************//////////////////////////////
                                                var content = '<div class="leaflet-popup-content" style="width: 436px;"><h3 class="recreativeEventMapTitle" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">' + '</h3>';
                                               //Popup nuovo stile uguali a quelli degli eventi ricreativi
                                                    if (feature.get('descr')){
                                                             content = '<div class="leaflet-popup-content" style="width: 436px;"><h3 class="recreativeEventMapTitle" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">' + feature.get('descr') + '</h3>';
                                                    }
                                                    if (feature.get('descrizion')){
                                                            content = '<div class="leaflet-popup-content" style="width: 436px;"><h3 class="recreativeEventMapTitle" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">' + feature.get('descrizion') + '</h3>';
                                                     }
                                                    if (feature.get('name')){
                                                             content = '<div class="leaflet-popup-content" style="width: 436px;"><h3 class="recreativeEventMapTitle" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">' + feature.get('name') + '</h3>';
                                                    }
                                                    content += '<div class="recreativeEventMapBtnContainer"><button  class="recreativeEventMapDetailsBtn recreativeEventMapBtn recreativeEventMapBtnActive"  type="button" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');" onclick="myFunctionDet('+id_name+');" id="'+id_name+'_detailBtn" >Details</button><button data-id="' + latLngId + '" class="recreativeEventMapDescriptionBtn recreativeEventMapBtn" type="button" data-role="button" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');" onclick="myFunctionDecr('+id_name+');" id="'+id_name+'_dscrBtn">Description</button>';
                                                    ///
                                                    if(geoJsonServiceData.hasOwnProperty("realtime")){
                                                       content +='<button data-id="' + latLngId + '" class="recreativeEventMapContactsBtn recreativeEventMapBtn" type="button" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');" onclick="myFunctionRT('+id_name+');" id="'+id_name+'_rtBtn">RT data</button>'; 
                                                    //onclick="myFunctionRT('+id_name+');" id="'+id_name+'_rtBtn"
                                                    }
                                                    ///
                                                    content += '</div>';
                                                    content += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer ol_detail" id="'+id_name+'_ol_detail">';
                                                    content += '<table class="gisPopupGeneralDataTable">';
                                                    //Intestazione
                                                    content += '<thead>';
                                                    content += '<th style="background: ' + evt.color2 + '">Description</th>';
                                                    content += '<th style="background: ' + evt.color2 + '">Value</th>';
                                                    content += '</thead>';
                                                    content += '<tbody>';
                                                var popupText = '';
                                                if(serviceProperties.hasOwnProperty('website'))
                                                            {
                                                                if((serviceProperties.website !== '')&&(serviceProperties.website !== undefined)&&(serviceProperties.website !== 'undefined')&&(serviceProperties.website !== null)&&(serviceProperties.website !== 'null'))
                                                                {
                                                                    if(serviceProperties.website.includes('http')||serviceProperties.website.includes('https'))
                                                                    {
                                                                        popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                                                    }
                                                                    else
                                                                    {
                                                                        popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    popupText += '<tr><td>Website</td><td>-</td></tr>';
                                                                }
                                                            }
                                                            else
                                                            {
                                                                popupText += '<tr><td>Website</td><td>-</td></tr>';
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('email'))
                                                            {
                                                                if((serviceProperties.email !== '')&&(serviceProperties.email !== undefined)&&(serviceProperties.email !== 'undefined')&&(serviceProperties.email !== null)&&(serviceProperties.email !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>E-Mail</td><td>' + serviceProperties.email + '<td></tr>';
                                                                }
                                                                else
                                                                {
                                                                    popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                                                                }
                                                            }
                                                            else
                                                            {
                                                                popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('address'))
                                                            {
                                                                if((serviceProperties.address !== '')&&(serviceProperties.address !== undefined)&&(serviceProperties.address !== 'undefined')&&(serviceProperties.address !== null)&&(serviceProperties.address !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>Address</td><td>' + serviceProperties.address + '</td></tr>';
                                                                }
                                                                else
                                                                {
                                                                    popupText += '<tr><td>Address</td><td>-</td></tr>';
                                                                }
                                                            }
                                                            else
                                                            {
                                                                popupText += '<tr><td>Address</td><td>-</td></tr>';
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('civic'))
                                                            {
                                                                if((serviceProperties.civic !== '')&&(serviceProperties.civic !== undefined)&&(serviceProperties.civic !== 'undefined')&&(serviceProperties.civic !== null)&&(serviceProperties.civic !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>Civic n.</td><td>' + serviceProperties.civic + '</td></tr>';
                                                                }
                                                                else
                                                                {
                                                                    popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                                                                }
                                                            }
                                                            else
                                                            {
                                                                popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('cap'))
                                                            {
                                                                if((serviceProperties.cap !== '')&&(serviceProperties.cap !== undefined)&&(serviceProperties.cap !== 'undefined')&&(serviceProperties.cap !== null)&&(serviceProperties.cap !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>C.A.P.</td><td>' + serviceProperties.cap + '</td></tr>';
                                                                }
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('city'))
                                                            {
                                                                if((serviceProperties.city !== '')&&(serviceProperties.city !== undefined)&&(serviceProperties.city !== 'undefined')&&(serviceProperties.city !== null)&&(serviceProperties.city !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>City</td><td>' + serviceProperties.city + '</td></tr>';
                                                                }
                                                                else
                                                                {
                                                                    popupText += '<tr><td>City</td><td>-</td></tr>';
                                                                }
                                                            }
                                                            else
                                                            {
                                                                popupText += '<tr><td>City</td><td>-</td></tr>';
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('province'))
                                                            {
                                                                if((serviceProperties.province !== '')&&(serviceProperties.province !== undefined)&&(serviceProperties.province !== 'undefined')&&(serviceProperties.province !== null)&&(serviceProperties.province !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>Province</td><td>' + serviceProperties.province + '</td></tr>';
                                                                }
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('phone'))
                                                            {
                                                                if((serviceProperties.phone !== '')&&(serviceProperties.phone !== undefined)&&(serviceProperties.phone !== 'undefined')&&(serviceProperties.phone !== null)&&(serviceProperties.phone !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>Phone</td><td>' + serviceProperties.phone + '</td></tr>';
                                                                }
                                                                else
                                                                {
                                                                    popupText += '<tr><td>Phone</td><td>-</td></tr>';
                                                                }
                                                            }
                                                            else
                                                            {
                                                                popupText += '<tr><td>Phone</td><td>-</td></tr>';
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('fax'))
                                                            {
                                                                if((serviceProperties.fax !== '')&&(serviceProperties.fax !== undefined)&&(serviceProperties.fax !== 'undefined')&&(serviceProperties.fax !== null)&&(serviceProperties.fax !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>Fax</td><td>' + serviceProperties.fax + '</td></tr>';
                                                                }
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('note'))
                                                            {
                                                                if((serviceProperties.note !== '')&&(serviceProperties.note !== undefined)&&(serviceProperties.note !== 'undefined')&&(serviceProperties.note !== null)&&(serviceProperties.note !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>Notes</td><td>' + serviceProperties.note + '</td></tr>';
                                                                }
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('agency'))
                                                            {
                                                                if((serviceProperties.agency !== '')&&(serviceProperties.agency !== undefined)&&(serviceProperties.agency !== 'undefined')&&(serviceProperties.agency !== null)&&(serviceProperties.agency !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>Agency</td><td>' + serviceProperties.agency + '</td></tr>';
                                                                }
                                                            }
   
                                                            if(serviceProperties.hasOwnProperty('code'))
                                                            {
                                                                if((serviceProperties.code !== '')&&(serviceProperties.code !== undefined)&&(serviceProperties.code !== 'undefined')&&(serviceProperties.code !== null)&&(serviceProperties.code !== 'null'))
                                                                {
                                                                    popupText += '<tr><td>Code</td><td>' + serviceProperties.code + '</td></tr>';
                                                                }
                                                            }
                                                            content +=popupText;
                                                /////////////////////*****************************************//////////////////////////////
                                                content += '</tbody>';
                                                    content += '</table>';
                                                    content += '</div>';
                                                    content += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer ol_descr" id="'+id_name+'_ol_descr">';
                                                    if (serviceProperties.hasOwnProperty('description')){
                                                            if((serviceProperties.description !== '')&&(serviceProperties.description !== undefined)&&(serviceProperties.description !== 'undefined')&&(serviceProperties.description !== null)&&(serviceProperties.description !== 'null'))
                                                                {
                                                                content  += serviceProperties.description+'<br>';
                                                            }else{
                                                                content += "No description available";
                                                            }
                                                    }else{
                                                            content += "No description available";
                                                        }
                                                    content += '</div>';
                                                    //content += '</div>';
                                                    $(this).css("background", evt.color1);
                                                    $(this).css("background", "-webkit-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                    $(this).css("background", "background: -o-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                    $(this).css("background", "background: -moz-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                    $(this).css("background", "background: linear-gradient(to left, " + evt.color1 + ", " + evt.color2 + ")");
                                           ////////////////////GESTIONE DI RT_DATA///////////////
                                           if(geoJsonServiceData.hasOwnProperty("realtime"))
                        {
                        
                        //var latLngId = event.target.getLatLng().lat + "" + event.target.getLatLng().lng;
                        
                        
                        var serviceProperties = fatherNode.features[0].properties;
                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");
//////////////
                        if((serviceClass.includes("Emergency"))&&(serviceSubclass.includes("First aid"))){
                            console.log("First aid");
                            ////////////
                            content += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer ol_rt" id="'+id_name+'_ol_rt">';
                            var realTimeData = geoJsonServiceData.realtime;
                            var last_update = realTimeData.results.bindings[0].measuredTime.value.replace("T", " ");
                            console.log(last_update);
                            content += '<div class="popupLastUpdateContainer centerWithFlex"><b>Last update:&nbsp;</b><span class="popupLastUpdate" data-id="' + latLngId + '">'+last_update+'</span></div>';
                                    //Tabella nuovo stile
                            //      //Tabella ad hoc per First Aid
                                    content += '<table id="' + latLngId + '" class="psPopupTable">';
                                    var series = {  
                                        "firstAxis":{  
                                           "desc":"Priority",
                                           "labels":[  
                                              "Red code",
                                              "Yellow code",
                                              "Green code",
                                              "Blue code",
                                              "White code"
                                           ]
                                        },
                                        "secondAxis":{  
                                           "desc":"Status",
                                           "labels":[],
                                           "series":[]
                                        }
                                     };

                                    var dataSlot = null;
                                    
                                    measuredTime = realTimeData.results.bindings[0].measuredTime.value.replace("T", " ").replace("Z", "");
                                    
                                    for(var i = 0; i < realTimeData.results.bindings.length; i++)
                                    {
                                        if(realTimeData.results.bindings[i].state.value.indexOf("estinazione") > 0)
                                        {
                                           series.secondAxis.labels.push("Addressed");
                                        }

                                        if(realTimeData.results.bindings[i].state.value.indexOf("ttesa") > 0)
                                        {
                                           series.secondAxis.labels.push("Waiting");
                                        }

                                        if(realTimeData.results.bindings[i].state.value.indexOf("isita") > 0)
                                        {
                                           series.secondAxis.labels.push("In visit");
                                        }

                                        if(realTimeData.results.bindings[i].state.value.indexOf("emporanea") > 0)
                                        {
                                           series.secondAxis.labels.push("Observation");
                                        }

                                        if(realTimeData.results.bindings[i].state.value.indexOf("tali") > 0)
                                        {
                                           series.secondAxis.labels.push("Totals");
                                        }

                                       dataSlot = [];
                                       dataSlot.push(realTimeData.results.bindings[i].redCode.value);
                                       dataSlot.push(realTimeData.results.bindings[i].yellowCode.value);
                                       dataSlot.push(realTimeData.results.bindings[i].greenCode.value);
                                       dataSlot.push(realTimeData.results.bindings[i].blueCode.value);
                                       dataSlot.push(realTimeData.results.bindings[i].whiteCode.value);

                                       series.secondAxis.series.push(dataSlot);
                                    }

                                    var colsQt = parseInt(parseInt(series.firstAxis.labels.length) + 1);
                                    var rowsQt = parseInt(parseInt(series.secondAxis.labels.length) + 1);
                                    
                                    for(var i = 0; i < rowsQt; i++)
                                    {
                                        var newRow = $("<tr></tr>");
                                        var z = parseInt(parseInt(i) -1);

                                        if(i === 0)
                                        {
                                            //Riga di intestazione
                                            for(var j = 0; j < colsQt; j++)
                                            {
                                                if(j === 0)
                                                {
                                                    //Cella (0,0)
                                                    var newCell = $("<td></td>");

                                                    newCell.css("background-color", "transparent");
                                                }
                                                else
                                                {
                                                    //Celle labels
                                                    var k = parseInt(parseInt(j) - 1);
                                                    var colLabelBckColor = null;
                                                    switch(k)
                                                    {
                                                       case 0:
                                                          colLabelBckColor = "#ff0000";
                                                          break;

                                                       case 1:
                                                          colLabelBckColor = "#ffff00";
                                                          break;

                                                       case 2:
                                                          colLabelBckColor = "#66ff33";
                                                          break;

                                                       case 3:
                                                          colLabelBckColor = "#66ccff";
                                                          break;

                                                       case 4:
                                                          colLabelBckColor = "#ffffff";
                                                          break;   
                                                    }

                                                    newCell = $("<td><span>" + series.firstAxis.labels[k] + "</span></td>");
                                                    newCell.css("font-weight", "bold");
                                                    newCell.css("background-color", colLabelBckColor);
                                                }
                                                newRow.append(newCell);
                                            }
                                        }
                                        else
                                        {
                                            //Righe dati
                                            for(var j = 0; j < colsQt; j++)
                                            {
                                                k = parseInt(parseInt(j) -1);
                                                if(j === 0)
                                                {
                                                    //Cella label
                                                    newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                                                    newCell.css("font-weight", "bold");
                                                }
                                                else
                                                {
                                                    //Celle dati
                                                    newCell = $("<td>" + series.secondAxis.series[z][k] + "</td>");
                                                    if(i === (rowsQt - 1))
                                                    {
                                                       newCell.css('font-weight', 'bold');
                                                       switch(j)
                                                       {
                                                          case 1:
                                                             newCell.css('background-color', '#ffb3b3');
                                                             break;

                                                          case 2:
                                                             newCell.css('background-color', '#ffff99');
                                                             break;

                                                          case 3:
                                                             newCell.css('background-color', '#d9ffcc');
                                                             break;

                                                          case 4:
                                                             newCell.css('background-color', '#cceeff');
                                                             break;

                                                          case 5:
                                                             newCell.css('background-color', 'white');
                                                             break;   
                                                       }
                                                    }
                                                }
                                                newRow.append(newCell);
                                            }
                                        }    
                                        content += newRow.prop('outerHTML');
                                    }
                                    
                                    content += '</table></div>';
                            ///////////
                        }else{
                                content += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer ol_rt" id="'+id_name+'_ol_rt">';
                                var realTimeData = geoJsonServiceData.realtime;
                                var last_update = realTimeData.results.bindings[0].measuredTime.value.replace("T", " ");
                                console.log(last_update);
                                content += '<div class="popupLastUpdateContainer centerWithFlex"><b>Last update:&nbsp;</b><span class="popupLastUpdate" data-id="' + latLngId + '">'+last_update+'</span></div>';
                                    //Tabella nuovo stile
                                    content += '<table id="' + id_name + '_table" class="gisPopupTable">';

                                    //Intestazione
                                    content += '<thead>';
                                    content += '<th style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">Description</th>';
                                    content += '<th style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">Value</th>';
                                    content += '<th colspan="5" style="background: ' + evt.color1 + '; background: -webkit-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -o-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: -moz-linear-gradient(right, ' + evt.color1 + ', ' + evt.color2 + '); background: linear-gradient(to right, ' + evt.color1 + ', ' + evt.color2 + ');">Buttons</th>';
                                    content += '</thead>';

                                    //Corpo
                                    content += '<tbody>';
                                    var dataDesc, dataVal, data4HBtn, dataDayBtn, data7DayBtn, data30DayBtn, rtDataAgeSec, datalvBtn = null;
                                    
                                    for(var i = 0; i < realTimeData.head.vars.length; i++)
                                    {
                                        if((realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.trim() !== '')&&(realTimeData.head.vars[i] !== null)&&(realTimeData.head.vars[i] !== 'undefined'))
                                        {
                                            if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                            {
                                                if(!realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.includes('Not Available'))
                                                {
                                                    //realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                                    ////
                                                    dataDesc = realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function(str){ return str.toUpperCase(); });
                                                    dataVal = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value;
                                                    datalvBtn = '<td><button data-id="' + latLngId + '" type="button" class="lastValueBtn btn btn-sm" data-fake="' + evt.fake + '" data-fakeid="' + evt.fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.values_.serviceUri + '" data-lastDataClicked="false" data-targetWidgets="" data-lastValue="' + realTimeData.results.bindings[0][realTimeData.head.vars[i]].value + '" data-color1="' + evt.color1 + '" data-color2="' + evt.color2 + '">Last<br>value</button></td>';
                                                    data4HBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-fakeid="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.values_.serviceUri + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="" data-color1="' + evt.color1 + '" data-color2="' + evt.color2 + '">Last<br>4 hours</button></td>';
                                                    dataDayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.values_.serviceUri + '" data-timeTrendClicked="false" data-range-shown="24 hours" data-range="1/DAY" data-targetWidgets="" data-color1="' + evt.color1 + '" data-color2="' + evt.color2 + '">Last<br>24 hours</button></td>';
                                                    data7DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.values_.serviceUri + '" data-timeTrendClicked="false" data-range-shown="7 days" data-range="7/DAY" data-targetWidgets="" data-color1="' + evt.color1 + '" data-color2="' + evt.color2 + '">Last<br>7 days</button></td>';
                                                    data30DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.values_.serviceUri + '" data-timeTrendClicked="false" data-range-shown="30 days" data-range="30/DAY" data-targetWidgets="" data-color1="' + evt.color1 + '" data-color2="' + evt.color2 + '">Last<br>30 days</button></td>';
                                                    content += '<tr><td>' + dataDesc + '</td><td>' + dataVal + '</td>'+ datalvBtn + data4HBtn + dataDayBtn + data7DayBtn + data30DayBtn + '</tr>';
                                                }
                                            }
                                            else
                                            {
                                                var measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                                //Calcolo age
                                                var now = new Date();
                                                var measuredTimeDate = new Date(measuredTime);
                                                rtDataAgeSec = Math.abs(now - measuredTimeDate)/1000;
                                                console.log("rtDataAgeSec: "+rtDataAgeSec);
                                                ///////////////
                                                $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').off('mouseenter');
                                                $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').off('mouseleave');
                                                $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn[data-id="' + latLngId + '"]').hover(function(){
                                                    if($(this).attr("data-lastDataClicked") === "false")
                                                    {
                                                        $(this).css("background", evt.color1);
                                                        $(this).css("background", "-webkit-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                        $(this).css("background", "background: -o-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                        $(this).css("background", "background: -moz-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                        $(this).css("background", "background: linear-gradient(to left, " + evt.color1 + ", " + evt.color2 + ")");
                                                        $(this).css("font-weight", "bold");
                                                    }

                                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                                    var colIndex = $(this).parent().index();
                                                    //var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html();
                                                    var title = $(this).parents("tr").find("td").eq(0).html();

                                                    for(var i = 0; i < widgetTargetList.length; i++)
                                                    {
                                                        $.event.trigger({
                                                            type: "mouseOverLastDataFromExternalContentGis_" + widgetTargetList[i],
                                                            eventGenerator: $(this),
                                                            targetWidget: widgetTargetList[i],
                                                            value: $(this).attr("data-lastValue"),
                                                            color1: $(this).attr("data-color1"),
                                                            color2: $(this).attr("data-color2"),
                                                            widgetTitle: title
                                                        }); 
                                                    }
                                                }, 
                                                
                                                        );
                                //////////////////
                                $(this).css("background", "-webkit-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                $(this).css("background", "background: -o-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                $(this).css("background", "background: -moz-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                $(this).css("background", "background: linear-gradient(to left, " + evt.color1 + ", " + evt.color2 + ")");
                                 ////////////////
                                            }
                                        }
                                    }
                                    
                                    content += '</tbody>';
                                    content+= '</table>';
                                    content += '<p><b>Keep data on target widget(s) after popup close: </b><input data-id="' + latLngId + '" type="checkbox" class="gisPopupKeepDataCheck" data-keepData="false"/></p>'; 
                                
                                
                                evt.hasRealTime = true;
                                content += '</div>';
                            
                        }
                    }   
                  /////////////////////////////
                 ////////////////////FINE GESTIONE DI RT_DATA//////////
                                           
                                           content_element.innerHTML = content;
                                       if (Array.isArray(coord[0])){
                                                       var poin = coord[0].length;
                                                       var lun = poin/2;
                                                       if (!(coord[0][lun])){
                                                               overlay.setPosition(coord[0][0][0]);
                                                       }else{
                                                               overlay.setPosition(coord[0][lun]);
                                                           }
                                                       }else{
   				overlay.setPosition(coord);
                                               		}
                                                        
                                       /////I DATI VANNO GESTITI QUI////////
                                       //***///GESTIRE QUI LA MODIFICA DEI DATI///***//
                                        if(rtDataAgeSec > 14400)
                                                            {
                                                                $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "true");
                                                                //Disabilitiamo i 24Hours se last update più vecchio di 24 ore
                                                                if(rtDataAgeSec > 86400)
                                                                {
                                                                    $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "true");
                                                                    //Disabilitiamo i 7 days se last update più vecchio di 7 days
                                                                    if(rtDataAgeSec > 604800)
                                                                    {
                                                                        $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "true");
                                                                        //Disabilitiamo i 30 days se last update più vecchio di 30 days
                                                                        if(rtDataAgeSec > 18144000)
                                                                        {
                                                                           $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "true");
                                                                        }
                                                                        else
                                                                        {
                                                                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                                                                }
                                                            }
                                                            else
                                                            {
                                                                $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "false");
                                                                $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                                                                $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                                                $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                                                            }

                                                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').off('mouseenter');
                                                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').off('mouseleave');
                                                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"]').hover(function(){
                                                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                                                {
                                                                    $(this).css("background-color", "#e6e6e6");
                                                                    $(this).off("hover");
                                                                    $(this).off("click");
                                                                }
                                                                else
                                                                {
                                                                    if($(this).attr("data-timeTrendClicked") === "false")
                                                                    {
                                                                        $(this).css("background", evt.color1);
                                                                        $(this).css("background", "-webkit-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                                        $(this).css("background", "background: -o-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                                        $(this).css("background", "background: -moz-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                                        $(this).css("background", "background: linear-gradient(to left, " + evt.color1 + ", " + evt.color2 + ")");
                                                                        $(this).css("font-weight", "bold");
                                                                    }

                                                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                                                    var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");

                                                                    for(var i = 0; i < widgetTargetList.length; i++)
                                                                    {
                                                                        $.event.trigger({
                                                                            type: "mouseOverTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                                                            eventGenerator: $(this),
                                                                            targetWidget: widgetTargetList[i],
                                                                            value: $(this).attr("data-lastValue"),
                                                                            color1: $(this).attr("data-color1"),
                                                                            color2: $(this).attr("data-color2"),
                                                                            widgetTitle: title
                                                                        }); 
                                                                    }
                                                                }
                                                            }, 
                                                            function(){
                                                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                                                {
                                                                    $(this).css("background-color", "#e6e6e6");
                                                                    $(this).off("hover");
                                                                    $(this).off("click");
                                                                }
                                                                else
                                                                {
                                                                    if($(this).attr("data-timeTrendClicked")=== "false")
                                                                    {
                                                                        $(this).css("background", evt.color2);
                                                                        $(this).css("font-weight", "normal"); 
                                                                    }
                                                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                                                    for(var i = 0; i < widgetTargetList.length; i++)
                                                                    {
                                                                        $.event.trigger({
                                                                            type: "mouseOutTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                                                            eventGenerator: $(this),
                                                                            targetWidget: widgetTargetList[i],
                                                                            value: $(this).attr("data-lastValue"),
                                                                            color1: $(this).attr("data-color1"),
                                                                            color2: $(this).attr("data-color2")
                                                                        }); 
                                                                    }
                                                                }
                                                            });
                                                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn[data-id=' + latLngId + ']').off('click');
                                                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn[data-id=' + latLngId + ']').click(function(event){
                                                                $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').each(function(i){
                                                                    $(this).css("background", $(this).attr("data-color2"));
                                                                });
                                                                $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').css("font-weight", "normal");
                                                                $(this).css("background", $(this).attr("data-color1"));
                                                                $(this).css("font-weight", "bold");
                                                                $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').attr("data-lastDataClicked", "false");
                                                                $(this).attr("data-lastDataClicked", "true");
                                                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                                                var colIndex = $(this).parent().index();
                                                                var title = $(this).parents("tr").find("td").eq(0).html();
                                                                for(var i = 0; i < widgetTargetList.length; i++)
                                                                {
                                                                    $.event.trigger({
                                                                        type: "showLastDataFromExternalContentGis_" + widgetTargetList[i],
                                                                        eventGenerator: $(this),
                                                                        targetWidget: widgetTargetList[i],
                                                                        value: $(this).attr("data-lastValue"),
                                                                        color1: $(this).attr("data-color1"),
                                                                        color2: $(this).attr("data-color2"),
                                                                        widgetTitle: title,
                                                                        field: $(this).attr("data-field"),
                                                                        serviceUri: $(this).attr("data-serviceUri"),
                                                                        marker: markersCache["" + $(this).attr("data-id") + ""],
                                                                        mapRef: gisMapRef,
                                                                        fake: $(this).attr("data-fake"),
                                                                        fakeId: $(this).attr("data-fakeId")
                                                                    });
                                                                }
                                                            });
                                                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').off('click');
                                                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').click(function(event){
                                                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                                                {
                                                                            $(this).css("background-color", "#e6e6e6");
                                                                            $(this).off("hover");
                                                                            $(this).off("click");
                                                                }
                                                                else
                                                                {
                                                                    
                                                                    //$('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').css("background", $(this).attr("data-color2"));
                                                                    $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').css("font-weight", "normal");
                                                                    $(this).css("background", $(this).attr("data-color1"));
                                                                    $(this).css("font-weight", "bold");
                                                                    $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                                                    $(this).attr("data-timeTrendClicked", "true");
                                                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                                                    var colIndex = $(this).parent().index();
                                                                    var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");
                                                                    var lastUpdateTime = $(this).parents('div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();
                                                                    var now = new Date();
                                                                    var lastUpdateDate = new Date(lastUpdateTime);
                                                                    var diff = parseFloat(Math.abs(now-lastUpdateDate)/1000);
                                                                    var range = $(this).attr("data-range");
                                                                    
                                                                    for(var i = 0; i < widgetTargetList.length; i++)
                                                                    {
                                                                        $.event.trigger({
                                                                            type: "showTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                                                            eventGenerator: $(this),
                                                                            targetWidget: widgetTargetList[i],
                                                                            range: range,
                                                                            color1: $(this).attr("data-color1"),
                                                                            color2: $(this).attr("data-color2"),
                                                                            widgetTitle: title,
                                                                            field: $(this).attr("data-field"),
                                                                            serviceUri: $(this).attr("data-serviceUri"),
                                                                            marker: markersCache["" + $(this).attr("data-id") + ""],
                                                                            mapRef: gisMapRef,
                                                                            fake: false
                                                                            //fake: $(this).attr("data-fake")
                                                                        }); 
                                                                    }
                                                                }
                                                            });
                                                            
                                                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"]').each(function(i){
                                                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                                                {
                                                                    
                                                                    if($(this).attr("data-disabled") === "true"){
                                                                            $(this).css("background-color", "#e6e6e6");
                                                                            $(this).off("hover");
                                                                            $(this).off("click");
                                                                     }
                                                                }else{
                                                                    $(this).css("background", evt.color2);
                                                                    $(this).css("font-weight", "normal");
                                                                }
                                                            });
                                                            
                                                            
                                       //********************************************//
                                                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').off('mouseenter');
                                                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').off('mouseleave');
                                                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn[data-id="' + latLngId + '"]').hover(function(){
                                                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                                                {
                                                                    $(this).css("background-color", "#e6e6e6");
                                                                    $(this).off("hover");
                                                                    $(this).off("click");
                                                                }
                                                                else
                                                                {
                                                                    if($(this).attr("data-timeTrendClicked") === "false")
                                                                    {
                                                                        $(this).css("background", evt.color1);
                                                                        $(this).css("background", "-webkit-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                                        $(this).css("background", "background: -o-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                                        $(this).css("background", "background: -moz-linear-gradient(left, " + evt.color1 + ", " + evt.color2 + ")");
                                                                        $(this).css("background", "background: linear-gradient(to left, " + evt.color1 + ", " + evt.color2 + ")");
                                                                        $(this).css("font-weight", "bold");
                                                                    }

                                                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                                                    var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");

                                                                    for(var i = 0; i < widgetTargetList.length; i++)
                                                                    {
                                                                        $.event.trigger({
                                                                            type: "mouseOverTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                                                            eventGenerator: $(this),
                                                                            targetWidget: widgetTargetList[i],
                                                                            value: $(this).attr("data-lastValue"),
                                                                            color1: $(this).attr("data-color1"),
                                                                            color2: $(this).attr("data-color2"),
                                                                            widgetTitle: title
                                                                        }); 
                                                                    }
                                                                }
                                                            }, 
                                                            function(){
                                                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                                                {
                                                                    $(this).css("background-color", "#e6e6e6");
                                                                    $(this).off("hover");
                                                                    $(this).off("click");
                                                                }
                                                                else
                                                                {
                                                                    if($(this).attr("data-timeTrendClicked")=== "false")
                                                                    {
                                                                        $(this).css("background", evt.color2);
                                                                        $(this).css("font-weight", "normal"); 
                                                                    }
                                                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                                                    for(var i = 0; i < widgetTargetList.length; i++)
                                                                    {
                                                                        $.event.trigger({
                                                                            type: "mouseOutTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                                                            eventGenerator: $(this),
                                                                            targetWidget: widgetTargetList[i],
                                                                            value: $(this).attr("data-lastValue"),
                                                                            color1: $(this).attr("data-color1"),
                                                                            color2: $(this).attr("data-color2")
                                                                        }); 
                                                                    }
                                                                }
                                                            });
                                                            //
                                                            //////
                                                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn[data-id="' + latLngId + '"]').each(function(i){
                                                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                                                {
                                                                    $(this).css("background-color", "#e6e6e6");
                                                                    $(this).off("hover");
                                                                    $(this).off("click");
                                                                }else{
                                                                    $(this).css("background", evt.color2);
                                                                    $(this).css("font-weight", "normal"); 
                                                                }
                                                            });
                                               /////////////////////////////////////
                                             }
                                         });
                                    }
   //ExternalContent_1226_widgetGisWFS8061_popup
   /******/
   /*
                           content_element.innerHTML = content;
                                       if (Array.isArray(coord[0])){
                                                       var poin = coord[0].length;
                                                       var lun = poin/2;
                                                       if (!(coord[0][lun])){
                                                               overlay.setPosition(coord[0][0][0]);
                                                       }else{
                                                               overlay.setPosition(coord[0][lun]);
                                                           }
                                                       }else{
   				overlay.setPosition(coord);
                                               		}
   */
   			}
   });
   map.on('pointermove', function(e) {
   	if (e.dragging) return;					   
   	var pixel = map.getEventPixel(e.originalEvent);
   	var hit = map.hasFeatureAtPixel(pixel);
   	map.getTarget().style.cursor = hit ? 'pointer' : '';
   	  });
    }else{
   $('#<?= $_REQUEST['name_w'] ?>_popup').hide();
    }                             
   //
     
   });//Fine document ready
   
   //
   function myFunctionDet(id_d) {
   var id0=id_d.id;
   $('#'+id0+'_ol_detail').show();
   $('#'+id0+'_ol_descr').hide();
   $('#'+id0+'_ol_rt').hide();
   $('#'+id0+'_detailBtn').css({"font-weight":"bold"});
   $('#'+id0+'_dscrBtn').css({"font-weight":"normal"});
   $('#'+id0+'_rtBtn').css({"font-weight":"normal"});
   //
   }
   
   function myFunctionDecr(id_d) {
   var id0=id_d.id;
   $('#'+id0+'_ol_detail').hide();
   $('#'+id0+'_ol_rt').hide();
   $('#'+id0+'_ol_descr').show();
   $('#'+id0+'_detailBtn').css({"font-weight":"normal"});
   $('#'+id0+'_dscrBtn').css({"font-weight":"bold"});
   $('#'+id0+'_rtBtn').css({"font-weight":"normal"});
   //
   }
   
   function myFunctionRT(id_d){
   var id0=id_d.id;
   $('#'+id0+'_ol_detail').hide();
   $('#'+id0+'_ol_descr').hide();
   $('#'+id0+'_ol_rt').show();
   $('#'+id0+'_detailBtn').css({"font-weight":"normal"});
   $('#'+id0+'_dscrBtn').css({"font-weight":"normal"});
   $('#'+id0+'_rtBtn').css({"font-weight":"bold"});   
   }
   
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div" data-emptyMapShown="false">
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
         <p id="<?= $_REQUEST['name_w'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
         <div id="<?= $_REQUEST['name_w'] ?>_mapDiv" class="mapDiv"></div>
         <div id="<?= $_REQUEST['name_w'] ?>_gisMapDiv" class="gisMapDiv"></div>
         <div id="<?= $_REQUEST['name_w'] ?>_wrapper" class="iframeWrapper">
            <div id="<?= $_REQUEST['name_w'] ?>_zoomDisplay" class="zoomDisplay"></div>
            <iframe id="<?= $_REQUEST['name_w'] ?>_iFrame" class="iFrame"></iframe>
         </div>
         <div id="<?= $_REQUEST['name_w'] ?>_defaultMapDiv" class="defaultMapDiv"></div>
         <div id="<?= $_REQUEST['name_w'] ?>_defaultMapPicDiv" class="defaultMapPic"></div>
         <input type="hidden" id="<?= $_REQUEST['name_w'] ?>_driverWidgetType" val=""/>
         <input type="hidden" id="<?= $_REQUEST['name_w'] ?>_netAnalysisServiceMapUrl" val=""/>
         <input type="hidden" id="<?= $_REQUEST['name_w'] ?>_buttonUrl" val=""/>
         <input type="hidden" id="<?= $_REQUEST['name_w'] ?>_recreativeEventsUrl" val=""/>
      </div>
   </div>
</div>
<div class="leaflet-pane leaflet-popup-pane">
   <div id="<?= $_REQUEST['name_w'] ?>_popup" class="ol-popup  leaflet-popup  leaflet-zoom-animated">
      <a href="#" id="<?= $_REQUEST['name_w'] ?>_ol-popup-closer" class="ol-popup-closer"></a>
      <div class="leaflet-popup-content-wrapper">
         <div id="<?= $_REQUEST['name_w'] ?>_popup-content"></div>
      </div>
   </div>
</div>