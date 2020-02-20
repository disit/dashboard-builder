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

<style type="text/css">
    .left{
        float:left;
    }
    .right{
        float: right;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 80px;
        height: 15px;
        float:right
    }

    .switch input {display:none;}

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: -4px;
        background-color: #DBDBDB;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #86C5F9;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(62px);
        -ms-transform: translateX(62px);
        transform: translateX(62px);
    }

    /*------ ADDED CSS ---------*/
    .animationOn
    {
        display: none;
    }

    .animationOn, .animationOff
    {
        color: white;
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 50%;
        font-size: 11px;
        font-family: Verdana, sans-serif;
    }

    input:checked+ .slider .on
    {display: block;}

    input:checked + .slider .off
    {display: none;}

    /*--------- END --------*/

    /* Rounded sliders */
    .slider.round {
        border-radius: 28px;
    }

    .slider.round:before {
        border-radius: 50%;}
    </style>


    <!-- CORTI - 3D Map - OSMBuildings -->
    <link href="../widgets/layers/OSMBuildings.css" rel="stylesheet">
    <script src="../widgets/layers/OSMBuildings.js"></script>

    <!-- Bring in the leaflet KML plugin -->
    <script src="../widgets/layers/KML.js"></script> 

    <script type="text/javascript" src="../js/heatmap/heatmap.js"></script>
    <script type="text/javascript" src="../js/heatmap/leaflet-heatmap.js"></script>

    <script src="../trafficRTDetails/js/leaflet.awesome-markers.min.js"></script>
    <script src="../trafficRTDetails/js/jquery.dialogextend.js"></script>
    <script src="../trafficRTDetails/js/leaflet-gps.js"></script>
    <script src="../trafficRTDetails/js/wicket.js"></script>
    <script src="../trafficRTDetails/js/wicket-leaflet.js"></script>
    <script src="../trafficRTDetails/js/date.format.js"></script>
    <script src="../trafficRTDetails/js/zoomHandler.js"></script>
    <script src="../trafficRTDetails/js/OpenLayers-2.13.1/OpenLayers.js"></script>

    <script type="text/javascript" src="../js/date_fns.min.js"></script>
    <script type="text/javascript" src="../js/moment-timezone-with-data.js"></script>
    <script type="text/javascript" src="../js/moment-with-locales.min.js"></script> 

    <!-- LEAFLET ANIMATOR PLUGIN -->
    <!-- <script type="text/javascript" src="../js/leaflet-wms-animator.js"></script> -->

    <script type='text/javascript'>

        //Ogni "main" lato client di un widget è semple incluso nel risponditore ad evento ready del documento, così siamo sicuri di operare sulla pagina già caricata
        $(document).ready(function <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId) {
<?php
$titlePatterns = array();
$titlePatterns[0] = '/_/';
$titlePatterns[1] = '/\'/';
$replacements = array();
$replacements[0] = ' ';
$replacements[1] = '&apos;';
$title = $_REQUEST['title_w'];
?>

            var headerHeight = 25;
            var hostFile = "<?= $_REQUEST['hostFile'] ?>";
            var widgetName = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>";
            var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
            var fontSize = "<?= $_REQUEST['fontSize'] ?>";
            var fontColor = "<?= $_REQUEST['fontColor'] ?>";
            var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
            var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';
            var showTitle = "<?= $_REQUEST['showTitle'] ?>";
            var showHeader = null;
            var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
            var styleParameters, metricName, udm, udmPos, appId, flowId, nrMetricType,
                    sm_field, sizeRowsWidget, sm_based, rowParameters, fontSize, countdownRef, widgetTitle, widgetHeaderColor,
                    widgetHeaderFontColor, showHeader, widgetParameters, chartColor, dataLabelsFontSize, dataLabelsFontColor,
                    chartLabelsFontSize, chartLabelsFontColor, titleWidth, enableFullscreenModal,
                    enableFullscreenTab, shownPolyGroup, geoServerUrl, heatmapUrl = null;
            var eventsOnMap = [];
            var addMode = null;
            heatmapMetricName = "";
            heatmapRange = [];
            prevZoom = null;

            //Variabili per il selector
            var gisLayersOnMap = {};
            var gisGeometryLayersOnMap = {};
            var stopGeometryAjax = {};
            var gisGeometryTankForFullscreen = {};
            var checkTankInterval = null;
            var markersCache = {};
            var myPOIId, myPOIlat, myPOIlng = "";                   // MyPOI Mod

            //Variabili multi-mappa
            var map = {};
            var baseQuery = null;
            /*   current_radius = null;
             current_opacity = null;
             changeRadiusOnZoom = false;
             estimatedRadius = null;
             estimateRadiusFlag = false;
             fullscreenHeatmap = null;
             fullscreenHeatmapFirstInstantiation = false;
             fullscreenHeatmapFirstInst = true;
             heatmapLegendColorsFullscreen = null;
             legendHeatmapFullscreen = null;
             mapName = null;
             mapDate = null;
             resetPageFlag = null;
             wmsDatasetName = null;
             passedParams = null;    */

            var current_radius = null;
            var current_opacity = null;
            var changeRadiusOnZoom = false;
            var estimatedRadius = null;
            var estimateRadiusFlag = false;
            var fullscreenHeatmap = null;
            var fullscreenHeatmapFirstInstantiation = false;
            var fullscreenHeatmapFirstInst = true;
            var heatmapLegendColorsFullscreen = null;
            var legendHeatmapFullscreen = null;
            var mapName = null;
            var mapDate = null;
            var resetPageFlag = null;
            var wmsDatasetName = null;
            var passedParams = null;
            var animationFlag = false;

            var dataForApi = "";

            var daysArray = [];
            var userTimeOffset = new Date().getTimezoneOffset();
            var snap4CityServerTime = new Date().toLocaleString("it-IT", {timeZone: "Europe/Rome"});
            var usaTime = new Date(usaTime);
            var snap4CityServerTimeOffset = "";

            //Definizioni di funzione

            console.log("entrato in widget3DMap. WidgetName = " + widgetName);

            /*  current_page = 0;
             records_per_page = 1;
             wmsLayer = null;
             wmsLayerFullscreen = null;*/

            var current_page = 0;
            var records_per_page = 1;
            var wmsLayer = null;
            var wmsLayerFullscreen = null;

            // 3d vars
            timestampISO3D = '';
            titleHeatamp3DControls = '';
            heatmapLayer3D = null;


            dynamicCor = [];
            dynamicPos = [];

            function onEachFeature(feature, layer) {
                //console.log(layer);

                /*var dataObj = {};
                 
                 dataObj.lat = layer.feature.geometry.coordinates[1];
                 dataObj.lng = layer.feature.geometry.coordinates[0];
                 dataObj.eventType = "selectorEvent";
                 
                 map.eventsOnMap.push(dataObj);
                 console.log(map.eventsOnMap);*/

            }

            //Funzione di associazione delle icone alle feature e preparazione popup per la mappa GIS
            markerList = [];
            markerIndex = 0;
            function gisPrepareCustomMarker(feature, latlng) {
                var mapPinImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                var markerIcon = L.icon({
                    iconUrl: mapPinImg,
                    iconAnchor: [16, 37]
                });

                var marker = new L.Marker(latlng, {icon: markerIcon});
                markerList.push(marker);

                // CORTI - marker 3D
                if (is3DViewOn) {
                    setTimeout(function () {
                        editCSSFor3DWidgets();
                    }, 100);
                }

                var latLngKey = latlng.lat + "" + latlng.lng;

                // CORTI - marker 3D
//                if(is3DViewOn){
//                    var marker3D;
//                    marker3D = map.default3DMapRef.addMarker({ latitude: latlng.lat, longitude: latlng.lng, altitude: 50 }, {}, { url: null, color: 'darkgreen'});
//                    var pos = map.default3DMapRef.project(latlng.lat, latlng.lng, 1);
//                    var coord = { lat: latlng.lat, lon: latlng.lng };
//
//                    dynamicCor[latLngKey] = coord;
//                    dynamicPos[latLngKey] = pos;
//                                    
//                    marker3D.on('pointerup', function(){ 
//                        this.color = 'darkorange';
//                        marker.fire('click');
//                        for (i=1;i<=dynamicPos.length;i++){
//                            if (document.getElementById('label'+ i)!= null){
//                                dynamicPos[i]= osmb.project(dynamicCor[i].lat, dynamicCor[i].lon, 1);
//                                document.getElementById('label'+ i).style.left = Math.round(dynamicPos[i].x) + 'px';
//                                document.getElementById('label'+ i).style.top = Math.round(dynamicPos[i].y) +60 + 'px';
//                            }
//                        }
//                    });
//                }
//                markerIndex++;

                latLngKey = latLngKey.replace(".", "");
                latLngKey = latLngKey.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona
                markersCache["" + latLngKey + ""] = marker;

                marker.on('mouseover', function (event) {
                    if (!is3DViewOn) {
                        var hoverImg = '../img/gisMapIcons/over/' + feature.properties.serviceType + '_over.png';
                        var hoverIcon = L.icon({
                            iconUrl: hoverImg
                        });
                        event.target.setIcon(hoverIcon);
                    } else {
                        editCSSFor3DWidgets();
                    }
                    
                });

                marker.on('mouseout', function (event) {
                    if (!is3DViewOn) {
                        var outImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                        var outIcon = L.icon({
                            iconUrl: outImg
                        });
                        event.target.setIcon(outIcon);
                    } else {
                        editCSSFor3DWidgets();
                    }
                    
                });

                marker.on('click', function (event) {

                    // opacizza
                    hideMarkers();

                    let targetMarker = event.target;

                    var hoverImg = '../img/gisMapIcons/over/' + feature.properties.serviceType + '_over.png';
                    var hoverIcon = L.icon({
                        iconUrl: hoverImg
                    });
                    event.target.setIcon(hoverIcon);

                    map.defaultMapRef.off('moveend');

                    event.target.unbindPopup();
                    newpopup = null;
                    var popupText, realTimeData, measuredTime, rtDataAgeSec, targetWidgets, color1, color2 = null;
                    var urlToCall, fake, fakeId = null;

                    if (feature.properties.fake === 'true') {
                        urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + feature.id;
                        fake = true;
                        fakeId = feature.id;
                    } else {
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
                        success: function (geoJsonServiceData) {
                            var fatherNode = null;
                            if (geoJsonServiceData.hasOwnProperty("BusStop")) {
                                fatherNode = geoJsonServiceData.BusStop;
                            } else {
                                if (geoJsonServiceData.hasOwnProperty("Sensor")) {
                                    fatherNode = geoJsonServiceData.Sensor;
                                } else {
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

                            if (serviceProperties.hasOwnProperty('website')) {
                                if ((serviceProperties.website !== '') && (serviceProperties.website !== undefined) && (serviceProperties.website !== 'undefined') && (serviceProperties.website !== null) && (serviceProperties.website !== 'null')) {
                                    if (serviceProperties.website.includes('http') || serviceProperties.website.includes('https')) {
                                        popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                    } else {
                                        popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                    }
                                } else {
                                    popupText += '<tr><td>Website</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>Website</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('email')) {
                                if ((serviceProperties.email !== '') && (serviceProperties.email !== undefined) && (serviceProperties.email !== 'undefined') && (serviceProperties.email !== null) && (serviceProperties.email !== 'null')) {
                                    popupText += '<tr><td>E-Mail</td><td>' + serviceProperties.email + '<td></tr>';
                                } else {
                                    popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('address')) {
                                if ((serviceProperties.address !== '') && (serviceProperties.address !== undefined) && (serviceProperties.address !== 'undefined') && (serviceProperties.address !== null) && (serviceProperties.address !== 'null')) {
                                    popupText += '<tr><td>Address</td><td>' + serviceProperties.address + '</td></tr>';
                                } else {
                                    popupText += '<tr><td>Address</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>Address</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('civic')) {
                                if ((serviceProperties.civic !== '') && (serviceProperties.civic !== undefined) && (serviceProperties.civic !== 'undefined') && (serviceProperties.civic !== null) && (serviceProperties.civic !== 'null')) {
                                    popupText += '<tr><td>Civic n.</td><td>' + serviceProperties.civic + '</td></tr>';
                                } else {
                                    popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('cap')) {
                                if ((serviceProperties.cap !== '') && (serviceProperties.cap !== undefined) && (serviceProperties.cap !== 'undefined') && (serviceProperties.cap !== null) && (serviceProperties.cap !== 'null')) {
                                    popupText += '<tr><td>C.A.P.</td><td>' + serviceProperties.cap + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('city')) {
                                if ((serviceProperties.city !== '') && (serviceProperties.city !== undefined) && (serviceProperties.city !== 'undefined') && (serviceProperties.city !== null) && (serviceProperties.city !== 'null')) {
                                    popupText += '<tr><td>City</td><td>' + serviceProperties.city + '</td></tr>';
                                } else {
                                    popupText += '<tr><td>City</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>City</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('province')) {
                                if ((serviceProperties.province !== '') && (serviceProperties.province !== undefined) && (serviceProperties.province !== 'undefined') && (serviceProperties.province !== null) && (serviceProperties.province !== 'null')) {
                                    popupText += '<tr><td>Province</td><td>' + serviceProperties.province + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('phone')) {
                                if ((serviceProperties.phone !== '') && (serviceProperties.phone !== undefined) && (serviceProperties.phone !== 'undefined') && (serviceProperties.phone !== null) && (serviceProperties.phone !== 'null')) {
                                    popupText += '<tr><td>Phone</td><td>' + serviceProperties.phone + '</td></tr>';
                                } else {
                                    popupText += '<tr><td>Phone</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>Phone</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('fax')) {
                                if ((serviceProperties.fax !== '') && (serviceProperties.fax !== undefined) && (serviceProperties.fax !== 'undefined') && (serviceProperties.fax !== null) && (serviceProperties.fax !== 'null')) {
                                    popupText += '<tr><td>Fax</td><td>' + serviceProperties.fax + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('note')) {
                                if ((serviceProperties.note !== '') && (serviceProperties.note !== undefined) && (serviceProperties.note !== 'undefined') && (serviceProperties.note !== null) && (serviceProperties.note !== 'null')) {
                                    popupText += '<tr><td>Notes</td><td>' + serviceProperties.note + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('agency')) {
                                if ((serviceProperties.agency !== '') && (serviceProperties.agency !== undefined) && (serviceProperties.agency !== 'undefined') && (serviceProperties.agency !== null) && (serviceProperties.agency !== 'null')) {
                                    popupText += '<tr><td>Agency</td><td>' + serviceProperties.agency + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('code')) {
                                if ((serviceProperties.code !== '') && (serviceProperties.code !== undefined) && (serviceProperties.code !== 'undefined') && (serviceProperties.code !== null) && (serviceProperties.code !== 'null')) {
                                    popupText += '<tr><td>Code</td><td>' + serviceProperties.code + '</td></tr>';
                                }
                            }

                            popupText += '</tbody>';
                            popupText += '</table>';

                            if (geoJsonServiceData.hasOwnProperty('busLines')) {
                                if (geoJsonServiceData.busLines.results.bindings.length > 0) {
                                    popupText += '<b>Lines: </b>';
                                    for (var i = 0; i < geoJsonServiceData.busLines.results.bindings.length; i++) {
                                        popupText += '<span style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + geoJsonServiceData.busLines.results.bindings[i].busLine.value + '</span> ';
                                    }
                                }
                            }

                            popupText += '</div>';

                            popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';

                            if (serviceProperties.hasOwnProperty('description')) {
                                if ((serviceProperties.description !== '') && (serviceProperties.description !== undefined) && (serviceProperties.description !== 'undefined') && (serviceProperties.description !== null) && (serviceProperties.description !== 'null')) {
                                    popupText += serviceProperties.description + "<br>";
                                } else {
                                    popupText += "No description available";
                                }
                            } else {
                                popupText += 'No description available';
                            }

                            popupText += '</div>';

                            popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';

                            var hasRealTime = false;

                            if (geoJsonServiceData.hasOwnProperty("realtime")) {
                                if (!jQuery.isEmptyObject(geoJsonServiceData.realtime)) {
                                    realTimeData = geoJsonServiceData.realtime;
                                    popupText += '<div class="popupLastUpdateContainer centerWithFlex"><b>Last update:&nbsp;</b><span class="popupLastUpdate" data-id="' + latLngId + '"></span></div>';

                                    if ((serviceClass.includes("Emergency")) && (serviceSubclass.includes("First aid"))) {
                                        //Tabella ad hoc per First Aid
                                        popupText += '<table id="' + latLngId + '" class="psPopupTable">';
                                        var series = {
                                            "firstAxis": {
                                                "desc": "Priority",
                                                "labels": [
                                                    "Red code",
                                                    "Yellow code",
                                                    "Green code",
                                                    "Blue code",
                                                    "White code"
                                                ]
                                            },
                                            "secondAxis": {
                                                "desc": "Status",
                                                "labels": [],
                                                "series": []
                                            }
                                        };

                                        var dataSlot = null;

                                        measuredTime = realTimeData.results.bindings[0].measuredTime.value.replace("T", " ").replace("Z", "");

                                        for (var i = 0; i < realTimeData.results.bindings.length; i++) {
                                            if (realTimeData.results.bindings[i].state.value.indexOf("estinazione") > 0) {
                                                series.secondAxis.labels.push("Addressed");
                                            }

                                            if (realTimeData.results.bindings[i].state.value.indexOf("ttesa") > 0) {
                                                series.secondAxis.labels.push("Waiting");
                                            }

                                            if (realTimeData.results.bindings[i].state.value.indexOf("isita") > 0) {
                                                series.secondAxis.labels.push("In visit");
                                            }

                                            if (realTimeData.results.bindings[i].state.value.indexOf("emporanea") > 0) {
                                                series.secondAxis.labels.push("Observation");
                                            }

                                            if (realTimeData.results.bindings[i].state.value.indexOf("tali") > 0) {
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

                                        for (var i = 0; i < rowsQt; i++) {
                                            var newRow = $("<tr></tr>");
                                            var z = parseInt(parseInt(i) - 1);

                                            if (i === 0) {
                                                //Riga di intestazione
                                                for (var j = 0; j < colsQt; j++) {
                                                    if (j === 0) {
                                                        //Cella (0,0)
                                                        var newCell = $("<td></td>");

                                                        newCell.css("background-color", "transparent");
                                                    } else {
                                                        //Celle labels
                                                        var k = parseInt(parseInt(j) - 1);
                                                        var colLabelBckColor = null;
                                                        switch (k) {
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
                                            } else {
                                                //Righe dati
                                                for (var j = 0; j < colsQt; j++) {
                                                    k = parseInt(parseInt(j) - 1);
                                                    if (j === 0) {
                                                        //Cella label
                                                        newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                                                        newCell.css("font-weight", "bold");
                                                    } else {
                                                        //Celle dati
                                                        newCell = $("<td>" + series.secondAxis.series[z][k] + "</td>");
                                                        if (i === (rowsQt - 1)) {
                                                            newCell.css('font-weight', 'bold');
                                                            switch (j) {
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
                                    } else {
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
                                        var dataDesc, dataVal, dataLastBtn, data4HBtn, dataDayBtn, data7DayBtn,
                                                data30DayBtn = null;
                                        for (var i = 0; i < realTimeData.head.vars.length; i++) {
                                            if (realTimeData.results.bindings[0][realTimeData.head.vars[i]] !== null && realTimeData.results.bindings[0][realTimeData.head.vars[i]] !== undefined) {
                                                if ((realTimeData.results.bindings[0][realTimeData.head.vars[i]]) && (realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.trim() !== '') && (realTimeData.head.vars[i] !== null) && (realTimeData.head.vars[i] !== 'undefined')) {
                                                    if ((realTimeData.head.vars[i] !== 'updating') && (realTimeData.head.vars[i] !== 'measuredTime') && (realTimeData.head.vars[i] !== 'instantTime')) {
                                                        if (!realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.includes('Not Available')) {
                                                            //realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                                            /*   dataDesc = realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function (str) {
                                                             return str.toUpperCase();
                                                             });*/
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
                            
                            // CORTI - disable autopan in 3D
                            let autoPan = true;
                            let autoClose = false;
                            if(is3DViewOn){
                                autoPan = false;
                                autoClose = true;
                            }

                            newpopup = L.popup({
                                closeOnClick: false, //Non lo levare, sennò autoclose:false non funziona
                                autoClose: autoClose,
                                autoPan: autoPan,
                                offset: [15, 0],
                                minWidth: 435,
                                maxWidth: 435
                            }).setContent(popupText);

                            event.target.bindPopup(newpopup).openPopup();

                            if (hasRealTime) {
                                $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').show();
                                $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                                $('#<?= $_REQUEST['name_w'] ?>_map span.popupLastUpdate[data-id="' + latLngId + '"]').html(measuredTime);
                            } else {
                                $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').hide();
                            }

                            $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').click(function () {
                                $('#' + widgetName + '_map div.recreativeEventMapDataContainer').hide();
                                $('#' + widgetName + '_map div.recreativeEventMapDetailsContainer').show();
                                $('#' + widgetName + '_map button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                $(this).addClass('recreativeEventMapBtnActive');
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').click(function () {
                                $('#' + widgetName + '_map div.recreativeEventMapDataContainer').hide();
                                $('#' + widgetName + '_map div.recreativeEventMapDescContainer').show();
                                $('#' + widgetName + '_map button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                $(this).addClass('recreativeEventMapBtnActive');
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').click(function () {
                                $('#' + widgetName + '_map div.recreativeEventMapDataContainer').hide();
                                $('#' + widgetName + '_map div.recreativeEventMapContactsContainer').show();
                                $('#' + widgetName + '_map button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                $(this).addClass('recreativeEventMapBtnActive');
                            });

                            if (hasRealTime) {
                                $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                            }

                            $('#<?= $_REQUEST['name_w'] ?>_map table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("background", color2);
                            $('#<?= $_REQUEST['name_w'] ?>_map table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("border", "none");
                            $('#<?= $_REQUEST['name_w'] ?>_map table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("color", "black");

                            $('#<?= $_REQUEST['name_w'] ?>_map table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').focus(function () {
                                $(this).css("outline", "0");
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_map input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_map input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').click(function () {
                                if ($(this).attr("data-keepData") === "false") {
                                    $(this).attr("data-keepData", "true");
                                } else {
                                    $(this).attr("data-keepData", "false");
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').off('mouseenter');
                            $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').off('mouseleave');
                            $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn[data-id="' + latLngId + '"]').hover(function () {
                                if ($(this).attr("data-lastDataClicked") === "false") {
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

                                for (var i = 0; i < widgetTargetList.length; i++) {
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
                                    function () {
                                        if ($(this).attr("data-lastDataClicked") === "false") {
                                            $(this).css("background", color2);
                                            $(this).css("font-weight", "normal");
                                        }
                                        var widgetTargetList = $(this).attr("data-targetWidgets").split(',');

                                        for (var i = 0; i < widgetTargetList.length; i++) {
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
                            if (rtDataAgeSec > 14400) {
                                $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "true");
                                //Disabilitiamo i 24Hours se last update più vecchio di 24 ore
                                if (rtDataAgeSec > 86400) {
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "true");
                                    //Disabilitiamo i 7 days se last update più vecchio di 7 days
                                    if (rtDataAgeSec > 604800) {
                                        $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "true");
                                        //Disabilitiamo i 30 days se last update più vecchio di 30 days
                                        if (rtDataAgeSec > 18144000) {
                                            $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "true");
                                        } else {
                                            $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                                        }
                                    } else {
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                    }
                                } else {
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                                }
                            } else {
                                $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "false");
                                $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                                $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                            }

                            $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').off('mouseenter');
                            $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').off('mouseleave');
                            $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"]').hover(function () {
                                if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                    $(this).css("background-color", "#e6e6e6");
                                    $(this).off("hover");
                                    $(this).off("click");
                                } else {
                                    if ($(this).attr("data-timeTrendClicked") === "false") {
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

                                    for (var i = 0; i < widgetTargetList.length; i++) {
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
                                    function () {
                                        if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                            $(this).css("background-color", "#e6e6e6");
                                            $(this).off("hover");
                                            $(this).off("click");
                                        } else {
                                            if ($(this).attr("data-timeTrendClicked") === "false") {
                                                $(this).css("background", color2);
                                                $(this).css("font-weight", "normal");
                                            }

                                            var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                            for (var i = 0; i < widgetTargetList.length; i++) {
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

                            $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn[data-id=' + latLngId + ']').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn[data-id=' + latLngId + ']').click(function (event) {
                                $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').each(function (i) {
                                    $(this).css("background", $(this).attr("data-color2"));
                                });
                                $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').css("font-weight", "normal");
                                $(this).css("background", $(this).attr("data-color1"));
                                $(this).css("font-weight", "bold");
                                $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').attr("data-lastDataClicked", "false");
                                $(this).attr("data-lastDataClicked", "true");
                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                var colIndex = $(this).parent().index();
                                var title = $(this).parents("tr").find("td").eq(0).html();

                                for (var i = 0; i < widgetTargetList.length; i++) {
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
                                        mapRef: map.defaultMapRef,
                                        fake: $(this).attr("data-fake"),
                                        fakeId: $(this).attr("data-fakeId")
                                    });
                                }

                                $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"]').each(function (i) {
                                    if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                        $(this).css("background-color", "#e6e6e6");
                                        $(this).off("hover");
                                        $(this).off("click");
                                    }
                                });

                            });

                            $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').click(function (event) {
                                if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                    $(this).css("background-color", "#e6e6e6");
                                    $(this).off("hover");
                                    $(this).off("click");
                                } else {
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').css("background", $(this).attr("data-color2"));
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').css("font-weight", "normal");
                                    $(this).css("background", $(this).attr("data-color1"));
                                    $(this).css("font-weight", "bold");
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                    $(this).attr("data-timeTrendClicked", "true");
                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                    var colIndex = $(this).parent().index();
                                    var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");
                                    var lastUpdateTime = $(this).parents('div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();

                                    var now = new Date();
                                    var lastUpdateDate = new Date(lastUpdateTime);
                                    var diff = parseFloat(Math.abs(now - lastUpdateDate) / 1000);
                                    var range = $(this).attr("data-range");

                                    for (var i = 0; i < widgetTargetList.length; i++) {
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
                                            mapRef: map.defaultMapRef,
                                            fake: false
                                                    //fake: $(this).attr("data-fake")
                                        });
                                    }

                                    $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"]').each(function (i) {
                                        if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                            $(this).css("background-color", "#e6e6e6");
                                            $(this).off("hover");
                                            $(this).off("click");
                                        }
                                    });
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"]').each(function (i) {
                                if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                    $(this).css("background-color", "#e6e6e6");
                                    $(this).off("hover");
                                    $(this).off("click");
                                }
                            });

                            map.defaultMapRef.off('popupclose');
                            map.defaultMapRef.on('popupclose', function (closeEvt) {
                                // CORTI
                                // icona marker
                                var outImg = '../img/gisMapIcons/' + feature.properties.serviceType + '.png';
                                var outIcon = L.icon({
                                    iconUrl: outImg
                                });
                                targetMarker.setIcon(outIcon);

                                hideMarkers();
                                editCSSFor3DWidgets();

                                var popupContent = $('<div></div>');
                                popupContent.html(closeEvt.popup._content);

                                if (popupContent.find("button.lastValueBtn").length > 0) {
                                    var widgetTargetList = popupContent.find("button.lastValueBtn").eq(0).attr("data-targetWidgets").split(',');

                                    if (($('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn[data-lastDataClicked=true]').length > 0) && ($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false")) {
                                        for (var i = 0; i < widgetTargetList.length; i++) {
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

                                    if (($('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-timeTrendClicked=true]').length > 0) && ($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false")) {
                                        for (var i = 0; i < widgetTargetList.length; i++) {
                                            $.event.trigger({
                                                type: "restoreOriginalTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                                eventGenerator: $(this),
                                                targetWidget: widgetTargetList[i]
                                            });
                                        }
                                    }
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_map div.leaflet-popup').off('click');
                            /*  $('#<?= $_REQUEST['name_w'] ?>_map div.leaflet-popup').on('click', function () {
                             var compLatLngId = $(this).find('input[type=hidden]').val();
                             
                             $('#<?= $_REQUEST['name_w'] ?>_map div.leaflet-popup').css("z-index", "-1");
                             $(this).css("z-index", "999999");
                             
                             $('#<?= $_REQUEST['name_w'] ?>_map input.gisPopupKeepDataCheck').off('click');
                             $('#<?= $_REQUEST['name_w'] ?>_map input.gisPopupKeepDataCheck[data-id="' + compLatLngId + '"]').click(function () {
                             if ($(this).attr("data-keepData") === "false") {
                             $(this).attr("data-keepData", "true");
                             }
                             else {
                             $(this).attr("data-keepData", "false");
                             }
                             });
                             
                             $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').off('mouseenter');
                             $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').off('mouseleave');
                             $(this).find('button.lastValueBtn[data-id="' + compLatLngId + '"]').hover(function () {
                             if ($(this).attr("data-lastDataClicked") === "false") {
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
                             
                             for (var i = 0; i < widgetTargetList.length; i++) {
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
                             function () {
                             if ($(this).attr("data-lastDataClicked") === "false") {
                             $(this).css("background", $(this).attr('data-color2'));
                             $(this).css("font-weight", "normal");
                             }
                             var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                             
                             for (var i = 0; i < widgetTargetList.length; i++) {
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
                             
                             $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').off('mouseenter');
                             $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').off('mouseleave');
                             $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + compLatLngId + '"]').hover(function () {
                             if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                             $(this).css("background-color", "#e6e6e6");
                             $(this).off("hover");
                             $(this).off("click");
                             }
                             else {
                             if ($(this).attr("data-timeTrendClicked") === "false") {
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
                             
                             for (var i = 0; i < widgetTargetList.length; i++) {
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
                             function () {
                             if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                             $(this).css("background-color", "#e6e6e6");
                             $(this).off("hover");
                             $(this).off("click");
                             }
                             else {
                             if ($(this).attr("data-timeTrendClicked") === "false") {
                             $(this).css("background", $(this).attr('data-color2'));
                             $(this).css("font-weight", "normal");
                             }
                             
                             var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                             for (var i = 0; i < widgetTargetList.length; i++) {
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
                             
                             $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').off('click');
                             $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').click(function (event) {
                             $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').each(function (i) {
                             $(this).css("background", $(this).attr("data-color2"));
                             });
                             $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').css("font-weight", "normal");
                             $(this).css("background", $(this).attr("data-color1"));
                             $(this).css("font-weight", "bold");
                             $('#<?= $_REQUEST['name_w'] ?>_map button.lastValueBtn').attr("data-lastDataClicked", "false");
                             $(this).attr("data-lastDataClicked", "true");
                             var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                             var colIndex = $(this).parent().index();
                             var title = $(this).parents("tr").find("td").eq(0).html();
                             
                             for (var i = 0; i < widgetTargetList.length; i++) {
                             $.event.trigger({
                             type: "showLastDataFromExternalContentGis_" + widgetTargetList[i],
                             eventGenerator: $(this),
                             targetWidget: widgetTargetList[i],
                             value: $(this).attr("data-lastValue"),
                             color1: $(this).attr("data-color1"),
                             color2: $(this).attr("data-color2"),
                             widgetTitle: title,
                             marker: markersCache["" + $(this).attr("data-id") + ""],
                             mapRef: map.defaultMapRef,
                             field: $(this).attr("data-field"),
                             serviceUri: $(this).attr("data-serviceUri"),
                             fake: $(this).attr("data-fake"),
                             fakeId: $(this).attr("data-fakeId")
                             });
                             }
                             });
                             
                             $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').off('click');
                             $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').click(function (event) {
                             if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                             $(this).css("background-color", "#e6e6e6");
                             $(this).off("hover");
                             $(this).off("click");
                             }
                             else {
                             //    $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').each(function (i) {
                             //        $(this).css("background", $(this).attr("data-color2"));
                             //    });
                             $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').css("font-weight", "normal");
                             $(this).css("background", $(this).attr("data-color1"));
                             $(this).css("font-weight", "bold");
                             $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                             $(this).attr("data-timeTrendClicked", "true");
                             var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                             var colIndex = $(this).parent().index();
                             var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");
                             var lastUpdateTime = $(this).parents('div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();
                             
                             var now = new Date();
                             var lastUpdateDate = new Date(lastUpdateTime);
                             var diff = parseFloat(Math.abs(now - lastUpdateDate) / 1000);
                             var range = $(this).attr("data-range");
                             
                             for (var i = 0; i < widgetTargetList.length; i++) {
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
                             mapRef: map.defaultMapRef,
                             fake: $(this).attr("data-fake"),
                             fakeId: $(this).attr("data-fakeId")
                             });
                             }
                             }
                             });
                             
                             $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"]').each(function (i) {
                             if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                             $(this).css("background-color", "#e6e6e6");
                             $(this).off("hover");
                             $(this).off("click");
                             }
                             });
                             }); */
                        },
                        error: function (errorData) {
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
                                maxWidth: 600
                            }).openPopup();
                        },
                        complete: function () {
                            // CORTI - mantiene 3D markers
                            if (is3DViewOn) {
                                editCSSFor3DWidgets();
                            }
                        }
                    });

                    // CORTI - mantiene 3D markers
                    if (is3DViewOn) {
                        editCSSFor3DWidgets();
                    }
                    
                });

                return marker;
            }

            function gisPrepareCustomMarkerFullScreen(feature, latlng) {
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

                marker.on('mouseover', function (event) {
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

                marker.on('click', function (event) {
                    fullscreendefaultMapRef.off('moveend');

                    event.target.unbindPopup();
                    newpopup = null;
                    var popupText, realTimeData, measuredTime, rtDataAgeSec, targetWidgets, color1, color2 = null;
                    var urlToCall, fake, fakeId = null;

                    if (feature.properties.fake === 'true') {
                        urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + feature.id;
                        fake = true;
                        fakeId = feature.id;
                    } else {
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
                        success: function (geoJsonServiceData) {
                            var fatherNode = null;
                            if (geoJsonServiceData.hasOwnProperty("BusStop")) {
                                fatherNode = geoJsonServiceData.BusStop;
                            } else {
                                if (geoJsonServiceData.hasOwnProperty("Sensor")) {
                                    fatherNode = geoJsonServiceData.Sensor;
                                } else {
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

                            if (serviceProperties.hasOwnProperty('website')) {
                                if ((serviceProperties.website !== '') && (serviceProperties.website !== undefined) && (serviceProperties.website !== 'undefined') && (serviceProperties.website !== null) && (serviceProperties.website !== 'null')) {
                                    if (serviceProperties.website.includes('http') || serviceProperties.website.includes('https')) {
                                        popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                    } else {
                                        popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                    }
                                } else {
                                    popupText += '<tr><td>Website</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>Website</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('email')) {
                                if ((serviceProperties.email !== '') && (serviceProperties.email !== undefined) && (serviceProperties.email !== 'undefined') && (serviceProperties.email !== null) && (serviceProperties.email !== 'null')) {
                                    popupText += '<tr><td>E-Mail</td><td>' + serviceProperties.email + '<td></tr>';
                                } else {
                                    popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('address')) {
                                if ((serviceProperties.address !== '') && (serviceProperties.address !== undefined) && (serviceProperties.address !== 'undefined') && (serviceProperties.address !== null) && (serviceProperties.address !== 'null')) {
                                    popupText += '<tr><td>Address</td><td>' + serviceProperties.address + '</td></tr>';
                                } else {
                                    popupText += '<tr><td>Address</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>Address</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('civic')) {
                                if ((serviceProperties.civic !== '') && (serviceProperties.civic !== undefined) && (serviceProperties.civic !== 'undefined') && (serviceProperties.civic !== null) && (serviceProperties.civic !== 'null')) {
                                    popupText += '<tr><td>Civic n.</td><td>' + serviceProperties.civic + '</td></tr>';
                                } else {
                                    popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('cap')) {
                                if ((serviceProperties.cap !== '') && (serviceProperties.cap !== undefined) && (serviceProperties.cap !== 'undefined') && (serviceProperties.cap !== null) && (serviceProperties.cap !== 'null')) {
                                    popupText += '<tr><td>C.A.P.</td><td>' + serviceProperties.cap + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('city')) {
                                if ((serviceProperties.city !== '') && (serviceProperties.city !== undefined) && (serviceProperties.city !== 'undefined') && (serviceProperties.city !== null) && (serviceProperties.city !== 'null')) {
                                    popupText += '<tr><td>City</td><td>' + serviceProperties.city + '</td></tr>';
                                } else {
                                    popupText += '<tr><td>City</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>City</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('province')) {
                                if ((serviceProperties.province !== '') && (serviceProperties.province !== undefined) && (serviceProperties.province !== 'undefined') && (serviceProperties.province !== null) && (serviceProperties.province !== 'null')) {
                                    popupText += '<tr><td>Province</td><td>' + serviceProperties.province + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('phone')) {
                                if ((serviceProperties.phone !== '') && (serviceProperties.phone !== undefined) && (serviceProperties.phone !== 'undefined') && (serviceProperties.phone !== null) && (serviceProperties.phone !== 'null')) {
                                    popupText += '<tr><td>Phone</td><td>' + serviceProperties.phone + '</td></tr>';
                                } else {
                                    popupText += '<tr><td>Phone</td><td>-</td></tr>';
                                }
                            } else {
                                popupText += '<tr><td>Phone</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('fax')) {
                                if ((serviceProperties.fax !== '') && (serviceProperties.fax !== undefined) && (serviceProperties.fax !== 'undefined') && (serviceProperties.fax !== null) && (serviceProperties.fax !== 'null')) {
                                    popupText += '<tr><td>Fax</td><td>' + serviceProperties.fax + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('note')) {
                                if ((serviceProperties.note !== '') && (serviceProperties.note !== undefined) && (serviceProperties.note !== 'undefined') && (serviceProperties.note !== null) && (serviceProperties.note !== 'null')) {
                                    popupText += '<tr><td>Notes</td><td>' + serviceProperties.note + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('agency')) {
                                if ((serviceProperties.agency !== '') && (serviceProperties.agency !== undefined) && (serviceProperties.agency !== 'undefined') && (serviceProperties.agency !== null) && (serviceProperties.agency !== 'null')) {
                                    popupText += '<tr><td>Agency</td><td>' + serviceProperties.agency + '</td></tr>';
                                }
                            }

                            if (serviceProperties.hasOwnProperty('code')) {
                                if ((serviceProperties.code !== '') && (serviceProperties.code !== undefined) && (serviceProperties.code !== 'undefined') && (serviceProperties.code !== null) && (serviceProperties.code !== 'null')) {
                                    popupText += '<tr><td>Code</td><td>' + serviceProperties.code + '</td></tr>';
                                }
                            }

                            popupText += '</tbody>';
                            popupText += '</table>';

                            if (geoJsonServiceData.hasOwnProperty('busLines')) {
                                if (geoJsonServiceData.busLines.results.bindings.length > 0) {
                                    popupText += '<b>Lines: </b>';
                                    for (var i = 0; i < geoJsonServiceData.busLines.results.bindings.length; i++) {
                                        popupText += '<span style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + geoJsonServiceData.busLines.results.bindings[i].busLine.value + '</span> ';
                                    }
                                }
                            }

                            popupText += '</div>';

                            popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';

                            if (serviceProperties.hasOwnProperty('description')) {
                                if ((serviceProperties.description !== '') && (serviceProperties.description !== undefined) && (serviceProperties.description !== 'undefined') && (serviceProperties.description !== null) && (serviceProperties.description !== 'null')) {
                                    popupText += serviceProperties.description + "<br>";
                                } else {
                                    popupText += "No description available";
                                }
                            } else {
                                popupText += 'No description available';
                            }

                            popupText += '</div>';

                            popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';

                            var hasRealTime = false;

                            if (geoJsonServiceData.hasOwnProperty("realtime")) {
                                if (!jQuery.isEmptyObject(geoJsonServiceData.realtime)) {
                                    realTimeData = geoJsonServiceData.realtime;
                                    popupText += '<div class="popupLastUpdateContainer centerWithFlex"><b>Last update:&nbsp;</b><span class="popupLastUpdate" data-id="' + latLngId + '"></span></div>';

                                    if ((serviceClass.includes("Emergency")) && (serviceSubclass.includes("First aid"))) {
                                        //Tabella ad hoc per First Aid
                                        popupText += '<table id="' + latLngId + '" class="psPopupTable">';
                                        var series = {
                                            "firstAxis": {
                                                "desc": "Priority",
                                                "labels": [
                                                    "Red code",
                                                    "Yellow code",
                                                    "Green code",
                                                    "Blue code",
                                                    "White code"
                                                ]
                                            },
                                            "secondAxis": {
                                                "desc": "Status",
                                                "labels": [],
                                                "series": []
                                            }
                                        };

                                        var dataSlot = null;

                                        measuredTime = realTimeData.results.bindings[0].measuredTime.value.replace("T", " ").replace("Z", "");

                                        for (var i = 0; i < realTimeData.results.bindings.length; i++) {
                                            if (realTimeData.results.bindings[i].state.value.indexOf("estinazione") > 0) {
                                                series.secondAxis.labels.push("Addressed");
                                            }

                                            if (realTimeData.results.bindings[i].state.value.indexOf("ttesa") > 0) {
                                                series.secondAxis.labels.push("Waiting");
                                            }

                                            if (realTimeData.results.bindings[i].state.value.indexOf("isita") > 0) {
                                                series.secondAxis.labels.push("In visit");
                                            }

                                            if (realTimeData.results.bindings[i].state.value.indexOf("emporanea") > 0) {
                                                series.secondAxis.labels.push("Observation");
                                            }

                                            if (realTimeData.results.bindings[i].state.value.indexOf("tali") > 0) {
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

                                        for (var i = 0; i < rowsQt; i++) {
                                            var newRow = $("<tr></tr>");
                                            var z = parseInt(parseInt(i) - 1);

                                            if (i === 0) {
                                                //Riga di intestazione
                                                for (var j = 0; j < colsQt; j++) {
                                                    if (j === 0) {
                                                        //Cella (0,0)
                                                        var newCell = $("<td></td>");

                                                        newCell.css("background-color", "transparent");
                                                    } else {
                                                        //Celle labels
                                                        var k = parseInt(parseInt(j) - 1);
                                                        var colLabelBckColor = null;
                                                        switch (k) {
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
                                            } else {
                                                //Righe dati
                                                for (var j = 0; j < colsQt; j++) {
                                                    k = parseInt(parseInt(j) - 1);
                                                    if (j === 0) {
                                                        //Cella label
                                                        newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                                                        newCell.css("font-weight", "bold");
                                                    } else {
                                                        //Celle dati
                                                        newCell = $("<td>" + series.secondAxis.series[z][k] + "</td>");
                                                        if (i === (rowsQt - 1)) {
                                                            newCell.css('font-weight', 'bold');
                                                            switch (j) {
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
                                    } else {
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
                                        var dataDesc, dataVal, dataLastBtn, data4HBtn, dataDayBtn, data7DayBtn,
                                                data30DayBtn = null;
                                        for (var i = 0; i < realTimeData.head.vars.length; i++) {
                                            if (realTimeData.results.bindings[0][realTimeData.head.vars[i]] !== null && realTimeData.results.bindings[0][realTimeData.head.vars[i]] !== undefined) {
                                                if ((realTimeData.results.bindings[0][realTimeData.head.vars[i]]) && (realTimeData.head.vars[i] !== null) && (realTimeData.head.vars[i] !== 'undefined')) {
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
                            
                            // CORTI - disable autopan in 3D
                            let autoPan = true;
                            let autoClose = false;
                            if(is3DViewOn){
                                autoPan = false;
                                autoClose = true;
                            }

                            newpopup = L.popup({
                                closeOnClick: false, //Non lo levare, sennò autoclose:false non funziona
                                autoClose: autoClose,
                                autoPan: autoPan,
                                offset: [15, 0],
                                minWidth: 435,
                                maxWidth: 435
                            }).setContent(popupText);

                            event.target.bindPopup(newpopup).openPopup();

                            if (hasRealTime) {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').show();
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap span.popupLastUpdate[data-id="' + latLngId + '"]').html(measuredTime);
                            } else {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').hide();
                            }

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapDetailsBtn[data-id="' + latLngId + '"]').click(function () {
                                $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDataContainer').hide();
                                $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDetailsContainer').show();
                                $('#' + widgetName + '_modalLinkOpenBodyDefaultMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                $(this).addClass('recreativeEventMapBtnActive');
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapDescriptionBtn[data-id="' + latLngId + '"]').click(function () {
                                $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDataContainer').hide();
                                $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDescContainer').show();
                                $('#' + widgetName + '_modalLinkOpenBodyDefaultMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                $(this).addClass('recreativeEventMapBtnActive');
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').click(function () {
                                $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDataContainer').hide();
                                $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapContactsContainer').show();
                                $('#' + widgetName + '_modalLinkOpenBodyDefaultMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                $(this).addClass('recreativeEventMapBtnActive');
                            });

                            if (hasRealTime) {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                            }

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("background", color2);
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("border", "none");
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').css("color", "black");

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap table.gisPopupTable[id="' + latLngId + '"] button.btn-sm').focus(function () {
                                $(this).css("outline", "0");
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap input.gisPopupKeepDataCheck[data-id="' + latLngId + '"]').click(function () {
                                if ($(this).attr("data-keepData") === "false") {
                                    $(this).attr("data-keepData", "true");
                                } else {
                                    $(this).attr("data-keepData", "false");
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').off('mouseenter');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').off('mouseleave');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn[data-id="' + latLngId + '"]').hover(function () {
                                if ($(this).attr("data-lastDataClicked") === "false") {
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

                                for (var i = 0; i < widgetTargetList.length; i++) {
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
                                    function () {
                                        if ($(this).attr("data-lastDataClicked") === "false") {
                                            $(this).css("background", color2);
                                            $(this).css("font-weight", "normal");
                                        }
                                        var widgetTargetList = $(this).attr("data-targetWidgets").split(',');

                                        for (var i = 0; i < widgetTargetList.length; i++) {
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
                            if (rtDataAgeSec > 14400) {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "true");
                                //Disabilitiamo i 24Hours se last update più vecchio di 24 ore
                                if (rtDataAgeSec > 86400) {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "true");
                                    //Disabilitiamo i 7 days se last update più vecchio di 7 days
                                    if (rtDataAgeSec > 604800) {
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "true");
                                        //Disabilitiamo i 30 days se last update più vecchio di 30 days
                                        if (rtDataAgeSec > 18144000) {
                                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "true");
                                        } else {
                                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                                        }
                                    } else {
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                    }
                                } else {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                                }
                            } else {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="4/HOUR"]').attr("data-disabled", "false");
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                            }

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').off('mouseenter');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').off('mouseleave');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"]').hover(function () {
                                if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                    $(this).css("background-color", "#e6e6e6");
                                    $(this).off("hover");
                                    $(this).off("click");
                                } else {
                                    if ($(this).attr("data-timeTrendClicked") === "false") {
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

                                    for (var i = 0; i < widgetTargetList.length; i++) {
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
                                    function () {
                                        if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                            $(this).css("background-color", "#e6e6e6");
                                            $(this).off("hover");
                                            $(this).off("click");
                                        } else {
                                            if ($(this).attr("data-timeTrendClicked") === "false") {
                                                $(this).css("background", color2);
                                                $(this).css("font-weight", "normal");
                                            }

                                            var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                            for (var i = 0; i < widgetTargetList.length; i++) {
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

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn[data-id=' + latLngId + ']').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn[data-id=' + latLngId + ']').click(function (event) {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').each(function (i) {
                                    $(this).css("background", $(this).attr("data-color2"));
                                });
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').css("font-weight", "normal");
                                $(this).css("background", $(this).attr("data-color1"));
                                $(this).css("font-weight", "bold");
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').attr("data-lastDataClicked", "false");
                                $(this).attr("data-lastDataClicked", "true");
                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                var colIndex = $(this).parent().index();
                                var title = $(this).parents("tr").find("td").eq(0).html();

                                for (var i = 0; i < widgetTargetList.length; i++) {
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
                                        mapRef: fullscreendefaultMapRef,
                                        fake: $(this).attr("data-fake"),
                                        fakeId: $(this).attr("data-fakeId")
                                    });
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').click(function (event) {
                                if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                    $(this).css("background-color", "#e6e6e6");
                                    $(this).off("hover");
                                    $(this).off("click");
                                } else {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').css("background", $(this).attr("data-color2"));
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').css("font-weight", "normal");
                                    $(this).css("background", $(this).attr("data-color1"));
                                    $(this).css("font-weight", "bold");
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                    $(this).attr("data-timeTrendClicked", "true");
                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                    var colIndex = $(this).parent().index();
                                    var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");
                                    var lastUpdateTime = $(this).parents('div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();

                                    var now = new Date();
                                    var lastUpdateDate = new Date(lastUpdateTime);
                                    var diff = parseFloat(Math.abs(now - lastUpdateDate) / 1000);
                                    var range = $(this).attr("data-range");

                                    for (var i = 0; i < widgetTargetList.length; i++) {
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
                                            mapRef: fullscreendefaultMapRef,
                                            fake: false
                                                    //fake: $(this).attr("data-fake")
                                        });
                                    }
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"]').each(function (i) {
                                if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                    $(this).css("background-color", "#e6e6e6");
                                    $(this).off("hover");
                                    $(this).off("click");
                                }
                            });

                            fullscreendefaultMapRef.off('popupclose');
                            fullscreendefaultMapRef.on('popupclose', function (closeEvt) {
                                var popupContent = $('<div></div>');
                                popupContent.html(closeEvt.popup._content);

                                if (popupContent.find("button.lastValueBtn").length > 0) {
                                    var widgetTargetList = popupContent.find("button.lastValueBtn").eq(0).attr("data-targetWidgets").split(',');

                                    if (($('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn[data-lastDataClicked=true]').length > 0) && ($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false")) {
                                        for (var i = 0; i < widgetTargetList.length; i++) {
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

                                    if (($('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-timeTrendClicked=true]').length > 0) && ($('input.gisPopupKeepDataCheck').attr('data-keepData') === "false")) {
                                        for (var i = 0; i < widgetTargetList.length; i++) {
                                            $.event.trigger({
                                                type: "restoreOriginalTimeTrendFromExternalContentGis_" + widgetTargetList[i],
                                                eventGenerator: $(this),
                                                targetWidget: widgetTargetList[i]
                                            });
                                        }
                                    }
                                }
                            });

                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap div.leaflet-popup').off('click');
                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap div.leaflet-popup').on('click', function () {
                                var compLatLngId = $(this).find('input[type=hidden]').val();

                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap div.leaflet-popup').css("z-index", "-1");
                                $(this).css("z-index", "999999");

                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap input.gisPopupKeepDataCheck').off('click');
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap input.gisPopupKeepDataCheck[data-id="' + compLatLngId + '"]').click(function () {
                                    if ($(this).attr("data-keepData") === "false") {
                                        $(this).attr("data-keepData", "true");
                                    } else {
                                        $(this).attr("data-keepData", "false");
                                    }
                                });

                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').off('mouseenter');
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').off('mouseleave');
                                $(this).find('button.lastValueBtn[data-id="' + compLatLngId + '"]').hover(function () {
                                    if ($(this).attr("data-lastDataClicked") === "false") {
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

                                    for (var i = 0; i < widgetTargetList.length; i++) {
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
                                        function () {
                                            if ($(this).attr("data-lastDataClicked") === "false") {
                                                $(this).css("background", $(this).attr('data-color2'));
                                                $(this).css("font-weight", "normal");
                                            }
                                            var widgetTargetList = $(this).attr("data-targetWidgets").split(',');

                                            for (var i = 0; i < widgetTargetList.length; i++) {
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

                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').off('mouseenter');
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').off('mouseleave');
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + compLatLngId + '"]').hover(function () {
                                    if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                        $(this).css("background-color", "#e6e6e6");
                                        $(this).off("hover");
                                        $(this).off("click");
                                    } else {
                                        if ($(this).attr("data-timeTrendClicked") === "false") {
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

                                        for (var i = 0; i < widgetTargetList.length; i++) {
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
                                        function () {
                                            if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                                $(this).css("background-color", "#e6e6e6");
                                                $(this).off("hover");
                                                $(this).off("click");
                                            } else {
                                                if ($(this).attr("data-timeTrendClicked") === "false") {
                                                    $(this).css("background", $(this).attr('data-color2'));
                                                    $(this).css("font-weight", "normal");
                                                }

                                                var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                                for (var i = 0; i < widgetTargetList.length; i++) {
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

                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').off('click');
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').click(function (event) {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').each(function (i) {
                                        $(this).css("background", $(this).attr("data-color2"));
                                    });
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').css("font-weight", "normal");
                                    $(this).css("background", $(this).attr("data-color1"));
                                    $(this).css("font-weight", "bold");
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.lastValueBtn').attr("data-lastDataClicked", "false");
                                    $(this).attr("data-lastDataClicked", "true");
                                    var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                    var colIndex = $(this).parent().index();
                                    var title = $(this).parents("tr").find("td").eq(0).html();

                                    for (var i = 0; i < widgetTargetList.length; i++) {
                                        $.event.trigger({
                                            type: "showLastDataFromExternalContentGis_" + widgetTargetList[i],
                                            eventGenerator: $(this),
                                            targetWidget: widgetTargetList[i],
                                            value: $(this).attr("data-lastValue"),
                                            color1: $(this).attr("data-color1"),
                                            color2: $(this).attr("data-color2"),
                                            widgetTitle: title,
                                            marker: markersCache["" + $(this).attr("data-id") + ""],
                                            mapRef: fullscreendefaultMapRef,
                                            field: $(this).attr("data-field"),
                                            serviceUri: $(this).attr("data-serviceUri"),
                                            fake: $(this).attr("data-fake"),
                                            fakeId: $(this).attr("data-fakeId")
                                        });
                                    }
                                });

                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').off('click');
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').click(function (event) {
                                    if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                        $(this).css("background-color", "#e6e6e6");
                                        $(this).off("hover");
                                        $(this).off("click");
                                    } else {
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').each(function (i) {
                                            $(this).css("background", $(this).attr("data-color2"));
                                        });
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').css("font-weight", "normal");
                                        $(this).css("background", $(this).attr("data-color1"));
                                        $(this).css("font-weight", "bold");
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                        $(this).attr("data-timeTrendClicked", "true");
                                        var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                        var colIndex = $(this).parent().index();
                                        var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");
                                        var lastUpdateTime = $(this).parents('div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();

                                        var now = new Date();
                                        var lastUpdateDate = new Date(lastUpdateTime);
                                        var diff = parseFloat(Math.abs(now - lastUpdateDate) / 1000);
                                        var range = $(this).attr("data-range");

                                        for (var i = 0; i < widgetTargetList.length; i++) {
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
                                                mapRef: fullscreendefaultMapRef,
                                                fake: $(this).attr("data-fake"),
                                                fakeId: $(this).attr("data-fakeId")
                                            });
                                        }
                                    }
                                });

                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"]').each(function (i) {
                                    if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                        $(this).css("background-color", "#e6e6e6");
                                        $(this).off("hover");
                                        $(this).off("click");
                                    }
                                });
                            });
                        },
                        error: function (errorData) {
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
                                maxWidth: 600
                            }).openPopup();
                        },
                        // CORTI
                        complete: function(){
                            editCSSFor3DWidgets();
                        }
                    });
                });

                return marker;
            }

            function createFullscreenModal() {
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
                        '<div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_modalLinkLoading" class="loadingDiv">' +
                        '<div class="LoadingTextDiv">' +
                        '<p>Loading data, please wait</p>' +
                        '</div>' +
                        '<div class="loadingIconDiv">' +
                        '<i class="fa fa-spinner fa-spin"></i>' +
                        '</div>' +
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
                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpenCloseBtn").click(function () {
                    /*   if ($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").is(":visible")) {
                     
                     fullscreendefaultMapRef.off();
                     fullscreendefaultMapRef.remove();
                     }   */

                    if ($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").is(":visible")) {
                        fullscreenMapRef.off();
                        fullscreenMapRef.remove();
                    }

                    if ($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").is(":visible")) {
                        for (var key in gisGeometryTankForFullscreen) {
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
                    var stopFlag = 1;
                });
            }

            //calcolo automatico del rettangolo di dimensioni minime per mostrare tutti e soli i pin col massimo grado di zoom possibile
            function resizeMapView(mapRef) {

                let minLat = +90;
                let minLng = +180;
                let maxLat = -90;
                let maxLng = -180;


                for (let i = 0; i < map.eventsOnMap.length; i++) {
                    if (map.eventsOnMap[i].eventType !== 'heatmap') {
                        if (map.eventsOnMap[i].eventType === 'evacuationPlan') {
                            if (map.eventsOnMap[i].polyGroup.minLng < minLng) {
                                minLng = map.eventsOnMap[i].polyGroup.minLng;
                            }

                            if (map.eventsOnMap[i].polyGroup.maxLng > maxLng) {
                                maxLng = map.eventsOnMap[i].polyGroup.maxLng;
                            }

                            if (map.eventsOnMap[i].polyGroup.minLat < minLat) {
                                minLat = map.eventsOnMap[i].polyGroup.minLat;
                            }

                            if (map.eventsOnMap[i].polyGroup.maxLat > maxLat) {
                                maxLat = map.eventsOnMap[i].polyGroup.maxLat;
                            }
                        }
                        if (map.eventsOnMap[i].eventType === 'trafficRealTimeDetails') {
                            if (map.eventsOnMap[i].minLng < minLng) {
                                minLng = map.eventsOnMap[i].minLng;
                            }

                            if (map.eventsOnMap[i].maxLng > maxLng) {
                                maxLng = map.eventsOnMap[i].maxLng;
                            }

                            if (map.eventsOnMap[i].minLat < minLat) {
                                minLat = map.eventsOnMap[i].minLat;
                            }

                            if (map.eventsOnMap[i].maxLat > maxLat) {
                                maxLat = map.eventsOnMap[i].maxLat;
                            }
                        } else {
                            if (map.eventsOnMap[i].lng < minLng) {
                                minLng = map.eventsOnMap[i].lng;
                            }

                            if (map.eventsOnMap[i].lng > maxLng) {
                                maxLng = map.eventsOnMap[i].lng;
                            }

                            if (map.eventsOnMap[i].lat < minLat) {
                                minLat = map.eventsOnMap[i].lat;
                            }

                            if (map.eventsOnMap[i].lat > maxLat) {
                                maxLat = map.eventsOnMap[i].lat;
                            }
                        }

                    }
                }

                if (map.eventsOnMap.length > 0) {
                    mapRef.fitBounds([
                        [minLat, minLng],
                        [maxLat, maxLng]
                    ]);
                } else {
                    var latInit = 43.769789;
                    var lngInit = 11.255694;
                    //    map.defaultMapRef.setView([43.769789, 11.255694], 11);
                    //   map.defaultMapRef.setView([43.769789, 11.255694], widgetParameters.zoom);
                    if (widgetParameters.latLng[0] != null && widgetParameters.latLng[0] != '') {
                        latInit = widgetParameters.latLng[0];
                    }
                    if (widgetParameters.latLng[1] != null && widgetParameters.latLng[1] != '') {
                        lngInit = widgetParameters.latLng[1];
                    }
                    map.defaultMapRef.setView([latInit, lngInit], widgetParameters.zoom);
                }
            }

            //Tipicamente questa funzione viene invocata dopo che sono stati scaricati i dati per il widget (se ne ha bisogno) e ci va dentro la logica che costruisce il contenuto del widget
            function populateWidget(center = [], load3d = true) {
                let lastPopup = null;

                showWidgetContent(widgetName);

                let mapDivLocal = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_map";
                map.mapName = widgetName;
                map.mapDivLocal = mapDivLocal;
                var latInit = 43.769789;
                var lngInit = 11.255694;

                if (widgetParameters.latLng[0] != null && widgetParameters.latLng[0] != '') {
                    latInit = widgetParameters.latLng[0];
                }
                if (widgetParameters.latLng[1] != null && widgetParameters.latLng[1] != '') {
                    lngInit = widgetParameters.latLng[1];
                }
//                map.defaultMapRef = L.map(mapDivLocal).setView([latInit, lngInit], widgetParameters.zoom);
                if (center.length > 0 && !load3d) { // false perchè avviene quando il 3d è già stato caricato
                    map.defaultMapRef = L.map(mapDivLocal, {zoomControl: false, zoomSnap: 0.05}).setView(center, 16);
                    $("#<?= $_REQUEST['name_w'] ?>_map").find('.leaflet-control-zoom-display').css('display', 'none');

                    // disabilita interazioni (view 3D attiva)
                    map.defaultMapRef.dragging.disable();
                    map.defaultMapRef.touchZoom.disable();
                    map.defaultMapRef.doubleClickZoom.disable();
                    map.defaultMapRef.scrollWheelZoom.disable();
                }else{
                    map.defaultMapRef = L.map(mapDivLocal).setView([latInit, lngInit], 16);
                    $("#<?= $_REQUEST['name_w'] ?>_map").find('.leaflet-control-zoom-display').css('display','block');
                }

                map.eventsOnMap = eventsOnMap;

                if (!is3DViewOn) {
                    map.defaultLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                        maxZoom: 18
                    }).addTo(map.defaultMapRef);
                }

                map.defaultMapRef.attributionControl.setPrefix('');

                var rgbToHex = function (rgb) {
                    var hex = Number(rgb).toString(16);
                    if (hex.length < 2) {
                        hex = "0" + hex;
                    }
                    return hex;
                };

                var fullColorHex = function (rgbArray) {
                    var red = rgbToHex((rgbArray.split(",")[0]).trim());
                    var green = rgbToHex((rgbArray.split(",")[1]).trim());
                    var blue = rgbToHex((rgbArray.split(",")[2]).trim());
                    return red + green + blue;
                };

                // Crea un array con tutti i giorni disponibili per la heatmap corrente dai metadati
                function initDaysArray(heatmapMetaData) {
                    var outArray = [];
                    var outMillisArray = [];
                    for (n = 0; n < heatmapData.length; n++) {
                        outArray[n] = dateFns.parse(heatmapData[n].metadata.date.replace(" ", "T"));
                        outMillisArray[n] = outArray[n].valueOf();
                    }
                    var dateNow = new Date(Date.now());
                    var result = dateFns.closestTo(dateNow, outArray);
                    var idx = outMillisArray.indexOf(result.valueOf());
                    while (dateFns.isAfter(result, dateNow)) {
                        if (idx < outArray.length) {
                            result = outArray[++idx];
                        }
                    }
                    if (idx > heatmapData.length - 1) {
                        current_page = heatmapData.length - 1;
                    } else {
                        current_page = idx;
                    }

                    var utcDate = getUTCDate(Date.now());
                    //    var gmtDate = getGMTDate(Date.now());
                    var clientTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

                    var datum = new Date(Date.now());
                    if (isValidDate(datum)) {
                        var convertedDate = datum.epochConverterGMTString();
                        var relativeDate = datum.relativeDate();
                        var clientLocaleTime = datum.toString();
                    }
                    return outArray;
                    //    return outMillisArray;
                }

                //Risponditore ad eventi innescati dagli widget pilota (aggiungi evento, togli evento)

                $(document).on('addAlarm', function (event) {
                    if (event.target === map.mapName) {
                        function addAlarmsToMap() {
                            let passedData = event.passedData;

                            for (let j = 0; j < passedData.length; j++) {

                                let lat = passedData[j].lat;
                                let lng = passedData[j].lng;
                                let eventType = passedData[j].eventType;
                                let eventName = passedData[j].eventName;
                                let eventStartDate = passedData[j].eventStartDate;
                                let eventStartTime = passedData[j].eventStartTime;
                                let eventSeverity = passedData[j].eventSeverity;
                                passedData[j].type = "alarmEvent";

                                //Creazione dell'icona custom per il pin
                                switch (eventSeverity) {
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

                                let pinIcon = new L.DivIcon({
                                    className: null,
                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                    iconAnchor: [18, 36]
                                });

                                let markerLocation = new L.LatLng(lat, lng);
                                let marker = new L.Marker(markerLocation, {icon: pinIcon});
                                passedData[j].marker = marker;

                                //Creazione del popup per il pin appena creato
                                let popupText = "<span class='mapPopupTitle'>" + eventName + "</span>" +
                                        "<span class='mapPopupLine'><i>Start date: </i>" + eventStartDate + " - " + eventStartTime + "</span>" +
                                        "<span class='mapPopupLine'><i>Event type: </i>" + alarmTypes[eventType].desc.toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'><i>Event severity: </i><span style='background-color: " + severityColor + "'>" + eventSeverity.toUpperCase() + "</span></span>";

                                map.defaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth: 600}).openPopup();

                                map.eventsOnMap.push(passedData[j]);

                            }
                        }

                        if (addMode === 'additive') {
                            addAlarmsToMap();
                        }

                        if (addMode === 'exclusive') {
                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if (map.eventsOnMap[i].type !== 'addAlarm') {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;
                                    break;
                                }
                            }
                            //Remove WidgetEvacuationPlans active pins
                            $.event.trigger({
                                type: "removeEvacuationPlanPin",
                            });
                            //Remove WidgetSelector active pins
                            $.event.trigger({
                                type: "removeSelectorEventPin",
                            });
                            //Remove WidgetEvents active pins
                            $.event.trigger({
                                type: "removeEventFIPin",
                            });
                            //Remove WidgetResources active pins
                            $.event.trigger({
                                type: "removeResourcePin",
                            });
                            //Remove WidgetOperatorEvents active pins
                            $.event.trigger({
                                type: "removeOperatorEventPin",
                            });
                            //Remove WidgetTrafficEvents active pins
                            $.event.trigger({
                                type: "removeTrafficEventPin",
                            });
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                maxZoom: 18
                            }).addTo(map.defaultMapRef);

                            addAlarmsToMap();
                        }

                        //console.log(map.eventsOnMap.length);

                        //   resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('addEvacuationPlan', function (event) {
                    if (event.target === map.mapName) {
                        function addEvacuationPlanToMap() {
                            let passedData = event.passedData;

                            for (let k = 0; k < passedData.length; k++) {

                                let plansObj = passedData[k].plansObj;
                                let planId = passedData[k].planId;
                                let evacuationColors = passedData[k].colors;

                                shownPolyGroup = L.featureGroup();
                                shownPolyGroup.eventType = passedData[k].eventType;


                                for (let j = 0; j < plansObj[planId].payload.evacuation_paths.length; j++) {
                                    path = [];

                                    for (let i = 0; i < plansObj[planId].payload.evacuation_paths[j].coords.length; i++) {
                                        let point = [];
                                        point[0] = plansObj[planId].payload.evacuation_paths[j].coords[i].latitude;
                                        point[1] = plansObj[planId].payload.evacuation_paths[j].coords[i].longitude;
                                        path.push(point);
//                                        console.log(path);
                                    }

                                    let polyline = L.polyline(path, {color: evacuationColors[j % 6]});
                                    shownPolyGroup.addLayer(polyline);
                                }
                                passedData[k].polyGroup = shownPolyGroup;
                                map.eventsOnMap.push(passedData[k]);
                            }
                            map.defaultMapRef.addLayer(shownPolyGroup);

                            shownPolyGroup.maxLat = shownPolyGroup.getBounds()._northEast.lat;
                            shownPolyGroup.minLat = shownPolyGroup.getBounds()._southWest.lat;
                            shownPolyGroup.maxLng = shownPolyGroup.getBounds()._northEast.lng;
                            shownPolyGroup.minLng = shownPolyGroup.getBounds()._southWest.lng;
                        }

                        if (addMode === 'additive') {
                            addEvacuationPlanToMap();
                        }

                        if (addMode === 'exclusive') {
                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if (map.eventsOnMap[i].eventType !== 'evacuationPlan') {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;
                                    break;
                                }
                            }
                            //Remove WidgetAlarm active pins
                            $.event.trigger({
                                type: "removeAlarmPin",
                            });
                            //Remove WidgetSelector active pins
                            $.event.trigger({
                                type: "removeSelectorEventPin",
                            });
                            //Remove WidgetEvents active pins
                            $.event.trigger({
                                type: "removeEventFIPin",
                            });
                            //Remove WidgetResources active pins
                            $.event.trigger({
                                type: "removeResourcePin",
                            });
                            //Remove WidgetOperatorEvents active pins
                            $.event.trigger({
                                type: "removeOperatorEventPin",
                            });
                            //Remove WidgetTrafficEvents active pins
                            $.event.trigger({
                                type: "removeTrafficEventPin",
                            });
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                maxZoom: 18
                            }).addTo(map.defaultMapRef);

                            addEvacuationPlanToMap();
                        }

                        //console.log(map.eventsOnMap.length);

                        // resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('addSelectorPin', function (event) {
                    if (event.target === map.mapName) {
                        if (lastPopup !== null) {
                            lastPopup.closePopup();
                        }

                        function addSelectorEventToMap() {
                            var passedData = event.passedData;

                            var mapBounds = map.defaultMapRef.getBounds();
                            var query = passedData.query;
                            var targets = passedData.targets;
                            var eventGenerator = passedData.eventGenerator;
                            var color1 = passedData.color1;
                            var color2 = passedData.color2;
                            var queryType = passedData.queryType;
                            var desc = passedData.desc;
                            var display = passedData.display;

                            var loadingDiv = $('<div class="gisMapLoadingDiv"></div>');

                            if ($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length > 0) {
                                loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').last());
                            } else {
                                loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_map'));
                            }

                            loadingDiv.css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - ($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length * loadingDiv.height())) + "px");
                            loadingDiv.css("left", ($('#<?= $_REQUEST['name_w'] ?>_div').width() - loadingDiv.width()) + "px");

                            var loadingText = $('<p class="gisMapLoadingDivTextPar">adding <b>' + desc.toLowerCase() + '</b> to map<br><i class="fa fa-circle-o-notch fa-spin" style="font-size: 30px"></i></p>');
                            var loadOkText = $('<p class="gisMapLoadingDivTextPar"><b>' + desc.toLowerCase() + '</b> added to map<br><i class="fa fa-check" style="font-size: 30px"></i></p>');
                            var loadKoText = $('<p class="gisMapLoadingDivTextPar">error adding <b>' + desc.toLowerCase() + '</b> to map<br><i class="fa fa-close" style="font-size: 30px"></i></p>');

                            loadingDiv.css("background", color1);
                            loadingDiv.css("background", "-webkit-linear-gradient(left top, " + color1 + ", " + color2 + ")");
                            loadingDiv.css("background", "-o-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                            loadingDiv.css("background", "-moz-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                            loadingDiv.css("background", "linear-gradient(to bottom right, " + color1 + ", " + color2 + ")");

                            // CORTI - evita loadingDiv in 3D View
                            if (!is3DViewOn) {
                                loadingDiv.show();

                                loadingDiv.append(loadingText);
                                loadingDiv.css("opacity", 1);
                            }

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

                            var pattern = new RegExp(re1 + re2 + re3 + re4 + re5 + re6 + re7 + re8 + re9, ["i"]);

                            /*   if (queryType === "Default") {
                             if (pattern.test(query)) {
                             query = query.replace(pattern, "selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng);
                             }
                             else {
                             query = query + "&selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng;
                             }
                             }
                             
                             if (targets !== "") {
                             targets = targets.split(",");
                             }
                             else {
                             targets = [];
                             }*/

                            if (queryType === "Default")
                            {
                                if (passedData.query.includes("datamanager/api/v1/poidata/")) {
                                    if (passedData.desc != "My POI") {
                                        myPOIId = passedData.query.split("datamanager/api/v1/poidata/")[1];
                                        apiUrl = "../controllers/myPOIProxy.php";
                                        dataForApi = myPOIId;
                                        query = passedData.query;
                                    } else {
                                        apiUrl = "../controllers/myPOIProxy.php";
                                        dataForApi = "All";
                                        query = passedData.query;
                                    }
                                } else if (passedData.query.includes("/iot/") && !passedData.query.includes("/api/v1/")) {
                                    query = "https://www.disit.org/superservicemap/api/v1/?serviceUri=" + passedData.query + "&format=json";
                                } else {

                                    if (pattern.test(passedData.query)) {
                                        //console.log("Service Map selection substitution");
                                        query = passedData.query.replace(pattern, "selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng);
                                    } else {
                                        //console.log("Service Map selection addition");
                                        query = passedData.query + "&selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng;
                                    }
                                }
                                if (!query.includes("&maxResults")) {
                                    if (!query.includes("&queryId")) {
                                        query = query + "&maxResults=0";
                                    }
                                }
                            } else if (queryType === "MyPOI") {
                                if (passedData.desc != "My POI") {
                                    myPOIId = passedData.query.split("datamanager/api/v1/poidata/")[1];
                                    apiUrl = "../controllers/myPOIProxy.php";
                                    dataForApi = myPOIId;
                                    query = passedData.query;
                                } else {
                                    apiUrl = "../controllers/myPOIProxy.php";
                                    dataForApi = "All";
                                    query = passedData.query;
                                }

                            } else
                            {
                                query = passedData.query;
                            }

                            if (passedData.targets !== "")
                            {
                                targets = passedData.targets.split(",");
                            } else
                            {
                                targets = [];
                            }

                            if (queryType != "MyPOI" && !passedData.query.includes("datamanager/api/v1/poidata/")) {
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
                                //    url: query + "&geometry=true&fullCount=false",
                                url: apiUrl,
                                type: "GET",
                                data: {
                                    myPOIId: dataForApi
                                },
                                async: true,
                                timeout: 0,
                                dataType: 'json',
                                success: function (geoJsonData) {
                                    var fatherGeoJsonNode = {};

                                    /*    if (queryType === "Default") {
                                     if (geoJsonData.hasOwnProperty("BusStops")) {
                                     fatherGeoJsonNode = geoJsonData.BusStops;
                                     }
                                     else {
                                     if (geoJsonData.hasOwnProperty("SensorSites")) {
                                     fatherGeoJsonNode = geoJsonData.SensorSites;
                                     }
                                     else {
                                     fatherGeoJsonNode = geoJsonData.Services;
                                     }
                                     }
                                     }
                                     else {
                                     if (geoJsonData.hasOwnProperty("BusStop")) {
                                     fatherGeoJsonNode = geoJsonData.BusStop;
                                     }
                                     else {
                                     if (geoJsonData.hasOwnProperty("Sensor")) {
                                     fatherGeoJsonNode = geoJsonData.Sensor;
                                     }
                                     else {
                                     if (geoJsonData.hasOwnProperty("Service")) {
                                     fatherGeoJsonNode = geoJsonData.Service;
                                     }
                                     else {
                                     fatherGeoJsonNode = geoJsonData.Services;
                                     }
                                     }
                                     }
                                     }*/

                                    if (queryType === "Default")
                                    {
                                        if (passedData.query.includes("datamanager/api/v1/poidata/")) {
                                            fatherGeoJsonNode.features = [];
                                            if (passedData.desc != "My POI") {
                                                fatherGeoJsonNode.features[0] = geoJsonData;
                                            } else {
                                                fatherGeoJsonNode.features = geoJsonData;
                                            }
                                            fatherGeoJsonNode.type = "FeatureCollection";
                                        } else {
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
                                             }*/
                                        }
                                    } else if (queryType === "MyPOI")
                                    {
                                        fatherGeoJsonNode.features = [];
                                        if (passedData.desc != "My POI") {
                                            fatherGeoJsonNode.features[0] = geoJsonData;
                                        } else {
                                            fatherGeoJsonNode.features = geoJsonData;
                                        }
                                        fatherGeoJsonNode.type = "FeatureCollection";
                                    } else
                                    {
                                        /*   var countObjKeys = 0;
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
                                        if (geoJsonData.hasOwnProperty("BusStop"))
                                        {
                                            fatherGeoJsonNode = geoJsonData.BusStop;
                                        } else
                                        {
                                            if (geoJsonData.hasOwnProperty("Sensor"))
                                            {
                                                fatherGeoJsonNode = geoJsonData.Sensor;
                                            } else
                                            {
                                                if (geoJsonData.hasOwnProperty("Service"))
                                                {
                                                    fatherGeoJsonNode = geoJsonData.Service;
                                                } else
                                                {
                                                    fatherGeoJsonNode = geoJsonData.Services;
                                                }
                                            }
                                        }
                                    }


                                    for (var i = 0; i < fatherGeoJsonNode.features.length; i++) {

                                        var dataObj = {};

                                        fatherGeoJsonNode.features[i].properties.targetWidgets = targets;
                                        fatherGeoJsonNode.features[i].properties.color1 = color1;
                                        fatherGeoJsonNode.features[i].properties.color2 = color2;

                                        dataObj.lat = fatherGeoJsonNode.features[i].geometry.coordinates[1];
                                        dataObj.lng = fatherGeoJsonNode.features[i].geometry.coordinates[0];
                                        dataObj.eventType = "selectorEvent";
                                        dataObj.desc = desc;
                                        dataObj.query = passedData.query;
                                        dataObj.targets = passedData.targets;
                                        dataObj.eventGenerator = passedData.eventGenerator;
                                        dataObj.color1 = passedData.color1;
                                        dataObj.color2 = passedData.color2;
                                        dataObj.queryType = passedData.queryType;
                                        dataObj.display = passedData.display;

                                        //    map.eventsOnMap.push(dataObj);
                                    }

                                    map.eventsOnMap.push(dataObj);

                                    if ((!gisLayersOnMap.hasOwnProperty(desc) && (display !== 'geometries')) || (is3DViewOn && display !== 'geometries')) {
                                        gisLayersOnMap[desc] = L.geoJSON(fatherGeoJsonNode, {
                                            pointToLayer: gisPrepareCustomMarker,
                                            onEachFeature: onEachFeature
                                        }).addTo(map.defaultMapRef);
                                    }

                                    // CORTI - tentativo di inserimento icone in mappa 3D
//                                    if(is3DViewOn){
////                                      var geoJSON3D = { "type": "FeatureCollection", "features": ciclePathFeature };
//                                        map.default3DMapRef.addGeoJSON("http://dashboard/dashboardSmartCity/widgets/layers/edificato/cyclingPaths.geojson");
//                                        console.log(JSON.stringify(fatherGeoJsonNode));
//                                    }

                                    if (loadingDiv) {
                                        loadingDiv.empty();
                                        loadingDiv.append(loadOkText);

                                        parHeight = loadOkText.height();
                                        parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                        loadOkText.css("margin-top", parMarginTop + "px");

                                        setTimeout(function () {
                                            loadingDiv.css("opacity", 0);
                                            setTimeout(function () {
                                                loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function () {
                                                    $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                });
                                                loadingDiv.remove();
                                            }, 350);
                                        }, 1000);
                                    }

                                    eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").hide();
                                    eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("font-weight", "bold");
                                    eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("color", eventGenerator.attr("data-activeFontColor"));
                                    if (eventGenerator.parents("div.gisMapPtrContainer").find('a.gisPinLink').attr("data-symbolMode") === 'auto') {
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").html("near_me");
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("color", "white");
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("text-shadow", "2px 2px 4px black");
                                    } else {
                                        //Evidenziazione che gli eventi di questa query sono su mappa in caso di icona custom
                                        eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").show();
                                        eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    }

                                    eventGenerator.show();

                                    var wkt = null;

                                    if (display !== 'pins') {
                                        stopGeometryAjax[desc] = false;
                                        gisGeometryTankForFullscreen[desc] = {
                                            capacity: fatherGeoJsonNode.features.length,
                                            shown: false,
                                            tank: [],
                                            lastConsumedIndex: 0
                                        };

                                        for (var i = 0; i < fatherGeoJsonNode.features.length; i++) {
                                            if (fatherGeoJsonNode.features[i].properties.hasOwnProperty('hasGeometry') && fatherGeoJsonNode.features[i].properties.hasOwnProperty('serviceUri')) {
                                                if (fatherGeoJsonNode.features[i].properties.hasGeometry === true) {
                                                    //gisGeometryServiceUriToShowFullscreen[event.desc].push(fatherGeoJsonNode.features[i].properties.serviceUri);

                                                    $.ajax({
                                                        url: "<?php echo $superServiceMapUrlPrefix; ?>" + "/api/v1/?serviceUri=" + fatherGeoJsonNode.features[i].properties.serviceUri,
                                                        type: "GET",
                                                        data: {},
                                                        async: true,
                                                        timeout: 0,
                                                        dataType: 'json',
                                                        success: function (geometryGeoJson) {
                                                            if (!stopGeometryAjax[desc]) {
                                                                // Creazione nuova istanza del parser Wkt
                                                                wkt = new Wkt.Wkt();

                                                                // Lettura del WKT dalla risposta
                                                                wkt.read(geometryGeoJson.Service.features[0].properties.wktGeometry, null);

                                                                var ciclePathFeature = [
                                                                    {
                                                                        type: "Feature",
                                                                        properties: geometryGeoJson.Service.features[0].properties,
                                                                        geometry: wkt.toJson()
                                                                    }
                                                                ];

                                                                if (!gisGeometryLayersOnMap.hasOwnProperty(desc)) {
                                                                    gisGeometryLayersOnMap[desc] = [];
                                                                }

                                                                // CORTI - Pane
                                                                if (map.defaultMapRef) {
                                                                    map.defaultMapRef.createPane('ciclePathFeature');
                                                                    map.defaultMapRef.getPane('ciclePathFeature').style.zIndex = 420;
                                                                }

                                                                gisGeometryLayersOnMap[desc].push(L.geoJSON(ciclePathFeature, {pane: 'ciclePathFeature'}).addTo(map.defaultMapRef));
                                                                
                                                                // CORTI - geoJSON 3D
                                                                // Tentativo di aggiungere json piste ciclabili al 3d
//                                                                if(is3DViewOn){
//                                                                    var geoJSON3D = { "type": "FeatureCollection", "features": ciclePathFeature };
//                                                                    map.default3DMapRef.addGeoJSON("http://dashboard/dashboardSmartCity/widgets/layers/edificato/cyclingPaths.geojson");
//                                                                    console.log(JSON.stringify(geoJSON3D));
//                                                                }
                                                                
                                                                gisGeometryTankForFullscreen[desc].tank.push(ciclePathFeature);
                                                            }
                                                        },
                                                        error: function (geometryErrorData) {
                                                            console.log("Ko");
                                                            console.log(JSON.stringify(geometryErrorData));
                                                        },
                                                        // CORTI
                                                        complete: function(){
                                                            editCSSFor3DWidgets();
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    }
                                },
                                error: function (errorData) {
                                    gisLayersOnMap[event.desc] = "loadError";

                                    if (loadingDiv) {
                                        loadingDiv.empty();
                                        loadingDiv.append(loadKoText);

                                        parHeight = loadKoText.height();
                                        parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                        loadKoText.css("margin-top", parMarginTop + "px");

                                        setTimeout(function () {
                                            loadingDiv.css("opacity", 0);
                                            setTimeout(function () {
                                                loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                    $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                });
                                                loadingDiv.remove();
                                            }, 350);
                                        }, 1000);
                                    }

                                    eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").hide();
                                    eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadErrorIcon").show();

                                    setTimeout(function () {
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadErrorIcon").hide();
                                        eventGenerator.parents("div.gisMapPtrContainer").find("a.gisPinLink").attr("data-onMap", "false");
                                        eventGenerator.parents("div.gisMapPtrContainer").find("a.gisPinLink").show();
                                    }, 1500);

                                    console.log("Error in getting GeoJSON from ServiceMap");
                                    console.log(JSON.stringify(errorData));
                                },
                                // CORTI
                                complete: function(){
                                    editCSSFor3DWidgets();
                                }
                            });
                        }

                        if (addMode === 'additive') {
                            addSelectorEventToMap();
                        }

                        if (addMode === 'exclusive') {
                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if (map.eventsOnMap[i].eventType !== 'selectorEvent') {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;
                                    break;
                                }
                            }
                            //Remove WidgetAlarm active pins
                            $.event.trigger({
                                type: "removeAlarmPin",
                            });
                            //Remove WidgetEvacuationPlans active pins
                            $.event.trigger({
                                type: "removeEvacuationPlanPin",
                            });
                            //Remove WidgetEvents active pins
                            $.event.trigger({
                                type: "removeEventFIPin",
                            });
                            //Remove WidgetResources active pins
                            $.event.trigger({
                                type: "removeResourcePin",
                            });
                            //Remove WidgetOperatorEvents active pins
                            $.event.trigger({
                                type: "removeOperatorEventPin",
                            });
                            //Remove WidgetTrafficEvents active pins
                            $.event.trigger({
                                type: "removeTrafficEventPin",
                            });
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                maxZoom: 18
                            }).addTo(map.defaultMapRef);

                            addSelectorEventToMap();
                        }

                        //  resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('addEventFI', function (event) {
                    if (event.target === map.mapName) {
                        function addEventFIToMap() {
                            let passedData = event.passedData;

                            for (let j = 0; j < passedData.length; j++) {

                                let lat = passedData[j].lat;
                                let lng = passedData[j].lng;
                                let categoryIT = passedData[j].categoryIT;

                                let name = passedData[j].name;
                                if (name.includes('?')) {
                                    name = name.replace(/\?/g, "'");
                                }

                                let place = passedData[j].place;
                                if (place.includes('?')) {
                                    place = place.replace(/\?/g, "'");
                                }

                                let startDate = passedData[j].startDate;
                                let endDate = passedData[j].endDate;
                                let startTime = passedData[j].startTime;
                                let freeEvent = passedData[j].freeEvent;
                                let address = passedData[j].address;
                                if (address.includes('?')) {
                                    address = address.replace(/\?/g, "'");
                                }

                                let civic = passedData[j].civic;
                                let price = passedData[j].price;
                                let phone = passedData[j].phone;
                                let descriptionIT = passedData[j].descriptionIT;
                                if (descriptionIT.includes('?')) {
                                    descriptionIT = descriptionIT.replace(/\?/g, "'");
                                }

                                let website = passedData[j].website;
                                let colorClass = passedData[j].colorClass;
                                let mapIconName = passedData[j].mapIconName;

                                let mapPinImg = '../img/eventsIcons/' + mapIconName + '.png';

                                let pinIcon = new L.DivIcon({
                                    className: null,
                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                    iconAnchor: [18, 36]
                                });

                                let markerLocation = new L.LatLng(lat, lng);
                                let marker = new L.Marker(markerLocation, {icon: pinIcon});
                                passedData[j].marker = marker;

                                //Creazione del popup per il pin appena creato
                                let popupText = '<h3 class="' + colorClass + ' recreativeEventMapTitle">' + name + '</h3>';
                                popupText += '<div class="recreativeEventMapBtnContainer"><button class="recreativeEventMapDetailsBtn recreativeEventMapBtn ' + colorClass + ' recreativeEventMapBtnActive" type="button">Details</button><button class="recreativeEventMapDescriptionBtn recreativeEventMapBtn ' + colorClass + '" type="button">Description</button><button class="recreativeEventMapTimingBtn recreativeEventMapBtn ' + colorClass + '" type="button">Timing</button><button class="recreativeEventMapContactsBtn recreativeEventMapBtn ' + colorClass + '" type="button">Contacts</button></div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer">';
                                if ((place !== 'undefined') || (address !== 'undefined')) {
                                    if (categoryIT !== 'undefined') {
                                        popupText += '<b>Category: </b>' + categoryIT;
                                    }

                                    if (place !== 'undefined') {
                                        popupText += '<br/>';
                                        popupText += '<b>Location: </b>' + place;
                                    }

                                    if (address !== 'undefined') {
                                        popupText += '<br/>';
                                        popupText += '<b>Address: </b>' + address;
                                        if (civic !== 'undefined') {
                                            popupText += ' ' + civic;
                                        }
                                    }

                                    if (freeEvent !== 'undefined') {
                                        popupText += '<br/>';
                                        if ((freeEvent !== 'yes') && (freeEvent !== 'YES') && (freeEvent !== 'Yes')) {
                                            if (price !== 'undefined') {
                                                popupText += '<b>Price (€) : </b>' + price + "<br>";
                                            } else {
                                                popupText += '<b>Price (€) : </b>N/A<br>';
                                            }
                                        } else {
                                            popupText += '<b>Free event: </b>' + freeEvent + '<br>';
                                        }
                                    }
                                } else {
                                    popupText += 'No further details available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';
                                if (descriptionIT !== 'undefined') {
                                    popupText += descriptionIT;
                                } else {
                                    popupText += 'No description available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapTimingContainer">';
                                if ((startDate !== 'undefined') || (endDate !== 'undefined') || (startTime !== 'undefined')) {
                                    popupText += '<b>From: </b>';
                                    if (startDate !== 'undefined') {
                                        popupText += startDate;
                                    } else {
                                        popupText += 'N/A';
                                    }
                                    popupText += '<br/>';

                                    popupText += '<b>To: </b>';
                                    if (endDate !== 'undefined') {
                                        popupText += endDate;
                                    } else {
                                        popupText += 'N/A';
                                    }
                                    popupText += '<br/>';

                                    if (startTime !== 'undefined') {
                                        popupText += '<b>Times: </b>' + startTime + '<br/>';
                                    } else {
                                        popupText += '<b>Times: </b>N/A<br/>';
                                    }

                                } else {
                                    popupText += 'No timings info available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';
                                if ((phone !== 'undefined') || (website !== 'undefined')) {
                                    if (phone !== 'undefined') {
                                        popupText += '<b>Phone: </b>' + phone + '<br/>';
                                    } else {
                                        popupText += '<b>Phone: </b>N/A<br/>';
                                    }

                                    if (website !== 'undefined') {
                                        if (website.includes('http') || website.includes('https')) {
                                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                                        } else {
                                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                                        }
                                    } else {
                                        popupText += '<b>Website: </b>N/A';
                                    }
                                } else {
                                    popupText += 'No contacts info available';
                                }
                                popupText += '</div>';

                                map.defaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth: 300});

                                lastPopup.on('popupopen', function () {
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapDetailsBtn').off('click');
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapDetailsBtn').click(function () {
                                        $('#' + widgetName + '_map div.recreativeEventMapDataContainer').hide();
                                        $('#' + widgetName + '_map div.recreativeEventMapDetailsContainer').show();
                                        $('#' + widgetName + '_map button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                        $(this).addClass('recreativeEventMapBtnActive');
                                    });

                                    $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapDescriptionBtn').off('click');
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapDescriptionBtn').click(function () {
                                        $('#' + widgetName + '_map div.recreativeEventMapDataContainer').hide();
                                        $('#' + widgetName + '_map div.recreativeEventMapDescContainer').show();
                                        $('#' + widgetName + '_map button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                        $(this).addClass('recreativeEventMapBtnActive');
                                    });

                                    $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapTimingBtn').off('click');
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapTimingBtn').click(function () {
                                        $('#' + widgetName + '_map div.recreativeEventMapDataContainer').hide();
                                        $('#' + widgetName + '_map div.recreativeEventMapTimingContainer').show();
                                        $('#' + widgetName + '_map button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                        $(this).addClass('recreativeEventMapBtnActive');
                                    });

                                    $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn').off('click');
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn').click(function () {
                                        $('#' + widgetName + '_map div.recreativeEventMapDataContainer').hide();
                                        $('#' + widgetName + '_map div.recreativeEventMapContactsContainer').show();
                                        $('#' + widgetName + '_map button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                        $(this).addClass('recreativeEventMapBtnActive');
                                    });
                                });

                                lastPopup.openPopup();

                                map.eventsOnMap.push(passedData[j]);
                            }
                        }

                        if (addMode === 'additive') {
                            addEventFIToMap();
                        }

                        if (addMode === 'exclusive') {
                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if (map.eventsOnMap[i].eventType !== 'eventFI') {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;
                                    break;
                                }
                            }
                            //Remove WidgetAlarm active pins
                            $.event.trigger({
                                type: "removeAlarmPin",
                            });
                            //Remove WidgetEvacuationPlans active pins
                            $.event.trigger({
                                type: "removeEvacuationPlanPin",
                            });
                            //Remove WidgetSelector active pins
                            $.event.trigger({
                                type: "removeSelectorEventPin",
                            });
                            //Remove WidgetResources active pins
                            $.event.trigger({
                                type: "removeResourcePin",
                            });
                            //Remove WidgetOperatorEvents active pins
                            $.event.trigger({
                                type: "removeOperatorEventPin",
                            });
                            //Remove WidgetTrafficEvents active pins
                            $.event.trigger({
                                type: "removeTrafficEventPin",
                            });
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                maxZoom: 18
                            }).addTo(map.defaultMapRef);

                            addEventFIToMap();
                        }

                        //console.log(map.eventsOnMap.length);

                        //  resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('addResource', function (event) {
                    if (event.target === map.mapName) {
                        function addResourceToMap() {
                            let passedData = event.passedData;

                            for (let j = 0; j < passedData.length; j++) {

                                let lat = passedData[j].lat;
                                let lng = passedData[j].lng;
                                let eventType = passedData[j].eventType;
                                let eventName = passedData[j].eventName;
                                let eventStartDate = passedData[j].eventStartDate;
                                let eventStartTime = passedData[j].eventStartTime;

                                mapPinImg = '../img/resourceIcons/metroMap.png';

                                pinIcon = new L.DivIcon({
                                    className: null,
                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                    iconAnchor: [18, 36]
                                });

                                var markerLocation = new L.LatLng(lat, lng);
                                var marker = new L.Marker(markerLocation, {icon: pinIcon});

                                passedData[j].marker = marker;

                                //Creazione del popup per il pin appena creato
                                var popupText = "<span class='mapPopupTitle'>" + eventName.toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'>" + eventStartDate + " - " + eventStartTime + "</span>";

                                map.defaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [-5, -40]}).openPopup();

                                map.eventsOnMap.push(passedData[j]);

                            }
                        }

                        if (addMode === 'additive') {
                            addResourceToMap();
                        }

                        if (addMode === 'exclusive') {
                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if (map.eventsOnMap[i].eventType !== 'resource') {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;
                                    break;
                                }
                            }
                            //Remove WidgetAlarm active pins
                            $.event.trigger({
                                type: "removeAlarmPin",
                            });
                            //Remove WidgetEvacuationPlans active pins
                            $.event.trigger({
                                type: "removeEvacuationPlanPin",
                            });
                            //Remove WidgetSelector active pins
                            $.event.trigger({
                                type: "removeSelectorEventPin",
                            });
                            //Remove WidgetEvents active pins
                            $.event.trigger({
                                type: "removeEventFIPin",
                            });
                            //Remove WidgetOperatorEvents active pins
                            $.event.trigger({
                                type: "removeOperatorEventPin",
                            });
                            //Remove WidgetTrafficEvents active pins
                            $.event.trigger({
                                type: "removeTrafficEventPin",
                            });
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                maxZoom: 18
                            }).addTo(map.defaultMapRef);

                            addResourceToMap();
                        }

                        //console.log(map.eventsOnMap.length);

                        //   resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('addOperatorEvent', function (event) {
                    if (event.target === map.mapName) {
                        function addOperatorEventToMap() {
                            let passedData = event.passedData;

                            for (let j = 0; j < passedData.length; j++) {

                                let lat = passedData[j].lat;
                                let lng = passedData[j].lng;
                                let eventType = passedData[j].eventType;
                                let eventName = passedData[j].eventName;
                                let eventStartDate = passedData[j].eventStartDate;
                                let eventStartTime = passedData[j].eventStartTime;
                                let eventPeopleNumber = parseInt(passedData[j].eventPeopleNumber);
                                let eventOperatorName = passedData[j].eventOperatorName;
                                let eventColor = passedData[j].eventColor;


                                let markerLocation = new L.LatLng(lat, lng);
                                let marker = new L.Marker(markerLocation);
                                passedData[j].marker = marker;

                                //Creazione del popup per il pin appena creato
                                popupText = "<span class='mapPopupTitle'>" + eventColor.toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'>" + eventStartDate + " - " + eventStartTime + "</span>" +
                                        "<span class='mapPopupLine'>PEOPLE INVOLVED: " + eventPeopleNumber + "</span>" +
                                        "<span class='mapPopupLine'>OPERATOR: " + eventOperatorName.toUpperCase() + "</span>";

                                map.defaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [0, 0]}).openPopup();

                                map.eventsOnMap.push(passedData[j]);

                            }
                        }

                        if (addMode === 'additive') {
                            addOperatorEventToMap();
                        }

                        if (addMode === 'exclusive') {
                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if (map.eventsOnMap[i].eventType !== 'OperatorEvent') {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;
                                    break;
                                }
                            }
                            //Remove WidgetAlarm active pins
                            $.event.trigger({
                                type: "removeAlarmPin",
                            });
                            //Remove WidgetEvacuationPlans active pins
                            $.event.trigger({
                                type: "removeEvacuationPlanPin",
                            });
                            //Remove WidgetSelector active pins
                            $.event.trigger({
                                type: "removeSelectorEventPin",
                            });
                            //Remove WidgetEvents active pins
                            $.event.trigger({
                                type: "removeEventFIPin",
                            });
                            //Remove WidgetResources active pins
                            $.event.trigger({
                                type: "removeResourcePin",
                            });
                            //Remove WidgetTrafficEvents active pins
                            $.event.trigger({
                                type: "removeTrafficEventPin",
                            });
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                maxZoom: 18
                            }).addTo(map.defaultMapRef);

                            addOperatorEventToMap();
                        }

                        //console.log(map.eventsOnMap.length);

                        // resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('addTrafficEvent', function (event) {
                    if (event.target === map.mapName) {
                        function addTrafficEventToMap() {
                            let passedData = event.passedData;

                            for (let j = 0; j < passedData.length; j++) {

                                let lat = passedData[j].lat;
                                let lng = passedData[j].lng;
                                let eventType = passedData[j].eventType;
                                let eventSubtype = passedData[j].eventSubtype;
                                let eventName = passedData[j].eventName;
                                let eventStartDate = passedData[j].eventStartDate;
                                let eventStartTime = passedData[j].eventStartTime;
                                let eventSeverity = passedData[j].eventSeverity;
                                let eventseveritynum = passedData[j].eventseveritynum;
                                passedData[j].type = "trafficEvent";

                                //Creazione dell'icona custom per il pin
                                switch (eventSeverity) {
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

                                let pinIcon = new L.DivIcon({
                                    className: null,
                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                    iconAnchor: [18, 36]
                                });

                                let markerLocation = new L.LatLng(lat, lng);
                                let marker = new L.Marker(markerLocation, {icon: pinIcon});
                                passedData[j].marker = marker;

                                //Creazione del popup per il pin appena creato
                                popupText = "<span class='mapPopupTitle'>" + eventName + "</span>" +
                                        "<span class='mapPopupLine'><i>Start date</i>: " + eventStartDate + " - " + eventStartTime + "</span>" +
                                        "<span class='mapPopupLine'><i>Event type</i>: " + trafficEventTypes["type" + eventType].desc.toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'><i>Event subtype</i>: " + trafficEventSubTypes["subType" + eventSubtype].toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'><i>Event severity</i>: " + eventseveritynum + " - <span style='background-color: " + severityColor + "'>" + eventSeverity.toUpperCase() + "</span></span>";

                                map.defaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth: 600}).openPopup();

                                map.eventsOnMap.push(passedData[j]);
                            }
                        }

                        if (addMode === 'additive') {
                            addTrafficEventToMap();
                        }

                        if (addMode === 'exclusive') {
                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if (map.eventsOnMap[i].type !== "trafficEvent") {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;
                                    break;
                                }
                            }
                            //Remove WidgetAlarm active pins
                            $.event.trigger({
                                type: "removeAlarmPin",
                            });
                            //Remove WidgetEvacuationPlans active pins
                            $.event.trigger({
                                type: "removeEvacuationPlanPin",
                            });
                            //Remove WidgetSelector active pins
                            $.event.trigger({
                                type: "removeSelectorEventPin",
                            });
                            //Remove WidgetEvents active pins
                            $.event.trigger({
                                type: "removeEventFIPin",
                            });
                            //Remove WidgetResources active pins
                            $.event.trigger({
                                type: "removeResourcePin",
                            });
                            //Remove WidgetOperatorEvents active pins
                            $.event.trigger({
                                type: "removeOperatorEventPin",
                            });
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                maxZoom: 18
                            }).addTo(map.defaultMapRef);

                            addTrafficEventToMap();
                        }

                        //console.log(map.eventsOnMap.length);

                        //  resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('addTrafficRealTimeDetails', function (event) {
                    if (event.target === map.mapName) {
                        var so = map.defaultMapRef.getBounds()._southWest;
                        var ne = map.defaultMapRef.getBounds()._northEast;
                        var zm = Math.round(map.defaultMapRef.getZoom() - 1);

                        var roadsJson = event.passedData + "?sLat=" + so.lat + "&sLong=" + so.lng + "&eLat=" + ne.lat + "&eLong=" + ne.lng + "&zoom=" + zm;

                        function addTrafficRTDetailsToMap() {
                            var event = {};
                            event.eventType = "trafficRealTimeDetails";
                            event.maxLat = ne.lat;
                            event.minLat = so.lat;
                            event.maxLng = ne.lng;
                            event.minLng = so.lng;
                            event.zm = zm;


                            var myMarker = new L.LayerGroup();

                            /*    $.ajax({
                             //    url: "../trafficRTDetails/sensorsCoord.json",
                             url: "https://firenzetraffic.km4city.org/trafficRTDetails/sensorsCoord.php",
                             type: "GET",
                             async: false,
                             cache: false,
                             dataType: 'json',
                             success: function (_sensors) {
                             sensors = JSON.parse(_sensors);
                             for (var i = 0; i < sensors.length; i++) {
                             if (sensors[i].sensorLat > so.lat && sensors[i].sensorLat < ne.lat && sensors[i].sensorLong > so.lng && sensors[i].sensorLong < ne.lng) {
                             var mark = L.circleMarker([sensors[i].sensorLat, sensors[i].sensorLong]);
                             mark.addTo(myMarker);
                             }
                             }
                             myMarker.addTo(map.defaultMapRef);
                             }
                             }); */

                            event.marker = myMarker;

                            map.defaultMapRef.on('click', function (e) {
                                var bnds = map.defaultMapRef.getBounds()
//                                console.log(bnds.getSouth() + ";" + bnds.getWest() + ";" + bnds.getNorth() + ";" + bnds.getEast());
                                if (roads == null)
                                    loadRoads();
                                else {
                                }
                            });

                            // CORTI - zIndex
                            if (map.defaultMapRef) {
                                map.defaultMapRef.createPane('trafficFlow');
                                map.defaultMapRef.getPane('trafficFlow').style.zIndex = 420;
                            }

                            var wktLayer = new L.LayerGroup();
                            var roads = null;
                            var time = 0;

                            loadRoads();

                            function loadRoads() {
                                defaults = {
                                    icon: new L.DivIcon({className: "geo-icon"}),
                                    editable: true,
                                    color: '#AA0000',
                                    weight: 2.5,
                                    opacity: 1,
                                    fillColor: '#AA0000',
                                    fillOpacity: 1,
                                    pane: 'trafficFlow'
                                };

                                $.ajax({
                                    url: roadsJson,
                                    type: "GET",
                                    async: true,
                                    dataType: 'json',
                                    success: function (_roads) {
                                        roads = JSON.parse(JSON.stringify(_roads));

                                        loadDensity();
                                    },
                                    error: function (err) {
                                        console.log(err);
                                        alert("error see log json");
                                    },
                                    // CORTI
                                    complete: function(){
                                        editCSSFor3DWidgets();
                                    }
                                });
                            }

                            function loadDensity() {
                                $.ajax({
                                    //    url: "http://localhost/dashboardSmartCity/trafficRTDetails/density/read.php" + "?sLat=" + so.lat + "&sLong=" + so.lng + "&eLat=" + ne.lat + "&eLong=" + ne.lng + "&zoom=" + zm,
                                    url: "https://firenzetraffic.km4city.org/trafficRTDetails/density/read.php" + "?sLat=" + so.lat + "&sLong=" + so.lng + "&eLat=" + ne.lat + "&eLong=" + ne.lng + "&zoom=" + zm,
                                    type: "GET",
                                    async: false,
                                    cache: false,
                                    dataType: 'json',
                                    success: function (_density) {
                                        density = JSON.parse(JSON.stringify(_density));

                                        for (var i = 0; i < roads.length; i++) {
                                            if (density.hasOwnProperty((roads[i].road))) {
                                                roads[i].data = density[roads[i].road].data;
                                            }
                                        }

                                        event.roads = roads;

                                        time = 0;
                                        draw(time);
//                                        console.log("@time " + time);
                                    },
                                    error: function (err) {
                                        console.log(err);
                                        alert("error see log json");
                                    },
                                    // CORTI
                                    complete: function(){
                                        editCSSFor3DWidgets();
                                    }
                                });
                            }

                            function draw(t) {
                                if (roads == null)
                                    return;
                                //wktLayer.clearLayers();
                                for (var i = 0; i < roads.length; i++) {
                                    var segs = roads[i].segments;
                                    for (var j = 0; j < segs.length; j++) {
                                        var seg = segs[j];
                                        if (typeof seg.start != "undefined") {
                                            var wktPoint = "POINT(" + seg.start.long + " " + seg.start.lat + ")";
                                            var wktLine = "LINESTRING(" + seg.start.long + " " + seg.start.lat + "," + seg.end.long + " " + seg.end.lat + ")";

                                            try {
                                                if (!jQuery.isEmptyObject(roads[i].data[0])) {
                                                    var value = Number(roads[i].data[t][seg.id].replace(",", "."));
                                                    //console.log(value);
                                                    var green = 0.3;
                                                    var yellow = 0.6;
                                                    var orange = 0.9;
                                                    if (seg.Lanes == 2) {
                                                        green = 0.6;
                                                        yellow = 1.2;
                                                        orange = 1.8;
                                                    }
                                                    if (seg.FIPILI == 1) {
                                                        green = 0.25;
                                                        yellow = 0.5;
                                                        orange = 0.75;
                                                    }
                                                    if (seg.Lanes == 3) {
                                                        green = 0.9;
                                                        yellow = 1.5;
                                                        orange = 2;
                                                    }
                                                    if (seg.Lanes == 4) {
                                                        green = 1.2;
                                                        yellow = 1.6;
                                                        orange = 2;
                                                    }
                                                    if (seg.Lanes == 5) {
                                                        green = 1.6;
                                                        yellow = 2;
                                                        orange = 2.4;
                                                    }
                                                    if (seg.Lanes == 6) {
                                                        green = 2;
                                                        yellow = 2.4;
                                                        orange = 2.8;
                                                    }
                                                    if (value <= green)
                                                        defaults.color = "#00ff00";
                                                    else if (value <= yellow)
                                                        defaults.color = "#ffff00";
                                                    else if (value <= orange)
                                                        defaults.color = "#ff8c00";
                                                    else
                                                        defaults.color = "#ff0000";
                                                    defaults.fillColor = defaults.color;

                                                    if (!seg.obj) {
                                                        var wkt = new Wkt.Wkt();
                                                        wkt.read(wktLine, "newMap");
                                                        obj = wkt.toObject(defaults);
                                                        obj.options.trafficFlow = true;     // ADD GP
                                                        obj.addTo(wktLayer);
                                                        seg.obj = obj;

                                                    } else {
                                                        seg.obj.setStyle(defaults);
                                                    }
                                                }
                                            } catch (e) {
                                                console.log(e);
                                            }
                                        }
                                    }
                                }
                                wktLayer.addTo(map.defaultMapRef);
                            }

                            event.trafficLayer = wktLayer;

                            //Create legend
                            var legend = L.control({position: 'bottomright'});

                            legend.onAdd = function (map) {

                                var div = L.DomUtil.create('div', 'info legend'),
                                        grades = ["Legend"],
                                        //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                        labels = ["https://firenzetraffic.km4city.org/trafficRTDetails/legend.png"];

                                // loop through our density intervals and generate a label with a colored square for each interval
                                for (var i = 0; i < grades.length; i++) {
                                    div.innerHTML +=
                                            grades[i] + (" <img src=" + labels[i] + " height='120' width='80' background='#cccccc'>") + '<br>';
                                }

                                return div;
                            };

                            legend.addTo(map.defaultMapRef);

                            event.legend = legend;
                            map.eventsOnMap.push(event);
                            //window.setInterval("loadDensity();", 300000);
                        }

                        if (addMode === 'additive') {
                            addTrafficRTDetailsToMap();
                        }

                        if (addMode === 'exclusive') {
                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if (map.eventsOnMap[i].type !== "trafficRealTimeDetails") {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;
                                    break;
                                }
                            }
                            //Remove WidgetAlarm active pins
                            $.event.trigger({
                                type: "removeAlarmPin",
                            });
                            //Remove WidgetEvacuationPlans active pins
                            $.event.trigger({
                                type: "removeEvacuationPlanPin",
                            });
                            //Remove WidgetEvents active pins
                            $.event.trigger({
                                type: "removeEventFIPin",
                            });
                            //Remove WidgetResources active pins
                            $.event.trigger({
                                type: "removeResourcePin",
                            });
                            //Remove WidgetOperatorEvents active pins
                            $.event.trigger({
                                type: "removeOperatorEventPin",
                            });
                            //Remove WidgetTrafficEvents active pins
                            $.event.trigger({
                                type: "removeTrafficEventPin",
                            });
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                maxZoom: 18
                            }).addTo(map.defaultMapRef);

                            addTrafficRTDetailsToMap();
                        }

                        //resizeMapView(map.defaultMapRef);
                    }
                });

                $(document).on('addHeatmap', function (event) {
                    if (event.target === map.mapName) {
                        map.defaultMapRef.off('click');
                        //   window.addHeatmapToMap = function() {

                        //Crea un layer per la heatmap (i dati gli verranno passati nell'evento)
                        //heatmap configuration
                        function initHeatmapLayer(heatmapRangeObject) {

                            var heatmapCfg = {};
                            var colorScale = {};
                            var colorGradient = {};
                            var gradientString = "";

                            map.cfg = JSON.parse(heatmapRangeObject[0].leafletConfigJSON);
                            //    map.cfg['blur'] = 0.85;

                            if (current_radius != null) {
                                map.cfg['radius'] = current_radius;
                            }
                            if (current_opacity != null) {
                                map.cfg['maxOpacity'] = current_opacity;
                            }

                            $.ajax({
                                url: "https://heatmap.snap4city.org/getColorMap.php?metricName=" + map.testMetadata.metadata.metricName,
                                type: "GET",
                                async: false,
                                dataType: 'json',
                                success: function (dataColorScale) {
                                    colorScale = dataColorScale;
                                },
                                error: function (err) {
                                    alert("Error in retrieving color map scale: ");
                                    console.log(err);
                                },
                                // CORTI
                                complete: function(){
                                    editCSSFor3DWidgets();
                                }
                            });

                            var minVal = colorScale[0].min;
                            if (minVal === null || minVal === undefined) {
                                minVal = heatmapRangeObject[0].range1Inf;
                            }

                            var maxVal = colorScale[colorScale.length - 1].min;
                            if (maxVal === null || maxVal === undefined) {
                                maxVal = heatmapRangeObject[0].range10Inf;
                            }
                            colorGradient[0] = 0;
                            colorGradient[colorScale.length - 1] = 1;
                            gradientString = '{ "' + colorGradient[0] + '": "#' + fullColorHex(colorScale[0].rgb.substring(1, colorScale[0].rgb.length - 1)) + '", ';
                            for (let k1 = 1; k1 < colorScale.length - 1; k1++) {
                                colorGradient[k1] = (colorScale[k1].min - minVal) / (maxVal - minVal);
                                gradientString = gradientString + '"' + colorGradient[k1] + '": "#' + fullColorHex(colorScale[k1].rgb.substring(1, colorScale[k1].rgb.length - 1)) + '", ';
                            }
                            gradientString = gradientString + '"' + colorGradient[colorScale.length - 1] + '": "#' + fullColorHex(colorScale[colorScale.length - 1].rgb.substring(1, colorScale[colorScale.length - 1].rgb.length - 1)) + '"}';
                            map.cfg.gradient = JSON.parse(gradientString);
                            map.heatmapLayer = new HeatmapOverlay(map.cfg);
                            //map.heatmapLayer.zIndex = 20;
                            //  map.legendHeatmap = L.control({position: 'topright'});
                        }

                        if (!map.legendHeatmap) {
                            map.legendHeatmap = L.control({position: 'topright'});
                        }

                        function changeHeatmapPage(page)
                        {
                            var btn_next = document.getElementById("<?= $_REQUEST['name_w'] ?>_nextButt");
                            var btn_prev = document.getElementById("<?= $_REQUEST['name_w'] ?>_prevButt");
                            var heatmapDescr = document.getElementById("<?= $_REQUEST['name_w'] ?>_heatMapDescr");

                            // Validate page
                            if (numHeatmapPages() > 1) {
                                if (page < 1)
                                    page = 1;
                                if (page > numHeatmapPages())
                                    page = numHeatmapPages();

                                if (current_page == 0) {
                                    if (!is3DViewOn) {
                                        btn_next.style.visibility = "hidden";
                                    } else {
                                        $('.heatmap-3d-controls').find('.next').addClass('hidden');
                                    }
                                } else {
                                    if (!is3DViewOn) {
                                        btn_next.style.visibility = "visible";
                                    } else {
                                        $('.heatmap-3d-controls').find('.next').removeClass('hidden');
                                    }
                                }

                                if (current_page == numHeatmapPages() - 1) {
                                    if (!is3DViewOn) {
                                        btn_prev.style.visibility = "hidden";
                                    } else {
                                        $('.heatmap-3d-controls').find('.prev').addClass('hidden');
                                    }
                                } else {
                                    if (!is3DViewOn) {
                                        btn_prev.style.visibility = "visible";
                                    } else {
                                        $('.heatmap-3d-controls').find('.prev').removeClass('hidden');
                                    }
                                }
                            }

                            if (current_page < numHeatmapPages()) {
                                //  $("#heatMapDescr").text(heatmapData[current_page].metadata[0].date);  // OLD-API
                                //    heatmapDescr.text(heatmapData[current_page].metadata.date);
//                                heatmapDescr.firstChild.wholeText = heatmapData[current_page].metadata.date;
                                // heatmapData[current_page].metadata[0].date
                            }
                        }

                        function numHeatmapPages()
                        {
                            //    return Math.ceil(heatmapData.length / records_per_page);
                            return heatmapData.length;
                        }


                        function setOption(option, value, decimals) {
                            if (baseQuery.includes("heatmap.php")) {
                                if (option == "radius") {       // AGGIUNGERE SE FLAG è TRUE SI METTE IL VALORE DI CONFIG
                                    if (resetPageFlag) {
                                        if (resetPageFlag === true) {
                                            current_radius = map.cfg['radius'];
                                        } else {
                                            current_radius = Math.max(value, 2);
                                        }
                                    } else {
                                        current_radius = Math.max(value, 2);
                                    }
                                    map.cfg["radius"] = current_radius.toFixed(1);
                                    if (decimals) {
                                        $("#<?= $_REQUEST['name_w'] ?>_range" + option).text(parseFloat(current_radius).toFixed(parseInt(decimals)));
                                        $("#<?= $_REQUEST['name_w'] ?>_slider" + option).attr("value", parseFloat(current_radius).toFixed(parseInt(decimals)));
                                    }
                                } else if (option == "maxOpacity") {
                                    if (resetPageFlag) {
                                        if (resetPageFlag === true) {
                                            current_opacity = map.cfg['maxOpacity'];
                                        } else {
                                            current_opacity = value;
                                        }
                                    } else {
                                        current_opacity = value;
                                    }
                                    map.cfg["maxOpacity"] = current_opacity;
                                    if (decimals) {
                                        $("#<?= $_REQUEST['name_w'] ?>_range" + option).text(parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                        $("#<?= $_REQUEST['name_w'] ?>_slider" + option).attr("value", parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                    }
                                }
                                // update the heatmap with the new configuration
                                map.heatmapLayer.configure(map.cfg);
                            } else {
                                if (option == "maxOpacity") {
                                    if (wmsLayer) {
                                        wmsLayer.setOpacity(value);
                                        current_opacity = value;
                                        if (decimals) {
                                            $("#<?= $_REQUEST['name_w'] ?>_range" + option).text(parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                            $("#<?= $_REQUEST['name_w'] ?>_slider" + option).attr("value", parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                        }
                                    }
                                }
                                map.heatmapLayer.configure(map.cfg);
                            }
                        }

                        function upSlider(color, step, decimals, max) {
                            let value = $("#<?= $_REQUEST['name_w'] ?>_slider" + color).attr("value");
                            if (parseFloat(parseFloat(value) + parseFloat(step)) <= max) {
                                $("#<?= $_REQUEST['name_w'] ?>_range" + color).text(parseFloat(parseFloat(value) + parseFloat(step)).toFixed(parseInt(decimals)));
                                document.getElementById("<?= $_REQUEST['name_w'] ?>_slider" + color).value = parseFloat(parseFloat(value) + parseFloat(step)).toFixed(parseInt(decimals));
                                $("#<?= $_REQUEST['name_w'] ?>_slider" + color).trigger('change');
                            }
                        }

                        function downSlider(color, step, decimals, min) {
                            let value = $("#<?= $_REQUEST['name_w'] ?>_slider" + color).attr("value");
                            if (parseFloat(parseFloat(value) - parseFloat(step)) >= min) {
                                $("#<?= $_REQUEST['name_w'] ?>_range" + color).text(parseFloat(parseFloat(value) - parseFloat(step)).toFixed(parseInt(decimals)));
                                document.getElementById("<?= $_REQUEST['name_w'] ?>_slider" + color).value = parseFloat(parseFloat(value) - parseFloat(step)).toFixed(parseInt(decimals));
                                $("#<?= $_REQUEST['name_w'] ?>_slider" + color).trigger('change');
                            }
                        }

                        function removeHeatmap(resetPageFlag) {
                            if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                if (resetPageFlag == true) {
                                    current_page = 0;     // CTR SE VA BENE BISOGNA DISTINGUERE IL CASO CHE SI STIA NAVIGANDO LA STESSA HEATMAP_NAME OPPURE UN'ALTRA NUOVA HEATMP_NAME
                                    current_radius = null;
                                    current_opacity = null;
                                    changeRadiusOnZoom = false;
                                    estimateRadiusFlag = false;
                                    estimatedRadius = null;
                                    wmsDatasetName = null;
                                }
                                map.testData = [];
                                map.heatmapLayer.setData({data: []});
                                map.defaultMapRef.removeLayer(map.heatmapLayer);
                                if (resetPageFlag != true) {
                                    if (map.cfg["radius"] != current_radius) {
                                        setOption('radius', current_radius, 1);
                                    }
                                    if (map.cfg["maxOpacity"] != current_opacity) {
                                        setOption('maxOpacity', current_opacity, 2);
                                    }
                                }
                                map.defaultMapRef.removeControl(map.legendHeatmap);
                                /*    if(map.heatmapLegendColors) {
                                 map.defaultMapRef.removeControl(map.heatmapLegendColors);
                                 }*/
                            } else {    // NEW WMS HEATMAP
                                if (resetPageFlag == true) {
                                    current_page = 0;
                                }
                                map.defaultMapRef.removeLayer(wmsLayer);
                                map.defaultMapRef.removeControl(map.legendHeatmap);
                            }
                        }

                        function removeHeatmapColorLegend(index, resetPageFlag) {
                            if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                if (resetPageFlag == true) {
                                    current_page = 0;     // CTR SE VA BENE BISOGNA DISTINGUERE IL CASO CHE SI STIA NAVIGANDO LA STESSA HEATMAP_NAME OPPURE UN'ALTRA NUOVA HEATMP_NAME
                                    current_radius = null;
                                    current_opacity = null;
                                    changeRadiusOnZoom = false;
                                    estimateRadiusFlag = false;
                                    estimatedRadius = null;
                                    wmsDatasetName = null;
                                }
                                map.testData = [];
                                map.heatmapLayer.setData({data: []});
                                map.defaultMapRef.removeLayer(map.heatmapLayer);
                                if (resetPageFlag != true) {
                                    if (map.cfg["radius"] != current_radius) {
                                        setOption('radius', current_radius, 1);
                                    }
                                    if (map.cfg["maxOpacity"] != current_opacity) {
                                        setOption('maxOpacity', current_opacity, 2);
                                    }
                                }
                                map.defaultMapRef.removeControl(map.eventsOnMap[index].legendColors);
                            } else {    // NEW WMS HEATMAP
                                if (resetPageFlag == true) {
                                    current_page = 0;
                                }
                                map.defaultMapRef.removeControl(map.eventsOnMap[index].legendColors);
                                map.defaultMapRef.removeLayer(wmsLayer);
                            }
                        }

                        function updateChangeRadiusOnZoom(htmlElement) {
                            if (htmlElement.checked) {
                                changeRadiusOnZoom = true;
                                $("#<?= $_REQUEST['name_w'] ?>_estimateRad").attr('disabled', false);
                            } else {
                                changeRadiusOnZoom = false;
                                $("#<?= $_REQUEST['name_w'] ?>_estimateRad").attr('disabled', true);
                            }
                            //  $("#radiusEstCnt").toggle(htmlElement.checked);
                        }

                        function computeRadiusOnData(htmlElement) {
                            if (htmlElement.checked) {
                                estimateRadiusFlag = true;
                                $("#<?= $_REQUEST['name_w'] ?>_changeRad").attr('disabled', true);
                            } else {
                                estimateRadiusFlag = false;
                                $("#<?= $_REQUEST['name_w'] ?>_changeRad").attr('disabled', false);
                            }
                        }

                        map.legendHeatmap.onAdd = function () {
                            map.legendHeatmapDiv = L.DomUtil.create('div');
                            map.legendHeatmapDiv.id = "heatmapLegend";
                            // disable interaction of this div with map
                            if (L.Browser.touch) {
                                L.DomEvent.disableClickPropagation(map.legendHeatmapDiv);
                                L.DomEvent.on(map.legendHeatmapDiv, 'mousewheel', L.DomEvent.stopPropagation);
                            } else {
                                L.DomEvent.on(map.legendHeatmapDiv, 'click', L.DomEvent.stopPropagation);
                            }
                            map.legendHeatmapDiv.style.width = "340px";
                            map.legendHeatmapDiv.style.fontWeight = "bold";
                            map.legendHeatmapDiv.style.background = "#cccccc";
                            //  map.legendHeatmapDiv.style.background = "rgba(255,255,255,0.5)";
                            //map.legendHeatmap.style.background = "-webkit-gradient(linear, left top, left bottom, from(#eeeeee), to(#cccccc))";
                            map.legendHeatmapDiv.style.padding = "10px";

                            //categories = ['blue', 'cyan', 'green', 'yellowgreen', 'yellow', 'gold', 'orange', 'darkorange', 'tomato', 'orangered', 'red'];
                            let colors = [];
                            colors['blue'] = '#0000FF';
                            colors['cyan'] = '#00FFFF';
                            colors['green'] = '#008000';
                            colors['yellowgreen'] = '#9ACD32';
                            colors['yellow'] = '#FFFF00';
                            colors['gold'] = '#FFD700';
                            colors['orange'] = '#FFA500';
                            colors['darkorange'] = '#FF8C00';
                            colors['orangered'] = '#FF4500';
                            colors['tomato'] = '#FF6347';
                            colors['red'] = '#FF0000';
                            let colors_value = [];
                            colors_value['blue'] = '#0000FF';
                            colors_value['cyan'] = '#00FFFF';
                            colors_value['green'] = '#008000';
                            colors_value['yellowgreen'] = '#9ACD32';
                            colors_value['yellow'] = '#FFFF00';
                            colors_value['gold'] = '#FFD700';
                            colors_value['orange'] = '#FFA500';
                            colors_value['darkorange'] = '#FF8C00';
                            colors_value['tomato'] = '#FF6347';
                            colors_value['orangered'] = '#FF4500';
                            colors_value['red'] = '#FF0000';
                            //  map.legendHeatmapDiv.innerHTML += '<div class="textTitle" style="text-align:center">' + map.testMetadata.metadata[0].mapName + '</div>';  // OLD-API
                            map.legendHeatmapDiv.innerHTML += '<div class="textTitle" style="text-align:center">' + mapName + '</div>';
                            if (!baseQuery.includes("heatmap.php")) {
                                map.legendHeatmapDiv.innerHTML += '<div id="<?= $_REQUEST['name_w'] ?>_controlsContainer" style="height:20px"><div class="text"  style="width:50%; float:left">' + '<?php echo ucfirst(isset($_REQUEST["profile"]) ? $_REQUEST["profile"] : "Heatmap Controls:"); ?></div><div class="text" style="width:50%; float:right"><label class="switch"><input type="checkbox" id="<?= $_REQUEST['name_w'] ?>_animation"><div class="slider round"><span class="animationOn"></span><span class="animationOff" style="color: black; text-align: right">24H</span><span class="animationOn" style="color: black; text-align: right">Static</span></div></label></div></div>';
                            } else {
                                map.legendHeatmapDiv.innerHTML += '<div class="text">' + '<?php echo ucfirst(isset($_REQUEST["profile"]) ? $_REQUEST["profile"] : "Heatmap Controls:"); ?></div>';
                            }
                            //    map.legendHeatmapDiv.innerHTML += '</div>';
                            // radius
                            if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP
                                map.legendHeatmapDiv.innerHTML +=
                                        '<div id="heatmapRadiusControl" style="margin-top:10px">' +
                                        '<div style="display:inline-block; vertical-align:super;">Radius (px):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>' +
                                        '<div id= "<?= $_REQUEST['name_w'] ?>_downSlider_radius" style="display:inline-block; vertical-align:super; color: #0078A8">&#10094;</div>&nbsp;&nbsp;&nbsp;' +
                                        //  '<input id="<?= $_REQUEST['name_w'] ?>_sliderradius" style="display:inline-block; vertical-align:baseline; width:auto" type="range" min="0" max="0.0010" value="0.0008" step="0.00001">' +
                                        //  '<input id="<?= $_REQUEST['name_w'] ?>_sliderradius" style="display:inline-block; vertical-align:baseline; width:auto" type="range" min="1" max="' + estimatedRadius * 20 + '" value="' + current_radius + '" step="' + Math.floor((estimatedRadius * 20)/40) + '">' +
                                        '<input id="<?= $_REQUEST['name_w'] ?>_sliderradius" style="display:inline-block; vertical-align:baseline; width:auto" type="range" min="1" max="' + estimatedRadius * 30 + '" value="' + current_radius + '" step="2">' +
                                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div id="upSlider_radius" style="display:inline-block; vertical-align:super; color: #0078A8">&#10095;</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                                        '<span id="<?= $_REQUEST['name_w'] ?>_rangeradius" style="display:inline-block; vertical-align:super;">' + current_radius + '</span>' +
                                        '</div>';
                            }
                            // max opacity
                            map.legendHeatmapDiv.innerHTML +=
                                    '<div id="heatmapOpacityControl">' +
                                    '<div style="display:inline-block; vertical-align:super;">Max Opacity: &nbsp;&nbsp;&nbsp;&nbsp;</div>' +
                                    '<div id="<?= $_REQUEST['name_w'] ?>_downSlider_opacity" style="display:inline-block; vertical-align:super; color: #0078A8">&#10094;</div>&nbsp;&nbsp;&nbsp;' +
                                    '<input id="<?= $_REQUEST['name_w'] ?>_slidermaxOpacity" style="display:inline-block; vertical-align:baseline; width:auto" type="range" min="0" max="1" value="' + current_opacity + '" step="0.01">' +
                                    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div id="upSlider_opacity" style="display:inline-block;vertical-align:super; color: #0078A8">&#10095;</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                                    '<span id="<?= $_REQUEST['name_w'] ?>_rangemaxOpacity" style="display:inline-block;vertical-align:super;">' + current_opacity + '</span>' +
                                    '</div>';

                            // Heatmap Navigation Buottons (prev & next)
                            map.legendHeatmapDiv.innerHTML +=
                                    '<div id="heatmapNavigationCnt">' +
                                    //   '<a href="javascript:prevHeatmapPage()" id="btn_prev">Prev</a>'
                                    //   '<a href="javascript:nextHeatmapPage()" id="btn_next">Next</a>'
                                    //   '<a onClick="javascript:prevHeatmapPage()" id="btn_prev">Prev</a>'
                                    //   '<a onClick="javascript:nextHeatmapPage()" id="btn_next">Next</a>'
                                    '<input type="button" id="<?= $_REQUEST['name_w'] ?>_prevButt" value="< Prev" style="float: left"/>' +
                                    '<input type="button" id="<?= $_REQUEST['name_w'] ?>_nextButt" value="Next >" style="float: right"/>' +
                                    //  '<div id="heatMapDescr" style="text-align: center">' + map.testMetadata.metadata[0].date + '</p>' +   // OLD-API
                                    '<div id="<?= $_REQUEST['name_w'] ?>_heatMapDescr" style="text-align: center">' + mapDate + '</p>' +
                                    //  '<a href="#" id="prevHeatmapPage">&lt; Prev</a>'
                                    //  '<a href="#" id="nextHeatmapPage">Next &gt;</a>'
                                    '</div>';
                            if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                map.legendHeatmapDiv.innerHTML +=
                                        '<div id="radiusCnt">' +
                                        // '<input type="checkbox" name="checkfield" id="g01-01" onchange="updateChangeRadiusOnZoom(this)"/> Change Radius on Zoom' +
                                        '<input type="checkbox" name="checkfield" id="<?= $_REQUEST['name_w'] ?>_changeRad"/> Change Radius on Zoom' +
                                        '</div>';
                                map.legendHeatmapDiv.innerHTML +=
                                        '<div id="radiusEstCnt"">' +
                                        // '<input type="checkbox" name="checkfield" id="g01-01" onchange="updateChangeRadiusOnZoom(this)"/> Change Radius on Zoom' +
                                        '<input type="checkbox" name="checkfield" id="<?= $_REQUEST['name_w'] ?>_estimateRad" disabled="true"/> Estimate Radius Based on Data' +
                                        '</div>';
                            }

                            function checkLegend() {
                                /*   if(document.getElementById("<?= $_REQUEST['name_w'] ?>_downSlider_radius") == null){
                                 setTimeout(checkLegend, 500);
                                 }
                                 else{   */
                                if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_sliderradius").addEventListener("input", function () {
                                        setOption('radius', this.value, 1)
                                    }, false);
                                }

                                //document.getElementById("<?= $_REQUEST['name_w'] ?>_downSlider_opacity").addEventListener("click", function(){ downSlider('maxOpacity', 0.1, 2, 0)}, false);
                                document.getElementById("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").addEventListener("input", function () {
                                    setOption('maxOpacity', this.value, 2)
                                }, false);
                                //document.getElementById("<?= $_REQUEST['name_w'] ?>_rangemaxOpacity").addEventListener("click", function(){ upSlider('maxOpacity', 0.01, 2, 0.8)}, false);

                                if (!baseQuery.includes("heatmap.php")) {
                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_animation").addEventListener("click", function (evt) {
                                        if (is3DViewOn) {
                                            alert('24H function is not available for 3D map.');
                                            evt.stopPropagation();
                                            evt.preventDefault();
                                        } else {
                                            animateHeatmap()
                                        }
                                    }, false);
                                }
                                document.getElementById("<?= $_REQUEST['name_w'] ?>_prevButt").addEventListener("click", function () {
                                    prevHeatmapPage();
                                }, false);
                                document.getElementById("<?= $_REQUEST['name_w'] ?>_nextButt").addEventListener("click", function () {
                                    nextHeatmapPage();
                                }, false);

                                // CORTI - listener per 3D heatmaps
                                if (is3DViewOn) {
                                    // heatmap controls
                                    $('.heatmap-3d-controls').off();
                                    $('.heatmap-3d-controls').on('click', '.prev', function () {
                                        prevHeatmapPage();

                                        $('.heatmap-3d-controls').find('.time').text(timestampISO3D);
                                        $('.heatmap-3d-controls').find('.title').text(titleHeatamp3DControls);
//                                        set2Dto3DMap();

                                        $('.leaflet-control-container').css('display', 'none');
                                    });
                                    $('.heatmap-3d-controls').on('click', '.next', function () {
                                        nextHeatmapPage();

                                        $('.heatmap-3d-controls').find('.time').text(timestampISO3D);
                                        $('.heatmap-3d-controls').find('.title').text(titleHeatamp3DControls);
//                                        set2Dto3DMap();

                                        $('.leaflet-control-container').css('display', 'none');
                                    });
                                }

                                if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_changeRad").addEventListener("change", function () {
                                        updateChangeRadiusOnZoom(this)
                                    }, false);
                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_estimateRad").addEventListener("change", function () {
                                        computeRadiusOnData(this)
                                    }, false);
                                }

                                if (current_page == 0) {
                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_nextButt").style.visibility = "hidden";
                                } else {
                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_nextButt").style.visibility = "visible";
                                }

                                if (current_page == numHeatmapPages() - 1) {
                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_prevButt").style.visibility = "hidden";
                                } else {
                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_prevButt").style.visibility = "visible";
                                }
                                //    }
                            }
                            setTimeout(checkLegend, 500);

                            return map.legendHeatmapDiv;
                        };

                        function nextHeatmapPage()
                        {
                            animationFlag = false;
                            if (current_page > 0) {
                                current_page--;
                                changeHeatmapPage(current_page);

                                for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                    if (map.eventsOnMap[i].eventType === 'heatmap') {
                                        removeHeatmap(false);
                                        map.eventsOnMap.splice(i, 1);
                                    } else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                        removeHeatmapColorLegend(i, false);
                                        map.eventsOnMap.splice(i, 1);
                                    } else if (map.eventsOnMap[i] !== null && map.eventsOnMap[i] !== undefined) {
                                        map.defaultMapRef.removeLayer(map.eventsOnMap[i]);
                                        map.eventsOnMap.splice(i, 1);
                                    }
                                }

                                if (addMode === 'additive') {
                                    //   if (baseQuery.includes("heatmap.php")) {
                                    // addHeatmapToMap();
                                    addHeatmapFromClient(false);
                                    /*   } else {
                                     // addHeatmapFromWMSClient();        // TBD
                                     }*/
                                }
                                if (addMode === 'exclusive') {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;

                                    //Remove WidgetAlarm active pins
                                    $.event.trigger({
                                        type: "removeAlarmPin",
                                    });
                                    //Remove WidgetEvacuationPlans active pins
                                    $.event.trigger({
                                        type: "removeEvacuationPlanPin",
                                    });
                                    //Remove WidgetEvents active pins
                                    $.event.trigger({
                                        type: "removeEventFIPin",
                                    });
                                    //Remove WidgetResources active pins
                                    $.event.trigger({
                                        type: "removeResourcePin",
                                    });
                                    //Remove WidgetOperatorEvents active pins
                                    $.event.trigger({
                                        type: "removeOperatorEventPin",
                                    });
                                    //Remove WidgetTrafficEvents active pins
                                    $.event.trigger({
                                        type: "removeTrafficEventPin",
                                    });
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                        maxZoom: 18
                                    }).addTo(map.defaultMapRef);

                                    addHeatmapFromClient(false);
                                }

                            }
                        }

                        function animateHeatmap()
                        {
                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if (map.eventsOnMap[i].eventType === 'heatmap') {
                                    removeHeatmap(false);
                                    map.eventsOnMap.splice(i, 1);
                                } else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                    removeHeatmapColorLegend(i, false);
                                    map.eventsOnMap.splice(i, 1);
                                } else if (map.eventsOnMap[i] !== null && map.eventsOnMap[i] !== undefined) {
                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i]);
                                    map.eventsOnMap.splice(i, 1);
                                }
                            }
                            if (animationFlag === false) {
                                animationFlag = true;
                                addHeatmapFromClient(animationFlag);
                            } else {
                                animationFlag = false;
                                for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                    if (map.eventsOnMap[i].eventType === 'heatmap') {
                                        removeHeatmap(false);
                                        //    removeHeatmapColorLegend(i, false);
                                        map.eventsOnMap.splice(i, 1);
                                    } /*else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                     removeHeatmapColorLegend(i, false);
                                     map.eventsOnMap.splice(i, 1);
                                     }*/
                                }
                                addHeatmapFromClient(animationFlag);
                            }
                        }

                        //   window.nextHeatmapPage = function()
                        function prevHeatmapPage()
                        {
                            animationFlag = false;
                            if (current_page < numHeatmapPages() - 1) {
                                current_page++;
                                changeHeatmapPage(current_page);

                                if (!is3DViewOn) {
                                    for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                        if (map.eventsOnMap[i].eventType === 'heatmap') {
                                            removeHeatmap(false);
                                            map.eventsOnMap.splice(i, 1);
                                        } else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                            removeHeatmapColorLegend(i, false);
                                            map.eventsOnMap.splice(i, 1);
                                        } else if (map.eventsOnMap[i] !== null && map.eventsOnMap[i] !== undefined) {
                                            map.defaultMapRef.removeLayer(map.eventsOnMap[i]);
                                            map.eventsOnMap.splice(i, 1);
                                        }
                                    }
                                }

                                if (addMode === 'additive') {
                                    //   if (baseQuery.includes("heatmap.php")) {
                                    // addHeatmapToMap();
                                    addHeatmapFromClient(false);
                                    /*   } else {
                                     // addHeatmapFromWMSClient();        // TBD
                                     }*/
                                }
                                if (addMode === 'exclusive') {
                                    map.defaultMapRef.eachLayer(function (layer) {
                                        map.defaultMapRef.removeLayer(layer);
                                    });
                                    map.eventsOnMap.length = 0;

                                    //Remove WidgetAlarm active pins
                                    $.event.trigger({
                                        type: "removeAlarmPin",
                                    });
                                    //Remove WidgetEvacuationPlans active pins
                                    $.event.trigger({
                                        type: "removeEvacuationPlanPin",
                                    });
                                    //Remove WidgetEvents active pins
                                    $.event.trigger({
                                        type: "removeEventFIPin",
                                    });
                                    //Remove WidgetResources active pins
                                    $.event.trigger({
                                        type: "removeResourcePin",
                                    });
                                    //Remove WidgetOperatorEvents active pins
                                    $.event.trigger({
                                        type: "removeOperatorEventPin",
                                    });
                                    //Remove WidgetTrafficEvents active pins
                                    $.event.trigger({
                                        type: "removeTrafficEventPin",
                                    });
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                        maxZoom: 18
                                    }).addTo(map.defaultMapRef);

                                    addHeatmapFromClient(false);
                                }

                            }
                        }

                        function prepareCustomMarkerForPointAndClick(dataObj, color1, color2)
                        {
                            var latLngId = dataObj.latitude + "" + dataObj.longitude;
                            latLngId = latLngId.replace(".", "");
                            latLngId = latLngId.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona

                            var popupText = '<h3 class="recreativeEventMapTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + '); text-transform: none;">' + dataObj.mapName + '</h3>';
                            popupText += '<div class="recreativeEventMapBtnContainer"><span data-id="' + latLngId + '" class="recreativeEventMapDetailsBtn recreativeEventMapBtn recreativeEventMapBtnActive" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Heatmap Details</span></div>';

                            popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer" style="height:100px; width:270px;">';

                            popupText += '<table id="' + latLngId + '" class="gisPopupGeneralDataTable" style="width:90%">';
                            //Intestazione
                            popupText += '<thead>';
                            popupText += '<th style="background: ' + color2 + '">Description</th>';
                            popupText += '<th style="background: ' + color2 + '">Value</th>';
                            popupText += '</thead>';

                            //Corpo
                            popupText += '<tbody>';

                            //    var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                            //    var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                            //    var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);

                            var dateTime = new Date(dataObj.dataTime);// Milliseconds to date
                            dateTime = dateTime.getDate() + "\/" + parseInt(dateTime.getMonth() + 1) + "\/" + dateTime.getFullYear() + " " + dateTime.getHours() + ":" + dateTime.getMinutes() + ":" + dateTime.getSeconds();

                            popupText += '<tr><td style="text-align:left; font-size: 12px;">Date & Time:</td><td style="font-size: 12px;">' + dateTime + '</td></tr>';
                            popupText += '<tr><td style="text-align:left; font-size: 12px;">Metric Name:</td><td style="font-size: 12px;">' + dataObj.metricName + '</td></tr>';
                            popupText += '<tr><td style="text-align:left; font-size: 12px;">Heatmap Value:</td><td style="font-size: 12px;">' + dataObj.value + '</td></tr>';
                            popupText += '<tr><td style="text-align:left; font-size: 12px;">Coordinates:</td><td style="font-size: 12px;">' + dataObj.latitude + ', ' + dataObj.longitude + '</td></tr>';

                            return popupText;
                        }

                        //   $('#'+event.target).on('click', function(e) {
                        map.defaultMapRef.on('click', function (e) {
                            // CORTI - evita popup se heatmap sotto edifici 3d
                            if (is3DViewOn) {
                                return;
                            }

                            //    if (map.testMetadata.metadata.file != 1) {
                            var heatmapPointAndClickData = null;
                            //  alert("Click on Map !");
                            var pointAndClickCoord = e.latlng;
                            var pointAndClickLat = pointAndClickCoord.lat.toFixed(5);
                            var pointAndClickLng = pointAndClickCoord.lng.toFixed(5);
                            var pointAndClickApiUrl = "https://heatmap.snap4city.org/interp.php?latitude=" + pointAndClickLat + "&longitude=" + pointAndClickLng + "&dataset=" + map.testMetadata.metadata.mapName + "&date=" + map.testMetadata.metadata.date;
                            $.ajax({
                                url: pointAndClickApiUrl,
                                async: true,
                                success: function (heatmapPointAndClickData) {
                                    var popupData = {};
                                    popupData.mapName = heatmapPointAndClickData.mapName;
                                    popupData.latitude = pointAndClickLat;
                                    popupData.longitude = pointAndClickLng;
                                    popupData.metricName = heatmapPointAndClickData.metricName;
                                    popupData.dataTime = heatmapPointAndClickData.date;
                                    if (heatmapPointAndClickData.value) {
                                        popupData.value = heatmapPointAndClickData.value.toFixed(5);
                                        var customPointAndClickContent = prepareCustomMarkerForPointAndClick(popupData, "#C2D6D6", "#D1E0E0")
                                        //   var pointAndClickPopup = L.popup(customPointAndClickMarker).openOn(map.defaultMapRef);
                            
                                        // CORTI - disable autopan in 3D
                                        let autoPan = true;
                                        if(is3DViewOn){
                                            autoPan = false;
                                        }

                                        var popup = L.popup({ autoPan: autoPan })
                                                .setLatLng(pointAndClickCoord)
                                                .setContent(customPointAndClickContent)
                                                .openOn(map.defaultMapRef);
                                    }
                                },
                                error: function (errorData) {
                                    console.log("Ko Point&Click Heatmap API");
                                    console.log(JSON.stringify(errorData));
                                },
                                // CORTI
                                complete: function(){
                                    editCSSFor3DWidgets();
                                }
                            });
                            //    }
                        });

                        function distance(lat1, lon1, lat2, lon2, unit) {   // unit: 'K' for Kilometers
                            if ((lat1 == lat2) && (lon1 == lon2)) {
                                return 0;
                            } else {
                                var radlat1 = Math.PI * lat1 / 180;
                                var radlat2 = Math.PI * lat2 / 180;
                                var theta = lon1 - lon2;
                                var radtheta = Math.PI * theta / 180;
                                var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                                if (dist > 1) {
                                    dist = 1;
                                }
                                dist = Math.acos(dist);
                                dist = dist * 180 / Math.PI;
                                dist = dist * 60 * 1.1515;
                                if (unit == "K") {
                                    dist = dist * 1.609344
                                }
                                if (unit == "N") {
                                    dist = dist * 0.8684
                                }
                                return dist;
                            }
                        }

                        function getRadius() {
                            var radius;
                            var currentZoom = map.defaultMapRef.getZoom();
                            if (estimateRadiusFlag && estimatedRadius) {
                                metresPerPixel = 40075016.686 * Math.abs(Math.cos(map.defaultMapRef.getCenter().lat * Math.PI / 180)) / Math.pow(2, currentZoom + 8);
                                radius = ((estimatedRadius * 1000) / metresPerPixel) / 50;
                                if (radius > 1000) {

                                } else if (radius > 1) {
                                    if (currentZoom < prevZoom) {
                                        prevZoom = currentZoom;
                                        return radius / 1.2;
                                    } else {
                                        prevZoom = currentZoom;
                                        return radius / 1.2;
                                    }
                                } else {
                                    prevZoom = currentZoom;
                                    return 1;
                                }
                            }
                            if (prevZoom == null) {
                                prevZoom = widgetParameters.zoom;
                            }
                            if (currentZoom === 7) {
                                radius = 1;
                            } else if (currentZoom === 8) {
                                radius = 1;
                            } else if (currentZoom === 9) {
                                radius = 1;
                            } else if (currentZoom === 10) {
                                if (currentZoom > prevZoom) {
                                    radius = 2;
                                } else {
                                    radius = 1;
                                }
                            } else if (currentZoom === 11) {
                                if (currentZoom > prevZoom) {
                                    radius = 3.5;
                                } else {
                                    radius = 2;
                                }
                            } else if (currentZoom === 12) {
                                if (currentZoom > prevZoom) {
                                    radius = 10;
                                } else {
                                    radius = 3.5;
                                }
                            } else if (currentZoom === 13) {
                                if (currentZoom > prevZoom) {
                                    radius = 16;
                                } else {
                                    radius = 10;
                                }
                            } else if (currentZoom === 14) {
                                if (currentZoom > prevZoom) {
                                    radius = 31;
                                } else {
                                    radius = 16;
                                }
                            } else if (currentZoom === 15) {
                                if (currentZoom > prevZoom) {
                                    radius = 60;
                                } else {
                                    radius = 31;
                                }
                            } else if (currentZoom === 16) {
                                if (currentZoom > prevZoom) {
                                    radius = 80;
                                } else {
                                    radius = 60;
                                }
                            } else if (currentZoom === 17) {
                                if (currentZoom > prevZoom) {
                                    radius = 100;
                                } else {
                                    radius = 80;
                                }
                            } else if (currentZoom === 18) {
                                if (currentZoom > prevZoom) {
                                    radius = 130;
                                } else {
                                    radius = 100;
                                }
                            }
                            prevZoom = currentZoom;
                            return radius;
                        }

                        //    map.defaultMapRef.on('zoomstart', function(ev) {
                        map.defaultMapRef.on('zoomend', function (ev) {
                            if (prevZoom === null) {
                                prevZoom = widgetParameters.zoom;
                            }
                            // zoom level changed... adjust heatmap layer options!
                            if (changeRadiusOnZoom === true) {

                                if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP
                                    // INSERIRE CAMBIO SLIDER ZOOM
                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_sliderradius").value = parseFloat(getRadius()).toFixed(1);
                                    setOption('radius', getRadius(), 1);           // MODALITA HEATMAP ON ZOOM
                                }
                            } else {
                                setOption('radius', current_radius, 1);
                            }
                        });


                        function addHeatmapToMap() {
                            animationFlag = false;
                            //    current_page = 0;
                            try {
                                if (map.eventsOnMap.length > 0) {
                                    for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
				    	if (map.eventsOnMap[i] != undefined) {
	                                        if (map.eventsOnMap[i].eventType === 'heatmap') {
	                                            removeHeatmap(true);
	                                            map.eventsOnMap.splice(i, 1);
	                                        } else if (map.eventsOnMap[i].type === 'addHeatmap') {
	                                            removeHeatmapColorLegend(i, true);
	                                            map.eventsOnMap.splice(i, 1);
	                                        } else if (map.eventsOnMap[i] !== null && map.eventsOnMap[i] !== undefined) {
	                                            if (map.eventsOnMap[i].type === 'trafficRealTimeDetails') {
	                                                map.defaultMapRef.removeLayer(map.eventsOnMap[i]);
	                                                map.eventsOnMap.splice(i, 1);
	                                            } else if (map.eventsOnMap[i]._url) {
	                                                if (map.eventsOnMap[i]._url.includes("animate")) {
	                                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i]);
	                                                    map.eventsOnMap.splice(i, 1);
	                                                }
	                                            }
	                                        }
					  }
                                    }
                                }

                                if (!event.passedData.includes("heatmap.php")) {
                                    passedParams = event.passedParams;

                                    var color1 = passedParams.color1;
                                    var color2 = passedParams.color2;
                                    var desc = passedParams.desc;

                                    var loadingDiv = $('<div class="gisMapLoadingDiv"></div>');

                                    if ($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length > 0) {
                                        loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').last());
                                    } else {
                                        loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_map'));
                                    }

                                    loadingDiv.css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - ($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length * loadingDiv.height())) + "px");
                                    loadingDiv.css("left", ($('#<?= $_REQUEST['name_w'] ?>_div').width() - loadingDiv.width()) + "px");

                                    var loadingText = $('<p class="gisMapLoadingDivTextPar">adding <b>' + desc.toLowerCase() + '</b> to map<br><i class="fa fa-circle-o-notch fa-spin" style="font-size: 30px"></i></p>');
                                    var loadOkText = $('<p class="gisMapLoadingDivTextPar"><b>' + desc.toLowerCase() + '</b> added to map<br><i class="fa fa-check" style="font-size: 30px"></i></p>');
                                    var loadKoText = $('<p class="gisMapLoadingDivTextPar">error adding <b>' + desc.toLowerCase() + '</b> to map<br><i class="fa fa-close" style="font-size: 30px"></i></p>');

                                    loadingDiv.css("background", color1);
                                    loadingDiv.css("background", "-webkit-linear-gradient(left top, " + color1 + ", " + color2 + ")");
                                    loadingDiv.css("background", "-o-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                                    loadingDiv.css("background", "-moz-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                                    loadingDiv.css("background", "linear-gradient(to bottom right, " + color1 + ", " + color2 + ")");

                                    // CORTI - evita loadingDiv nella vista 3D
                                    if (!is3DViewOn) {
                                        loadingDiv.show();
                                    }

                                    loadingDiv.append(loadingText);
                                    loadingDiv.css("opacity", 1);

                                    var parHeight = loadingText.height();
                                    var parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                    loadingText.css("margin-top", parMarginTop + "px");
                                }

                                let heatmap = {};
                                heatmap.eventType = "heatmap";
                                baseQuery = event.passedData;
                                let latitude_min = map.defaultMapRef.getBounds()._southWest.lat;
                                let latitude_max = map.defaultMapRef.getBounds()._northEast.lat;
                                let longitude_min = map.defaultMapRef.getBounds()._southWest.lng;
                                let longitude_max = map.defaultMapRef.getBounds()._northEast.lng;
                                let query = "";
                                if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP
                                    //  query = baseQuery + '&limit=30&latitude_min=' + latitude_min + '&latitude_max=' + latitude_max + '&longitude_min=' + longitude_min + '&longitude_max=' + longitude_max;
                                    query = baseQuery + '&latitude_min=' + latitude_min + '&latitude_max=' + latitude_max + '&longitude_min=' + longitude_min + '&longitude_max=' + longitude_max;
                                    query = query.replace("heatmap.php", "heatmap-metadata.php");       // CON QUESTA RIGA SI PREDONO SOLO I METADATI ORA !!!
                                    let metricNameSplit = baseQuery.split("metricName=")[1];
                                } else {
                                    //  let metricNameSplit = baseQuery.split("metricName=")[1];
                                    //  heatmapMetricName = baseQuery.split("metricName=")[1];
                                    //    var datasetNameAux = baseQuery.split("https://wmsserver.snap4city.org/geoserver/Snap4City/wms?service=WMS&layers=")[1];
                                    var datasetNameAux = baseQuery.split("WMS&layers=")[1];
                                    wmsDatasetName = datasetNameAux.split("&metricName=")[0];
                                    query = 'https://heatmap.snap4city.org/heatmap-metadata.php?dataset=' + wmsDatasetName + '&latitude_min=' + latitude_min + '&latitude_max=' + latitude_max + '&longitude_min=' + longitude_min + '&longitude_max=' + longitude_max;
                                }

                                heatmapData = null;
                                $.ajax({
                                    url: query,
                                    async: false,
                                    cache: false,
                                    dataType: "text",
                                    success: function (data) {
                                        heatmapData = JSON.parse(data);
                                    },
                                    error: function (errorData) {
                                        console.log("Ko Heatmap");
                                        console.log(JSON.stringify(errorData));
                                    },
                                    // CORTI
                                    complete: function(){
                                        editCSSFor3DWidgets();
                                    }
                                });

                                //     for (var i = 0; i < heatmapData.length; i++) {
                                //heatmap recommender data
                                /*   map.testData = {
                                 //   max: 8,
                                 data: heatmapData[current_page].data
                                 };  */

                                // Initialize array of Days from metadata
                                daysArray = initDaysArray(heatmapData);

                                //heatmap recommender metadata
                                map.testMetadata = {
                                    //   max: 8,
                                    metadata: heatmapData[current_page].metadata
                                };

                                if (map.testMetadata.metadata.metricName !== undefined) {
                                    heatmapMetricName = map.testMetadata.metadata.metricName
                                } else {
                                    heatmapMetricName = "airTemperature";
                                    mapName = "WMS_PROVA";
                                }

                                if (map.testMetadata.metadata.mapName !== undefined) {
                                    mapName = map.testMetadata.metadata.mapName;
                                } else {
                                    mapName = "WMS_PROVA";
                                }

                                if (map.testMetadata.metadata.date !== undefined) {
                                    mapDate = map.testMetadata.metadata.date;
                                } else {
                                    mapDate = "DATA";
                                }

                                $.ajax({
                                    url: "../controllers/getHeatmapRange.php",
                                    type: "GET",
                                    data: {
                                        metricName: heatmapMetricName
                                    },
                                    async: true,
                                    dataType: 'json',
                                    success: function (data) {
                                        try {
                                            if (data['detail'] == "Ok") {

                                                //  if (data['heatmapRange'].length > 1) {
                                                if (data['heatmapRange'][0]) {
                                                    heatmapRange = data['heatmapRange'];
                                                    initHeatmapLayer(heatmapRange);   // OLD-API
                                                    // Set current_radius come variabile globale per essere sincronizzata attraverso le varie azioni (zoom ecc...)
                                                    if (current_radius == null) {
                                                        current_radius = map.cfg.radius;
                                                    }
                                                    if (current_opacity == null) {
                                                        current_opacity = map.cfg.maxOpacity;
                                                    }

                                                } else {
                                                    heatmapRange = [];
                                                }

                                                if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP
                                                    //    if (event.passedData.includes("heatmap.php")) {
                                                    addHeatmapFromClient(false);

                                                } else {                    // NEW HEATMAP  FIRST INSTANTIATION
                                                    // CORTI - Pane
                                                    if (map.defaultMapRef) {
                                                        map.defaultMapRef.createPane('Snap4City:' + wmsDatasetName);
                                                        map.defaultMapRef.getPane('Snap4City:' + wmsDatasetName).style.zIndex = 420;
                                                    }

                                                    //   if (animationFlag === false) {
                                                    //   var timestampISO = "2019-01-23T20:20:15.000Z";
                                                    var timestamp = map.testMetadata.metadata.date;
                                                    var timestampISO = timestamp.replace(" ", "T") + ".000Z";

                                                    // CORTI - con la mappa 3d la heatmap è nativa
                                                    wmsLayer = L.tileLayer.wms("https://wmsserver.snap4city.org/geoserver/Snap4City/wms", {
                                                        layers: 'Snap4City:' + wmsDatasetName,
                                                        format: 'image/png',
                                                        crs: L.CRS.EPSG4326,
                                                        transparent: true,
                                                        opacity: current_opacity,
                                                        time: timestampISO,
                                                        //  bbox: [24.7926004025304,60.1025194986424,25.1905923952885,60.2516802986263],
                                                        tiled: true, // TESTARE COME ANTWERP ??
                                                        //  attribution: "IGN ©"
                                                        pane: 'Snap4City:' + wmsDatasetName,
                                                    }).addTo(map.defaultMapRef);
                                                    console.log('Snap4City:' + wmsDatasetName);
                                                    // CORTI - inclusione heatmap nella mappa 3d
                                                    if (is3DViewOn) {
                                                        setOption('maxOpacity', 0, 2);
                                                        timestampISO3D = timestampISO;
                                                        titleHeatamp3DControls = wmsDatasetName;
                                                        let tileSize = 512;
                                                        if (wmsDatasetName == "GRALheatmap") {
                                                            tileSize = 256;
                                                        }
                                                        heatmapLayer3D = map.default3DMapRef.addMapTiles('https://wmsserver.snap4city.org/geoserver/Snap4City/wms?service=WMS&request=GetMap&layers=Snap4City%3A' + wmsDatasetName + '&styles=&format=image%2Fpng&transparent=true&version=1.1.1&time=' + timestampISO + '&tiled=true&width=' + tileSize + '&height=' + tileSize + '&srs=EPSG%3A4326/{z}/{x}/{y}');
                                                    }

                                                    //    current_opacity = 0.5;

                                                    // add legend to map
                                                    map.legendHeatmap.addTo(map.defaultMapRef);
                                                    map.eventsOnMap.push(heatmap);
                                                    var mapControlsContainer = document.getElementsByClassName("leaflet-control")[0];

                                                    var heatmapLegendColors = L.control({position: 'bottomleft'});

                                                    heatmapLegendColors.onAdd = function (map) {

                                                        var div = L.DomUtil.create('div', 'info legend'),
                                                                grades = ["Legend"];
                                                        //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                                        var legendImgPath = heatmapRange[0].iconPath; // OLD-API
                                                        div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';    /// OLD-API
                                                        return div;
                                                    };

                                                    heatmapLegendColors.addTo(map.defaultMapRef);
                                                    //  map.eventsOnMap.push(heatmap);

                                                    event.legendColors = heatmapLegendColors;
                                                    map.eventsOnMap.push(event);

                                                    if (loadingDiv) {
                                                        loadingDiv.empty();
                                                        loadingDiv.append(loadOkText);

                                                        parHeight = loadOkText.height();
                                                        parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                        loadOkText.css("margin-top", parMarginTop + "px");

                                                        setTimeout(function () {
                                                            loadingDiv.css("opacity", 0);
                                                            setTimeout(function () {
                                                                loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                                    $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                });
                                                                loadingDiv.remove();
                                                            }, 350);
                                                        }, 1000);
                                                    }
                                                    //  } else {

                                                    //  }    // FINE ELSE ANIMATION
                                                }    // FINE ELSE NEW WMS HEATMAP FIRST INSTANTIATION

                                            } else {
                                                console.log("Ko Heatmap");
                                                console.log(JSON.stringify(errorData));
                                                if (loadingDiv) {
                                                    loadingDiv.empty();
                                                    loadingDiv.append(loadKoText);

                                                    parHeight = loadKoText.height();
                                                    parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                    loadKoText.css("margin-top", parMarginTop + "px");

                                                    setTimeout(function () {
                                                        loadingDiv.css("opacity", 0);
                                                        setTimeout(function () {
                                                            loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                                $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                            });
                                                            loadingDiv.remove();

                                                            // CORTI,
                                                            editCSSFor3DWidgets();

                                                        }, 350);
                                                    }, 1000);
                                                }
                                            }
                                        } catch (err) {
                                            if (loadingDiv) {
                                                loadingDiv.empty();
                                                loadingDiv.append(loadKoText);

                                                parHeight = loadKoText.height();
                                                parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                loadKoText.css("margin-top", parMarginTop + "px");
                                                console.log("Error: " + err);
                                                setTimeout(function () {
                                                    loadingDiv.css("opacity", 0);
                                                    setTimeout(function () {
                                                        loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                            $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                        });
                                                        loadingDiv.remove();

                                                        // CORTI,
                                                        editCSSFor3DWidgets();

                                                    }, 350);
                                                }, 1000);
                                            }
                                        }
                                    },
                                    error: function (errorData) {
                                        console.log("Ko Heatmap");
                                        console.log(JSON.stringify(errorData));
                                        if (loadingDiv) {
                                            loadingDiv.empty();
                                            loadingDiv.append(loadKoText);

                                            parHeight = loadKoText.height();
                                            parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                            loadKoText.css("margin-top", parMarginTop + "px");

                                            setTimeout(function () {
                                                loadingDiv.css("opacity", 0);
                                                setTimeout(function () {
                                                    loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                        $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                    });
                                                    loadingDiv.remove();
                                                }, 350);
                                            }, 1000);
                                        }
                                    },
                                    // CORTI
                                    complete: function(){
                                        editCSSFor3DWidgets();
                                    }
                                });
                            } catch (err) {
                                if (loadingDiv) {
                                    loadingDiv.empty();
                                    loadingDiv.append(loadKoText);

                                    parHeight = loadKoText.height();
                                    parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                    loadKoText.css("margin-top", parMarginTop + "px");
                                    console.log("Error: " + err);
                                    setTimeout(function () {
                                        loadingDiv.css("opacity", 0);
                                        setTimeout(function () {
                                            loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                            });
                                            loadingDiv.remove();
                                        }, 350);
                                    }, 1000);
                                }
                            }
                        }

                        //   window.addHeatmapFromClient = function(animationFlag) {
                        function addHeatmapFromClient(animationFlag) {

                            let heatmap = {};
                            heatmap.eventType = "heatmap";

                            /*   map.testData = {
                             //   max: 8,
                             data: heatmapData[current_page].data
                             };  */

                            //heatmap recommender metadata

                            passedParams = event.passedParams;

                            var color1 = passedParams.color1;
                            var color2 = passedParams.color2;
                            var desc = passedParams.desc;

                            var loadingDiv = $('<div class="gisMapLoadingDiv"></div>');

                            if ($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length > 0) {
                                loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').last());
                            } else {
                                loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_map'));
                            }

                            loadingDiv.css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - ($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length * loadingDiv.height())) + "px");
                            loadingDiv.css("left", ($('#<?= $_REQUEST['name_w'] ?>_div').width() - loadingDiv.width()) + "px");

                            var loadingText = $('<p class="gisMapLoadingDivTextPar">adding <b>' + desc.toLowerCase() + '</b> to map<br><i class="fa fa-circle-o-notch fa-spin" style="font-size: 30px"></i></p>');
                            var loadOkText = $('<p class="gisMapLoadingDivTextPar"><b>' + desc.toLowerCase() + '</b> added to map<br><i class="fa fa-check" style="font-size: 30px"></i></p>');
                            var loadKoText = $('<p class="gisMapLoadingDivTextPar">error adding <b>' + desc.toLowerCase() + '</b> to map<br><i class="fa fa-close" style="font-size: 30px"></i></p>');

                            loadingDiv.css("background", color1);
                            loadingDiv.css("background", "-webkit-linear-gradient(left top, " + color1 + ", " + color2 + ")");
                            loadingDiv.css("background", "-o-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                            loadingDiv.css("background", "-moz-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                            loadingDiv.css("background", "linear-gradient(to bottom right, " + color1 + ", " + color2 + ")");

                            // CORTI - evita loadingDiv nella vista 3D
                            if (!is3DViewOn) {
                                loadingDiv.show();
                            }

                            loadingDiv.append(loadingText);
                            loadingDiv.css("opacity", 1);

                            var parHeight = loadingText.height();
                            var parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                            loadingText.css("margin-top", parMarginTop + "px");

                            let latitude_min = map.defaultMapRef.getBounds()._southWest.lat;
                            let latitude_max = map.defaultMapRef.getBounds()._northEast.lat;
                            let longitude_min = map.defaultMapRef.getBounds()._southWest.lng;
                            let longitude_max = map.defaultMapRef.getBounds()._northEast.lng;
                            let query = "";
                            if (event.passedData.includes("heatmap.php")) {    // OLD HEATMAP
                                //  query = baseQuery + '&limit=30&latitude_min=' + latitude_min + '&latitude_max=' + latitude_max + '&longitude_min=' + longitude_min + '&longitude_max=' + longitude_max;
                                query = event.passedData + '&latitude_min=' + latitude_min + '&latitude_max=' + latitude_max + '&longitude_min=' + longitude_min + '&longitude_max=' + longitude_max;
                                query = query.replace("heatmap.php", "heatmap-metadata.php");       // CON QUESTA RIGA SI PREDONO SOLO I METADATI ORA !!!
                                let metricNameSplit = event.passedData.split("metricName=")[1];
                            } else {
                                //  let metricNameSplit = baseQuery.split("metricName=")[1];
                                //  heatmapMetricName = baseQuery.split("metricName=")[1];
                                //    var datasetNameAux = baseQuery.split("https://wmsserver.snap4city.org/geoserver/Snap4City/wms?service=WMS&layers=")[1];
                                var datasetNameAux = event.passedData.split("WMS&layers=")[1];
                                wmsDatasetName = datasetNameAux.split("&metricName=")[0];
                                query = 'https://heatmap.snap4city.org/heatmap-metadata.php?dataset=' + wmsDatasetName + '&latitude_min=' + latitude_min + '&latitude_max=' + latitude_max + '&longitude_min=' + longitude_min + '&longitude_max=' + longitude_max;
                            }

                            heatmapData = null;
                            $.ajax({
                                url: query,
                                async: false,
                                cache: false,
                                dataType: "text",
                                success: function (data) {
                                    heatmapData = JSON.parse(data);
                                },
                                error: function (errorData) {
                                    console.log("Ko Heatmap");
                                    console.log(JSON.stringify(errorData));
                                },
                                // CORTI
                                complete: function(){
                                    editCSSFor3DWidgets();
                                }
                            });

                            map.testMetadata = {
                                //   max: 8,
                                metadata: heatmapData[current_page].metadata
                            };

                            if (map.testMetadata.metadata.metricName !== undefined) {
                                heatmapMetricName = map.testMetadata.metadata.metricName
                            } else {
                                heatmapMetricName = "airTemperature";
                                mapName = "WMS_PROVA";
                            }

                            if (map.testMetadata.metadata.mapName !== undefined) {
                                mapName = map.testMetadata.metadata.mapName;
                            } else {
                                mapName = "WMS_PROVA";
                            }

                            if (map.testMetadata.metadata.date !== undefined) {
                                mapDate = map.testMetadata.metadata.date;
                            } else {
                                mapDate = "DATA";
                            }

                            $.ajax({
                                url: "../controllers/getHeatmapRange.php",
                                type: "GET",
                                data: {
                                    metricName: heatmapMetricName
                                },
                                async: true,
                                dataType: 'json',
                                success: function (data) {
                                    try {
                                        if (data['detail'] == "Ok") {
                                            //  if (data['heatmapRange'].length > 1) {

                                            if (data['heatmapRange'][0]) {
                                                heatmapRange = data['heatmapRange'];
                                                initHeatmapLayer(heatmapRange);   // OLD-API
                                                // Gestione della sincronia dei check-box del cambio raggio on zoom e computo raggio su base dati dopo aggiornamento legenda

                                            } else {
                                                heatmapRange = [];
                                            }

                                            if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP


                                                let dataQuery = "https://heatmap.snap4city.org/data/" + mapName + "/" + heatmapMetricName + "/" + mapDate.replace(" ", "T") + "Z/0";

                                                $.ajax({
                                                    url: dataQuery,
                                                    type: "GET",
                                                    data: {
                                                    },
                                                    async: true,
                                                    cache: false,
                                                    dataType: 'json',
                                                    success: function (heatmapResData) {
                                                        if (heatmapResData['data']) {
                                                            //    heatmapRange = heatmapData['heatmapRange'];
                                                            initHeatmapLayer(heatmapRange);   // OLD-API
                                                            // Set current_radius come variabile globale per essere sincronizzata attraverso le varie azioni (zoom ecc...)
                                                            if (current_radius == null) {
                                                                current_radius = map.cfg.radius;
                                                            }
                                                            if (current_opacity == null) {
                                                                current_opacity = map.cfg.maxOpacity;
                                                            }

                                                        } else {
                                                            heatmapRange = [];
                                                        }

                                                        if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP
                                                            map.testData = {
                                                                //   max: 8,
                                                                data: heatmapResData.data
                                                            };

                                                            //heatmap recommender metadata
                                                            map.testMetadata = {
                                                                //   max: 8,
                                                                metadata: heatmapResData.metadata
                                                            };

                                                            if (heatmapRange[0].range1Inf == null) {
                                                                if (heatmapMetricName == "EAQI" || heatmapMetricName == "CAQI") {
                                                                    heatmapRange[0].range1Inf = heatmapRange[0].range4Inf;
                                                                } else if (heatmapMetricName == "CO" || heatmapMetricName == "Benzene") {
                                                                    heatmapRange[0].range1Inf = heatmapRange[0].range3Inf;
                                                                    heatmapRange[0].range10Inf = heatmapRange[0].range8Inf;
                                                                }
                                                            }
                                                            map.heatmapLayer.setData({max: heatmapRange[0].range10Inf, min: heatmapRange[0].range1Inf, data: map.testData.data});
                                                            map.defaultMapRef.addLayer(map.heatmapLayer);   // OLD HEATMAP
                                                            //    if (estimateRadiusFlag === true) {
                                                            var distArray = [];             // MODALITA HEATMAP ON DATA DISTANCE
                                                            if (heatmapResData.length > 20) {
                                                                for (k = 0; k < 20; k++) {
                                                                    distArray[k] = distance(heatmapResData[k].latitude, heatmapResData[k].latitude, heatmapResData[k + 1].latitude, heatmapResData[k + 1].latitude, "K");
                                                                }

                                                                var sum = 0;
                                                                for (var i = 0; i < distArray.length; i++) {
                                                                    sum += distArray[i];
                                                                }
                                                                estimatedRadius = sum / distArray.length;
                                                                if (estimatedRadius <= 1) {
                                                                    estimatedRadius = 2;
                                                                }
                                                                //   if (estimateRadiusFlag === true) {
                                                            } else {
                                                                estimatedRadius = current_radius;
                                                            }

                                                            metresPerPixel = 40075016.686 * Math.abs(Math.cos(map.defaultMapRef.getCenter().lat * Math.PI / 180)) / Math.pow(2, map.defaultMapRef.getZoom() + 8);
                                                            var initRadius = ((estimatedRadius * 1000) / metresPerPixel) / 50;
                                                            if (current_page == 0) {
                                                                setOption('radius', initRadius.toFixed(1), 1);
                                                            } else {
                                                                setOption('radius', current_radius.toFixed(1), 1);
                                                            }
                                                            //   }
                                                        } else {                    // NEW HEATMAP
                                                            //   var timestampISO = "2019-01-23T20:20:15.000Z";
                                                            var timestamp = map.testMetadata.metadata.date;
                                                            var timestampISO = timestamp.replace(" ", "T") + ".000Z";

                                                            // CORTI - con la mappa 3d la heatmap è nativa
                                                            wmsLayer = L.tileLayer.wms("https://wmsserver.snap4city.org/geoserver/Snap4City/wms", {
                                                                layers: 'Snap4City:' + wmsDatasetName,
                                                                format: 'image/png',
                                                                crs: L.CRS.EPSG4326,
                                                                transparent: true,
                                                                opacity: current_opacity,
                                                                time: timestampISO,
                                                                //  bbox: [24.7926004025304,60.1025194986424,25.1905923952885,60.2516802986263],
                                                                tiled: true
                                                                        //  attribution: "IGN ©"
                                                            }).addTo(map.defaultMapRef);
                                                            //     current_opacity = 0.5;

                                                            // CORTI - inclusione heatmap nella mappa 3d
                                                            if (is3DViewOn) {
                                                                setOption('maxOpacity', 0, 2);
                                                                timestampISO3D = timestampISO;
                                                                titleHeatamp3DControls = wmsDatasetName;
                                                                let tileSize = 512;
                                                                if (wmsDatasetName == "GRALheatmap") {
                                                                    tileSize = 256;
                                                                }
                                                                heatmapLayer3D = map.default3DMapRef.addMapTiles('https://wmsserver.snap4city.org/geoserver/Snap4City/wms?service=WMS&request=GetMap&layers=Snap4City%3A' + wmsDatasetName + '&styles=&format=image%2Fpng&version=1.1.1&time=' + timestampISO + '&tiled=true&width=' + tileSize + '&height=' + tileSize + '&srs=EPSG%3A4326/{z}/{x}/{y}');
                                                            }

                                                        }

                                                        // add legend to map
                                                        map.legendHeatmap.addTo(map.defaultMapRef);
                                                        map.eventsOnMap.push(heatmap);
                                                        var mapControlsContainer = document.getElementsByClassName("leaflet-control")[0];

                                                        //    var legendImgPath = heatmapRange[0].iconPath;
                                                        //     div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';
                                                        var heatmapLegendColors = L.control({position: 'bottomleft'});

                                                        heatmapLegendColors.onAdd = function (map) {

                                                            var div = L.DomUtil.create('div', 'info legend'),
                                                                    grades = ["Legend"];
                                                            //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                                            var legendImgPath = heatmapRange[0].iconPath; // OLD-API
                                                            div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';    /// OLD-API
                                                            return div;
                                                        };

                                                        heatmapLegendColors.addTo(map.defaultMapRef);
                                                        //    map.eventsOnMap.push(heatmap);

                                                        event.legendColors = heatmapLegendColors;
                                                        map.eventsOnMap.push(event);

                                                        if (changeRadiusOnZoom) {
                                                            $('#<?= $_REQUEST['name_w'] ?>_changeRad').prop('checked', true);
                                                            if (estimateRadiusFlag) {
                                                                $('#<?= $_REQUEST['name_w'] ?>_changeRad').prop('disabled', true);
                                                            }
                                                        }

                                                        if (estimateRadiusFlag) {
                                                            $('#<?= $_REQUEST['name_w'] ?>_estimateRad').prop('checked', true);
                                                            $('#<?= $_REQUEST['name_w'] ?>_estimateRad').prop('disabled', false);
                                                        } else {
                                                            $('#<?= $_REQUEST['name_w'] ?>_estimateRad').prop('disabled', false);
                                                        }

                                                        if (loadingDiv) {
                                                            loadingDiv.empty();
                                                            loadingDiv.append(loadOkText);

                                                            parHeight = loadOkText.height();
                                                            parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                            loadOkText.css("margin-top", parMarginTop + "px");

                                                            setTimeout(function () {
                                                                loadingDiv.css("opacity", 0);
                                                                setTimeout(function () {
                                                                    loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                                        $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                    });
                                                                    loadingDiv.remove();
                                                                }, 350);
                                                            }, 1000);
                                                        }
                                                    },
                                                    error: function (errorData) {
                                                        console.log("Ko Heatmap");
                                                        console.log(JSON.stringify(errorData));
                                                        if (loadingDiv) {
                                                            loadingDiv.empty();
                                                            loadingDiv.append(loadKoText);

                                                            parHeight = loadKoText.height();
                                                            parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                            loadKoText.css("margin-top", parMarginTop + "px");

                                                            setTimeout(function () {
                                                                loadingDiv.css("opacity", 0);
                                                                setTimeout(function () {
                                                                    loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                                        $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                    });
                                                                    loadingDiv.remove();
                                                                }, 350);
                                                            }, 1000);
                                                        }
                                                    },
                                                    // CORTI
                                                    complete: function(){
                                                        editCSSFor3DWidgets();
                                                    }
                                                });


                                            } else {
                                                if (animationFlag === false) {
                                                    // NEW HEATMAP
                                                    var timestamp = map.testMetadata.metadata.date;
                                                    var timestampISO = timestamp.replace(" ", "T") + ".000Z";

                                                    // CORTI - con la mappa 3d la heatmap è nativa
                                                    wmsLayer = L.tileLayer.wms("https://wmsserver.snap4city.org/geoserver/Snap4City/wms", {
                                                        layers: 'Snap4City:' + wmsDatasetName,
                                                        format: 'image/png',
                                                        crs: L.CRS.EPSG4326,
                                                        transparent: true,
                                                        opacity: current_opacity,
                                                        time: timestampISO,
                                                        //  bbox: [24.7926004025304,60.1025194986424,25.1905923952885,60.2516802986263],
                                                        tiled: true
                                                                //  attribution: "IGN ©"
                                                    }).addTo(map.defaultMapRef);

                                                    // CORTI - inclusione heatmap nella mappa 3d
                                                    if (is3DViewOn) {
                                                        setOption('maxOpacity', 0, 2);
                                                        timestampISO3D = timestampISO;
                                                        titleHeatamp3DControls = wmsDatasetName;
                                                        let tileSize = 512;
                                                        if (wmsDatasetName == "GRALheatmap") {
                                                            tileSize = 256;
                                                        }
                                                        heatmapLayer3D = map.default3DMapRef.addMapTiles('https://wmsserver.snap4city.org/geoserver/Snap4City/wms?service=WMS&request=GetMap&layers=Snap4City%3A' + wmsDatasetName + '&styles=&format=image%2Fpng&transparent=true&version=1.1.1&time=' + timestampISO + '&tiled=true&width=' + tileSize + '&height=' + tileSize + '&srs=EPSG%3A4326/{z}/{x}/{y}');
                                                    }

                                                    // add legend to map
                                                    map.legendHeatmap.addTo(map.defaultMapRef);
                                                    var heatmapLegendColors = L.control({position: 'bottomleft'});

                                                    heatmapLegendColors.onAdd = function (map) {

                                                        var div = L.DomUtil.create('div', 'info legend'),
                                                                grades = ["Legend"];
                                                        //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                                        var legendImgPath = heatmapRange[0].iconPath;         // OLD-API
                                                        div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';    // OLD-API
                                                        return div;
                                                    };

                                                    heatmapLegendColors.addTo(map.defaultMapRef);
                                                    map.eventsOnMap.push(heatmap);
                                                    event.legendColors = heatmapLegendColors;
                                                    map.eventsOnMap.push(event);

                                                    if (loadingDiv) {
                                                        loadingDiv.empty();
                                                        loadingDiv.append(loadOkText);

                                                        parHeight = loadOkText.height();
                                                        parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                        loadOkText.css("margin-top", parMarginTop + "px");

                                                        setTimeout(function () {
                                                            loadingDiv.css("opacity", 0);
                                                            setTimeout(function () {
                                                                loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                                    $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                });
                                                                loadingDiv.remove();
                                                            }, 350);
                                                        }, 1000);
                                                    }
                                                } else {
                                                    // ANIMATION WMS HEATMAP

                                                    var animationCurrentDayTimestamp = [];
                                                    var animationCurrentDayFwdTimestamp = [];
                                                    var animationCurrentDayBckwdTimestamp = [];
                                                    var animationStringTimestamp = "";
                                                    var timestamp = map.testMetadata.metadata.date;
                                                    //    var timestampISO = timestamp.replace(" ", "T") + ".000Z";
                                                    var day = timestamp.substring(0, 10);
                                                    if (current_page == 0) {
                                                        var offsetFwd = current_page;
                                                        while (heatmapData[offsetFwd].metadata['date'].substring(0, 10) == day) {
                                                            animationCurrentDayFwdTimestamp.push(heatmapData[offsetFwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                            offsetFwd++;
                                                            if (offsetFwd > numHeatmapPages() - 1) {
                                                                break;
                                                            }
                                                        }
                                                    } else if (current_page == numHeatmapPages() - 1) {
                                                        var offsetBckwd = current_page - 1;
                                                        while (heatmapData[offsetBckwd].metadata['date'].substring(0, 10) == day) {
                                                            animationCurrentDayBckwdTimestamp.push(heatmapData[offsetBckwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                            offsetBckwd--;
                                                            if (offsetBckwd < 0) {
                                                                break;
                                                            }
                                                        }
                                                    } else {
                                                        var offsetFwd = current_page;
                                                        while (heatmapData[offsetFwd].metadata['date'].substring(0, 10) == day) {
                                                            animationCurrentDayFwdTimestamp.push(heatmapData[offsetFwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                            offsetFwd++;
                                                            if (offsetFwd > numHeatmapPages() - 1) {
                                                                break;
                                                            }
                                                        }
                                                        var offsetBckwd = current_page - 1;
                                                        while (heatmapData[offsetBckwd].metadata['date'].substring(0, 10) == day) {
                                                            animationCurrentDayBckwdTimestamp.push(heatmapData[offsetBckwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                            offsetBckwd--;
                                                            if (offsetBckwd < 0) {
                                                                break;
                                                            }
                                                        }
                                                    }

                                                    /*     if (animationCurrentDayFwdTimestamp.length == 0) {
                                                     animationCurrentDayTimestamp = animationCurrentDayBckwdTimestamp;
                                                     } else if (animationCurrentDayBckwdTimestamp.length == 0) {
                                                     animationCurrentDayTimestamp = animationCurrentDayFwdTimestamp;
                                                     } else {*/
                                                    animationCurrentDayTimestamp = animationCurrentDayFwdTimestamp.reverse().concat(animationCurrentDayBckwdTimestamp);
                                                    //    animationCurrentDayTimestamp = animationCurrentDayTimestamp.reverse();
                                                    animationStringTimestamp = animationCurrentDayTimestamp.join(",");
                                                    //  }


                                                    var bboxJson = {};
                                                    $.ajax({
                                                        url: "https://heatmap.snap4city.org/bbox.php?layer=" + map.testMetadata.metadata.mapName,
                                                        type: "GET",
                                                        async: false,
                                                        dataType: 'json',
                                                        success: function (resultBbox) {
                                                            bboxJson = resultBbox;
                                                        },
                                                        error: function (errbbox) {
                                                            alert("Error in retrieving bounding box for current heatmap: " + mapName);
                                                            console.log(errbbox);
                                                        },
                                                        // CORTI
                                                        complete: function(){
                                                            editCSSFor3DWidgets();
                                                        }
                                                    });

                                                    /*    var bboxPage = "https://wmsserver.snap4city.org/"
                                                     var bboxHtmlContent = "";
                                                     $.get("test.php", function(htmlData){
                                                     bboxHtmlContent = htmlData;
                                                     });  */

                                                    /*       var args = {
                                                     
                                                     // reference to your leaflet map
                                                     map: map.defaultMapRef,
                                                     
                                                     // WMS endpoint
                                                     url: 'https://wmsserver.snap4city.org/geoserver/Snap4City/wms',
                                                     
                                                     // time slices to create (u probably want more than 2)
                                                     times: ["2019-04-18T11:06:18.000Z", "2019-04-18T09:06:18.000Z", "2019-04-18T07:06:18.000Z", "2019-04-18T05:06:18.000Z", "2019-04-18T03:06:18.000Z", "2019-04-18T01:06:18.000Z"],
                                                     
                                                     // the bounds for the entire target WMS layer
                                                     bbox: ["24.90215", "60.1615000000001", "24.98005", "60.1959"],
                                                     
                                                     // how long to show each frame in the animation
                                                     timeoutMs: 300,
                                                     
                                                     frames: [
                                                     {
                                                     "time": "2019-04-18T11:06:18.000Z",
                                                     "img": "https://heatmap.snap4city.org/base64.php?layer="+wmsDatasetName+"&date=20190418T110618Z"
                                                     // "img": "SUkqAAgAAAATAAABAwABAAAAJwAAAAEBAwABAAAAJwAAAAIBAwAEAAAA8gAAAAMBAwABAAAACAAAAAYBAwABAAAAAgAAABUBAwABAAAABAAAABwBAwABAAAAAQAAAD0BAwABAAAAAQAAAEIBAwABAAAAAAEAAEMBAwABAAAAAAEAAEQBBAABAAAAogEAAEUBBAABAAAAwwEAAFIBAwABAAAAAgAAAFMBAwAEAAAA+gAAAA6DDAADAAAAAgEAAIKEDAAGAAAAGgEAAK+HAwAgAAAASgEAALCHDAACAAAAigEAALGHAgAIAAAAmgEAAAAAAAAIAAgACAAIAAEAAQABAAEAg3TCSFm1Xz+4fMraslFMPwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA20/G+DDnOEAug5NwBhlOQAAAAAAAAAAAAQABAAAABwAABAAAAQACAAEEAAABAAEAAAgAAAEA5hABCLGHBwAAAAYIAAABAI4jCQiwhwEAAQALCLCHAQAAAIhtdJYdpHJAAAAAQKZUWEFXR1MgODR8AHja7djRCcJAEEXRrdaeUssUNyKYD8HAohHMvnNkGhi4s8Qxfq9vowcQ59G+/iG3fTcA9G8jkNm+GwDZ7bsBEN5/6R/i2q/XsSnIbF//ENJ/HY+NQWb7+oeF+6+JaTcAUtvfxwbhou3XZO8H7esfgvrv92OTkNm+/iG3fTcAFu5/pv39B2S2r3/Ibd8NgDX6/7R9/UNu+24AXLf/M9rXP+S2vz0HyGxf//Af/fdc39590L7vfdC+//kht3/dg/Y1D8HtAxH96x7y2tc8ZPave8hr38Zglfg1D95+vUPq228ZEGRrzQMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHzhDpylbKQ="
                                                     },
                                                     {
                                                     "time": "2019-04-18T09:06:18.000Z",
                                                     "img": "https://heatmap.snap4city.org/base64.php?layer="+wmsDatasetName+"&date=20190418T090618Z"
                                                     //   "img": "SUkqAAgAAAATAAABAwABAAAAJwAAAAEBAwABAAAAJwAAAAIBAwAEAAAA8gAAAAMBAwABAAAACAAAAAYBAwABAAAAAgAAABUBAwABAAAABAAAABwBAwABAAAAAQAAAD0BAwABAAAAAQAAAEIBAwABAAAAAAEAAEMBAwABAAAAAAEAAEQBBAABAAAAogEAAEUBBAABAAAAxwEAAFIBAwABAAAAAgAAAFMBAwAEAAAA+gAAAA6DDAADAAAAAgEAAIKEDAAGAAAAGgEAAK+HAwAgAAAASgEAALCHDAACAAAAigEAALGHAgAIAAAAmgEAAAAAAAAIAAgACAAIAAEAAQABAAEAg3TCSFm1Xz+4fMraslFMPwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA20/G+DDnOEAug5NwBhlOQAAAAAAAAAAAAQABAAAABwAABAAAAQACAAEEAAABAAEAAAgAAAEA5hABCLGHBwAAAAYIAAABAI4jCQiwhwEAAQALCLCHAQAAAIhtdJYdpHJAAAAAQKZUWEFXR1MgODR8AHja7dXRCcMwEERBVeueUouKu5DkJwKDHWKDrZ2Ba2DhSa2dr5ZWDYjzal//kNu+NwD0bxHIbN8bANntewNA/xaCzParf85SENR+H89aENJ/Xz+LQWb7+ofJ++/bZzm4eft9X+veAND+cKV/iOu/xrMkZLavf8ht3xsAAf3X9lkWJmu/9p91IbP9d//eAJij/1+71z9kt+8NgPv2Xwe0r3+4Vvvf3Z7153sD4NrtH3BN/xDZv+5B+5qHjPZ1D5n96x60r3nIaF/3kNm/7iG4fSCuf8tBVvsWg1ni1zz4+/UOqX+/MSDIozQPAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADwhycnMYt0"
                                                     },
                                                     {
                                                     "time": "2019-04-18T07:06:18.000Z",
                                                     "img": https://heatmap.snap4city.org/base64.php?layer="+wmsDatasetName+"&date=20190418T070618Z
                                                     //    "img": "SUkqAAgAAAATAAABAwABAAAAJwAAAAEBAwABAAAAJwAAAAIBAwAEAAAA8gAAAAMBAwABAAAACAAAAAYBAwABAAAAAgAAABUBAwABAAAABAAAABwBAwABAAAAAQAAAD0BAwABAAAAAQAAAEIBAwABAAAAAAEAAEMBAwABAAAAAAEAAEQBBAABAAAAogEAAEUBBAABAAAAzwEAAFIBAwABAAAAAgAAAFMBAwAEAAAA+gAAAA6DDAADAAAAAgEAAIKEDAAGAAAAGgEAAK+HAwAgAAAASgEAALCHDAACAAAAigEAALGHAgAIAAAAmgEAAAAAAAAIAAgACAAIAAEAAQABAAEAg3TCSFm1Xz+4fMraslFMPwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA20/G+DDnOEAug5NwBhlOQAAAAAAAAAAAAQABAAAABwAABAAAAQACAAEEAAABAAEAAAgAAAEA5hABCLGHBwAAAAYIAAABAI4jCQiwhwEAAQALCLCHAQAAAIhtdJYdpHJAAAAAQKZUWEFXR1MgODR8AHja7dbRDYJAEEVRqqUna9nixighJCofupLAvnNgGpjkDkzT8er5Amke7esfctuveRkbgaD+5619/UNu+24AZLfvBoD+bQgy23cDILv9avqHyP7bNjYGIe2397E1COi/7Y/NQWb7bgBcvP214x+61z8M0n/n2CQEtl/L2CZktu8GQFD/tT82C5nt6x9y23cDYMD+67uxZQhtf32A6/ZfHe3rH3LbdwPgPO2/9np09/qH8/b/6Rb8s33g/O2Xf33Qfkf7wPD96x7C2wdi2tc9ZPave8hr3wYhr3+bg6z2bQxGiV/z4Nuvd0j99lsGBLmV5gEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADrcAd+SmZk="
                                                     },
                                                     {
                                                     "time": "2019-04-18T05:06:18.000Z",
                                                     "img": "https://heatmap.snap4city.org/base64.php?layer="+wmsDatasetName+"&date=20190418T050618Z"
                                                     //   "img": "SUkqAAgAAAATAAABAwABAAAAJwAAAAEBAwABAAAAJwAAAAIBAwAEAAAA8gAAAAMBAwABAAAACAAAAAYBAwABAAAAAgAAABUBAwABAAAABAAAABwBAwABAAAAAQAAAD0BAwABAAAAAQAAAEIBAwABAAAAAAEAAEMBAwABAAAAAAEAAEQBBAABAAAAogEAAEUBBAABAAAA1AEAAFIBAwABAAAAAgAAAFMBAwAEAAAA+gAAAA6DDAADAAAAAgEAAIKEDAAGAAAAGgEAAK+HAwAgAAAASgEAALCHDAACAAAAigEAALGHAgAIAAAAmgEAAAAAAAAIAAgACAAIAAEAAQABAAEAg3TCSFm1Xz+4fMraslFMPwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA20/G+DDnOEAug5NwBhlOQAAAAAAAAAAAAQABAAAABwAABAAAAQACAAEEAAABAAEAAAgAAAEA5hABCLGHBwAAAAYIAAABAI4jCQiwhwEAAQALCLCHAQAAAIhtdJYdpHJAAAAAQKZUWEFXR1MgODR8AHja7dvRCYNAEEVRq7Wn1LLFTUggSIgYRQJx3jnrNDBwd7+cpt+r5wekebSvf8ht3x0A4f3P+oe49uf3sRlo3v782b07APSvf8ht3x0A2e3rH7L7r+EOgLj2xzK2BiH9j/WxOWjc/vg+NgjN2h/7xxbhov2PY627A0D7+gftVy1jqxDSf62PzULj9mt7bBcatl/7x5ahSf91fGwZLt5+nRvbhgv2f7b71wH+p/063/au7t0BENP+5gFa9q97yGtf85DXvu4hr3/dQ177moe89nUPef3rHvLa1zz07n/XAdq+/ZqHxm7+ywFvv/9wIfXttwxIar80DwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAcMIdCPB4EA=="
                                                     },
                                                     {
                                                     "time": "2019-04-18T03:06:18.000Z",
                                                     "img": "https://heatmap.snap4city.org/base64.php?layer="+wmsDatasetName+"&date=20190418T030618Z"
                                                     //    "img": "SUkqAAgAAAATAAABAwABAAAAJwAAAAEBAwABAAAAJwAAAAIBAwAEAAAA8gAAAAMBAwABAAAACAAAAAYBAwABAAAAAgAAABUBAwABAAAABAAAABwBAwABAAAAAQAAAD0BAwABAAAAAQAAAEIBAwABAAAAAAEAAEMBAwABAAAAAAEAAEQBBAABAAAAogEAAEUBBAABAAAAwwEAAFIBAwABAAAAAgAAAFMBAwAEAAAA+gAAAA6DDAADAAAAAgEAAIKEDAAGAAAAGgEAAK+HAwAgAAAASgEAALCHDAACAAAAigEAALGHAgAIAAAAmgEAAAAAAAAIAAgACAAIAAEAAQABAAEAg3TCSFm1Xz+4fMraslFMPwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA20/G+DDnOEAug5NwBhlOQAAAAAAAAAAAAQABAAAABwAABAAAAQACAAEEAAABAAEAAAgAAAEA5hABCLGHBwAAAAYIAAABAI4jCQiwhwEAAQALCLCHAQAAAIhtdJYdpHJAAAAAQKZUWEFXR1MgODR8AHja7dfBCcMwEEVBVZueVEuK2xDwJSEQG1tg6c+IbUDwVnZr49WjVQPi1LPVe9wEZLZvB4D+3Qhktm8HQHb7dgDo3w1BZvt2AAS2X5/jtmDh/uv/uDFYrP3aP24NFum/jrVvB0B2+3YAZLevfwjv3w6AnH/+XwdYsv3dB5i+/1MHmK79yw4wTftDDnDr/oce4JbtDz99G+BmC2Bg798DrNd+PzDA3O33EwPM03+/aIA52tc85LWve8jrX/eQ177mIa993UMW3/iQ+/ZrHrz9ugdvv+Zh/Xe/NA/efr1D5NsPJD38mgcAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADjhBT9JH44="
                                                     },
                                                     {
                                                     "time": "2019-04-18T01:06:18.000Z",
                                                     "img": "https://heatmap.snap4city.org/base64.php?layer="+wmsDatasetName+"&date=20190418T010618Z"
                                                     //  "img": "SUkqAAgAAAATAAABAwABAAAAJwAAAAEBAwABAAAAJwAAAAIBAwAEAAAA8gAAAAMBAwABAAAACAAAAAYBAwABAAAAAgAAABUBAwABAAAABAAAABwBAwABAAAAAQAAAD0BAwABAAAAAQAAAEIBAwABAAAAAAEAAEMBAwABAAAAAAEAAEQBBAABAAAAogEAAEUBBAABAAAAvgEAAFIBAwABAAAAAgAAAFMBAwAEAAAA+gAAAA6DDAADAAAAAgEAAIKEDAAGAAAAGgEAAK+HAwAgAAAASgEAALCHDAACAAAAigEAALGHAgAIAAAAmgEAAAAAAAAIAAgACAAIAAEAAQABAAEAg3TCSFm1Xz+4fMraslFMPwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA20/G+DDnOEAug5NwBhlOQAAAAAAAAAAAAQABAAAABwAABAAAAQACAAEEAAABAAEAAAgAAAEA5hABCLGHBwAAAAYIAAABAI4jCQiwhwEAAQALCLCHAQAAAIhtdJYdpHJAAAAAQKZUWEFXR1MgODR8AHja7ddbCsJAEEXB2f8OerUjIkI+FJOY59yqpjcgnOnY2v56b70BcZ7t6x9y2/cGgP79IpDZvjcAstv3BkDw7X8PkPGf/9sAw7b/c4Dh+l80wCDxrxggt39vAOS27w2A7Pb1D5n912SBjPbrwwLjtl8zFhir/1q4wP3br5UL3LP92miB+/RfGy9w/fZrxwWu2f+e3Vd/LXCt9o/ofrrA+Y5sXv8weP+6h7z2NQ9Z7bv1kNe/7iGvfc1DVvtuPeT1r3vIa1/zkNe/7iH39msexmy/z/vuB3JuP5Bz+4EgvukBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD+8QDzyKb4"
                                                     }
                                                     ],
                                                     
                                                     // OPTIONAL - only required if you are not providing your own frames
                                                     // **See defining image request for more info**
                                                     // due to CORS restrictions, you need to define an async function to ask your proxy server to make the WMS
                                                     // GetMap request and resolve the result (as a base64 encoded string). This example is using a call to a server function called
                                                     // 'getImage' (in MeteorJS). Note that if your target WMS is CORS enabled, you can just define a direct HTTP request here instead.
                                                     proxyFunction: function(requestUrl, time, resolve, reject){
                                                     
                                                     $.ajax({
                                                     type: "GET",
                                                     url: requestUrl,
                                                     beforeSend: function (xhr) {
                                                     xhr.overrideMimeType('text/plain; charset=x-user-defined');
                                                     },
                                                     success: function (result, textStatus, jqXHR) {
                                                     if(result.length < 1){
                                                     alert("The thumbnail doesn't exist");
                                                     $("#thumbnail").attr("src", "data:image/png;base64,");
                                                     return
                                                     }
                                                     
                                                     var binary = "";
                                                     var responseText = jqXHR.responseText;
                                                     var responseTextLen = responseText.length;
                                                     
                                                     for ( i = 0; i < responseTextLen; i++ ) {
                                                     binary += String.fromCharCode(responseText.charCodeAt(i) & 255)
                                                     }
                                                     //   $("#thumbnail").attr("src", "data:image/png;base64,"+btoa(binary));
                                                     resolve({ time: time, img: btoa(binary) });
                                                     },
                                                     error: function(xhr, textStatus, errorThrown){
                                                     alert("Error in getting document "+textStatus);
                                                     }
                                                     });
                                                     
                                                     },
                                                     
                                                     // OPTIONAL - only required if you are not providing your own frames
                                                     // your WMS query params
                                                     params: {
                                                     BBOX: "24.90215,60.1615000000001,24.98005,60.1959",
                                                     LAYERS: "Snap4City:" + wmsDatasetName,
                                                     SRS: "EPSG:4326",
                                                     VERSION: "1.1.1",
                                                     WIDTH: 256,
                                                     HEIGHT: 256,
                                                     transparent: true,
                                                     
                                                     // ncWMS params (optional)
                                                     //    abovemaxcolor: "extend",
                                                     //    belowmincolor: "extend",
                                                     //    colorscalerange: "10.839295,13.386014",
                                                     //    elevation: "-5.050000000000001",
                                                     format: "image/png",
                                                     //    logscale: false,
                                                     //    numcolorbands: "50",
                                                     opacity: current_opacity,
                                                     //    styles: "boxfill/rainbow"
                                                     }
                                                     
                                                     };
                                                     
                                                     LeafletWmsAnimator.initAnimation(args, function(frames){
                                                     
                                                     // if you didn't provide your own frames this callback function returns the
                                                     // array of images with their respective time stamps (e.g. you can use timestamps in UI)
                                                     });  */

                                                    var upEastLat = parseFloat(bboxJson['maxy']);
                                                    var upEastLon = parseFloat(bboxJson['maxx']);
                                                    var bottomWestLat = parseFloat(bboxJson['miny']);
                                                    var bottomWestLon = parseFloat(bboxJson['minx']);
                                                    var imageUrl = 'http://wmsserver.snap4city.org/geoserver/wms/animate?LAYERS=' + wmsDatasetName + '&aparam=time&avalues=' + animationStringTimestamp + '&format=image/gif;subtype=animated&format_options=gif_loop_continuosly:true;layout:message;gif_frames_delay:500&transparent=true';
                                                    var imageBounds = [[bottomWestLat, bottomWestLon], [upEastLat, upEastLon]];
                                                    var overlayOpacity = current_opacity;

                                                    // ANIMATED GIF LAYER
                                                    var animatedLayer = L.imageOverlay(imageUrl, imageBounds, {opacity: overlayOpacity}).addTo(map.defaultMapRef);

                                                    // add legend to map
                                                    map.legendHeatmap.addTo(map.defaultMapRef);
                                                    //    $("<?= $_REQUEST['name_w'] ?>_animation").prop("checked",true);
                                                    document.getElementById("<?= $_REQUEST['name_w'] ?>_animation").checked = true;
                                                    //     $("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider({ disabled: "true" });
                                                    $("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider('disable');
                                                    //     document.getElementById("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider({ disabled: "true" });
                                                    //     document.getElementById("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider({ disabled: "true" });
                                                    map.eventsOnMap.push(animatedLayer);
                                                    var mapControlsContainer = document.getElementsByClassName("leaflet-control")[0];

                                                    var heatmapLegendColors = L.control({position: 'bottomleft'});

                                                    heatmapLegendColors.onAdd = function (map) {

                                                        var div = L.DomUtil.create('div', 'info legend'),
                                                                grades = ["Legend"];
                                                        //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                                        var legendImgPath = heatmapRange[0].iconPath; // OLD-API
                                                        div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';    /// OLD-API
                                                        return div;
                                                    };

                                                    heatmapLegendColors.addTo(map.defaultMapRef);
                                                    //  map.eventsOnMap.push(heatmap);

                                                    event.legendColors = heatmapLegendColors;
                                                    map.eventsOnMap.push(event);

                                                    if (loadingDiv) {
                                                        loadingDiv.empty();
                                                        loadingDiv.append(loadOkText);

                                                        parHeight = loadOkText.height();
                                                        parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                        loadOkText.css("margin-top", parMarginTop + "px");

                                                        setTimeout(function () {
                                                            loadingDiv.css("opacity", 0);
                                                            setTimeout(function () {
                                                                loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                                    $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                });
                                                                loadingDiv.remove();
                                                            }, 350);
                                                        }, 1000);
                                                    }
                                                }
                                            }

                                        } else {
                                            console.log("Ko Heatmap");
                                            console.log(JSON.stringify(errorData));

                                            if (loadingDiv) {
                                                loadingDiv.empty();
                                                loadingDiv.append(loadKoText);

                                                parHeight = loadKoText.height();
                                                parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                loadKoText.css("margin-top", parMarginTop + "px");

                                                setTimeout(function () {
                                                    loadingDiv.css("opacity", 0);
                                                    setTimeout(function () {
                                                        loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                            $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                        });
                                                        loadingDiv.remove();
                                                    }, 350);
                                                }, 1000);
                                            }
                                        }
                                    } catch (err) {
                                        if (loadingDiv) {
                                            loadingDiv.empty();
                                            loadingDiv.append(loadKoText);

                                            parHeight = loadKoText.height();
                                            parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                            loadKoText.css("margin-top", parMarginTop + "px");
                                            console.log("Error: " + err);
                                            setTimeout(function () {
                                                loadingDiv.css("opacity", 0);
                                                setTimeout(function () {
                                                    loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                        $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                    });
                                                    loadingDiv.remove();
                                                }, 350);
                                            }, 1000);
                                        }
                                    }
                                },
                                error: function (errorData) {
                                    console.log("Ko Heatmap");
                                    console.log(JSON.stringify(errorData));

                                    if (loadingDiv) {
                                        loadingDiv.empty();
                                        loadingDiv.append(loadKoText);

                                        parHeight = loadKoText.height();
                                        parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                        loadKoText.css("margin-top", parMarginTop + "px");

                                        setTimeout(function () {
                                            loadingDiv.css("opacity", 0);
                                            setTimeout(function () {
                                                loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                    $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                });
                                                loadingDiv.remove();
                                            }, 350);
                                        }, 1000);
                                    }
                                },
                                // CORTI
                                complete: function(){
                                    editCSSFor3DWidgets();
                                }
                            });

                        }

                        if (addMode === 'additive') {
                            //   if (event.animationFlag === true) {
                            //       addHeatmapToMap(true);
                            //   } else {
                            addHeatmapToMap();
                            //   }
                        }
                        if (addMode === 'exclusive') {
                            map.defaultMapRef.eachLayer(function (layer) {
                                map.defaultMapRef.removeLayer(layer);
                            });
                            map.eventsOnMap.length = 0;

                            //Remove WidgetAlarm active pins
                            $.event.trigger({
                                type: "removeAlarmPin",
                            });
                            //Remove WidgetEvacuationPlans active pins
                            $.event.trigger({
                                type: "removeEvacuationPlanPin",
                            });
                            //Remove WidgetEvents active pins
                            $.event.trigger({
                                type: "removeEventFIPin",
                            });
                            //Remove WidgetResources active pins
                            $.event.trigger({
                                type: "removeResourcePin",
                            });
                            //Remove WidgetOperatorEvents active pins
                            $.event.trigger({
                                type: "removeOperatorEventPin",
                            });
                            //Remove WidgetTrafficEvents active pins
                            $.event.trigger({
                                type: "removeTrafficEventPin",
                            });
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                maxZoom: 18
                            }).addTo(map.defaultMapRef);

                            addHeatmapToMap();
                        }
                    }
                });

                $(document).on('removeAlarm', function (event) {
                    if (event.target === map.mapName) {
                        let passedData = event.passedData;

                        for (let j = 0; j < passedData.length; j++) {

                            let lng = passedData[j].lng;
                            let lat = passedData[j].lat;
                            let eventType = passedData[j].eventType;
                            let eventName = passedData[j].eventName;

                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if ((map.eventsOnMap[i].lng === lng) && (map.eventsOnMap[i].lat === lat) && (map.eventsOnMap[i].eventType === eventType) && (map.eventsOnMap[i].eventName === eventName)) {
                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i].marker);
                                    map.eventsOnMap.splice(i, 1);
                                }
                            }
                        }

                        if (lastPopup !== null) {
                            lastPopup.closePopup();
                        }
                        //console.log(map.eventsOnMap.length);

                        //   resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('removeEvacuationPlans', function (event) {
                    if (event.target === map.mapName) {
                        if (lastPopup !== null) {
                            lastPopup.closePopup();
                        }

                        for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                            if (map.eventsOnMap[i].eventType === 'evacuationPlan') {
                                map.defaultMapRef.removeLayer(map.eventsOnMap[i].polyGroup);
                                map.eventsOnMap.splice(i, 1);
                                //    resizeMapView(map.defaultMapRef);
                            }
                        }
                        //console.log(map.eventsOnMap.length);
                    }
                });
                $(document).on('removeSelectorPin', function (event) {
                    if (event.target === map.mapName) {
                        var passedData = event.passedData;

                        var desc = passedData.desc;
                        var display = passedData.display;

                        if (stopGeometryAjax.hasOwnProperty(desc)) {
                            stopGeometryAjax[desc] = true;
                        }

                        if (display !== 'geometries') {
                            if (gisLayersOnMap[desc] !== "loadError") {
                                map.defaultMapRef.removeLayer(gisLayersOnMap[desc]);

                                if (gisGeometryLayersOnMap.hasOwnProperty(desc)) {
                                    if (gisGeometryLayersOnMap[desc].length > 0) {
                                        for (var i = 0; i < gisGeometryLayersOnMap[desc].length; i++) {
                                            map.defaultMapRef.removeLayer(gisGeometryLayersOnMap[desc][i]);
                                        }
                                        delete gisGeometryLayersOnMap[desc];
                                    }
                                }
                            }
                            delete gisLayersOnMap[desc];
                        } else {
                            if (gisGeometryLayersOnMap.hasOwnProperty(desc)) {
                                if (gisGeometryLayersOnMap[desc].length > 0) {
                                    for (var i = 0; i < gisGeometryLayersOnMap[desc].length; i++) {
                                        map.defaultMapRef.removeLayer(gisGeometryLayersOnMap[desc][i]);
                                    }
                                    delete gisGeometryLayersOnMap[desc];
                                }
                            }
                        }

                        delete gisGeometryTankForFullscreen[desc];

                        for (i = map.eventsOnMap.length - 1; i >= 0; i--) {
                            if (map.eventsOnMap[i] && (map.eventsOnMap[i].eventType === 'selectorEvent') && (map.eventsOnMap[i].desc === desc)) {
                                map.eventsOnMap.splice(i, 1);
                            }
                        }

                        //console.log(map.eventsOnMap.length);

                        //  resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('removeEventFI', function (event) {
                    if (event.target === map.mapName) {
                        let passedData = event.passedData;

                        for (let j = 0; j < passedData.length; j++) {

                            let lng = passedData[j].lng;
                            let lat = passedData[j].lat;
                            let eventType = passedData[j].eventType;
                            let eventName = passedData[j].name;

                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if ((map.eventsOnMap[i].lng === lng) && (map.eventsOnMap[i].lat === lat) && (map.eventsOnMap[i].eventType === eventType) && (map.eventsOnMap[i].name === eventName)) {
                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i].marker);
                                    map.eventsOnMap.splice(i, 1);
                                }
                            }
                        }

                        if (lastPopup !== null) {
                            lastPopup.closePopup();
                        }
                        //console.log(map.eventsOnMap.length);

                        //  resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('removeResource', function (event) {
                    if (event.target === map.mapName) {
                        let passedData = event.passedData;

                        for (let j = 0; j < passedData.length; j++) {

                            let lng = passedData[j].lng;
                            let lat = passedData[j].lat;
                            let eventName = passedData[j].eventName;
                            let eventType = passedData[j].eventType;

                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if ((map.eventsOnMap[i].lng === lng) && (map.eventsOnMap[i].lat === lat) && (map.eventsOnMap[i].eventType === eventType) && (map.eventsOnMap[i].eventName === eventName)) {
                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i].marker);
                                    map.eventsOnMap.splice(i, 1);
                                }
                            }
                        }

                        if (lastPopup !== null) {
                            lastPopup.closePopup();
                        }
                        //console.log(map.eventsOnMap.length);

                        //   resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('removeOperatorEvent', function (event) {
                    if (event.target === map.mapName) {
                        let passedData = event.passedData;

                        for (let j = 0; j < passedData.length; j++) {

                            let lng = passedData[j].lng;
                            let lat = passedData[j].lat;
                            let eventType = passedData[j].eventType;
                            let eventName = passedData[j].name;

                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if ((map.eventsOnMap[i].lng === lng) && (map.eventsOnMap[i].lat === lat) && (map.eventsOnMap[i].eventType === eventType) && (map.eventsOnMap[i].name === eventName)) {
                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i].marker);
                                    map.eventsOnMap.splice(i, 1);
                                }
                            }
                        }

                        if (lastPopup !== null) {
                            lastPopup.closePopup();
                        }
                        //console.log(map.eventsOnMap.length);

                        //  resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('removeTrafficEvent', function (event) {
                    if (event.target === map.mapName) {
                        let passedData = event.passedData;

                        for (let j = 0; j < passedData.length; j++) {

                            let lng = passedData[j].lng;
                            let lat = passedData[j].lat;
                            let eventType = passedData[j].eventType;
                            let eventName = passedData[j].eventName;

                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                if ((map.eventsOnMap[i].lng === lng) && (map.eventsOnMap[i].lat === lat) && (map.eventsOnMap[i].eventType === eventType) && (map.eventsOnMap[i].eventName === eventName)) {
                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i].marker);
                                    map.eventsOnMap.splice(i, 1);
                                }
                            }
                        }

                        if (lastPopup !== null) {
                            lastPopup.closePopup();
                        }

                        //console.log(map.eventsOnMap.length);

                        //  resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('removeTrafficRealTimeDetails', function (event) {
                    if (event.target === map.mapName) {

                        for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                            if (map.eventsOnMap[i] != undefined) {
                                if (map.eventsOnMap[i].eventType === "trafficRealTimeDetails") {
                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i].marker);
                                    map.defaultMapRef.removeControl(map.eventsOnMap[i].legend);
                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i].trafficLayer);
                                    map.eventsOnMap.splice(i, 1);
                                }
                            }
                        }

                        //  resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('removeHeatmap', function (event) {

                    function removeHeatmap(resetPageFlag) {
                        if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                            if (resetPageFlag == true) {
                                current_page = 0;     // CTR SE VA BENE BISOGNA DISTINGUERE IL CASO CHE SI STIA NAVIGANDO LA STESSA HEATMAP_NAME OPPURE UN'ALTRA NUOVA HEATMP_NAME
                                current_radius = null;
                                current_opacity = null;
                                changeRadiusOnZoom = false;
                                estimateRadiusFlag = false;
                                estimatedRadius = null;
                                wmsDatasetName = null;
                            }
                            map.testData = [];
                            map.heatmapLayer.setData({data: []});
                            map.defaultMapRef.removeLayer(map.heatmapLayer);
                            if (resetPageFlag != true) {
                                if (map.cfg["radius"] != current_radius) {
                                    setOption('radius', current_radius, 1);
                                }
                                if (map.cfg["maxOpacity"] != current_opacity) {
                                    setOption('maxOpacity', current_opacity, 2);
                                }
                            }
                            map.defaultMapRef.removeControl(map.legendHeatmap);
                            /*    if(map.heatmapLegendColors) {
                             map.defaultMapRef.removeControl(map.heatmapLegendColors);
                             }*/
                        } else {    // NEW WMS HEATMAP
                            if (resetPageFlag == true) {
                                current_page = 0;
                            }
                            map.defaultMapRef.removeLayer(wmsLayer);
                            map.defaultMapRef.removeControl(map.legendHeatmap);
                        }
                    }

                    function removeHeatmapColorLegend(index, resetPageFlag) {
                        if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                            if (resetPageFlag == true) {
                                current_page = 0;     // CTR SE VA BENE BISOGNA DISTINGUERE IL CASO CHE SI STIA NAVIGANDO LA STESSA HEATMAP_NAME OPPURE UN'ALTRA NUOVA HEATMP_NAME
                                current_radius = null;
                                current_opacity = null;
                                changeRadiusOnZoom = false;
                                estimateRadiusFlag = false;
                                estimatedRadius = null;
                                wmsDatasetName = null;
                            }
                            map.testData = [];
                            map.heatmapLayer.setData({data: []});
                            map.defaultMapRef.removeLayer(map.heatmapLayer);
                            if (resetPageFlag != true) {
                                if (map.cfg["radius"] != current_radius) {
                                    setOption('radius', current_radius, 1);
                                }
                                if (map.cfg["maxOpacity"] != current_opacity) {
                                    setOption('maxOpacity', current_opacity, 2);
                                }
                            }
                            map.defaultMapRef.removeControl(map.eventsOnMap[index].legendColors);
                        } else {    // NEW WMS HEATMAP
                            if (resetPageFlag == true) {
                                current_page = 0;
                            }
                            map.defaultMapRef.removeControl(map.eventsOnMap[index].legendColors);
                            map.defaultMapRef.removeLayer(wmsLayer);
                        }
                    }

                    if (event.target === map.mapName) {
                        for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
			    if (map.eventsOnMap[i] != undefined) {
	                            if (map.eventsOnMap[i].eventType === 'heatmap') {
	                                removeHeatmap(true);
	                                map.eventsOnMap.splice(i, 1);
	                            } else if (map.eventsOnMap[i].type === 'addHeatmap') {
	                                removeHeatmapColorLegend(i, true);
	                                map.eventsOnMap.splice(i, 1);
	                            } else if (map.eventsOnMap[i] !== null && map.eventsOnMap[i] !== undefined) {
	                                if (map.eventsOnMap[i].eventType != 'trafficRealTimeDetails') {
	                                    map.defaultMapRef.removeLayer(map.eventsOnMap[i]);
	                                    map.eventsOnMap.splice(i, 1);
	                                    removeHeatmap(true);
	                                }
	                            }
			     }
                        }
                    }
                    map.defaultMapRef.off('click');
                });

                $(document).on('toggleAddMode', function (event) {
                    addMode = event.addMode;
                    //console.log(addMode);
                });

            }

            // Funzione che risponde all'evento resize del widget, indotto o dal ridimensionatore manuale dell'editor di dashboard oppure dalla dashboard stessa in modalità responsive
            function resizeWidget() {
                setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            }

            //Fine definizioni di funzione

            //Inizio del main script

            /*IMPORTANTE - Chiamata al modulo server che reperisce i parametri di costruzione del widget dal database (tipicamente
             * da tabella Config_widget_dashboard, la quale memorizza un record per ogni istanza di widget. Tale record viene scritto
             * quando il widget viene creato
             */
            function newMap(center = [], load3D = true) {
                $.ajax({
                    url: "../controllers/getWidgetParams.php",
                    type: "GET",
                    data: {
                        widgetName: "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>"
                    },
                    async: true,
                    dataType: 'json',
                    success: function (widgetData) {
                        //Parametri di costruzione del widget (struttura e aspetto)
                        showTitle = widgetData.params.showTitle;
                        widgetContentColor = widgetData.params.color_w;
                        fontSize = widgetData.params.fontSize;
                        fontColor = widgetData.params.fontColor;
                        hasTimer = widgetData.params.hasTimer;
                        chartColor = widgetData.params.chartColor;
                        dataLabelsFontSize = widgetData.params.dataLabelsFontSize;
                        dataLabelsFontColor = widgetData.params.dataLabelsFontColor;
                        chartLabelsFontSize = widgetData.params.chartLabelsFontSize;
                        chartLabelsFontColor = widgetData.params.chartLabelsFontColor;
                        appId = widgetData.params.appId;
                        flowId = widgetData.params.flowId;
                        nrMetricType = widgetData.params.nrMetricType;
                        sm_based = widgetData.params.sm_based;
                        rowParameters = widgetData.params.rowParameters;
                        sm_field = widgetData.params.sm_field;
                        addMode = widgetData.params.viewMode;
                        enableFullscreenModal = widgetData.params.enableFullscreenModal;
                        enableFullscreenTab = widgetData.params.enableFullscreenTab;
                        geoServerUrl = widgetData.geoServerUrl;
                        heatmapUrl = widgetData.heatmapUrl;

                        if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
                            showHeader = false;
                        } else {
                            showHeader = true;
                        }

                        metricName = "<?= $_REQUEST['id_metric'] ?>";
                        widgetTitle = widgetData.params.title_w;
                        widgetHeaderColor = widgetData.params.frame_color_w;
                        widgetHeaderFontColor = widgetData.params.headerFontColor;
                        sizeRowsWidget = parseInt(widgetData.params.size_rows);
                        styleParameters = JSON.parse(widgetData.params.styleParameters);
                        widgetParameters = JSON.parse(widgetData.params.parameters);

                        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);

                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').off('resizeWidgets');
                        $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

                        $("#" + widgetName + "_buttonsDiv").css("height", "100%");
                        $("#" + widgetName + "_buttonsDiv").css("float", "left");

                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).css("font-size", "20px");
                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(2).hover(function () {
                            $(this).find("span").css("color", "red");
                        }, function () {
                            $(this).find("span").css("color", widgetHeaderFontColor);
                        });
                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).css("font-size", "20px");
                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(3).hover(function () {
                            $(this).find("span").css("color", "red");
                        }, function () {
                            $(this).find("span").css("color", widgetHeaderFontColor);
                        });

                        if (hostFile === "config") {
                            if ((enableFullscreenModal === 'yes') && (enableFullscreenTab === 'yes')) {
                                $("#" + widgetName + "_buttonsDiv").css("width", "50px");
                                titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 25 - 2));
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                            } else {
                                if ((enableFullscreenModal === 'yes') && (enableFullscreenTab === 'no')) {
                                    $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 25 - 2));
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();
                                } else {
                                    if ((enableFullscreenModal === 'no') && (enableFullscreenTab === 'yes')) {
                                        $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                        titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 25 - 2));
                                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                                    } else {
                                        $("#" + widgetName + "_buttonsDiv").css("width", "0px");
                                        $("#" + widgetName + "_buttonsDiv").hide();
                                        titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 0 - 25 - 2));
                                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                                    }
                                }
                            }
                        } else {
                            if ((enableFullscreenTab === 'yes') && (enableFullscreenModal === 'yes')) {
                                $("#" + widgetName + "_buttonsDiv").css("width", "50px");
                                titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 2));
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                            } else {
                                if ((enableFullscreenTab === 'yes') && (enableFullscreenModal === 'no')) {
                                    $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                                } else {
                                    if ((enableFullscreenTab === 'no') && (enableFullscreenModal === 'yes')) {
                                        $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                        titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                                        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();
                                    } else {
                                        $("#" + widgetName + "_buttonsDiv").hide();
                                        titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 2));
                                    }
                                }
                            }
                        }

                        $("#" + widgetName + "_titleDiv").css("width", titleWidth + "px");

                        if (firstLoad === false) {
                            showWidgetContent(widgetName);
                        } else {
                            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                        }
                        populateWidget(center, load3D);
                        //   globalMapView = true;

                        // parte mappa 3D - CORTI
                        if(load3D){
                            setTimeout(function () {
                                initMapsAndListeners(map);
                            }, 3000);
                        }
                        // hide fullscreen
                        $('#<?= $_REQUEST['name_w'] ?>_buttonsDiv').addClass('hidden');

                    },
                    error: function (errorData) {

                    },
                    // CORTI
                    complete: function(){
                        editCSSFor3DWidgets();
                    }
                });
            }
            newMap();


            //Risponditore ad evento resize
            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('customResizeEvent', function (event) {
                resizeWidget();
            });

            //Usata solo per widget con grafico Highchart al proprio interno (non è il nostro caso)
            $(document).on('resizeHighchart_' + widgetName, function (event) {
                showHeader = event.showHeader;
            });

            createFullscreenModal();

            //Avvio del conto alla rovescia per il ricaricamento periodico del widget
            //countdownRef = startCountdown(widgetName, timeToReload, <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId);

            $('#<?= $_REQUEST['name_w'] ?>_buttonsDiv a.iconFullscreenModal').click(function () {

                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen h4.modal-title").html($("#<?= $_REQUEST['name_w'] ?>_titleDiv").html());
                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen iframe").hide();

                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyMap").hide();
                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenGisMap").hide();
                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").show();


                //Creazione mappa
                setTimeout(function () {
                    var mapdiv = "<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap";
                    var latInit = 43.769789;
                    var lngInit = 11.255694;
                    //    fullscreendefaultMapRef = L.map(mapdiv).setView([43.769789, 11.255694], 11);
                    //    fullscreendefaultMapRef = L.map(mapdiv).setView([43.769789, 11.255694], widgetParameters.zoom);
                    if (widgetParameters.latLng[0] != null && widgetParameters.latLng[0] != '') {
                        latInit = widgetParameters.latLng[0];
                    }
                    if (widgetParameters.latLng[1] != null && widgetParameters.latLng[1] != '') {
                        lngInit = widgetParameters.latLng[1];
                    }
                    if (fullscreenHeatmapFirstInstantiation === false) {
                        fullscreendefaultMapRef = L.map(mapdiv).setView([latInit, lngInit], widgetParameters.zoom);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                            maxZoom: 18
                        }).addTo(fullscreendefaultMapRef);
                        fullscreendefaultMapRef.attributionControl.setPrefix('');
                        fullscreenHeatmapFirstInstantiation = true;
                    }

                    //Popolamento mappa (se ci sono eventi su mappa originaria)
                    //if ($('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen input.fullscreenEventPoint').length > 0) {}
                    if (map.eventsOnMap.length > 0) {

                        for (let i = 0; i < map.eventsOnMap.length; i++) {

                            if (map.eventsOnMap[i].type === 'alarmEvent') {
                                let lat = map.eventsOnMap[i].lat;
                                let lng = map.eventsOnMap[i].lng;
                                let eventType = map.eventsOnMap[i].eventType;
                                let eventName = map.eventsOnMap[i].eventName;
                                let eventStartDate = map.eventsOnMap[i].eventStartDate;
                                let eventStartTime = map.eventsOnMap[i].eventStartTime;
                                let eventSeverity = map.eventsOnMap[i].eventSeverity;

                                //Creazione dell'icona custom per il pin
                                switch (eventSeverity) {
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

                                let pinIcon = new L.DivIcon({
                                    className: null,
                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                    iconAnchor: [18, 36]
                                });

                                let markerLocation = new L.LatLng(lat, lng);
                                let marker = new L.Marker(markerLocation, {icon: pinIcon});

                                //Creazione del popup per il pin appena creato
                                let popupText = "<span class='mapPopupTitle'>" + eventName + "</span>" +
                                        "<span class='mapPopupLine'><i>Start date: </i>" + eventStartDate + " - " + eventStartTime + "</span>" +
                                        "<span class='mapPopupLine'><i>Event type: </i>" + alarmTypes[eventType].desc.toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'><i>Event severity: </i><span style='background-color: " + severityColor + "'>" + eventSeverity.toUpperCase() + "</span></span>";

                                fullscreendefaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth: 600}).openPopup();
                            }
                            if (map.eventsOnMap[i].eventType === 'evacuationPlan') {
                                let plansObj = map.eventsOnMap[i].plansObj;
                                let planId = map.eventsOnMap[i].planId;
                                let evacuationColors = map.eventsOnMap[i].colors;

                                shownPolyGroup = L.featureGroup();


                                for (let j = 0; j < plansObj[planId].payload.evacuation_paths.length; j++) {
                                    path = [];

                                    for (let i = 0; i < plansObj[planId].payload.evacuation_paths[j].coords.length; i++) {
                                        let point = [];
                                        point[0] = plansObj[planId].payload.evacuation_paths[j].coords[i].latitude;
                                        point[1] = plansObj[planId].payload.evacuation_paths[j].coords[i].longitude;
                                        path.push(point);
                                    }

                                    let polyline = L.polyline(path, {color: evacuationColors[j % 6]});
                                    shownPolyGroup.addLayer(polyline);
                                }

                                fullscreendefaultMapRef.addLayer(shownPolyGroup);
                            }
                            if (map.eventsOnMap[i].eventType === 'selectorEvent') {

                                var mapBounds = fullscreendefaultMapRef.getBounds();
                                var query = map.eventsOnMap[i].query;
                                var targets = map.eventsOnMap[i].targets;
                                var eventGenerator = map.eventsOnMap[i].eventGenerator;
                                var color1 = map.eventsOnMap[i].color1;
                                var color2 = map.eventsOnMap[i].color2;
                                var queryType = map.eventsOnMap[i].queryType;
                                var desc = map.eventsOnMap[i].desc;
                                var display = map.eventsOnMap[i].display;

                                var re1 = '(selection)';	// Word 1
                                var re2 = '(=)';	// Any Single Character 1
                                var re3 = '([+-]?\\d*\\.\\d+)(?![-+0-9\\.])';	// Float 1
                                var re4 = '(;|%3B)';	// Any Single Character 2
                                var re5 = '([+-]?\\d*\\.\\d+)(?![-+0-9\\.])';	// Float 2
                                var re6 = '(;|%3B)?';	// Any Single Character 3
                                var re7 = '([+-]?\\d*\\.\\d+)?(?![-+0-9\\.])?';	// Float 3
                                var re8 = '(;|%3B)?';	// Any Single Character 4
                                var re9 = '([+-]?\\d*\\.\\d+)?(?![-+0-9\\.])?';	// Float 4

                                var pattern = new RegExp(re1 + re2 + re3 + re4 + re5 + re6 + re7 + re8 + re9, ["i"]);

                                if (queryType === "Default") {
                                    if (map.eventsOnMap[i].query.includes("datamanager/api/v1/poidata/")) {
                                        if (map.eventsOnMap[i].desc != "My POI") {
                                            myPOIId = map.eventsOnMap[i].query.split("datamanager/api/v1/poidata/")[1];
                                            apiUrl = "../controllers/myPOIProxy.php";
                                            dataForApi = myPOIId;
                                            query = map.eventsOnMap[i].query;
                                        } else {
                                            apiUrl = "../controllers/myPOIProxy.php";
                                            dataForApi = "All";
                                            query = map.eventsOnMap[i].query;
                                        }
                                    } else if (map.eventsOnMap[i].query.includes("/iot/") && !passedData.query.includes("/api/v1/")) {
                                        query = "https://www.disit.org/superservicemap/api/v1/?serviceUri=" + map.eventsOnMap[i].query + "&format=json";
                                    } else {
                                        if (pattern.test(query)) {
                                            query = query.replace(pattern, "selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng);
                                        } else {
                                            query = query + "&selection=" + mapBounds["_southWest"].lat + ";" + mapBounds["_southWest"].lng + ";" + mapBounds["_northEast"].lat + ";" + mapBounds["_northEast"].lng;
                                        }
                                    }
                                } else if (queryType === "MyPOI") {
                                    if (map.eventsOnMap[i].desc != "My POI") {
                                        myPOIId = map.eventsOnMap[i].query.split("datamanager/api/v1/poidata/")[1];
                                        apiUrl = "../controllers/myPOIProxy.php";
                                        dataForApi = myPOIId;
                                        query = map.eventsOnMap[i].query;
                                    } else {
                                        apiUrl = "../controllers/myPOIProxy.php";
                                        dataForApi = "All";
                                        query = map.eventsOnMap[i].query;
                                    }

                                } else
                                {
                                    query = map.eventsOnMap[i].query;
                                }

                                if (targets !== "") {
                                    targets = targets.split(",");
                                } else {
                                    targets = [];
                                }

                                if (queryType != "MyPOI" && !map.eventsOnMap[i].query.includes("datamanager/api/v1/poidata/")) {
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
                                    //    url: query + "&geometry=true&fullCount=false",
                                    url: apiUrl,
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    timeout: 0,
                                    dataType: 'json',
                                    success: function (geoJsonData) {
                                        var fatherGeoJsonNode = null;

                                        if (queryType === "Default") {
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
                                             }
                                             else {
                                             if (geoJsonData.hasOwnProperty("SensorSites")) {
                                             fatherGeoJsonNode = geoJsonData.SensorSites;
                                             }
                                             else {
                                             fatherGeoJsonNode = geoJsonData.Services;
                                             }
                                             }*/
                                        } else if (queryType === "MyPOI")
                                        {
                                            fatherGeoJsonNode.features = [];
                                            if (map.eventsOnMap[i].desc != "My POI") {
                                                fatherGeoJsonNode.features[0] = geoJsonData;
                                            } else {
                                                fatherGeoJsonNode.features = geoJsonData;
                                            }
                                            fatherGeoJsonNode.type = "FeatureCollection";
                                        } else {
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
                                            /*    if (geoJsonData.hasOwnProperty("BusStop")) {
                                             fatherGeoJsonNode = geoJsonData.BusStop;
                                             }
                                             else {
                                             if (geoJsonData.hasOwnProperty("Sensor")) {
                                             fatherGeoJsonNode = geoJsonData.Sensor;
                                             }
                                             else {
                                             if (geoJsonData.hasOwnProperty("Service")) {
                                             fatherGeoJsonNode = geoJsonData.Service;
                                             }
                                             else {
                                             fatherGeoJsonNode = geoJsonData.Services;
                                             }
                                             }
                                             }*/
                                        }

                                        for (var i = 0; i < fatherGeoJsonNode.features.length; i++) {

                                            fatherGeoJsonNode.features[i].properties.targetWidgets = targets;
                                            fatherGeoJsonNode.features[i].properties.color1 = color1;
                                            fatherGeoJsonNode.features[i].properties.color2 = color2;
                                        }

                                        if ((gisLayersOnMap.hasOwnProperty(desc) && (display !== 'geometries')) || is3DViewOn) {
                                            gisLayersOnMap[desc] = L.geoJSON(fatherGeoJsonNode, {
                                                pointToLayer: gisPrepareCustomMarkerFullScreen,
                                                onEachFeature: onEachFeature
                                            }).addTo(fullscreendefaultMapRef);
                                        }

                                        //eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").hide();
                                        eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("font-weight", "bold");
                                        eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("color", eventGenerator.attr("data-activeFontColor"));
                                        if (eventGenerator.parents("div.gisMapPtrContainer").find('a.gisPinLink').attr("data-symbolMode") === 'auto') {
                                            eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").html("near_me");
                                            eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("color", "white");
                                            eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("text-shadow", "2px 2px 4px black");
                                        } else {
                                            //Evidenziazione che gli eventi di questa query sono su mappa in caso di icona custom
                                            eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").show();
                                            eventGenerator.parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                        }

                                        eventGenerator.show();

                                        var wkt = null;

                                        if (display !== 'pins') {
                                            stopGeometryAjax[desc] = false;
                                            gisGeometryTankForFullscreen[desc] = {
                                                capacity: fatherGeoJsonNode.features.length,
                                                shown: false,
                                                tank: [],
                                                lastConsumedIndex: 0
                                            };

                                            for (var i = 0; i < fatherGeoJsonNode.features.length; i++) {
                                                if (fatherGeoJsonNode.features[i].properties.hasOwnProperty('hasGeometry') && fatherGeoJsonNode.features[i].properties.hasOwnProperty('serviceUri')) {
                                                    if (fatherGeoJsonNode.features[i].properties.hasGeometry === true) {
                                                        //gisGeometryServiceUriToShowFullscreen[event.desc].push(fatherGeoJsonNode.features[i].properties.serviceUri);

                                                        $.ajax({
                                                            url: "<?php echo $superServiceMapUrlPrefix; ?>" + "/api/v1/?serviceUri=" + fatherGeoJsonNode.features[i].properties.serviceUri,
                                                            type: "GET",
                                                            data: {},
                                                            async: true,
                                                            timeout: 0,
                                                            dataType: 'json',
                                                            success: function (geometryGeoJson) {
                                                                if (!stopGeometryAjax[desc]) {
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

                                                                    if (!gisGeometryLayersOnMap.hasOwnProperty(desc)) {
                                                                        gisGeometryLayersOnMap[desc] = [];
                                                                    }

                                                                    gisGeometryLayersOnMap[desc].push(L.geoJSON(ciclePathFeature, {}).addTo(fullscreendefaultMapRef));
                                                                    gisGeometryTankForFullscreen[desc].tank.push(ciclePathFeature);
                                                                }
                                                            },
                                                            error: function (geometryErrorData) {
                                                                console.log("Ko");
                                                                console.log(JSON.stringify(geometryErrorData));
                                                            },
                                                            // CORTI
                                                            complete: function(){
                                                                editCSSFor3DWidgets();
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        }
                                    },
                                });
                            }
                            if (map.eventsOnMap[i].eventType === 'eventFI') {
                                let lat = map.eventsOnMap[i].lat;
                                let lng = map.eventsOnMap[i].lng;
                                let categoryIT = map.eventsOnMap[i].categoryIT;

                                let name = map.eventsOnMap[i].name;
                                if (name.includes('?')) {
                                    name = name.replace(/\?/g, "'");
                                }

                                let place = map.eventsOnMap[i].place;
                                if (place.includes('?')) {
                                    place = place.replace(/\?/g, "'");
                                }

                                let startDate = map.eventsOnMap[i].startDate;
                                let endDate = map.eventsOnMap[i].endDate;
                                let startTime = map.eventsOnMap[i].startTime;
                                let freeEvent = map.eventsOnMap[i].freeEvent;
                                let address = map.eventsOnMap[i].address;
                                if (address.includes('?')) {
                                    address = address.replace(/\?/g, "'");
                                }

                                let civic = map.eventsOnMap[i].civic;
                                let price = map.eventsOnMap[i].price;
                                let phone = map.eventsOnMap[i].phone;
                                let descriptionIT = map.eventsOnMap[i].descriptionIT;
                                if (descriptionIT.includes('?')) {
                                    descriptionIT = descriptionIT.replace(/\?/g, "'");
                                }

                                let website = map.eventsOnMap[i].website;
                                let colorClass = map.eventsOnMap[i].colorClass;
                                let mapIconName = map.eventsOnMap[i].mapIconName;

                                let mapPinImg = '../img/eventsIcons/' + mapIconName + '.png';

                                let pinIcon = new L.DivIcon({
                                    className: null,
                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                    iconAnchor: [18, 36]
                                });

                                let markerLocation = new L.LatLng(lat, lng);
                                let marker = new L.Marker(markerLocation, {icon: pinIcon});

                                //Creazione del popup per il pin appena creato
                                let popupText = '<h3 class="' + colorClass + ' recreativeEventMapTitle">' + name + '</h3>';
                                popupText += '<div class="recreativeEventMapBtnContainer"><button class="recreativeEventMapDetailsBtn recreativeEventMapBtn ' + colorClass + ' recreativeEventMapBtnActive" type="button">Details</button><button class="recreativeEventMapDescriptionBtn recreativeEventMapBtn ' + colorClass + '" type="button">Description</button><button class="recreativeEventMapTimingBtn recreativeEventMapBtn ' + colorClass + '" type="button">Timing</button><button class="recreativeEventMapContactsBtn recreativeEventMapBtn ' + colorClass + '" type="button">Contacts</button></div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer">';
                                if ((place !== 'undefined') || (address !== 'undefined')) {
                                    if (categoryIT !== 'undefined') {
                                        popupText += '<b>Category: </b>' + categoryIT;
                                    }

                                    if (place !== 'undefined') {
                                        popupText += '<br/>';
                                        popupText += '<b>Location: </b>' + place;
                                    }

                                    if (address !== 'undefined') {
                                        popupText += '<br/>';
                                        popupText += '<b>Address: </b>' + address;
                                        if (civic !== 'undefined') {
                                            popupText += ' ' + civic;
                                        }
                                    }

                                    if (freeEvent !== 'undefined') {
                                        popupText += '<br/>';
                                        if ((freeEvent !== 'yes') && (freeEvent !== 'YES') && (freeEvent !== 'Yes')) {
                                            if (price !== 'undefined') {
                                                popupText += '<b>Price (€) : </b>' + price + "<br>";
                                            } else {
                                                popupText += '<b>Price (€) : </b>N/A<br>';
                                            }
                                        } else {
                                            popupText += '<b>Free event: </b>' + freeEvent + '<br>';
                                        }
                                    }
                                } else {
                                    popupText += 'No further details available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';
                                if (descriptionIT !== 'undefined') {
                                    popupText += descriptionIT;
                                } else {
                                    popupText += 'No description available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapTimingContainer">';
                                if ((startDate !== 'undefined') || (endDate !== 'undefined') || (startTime !== 'undefined')) {
                                    popupText += '<b>From: </b>';
                                    if (startDate !== 'undefined') {
                                        popupText += startDate;
                                    } else {
                                        popupText += 'N/A';
                                    }
                                    popupText += '<br/>';

                                    popupText += '<b>To: </b>';
                                    if (endDate !== 'undefined') {
                                        popupText += endDate;
                                    } else {
                                        popupText += 'N/A';
                                    }
                                    popupText += '<br/>';

                                    if (startTime !== 'undefined') {
                                        popupText += '<b>Times: </b>' + startTime + '<br/>';
                                    } else {
                                        popupText += '<b>Times: </b>N/A<br/>';
                                    }

                                } else {
                                    popupText += 'No timings info available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';
                                if ((phone !== 'undefined') || (website !== 'undefined')) {
                                    if (phone !== 'undefined') {
                                        popupText += '<b>Phone: </b>' + phone + '<br/>';
                                    } else {
                                        popupText += '<b>Phone: </b>N/A<br/>';
                                    }

                                    if (website !== 'undefined') {
                                        if (website.includes('http') || website.includes('https')) {
                                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                                        } else {
                                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                                        }
                                    } else {
                                        popupText += '<b>Website: </b>N/A';
                                    }
                                } else {
                                    popupText += 'No contacts info available';
                                }
                                popupText += '</div>';

                                fullscreendefaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth: 300});

                                lastPopup.on('popupopen', function () {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapDetailsBtn').off('click');
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapDetailsBtn').click(function () {
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDataContainer').hide();
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDetailsContainer').show();
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                        $(this).addClass('recreativeEventMapBtnActive');
                                    });

                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapDescriptionBtn').off('click');
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapDescriptionBtn').click(function () {
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDataContainer').hide();
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDescContainer').show();
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                        $(this).addClass('recreativeEventMapBtnActive');
                                    });

                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapTimingBtn').off('click');
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapTimingBtn').click(function () {
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDataContainer').hide();
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapTimingContainer').show();
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                        $(this).addClass('recreativeEventMapBtnActive');
                                    });

                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn').off('click');
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn').click(function () {
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapDataContainer').hide();
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap div.recreativeEventMapContactsContainer').show();
                                        $('#' + widgetName + '_modalLinkOpenBodyDefaultMap button.recreativeEventMapBtn').removeClass('recreativeEventMapBtnActive');
                                        $(this).addClass('recreativeEventMapBtnActive');
                                    });
                                });

                                lastPopup.openPopup();
                            }
                            if (map.eventsOnMap[i].eventType === 'resource') {
                                let lat = map.eventsOnMap[i].lat;
                                let lng = map.eventsOnMap[i].lng;
                                let eventName = map.eventsOnMap[i].eventName;
                                let eventStartDate = map.eventsOnMap[i].eventStartDate;
                                let eventStartTime = map.eventsOnMap[i].eventStartTime;

                                mapPinImg = '../img/resourceIcons/metroMap.png';

                                pinIcon = new L.DivIcon({
                                    className: null,
                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                    iconAnchor: [18, 36]
                                });

                                var markerLocation = new L.LatLng(lat, lng);
                                var marker = new L.Marker(markerLocation, {icon: pinIcon});

                                //Creazione del popup per il pin appena creato
                                var popupText = "<span class='mapPopupTitle'>" + eventName.toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'>" + eventStartDate + " - " + eventStartTime + "</span>";

                                fullscreendefaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [-5, -40]}).openPopup();
                            }
                            if (map.eventsOnMap[i].eventType === 'OperatorEvent') {
                                let lat = map.eventsOnMap[i].lat;
                                let lng = map.eventsOnMap[i].lng;
                                let eventStartDate = map.eventsOnMap[i].eventStartDate;
                                let eventStartTime = map.eventsOnMap[i].eventStartTime;
                                let eventPeopleNumber = parseInt(map.eventsOnMap[i].eventPeopleNumber);
                                let eventOperatorName = map.eventsOnMap[i].eventOperatorName;
                                let eventColor = map.eventsOnMap[i].eventColor;


                                let markerLocation = new L.LatLng(lat, lng);
                                let marker = new L.Marker(markerLocation);

                                //Creazione del popup per il pin appena creato
                                popupText = "<span class='mapPopupTitle'>" + eventColor.toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'>" + eventStartDate + " - " + eventStartTime + "</span>" +
                                        "<span class='mapPopupLine'>PEOPLE INVOLVED: " + eventPeopleNumber + "</span>" +
                                        "<span class='mapPopupLine'>OPERATOR: " + eventOperatorName.toUpperCase() + "</span>";

                                fullscreendefaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [0, 0]}).openPopup();
                            }
                            if (map.eventsOnMap[i].type === 'trafficEvent') {
                                let lat = map.eventsOnMap[i].lat;
                                let lng = map.eventsOnMap[i].lng;
                                let eventType = map.eventsOnMap[i].eventType;
                                let eventSubtype = map.eventsOnMap[i].eventSubtype;
                                let eventName = map.eventsOnMap[i].eventName;
                                let eventStartDate = map.eventsOnMap[i].eventStartDate;
                                let eventStartTime = map.eventsOnMap[i].eventStartTime;
                                let eventSeverity = map.eventsOnMap[i].eventSeverity;
                                let eventseveritynum = map.eventsOnMap[i].eventseveritynum;


                                //Creazione dell'icona custom per il pin
                                switch (eventSeverity) {
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

                                let pinIcon = new L.DivIcon({
                                    className: null,
                                    html: '<img src="' + mapPinImg + '" class="leafletPin" />',
                                    iconAnchor: [18, 36]
                                });

                                let markerLocation = new L.LatLng(lat, lng);
                                let marker = new L.Marker(markerLocation, {icon: pinIcon});

                                //Creazione del popup per il pin appena creato
                                popupText = "<span class='mapPopupTitle'>" + eventName + "</span>" +
                                        "<span class='mapPopupLine'><i>Start date</i>: " + eventStartDate + " - " + eventStartTime + "</span>" +
                                        "<span class='mapPopupLine'><i>Event type</i>: " + trafficEventTypes["type" + eventType].desc.toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'><i>Event subtype</i>: " + trafficEventSubTypes["subType" + eventSubtype].toUpperCase() + "</span>" +
                                        "<span class='mapPopupLine'><i>Event severity</i>: " + eventseveritynum + " - <span style='background-color: " + severityColor + "'>" + eventSeverity.toUpperCase() + "</span></span>";

                                fullscreendefaultMapRef.addLayer(marker);
                                lastPopup = marker.bindPopup(popupText, {offset: [-5, -40], maxWidth: 600}).openPopup();
                            }
                            if (map.eventsOnMap[i].eventType === 'trafficRealTimeDetails') {

                                var event = map.eventsOnMap[i];

                                var myMarker = new L.LayerGroup();

                                $.ajax({
                                    //    url: "../trafficRTDetails/sensorsCoord.json",
                                    url: "https://firenzetraffic.km4city.org/trafficRTDetails/sensorsCoord.php",
                                    type: "GET",
                                    async: false,
                                    cache: false,
                                    dataType: 'json',
                                    success: function (_sensors) {
                                        sensors = JSON.parse(_sensors);
                                        for (var i = 0; i < sensors.length; i++) {
                                            if (sensors[i].sensorLat > event.minLat && sensors[i].sensorLat < event.maxLat && sensors[i].sensorLong > event.minLng && sensors[i].sensorLong < event.maxLng) {
                                                var mark = L.circleMarker([sensors[i].sensorLat, sensors[i].sensorLong]);
                                                mark.addTo(myMarker);
                                            }
                                        }
                                        myMarker.addTo(fullscreendefaultMapRef);
                                    }
                                });

                                map.defaultMapRef.on('click', function (e) {
                                    var bnds = map.defaultMapRef.getBounds()
//                                    console.log(bnds.getSouth() + ";" + bnds.getWest() + ";" + bnds.getNorth() + ";" + bnds.getEast());
                                    if (roads == null)
                                        loadRoads();
                                    else {
                                    }
                                });

                                var wktLayer = new L.LayerGroup();
                                var roads = null;
                                var time = 0;

                                loadRoads();

                                function loadRoads() {
                                    defaults = {
                                        icon: new L.DivIcon({className: "geo-icon"}),
                                        editable: true,
                                        color: '#AA0000',
                                        weight: 2.5,
                                        opacity: 1,
                                        fillColor: '#AA0000',
                                        fillOpacity: 1
                                    };

                                    $.ajax({
                                        //    url: "http://localhost/dashboardSmartCity/trafficRTDetails/roads/read.php" + "?sLat=" + event.minLat + "&sLong=" + event.minLng + "&eLat=" + event.maxLat + "&eLong=" + event.maxLng + "&zoom=" + event.zm,
                                        url: "https://firenzetraffic.km4city.org/trafficRTDetails/roads/read.php" + "?sLat=" + event.minLat + "&sLong=" + event.minLng + "&eLat=" + event.maxLat + "&eLong=" + event.maxLng + "&zoom=" + event.zm, // MOD GP
                                        type: "GET",
                                        async: true,
                                        dataType: 'json',
                                        success: function (_roads) {
                                            roads = JSON.parse(JSON.stringify(_roads));

                                            loadDensity();
                                        },
                                        error: function (err) {
                                            console.log(err);
                                            alert("error see log json");
                                        },
                                        // CORTI
                                        complete: function(){
                                            editCSSFor3DWidgets();
                                        }
                                    });
                                }

                                function loadDensity() {
                                    $.ajax({
                                        //    url: "http://localhost/dashboardSmartCity/trafficRTDetails/density/read.php" + "?sLat=" + event.minLat + "&sLong=" + event.minLng + "&eLat=" + event.maxLat + "&eLong=" + event.maxLng + "&zoom=" + event.zm,
                                        url: "https://firenzetraffic.km4city.org/trafficRTDetails/density/read.php" + "?sLat=" + event.minLat + "&sLong=" + event.minLng + "&eLat=" + event.maxLat + "&eLong=" + event.maxLng + "&zoom=" + event.zm, // MOD GP
                                        type: "GET",
                                        async: false,
                                        cache: false,
                                        dataType: 'json',
                                        success: function (_density) {
                                            density = JSON.parse(JSON.stringify(_density));

                                            for (var i = 0; i < roads.length; i++) {
                                                if (density.hasOwnProperty((roads[i].road))) {
                                                    roads[i].data = density[roads[i].road].data;
                                                }
                                            }

                                            time = 0;
                                            draw(time);
//                                            console.log("@time " + time);
                                        },
                                        error: function (err) {
                                            console.log(err);
                                            alert("error see log json");
                                        },
                                        // CORTI
                                        complete: function(){
                                            editCSSFor3DWidgets();
                                        }
                                    });
                                }

                                function draw(t) {
                                    if (roads == null)
                                        return;
                                    //wktLayer.clearLayers();
                                    for (var i = 0; i < roads.length; i++) {
                                        var segs = roads[i].segments;
                                        for (var j = 0; j < segs.length; j++) {
                                            var seg = segs[j];
                                            if (typeof seg.start != "undefined") {
                                                var wktPoint = "POINT(" + seg.start.long + " " + seg.start.lat + ")";
                                                var wktLine = "LINESTRING(" + seg.start.long + " " + seg.start.lat + "," + seg.end.long + " " + seg.end.lat + ")";

                                                try {
                                                    if (!jQuery.isEmptyObject(roads[i].data[0])) {
                                                        var value = Number(roads[i].data[t][seg.id].replace(",", "."));
                                                        //console.log(value);
                                                        var green = 0.3;
                                                        var yellow = 0.6;
                                                        var orange = 0.9;
                                                        if (seg.Lanes == 2) {
                                                            green = 0.6;
                                                            yellow = 1.2;
                                                            orange = 1.8;
                                                        }
                                                        if (seg.FIPILI == 1) {
                                                            green = 0.25;
                                                            yellow = 0.5;
                                                            orange = 0.75;
                                                        }
                                                        if (seg.Lanes == 3) {
                                                            green = 0.9;
                                                            yellow = 1.5;
                                                            orange = 2;
                                                        }
                                                        if (seg.Lanes == 4) {
                                                            green = 1.2;
                                                            yellow = 1.6;
                                                            orange = 2;
                                                        }
                                                        if (seg.Lanes == 5) {
                                                            green = 1.6;
                                                            yellow = 2;
                                                            orange = 2.4;
                                                        }
                                                        if (seg.Lanes == 6) {
                                                            green = 2;
                                                            yellow = 2.4;
                                                            orange = 2.8;
                                                        }
                                                        if (value <= green)
                                                            defaults.color = "#00ff00";
                                                        else if (value <= yellow)
                                                            defaults.color = "#ffff00";
                                                        else if (value <= orange)
                                                            defaults.color = "#ff8c00";
                                                        else
                                                            defaults.color = "#ff0000";
                                                        defaults.fillColor = defaults.color;

                                                        if (!seg.obj) {
                                                            var wkt = new Wkt.Wkt();
                                                            wkt.read(wktLine, "newMap");
                                                            obj = wkt.toObject(defaults);
                                                            obj.addTo(wktLayer);
                                                            seg.obj = obj;

                                                        } else {
                                                            seg.obj.setStyle(defaults);
                                                        }
                                                    }
                                                } catch (e) {
                                                    console.log(e);
                                                }
                                            }
                                        }
                                    }
                                    wktLayer.addTo(fullscreendefaultMapRef);
                                }

                                //Create legend
                                var legend = L.control({position: 'bottomright'});

                                legend.onAdd = function (map) {

                                    var div = L.DomUtil.create('div', 'info legend'),
                                            grades = ["Legend"],
                                            //    labels = ["http://localhost/dash/trafficRTDetails/legend.png"];
                                            labels = ["https://firenzetraffic.km4city.org/trafficRTDetails/legend.png"];   // MOD GP

                                    // loop through our density intervals and generate a label with a colored square for each interval
                                    for (var i = 0; i < grades.length; i++) {
                                        div.innerHTML +=
                                                grades[i] + (" <img src=" + labels[i] + " height='120' width='80' background='#cccccc'>") + '<br>';
                                    }

                                    return div;
                                };

                                legend.addTo(fullscreendefaultMapRef);
                            }
                            if (map.eventsOnMap[i].eventType === 'heatmap' || map.eventsOnMap[i].eventType === undefined) {
                                /*  let cfg = {
                                 // radius should be small ONLY if scaleRadius is true (or small radius is intended)
                                 // if scaleRadius is false it will be the constant radius used in pixels
                                 "radius": 0.0008,
                                 "maxOpacity": .8,
                                 // scales the radius based on map zoom
                                 "scaleRadius": true,
                                 // if set to false the heatmap uses the global maximum for colorization
                                 // if activated: uses the data maximum within the current map boundaries
                                 //   (there will always be a red spot with useLocalExtremas true)
                                 "useLocalExtrema": false,
                                 // which field name in your data represents the latitude - default "lat"
                                 latField: 'latitude',
                                 // which field name in your data represents the longitude - default "lng"
                                 lngField: 'longitude',
                                 // which field name in your data represents the data value - default "value"
                                 valueField: 'value',
                                 gradient: {
                                 // enter n keys between 0 and 1 here
                                 // for gradient color customization
                                 '.0': 'blue',
                                 '.1': 'cyan',
                                 '.2': 'green',
                                 '.3': 'yellowgreen',
                                 '.4': 'yellow',
                                 '.5': 'gold',
                                 '.6': 'orange',
                                 '.7': 'darkorange',
                                 '.8': 'tomato',
                                 '.9': 'orangered',
                                 '1.0': 'red'
                                 }
                                 };*/

                                fullscreendefaultMapRef.off('click');

                                function prepareCustomMarkerForPointAndClickFullScreen(dataObj, color1, color2)
                                {
                                    var latLngId = dataObj.latitude + "" + dataObj.longitude;
                                    latLngId = latLngId.replace(".", "");
                                    latLngId = latLngId.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona

                                    var popupText = '<h3 class="recreativeEventMapTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + '); text-transform: none;">' + dataObj.mapName + '</h3>';
                                    popupText += '<div class="recreativeEventMapBtnContainer"><span data-id="' + latLngId + '" class="recreativeEventMapDetailsBtn recreativeEventMapBtn recreativeEventMapBtnActive" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Heatmap Details</span></div>';

                                    popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer" style="height:100px; width:270px;">';

                                    popupText += '<table id="' + latLngId + '" class="gisPopupGeneralDataTable" style="width:90%">';
                                    //Intestazione
                                    popupText += '<thead>';
                                    popupText += '<th style="background: ' + color2 + '">Description</th>';
                                    popupText += '<th style="background: ' + color2 + '">Value</th>';
                                    popupText += '</thead>';

                                    //Corpo
                                    popupText += '<tbody>';

                                    //    var myKPIFromTimeRangeUTC = new Date(myKPIFromTimeRange).toUTCString();
                                    //    var myKPIFromTimeRangeISO = new Date(myKPIFromTimeRangeUTC).toISOString();
                                    //    var myKPIFromTimeRangeISOTrimmed = myKPIFromTimeRangeISO.substring(0, isoDate.length - 8);

                                    var dateTime = new Date(dataObj.dataTime);// Milliseconds to date
                                    dateTime = dateTime.getDate() + "\/" + parseInt(dateTime.getMonth() + 1) + "\/" + dateTime.getFullYear() + " " + dateTime.getHours() + ":" + dateTime.getMinutes() + ":" + dateTime.getSeconds();

                                    popupText += '<tr><td style="text-align:left; font-size: 12px;">Date & Time:</td><td style="font-size: 12px;">' + dateTime + '</td></tr>';
                                    popupText += '<tr><td style="text-align:left; font-size: 12px;">Metric Name:</td><td style="font-size: 12px;">' + dataObj.metricName + '</td></tr>';
                                    popupText += '<tr><td style="text-align:left; font-size: 12px;">Heatmap Value:</td><td style="font-size: 12px;">' + dataObj.value + '</td></tr>';
                                    popupText += '<tr><td style="text-align:left; font-size: 12px;">Coordinates:</td><td style="font-size: 12px;">' + dataObj.latitude + ', ' + dataObj.longitude + '</td></tr>';

                                    return popupText;
                                }

                                fullscreendefaultMapRef.on('click', function (e) {
                                    if (map.testMetadata.metadata.file != 1) {
                                        var heatmapPointAndClickData = null;
                                        //  alert("Click on Map !");
                                        var pointAndClickCoord = e.latlng;
                                        var pointAndClickLat = pointAndClickCoord.lat.toFixed(5);
                                        var pointAndClickLng = pointAndClickCoord.lng.toFixed(5);
                                        var pointAndClickApiUrl = "https://heatmap.snap4city.org/interp.php?latitude=" + pointAndClickLat + "&longitude=" + pointAndClickLng + "&dataset=" + map.testMetadata.metadata.mapName + "&date=" + map.testMetadata.metadata.date;
                                        $.ajax({
                                            url: pointAndClickApiUrl,
                                            async: true,
                                            success: function (heatmapPointAndClickData) {
                                                var popupData = {};
                                                popupData.mapName = heatmapPointAndClickData.mapName;
                                                popupData.latitude = pointAndClickLat;
                                                popupData.longitude = pointAndClickLng;
                                                popupData.metricName = heatmapPointAndClickData.metricName;
                                                popupData.dataTime = heatmapPointAndClickData.date;
                                                if (heatmapPointAndClickData.value) {
                                                    popupData.value = heatmapPointAndClickData.value.toFixed(5);
                                                    var customPointAndClickContent = prepareCustomMarkerForPointAndClickFullScreen(popupData, "#C2D6D6", "#D1E0E0")
                                                    //   var pointAndClickPopup = L.popup(customPointAndClickMarker).openOn(map.defaultMapRef);
                            
                                                    // CORTI - disable autopan in 3D
                                                    let autoPan = true;
                                                    if(is3DViewOn){
                                                        autoPan = false;
                                                    }

                                                    var popup = L.popup({ autoPan: autoPan })
                                                            .setLatLng(pointAndClickCoord)
                                                            .setContent(customPointAndClickContent)
                                                            .openOn(fullscreendefaultMapRef);
                                                }
                                            },
                                            error: function (errorData) {
                                                console.log("Ko Point&Click Heatmap API");
                                                console.log(JSON.stringify(errorData));
                                            },
                                            // CORTI
                                            complete: function(){
                                                editCSSFor3DWidgets();
                                            }
                                        });
                                    }
                                });

                                // CANCELLARE PRIMA IL LAYER PRCEDENTE !!!

                                if (fullscreenHeatmapFirstInst != true) {
                                    for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                        if (map.eventsOnMap[i].eventType === 'heatmap') {
                                            removeHeatmap(false);
                                            //    removeHeatmapColorLegend(i, false);
                                            //  map.eventsOnMap.splice(i, 1);
                                        } else if (map.eventsOnMap[i].eventType === undefined) {
                                            removeHeatmap(false);
                                        }
                                    }
                                } else {
                                    fullscreenHeatmapFirstInst = false;
                                }

                                legendHeatmapFullscreen = L.control({position: 'topright'});


                                window.addHeatmapFromFullscreenClient = function (animationFlag) {
                                    //  function addHeatMapFromClient() {

                                    var color1 = passedParams.color1;
                                    var color2 = passedParams.color2;
                                    var desc = passedParams.desc;

                                    var loadingDiv = $('<div class="gisMapModalLoadingDiv"></div>');

                                    if ($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapModalLoadingDiv').length > 0) {
                                        loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapModalLoadingDiv').last());
                                    } else {
                                        loadingDiv.insertAfter($('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen'));
                                    }

                                    loadingDiv.css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - ($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapModalLoadingDiv').length * loadingDiv.height())) + "px");
                                    loadingDiv.css("left", ($('#<?= $_REQUEST['name_w'] ?>_div').width() - loadingDiv.width()) + "px");

                                    var loadingText = $('<p class="gisMapModalLoadingDivTextPar">adding <b>' + desc.toLowerCase() + '</b> to map<br><i class="fa fa-circle-o-notch fa-spin" style="font-size: 30px"></i></p>');
                                    var loadOkText = $('<p class="gisMapModalLoadingDivTextPar"><b>' + desc.toLowerCase() + '</b> added to map<br><i class="fa fa-check" style="font-size: 30px"></i></p>');
                                    var loadKoText = $('<p class="gisMapModalLoadingDivTextPar">error adding <b>' + desc.toLowerCase() + '</b> to map<br><i class="fa fa-close" style="font-size: 30px"></i></p>');

                                    loadingDiv.css("background", color1);
                                    loadingDiv.css("background", "-webkit-linear-gradient(left top, " + color1 + ", " + color2 + ")");
                                    loadingDiv.css("background", "-o-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                                    loadingDiv.css("background", "-moz-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                                    loadingDiv.css("background", "linear-gradient(to bottom right, " + color1 + ", " + color2 + ")");

                                    // CORTI - evita loadingDiv nella vista 3D
                                    if (!is3DViewOn) {
                                        loadingDiv.show();
                                    }

                                    loadingDiv.append(loadingText);
                                    loadingDiv.css("opacity", 1);

                                    var parHeight = loadingText.height();
                                    var parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                    loadingText.css("margin-top", parMarginTop + "px");

                                    let heatmap = {};
                                    heatmap.eventType = "heatmap";

                                    /*   map.testData = {
                                     //   max: 8,
                                     data: heatmapData[current_page].data
                                     };  */

                                    //heatmap recommender metadata
                                    map.testMetadata = {
                                        //   max: 8,
                                        metadata: heatmapData[current_page].metadata
                                    };

                                    if (map.testMetadata.metadata.metricName !== undefined) {
                                        heatmapMetricName = map.testMetadata.metadata.metricName
                                    } else {
                                        heatmapMetricName = "airTemperature";
                                        mapName = "WMS_PROVA";
                                    }

                                    if (map.testMetadata.metadata.mapName !== undefined) {
                                        mapName = map.testMetadata.metadata.mapName;
                                    } else {
                                        mapName = "WMS_PROVA";
                                    }

                                    if (map.testMetadata.metadata.date !== undefined) {
                                        mapDate = map.testMetadata.metadata.date;
                                    } else {
                                        mapDate = "DATA";
                                    }

                                    $.ajax({
                                        url: "../controllers/getHeatmapRange.php",
                                        type: "GET",
                                        data: {
                                            metricName: heatmapMetricName
                                        },
                                        async: true,
                                        dataType: 'json',
                                        success: function (data) {
                                            try {
                                                if (data['detail'] == "Ok") {
                                                    //  if (data['heatmapRange'].length > 1) {

                                                    if (data['heatmapRange'][0]) {
                                                        heatmapRange = data['heatmapRange'];
                                                        initHeatmapLayer(heatmapRange);   // OLD-API
                                                        // Gestione della sincronia dei check-box del cambio raggio on zoom e computo raggio su base dati dopo aggiornamento legenda

                                                    } else {
                                                        heatmapRange = [];
                                                    }

                                                    if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP


                                                        let dataQuery = "https://heatmap.snap4city.org/data/" + mapName + "/" + heatmapMetricName + "/" + mapDate.replace(" ", "T") + "Z/0";

                                                        $.ajax({
                                                            url: dataQuery,
                                                            type: "GET",
                                                            data: {
                                                            },
                                                            async: true,
                                                            cache: false,
                                                            dataType: 'json',
                                                            success: function (heatmapResData) {
                                                                if (heatmapResData['data']) {
                                                                    //    heatmapRange = heatmapData['heatmapRange'];
                                                                    initHeatmapLayer(heatmapRange);   // OLD-API
                                                                    // Set current_radius come variabile globale per essere sincronizzata attraverso le varie azioni (zoom ecc...)
                                                                    if (current_radius == null) {
                                                                        current_radius = map.cfg.radius;
                                                                    }
                                                                    if (current_opacity == null) {
                                                                        current_opacity = map.cfg.maxOpacity;
                                                                    }

                                                                } else {
                                                                    heatmapRange = [];
                                                                }

                                                                if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP
                                                                    map.testData = {
                                                                        //   max: 8,
                                                                        data: heatmapResData.data
                                                                    };

                                                                    //heatmap recommender metadata
                                                                    map.testMetadata = {
                                                                        //   max: 8,
                                                                        metadata: heatmapResData.metadata
                                                                    };

                                                                    if (heatmapRange[0].range1Inf == null) {
                                                                        if (heatmapMetricName == "EAQI" || heatmapMetricName == "CAQI") {
                                                                            heatmapRange[0].range1Inf = heatmapRange[0].range4Inf;
                                                                        } else if (heatmapMetricName == "CO" || heatmapMetricName == "Benzene") {
                                                                            heatmapRange[0].range1Inf = heatmapRange[0].range3Inf;
                                                                            heatmapRange[0].range10Inf = heatmapRange[0].range8Inf;
                                                                        }
                                                                    }

                                                                    fullscreenHeatmap.setData({max: heatmapRange[0].range10Inf, min: heatmapRange[0].range1Inf, data: map.testData.data});
                                                                    fullscreendefaultMapRef.addLayer(fullscreenHeatmap);   // OLD HEATMAP
                                                                    //    if (estimateRadiusFlag === true) {
                                                                    var distArray = [];             // MODALITA HEATMAP ON DATA DISTANCE
                                                                    if (heatmapResData.length > 20) {
                                                                        for (k = 0; k < 20; k++) {
                                                                            distArray[k] = distance(heatmapResData[k].latitude, heatmapResData[k].latitude, heatmapResData[k + 1].latitude, heatmapResData[k + 1].latitude, "K");
                                                                        }

                                                                        var sum = 0;
                                                                        for (var i = 0; i < distArray.length; i++) {
                                                                            sum += distArray[i];
                                                                        }
                                                                        estimatedRadius = sum / distArray.length;
                                                                        if (estimatedRadius <= 1) {
                                                                            estimatedRadius = 2;
                                                                        }
                                                                        //   if (estimateRadiusFlag === true) {
                                                                    } else {
                                                                        estimatedRadius = current_radius;
                                                                    }

                                                                    metresPerPixel = 40075016.686 * Math.abs(Math.cos(fullscreendefaultMapRef.getCenter().lat * Math.PI / 180)) / Math.pow(2, fullscreendefaultMapRef.getZoom() + 8);
                                                                    var initRadius = ((estimatedRadius * 1000) / metresPerPixel) / 50;
                                                                    if (current_page == 0) {
                                                                        setOption('radius', initRadius.toFixed(1), 1);
                                                                    } else {
                                                                        setOption('radius', current_radius.toFixed(1), 1);
                                                                    }
                                                                    //   }
                                                                } else {                    // NEW HEATMAP
                                                                    //   var timestampISO = "2019-01-23T20:20:15.000Z";
                                                                    var timestamp = map.testMetadata.metadata.date;
                                                                    var timestampISO = timestamp.replace(" ", "T") + ".000Z";
                                                                    wmsLayerFullscreen = L.tileLayer.wms("https://wmsserver.snap4city.org/geoserver/Snap4City/wms", {
                                                                        layers: 'Snap4City:' + wmsDatasetName,
                                                                        format: 'image/png',
                                                                        crs: L.CRS.EPSG4326,
                                                                        transparent: true,
                                                                        opacity: current_opacity,
                                                                        time: timestampISO,
                                                                        //  bbox: [24.7926004025304,60.1025194986424,25.1905923952885,60.2516802986263],
                                                                        tiled: true
                                                                                //  attribution: "IGN ©"
                                                                    }).addTo(fullscreendefaultMapRef);
                                                                    //   current_opacity = 0.5;

                                                                }

                                                                // add legend to map
                                                                legendHeatmapFullscreen.addTo(fullscreendefaultMapRef);
                                                                map.eventsOnMap.push(heatmap);
                                                                var mapControlsContainer = document.getElementsByClassName("leaflet-control")[0];

                                                                //    var legendImgPath = heatmapRange[0].iconPath;
                                                                //     div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';
                                                                heatmapLegendColorsFullscreen = L.control({position: 'bottomleft'});

                                                                heatmapLegendColorsFullscreen.onAdd = function (map) {

                                                                    var div = L.DomUtil.create('div', 'info legend'),
                                                                            grades = ["Legend"];
                                                                    //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                                                    var legendImgPath = heatmapRange[0].iconPath; // OLD-API
                                                                    div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';    /// OLD-API
                                                                    return div;
                                                                };

                                                                heatmapLegendColorsFullscreen.addTo(fullscreendefaultMapRef);

                                                                if (changeRadiusOnZoom) {
                                                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad').prop('checked', true);
                                                                    if (estimateRadiusFlag) {
                                                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad').prop('disabled', true);
                                                                    }
                                                                }

                                                                if (estimateRadiusFlag) {
                                                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad').prop('checked', true);
                                                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad').prop('disabled', false);
                                                                } else {
                                                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad').prop('disabled', false);
                                                                }

                                                                if (loadingDiv) {
                                                                    loadingDiv.empty();
                                                                    loadingDiv.append(loadOkText);

                                                                    parHeight = loadOkText.height();
                                                                    parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                                    loadOkText.css("margin-top", parMarginTop + "px");

                                                                    setTimeout(function () {
                                                                        loadingDiv.css("opacity", 0);
                                                                        setTimeout(function () {
                                                                            loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapModalLoadingDiv").each(function (i) {
                                                                                $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapModalLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                            });
                                                                            loadingDiv.remove();
                                                                        }, 350);
                                                                    }, 1000);
                                                                }

                                                            },
                                                            error: function (errorData) {
                                                                console.log("Ko Heatmap");
                                                                console.log(JSON.stringify(errorData));

                                                                if (loadingDiv) {
                                                                    loadingDiv.empty();
                                                                    loadingDiv.append(loadKoText);

                                                                    parHeight = loadKoText.height();
                                                                    parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                                    loadKoText.css("margin-top", parMarginTop + "px");

                                                                    setTimeout(function () {
                                                                        loadingDiv.css("opacity", 0);
                                                                        setTimeout(function () {
                                                                            loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapModalLoadingDiv").each(function (i) {
                                                                                $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapModalLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                            });
                                                                            loadingDiv.remove();
                                                                        }, 350);
                                                                    }, 1000);
                                                                }
                                                            },
                                                            // CORTI
                                                            complete: function(){
                                                                editCSSFor3DWidgets();
                                                            }
                                                        });


                                                    } else {
                                                        if (animationFlag === false) {
                                                            // NEW HEATMAP
                                                            var timestamp = map.testMetadata.metadata.date;
                                                            var timestampISO = timestamp.replace(" ", "T") + ".000Z";
                                                            wmsLayerFullscreen = L.tileLayer.wms("https://wmsserver.snap4city.org/geoserver/Snap4City/wms", {
                                                                layers: 'Snap4City:' + wmsDatasetName,
                                                                format: 'image/png',
                                                                crs: L.CRS.EPSG4326,
                                                                transparent: true,
                                                                opacity: current_opacity,
                                                                time: timestampISO,
                                                                //  bbox: [24.7926004025304,60.1025194986424,25.1905923952885,60.2516802986263],
                                                                tiled: true
                                                                        //  attribution: "IGN ©"
                                                            }).addTo(fullscreendefaultMapRef);

                                                            // add legend to map
                                                            legendHeatmapFullscreen.addTo(fullscreendefaultMapRef);
                                                            heatmapLegendColorsFullscreen = L.control({position: 'bottomleft'});

                                                            heatmapLegendColorsFullscreen.onAdd = function (map) {

                                                                var div = L.DomUtil.create('div', 'info legend'),
                                                                        grades = ["Legend"];
                                                                //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                                                var legendImgPath = heatmapRange[0].iconPath;         // OLD-API
                                                                div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';    // OLD-API
                                                                return div;
                                                            };

                                                            heatmapLegendColorsFullscreen.addTo(fullscreendefaultMapRef);
                                                            map.eventsOnMap.push(heatmap);
                                                            //    event.legendColors = heatmapLegendColorsFullscreen;

                                                            if (loadingDiv) {
                                                                loadingDiv.empty();
                                                                loadingDiv.append(loadOkText);

                                                                parHeight = loadOkText.height();
                                                                parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                                loadOkText.css("margin-top", parMarginTop + "px");

                                                                setTimeout(function () {
                                                                    loadingDiv.css("opacity", 0);
                                                                    setTimeout(function () {
                                                                        loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                                            $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                        });
                                                                        loadingDiv.remove();
                                                                    }, 350);
                                                                }, 1000);
                                                            }
                                                        } else {
                                                            // ANIMATION WMS HEATMAP
                                                            var animationCurrentDayTimestamp = [];
                                                            var animationCurrentDayFwdTimestamp = [];
                                                            var animationCurrentDayBckwdTimestamp = [];
                                                            var animationStringTimestamp = "";
                                                            var timestamp = map.testMetadata.metadata.date;
                                                            //    var timestampISO = timestamp.replace(" ", "T") + ".000Z";
                                                            var day = timestamp.substring(0, 10);
                                                            if (current_page == 0) {
                                                                var offsetFwd = current_page;
                                                                while (heatmapData[offsetFwd].metadata['date'].substring(0, 10) == day) {
                                                                    animationCurrentDayFwdTimestamp.push(heatmapData[offsetFwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                                    offsetFwd++;
                                                                }
                                                            } else if (current_page == numHeatmapPages() - 1) {
                                                                var offsetBckwd = current_page - 1;
                                                                while (heatmapData[offsetBckwd].metadata['date'].substring(0, 10) == day) {
                                                                    animationCurrentDayBckwdTimestamp.push(heatmapData[offsetBckwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                                    offsetBckwd--;
                                                                    if (offsetBckwd < 0) {
                                                                        break;
                                                                    }
                                                                }
                                                            } else {
                                                                var offsetFwd = current_page;
                                                                while (heatmapData[offsetFwd].metadata['date'].substring(0, 10) == day) {
                                                                    animationCurrentDayFwdTimestamp.push(heatmapData[offsetFwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                                    offsetFwd++;
                                                                }
                                                                var offsetBckwd = current_page - 1;
                                                                while (heatmapData[offsetBckwd].metadata['date'].substring(0, 10) == day) {
                                                                    animationCurrentDayBckwdTimestamp.push(heatmapData[offsetBckwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                                    offsetBckwd--;
                                                                    if (offsetBckwd < 0) {
                                                                        break;
                                                                    }
                                                                }
                                                            }

                                                            animationCurrentDayTimestamp = animationCurrentDayFwdTimestamp.reverse().concat(animationCurrentDayBckwdTimestamp);
                                                            //    animationCurrentDayTimestamp = animationCurrentDayTimestamp.reverse();
                                                            animationStringTimestamp = animationCurrentDayTimestamp.join(",");

                                                            var bboxJson = {};
                                                            $.ajax({
                                                                url: "https://heatmap.snap4city.org/bbox.php?layer=" + map.testMetadata.metadata.mapName,
                                                                type: "GET",
                                                                async: false,
                                                                dataType: 'json',
                                                                success: function (resultBbox) {
                                                                    bboxJson = resultBbox;
                                                                },
                                                                error: function (errbbox) {
                                                                    alert("Error in retrieving bounding box for current heatmap: " + mapName);
                                                                    console.log(errbbox);
                                                                },
                                                                // CORTI
                                                                complete: function(){
                                                                    editCSSFor3DWidgets();
                                                                }
                                                            });

                                                            var upEastLat = parseFloat(bboxJson['maxy']);
                                                            var upEastLon = parseFloat(bboxJson['maxx']);
                                                            var bottomWestLat = parseFloat(bboxJson['miny']);
                                                            var bottomWestLon = parseFloat(bboxJson['minx']);
                                                            var imageUrl = 'http://wmsserver.snap4city.org/geoserver/wms/animate?LAYERS=' + wmsDatasetName + '&aparam=time&avalues=' + animationStringTimestamp + '&format=image/gif;subtype=animated&format_options=gif_loop_continuosly:true;layout:message;gif_frames_delay:500&transparent=true';
                                                            var imageBounds = [[bottomWestLat, bottomWestLon], [upEastLat, upEastLon]];
                                                            var overlayOpacity = current_opacity;

                                                            // ANIMATED GIF LAYER
                                                            var animatedLayer = L.imageOverlay(imageUrl, imageBounds, {opacity: overlayOpacity}).addTo(fullscreendefaultMapRef);

                                                            // add legend to map
                                                            map.legendHeatmap.addTo(map.defaultMapRef);
                                                            //    $("<?= $_REQUEST['name_w'] ?>_animation").prop("checked",true);
                                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_animation").checked = true;
                                                            //     $("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider({ disabled: "true" });
                                                            $("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider('disable');
                                                            //     document.getElementById("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider({ disabled: "true" });
                                                            //     document.getElementById("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider({ disabled: "true" });
                                                            map.eventsOnMap.push(animatedLayer);
                                                            var mapControlsContainer = document.getElementsByClassName("leaflet-control")[0];

                                                            var heatmapLegendColors = L.control({position: 'bottomleft'});

                                                            heatmapLegendColors.onAdd = function (map) {

                                                                var div = L.DomUtil.create('div', 'info legend'),
                                                                        grades = ["Legend"];
                                                                //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                                                var legendImgPath = heatmapRange[0].iconPath; // OLD-API
                                                                div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';    /// OLD-API
                                                                return div;
                                                            };

                                                            // add legend to map
                                                            legendHeatmapFullscreen.addTo(fullscreendefaultMapRef);
                                                            map.eventsOnMap.push(heatmap);
                                                            var mapControlsContainer = document.getElementsByClassName("leaflet-control")[0];

                                                            //    var legendImgPath = heatmapRange[0].iconPath;
                                                            //     div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';
                                                            heatmapLegendColorsFullscreen = L.control({position: 'bottomleft'});

                                                            heatmapLegendColorsFullscreen.onAdd = function (map) {

                                                                var div = L.DomUtil.create('div', 'info legend'),
                                                                        grades = ["Legend"];
                                                                //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                                                var legendImgPath = heatmapRange[0].iconPath; // OLD-API
                                                                div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';    /// OLD-API
                                                                return div;
                                                            };

                                                            heatmapLegendColorsFullscreen.addTo(fullscreendefaultMapRef);

                                                            if (changeRadiusOnZoom) {
                                                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad').prop('checked', true);
                                                                if (estimateRadiusFlag) {
                                                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad').prop('disabled', true);
                                                                }
                                                            }

                                                            if (estimateRadiusFlag) {
                                                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad').prop('checked', true);
                                                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad').prop('disabled', false);
                                                            } else {
                                                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad').prop('disabled', false);
                                                            }

                                                            if (loadingDiv) {
                                                                loadingDiv.empty();
                                                                loadingDiv.append(loadOkText);

                                                                parHeight = loadOkText.height();
                                                                parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                                loadOkText.css("margin-top", parMarginTop + "px");

                                                                setTimeout(function () {
                                                                    loadingDiv.css("opacity", 0);
                                                                    setTimeout(function () {
                                                                        loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapModalLoadingDiv").each(function (i) {
                                                                            $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapModalLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                        });
                                                                        loadingDiv.remove();
                                                                    }, 350);
                                                                }, 1000);
                                                            }
                                                        }
                                                    }

                                                } else {
                                                    console.log("Ko Heatmap");
                                                    console.log(JSON.stringify(errorData));

                                                    if (loadingDiv) {
                                                        loadingDiv.empty();
                                                        loadingDiv.append(loadKoText);

                                                        parHeight = loadKoText.height();
                                                        parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                        loadKoText.css("margin-top", parMarginTop + "px");

                                                        setTimeout(function () {
                                                            loadingDiv.css("opacity", 0);
                                                            setTimeout(function () {
                                                                loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                                    $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                                });
                                                                loadingDiv.remove();
                                                            }, 350);
                                                        }, 1000);
                                                    }
                                                }
                                            } catch (err) {
                                                if (loadingDiv) {
                                                    loadingDiv.empty();
                                                    loadingDiv.append(loadKoText);

                                                    parHeight = loadKoText.height();
                                                    parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                    loadKoText.css("margin-top", parMarginTop + "px");
                                                    console.log("Error: " + err);
                                                    setTimeout(function () {
                                                        loadingDiv.css("opacity", 0);
                                                        setTimeout(function () {
                                                            loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                                $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                            });
                                                            loadingDiv.remove();
                                                        }, 350);
                                                    }, 1000);
                                                }
                                            }
                                        },
                                        error: function (errorData) {
                                            console.log("Ko Heatmap");
                                            console.log(JSON.stringify(errorData));

                                            if (loadingDiv) {
                                                loadingDiv.empty();
                                                loadingDiv.append(loadKoText);

                                                parHeight = loadKoText.height();
                                                parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                                loadKoText.css("margin-top", parMarginTop + "px");

                                                setTimeout(function () {
                                                    loadingDiv.css("opacity", 0);
                                                    setTimeout(function () {
                                                        loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                                            $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                                        });
                                                        loadingDiv.remove();
                                                    }, 350);
                                                }, 1000);
                                            }
                                        },
                                        // CORTI
                                        complete: function(){
                                            editCSSFor3DWidgets();
                                        }
                                    });

                                }


                                function distance(lat1, lon1, lat2, lon2, unit) {   // unit: 'K' for Kilometers
                                    if ((lat1 == lat2) && (lon1 == lon2)) {
                                        return 0;
                                    } else {
                                        var radlat1 = Math.PI * lat1 / 180;
                                        var radlat2 = Math.PI * lat2 / 180;
                                        var theta = lon1 - lon2;
                                        var radtheta = Math.PI * theta / 180;
                                        var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                                        if (dist > 1) {
                                            dist = 1;
                                        }
                                        dist = Math.acos(dist);
                                        dist = dist * 180 / Math.PI;
                                        dist = dist * 60 * 1.1515;
                                        if (unit == "K") {
                                            dist = dist * 1.609344
                                        }
                                        if (unit == "N") {
                                            dist = dist * 0.8684
                                        }
                                        return dist;
                                    }
                                }

                                function getRadius() {
                                    var radius;
                                    var currentZoom = fullscreendefaultMapRef.getZoom();
                                    if (estimateRadiusFlag && estimatedRadius) {
                                        metresPerPixel = 40075016.686 * Math.abs(Math.cos(fullscreendefaultMapRef.getCenter().lat * Math.PI / 180)) / Math.pow(2, currentZoom + 8);
                                        radius = ((estimatedRadius * 1000) / metresPerPixel) / 50;
                                        if (radius > 1000) {

                                        } else if (radius > 1) {
                                            if (currentZoom < prevZoom) {
                                                prevZoom = currentZoom;
                                                return radius / 1.2;
                                            } else {
                                                prevZoom = currentZoom;
                                                return radius / 1.2;
                                            }
                                        } else {
                                            prevZoom = currentZoom;
                                            return 1;
                                        }
                                    }
                                    if (prevZoom == null) {
                                        prevZoom = widgetParameters.zoom;
                                    }
                                    if (currentZoom === 7) {
                                        radius = 1;
                                    } else if (currentZoom === 8) {
                                        radius = 1;
                                    } else if (currentZoom === 9) {
                                        radius = 1;
                                    } else if (currentZoom === 10) {
                                        if (currentZoom > prevZoom) {
                                            radius = 2;
                                        } else {
                                            radius = 1;
                                        }
                                    } else if (currentZoom === 11) {
                                        if (currentZoom > prevZoom) {
                                            radius = 3.5;
                                        } else {
                                            radius = 2;
                                        }
                                    } else if (currentZoom === 12) {
                                        if (currentZoom > prevZoom) {
                                            radius = 10;
                                        } else {
                                            radius = 3.5;
                                        }
                                    } else if (currentZoom === 13) {
                                        if (currentZoom > prevZoom) {
                                            radius = 16;
                                        } else {
                                            radius = 10;
                                        }
                                    } else if (currentZoom === 14) {
                                        if (currentZoom > prevZoom) {
                                            radius = 31;
                                        } else {
                                            radius = 16;
                                        }
                                    } else if (currentZoom === 15) {
                                        if (currentZoom > prevZoom) {
                                            radius = 60;
                                        } else {
                                            radius = 31;
                                        }
                                    } else if (currentZoom === 16) {
                                        if (currentZoom > prevZoom) {
                                            radius = 80;
                                        } else {
                                            radius = 60;
                                        }
                                    } else if (currentZoom === 17) {
                                        if (currentZoom > prevZoom) {
                                            radius = 100;
                                        } else {
                                            radius = 80;
                                        }
                                    } else if (currentZoom === 18) {
                                        if (currentZoom > prevZoom) {
                                            radius = 130;
                                        } else {
                                            radius = 100;
                                        }
                                    }
                                    prevZoom = currentZoom;
                                    return radius;
                                }

                                //    fullscreendefaultMapRef.on('zoomstart', function(ev) {
                                fullscreendefaultMapRef.on('zoomend', function (ev) {
                                    // zoom level changed... adjust heatmap layer options!
                                    if (changeRadiusOnZoom === true) {
                                        if (prevZoom === null) {
                                            prevZoom = widgetParameters.zoom;
                                        }

                                        if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP
                                            // INSERIRE CAMBIO SLIDER ZOOM
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_sliderradius").value = parseFloat(getRadius()).toFixed(1);
                                            setOption('radius', getRadius(), 1)           // MODALITA HEATMAP ON ZOOM
                                        }
                                    }
                                });

                                function initHeatmapLayer(heatmapRangeObject) {

                                    var heatmapCfg = {};

                                    map.cfg = JSON.parse(heatmapRangeObject[0].leafletConfigJSON);
                                    //    map.cfg['blur'] = 0.85;

                                    if (current_radius != null) {
                                        map.cfg['radius'] = current_radius;
                                    }
                                    if (current_opacity != null) {
                                        map.cfg['maxOpacity'] = current_opacity;
                                    }

                                    fullscreenHeatmap = new HeatmapOverlay(map.cfg);
                                    //map.heatmapLayer.zIndex = 20;
                                    //  map.legendHeatmap = L.control({position: 'topright'});
                                }

                                function nextHeatmapPage()
                                {
                                    animationFlag = false;
                                    if (current_page > 0) {
                                        current_page--;
                                        changeHeatmapPage(current_page);

                                        if (!is3DViewOn) {
                                            for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                                if (map.eventsOnMap[i].eventType === 'heatmap') {
                                                    removeHeatmap(false);
                                                    map.eventsOnMap.splice(i, 1);
                                                } else if (map.eventsOnMap[i].eventType === undefined && map.eventsOnMap[i].type === undefined) {
                                                    fullscreendefaultMapRef.eachLayer(function (layer) {
                                                        fullscreendefaultMapRef.removeLayer(layer);
                                                    });
                                                    removeHeatmap(false);
                                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                                        maxZoom: 18
                                                    }).addTo(fullscreendefaultMapRef);
                                                }
                                            }
                                        }
                                        if (addMode === 'additive') {
                                            //  if (baseQuery.includes("heatmap.php")) {
                                            // addHeatmapToMap();
                                            addHeatmapFromFullscreenClient(false);
                                            /*    } else {
                                             // addHeatmapFromWMSClient();        // TBD
                                             }*/
                                        }
                                        if (addMode === 'exclusive') {
                                            fullscreendefaultMapRef.eachLayer(function (layer) {
                                                fullscreendefaultMapRef.removeLayer(layer);
                                            });
                                            map.eventsOnMap.length = 0;

                                            //Remove WidgetAlarm active pins
                                            $.event.trigger({
                                                type: "removeAlarmPin",
                                            });
                                            //Remove WidgetEvacuationPlans active pins
                                            $.event.trigger({
                                                type: "removeEvacuationPlanPin",
                                            });
                                            //Remove WidgetEvents active pins
                                            $.event.trigger({
                                                type: "removeEventFIPin",
                                            });
                                            //Remove WidgetResources active pins
                                            $.event.trigger({
                                                type: "removeResourcePin",
                                            });
                                            //Remove WidgetOperatorEvents active pins
                                            $.event.trigger({
                                                type: "removeOperatorEventPin",
                                            });
                                            //Remove WidgetTrafficEvents active pins
                                            $.event.trigger({
                                                type: "removeTrafficEventPin",
                                            });
                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                                maxZoom: 18
                                            }).addTo(fullscreendefaultMapRef);

                                            addHeatmapFromClient();
                                        }

                                    }
                                }

                                //   window.nextHeatmapPage = function()
                                function prevHeatmapPage()
                                {
                                    animationFlag = false;
                                    if (current_page < numHeatmapPages() - 1) {
                                        current_page++;
                                        changeHeatmapPage(current_page);

                                        for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                            if (map.eventsOnMap[i].eventType === 'heatmap') {
                                                removeHeatmap(false);
                                                //    removeHeatmapColorLegend(i, false);
                                                map.eventsOnMap.splice(i, 1);
                                            } else if (map.eventsOnMap[i].eventType === undefined && map.eventsOnMap[i].type === undefined) {
                                                //  fullscreendefaultMapRef.removeLayer(map.eventsOnMap[i]);
                                                fullscreendefaultMapRef.eachLayer(function (layer) {
                                                    fullscreendefaultMapRef.removeLayer(layer);
                                                });
                                                removeHeatmap(false);
                                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                    attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                                    maxZoom: 18
                                                }).addTo(fullscreendefaultMapRef);
                                            }
                                        }

                                        if (addMode === 'additive') {
                                            //  if (baseQuery.includes("heatmap.php")) {
                                            // addHeatmapToMap();
                                            addHeatmapFromFullscreenClient(false);
                                            /*    } else {
                                             // addHeatmapFromWMSClient();        // TBD
                                             }*/
                                        }
                                        if (addMode === 'exclusive') {
                                            fullscreendefaultMapRef.eachLayer(function (layer) {
                                                fullscreendefaultMapRef.removeLayer(layer);
                                            });
                                            map.eventsOnMap.length = 0;

                                            //Remove WidgetAlarm active pins
                                            $.event.trigger({
                                                type: "removeAlarmPin",
                                            });
                                            //Remove WidgetEvacuationPlans active pins
                                            $.event.trigger({
                                                type: "removeEvacuationPlanPin",
                                            });
                                            //Remove WidgetEvents active pins
                                            $.event.trigger({
                                                type: "removeEventFIPin",
                                            });
                                            //Remove WidgetResources active pins
                                            $.event.trigger({
                                                type: "removeResourcePin",
                                            });
                                            //Remove WidgetOperatorEvents active pins
                                            $.event.trigger({
                                                type: "removeOperatorEventPin",
                                            });
                                            //Remove WidgetTrafficEvents active pins
                                            $.event.trigger({
                                                type: "removeTrafficEventPin",
                                            });
                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                                maxZoom: 18
                                            }).addTo(fullscreendefaultMapRef);

                                            addHeatmapFromFullscreenClient(false);
                                        }
                                    }
                                }

                                function animateFullscreenHeatmap()
                                {
                                    for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                        if (map.eventsOnMap[i].eventType === 'heatmap') {
                                            removeHeatmap(false);
                                            map.eventsOnMap.splice(i, 1);
                                        } else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                            removeHeatmapColorLegend(i, false);
                                            map.eventsOnMap.splice(i, 1);
                                        } else if (map.eventsOnMap[i] !== null && map.eventsOnMap[i] !== undefined) {
                                            map.defaultMapRef.removeLayer(map.eventsOnMap[i]);
                                            map.eventsOnMap.splice(i, 1);
                                        }
                                    }
                                    if (animationFlag === false) {
                                        animationFlag = true;
                                        addHeatmapFromFullscreenClient(animationFlag);
                                    } else {
                                        animationFlag = false;
                                        for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                            if (map.eventsOnMap[i].eventType === 'heatmap') {
                                                removeHeatmap(false);
                                                //    removeHeatmapColorLegend(i, false);
                                                map.eventsOnMap.splice(i, 1);
                                            } /*else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                             removeHeatmapColorLegend(i, false);
                                             map.eventsOnMap.splice(i, 1);
                                             }*/
                                        }
                                        addHeatmapFromFullscreenClient(animationFlag);
                                    }
                                }

                                function changeHeatmapPage(page)
                                {
                                    var btn_next = document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_nextButt");
                                    var btn_prev = document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_prevButt");
                                    var heatmapDescr = document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_heatMapDescr");

                                    // Validate page
                                    if (numHeatmapPages() > 1) {
                                        if (page < 1)
                                            page = 1;
                                        if (page > numHeatmapPages())
                                            page = numHeatmapPages();

                                        if (current_page == 0) {
                                            if (!is3DViewOn) {
                                                btn_next.style.visibility = "hidden";
                                            } else {
                                                $('.heatmap-3d-controls').find('.next').addClass('hidden');
                                            }
                                        } else {
                                            if (!is3DViewOn) {
                                                btn_next.style.visibility = "visible";
                                            } else {
                                                $('.heatmap-3d-controls').find('.next').removeClass('hidden');
                                            }
                                        }

                                        if (current_page == numHeatmapPages() - 1) {
                                            if (!is3DViewOn) {
                                                btn_prev.style.visibility = "hidden";
                                            } else {
                                                $('.heatmap-3d-controls').find('.prev').addClass('hidden');
                                            }
                                        } else {
                                            if (!is3DViewOn) {
                                                btn_prev.style.visibility = "visible";
                                            } else {
                                                $('.heatmap-3d-controls').find('.prev').removeClass('hidden');
                                            }
                                        }
                                    }

                                    if (current_page < numHeatmapPages()) {
                                        //  $("#modalLinkOpenHeatMapDescr").text(heatmapData[current_page].metadata[0].date); // OLD-API
                                        //   heatmapDescr.text(heatmapData[current_page].metadata.date);
//                                        heatmapDescr.firstChild.wholeText = heatmapData[current_page].metadata.date;
                                        // heatmapData[current_page].metadata[0].date
                                    }
                                }

                                function numHeatmapPages()
                                {
                                    //   return Math.ceil(heatmapData.length / records_per_page);
                                    return heatmapData.length;
                                }


                                function updateChangeRadiusOnZoom(htmlElement) {
                                    if (htmlElement.checked) {
                                        changeRadiusOnZoom = true;
                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad").attr('disabled', false);
                                    } else {
                                        changeRadiusOnZoom = false;
                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad").attr('disabled', true);
                                    }
                                    //  $("#radiusEstCnt").toggle(htmlElement.checked);
                                }

                                function computeRadiusOnData(htmlElement) {
                                    if (htmlElement.checked) {
                                        estimateRadiusFlag = true;
                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad").attr('disabled', true);
                                    } else {
                                        estimateRadiusFlag = false;
                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad").attr('disabled', false);
                                    }
                                }

                                function setOption(option, value, decimals) {
                                    if (baseQuery.includes("heatmap.php")) {
                                        if (option == "radius") {       // AGGIUNGERE SE FLAG è TRUE SI METTE IL VALORE DI CONFIG
                                            if (resetPageFlag) {
                                                if (resetPageFlag === true) {
                                                    current_radius = map.cfg['radius'];
                                                } else {
                                                    current_radius = Math.max(value, 2);
                                                }
                                            } else {
                                                current_radius = Math.max(value, 2);
                                            }
                                            map.cfg["radius"] = current_radius.toFixed(1);
                                            if (decimals) {
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_range" + option).text(parseFloat(current_radius).toFixed(parseInt(decimals)));
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + option).attr("value", parseFloat(current_radius).toFixed(parseInt(decimals)));
                                            }
                                        } else if (option == "maxOpacity") {
                                            if (resetPageFlag) {
                                                if (resetPageFlag === true) {
                                                    current_opacity = map.cfg['maxOpacity'];
                                                } else {
                                                    current_opacity = value;
                                                }
                                            } else {
                                                current_opacity = value;
                                            }
                                            map.cfg["maxOpacity"] = current_opacity;
                                            if (decimals) {
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_range" + option).text(parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + option).attr("value", parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                            }
                                        }
                                        // update the heatmap with the new configuration
                                        //  map.heatmapLayer.configure(map.cfg);
                                        fullscreenHeatmap.configure(map.cfg);
                                    } else {
                                        if (option == "maxOpacity") {
                                            if (wmsLayerFullscreen) {
                                                wmsLayerFullscreen.setOpacity(value);
                                                current_opacity = value;
                                                if (decimals) {
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_range" + option).text(parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                                    $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + option).attr("value", parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                                }
                                            }
                                        }
                                        fullscreenHeatmap.configure(map.cfg);
                                    }

                                }

                                /*    function setOption(option, value, decimals) {
                                 if (baseQuery.includes("heatmap.php")) {
                                 if (option == "radius") {       // AGGIUNGERE SE FLAG è TRUE SI METTE IL VALORE DI CONFIG
                                 if (resetPageFlag) {
                                 if (resetPageFlag === true) {
                                 current_radius = map.cfg['radius'];
                                 } else {
                                 current_radius = Math.max(value, 2);
                                 }
                                 } else {
                                 current_radius = Math.max(value, 2);
                                 }
                                 map.cfg["radius"] = current_radius.toFixed(1);
                                 if (decimals) {
                                 $("#<?= $_REQUEST['name_w'] ?>_range" + option).text(parseFloat(current_radius).toFixed(parseInt(decimals)));
                                 $("#<?= $_REQUEST['name_w'] ?>_slider" + option).attr("value", parseFloat(current_radius).toFixed(parseInt(decimals)));
                                 }
                                 } else if (option == "maxOpacity") {
                                 if (resetPageFlag) {
                                 if (resetPageFlag === true) {
                                 current_opacity = map.cfg['maxOpacity'];
                                 } else {
                                 current_opacity = value;
                                 }
                                 } else {
                                 current_opacity = value;
                                 }
                                 map.cfg["maxOpacity"] = current_opacity;
                                 if (decimals) {
                                 $("#<?= $_REQUEST['name_w'] ?>_range" + option).text(parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                 $("#<?= $_REQUEST['name_w'] ?>_slider" + option).attr("value", parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                 }
                                 }
                                 // update the heatmap with the new configuration
                                 map.heatmapLayer.configure(map.cfg);
                                 } else {
                                 if (option == "maxOpacity") {
                                 if (wmsLayerFullscreen) {
                                 wmsLayerFullscreen.setOpacity(value);
                                 current_opacity = value;
                                 if (decimals) {
                                 $("#<?= $_REQUEST['name_w'] ?>_range" + option).text(parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                 $("#<?= $_REQUEST['name_w'] ?>_slider" + option).attr("value", parseFloat(current_opacity).toFixed(parseInt(decimals)));
                                 }
                                 // });
                                 }
                                 }
                                 }
                                 map.heatmapLayer.configure(map.cfg);
                                 }   */

                                /*    function setOption(option, value, decimals) {
                                 if (baseQuery.includes("heatmap.php")) {
                                 cfg[option] = value;
                                 if (decimals) {
                                 $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_range" + option).text(parseFloat(value).toFixed(parseInt(decimals)));
                                 $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + option).attr("value", parseFloat(value).toFixed(parseInt(decimals)));
                                 }
                                 if (option == "radius") {       // AGGIUNGERE SE FLAG è TRUE SI METTE IL VALORE DI CONFIG
                                 if (resetPageFlag) {
                                 if (resetPageFlag === true) {
                                 current_radius = map.cfg['radius'];
                                 } else {
                                 current_radius = Math.max(value, 2);
                                 }
                                 } else {
                                 current_radius = Math.max(value, 2);
                                 }
                                 } else if (option == "maxOpacity") {
                                 if (resetPageFlag) {
                                 if (resetPageFlag === true) {
                                 current_opacity = map.cfg['maxOpacity'];
                                 } else {
                                 current_opacity = value;
                                 }
                                 } else {
                                 current_opacity = value;
                                 }
                                 }
                                 // update the heatmap with the new configuration
                                 fullscreenHeatmap.configure(cfg);
                                 } else {
                                 if (option == "maxOpacity") {
                                 if (wmsLayerFullscreen) {
                                 // wmsLayerFullscreen.eachLayer(function (layer) {
                                 var density = wmsLayerFullscreen.options["opacity"];
                                 wmsLayerFullscreen.setStyle(getStyle(density));
                                 current_opacity = value;
                                 // });
                                 }
                                 }
                                 }
                                 }   */

                                function upSlider(color, step, decimals, max) {
                                    let value = $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + color).attr("value");
                                    if (parseFloat(parseFloat(value) + parseFloat(step)) <= max) {
                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_range" + color).text(parseFloat(parseFloat(value) + parseFloat(step)).toFixed(parseInt(decimals)));
                                        document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + color).value = parseFloat(parseFloat(value) + parseFloat(step)).toFixed(parseInt(decimals));
                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + color).trigger('change');
                                    }
                                }

                                function downSlider(color, step, decimals, min) {
                                    let value = $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + color).attr("value");
                                    if (parseFloat(parseFloat(value) - parseFloat(step)) >= min) {
                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_range" + color).text(parseFloat(parseFloat(value) - parseFloat(step)).toFixed(parseInt(decimals)));
                                        document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + color).value = parseFloat(parseFloat(value) - parseFloat(step)).toFixed(parseInt(decimals));
                                        $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slider" + color).trigger('change');
                                    }
                                }

                                function removeHeatmap(resetPageFlag) {
                                    if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                        if (resetPageFlag == true) {
                                            current_page = 0;     // CTR SE VA BENE BISOGNA DISTINGUERE IL CASO CHE SI STIA NAVIGANDO LA STESSA HEATMAP_NAME OPPURE UN'ALTRA NUOVA HEATMP_NAME
                                            current_radius = null;
                                            current_opacity = null;
                                            changeRadiusOnZoom = false;
                                            estimateRadiusFlag = false;
                                            estimatedRadius = null;
                                            wmsDatasetName = null;
                                        }
                                        //   map.testData = [];
                                        if (wmsLayerFullscreen) {
                                            fullscreendefaultMapRef.removeLayer(wmsLayerFullscreen);
                                            wmsLayerFullscreen = null;
                                        } else {
                                            fullscreenHeatmap.setData({data: []});
                                            fullscreendefaultMapRef.removeLayer(fullscreenHeatmap);
                                            fullscreenHeatmap = null;
                                        }
                                        if (resetPageFlag != true) {
                                            if (map.cfg["radius"] != current_radius) {
                                                setOption('radius', current_radius, 1);
                                            }
                                            if (map.cfg["maxOpacity"] != current_opacity) {
                                                setOption('maxOpacity', current_opacity, 2);
                                            }
                                        }
                                        fullscreendefaultMapRef.removeControl(legendHeatmapFullscreen);
                                        if (heatmapLegendColorsFullscreen) {
                                            fullscreendefaultMapRef.removeControl(heatmapLegendColorsFullscreen);
                                        }
                                    } else {    // NEW WMS HEATMAP
                                        if (resetPageFlag == true) {
                                            current_page = 0;
                                        }
                                        if (fullscreenHeatmap) {
                                            fullscreenHeatmap.setData({data: []});
                                            fullscreendefaultMapRef.removeLayer(fullscreenHeatmap);
                                            fullscreenHeatmap = null;
                                        }
                                        if (wmsLayerFullscreen) {
                                            fullscreendefaultMapRef.removeLayer(wmsLayerFullscreen);
                                            wmsLayerFullscreen = null;
                                        }
                                        fullscreendefaultMapRef.removeControl(legendHeatmapFullscreen);
                                        fullscreendefaultMapRef.removeControl(heatmapLegendColorsFullscreen);
                                    }
                                }

                                function removeHeatmapColorLegend(index, resetPageFlag) {
                                    if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                        if (resetPageFlag == true) {
                                            current_page = 0;     // CTR SE VA BENE BISOGNA DISTINGUERE IL CASO CHE SI STIA NAVIGANDO LA STESSA HEATMAP_NAME OPPURE UN'ALTRA NUOVA HEATMP_NAME
                                            current_radius = null;
                                            current_opacity = null;
                                            changeRadiusOnZoom = false;
                                            estimateRadiusFlag = false;
                                            estimatedRadius = null;
                                            wmsDatasetName = null;
                                        }
                                        map.testData = [];
                                        fullscreenHeatmap.setData({data: []});
                                        fullscreendefaultMapRef.removeLayer(fullscreenHeatmap);
                                        if (resetPageFlag != true) {
                                            if (map.cfg["radius"] != current_radius) {
                                                setOption('radius', current_radius, 1);
                                            }
                                            if (map.cfg["maxOpacity"] != current_opacity) {
                                                setOption('maxOpacity', current_opacity, 2);
                                            }
                                        }
                                        //    fullscreendefaultMapRef.removeControl(map.eventsOnMap[index].legendColors);
                                        fullscreendefaultMapRef.removeControl(heatmapLegendColorsFullscreen);
                                    } else {    // NEW WMS HEATMAP
                                        if (resetPageFlag == true) {
                                            current_page = 0;
                                        }
                                        //    fullscreendefaultMapRef.removeControl(map.eventsOnMap[index].legendColors);
                                        fullscreendefaultMapRef.removeControl(heatmapLegendColorsFullscreen);
                                        if (wmsLayerFullscreen) {
                                            fullscreendefaultMapRef.removeLayer(wmsLayerFullscreen);
                                        }
                                    }
                                }

                                function updateChangeRadiusOnZoom(htmlElement) {
                                    if (htmlElement.checked) {
                                        changeRadiusOnZoom = true;
                                        $("#<?= $_REQUEST['name_w'] ?>_estimateRad").attr('disabled', false);
                                    } else {
                                        changeRadiusOnZoom = false;
                                        $("#<?= $_REQUEST['name_w'] ?>_estimateRad").attr('disabled', true);
                                    }
                                    //  $("#radiusEstCnt").toggle(htmlElement.checked);
                                }

                                function computeRadiusOnData(htmlElement) {
                                    if (htmlElement.checked) {
                                        estimateRadiusFlag = true;
                                        $("#<?= $_REQUEST['name_w'] ?>_changeRad").attr('disabled', true);
                                    } else {
                                        estimateRadiusFlag = false;
                                        $("#<?= $_REQUEST['name_w'] ?>_changeRad").attr('disabled', false);
                                    }
                                }

                                legendHeatmapFullscreen.onAdd = function () {
                                    let legendHeatmapDiv = L.DomUtil.create('div');
                                    legendHeatmapDiv.id = "heatmapLegend";
                                    // disable interaction of this div with map
                                    if (L.Browser.touch) {
                                        L.DomEvent.disableClickPropagation(legendHeatmapDiv);
                                        L.DomEvent.on(legendHeatmapDiv, 'mousewheel', L.DomEvent.stopPropagation);
                                    } else {
                                        L.DomEvent.on(legendHeatmapDiv, 'click', L.DomEvent.stopPropagation);
                                    }
                                    legendHeatmapDiv.style.width = "340px";
                                    legendHeatmapDiv.style.fontWeight = "bold";
                                    legendHeatmapDiv.style.background = "#cccccc";
                                    //map.legendHeatmap.style.background = "-webkit-gradient(linear, left top, left bottom, from(#eeeeee), to(#cccccc))";
                                    legendHeatmapDiv.style.padding = "10px";

                                    //categories = ['blue', 'cyan', 'green', 'yellowgreen', 'yellow', 'gold', 'orange', 'darkorange', 'tomato', 'orangered', 'red'];
                                    let colors = [];
                                    /*   colors['blue'] = '#0000FF';
                                     colors['cyan'] = '#00FFFF';
                                     colors['green'] = '#008000';
                                     colors['yellowgreen'] = '#9ACD32';
                                     colors['yellow'] = '#FFFF00';
                                     colors['gold'] = '#FFD700';
                                     colors['orange'] = '#FFA500';
                                     colors['darkorange'] = '#FF8C00';
                                     colors['orangered'] = '#FF4500';
                                     colors['tomato'] = '#FF6347';
                                     colors['red'] = '#FF0000';  */
                                    colors['blue'] = 'rgb(0,0,255)';
                                    colors['cyan'] = 'rgb(0,153,255)';
                                    colors['green'] = 'rgb(0,153,0)';
                                    colors['yellowgreen'] = 'rgb(0,255,0)';
                                    colors['yellow'] = 'rgb(255,255,0)';
                                    colors['gold'] = 'rgb(255,187,0)';
                                    colors['orange'] = 'rgb(255,102,0)';
                                    colors['red'] = 'rgb(255,0,0)';
                                    colors['darkred'] = 'rgb(153,0,0)';
                                    colors['maroon'] = 'rgb(84, 0, 0)';
                                    //   colors['red'] = '#FF0000';
                                    let colors_value = [];
                                    colors_value['blue'] = 'rgb(0,0,255)';
                                    colors_value['cyan'] = 'rgb(0,153,255)';
                                    colors_value['green'] = 'rgb(0,153,0)';
                                    colors_value['yellowgreen'] = 'rgb(0,255,0)';
                                    colors_value['yellow'] = 'rgb(255,255,0)';
                                    colors_value['gold'] = 'rgb(255,187,0)';
                                    colors_value['orange'] = 'rgb(255,102,0)';
                                    colors_value['red'] = 'rgb(255,0,0)';
                                    colors_value['darkred'] = 'rgb(153,0,0)';
                                    colors_value['maroon'] = 'rgb(84, 0, 0)';
                                    //  colors_value['red'] = '#FF0000';
                                    //   legendHeatmapDiv.innerHTML += '<div class="textTitle" style="text-align:center">' + heatmapMetricName + '</div>';
                                    //    legendHeatmapDiv.innerHTML += '<div class="textTitle" style="text-align:center">' + map.testMetadata.metadata[0].mapName + + '</div>'; // OLD-API
                                    legendHeatmapDiv.innerHTML += '<div class="textTitle" style="text-align:center">' + mapName + '</div>';
                                    legendHeatmapDiv.innerHTML += '<div class="text">' + '<?php echo ucfirst(isset($_REQUEST["profile"]) ? $_REQUEST["profile"] : "Heatmap Controls"); ?>' + '</div>';
                                    /*  if (!baseQuery.includes("heatmap.php")) {
                                     legendHeatmapDiv.innerHTML += '<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_controlsContainer" style="height:20px"><div class="text"  style="width:50%; float:left">' + '<?php echo ucfirst(isset($_REQUEST["profile"]) ? $_REQUEST["profile"] : "Heatmap Controls:"); ?></div><div class="text" style="width:50%; float:right"><label class="switch"><input type="checkbox" id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_animation"><div class="slider round"><span class="animationOn"></span><span class="animationOff" style="color: black; text-align: right">24H</span><span class="animationOn" style="color: black; text-align: right">Static</span></div></label></div></div>';
                                     } else {
                                     legendHeatmapDiv.innerHTML += '<div class="text">' + '<?php echo ucfirst(isset($_REQUEST["profile"]) ? $_REQUEST["profile"] : "Heatmap Controls:"); ?></div>';
                                     }*/
                                    // radius
                                    if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                        legendHeatmapDiv.innerHTML +=
                                                '<div id="heatmapRadiusControl" style="margin-top:10px">' +
                                                '<div style="display:inline-block; vertical-align:super;">Radius (px):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>' +
                                                '<div id= "<?= $_REQUEST['name_w'] ?>_modalLinkOpen_downSlider_radius" style="display:inline-block; vertical-align:super; color: #0078A8">&#10094;</div>&nbsp;&nbsp;&nbsp;' +
                                                //  '<input id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_sliderradius" style="display:inline-block; vertical-align:baseline; width:auto" type="range" min="0" max="0.0010" value="0.0008" step="0.00001">' +
                                                //  '<input id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_sliderradius" style="display:inline-block; vertical-align:baseline; width:auto" type="range" min="1" max="' + estimatedRadius * 50 + '" value="' + current_radius + '" step="' + Math.floor((estimatedRadius * 50)/40) + '">' +
                                                '<input id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_sliderradius" style="display:inline-block; vertical-align:baseline; width:auto" type="range" min="1" max="' + estimatedRadius * 30 + '" value="' + current_radius + '" step="2">' +
                                                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div id="upSlider_radius" style="display:inline-block; vertical-align:super; color: #0078A8">&#10095;</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                                                '<span id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_rangeradius" style="display:inline-block; vertical-align:super;">' + current_radius + '</span>' +
                                                '</div>';
                                    }
                                    // max opacity
                                    legendHeatmapDiv.innerHTML +=
                                            '<div id="heatmapOpacityControl">' +
                                            '<div style="display:inline-block; vertical-align:super;">Max Opacity: &nbsp;&nbsp;&nbsp;&nbsp;</div>' +
                                            '<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_downSlider_opacity" style="display:inline-block; vertical-align:super; color: #0078A8">&#10094;</div>&nbsp;&nbsp;&nbsp;' +
                                            '<input id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slidermaxOpacity" style="display:inline-block; vertical-align:baseline; width:auto" type="range" min="0" max="1" value="' + current_opacity + '" step="0.01">' +
                                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div id="upSlider_opacity" style="display:inline-block;vertical-align:super; color: #0078A8">&#10095;</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                                            '<span id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_rangemaxOpacity" style="display:inline-block;vertical-align:super;">' + current_opacity + '</span>' +
                                            '</div>';
                                    legendHeatmapDiv.innerHTML +=
                                            '<div id="heatmapNavigationCnt">' +
                                            '<input type="button" id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_prevButt" value="< Prev" style="float: left"/>' +
                                            '<input type="button" id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_nextButt" value="Next >" style="float: right"/>' +
                                            //  '<div id="modalLinkOpenHeatMapDescr" style="text-align: center">' + map.testMetadata.metadata[0].date + '</p>' +  // OLD-API
                                            '<div id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_heatMapDescr" style="text-align: center">' + mapDate + '</p>' +
                                            '</div>';
                                    if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                        legendHeatmapDiv.innerHTML +=
                                                '<div id="radiusCnt">' +
                                                // '<input type="checkbox" name="checkfield" id="g01-01" onchange="updateChangeRadiusOnZoom(this)"/> Change Radius on Zoom' +
                                                '<input type="checkbox" name="checkfield" id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad"/> Change Radius on Zoom' +
                                                '</div>';
                                        legendHeatmapDiv.innerHTML +=
                                                '<div id="radiusEstCnt">' +
                                                // '<input type="checkbox" name="checkfield" id="g01-01" onchange="updateChangeRadiusOnZoom(this)"/> Change Radius on Zoom' +
                                                '<input type="checkbox" name="checkfield" id="<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad" disabled="true"/> Estimate Radius Based on Data' +
                                                '</div>';
                                    }

                                    function checkLegend() {
                                        /*  if(document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_downSlider_radius") == null){
                                         setTimeout(checkLegend, 500);
                                         }
                                         else{   */
                                        if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_sliderradius").addEventListener("input", function () {
                                                setOption('radius', this.value, 1)
                                            }, false);
                                        }
                                        document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slidermaxOpacity").addEventListener("input", function () {
                                            setOption('maxOpacity', this.value, 2)
                                        }, false);

                                        if (!baseQuery.includes("heatmap.php")) {
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_animation").addEventListener("click", function () {
                                                animateFullscreenHeatmap()
                                            }, false);
                                        }

                                        document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_prevButt").addEventListener("click", function () {
                                            prevHeatmapPage()
                                        }, false);
                                        document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_nextButt").addEventListener("click", function () {
                                            nextHeatmapPage()
                                        }, false);

                                        if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad").addEventListener("change", function () {
                                                updateChangeRadiusOnZoom(this)
                                            }, false);
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad").addEventListener("change", function () {
                                                computeRadiusOnData(this)
                                            }, false);
                                        }

                                        if (current_page == 0) {
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_nextButt").style.visibility = "hidden";
                                        } else {
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_nextButt").style.visibility = "visible";
                                        }

                                        if (current_page == numHeatmapPages() - 1) {
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_prevButt").style.visibility = "hidden";
                                        } else {
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_prevButt").style.visibility = "visible";
                                        }
                                        //    }
                                    }
                                    setTimeout(checkLegend, 500);

                                    return legendHeatmapDiv;
                                };

                                /*  fullscreendefaultMapRef.eachLayer(function (layer) {
                                 fullscreendefaultMapRef.removeLayer(layer);
                                 });*/

                                /*   let cfg = JSON.parse(heatmapRange[0].leafletConfigJSON);
                                 
                                 if (current_radius != null) {
                                 cfg['radius'] = current_radius;
                                 }
                                 if (current_opacity != null) {
                                 cfg['maxOpacity'] = current_opacity;
                                 }*/

                                if (current_radius != null) {
                                    map.cfg['radius'] = current_radius;
                                }
                                if (current_opacity != null) {
                                    map.cfg['maxOpacity'] = current_opacity;
                                }

                                //   map.heatmapLayer.setData({max:heatmapRange[0].range10Inf, min:heatmapRange[0].range1Inf, data:map.testData.data});

                                fullscreenHeatmap = new HeatmapOverlay(map.cfg);

                                if (baseQuery.includes("heatmap.php")) {
                                    fullscreenHeatmap.setData(map.testData);
                                    fullscreendefaultMapRef.addLayer(fullscreenHeatmap);
                                } else {
                                    if (animationFlag === false) {
                                        var timestamp = map.testMetadata.metadata.date;
                                        var timestampISO = timestamp.replace(" ", "T") + ".000Z";
                                        wmsLayerFullscreen = L.tileLayer.wms("https://wmsserver.snap4city.org/geoserver/Snap4City/wms", {
                                            layers: 'Snap4City:' + wmsDatasetName,
                                            format: 'image/png',
                                            crs: L.CRS.EPSG4326,
                                            transparent: true,
                                            opacity: current_opacity,
                                            time: timestampISO,
                                            //  bbox: [24.7926004025304,60.1025194986424,25.1905923952885,60.2516802986263],
                                            tiled: true   // TESTARE COME ANTWERP ??
                                                    //  attribution: "IGN ©"
                                        }).addTo(fullscreendefaultMapRef);
                                    } else {
                                        // ANIMATION WMS HEATMAP
                                        var animationCurrentDayTimestamp = [];
                                        var animationCurrentDayFwdTimestamp = [];
                                        var animationCurrentDayBckwdTimestamp = [];
                                        var animationStringTimestamp = "";
                                        var timestamp = map.testMetadata.metadata.date;
                                        //    var timestampISO = timestamp.replace(" ", "T") + ".000Z";
                                        var day = timestamp.substring(0, 10);
                                        if (current_page == 0) {
                                            var offsetFwd = current_page;
                                            while (heatmapData[offsetFwd].metadata['date'].substring(0, 10) == day) {
                                                animationCurrentDayFwdTimestamp.push(heatmapData[offsetFwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                offsetFwd++;
                                            }
                                        } else if (current_page == numHeatmapPages() - 1) {
                                            var offsetBckwd = current_page - 1;
                                            while (heatmapData[offsetBckwd].metadata['date'].substring(0, 10) == day) {
                                                animationCurrentDayBckwdTimestamp.push(heatmapData[offsetBckwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                offsetBckwd--;
                                                if (offsetBckwd < 0) {
                                                    break;
                                                }
                                            }
                                        } else {
                                            var offsetFwd = current_page;
                                            while (heatmapData[offsetFwd].metadata['date'].substring(0, 10) == day) {
                                                animationCurrentDayFwdTimestamp.push(heatmapData[offsetFwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                offsetFwd++;
                                            }
                                            var offsetBckwd = current_page - 1;
                                            while (heatmapData[offsetBckwd].metadata['date'].substring(0, 10) == day) {
                                                animationCurrentDayBckwdTimestamp.push(heatmapData[offsetBckwd].metadata['date'].replace(" ", "T") + ".000Z");
                                                offsetBckwd--;
                                                if (offsetBckwd < 0) {
                                                    break;
                                                }
                                            }
                                        }

                                        animationCurrentDayTimestamp = animationCurrentDayFwdTimestamp.reverse().concat(animationCurrentDayBckwdTimestamp);
                                        //    animationCurrentDayTimestamp = animationCurrentDayTimestamp.reverse();
                                        animationStringTimestamp = animationCurrentDayTimestamp.join(",");

                                        var bboxJson = {};
                                        $.ajax({
                                            url: "https://heatmap.snap4city.org/bbox.php?layer=" + map.testMetadata.metadata.mapName,
                                            type: "GET",
                                            async: false,
                                            dataType: 'json',
                                            success: function (resultBbox) {
                                                bboxJson = resultBbox;
                                            },
                                            error: function (errbbox) {
                                                alert("Error in retrieving bounding box for current heatmap: " + mapName);
                                                console.log(errbbox);
                                            },
                                            // CORTI
                                            complete: function(){
                                                editCSSFor3DWidgets();
                                            }
                                        });

                                        var upEastLat = parseFloat(bboxJson['maxy']);
                                        var upEastLon = parseFloat(bboxJson['maxx']);
                                        var bottomWestLat = parseFloat(bboxJson['miny']);
                                        var bottomWestLon = parseFloat(bboxJson['minx']);
                                        var imageUrl = 'http://wmsserver.snap4city.org/geoserver/wms/animate?LAYERS=' + wmsDatasetName + '&aparam=time&avalues=' + animationStringTimestamp + '&format=image/gif;subtype=animated&format_options=gif_loop_continuosly:true;layout:message;gif_frames_delay:500&transparent=true';
                                        var imageBounds = [[bottomWestLat, bottomWestLon], [upEastLat, upEastLon]];
                                        var overlayOpacity = current_opacity;

                                        // ANIMATED GIF LAYER
                                        var animatedLayer = L.imageOverlay(imageUrl, imageBounds, {opacity: overlayOpacity}).addTo(fullscreendefaultMapRef);

                                        /*    // add legend to map
                                         map.legendHeatmap.addTo(map.defaultMapRef);
                                         //    $("<?= $_REQUEST['name_w'] ?>_animation").prop("checked",true);
                                         document.getElementById("<?= $_REQUEST['name_w'] ?>_animation").checked = true;
                                         //     $("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider({ disabled: "true" });
                                         $("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider('disable');
                                         //     document.getElementById("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider({ disabled: "true" });
                                         //     document.getElementById("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").slider({ disabled: "true" });
                                         map.eventsOnMap.push(animatedLayer);
                                         var mapControlsContainer = document.getElementsByClassName("leaflet-control")[0];
                                         
                                         var heatmapLegendColors = L.control({position: 'bottomleft'});
                                         
                                         heatmapLegendColors.onAdd = function (map) {
                                         
                                         var div = L.DomUtil.create('div', 'info legend'),
                                         grades = ["Legend"];
                                         //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                         var legendImgPath = heatmapRange[0].iconPath; // OLD-API
                                         div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';    /// OLD-API
                                         return div;
                                         };
                                         
                                         heatmapLegendColors.addTo(map.defaultMapRef);
                                         //  map.eventsOnMap.push(heatmap);
                                         
                                         event.legendColors = heatmapLegendColors;
                                         map.eventsOnMap.push(event);
                                         
                                         loadingDiv.empty();
                                         loadingDiv.append(loadOkText);
                                         
                                         parHeight = loadOkText.height();
                                         parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                         loadOkText.css("margin-top", parMarginTop + "px");
                                         
                                         setTimeout(function () {
                                         loadingDiv.css("opacity", 0);
                                         setTimeout(function () {
                                         loadingDiv.nextAll("#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv").each(function (i) {
                                         $(this).css("top", ($('#<?= $_REQUEST['name_w'] ?>_div').height() - (($('#<?= $_REQUEST['name_w'] ?>_content div.gisMapLoadingDiv').length - 1) * loadingDiv.height())) + "px");
                                         });
                                         loadingDiv.remove();
                                         }, 350);
                                         }, 1000);   */
                                    }
                                }

                                //  fullscreendefaultMapRef.addLayer(map.heatmapLayer);

                                legendHeatmapFullscreen.addTo(fullscreendefaultMapRef);

                                heatmapLegendColorsFullscreen = L.control({position: 'bottomleft'});

                                heatmapLegendColorsFullscreen.onAdd = function (map) {

                                    var div = L.DomUtil.create('div', 'info legend'),
                                            grades = ["Legend"];
                                    //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                    var legendImgPath = heatmapRange[0].iconPath;
                                    div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';
                                    return div;
                                };

                                heatmapLegendColorsFullscreen.addTo(fullscreendefaultMapRef);

                                if (changeRadiusOnZoom) {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad').prop('checked', true);
                                    if (estimateRadiusFlag) {
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad').prop('disabled', true);
                                    }
                                }

                                if (estimateRadiusFlag) {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad').prop('checked', true);
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad').prop('disabled', false);
                                } else {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad').prop('disabled', false);
                                }

                            }

                            //  resizeMapView(fullscreendefaultMapRef);
                        }

                    }
                }, 750);    // PANTALEO - AUMENTARE UN PO' IL VALORE DI setTimeOut QUI SE LA MAPPA NON CARICA ABBASTANZA VELOCEMENTE SE HA UNA HEATMAP DI DEFAULT

                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen").modal('show');

            });



            /**
             * ANDREA CORTI - Implementazione mappa 3D tramite libreria OSM Buildings e inclusione
             * di edifici da file GeoJSON, layers da GeoServer e layers da server esterni;
             * inclusione di tutti i dati della mappa 2d ancorando la mappa stessa allo stesso container della 3d
             * e inclinandone la prospettiva in relazione alle impostazioni tridimensionali
             */

            // variabili globali
            $map2DElem = $("#<?= $_REQUEST['name_w'] ?>_map"); // elemento della mappa 2d

            layersCreated = []; // array di layers creati
            layersAddedToMap = []; // array di layers aggiunti alla mappa 2d
            is3DViewOn = false; // identifica se la visuale è settata sulla mappa 3d oppure no
            current3DZoom = 15; // zoom offset nativo fra 2D e 3D
            translate2D = "0px, 0px"; // traslazione iniziale mappa 2d applicata come layer alla 3d
            height = $map2DElem.height(); // parametro per settare l'altezza della mappa 2d applicata alla 3d

            /*
             * Inizializza i bottoni base de dropdown menu (2D e 3D) e i listener
             * @param {object} map
             * @returns {void}
             */
            function initMapsAndListeners(map) {
                let map2D = map.defaultMapRef;

                // ready
              //  map2D.panTo(new L.LatLng(43.769789, 11.255694));

                // load menu
                getMenuAjaxCall();
                
                // inclina mappa dopo click sui pinLink
                $('.gisPinLink').on('mousedown', function () {
                    if (!is3DViewOn) {
                        return;
                    }

                    if (!map.defaultMapRef) {
                        $('#sensorsSwitch').trigger('click');
                    }
                });

                // switch to 2DMap
                $('#2DButton').on('click', function () {
                    if (!is3DViewOn) {
                        return;
                    }
                    is3DViewOn = false;
                    $('#sensorsSwitch').css('display', 'none');
                    clear2DMap(false);

                    newMap();

                    // select menu map
                    $(this).addClass('map-selected');
                    $('#3DButton').removeClass('map-selected');

                    // disabilita 3d map
                    if (map.default3DMapRef) {
                        $('.osmb').css('display', 'none');
                        map.default3DMapRef.isDisabled(true);
                    }

                    // 3d controls
                    $('#controls3D').css('display', 'none');

                    // 3D availability
                    $('.3DOff').removeClass('hidden');
                    $('#checkablesHeader').removeClass('hidden');
                });

                // switch to 3DMap
                $('#3DButton').on('click', function () {
                    if (is3DViewOn) {
                        return;
                    }
                    is3DViewOn = true;

                    // disabilita eventi per la mappa 2d

                    // load 3D map 
                    if (!map.default3DMapRef) {
                        load3DMap();
                    } else {
                        $('.osmb').css('display', 'block');
                        map.default3DMapRef.isDisabled(false);
                    }
                    $('#sensorsSwitch').css('display', 'block');
//                    
                    // select menu map
                    $(this).addClass('map-selected');
                    $('#2DButton').removeClass('map-selected');

                    // 3d controls
                    $('#controls3D').css('display', 'block');

                    // 3D availability
                    $('.3DOff').addClass('hidden');
                    $('#checkablesHeader').addClass('hidden');

                    clear2DMap(false);
                });
            }
            
            /*
             * Carica e inizializza la mappa 3d applicando listener e azioni
             * @param {bool} loadOrthmap
             * @returns {void}
             */
            function load3DMap(loadOrthmap = true) {
                if (map.default3DMapRef) {
                    map.default3DMapRef.destroy();
                    map.default3DMapRef = null;
                }

                map.default3DMapRef = new OSMBuildings({
                    container: '<?= $_REQUEST['name_w'] ?>_map',
                    position: {latitude: map.defaultMapRef.getCenter().lat, longitude: map.defaultMapRef.getCenter().lng},
                    zoom: current3DZoom,
                    minZoom: 13,
                    maxZoom: 18,
                    tilt: 30,
                    fastMode: false,
                    attribution: '© Data <a href="https://openstreetmap.org/copyright/">OpenStreetMap</a> © Map <a href="https://mapbox.com/">Mapbox</a> © 3D <a href="https://osmbuildings.org/copyright/">OSM Buildings</a>'
                });

                if (loadOrthmap) {
                    mainOrthmap = map.default3DMapRef.addMapTiles('https://a.tile.openstreetmap.org/{z}/{x}/{y}.png');
                }

                // edifici
                loadBuildings();

                clear2DMap();

                pointerDown = false;
                pointerMoving = false;
                map.default3DMapRef.on('pointermove', function () {
                    if (pointerDown && !pointerMoving) {
                        pointerMoving = true;
                        if (map.defaultMapRef) {
                            hideMarkers();
                            clear2DMap();
                        }
                    }
                });
                $('body').on('mousemove', function () {
                    if (pointerDown && !pointerMoving) {
                        pointerMoving = true;
                        if (map.defaultMapRef) {
                            hideMarkers();
                            clear2DMap();
                        }
                    }
                });
                map.default3DMapRef.on('pointerdown', function (e) {
                    pointerDown = true;
                });
                map.default3DMapRef.on('pointerup', function () {
                    if (pointerMoving) {
                        set2Dto3DMap();
                    }
                    pointerDown = false;
                    pointerMoving = false;
                });
                $('body').on('mouseup', function () {
                    if (!is3DViewOn) {
                        return;
                    }
                    if (pointerMoving) {
                        set2Dto3DMap();
                    }
                    pointerDown = false;
                    pointerMoving = false;
                });
                map.default3DMapRef.on('zoom', function () {
                    if (map.defaultMapRef) {
                        hideMarkers();
                        get3DParamsByTilt();
                        editCSSFor3DWidgets();
                    }

                    // zoom UI
                    setUIZoomValue();
                });

                map.default3DMapRef.on('tilt', function () {
                    if (map.defaultMapRef) {
                        hideMarkers();
                        get3DParamsByTilt();
                        editCSSFor3DWidgets();
                    }
                });

                map.default3DMapRef.on('rotate', function () {
                    if (map.defaultMapRef) {
                        hideMarkers();
                        get3DParamsByTilt();
                        editCSSFor3DWidgets();
                    }
                });

                // zoom UI
                setUIZoomValue();

                // 3D sensors switch 
                $('#sensorsSwitchButton').on('click', function () {
                    if (!is3DViewOn) {
                        return;
                    }
                    set2Dto3DMap();

                    // add previous enabled sensors
                    let pinArray = [];
                    // get dei sensori attivi
                    $('.gisPinLink').each(function (evt) {
                        if ($(this).attr('data-onmap') == "true" && (!$(this).attr('data-query').includes("heatmap.php") && !$(this).attr('data-query').includes("wmsserver.snap4city.org"))) {
                            $(this).trigger('click');
                            pinArray.push(this);
                        }
                    });

                    setTimeout(function () {
                        for (let i = 0; i < pinArray.length; i++) {
                            $(pinArray[i]).click();
                        }
                        pinArray = [];
                    }, 100);
                });

                // map button control
                var controlButtons = document.querySelectorAll('.control button');
                for (var i = 0; i < controlButtons.length; i++) {
                    controlButtons[i].addEventListener('click', function (e) {
                        // controls
                        var button = this;
                        var parentClassList = button.parentNode.classList;
                        var direction = button.classList.contains('inc') ? 1 : -1;
                        var increment;
                        var property;

                        if (parentClassList.contains('tilt')) {
                            property = 'Tilt';
                            increment = direction * 10;
                        }
                        if (parentClassList.contains('rotation')) {
                            property = 'Rotation';
                            increment = direction * 10;
                        }
                        if (parentClassList.contains('zoom')) {
                            property = 'Zoom';
                            increment = direction * 1;
                        }
                        if (property) {
                            map.default3DMapRef['set' + property](map.default3DMapRef['get' + property]() + increment);
                        }
                    });
                }

                $('#timepicker').on('change', function () {
                    if ($('#timepicker').val() != "Daytime") {
                        // date: year, month, day, hour, minutes
                        map.default3DMapRef.setDate(new Date(2019, 11, 10, $('#timepicker').val(), 0));
                    } else {
                        map.default3DMapRef.setDate(new Date());
                    }
                });

                // heatmap pinLink listener
                $('.gisPinLink').on('click', function () {
                    if ($(this).attr('data-query').includes("heatmap.php") || $(this).attr('data-query').includes("wmsserver.snap4city.org")) {
                        if ($(this).attr('data-onmap') == "false") {
                            $('.heatmap-3d-controls').css('display', 'none');
                            map.default3DMapRef.remove(heatmapLayer3D);
                            mainOrthmap = map.default3DMapRef.addMapTiles('https://a.tile.openstreetmap.org/{z}/{x}/{y}.png');
                        } else {
                            $('.heatmap-3d-controls').css('display', 'block');
                        }
                    }
                });

                // reload sensors at init
                setTimeout(function () {
                    $('#sensorsSwitchButton').click();
                }, 500);

            }

            /*
             * Inizializza e include gli edifici alla mappa 3d
             * @returns {void}
             */
            function loadBuildings() {
                // standard
//                map.default3DMapRef.addGeoJSONTiles('https://{s}.data.osmbuildings.org/0.2/anonymous/tile/{z}/{x}/{y}.json');
//                
                // mappe OSMB
//              buildings3d = map.default3DMapRef.addGeoJSONTiles('http://dashboard/dashboardSmartCity/widgets/layers/edificato/{s}/{z}/{x}/{y}.json');
//              
                // mappe DISIT
              //  buildings3d = map.default3DMapRef.addGeoJSON('http://localhost/dashboardSmartCity/widgets/layers/edificato/AltezzeEdificiFirenze.geojson');
                buildings3d = map.default3DMapRef.addGeoJSON('https://www.snap4city.org/dashboardSmartCity/widgets/layers/edificato/AltezzeEdificiFirenze.geojson');
            }

            /*
             * Setta tutte le impostazioni per applicare la vista prospettica alla mappa 2d,
             * in modo tale che sia applicabile al container della mappa 3d
             * @param {bool} load3d
             * @returns {void}
             */
            function set2Dto3DMap(load3d = false) {
                if (!is3DViewOn) {
                    return;
                }

                if (!map.defaultMapRef) {
                    get3DParamsByTilt();
                    newMap([map.default3DMapRef.getPosition().latitude, map.default3DMapRef.getPosition().longitude], load3d);
                }

                // rendi marker e popup invisibile fino a che non sono in 3D
                $map2DElem.find('.leaflet-map-pane').find('.leaflet-marker-pane').css('opacity', 0);
                $map2DElem.find('.leaflet-map-pane').find('.leaflet-popup-pane').css('opacity', 0);
                $('.leaflet-control-container').css('display', 'none');

                // pin 
                $('.gisPinLink').click(function () {
                    setTimeout(function () {
                        get3DParamsByTilt();
                        editCSSFor3DWidgets();
                    }, 100);
                });

                // add previous enabled sensors
                let pinArray = [];
                $('.gisPinLink').each(function (evt) {
//                    if (pin <= 1) {
                    if ($(this).attr('data-onmap') == "true" && (!$(this).attr('data-query').includes("heatmap.php") && !$(this).attr('data-query').includes("wmsserver.snap4city.org"))) {
                        $(this).trigger('click');
                        pinArray.push(this);
                    }
                });

                setTimeout(function () {
                    for (let i = 0; i < pinArray.length; i++) {
                        $(pinArray[i]).click();
                    }
                    pinArray = [];
                }, 100);

                setTimeout(function () {
                    // rendi tutto visibile
                    $map2DElem.find('.leaflet-map-pane').find('.leaflet-marker-pane').css('opacity', 1);
                    $map2DElem.find('.leaflet-map-pane').find('.leaflet-popup-pane').css('opacity', 1);

                    // css
                    editCSSFor3DWidgets();
                }, 500);
            }
            
            /*
             * Applica tutte le modifiche ai CSS per applicare la vista prospettica alla mappa 2d e ai relativi divs
             * @returns {void}
             */
            function editCSSFor3DWidgets() {
                if (!is3DViewOn) {
                    return;
                }

                // prospettiva div container
                $('.leaflet-container').css('background', 'transparent');
                $map2DElem.find('.leaflet-map-pane').addClass('perspective-div');
                $map2DElem.find('.leaflet-map-pane').css('height', height).css('width', $map2DElem.width());

                // rotazione divs
                $map2DElem.find('.leaflet-map-pane').find('.leaflet-pane').not('.leaflet-popup-pane').addClass('rotate3d-div');
                $map2DElem.find('.leaflet-map-pane').find('.leaflet-pane').not('.leaflet-popup-pane')
                        .css('transform', 'rotateX(' + (map.default3DMapRef.getTilt()) + 'deg) rotate(' + (-map.default3DMapRef.getRotation()) + 'deg) translate(' + translate2D + ')')
                        .css('height', $map2DElem.height()).css('width', $map2DElem.width());

                // sync centers & zoom
                if (map.defaultMapRef) {
                    map.defaultMapRef.setZoom(map.default3DMapRef.getZoom() + zoomOffset, {animate: false});
                }

                // heatmap controls
                $('.leaflet-control-container').css('display', 'none');
                $('.heatmap-3d-controls').find('.time').text(timestampISO3D);
                $('.heatmap-3d-controls').find('.title').text(titleHeatamp3DControls);

                setTimeout(function () {
                    inverseMarkerAndPopup3DRotation();
                }, 0);
            }
            
            /*
             * Nasconde tutti i markers, utile per effettuare le modifiche ai CSS prima della visualizzazione
             * @returns {void}
             */
            function hideMarkers() {
                // opacizza markers
                $map2DElem.find('.leaflet-map-pane').find('.leaflet-marker-pane').find('.leaflet-marker-icon').each(function () {
                    // opacizza prima del rendering            
                    $(this).css('opacity', 0);
                });
            }
            
            /*
             * Applica un antirotazione ai markers e ai popup per renderli indipendenti dalla prospettiva 3d
             * @returns {void}
             */
            function inverseMarkerAndPopup3DRotation() {
                if (!is3DViewOn) {
                    return;
                }
                // markers
                $map2DElem.find('.leaflet-map-pane').find('.leaflet-marker-pane').find('.leaflet-marker-icon').each(function () {
                    $(this).css('opacity', 0);
                    $(this).css('transform-origin', '50% 100%');
                    // tilt
                    if ($(this).attr('style').indexOf("rotateX") >= 0 && $(this).attr('style').indexOf("matrix3d") < 0) {
                        const regex = /rotateX\(-?[0-9]{1,2}deg\)/gi;
                        $(this).attr('style', $(this).attr('style').replace(regex, 'rotateX(-' + map.default3DMapRef.getTilt() + 'deg)'));
                    } else if ($(this).attr('style').indexOf("rotateX") < 0 && $(this).attr('style').indexOf("matrix3d") < 0) {
                        $(this).css('transform', $(this).css('transform') + ' rotateX(-' + map.default3DMapRef.getTilt() + 'deg)');
                    }
                    // rotation
                    if ($(this).attr('style').indexOf("rotate(") < 0) {
                        $(this).attr('style', $(this).attr('style').replace('rotateX', 'rotate(0deg) rotateX'));
                        markerRot = 0;
                    }
                    $(this).attr('style', $(this).attr('style').replace('rotate(' + markerRot + 'deg)', 'rotate(' + map.default3DMapRef.getRotation() + 'deg)'));
                    $(this).css('opacity', 1);
                });
                markerRot = map.default3DMapRef.getRotation();

                // popups
                $map2DElem.find('.leaflet-map-pane').find('.leaflet-popup-pane').find('.leaflet-popup').each(function () {
                    $(this).css('transform', '');
                    $(this).css('bottom', popupBottom + 'px').css('left', '70px');
                });
                $map2DElem.find('.leaflet-map-pane').find('.leaflet-popup-pane').find('.leaflet-popup').find('.leaflet-popup-tip-container').css('opacity', 0);
                $map2DElem.find('#heatmapLegend').css('right', '120px');
            }

            /*
             * Setta valore di zoom
             * @returns {void}
             */
            function setUIZoomValue() {
                $('.zoom-info-value').text(Math.round(map.default3DMapRef.getZoom() * 10) / 10);
            }

            /*
             * Distrugge la mappa 2d e tutte le reference
             * @param {bool} resetSensors
             * @returns {void}
             */
            function clear2DMap(resetSensors = true) {
                if (map.defaultMapRef) {
                    map.defaultMapRef.remove();
                    map.defaultMapRef = null;
                    $map2DElem.find('.leaflet-pane').remove();
                    $map2DElem.attr('class', '');
                }

                $(document).unbind('addAlarm');
                $(document).unbind('addEvacuationPlan');
                $(document).unbind('addSelectorPin');
                $(document).unbind('addEventFI');
                $(document).unbind('addResource');
                $(document).unbind('addOperatorEvent');
                $(document).unbind('addTrafficEvent');
                $(document).unbind('addTrafficRealTimeDetails');
                $(document).unbind('addHeatmap');

                $(document).unbind('removeAlarm');
                $(document).unbind('removeEvacuationPlan');
                $(document).unbind('removeSelectorPin');
                $(document).unbind('removeEventFI');
                $(document).unbind('removeResource');
                $(document).unbind('removeOperatorEvent');
                $(document).unbind('removeTrafficEvent');
                $(document).unbind('removeTrafficRealTimeDetails');
                $(document).unbind('removeHeatmap');
            }

            /*
             * Carica i campi del dropdown menu dal database
             * @returns {void}
             */
            function getMenuAjaxCall() {
                if ($('.appendable').length > 0) {
                    return;
                }

                $.ajax({
                    url: "../controllers/getWidgetParams.php?widgetName=<?php echo $_REQUEST['name_w']; ?>",
                    type: "GET",
                    data: {},
                    async: true,
                    dataType: 'json',
                    success: function (data) {
                        let parameters = JSON.parse(data.params.parameters);
                        parameters.dropdownMenu.reverse().forEach(function (menu) {
                            if (menu.active !== false) {
                                let dropdownMenuField = $('#dropdownMenuTemplate').html();
                                $('#' + menu.header + 'Header').after(dropdownMenuField);
                                let $item = $('#mapOptions').find('.appendable').first();
                                $item.find('a').append(menu.label);

                                // disable for 3D if needed
                                if (menu.availableFor3D == 'false') {
                                    $item.addClass('3DOff');
                                }

                                // icon
                                if (menu.external) {
                                    $item.find('.appendable-icon').addClass('fa-map-pin');
                                } else {
                                    $item.find('.appendable-icon').addClass('fa-check');
                                }

                                // listener
                                $item.find('a').click(function (evt) {
                                    // check if layer is removable
                                    let removeLayer = false;
                                    if (menu.header !== "checkables") {
                                        removeAllLayers(map.defaultMapRef);
                                        removeAllIcons();
                                    } else {
                                        if (!$(evt.target).find('.appendable-icon').hasClass('hidden')) {
                                            removeLayer = true;
                                        }
                                    }

                                    // action
                                    if (!removeLayer) {
                                        switch (menu.service) {
                                            case "tileLayer":
                                                addTileLayer(evt, menu);
                                                break;
                                            case "WMS":
                                                addLayerWMS(evt, menu);
                                                break;
                                            case "KML":
                                                addLayerKML(evt, menu);
                                                break;
                                            case "GeoJSON":
                                                addLayerGeoJSON(evt, menu);
                                                break;
                                            case "SVG":
                                                addLayerSVG(evt, menu);
                                                break;
                                            default:
                                                console.log("No service selected.");
                                        }

                                        // icon
                                        if (is3DViewOn) {
                                            // remove all others orthmaps
                                            $('.dropdown-menu').find('.fa-check').addClass('hidden');
                                        }
                                        $(evt.target).find('.appendable-icon').removeClass('hidden');
                                    } else {
                                        removeLayerById(menu.id, evt);
                                    }

                                    // avoid dropdown close on click
                                    evt.stopPropagation();
                                });
                            }
                        });

                        // select default map as active
                        $('#mapOptions').find('.appendable-icon').first().removeClass('hidden')
                    },
                    error: function () {
                        console.log("An error occurred.");
                    },
                    complete: function () {
                    }
                });
            }

            /*
             * Aggiunge un layer alla mappa 2d o alla 3d tramite standard WMS (XYZ riadattato per la mappa 3d)
             * @param {object} evt
             * @param {object} menu
             * @returns {void}
             */
            function addLayerWMS(evt, menu) {
                let imageType = 'png';
                if (menu.imageType) {
                    imageType = menu.imageType;
                }
                if (!is3DViewOn) {
                    for (var subLayerIndex = 0; subLayerIndex < menu.layers.length; subLayerIndex++) {

                        // zIndex
                        if (map.defaultMapRef) {
                            map.defaultMapRef.createPane(menu.id + menu.layers[subLayerIndex].name);
                            if (menu.zIndex) {
                                map.defaultMapRef.getPane(menu.id + menu.layers[subLayerIndex].name).style.zIndex = menu.zIndex;
                            }
                        }

                        let layer = L.tileLayer.wms(menu.linkUrl, {
                            layers: menu.layers[subLayerIndex].name,
                            format: 'image/' + imageType,
                            transparent: true,
                            version: '1.1.0',
                            minZoom: menu.minZoom,
                            maxZoom: menu.maxZoom,
                            attribution: "",
                            pane: menu.id + menu.layers[subLayerIndex].name
                        });

                        if (!arrayContains(layersCreated, layer)) {
                            layersCreated.push({"menu": menu, "layer": layer, "subLayerIndex": subLayerIndex});
                        }

                        // check zoom and add to map
                        addLayerToMapByZoom(menu, layer, subLayerIndex, layersCreated.length - 1);

                    }

                } else {
                    // 3D
                    map.default3DMapRef.remove(mainOrthmap);
                    mainOrthmap = map.default3DMapRef.addMapTiles(menu.linkUrl + '?service=WMS&request=GetMap&layers=' + menu.layer3d + '&styles=&format=image%2Fjpeg&version=1.1.1&tiled=true&width=256&height=256&srs=EPSG%3A4326/{z}/{x}/{y}');
                }
            }

            /*
             * Include un determinato layer alla mappa 2d in base allo zoom corrente
             * @param {object} menu
             * @param {string} layer
             * @param {string} subLayerIndex
             * @param {int} i
             * @returns {void}
             */
            function addLayerToMapByZoom(menu, layer, subLayerIndex, i) {
                let zoom = map.defaultMapRef.getZoom();
                if ((zoom <= menu.layers[subLayerIndex].maxZoom && zoom >= menu.layers[subLayerIndex].minZoom) || !menu.layers[subLayerIndex].minZoom) {
                    // check if layer is already on the map
                    if (!map.defaultMapRef.hasLayer(layer)) {

                        // 2D
                        layer.on('loading', function () {
                            $('#loadingMenu').removeClass('hidden');
                        }).on('load', function () {
                            $('#loadingMenu').addClass('hidden');
                        }).addTo(map.defaultMapRef);

                        if (!arrayContains(layersAddedToMap, layer)) {
                            layersAddedToMap.push({"id": menu.id, "layer": layer});
                        }
                    }
                } else {
                    map.defaultMapRef.removeLayer(layer);
                    // remove from array layersAddedToMap
                    for (var j = 0; j < layersAddedToMap.length; j++) {
                        if (layersAddedToMap[j].layer.options.layers === layer.options.layers) {
                            layersAddedToMap.splice(j, 1);
                            j--;
                        }
                    }
                }
            }

            /*
             * Rimuove un layer in base al'id passato come parametro
             * @param {string} layerId
             * @param {object} evt
             * @returns {void}
             */
            function removeLayerById(layerId, evt) {
                // remove from array layersAddedToMap
                for (var i = 0; i < layersAddedToMap.length; i++) {
                    if (layersAddedToMap[i].id === layerId) {
                        map.defaultMapRef.removeLayer(layersAddedToMap[i].layer);
                        // remove from array
                        layersAddedToMap.splice(i, 1);
                        i--;
                    }
                }
                // remove from array layersCreated
                for (var j = 0; j < layersCreated.length; j++) {
                    if (layersCreated[j].menu.id === layerId) {
                        map.defaultMapRef.removeLayer(layersCreated[j].layer);
                        // remove from array
                        layersCreated.splice(j, 1);
                        j--;
                    }
                }
                if (evt) {
                    $(evt.target).find('.appendable-icon').addClass('hidden');
                }
                $('#loadingMenu').addClass('hidden');
            }

            /*
             * Rimuove tutti i layers dalla mappa
             * @param {object} map
             * @returns {void}
             */
            function removeAllLayers(map) {
                if (map) {
                    map.eachLayer(function (layer) {
                        map.removeLayer(layer);
                    });
                    layersAddedToMap = [];
                    layersCreated = [];

                    // remove icons
                    removeAllIcons();
                }
            }

            /*
             * Aggiunge un layer alla mappa 2d o alla 3d tramite standard XYZ
             * @param {object} evt
             * @param {object} menu
             * @returns {void}
             */
            function addTileLayer(evt, menu) {
                let layer;
                // 2D
                if (!is3DViewOn) {
                    if (map.defaultMapRef) {
                        if (menu.minZoom && menu.maxZoom) {
                            layer = L.tileLayer(menu.linkUrl, {
                                attribution: menu.layerAttribution,
                                apikey: menu.apiKey,
                                minZoom: menu.minZoom,
                                maxZoom: menu.maxZoom,
                            }).addTo(map.defaultMapRef);
                        } else {
                            layer = L.tileLayer(menu.linkUrl, {
                                attribution: menu.layerAttribution,
                                apikey: menu.apiKey,
                            }).addTo(map.defaultMapRef);
                        }

                        layersAddedToMap.push({"id": menu.id, "layer": layer});
                    }
                } else {
                    // 3D
                    // l'edit dell'url nel 3D evita bug tile vuoti
                    let linkUrl = "";
                    if (menu.linkUrl3D) {
                        linkUrl = menu.linkUrl3D;
                    } else {
                        linkUrl = menu.linkUrl;
                    }
                    map.default3DMapRef.remove(mainOrthmap);
                    if (menu.minZoom && menu.maxZoom) {
                        mainOrthmap = map.default3DMapRef.addMapTiles(linkUrl, {
                            attribution: menu.layerAttribution,
                            apikey: menu.apiKey,
                            minZoom: menu.minZoom,
                            maxZoom: menu.maxZoom
                        });
                    } else {
                        mainOrthmap = map.default3DMapRef.addMapTiles(linkUrl, {
                            attribution: menu.layerAttribution,
                            apikey: menu.apiKey
                        });
                    }
                }
            }

            /*
             * Aggiunge un layer alla mappa 2d o alla 3d estraendolo da un file KML
             * @param {object} evt
             * @param {object} menu
             * @returns {void}
             */
            function addLayerKML(evt, menu) {
                var kmlLayer = new L.KML(menu.linkUrl, {
                    async: true
                });
                map.defaultMapRef.addLayer(kmlLayer);
                layersAddedToMap.push({"id": menu.id, "layer": kmlLayer});

                map.defaultMapRef.kmlLayer.zIndex = 420;
            }

            /*
             * Aggiunge un layer alla mappa 2d o alla 3d estraendolo da file GeoJSON
             * @param {object} evt
             * @param {object} menu
             * @returns {void}
             */
            function addLayerGeoJSON(evt, menu) {

                // zIndex
                if (map.defaultMapRef) {
                    map.defaultMapRef.createPane(menu.id);
                    if (menu.zIndex) {
                        map.defaultMapRef.getPane(menu.id).style.zIndex = menu.zIndex;
                    }
                }

                jQuery.getJSON(menu.linkUrl, function (data) {
                    let layer = L.geoJSON(data, {
                        pane: menu.id
                    }).addTo(map.defaultMapRef);

                    layersAddedToMap.push({"id": menu.id, "layer": layer});
                });
            }

            /*
             * Aggiunge un layer alla mappa 2d o alla 3d estraendolo da un'immagine vettoriale
             * @param {object} evt
             * @param {object} menu
             * @returns {void}
             */
            function addLayerSVG(evt, menu) {
                let imageBounds = [[9.716489, 42.2392816], [12.3529926, 44.47160041252872]];
                L.imageOverlay(menu.linkUrl, imageBounds).addTo(map.defaultMapRef);
            }

            /*
             * Rimuove tutte le icone dal dropdown menu
             * @returns {void}
             */
            function removeAllIcons() {
                $('.appendable-icon').addClass('hidden');
            }

            /*
             * Check se un array contiene un determinato layer
             * @param {array} array
             * @param {string} layer
             * @returns {bool}
             */
            function arrayContains(array, layer) {
                for (var i = 0; i < array.length; i++) {
                    if (array[i].layer.options.layers === layer.options.layers) {
                        return true;
                    }
                }
                return false;
            }

            /*
             * Get e Set di paramteri utili per la mappa 3d
             * @returns {void}
             */
            function get3DParamsByTilt() {
                let tilt = map.default3DMapRef.getTilt();
                zoomOffset = 0.25;
                popupBottom = -360;
                height = $map2DElem.height() * 2;
            }

        });//Fine document ready
    </script>

    <style>
    #mapOptions{
        position: absolute;
        top: 36px;
        left: 70px;
        z-index: 400;
    }
    .dropdown-menu .dropdown-header{
        padding-left: 10px;
        color: #c3c3c3;
    }
    .dropdown-menu .dropdown-item{
        padding-left: 10px;
    }
    .perspective-div{
        position: absolute;
        perspective: 1234px;
        pointer-events: none;
    }
    .rotate3d-div{
        position: absolute;
        transform-style: preserve-3d;
    }
    .leaflet-tuscanyBoundaries-pane{
        pointer-events: none;
    }
    .leaflet-popup-pane{
        pointer-events: all;
    }
    .inverse-rotate3d-div{
        position: absolute;
        transform-style: preserve-3d;
    }
    #sensorsSwitch{
        display: none;
        position: absolute;
        top: 36px;
        left: 180px;
        z-index: 400;
    }
    .leaflet-popup-3d{
        position: fixed !important;
        top: 67px;
        left: 90px;
    }
    .leaflet-popup-3d .leaflet-popup-tip-container{
        display: none;
    }
    .osmb:hover{
        cursor: grab !important;
    }
    .osmb:active{
        cursor: grabbing !important;
    }
    .map-selected{
        color: royalblue !important;
    }

    #controls3D{
        display: none;
        position: absolute;
        top: -80px;
        right: 20px;
        width: 110px;
        z-index: 400;
    }
    .control {
        position: absolute;
        left: 10px;
        z-index: 1000;
    }

    .control.tilt {
        top: 100px;
    }

    .control.rotation {
        top: 145px;
    }

    .control.zoom {
        top: 190px;
    }

    .control.time {
        top: 235px;
    }

    .control.zoom button{
        font-weight: normal;
    }

    .control button {
        width: 30px;
        height: 30px;
        margin: 15px 0 0 15px;
        border: 1px solid #999999;
        background: #ffffff;
        opacity: 0.6;
        border-radius: 5px;
        box-shadow: 0 0 5px #666666;
        font-weight: bold;
        text-align: center;
    }

    .control button:hover, .control select:hover {
        opacity: 1;
        cursor: pointer;
    }

    .control select {
        width: 77px;
        height: 30px;
        margin: 15px 0 0 15px;
        border: 1px solid #999999;
        background: #ffffff;
        opacity: 0.6;
        border-radius: 5px;
        box-shadow: 0 0 5px #666666;
        font-weight: bold;
        text-align: center;
    }

    .zoom-info{
        top: 100px;
        left: -55px;
        opacity: 1;
    }
    .heatmap-3d-controls{
        display: none;
        top: 120px;
        left: -320px;
        opacity: 1;
        background: lightgrey;
        padding-right: 15px;
        padding-bottom: 15px;
        max-width: 230px;
    }
    .heatmap-3d-controls .title{
        padding-top: 10px;
        padding-left: 15px;
    }
    .heatmap-3d-controls .time{
        width: 200px;
    }
    .heatmap-3d-controls .prev{
        width: 90px;
    }
    .heatmap-3d-controls .next{
        width: 90px;
    }
</style>

<div class="widget" id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div">
    <div class='ui-widget-content'>
        <!-- Inclusione del modulo comune che costruisce la testata del widget, JS incluso -->
        <?php include '../widgets/widgetHeader.php'; ?>

        <!-- Inclusione del modulo comune che costruisce il menu constestaule di gestione del widget -->
        <?php include '../widgets/widgetCtxMenu.php'; ?>

        <!-- Schermata di loading -->
        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>

        <!-- Contenitore esterno del contenuto del widget -->
        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content" class="content">

            <!-- Modulo comune per la gestione dei dimensionatori del widget in edit dashboard -->
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>

            <!-- Pannello che viene mostrato quando non ci sono dati disponibili per il widget in esame -->
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert"
                 class="noDataAlert">
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText"
                     class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertIcon"
                     class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>

            <!-- Dentro questo DIV ci va il contenuto vero e proprio (e specifico) del widget (si chiama _chartContainer solo per legacy, non contiene necessariamente un grafico) -->
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer"
                 class="chartContainer">
                <!-- Originale 1-->
                <!--<div id="map" style="height: 180px"></div>-->

                <!-- Correzione 1 -->
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_map"
                     style="height: 100%; width: 100%;"></div>

                <!-- Layers & 3D -->
                <div class="dropdown" id="mapOptions">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="fa fa-spinner fa-spin hidden" id="loadingMenu"></i> Options
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a class="dropdown-item map-selected" href="#" id="2DButton"><i class="fa fa-map"></i>&nbsp;&nbsp;2D Map</a></li>
                        <li><a class="dropdown-item" href="#" id="3DButton"><i class="fa fa-building"></i>&nbsp;&nbsp;3D Map</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header" id="layersHeader">World OrthMaps</li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header" id="checkablesHeader">Checkable Layers/Maps</li>
                    </ul>
                    <template id="dropdownMenuTemplate">
                        <li class="appendable">
                            <a class="dropdown-item" href="#">
                                <i class="fa appendable-icon hidden"></i>
                            </a>
                        </li>
                    </template>
                </div>

                <!-- 3D sensors switch -->
                <div id="sensorsSwitch">
                    <button class="btn btn-warning" type="button" id="sensorsSwitchButton" data-info="drag">
                        <i class="fa fa-info"></i>&nbsp;Reload Sensors
                    </button>
                </div>

                <!-- 3D controls -->
                <div id="controls3D">
                    <div class="control zoom-info">
                        <button class="dec zoom-info-value"></button>
                    </div>

                    <div class="control tilt">
                        <button class="dec">↙</button>
                        <button class="inc">↗</button>
                    </div>

                    <div class="control rotation">
                        <button class="inc">↶</button>
                        <button class="dec">↷</button>
                    </div>

                    <div class="control zoom">
                        <button class="dec">-</button>
                        <button class="inc">+</button>
                    </div>

                    <div class="control time">
                        <select id="timepicker" >
                            <option value="Daytime">Now</option>
                            <option value="0">00:00</option>
                            <option value="1">1:00</option>
                            <option value="2">2:00</option>
                            <option value="3">3:00</option>
                            <option value="4">4:00</option>
                            <option value="5">5:00</option>
                            <option value="6">6:00</option>
                            <option value="7">7:00</option>
                            <option value="8">8:00</option>
                            <option value="9">9:00</option>
                            <option value="10">10:00</option>
                            <option value="11">11:00</option>
                            <option value="12">12:00</option>
                            <option value="13">13:00</option>
                            <option value="14">14:00</option>
                            <option value="15">15:00</option>
                            <option value="16">16:00</option>
                            <option value="17">17:00</option>
                            <option value="18">18:00</option>
                            <option value="19">19:00</option>
                            <option value="20">20:00</option>
                            <option value="21">21:00</option>
                            <option value="22">22:00</option>
                            <option value="23">23:00</option>
                        </select>
                    </div>

                    <div class="control heatmap-3d-controls">
                        <div class="title"></div>
                        <button class="dec time ml-1"></button><br />
                        <button class="dec prev">< Prev</button>
                        <button class="dec next ml-1">Next ></button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
