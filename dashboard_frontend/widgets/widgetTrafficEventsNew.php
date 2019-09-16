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

<script type='text/javascript' charset="utf-8">
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
            eventLog("Returned the following ERROR in widgetTrafficEventsNew.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
            exit();
        }
        ?>
        var timeFontSize, scroller, widgetProperties, styleParameters, icon, serviceUri,
            eventName, eventType, newRow, newIcon, widgetTargetList, backgroundTitleClass, backgroundFieldsClass,
            background, originalHeaderColor, originalBorderColor, eventTitle, temp, day, month, hour, min, sec,
            eventStart,
            eventName, serviceUri, eventLat, eventLng, eventTooltip, eventStartDate, eventStartTime, eventSeverityDesc,
            eventsNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx, eventSubtype, dataContainer,
            middleContainer, subTypeContainer,
            dateContainer, timeContainer, dateTimeFontSize, severityContainer, mapPtrContainer, pinContainer,
            pinMsgContainer,
            mapPinImg, eventNameWithCase, eventSeverity, typeId, subtypeId, lastPopup, defaultCategory,
            widgetParameters = null;

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
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var eventsArray = [];
        var eventsOnMaps = {};
        var allIncidentsOnMap = false;
        var allRoadWorksOnMap = false;
        var allSnowOnTheRoadsOnMap = false;
        var allWeatherDataOnMap = false;
        var allWindOnMap = false;

        var severitySortState = 0;
        var timeSortState = 0;
        var typeSortState = 0;
        var headerHeight = 25;
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader, countdownRef = null;

        var eventTypes = [];

        console.log("Entrato in Widget Traffic Events New: " + widgetName);

        if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
            showHeader = false;
        }
        else {
            showHeader = true;
        }

        var targetsArrayForNotify = [];

        if (url === "null") {
            url = null;
        }

        timeFontSize = parseInt(fontSize * 1.6);

        //fontSizePin = parseInt(fontSize*0.95);

        $(document).on("esbEventAdded", function (event) {
            if (event.generator !== "<?= $_REQUEST['name_w'] ?>") {
                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").each(function (i) {
                    if ($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap") === "true") {
                        var evtType = null;

                        for (widgetName in widgetTargetList) {
                            evtType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-eventtype");

                            for (var index in widgetTargetList[widgetName]) {
                                if (evtType === widgetTargetList[widgetName][index]) {
                                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap", "false");

                                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).removeClass("onMapTrafficEventPinAnimated");
                                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");

                                    eventsOnMaps[widgetName].eventsNumber = 0;
                                    eventsOnMaps[widgetName].eventsPoints.splice(0);
                                    eventsOnMaps[widgetName].mapRef = null;

                                    severitySortState = 0;
                                    timeSortState = 0;
                                    typeSortState = 0;
                                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("img").attr("src", "../img/trafficIcons/severity.png");
                                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");
                                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("img").attr("src", "../img/trafficIcons/time.png");
                                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html("");
                                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(2).find("img").attr("src", "../img/trafficIcons/type.png");
                                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(2).find("div.trafficEventsButtonIndicator").html("");

                                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "black");
                                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                                    allIncidentsOnMap = false;


                                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "black");
                                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");
                                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                                    allRoadWorksOnMap = false;

                                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "black");
                                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");
                                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                                    allSnowOnTheRoadsOnMap = false;

                                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "black");
                                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");
                                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                                    allWeatherDataOnMap = false;

                                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "black");
                                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");
                                    $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                                    allWindOnMap = false;
                                }
                            }
                        }
                    }
                });
            }
        });

        //Definizioni di funzione
        function populateWidget(fromSort) {
            var i = 0;

            if (!fromSort) {
                var btnIndicatorFontSize = $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").eq(0).width() / 48.4375;
                $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").css("font-size", btnIndicatorFontSize + "em");

                eventsArray.sort(function (a, b) {
                    var itemA = new Date(a.payload.start_time);
                    var itemB = new Date(b.payload.start_time);
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

                timeSortState = 2;

                $("#<?= $_REQUEST['name_w'] ?>_content .trafficEventsButtonIndicator").eq(1).html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
            }

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').empty();

            for (var key in eventsArray) {
                temp = eventsArray[key].payload.start_time;
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

                if (eventsArray[key].payload.hasOwnProperty("notes")) {
                    eventNameWithCase = eventsArray[key].payload.notes.replace(/\./g, "");
                    eventNameWithCase = eventsArray[key].payload.notes.replace(/'/g, "&apos;");
                    eventNameWithCase = eventsArray[key].payload.notes.replace(/\u0027/g, "&apos;");
                    //Queste due istruzioni (anche una sola delle due) fanno troncare il fumetto nella mappa se la stringa contiene solo singoli apostrofi
                    //eventNameWithCase = eventsArray[key].payload.notes.replace(/"/g, "&quot;");
                    //eventNameWithCase = eventsArray[key].payload.notes.replace(/\u0022/g, "&quot;");

                    eventName = eventsArray[key].payload.notes.replace(/\./g, "").toLowerCase();
                    eventName = eventsArray[key].payload.notes.replace(/'/g, "&apos;").toLowerCase();
                    eventName = eventsArray[key].payload.notes.replace(/\u0027/g, "&apos;").toLowerCase();
                }
                else {
                    eventNameWithCase = "Event with no name";
                    eventName = "event with no name";
                }

                eventLat = eventsArray[key].payload.coords.latitude;
                eventLng = eventsArray[key].payload.coords.longitude;
                eventSeverity = eventsArray[key].payload.severity;
                typeId = eventsArray[key].payload.type_id;
                subtypeId = eventsArray[key].payload.sub_type_id;

                newRow = $('<div class="trafficEventRow" data-startTime="' + temp + '"></div>');

                switch (eventSeverity) {
                    case 0:
                    case 1:
                    case 2:
                    case 3://low
                        backgroundTitleClass = "lowSeverityTitle";
                        backgroundFieldsClass = "lowSeverity";//Giallo
                        background = "#ffcc00";
                        eventSeverityDesc = "Low";
                        break;

                    case 4:
                    case 5:
                    case 6://med
                        backgroundTitleClass = "medSeverityTitle";
                        backgroundFieldsClass = "medSeverity";//Arancio
                        background = "#ff9900";
                        eventSeverityDesc = "Med";
                        break;

                    case 7:
                    case 8:
                    case 9:
                    case 10://high
                        backgroundTitleClass = "highSeverityTitle";
                        backgroundFieldsClass = "highSeverity";//Rosso
                        background = "#ff6666";
                        eventSeverityDesc = "High";
                        break;
                }

                eventType = typeId.replace("urn:rixf:it.swarco.resolute/typeId/", "");
                eventSubtype = subtypeId.replace("urn:rixf:it.swarco.resolute/subTypeId/", "");
                icon = $('<img src="../img/trafficIcons/' + trafficEventTypes["type" + eventType].icon + '" />');
                eventTooltip = trafficEventTypes["type" + eventType].desc;

                newRow.css("height", rowPercHeight + "%");
                eventTitle = $('<div class="eventTitle"><p class="eventTitlePar"><span>' + eventName + '</span></p></div>');
                eventTitle.addClass(backgroundTitleClass);

                eventTitle.css("height", "max-content");
                $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').append(newRow);

                newRow.append(eventTitle);

                dataContainer = $('<div class="trafficEventDataContainer"></div>');
                newRow.append(dataContainer);

                newIcon = $("<div class='trafficEventIcon centerWithFlex' data-toggle='tooltip' data-placement='top' title='" + eventTooltip + "'></div>");
                newIcon.append(icon);
                newIcon.addClass(backgroundFieldsClass);
                dataContainer.append(newIcon);

                middleContainer = $("<div class='trafficEventMiddleContainer'></div>");
                subTypeContainer = $("<div class='trafficEventSubTypeContainer'></div>");

                subTypeContainer.html(trafficEventSubTypes["subType" + eventSubtype]);
                subTypeContainer.addClass(backgroundFieldsClass);
                //subTypeFontSize = parseInt(fontSize) + 1;
                //subTypeContainer.css("font-size", subTypeFontSize + "px");

                middleContainer.append(subTypeContainer);

                dateContainer = $("<div class='trafficEventDateContainer centerWithFlex'></div>");

                dateContainer.addClass(backgroundFieldsClass);
                dateContainer.html(eventStartDate);
                middleContainer.append(dateContainer);

                timeContainer = $("<div class='trafficEventTimeContainer centerWithFlex'></div>");
                timeContainer.addClass(backgroundFieldsClass);
                timeContainer.html(eventStartTime);
                middleContainer.append(timeContainer);

                dateTimeFontSize = parseInt(fontSize) - 1;
                dateContainer.css("font-size", dateTimeFontSize + "px");
                timeContainer.css("font-size", dateTimeFontSize + "px");

                severityContainer = $("<div class='trafficEventSeverityContainer centerWithFlex'></div>");
                severityContainer.addClass(backgroundFieldsClass);
                severityContainer.html(eventSeverity);
                middleContainer.append(severityContainer);

                dataContainer.append(middleContainer);

                mapPtrContainer = $("<div class='trafficEventMapPtr'></div>");
                mapPtrContainer.addClass(backgroundFieldsClass);

                pinContainer = $("<div class='trafficEventPinContainer'><a class='trafficEventLink' data-eventstartdate='" + eventStartDate + "' data-eventstarttime='" + eventStartTime + "' data-eventseveritynum='" + eventSeverity + "'  data-eventSeverity='" + eventSeverityDesc + "' data-eventType='" + eventType + "' data-eventsubtype='" + eventSubtype + "' data-eventname='" + eventNameWithCase +
                    "' data-eventlat='" + eventLat + "' data-eventlng='" + eventLng + "' data-background='" + background + "' data-onMap='false'><span><i class='material-icons'>place</i></span></a></div>");
                mapPtrContainer.append(pinContainer);
                pinMsgContainer = $("<div class='trafficEventPinMsgContainer'></div>");
                //pinMsgContainer.css("font-size", fontSizePin + "px");
                mapPtrContainer.append(pinMsgContainer);

                dataContainer.append(mapPtrContainer);

                if (i < (eventsNumber - 1)) {
                    newRow.css("margin-bottom", "4px");
                }
                else {
                    newRow.css("margin-bottom", "0px");
                }

                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);

                //Interazione cross-widget
                pinContainer.find("a.trafficEventLink").hover(
                    function () {
                        var localEventType = $(this).attr("data-eventType");
                        var localBackground = $(this).attr("data-background");

                        originalHeaderColor = {};
                        originalBorderColor = {};

                        for (var widgetName in widgetTargetList) {
                            originalHeaderColor[widgetName] = $("#" + widgetName + "_header").css("background-color");
                            originalBorderColor[widgetName] = $("#" + widgetName).css("border-color");

                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === localEventType) {
                                    if ($(this).attr("data-onMap") === 'false') {
                                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("show");
                                    }
                                    else {
                                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("hide");
                                    }

                                    if ($(this).attr("data-eventType") === localEventType) {
                                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
                                        $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                                    }

                                    $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "white");

                                    $("#" + widgetName + "_header").css("background", localBackground);
                                    $("#" + widgetName).css("border-color", localBackground);
                                }
                                else {
                                    //Caso in cui non ci sono target per l'evento disponibile: per ora ok così, poi raffineremo il comportamento
                                }
                            }
                        }
                    },
                    function () {
                        var localEventType = $(this).attr("data-eventType");
                        for (var widgetName in widgetTargetList) {
                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === localEventType) {
                                    if ($(this).attr("data-onMap") === 'false') {
                                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                                    }
                                    else {
                                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                                    }

                                    if ($(this).attr("data-eventType") === localEventType) {
                                        $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
                                        $(this).find("i.material-icons").removeClass("onMapTrafficEventPinAnimated");
                                    }

                                    $(this).parent().parent().find("div.trafficEventPinMsgContainer").css("color", "black");

                                    $("#" + widgetName + "_header").css("background", originalHeaderColor[widgetName]);
                                    $("#" + widgetName).css("border-color", originalBorderColor[widgetName]);
                                }
                            }
                        }
                    }
                );

                pinContainer.find("a.trafficEventLink").click(function () {
                    var localEventType = $(this).attr("data-eventType");
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

                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                    allIncidentsOnMap = false;


                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                    allRoadWorksOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                    allSnowOnTheRoadsOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                    allWeatherDataOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                    allWindOnMap = false;

                    switch (localEventType) {
                        case "1":
                        case "12":
                            if (allIncidentsOnMap === true) {
                                $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                                $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "black");
                                $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                                allIncidentsOnMap = false;
                            }
                            break;

                        case "25":
                            if (allRoadWorksOnMap === true) {
                                $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");
                                $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "black");
                                $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                                allRoadWorksOnMap = false;
                            }
                            break;

                        case "30":
                            if (allSnowOnTheRoadsOnMap === true) {
                                $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");
                                $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "black");
                                $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                                allSnowOnTheRoadsOnMap = false;
                            }
                            break;

                        case "32":
                            if (allWeatherDataOnMap === true) {
                                $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");
                                $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "black");
                                $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                                allWeatherDataOnMap = false;
                            }
                            break;

                        case "33":
                            if (allWindOnMap === true) {
                                $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");
                                $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "black");
                                $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                                allWindOnMap = false;
                            }
                            break;
                    }

                    targetsArrayForNotify = [];

                    for (var widgetName in widgetTargetList) {
                        for (var key in widgetTargetList[widgetName]) {
                            if (widgetTargetList[widgetName][key] === localEventType) {
                                if (goesOnMap) {
                                    targetsArrayForNotify.push(widgetName);
                                    addEventToMap($(this), widgetName);
                                }
                                else {
                                    removeEventFromMap($(this), widgetName);
                                }
                            }
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
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventSubTypeContainer').css("font-size", maxTitleFontSize + "px");
            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventDateContainer').eq(0).width() * 0.15;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventDateContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventTimeContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventSeverityContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventLink i').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 1.5) + "px");

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinMsgContainer').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 2.846) + "px");

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer [data-toggle="tooltip"]').tooltip({
                html: true
            });
        }

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

        function removeAllEventsFromMap(eventType) {
            for (var widgetName in widgetTargetList) {

                var passedData = [];

                for (var key in widgetTargetList[widgetName]) {
                    if (widgetTargetList[widgetName][key] === eventType) {
                        $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink[data-eventtype=" + eventType + "]").each(function () {
                            var lat = $(this).attr("data-eventlat");
                            var lng = $(this).attr("data-eventlng");
                            var eventType = $(this).attr("data-eventType");
                            var eventName = $(this).attr("data-eventname");

                            var coordsAndType = {};

                            coordsAndType.lng = lng;
                            coordsAndType.lat = lat;
                            coordsAndType.eventType = eventType;
                            coordsAndType.eventName = eventName;

                            passedData.push(coordsAndType);
                        });
                    }
                }
                $.event.trigger({
                    type: "removeTrafficEvent",
                    target: widgetName,
                    passedData: passedData
                });
            }

            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink[data-eventtype=" + eventType + "]").each(function (i) {
                $(this).attr("data-onMap", 'false');
                $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                $(this).removeClass("onMapTrafficEventPinAnimated");
                $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
            });
        }

        function addAllEventsToMap(eventType) {
            for (var widgetName in widgetTargetList) {

                var passedData = [];

                for (var key in widgetTargetList[widgetName]) {
                    if (widgetTargetList[widgetName][key] === eventType) {
                        $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink[data-eventtype=" + eventType + "]").each(function (i) {
                            var lat = $(this).attr("data-eventlat");
                            var lng = $(this).attr("data-eventlng");
                            var eventType = $(this).attr("data-eventType");
                            var eventSubtype = $(this).attr("data-eventsubtype");
                            var eventName = $(this).attr("data-eventname");
                            var eventSeverity = $(this).attr("data-eventSeverity");
                            var eventseveritynum = $(this).attr("data-eventseveritynum");
                            var eventStartDate = $(this).attr("data-eventstartdate");
                            var eventStartTime = $(this).attr("data-eventstarttime");

                            var coordsAndType = {};

                            coordsAndType.lng = lng;
                            coordsAndType.lat = lat;
                            coordsAndType.eventType = eventType;
                            coordsAndType.eventSubtype = eventSubtype;
                            coordsAndType.eventName = eventName;
                            coordsAndType.eventSeverity = eventSeverity;
                            coordsAndType.eventseveritynum = eventseveritynum;
                            coordsAndType.eventStartDate = eventStartDate;
                            coordsAndType.eventStartTime = eventStartTime;

                            passedData.push(coordsAndType);
                            eventTypes.push(eventType);

                        });
                    }
                }
                $.event.trigger({
                    type: "addTrafficEvent",
                    target: widgetName,
                    passedData: passedData
                });
            }
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink[data-eventtype=" + eventType + "]").each(function (i) {
                $(this).attr("data-onMap", 'true');
                $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                $(this).addClass("onMapTrafficEventPinAnimated");
                $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");
            });
        }

        function addEventToMap(eventLink, widgetName) {
            var passedData = [];

            var lat = eventLink.attr("data-eventlat");
            var lng = eventLink.attr("data-eventlng");
            var eventType = eventLink.attr("data-eventType");
            var eventSubtype = eventLink.attr("data-eventsubtype");
            var eventName = eventLink.attr("data-eventname");
            var eventSeverity = eventLink.attr("data-eventSeverity");
            var eventseveritynum = eventLink.attr("data-eventseveritynum");
            var eventStartDate = eventLink.attr("data-eventstartdate");
            var eventStartTime = eventLink.attr("data-eventstarttime");

            var coordsAndType = {};

            coordsAndType.lng = lng;
            coordsAndType.lat = lat;
            coordsAndType.eventType = eventType;
            coordsAndType.eventSubtype = eventSubtype;
            coordsAndType.eventName = eventName;
            coordsAndType.eventSeverity = eventSeverity;
            coordsAndType.eventseveritynum = eventseveritynum;
            coordsAndType.eventStartDate = eventStartDate;
            coordsAndType.eventStartTime = eventStartTime;

            passedData.push(coordsAndType);
            eventTypes.push(eventType);

            $.event.trigger({
                type: "addTrafficEvent",
                target: widgetName,
                passedData: passedData
            });
        }

        function removeEventFromMap(eventLink, widgetName) {
            var passedData = [];
            var coordsAndType = {};

            var lat = eventLink.attr("data-eventlat");
            var lng = eventLink.attr("data-eventlng");
            var eventType = eventLink.attr("data-eventType");
            var eventName = eventLink.attr("data-eventname");

            coordsAndType.lng = lng;
            coordsAndType.lat = lat;
            coordsAndType.eventType = eventType;
            coordsAndType.eventName = eventName;

            passedData.push(coordsAndType);

            $.event.trigger({
                type: "removeTrafficEvent",
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
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventSubTypeContainer').css("font-size", maxTitleFontSize + "px");
            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventDateContainer').eq(0).width() * 0.15;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventDateContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventTimeContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventSeverityContainer').css("font-size", subdataFontSize + "px");
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
        $(document).on('removeTrafficEventPin', function () {
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
        if (('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "") && ('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "null")) {
            styleParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>');
            defaultCategory = styleParameters.defaultCategory;
        }

        if ('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>'.length > 0) {
            widgetParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>');
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
                operation: "getTrafficEvents",
                choosenOption: styleParameters.choosenOption,
                time: styleParameters.time,
                timeUdm: styleParameters.timeUdm,
                events: styleParameters.events
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

                $("#<?= $_REQUEST['name_w'] ?>_noDataAlert").hide();
                $('#<?= $_REQUEST['name_w'] ?>_buttonsContainer').show();
                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").show();

                eventsNumber = Object.keys(data).length;
                widgetWidth = $('#<?= $_REQUEST['name_w'] ?>_div').width();
                shownHeight = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").prop("offsetHeight");
                rowPercHeight = 75 * 100 / shownHeight;
                contentHeightPx = eventsNumber * 100;

                //Inserimento una tantum degli eventi nell'apposito array (per ordinamenti)
                for (var key in data) {
                    var localPayload = JSON.parse(data[key].payload);
                    data[key].payload = localPayload;
                    eventsArray.push(data[key]);
                }

                populateWidget(false);

                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).hover(
                    function () {
                        $(this).css("cursor", "pointer");
                        $(this).css("color", "white");
                        $(this).find("img").attr("src", "../img/trafficIcons/severityWhite.png");

                        switch (severitySortState) {
                            case 0://Crescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                $(this).attr("title", "Sort by severity - Ascending");
                                break;

                            case 1://Decrescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                $(this).attr("title", "Sort by severity - Descending");
                                break;

                            case 2://No sort
                                $(this).find("div.trafficEventsButtonIndicator").html('no sort');
                                $(this).attr("title", "Sort by severity - None");
                                break;
                        }
                    },
                    function () {
                        $(this).css("cursor", "auto");
                        $(this).css("color", "black");
                        $(this).find("img").attr("src", "../img/trafficIcons/severity.png");

                        switch (severitySortState) {
                            case 0://No sort
                                $(this).find("div.trafficEventsButtonIndicator").html('');
                                break;

                            case 1://Crescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                break;

                            case 2://Decrescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                break;
                        }
                    }
                );

                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).hover(
                    function () {
                        $(this).css("cursor", "pointer");
                        $(this).css("color", "white");
                        $(this).find("img").attr("src", "../img/trafficIcons/timeWhite.png");
                        switch (timeSortState) {
                            case 1://Decrescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                $(this).attr("title", "Sort by time - Descending");
                                break;

                            case 2://Crescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                $(this).attr("title", "Sort by time - Ascending");
                                break;
                        }
                    },
                    function () {
                        $(this).css("cursor", "auto");
                        $(this).css("color", "black");
                        $(this).find("img").attr("src", "../img/trafficIcons/time.png");

                        switch (timeSortState) {
                            /*case 0://No sort
                                $(this).find("div.trafficEventsButtonIndicator").html('');
                                break;*/

                            case 1://Crescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                break;

                            case 2://Decrescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                break;
                        }
                    }
                );

                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(2).hover(
                    function () {
                        $(this).css("cursor", "pointer");
                        $(this).css("color", "white");
                        $(this).find("img").attr("src", "../img/trafficIcons/typeWhite.png");
                        switch (typeSortState) {
                            case 0://Crescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                $(this).attr("title", "Sort by type - Ascending");
                                break;

                            case 1://Decrescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                $(this).attr("title", "Sort by type - Descending");
                                break;

                            case 2://No sort
                                $(this).find("div.trafficEventsButtonIndicator").html('no sort');
                                $(this).attr("title", "Sort by type - None");
                                break;
                        }
                    },
                    function () {
                        $(this).css("cursor", "auto");
                        $(this).css("color", "black");
                        $(this).find("img").attr("src", "../img/trafficIcons/type.png");

                        switch (typeSortState) {
                            case 0://No sort
                                $(this).find("div.trafficEventsButtonIndicator").html('');
                                break;

                            case 1://Crescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                                break;

                            case 2://Decrescente verso il basso
                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                                break;
                        }
                    }
                );

                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).click(function () {
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");

                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");

                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");

                    $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");


                    var localEventType = null;
                    var localEventName = null;
                    var localEventLat = null;
                    var localEventLng = null;
                    var localEventStartDate = null;
                    var localEventStartTime = null;
                    var localEventSeverity = null;
                    var localEventSubtype = null;
                    var localEventSeveritynum = null;

                    switch (severitySortState) {
                        case 0:
                            eventsArray.sort(function (a, b) {
                                return a.payload.severity - b.payload.severity;
                            });

                            populateWidget(true);

                            for (var i = 0; i < eventTypes.length; i++) {
                                removeAllEventsFromMap(eventTypes[i]);
                            }

                            //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                            var passedData = [];

                            localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                            localEventSubtype = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventsubtype");
                            localEventName = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventname");
                            localEventLat = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlat");
                            localEventLng = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlng");
                            localEventStartDate = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstartdate");
                            localEventStartTime = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstarttime");
                            localEventSeverity = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventSeverity");
                            localEventSeveritynum = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventseveritynum");

                            var coordsAndType = {};

                            coordsAndType.lng = localEventLng;
                            coordsAndType.lat = localEventLat;
                            coordsAndType.eventType = localEventType;
                            coordsAndType.eventSubtype = localEventSubtype;
                            coordsAndType.eventName = localEventName;
                            coordsAndType.eventSeverity = localEventSeverity;
                            coordsAndType.eventSeveritynum = localEventSeveritynum;
                            coordsAndType.eventStartDate = localEventStartDate;
                            coordsAndType.eventStartTime = localEventStartTime;

                            passedData.push(coordsAndType);
                            eventTypes.push(localEventType);

                            for(var widgetName in widgetTargetList) {
                                for (var key in widgetTargetList[widgetName]) {
                                    if (widgetTargetList[widgetName][key] === localEventType) {
                                        $.event.trigger({
                                            type: "addTrafficEvent",
                                            target: widgetName,
                                            passedData: passedData
                                        });
                                    }
                                }
                            }
                            severitySortState = 1;
                            $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                            break;

                        case 1:
                            severitySortState = 2;
                            eventsArray.sort(function (a, b) {
                                return b.payload.severity - a.payload.severity;
                            });

                            populateWidget(true);
                            for (var i = 0; i < eventTypes.length; i++) {
                                removeAllEventsFromMap(eventTypes[i]);
                            }

                            var passedData = [];

                            localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                            localEventSubtype = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventsubtype");
                            localEventName = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventname");
                            localEventLat = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlat");
                            localEventLng = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlng");
                            localEventStartDate = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstartdate");
                            localEventStartTime = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstarttime");
                            localEventSeverity = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventSeverity");
                            localEventSeveritynum = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventseveritynum");

                            var coordsAndType = {};

                            coordsAndType.lng = localEventLng;
                            coordsAndType.lat = localEventLat;
                            coordsAndType.eventType = localEventType;
                            coordsAndType.eventSubtype = localEventSubtype;
                            coordsAndType.eventName = localEventName;
                            coordsAndType.eventSeverity = localEventSeverity;
                            coordsAndType.eventSeveritynum = localEventSeveritynum;
                            coordsAndType.eventStartDate = localEventStartDate;
                            coordsAndType.eventStartTime = localEventStartTime;

                            passedData.push(coordsAndType);
                            eventTypes.push(localEventType);

                            for(var widgetName in widgetTargetList) {
                                for (var key in widgetTargetList[widgetName]) {
                                    if (widgetTargetList[widgetName][key] === localEventType) {
                                        $.event.trigger({
                                            type: "addTrafficEvent",
                                            target: widgetName,
                                            passedData: passedData
                                        });
                                    }
                                }
                            }

                            $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                            break;

                        case 2:
                            severitySortState = 0;
                            eventsArray.sort(function (a, b) {
                                return a.id - b.id;
                            });

                            populateWidget(true);

                            for (var i = 0; i < eventTypes.length; i++) {
                                removeAllEventsFromMap(eventTypes[i]);
                            }

                            $(this).find("div.trafficEventsButtonIndicator").html('');
                            break;
                    }
                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html('');
                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(2).find("div.trafficEventsButtonIndicator").html('');
                    timeSortState = 0;
                    typeSortState = 0;
                });

                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).click(function () {
                    var localEventType = null;
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");

                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");

                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");

                    $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");

                    var localEventType = null;
                    var localEventName = null;
                    var localEventLat = null;
                    var localEventLng = null;
                    var localEventStartDate = null;
                    var localEventStartTime = null;
                    var localEventSeverity = null;
                    var localEventSubtype = null;
                    var localEventSeveritynum = null;

                    switch (timeSortState) {
                        case 1:
                            eventsArray.sort(function (a, b) {
                                var itemA = new Date(a.payload.start_time);
                                var itemB = new Date(b.payload.start_time);
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

                            for (var i = 0; i < eventTypes.length; i++) {
                                removeAllEventsFromMap(eventTypes[i]);
                            }

                            //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                            var passedData = [];

                            localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                            localEventSubtype = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventsubtype");
                            localEventName = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventname");
                            localEventLat = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlat");
                            localEventLng = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlng");
                            localEventStartDate = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstartdate");
                            localEventStartTime = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstarttime");
                            localEventSeverity = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventSeverity");
                            localEventSeveritynum = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventseveritynum");

                            var coordsAndType = {};

                            coordsAndType.lng = localEventLng;
                            coordsAndType.lat = localEventLat;
                            coordsAndType.eventType = localEventType;
                            coordsAndType.eventSubtype = localEventSubtype;
                            coordsAndType.eventName = localEventName;
                            coordsAndType.eventSeverity = localEventSeverity;
                            coordsAndType.eventSeveritynum = localEventSeveritynum;
                            coordsAndType.eventStartDate = localEventStartDate;
                            coordsAndType.eventStartTime = localEventStartTime;

                            passedData.push(coordsAndType);
                            eventTypes.push(localEventType);

                            for(var widgetName in widgetTargetList) {
                                for (var key in widgetTargetList[widgetName]) {
                                    if (widgetTargetList[widgetName][key] === localEventType) {
                                        $.event.trigger({
                                            type: "addTrafficEvent",
                                            target: widgetName,
                                            passedData: passedData
                                        });
                                    }
                                }
                            }
                            timeSortState = 2;
                            $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                            break;

                        case 2:
                            eventsArray.sort(function (a, b) {
                                var itemA = new Date(a.payload.start_time);
                                var itemB = new Date(b.payload.start_time);
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

                            for (var i = 0; i < eventTypes.length; i++) {
                                removeAllEventsFromMap(eventTypes[i]);
                            }

                            //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                            var passedData = [];

                            localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                            localEventSubtype = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventsubtype");
                            localEventName = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventname");
                            localEventLat = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlat");
                            localEventLng = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlng");
                            localEventStartDate = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstartdate");
                            localEventStartTime = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstarttime");
                            localEventSeverity = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventSeverity");
                            localEventSeveritynum = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventseveritynum");

                            var coordsAndType = {};

                            coordsAndType.lng = localEventLng;
                            coordsAndType.lat = localEventLat;
                            coordsAndType.eventType = localEventType;
                            coordsAndType.eventSubtype = localEventSubtype;
                            coordsAndType.eventName = localEventName;
                            coordsAndType.eventSeverity = localEventSeverity;
                            coordsAndType.eventSeveritynum = localEventSeveritynum;
                            coordsAndType.eventStartDate = localEventStartDate;
                            coordsAndType.eventStartTime = localEventStartTime;

                            passedData.push(coordsAndType);
                            eventTypes.push(localEventType);

                            for(var widgetName in widgetTargetList) {
                                for (var key in widgetTargetList[widgetName]) {
                                    if (widgetTargetList[widgetName][key] === localEventType) {
                                        $.event.trigger({
                                            type: "addTrafficEvent",
                                            target: widgetName,
                                            passedData: passedData
                                        });
                                    }
                                }
                            }

                            timeSortState = 1;
                            $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                            break;
                    }
                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html('');
                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(2).find("div.trafficEventsButtonIndicator").html('');
                    severitySortState = 0;
                    typeSortState = 0;
                });

                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(2).click(function () {
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");

                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");

                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");

                    $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "#000000");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");

                    var localEventType = null;
                    var localEventName = null;
                    var localEventLat = null;
                    var localEventLng = null;
                    var localEventStartDate = null;
                    var localEventStartTime = null;
                    var localEventSeverity = null;
                    var localEventSubtype = null;
                    var localEventSeveritynum = null;

                    switch (typeSortState) {
                        case 0:
                            eventsArray.sort(function (a, b) {
                                var typeIndexA = a.payload.type_id.replace("urn:rixf:it.swarco.resolute/typeId/", "");
                                var typeIndexB = b.payload.type_id.replace("urn:rixf:it.swarco.resolute/typeId/", "");
                                var typeA = trafficEventTypes["type" + typeIndexA].desc.toLowerCase();
                                var typeB = trafficEventTypes["type" + typeIndexB].desc.toLowerCase();
                                if (typeA > typeB) {
                                    return 1;
                                }
                                else {
                                    if (typeA < typeB) {
                                        return -1;
                                    }
                                    else {
                                        return 0;
                                    }
                                }
                            });

                            populateWidget(true);

                            for (var i = 0; i < eventTypes.length; i++) {
                                removeAllEventsFromMap(eventTypes[i]);
                            }

                            //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                            var passedData = [];

                            localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                            localEventSubtype = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventsubtype");
                            localEventName = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventname");
                            localEventLat = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlat");
                            localEventLng = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlng");
                            localEventStartDate = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstartdate");
                            localEventStartTime = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstarttime");
                            localEventSeverity = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventSeverity");
                            localEventSeveritynum = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventseveritynum");

                            var coordsAndType = {};

                            coordsAndType.lng = localEventLng;
                            coordsAndType.lat = localEventLat;
                            coordsAndType.eventType = localEventType;
                            coordsAndType.eventSubtype = localEventSubtype;
                            coordsAndType.eventName = localEventName;
                            coordsAndType.eventSeverity = localEventSeverity;
                            coordsAndType.eventSeveritynum = localEventSeveritynum;
                            coordsAndType.eventStartDate = localEventStartDate;
                            coordsAndType.eventStartTime = localEventStartTime;

                            passedData.push(coordsAndType);
                            eventTypes.push(localEventType);

                            for(var widgetName in widgetTargetList) {
                                for (var key in widgetTargetList[widgetName]) {
                                    if (widgetTargetList[widgetName][key] === localEventType) {
                                        $.event.trigger({
                                            type: "addTrafficEvent",
                                            target: widgetName,
                                            passedData: passedData
                                        });
                                    }
                                }
                            }

                            typeSortState = 1;
                            $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down" style="font-size: 20px"></i>');
                            break;

                        case 1:
                            eventsArray.sort(function (a, b) {
                                var typeIndexA = a.payload.type_id.replace("urn:rixf:it.swarco.resolute/typeId/", "");
                                var typeIndexB = b.payload.type_id.replace("urn:rixf:it.swarco.resolute/typeId/", "");
                                var typeA = trafficEventTypes["type" + typeIndexA].desc.toLowerCase();
                                var typeB = trafficEventTypes["type" + typeIndexB].desc.toLowerCase();
                                if (typeA < typeB) {
                                    return 1;
                                }
                                else {
                                    if (typeA > typeB) {
                                        return -1;
                                    }
                                    else {
                                        return 0;
                                    }
                                }
                            });

                            populateWidget(true);

                            for (var i = 0; i < eventTypes.length; i++) {
                                removeAllEventsFromMap(eventTypes[i]);
                            }


                            //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                            var passedData = [];

                            localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                            localEventSubtype = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventsubtype");
                            localEventName = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventname");
                            localEventLat = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlat");
                            localEventLng = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventlng");
                            localEventStartDate = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstartdate");
                            localEventStartTime = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventstarttime");
                            localEventSeverity = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventSeverity");
                            localEventSeveritynum = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventseveritynum");

                            var coordsAndType = {};

                            coordsAndType.lng = localEventLng;
                            coordsAndType.lat = localEventLat;
                            coordsAndType.eventType = localEventType;
                            coordsAndType.eventSubtype = localEventSubtype;
                            coordsAndType.eventName = localEventName;
                            coordsAndType.eventSeverity = localEventSeverity;
                            coordsAndType.eventSeveritynum = localEventSeveritynum;
                            coordsAndType.eventStartDate = localEventStartDate;
                            coordsAndType.eventStartTime = localEventStartTime;

                            passedData.push(coordsAndType);
                            eventTypes.push(localEventType);

                            for(var widgetName in widgetTargetList) {
                                for (var key in widgetTargetList[widgetName]) {
                                    if (widgetTargetList[widgetName][key] === localEventType) {
                                        $.event.trigger({
                                            type: "addTrafficEvent",
                                            target: widgetName,
                                            passedData: passedData
                                        });
                                    }
                                }
                            }

                            typeSortState = 2;
                            $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up" style="font-size: 20px"></i>');
                            break;

                        case 2:
                            eventsArray.sort(function (a, b) {
                                return a.id - b.id;
                            });

                            populateWidget(true);

                            for (var i = 0; i < eventTypes.length; i++) {
                                removeAllEventsFromMap(eventTypes[i]);
                            }

                            typeSortState = 0;
                            $(this).find("div.trafficEventsButtonIndicator").html('');
                            break;
                    }
                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html('');
                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html('');
                    severitySortState = 0;
                    timeSortState = 0;
                });


                //Bottoni per categoria
                $("#<?= $_REQUEST['name_w'] ?>_incidentsButton").hover(
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crashWhite.png");
                        $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").css("cursor", "pointer");

                        originalHeaderColor = {};
                        originalBorderColor = {};

                        for (var widgetName in widgetTargetList) {
                            originalHeaderColor[widgetName] = $("#" + widgetName + "_header").css("background-color");
                            originalBorderColor[widgetName] = $("#" + widgetName).css("border-color");

                            for (var key in widgetTargetList[widgetName]) {
                                if ((widgetTargetList[widgetName][key] === "1") || (widgetTargetList[widgetName][key] === "12")) {
                                    $("#" + widgetName + "_header").css("background", "#ff6666");
                                    $("#" + widgetName).css("border-color", "#ff6666");
                                }
                            }
                        }//Fine del for

                        if (allIncidentsOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("show");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("hide");
                        }
                    },
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                        $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").css("cursor", "auto");

                        for (var widgetName in widgetTargetList) {
                            for (var key in widgetTargetList[widgetName]) {
                                if ((widgetTargetList[widgetName][key] === "1") || (widgetTargetList[widgetName][key] === "12")) {
                                    $("#" + widgetName + "_header").css("background", originalHeaderColor[widgetName]);
                                    $("#" + widgetName).css("border-color", originalBorderColor[widgetName]);
                                }
                            }
                        }

                        if (allIncidentsOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "#000000");
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crashWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("map");
                        }
                    });

                $("#<?= $_REQUEST['name_w'] ?>_incidentsButton").click(function () {
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                    allRoadWorksOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                    allSnowOnTheRoadsOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                    allWeatherDataOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                    allWindOnMap = false;


                    if (allIncidentsOnMap === false) {
                        addAllEventsToMap("1");
                        allIncidentsOnMap = true;
                    }
                    else {
                        removeAllEventsFromMap("1");
                        allIncidentsOnMap = false;
                    }
                });

                $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton").hover(
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorksWhite.png");
                        $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").css("cursor", "pointer");

                        originalHeaderColor = {};
                        originalBorderColor = {};

                        for (var widgetName in widgetTargetList) {
                            originalHeaderColor[widgetName] = $("#" + widgetName + "_header").css("background-color");
                            originalBorderColor[widgetName] = $("#" + widgetName).css("border-color");

                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === "25") {
                                    $("#" + widgetName + "_header").css("background", "#ff6666");
                                    $("#" + widgetName).css("border-color", "#ff6666");
                                }
                            }
                        }//Fine del for

                        if (allRoadWorksOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("show");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("hide");
                        }
                    },
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                        $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").css("cursor", "auto");

                        for (var widgetName in widgetTargetList) {
                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === "25") {
                                    $("#" + widgetName + "_header").css("background", originalHeaderColor[widgetName]);
                                    $("#" + widgetName).css("border-color", originalBorderColor[widgetName]);
                                }
                            }
                        }

                        if (allRoadWorksOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "#000000");
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorksWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("map");
                        }
                    });

                $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton").click(function () {
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                    allIncidentsOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                    allSnowOnTheRoadsOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                    allWeatherDataOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                    allWindOnMap = false;

                    if (allRoadWorksOnMap === false) {
                        addAllEventsToMap("25");
                        allRoadWorksOnMap = true;
                    }
                    else {
                        removeAllEventsFromMap("25");
                        allRoadWorksOnMap = false;
                    }
                });

                $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton").hover(
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snowWhite.png");
                        $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").css("cursor", "pointer");

                        originalHeaderColor = {};
                        originalBorderColor = {};

                        for (var widgetName in widgetTargetList) {
                            originalHeaderColor[widgetName] = $("#" + widgetName + "_header").css("background-color");
                            originalBorderColor[widgetName] = $("#" + widgetName).css("border-color");

                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === "30") {
                                    $("#" + widgetName + "_header").css("background", "#ff6666");
                                    $("#" + widgetName).css("border-color", "#ff6666");
                                }
                            }
                        }//Fine del for

                        if (allSnowOnTheRoadsOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("show");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("hide");
                        }
                    },
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                        $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").css("cursor", "auto");

                        for (var widgetName in widgetTargetList) {
                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === "30") {
                                    $("#" + widgetName + "_header").css("background", originalHeaderColor[widgetName]);
                                    $("#" + widgetName).css("border-color", originalBorderColor[widgetName]);
                                }
                            }
                        }

                        if (allSnowOnTheRoadsOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "#000000");
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snowWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("map");
                        }
                    });

                $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton").click(function () {
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                    allIncidentsOnMap = false;


                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                    allRoadWorksOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                    allWeatherDataOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                    allWindOnMap = false;

                    if (allSnowOnTheRoadsOnMap === false) {
                        addAllEventsToMap("30");
                        allSnowOnTheRoadsOnMap = true;
                    }
                    else {
                        removeAllEventsFromMap("30");
                        allSnowOnTheRoadsOnMap = false;
                    }
                });

                $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton").hover(
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherDataWhite.png");
                        $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").css("cursor", "pointer");

                        originalHeaderColor = {};
                        originalBorderColor = {};

                        for (var widgetName in widgetTargetList) {
                            originalHeaderColor[widgetName] = $("#" + widgetName + "_header").css("background-color");
                            originalBorderColor[widgetName] = $("#" + widgetName).css("border-color");

                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === "32") {
                                    $("#" + widgetName + "_header").css("background", "#ff6666");
                                    $("#" + widgetName).css("border-color", "#ff6666");
                                }
                            }
                        }//Fine del for

                        if (allWeatherDataOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("show");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("hide");
                        }
                    },
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                        $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").css("cursor", "auto");

                        for (var widgetName in widgetTargetList) {
                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === "32") {
                                    $("#" + widgetName + "_header").css("background", originalHeaderColor[widgetName]);
                                    $("#" + widgetName).css("border-color", originalBorderColor[widgetName]);
                                }
                            }
                        }

                        if (allWeatherDataOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "#000000");
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherDataWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("map");
                        }
                    });

                $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton").click(function () {
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                    allIncidentsOnMap = false;


                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                    allRoadWorksOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                    allSnowOnTheRoadsOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                    allWindOnMap = false;

                    if (allWeatherDataOnMap === false) {
                        addAllEventsToMap("32");
                        allWeatherDataOnMap = true;
                    }
                    else {
                        removeAllEventsFromMap("32");
                        allWeatherDataOnMap = false;
                    }
                });

                $("#<?= $_REQUEST['name_w'] ?>_windButton").hover(
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/windWhite.png");
                        $("#<?= $_REQUEST['name_w'] ?>_windButton img").css("cursor", "pointer");

                        originalHeaderColor = {};
                        originalBorderColor = {};

                        for (var widgetName in widgetTargetList) {
                            originalHeaderColor[widgetName] = $("#" + widgetName + "_header").css("background-color");
                            originalBorderColor[widgetName] = $("#" + widgetName).css("border-color");

                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === "33") {
                                    $("#" + widgetName + "_header").css("background", "#ff6666");
                                    $("#" + widgetName).css("border-color", "#ff6666");
                                }
                            }
                        }//Fine del for

                        if (allWindOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("show");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("hide");
                        }
                    },
                    function () {
                        $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                        $("#<?= $_REQUEST['name_w'] ?>_windButton img").css("cursor", "auto");

                        for (var widgetName in widgetTargetList) {
                            for (var key in widgetTargetList[widgetName]) {
                                if (widgetTargetList[widgetName][key] === "32") {
                                    $("#" + widgetName + "_header").css("background", originalHeaderColor[widgetName]);
                                    $("#" + widgetName).css("border-color", originalBorderColor[widgetName]);
                                }
                            }
                        }

                        if (allWindOnMap === false) {
                            $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/wind.png");
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "#000000");
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("");
                        }
                        else {
                            $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/windWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("map");
                        }
                    });

                $("#<?= $_REQUEST['name_w'] ?>_windButton").click(function () {
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crash.png");
                    allIncidentsOnMap = false;


                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorks.png");
                    allRoadWorksOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snow.png");
                    allSnowOnTheRoadsOnMap = false;

                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "black");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherData.png");
                    allWeatherDataOnMap = false;

                    if (allWindOnMap === false) {
                        addAllEventsToMap("33");
                        allWindOnMap = true;
                    }
                    else {
                        removeAllEventsFromMap("33");
                        allWindOnMap = false;
                    }
                });

                setTimeout(function () {
                    switch (defaultCategory) {
                        case "incident":
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton img").attr("src", "../img/trafficIcons/crashWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton div.trafficEventsButtonIndicator").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_incidentsButton").trigger("click");
                            break;

                        case "roadWorks":
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton img").attr("src", "../img/trafficIcons/roadWorksWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton div.trafficEventsButtonIndicator").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_roadOnWorksButton").trigger("click");
                            break;

                        case "snow":
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton img").attr("src", "../img/trafficIcons/snowWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton div.trafficEventsButtonIndicator").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton").trigger("click");
                            break;

                        case "weatherData":
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton img").attr("src", "../img/trafficIcons/weatherDataWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton div.trafficEventsButtonIndicator").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_weatherDataButton").trigger("click");
                            break;

                        case "wind":
                            $("#<?= $_REQUEST['name_w'] ?>_windButton img").attr("src", "../img/trafficIcons/windWhite.png");
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").css("color", "#FFFFFF");
                            $("#<?= $_REQUEST['name_w'] ?>_windButton div.trafficEventsButtonIndicator").html("map");
                            $("#<?= $_REQUEST['name_w'] ?>_windButton").trigger("click");
                            break;

                        default:
                            break;
                    }
                }, parseInt("<?php echo $crossWidgetDefaultLoadWaitTime; ?>"));

                scroller = setInterval(stepDownInterval, speed);
                var timeToClearScroll = (timeToReload - 0.5) * 1000;

                setTimeout(function () {
                    clearInterval(scroller);
                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").off();

                    //$(document).off("esbEventAdded");

                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html("");
                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(2).find("div.trafficEventsButtonIndicator").html("");

                    //Ripristino delle homepage native per gli widget targets al reload
                    for (var widgetName in widgetTargetList) {
                        if ($("#" + widgetName + "_driverWidgetType").val() === 'trafficEvents') {
                            loadDefaultMap(widgetName);
                        }
                        else {
                            //console.log("Attualmente non pilotato da trafficEvents");
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
                <div id="<?= $_REQUEST['name_w'] ?>_buttonsContainer" class="trafficEventsButtonsContainer">
                    <div class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/severity.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                    <div class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/time.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                    <div class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/type.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_incidentsButton" class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/crash.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_roadOnWorksButton" class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/roadWorks.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_snowOnTheRoadsButton" class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/snow.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_weatherDataButton" class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/weatherData.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_windButton" class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/wind.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_rollerContainer" class="trafficEventsRollerContainer"></div>
            </div>
        </div>
    </div>
</div> 