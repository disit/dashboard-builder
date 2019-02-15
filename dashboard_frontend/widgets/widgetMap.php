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
<script src="../trafficRTDetails/js/OpenLayers-2.13.1/OpenLayers.js"></script>

<script type='text/javascript'>

    //Ogni "main" lato client di un widget è semple incluso nel risponditore ad evento ready del documento, così siamo sicuri di operare sulla pagina già caricata
    $(document).ready(function <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w']))?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId) {
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
            var widgetName = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w']))?>";
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
                enableFullscreenTab, shownPolyGroup = null;
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
            current_radius = null;
            current_opacity = null;
            changeRadiusOnZoom = false;
            estimatedRadius = null;
            estimateRadiusFlag = false;
            fullscreenHeatmap = null;
            fullscreenHeatmapFirstInstantiation = false;
            mapName = null;
            mapDate = null;
            resetPageFlag = null;
            wmsDatasetName = null;

            //Definizioni di funzione

            console.log("entrato in widgetMap. WidgetName = " + widgetName);

            current_page = 0;
            records_per_page = 1;

            wmsLayer = null;

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
                    map.defaultMapRef.off('moveend');

                    event.target.unbindPopup();
                    newpopup = null;
                    var popupText, realTimeData, measuredTime, rtDataAgeSec, targetWidgets, color1, color2 = null;
                    var urlToCall, fake, fakeId = null;

                    if (feature.properties.fake === 'true') {
                        urlToCall = "../serviceMapFake.php?getSingleGeoJson=true&singleGeoJsonId=" + feature.id;
                        fake = true;
                        fakeId = feature.id;
                    }
                    else {
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
                            }
                            else {
                                if (geoJsonServiceData.hasOwnProperty("Sensor")) {
                                    fatherNode = geoJsonServiceData.Sensor;
                                }
                                else {
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
                                    }
                                    else {
                                        popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                    }
                                }
                                else {
                                    popupText += '<tr><td>Website</td><td>-</td></tr>';
                                }
                            }
                            else {
                                popupText += '<tr><td>Website</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('email')) {
                                if ((serviceProperties.email !== '') && (serviceProperties.email !== undefined) && (serviceProperties.email !== 'undefined') && (serviceProperties.email !== null) && (serviceProperties.email !== 'null')) {
                                    popupText += '<tr><td>E-Mail</td><td>' + serviceProperties.email + '<td></tr>';
                                }
                                else {
                                    popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                                }
                            }
                            else {
                                popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('address')) {
                                if ((serviceProperties.address !== '') && (serviceProperties.address !== undefined) && (serviceProperties.address !== 'undefined') && (serviceProperties.address !== null) && (serviceProperties.address !== 'null')) {
                                    popupText += '<tr><td>Address</td><td>' + serviceProperties.address + '</td></tr>';
                                }
                                else {
                                    popupText += '<tr><td>Address</td><td>-</td></tr>';
                                }
                            }
                            else {
                                popupText += '<tr><td>Address</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('civic')) {
                                if ((serviceProperties.civic !== '') && (serviceProperties.civic !== undefined) && (serviceProperties.civic !== 'undefined') && (serviceProperties.civic !== null) && (serviceProperties.civic !== 'null')) {
                                    popupText += '<tr><td>Civic n.</td><td>' + serviceProperties.civic + '</td></tr>';
                                }
                                else {
                                    popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                                }
                            }
                            else {
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
                                }
                                else {
                                    popupText += '<tr><td>City</td><td>-</td></tr>';
                                }
                            }
                            else {
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
                                }
                                else {
                                    popupText += '<tr><td>Phone</td><td>-</td></tr>';
                                }
                            }
                            else {
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
                                }
                                else {
                                    popupText += "No description available";
                                }
                            }
                            else {
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
                                                    }
                                                    else {
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
                                            }
                                            else {
                                                //Righe dati
                                                for (var j = 0; j < colsQt; j++) {
                                                    k = parseInt(parseInt(j) - 1);
                                                    if (j === 0) {
                                                        //Cella label
                                                        newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                                                        newCell.css("font-weight", "bold");
                                                    }
                                                    else {
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
                                    }
                                    else {
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
                                            if ((realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.trim() !== '') && (realTimeData.head.vars[i] !== null) && (realTimeData.head.vars[i] !== 'undefined')) {
                                                if ((realTimeData.head.vars[i] !== 'updating') && (realTimeData.head.vars[i] !== 'measuredTime') && (realTimeData.head.vars[i] !== 'instantTime')) {
                                                    if (!realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.includes('Not Available')) {
                                                        //realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                                        dataDesc = realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function (str) {
                                                            return str.toUpperCase();
                                                        });
                                                        dataVal = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value;
                                                        dataLastBtn = '<td><button data-id="' + latLngId + '" type="button" class="lastValueBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-lastDataClicked="false" data-targetWidgets="' + targetWidgets + '" data-lastValue="' + realTimeData.results.bindings[0][realTimeData.head.vars[i]].value + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>value</button></td>';
                                                        data4HBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>4 hours</button></td>';
                                                        dataDayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="Day" data-range="1/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>24 hours</button></td>';
                                                        data7DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="7 days" data-range="7/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>7 days</button></td>';
                                                        data30DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="30 days" data-range="30/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>30 days</button></td>';
                                                        popupText += '<tr><td>' + dataDesc + '</td><td>' + dataVal + '</td>' + dataLastBtn + data4HBtn + dataDayBtn + data7DayBtn + data30DayBtn + '</tr>';
                                                    }
                                                }
                                                else {
                                                    measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                                    var now = new Date();
                                                    var measuredTimeDate = new Date(measuredTime);
                                                    rtDataAgeSec = Math.abs(now - measuredTimeDate) / 1000;
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
                                maxWidth: 435
                            }).setContent(popupText);

                            event.target.bindPopup(newpopup).openPopup();

                            if (hasRealTime) {
                                $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').show();
                                $('#<?= $_REQUEST['name_w'] ?>_map button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                                $('#<?= $_REQUEST['name_w'] ?>_map span.popupLastUpdate[data-id="' + latLngId + '"]').html(measuredTime);
                            }
                            else {
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
                                }
                                else {
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
                                        }
                                        else {
                                            $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                                        }
                                    }
                                    else {
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                    }
                                }
                                else {
                                    $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                                }
                            }
                            else {
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
                                    }
                                    else {
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
                                    }
                                    else {
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
                                }
                                else {
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
                        }
                    });
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
                    }
                    else {
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
                            }
                            else {
                                if (geoJsonServiceData.hasOwnProperty("Sensor")) {
                                    fatherNode = geoJsonServiceData.Sensor;
                                }
                                else {
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
                                    }
                                    else {
                                        popupText += '<tr><td>Website</td><td><a href="' + serviceProperties.website + '" target="_blank">Link</a></td></tr>';
                                    }
                                }
                                else {
                                    popupText += '<tr><td>Website</td><td>-</td></tr>';
                                }
                            }
                            else {
                                popupText += '<tr><td>Website</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('email')) {
                                if ((serviceProperties.email !== '') && (serviceProperties.email !== undefined) && (serviceProperties.email !== 'undefined') && (serviceProperties.email !== null) && (serviceProperties.email !== 'null')) {
                                    popupText += '<tr><td>E-Mail</td><td>' + serviceProperties.email + '<td></tr>';
                                }
                                else {
                                    popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                                }
                            }
                            else {
                                popupText += '<tr><td>E-Mail</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('address')) {
                                if ((serviceProperties.address !== '') && (serviceProperties.address !== undefined) && (serviceProperties.address !== 'undefined') && (serviceProperties.address !== null) && (serviceProperties.address !== 'null')) {
                                    popupText += '<tr><td>Address</td><td>' + serviceProperties.address + '</td></tr>';
                                }
                                else {
                                    popupText += '<tr><td>Address</td><td>-</td></tr>';
                                }
                            }
                            else {
                                popupText += '<tr><td>Address</td><td>-</td></tr>';
                            }

                            if (serviceProperties.hasOwnProperty('civic')) {
                                if ((serviceProperties.civic !== '') && (serviceProperties.civic !== undefined) && (serviceProperties.civic !== 'undefined') && (serviceProperties.civic !== null) && (serviceProperties.civic !== 'null')) {
                                    popupText += '<tr><td>Civic n.</td><td>' + serviceProperties.civic + '</td></tr>';
                                }
                                else {
                                    popupText += '<tr><td>Civic n.</td><td>-</td></tr>';
                                }
                            }
                            else {
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
                                }
                                else {
                                    popupText += '<tr><td>City</td><td>-</td></tr>';
                                }
                            }
                            else {
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
                                }
                                else {
                                    popupText += '<tr><td>Phone</td><td>-</td></tr>';
                                }
                            }
                            else {
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
                                }
                                else {
                                    popupText += "No description available";
                                }
                            }
                            else {
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
                                                    }
                                                    else {
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
                                            }
                                            else {
                                                //Righe dati
                                                for (var j = 0; j < colsQt; j++) {
                                                    k = parseInt(parseInt(j) - 1);
                                                    if (j === 0) {
                                                        //Cella label
                                                        newCell = $("<td>" + series.secondAxis.labels[z] + "</td>");
                                                        newCell.css("font-weight", "bold");
                                                    }
                                                    else {
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
                                    }
                                    else {
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
                                            if ((realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.trim() !== '') && (realTimeData.head.vars[i] !== null) && (realTimeData.head.vars[i] !== 'undefined')) {
                                                if ((realTimeData.head.vars[i] !== 'updating') && (realTimeData.head.vars[i] !== 'measuredTime') && (realTimeData.head.vars[i] !== 'instantTime')) {
                                                    if (!realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.includes('Not Available')) {
                                                        //realTimeData.results.bindings[0][realTimeData.head.vars[i]].value = '-';
                                                        dataDesc = realTimeData.head.vars[i].replace(/([A-Z])/g, ' $1').replace(/^./, function (str) {
                                                            return str.toUpperCase();
                                                        });
                                                        dataVal = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value;
                                                        dataLastBtn = '<td><button data-id="' + latLngId + '" type="button" class="lastValueBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-lastDataClicked="false" data-targetWidgets="' + targetWidgets + '" data-lastValue="' + realTimeData.results.bindings[0][realTimeData.head.vars[i]].value + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>value</button></td>';
                                                        data4HBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-fakeid="' + fakeId + '" data-id="' + latLngId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="4 Hours" data-range="4/HOUR" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>4 hours</button></td>';
                                                        dataDayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="Day" data-range="1/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>24 hours</button></td>';
                                                        data7DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="7 days" data-range="7/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>7 days</button></td>';
                                                        data30DayBtn = '<td><button data-id="' + latLngId + '" type="button" class="timeTrendBtn btn btn-sm" data-fake="' + fake + '" data-id="' + fakeId + '" data-field="' + realTimeData.head.vars[i] + '" data-serviceUri="' + feature.properties.serviceUri + '" data-timeTrendClicked="false" data-range-shown="30 days" data-range="30/DAY" data-targetWidgets="' + targetWidgets + '" data-color1="' + color1 + '" data-color2="' + color2 + '">Last<br>30 days</button></td>';
                                                        popupText += '<tr><td>' + dataDesc + '</td><td>' + dataVal + '</td>' + dataLastBtn + data4HBtn + dataDayBtn + data7DayBtn + data30DayBtn + '</tr>';
                                                    }
                                                }
                                                else {
                                                    measuredTime = realTimeData.results.bindings[0][realTimeData.head.vars[i]].value.replace("T", " ");
                                                    var now = new Date();
                                                    var measuredTimeDate = new Date(measuredTime);
                                                    rtDataAgeSec = Math.abs(now - measuredTimeDate) / 1000;
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
                                maxWidth: 435
                            }).setContent(popupText);

                            event.target.bindPopup(newpopup).openPopup();

                            if (hasRealTime) {
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').show();
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.recreativeEventMapContactsBtn[data-id="' + latLngId + '"]').trigger("click");
                                $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap span.popupLastUpdate[data-id="' + latLngId + '"]').html(measuredTime);
                            }
                            else {
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
                                }
                                else {
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
                                        }
                                        else {
                                            $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="30/DAY"]').attr("data-disabled", "false");
                                        }
                                    }
                                    else {
                                        $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpen button.timeTrendBtn[data-id="' + latLngId + '"][data-range="7/DAY"]').attr("data-disabled", "false");
                                    }
                                }
                                else {
                                    $('#<?= $_REQUEST['name_w'] ?>_modalLinkOpenBodyDefaultMap button.timeTrendBtn[data-id="' + latLngId + '"][data-range="1/DAY"]').attr("data-disabled", "false");
                                }
                            }
                            else {
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
                                    }
                                    else {
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
                                    }
                                    else {
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
                                }
                                else {
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
                                    }
                                    else {
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
                                    }
                                    else {
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
                    if ($("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen div.modalLinkOpenBodyDefaultMap").is(":visible")) {

                        fullscreendefaultMapRef.off();
                        fullscreendefaultMapRef.remove();
                    }

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
                        }
                        else {
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
                }
                else {
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
            function populateWidget() {
                let lastPopup = null;

                showWidgetContent(widgetName);

                let mapDivLocal = "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_map";
                map.mapName = widgetName;
                map.mapDivLocal = mapDivLocal;
                var latInit = 43.769789;
                var lngInit = 11.255694;
            //    console.log("Map ZOOM: " + widgetParameters.zoom);
            //    map.defaultMapRef = L.map(mapDivLocal).setView([43.769789, 11.255694], 11);
            //    map.defaultMapRef = L.map(mapDivLocal).setView([43.769789, 11.255694], widgetParameters.zoom);
                if (widgetParameters.latLng[0] != null && widgetParameters.latLng[0] != '') {
                        latInit = widgetParameters.latLng[0];
                }
                if (widgetParameters.latLng[1] != null && widgetParameters.latLng[1] != '') {
                    lngInit = widgetParameters.latLng[1];
                }
                map.defaultMapRef = L.map(mapDivLocal).setView([latInit, lngInit], widgetParameters.zoom);
                
                map.eventsOnMap = eventsOnMap;

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                    maxZoom: 18
                }).addTo(map.defaultMapRef);

                map.defaultMapRef.attributionControl.setPrefix('');

                //Crea un layer per la heatmap (i dati gli verranno passati nell'evento)
                //heatmap configuration
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

                    map.heatmapLayer = new HeatmapOverlay(map.cfg);
                    //map.heatmapLayer.zIndex = 20;
                  //  map.legendHeatmap = L.control({position: 'topright'});
                }

                map.legendHeatmap = L.control({position: 'topright'});

                function nextHeatmapPage()
                {
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
                            }
                        }

                        if (addMode === 'additive') {
                         //   if (baseQuery.includes("heatmap.php")) {
                                // addHeatmapToMap();
                                addHeatmapFromClient();
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

                            addHeatmapFromClient();
                        }

                    }
                }

                //   window.nextHeatmapPage = function()
                function prevHeatmapPage()
                {
                    if (current_page < numHeatmapPages() - 1) {
                        current_page++;
                        changeHeatmapPage(current_page);

                        for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                            if (map.eventsOnMap[i].eventType === 'heatmap') {
                                removeHeatmap(false);
                                map.eventsOnMap.splice(i, 1);
                            } else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                removeHeatmapColorLegend(i, false);
                                map.eventsOnMap.splice(i, 1);
                            }
                        }

                        if (addMode === 'additive') {
                         //   if (baseQuery.includes("heatmap.php")) {
                                // addHeatmapToMap();
                                addHeatmapFromClient();
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

                            addHeatmapFromClient();
                        }

                    }
                }

                function changeHeatmapPage(page)
                {
                    var btn_next = document.getElementById("<?= $_REQUEST['name_w'] ?>_nextButt");
                    var btn_prev = document.getElementById("<?= $_REQUEST['name_w'] ?>_prevButt");

                    // Validate page
                    if (numHeatmapPages() > 1) {
                        if (page < 1) page = 1;
                        if (page > numHeatmapPages()) page = numHeatmapPages();

                        if (current_page == 0) {
                            btn_next.style.visibility = "hidden";
                        } else {
                            btn_next.style.visibility = "visible";
                        }

                        if (current_page == numHeatmapPages() - 1) {
                            btn_prev.style.visibility = "hidden";
                        } else {
                            btn_prev.style.visibility = "visible";
                        }
                    }

                    if (current_page < numHeatmapPages()) {
                      //  $("#heatMapDescr").text(heatmapData[current_page].metadata[0].date);  // OLD-API
                        $("#heatMapDescr").text(heatmapData[current_page].metadata.date);
                        // heatmapData[current_page].metadata[0].date
                    }
                }

                function numHeatmapPages()
                {
                    return Math.ceil(heatmapData.length / records_per_page);
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
                    }
                    map.heatmapLayer.configure(map.cfg);
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
                            if(map.cfg["radius"] != current_radius) {
                                setOption('radius', current_radius, 1);
                            }
                            if(map.cfg["maxOpacity"] != current_opacity) {
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
                            if(map.cfg["radius"] != current_radius) {
                                setOption('radius', current_radius, 1);
                            }
                            if(map.cfg["maxOpacity"] != current_opacity) {
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
                    map.legendHeatmapDiv.innerHTML += '<div class="text">' + '<?php echo ucfirst(isset($_REQUEST["profile"]) ? $_REQUEST["profile"] : "Heatmap Controls"); ?>' + '</div>';
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
                        '<div id="heatMapDescr" style="text-align: center">' + mapDate + '</p>' +
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

                    function checkLegend(){
                     /*   if(document.getElementById("<?= $_REQUEST['name_w'] ?>_downSlider_radius") == null){
                            setTimeout(checkLegend, 500);
                        }
                        else{   */
                            if (baseQuery.includes("heatmap.php"))  {   // OLD HEATMAP
                                document.getElementById("<?= $_REQUEST['name_w'] ?>_sliderradius").addEventListener("input",function(){  setOption('radius', this.value, 1)}, false);
                            }

                            //document.getElementById("<?= $_REQUEST['name_w'] ?>_downSlider_opacity").addEventListener("click", function(){ downSlider('maxOpacity', 0.1, 2, 0)}, false);
                            document.getElementById("<?= $_REQUEST['name_w'] ?>_slidermaxOpacity").addEventListener("input", function(){ setOption('maxOpacity', this.value, 2)}, false);
                            //document.getElementById("<?= $_REQUEST['name_w'] ?>_rangemaxOpacity").addEventListener("click", function(){ upSlider('maxOpacity', 0.01, 2, 0.8)}, false);

                            document.getElementById("<?= $_REQUEST['name_w'] ?>_prevButt").addEventListener("click", function(){ prevHeatmapPage()}, false);
                            document.getElementById("<?= $_REQUEST['name_w'] ?>_nextButt").addEventListener("click", function(){ nextHeatmapPage()}, false);

                            if (baseQuery.includes("heatmap.php")) {   // OLD HEATMAP
                                document.getElementById("<?= $_REQUEST['name_w'] ?>_changeRad").addEventListener("change", function(){ updateChangeRadiusOnZoom(this)}, false);
                                document.getElementById("<?= $_REQUEST['name_w'] ?>_estimateRad").addEventListener("change", function(){ computeRadiusOnData(this)}, false);
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
                                        console.log(path);
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
                            }
                            else {
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

                            var pattern = new RegExp(re1 + re2 + re3 + re4 + re5 + re6 + re7 + re8 + re9, ["i"]);

                            if (queryType === "Default") {
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
                            }

                            $.ajax({
                                url: query + "&geometry=true&fullCount=false",
                                type: "GET",
                                data: {},
                                async: true,
                                timeout: 0,
                                dataType: 'json',
                                success: function (geoJsonData) {
                                    var fatherGeoJsonNode = null;

                                    if (queryType === "Default") {
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

                                        map.eventsOnMap.push(dataObj);
                                    }

                                    if (!gisLayersOnMap.hasOwnProperty(desc) && (display !== 'geometries')) {
                                        gisLayersOnMap[desc] = L.geoJSON(fatherGeoJsonNode, {
                                            pointToLayer: gisPrepareCustomMarker,
                                            onEachFeature: onEachFeature
                                        }).addTo(map.defaultMapRef);
                                    }

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

                                    eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").hide();
                                    eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("font-weight", "bold");
                                    eventGenerator.parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("color", eventGenerator.attr("data-activeFontColor"));
                                    if (eventGenerator.parents("div.gisMapPtrContainer").find('a.gisPinLink').attr("data-symbolMode") === 'auto') {
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").html("near_me");
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("color", "white");
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisPinIcon").css("text-shadow", "2px 2px 4px black");
                                    }
                                    else {
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

                                                                gisGeometryLayersOnMap[desc].push(L.geoJSON(ciclePathFeature, {}).addTo(map.defaultMapRef));
                                                                gisGeometryTankForFullscreen[desc].tank.push(ciclePathFeature);
                                                            }
                                                        },
                                                        error: function (geometryErrorData) {
                                                            console.log("Ko");
                                                            console.log(JSON.stringify(geometryErrorData));
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    }
                                },
                                error: function (errorData) {
                                    gisLayersOnMap[event.desc] = "loadError";

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

                                    eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").hide();
                                    eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadErrorIcon").show();

                                    setTimeout(function () {
                                        eventGenerator.parents("div.gisMapPtrContainer").find("i.gisLoadErrorIcon").hide();
                                        eventGenerator.parents("div.gisMapPtrContainer").find("a.gisPinLink").attr("data-onMap", "false");
                                        eventGenerator.parents("div.gisMapPtrContainer").find("a.gisPinLink").show();
                                    }, 1500);

                                    console.log("Error in getting GeoJSON from ServiceMap");
                                    console.log(JSON.stringify(errorData));
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
                                            }
                                            else {
                                                popupText += '<b>Price (€) : </b>N/A<br>';
                                            }
                                        }
                                        else {
                                            popupText += '<b>Free event: </b>' + freeEvent + '<br>';
                                        }
                                    }
                                }
                                else {
                                    popupText += 'No further details available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';
                                if (descriptionIT !== 'undefined') {
                                    popupText += descriptionIT;
                                }
                                else {
                                    popupText += 'No description available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapTimingContainer">';
                                if ((startDate !== 'undefined') || (endDate !== 'undefined') || (startTime !== 'undefined')) {
                                    popupText += '<b>From: </b>';
                                    if (startDate !== 'undefined') {
                                        popupText += startDate;
                                    }
                                    else {
                                        popupText += 'N/A';
                                    }
                                    popupText += '<br/>';

                                    popupText += '<b>To: </b>';
                                    if (endDate !== 'undefined') {
                                        popupText += endDate;
                                    }
                                    else {
                                        popupText += 'N/A';
                                    }
                                    popupText += '<br/>';

                                    if (startTime !== 'undefined') {
                                        popupText += '<b>Times: </b>' + startTime + '<br/>';
                                    }
                                    else {
                                        popupText += '<b>Times: </b>N/A<br/>';
                                    }

                                }
                                else {
                                    popupText += 'No timings info available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';
                                if ((phone !== 'undefined') || (website !== 'undefined')) {
                                    if (phone !== 'undefined') {
                                        popupText += '<b>Phone: </b>' + phone + '<br/>';
                                    }
                                    else {
                                        popupText += '<b>Phone: </b>N/A<br/>';
                                    }

                                    if (website !== 'undefined') {
                                        if (website.includes('http') || website.includes('https')) {
                                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                                        }
                                        else {
                                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                                        }
                                    }
                                    else {
                                        popupText += '<b>Website: </b>N/A';
                                    }
                                }
                                else {
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
                        var zm = map.defaultMapRef.getZoom();

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
                                console.log(bnds.getSouth() + ";" + bnds.getWest() + ";" + bnds.getNorth() + ";" + bnds.getEast());
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
                                        console.log("@time " + time);
                                    },
                                    error: function (err) {
                                        console.log(err);
                                        alert("error see log json");
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
                     //   window.addHeatmapToMap = function() {

                        function distance(lat1, lon1, lat2, lon2, unit) {   // unit: 'K' for Kilometers
                            if ((lat1 == lat2) && (lon1 == lon2)) {
                                return 0;
                            }
                            else {
                                var radlat1 = Math.PI * lat1/180;
                                var radlat2 = Math.PI * lat2/180;
                                var theta = lon1-lon2;
                                var radtheta = Math.PI * theta/180;
                                var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                                if (dist > 1) {
                                    dist = 1;
                                }
                                dist = Math.acos(dist);
                                dist = dist * 180/Math.PI;
                                dist = dist * 60 * 1.1515;
                                if (unit=="K") { dist = dist * 1.609344 }
                                if (unit=="N") { dist = dist * 0.8684 }
                                return dist;
                            }
                        }

                        function getRadius(){
                            var radius;
                            var currentZoom = map.defaultMapRef.getZoom();
                            if (estimateRadiusFlag && estimatedRadius) {
                                metresPerPixel = 40075016.686 * Math.abs(Math.cos(map.defaultMapRef.getCenter().lat * Math.PI / 180)) / Math.pow(2, currentZoom + 8);
                                radius = ((estimatedRadius * 1000) / metresPerPixel) / 10;
                                if (radius > 1000) {

                                } else if (radius > 1) {
                                    if (currentZoom < prevZoom) {
                                        prevZoom = currentZoom;
                                        return radius/1.2;
                                    } else {
                                        prevZoom = currentZoom;
                                        return radius/1.2;
                                    }
                                } else {
                                    prevZoom = currentZoom;
                                    return 1;
                                }
                            }
                            if (prevZoom == null) {
                                prevZoom = widgetParameters.zoom;
                            }
                            if (currentZoom === 7){
                                radius = 1;
                            }
                            else if (currentZoom === 8) {
                                radius = 1;
                            }
                            else if (currentZoom === 9) {
                                radius = 1;
                            }
                            else if (currentZoom === 10) {
                                if (currentZoom > prevZoom) {
                                    radius = 2;
                                } else {
                                    radius = 1;
                                }
                            }
                            else if (currentZoom === 11) {
                                if (currentZoom > prevZoom) {
                                    radius = 3.5;
                                } else {
                                    radius = 2;
                                }
                            }
                            else if (currentZoom === 12) {
                                if (currentZoom > prevZoom) {
                                    radius = 10;
                                } else {
                                    radius = 3.5;
                                }
                            }
                            else if (currentZoom === 13) {
                                if (currentZoom > prevZoom) {
                                    radius = 16;
                                } else {
                                    radius = 10;
                                }
                            }
                            else if (currentZoom === 14) {
                                if (currentZoom > prevZoom) {
                                    radius = 31;
                                } else {
                                    radius = 16;
                                }
                            }
                            else if (currentZoom === 15) {
                                if (currentZoom > prevZoom) {
                                    radius = 60;
                                } else {
                                    radius = 31;
                                }
                            }
                            else if (currentZoom === 16) {
                                if (currentZoom > prevZoom) {
                                    radius = 80;
                                } else {
                                    radius = 60;
                                }
                            }
                            else if (currentZoom === 17) {
                                if (currentZoom > prevZoom) {
                                    radius = 100;
                                } else {
                                    radius = 80;
                                }
                            }
                            else if (currentZoom === 18) {
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
                        map.defaultMapRef.on('zoomend', function(ev) {
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
                            if (map.eventsOnMap.length > 0) {
                                for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                    if (map.eventsOnMap[i].eventType === 'heatmap') {
                                        removeHeatmap(true);
                                        map.eventsOnMap.splice(i, 1);
                                    } else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                        removeHeatmapColorLegend(i, true);
                                        map.eventsOnMap.splice(i, 1);
                                    }
                                }
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
                                query = baseQuery + '&limit=30&latitude_min=' + latitude_min + '&latitude_max=' + latitude_max + '&longitude_min=' + longitude_min + '&longitude_max=' + longitude_max;
                                let metricNameSplit = baseQuery.split("metricName=")[1];
                            } else {
                              //  let metricNameSplit = baseQuery.split("metricName=")[1];
                              //  heatmapMetricName = baseQuery.split("metricName=")[1];
                            //    var datasetNameAux = baseQuery.split("https://wmsserver.snap4city.org/geoserver/Snap4City/wms?service=WMS&layers=")[1];
                                var datasetNameAux = baseQuery.split("WMS&layers=")[1];
                                wmsDatasetName = datasetNameAux.split("&metricName=")[0];
                                query = 'https://heatmap.snap4city.org/heatmap.php?dataset=' + wmsDatasetName + '&limit=30&latitude_min=' + latitude_min + '&latitude_max=' + latitude_max + '&longitude_min=' + longitude_min + '&longitude_max=' + longitude_max;
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
                                }
                            });

                            //     for (var i = 0; i < heatmapData.length; i++) {
                            //heatmap recommender data
                            map.testData = {
                                //   max: 8,
                                data: heatmapData[current_page].data
                            };

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
                                            map.heatmapLayer.setData({max:heatmapRange[0].range10Inf, min:heatmapRange[0].range1Inf, data:map.testData.data});
                                            map.defaultMapRef.addLayer(map.heatmapLayer);   // OLD HEATMAP
                                            //    if (estimateRadiusFlag === true) {
                                            var distArray = [];             // MODALITA HEATMAP ON DATA DISTANCE
                                            if (map.testData.data.length > 20) {
                                                for (k = 0; k < 20; k++) {
                                                    distArray[k] = distance(map.testData.data[k].latitude, map.testData.data[k].latitude, map.testData.data[k + 1].latitude, map.testData.data[k + 1].latitude, "K");
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
                                            var initRadius = ((estimatedRadius * 1000) / metresPerPixel) / 10;
                                            setOption('radius', initRadius.toFixed(1), 1);
                                            //   }
                                        } else {                    // NEW HEATMAP  FIRST
                                         //   var timestampISO = "2019-01-23T20:20:15.000Z";
                                            var timestamp = map.testMetadata.metadata.date;
                                            var timestampISO = timestamp.replace(" ", "T") + ".000Z";
                                            wmsLayer = L.tileLayer.wms("https://wmsserver.snap4city.org/geoserver/Snap4City/wms", {
                                                layers: 'Snap4City:' + wmsDatasetName,
                                                format: 'image/png',
                                                crs: L.CRS.EPSG4326,
                                                transparent: true,
                                                opacity: 0.5,
                                                time: timestampISO,
                                              //  bbox: [24.7926004025304,60.1025194986424,25.1905923952885,60.2516802986263],
                                                tiled: true   // TESTARE COME ANTWERP ??
                                              //  attribution: "IGN ©"
                                            }).addTo(map.defaultMapRef);
                                            current_opacity = 0.5;

                                          //  var imageUrl = 'http://blackicemedia.com/presentations/2013-02-hires/img/awesome_tiger.svg',
                                         /*   var imageUrl = '../img/prova.png';
                                               // imageBounds = [[48.979253, 2.262622], [48.985253, 2.270622]];
                                                imageBounds = [[60.2322, 24.8188], [60.1572, 25.095]];

                                            L.imageOverlay(imageUrl, imageBounds).addTo(map.defaultMapRef);*/

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
                                      //  map.eventsOnMap.push(heatmap);

                                        event.legendColors = heatmapLegendColors;
                                        map.eventsOnMap.push(event);
                                    } else {
                                        console.log("Ko Heatmap");
                                        console.log(JSON.stringify(errorData));
                                    }
                                },
                                error: function (errorData) {
                                    console.log("Ko Heatmap");
                                    console.log(JSON.stringify(errorData));
                                }
                            });

                        }

                        window.addHeatmapFromClient = function() {
                      //  function addHeatMapFromClient() {

                            let heatmap = {};
                            heatmap.eventType = "heatmap";

                            map.testData = {
                                //   max: 8,
                                data: heatmapData[current_page].data
                            };

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
                                            // Imposta il max e il min della scala di normalizzazione del dataset come il max e il min del range di configurazione
                                            map.heatmapLayer.setData({max:heatmapRange[0].range10Inf, min:heatmapRange[0].range1Inf, data:map.testData.data});
                                            map.defaultMapRef.addLayer(map.heatmapLayer);     // OLD HEATMAP
                                        } else {                                                        // NEW HEATMAP
                                            var timestamp = map.testMetadata.metadata.date;
                                            var timestampISO = timestamp.replace(" ", "T") + ".000Z";
                                            wmsLayer = L.tileLayer.wms("https://wmsserver.snap4city.org/geoserver/Snap4City/wms", {
                                                layers: 'Snap4City:' + wmsDatasetName,
                                                format: 'image/png',
                                                crs: L.CRS.EPSG4326,
                                                transparent: true,
                                                opacity: current_opacity,
                                                time: timestampISO,
                                                //  bbox: [24.7926004025304,60.1025194986424,25.1905923952885,60.2516802986263],
                                                tiled: true   // TESTARE COME ANTWERP ??
                                                //  attribution: "IGN ©"
                                            }).addTo(map.defaultMapRef);
                                        }

                                        // add legend to map
                                        map.legendHeatmap.addTo(map.defaultMapRef);
                                        map.eventsOnMap.push(heatmap);

                                        if(changeRadiusOnZoom) {
                                            $('#<?= $_REQUEST['name_w'] ?>_changeRad').prop('checked', true);
                                            if(estimateRadiusFlag) {
                                                $('#<?= $_REQUEST['name_w'] ?>_changeRad').prop('disabled', true);
                                            }
                                        }

                                        if(estimateRadiusFlag) {
                                            $('#<?= $_REQUEST['name_w'] ?>_estimateRad').prop('checked', true);
                                            $('#<?= $_REQUEST['name_w'] ?>_estimateRad').prop('disabled', false);
                                        } else {
                                            $('#<?= $_REQUEST['name_w'] ?>_estimateRad').prop('disabled', false);
                                        }

                                        var mapControlsContainer = document.getElementsByClassName("leaflet-control")[0];

                                        //    var legendImgPath = heatmapRange[0].iconPath;
                                        //     div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';
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
                                        //  map.eventsOnMap.push(heatmap);
                                        event.legendColors = heatmapLegendColors;
                                        map.eventsOnMap.push(event);

                                    } else {
                                        console.log("Ko Heatmap");
                                        console.log(JSON.stringify(errorData));
                                    }
                                },
                                error: function (errorData) {
                                    console.log("Ko Heatmap");
                                    console.log(JSON.stringify(errorData));
                                }
                            });

                        }

                        if (addMode === 'additive') {
                            addHeatmapToMap();
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
                        }
                        else {
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
                            if ((map.eventsOnMap[i].eventType === 'selectorEvent') && (map.eventsOnMap[i].desc === desc)) {
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
                            if (map.eventsOnMap[i].eventType === "trafficRealTimeDetails") {
                                map.defaultMapRef.removeLayer(map.eventsOnMap[i].marker);
                                map.defaultMapRef.removeControl(map.eventsOnMap[i].legend);
                                map.defaultMapRef.removeLayer(map.eventsOnMap[i].trafficLayer);
                                map.eventsOnMap.splice(i, 1);
                            }
                        }

                      //  resizeMapView(map.defaultMapRef);
                    }
                });
                $(document).on('removeHeatmap', function (event) {
                    if (event.target === map.mapName) {
                        for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                            if (map.eventsOnMap[i].eventType === 'heatmap') {
                                removeHeatmap(true);
                                map.eventsOnMap.splice(i, 1);
                            } else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                removeHeatmapColorLegend(i, true);
                                map.eventsOnMap.splice(i, 1);
                            }
                        }
                    }
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

                    if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
                        showHeader = false;
                    }
                    else {
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
                        }
                        else {
                            if ((enableFullscreenModal === 'yes') && (enableFullscreenTab === 'no')) {
                                $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 25 - 2));
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();
                            }
                            else {
                                if ((enableFullscreenModal === 'no') && (enableFullscreenTab === 'yes')) {
                                    $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 25 - 2));
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                                }
                                else {
                                    $("#" + widgetName + "_buttonsDiv").css("width", "0px");
                                    $("#" + widgetName + "_buttonsDiv").hide();
                                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 0 - 25 - 2));
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                                }
                            }
                        }
                    }
                    else {
                        if ((enableFullscreenTab === 'yes') && (enableFullscreenModal === 'yes')) {
                            $("#" + widgetName + "_buttonsDiv").css("width", "50px");
                            titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 2));
                            $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                            $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                        }
                        else {
                            if ((enableFullscreenTab === 'yes') && (enableFullscreenModal === 'no')) {
                                $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).hide();
                                $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).show();
                            }
                            else {
                                if ((enableFullscreenTab === 'no') && (enableFullscreenModal === 'yes')) {
                                    $("#" + widgetName + "_buttonsDiv").css("width", "25px");
                                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(0).show();
                                    $("#" + widgetName + "_buttonsDiv div.singleBtnContainer").eq(1).hide();
                                }
                                else {
                                    $("#" + widgetName + "_buttonsDiv").hide();
                                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 2));
                                }
                            }
                        }
                    }

                    $("#" + widgetName + "_titleDiv").css("width", titleWidth + "px");

                    if (firstLoad === false) {
                        showWidgetContent(widgetName);
                    }
                    else {
                        setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                    }
                    populateWidget();
                 //   globalMapView = true;
                },
                error: function (errorData) {

                }
            });

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
                                }

                                $.ajax({
                                    url: query + "&geometry=true&fullCount=false",
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    timeout: 0,
                                    dataType: 'json',
                                    success: function (geoJsonData) {
                                        var fatherGeoJsonNode = null;

                                        if (queryType === "Default") {
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
                                        }

                                        for (var i = 0; i < fatherGeoJsonNode.features.length; i++) {

                                            fatherGeoJsonNode.features[i].properties.targetWidgets = targets;
                                            fatherGeoJsonNode.features[i].properties.color1 = color1;
                                            fatherGeoJsonNode.features[i].properties.color2 = color2;
                                        }

                                        if (gisLayersOnMap.hasOwnProperty(desc) && (display !== 'geometries')) {
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
                                        }
                                        else {
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
                                            }
                                            else {
                                                popupText += '<b>Price (€) : </b>N/A<br>';
                                            }
                                        }
                                        else {
                                            popupText += '<b>Free event: </b>' + freeEvent + '<br>';
                                        }
                                    }
                                }
                                else {
                                    popupText += 'No further details available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDescContainer">';
                                if (descriptionIT !== 'undefined') {
                                    popupText += descriptionIT;
                                }
                                else {
                                    popupText += 'No description available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapTimingContainer">';
                                if ((startDate !== 'undefined') || (endDate !== 'undefined') || (startTime !== 'undefined')) {
                                    popupText += '<b>From: </b>';
                                    if (startDate !== 'undefined') {
                                        popupText += startDate;
                                    }
                                    else {
                                        popupText += 'N/A';
                                    }
                                    popupText += '<br/>';

                                    popupText += '<b>To: </b>';
                                    if (endDate !== 'undefined') {
                                        popupText += endDate;
                                    }
                                    else {
                                        popupText += 'N/A';
                                    }
                                    popupText += '<br/>';

                                    if (startTime !== 'undefined') {
                                        popupText += '<b>Times: </b>' + startTime + '<br/>';
                                    }
                                    else {
                                        popupText += '<b>Times: </b>N/A<br/>';
                                    }

                                }
                                else {
                                    popupText += 'No timings info available';
                                }
                                popupText += '</div>';

                                popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapContactsContainer">';
                                if ((phone !== 'undefined') || (website !== 'undefined')) {
                                    if (phone !== 'undefined') {
                                        popupText += '<b>Phone: </b>' + phone + '<br/>';
                                    }
                                    else {
                                        popupText += '<b>Phone: </b>N/A<br/>';
                                    }

                                    if (website !== 'undefined') {
                                        if (website.includes('http') || website.includes('https')) {
                                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                                        }
                                        else {
                                            popupText += '<b><a href="' + website + '" target="_blank">Website</a></b><br>';
                                        }
                                    }
                                    else {
                                        popupText += '<b>Website: </b>N/A';
                                    }
                                }
                                else {
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
                                    console.log(bnds.getSouth() + ";" + bnds.getWest() + ";" + bnds.getNorth() + ";" + bnds.getEast());
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
                                        url: "https://firenzetraffic.km4city.org/trafficRTDetails/roads/read.php" + "?sLat=" + event.minLat + "&sLong=" + event.minLng + "&eLat=" + event.maxLat + "&eLong=" + event.maxLng + "&zoom=" + event.zm,     // MOD GP
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
                                        }
                                    });
                                }

                                function loadDensity() {
                                    $.ajax({
                                    //    url: "http://localhost/dashboardSmartCity/trafficRTDetails/density/read.php" + "?sLat=" + event.minLat + "&sLong=" + event.minLng + "&eLat=" + event.maxLat + "&eLong=" + event.maxLng + "&zoom=" + event.zm,
                                        url: "https://firenzetraffic.km4city.org/trafficRTDetails/density/read.php" + "?sLat=" + event.minLat + "&sLong=" + event.minLng + "&eLat=" + event.maxLat + "&eLong=" + event.maxLng + "&zoom=" + event.zm,   // MOD GP
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
                                            console.log("@time " + time);
                                        },
                                        error: function (err) {
                                            console.log(err);
                                            alert("error see log json");
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
                            if (map.eventsOnMap[i].eventType === 'heatmap'){
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

                                let legendHeatmap = L.control({position: 'topright'});

                                function distance(lat1, lon1, lat2, lon2, unit) {   // unit: 'K' for Kilometers
                                    if ((lat1 == lat2) && (lon1 == lon2)) {
                                        return 0;
                                    }
                                    else {
                                        var radlat1 = Math.PI * lat1/180;
                                        var radlat2 = Math.PI * lat2/180;
                                        var theta = lon1-lon2;
                                        var radtheta = Math.PI * theta/180;
                                        var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                                        if (dist > 1) {
                                            dist = 1;
                                        }
                                        dist = Math.acos(dist);
                                        dist = dist * 180/Math.PI;
                                        dist = dist * 60 * 1.1515;
                                        if (unit=="K") { dist = dist * 1.609344 }
                                        if (unit=="N") { dist = dist * 0.8684 }
                                        return dist;
                                    }
                                }

                                function getRadius(){
                                    var radius;
                                    var currentZoom = fullscreendefaultMapRef.getZoom();
                                    if (estimateRadiusFlag && estimatedRadius) {
                                        metresPerPixel = 40075016.686 * Math.abs(Math.cos(fullscreendefaultMapRef.getCenter().lat * Math.PI / 180)) / Math.pow(2, currentZoom + 8);
                                        radius = ((estimatedRadius * 1000) / metresPerPixel) / 10;
                                        if (radius > 1000) {

                                        } else if (radius > 1) {
                                            if (currentZoom < prevZoom) {
                                                prevZoom = currentZoom;
                                                return radius/1.2;
                                            } else {
                                                prevZoom = currentZoom;
                                                return radius/1.2;
                                            }
                                        } else {
                                            prevZoom = currentZoom;
                                            return 1;
                                        }
                                    }
                                    if (prevZoom == null) {
                                        prevZoom = widgetParameters.zoom;
                                    }
                                    if (currentZoom === 7){
                                        radius = 1;
                                    }
                                    else if (currentZoom === 8) {
                                        radius = 1;
                                    }
                                    else if (currentZoom === 9) {
                                        radius = 1;
                                    }
                                    else if (currentZoom === 10) {
                                        if (currentZoom > prevZoom) {
                                            radius = 2;
                                        } else {
                                            radius = 1;
                                        }
                                    }
                                    else if (currentZoom === 11) {
                                        if (currentZoom > prevZoom) {
                                            radius = 3.5;
                                        } else {
                                            radius = 2;
                                        }
                                    }
                                    else if (currentZoom === 12) {
                                        if (currentZoom > prevZoom) {
                                            radius = 10;
                                        } else {
                                            radius = 3.5;
                                        }
                                    }
                                    else if (currentZoom === 13) {
                                        if (currentZoom > prevZoom) {
                                            radius = 16;
                                        } else {
                                            radius = 10;
                                        }
                                    }
                                    else if (currentZoom === 14) {
                                        if (currentZoom > prevZoom) {
                                            radius = 31;
                                        } else {
                                            radius = 16;
                                        }
                                    }
                                    else if (currentZoom === 15) {
                                        if (currentZoom > prevZoom) {
                                            radius = 60;
                                        } else {
                                            radius = 31;
                                        }
                                    }
                                    else if (currentZoom === 16) {
                                        if (currentZoom > prevZoom) {
                                            radius = 80;
                                        } else {
                                            radius = 60;
                                        }
                                    }
                                    else if (currentZoom === 17) {
                                        if (currentZoom > prevZoom) {
                                            radius = 100;
                                        } else {
                                            radius = 80;
                                        }
                                    }
                                    else if (currentZoom === 18) {
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
                                fullscreendefaultMapRef.on('zoomend', function(ev) {
                                    // zoom level changed... adjust heatmap layer options!
                                    if (changeRadiusOnZoom === true) {
                                        if (prevZoom === null) {
                                            prevZoom = widgetParameters.zoom;
                                        }

                                        if (baseQuery.includes("heatmap.php")) {    // OLD HEATMAP
                                            // INSERIRE CAMBIO SLIDER ZOOM
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_sliderradius").value = parseFloat(getRadius()).toFixed(1);
                                            setOption('radius', getRadius(), 1)           // MODALITA HEATMAP ON ZOOM
                                        }
                                    }
                                });

                                function nextHeatmapPage()
                                {
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
                                            }
                                        }

                                        if (addMode === 'additive') {
                                          //  if (baseQuery.includes("heatmap.php")) {
                                                // addHeatmapToMap();
                                                addHeatmapFromClient();
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
                                    if (current_page < numHeatmapPages() - 1) {
                                        current_page++;
                                        changeHeatmapPage(current_page);

                                        for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                            if (map.eventsOnMap[i].eventType === 'heatmap') {
                                                removeHeatmap(false);
                                                map.eventsOnMap.splice(i, 1);
                                            } else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                                removeHeatmapColorLegend(i, false);
                                                map.eventsOnMap.splice(i, 1);
                                            }
                                        }

                                        if (addMode === 'additive') {
                                          //  if (baseQuery.includes("heatmap.php")) {
                                                // addHeatmapToMap();
                                                addHeatmapFromClient();
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

                                function changeHeatmapPage(page)
                                {
                                    var btn_next = document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_nextButt");
                                    var btn_prev = document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_prevButt");

                                    // Validate page
                                    if (numHeatmapPages() > 1) {
                                        if (page < 1) page = 1;
                                        if (page > numHeatmapPages()) page = numHeatmapPages();

                                        if (current_page == 0) {
                                            btn_next.style.visibility = "hidden";
                                        } else {
                                            btn_next.style.visibility = "visible";
                                        }

                                        if (current_page == numHeatmapPages() - 1) {
                                            btn_prev.style.visibility = "hidden";
                                        } else {
                                            btn_prev.style.visibility = "visible";
                                        }
                                    }

                                    if (current_page < numHeatmapPages()) {
                                      //  $("#modalLinkOpenHeatMapDescr").text(heatmapData[current_page].metadata[0].date); // OLD-API
                                        $("#modalLinkOpenHeatMapDescr").text(heatmapData[current_page].metadata.date);
                                        // heatmapData[current_page].metadata[0].date
                                    }
                                }

                                function numHeatmapPages()
                                {
                                    return Math.ceil(heatmapData.length / records_per_page);
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
                                      //  map.heatmapLayer.configure(map.cfg);
                                        fullscreenHeatmap.configure(map.cfg);
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
                                    }
                                    fullscreenHeatmap.configure(map.cfg);
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
                                            if (wmsLayer) {
                                                wmsLayer.setOpacity(value);
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
                                            if (wmsLayer) {
                                               // wmsLayer.eachLayer(function (layer) {
                                                    var density = wmsLayer.options["opacity"];
                                                    wmsLayer.setStyle(getStyle(density));
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

                                legendHeatmap.onAdd = function () {
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
                                        '<div id="modalLinkOpenHeatMapDescr" style="text-align: center">' + mapDate + '</p>' +
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

                                    function checkLegend(){
                                      /*  if(document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_downSlider_radius") == null){
                                            setTimeout(checkLegend, 500);
                                        }
                                        else{   */
                                            if (baseQuery.includes("heatmap.php"))  {   // OLD HEATMAP
                                                document.getElementById("<?= $_REQUEST['name_w'] ?>_sliderradius").addEventListener("input",function(){  setOption('radius', this.value, 1)}, false);
                                            }
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_slidermaxOpacity").addEventListener("input", function(){ setOption('maxOpacity', this.value, 2)}, false);

                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_prevButt").addEventListener("click", function(){ prevHeatmapPage()}, false);
                                            document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_nextButt").addEventListener("click", function(){ nextHeatmapPage()}, false);

                                            if (baseQuery.includes("heatmap.php"))  {   // OLD HEATMAP
                                                document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_changeRad").addEventListener("change", function(){ updateChangeRadiusOnZoom(this)}, false);
                                                document.getElementById("<?= $_REQUEST['name_w'] ?>_modalLinkOpen_estimateRad").addEventListener("change", function(){ computeRadiusOnData(this)}, false);
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

                                if(fullscreenHeatmap) {
                                    fullscreendefaultMapRef.removeLayer(fullscreenHeatmap);
                                }

                             /*   L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                                    maxZoom: 18
                                }).addTo(fullscreendefaultMapRef);*/


                                /*   if (map.eventsOnMap.length > 0) {
                                       for (let i = map.eventsOnMap.length - 1; i >= 0; i--) {
                                           if (map.eventsOnMap[i].eventType === 'heatmap') {
                                               removeHeatmap(true);
                                               map.eventsOnMap.splice(i, 1);
                                           } else if (map.eventsOnMap[i].type === 'addHeatmap') {
                                               removeHeatmapColorLegend(i, true);
                                               map.eventsOnMap.splice(i, 1);
                                           }
                                       }
                                   }*/

                                let cfg = JSON.parse(heatmapRange[0].leafletConfigJSON);

                                if (current_radius != null) {
                                    cfg['radius'] = current_radius;
                                }
                                if (current_opacity != null) {
                                    cfg['maxOpacity'] = current_opacity;
                                }

                             //   map.heatmapLayer.setData({max:heatmapRange[0].range10Inf, min:heatmapRange[0].range1Inf, data:map.testData.data});

                                fullscreenHeatmap = new HeatmapOverlay(cfg);

                                fullscreenHeatmap.setData(map.testData);
                                if (baseQuery.includes("heatmap.php")) {
                                    fullscreendefaultMapRef.addLayer(fullscreenHeatmap);
                                } else {
                                    var timestamp = map.testMetadata.metadata.date;
                                    var timestampISO = timestamp.replace(" ", "T") + ".000Z";
                                    wmsLayer = L.tileLayer.wms("https://wmsserver.snap4city.org/geoserver/Snap4City/wms", {
                                        layers: 'Snap4City:' + wmsDatasetName,
                                        format: 'image/png',
                                        crs: L.CRS.EPSG4326,
                                        transparent: true,
                                        opacity: 0.5,
                                        time: timestampISO,
                                        //  bbox: [24.7926004025304,60.1025194986424,25.1905923952885,60.2516802986263],
                                        tiled: true   // TESTARE COME ANTWERP ??
                                        //  attribution: "IGN ©"
                                    }).addTo(map.fullscreendefaultMapRef);
                                }

                              //  fullscreendefaultMapRef.addLayer(map.heatmapLayer);

                                legendHeatmap.addTo(fullscreendefaultMapRef);

                                var heatmapLegendColors = L.control({position: 'bottomleft'});

                                heatmapLegendColors.onAdd = function (map) {

                                    var div = L.DomUtil.create('div', 'info legend'),
                                        grades = ["Legend"];
                                    //    labels = ["http://localhost/dashboardSmartCity/trafficRTDetails/legend.png"];
                                    var legendImgPath = heatmapRange[0].iconPath;
                                    div.innerHTML += " <img src=" + legendImgPath + " height='100%'" + '<br>';
                                    return div;
                                };

                                heatmapLegendColors.addTo(fullscreendefaultMapRef);

                            }

                          //  resizeMapView(fullscreendefaultMapRef);
                        }

                    }
                }, 750);    // PANTALEO - AUMENTARE UN PO' IL VALORE DI setTimeOut QUI SE LA MAPPA NON CARICA ABBASTANZA VELOCEMENTE SE HA UNA HEATMAP DI DEFAULT

                $("#<?= $_REQUEST['name_w'] ?>_modalLinkOpen").modal('show');

            });
        }
    )
    ;//Fine document ready
</script>

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
            </div>
        </div>
    </div>
</div>
