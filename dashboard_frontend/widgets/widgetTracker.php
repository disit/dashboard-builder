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
<link rel="stylesheet" href="../css/widgetTracker.css">
<script type='text/javascript'>
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

    //    $('[data-toggle="tooltip"]').tooltip();

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
        var wsRetryActive, wsRetryTime = null;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";
        var styleParameters, metricType, originalMetricType, metricName, mapRef, pattern, udm, udmPos, threshold,
            thresholdEval, appId, flowId, nrMetricType,
            sm_field, sizeRowsWidget, fontSize, value, countdownRef, widgetTitle, metricData,
            widgetHeaderColor, optionsMenuLeft,
            widgetHeaderFontColor, widgetOriginalBorderColor, urlToCall, showHeader, motivation, trackSrcList,
            getMyPersonalDataUrl, newVariableTitlePopup,
            widgetParameters, webSocket, openWs, openWsConn, wsError, manageIncomingWsMsg, wsClosed, chartColor,
            dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor = null;
        var newControlRow = [];
        var newControlColorBtn = [];
        var newControlVariableName = [];
        var newControlCaret = [];
        var newControlSubCnt = [];
        var newControlSubRow = [];
        var newControlSubLbl = [];
        var newControlSubField = [];
        var dateDiv = [];
        var newControlDateSubField = [];
        var showControl = [];
        var followControl = [];
        var timeTrendControl = [];
        var prevBtn = [];
        var nextBtn = [];

        defaultColors = ["#ff9900", "#ff6666", "#00e6e6", "#33ccff", "#33cc33", "#009900", "#ffdb4d"];
        var currentRoutes = [];
        var pastTrips = [];
        var lastTrips = [];
        var lastPosMarkers = [];
        var mapFollowers = [];
        var tripIndexArray = [];
        tripsDataGlobal = {};
        tripsDaysGlobal = {};
        mapMarkers = {};
        currentTripMarkerId = 0;
        currentDay = [];
        var ajaxData = {};
        var ajaxInnerData = {};
        var actionData = {};
        var appName = "";
        var trajectory = {};
        var dataType = [];
        var trajectoryGroup = {};
        var oms = {}    // OverlappingMarkerSpiderfier for Leaflet
        var lastSample = null;
        wizardRows = null;
        trackSrcIndex = null;
        viewFlag = [];
        var rowParameters = [];
        var sm_based = [];

        console.log("Entrato in widgetTracker: " + widgetName);

        //Definizioni di funzione specifiche del widget
        function personalPoller(trackIndex) {
            var localMoving = false, last, lastSample, demiLastSample, lat1, lat2, lon1, lon2, moving, now, elapsedTime,
                elapsedTimeSeconds, elapsedTimeHours, lastSpeed = null;
            last = 1;

            if (sm_based[trackIndex] === "MyKPI") {
                if (rowParameters[trackIndex].includes("datamanager/api/v1/poidata/")) {
                    rowParameters[trackIndex] = rowParameters[trackIndex].split("datamanager/api/v1/poidata/")[1];
                }
                ajaxData = {
                    "myKpiId": rowParameters[trackIndex],
                  //  "lastValue":1
                    //  "timeRange": myKPITimeRange
                };
                urlAjaxCall = "../controllers/myKpiProxy.php";
            } else {
                urlAjaxCall = "../controllers/myPersonalDataProxy.php?variableName=" + encodeURI(trackSrcList[trackIndex].variableName) + "&last=" + last + "&motivation=" + encodeURI(trackSrcList[trackIndex].motivation);
            }

            //    getMyPersonalDataUrl = "../controllers/myPersonalDataProxy.php?variableName=" + encodeURI(trackSrcList[trackIndex].variableName) + "&last=" + last + "&motivation=" + encodeURI(trackSrcList[trackIndex].motivation);

            $.ajax({
                url: urlAjaxCall,
                type: "GET",
                //    data: {},
                data: ajaxData,
                async: false,
                dataType: 'json',
                success: function (newPosition) {

                    if (newPosition[0].APPID != undefined) {  // Se è definito questo parametro allora è un My Personal Data e quindi filtriamo per APPName

                        lastSample = newPosition[0];

                        /*    for (index = 0; index < tripsDataGlobal[trackIndex].length; index++) {
                                if (newPosition[index].APPName != appName) {
                                    tripsData.splice(index, 1);
                                    index--;
                                }
                            }   */

                        if (typeof newPosition !== 'undefined' && newPosition.length > 0) {
                            lastPosMarkers[trackIndex].setLatLng(JSON.parse(newPosition[0].variableValue));
                        }

                    } else {    // Altrimenti è un MyKPI o MyData allora rimappiamo alcune proprietà per compatibilità

                        newPosition = newPosition.reverse();
                        lastSample = newPosition[tripsDataGlobal[trackIndex].length - 1];

                        for (let index = 0; index < newPosition.length; index++) {
                            if (newPosition[index].latitude !== undefined && newPosition[index].longitude !== undefined) {
                                if (newPosition[index].latitude !== undefined && newPosition[index].longitude !== undefined) {
                                    newPosition[index].variableValue = "[" + newPosition[index].latitude + "," + newPosition[index].longitude + "]";
                                    //    tripsData[index].variableName = trackSrcList[trackSrcIndex-1].variableName;
                                    newPosition[index].variableName = trackSrcList[trackIndex].variableName;
                                    lastSample = newPosition[index];
                                    lastPosMarkers[trackIndex].setLatLng(JSON.parse(newPosition[index].variableValue));
                                    break;
                                } else {
                                    newPosition.splice(index, 1);
                                    index--;
                                }
                            }
                        }
                    }

                    if (mapFollowers[trackIndex] === true) {
                        mapRef.flyTo(JSON.parse(lastSample.variableValue), 17);
                    }

                    if (lastTrips[trackIndex] !== null) {
                        console.log("Ultimo viaggio in corso");
                        demiLastSample = lastTrips[trackIndex][lastTrips[trackIndex].length - 1];
                        lat1 = JSON.parse(demiLastSample.variableValue)[0];
                        lat2 = JSON.parse(lastSample.variableValue)[0];
                        lon1 = JSON.parse(demiLastSample.variableValue)[1];
                        lon2 = JSON.parse(lastSample.variableValue)[1];

                        localMoving = false;

                        //Fermo se non manda dati da più di 4 min
                        now = new Date();
                        if (Math.abs(lastSample.dataTime - now.getTime()) > 240000)
                        //   if(Math.abs(lastSample.dataTime - now.getTime()) > 31540000000)
                        {
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;N\/A');
                        } else {
                            var R = 6371; // Raggio della terra in km
                            var dLat = deg2rad(lat2 - lat1);
                            var dLon = deg2rad(lon2 - lon1);
                            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
                            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                            var d = R * c; // Distanza tra i due punti in km

                            //Tempo trascorso in millisecondi
                            elapsedTime = Math.abs(lastSample.dataTime - demiLastSample.dataTime);
                            elapsedTimeSeconds = parseFloat(elapsedTime / 1000);
                            elapsedTimeHours = parseFloat(elapsedTimeSeconds / 3600);

                            //Ultima velocità in chilometri orari
                            lastSpeed = parseFloat(d / elapsedTimeHours);

                            console.log("Elapsed time hours: " + elapsedTimeHours + " - Last speed: " + lastSpeed + " - Last distance: " + d);

                            //Se si è spostato più di dieci metri fra gli ultimi due campioni e a più di 1 km/h consideriamolo in movimento
                            //    if((d > 0.01)&&(lastSpeed > 1))
                            //    {
                            localMoving = true;
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;moving');
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;' + lastSpeed.toFixed(2) + ' Km/h');
                            /*   }
                               else
                               {
                                   $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;0 Km/h');
                            }*/

                            lastTrips[trackIndex].push(lastSample);
                            currentRoutes[trackIndex].addLatLng(JSON.parse(lastSample.variableValue));
                        }
                    } else {
                        //Per il primo campione in assoluto l'algoritmo è diverso
                        console.log("Primo campione nuovo viaggio");

                        localMoving = false;

                        //Fermo se non manda dati da più di 4 min
                        now = new Date();
                        if (Math.abs(lastSample.dataTime - now.getTime()) > 240000)
                        //    if(Math.abs(lastSample.dataTime - now.getTime()) > 31540000000)
                        {
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;N\/A');
                        } else {
                            localMoving = true;
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;first sample');
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;N\/A');

                            lastTrips[trackIndex] = [];
                            lastTrips[trackIndex].push(lastSample);
                            currentRoutes[trackIndex].addLatLng(JSON.parse(lastSample.variableValue));
                        }
                    }

                    if (localMoving) {
                        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsColorBtn[data-variableName="' + lastSample.variableName + '"]').attr('data-moving', true);
                    } else {
                        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsColorBtn[data-variableName="' + lastSample.variableName + '"]').attr('data-moving', false);
                    }

                    var lastSampleDate = new Date(lastSample.dataTime);
                    lastSampleDate = lastSampleDate.getDate() + "\/" + parseInt(lastSampleDate.getMonth() + 1) + "\/" + lastSampleDate.getFullYear() + " " + lastSampleDate.getHours() + ":" + lastSampleDate.getMinutes() + ":" + lastSampleDate.getSeconds();

                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.lastSampleCnt').html('&nbsp;' + lastSampleDate);

                },
                error: function (errorData) {
                    console.log("Error in data retrieval");
                    console.log(JSON.stringify(errorData));
                    var stopFlag = 1;
                }
            });
        }

        function LightenDarkenColor(color, percent) {

            var num = parseInt(color,16),
                amt = Math.round(2.55 * percent),
                R 	= (num >> 16) + amt,
                B 	= (num >> 8 & 0x00FF) + amt,
                G 	= (num & 0x0000FF) + amt;

            return (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (B<255?B<1?0:B:255)*0x100 + (G<255?G<1?0:G:255)).toString(16).slice(1);

        }

        function prepareCustomMarker(trxId, id, marker, dataObj, color1, color2)
        {
            var latLngId = dataObj.latitude + "" + dataObj.longitude + "_" + trxId + "" + id;
            latLngId = latLngId.replace(".", "");
            latLngId = latLngId.replace(".", "");//Incomprensibile il motivo ma con l'espressione regolare /./g non funziona

            var popupText = '<h3 class="recreativeEventMapTitle" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">' + dataObj.variableName + '</h3>';
            popupText += '<div class="recreativeEventMapBtnContainer"><button data-id="' + latLngId + '" class="recreativeEventMapDetailsBtn recreativeEventMapBtn recreativeEventMapBtnActive" type="button" style="background: ' + color1 + '; background: -webkit-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -o-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: -moz-linear-gradient(right, ' + color1 + ', ' + color2 + '); background: linear-gradient(to right, ' + color1 + ', ' + color2 + ');">Details</button></div>';

            popupText += '<div class="recreativeEventMapDataContainer recreativeEventMapDetailsContainer" style="height:100px">';

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
            popupText += '<tr><td style="text-align:left; font-size: 12px;">Metric Name:</td><td style="font-size: 12px;">' + dataObj.motivation + '</td></tr>';
            popupText += '<tr><td style="text-align:left; font-size: 12px;">Value:</td><td style="font-size: 12px;">' + dataObj.value + '</td></tr>';
            popupText += '<tr><td style="text-align:left; font-size: 12px;">Coordinates:</td><td style="font-size: 12px;">' + dataObj.latitude + ', ' + dataObj.longitude + '</td></tr>';

            return popupText;
        }


        function buildPastTrips(trackIndex, sm_based, rowParameters, wizardRows, loadingDiv, loadOkText, loadKoText)
        {
            var lat1, lat2, lon1, lon2, moving, lastSample, demiLastSample, now, elapsedTime, elapsedTimeSeconds, elapsedTimeHours, lastSpeed = null;
            var tripsUrl = "../controllers/myPersonalDataProxy.php?variableName=" + encodeURI(trackSrcList[trackIndex].variableName) + "&motivation=" + encodeURI(trackSrcList[trackIndex].motivation);
            appName = trackSrcList[trackIndex].appName;
            var ajaxData = {};
        //    if (wizardRows[Object.keys(wizardRows)[0]].high_level_type === "MyKPI") {
            if (dataType[trackIndex] === "MyKPI") {
                if (rowParameters.includes("datamanager/api/v1/poidata/")) {
                    rowParameters = rowParameters.split("datamanager/api/v1/poidata/")[1];
                }
                ajaxData = {
                    "myKpiId": rowParameters,
                    "action": "getDistinctDays"
                    //  "timeRange": myKPITimeRange
                };
            //    actionData = "getDistinctDays";
                tripsUrl = "../controllers/myKpiProxy.php";
            }
            
            $.ajax({
                url: tripsUrl,
                type: "GET",
                data: ajaxData,
                async: false,
                dataType: 'json',
                success: function (tripsDays)
                {
                    tripsDaysGlobal[trackSrcIndex] = tripsDays.reverse();
                    var myKPITimeRange = "&from=" + tripsDaysGlobal[trackSrcIndex][currentDay[trackSrcIndex]]+"T00:00" + "&to=" + tripsDaysGlobal[trackSrcIndex][currentDay[trackSrcIndex]]+"T23:59";

                    if (wizardRows[Object.keys(wizardRows)[0]].high_level_type === "MyKPI") {
                        if (rowParameters.includes("datamanager/api/v1/poidata/")) {
                            rowParameters = rowParameters.split("datamanager/api/v1/poidata/")[1];
                        }
                        ajaxInnerData = {
                            "myKpiId": rowParameters,
                          //  "action": "getDistinctDays"
                            "timeRange": myKPITimeRange
                        };
                        //    actionData = "getDistinctDays";
                        tripsUrl = "../controllers/myKpiProxy.php";
                    }

                    $.ajax({
                        url: tripsUrl,
                        type: "GET",
                        data: ajaxInnerData,
                        async: false,
                        dataType: 'json',
                        success: function (tripsData) {
                            var stopFlag = 1;

                            if (tripsData[0].APPID != undefined) {  // Se è definito questo parametro allora è un My Personal Data e quindi filtriamo per APPName
                                for (index = 0; index < tripsData.length; index++) {
                                    if (tripsData[index].APPName != appName) {
                                        tripsData.splice(index, 1);
                                        index--;
                                    }
                                }
                            } else {    // Altrimenti è un MyKPI o MyData allora rimappiamo alcune proprietà per compatibilità
                                for (index = 0; index < tripsData.length; index++) {
                                    if (tripsData[index].latitude !== undefined && tripsData[index].longitude !== undefined) {
                                        tripsData[index].variableValue = "[" + tripsData[index].latitude + "," + tripsData[index].longitude + "]";
                                    //    tripsData[index].variableName = trackSrcList[trackSrcIndex-1].variableName;
                                        tripsData[index].variableName = trackSrcList[trackSrcIndex].variableName;
                                        tripsData[index].motivation = trackSrcList[trackSrcIndex].motivation;
                                    } else {
                                        tripsData.splice(index, 1);
                                        index--;
                                    }
                                }
                            }

                            //Ordiniamento dei campioni per timestamp
                            tripsData.sort(compareNavSamples);

                            //Costruzione dei viaggi
                            mapMarkers[trackIndex] = [];
                            pastTrips[trackIndex] = [];
                            var newTrip = [];
                            pastTrips[trackIndex].push(newTrip);
                            for(var i = 0; i < tripsData.length; i++)
                            {
                                if(i < (tripsData.length - 1))
                                {
                                    //Aggiunta campione a viaggio corrente
                                    newTrip.push(tripsData[i]);
                                    if(Math.abs(tripsData[i].dataTime - tripsData[i+1].dataTime) > 120000)
                                    {
                                        //Chiusura viaggio corrente e apertura nuovo viaggio
                                        newTrip = [];
                                        pastTrips[trackIndex].push(newTrip);
                                    }

                                    var icon = L.divIcon({
                                        className: 'trackerPositionMarkerSecondary',
                                    //    iconSize: [16, 16],
                                    //    iconAnchor: [6, 8],
                                    //    html:'<div style="color: ' + defaultColors[trackIndex%7] + '"><i class="fa fa-circle" style="font-size:0.8em"></i></div>'
                                        html:'<span style="text-align: center; float: left; width: 10px; height: 10px; border: 1px solid #000000; border-radius: 100%; background-color: ' + defaultColors[trackIndex%7] + '; color: white;"></span>'
                                    });

                                //    var popupData = [];

                                    var color2 = "#" + LightenDarkenColor(defaultColors[trackIndex % 7].replace('#',''), 25);
                                    var customPopup = prepareCustomMarker(trackIndex, i, mapMarkers[trackIndex][i], tripsData[i], defaultColors[trackIndex%7], color2);
                                //    mapMarkers[trackIndex].push(L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}));
                                    mapMarkers[trackIndex][i] = L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}).bindPopup(customPopup);

                                }
                                else
                                {
                                    //Ultimo campione: può essere un viaggio standalone o essere aggiunto all'ultimo viaggio
                                    if(Math.abs(tripsData[i].dataTime - tripsData[i-1].dataTime) < 120000)
                                    {
                                        //Aggiunta campione a viaggio corrente
                                        newTrip.push(tripsData[i]);
                                    }
                                    else
                                    {
                                        newTrip = [];
                                        pastTrips[trackIndex].push(newTrip);
                                        newTrip.push(tripsData[i]);
                                    }

                                    //Il marker su mappa dell'ultima posizione lo mostriamo sempre
                                    var icon = L.divIcon({
                                        className: 'trackerPositionMarker',
                                        iconSize: [32, 50],
                                        iconAnchor: [16, 50],
                                        html:'<div style="color: ' + defaultColors[trackIndex%7] + '"><i class="fa fa-map-marker" style="text-shadow: black 2px 2px 2px";></i></div>'
                                    });

                                    var color2 = "#" + LightenDarkenColor(defaultColors[trackIndex % 7].replace('#',''), 25);
                                    var customPopup = prepareCustomMarker(trackIndex, i, mapMarkers[trackIndex][i], tripsData[i], defaultColors[trackIndex%7], color2);
                                    lastPosMarkers[trackIndex] = L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}).bindPopup(customPopup);
                                    mapMarkers[trackIndex][i] = L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}).bindPopup(customPopup);
                                 //   mapRef.addLayer(lastPosMarkers[trackIndex]);
                                    var stopFlag = 1;
                                //    lastPosMarkers[trackIndex].addTo(mapRef);
                                }

                                // SHOW ALL MARKERS
                            /*    var icon = L.divIcon({
                                    className: 'trackerPositionMarker',
                                    iconSize: [32, 50],
                                    iconAnchor: [16, 50],
                                    html:'<div style="color: ' + defaultColors[trackIndex%7] + '"><i class="fa fa-map-marker"></i></div>'
                                }); */

                                // mapMarkers[trackIndex].push(L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}));
                            //    lastPosMarkers[trackIndex] = L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon});
                                if (viewFlag[trackIndex] === true) {
                                    mapRef.addLayer(mapMarkers[trackIndex][i]);
                                    oms.addMarker(mapMarkers[trackIndex][i]);
                                }
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

                            // DRAW the TRAJECTORY for CURRENT KPI/METRIC
                            trajectory[trackIndex] = [];
                            for(i in mapMarkers[trackIndex]) {
                                var x = mapMarkers[trackIndex][i]._latlng.lat;
                                var y = mapMarkers[trackIndex][i]._latlng.lng;
                                trajectory[trackIndex].push([x, y]);
                            }

                         //   var colorTrajectory = LightenDarkenColor(defaultColors[trackIndex % 7], -20);
                            trajectoryGroup[trackIndex] = [];
                            if (viewFlag[trackIndex] === true) {
                                trajectoryGroup[trackIndex] = L.polyline(trajectory[trackIndex], {
                                    color: defaultColors[trackIndex % 7],
                                    weight: 3,
                                    opacity: 0.8,
                                    smoothFactor: 1
                                }).addTo(mapRef);
                            }

                            //Condizione iniziale: fermo o in movimento
                            lastSample = tripsData[tripsData.length - 1];
                            demiLastSample = tripsData[tripsData.length - 2];
                            tripsDataGlobal[trackIndex] = tripsData;

                            var lastSampleDate = new Date(lastSample.dataTime);
                            lastSampleDate = lastSampleDate.getDate() + "\/" + parseInt(lastSampleDate.getMonth() + 1) + "\/" + lastSampleDate.getFullYear() + " " + lastSampleDate.getHours() + ":" + lastSampleDate.getMinutes() + ":" + lastSampleDate.getSeconds();

                            var dateString = "";
                            if (wizardRows[Object.keys(wizardRows)[0]].high_level_type === "MyKPI") {
                                dateString = tripsDaysGlobal[trackIndex][currentDay[trackIndex]];
                            } else {

                            }
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.trackDateShow').html('&nbsp;' + dateString);

                            lat1 = JSON.parse(demiLastSample.variableValue)[0];
                            lat2 = JSON.parse(lastSample.variableValue)[0];
                            lon1 = JSON.parse(demiLastSample.variableValue)[1];
                            lon2 = JSON.parse(lastSample.variableValue)[1];

                            var lastSampleDate = new Date(lastSample.dataTime);
                            lastSampleDate = lastSampleDate.getDate() + "\/" + parseInt(lastSampleDate.getMonth() + 1) + "\/" + lastSampleDate.getFullYear() + " " + lastSampleDate.getHours() + ":" + lastSampleDate.getMinutes() + ":" + lastSampleDate.getSeconds();

                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.lastSampleCnt').html('&nbsp;' + lastSampleDate);

                            moving = false;

                            //Fermo se non manda dati da più di 4 min
                            now = new Date();
                            if(Math.abs(lastSample.dataTime - now.getTime()) > 240000)
                        //    if(Math.abs(lastSample.dataTime - now.getTime()) > 31540000000)
                            {
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;N\/A');
                            }
                            else
                            {
                                var R = 6371; // Raggio della terra in km
                                var dLat = deg2rad(lat2-lat1);
                                var dLon = deg2rad(lon2-lon1);
                                var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2);
                                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                                var d = R * c; // Distanza tra i due punti in km

                                //Tempo trascorso in millisecondi
                                elapsedTime = Math.abs(lastSample.dataTime - demiLastSample.dataTime);
                                elapsedTimeSeconds = parseFloat(elapsedTime / 1000);
                                elapsedTimeHours = parseFloat(elapsedTimeSeconds / 3600);

                                //Ultima velocità in chilometri orari
                                lastSpeed = parseFloat(d / elapsedTimeHours);

                                //Se si è spostato più di dieci metri fra gli ultimi due campioni e a più di 1 km/h consideriamolo in movimento
                            //    if((d > 0.01)&&(lastSpeed > 1))
                            //    {
                                    moving = true;
                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;moving');
                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;' + lastSpeed.toFixed(2) + ' Km/h');
                            //    }
                            //    else
                            //    {
                            //        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                            //        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;0 Km/h');

                            }

                            //Se ultimo viaggio in corso lo mostriamo su mappa
                            if(moving)
                            {
                                lastTrips[trackIndex] = pastTrips[trackIndex][pastTrips[trackIndex].length-1];
                                for(var j = 0; j < lastTrips[trackIndex].length; j++)
                                {
                                    currentRoutes[trackIndex].addLatLng(JSON.parse(lastTrips[trackIndex][j].variableValue));
                                }
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsColorBtn[data-variableName="' + lastSample.variableName + '"]').attr('data-moving', true);
                            }
                            else
                            {
                                lastTrips[trackIndex] = null;
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsColorBtn[data-variableName="' + lastSample.variableName + '"]').attr('data-moving', false);
                            }
                            //animateColorCnt(trackIndex);
                        },
                        error: function (errorData) {
                            var stopFlag = 1;
                        },
                    });

                },
                error: function(errorData)
                {
                    var stopFlag = 1;
                }
            });
        }
        
        function deg2rad(deg) 
        {
            return deg * (Math.PI/180);
        }
        
        function compareNavSamples(a, b)
        {
            if(a.dataTime < b.dataTime)
            {
                return -1;
            }
                
            if(a.dataTime > b.dataTime)
            {
                return 1;
            }
            
            return 0;
        }
        
        /*function animateColorCnt(index)
        {
            var colorCnt = $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsColorBtn').eq(index);
            if(colorCnt.attr('data-moving') === 'true')
            {
                console.log("Animazione: " + index);
                colorCnt.animate({opacity:'+=1'}, 1000);
                colorCnt.animate({opacity:'-=0.5'}, 1000, animateColorCnt(index));
            }
            else
            {
                console.log("Fisso" + index);
                animateColorCnt(index)
            }
        }*/
        
        function resizeWidget()
	    {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
            
            optionsMenuLeft = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').width() - $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').width() - 5;
            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').css('left', optionsMenuLeft);
            /*$('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value').textfill({
                maxFontPixels: -20
            });

            if(fontSize < parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', '')))
            {
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").css('font-size', fontSize + 'px');
            }
            else
            {
                $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span").css('font-size', parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', ''))*0.8);
            }

            $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_udm").css('font-size', parseInt($('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_value span').css('font-size').replace('px', ''))*0.45);*/
	    }
        //Fine definizioni di funzione

        $.ajax({
                url: "../controllers/getWidgetParams.php",
                type: "GET",
                data: {
                    widgetName: "<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>"
                },
                async: true,
                dataType: 'json',
                success: function(widgetData) 
                {
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
                //    sm_based = widgetData.params.sm_based;
                //    rowParameters = widgetData.params.rowParameters;
                    sm_field = widgetData.params.sm_field;
                    trackSrcList = JSON.parse(widgetData.params.parameters);
                    wizardRows = JSON.parse(widgetData.params.wizardRowIds);
                    for (n = 0; n < Object.keys(wizardRows).length; n++) {
                        rowParameters.push(wizardRows[Object.keys(wizardRows)[n]].parameters);
                     //   sm_based.push(wizardRows[Object.keys(wizardRows)[n]].sm_based);
                        sm_based.push(wizardRows[Object.keys(wizardRows)[n]].high_level_type);
                        dataType.push(wizardRows[Object.keys(wizardRows)[n]].high_level_type);
                    }
                //    dataType = wizardRows[Object.keys(wizardRows)[0]].high_level_type;
                    
                    if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
                    {
                        showHeader = false;
                    }
                    else
                    {
                        showHeader = true;
                    } 

                    if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
                    {
                        metricName = "<?= $_REQUEST['id_metric'] ?>";
                        widgetTitle = widgetData.params.title_w;
                        widgetHeaderColor = widgetData.params.frame_color_w;
                        widgetHeaderFontColor = widgetData.params.headerFontColor;
                        udm = widgetData.params.udm;
                        udmPos = widgetData.params.udmPos;
                        sizeRowsWidget = parseInt(widgetData.params.size_rows);
                        styleParameters = JSON.parse(widgetData.params.styleParameters);
                        widgetParameters = JSON.parse(widgetData.params.parameters);
                    }
                    else
                    {
                        metricName = metricNameFromDriver;
                        widgetTitleFromDriver.replace(/_/g, " ");
                        widgetTitleFromDriver.replace(/\'/g, "&apos;");
                        widgetTitle = widgetTitleFromDriver;
                        $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
                        widgetHeaderColor = widgetHeaderColorFromDriver;
                        widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
                    }
                    
                    setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').off('resizeWidgets');
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

                    optionsMenuLeft = $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div').width() - $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').width() - 5;
                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').css('left', optionsMenuLeft);

                    if(firstLoad === false)
                    {
                        showWidgetContent(widgetName);
                    }
                    else
                    {
                        setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
                    }
                    
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_infoButtonDiv a.info_source').show();
                     
                    //Creazione mappa
                    $("#" + widgetName + "_loading").css("display", "none");
                    $("#" + widgetName + "_content").css("display", "block");
                    
                    mapRef = L.map("<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer");
                    
                    mapRef.setView([43.774199, 11.259099], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                       attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                       maxZoom: 18,
                       closePopupOnClick: false
                    }).addTo(mapRef);
                    mapRef.attributionControl.setPrefix('');

                    oms = new OverlappingMarkerSpiderfier(mapRef, {keepSpiderfied : true});

                    for (trackSrcIndex = 0; trackSrcIndex < trackSrcList.length; trackSrcIndex++) {
                        switch (sm_based[trackSrcIndex]) {
                            case 'yes':
                                /*$.ajax({
                                    url: rowParameters,
                                    type: "GET",
                                    data: {},
                                    async: true,
                                    dataType: 'json',
                                    success: function (data)
                                    {
                                        var originalMetricType = data.Service.features[0].properties.realtimeAttributes[sm_field].data_type;
                                        udm = data.Service.features[0].properties.realtimeAttributes[sm_field].value_unit;

                                        metricData = {
                                            data:[
                                               {
                                                  commit:{
                                                     author:{
                                                        IdMetric_data: sm_field,
                                                        computationDate: null,
                                                        value_num:null,
                                                        value_perc1: null,
                                                        value_perc2: null,
                                                        value_perc3: null,
                                                        value_text: null,
                                                        quant_perc1: null,
                                                        quant_perc2: null,
                                                        quant_perc3: null,
                                                        tot_perc1: null,
                                                        tot_perc2: null,
                                                        tot_perc3: null,
                                                        series: null,
                                                        descrip: sm_field,
                                                        metricType: null,
                                                        threshold:null,
                                                        thresholdEval:null,
                                                        field1Desc: null,
                                                        field2Desc: null,
                                                        field3Desc: null,
                                                        hasNegativeValues: "1"
                                                     }
                                                  }
                                               }
                                            ]
                                        };

                                        switch(originalMetricType)
                                        {
                                            case "float":
                                                metricData.data[0].commit.author.metricType = "Float";
                                                metricData.data[0].commit.author.value_num = parseFloat(data.realtime.results.bindings[0][sm_field].value);
                                                break;

                                            case "integer":
                                                metricData.data[0].commit.author.metricType = "Intero";
                                                metricData.data[0].commit.author.value_num = parseInt(data.realtime.results.bindings[0][sm_field].value);
                                                break;

                                            default:
                                                metricData.data[0].commit.author.metricType = "Testuale";
                                                metricData.data[0].commit.author.value_text = data.realtime.results.bindings[0][sm_field].value;
                                                break;
                                        }

                                        $("#" + widgetName + "_loading").css("display", "none");
                                        $("#" + widgetName + "_content").css("display", "block");
                                        populateWidget();
                                    },
                                    error: function(errorData)
                                    {
                                        metricData = null;
                                        console.log("Error in data retrieval");
                                        console.log(JSON.stringify(errorData));
                                        if(firstLoad !== false)
                                        {
                                           $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                       $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                }
                            });*/
                                break;

                            case 'no':
                                /*$.ajax({
                                    url: getMetricDataUrl,
                                    type: "GET",
                                    data: {"IdMisura": ["<?= $_REQUEST['id_metric'] ?>"]},
                                async: true,
                                dataType: 'json',
                                success: function (data) 
                                {
                                    metricData = data;
                                    $("#" + widgetName + "_loading").css("display", "none");
                                    $("#" + widgetName + "_content").css("display", "block");
                                    populateWidget();
                                },
                                error: function(errorData)
                                {
                                    metricData = null;
                                    console.log("Error in data retrieval");
                                    console.log(JSON.stringify(errorData));
                                    if(firstLoad !== false)
                                    {
                                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
                                       $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading").hide();
                                       $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
                                    }
                                }
                            });*/
                                break;

                            case 'MyKPI':
                            case 'MyData':
                            case 'My Personal Data':

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

                                    var loadingText = $('<p class="gisMapLoadingDivTextPar">adding <b>' + trackSrcList[trackSrcIndex].variableName.toLowerCase() + '</b> to map<br><i class="fa fa-circle-o-notch fa-spin" style="font-size: 30px"></i></p>');
                                    var loadOkText = $('<p class="gisMapLoadingDivTextPar"><b>' + trackSrcList[trackSrcIndex].variableName.toLowerCase() + '</b> added to map<br><i class="fa fa-check" style="font-size: 30px"></i></p>');
                                    var loadKoText = $('<p class="gisMapLoadingDivTextPar">error adding <b>' + trackSrcList[trackSrcIndex].variableName.toLowerCase() + '</b> to map<br><i class="fa fa-close" style="font-size: 30px"></i></p>');

                                    loadingDiv.css("background", defaultColors[trackSrcIndex]);
                                    loadingDiv.css("background", "-webkit-linear-gradient(left top, " + defaultColors[trackSrcIndex] + ", " + "#" + LightenDarkenColor(defaultColors[trackSrcIndex].replace('#',''), 25) + ")");
                                    loadingDiv.css("background", "-o-linear-gradient(bottom right, " + defaultColors[trackSrcIndex] + ", " + "#" + LightenDarkenColor(defaultColors[trackSrcIndex].replace('#',''), 25) + ")");
                                    loadingDiv.css("background", "-moz-linear-gradient(bottom right, " + defaultColors[trackSrcIndex] + ", " + "#" + LightenDarkenColor(defaultColors[trackSrcIndex].replace('#',''), 25) + ")");
                                    loadingDiv.css("background", "linear-gradient(to bottom right, " + defaultColors[trackSrcIndex] + ", " + "#" + LightenDarkenColor(defaultColors[trackSrcIndex].replace('#',''), 25) + ")");

                                    loadingDiv.show();

                                    loadingDiv.append(loadingText);
                                    loadingDiv.css("opacity", 1);

                                    var parHeight = loadingText.height();
                                    var parMarginTop = Math.floor((loadingDiv.height() - parHeight) / 2);
                                    loadingText.css("margin-top", parMarginTop + "px");

                            //    for (trackSrcIndex = 0; trackSrcIndex < trackSrcList.length; trackSrcIndex++) {
                                    //Map followers disattivati di default all'avvio
                                    mapFollowers[trackSrcIndex] = false;

                                    //Costruzione menu
                                    newControlRow[trackSrcIndex] = $('<div class="trackControlsRow"></div>');
                                    newControlColorBtn[trackSrcIndex] = $('<div class="trackControlsColorCnt centerWithFlex"><div class="trackControlsColorBtn" data-moving="false" data-variableName="' + trackSrcList[trackSrcIndex].variableName + '"></div></div>');
                                    newControlColorBtn[trackSrcIndex].find('.trackControlsColorBtn').css('background-color', defaultColors[trackSrcIndex % 7]);
                                    newControlVariableName[trackSrcIndex] = $('<div class="trackControlsVariableName">' + trackSrcList[trackSrcIndex].variableName + '</div>');
                                    //   newControlVariableName[trackSrcIndex] = $('<div class="trackControlsVariableName tooltip">' + trackSrcList[trackSrcIndex].variableName + '<span class="tooltiptext">PROVA POPUP</span></div>');
                                    newControlCaret[trackSrcIndex] = $('<div class="trackControlsCaret" data-variableName="' + trackSrcList[trackSrcIndex].variableName + '"><i class="fa fa-caret-down"></i></div>');

                                    newControlSubCnt[trackSrcIndex] = $('<div class="trackControlsSubcnt" data-variableName="' + trackSrcList[trackSrcIndex].variableName + '"></div>');

                                    newControlSubRow[trackSrcIndex] = $('<div class="trackControlsSubrow"></div>');
                                    newControlSubLbl[trackSrcIndex] = $('<div class="trackControlsSublbl">Status:</div>');
                                    newControlSubField[trackSrcIndex] = $('<div class="trackControlsSubfield statusCnt">&nbsp;N\/A</div>');
                                    newControlSubRow[trackSrcIndex].append(newControlSubLbl[trackSrcIndex]);
                                    newControlSubRow[trackSrcIndex].append(newControlSubField[trackSrcIndex]);
                                    newControlSubCnt[trackSrcIndex].append(newControlSubRow[trackSrcIndex]);

                                    newControlSubRow[trackSrcIndex] = $('<div class="trackControlsSubrow"></div>');
                                    newControlSubLbl[trackSrcIndex] = $('<div class="trackControlsSublbl">Speed:</div>');
                                    newControlSubField[trackSrcIndex] = $('<div class="trackControlsSubfield speedCnt">&nbsp;N\/A</div>');
                                    newControlSubRow[trackSrcIndex].append(newControlSubLbl[trackSrcIndex]);
                                    newControlSubRow[trackSrcIndex].append(newControlSubField[trackSrcIndex]);
                                    newControlSubCnt[trackSrcIndex].append(newControlSubRow[trackSrcIndex]);

                                    newControlSubRow[trackSrcIndex] = $('<div class="trackControlsSubrow"></div>');
                                    newControlSubLbl[trackSrcIndex] = $('<div class="trackControlsSublbl">Last sent:</div>');
                                    newControlSubField[trackSrcIndex] = $('<div class="trackControlsSubfield lastSampleCnt" style="font-size: 10px;">&nbsp;N\/A</div>');
                                    newControlSubRow[trackSrcIndex].append(newControlSubLbl[trackSrcIndex]);
                                    newControlSubRow[trackSrcIndex].append(newControlSubField[trackSrcIndex]);
                                    newControlSubCnt[trackSrcIndex].append(newControlSubRow[trackSrcIndex]);

                                    newControlSubRow[trackSrcIndex] = $('<div class="trackControlsSubrow"></div>');
                                    dateDiv[trackSrcIndex] = $('<div class="trackControlsSublbl">Track Date:&nbsp;</div>');
                                    newControlDateSubField[trackSrcIndex] = $('<div class="trackControlsDateSubfield trackDateShow">&nbsp;N\/A</div>');
                                    newControlSubRow[trackSrcIndex].append(dateDiv[trackSrcIndex]);
                                    newControlSubRow[trackSrcIndex].append(newControlDateSubField[trackSrcIndex]);
                                    newControlSubCnt[trackSrcIndex].append(newControlSubRow[trackSrcIndex]);

                                    newControlSubRow[trackSrcIndex] = $('<div class="trackControlsSubrow trackControlsBtnRow row"></div>');
                                    newControlSubCnt[trackSrcIndex].append(newControlSubRow[trackSrcIndex]);

                                    showControl[trackSrcIndex] = $('<div id="showControl"' + trackSrcIndex + '"  class="trackControlsBtnCnt col-xs-4 trackControlShow" data-active="true" data-hoverColor="' + defaultColors[trackSrcIndex % 7] + '" data-trackIndex="' + trackSrcIndex + '" style="color:' + defaultColors[trackSrcIndex % 7] + '"><i class="fa fa-eye"></i></div>');
                                    newControlSubRow[trackSrcIndex].append(showControl[trackSrcIndex]);
                                    viewFlag[trackSrcIndex] = true;

                                    followControl[trackSrcIndex] = $('<div id="followControl"' + trackSrcIndex + '" class="trackControlsBtnCnt col-xs-4 trackControlFollow" data-hoverColor="' + defaultColors[trackSrcIndex % 7] + '" data-active="false" data-trackIndex="' + trackSrcIndex + '"><i class="fa fa-external-link"></i></div>');
                                    newControlSubRow[trackSrcIndex].append(followControl[trackSrcIndex]);

                                    timeTrendControl[trackSrcIndex] = $('<div id="timeTrendControl" class="trackControlsBtnCnt col-xs-4" data-targetWidgets="' + widgetParameters[trackSrcIndex].targets + '" data-active="false" data-trackIndex="' + trackSrcIndex + '" data-hoverColor="' + defaultColors[trackSrcIndex % 7] + '"><i class="fa fa-line-chart"></i></div>');
                                    newControlSubRow[trackSrcIndex].append(timeTrendControl[trackSrcIndex]);
                               //     newControlSubRow[trackSrcIndex].append(newControlSubLbl[trackSrcIndex]);

                                    newControlSubRow[trackSrcIndex] = $('<div class="trackControlsSubrow trackInfoRow row"></div>');
                                    newControlSubCnt[trackSrcIndex].append(newControlSubRow[trackSrcIndex]);

                                    newControlSubRow[trackSrcIndex] = $('<div id="trackNavigation' + trackSrcIndex + '" class="trackControlsSubrow trackNavigateBtnRow row"></div>');
                                    newControlSubCnt[trackSrcIndex].append(newControlSubRow[trackSrcIndex]);
                                    //   var prevBtn[trackSrcIndex] = $('<div class="trackControlsBtnCnt col-xs-4 trackControlShow" data-active="true" data-hoverColor="' + defaultColors[trackSrcIndex%7] + '" data-trackIndex="' + trackSrcIndex + '" style="color:' + defaultColors[trackSrcIndex%7] + '; float: left;"><i class="fa fa-arrow-left"></i>prev</div>');
                                    prevBtn[trackSrcIndex] = $('<div id="prevButton" class="trackControlsBtnCnt col-xs-4 trackControlShow" data-active="false" data-hoverColor="' + defaultColors[trackSrcIndex % 7] + '" data-trackIndex="' + trackSrcIndex + '" style="color:white; float: left; font-size: 12px; width: 50%; padding-right:0px"><i class="fa fa-arrow-circle-left"></i>&nbsp;prev day</div>');
                                    newControlSubRow[trackSrcIndex].append(prevBtn[trackSrcIndex]);
                                    nextBtn[trackSrcIndex] = $('<div id="nextButton" class="trackControlsBtnCnt col-xs-4 trackControlShow" data-active="false" data-hoverColor="' + defaultColors[trackSrcIndex % 7] + '" data-trackIndex="' + trackSrcIndex + '" style="color:white; float: right; font-size: 12px; width: 50%; padding-left:0px; visibility:hidden">next day&nbsp;<i class="fa fa-arrow-circle-right"></i></div>');
                                    newControlSubRow[trackSrcIndex].append(nextBtn[trackSrcIndex]);


                                    newControlRow[trackSrcIndex].append(newControlColorBtn[trackSrcIndex]);
                                    newControlRow[trackSrcIndex].append(newControlVariableName[trackSrcIndex]);
                                    newControlRow[trackSrcIndex].append(newControlCaret[trackSrcIndex]);
                                    //    newControlRow[trackSrcIndex].append(newVariableTitlePopup);

                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').append(newControlRow[trackSrcIndex]);
                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').append(newControlSubCnt[trackSrcIndex]);

                                    /*    newControlVariableName[trackSrcIndex].hover(function(){
                                            newVariableTitlePopup.show();
                                        });*/

                                    newControlCaret[trackSrcIndex].hover(function () {
                                        $(this).css('color', $(this).parents('div.trackControlsRow').find('div.trackControlsColorBtn').css('background-color'));
                                    }, function () {
                                        $(this).css('color', 'white');
                                    });

                                    newControlCaret[trackSrcIndex].click(function () {
                                        var variableName = $(this).attr('data-variableName');

                                        if ($('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + variableName + '"]').is(':visible')) {
                                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + variableName + '"]').hide();
                                            $(this).find('i').removeClass('fa-caret-up');
                                            $(this).find('i').addClass('fa-caret-down');
                                        } else {
                                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + variableName + '"]').show();
                                            $(this).find('i').removeClass('fa-caret-down');
                                            $(this).find('i').addClass('fa-caret-up');
                                        }
                                    });

                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').find('div.trackControlsBtnCnt').hover(function () {
                                        if ($(this).attr('data-active') === 'false') {
                                            $(this).css('color', $(this).attr('data-hoverColor'));
                                        }

                                    }, function () {
                                        if ($(this).attr('data-active') === 'false') {
                                            $(this).css('color', 'white');
                                        }
                                    });

                            //        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').find('#timeTrendControl').hover(function () {
                                    timeTrendControl[trackSrcIndex].hover(function (event) {

                                        if ($(this).attr('data-active') === 'false') {
                                            $(this).css('color', $(this).attr('data-hoverColor'));
                                        }
                                        var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                        var localTrackIndex = parseInt($(this).attr('data-trackIndex'));
                                        var colorHover1 = $(this).parents('.trackControlsBtnRow').children(0).attr('style').replace("color:", "").replace(";", "").trim();
                                        var title = $(this).parents('.trackControlsSubcnt').attr('data-variablename') + " - " + tripsDataGlobal[localTrackIndex][0].motivation + " On Day: " + tripsDaysGlobal[localTrackIndex][currentDay[localTrackIndex]];
                                     //   var colorHover2 = "#" + LightenDarkenColor(colorHover1.replace('#',''), 25);

                                        for(var i = 0; i < widgetTargetList.length; i++)
                                        {
                                            $.event.trigger({
                                                type: "mouseOverTimeTrendFromTracker_" + widgetTargetList[i],
                                                eventGenerator: $(this),
                                                targetWidget: widgetTargetList[i],
                                                value: $(this).attr("data-lastValue"),
                                                color1: colorHover1,
                                        //        color2: colorHover2,
                                                widgetTitle: title
                                            });
                                        }


                                    }, function(){

                                        if ($(this).attr('data-active') === 'false') {
                                            $(this).css('color', 'white');
                                        }

                                        var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                        for(var i = 0; i < widgetTargetList.length; i++)
                                        {
                                            $.event.trigger({
                                                type: "mouseOutTimeTrendFromTracker_" + widgetTargetList[i],
                                                eventGenerator: $(this),
                                                targetWidget: widgetTargetList[i],
                                                value: $(this).attr("data-lastValue"),
                                                color1: $(this).attr("data-color1"),
                                                color2: $(this).attr("data-color2")
                                            });
                                        }
                                     //   }
                                    });

                                    function numTripMarkers(trackIndex) {
                                        return tripsDataGlobal[trackIndex].length;
                                    }

                                    function numDays(trackIndex) {
                                        return tripsDaysGlobal[trackIndex].length;
                                    }

                                    function changeDay(page, trackIndex) {
                                        if (numDays(trackIndex) > 1) {
                                            if (page < 1) page = 1;
                                            if (page > numDays(trackIndex)) page = numDays(trackIndex);

                                            if (currentDay[trackIndex] == 0) {
                                                nextBtn[trackIndex][0].style.visibility = "hidden";
                                            } else {
                                                nextBtn[trackIndex][0].style.visibility = "visible";
                                            }

                                            if (currentDay[trackIndex] == numDays(trackIndex) - 1) {
                                                prevBtn[trackIndex][0].style.visibility = "hidden";
                                            } else {
                                                prevBtn[trackIndex][0].style.visibility = "visible";
                                            }
                                        }

                                        if (currentDay[trackIndex] < numDays(trackIndex)) {
                                            // Popolare con data e ora da object
                                            dayString = tripsDaysGlobal[trackIndex][currentDay[trackIndex]];
                                        }
                                    }

                                    function changeTripMarkerId(page, trackIndex) {
                                        if (numTripMarkers(trackIndex) > 1) {
                                            if (page < 1) page = 1;
                                            if (page > numTripMarkers(trackIndex)) page = numTripMarkers(trackIndex);

                                            if (currentTripMarkerId == 0) {
                                                nextBtn[trackIndex][0].style.visibility = "hidden";
                                            } else {
                                                nextBtn[trackIndex][0].style.visibility = "visible";
                                            }

                                            if (currentTripMarkerId == numTripMarkers(trackIndex) - 1) {
                                                prevBtn[trackIndex][0].style.visibility = "hidden";
                                            } else {
                                                prevBtn[trackIndex][0].style.visibility = "visible";
                                            }
                                        }

                                        if (currentTripMarkerId < numTripMarkers(trackIndex)) {
                                            // Popolare con data e ora da object
                                            dayString = "";
                                        }
                                    }

                                    function prevTripMarker(trackIndex) {
                                        if (currentTripMarkerId < numTripMarkers(trackIndex) - 1) {
                                            currentTripMarkerId++;
                                        }

                                        var icon = L.divIcon({
                                            className: 'trackerPositionMarker',
                                            iconSize: [32, 50],
                                            iconAnchor: [16, 50],
                                            html: '<div style="color: ' + defaultColors[trackIndex % 7] + '"><i class="fa fa-map-marker" style="text-shadow: black 2px 2px 2px";></i></div>'
                                        });

                                        if (viewFlag[trackIndex] === true) {
                                            mapRef.addLayer(mapMarkers[trackIndex][numTripMarkers(trackIndex) - 1 - currentTripMarkerId]);
                                            oms.addMarker(mapMarkers[trackIndex][numTripMarkers(trackIndex) - 1 - currentTripMarkerId]);
                                        }

                                        changeTripMarkerId(currentTripMarkerId, trackIndex);
                                    }

                                    function nextTripMarker(trackIndex) {
                                        mapRef.removeLayer(mapMarkers[trackIndex][parseInt(numTripMarkers(trackIndex) - 1 - currentTripMarkerId)]);

                                        if (currentTripMarkerId > 0) {
                                            currentTripMarkerId--;
                                            changeTripMarkerId(currentTripMarkerId, trackIndex);
                                        }

                                        changeTripMarkerId(currentTripMarkerId, trackIndex);

                                    }


                                    function navigateTripDays(urlDayTrip, ajaxDayTripData, trackIndex, dataType, rowParameters) {
                                        $.ajax({
                                            url: urlDayTrip,
                                            type: "GET",
                                            data: ajaxDayTripData,
                                            async: true,
                                            dataType: 'json',
                                            success: function (tripsData) {
                                                if (tripsData[0].APPID != undefined) {  // Se è definito questo parametro allora è un My Personal Data e quindi filtriamo per APPName
                                                    for (index = 0; index < tripsData.length; index++) {
                                                        if (tripsData[index].APPName != appName) {
                                                            tripsData.splice(index, 1);
                                                            index--;
                                                        }
                                                    }
                                                } else {    // Altrimenti è un MyKPI o MyData allora rimappiamo alcune proprietà per compatibilità
                                                    for (index = 0; index < tripsData.length; index++) {
                                                        if (tripsData[index].latitude !== undefined && tripsData[index].longitude !== undefined) {
                                                            tripsData[index].variableValue = "[" + tripsData[index].latitude + "," + tripsData[index].longitude + "]";
                                                            //    tripsData[index].variableName = trackSrcList[trackSrcIndex-1].variableName;
                                                            tripsData[index].variableName = trackSrcList[trackIndex].variableName;
                                                            tripsData[index].motivation = trackSrcList[trackIndex].motivation;
                                                        } else {
                                                            tripsData.splice(index, 1);
                                                            index--;
                                                        }
                                                    }
                                                }

                                                //Ordiniamento dei campioni per timestamp
                                                tripsData.sort(compareNavSamples);

                                                //Costruzione dei viaggi
                                                mapMarkers[trackIndex] = [];
                                                pastTrips[trackIndex] = [];
                                                var newTrip = [];
                                                pastTrips[trackIndex].push(newTrip);
                                                for (var i = 0; i < tripsData.length; i++) {
                                                    if (i < (tripsData.length - 1)) {
                                                        //Aggiunta campione a viaggio corrente
                                                        newTrip.push(tripsData[i]);
                                                        if (Math.abs(tripsData[i].dataTime - tripsData[i + 1].dataTime) > 120000) {
                                                            //Chiusura viaggio corrente e apertura nuovo viaggio
                                                            newTrip = [];
                                                            pastTrips[trackIndex].push(newTrip);
                                                        }

                                                        var icon = L.divIcon({
                                                            className: 'trackerPositionMarkerSecondary',
                                                        //    iconSize: [16, 16],
                                                        //    iconAnchor: [6, 8],
                                                        //    html: '<div style="color: ' + defaultColors[trackIndex % 7] + '"><i class="fa fa-circle"></i></div>'
                                                            html:'<span style="text-align: center; float: left; width: 10px; height: 10px; border: 1px solid #000000; border-radius: 100%; background-color: ' + defaultColors[trackIndex%7] + '; color: white;"></span>'
                                                        });

                                                        var color2 = "#" + LightenDarkenColor(defaultColors[trackIndex % 7].replace('#',''), 25);
                                                        var customPopup = prepareCustomMarker(trackIndex, i, mapMarkers[trackIndex][i], tripsData[i], defaultColors[trackIndex % 7], color2);
                                                        //    mapMarkers[trackIndex].push(L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}));
                                                        mapMarkers[trackIndex][i] = L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}).bindPopup(customPopup);

                                                    } else {
                                                        //Ultimo campione: può essere un viaggio standalone o essere aggiunto all'ultimo viaggio
                                                        if (Math.abs(tripsData[i].dataTime - tripsData[i - 1].dataTime) < 120000) {
                                                            //Aggiunta campione a viaggio corrente
                                                            newTrip.push(tripsData[i]);
                                                        } else {
                                                            newTrip = [];
                                                            pastTrips[trackIndex].push(newTrip);
                                                            newTrip.push(tripsData[i]);
                                                        }

                                                        //Il marker su mappa dell'ultima posizione lo mostriamo sempre
                                                        var icon = L.divIcon({
                                                            className: 'trackerPositionMarker',
                                                            iconSize: [32, 50],
                                                            iconAnchor: [16, 50],
                                                            html: '<div style="color: ' + defaultColors[trackIndex % 7] + '"><i class="fa fa-map-marker" style="text-shadow: black 2px 2px 2px";></i></div>'
                                                         /*   html:   '<div id="altMapMarkerContainer">' +
                                                                        '<div id="innermapMarker" style="color: ' + defaultColors[trackIndex % 7] + '">' +
                                                                            '<i class="fa fa-map-marker mapMarkerStyle"></i>' +
                                                                        '</div>' +
                                                                        '<div id="outerMapMarker">' +
                                                                    //        '<img src="https://img.icons8.com/pastel-glyph/64/000000/place-marker.png" alt="">' +
                                                                            '<img src="../img/customIcons/marker-map-border.png">' +
                                                                        '</div>' +
                                                                    '</div>'    */
                                                        });

                                                        var color2 = "#" + LightenDarkenColor(defaultColors[trackIndex % 7].replace('#',''), 25);
                                                        var customPopup = prepareCustomMarker(trackIndex, i, mapMarkers[trackIndex][i], tripsData[i], defaultColors[trackIndex % 7], color2);
                                                        lastPosMarkers[trackIndex] = L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}).bindPopup(customPopup);
                                                        mapMarkers[trackIndex][i] = L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}).bindPopup(customPopup);
                                                        //   mapRef.addLayer(lastPosMarkers[trackIndex]);
                                                        var stopFlag = 1;
                                                        //    lastPosMarkers[trackIndex].addTo(mapRef);
                                                    }

                                                    // mapMarkers[trackIndex].push(L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon}));
                                                    //    lastPosMarkers[trackIndex] = L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon});
                                                    if (viewFlag[trackIndex] === true) {
                                                        mapRef.addLayer(mapMarkers[trackIndex][i]);
                                                        oms.addMarker(mapMarkers[trackIndex][i]);
                                                    }
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

                                                // DRAW the TRAJECTORY for CURRENT KPI/METRIC
                                                trajectory[trackIndex] = [];
                                                for (i in mapMarkers[trackIndex]) {
                                                    var x = mapMarkers[trackIndex][i]._latlng.lat;
                                                    var y = mapMarkers[trackIndex][i]._latlng.lng;
                                                    trajectory[trackIndex].push([x, y]);
                                                }

                                            //    var colorTrajectory = LightenDarkenColor(defaultColors[trackIndex % 7], -20);
                                                trajectoryGroup[trackIndex] = [];
                                                if (viewFlag[trackIndex] === true) {
                                                    trajectoryGroup[trackIndex] = L.polyline(trajectory[trackIndex], {
                                                        color: defaultColors[trackIndex % 7],
                                                        weight: 3,
                                                        opacity: 0.8,
                                                        smoothFactor: 1
                                                    }).addTo(mapRef);
                                                }

                                                var dateString = "";
                                                if (wizardRows[Object.keys(wizardRows)[0]].high_level_type === "MyKPI") {
                                                    dateString = tripsDaysGlobal[trackIndex][currentDay[trackIndex]];
                                                } else {

                                                }

                                                //Condizione iniziale: fermo o in movimento
                                                lastSample = tripsData[tripsData.length - 1];
                                                demiLastSample = tripsData[tripsData.length - 2];
                                                tripsDataGlobal[trackIndex] = tripsData;

                                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.trackDateShow').html('&nbsp;' + dateString);

                                                var lastSampleDate = new Date(lastSample.dataTime);
                                                lastSampleDate = lastSampleDate.getDate() + "\/" + parseInt(lastSampleDate.getMonth() + 1) + "\/" + lastSampleDate.getFullYear() + " " + lastSampleDate.getHours() + ":" + lastSampleDate.getMinutes() + ":" + lastSampleDate.getSeconds();

                                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.lastSampleCnt').html('&nbsp;' + lastSampleDate);

                                                lat1 = JSON.parse(demiLastSample.variableValue)[0];
                                                lat2 = JSON.parse(lastSample.variableValue)[0];
                                                lon1 = JSON.parse(demiLastSample.variableValue)[1];
                                                lon2 = JSON.parse(lastSample.variableValue)[1];

                                                moving = false;

                                                //Fermo se non manda dati da più di 4 min
                                                now = new Date();
                                                if (Math.abs(lastSample.dataTime - now.getTime()) > 240000)
                                                //    if(Math.abs(lastSample.dataTime - now.getTime()) > 31540000000)
                                                {
                                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;N\/A');
                                                } else {
                                                    var R = 6371; // Raggio della terra in km
                                                    var dLat = deg2rad(lat2 - lat1);
                                                    var dLon = deg2rad(lon2 - lon1);
                                                    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
                                                    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                                                    var d = R * c; // Distanza tra i due punti in km

                                                    //Tempo trascorso in millisecondi
                                                    elapsedTime = Math.abs(lastSample.dataTime - demiLastSample.dataTime);
                                                    elapsedTimeSeconds = parseFloat(elapsedTime / 1000);
                                                    elapsedTimeHours = parseFloat(elapsedTimeSeconds / 3600);

                                                    //Ultima velocità in chilometri orari
                                                    lastSpeed = parseFloat(d / elapsedTimeHours);

                                                    //Se si è spostato più di dieci metri fra gli ultimi due campioni e a più di 1 km/h consideriamolo in movimento
                                                    //    if((d > 0.01)&&(lastSpeed > 1))
                                                    //    {
                                                    moving = true;
                                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;moving');
                                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;' + lastSpeed.toFixed(2) + ' Km/h');
                                                    //    }
                                                    //    else
                                                    //    {
                                                    //        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                                                    //        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;0 Km/h');

                                                }

                                                //Se ultimo viaggio in corso lo mostriamo su mappa
                                                if (moving) {
                                                    lastTrips[trackIndex] = pastTrips[trackIndex][pastTrips[trackIndex].length - 1];
                                                    for (var j = 0; j < lastTrips[trackIndex].length; j++) {
                                                        currentRoutes[trackIndex].addLatLng(JSON.parse(lastTrips[trackIndex][j].variableValue));
                                                    }
                                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsColorBtn[data-variableName="' + lastSample.variableName + '"]').attr('data-moving', true);
                                                } else {
                                                    lastTrips[trackIndex] = null;
                                                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsColorBtn[data-variableName="' + lastSample.variableName + '"]').attr('data-moving', false);
                                                }
                                                //animateColorCnt(trackIndex);
                                            },
                                            error: function (errorData) {
                                                var stopFlag = 1;
                                            },
                                        });

                                    }

                                    function prevDayTrip(trackIndex, dataType, rowParameters) {
                                        var urlDayTrip = "";
                                        var ajaxDayTripData = {};

                                        for (k = 0; k < mapMarkers[trackIndex].length; k++) {
                                            mapRef.removeLayer(mapMarkers[trackIndex][k]);
                                            oms.removeMarker(mapMarkers[trackIndex][k]);
                                        }
                                        mapRef.removeLayer(trajectoryGroup[trackIndex]);

                                        if (currentDay[trackIndex] < numDays(trackIndex) - 1) {
                                            currentDay[trackIndex]++;
                                        }

                                        var myKPITimeRange = "&from=" + tripsDaysGlobal[trackIndex][currentDay[trackIndex]] + "T00:00" + "&to=" + tripsDaysGlobal[trackIndex][currentDay[trackIndex]] + "T23:59";
                                        //    if (wizardRows[Object.keys(wizardRows)[0]].high_level_type === "MyKPI") {
                                        if (dataType[trackIndex] === "MyKPI") {
                                            if (rowParameters[trackIndex].includes("datamanager/api/v1/poidata/")) {
                                                rowParameters[trackIndex] = rowParameters[trackIndex].split("datamanager/api/v1/poidata/")[1];
                                            }
                                            ajaxDayTripData = {
                                                "myKpiId": rowParameters[trackIndex],
                                                //  "action": "getDistinctDays"
                                                "timeRange": myKPITimeRange
                                            };
                                            //    actionData = "getDistinctDays";
                                            urlDayTrip = "../controllers/myKpiProxy.php";
                                        }

                                        navigateTripDays(urlDayTrip, ajaxDayTripData, trackIndex, dataType[trackIndex], rowParameters[trackIndex]);

                                        /*    var icon = L.divIcon({
                                                className: 'trackerPositionMarker',
                                                iconSize: [32, 50],
                                                iconAnchor: [16, 50],
                                                html:'<div style="color: ' + defaultColors[trackIndex%7] + '"><i class="fa fa-map-marker"></i></div>'
                                            });*/

                                        //   mapRef.addLayer(mapMarkers[trackIndex][numDays(trackIndex) - 1 - currentDay]);
                                        changeDay(currentDay[trackIndex], trackIndex);

                                        timeTrendControl[trackIndex].click();
                                    }

                                    function nextDayTrip(trackIndex, dataType, rowParameters) {
                                        var urlDayTrip = "";
                                        var ajaxDayTripData = {};

                                        for (k = 0; k < mapMarkers[trackIndex].length; k++) {
                                            mapRef.removeLayer(mapMarkers[trackIndex][k]);
                                            oms.removeMarker(mapMarkers[trackIndex][k]);
                                        }
                                        mapRef.removeLayer(trajectoryGroup[trackIndex]);

                                        if (currentDay[trackIndex] > 0) {
                                            currentDay[trackIndex]--;
                                            changeDay(currentDay[trackIndex], trackIndex);
                                        }

                                        var myKPITimeRange = "&from=" + tripsDaysGlobal[trackIndex][currentDay[trackIndex]] + "T00:00" + "&to=" + tripsDaysGlobal[trackIndex][currentDay[trackIndex]] + "T23:59";
                                        //    if (wizardRows[Object.keys(wizardRows)[0]].high_level_type === "MyKPI") {
                                        if (dataType[trackIndex] === "MyKPI") {
                                            if (rowParameters[trackIndex].includes("datamanager/api/v1/poidata/")) {
                                                rowParameters[trackIndex] = rowParameters[trackIndex].split("datamanager/api/v1/poidata/")[1];
                                            }
                                            ajaxDayTripData = {
                                                "myKpiId": rowParameters[trackIndex],
                                                //  "action": "getDistinctDays"
                                                "timeRange": myKPITimeRange
                                            };
                                            //    actionData = "getDistinctDays";
                                            urlDayTrip = "../controllers/myKpiProxy.php";
                                        }

                                        navigateTripDays(urlDayTrip, ajaxDayTripData, trackIndex, dataType, rowParameters[trackIndex]);

                                        /*    var icon = L.divIcon({
                                                className: 'trackerPositionMarker',
                                                iconSize: [32, 50],
                                                iconAnchor: [16, 50],
                                                html:'<div style="color: ' + defaultColors[trackIndex%7] + '"><i class="fa fa-map-marker"></i></div>'
                                            });*/

                                        //   mapRef.addLayer(mapMarkers[trackIndex][numDays(trackIndex) - 1 - currentDay[trackIndex]]);
                                        changeDay(currentDay[trackIndex], trackIndex);

                                        timeTrendControl[trackIndex].click();
                                    }

                                    prevBtn[trackSrcIndex].click(function () {
                                 //   $('#prevButton').click(function(){
                                        prevDayTrip($(this).attr("data-trackIndex"), dataType, rowParameters);
                                        //    prevTripMarker($(this).attr("data-trackIndex"));
                                    });

                                    nextBtn[trackSrcIndex].click(function () {
                                //    $('#nextButton').click(function(){
                                        nextDayTrip($(this).attr("data-trackIndex"), dataType, rowParameters);
                                        //nextTripMarker($(this).attr("data-trackIndex"));
                                    });

                                    //    var popup = new L.Popup();
                                    oms.addListener('click', function (marker) {
                                        marker.openPopup();
                                    });

                                    oms.addListener('spiderfy', function (markers) {
                                        mapRef.closePopup();
                                    });

                                    followControl[trackSrcIndex].click(function () {
                              //      $('#followControl').click(function () {
                                        for (var i = 0; i < mapFollowers.length; i++) {
                                            if (i !== parseInt($(this).attr('data-trackIndex'))) {
                                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').find('div.trackControlFollow').eq(i).attr('data-active', false);
                                                mapFollowers[$(this).attr("data-trackIndex")][i] = false;
                                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').find('div.trackControlFollow').eq(i).css('color', 'white');
                                            }
                                        }

                                        var localTrackIndex = parseInt($(this).attr('data-trackIndex'));

                                        if ($(this).attr('data-active') === 'false') {
                                            $(this).attr('data-active', true);
                                            mapFollowers[localTrackIndex] = true;
                                            $(this).css('color', $(this).attr('data-hoverColor'));
                                        } else {
                                            $(this).attr('data-active', false);
                                            mapFollowers[localTrackIndex] = false;
                                            $(this).css('color', 'white');
                                        }
                                    });

                                // GESTORE DEL CLICK PER TIME TREND
                                timeTrendControl[trackSrcIndex].click(function (event) {
                                  //  if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                      //  $(this).css("background-color", "#e6e6e6");
                                   //     $(this).off("hover");
                                      //  $(this).off("click");
                                  //  }
                                 //   else {
                                     /*   $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').css("background", $(this).attr("data-color2"));
                                        $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').css("font-weight", "normal");
                                        $(this).css("background", $(this).attr("data-color1"));
                                        $(this).css("font-weight", "bold");
                                        $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn').attr("data-timeTrendClicked", "false");
                                        $(this).attr("data-timeTrendClicked", "true");*/
                                        var localTrackIndex = parseInt($(this).attr('data-trackIndex'));
                                        var widgetTargetList = $(this).attr("data-targetWidgets").split(',');
                                    //    var colIndex = $(this).parent().index();
                                    //    var title = $(this).parents("tr").find("td").eq(0).html() + " - " + $(this).attr("data-range-shown");
                                        var title = $(this).parents('.trackControlsSubcnt').attr('data-variablename') + " - " + tripsDataGlobal[localTrackIndex][0].motivation;
                                    //    var lastUpdateTime = $(this).parents('div.recreativeEventMapContactsContainer').find('span.popupLastUpdate').html();

                                    //    var now = new Date();
                                    //    var lastUpdateDate = new Date(lastUpdateTime);
                                    //    var diff = parseFloat(Math.abs(now - lastUpdateDate) / 1000);
                                    //    var range = $(this).attr("data-range");
                                        var range = "1/DAY";
                                        var day = tripsDaysGlobal[localTrackIndex][currentDay[localTrackIndex]];
                                        var firstColor = ($(this).parents('.trackControlsBtnRow').children(0).attr('style')).replace('color:','').replace(";", "").trim();
                                        var secondColor = "#" + LightenDarkenColor(firstColor.replace('#',''), 25);
                                        var passedRowParams = "";
                                        if (wizardRows[Object.keys(wizardRows)[localTrackIndex]].parameters.includes("datamanager/api/v1/poidata/")) {
                                            passedRowParams = wizardRows[Object.keys(wizardRows)[localTrackIndex]].parameters.split("datamanager/api/v1/poidata/")[1];
                                        } else {
                                            passedRowParams = wizardRows[Object.keys(wizardRows)[localTrackIndex]].parameters;
                                        }

                                        for (var i = 0; i < widgetTargetList.length; i++) {
                                            $.event.trigger({
                                                type: "showTimeTrendFromTracker_" + widgetTargetList[i],
                                                eventGenerator: $(this),
                                                targetWidget: widgetTargetList[i],
                                                range: range,
                                                color1: firstColor,
                                                color2: secondColor,
                                                widgetTitle: title,
                                                field: $(this).attr("data-field"),
                                                rowParams: passedRowParams,
                                                serviceUri: $(this).attr("data-serviceUri"),
                                            //    marker: markersCache["" + $(this).attr("data-id") + ""],
                                                mapRef: mapRef,
                                                day: day,
                                                fake: false
                                                //fake: $(this).attr("data-fake")
                                            });
                                        }

                                    /*    $('#<?= $_REQUEST['name_w'] ?>_map button.timeTrendBtn[data-id="' + latLngId + '"]').each(function (i) {
                                            if (isNaN(parseFloat($(this).parents('tr').find('td').eq(1).html())) || ($(this).attr("data-disabled") === "true")) {
                                                $(this).css("background-color", "#e6e6e6");
                                                $(this).off("hover");
                                                $(this).off("click");
                                            }
                                        });*/
                                //    }
                                });

                                    showControl[trackSrcIndex].click(function () {
                             //       $('#showControl').click(function () {
                                        if ($(this).attr('data-active') === 'false') {
                                            $(this).attr('data-active', true);
                                            $(this).find('i.fa').removeClass('fa-eye-slash');
                                            $(this).find('i.fa').addClass('fa-eye');
                                            $(this).css('color', $(this).attr('data-hoverColor'));

                                            for (k = 0; k < mapMarkers[$(this).attr('data-trackIndex')].length; k++) {
                                                mapRef.addLayer(mapMarkers[$(this).attr('data-trackIndex')][k]);
                                                oms.addMarker(mapMarkers[$(this).attr('data-trackIndex')][k]);
                                            }
                                            mapRef.addLayer(trajectoryGroup[$(this).attr('data-trackIndex')]);

                                            //    mapRef.addLayer(lastPosMarkers[parseInt($(this).attr('data-trackIndex'))]);
                                            //    mapRef.addLayer(currentRoutes[parseInt($(this).attr('data-trackIndex'))]);
                                            viewFlag[$(this).attr('data-trackIndex')] = true;
                                            var element = "#trackNavigation" + $(this).attr('data-trackIndex');
                                            $(element).show();
                                        } else {
                                            $(this).attr('data-active', false);
                                            $(this).find('i.fa').removeClass('fa-eye');
                                            $(this).find('i.fa').addClass('fa-eye-slash');
                                            $(this).css('color', 'white');

                                            for (k = 0; k < mapMarkers[$(this).attr('data-trackIndex')].length; k++) {
                                                mapRef.removeLayer(mapMarkers[$(this).attr('data-trackIndex')][k]);
                                                oms.removeMarker(mapMarkers[$(this).attr('data-trackIndex')][k]);
                                            }
                                            mapRef.removeLayer(trajectoryGroup[$(this).attr('data-trackIndex')]);

                                            //   mapRef.removeLayer(lastPosMarkers[parseInt($(this).attr('data-trackIndex'))]);
                                            //   mapRef.removeLayer(currentRoutes[parseInt($(this).attr('data-trackIndex'))]);
                                            viewFlag[$(this).attr('data-trackIndex')] = false;
                                            var element = "#trackNavigation" + $(this).attr('data-trackIndex');
                                            $(element).hide();
                                            var element2 = "#showControl" + $(this).attr('data-trackIndex');
                                            $(element2).hide();
                                            var element3 = "#followControl" + $(this).attr('data-trackIndex');
                                            $(element3).hide();
                                            //   $('.trackNavigateBtnRow').hide();
                                        }
                                    });

                                    //Costruzione polylines
                                    currentRoutes[trackSrcIndex] = new L.Polyline([], {
                                        color: defaultColors[trackSrcIndex % 7],
                                        weight: 3,
                                        opacity: 0.8,
                                        smoothFactor: 1
                                    });

                                    mapRef.addLayer(currentRoutes[trackSrcIndex]);

                                    //Init Day 0 for track #trackSrcIndex
                                    currentDay[trackSrcIndex] = 0;

                                    //Costruzione viaggi passatitrackDateShow
                                    buildPastTrips(trackSrcIndex, sm_based[trackSrcIndex], rowParameters[trackSrcIndex], wizardRows, loadingDiv, loadOkText, loadKoText);

                                  /*  mapRef.on('zoomend', function() {
                                        var currentZoom = mapRef.getZoom();
                                        for (let n = 0; n < trackSrcList.length; n++ ) {
                                            if (viewFlag[n] === true) {
                                                for (let i = 0; i < tripsDataGlobal[n].length; i++) {
                                                    var circleSize = mapRef.getZoom * 2;
                                                    var zoomedIcon = L.divIcon({
                                                        className: 'trackerPositionMarker',
                                                        html:'<span style="text-align: center; float: left; width: ' + circleSize + 'px; height: ' + circleSize + 'px; border: 1px solid #000000; border-radius: 100%; background-color: ' + defaultColors[trackIndex%7] + '; color: white;"></span>'
                                                    });
                                                    mapMarkers[n][i].setIcon(zoomedIcon);
                                                }
                                                var zoomedIcon = L.divIcon({
                                                    className: 'trackerPositionMarker',
                                                    html:'<div style="color: ' + defaultColors[n] + '"><i class="fa fa-map-marker"></i></div>'
                                                });
                                                mapMarkers[n][tripsDataGlobal[n].length - 1].setIcon(zoomedIcon);
                                            }
                                        }
                                    }); */

                                    //Avvio poller  // PER ORA DISABILITATO
                                //    setInterval(personalPoller.bind(null, trackSrcIndex), 5000);
                            //    }
                                break;
                        }
                    }

                    // CARICARE WIDGET TIME TREND PARTIRE DA MODELLO WIDGET SCHEMA IN NOTEPAD++ DA PASSARE COME dashboardWidgets[i]

                /*    $("#gridsterUl").find("li#" + dashboardWidgets[i]['name_w']).load("../widgets/" + encodeURIComponent("widgetTimeTrend.php", dashboardWidgets[i], function () {
                        $(this).find(".icons-modify-widget").css("display", "inline");
                        $(this).find(".modifyWidgetGenContent").css("display", "block");
                        $(this).find(".pcCountdownContainer").css("display", "none");
                        $(this).find(".iconsModifyPcWidget").css("display", "flex");
                        $(this).find(".iconsModifyPcWidget").css("align-items", "center");
                        $(this).find(".iconsModifyPcWidget").css("justify-content", "flex-end");
                    }); */
                    
                },
                error: function(errorData)
                {
                    var stopFlag = 1;
                }
        });
        
        
        //Web socket 
        /*openWs = function(e)
        {
            console.log("Widget " + widgetTitle + " is trying to open WebSocket");
            try
            {
                <?php
                    /*$genFileContent = parse_ini_file("../conf/environment.ini");
                    $wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
                    $wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$genFileContent['environment']['value']];
                    $wsServerPort = $wsServerContent["wsServerPort"][$genFileContent['environment']['value']];
                    $wsPath = $wsServerContent["wsServerPath"][$genFileContent['environment']['value']];
                    $wsProtocol = $wsServerContent["wsServerProtocol"][$genFileContent['environment']['value']];
                    $wsRetryActive = $wsServerContent["wsServerRetryActive"][$genFileContent['environment']['value']];
                    $wsRetryTime = $wsServerContent["wsServerRetryTime"][$genFileContent['environment']['value']];
                    echo 'wsRetryActive = "' . $wsRetryActive . '";';
                    echo 'wsRetryTime = ' . $wsRetryTime . ';';
                    echo 'webSocket = new WebSocket("' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '");';*/
                ?>
                                            
                webSocket.addEventListener('open', openWsConn);
                webSocket.addEventListener('close', wsClosed);
            }
            catch(e)
            {
                console.log("Widget " + widgetTitle + " could not connect to WebSocket");
                wsClosed();
            }
        };
        
        manageIncomingWsMsg = function(msg)
        {
            console.log("Widget " + widgetTitle + " got new data from WebSocket: \n" + msg.data);
            var msgObj = JSON.parse(msg.data);

            switch(msgObj.msgType)
            {
                case "newNRMetricData":
                    if(encodeURIComponent(msgObj.metricName) === encodeURIComponent(metricName))
                    {
                        var newWsValue = msgObj.newValue;

                        if(metricType === 'Float')
                        {
                            newWsValue = parseFloat(newWsValue).toFixed(1);
                        }

                        if(udm !== null)
                        {
                           if(udmPos === 'next')
                           {
                                                
                            }
                           else
                           {
                              
                           }
                        }
                        else
                        {
                            
                        }
                    }
                    break;

                default:
                    break;
            }
        };
        
        openWsConn = function(e)
        {
            console.log("Widget " + widgetTitle + " connected successfully to WebSocket");
            var wsRegistration = {
                msgType: "ClientWidgetRegistration",
                userType: "widgetInstance",
                metricName: encodeURIComponent(metricName)
              };
              webSocket.send(JSON.stringify(wsRegistration));

              setTimeout(function(){
                  webSocket.removeEventListener('close', wsClosed);
                  webSocket.removeEventListener('open', openWsConn);
                  webSocket.removeEventListener('message', manageIncomingWsMsg);
                  webSocket.close();
                  webSocket = null;
              }, (timeToReload - 2)*1000);
              
            webSocket.addEventListener('message', manageIncomingWsMsg);
        };
        
        wsClosed = function(e)
        {
            console.log("Widget " + widgetTitle + " got WebSocket closed");
            
            webSocket.removeEventListener('close', wsClosed);
            webSocket.removeEventListener('open', openWsConn);
            webSocket.removeEventListener('message', manageIncomingWsMsg);
            webSocket = null;
            if(wsRetryActive === 'yes')
            {
                console.log("Widget " + widgetTitle + " will retry WebSocket reconnection in " + parseInt(wsRetryTime) + "s");
                setTimeout(openWs, parseInt(wsRetryTime*1000));
            }	
        };
        
        //Per ora non usata
        wsError = function(e)
        {
            console.log("Widget " + widgetTitle + " got WebSocket error: " + e);
        };
        
        openWs();*/
        
        $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });
        
        $(document).on('resizeHighchart_' + widgetName, function(event)
        {
            showHeader = event.showHeader;
        });  
});//Fine document ready 
</script>

<div class="widget" id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>
        
        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert" class="noDataAlert">
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer" class="chartContainer">
                
            </div>
            <div id="<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt" class="trackControlsCnt">
                
            </div>
        </div>
    </div>	
</div> 
