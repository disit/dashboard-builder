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
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetExternalContent.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
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
        var myPOIId, myPOIlat, myPOIlng = "";
        console.log("External Content: " + widgetName);
        
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
                                '<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisMap" class="modalLinkOpenGisMap" data-mapRef="null"></div>' +
                                '<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend" class="modalLinkOpenGisTimeTrend"></div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" id="<?= $_REQUEST['name_w'] ?>_modalLinkOpenCloseBtn" class="btn btn-primary">Back to dashboard</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>');
                                    
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
            defaultMapRef = L.map(mapDivLocal).setView([43.769789, 11.255694], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
               maxZoom: 18
            }).addTo(defaultMapRef);
            defaultMapRef.attributionControl.setPrefix('');
        }
        
        //Creazione della mappa vuota per il widget in modalità GIS target, non per il suo popup fullscreen
        function loadGisMap()
        {
            var mapDivLocal = "<?= $_REQUEST['name_w'] ?>_gisMapDiv";
            
            gisMapRef = L.map(mapDivLocal);
            
            /*gisMapRef.on('load', function(){
                $.ajax({
                    url: "../management/nrSendGpsProxy.php",
                    type: "POST",
                    data: {
                        httpRelativeUrl: coordsCollectionUri,
                        username: widgetProperties.param.creator,
                        dashboardTitle: $('#dashboardTitle span').text(),
                        gpsData: JSON.stringify({
                            latitude: gisMapRef.getCenter().lat,
                            longitude: gisMapRef.getCenter().lng,
                            accuracy: null,
                            altitude: null,
                            altitudeAccuracy: null,
                            heading: null,
                            speed: null
                        })
                    },
                    async: true,
                    dataType: 'json',
                    success: function(data)
                    {
                        console.log("Map center sent OK");
                        console.log(JSON.stringify(data));
                    },
                    error: function(errorData)
                    {
                        console.log("Map center sent KO");
                        console.log(JSON.stringify(errorData));
                    }
                });
            });*/
            
            /*gisMapRef.on('moveend', function(){
                $.ajax({
                    url: "../management/nrSendGpsProxy.php",
                    type: "POST",
                    data: {
                        httpRelativeUrl: coordsCollectionUri,
                        username: widgetProperties.param.creator,
                        dashboardTitle: $('#dashboardTitle span').text(),
                        gpsData: JSON.stringify({
                            latitude: gisMapRef.getCenter().lat,
                            longitude: gisMapRef.getCenter().lng,
                            accuracy: null,
                            altitude: null,
                            altitudeAccuracy: null,
                            heading: null,
                            speed: null
                        })
                    },
                    async: true,
                    dataType: 'json',
                    success: function(data)
                    {
                        console.log("Map center sent OK");
                        console.log(JSON.stringify(data));
                    },
                    error: function(errorData)
                    {
                        console.log("Map center sent KO");
                        console.log(JSON.stringify(errorData));
                    }
                });
            });*/
             
            
            gisMapRef.setView(widgetParameters.latLng, widgetParameters.zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
               maxZoom: 18,
               closePopupOnClick: false
            }).addTo(gisMapRef);
            gisMapRef.attributionControl.setPrefix('');
        }
        
        //Funzione eseguita per ciascuna feature della mappa GIS dopo che le feature vengano aggiunte - Per ora non usata
        function gisPrepareEachFeature(feature, layer) 
        {
            
        }
        
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
        
        //Funzione di associazione delle icone alle feature e preparazione popup per la mappa GIS
        function gisPrepareCustomMarker(feature, latlng) {
            var mapPinImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
            var markerIcon = L.icon({
                iconUrl: mapPinImg,
                iconAnchor: [16, 37]
            });
            
            var marker = new L.Marker(latlng, {icon: markerIcon});
           
            var latLngKey = latlng.lat + "" + latlng.lng;
            latLngKey = latLngKey.replace(".", "");
            latLngKey = latLngKey.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona
            markersCache["" + latLngKey + ""] = marker;
            
            marker.on('mouseover', function(event) {
                var hoverImg = '../img/gisMapIcons/over/' + feature.properties.serviceType + '_over.png';
                var hoverIcon = L.icon({
                    iconUrl: hoverImg
                });
                event.target.setIcon(hoverIcon);
            });
            
            marker.on('mouseout', function (event) {
                var outImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                var outIcon = L.icon({
                    iconUrl: outImg
                });
                event.target.setIcon(outIcon);
            });
            
            marker.on('click', function(event){
                gisMapRef.off('moveend');
                
                /*$.ajax({
                    url: "../management/nrSendGpsProxy.php",
                    type: "POST",
                    data: {
                        httpRelativeUrl: coordsCollectionUri,
                        username: widgetProperties.param.creator,
                        dashboardTitle: $('#dashboardTitle span').text(),
                        gpsData: JSON.stringify({
                            latitude: latlng.lat,
                            longitude: latlng.lng,
                            accuracy: null,
                            altitude: null,
                            altitudeAccuracy: null,
                            heading: null,
                            speed: null
                        })
                    },
                    async: true,
                    dataType: 'json',
                    success: function(data)
                    {
                        console.log("Map center sent from click OK");
                        console.log(JSON.stringify(data));
                    },
                    error: function(errorData)
                    {
                        console.log("Map center sent from click KO");
                        console.log(JSON.stringify(errorData));
                    },
                    complete: function()
                    {
                        setTimeout(function(){
                            gisMapRef.on('moveend', function(){
                                $.ajax({
                                    url: "../management/nrSendGpsProxy.php",
                                    type: "POST",
                                    data: {
                                        httpRelativeUrl: coordsCollectionUri,
                                        username: widgetProperties.param.creator,
                                        dashboardTitle: $('#dashboardTitle span').text(),
                                        gpsData: JSON.stringify({
                                            latitude: gisMapRef.getCenter().lat,
                                            longitude: gisMapRef.getCenter().lng,
                                            accuracy: null,
                                            altitude: null,
                                            altitudeAccuracy: null,
                                            heading: null,
                                            speed: null
                                        })
                                    },
                                    async: true,
                                    dataType: 'json',
                                    success: function(data)
                                    {
                                        console.log("Map center sent OK");
                                        console.log(JSON.stringify(data));
                                    },
                                    error: function(errorData)
                                    {
                                        console.log("Map center sent KO");
                                        console.log(JSON.stringify(errorData));
                                    }
                                });
                            }); 
                        }, 1000);
                    }
                });*/
                
                event.target.unbindPopup();
                newpopup = null;
                var popupText, realTimeData, measuredTime, rtDataAgeSec, targetWidgets, color1, color2 = null;
                var urlToCall, fake, fakeId = null;
                
                if(feature.properties.fake === 'true')
                {
                    urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + feature.id;
                    fake = true;
                    fakeId = feature.id;
                }
                else
                {
                  //  urlToCall = "<?php echo $superServiceMapUrlPrefix; ?>api/v1/?serviceUri=" + feature.properties.serviceUri + "&format=json";
                    urlToCall = "<?php echo $superServiceMapUrlPrefix; ?>api/v1/?serviceUri=" + encodeURI(feature.properties.serviceUri) + "&format=json&fullCount=false";
                    fake = false;
                }
                
                var latLngId = event.target.getLatLng().lat + "" + event.target.getLatLng().lng;
                latLngId = latLngId.replace(".", "");
                latLngId = latLngId.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona
                
                $.ajax({
                    url: urlToCall,
                    type: "GET",
                    data: {},
                    async: true,
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
                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");

                        fatherNode.features[0].properties.targetWidgets = feature.properties.targetWidgets;
                        fatherNode.features[0].properties.color1 = feature.properties.color1;
                        fatherNode.features[0].properties.color2 = feature.properties.color2;
                        targetWidgets = feature.properties.targetWidgets;
                        color1 = feature.properties.color1;
                        color2 = feature.properties.color2;
                        
                        //Popup nuovo stile uguali a quelli degli eventi ricreativi
                        popupText = '<h3 class="recreativeEventMapTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + serviceProperties.name + '</h3>';
                        if((serviceProperties.serviceUri !== '')&&(serviceProperties.serviceUri !== undefined)&&(serviceProperties.serviceUri !== 'undefined')&&(serviceProperties.serviceUri !== null)&&(serviceProperties.serviceUri !== 'null')) {
                            popupText += '<div class="recreativeEventMapSubTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + "Value Name: " + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + '</div>';
                           // popupText += '<div class="recreativeEventMapSubTitle">' + "Value Name: " + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + '</div>';
                        }
                        popupText += '<div class="recreativeEventMapBtnContainer"><button data-id="' + latLngId + '" class="recreativeEventMapDetailsBtn recreativeEventMapBtn recreativeEventMapBtnActive" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Details</button><button data-id="' + latLngId + '" class="recreativeEventMapDescriptionBtn recreativeEventMapBtn" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Description</button><button data-id="' + latLngId + '" class="recreativeEventMapContactsBtn recreativeEventMapBtn" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">RT data</button></div>';

                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer">';
                        
                        popupText += '<table id="' + latLngId + '" class="gisPopupGeneralDataTable">';
                        //Intestazione
                        popupText += '<thead>';
                        popupText += '<th style="background: ' + color2 + '">Description</th>';
                        popupText += '<th style="background: ' + color2 + '">Value</th>';
                        popupText += '</thead>';

                        //Corpo
                        popupText += '<tbody>';

                        if((serviceProperties.serviceUri !== '')&&(serviceProperties.serviceUri !== undefined)&&(serviceProperties.serviceUri !== 'undefined')&&(serviceProperties.serviceUri !== null)&&(serviceProperties.serviceUri !== 'null')) {
                            popupText += '<tr><td>Value Name</td><td>' + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + '<td></tr>';
                        }

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
                        
                        popupText += '</tbody>';
                        popupText += '</table>';
                        
                        if(geoJsonServiceData.hasOwnProperty('busLines'))
                        {
                            if(geoJsonServiceData.busLines.results.bindings.length > 0)
                            {
                                popupText += '<b>Lines: </b>';
                                for(var i = 0; i < geoJsonServiceData.busLines.results.bindings.length; i++)
                                {
                                   popupText += '<span style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + geoJsonServiceData.busLines.results.bindings[i].busLine.value + '</span> ';     
                                }
                            }
                        }
                        
                        popupText += '</div>';
                        
                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';

                        if((serviceProperties.serviceUri !== '')&&(serviceProperties.serviceUri !== undefined)&&(serviceProperties.serviceUri !== 'undefined')&&(serviceProperties.serviceUri !== null)&&(serviceProperties.serviceUri !== 'null')) {
                            popupText += "Value Name: " + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + "<br>";
                        }

                        if((serviceProperties.serviceType !== '')&&(serviceProperties.serviceType !== undefined)&&(serviceProperties.serviceType !== 'undefined')&&(serviceProperties.serviceType !== null)&&(serviceProperties.serviceType !== 'null')) {
                            popupText += "Nature: " + serviceProperties.serviceType.split(/_(.+)/)[0] + "<br>";
                            popupText += "Subnature: " + serviceProperties.serviceType.split(/_(.+)/)[1] + "<br><br>";
                        }

                        if(serviceProperties.hasOwnProperty('description'))
                        {
                            if((serviceProperties.description !== '')&&(serviceProperties.description !== undefined)&&(serviceProperties.description !== 'undefined')&&(serviceProperties.description !== null)&&(serviceProperties.description !== 'null'))
                            {
                                popupText += serviceProperties.description + "<br>";
                            }
                            else
                            {
                                popupText += "No description available";
                            }
                        }
                        else
                        {
                            popupText += 'No description available';
                        }
                        
                        popupText += '</div>';
                        
                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';
                        
                        var hasRealTime = false;
                        
                        if(geoJsonServiceData.hasOwnProperty("realtime"))
                        {
                            if(!jQuery.isEmptyObject(geoJsonServiceData.realtime))
                            {
                                realTimeData = geoJsonServiceData.realtime;
                                popupText += '<div class="popupLastUpdateContainer centerWithFlex"><b>Last update:&nbsp;</b><span class="popupLastUpdate" data-id="' + latLngId + '"></span></div>';
                                
                                if((serviceClass.includes("Emergency"))&&(serviceSubclass.includes("First aid")))
                                {
                                    //Tabella ad hoc per First Aid
                                    popupText += '<table id="' + latLngId + '" class="psPopupTable">';
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
                                        popupText += newRow.prop('outerHTML');
                                    }
                                    
                                    popupText += '</table>';
                                }
                                else
                                {
                                    //Tabella nuovo stile
                                    popupText += '<table id="' + latLngId + '" class="gisPopupTable">';

                                    //Intestazione
                                    popupText += '<thead>';
                                    popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Description</th>';
                                    popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Value</th>';
                                    popupText += '<th colspan="5" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Buttons</th>';
                                    popupText += '</thead>';

                                    //Corpo
                                    popupText += '<tbody>';
                                    var dataDesc, dataVal, dataLastBtn, data4HBtn, dataDayBtn, data7DayBtn, data30DayBtn = null;
                                    for(var i = 0; i < realTimeData.head.vars.length; i++)
                                    {
                                        if(realTimeData.results.bindings[0][realTimeData.head.vars[i]] !== null && realTimeData.results.bindings[0][realTimeData.head.vars[i]] !== undefined) {
                                            if ((realTimeData.results.bindings[0][realTimeData.head.vars[i]]) && (realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.trim() !== '') && (realTimeData.head.vars[i] !== null) && (realTimeData.head.vars[i] !== 'undefined')) {
                                                if ((realTimeData.head.vars[i] !== 'updating') && (realTimeData.head.vars[i] !== 'measuredTime') && (realTimeData.head.vars[i] !== 'instantTime')) {
                                                    if (!realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.includes('Not Available')) {
                                                        //realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                                     /*   dataDesc = realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function (str) {
                                                            return str.toUpperCase();
                                                        }); */
                                                        dataDesc = realTimeData.head.vars[i];
                                                        dataVal = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value;
                                                        dataLastBtn = '<td><button data-id="' + latLngId + '" type="button" class="lastValueBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-lastDataClicked="false" data-targetWidgets="' + targetWidgets + '" data-lastValue="' + realTimeData.results.bindings[0][realTimeData.head.vars[i]].value + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>value</button></td>';
                                                        data4HBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>4 hours</button></td>';
                                                        dataDayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="Day" data-range="1/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>24 hours</button></td>';
                                                        data7DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="7 days" data-range="7/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>7 days</button></td>';
                                                        data30DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="30 days" data-range="30/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>30 days</button></td>';
                                                        popupText += '<tr><td>' + dataDesc + '</td><td>' + dataVal + '</td>' + dataLastBtn + data4HBtn + dataDayBtn + data7DayBtn + data30DayBtn + '</tr>';
                                                    }
                                                } else {
                                                    measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                                    var now = new Date();
                                                    var measuredTimeDate = new Date(measuredTime);
                                                    rtDataAgeSec = Math.abs(now - measuredTimeDate) / 1000;
                                                }
                                            }
                                        }
                                    }
                                    popupText += '</tbody>';
                                    popupText += '</table>';
                                    popupText += '<p><b>Keep data on target widget(s) after popup close: </b><input data-id="' + latLngId + '" type="checkbox" class="gisPopupKeepDataCheck" data-keepData="false"/></p>'; 
                                }
                                
                                hasRealTime = true;
                            }
                        }
                        
                        popupText += '</div>';
                        
                        newpopup = L.popup({
                            closeOnClick: false,//Non lo levare, sennò autoclose:false non funziona
                            autoClose: false,
                            offset: [15, 0], 
                            minWidth: 435, 
                            maxWidth : 435
                        }).setContent(popupText);
                        
                        event.target.bindPopup(newpopup).openPopup();
                        
                        if(hasRealTime)
                        {
                            $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').show();
                            $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                            $('#<?= $_REQUEST['name_w'] ?> span.popupLastUpdate[data-id="' + latLngId + '"]').html(measuredTime);
                        }
                        else
                        {
                            $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').hide();
                        }
                        
                        $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').off('click');
                        $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').click(function(){
                            $('#' + widgetName + '_gisMapDiv div.recreativeEventMapDataContainer').hide();
                            $('#' + widgetName + '_gisMapDiv div.recreativeEventMapDetailsContainer').show();
                            $('#' + widgetName + '_gisMapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                            $(this).addClass('recreativeEventMapBtnActive');
                        });
                        
                        $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').off('click');
                        $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').click(function(){
                            $('#' + widgetName + '_gisMapDiv div.recreativeEventMapDataContainer').hide();
                            $('#' + widgetName + '_gisMapDiv div.recreativeEventMapDescContainer').show();
                            $('#' + widgetName + '_gisMapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                            $(this).addClass('recreativeEventMapBtnActive');
                        });

                        $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').off('click');
                        $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').click(function(){
                            $('#' + widgetName + '_gisMapDiv div.recreativeEventMapDataContainer').hide();
                            $('#' + widgetName + '_gisMapDiv div.recreativeEventMapContactsContainer').show();
                            $('#' + widgetName + '_gisMapDiv button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                            $(this).addClass('recreativeEventMapBtnActive');
                        }); 
                        
                        if(hasRealTime)
                        {
                            $('#<?= $_REQUEST['name_w'] ?> button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                        }
                        
                        $('#<?= $_REQUEST['name_w'] ?> table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("background", color2);
                        $('#<?= $_REQUEST['name_w'] ?> table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("border", "none");
                        $('#<?= $_REQUEST['name_w'] ?> table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("color", "black");

                        $('#<?= $_REQUEST['name_w'] ?> table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').focus(function(){
                            $(this).css("outline", "0");
                        });
                        
                        $('#<?= $_REQUEST['name_w'] ?> input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').off('click');
                        $('#<?= $_REQUEST['name_w'] ?> input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').click(function(){
                            if($(this).attr("data-keepData") === "false")
                            {
                               $(this).attr("data-keepData", "true"); 
                            }
                            else
                            {
                               $(this).attr("data-keepData", "false"); 
                            }
                        });

                        $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').off('mouseenter');
                        $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').off('mouseleave');
                        $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn[data-id="' + latLngId + '"]').hover(function(){
                            if($(this).attr("data-lastDataClicked") === "false")
                            {
                                $(this).css("background", color1);
                                $(this).css("background", "-webkit-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                $(this).css("background", "background: -o-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                $(this).css("background", "background: -moz-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                $(this).css("background", "background: linear-gradient(to left, " + color1 + ", " + color2 + ")");
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
                        function(){
                            if($(this).attr("data-lastDataClicked")=== "false")
                            {
                                $(this).css("background", color2);
                                $(this).css("font-weight", "normal"); 
                            }
                            var widgetTargetList = $(this).attr("data-targetWidgets").split(',');

                            for(var i = 0; i < widgetTargetList.length; i++)
                            {
                                $.event.trigger({
                                    type: "mouseOutLastDataFromExternalContentGis_" + widgetTargetList[i],
                                    eventGenerator: $(this),
                                    targetWidget: widgetTargetList[i],
                                    value: $(this).attr("data-lastValue"),
                                    color1: $(this).attr("data-color1"),
                                    color2: $(this).attr("data-color2")
                                }); 
                            }
                        });
                        
                        //Disabilitiamo i 4Hours se last update più vecchio di 4 ore
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
                                    $(this).css("background", color1);
                                    $(this).css("background", "-webkit-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                    $(this).css("background", "background: -o-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                    $(this).css("background", "background: -moz-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                    $(this).css("background", "background: linear-gradient(to left, " + color1 + ", " + color2 + ")");
                                    $(this).css("font-weight", "bold");
                                }

                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                //var colIndex = $(this).parent().index();
                                //var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html() + " - " + $(this).attr("data-range-shown");
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
                                    $(this).css("background", color2);
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
                                $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').css("background", $(this).attr("data-color2"));
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

                                $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"]').each(function(i){
                                    if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                    {
                                        $(this).css("background-color", "#e6e6e6");
                                        $(this).off("hover");
                                        $(this).off("click");
                                    }
                                });
                            }
                        });
                        
                        $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"]').each(function(i){
                            if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                            {
                                $(this).css("background-color", "#e6e6e6");
                                $(this).off("hover");
                                $(this).off("click");
                            }
                        });

                        gisMapRef.off('popupclose');
                        gisMapRef.on('popupclose', function(closeEvt) {
                            var popupContent = $('<div></div>');
                            popupContent.html(closeEvt.popup._content);
                            
                            if(popupContent.find("button.lastValueBtn").length > 0)
                            {
                                var widgetTargetList = popupContent.find("button.lastValueBtn").eq(0).attr("data-targetWidgets").split(',');

                                if(($('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn[data-lastDataClicked=true]').length > 0)&&($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false"))
                                {
                                    for(var i = 0; i < widgetTargetList.length; i++)
                                    {
                                        $.event.trigger({
                                            type: "restoreOriginalLastDataFromExternalContentGis_" + widgetTargetList[i],
                                            eventGenerator: $(this),
                                            targetWidget: widgetTargetList[i],
                                            value: $(this).attr("data-lastValue"),
                                            color1: $(this).attr("data-color1"),
                                            color2: $(this).attr("data-color2")
                                        }); 
                                    } 
                                }

                                if(($('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-timeTrendClicked=true]').length > 0)&&($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false"))
                                {
                                    for(var i = 0; i < widgetTargetList.length; i++)
                                    {
                                        $.event.trigger({
                                            type: "restoreOriginalTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                            eventGenerator: $(this),
                                            targetWidget: widgetTargetList[i]
                                        }); 
                                    } 
                                } 
                            }
                        });

                        $('#<?= $_REQUEST['name_w'] ?> div.leaflet-popup').off('click');
                     /*   $('#<?= $_REQUEST['name_w'] ?> div.leaflet-popup').on('click', function(){
                            var compLatLngId = $(this).find('input[type=hidden]').val();

                            $('#<?= $_REQUEST['name_w'] ?> div.leaflet-popup').css("z-index", "-1");
                            $(this).css("z-index", "999999");

                            $('#<?= $_REQUEST['name_w'] ?> input.gisPopupKeepDataCheck').off('click');
                            $('#<?= $_REQUEST['name_w'] ?> input.gisPopupKeepDataCheck[data-id="' + compLatLngId + '"]').click(function(){
                            if($(this).attr("data-keepData") === "false")
                                {
                                   $(this).attr("data-keepData", "true"); 
                                }
                                else
                                {
                                   $(this).attr("data-keepData", "false"); 
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').off('mouseenter');
                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').off('mouseleave');
                            $(this).find('button.lastValueBtn[data-id="' + compLatLngId + '"]').hover(function(){
                                if($(this).attr("data-lastDataClicked") === "false")
                                {
                                    $(this).css("background", $(this).attr('data-color1'));
                                    $(this).css("background", "-webkit-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                    $(this).css("background", "background: -o-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                    $(this).css("background", "background: -moz-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                    $(this).css("background", "background: linear-gradient(to left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
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
                            function(){
                                if($(this).attr("data-lastDataClicked")=== "false")
                                {
                                    $(this).css("background", $(this).attr('data-color2'));
                                    $(this).css("font-weight", "normal"); 
                                }
                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');

                                for(var i = 0; i < widgetTargetList.length; i++)
                                {
                                    $.event.trigger({
                                        type: "mouseOutLastDataFromExternalContentGis_" + widgetTargetList[i],
                                        eventGenerator: $(this),
                                        targetWidget: widgetTargetList[i],
                                        value: $(this).attr("data-lastValue"),
                                        color1: $(this).attr("data-color1"),
                                        color2: $(this).attr("data-color2")
                                    }); 
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').off('mouseenter');
                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').off('mouseleave');
                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + compLatLngId + '"]').hover(function()
                            {
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
                                        $(this).css("background", $(this).attr('data-color1'));
                                        $(this).css("background", "-webkit-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                        $(this).css("background", "background: -o-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                        $(this).css("background", "background: -moz-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                        $(this).css("background", "background: linear-gradient(to left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                        $(this).css("font-weight", "bold");
                                    }

                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                    var colIndex = $(this).parent().index();
                                    //var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html() + " - " + $(this).attr("data-range-shown");
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
                                        $(this).css("background", $(this).attr('data-color2'));
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

                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').off('click');
                            $('#<?= $_REQUEST['name_w'] ?> button.lastValueBtn').click(function(event){
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
                                        marker: markersCache["" + $(this).attr("data-id") + ""],
                                        mapRef: gisMapRef,
                                        field: $(this).attr("data-field"),
                                        serviceUri: $(this).attr("data-serviceUri"),
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
                                    $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').each(function(i){
                                        $(this).css("background", $(this).attr("data-color2"));
                                    });
                                    $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').css("font-weight", "normal");
                                    $(this).css("background", $(this).attr("data-color1"));
                                    $(this).css("font-weight", "bold");
                                    $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                    $(this).attr("data-timeTrendClicked", "true");
                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                    var colIndex = $(this).parent().index();
                                    var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");
                                    var lastUpdateTime = $(this).parents('#<?= $_REQUEST['name_w'] ?> div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();

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
                                            fake: $(this).attr("data-fake"),
                                            fakeId: $(this).attr("data-fakeId")
                                        }); 
                                    }
                                }
                            });
                            
                            $('#<?= $_REQUEST['name_w'] ?> button.timeTrendBtn[data-id="' + latLngId + '"]').each(function(i){
                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                {
                                    $(this).css("background-color", "#e6e6e6");
                                    $(this).off("hover");
                                    $(this).off("click");
                                }
                            });
                        }); */
                    },
                    error: function(errorData)
                    {
                        console.log("Error in data retrieval");
                        console.log(JSON.stringify(errorData));
                        var serviceProperties = feature.properties;
                        
                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");
                        
                        popupText = '<h3 class="gisPopupTitle">' + serviceProperties.name + '</h3>' +
                                    '<p><b>Typology: </b>' + serviceClass + " - " + serviceSubclass + '</p>' +
                                    '<p><i>Data are limited due to an issue in their retrieval</i></p>';
                            
                        event.target.bindPopup(popupText, {
                            offset: [15, 0], 
                            minWidth: 215, 
                            maxWidth : 600
                        }).openPopup();    
                    }
                });
            });
            
            return marker;
        }
        
        //Funzione di associazione delle icone alle feature e preparazione popup per la mappa GIS
        function gisPrepareCustomMarkerForFullscreen(feature, latlng) {
            var mapPinImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
            var markerIcon = L.icon({
                iconUrl: mapPinImg,
                iconAnchor: [16, 37]
            });
            
            var marker = new L.Marker(latlng, {icon: markerIcon});
            
            marker.on('mouseover', function(event) {
                var hoverImg = '../img/gisMapIcons/over/' + feature.properties.serviceType + '_over.png';
                var hoverIcon = L.icon({
                    iconUrl: hoverImg
                });
                event.target.setIcon(hoverIcon);
            });
            
            marker.on('mouseout', function (event) {
                var outImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                var outIcon = L.icon({
                    iconUrl: outImg
                });
                event.target.setIcon(outIcon);
            });
            
            marker.on('click', function(event){
                var popupText, realTimeData, measuredTime, rtDataAge, targetWidgets, color1, color2 = null;
                if(feature.properties.fake === 'true')
                {
                    urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + feature.id;
                    fake = true;
                    fakeId = feature.id;
                }
                else
                {
                  //  urlToCall = "<?php echo $superServiceMapUrlPrefix; ?>api/v1/?serviceUri=" + feature.properties.serviceUri + "&format=json";
                    urlToCall = "<?php echo $superServiceMapUrlPrefix; ?>api/v1/?serviceUri=" + encodeURI(feature.properties.serviceUri) + "&format=json&fullCount=false";
                    fake = false;
                }
                
                $.ajax({
                    url: urlToCall,
                    type: "GET",
                    data: {},
                    async: true,
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
                                fatherNode = geoJsonServiceData.Service;
                            }
                        }
                        
                        event.target.unbindPopup();
                        var serviceProperties = fatherNode.features[0].properties;
                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");

                        var latLngId = event.target.getLatLng().lat + "" + event.target.getLatLng().lng;
                        latLngId = latLngId.replace(".", "");
                        latLngId = latLngId.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona

                        fatherNode.features[0].properties.targetWidgets = feature.properties.targetWidgets;
                        fatherNode.features[0].properties.color1 = feature.properties.color1;
                        fatherNode.features[0].properties.color2 = feature.properties.color2;
                        targetWidgets = feature.properties.targetWidgets;
                        color1 = feature.properties.color1;
                        color2 = feature.properties.color2;
                        
                        //Popup nuovo stile uguali a quelli degli eventi ricreativi
                        popupText = '<h3 class="recreativeEventMapTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + serviceProperties.name + '</h3>';
                        if((serviceProperties.serviceUri !== '')&&(serviceProperties.serviceUri !== undefined)&&(serviceProperties.serviceUri !== 'undefined')&&(serviceProperties.serviceUri !== null)&&(serviceProperties.serviceUri !== 'null')) {
                            popupText += '<div class="recreativeEventMapSubTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + "Value Name: " + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + '</div>';
                          //  popupText += '<div class="recreativeEventMapSubTitle">' + "Value Name: " + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + '</div>';
                        }
                        popupText += '<div class="recreativeEventMapBtnContainer"><button data-id="' + latLngId + '" class="recreativeEventMapDetailsBtn recreativeEventMapBtn recreativeEventMapBtnActive" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Details</button><button data-id="' + latLngId + '" class="recreativeEventMapDescriptionBtn recreativeEventMapBtn" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Description</button><button data-id="' + latLngId + '" class="recreativeEventMapContactsBtn recreativeEventMapBtn" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">RT data</button></div>';

                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer">';
                        
                        popupText += '<table id="' + latLngId + '" class="gisPopupGeneralDataTable">';
                        //Intestazione
                        popupText += '<thead>';
                        popupText += '<th style="background: ' + color2 + '">Description</th>';
                        popupText += '<th style="background: ' + color2 + '">Value</th>';
                        popupText += '</thead>';

                        //Corpo
                        popupText += '<tbody>';

                        if((serviceProperties.serviceUri !== '')&&(serviceProperties.serviceUri !== undefined)&&(serviceProperties.serviceUri !== 'undefined')&&(serviceProperties.serviceUri !== null)&&(serviceProperties.serviceUri !== 'null')) {
                            popupText += '<tr><td>Value Name</td><td>' + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + '<td></tr>';
                        }

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
                        
                        popupText += '</tbody>';
                        popupText += '</table>';
                        
                        if(geoJsonServiceData.hasOwnProperty('busLines'))
                        {
                            if(geoJsonServiceData.busLines.results.bindings.length > 0)
                            {
                                popupText += '<b>Lines: </b>';
                                for(var i = 0; i < geoJsonServiceData.busLines.results.bindings.length; i++)
                                {
                                   popupText += '<span style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + geoJsonServiceData.busLines.results.bindings[i].busLine.value + '</span> ';     
                                }
                            }
                        }
                        
                        popupText += '</div>';
                        
                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';

                        if((serviceProperties.serviceUri !== '')&&(serviceProperties.serviceUri !== undefined)&&(serviceProperties.serviceUri !== 'undefined')&&(serviceProperties.serviceUri !== null)&&(serviceProperties.serviceUri !== 'null')) {
                            popupText += "Value Name: " + serviceProperties.serviceUri.split("/")[serviceProperties.serviceUri.split("/").length - 1] + "<br>";
                        }

                        if((serviceProperties.serviceType !== '')&&(serviceProperties.serviceType !== undefined)&&(serviceProperties.serviceType !== 'undefined')&&(serviceProperties.serviceType !== null)&&(serviceProperties.serviceType !== 'null')) {
                            popupText += "Nature: " + serviceProperties.serviceType.split(/_(.+)/)[0] + "<br>";
                            popupText += "Subnature: " + serviceProperties.serviceType.split(/_(.+)/)[1] + "<br><br>";
                        }

                        if(serviceProperties.hasOwnProperty('description'))
                        {
                            if((serviceProperties.description !== '')&&(serviceProperties.description !== undefined)&&(serviceProperties.description !== 'undefined')&&(serviceProperties.description !== null)&&(serviceProperties.description !== 'null'))
                            {
                                popupText += serviceProperties.description + "<br>";
                            }
                            else
                            {
                                popupText += "No description available";
                            }
                        }
                        else
                        {
                            popupText += 'No description available';
                        }
                        
                        popupText += '</div>';
                        
                        popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';
                        
                        var hasRealTime = false;
                        
                        if(geoJsonServiceData.hasOwnProperty("realtime"))
                        {
                            if(!jQuery.isEmptyObject(geoJsonServiceData.realtime))
                            {
                                realTimeData = geoJsonServiceData.realtime;
                                popupText += '<div class="popupLastUpdateContainer centerWithFlex"><b>Last update:&nbsp;</b><span class="popupLastUpdate" data-id="' + latLngId + '"></span></div>';
                                
                                if((serviceClass.includes("Emergency"))&&(serviceSubclass.includes("First aid")))
                                {
                                    //Tabella ad hoc per First Aid
                                    popupText += '<table id="' + latLngId + '" class="psPopupTable">';
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
                                        popupText += newRow.prop('outerHTML');
                                    }
                                    
                                    popupText += '</table>';
                                }
                                else
                                {
                                    //Tabella nuovo stile
                                    popupText += '<table id="' + latLngId + '" class="gisPopupTable">';

                                    //Intestazione
                                    popupText += '<thead>';
                                    popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Description</th>';
                                    popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Value</th>';
                                    popupText += '<th colspan="5" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Buttons</th>';
                                    popupText += '</thead>';

                                    //Corpo
                                    popupText += '<tbody>';
                                    var dataDesc, dataVal, data4HBtn, dataDayBtn, data7DayBtn, data30DayBtn, rtDataAgeSec = null;
                                    
                                    for(var i = 0; i < realTimeData.head.vars.length; i++)
                                    {
                                        if(realTimeData.results.bindings[0][realTimeData.head.vars[i]] !== null && realTimeData.results.bindings[0][realTimeData.head.vars[i]] !== undefined) {
                                            if ((realTimeData.results.bindings[0][realTimeData.head.vars[i]]) && (realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.trim() !== '') && (realTimeData.head.vars[i] !== null) && (realTimeData.head.vars[i] !== 'undefined')) {
                                                if ((realTimeData.head.vars[i] !== 'updating') && (realTimeData.head.vars[i] !== 'measuredTime') && (realTimeData.head.vars[i] !== 'instantTime')) {
                                                    if (!realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.includes('Not Available')) {
                                                        //realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                                    /*    dataDesc = realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function (str) {
                                                            return str.toUpperCase();
                                                        }); */
                                                        dataDesc = realTimeData.head.vars[i];
                                                        dataVal = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value;
                                                        data4HBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-fakeid="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>4 hours</button></td>';
                                                        dataDayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="24 hours" data-range="1/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>24 hours</button></td>';
                                                        data7DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="7 days" data-range="7/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>7 days</button></td>';
                                                        data30DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="30 days" data-range="30/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>30 days</button></td>';
                                                        popupText += '<tr><td>' + dataDesc + '</td><td>' + dataVal + '</td>' + data4HBtn + dataDayBtn + data7DayBtn + data30DayBtn + '</tr>';
                                                    }
                                                } else {
                                                    measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                                    //Calcolo age
                                                    var now = new Date();
                                                    var measuredTimeDate = new Date(measuredTime);
                                                    rtDataAgeSec = Math.abs(now - measuredTimeDate) / 1000;
                                                }
                                                /*if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                                {
                                                    dataDesc = realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function(str){ return str.toUpperCase(); });
                                                }

                                                if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                                {
                                                    if(realTimeData.results.bindings[0][realTimeData.head.vars[i]].value === 'Not Available')
                                                    {
                                                        realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                                    }
                                                    dataVal = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value;
                                                }
                                                else
                                                {
                                                    measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                                }

                                                if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                                {
                                                    data4HBtn = '<td style="padding-top: 7px; padding-bottom: 7px;"><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-fakeid="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">4 Hours</button></td>';
                                                }

                                                if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                                {
                                                    dataDayBtn = '<td style="padding-top: 7px; padding-bottom: 7px;"><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + false + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="Day" data-range="1/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Day</button></td>';
                                                }
                                                popupText += '<tr><td>' + dataDesc + '</td><td>' + dataVal + '</td>' + data4HBtn + dataDayBtn + '</tr>';*/
                                            }
                                        }
                                    }
                                    popupText += '</tbody>';
                                    popupText += '</table>';
                                }
                                
                                hasRealTime = true;
                            }
                        }
                        
                        popupText += '</div>';
                        newpopup = L.popup({
                            closeOnClick: false,//Non lo levare, sennò autoclose:false non funziona
                            autoClose: false,
                            offset: [15, 0], 
                            minWidth: 475,
                            maxWidth : 475
                        }).setContent(popupText);

                        event.target.bindPopup(newpopup).openPopup();
                        
                        if(hasRealTime)
                        {
                            $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').show();
                            $('span.popupLastUpdate[data-id="' + latLngId + '"]').html(measuredTime);
                        }
                        else
                        {
                            $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').hide();
                        }
                        
                        $('button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').off('click');
                        $('button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').click(function(){
                            $('#' + widgetName + '_modalLinkOpenGisMap div.recreativeEventMapDataContainer').hide();
                            $('#' + widgetName + '_modalLinkOpenGisMap div.recreativeEventMapDetailsContainer').show();
                            $('#' + widgetName + '_modalLinkOpenGisMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                            $(this).addClass('recreativeEventMapBtnActive');
                        });
                        
                        $('button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').off('click');
                        $('button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').click(function(){
                            $('#' + widgetName + '_modalLinkOpenGisMap div.recreativeEventMapDataContainer').hide();
                            $('#' + widgetName + '_modalLinkOpenGisMap div.recreativeEventMapDescContainer').show();
                            $('#' + widgetName + '_modalLinkOpenGisMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                            $(this).addClass('recreativeEventMapBtnActive');
                        });

                        $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').off('click');
                        $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').click(function(){
                            $('#' + widgetName + '_modalLinkOpenGisMap div.recreativeEventMapDataContainer').hide();
                            $('#' + widgetName + '_modalLinkOpenGisMap div.recreativeEventMapContactsContainer').show();
                            $('#' + widgetName + '_modalLinkOpenGisMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                            $(this).addClass('recreativeEventMapBtnActive');
                        }); 
                        
                        if(hasRealTime)
                        {
                            $('button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                        }

                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("width", "70px");
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("background", color2);
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("border", "none");
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("color", "black");

                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').focus(function(){
                            $(this).css("outline", "0");
                        });
                        
                        if(rtDataAgeSec > 14400)
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "true");
                            //Disabilitiamo i 24Hours se last update più vecchio di 24 ore
                            if(rtDataAgeSec > 86400)
                            {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "true");
                                //Disabilitiamo i 7 days se last update più vecchio di 7 days
                                if(rtDataAgeSec > 604800)
                                {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "true");
                                    //Disabilitiamo i 30 days se last update più vecchio di 30 days
                                    if(rtDataAgeSec > 18144000)
                                    {
                                       $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "true");
                                    }
                                    else
                                    {
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                                    }
                                }
                                else
                                {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                }
                            }
                            else
                            {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                            }
                        }
                        else
                        {
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "false");
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                        }

                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').off('mouseenter');
                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').off('mouseleave');
                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"]').hover(function(){
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
                                    $(this).css("background", color1);
                                    $(this).css("background", "-webkit-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                    $(this).css("background", "background: -o-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                    $(this).css("background", "background: -moz-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                    $(this).css("background", "background: linear-gradient(to left, " + color1 + ", " + color2 + ")");
                                    $(this).css("font-weight", "bold");
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
                                    $(this).css("background", color2);
                                    $(this).css("font-weight", "normal"); 
                                }
                            }
                        });

                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').off('click');
                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').click(function(event){
                            if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                            {
                                $(this).css("background-color", "#e6e6e6");
                                $(this).off("hover");
                                $(this).off("click");
                            }
                            else
                            {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').each(function(i){
                                    if($('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').eq(i).attr("data-disabled") === "false")
                                    {
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').eq(i).css("background", $(this).attr("data-color2"));
                                    }
                                });
                                //$('button.timeTrendBtn').css("background", $(this).attr("data-color2"));
                                $('button.timeTrendBtn').css("font-weight", "normal");
                                $(this).css("background", $(this).attr("data-color1"));
                                $(this).css("font-weight", "bold");
                                $('button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                $(this).attr("data-timeTrendClicked", "true");
                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisMap").css("height", "80%");

                                var field = $(this).attr("data-field");
                                var timeRange = $(this).attr("data-range");
                                var serviceUri = $(this).attr("data-serviceUri");
                                var color1 = $(this).attr("data-color1"); 
                                var color2 = $(this).attr("data-color2");
                                var timeTrendTitle = $(this).attr("data-title");
                                var timeTrendSubtitle = $(this).attr("data-range-shown");
                                var lastUpdateTime = $(this).parents('div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();

                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">Loading data, please wait</div><div style="text-align: center"><i class="fa fa-circle-o-notch fa-spin" style="font-size:48px"></i></div></div>');
                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                $("#gisTimeTrendLoadingContent").css("position", "relative");
                                $("#gisTimeTrendLoadingContent").css("top", "50%");
                                $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                switch(timeRange)
                                {
                                    case "4/HOUR":
                                        serviceMapTimeRange = "fromTime=4-hour";
                                        break;

                                    case "1/DAY":
                                        serviceMapTimeRange = "fromTime=1-day";
                                        break;
                                        
                                    case "7/DAY":
                                        serviceMapTimeRange = "fromTime=7-day";
                                        break;
                                        
                                    case "30/DAY":
                                        serviceMapTimeRange = "fromTime=30-day";
                                        break;     

                                    default:
                                        serviceMapTimeRange = "fromTime=1-day";
                                        break;
                                }
                                
                                $.ajax({
                                    url: "<?php echo $serviceMapUrlForTrendApi; ?>" + "?serviceUri=" + serviceUri + "&" + serviceMapTimeRange,
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    dataType: 'json',
                                    success: function(originalData) 
                                    {
                                        var convertedData = convertDataFromSmToDmForGis(originalData, field);
                                        
                                        if(convertedData)
                                        {
                                            if(convertedData.data.length > 2)
                                            {
                                                if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                                                {
                                                    //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)
                                                    drawTimeTrendInFullscreen(convertedData, timeRange, color1, color2, timeTrendTitle, timeTrendSubtitle);
                                                }
                                                else
                                                {
                                                    setTimeout(function(){
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").show();    
                                                        //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)
                                                        drawTimeTrendInFullscreen(convertedData, timeRange, color1, color2, timeTrendTitle, timeTrendSubtitle);
                                                    }, 500);
                                                }
                                            }
                                            else
                                            {
                                                if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                                                {
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                    $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                    $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                    $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                    $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                    $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                                }
                                                else
                                                {
                                                    setTimeout(function(){
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").show();    
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                        $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                        $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                        $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                        $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                        $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                                    }, 500);
                                                }

                                                console.log("Meno di due campioni restituiti da Service Map");
                                            }
                                        }
                                        else
                                        {
                                            if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                                            {
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                            }
                                            else
                                            {
                                                setTimeout(function(){
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").show();    
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                    $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                    $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                    $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                    $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                    $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                                }, 500);
                                            }
                                            console.log("Dati non disponibili da Service Map");
                                        }
                                    },
                                    error: function (data)
                                    {
                                        if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                                        {
                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                            $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                            $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                            $("#gisTimeTrendLoadingContent").css("position", "relative");
                                            $("#gisTimeTrendLoadingContent").css("top", "50%");
                                            $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                        }
                                        else
                                        {
                                            setTimeout(function(){
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").show();    
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                            }, 500);
                                        }
                                        console.log("Errore in scaricamento dati da Service Map");
                                        console.log(JSON.stringify(data));
                                    }
                                });
                            }
                        });
                        
                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"]').each(function(i){
                            if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                            {
                                $(this).css("background-color", "#e6e6e6");
                                $(this).off("hover");
                                $(this).off("click");
                            }
                        });

                        gisFullscreenMapRef.off('popupclose');
                        gisFullscreenMapRef.on('popupclose', function(closeEvt) {
                            var popupContent = $('<div></div>');
                            popupContent.html(closeEvt.popup._content);

                            if($('button.timeTrendBtn[data-timeTrendClicked=true]').length > 0)
                            {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisMap').css("height", "80vh");
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend').hide();
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend').css("height", "0vh");
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend').empty();
                            }
                        });

                        $('div.leaflet-popup').off("click");
                        $('div.leaflet-popup').on("click", function(){
                            var compLatLngId = $(this).find('input[type=hidden]').val();

                            $('div.leaflet-popup').css("z-index", "-1");
                            $(this).css("z-index", "999999");

                            $('button.timeTrendBtn').off('mouseenter');
                            $('button.timeTrendBtn').off('mouseleave');

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + compLatLngId + '"]').hover(function(){
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
                                        $(this).css("background", color1);
                                        $(this).css("background", "-webkit-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                        $(this).css("background", "background: -o-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                        $(this).css("background", "background: -moz-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                        $(this).css("background", "background: linear-gradient(to left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                        $(this).css("font-weight", "bold");
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
                                        $(this).css("background", $(this).attr('data-color2'));
                                        $(this).css("font-weight", "normal"); 
                                    }
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').click(function(event){
                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html()))||($(this).attr("data-disabled") === "true"))
                                {
                                    $(this).css("background-color", "#e6e6e6");
                                    $(this).off("hover");
                                    $(this).off("click");
                                }
                                else
                                {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').each(function(i){
                                        if($('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').eq(i).attr("data-disabled") === "false")
                                        {
                                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn').eq(i).css("background", $(this).attr("data-color2"));
                                        }
                                    });
                                    $('button.timeTrendBtn').css("font-weight", "normal");
                                    $(this).css("background", $(this).attr("data-color1"));
                                    $(this).css("font-weight", "bold");
                                    $('button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                    $(this).attr("data-timeTrendClicked", "true");
                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisMap").css("height", "80%");

                                    var field = $(this).attr("data-field");
                                    var timeRange = $(this).attr("data-range");
                                    var serviceUri = $(this).attr("data-serviceUri");
                                    var color1 = $(this).attr("data-color1"); 
                                    var color2 = $(this).attr("data-color2");
                                    var timeTrendTitle = $(this).attr("data-title");
                                    var timeTrendSubtitle = $(this).attr("data-range-shown");
                                    var lastUpdateTime = $(this).parents('div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();

                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">Loading data, please wait</div><div style="text-align: center"><i class="fa fa-circle-o-notch fa-spin" style="font-size:48px"></i></div></div>');
                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                    $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                    $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                    $("#gisTimeTrendLoadingContent").css("position", "relative");
                                    $("#gisTimeTrendLoadingContent").css("top", "50%");
                                    $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");

                                    switch(timeRange)
                                    {
                                        case "4/HOUR":
                                            serviceMapTimeRange = "fromTime=4-hour";
                                            break;

                                        case "1/DAY":
                                            serviceMapTimeRange = "fromTime=1-day";
                                            break;
                                            
                                        case "7/DAY":
                                            serviceMapTimeRange = "fromTime=7-day";
                                            break; 
                                            
                                        case "30/DAY":
                                            serviceMapTimeRange = "fromTime=30-day";
                                            break;      

                                        default:
                                            serviceMapTimeRange = "fromTime=1-day";
                                            break;
                                    }

                                    $.ajax({
                                        url: "<?php echo $serviceMapUrlForTrendApi; ?>" + "?serviceUri=" + serviceUri + "&" + serviceMapTimeRange,
                                        type: "GET",
                                        data: {},
                                        async: true,
                                        dataType: 'json',
                                        success: function(originalData) 
                                        {
                                            var convertedData = convertDataFromSmToDmForGis(originalData, field);
                                            
                                            if(convertedData)
                                            {
                                                if(convertedData.data.length > 0)
                                                {
                                                    if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                                                    {
                                                        //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)
                                                        drawTimeTrendInFullscreen(convertedData, timeRange, color1, color2, timeTrendTitle, timeTrendSubtitle);
                                                    }
                                                    else
                                                    {
                                                        setTimeout(function(){
                                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").show();    
                                                            //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)
                                                            drawTimeTrendInFullscreen(convertedData, timeRange, color1, color2, timeTrendTitle, timeTrendSubtitle);
                                                        }, 500);
                                                    }
                                                }
                                                else
                                                {
                                                    if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                                                    {
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                        $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                        $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                        $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                        $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                        $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                                    }
                                                    else
                                                    {
                                                        setTimeout(function(){
                                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").show();    
                                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                            $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                            $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                            $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                            $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                            $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                            $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                                        }, 500);
                                                    }
                                                    console.log("Meno di due campioni restituiti da Service Map");
                                                }
                                            }
                                            else
                                            {
                                                if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                                                {
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                    $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                    $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                    $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                    $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                    $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                                }
                                                else
                                                {
                                                    setTimeout(function(){
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").show();    
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                        $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                        $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                        $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                        $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                        $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                                    }, 500);
                                                }
                                                console.log("Dati non disponibili da Service Map");
                                            }
                                        },
                                        error: function (data)
                                        {
                                            if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                                            {
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                            }
                                            else
                                            {
                                                setTimeout(function(){
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").show();    
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").empty();
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">No data available</div><div style="text-align: center"><i class="fa fa-meh-o" style="font-size:48px"></i></div></div>');
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                                    $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                                    $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                                    $("#gisTimeTrendLoadingContent").css("position", "relative");
                                                    $("#gisTimeTrendLoadingContent").css("top", "50%");
                                                    $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                                }, 500);
                                            }
                                            console.log("Errore in scaricamento dati da Service Map");
                                            console.log(JSON.stringify(data));
                                        }
                                    });
                                }
                            });
                            
                            $('button.timeTrendBtn[data-id="' + latLngId + '"]').each(function(i){
                                if(isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())))
                                {
                                    $(this).css("background-color", "#e6e6e6");
                                    $(this).off("hover");
                                    $(this).off("click");
                                }
                            });
                        });
                    },
                    error: function(errorData)
                    {
                        console.log("Error in data retrieval");
                        console.log(JSON.stringify(errorData));
                        var serviceProperties = feature.properties;
                        
                        var underscoreIndex = serviceProperties.serviceType.indexOf("_");
                        var serviceClass = serviceProperties.serviceType.substr(0, underscoreIndex);
                        var serviceSubclass = serviceProperties.serviceType.substr(underscoreIndex);
                        serviceSubclass = serviceSubclass.replace(/_/g, " ");
                        
                        popupText = '<h3 class="gisPopupTitle">' + serviceProperties.name + '</h3>' +
                                    '<p><b>Typology: </b>' + serviceClass + " - " + serviceSubclass + '</p>' +
                                    '<p><i>Data are limited due to an issue in their retrieval</i></p>';
                            
                        event.target.bindPopup(popupText, {
                            offset: [15, 0], 
                            minWidth: 475, 
                            maxWidth : 475
                        }).openPopup();    
                    }
                });
            });
            return marker;
        }
        
        function convertDataFromSmToDmForGis(originalData, field)
        {
            var singleOriginalData, singleData, convertedDate = null;
            var convertedData = {
                data: []
            };
            
            var originalDataWithNoTime = 0;
            var originalDataNotNumeric = 0;
            
            if(originalData.hasOwnProperty("realtime"))
            {
                if(originalData.realtime.hasOwnProperty("results"))
                {
                    if(originalData.realtime.results.hasOwnProperty("bindings"))
                    {
                        if(originalData.realtime.results.bindings.length > 0)
                        {
                            for(var i = 0; i < originalData.realtime.results.bindings.length; i++)
                            {
                                singleData = {
                                    commit: {
                                        author: {
                                            IdMetric_data: null, //Si può lasciare null, non viene usato dal widget
                                            computationDate: null,
                                            value_perc1: null, //Non lo useremo mai
                                            value: null,
                                            descrip: null, //Mettici il nome della metrica splittato
                                            threshold: null, //Si può lasciare null, non viene usato dal widget
                                            thresholdEval: null //Si può lasciare null, non viene usato dal widget
                                        },
                                        range_dates: 0//Si può lasciare null, non viene usato dal widget
                                    }
                                };

                                singleOriginalData = originalData.realtime.results.bindings[i];
                                if(singleOriginalData.hasOwnProperty("updating"))
                                {
                                    convertedDate = singleOriginalData.updating.value;
                                }
                                else
                                {
                                    if(singleOriginalData.hasOwnProperty("measuredTime"))
                                    {
                                        convertedDate = singleOriginalData.measuredTime.value;
                                    }
                                    else
                                    {
                                        if(singleOriginalData.hasOwnProperty("instantTime"))
                                        {
                                            convertedDate = singleOriginalData.instantTime.value;
                                        }
                                        else
                                        {
                                            originalDataWithNoTime++;
                                            continue;
                                        }
                                    }
                                }

                                convertedDate = convertedDate.replace("T", " ");
                                var plusIndex = convertedDate.indexOf("+");
                                convertedDate = convertedDate.substr(0, plusIndex);
                                singleData.commit.author.computationDate = convertedDate;
                                
                                if(!isNaN(parseFloat(singleOriginalData[field].value)))
                                {
                                    singleData.commit.author.value = parseFloat(singleOriginalData[field].value);
                                }
                                else
                                {
                                    console.log("Categoria dato: " + field + " - Indice campione non numerico: " + i);
                                    originalDataNotNumeric++;
                                    continue;
                                }
                                
                                convertedData.data.push(singleData);
                            }
                            
                            convertedData.data.sort(function(a,b) 
                            {
                                var itemA = new Date(a.commit.author.computationDate); 
                                var itemB = new Date(b.commit.author.computationDate);
                                if (itemA < itemB)
                                {
                                   return -1;
                                }
                                else
                                {
                                   if (itemA > itemB)
                                   {
                                      return +1;
                                   }
                                   else
                                   {
                                      return 0; 
                                   }
                                }
                            });
                            
                            console.log("originalDataWithNoTime: " + originalDataWithNoTime + " - originalDataNotNumeric: " + originalDataNotNumeric);
                            
                            return convertedData;
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        
        //Ordinamento dei dati in ordine temporale crescente
        function convertedDataCompareForGis(a, b) 
        {
            var dateA = new Date(a.commit.author.computationDate);
            var dateB = new Date(b.commit.author.computationDate);
            if(dateA < dateB)
            {
                return -1;
            }
            else
            {
                if(dateA > dateB)
                {
                    return 1;
                }
                else
                {
                    return 0;
                } 
            }
        }
        
        function drawTimeTrendInFullscreen(data, timeRange, fullscreenTimeTrendColor, fullscreenTimeTrendFillColor, chartTitle, chartSubtitle)
        {
            var day, value, dayParts, timeParts, date, maxValue, nInterval, xAxisUnits = null;
            var seriesData = [];
            var valuesData = [];
            /*var xAxisUnits = [['millisecond', // unit name
                [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] // allowed multiples
            ], [
                'second',
                [1, 2, 5, 10, 15, 30]
            ], [
                'minute',
                [1, 2, 5, 10, 15, 30]
            ], [
                'hour',
                [1, 2, 3, 4, 6, 8, 12]
            ], [
                'day',
                [1]
            ], [
                'week',
                [1]
            ], [
                'month',
                [1, 3, 4, 6, 8, 10, 12]
            ], [
                'year',
                null
            ]];*/
        
            //timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
            //date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
            
            xAxisUnits = [['millisecond', 
                [1, 2, 5, 10, 20, 25, 50, 100, 200, 500] 
                ], [
                    'second',
                    [1, 2, 5, 10, 15, 30]
                ], [
                    'minute',
                    [1]
                ], [
                    'hour',
                    [1]
                ], [
                    'day',
                    [1]
                ], [
                    'week',
                    [1]
                ], [
                    'month',
                    [1]
                    //[1, 3, 4, 6, 8, 10, 12]
                ], [
                    'year',
                    null
                ]];
            
            for(var i = 0; i < data.data.length; i++) 
            {
                day = data.data[i].commit.author.computationDate;

                if((data.data[i].commit.author.value !== null) && (data.data[i].commit.author.value !== "")) 
                {
                    value = parseFloat(parseFloat(data.data[i].commit.author.value).toFixed(1));
                } 
                else if((data.data[i].commit.author.value_perc1 !== null) && (data.data[i].commit.author.value_perc1 !== "")) 
                {
                    if (value >= 100) 
                    {
                        value = parseFloat(parseFloat(data.data[i].commit.author.value_perc1).toFixed(0));
                    } 
                    else 
                    {
                        value = parseFloat(parseFloat(data.data[i].commit.author.value_perc1).toFixed(1));
                    }
                }

                /*dayParts = day.substring(0, day.indexOf(' ')).split('-');

                if((timeRange == '1/DAY') || (timeRange.includes("HOUR"))) 
                {
                    timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                    date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                }
                else 
                {
                    date = Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2]);
                }*/
                
                dayParts = day.substring(0, day.indexOf(' ')).split('-');
                timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');

                date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                console.log("Sample time from ServiceMap: " + dayParts[0] + "-" + (dayParts[1])+ "-" + dayParts[2] + " " + timeParts[0] + ":" + timeParts[1]);
                
                
                seriesData.push([date, value]);
                valuesData.push(value);
            }

            maxValue = Math.max.apply(Math, valuesData);
            nInterval = parseFloat((maxValue / 4).toFixed(1));
            
            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenGisTimeTrend').highcharts({
                    credits: {
                        enabled: false
                    },
                    chart: {
                        backgroundColor: 'white',
                        type: 'areaspline'
                    },
                    exporting: {
                        enabled: false
                    },
                    title: {
                        text: chartTitle,
                        margin: 2,
                        style: {
                            fontFamily: "Verdana"
                        }
                    },
                    subtitle: {
                        text: chartSubtitle,
                        margin: 4,
                        style: {
                            fontFamily: "Verdana"
                        }
                    },
                    xAxis: {
                        type: 'datetime',
                        units: xAxisUnits,
                        labels: {
                            enabled: true,
                            style: {
                                fontFamily: 'Verdana',
                                color: "black",
                                fontSize: "12px",
                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                "textOutline": "1px 1px contrast"
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: ''
                        },
                        min: 0,
                        max: maxValue,
                        tickInterval: nInterval,
                        labels: {
                            enabled: true,
                            style: {
                                fontFamily: 'Verdana',
                                color: "black",
                                fontSize: "12px",
                                "text-shadow": "1px 1px 1px rgba(0,0,0,0.12)",
                                "textOutline": "1px 1px contrast"
                            }
                        }
                    },
                    tooltip: {
                        valueSuffix: ''
                    },
                    series: [{
                            showInLegend: false,
                            name: 'data',
                            data: seriesData,
                            shadow: true,
                            fillColor: {
                                linearGradient: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 0,
                                    y2: 1
                                },
                                stops: [
                                    [0, fullscreenTimeTrendFillColor],
                                    [1, Highcharts.Color(fullscreenTimeTrendFillColor).setOpacity(0).get('rgba')]
                                ]
                            }
                        }],
                    plotOptions: {
                        areaspline: {
                            color: fullscreenTimeTrendColor,
                            fillColor: fullscreenTimeTrendFillColor
                        }
                    }
                });
            
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

        widgetProperties = getWidgetProperties(widgetName);
        
        createFullscreenModal();
        
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            url = widgetProperties.param.link_w;
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
                        
                        loadGisMap();
                        
                        $(document).off('addLayerFromGis_' + widgetName);
                        $(document).on('addLayerFromGis_' + widgetName, function(event)
                        {
                            var mapBounds = gisMapRef.getBounds();
                            var query, targets = null;
                            var eventGenerator = event.eventGenerator;
                            var color1 = event.color1;
                            var color2 = event.color2;
                            var queryType = event.queryType;
                            var apiUrl = "";
                            var dataForApi = "";

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

                            if(queryType === "Default")
                            {
                                if (event.query.includes("datamanager/api/v1/poidata/")) {
                                    if (event.desc != "My POI") {
                                        myPOIId = event.query.split("datamanager/api/v1/poidata/")[1];
                                        apiUrl = "../controllers/myPOIProxy.php";
                                        dataForApi = myPOIId;
                                        query = event.query;
                                    } else {
                                        apiUrl = "../controllers/myPOIProxy.php";
                                        dataForApi = "All";
                                        query = event.query;
                                    }
                                } else if (event.query.includes("/iot/") && !event.query.includes("/api/v1/")) {
                                    query = "https://www.disit.org/superservicemap/api/v1/?serviceUri=" + event.query + "&format=json";
                                } else {

                                    if (pattern.test(event.query)) {
                                        //console.log("Service Map selection substitution");
                                        query = event.query.replace(pattern, "selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng);
                                    } else {
                                        //console.log("Service Map selection addition");
                                        query = event.query + "&selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng;
                                    }
                                }
                                if (!query.includes("&maxResults")) {
                                    if (!query.includes("&queryId")) {
                                        query = query + "&maxResults=0";
                                    }
                                }
                            }
                            else if(queryType === "MyPOI") {
                                if (event.desc != "My POI") {
                                    myPOIId = event.query.split("datamanager/api/v1/poidata/")[1];
                                    apiUrl = "../controllers/myPOIProxy.php";
                                    dataForApi = myPOIId;
                                    query = event.query;
                                } else {
                                    apiUrl = "../controllers/myPOIProxy.php";
                                    dataForApi = "All";
                                    query = event.query;
                                }

                            }
                            else
                            {
                                query = event.query;
                            }

                            if(event.targets !== "")
                            {
                                targets = event.targets.split(",");
                            }
                            else
                            {
                                targets = [];
                            }

                            if(queryType != "MyPOI" && !event.query.includes("datamanager/api/v1/poidata/")) {
                                apiUrl = query + "&geometry=true&fullCount=false";
                            }

                        //    if (queryType === "Sensor" && query.includes("%2525")) {
                            if (query.includes("%2525") && !query.includes("%252525")) {
                                let queryPart1 = query.split("/resource/")[0];
                                let queryPart2 = (query.split("/resource/")[1]).split("&format=")[0];
                                let queryPart3 = query.split("&format=")[1];
                                if (queryPart3 != undefined) {
                                    apiUrl = queryPart1 + "/resource/" + encodeURI(queryPart2) + "&format=" + queryPart3;
                                } else {
                                    apiUrl = queryPart1 + "/resource/" + encodeURI(queryPart2);
                                }
                            }

                            $.ajax({
                             //   url: query + "&geometry=true&fullCount=false",
                                url: apiUrl,
                                type: "GET",
                                data: {
                                    myPOIId: dataForApi
                                },
                                async: true,
                                timeout: 0,
                                dataType: 'json',
                                success: function(geoJsonData)
                                {
                                    var fatherGeoJsonNode = {};

                                    if(queryType === "Default")
                                    {
                                        if (event.query.includes("datamanager/api/v1/poidata/")) {
                                            fatherGeoJsonNode.features = [];
                                            if (event.desc != "My POI") {
                                                fatherGeoJsonNode.features[0] = geoJsonData;
                                            } else {
                                                fatherGeoJsonNode.features = geoJsonData;
                                            }
                                            fatherGeoJsonNode.type = "FeatureCollection";
                                        }
                                        else {
                                            var countObjKeys = 0;
                                            var objContainer = {};
                                            Object.keys(geoJsonData).forEach(function (key) {
                                                if (countObjKeys == 0) {
                                                    if (geoJsonData.hasOwnProperty(key)) {
                                                        fatherGeoJsonNode = geoJsonData[key];
                                                    }
                                                } else {
                                                    if (geoJsonData.hasOwnProperty(key)) {
                                                        if (geoJsonData[key].features) {
                                                            fatherGeoJsonNode.features = fatherGeoJsonNode.features.concat(geoJsonData[key].features);
                                                        }
                                                    }
                                                }
                                                countObjKeys++;
                                            });
                                        /*    if (geoJsonData.hasOwnProperty("BusStops")) {
                                                fatherGeoJsonNode = geoJsonData.BusStops;
                                            } else {
                                                if (geoJsonData.hasOwnProperty("SensorSites")) {
                                                    fatherGeoJsonNode = geoJsonData.SensorSites;
                                                } else {
                                                    if (geoJsonData.hasOwnProperty("Service")) {
                                                        fatherGeoJsonNode = geoJsonData.Service;
                                                    } else {
                                                        fatherGeoJsonNode = geoJsonData.Services;
                                                    }
                                                }
                                            }   */
                                        }
                                    }
                                    else if (queryType === "MyPOI")
                                    {
                                        fatherGeoJsonNode.features = [];
                                        if (event.desc != "My POI") {
                                            fatherGeoJsonNode.features[0] = geoJsonData;
                                        } else {
                                            fatherGeoJsonNode.features = geoJsonData;
                                        }
                                        fatherGeoJsonNode.type = "FeatureCollection";
                                    }
                                    else
                                    {
                                      /*  var countObjKeys = 0;
                                        var objContainer = {};
                                        Object.keys(geoJsonData).forEach(function (key) {
                                            if (countObjKeys == 0) {
                                                if (geoJsonData.hasOwnProperty(key)) {
                                                    fatherGeoJsonNode = geoJsonData[key];
                                                }
                                            } else {
                                                if (geoJsonData.hasOwnProperty(key)) {
                                                    fatherGeoJsonNode.features = fatherGeoJsonNode.features.concat(geoJsonData[key].features);
                                                }
                                            }
                                            countObjKeys++;
                                        });*/
                                        if(geoJsonData.hasOwnProperty("BusStop"))
                                        {
                                            fatherGeoJsonNode = geoJsonData.BusStop;
                                        }
                                        else
                                        {
                                            if(geoJsonData.hasOwnProperty("Sensor"))
                                            {
                                                fatherGeoJsonNode = geoJsonData.Sensor;
                                            }
                                            else
                                            {
                                                if(geoJsonData.hasOwnProperty("Service"))
                                                {
                                                    fatherGeoJsonNode = geoJsonData.Service;
                                                }
                                                else
                                                {
                                                    fatherGeoJsonNode = geoJsonData.Services;
                                                }
                                            }
                                        }
                                    }


                                    for(var i = 0; i < fatherGeoJsonNode.features.length; i++)
                                    {
                                        fatherGeoJsonNode.features[i].properties.targetWidgets = targets;
                                        fatherGeoJsonNode.features[i].properties.color1 = color1;
                                        fatherGeoJsonNode.features[i].properties.color2 = color2;
                                    }

                                    if(!gisLayersOnMap.hasOwnProperty(event.desc)&&(event.display !== 'geometries'))
                                    {
                                        gisLayersOnMap[event.desc] = L.geoJSON(fatherGeoJsonNode, {
                                            pointToLayer: gisPrepareCustomMarker
                                            //onEachFeature: gisPrepareEachFeature, //Per ora non usata
                                            //filter: gisFilterOutOfBoundsPoints NON CANCELLARLA, UTILE COME OPZIONE DI BACKUP SE LA REGEX FUNZIONA MALE
                                        }).addTo(gisMapRef);
                                    }

                                    loadingDiv.empty();
                                    loadingDiv.append(loadOkText);

                                    parHeight = loadOkText.height();
                                    parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                    loadOkText.css("margin-top", parMarginTop + "px");

                                    setTimeout(function(){
                                        loadingDiv.css("opacity", 0);
                                        setTimeout(function(){
                                            loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function(i){
                                                $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                            });
                                            loadingDiv.remove();
                                        }, 350);
                                    }, 1000);

                                    eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").hide();
                                    eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("font-weight", "bold");
                                    eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("color", eventGenerator.attr("data-activeFontColor"));
                                    if(eventGenerator.parents("div.gisMapPtrContainer").find('a.gisPinLink').attr("data-symbolMode") === 'auto')
                                    {
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").html("near_me");
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("color", "white");
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("text-shadow", "2px 2px 4px black");
                                    }
                                    else
                                    {
                                        //Evidenziazione che gli eventi di questa query sono su mappa in caso di icona custom
                                        eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").show();
                                        eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    }

                                    eventGenerator.show();

                                    var wkt = null;

                                    if(event.display !== 'pins')
                                    {
                                        stopGeometryAjax[event.desc] = false;
                                        gisGeometryTankForFullscreen[event.desc] = {
                                            capacity: fatherGeoJsonNode.features.length,
                                            shown: false,
                                            tank: [],
                                            lastConsumedIndex: 0
                                        };

                                        for(var i = 0; i < fatherGeoJsonNode.features.length; i++)
                                        {
                                            if(fatherGeoJsonNode.features[i].properties.hasOwnProperty('hasGeometry')&&fatherGeoJsonNode.features[i].properties.hasOwnProperty('serviceUri'))
                                            {
                                                if(fatherGeoJsonNode.features[i].properties.hasGeometry === true)
                                                {
                                                    //gisGeometryServiceUriToShowFullscreen[event.desc].push(fatherGeoJsonNode.features[i].properties.serviceUri);

                                                    $.ajax({
                                                        url: "<?php echo $superServiceMapUrlPrefix; ?>" + "/api/v1/?serviceUri=" + fatherGeoJsonNode.features[i].properties.serviceUri,
                                                        type: "GET",
                                                        data: {},
                                                        async: true,
                                                        timeout: 0,
                                                        dataType: 'json',
                                                        success: function(geometryGeoJson)
                                                        {
                                                            if(!stopGeometryAjax[event.desc])
                                                            {
                                                                // Creazione nuova istanza del parser Wkt
                                                                wkt = new Wkt.Wkt();

                                                                // Lettura del WKT dalla risposta
                                                                wkt.read(geometryGeoJson.Service.features[0].properties.wktGeometry);

                                                                var ciclePathFeature = [
                                                                    {
                                                                        type: "Feature",
                                                                        properties: geometryGeoJson.Service.features[0].properties,
                                                                        geometry: wkt.toJson()
                                                                    }
                                                                ];

                                                                if(!gisGeometryLayersOnMap.hasOwnProperty(event.desc))
                                                                {
                                                                    gisGeometryLayersOnMap[event.desc] = [];
                                                                }

                                                                gisGeometryLayersOnMap[event.desc].push(L.geoJSON(ciclePathFeature, {}).addTo(gisMapRef));
                                                                gisGeometryTankForFullscreen[event.desc].tank.push(ciclePathFeature);
                                                            }
                                                        },
                                                        error: function(geometryErrorData)
                                                        {
                                                            console.log("Ko");
                                                            console.log(JSON.stringify(geometryErrorData));
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    }
                                },
                                error: function(errorData)
                                {
                                    gisLayersOnMap[event.desc] = "loadError";

                                    loadingDiv.empty();
                                    loadingDiv.append(loadKoText);

                                    parHeight = loadKoText.height();
                                    parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                    loadKoText.css("margin-top", parMarginTop + "px");

                                    setTimeout(function(){
                                        loadingDiv.css("opacity", 0);
                                        setTimeout(function(){
                                            loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function(i){
                                                $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                            });
                                            loadingDiv.remove();
                                        }, 350);
                                    }, 1000);

                                    eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").hide();
                                    eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadErrorIcon").show();

                                    setTimeout(function(){
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadErrorIcon").hide();
                                        eventGenerator.parents("div.gisMapPtrContainer").find("a.gisPinLink").attr("data-onMap", "false");
                                        eventGenerator.parents("div.gisMapPtrContainer").find("a.gisPinLink").show();
                                    }, 1500);

                                    console.log("Error in getting GeoJSON from ServiceMap");
                                    console.log(JSON.stringify(errorData));
                                }
                            });
                        });

                        $(document).off('removeLayerFromGis_' + widgetName);
                        $(document).on('removeLayerFromGis_' + widgetName, function(event) 
                        {
                            if(stopGeometryAjax.hasOwnProperty(event.desc))
                            {
                                stopGeometryAjax[event.desc] = true;
                            }
                            
                            if(event.display !== 'geometries')
                            {
                                if(gisLayersOnMap[event.desc] !== "loadError")
                                {
                                    gisMapRef.removeLayer(gisLayersOnMap[event.desc]);

                                    if(gisGeometryLayersOnMap.hasOwnProperty(event.desc))
                                    {
                                        if(gisGeometryLayersOnMap[event.desc].length > 0)
                                        {
                                            for(var i = 0; i < gisGeometryLayersOnMap[event.desc].length; i++)
                                            {
                                                gisMapRef.removeLayer(gisGeometryLayersOnMap[event.desc][i]);
                                            }
                                            delete gisGeometryLayersOnMap[event.desc];
                                        }
                                    }
                                }
                                delete gisLayersOnMap[event.desc];
                            }
                            else
                            {
                                if(gisGeometryLayersOnMap.hasOwnProperty(event.desc))
                                {
                                    if(gisGeometryLayersOnMap[event.desc].length > 0)
                                    {
                                        for(var i = 0; i < gisGeometryLayersOnMap[event.desc].length; i++)
                                        {
                                            gisMapRef.removeLayer(gisGeometryLayersOnMap[event.desc][i]);
                                        }
                                        delete gisGeometryLayersOnMap[event.desc];
                                    }
                                }
                            }
                            
                            delete gisGeometryTankForFullscreen[event.desc];
                            
                            /*if(gisGeometryServiceUriToShowFullscreen.hasOwnProperty(event.desc))
                            {
                                delete gisGeometryServiceUriToShowFullscreen[event.desc];
                            }*/
                        });

                        $(document).off('mouseOverFromGis_' + widgetName);
                        $(document).on('mouseOverFromGis_' + widgetName, function(evt) 
                        {
                            if(gisMapRef.hasLayer(gisLayersOnMap[evt.desc]))
                            {
                                gisLayersOnMap[evt.desc].eachLayer(function(marker) {
                                    marker.fire("mouseover");
                                });
                            }
                        });

                        $(document).off('mouseOutFromGis_' + widgetName);
                        $(document).on('mouseOutFromGis_' + widgetName, function(evt) 
                        {
                            if(gisMapRef.hasLayer(gisLayersOnMap[evt.desc]))
                            {
                                gisLayersOnMap[evt.desc].eachLayer(function(marker) {
                                    marker.fire("mouseout");
                                });
                            }
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
                            
                            //NON CANCELLARE - Versione in produzione: si mostrano solo quando sono arrivati tutti
                            /*checkTankInterval = setInterval(function(){
                                for(var key in gisGeometryTankForFullscreen)
                                {
                                    
                                    if((gisGeometryTankForFullscreen[key].tank.length === gisGeometryTankForFullscreen[key].capacity)&&(gisGeometryTankForFullscreen[key].shown === false))
                                    {
                                        for(var k = 0; k < gisGeometryTankForFullscreen[key].tank.length; k++)
                                        {
                                            L.geoJSON(gisGeometryTankForFullscreen[key].tank[k], {
                                                //pointToLayer: gisPrepareCustomMarkerForFullscreen
                                            }).addTo(gisFullscreenMapRef);
                                        }
                                        gisGeometryTankForFullscreen[key].shown = true;
                                    }
                                }
                                console.log("Check tank");
                            }, 250);*/
                            
                            
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
                        }, 250);
                        
                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen").modal('show');
                        /*for(var desc in gisGeometryServiceUriToShowFullscreen)
                        {
                            for(var i in gisGeometryServiceUriToShowFullscreen[desc])
                            {
                                $.ajax({
                                    url: "<?php echo $superServiceMapUrlPrefix; ?>" + "/api/v1/?serviceUri=" + gisGeometryServiceUriToShowFullscreen[desc][i],
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    timeout: 0,
                                    dataType: 'json',
                                    success: function(geometryGeoJson) 
                                    {
                                        if($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").is(":visible"))
                                        {
                                            // Creazione nuova istanza del parser Wkt
                                            var wkt = new Wkt.Wkt();

                                            // Lettura del WKT dalla risposta
                                            wkt.read(geometryGeoJson.Service.features[0].properties.wktGeometry);

                                            var ciclePathFeature = [
                                                {
                                                    type: "Feature",
                                                    properties: geometryGeoJson.Service.features[0].properties,
                                                    geometry: wkt.toJson()
                                                }
                                            ];

                                            L.geoJSON(ciclePathFeature, {}).addTo(gisFullscreenMapRef);
                                        }
                                    },
                                    error: function(geometryErrorData)
                                    {
                                        console.log("Ko");
                                        console.log(JSON.stringify(geometryErrorData));
                                    }
                                });
                            }
                        }*/
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
                      $("#newTabLinkOpenImpossibileMsg").html("It's not possibile to open an embedded map in an external page: please use the popup fullscreen option.");
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
    });//Fine document ready 
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
                <!--<div id="<?= $_REQUEST['name_w'] ?>_zoomControls" class="iframeZoomControls">
                    <div id="<?= $_REQUEST['name_w'] ?>_dimDiv" class="zoomControlsRow">
                        <div class="zoomControlsLabelDiv">
                            width
                        </div>
                        <div class="zoomControlsButtonsDiv">
                            <i id="<?= $_REQUEST['name_w'] ?>_xMin" class="fa fa-minus-square-o"></i>
                            <i id="<?= $_REQUEST['name_w'] ?>_xPlus" class="fa fa-plus-square-o"></i>
                        </div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_dimDiv" class="zoomControlsRow">
                        <div class="zoomControlsLabelDiv">
                            height
                        </div>
                        <div class="zoomControlsButtonsDiv">
                            <i id="<?= $_REQUEST['name_w'] ?>_yMin" class="fa fa-minus-square-o"></i>
                            <i id="<?= $_REQUEST['name_w'] ?>_yPlus" class="fa fa-plus-square-o"></i>
                        </div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_zoomDiv" class="zoomControlsRow">
                        <div class="zoomControlsLabelDiv">
                            zoom    
                        </div>
                        <div class="zoomControlsButtonsDiv">
                            <i id="<?= $_REQUEST['name_w'] ?>_zoomOut" class="fa fa-minus-square-o"></i> 
                            <i id="<?= $_REQUEST['name_w'] ?>_zoomIn" class="fa fa-plus-square-o"></i>
                        </div>
                    </div>
                </div>-->
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