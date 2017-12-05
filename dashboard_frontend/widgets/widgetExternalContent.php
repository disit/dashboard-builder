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
    $(document).ready(function iframe(firstLoad) 
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
                
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_content");
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
        var idWidget = "<?= $_GET['idWidget'] ?>";
        var zoomControlsColor = "<?= $_GET['zoomControlsColor'] ?>";
        var leftWrapper = "0px";
        var zoomTarget = "body";
        var currentZoom = "<?= $_GET['zoomFactor'] ?>";
        var currentScaleX = "<?= $_GET['scaleX'] ?>";
        var currentScaleY = "<?= $_GET['scaleY'] ?>";
        var controlsPosition = "<?= $_GET['controlsPosition'] ?>";
        var showTitle = "<?= $_GET['showTitle'] ?>";
        var controlsVisibility = "<?= $_GET['controlsVisibility'] ?>";
        var sizeX = "<?= $_GET['sizeX'] ?>";
        var sizeY = "<?= $_GET['sizeY'] ?>";
        var colore_frame = "<?= $_GET['frame_color'] ?>";
        var url = "<?= $_GET['link_w'] ?>";
        var numCols = "<?= $_GET['numCols'] ?>";
        var wrapperW = $('#<?= $_GET['name'] ?>_div').outerWidth();
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        var widgetProperties, styleParameters, topWrapper, height, zoomDisplayTimeout, wrapperH, titleWidth, sourceMapDivCopy, sourceIfram, 
            fullscreenMapRef, fullscreenDefaultMapRef, minLat, minLng, maxLat, maxLng, lat, lng, eventType, eventName, eventStartDate, eventStartTime, eventSeverity,
            mapPinImg, severityColor, pinIcon, markerLocation, marker, popupText, lastPopup, dataArray, eventSubtype, evtTypeForMaxZoom, pathsQt, gisMapRef,
            gisFullscreenMapRef, gisFullscreenMapCenter, gisFullscreenMapStartZoom, gisFullscreenMapStartBounds, widgetParameters, newpopup = null;
    
        var gisLayersOnMap = {};  
        var markersCache = {};
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
	{
            showHeader = false;
	}
	else
	{
            showHeader = true;
	}
        
        //Definizioni di funzione specifiche del widgetfunction setAutoFontSize(container)
        
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
        
        function changeDimX(op)
        {
            $("#<?= $_GET['name'] ?>_div").parent().attr("data-sizex", sizeX);
            var width = null;
            switch(op)
            {
                case "+":
                    width = parseInt($('#<?= $_GET['name'] ?>_div').outerWidth() + 76);
                    break;
                    
                case "-":
                    width = parseInt($('#<?= $_GET['name'] ?>_div').outerWidth() - 76);
                    break;
            }
            
            $('#<?= $_GET['name'] ?>_content').css("width", width + "px");
    
            var formData = new FormData();
            formData.set('widthUpdated', sizeX);
            formData.set('idWidget', idWidget);
            $.ajax({
                url: "process-form.php",
                data: formData,
                async: true,
                processData: false,
                contentType: false,  
                type: 'POST',
                success: function (msg) 
                {
                },
                error: function()
                {
                    console.log("Errore in chiamata PHP per scrittura zoom factor");
                }
            });
        }
        
        function changeDimY(op)
        {
            $("#<?= $_GET['name'] ?>_div").parent().attr("data-sizey", sizeY);
            var height = null;
            switch(op)
            {
                case "+":
                    height = parseInt($('#<?= $_GET['name'] ?>_div').outerHeight() + 38);
                    break;
                    
                case "-":
                    height = parseInt($('#<?= $_GET['name'] ?>_div').outerHeight() - 38);
                    break;
            }
            
            $('#<?= $_GET['name'] ?>_content').css("height", height + "px");
    
    
            var formData = new FormData();
            formData.set('heightUpdated', sizeY);
            formData.set('idWidget', idWidget);
            $.ajax({
                url: "process-form.php",
                data: formData,
                async: true,
                processData: false,
                contentType: false,  
                type: 'POST',
                success: function (msg) 
                {
                    
                },
                error: function()
                {
                    console.log("Errore in chiamata PHP per scrittura zoom factor");
                }
            }); 
        }
        
        function changeZoom()
        {
            var target = document.getElementById('<?= $_GET['name'] ?>_iFrame');
            target.contentWindow.postMessage(currentZoom, '*');
            
            var formData = new FormData();
            formData.set('zoomFactorUpdated', currentZoom);
            formData.set('idWidget', idWidget);
            $.ajax({
                url: "process-form.php",
                data: formData,
                async: true,
                processData: false,
                contentType: false,  
                type: 'POST',
                success: function (msg) 
                {
                },
                error: function()
                {
                    console.log("Errore in chiamata PHP per scrittura zoom factor");
                }
            });  
        }
        
        function updateZoomControlsPosition()
        {
            switch (controlsPosition)
            {
                case 'topleft':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "1%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "2%");
                    break;

                case 'topCenter':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "50%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "2%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("transform", "translateX(-50%");
                    break;

                case 'topRight':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "80%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "2%");
                    break;

                case 'middleRight':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "80%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "50%");
                    break;

                case 'bottomRight':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "80%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "78%");
                    break;

                case 'bottomMiddle':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "50%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "78%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("transform", "translateX(-50%");
                    break;

                case 'bottomLeft':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "1%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "78%");
                    break;

                case 'middleLeft':
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "1%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "50%");
                    break;

                default:
                    $('#<?= $_GET['name'] ?>_zoomControls').css("left", "1%");
                    $('#<?= $_GET['name'] ?>_zoomControls').css("top", "2%");
                    break;
            }
        }
        
        //Va aggiornata con showWidgetContent
        function iframeLoaded(event)
        {
            $('#<?= $_GET['name'] ?>_loading').css("display", "none");
            $('#<?= $_GET['name'] ?>_wrapper').css("width", "100%");
            $("#<?= $_GET['name'] ?>_wrapper").css("height", height);
            
            if((controlsVisibility === 'alwaysVisible') && (hostFile === 'config'))
            {
                $('#<?= $_GET['name'] ?>_zoomControls').css("display", "block");
                updateZoomControlsPosition();
            }
            var target = document.getElementById('<?= $_GET['name'] ?>_iFrame');
            target.contentWindow.postMessage(currentZoom, '*');
            
            $("#<?= $_GET['name'] ?>_content").contents().find("body").css("transform-origin", "0% 0%");
            
            if(firstLoad !== false)
            {
                $('#<?= $_GET['name'] ?>_loading').css("display", "none");
                $('#<?= $_GET['name'] ?>_wrapper').css("display", "block");
            }
        }
        
        function createFullscreenModal()
        {
            var fullscreenModal = $('<div class="modal fade" tabindex="-1" id="<?= $_GET['name'] ?>_modalLinkOpen" class="modalLinkOpen" role="dialog" aria-labelledby="myModalLabel">' +
                '<div class="modal-dialog" role="document">' +  
                    '<div class="modal-content">' +
                        '<div class="modal-header centerWithFlex">' +
                            '<h4 class="modal-title"></h4>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<div class="modalLinkOpenBody">' + 
                                '<iframe class="modalLinkOpenBodyIframe"></iframe>' +
                                '<div id="<?= $_GET['name'] ?>_modalLinkOpenBodyMap" class="modalLinkOpenBodyMap" data-mapRef="null"></div>' +
                                '<div id="<?= $_GET['name'] ?>_modalLinkOpenBodyDefaultMap" class="modalLinkOpenBodyDefaultMap" data-mapRef="null"></div>' +
                                '<div id="<?= $_GET['name'] ?>_modalLinkOpenGisMap" class="modalLinkOpenGisMap" data-mapRef="null"></div>' +
                                '<div id="<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend" class="modalLinkOpenGisTimeTrend"></div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" id="<?= $_GET['name'] ?>_modalLinkOpenCloseBtn" class="btn btn-primary">Back to dashboard</button>' +
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
            
            $("#<?= $_GET['name'] ?>_modalLinkOpenCloseBtn").off();
            $("#<?= $_GET['name'] ?>_modalLinkOpenCloseBtn").click(function(){
                if($("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").is(":visible"))
                {
                    fullscreenDefaultMapRef.off();
                    fullscreenDefaultMapRef.remove(); 
                }
        
                if($("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").is(":visible"))
                {
                    fullscreenMapRef.off();
                    fullscreenMapRef.remove(); 
                }
                
                if($("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenGisMap").is(":visible"))
                {
                    gisFullscreenMapRef.off();
                    gisFullscreenMapRef.remove();
                }
                
                fullscreenModal.find("div.modalLinkOpenGisMap").css("height", "80vh");
                $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").css("height", "0vh");
                $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").hide();
                $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").empty();
                
                $("#<?= $_GET['name'] ?>_modalLinkOpen").modal('hide');
                $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").modal('hide');
                $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").modal('hide');
            });
        }
        
        //Creazione della mappa di default per il widget, non per il suo popup fullscreen
        function loadDefaultMap()
        {
            var mapDivLocal = "<?= $_GET['name'] ?>_defaultMapDiv";
            var mapRefLocal = L.map(mapDivLocal).setView([43.769789, 11.255694], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
               maxZoom: 18
            }).addTo(mapRefLocal);
            mapRefLocal.attributionControl.setPrefix('');
        }
        
        //Creazione della mappa vuota per il widget in modalità GIS target, non per il suo popup fullscreen
        function loadGisMap()
        {
            var mapDivLocal = "<?= $_GET['name'] ?>_gisMapDiv";
            gisMapRef = L.map(mapDivLocal).setView(widgetParameters.latLng, widgetParameters.zoom);

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
                iconUrl: mapPinImg
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
                event.target.unbindPopup();
                newpopup = null;
                var popupText, realTimeData, measuredTime, targetWidgets, color1, color2 = null;
                var urlToCall, fake, fakeId = null;
                
                if(feature.properties.fake === 'true')
                {
                    urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + feature.id;
                    fake = true;
                    fakeId = feature.id;
                }
                else
                {
                    urlToCall = "<?php echo $serviceMapUrlPrefix; ?>api/v1/?serviceUri=" + feature.properties.serviceUri + "&format=json";
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
                        //console.log(JSON.stringify(geoJsonServiceData));
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

                        popupText = '<input type="hidden" value="' + latLngId + '"/>' +
                                    '<h3 class="gisPopupTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + serviceProperties.name + '</h3>' +
                                    '<p><b>Typology: </b>' + serviceClass + " - " + serviceSubclass + '<br>';

                        if(serviceProperties.hasOwnProperty('description'))
                        {
                            if((serviceProperties.description !== '')&&(serviceProperties.description !== undefined)&&(serviceProperties.description !== 'undefined')&&(serviceProperties.description !== null)&&(serviceProperties.description !== 'null'))
                            {
                                popupText += '<b>Description: </b>' + serviceProperties.description + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('website'))
                        {
                            if((serviceProperties.website !== '')&&(serviceProperties.website !== undefined)&&(serviceProperties.website !== 'undefined')&&(serviceProperties.website !== null)&&(serviceProperties.website !== 'null'))
                            {
                                if(serviceProperties.website.includes('http')||serviceProperties.website.includes('https'))
                                {
                                    popupText += '<b><a href="' + serviceProperties.website + '" target="_blank">Website</a></b><br>';
                                }
                                else
                                {
                                    popupText += '<b><a href="' + serviceProperties.website + '" target="_blank">Website</a></b><br>';
                                }
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('email'))
                        {
                            if((serviceProperties.email !== '')&&(serviceProperties.email !== undefined)&&(serviceProperties.email !== 'undefined')&&(serviceProperties.email !== null)&&(serviceProperties.email !== 'null'))
                            {
                                popupText += '<b>E-Mail: </b>' + serviceProperties.email + '<br>';
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('address'))
                        {
                            if((serviceProperties.address !== '')&&(serviceProperties.address !== undefined)&&(serviceProperties.address !== 'undefined')&&(serviceProperties.address !== null)&&(serviceProperties.address !== 'null'))
                            {
                                popupText += '<b>Address: </b>' + serviceProperties.address + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('civic'))
                        {
                            if((serviceProperties.civic !== '')&&(serviceProperties.civic !== undefined)&&(serviceProperties.civic !== 'undefined')&&(serviceProperties.civic !== null)&&(serviceProperties.civic !== 'null'))
                            {
                                popupText += '<b>Civic n.: </b>' + serviceProperties.civic + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('cap'))
                        {
                            if((serviceProperties.cap !== '')&&(serviceProperties.cap !== undefined)&&(serviceProperties.cap !== 'undefined')&&(serviceProperties.cap !== null)&&(serviceProperties.cap !== 'null'))
                            {
                                popupText += '<b>C.A.P.: </b>' + serviceProperties.cap + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('city'))
                        {
                            if((serviceProperties.city !== '')&&(serviceProperties.city !== undefined)&&(serviceProperties.city !== 'undefined')&&(serviceProperties.city !== null)&&(serviceProperties.city !== 'null'))
                            {
                                popupText += '<b>City: </b>' + serviceProperties.city + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('province'))
                        {
                            if((serviceProperties.province !== '')&&(serviceProperties.province !== undefined)&&(serviceProperties.province !== 'undefined')&&(serviceProperties.province !== null)&&(serviceProperties.province !== 'null'))
                            {
                                popupText += '<b>Province: </b>' + serviceProperties.province + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('phone'))
                        {
                            if((serviceProperties.phone !== '')&&(serviceProperties.phone !== undefined)&&(serviceProperties.phone !== 'undefined')&&(serviceProperties.phone !== null)&&(serviceProperties.phone !== 'null'))
                            {
                                popupText += '<b>Phone: </b>' + serviceProperties.phone + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('fax'))
                        {
                            if((serviceProperties.fax !== '')&&(serviceProperties.fax !== undefined)&&(serviceProperties.fax !== 'undefined')&&(serviceProperties.fax !== null)&&(serviceProperties.fax !== 'null'))
                            {
                                popupText += '<b>Fax: </b>' + serviceProperties.fax + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('note'))
                        {
                            if((serviceProperties.note !== '')&&(serviceProperties.note !== undefined)&&(serviceProperties.note !== 'undefined')&&(serviceProperties.note !== null)&&(serviceProperties.note !== 'null'))
                            {
                                popupText += '<b>Notes: </b>' + serviceProperties.note + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('agency'))
                        {
                            if((serviceProperties.agency !== '')&&(serviceProperties.agency !== undefined)&&(serviceProperties.agency !== 'undefined')&&(serviceProperties.agency !== null)&&(serviceProperties.agency !== 'null'))
                            {
                                popupText += '<b>Agency: </b>' + serviceProperties.agency + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('code'))
                        {
                            if((serviceProperties.code !== '')&&(serviceProperties.code !== undefined)&&(serviceProperties.code !== 'undefined')&&(serviceProperties.code !== null)&&(serviceProperties.code !== 'null'))
                            {
                                popupText += '<b>Code: </b>' + serviceProperties.code + "<br>";
                            }
                        }
                        
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
                        
                        popupText += '</p>';

                        if(geoJsonServiceData.hasOwnProperty("realtime"))
                        {
                            if(!jQuery.isEmptyObject(geoJsonServiceData.realtime))
                            {
                                realTimeData = geoJsonServiceData.realtime;
                                popupText += '<table id="' + latLngId + '" class="gisPopupTable" style="border: none">';

                                popupText += '<tr>';
                                for(var i = 0; i < realTimeData.head.vars.length; i++)
                                {
                                    if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                    {
                                        popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function(str){ return str.toUpperCase(); }) + '</th>';
                                    }
                                }
                                popupText += '</tr>';

                                popupText += '<tr>';
                                for(var i = 0; i < realTimeData.head.vars.length; i++)
                                {
                                    if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                    {
                                        if(realTimeData.results.bindings[0][realTimeData.head.vars[i]].value === 'Not Available')
                                        {
                                            popupText += '<td style="background: ' + color2 + '; background: -webkit-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: -o-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: -moz-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: linear-gradient(to right, ' + color2 + ', ' + 'white' + ');">-</td>'; 
                                            realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                        }
                                        else
                                        {
                                            popupText += '<td style="background: ' + color2 + '; background: -webkit-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: -o-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: -moz-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: linear-gradient(to right, ' + color2 + ', ' + 'white' + ');">' + realTimeData.results.bindings[0][realTimeData.head.vars[i]].value + '</td>';
                                        }
                                    }
                                    else
                                    {
                                        measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                    }
                                }
                                popupText += '</tr>';

                                popupText += '<tr>';
                                for(var i = 0; i < realTimeData.head.vars.length; i++)
                                {
                                    if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                    {
                                        popupText += '<td style="padding-top: 10px; padding-bottom: 2px;"><button type="button" class="lastValueBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-lastDataClicked="false" data-targetWidgets="' + targetWidgets + '" data-lastValue="' + realTimeData.results.bindings[0][realTimeData.head.vars[i]].value + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last</button></td>';
                                    }
                                }
                                popupText += '</tr>';

                                popupText += '<tr>';
                                for(var i = 0; i < realTimeData.head.vars.length; i++)
                                {
                                    if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                    {
                                        popupText += '<td style="padding-top: 2px; padding-bottom: 2px;"><button type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">4 Hours</button></td>';
                                    }
                                }
                                popupText += '</tr>';

                                popupText += '<tr>';
                                for(var i = 0; i < realTimeData.head.vars.length; i++)
                                {
                                    if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                    {
                                        popupText += '<td style="padding-top: 2px; padding-bottom: 2px;"><button type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-timeTrendClicked="false" data-range-shown="Day" data-range="1/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Day</button></td>';
                                    }
                                }

                                popupText += '</tr>';
                                popupText += '</table>';
                                popupText += '<p><b>Keep data on target widget(s) after popup close: </b><input data-id="' + latLngId + '" type="checkbox" class="gisPopupKeepDataCheck" data-keepData="false"/><br>';
                                popupText += '<b>Last update: </b>' + measuredTime + "</p>"; 
                            }
                        }
                        
                        newpopup = L.popup({
                            closeOnClick: false,//Non lo levare, sennò autoclose:false non funziona
                            autoClose: false,
                            offset: [15, 0], 
                            minWidth: 215, 
                            maxWidth : 1200
                        }).setContent(popupText);

                        event.target.bindPopup(newpopup).openPopup();
                        
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("width", "70px");
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("background", color2);
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("border", "none");
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("color", "black");

                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').focus(function(){
                            $(this).css("outline", "0");
                        });

                        $('input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').off('click');
                        $('input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').click(function(){
                            if($(this).attr("data-keepData") === "false")
                            {
                               $(this).attr("data-keepData", "true"); 
                            }
                            else
                            {
                               $(this).attr("data-keepData", "false"); 
                            }
                        });

                        $('button.lastValueBtn').off('mouseenter');
                        $('button.lastValueBtn').off('mouseleave');
                        $('button.lastValueBtn[data-id="' + latLngId + '"]').hover(function(){
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
                            var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html();

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

                        $('button.timeTrendBtn').off('mouseenter');
                        $('button.timeTrendBtn').off('mouseleave');
                        $('button.timeTrendBtn[data-id="' + latLngId + '"]').hover(function(){
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
                            var colIndex = $(this).parent().index();
                            var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html() + " - " + $(this).attr("data-range-shown");

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
                        }, 
                        function(){
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
                        });

                        $('button.lastValueBtn[data-id=' + latLngId + ']').off('click');
                        $('button.lastValueBtn[data-id=' + latLngId + ']').click(function(event){
                            $('button.lastValueBtn').each(function(i){
                                $(this).css("background", $(this).attr("data-color2"));
                            });
                            $('button.lastValueBtn').css("font-weight", "normal");
                            $(this).css("background", $(this).attr("data-color1"));
                            $(this).css("font-weight", "bold");
                            $('button.lastValueBtn').attr("data-lastDataClicked", "false");
                            $(this).attr("data-lastDataClicked", "true");
                            var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                            var colIndex = $(this).parent().index();
                            var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html();

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

                        $('button.timeTrendBtn').off('click');
                        $('button.timeTrendBtn').click(function(event){
                            $('button.timeTrendBtn').css("background", $(this).attr("data-color2"));
                            $('button.timeTrendBtn').css("font-weight", "normal");
                            $(this).css("background", $(this).attr("data-color1"));
                            $(this).css("font-weight", "bold");
                            $('button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                            $(this).attr("data-timeTrendClicked", "true");
                            var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                            var colIndex = $(this).parent().index();
                            var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html() + " - " + $(this).attr("data-range-shown");

                            for(var i = 0; i < widgetTargetList.length; i++)
                            {
                                $.event.trigger({
                                    type: "showTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                    eventGenerator: $(this),
                                    targetWidget: widgetTargetList[i],
                                    range: $(this).attr("data-range"),
                                    color1: $(this).attr("data-color1"),
                                    color2: $(this).attr("data-color2"),
                                    widgetTitle: title,
                                    field: $(this).attr("data-field"),
                                    serviceUri: $(this).attr("data-serviceUri"),
                                    marker: markersCache["" + $(this).attr("data-id") + ""],
                                    mapRef: gisMapRef,
                                    fake: $(this).attr("data-fake")
                                }); 
                            }
                        });

                        gisMapRef.off('popupclose');
                        gisMapRef.on('popupclose', function(closeEvt) {
                            var popupContent = $('<div></div>');
                            popupContent.html(closeEvt.popup._content);
                            
                            if(popupContent.find("button.lastValueBtn").length > 0)
                            {
                                var widgetTargetList = popupContent.find("button.lastValueBtn").eq(0).attr("data-targetWidgets").split(',');

                                if(($('button.lastValueBtn[data-lastDataClicked=true]').length > 0)&&($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false"))
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

                                if(($('button.timeTrendBtn[data-timeTrendClicked=true]').length > 0)&&($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false"))
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

                        $('div.leaflet-popup').off('click');
                        $('div.leaflet-popup').on('click', function(){
                            var compLatLngId = $(this).find('input[type=hidden]').val();

                            $('div.leaflet-popup').css("z-index", "-1");
                            $(this).css("z-index", "999999");

                            $('input.gisPopupKeepDataCheck').off('click');
                            $('input.gisPopupKeepDataCheck[data-id="' + compLatLngId + '"]').click(function(){
                            if($(this).attr("data-keepData") === "false")
                                {
                                   $(this).attr("data-keepData", "true"); 
                                }
                                else
                                {
                                   $(this).attr("data-keepData", "false"); 
                                }
                            });

                            $('button.lastValueBtn').off('mouseenter');
                            $('button.lastValueBtn').off('mouseleave');
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
                                var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html();

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

                            $('button.timeTrendBtn').off('mouseenter');
                            $('button.timeTrendBtn').off('mouseleave');

                            $('button.timeTrendBtn[data-id="' + compLatLngId + '"]').hover(function(){
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
                                var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html() + " - " + $(this).attr("data-range-shown");

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
                            }, 
                            function(){
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
                            });

                            $('button.lastValueBtn').off('click');
                            $('button.lastValueBtn').click(function(event){
                                $('button.lastValueBtn').each(function(i){
                                    $(this).css("background", $(this).attr("data-color2"));
                                });
                                $('button.lastValueBtn').css("font-weight", "normal");
                                $(this).css("background", $(this).attr("data-color1"));
                                $(this).css("font-weight", "bold");
                                $('button.lastValueBtn').attr("data-lastDataClicked", "false");
                                $(this).attr("data-lastDataClicked", "true");
                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                var colIndex = $(this).parent().index();
                                var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html();

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

                            $('button.timeTrendBtn').off('click');
                            $('button.timeTrendBtn').click(function(event){
                                $('button.timeTrendBtn').each(function(i){
                                    $(this).css("background", $(this).attr("data-color2"));
                                });
                                $('button.timeTrendBtn').css("font-weight", "normal");
                                $(this).css("background", $(this).attr("data-color1"));
                                $(this).css("font-weight", "bold");
                                $('button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                $(this).attr("data-timeTrendClicked", "true");
                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                var colIndex = $(this).parent().index();
                                var title = $(this).parents("tbody").find("tr").eq(0).find("th").eq(colIndex).html() + " - " + $(this).attr("data-range-shown");

                                for(var i = 0; i < widgetTargetList.length; i++)
                                {
                                    $.event.trigger({
                                        type: "showTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                        eventGenerator: $(this),
                                        targetWidget: widgetTargetList[i],
                                        range: $(this).attr("data-range"),
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
                iconUrl: mapPinImg
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
                var popupText, realTimeData, measuredTime, targetWidgets, color1, color2 = null;
                if(feature.properties.fake === 'true')
                {
                    urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + feature.id;
                    fake = true;
                    fakeId = feature.id;
                }
                else
                {
                    urlToCall = "<?php echo $serviceMapUrlPrefix; ?>api/v1/?serviceUri=" + feature.properties.serviceUri + "&format=json";
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
                        //console.log(JSON.stringify(geoJsonServiceData));
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

                        popupText = '<input type="hidden" value="' + latLngId + '"/>' +
                                    '<h3 class="gisPopupTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + serviceProperties.name + '</h3>' +
                                    '<p><b>Typology: </b>' + serviceClass + " - " + serviceSubclass + '<br>';

                        if(serviceProperties.hasOwnProperty('description'))
                        {
                            if((serviceProperties.description !== '')&&(serviceProperties.description !== undefined)&&(serviceProperties.description !== 'undefined')&&(serviceProperties.description !== null)&&(serviceProperties.description !== 'null'))
                            {
                                popupText += '<b>Description: </b>' + serviceProperties.description + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('website'))
                        {
                            if((serviceProperties.website !== '')&&(serviceProperties.website !== undefined)&&(serviceProperties.website !== 'undefined')&&(serviceProperties.website !== null)&&(serviceProperties.website !== 'null'))
                            {
                                if(serviceProperties.website.includes('http')||serviceProperties.website.includes('https'))
                                {
                                    popupText += '<b><a href="' + serviceProperties.website + '" target="_blank">Website</a></b><br>';
                                }
                                else
                                {
                                    popupText += '<b><a href="' + serviceProperties.website + '" target="_blank">Website</a></b><br>';
                                }
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('email'))
                        {
                            if((serviceProperties.email !== '')&&(serviceProperties.email !== undefined)&&(serviceProperties.email !== 'undefined')&&(serviceProperties.email !== null)&&(serviceProperties.email !== 'null'))
                            {
                                popupText += '<b>E-Mail: </b>' + serviceProperties.email + '<br>';
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('address'))
                        {
                            if((serviceProperties.address !== '')&&(serviceProperties.address !== undefined)&&(serviceProperties.address !== 'undefined')&&(serviceProperties.address !== null)&&(serviceProperties.address !== 'null'))
                            {
                                popupText += '<b>Address: </b>' + serviceProperties.address + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('civic'))
                        {
                            if((serviceProperties.civic !== '')&&(serviceProperties.civic !== undefined)&&(serviceProperties.civic !== 'undefined')&&(serviceProperties.civic !== null)&&(serviceProperties.civic !== 'null'))
                            {
                                popupText += '<b>Civic n.: </b>' + serviceProperties.civic + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('cap'))
                        {
                            if((serviceProperties.cap !== '')&&(serviceProperties.cap !== undefined)&&(serviceProperties.cap !== 'undefined')&&(serviceProperties.cap !== null)&&(serviceProperties.cap !== 'null'))
                            {
                                popupText += '<b>C.A.P.: </b>' + serviceProperties.cap + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('city'))
                        {
                            if((serviceProperties.city !== '')&&(serviceProperties.city !== undefined)&&(serviceProperties.city !== 'undefined')&&(serviceProperties.city !== null)&&(serviceProperties.city !== 'null'))
                            {
                                popupText += '<b>City: </b>' + serviceProperties.city + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('province'))
                        {
                            if((serviceProperties.province !== '')&&(serviceProperties.province !== undefined)&&(serviceProperties.province !== 'undefined')&&(serviceProperties.province !== null)&&(serviceProperties.province !== 'null'))
                            {
                                popupText += '<b>Province: </b>' + serviceProperties.province + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('phone'))
                        {
                            if((serviceProperties.phone !== '')&&(serviceProperties.phone !== undefined)&&(serviceProperties.phone !== 'undefined')&&(serviceProperties.phone !== null)&&(serviceProperties.phone !== 'null'))
                            {
                                popupText += '<b>Phone: </b>' + serviceProperties.phone + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('fax'))
                        {
                            if((serviceProperties.fax !== '')&&(serviceProperties.fax !== undefined)&&(serviceProperties.fax !== 'undefined')&&(serviceProperties.fax !== null)&&(serviceProperties.fax !== 'null'))
                            {
                                popupText += '<b>Fax: </b>' + serviceProperties.fax + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('note'))
                        {
                            if((serviceProperties.note !== '')&&(serviceProperties.note !== undefined)&&(serviceProperties.note !== 'undefined')&&(serviceProperties.note !== null)&&(serviceProperties.note !== 'null'))
                            {
                                popupText += '<b>Notes: </b>' + serviceProperties.note + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('agency'))
                        {
                            if((serviceProperties.agency !== '')&&(serviceProperties.agency !== undefined)&&(serviceProperties.agency !== 'undefined')&&(serviceProperties.agency !== null)&&(serviceProperties.agency !== 'null'))
                            {
                                popupText += '<b>Agency: </b>' + serviceProperties.agency + "<br>";
                            }
                        }
                        
                        if(serviceProperties.hasOwnProperty('code'))
                        {
                            if((serviceProperties.code !== '')&&(serviceProperties.code !== undefined)&&(serviceProperties.code !== 'undefined')&&(serviceProperties.code !== null)&&(serviceProperties.code !== 'null'))
                            {
                                popupText += '<b>Code: </b>' + serviceProperties.code + "<br>";
                            }
                        }
                        
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
                        
                        popupText += '</p>';

                        if(geoJsonServiceData.hasOwnProperty("realtime"))
                        {
                            if(!jQuery.isEmptyObject(geoJsonServiceData.realtime))
                            {
                                realTimeData = geoJsonServiceData.realtime;
                                popupText += '<table id="' + latLngId + '" class="gisPopupTable" style="border: none">';

                                popupText += '<tr>';
                                for(var i = 0; i < realTimeData.head.vars.length; i++)
                                {
                                    if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                    {
                                        popupText += '<th style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function(str){ return str.toUpperCase(); }) + '</th>';
                                    }
                                }
                                popupText += '</tr>';

                                popupText += '<tr>';
                                for(var i = 0; i < realTimeData.head.vars.length; i++)
                                {
                                    if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                    {
                                        if(realTimeData.results.bindings[0][realTimeData.head.vars[i]].value === 'Not Available')
                                        {
                                            popupText += '<td style="background: ' + color2 + '; background: -webkit-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: -o-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: -moz-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: linear-gradient(to right, ' + color2 + ', ' + 'white' + ');">-</td>'; 
                                            realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                        }
                                        else
                                        {
                                            popupText += '<td style="background: ' + color2 + '; background: -webkit-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: -o-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: -moz-linear-gradient(right, ' + color2 + ', ' + 'white' + '); background: linear-gradient(to right, ' + color2 + ', ' + 'white' + ');">' + realTimeData.results.bindings[0][realTimeData.head.vars[i]].value + '</td>';
                                        }
                                    }
                                    else
                                    {
                                        measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                    }
                                }
                                popupText += '</tr>';

                                popupText += '<tr>';
                                for(var i = 0; i < realTimeData.head.vars.length; i++)
                                {
                                    var timeTrendTitle = serviceProperties.name + " - " + realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function(str){ return str.toUpperCase(); });
                                    if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                    {
                                        popupText += '<td style="padding-top: 2px; padding-bottom: 2px;"><button type="button" class="timeTrendBtn btn btn-sm" data-id="' + latLngId + '" data-title="' + timeTrendTitle + '" data-field="' + realTimeData.head.vars[i] + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">4 Hours</button></td>';
                                    }
                                }
                                popupText += '</tr>';

                                popupText += '<tr>';
                                for(var i = 0; i < realTimeData.head.vars.length; i++)
                                {
                                    var timeTrendTitle = serviceProperties.name + " - " + realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function(str){ return str.toUpperCase(); });
                                    if((realTimeData.head.vars[i] !== 'updating')&&(realTimeData.head.vars[i] !== 'measuredTime')&&(realTimeData.head.vars[i] !== 'instantTime'))
                                    {
                                        popupText += '<td style="padding-top: 2px; padding-bottom: 2px;"><button type="button" class="timeTrendBtn btn btn-sm" data-id="' + latLngId + '" data-title="' + timeTrendTitle + '" data-field="' + realTimeData.head.vars[i] + '" data-timeTrendClicked="false" data-range-shown="Day" data-range="1/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Day</button></td>';
                                    }
                                }

                                popupText += '</tr>';
                                popupText += '</table>';
                                popupText += '<p><b>Last update: </b>' + measuredTime; 
                            }
                        }

                        newpopup = L.popup({
                            closeOnClick: false,//Non lo levare, sennò autoclose:false non funziona
                            autoClose: false,
                            offset: [15, 0], 
                            minWidth: 230,
                            maxWidth : 1200
                        }).setContent(popupText);

                        event.target.bindPopup(newpopup).openPopup();

                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("width", "70px");
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("background", color2);
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("border", "none");
                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("color", "black");

                        $('table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').focus(function(){
                            $(this).css("outline", "0");
                        });

                        $('button.timeTrendBtn').off('mouseenter');
                        $('button.timeTrendBtn').off('mouseleave');
                        $('button.timeTrendBtn[data-id="' + latLngId + '"]').hover(function(){
                            if($(this).attr("data-timeTrendClicked") === "false")
                            {
                                $(this).css("background", color1);
                                $(this).css("background", "-webkit-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                $(this).css("background", "background: -o-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                $(this).css("background", "background: -moz-linear-gradient(left, " + color1 + ", " + color2 + ")");
                                $(this).css("background", "background: linear-gradient(to left, " + color1 + ", " + color2 + ")");
                                $(this).css("font-weight", "bold");
                            }
                        }, 
                        function(){
                            if($(this).attr("data-timeTrendClicked")=== "false")
                            {
                                $(this).css("background", color2);
                                $(this).css("font-weight", "normal"); 
                            }
                        });

                        $('button.timeTrendBtn').off('click');
                        $('button.timeTrendBtn').click(function(event){
                            $('button.timeTrendBtn').css("background", $(this).attr("data-color2"));
                            $('button.timeTrendBtn').css("font-weight", "normal");
                            $(this).css("background", $(this).attr("data-color1"));
                            $(this).css("font-weight", "bold");
                            $('button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                            $(this).attr("data-timeTrendClicked", "true");
                            $("#<?= $_GET['name'] ?>_modalLinkOpenGisMap").css("height", "80%");

                            var field = $(this).attr("data-field");
                            var timeRange = $(this).attr("data-range");

                            $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").empty();
                            $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">Loading data, please wait</div><div style="text-align: center"><i class="fa fa-circle-o-notch fa-spin" style="font-size:48px"></i></div></div>');
                            $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                            $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                            $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                            $("#gisTimeTrendLoadingContent").css("position", "relative");
                            $("#gisTimeTrendLoadingContent").css("top", "50%");
                            $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                            //QUI CI DOVRAI INSERIRE LA CHIAMATA PER OTTENERE I DATI, FINCHE' NON ARRIVANO MOSTRI UN LOADING NEL DIV DEL GRAFICO
                            var convertedData = null;
                            switch(timeRange)
                            {
                                case "4/HOUR":
                                    convertedData = getFakeDataForTimeTrend4Hours();
                                    break;

                                case "1/DAY":
                                    convertedData = getFakeDataForTimeTrendDay();
                                    break;

                                default:
                                    convertedData = getFakeDataForTimeTrend4Hours();
                                    break;
                            }
                            
                            //var convertedData = convertDataFromSmToDmForGis(garageStazioneTimeTrend, field);
                            var color1 = $(this).attr("data-color1"); 
                            var color2 = $(this).attr("data-color2");
                            var timeTrendTitle = $(this).attr("data-title");
                            var timeTrendSubtitle = $(this).attr("data-range-shown");

                            //Vanno ordinati temporalmente in ordine crescente, sennò Highcharts solleva un'eccezione
                            //convertedData.data.sort(convertedDataCompareForGis);

                            if($("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                            {
                                //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)
                                drawTimeTrendInFullscreen(convertedData, timeRange, color1, color2, timeTrendTitle, timeTrendSubtitle);
                            }
                            else
                            {
                                setTimeout(function(){
                                    $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                    $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").show();    
                                    //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)
                                    drawTimeTrendInFullscreen(convertedData, timeRange, color1, color2, timeTrendTitle, timeTrendSubtitle);
                                }, 500);
                            }
                        });

                        gisFullscreenMapRef.off('popupclose');
                        gisFullscreenMapRef.on('popupclose', function(closeEvt) {
                            var popupContent = $('<div></div>');
                            popupContent.html(closeEvt.popup._content);

                            if($('button.timeTrendBtn[data-timeTrendClicked=true]').length > 0)
                            {
                                $('#<?= $_GET['name'] ?>_modalLinkOpenGisMap').css("height", "80vh");
                                $('#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend').hide();
                                $('#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend').css("height", "0vh");
                                $('#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend').empty();
                            }
                        });

                        $('div.leaflet-popup').off("click");
                        $('div.leaflet-popup').on("click", function(){
                            var compLatLngId = $(this).find('input[type=hidden]').val();

                            $('div.leaflet-popup').css("z-index", "-1");
                            $(this).css("z-index", "999999");

                            $('button.timeTrendBtn').off('mouseenter');
                            $('button.timeTrendBtn').off('mouseleave');

                            $('button.timeTrendBtn[data-id="' + compLatLngId + '"]').hover(function(){
                                if($(this).attr("data-timeTrendClicked") === "false")
                                {
                                    $(this).css("background", color1);
                                    $(this).css("background", "-webkit-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                    $(this).css("background", "background: -o-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                    $(this).css("background", "background: -moz-linear-gradient(left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                    $(this).css("background", "background: linear-gradient(to left, " + $(this).attr('data-color1') + ", " + $(this).attr('data-color2') + ")");
                                    $(this).css("font-weight", "bold");
                                }
                            }, 
                            function(){
                                if($(this).attr("data-timeTrendClicked")=== "false")
                                {
                                    $(this).css("background", $(this).attr('data-color2'));
                                    $(this).css("font-weight", "normal"); 
                                }
                            });

                            $('button.timeTrendBtn').off('click');
                            $('button.timeTrendBtn').click(function(event){
                                $('button.timeTrendBtn').each(function(i){
                                    $(this).css("background", $(this).attr("data-color2"));
                                });
                                $('button.timeTrendBtn').css("font-weight", "normal");
                                $(this).css("background", $(this).attr("data-color1"));
                                $(this).css("font-weight", "bold");
                                $('button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                $(this).attr("data-timeTrendClicked", "true");
                                $("#<?= $_GET['name'] ?>_modalLinkOpenGisMap").css("height", "80%");

                                var field = $(this).attr("data-field");
                                var timeRange = $(this).attr("data-range");

                                $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").empty();
                                $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").html('<div id="gisTimeTrendLoadingContent"><div style="text-align: center; font-size: 28px; font-family: Verdana, sans-serif; margin-bottom: 20px">Loading data, please wait</div><div style="text-align: center"><i class="fa fa-circle-o-notch fa-spin" style="font-size:48px"></i></div></div>');
                                $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").css("position", "relative");
                                $("#gisTimeTrendLoadingContent").css("margin-left", "auto");
                                $("#gisTimeTrendLoadingContent").css("margin-right", "auto");
                                $("#gisTimeTrendLoadingContent").css("position", "relative");
                                $("#gisTimeTrendLoadingContent").css("top", "50%");
                                $("#gisTimeTrendLoadingContent").css("transform", "translateY(-50%)");
                                //QUI CI DOVRAI INSERIRE LA CHIAMATA PER OTTENERE I DATI, FINCHE' NON ARRIVANO MOSTRI UN LOADING NEL DIV DEL GRAFICO

                                //var convertedData = convertDataFromSmToDmForGis(garageStazioneTimeTrend, field);
                                var convertedData = null;
                                switch(timeRange)
                                {
                                    case "4/HOUR":
                                        convertedData = getFakeDataForTimeTrend4Hours();
                                        break;

                                    case "1/DAY":
                                        convertedData = getFakeDataForTimeTrendDay();
                                        break;

                                    default:
                                        convertedData = getFakeDataForTimeTrend4Hours();
                                        break;
                                }
                                var color1 = $(this).attr("data-color1"); 
                                var color2 = $(this).attr("data-color2");
                                var timeTrendTitle = $(this).attr("data-title");
                                var timeTrendSubtitle = $(this).attr("data-range-shown");

                                //Vanno ordinati temporalmente in ordine crescente, sennò Highcharts solleva un'eccezione
                                //convertedData.data.sort(convertedDataCompareForGis);

                                if($("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").is(":visible"))
                                {
                                    //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)
                                    drawTimeTrendInFullscreen(convertedData, timeRange, color1, color2, timeTrendTitle, timeTrendSubtitle);
                                }
                                else
                                {
                                    setTimeout(function(){
                                        $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").css("height", "20%");
                                        $("#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend").show();    
                                        //RANGE TEMPORALI GESTIBILI DAL WIDGET: 4/HOUR, 12/HOUR, 1/DAY, 7/DAY, 30/DAY, 365/DAY (IL DRAW CANCELLA DA SOLO IL LOADING)
                                        drawTimeTrendInFullscreen(convertedData, timeRange, color1, color2, timeTrendTitle, timeTrendSubtitle);
                                    }, 500);
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
                            minWidth: 215, 
                            //maxWidth : 600
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
                            return false;
                        }
                    }
                }
                
                convertedDate = convertedDate.replace("T", " ");
                var plusIndex = convertedDate.indexOf("+");
                convertedDate = convertedDate.substr(0, plusIndex);
                singleData.commit.author.computationDate = convertedDate;
                singleData.commit.author.value = parseFloat(singleOriginalData[field].value);
                
                convertedData.data.push(singleData);
            }
            
            return convertedData;
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
            var day, value, dayParts, timeParts, date, maxValue, nInterval = null;
            var seriesData = [];
            var valuesData = [];
            var xAxisUnits = [['millisecond', // unit name
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

                dayParts = day.substring(0, day.indexOf(' ')).split('-');

                if((timeRange == '1/DAY') || (timeRange.includes("HOUR"))) 
                {
                    timeParts = day.substr(day.indexOf(' ') + 1, 5).split(':');
                    date = Date.UTC(dayParts[0], dayParts[1]-1, dayParts[2], timeParts[0], timeParts[1]);
                }
                else 
                {
                    date = Date.UTC(dayParts[0], dayParts[1] - 1, dayParts[2]);
                }

                seriesData.push([date, value]);
                valuesData.push(value);
            }

            maxValue = Math.max.apply(Math, valuesData);
            nInterval = parseFloat((maxValue / 4).toFixed(1));
            
            $('#<?= $_GET['name'] ?>_modalLinkOpenGisTimeTrend').highcharts({
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
        $("#<?= $_GET['name'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
        widgetProperties = getWidgetProperties(widgetName);
        
        createFullscreenModal();
        
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            $("a.iconFullscreenModal").tooltip();
            $("a.iconFullscreenTab").tooltip();
            
            styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));
            
            $('#<?= $_GET['name'] ?>_div').attr("data-noPointsUrl", url);
            $('#<?= $_GET['name'] ?>_div').attr("data-role", url);
            
            //Inizio eventuale codice ad hoc basato sui dati della metrica
            //showTitle è dalle impostazioni su singolo widget, show header dalle impostazioni su embed dashboard
            if((hostFile === "index") && (showHeader === false))
            {
                $('#<?= $_GET['name'] ?>_header').css("display", "none");
                height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight"));
                wrapperH = parseInt($('#<?= $_GET['name'] ?>_div').outerHeight());
                topWrapper = "0px";
            }
            else
            {
                if((hostFile === "index")&&(showTitle === "no")&&(showHeader === true))
                {
                    $('#<?= $_GET['name'] ?>_header').css("display", "none");
                    height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight"));
                    wrapperH = parseInt($('#<?= $_GET['name'] ?>_div').outerHeight());
                    topWrapper = "0px";
                }
                else
                {
                    $('#<?= $_GET['name'] ?>_header').css("display", "block");
                    height = parseInt($("#<?= $_GET['name'] ?>_div").prop("offsetHeight") - 25);
                    wrapperH = parseInt($('#<?= $_GET['name'] ?>_div').outerHeight() - 25);
                    topWrapper = "25px";

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
                           $("#" + widgetName + "_buttonsDiv").css("width", "100px");
                           titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 100 - 2));
                           $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).show();
                           $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).show();
                        }
                        else
                        {
                           if((widgetProperties.param.enableFullscreenModal === 'yes')&&(widgetProperties.param.enableFullscreenTab === 'no'))
                           {
                               $("#" + widgetName + "_buttonsDiv").css("width", "75px");
                               titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 75 - 2));
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).show();
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).hide();
                           }
                           else
                           {
                              if((widgetProperties.param.enableFullscreenModal === 'no')&&(widgetProperties.param.enableFullscreenTab === 'yes')) 
                              {
                                $("#" + widgetName + "_buttonsDiv").css("width", "75px");
                                titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 75 - 2));
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).hide();
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).show();   
                              }
                              else
                              {
                                $("#" + widgetName + "_buttonsDiv").css("width", "50px");
                                titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 2));
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).hide();
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).hide();
                              }
                           }
                        }
                    }
                    else
                    {
                       $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                       $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();

                       if((widgetProperties.param.enableFullscreenTab === 'yes')&&(widgetProperties.param.enableFullscreenModal === 'yes'))
                        {
                           $("#" + widgetName + "_buttonsDiv").css("width", "50px");
                           titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 2));
                           $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).show();
                           $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).show();
                        }
                        else
                        {
                           if((widgetProperties.param.enableFullscreenTab === 'yes')&&(widgetProperties.param.enableFullscreenModal === 'no'))
                           {
                               $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                               titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).hide();
                               $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).show();
                           }
                           else
                           {
                              if((widgetProperties.param.enableFullscreenTab === 'no')&&(widgetProperties.param.enableFullscreenModal === 'yes')) 
                              {
                                  $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                  titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                                  $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).show();
                                  $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).hide();
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

            $('#<?= $_GET['name'] ?>_content').css("width", wrapperW + "px");
            $('#<?= $_GET['name'] ?>_content').css("height", wrapperH + "px");
            
            if(firstLoad !== false)
            {
                showWidgetContent(widgetName);
            }
            else
            {
                elToEmpty.empty();
            }
            
            $("#<?= $_GET['name'] ?>_iFrame").load(iframeLoaded);
            
            switch(url)
            {
                case "map":
                   $("#<?= $_GET['name'] ?>_wrapper").hide(); 
                   $("#<?= $_GET['name'] ?>_mapDiv").hide();
                   $("#<?= $_GET['name'] ?>_gisMapDiv").hide();
                   $("#<?= $_GET['name'] ?>_defaultMapDiv").show(); 
                   loadDefaultMap(); 
                   break;
                  
                case "gisTarget":
                    $("#<?= $_GET['name'] ?>_wrapper").hide(); 
                    $("#<?= $_GET['name'] ?>_mapDiv").hide();
                    $("#<?= $_GET['name'] ?>_defaultMapDiv").hide();
                    $("#<?= $_GET['name'] ?>_gisMapDiv").show();
                    widgetParameters = JSON.parse(widgetProperties.param.parameters);
                    loadGisMap();
                    
                    $(document).off('addLayerFromGis_' + widgetName);
                    $(document).on('addLayerFromGis_' + widgetName, function(event) 
                    {
                        var mapBounds = gisMapRef.getBounds();
                        var query, targets = null;
                        var eventGenerator = event.eventGenerator;
                        var color1 = event.color1;
                        var color2 = event.color2;
                        
                        var loadingDiv = $('<div class="gisMapLoadingDiv"></div>');
                        
                        if($('#<?= $_GET['name'] ?>_content div.gisMapLoadingDiv').length > 0)
                        {
                            loadingDiv.insertAfter($('#<?= $_GET['name'] ?>_content div.gisMapLoadingDiv').last());
                        }
                        else
                        {
                            loadingDiv.insertAfter($('#<?= $_GET['name'] ?>_gisMapDiv'));
                        }
                        
                        loadingDiv.css("top", ($('#<?= $_GET['name'] ?>_div').height() - ($('#<?= $_GET['name'] ?>_content div.gisMapLoadingDiv').length * loadingDiv.height())) + "px");
                        loadingDiv.css("left", ($('#<?= $_GET['name'] ?>_div').width() - loadingDiv.width()) + "px");
                        
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
                        
                        if(pattern.test(event.query))
                        {
                            console.log("Service Map selection substitution");
                            query = event.query.replace(pattern, "selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng);
                        }
                        else
                        {
                            console.log("Service Map selection addition");
                            query = event.query + "&selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng;
                        }
                        
                        if(event.targets !== "")
                        {
                            targets = event.targets.split(",");
                        }
                        else
                        {
                            targets = [];
                        }
                        
                        $.ajax({
                            url: query,
                            type: "GET",
                            data: {},
                            async: true,
                            timeout: 0,
                            dataType: 'json',
                            success: function(geoJsonData) 
                            {
                                var fatherGeoJsonNode = null;
                                
                                if(geoJsonData.hasOwnProperty("BusStops"))
                                {
                                    fatherGeoJsonNode = geoJsonData.BusStops;
                                }
                                else
                                {
                                    if(geoJsonData.hasOwnProperty("SensorSites"))
                                    {
                                        fatherGeoJsonNode = geoJsonData.SensorSites;
                                    }
                                    else
                                    {
                                        fatherGeoJsonNode = geoJsonData.Services;
                                    }
                                }
                                
                                for(var i = 0; i < fatherGeoJsonNode.features.length; i++)
                                {
                                    fatherGeoJsonNode.features[i].properties.targetWidgets = targets;
                                    fatherGeoJsonNode.features[i].properties.color1 = color1;
                                    fatherGeoJsonNode.features[i].properties.color2 = color2;
                                }

                                if(!gisLayersOnMap.hasOwnProperty(event.desc))
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
                                        loadingDiv.nextAll("#<?= $_GET['name'] ?>_content div.gisMapLoadingDiv").each(function(i){
                                            $(this).css("top", ($('#<?= $_GET['name'] ?>_div').height() - (($('#<?= $_GET['name'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
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
                                    /*eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "63%");
                                    eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("height", "37%");
                                    eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "flex");
                                    eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("align-items", "flex-start");
                                    eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("justify-content", "center");
                                    eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("text-align", "center");  
                                    eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown span").css("display", "block");
                                    
                                    var localfontSize = 120;
                                    var containerH = eventGenerator.parents("div.gisMapPtrContainer").outerHeight();

                                    do {
                                        localfontSize = localfontSize - 1;
                                        eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown i").css("font-size", Math.floor(localfontSize / 3) + "px");
                                    }while(localfontSize > containerH);
                                    
                                    eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown i").css("text-shadow", "1px 1px 2px black");*/
                                }
                                
                                eventGenerator.show();
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
                                        loadingDiv.nextAll("#<?= $_GET['name'] ?>_content div.gisMapLoadingDiv").each(function(i){
                                            $(this).css("top", ($('#<?= $_GET['name'] ?>_div').height() - (($('#<?= $_GET['name'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
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
                        if(gisLayersOnMap[event.desc] !== "loadError")
                        {
                            gisMapRef.removeLayer(gisLayersOnMap[event.desc]);
                        }
                        delete gisLayersOnMap[event.desc];
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
                    break;
                  
                case "none":
                   $("#<?= $_GET['name'] ?>_mapDiv").hide();
                   $("#<?= $_GET['name'] ?>_defaultMapDiv").hide(); 
                   $("#<?= $_GET['name'] ?>_gisMapDiv").hide();
                   $("#<?= $_GET['name'] ?>_wrapper").show(); 
                   break;
                  
               default:
                   $("#<?= $_GET['name'] ?>_mapDiv").hide();
                   $("#<?= $_GET['name'] ?>_defaultMapDiv").hide(); 
                   $("#<?= $_GET['name'] ?>_gisMapDiv").hide();
                   $("#<?= $_GET['name'] ?>_wrapper").show();
                   $('#<?= $_GET['name'] ?>_iFrame').attr("src", url);
                   $('#<?= $_GET['name'] ?>_iFrame').attr("data-oldsrc", url);
                   break;
            }
            
            $('#<?= $_GET['name'] ?>_xPlus').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                sizeX = parseInt(parseInt(sizeX) + 1);
                changeDimX("+");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("width<br/>" + sizeX);
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                updateZoomControlsPosition();

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_xMin').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                sizeX = parseInt(parseInt(sizeX) - 1);
                changeDimX("-");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("width<br/>" + sizeX);
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                updateZoomControlsPosition();

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_yPlus').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                sizeY = parseInt(parseInt(sizeY) + 1);
                changeDimY("+");

                updateZoomControlsPosition();

                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("height<br/>" + sizeY);
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_yMin').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                sizeY = parseInt(parseInt(sizeY) - 1);
                changeDimY("-");

                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("height<br/>" + sizeY);
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                updateZoomControlsPosition();

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_zoomIn').on('click', function () 
            {
                clearTimeout(zoomDisplayTimeout);
                currentZoom = (parseFloat(currentZoom) + parseFloat('0.05')).toFixed(2);
                changeZoom();
                var percentZoom = parseInt(currentZoom*100);
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                $("#<?= $_GET['name'] ?>_zoomDisplay").html("zoom<br/>" + percentZoom + "%");
                $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");

                zoomDisplayTimeout = setTimeout(function(){
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                }, 300);
            });

            $('#<?= $_GET['name'] ?>_zoomOut').on('click', function () 
            {
                if(parseFloat(currentZoom - 0.1).toFixed(2) > 0.1)
                {
                    clearTimeout(zoomDisplayTimeout);
                    currentZoom = parseFloat(currentZoom - 0.05).toFixed(2);
                    changeZoom();
                    var percentZoom = parseInt(currentZoom*100);
                    $("#<?= $_GET['name'] ?>_zoomDisplay").html("");
                    $("#<?= $_GET['name'] ?>_zoomDisplay").html("zoom<br/>" + percentZoom + "%");
                    $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "visible");
                    zoomDisplayTimeout = setTimeout(function(){
                        $("#<?= $_GET['name'] ?>_zoomDisplay").css("visibility", "hidden");
                    }, 300);
                }
                else
                {
                    alert("You have reached the minimum zoom factor");
                }
            });
            
            $('#<?= $_GET['name'] ?>_buttonsDiv a.iconFullscreenModal').click(function()
            {
                switch(url)
                {
                    case "map":
                      $("#<?= $_GET['name'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_GET['name'] ?>_titleDiv").html());  
                      $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").hide(); 
                      
                      if($("#<?= $_GET['name'] ?>_defaultMapDiv").is(":visible"))
                      {
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").show();
                        
                        //Creazione mappa
                        setTimeout(function(){
                            var mapdiv = "<?= $_GET['name'] ?>_modalLinkOpenBodyDefaultMap";
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
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                        
                        if(($("#<?= $_GET['name'] ?>_driverWidgetType").val() !== 'newtworkAnalysis')&&($("#<?= $_GET['name'] ?>_driverWidgetType").val() !== 'button')/*&&($("#<?= $_GET['name'] ?>_driverWidgetType").val() !== 'recreativeEvents')*/)
                        {   
                            $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").show();
                        
                            setTimeout(function(){
                                //Creazione mappa
                                var mapdiv = "<?= $_GET['name'] ?>_modalLinkOpenBodyMap";
                                fullscreenMapRef = L.map(mapdiv).setView([43.769789, 11.255694], 11);

                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                   attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                   maxZoom: 18
                                }).addTo(fullscreenMapRef);
                                fullscreenMapRef.attributionControl.setPrefix('');

                                //Popolamento mappa (se ci sono eventi su mappa originaria)
                                if($('#<?= $_GET['name'] ?>_modalLinkOpen input.fullscreenEventPoint').length > 0)
                                {
                                    switch($('#<?= $_GET['name'] ?>_modalLinkOpen input.fullscreenEventPoint').eq(0).attr("data-eventType"))
                                    {
                                        case "recreativeEvents":
                                            minLat = +90;
                                            minLng = +180;
                                            maxLat = -90;
                                            maxLng = -180;
                                            
                                            var categoryIT, name, place, startDate, endDate, startTime, freeEvent, address, civic, price, phone,
                                                descriptionIT, website, colorClass, mapIconName = null;

                                            $("#<?= $_GET['name'] ?>_modalLinkOpen input.fullscreenEventPoint").each(function(i)
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
                                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />'
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

                                            $("#<?= $_GET['name'] ?>_modalLinkOpen input.fullscreenEventPoint").each(function(i)
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
                                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />'
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

                                            $("#<?= $_GET['name'] ?>_modalLinkOpen input.fullscreenEventPoint").each(function(i)
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
                                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />'
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

                                            $("#<?= $_GET['name'] ?>_modalLinkOpen input.fullscreenEventPoint").each(function(i)
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
                                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />'
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
                                            pathsQt = $('#<?= $_GET['name'] ?>_modalLinkOpen input.fullscreenEventPoint').eq(0).attr("data-pathsQt");    
                                            var polyline, polyColor = null; 
                                            var polyGroup = L.featureGroup();
                                            //Algoritmo di popolamento mappa
                                            for(var i = 0; i < pathsQt; i++)
                                            {
                                                var path = [];

                                                $("#<?= $_GET['name'] ?>_modalLinkOpen input.fullscreenEventPoint[data-polyIndex=" + i + "]").each(function(j)
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
                            switch($("#<?= $_GET['name'] ?>_driverWidgetType").val())
                            {
                                case "newtworkAnalysis":
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").show();  
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").attr("src", $("#<?= $_GET['name'] ?>_netAnalysisServiceMapUrl").val()); 
                                    break;
                                    
                                /*case "recreativeEvents":
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").show();  
                                    $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").attr("src", $("#<?= $_GET['name'] ?>_recreativeEventsUrl").val()); 
                                    break;    */
                            }
                        }
                      }
                      
                      $("#<?= $_GET['name'] ?>_modalLinkOpen").modal('show');
                      break;

                   case "none":
                        switch($("#<?= $_GET['name'] ?>_driverWidgetType").val())
                        {
                            case "button":
                                $("#<?= $_GET['name'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_GET['name'] ?>_titleDiv").html()); 
                                $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                                $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                                $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                                $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").show();  
                                $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").attr("src", $("#<?= $_GET['name'] ?>_buttonUrl").val()); 
                                $("#<?= $_GET['name'] ?>_modalLinkOpen").modal('show');
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
                        $("#<?= $_GET['name'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_GET['name'] ?>_titleDiv").html());  
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                        $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").hide();  
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenGisMap").show();
                        
                        setTimeout(function(){
                            gisFullscreenMapCenter = gisMapRef.getCenter();
                            gisFullscreenMapStartZoom = gisMapRef.getZoom();
                            gisFullscreenMapStartBounds = gisMapRef.getBounds();
                            
                            var gisFullscreenMapDiv = "<?= $_GET['name'] ?>_modalLinkOpenGisMap";
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
                        }, 250);
                        
                        $("#<?= $_GET['name'] ?>_modalLinkOpen").modal('show');
                        break;

                   default:
                        $("#<?= $_GET['name'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_GET['name'] ?>_titleDiv").html());  
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").hide();
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                        $("#<?= $_GET['name'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                        $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").show();  
                        
                        switch($("#<?= $_GET['name'] ?>_driverWidgetType").val())
                        {
                            case "button":
                                $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").attr("src", $("#<?= $_GET['name'] ?>_buttonUrl").val()); 
                                break;
                                
                            default:
                                $("#<?= $_GET['name'] ?>_modalLinkOpen iframe").attr("src", url); 
                                break;
                        }
                        
                        $("#<?= $_GET['name'] ?>_modalLinkOpen").modal('show');
                        break;
                }
            });
            
            $('#<?= $_GET['name'] ?>_buttonsDiv a.iconFullscreenTab').click(function()
            {
                switch(url)
                {
                   case "map": case "gisTarget":
                      $("#newTabLinkOpenImpossibileMsg").html("It's not possibile to open an embedded map in an external page: please use the popup fullscreen option.");
                      $("#newTabLinkOpenImpossibile").modal('show');
                      setTimeout(function(){
                          $("#newTabLinkOpenImpossibile").modal('hide');
                      }, 4000);
                      break;

                   case "none":
                      switch($("#<?= $_GET['name'] ?>_driverWidgetType").val())
                        {
                            case "button":
                                window.open($("#<?= $_GET['name'] ?>_buttonUrl").val(), '_blank');
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

                   default:
                        switch($("#<?= $_GET['name'] ?>_driverWidgetType").val())
                        {
                            case "button":
                                window.open($("#<?= $_GET['name'] ?>_buttonUrl").val(), '_blank');
                                break;
                                
                            default:
                                window.open(url, '_blank');
                                break;
                        } 
                      break;
                }
            });
        }    
        else
        {
            console.log("Errore in caricamento proprietà widget");
        }
    });//Fine document ready 
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div" data-emptyMapShown="false">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv">
               <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
               <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
               <div class="singleBtnContainer"><a class="iconFullscreenModal" href="#" data-toggle="tooltip" title="Fullscreen popup"><span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span></a></div>
               <div class="singleBtnContainer"><a class="iconFullscreenTab" href="#" data-toggle="tooltip" title="Fullscreen new tab"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></div>
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
            <div id="<?= $_GET['name'] ?>_mapDiv" class="mapDiv"></div>
            <div id="<?= $_GET['name'] ?>_gisMapDiv" class="gisMapDiv"></div>
            <div id="<?= $_GET['name'] ?>_wrapper" class="iframeWrapper">
                <div id="<?= $_GET['name'] ?>_zoomControls" class="iframeZoomControls">
                    <div id="<?= $_GET['name'] ?>_dimDiv" class="zoomControlsRow">
                        <div class="zoomControlsLabelDiv">
                            width
                        </div>
                        <div class="zoomControlsButtonsDiv">
                            <i id="<?= $_GET['name'] ?>_xMin" class="fa fa-minus-square-o"></i>
                            <i id="<?= $_GET['name'] ?>_xPlus" class="fa fa-plus-square-o"></i>
                        </div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_dimDiv" class="zoomControlsRow">
                        <div class="zoomControlsLabelDiv">
                            height
                        </div>
                        <div class="zoomControlsButtonsDiv">
                            <i id="<?= $_GET['name'] ?>_yMin" class="fa fa-minus-square-o"></i>
                            <i id="<?= $_GET['name'] ?>_yPlus" class="fa fa-plus-square-o"></i>
                        </div>
                    </div>
                    <div id="<?= $_GET['name'] ?>_zoomDiv" class="zoomControlsRow">
                        <div class="zoomControlsLabelDiv">
                            zoom    
                        </div>
                        <div class="zoomControlsButtonsDiv">
                            <i id="<?= $_GET['name'] ?>_zoomOut" class="fa fa-minus-square-o"></i> 
                            <i id="<?= $_GET['name'] ?>_zoomIn" class="fa fa-plus-square-o"></i>
                        </div>
                    </div>
                </div>
                <div id="<?= $_GET['name'] ?>_zoomDisplay" class="zoomDisplay"></div>
                <iframe id="<?= $_GET['name'] ?>_iFrame" class="iFrame"></iframe>
            </div>
            <div id="<?= $_GET['name'] ?>_defaultMapDiv" class="defaultMapDiv"></div>
            <input type="hidden" id="<?= $_GET['name'] ?>_driverWidgetType" val=""/>
            <input type="hidden" id="<?= $_GET['name'] ?>_netAnalysisServiceMapUrl" val=""/>
            <input type="hidden" id="<?= $_GET['name'] ?>_buttonUrl" val=""/>
            <input type="hidden" id="<?= $_GET['name'] ?>_recreativeEventsUrl" val=""/>
        </div>
    </div>	
</div>