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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) {
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
            eventLog("Returned the following ERROR in widgetResourcesNew.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
            exit();
        }
        ?>
        var scroller, widgetProperties, styleParameters, icon, serviceUri,
            eventName, eventType, newRow, newIcon, eventContentW, widgetTargetList, backgroundTitleClass,
            backgroundFieldsClass,
            background, originalHeaderColor, originalBorderColor, eventTitle, temp, day, month, hour, min, sec,
            eventStart,
            eventName, serviceUri, eventLat, eventLng, eventStartDate, eventStartTime,
            eventsNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx, eventContentWPerc, dataContainer,
            dateContainer, timeContainer, mapPtrContainer, pinContainer, pinMsgContainer,
            fontSizePin, dateFontSize, mapPinImg, eventNameWithCase, eventSeverity,
            lastPopup, widgetParameters, countdownRef = null;

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
        var eventsArray = [];
        var eventsOnMaps = {};
        var targetsArrayForNotify = [];

        var timeSortState = 0;

        if (url === "null") {
            url = null;
        }

        if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
            showHeader = false;
        }
        else {
            showHeader = true;
        }

        dateFontSize = parseInt(parseInt(fontSize) + 3);
        fontSizePin = 12;

        $(document).on("esbEventAdded", function (event) {
            if (event.generator !== "<?= $_REQUEST['name_w'] ?>") {
                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").each(function (i) {
                    if ($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap") === "true") {
                        for (widgetName in widgetTargetList) {
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap", "false");

                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).removeClass("onMapTrafficEventPinAnimated");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");

                            eventsOnMaps[widgetName].eventsNumber = 0;
                            eventsOnMaps[widgetName].eventsPoints.splice(0);
                            eventsOnMaps[widgetName].mapRef = null;

                            timeSortState = 0;
                            $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("img").attr("src", "../img/trafficIcons/time.png");
                            $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");
                        }
                    }
                });
            }
        });

        //Definizioni di funzione
        function loadDefaultMap(widgetName) {
            if ($('#' + widgetName + '_defaultMapDiv div.leaflet-map-pane').length > 0) {
                //Basta nasconderla, tanto viene distrutta e ricreata ad ogni utilizzo (per ora).
                $('#' + widgetName + '_mapDiv').hide();
                $('#' + widgetName + '_defaultMapDiv').show();
            }
            else {
                var mapdiv = widgetName + "_defaultMapDiv";
                var mapRef = L.map(mapdiv).setView([43.769789, 11.255694], 11);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                    maxZoom: 18
                }).addTo(mapRef);
                mapRef.attributionControl.setPrefix('');
            }
        }

        function populateWidget(fromSort) {
            var i = 0;

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').empty();

            for (var key in eventsArray) {
                temp = eventsArray[key].event_time;
                eventStart = new Date(temp);

                day = eventStart.getDate();
                if (day < 10) {
                    day = "0" + day.toString();
                }

                month = eventStart.getMonth() + 1;
                if (month < 10) {
                    month = "0" + month.toString();
                }

                hour = eventStart.getHours();
                if (hour < 10) {
                    hour = "0" + hour.toString();
                }

                min = eventStart.getMinutes();
                if (min < 10) {
                    min = "0" + min.toString();
                }

                sec = eventStart.getSeconds();
                if (sec < 10) {
                    sec = "0" + sec.toString();
                }

                eventStartDate = day + "/" + month + "/" + eventStart.getFullYear().toString();
                eventStartTime = hour + ":" + min + ":" + sec;
                eventNameWithCase = eventsArray[key].data_id.replace("urn:rixf:org.resolute-eu.simulator/resources/", "");
                eventName = eventsArray[key].data_id.replace("urn:rixf:org.resolute-eu.simulator/resources/", "").toLowerCase();
                eventLat = eventsArray[key].payload.locations[0].latitude;
                eventLng = eventsArray[key].payload.locations[0].longitude;

                newRow = $('<div class="trafficEventRow"></div>');
                $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').append(newRow);
                newRow.css("width", "100%");
                newRow.css("height", rowPercHeight + "%");
                backgroundTitleClass = "lowSeverityTitle";
                backgroundFieldsClass = "lowSeverity";//Giallo
                background = "#ffcc00";

                icon = $('<img src="../img/resourceIcons/metro.png" />');

                eventTitle = $('<div class="eventTitle"><p class="eventTitlePar"><span>' + eventName + '</span></p></div>');
                eventTitle.addClass(backgroundTitleClass);

                eventTitle.css("height", "30%");
                newRow.append(eventTitle);

                dataContainer = $('<div class="trafficEventDataContainer"></div>');
                newRow.append(dataContainer);

                newIcon = $("<div class='trafficEventIcon centerWithFlex'></div>");
                newIcon.append(icon);
                newIcon.addClass(backgroundFieldsClass);

                dataContainer.append(newIcon);
                newIcon.css("height", "100%");

                dateContainer = $("<div class='resourceDateContainer centerWithFlex'></div>");

                dateContainer.addClass(backgroundFieldsClass);
                dateContainer.html(eventStartDate);
                dataContainer.append(dateContainer);

                timeContainer = $("<div class='resourceTimeContainer centerWithFlex'></div>");

                timeContainer.addClass(backgroundFieldsClass);
                timeContainer.html(eventStartTime);
                dataContainer.append(timeContainer);

                mapPtrContainer = $("<div class='trafficEventMapPtr'></div>");
                mapPtrContainer.addClass(backgroundFieldsClass);

                pinContainer = $("<div class='trafficEventPinContainer'><a class='trafficEventLink' data-eventstartdate='" + eventStartDate + "' data-eventstarttime='" + eventStartTime + "' data-eventname='" + eventNameWithCase +
                    "' data-eventlat='" + eventLat + "' data-eventlng='" + eventLng + "' data-background='" + background + "' data-onMap='false'><i class='material-icons'>place</i></a></div>");
                mapPtrContainer.append(pinContainer);
                pinMsgContainer = $("<div class='trafficEventPinMsgContainer'></div>");

                mapPtrContainer.append(pinMsgContainer);

                dataContainer.append(mapPtrContainer);

                if (i < (eventsNumber - 1)) {
                    newRow.css("margin-bottom", "4px");
                }
                else {
                    newRow.css("margin-bottom", "0px");
                }

                //Interazione cross-widget
                pinContainer.find("a.trafficEventLink").hover(
                    function () {
                        var localBackground = $(this).attr("data-background");

                        originalHeaderColor = {};
                        originalBorderColor = {};

                        for (var index in widgetTargetList) {
                            originalHeaderColor[widgetTargetList[index]] = $("#" + widgetTargetList[index] + "_header").css("background-color");
                            originalBorderColor[widgetTargetList[index]] = $("#" + widgetTargetList[index]).css("border-color");

                            if ($(this).attr("data-onMap") === 'false') {
                                $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("show");
                            }
                            else {
                                $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("hide");
                            }

                            $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "white");

                            $("#" + widgetTargetList[index] + "_header").css("background", localBackground);
                            $("#" + widgetTargetList[index]).css("border-color", localBackground);
                        }
                    },
                    function () {
                        for (var index in widgetTargetList) {
                            if ($(this).attr("data-onMap") === 'false') {
                                $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                            }
                            else {
                                $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                            }

                            $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "black");

                            $("#" + widgetTargetList[index] + "_header").css("background", originalHeaderColor[widgetTargetList[index]]);
                            $("#" + widgetTargetList[index]).css("border-color", originalBorderColor[widgetTargetList[index]]);
                        }
                    }
                );

                pinContainer.find("a.trafficEventLink").click(function () {
                    var goesOnMap = false;

                    if ($(this).attr("data-onMap") === 'false') {
                        $(this).attr("data-onMap", 'true');
                        goesOnMap = true;
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                        $(this).addClass("onMapTrafficEventPinAnimated");
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                    }
                    else {
                        $(this).attr("data-onMap", 'false');
                        goesOnMap = false;
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                        $(this).removeClass("onMapTrafficEventPinAnimated");
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                    }

                    targetsArrayForNotify = [];

                    for (var index in widgetTargetList) {
                        if (goesOnMap) {
                            targetsArrayForNotify.push(widgetName);
                            addEventToMap($(this), widgetTargetList[index], index);
                        }
                        else {
                            removeEventFromMap($(this), widgetTargetList[index], index);
                        }
                    }

                    //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
                    $.event.trigger({
                        type: "esbEventAdded",
                        generator: "<?= $_REQUEST['name_w'] ?>",
                        targetsArray: targetsArrayForNotify
                    });

                });
                i++;

            }//Fine del for

            var maxTitleFontSize = $('div.eventTitle').height() * 0.75;

            if (maxTitleFontSize > fontSize) {
                maxTitleFontSize = fontSize;
            }

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer p.eventTitlePar span').css("font-size", maxTitleFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .alarmSeverityContainer').css("font-size", maxTitleFontSize + "px");
            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .resourceDateContainer').eq(0).width() * 0.12;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .resourceDateContainer ').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .resourceTimeContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventLink i').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 1.5) + "px");

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinMsgContainer').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 2.846) + "px");

            if (!fromSort) {
                var btnIndicatorFontSize = $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").eq(0).width() / 48.4375;
                $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").css("font-size", btnIndicatorFontSize + "em");
            }

            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer [data-toggle="tooltip"]').tooltip({
                html: true
            });
        }

        function updateFullscreenPointsList(widgetNameLocal, eventsPointsLocal) {
            var temp = null;
            $("#" + widgetNameLocal + "_driverWidgetType").val("resources");
            $('#' + widgetNameLocal + '_modalLinkOpen input.fullscreenEventPoint').remove();

            for (var i = 0; i < eventsPointsLocal.length; i++) {
                if ($('#' + widgetNameLocal + '_fullscreenEvent_' + i).length <= 0) {
                    temp = $('<input type="hidden" class="fullscreenEventPoint" data-eventType="resource" id="<?= $_REQUEST['name_w'] ?>_fullscreenEvent_' + i + '"/>');
                    temp.val(eventsPointsLocal[i].join("||"));
                    $('#' + widgetNameLocal + '_modalLinkOpen div.modalLinkOpenBody').append(temp);
                }
            }
        }

        function addEventToMap(eventLink, widgetName) {
            var passedData = [];

            var lat = eventLink.attr("data-eventlat");
            var lng = eventLink.attr("data-eventlng");
            var eventName = eventLink.attr("data-eventname");
            var eventStartDate = eventLink.attr("data-eventstartdate");
            var eventStartTime = eventLink.attr("data-eventstarttime");
            var coordsAndType = {};

            coordsAndType.lng = lng;//OS vuole le coordinate alla rovescia
            coordsAndType.lat = lat;
            coordsAndType.eventName = eventName;
            coordsAndType.eventStartDate = eventStartDate;
            coordsAndType.eventStartTime = eventStartTime;
            coordsAndType.eventType = "resource";

            passedData.push(coordsAndType);

            $.event.trigger({
                type: "addResource",
                target: widgetName,
                passedData: passedData
            });
        }

        function removeAllEventsFromMaps(fromSort) {
            for (var widgetName in widgetTargetList) {

                var passedData = [];

                for (var key in widgetTargetList[widgetName]) {
                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").each(function () {
                        var lat = $(this).attr("data-eventlat");
                        var lng = $(this).attr("data-eventlng");
                        var eventName = $(this).attr("data-eventname");

                        var coordsAndType = {};

                        coordsAndType.lng = lng;
                        coordsAndType.lat = lat;
                        coordsAndType.eventName = eventName;
                        coordsAndType.eventType = "resource";

                        passedData.push(coordsAndType);
                    });

                }
                $.event.trigger({
                    type: "removeResource",
                    target: widgetTargetList[widgetName],
                    passedData: passedData
                });
            }
        }

        function removeEventFromMap(eventLink, widgetName, widgetIndex) {
            var passedData = [];

            var lat = eventLink.attr("data-eventlat");
            var lng = eventLink.attr("data-eventlng");
            var eventName = eventLink.attr("data-eventname");

            var coordsAndType = {};

            coordsAndType.lng = lng;
            coordsAndType.lat = lat;
            coordsAndType.eventName = eventName;
            coordsAndType.eventType = "resource";

            passedData.push(coordsAndType);

            $.event.trigger({
                type: "removeResource",
                target: widgetName,
                passedData: passedData
            });
        }

        //Restituisce il JSON delle soglie se presente, altrimenti NULL
        function getThresholdsJson() {
            var thresholdsJson = null;
            if (jQuery.parseJSON(widgetProperties.param.parameters !== null)) {
                thresholdsJson = widgetProperties.param.parameters;
            }

            return thresholdsJson;
        }

        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getInfoJson() {
            var infoJson = null;
            if (jQuery.parseJSON(widgetProperties.param.infoJson !== null)) {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson);
            }

            return infoJson;
        }

        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getStyleParameters() {
            var styleParameters = null;
            if (jQuery.parseJSON(widgetProperties.param.styleParameters !== null)) {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters);
            }

            return styleParameters;
        }

        function stepDownInterval() {
            var oldPos = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop();
            var newPos = oldPos + 1;

            var oldScrollTop = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop();
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(newPos);
            var newScrollTop = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop();

            if (oldScrollTop === newScrollTop) {
                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);
            }
        }

        function resizeWidget() {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);

            var btnIndicatorFontSize = $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").eq(0).width() / 48.4375;
            $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").css("font-size", btnIndicatorFontSize + "em");

            shownHeight = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").prop("offsetHeight");
            rowPercHeight = 75 * 100 / shownHeight;

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer div.trafficEventRow').css("height", rowPercHeight + "%");
            var maxTitleFontSize = $('div.eventTitle').height() * 0.75;

            if (maxTitleFontSize > fontSize) {
                maxTitleFontSize = fontSize;
            }

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer p.eventTitlePar span').css("font-size", maxTitleFontSize + "px");

            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .resourceDateContainer').eq(0).width() * 0.12;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .resourceDateContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .resourceTimeContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventLink i').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 1.5) + "px");

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinMsgContainer').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 2.846) + "px");

            clearInterval(scroller);
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);
            scroller = setInterval(stepDownInterval, speed);
        }

        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function (event) {
            showHeader = event.showHeader;
        });
        $(document).on('removeResourcePin', function () {
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").each(function () {
                $(this).attr("data-onMap", 'false');
                $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                $(this).removeClass("onMapTrafficEventPinAnimated");
                $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
            });
        });
        //Fine definizioni di funzione 

        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer").css("background-color", $("#<?= $_REQUEST['name_w'] ?>_header").css("background-color"));

        if (firstLoad === false) {
            showWidgetContent(widgetName);
        }
        else {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }

        //addLink(widgetName, url, linkElement, divContainer);

        //Nuova versione
        if (('<?= $_REQUEST['styleParameters'] ?>' !== "") && ('<?= $_REQUEST['styleParameters'] ?>' !== "null")) {
            styleParameters = JSON.parse('<?= $_REQUEST['styleParameters'] ?>');
        }

        if ('<?= $_REQUEST['parameters'] ?>'.length > 0) {
            widgetParameters = JSON.parse('<?= $_REQUEST['parameters'] ?>');
        }

        widgetTargetList = widgetParameters;

        var targetName = null;

        for (var name in widgetTargetList) {
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
            success: function (data) {
                if (firstLoad !== false) {
                    showWidgetContent(widgetName);
                }
                else {
                    elToEmpty.empty();
                }

                eventsNumber = Object.keys(data).length;

                if (eventsNumber === 0) {
                    $('#<?= $_REQUEST['name_w'] ?>_buttonsContainer').hide();
                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").hide();
                    $("#<?= $_REQUEST['name_w'] ?>_noDataAlert").show();
                }
                else {
                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").show();

                    widgetWidth = $('#<?= $_REQUEST['name_w'] ?>_div').width();
                    shownHeight = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").prop("offsetHeight");
                    rowPercHeight = 75 * 100 / shownHeight;
                    contentHeightPx = eventsNumber * 100;
                    eventContentWPerc = null;

                    if (contentHeightPx > shownHeight) {
                        eventContentW = parseInt(widgetWidth - 45 - 22);
                    }
                    else {
                        eventContentW = parseInt(widgetWidth - 45 - 5);
                    }

                    eventContentWPerc = Math.floor(eventContentW / widgetWidth * 100);

                    //Inserimento una tantum degli eventi nell'apposito array (per ordinamenti)
                    for (var key in data) {
                        var localPayload = JSON.parse(data[key].payload);
                        data[key].payload = localPayload;
                        eventsArray.push(data[key]);
                    }

                    eventsArray.sort(function (a, b) {
                        var itemA = new Date(a.event_time);
                        var itemB = new Date(b.event_time);
                        if (itemA < itemB) {
                            return 1;
                        }
                        else {
                            if (itemA > itemB) {
                                return -1;
                            }
                            else {
                                return 0;
                            }
                        }
                    });

                    populateWidget(false);

                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).hover(
                        function () {
                            $(this).css("cursor", "pointer");
                            $(this).css("color", "white");
                            $(this).find("img").attr("src", "../img/trafficIcons/timeWhite.png");
                            switch (timeSortState) {
                                case 0://Crescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down"></i>');
                                    $(this).attr("title", "Sort by time - Ascending");
                                    break;

                                case 1://Decrescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up"></i>');
                                    $(this).attr("title", "Sort by time - Descending");
                                    break;

                                case 2://No sort
                                    $(this).find("div.trafficEventsButtonIndicator").html('no sort');
                                    $(this).attr("title", "Sort by time - None");
                                    break;
                            }
                        },
                        function () {
                            $(this).css("cursor", "auto");
                            $(this).css("color", "black");
                            $(this).find("img").attr("src", "../img/trafficIcons/time.png");

                            switch (timeSortState) {
                                case 0://No sort
                                    $(this).find("div.trafficEventsButtonIndicator").html('');
                                    break;

                                case 1://Crescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down"></i>');
                                    break;

                                case 2://Decrescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up"></i>');
                                    break;
                            }
                        }
                    );

                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).click(function () {
                        var localEventType = null;

                        switch (timeSortState) {
                            case 0:
                                eventsArray.sort(function (a, b) {
                                    var itemA = new Date(a.event_time);
                                    var itemB = new Date(b.event_time);
                                    if (itemA > itemB) {
                                        return 1;
                                    }
                                    else {
                                        if (itemA < itemB) {
                                            return -1;
                                        }
                                        else {
                                            return 0;
                                        }
                                    }
                                });

                                populateWidget(true);
                                removeAllEventsFromMaps(true);

                                //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                                targetsArrayForNotify = [];

                                for (var index in widgetTargetList) {
                                    targetsArrayForNotify.push(widgetTargetList[index]);
                                    $("#" + widgetTargetList[index] + "_wrapper").hide();
                                    $("#" + widgetTargetList[index] + "_defaultMapDiv").hide();
                                    $("#" + widgetTargetList[index] + "_mapDiv").show();
                                    addEventToMap($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetTargetList[index], index);
                                }

                                //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
                                $.event.trigger({
                                    type: "esbEventAdded",
                                    generator: "<?= $_REQUEST['name_w'] ?>",
                                    targetsArray: targetsArrayForNotify
                                });

                                timeSortState = 1;
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down"></i>');
                                break;

                            case 1:
                                eventsArray.sort(function (a, b) {
                                    var itemA = new Date(a.event_time);
                                    var itemB = new Date(b.event_time);
                                    if (itemA < itemB) {
                                        return 1;
                                    }
                                    else {
                                        if (itemA > itemB) {
                                            return -1;
                                        }
                                        else {
                                            return 0;
                                        }
                                    }
                                });

                                populateWidget(true);
                                removeAllEventsFromMaps(true);

                                //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                                for (var index in widgetTargetList) {
                                    $("#" + widgetTargetList[index] + "_wrapper").hide();
                                    $("#" + widgetTargetList[index] + "_defaultMapDiv").hide();
                                    $("#" + widgetTargetList[index] + "_mapDiv").show();
                                    addEventToMap($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetTargetList[index], index);
                                }

                                timeSortState = 2;
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up"></i>');
                                break;

                            case 2:
                                eventsArray.sort(function (a, b) {
                                    var itemA = new Date(a.event_time);
                                    var itemB = new Date(b.event_time);
                                    if (itemA < itemB) {
                                        return 1;
                                    }
                                    else {
                                        if (itemA > itemB) {
                                            return -1;
                                        }
                                        else {
                                            return 0;
                                        }
                                    }
                                });

                                populateWidget(true);
                                removeAllEventsFromMaps(false);

                                for (var index in widgetTargetList) {
                                    $("#" + widgetTargetList[index] + "_wrapper").hide();
                                    $("#" + widgetTargetList[index] + "_mapDiv").hide();
                                    $("#" + widgetTargetList[index] + "_defaultMapDiv").show();
                                }

                                timeSortState = 0;
                                $(this).find("div.trafficEventsButtonIndicator").html('');
                                break;
                        }
                        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html('');
                    });

                    scroller = setInterval(stepDownInterval, speed);
                    var timeToClearScroll = (timeToReload - 0.5) * 1000;

                    setTimeout(function () {
                        clearInterval(scroller);
                        $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").off();

                        //$(document).off("esbEventAdded");

                        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");

                        //Ripristino delle homepage native per gli widget targets al reload
                        for (var widgetName in widgetTargetList) {
                            if ($("#" + widgetTargetList[widgetName] + "_driverWidgetType").val() === 'resources') {
                                loadDefaultMap(widgetTargetList[widgetName]);
                            }
                            else {
                                //console.log("Attualmente non pilotato da resources");
                            }
                        }

                    }, timeToClearScroll);


                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").mouseenter(function () {
                        clearInterval(scroller);
                    });

                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").mouseleave(function () {
                        clearInterval(scroller);
                        scroller = setInterval(stepDownInterval, speed);
                    });
                }
            },
            error: function (data) {
                console.log("Ko");
                console.log(JSON.stringify(data));

                showWidgetContent(widgetName);
                $("#<?= $_REQUEST['name_w'] ?>_table").css("display", "none");
                $("#<?= $_REQUEST['name_w'] ?>_noDataAlert").css("display", "block");
            }
        });

        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function (event) {
            resizeWidget();
        });

        $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
        $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function (event) {
            clearInterval(countdownRef);
            timeToReload = event.newTimeToReload;
            countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        });

        countdownRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
    });//Fine document ready
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>

        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>

        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            <div id="<?= $_REQUEST['name_w'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_mainContainer" class="chartContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_buttonsContainer"
                     class="trafficEventsButtonsContainer centerWithFlex">
                    <div class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/time.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_rollerContainer" class="trafficEventsRollerContainer"></div>
            </div>
        </div>
    </div>
</div> 