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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, newEventFromWs) {
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
            eventLog("Returned the following ERROR in widgetOperatorEventListNew.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
            exit();
        }
        ?>
        var timeFontSize, scroller, widgetProperties, styleParameters, icon, serviceUri,
            eventName, eventType, newRow, newIcon, eventContentW, widgetTargetList, backgroundTitleClass,
            backgroundFieldsClass,
            background, originalHeaderColor, originalBorderColor, eventTitle, temp, day, month, hour, min, sec,
            eventStart,
            eventName, serviceUri, eventLat, eventLng, eventTooltip, eventStartDate, eventStartTime,
            eventsNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx, eventContentWPerc, dataContainer,
            middleContainer, subTypeContainer,
            dateContainer, timeContainer, severityContainer, mapPtrContainer, pinContainer, pinMsgContainer,
            fontSizePin, dateFontSize, mapPinImg, eventNameWithCase, eventSeverity, widgetPanToTargetList,
            typeId, lastPopup, widgetParameters, countdownRef, openWs, manageIncomingWsMsg, openWsConn, wsClosed = null;

        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var speed = 50;
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_mainContainer");
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var timeToReload = <?= sanitizeInt('frequency_w') ?>;
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer");
        var url = "<?= escapeForJS($_REQUEST['link_w']) ?>";
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
        var showHeader = null;
        var headerHeight = 25;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var wsRetryActive, wsRetryTime = null;
        var eventsArray = [];
        var eventsOnMaps = {};
        var targetsArrayForNotify = [];
        var goesOnMap = false;


        if (url === "null") {
            url = null;
        }

        if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
            showHeader = false;
        }
        else {
            showHeader = true;
        }

        timeFontSize = parseInt(fontSize * 1.6);
        dateFontSize = parseInt(fontSize * 0.95);
        fontSizePin = parseInt(fontSize * 0.95);

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
                temp = eventsArray[key].time;
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
                eventNameWithCase = eventsArray[key].codeColor;
                eventNameWithCase = eventsArray[key].codeColor.replace(/\./g, "");
                eventNameWithCase = eventsArray[key].codeColor.replace(/'/g, "&apos;");
                eventNameWithCase = eventsArray[key].codeColor.replace(/\u0027/g, "&apos;");

                eventName = eventsArray[key].codeColor.toLowerCase();
                eventName = eventsArray[key].codeColor.replace(/\./g, "").toLowerCase();
                eventName = eventsArray[key].codeColor.replace(/'/g, "&apos;").toLowerCase();
                eventName = eventsArray[key].codeColor.replace(/\u0027/g, "&apos;").toLowerCase();
                operatorName = eventsArray[key].user;
                eventLat = eventsArray[key].lat;
                eventLng = eventsArray[key].lng;
                eventSeverity = eventsArray[key].codeColor;
                peopleNumber = eventsArray[key].personNumber;

                newRow = $('<div class="trafficEventRow"></div>');

                switch (eventSeverity) {
                    case "White":
                        backgroundTitleClass = "operatorEventWhiteTitle";
                        backgroundFieldsClass = "operatorEventWhite";
                        background = "#ffffff";
                        break;

                    case "Blue":
                        backgroundTitleClass = "operatorEventBlueTitle";
                        backgroundFieldsClass = "operatorEventBlue";
                        background = "#66ccff";
                        break;

                    case "Green":
                        backgroundTitleClass = "operatorEventGreenTitle";
                        backgroundFieldsClass = "operatorEventGreen";
                        background = "#66ff33";
                        break;

                    case "Yellow":
                        backgroundTitleClass = "operatorEventYellowTitle";
                        backgroundFieldsClass = "operatorEventYellow";
                        background = "#ffff00";
                        break;

                    case "Red":
                        backgroundTitleClass = "highSeverityTitle";
                        backgroundFieldsClass = "highSeverity";
                        background = "#ff6666";
                        break;
                }

                newRow.css("height", rowPercHeight + "%");
                eventTitle = $('<div class="eventTitle"><p class="eventTitlePar"><span>' + eventStartDate + " " + eventStartTime + '</span></p></div>');
                eventTitle.addClass(backgroundTitleClass);
                eventTitle.css("height", "30%");
                $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').append(newRow);

                newRow.append(eventTitle);

                dataContainer = $('<div class="trafficEventDataContainer"></div>');
                newRow.append(dataContainer);

                peopleContainer = $("<div class='operatorEventPeopleContainer'><div class='operatorEventLabelContainer centerWithFlex'>people</div><div class='operatorEventDataContainer centerWithFlex'>" + peopleNumber + "</div></div>");
                peopleContainer.addClass(backgroundFieldsClass);
                dataContainer.append(peopleContainer);

                peopleContainer.find('div.operatorEventDataContainer').css('font-size', peopleContainer.find('div.operatorEventDataContainer').height() * 0.45 + 'px');
                peopleContainer.find('div.operatorEventLabelContainer').css('font-size', peopleContainer.find('div.operatorEventLabelContainer').height() * 0.7 + 'px');


                var operatorContainer = $("<div class='operatorEventOperatorContainer'><div class='operatorEventLabelContainer centerWithFlex'>operator</div><div class='operatorEventDataContainer centerWithFlex'>" + operatorName.toLowerCase() + "</div></div>");
                operatorContainer.addClass(backgroundFieldsClass);
                dataContainer.append(operatorContainer);

                operatorContainer.find('div.operatorEventDataContainer').css('font-size', operatorContainer.find('div.operatorEventDataContainer').height() * 0.45 + 'px');
                operatorContainer.find('div.operatorEventLabelContainer').css('font-size', operatorContainer.find('div.operatorEventLabelContainer').height() * 0.7 + 'px');

                mapPtrContainer = $("<div class='trafficEventMapPtr'></div>");
                mapPtrContainer.addClass(backgroundFieldsClass);

                pinContainer = $("<div class='trafficEventPinContainer'><a class='trafficEventLink' data-eventstartdate='" + eventStartDate + "' data-eventstarttime='" + eventStartTime + "' data-eventSeverity='" + eventSeverity + "' data-eventType='" + "OperatorEvent" + "' data-eventname='" + eventNameWithCase +
                    "' data-peopleNumber='" + peopleNumber + "' data-operatorName='" + operatorName + "' data-eventlat='" + eventLat + "' data-eventlng='" + eventLng + "' data-background='" + background + "' data-onMap='false'><i class='material-icons'>place</i></a></div>");
                mapPtrContainer.append(pinContainer);
                pinMsgContainer = $("<div class='trafficEventPinMsgContainer'></div>");
                mapPtrContainer.append(pinMsgContainer);

                dataContainer.append(mapPtrContainer);

                newRow.css("margin-bottom", "0px");

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

                    if ($(this).attr("data-onMap") === 'false') {
                        $(this).attr("data-onMap", 'true');
                        goesOnMap = true;
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                        $(this).addClass("onMapTrafficEventPinAnimated");
                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                        //Testing
                        for (var i in widgetPanToTargetList) {
                            $.event.trigger({
                                type: "centerMapForOperatorEvent_" + widgetPanToTargetList[i],
                                eventGenerator: $(this),
                                lat: $(this).attr("data-eventlat"),
                                lng: $(this).attr("data-eventlng")
                            });
                        }
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
                });
                i++;
            }//Fine del for


            var maxTitleFontSize = $('div.eventTitle').height() * 0.75;

            if (maxTitleFontSize > fontSize) {
                maxTitleFontSize = fontSize;
            }

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer p.eventTitlePar span').css("font-size", maxTitleFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .alarmSeverityContainer').css("font-size", maxTitleFontSize + "px");
            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .alarmDateContainer').eq(0).width() * 0.12;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .alarmDateContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .alarmTimeContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventLink i').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 1.5) + "px");

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinMsgContainer').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 2.846) + "px");

            if (!fromSort) {
                var btnIndicatorFontSize = $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").eq(0).width() / 48.4375;
                $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").css("font-size", btnIndicatorFontSize + "em");
            }

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer [data-toggle="tooltip"]').tooltip({
                html: true
            });

            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);

            if (newEventFromWs) {
                setTimeout(function () {
                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onMap", 'true');
                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                    for (var index in widgetTargetList) {
                        addEventToMap($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetTargetList[index], index);
                    }

                    //Testing
                    for (var i in widgetPanToTargetList) {
                        $.event.trigger({
                            type: "centerMapForOperatorEvent_" + widgetPanToTargetList[i],
                            eventGenerator: $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0),
                            lat: $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlat"),
                            lng: $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlng")
                        });
                    }
                }, 500);
            }
        }

        function updateFullscreenPointsList(widgetNameLocal, eventsPointsLocal) {
            var temp = null;
            $("#" + widgetNameLocal + "_driverWidgetType").val("operatorEvents");
            $('#' + widgetNameLocal + '_modalLinkOpen input.fullscreenEventPoint').remove();

            for (var i = 0; i < eventsPointsLocal.length; i++) {
                if ($('#' + widgetNameLocal + '_fullscreenEvent_' + i).length <= 0) {
                    temp = $('<input type="hidden" class="fullscreenEventPoint" data-eventType="resource" id="<?= $_REQUEST['name_w'] ?>_fullscreenEvent_' + i + '"/>');
                    temp.val(eventsPointsLocal[i].join("||"));
                    $('#' + widgetNameLocal + '_modalLinkOpen div.modalLinkOpenBody').append(temp);
                }
            }
        }

        function addEventToMap(eventLink, widgetName, widgetIndex) {
            let passedData = [];
            let coordsAndType = {};

            coordsAndType.lng = eventLink.attr("data-eventlng");//OS vuole le coordinate alla rovescia
            coordsAndType.lat = eventLink.attr("data-eventlat");
            coordsAndType.eventName = eventLink.attr("data-eventname");
            coordsAndType.eventType = "OperatorEvent";
            coordsAndType.eventStartDate = eventLink.attr("data-eventstartdate");
            coordsAndType.eventStartTime = eventLink.attr("data-eventstarttime");
            coordsAndType.eventPeopleNumber = eventLink.attr("data-peopleNumber");
            coordsAndType.eventOperatorName = eventLink.attr("data-operatorName");
            coordsAndType.eventColor = eventLink.attr("data-eventseverity");

            passedData.push(coordsAndType);

            $.event.trigger({
                type: "addOperatorEvent",
                target: widgetName,
                passedData: passedData
            });
        }

        function removeEventFromMap(eventLink, widgetName, widgetIndex) {
            let passedData = [];
            let coordsAndType = {};

            coordsAndType.lng = eventLink.attr("data-eventlng");//OS vuole le coordinate alla rovescia
            coordsAndType.lat = eventLink.attr("data-eventlat");
            coordsAndType.eventName = eventLink.attr("data-eventname");
            coordsAndType.eventType = eventLink.attr("data-eventtype");

            passedData.push(coordsAndType);

            $.event.trigger({
                type: "removeOperatorEvent",
                target: widgetName,
                passedData: passedData
            });

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
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .alarmSeverityContainer').css("font-size", maxTitleFontSize + "px");

            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .alarmDateContainer').eq(0).width() * 0.12;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .alarmDateContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .alarmTimeContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventLink i').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 1.5) + "px");

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinMsgContainer').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 2.846) + "px");

            clearInterval(scroller);
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);
            //scroller = setInterval(stepDownInterval, speed);
        }

        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function (event) {
            showHeader = event.showHeader;
        });
        $(document).on('removeOperatorEventPin', function () {
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

        //Nuova versione
        if (('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "") && ('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "null")) {
            styleParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>');
        }

        if ('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>'.length > 0) {
            widgetParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>');
        }


        $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").css("height", "100%");

        widgetTargetList = widgetParameters.targetEventsJson;
        widgetPanToTargetList = widgetParameters.targetPanToJson;
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
            url: "../management/iframeProxy.php",
            type: "GET",
            data: {
                action: "getOperatorEvents"
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
                    $("#<?= $_REQUEST['name_w'] ?>_noDataAlert").hide();
                    $('#<?= $_REQUEST['name_w'] ?>_buttonsContainer').show();
                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").show();

                    eventsNumber = Object.keys(data).length;
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
                    eventsArray = data.list;

                    eventsArray.sort(function (a, b) {
                        //return b.payload.open_time - a.payload.open_time;
                        var itemA = new Date(a.time);
                        var itemB = new Date(b.time);
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

                    //scroller = setInterval(stepDownInterval, speed);
                    var timeToClearScroll = (timeToReload - 0.5) * 1000;

                    setTimeout(function () {
                        //clearInterval(scroller);
                        //$("#<?= $_REQUEST['name_w'] ?>_rollerContainer").off();

                        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");
                        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html("");

                        //Ripristino delle homepage native per gli widget targets al reload, se pilotati per ultimi da questo widget
                        for (var widgetName in widgetTargetList) {
                            if (($("#" + widgetName + "_driverWidgetType").val() === 'operatorEvents') && (eventsOnMaps[widgetName].eventsNumber > 0)) {
                                loadDefaultMap(widgetName);
                            }
                        }

                    }, timeToClearScroll);


                    //Web socket
                    openWs = function (e) {
                        try {
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
                        catch (e) {
                            wsClosed();
                        }
                    };

                    manageIncomingWsMsg = function (msg) {
                        var msgObj = JSON.parse(msg.data);

                        switch (msgObj.msgType) {
                            case "newNRMetricData":
                                if ((encodeURIComponent(msgObj.metricName) === encodeURIComponent(metricName)) && (msgObj.newValue !== 'Off')) {
                                    clearInterval(countdownRef);

                                    for (var index in eventsOnMaps) {
                                        if (eventsOnMaps[index].mapRef !== null) {
                                            eventsOnMaps[index].mapRef.off();
                                            eventsOnMaps[index].mapRef.remove();
                                            eventsOnMaps[index].mapRef = null;
                                            $("#" + widgetTargetList[index] + "_mapDiv").remove();
                                            $('<div id="' + widgetTargetList[index] + '_mapDiv" class="mapDiv"></div>').insertBefore("#" + widgetTargetList[index] + "_wrapper");
                                            $("#" + widgetTargetList[index] + "_driverWidgetType").val("");
                                            $("#" + widgetTargetList[index] + "_netAnalysisServiceMapUrl").val("");
                                            $("#" + widgetTargetList[index] + "_buttonUrl").val("");
                                            $("#" + widgetTargetList[index] + "_recreativeEventsUrl").val("");
                                            $("#" + widgetTargetList[index] + "_mapDiv").hide();
                                            $("#" + widgetTargetList[index] + "_wrapper").hide();
                                            $("#" + widgetTargetList[index] + "_defaultMapDiv").show();
                                        }
                                    }

                                    setTimeout(function () {
                                        clearInterval(countdownRef);
                                        <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, true);
                                    }, 750);

                                }
                                break;

                            default:
                                break;
                        }
                    };

                    openWsConn = function (e) {
                        var wsRegistration = {
                            msgType: "ClientWidgetRegistration",
                            userType: "widgetInstance",
                            metricName: encodeURIComponent(metricName),
                            widgetUniqueName: "<?= $_REQUEST['name_w'] ?>"
                        };
                        webSocket.send(JSON.stringify(wsRegistration));

                        setTimeout(function () {
                            webSocket.removeEventListener('close', wsClosed);
                            webSocket.removeEventListener('open', openWsConn);
                            webSocket.removeEventListener('message', manageIncomingWsMsg);
                            webSocket.close();
                            webSocket = null;
                        }, (timeToReload - 2) * 1000);

                        webSocket.addEventListener('message', manageIncomingWsMsg);
                    };

                    wsClosed = function (e) {
                        webSocket.removeEventListener('close', wsClosed);
                        webSocket.removeEventListener('open', openWsConn);
                        webSocket.removeEventListener('message', manageIncomingWsMsg);
                        webSocket = null;
                        if (wsRetryActive === 'yes') {
                            setTimeout(openWs, parseInt(wsRetryTime * 1000));
                        }
                    };

                    //Per ora non usata
                    wsError = function (e) {

                    };

                    openWs();
                }
            },
            error: function (data) {
                console.log("Ko");
                console.log(JSON.stringify(data));

                showWidgetContent(widgetName);
                $('#<?= $_REQUEST['name_w'] ?>_buttonsContainer').hide();
                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").hide();
                $("#<?= $_REQUEST['name_w'] ?>_noDataAlert").show();
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
            <div id="<?= $_REQUEST['name_w'] ?>_mainContainer" class="chartContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlert" class="noDataAlert">
                    <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertText" class="noDataAlertText">
                        No data available
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                        <i class="fa fa-times"></i>
                    </div>
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_rollerContainer" class="trafficEventsRollerContainer"></div>
            </div>
        </div>
    </div>
</div>
