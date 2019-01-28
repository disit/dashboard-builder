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
    $(document).ready(function <?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w']))?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId)  
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
        var styleParameters, metricType, originalMetricType, metricName, mapRef, pattern, udm, udmPos, threshold, thresholdEval, appId, flowId, nrMetricType,
            sm_field, sizeRowsWidget, sm_based, rowParameters, fontSize, value, countdownRef, widgetTitle, metricData, widgetHeaderColor, optionsMenuLeft, newControlSubRow, newControlSubLbl, newControlCaret, newControlSubField,
            widgetHeaderFontColor, widgetOriginalBorderColor, urlToCall, showHeader, motivation, trackSrcList, getMyPersonalDataUrl, trackSrcIndex, newControlRow, newControlColorBtn, newControlVariableName, newControlSubCnt,
            widgetParameters, webSocket, openWs, openWsConn, wsError, manageIncomingWsMsg, wsClosed, chartColor, dataLabelsFontSize, dataLabelsFontColor, chartLabelsFontSize, chartLabelsFontColor = null;
        var defaultColors = ["#ffdb4d", "#ff9900", "#ff6666", "#00e6e6", "#33ccff", "#33cc33", "#009900"];
        var currentRoutes = [];
        var pastTrips = [];
        var lastTrips = [];
        var lastPosMarkers = [];
        var mapFollowers = [];
        
        //Definizioni di funzione specifiche del widget
        function personalPoller(trackIndex)
        {
            var localMoving = false, last, lastSample, demiLastSample, lat1, lat2, lon1, lon2, moving, now, elapsedTime, elapsedTimeSeconds, elapsedTimeHours, lastSpeed = null;
            last = 1;
            
            getMyPersonalDataUrl = "../controllers/myPersonalDataProxy.php?variableName=" + encodeURI(trackSrcList[trackIndex].variableName) + "&last=" + last + "&motivation=" + encodeURI(trackSrcList[trackIndex].motivation);
            
            $.ajax({
                url: getMyPersonalDataUrl,
                type: "GET",
                data: {},
                async: true,
                dataType: 'json',
                success: function (newPosition) 
                {
                    lastSample = newPosition[0];
                    
                    lastPosMarkers[trackIndex].setLatLng(JSON.parse(newPosition[0].variableValue));
                    
                    if(mapFollowers[trackIndex] === true)
                    {
                        mapRef.flyTo(JSON.parse(lastSample.variableValue), 17);
                    }
                    
                    if(lastTrips[trackIndex] !== null)
                    {
                        console.log("Ultimo viaggio in corso");
                        demiLastSample = lastTrips[trackIndex][lastTrips[trackIndex].length - 1];
                        lat1 = JSON.parse(demiLastSample.variableValue)[0];
                        lat2 = JSON.parse(lastSample.variableValue)[0];
                        lon1 = JSON.parse(demiLastSample.variableValue)[1];
                        lon2 = JSON.parse(lastSample.variableValue)[1];

                        localMoving = false;

                        //Fermo se non manda dati da più di 4 min
                        now = new Date();
                        if(Math.abs(lastSample.dataTime - now.getTime()) > 240000)
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

                            console.log("Elapsed time hours: " + elapsedTimeHours + " - Last speed: " + lastSpeed + " - Last distance: " + d);
                            
                            //Se si è spostato più di dieci metri fra gli ultimi due campioni e a più di 1 km/h consideriamolo in movimento
                            if((d > 0.01)&&(lastSpeed > 1))
                            {
                                localMoving = true;
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;moving');
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;' + lastSpeed.toFixed(2) + ' Km/h');
                            }
                            else
                            {
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;0 Km/h');
                            }
                            
                            lastTrips[trackIndex].push(lastSample);
                            currentRoutes[trackIndex].addLatLng(JSON.parse(lastSample.variableValue));
                        }
                    }
                    else
                    {
                        //Per il primo campione in assoluto l'algoritmo è diverso
                        console.log("Primo campione nuovo viaggio");

                        localMoving = false;

                        //Fermo se non manda dati da più di 4 min
                        now = new Date();
                        if(Math.abs(lastSample.dataTime - now.getTime()) > 240000)
                        {
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;N\/A');
                        }
                        else
                        {
                            localMoving = true;
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;first sample');
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;N\/A');
                            
                            lastTrips[trackIndex] = [];
                            lastTrips[trackIndex].push(lastSample);
                            currentRoutes[trackIndex].addLatLng(JSON.parse(lastSample.variableValue));
                        }
                    }
                    
                    if(localMoving)
                    {
                        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsColorBtn[data-variableName="' + lastSample.variableName + '"]').attr('data-moving', true);
                    }
                    else
                    {
                        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsColorBtn[data-variableName="' + lastSample.variableName + '"]').attr('data-moving', false);
                    }
                    
                    var lastSampleDate = new Date(lastSample.dataTime);
                    lastSampleDate = lastSampleDate.getDate() + "\/" + parseInt(lastSampleDate.getMonth() + 1) + "\/" + lastSampleDate.getFullYear() + " " + lastSampleDate.getHours() + ":" + lastSampleDate.getMinutes() + ":" + lastSampleDate.getSeconds();
                    
                    $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.lastSampleCnt').html('&nbsp;' + lastSampleDate);
                    
                },
                error: function(errorData)
                {

                }
            });
        }
        
        function buildPastTrips(trackIndex)
        {
            var lat1, lat2, lon1, lon2, moving, lastSample, demiLastSample, now, elapsedTime, elapsedTimeSeconds, elapsedTimeHours, lastSpeed = null;
            var tripsUrl = "../controllers/myPersonalDataProxy.php?variableName=" + encodeURI(trackSrcList[trackIndex].variableName) + "&motivation=" + encodeURI(trackSrcList[trackIndex].motivation);
            
            $.ajax({
                url: tripsUrl,
                type: "GET",
                data: {},
                async: true,
                dataType: 'json',
                success: function (tripsData) 
                {
                    //Ordiniamento dei campioni per timestamp
                    tripsData.sort(compareNavSamples);
                    
                    //Costruzione dei viaggi
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
                                html:'<div style="color: ' + defaultColors[trackIndex%7] + '"><i class="fa fa-map-marker"></i></div>'
                            });
                            
                            lastPosMarkers[trackIndex] = L.marker(JSON.parse(tripsData[i].variableValue), {icon: icon});
                            mapRef.addLayer(lastPosMarkers[trackIndex]);
                        }
                    }
                    
                    //Condizione iniziale: fermo o in movimento
                    lastSample = tripsData[tripsData.length - 1];
                    demiLastSample = tripsData[tripsData.length - 2];
                    
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
                    if(Math.abs(lastSample.dataTime - now.getTime()) > 240000)
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
                        if((d > 0.01)&&(lastSpeed > 1))
                        {
                            moving = true;
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;moving');
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;' + lastSpeed.toFixed(2) + ' Km/h');
                        }
                        else
                        {
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.statusCnt').html('&nbsp;still');
                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + lastSample.variableName + '"] div.speedCnt').html('&nbsp;0 Km/h');
                        }
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
                error: function(errorData)
                {

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
                    sm_based = widgetData.params.sm_based;
                    trackSrcList = JSON.parse(widgetData.params.parameters);
                    
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
                     
                    switch(sm_based)
                    {
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

                        case 'myPersonalData':
                            for(trackSrcIndex = 0; trackSrcIndex < trackSrcList.length; trackSrcIndex++)
                            {
                                //Map followers disattivati di default all'avvio
                                mapFollowers[trackSrcIndex] = false;
                                
                                //Costruzione menu
                                newControlRow = $('<div class="trackControlsRow"></div>');
                                newControlColorBtn = $('<div class="trackControlsColorCnt centerWithFlex"><div class="trackControlsColorBtn" data-moving="false" data-variableName="' + trackSrcList[trackSrcIndex].variableName + '"></div></div>');
                                newControlColorBtn.find('.trackControlsColorBtn').css('background-color', defaultColors[trackSrcIndex%7]);
                                newControlVariableName = $('<div class="trackControlsVariableName">' + trackSrcList[trackSrcIndex].variableName + '</div>');
                                newControlCaret = $('<div class="trackControlsCaret" data-variableName="' + trackSrcList[trackSrcIndex].variableName + '"><i class="fa fa-caret-down"></i></div>');
                                
                                newControlSubCnt = $('<div class="trackControlsSubcnt" data-variableName="' + trackSrcList[trackSrcIndex].variableName + '"></div>');
                                
                                newControlSubRow = $('<div class="trackControlsSubrow"></div>');
                                newControlSubLbl = $('<div class="trackControlsSublbl">Status:</div>');
                                newControlSubField = $('<div class="trackControlsSubfield statusCnt">&nbsp;N\/A</div>');
                                newControlSubRow.append(newControlSubLbl);
                                newControlSubRow.append(newControlSubField);
                                newControlSubCnt.append(newControlSubRow);
                                
                                newControlSubRow = $('<div class="trackControlsSubrow"></div>');
                                newControlSubLbl = $('<div class="trackControlsSublbl">Speed:</div>');
                                newControlSubField = $('<div class="trackControlsSubfield speedCnt">&nbsp;N\/A</div>');
                                newControlSubRow.append(newControlSubLbl);
                                newControlSubRow.append(newControlSubField);
                                newControlSubCnt.append(newControlSubRow);
                                
                                newControlSubRow = $('<div class="trackControlsSubrow"></div>');
                                newControlSubLbl = $('<div class="trackControlsSublbl">Last sent:</div>');
                                newControlSubField = $('<div class="trackControlsSubfield lastSampleCnt">&nbsp;N\/A</div>');
                                newControlSubRow.append(newControlSubLbl);
                                newControlSubRow.append(newControlSubField);
                                newControlSubCnt.append(newControlSubRow);
                                
                                newControlSubRow = $('<div class="trackControlsSubrow trackControlsBtnRow row"></div>');
                                newControlSubCnt.append(newControlSubRow);
                                
                                var showControl = $('<div class="trackControlsBtnCnt col-xs-4 trackControlShow" data-active="true" data-hoverColor="' + defaultColors[trackSrcIndex%7] + '" data-trackIndex="' + trackSrcIndex + '" style="color:' + defaultColors[trackSrcIndex%7] + '"><i class="fa fa-eye"></i></div>');
                                newControlSubRow.append(showControl);
                                
                                var followControl = $('<div class="trackControlsBtnCnt col-xs-4 trackControlFollow" data-hoverColor="' + defaultColors[trackSrcIndex%7] + '" data-active="false" data-trackIndex="' + trackSrcIndex + '"><i class="fa fa-external-link"></i></div>');
                                newControlSubRow.append(followControl);
                                
                                newControlSubLbl = $('<div class="trackControlsBtnCnt col-xs-4" data-active="false" data-hoverColor="' + defaultColors[trackSrcIndex%7] + '"><i class="fa fa-list"></i></div>');
                                newControlSubRow.append(newControlSubLbl);
                                
                                newControlRow.append(newControlColorBtn);
                                newControlRow.append(newControlVariableName);
                                newControlRow.append(newControlCaret);
                                
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').append(newControlRow);
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').append(newControlSubCnt);
                                
                                newControlCaret.hover(function(){
                                    $(this).css('color', $(this).parents('div.trackControlsRow').find('div.trackControlsColorBtn').css('background-color'));
                                }, function(){
                                    $(this).css('color', 'white');
                                });
                                
                                newControlCaret.click(function(){
                                    var variableName = $(this).attr('data-variableName');
                                    
                                    if($('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + variableName + '"]').is(':visible'))
                                    {
                                        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + variableName + '"]').hide();
                                        $(this).find('i').removeClass('fa-caret-up');
                                        $(this).find('i').addClass('fa-caret-down');
                                    }
                                    else
                                    {
                                        $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt div.trackControlsSubcnt[data-variableName="' + variableName + '"]').show();
                                        $(this).find('i').removeClass('fa-caret-down');
                                        $(this).find('i').addClass('fa-caret-up');
                                    }
                                });
                                
                                $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').find('div.trackControlsBtnCnt').hover(function()
                                {
                                    if($(this).attr('data-active') === 'false')
                                    {
                                        $(this).css('color', $(this).attr('data-hoverColor'));
                                    }
                                    
                                }, function(){
                                    if($(this).attr('data-active') === 'false')
                                    {
                                        $(this).css('color', 'white');
                                    }
                                });
                                
                                showControl.click(function()
                                {
                                    if($(this).attr('data-active') === 'false')
                                    {
                                        $(this).attr('data-active', true);
                                        $(this).find('i.fa').removeClass('fa-eye-slash');
                                        $(this).find('i.fa').addClass('fa-eye');
                                        $(this).css('color', $(this).attr('data-hoverColor'));
                                        
                                        mapRef.addLayer(lastPosMarkers[parseInt($(this).attr('data-trackIndex'))]);
                                        mapRef.addLayer(currentRoutes[parseInt($(this).attr('data-trackIndex'))]);
                                    }
                                    else
                                    {
                                        $(this).attr('data-active', false);
                                        $(this).find('i.fa').removeClass('fa-eye');
                                        $(this).find('i.fa').addClass('fa-eye-slash');
                                        $(this).css('color', 'white');
                                        
                                        mapRef.removeLayer(lastPosMarkers[parseInt($(this).attr('data-trackIndex'))]);
                                        mapRef.removeLayer(currentRoutes[parseInt($(this).attr('data-trackIndex'))]);
                                    }
                                });
                                
                                followControl.click(function()
                                {
                                    for(var i = 0; i < mapFollowers.length; i++)
                                    {
                                        if(i !== parseInt($(this).attr('data-trackIndex')))
                                        {
                                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').find('div.trackControlFollow').eq(i).attr('data-active', false);
                                            mapFollowers[i] = false;
                                            $('#<?=str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_trackControlsCnt').find('div.trackControlFollow').eq(i).css('color', 'white');
                                        }
                                    }
                                    
                                    var localTrackIndex = parseInt($(this).attr('data-trackIndex'));
                                    
                                    if($(this).attr('data-active') === 'false')
                                    {
                                        $(this).attr('data-active', true);
                                        mapFollowers[localTrackIndex] = true;
                                        $(this).css('color', $(this).attr('data-hoverColor'));
                                    }
                                    else
                                    {
                                        $(this).attr('data-active', false);
                                        mapFollowers[localTrackIndex] = false;
                                        $(this).css('color', 'white');
                                    }
                                });
                                
                                //Costruzione polylines
                                currentRoutes[trackSrcIndex] = new L.Polyline([], {
                                    color: defaultColors[trackSrcIndex%7],
                                    weight: 5,
                                    opacity: 0.8,
                                    smoothFactor: 1
                                });
                                
                                mapRef.addLayer(currentRoutes[trackSrcIndex]);
                                
                                //Costruzione viaggi passati 
                                buildPastTrips(trackSrcIndex);
                                
                                //Avvio poller
                                setInterval(personalPoller.bind(null, trackSrcIndex), 5000);
                            }
                            break;
                    }
                    
                },
                error: function(errorData)
                {
                    
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
