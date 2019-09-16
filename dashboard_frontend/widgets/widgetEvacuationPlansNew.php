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
            eventLog("Returned the following ERROR in widgetEvacuationPlansNew.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
            exit();
        }
        ?>

        var intervalRef, timeFontSize, scroller, widgetProperties, styleParameters, icon, serviceUri,
            planName, newRow, eventContentW, test, widgetTargetList, backgroundTitleClass, backgroundFieldsClass,
            background, originalHeaderColor, originalBorderColor, planTitle, temp, day, month, hour, min, sec,
            planStart,
            planName, serviceUri, planStartDate, planStartTime, widgetParameters,
            plansNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx, eventContentWPerc, dataContainer,
            dateContainer, dateTimeFontSize, mapPtrContainer, pinContainer, pinMsgContainer,
            fontSizePin, dateFontSize, mapPinImg, planId, pathsContainer, paths, statusContainer, status,
            detailsContainer, decisionContainer,
            decisionPtrContainer, decisionMsgContainer, statusOnPin, lastPopup = null;

        var planNames = new Array();
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
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var showHeader = null;

        var plansObj = {};
        var plansOrderedIds = [];
        var plansOnMaps = {};

        var timeSortState = 0;
        var decisionSortState = 0;

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

        $(document).on("esbEventAdded", function (event) {
            if (event.generator !== "<?= $_REQUEST['name_w'] ?>") {
                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").each(function (i) {
                    if ($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap") === "true") {
                        var evtType = null;
                        for (widgetName in widgetTargetList) {
                            evtType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-eventtype");
                            if ($.inArray(evtType, widgetTargetList[widgetName])) {
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).attr("data-onmap", "false");

                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).removeClass("onMapTrafficEventPinAnimated");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(i).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");

                                plansOnMaps[widgetName].shownPolyGroup = null;
                                plansOnMaps[widgetName].mapRef = null;

                                decisionSortState = 0;
                                timeSortState = 0;

                                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("img").attr("src", "../img/trafficIcons/time.png");
                                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");
                                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("img").attr("src", "../img/planIcons/decision.png");
                                $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html("");
                            }
                        }
                    }
                });
            }
        });

        $(document).on('removeEvacuationPlanPin', function () {
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").each(function () {
                $(this).attr("data-onMap", 'false');
                $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                $(this).removeClass("onMapTrafficEventPinAnimated");
                $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");
            });
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

            for (var z = 0; z < plansOrderedIds.length; z++) {
                key = plansOrderedIds[z];
                temp = plansObj[key].event_time;
                planStart = new Date(temp);

                day = planStart.getDate();
                if (day < 10) {
                    day = "0" + day.toString();
                }

                month = planStart.getMonth() + 1;
                if (month < 10) {
                    month = "0" + month.toString();
                }

                hour = planStart.getHours();
                if (hour < 10) {
                    hour = "0" + hour.toString();
                }

                min = planStart.getMinutes();
                if (min < 10) {
                    min = "0" + min.toString();
                }

                sec = planStart.getSeconds();
                if (sec < 10) {
                    sec = "0" + sec.toString();
                }

                planStartDate = day + "/" + month + "/" + planStart.getFullYear().toString();
                planStartTime = hour + ":" + min + ":" + sec;

                planName = "PLAN N." + plansObj[key].data_id.replace("urn:rixf:gr.certh/evacuationplan_ID/", "").toLowerCase();

                if ((plansObj[key].lastStatus === "") || (plansObj[key].lastStatus === "null") || (plansObj[key].lastStatus === null) || (plansObj[key].lastStatus === undefined)) {
                    plansObj[key].lastStatus = "PROPOSED";
                }

                status = plansObj[key].lastStatus;

                paths = plansObj[key].payload.evacuation_paths.length;
                planId = key;

                newRow = $('<div class="trafficEventRow"></div>');

                switch (status) {
                    case "PROPOSED":
                        backgroundTitleClass = "planProposedTitle";
                        backgroundFieldsClass = "planProposed";//Giallo
                        background = "#ffcc00";
                        break;

                    case "IN_PROGRESS":
                        backgroundTitleClass = "planProgressTitle";
                        backgroundFieldsClass = "planProgress";//Arancio
                        background = "#ff9900";
                        break;

                    case "APPROVED":
                        backgroundTitleClass = "planApprovedTitle";
                        backgroundFieldsClass = "planApproved";//Verde
                        background = "#80ff80";
                        break;

                    case "REJECTED":
                        backgroundTitleClass = "planRejectedTitle";
                        backgroundFieldsClass = "planRejected";//Rosso
                        background = "#ff6666";
                        break;

                    case "CLOSED":
                        backgroundTitleClass = "planClosedTitle";
                        backgroundFieldsClass = "planClosed";//Grigio
                        background = "#e6e6e6";
                        break;
                }

                newRow.css("height", rowPercHeight + "%");
                planTitle = $('<div class="eventTitle"><p class="eventTitlePar"><span>' + planName + '</span></p></div>');
                planTitle.addClass(backgroundTitleClass);
                planTitle.css("height", "30%");
                $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').append(newRow);

                newRow.append(planTitle);

                dataContainer = $('<div class="trafficEventDataContainer"></div>');
                newRow.append(dataContainer);

                detailsContainer = $('<div class="planDetailsContainer"></div>');

                statusContainer = $("<div class='planStatusContainer centerWithFlex'></div>");
                statusContainer.addClass(backgroundFieldsClass);
                statusContainer.html(status);
                detailsContainer.append(statusContainer);

                dateContainer = $("<div class='planDateContainer centerWithFlex'></div>");
                dateContainer.addClass(backgroundFieldsClass);
                dateContainer.html(planStartDate + " - " + planStartTime);
                detailsContainer.append(dateContainer);

                pathsContainer = $("<div class='planPathNumberContainer centerWithFlex'></div>");
                pathsContainer.addClass(backgroundFieldsClass);
                pathsContainer.html("PATHS: " + paths);
                detailsContainer.append(pathsContainer);

                dataContainer.append(detailsContainer);

                mapPtrContainer = $("<div class='trafficEventMapPtr'></div>");
                mapPtrContainer.addClass(backgroundFieldsClass);

                if (status.includes("progress")) {
                    statusOnPin = "inProgress";
                }
                else {
                    statusOnPin = status.toLowerCase();
                }


                pinContainer = $("<div class='trafficEventPinContainer'><a class='trafficEventLink' data-eventType='" + statusOnPin + "' data-eventstartdate='" + planStartDate + "' data-eventstarttime='" + planStartTime + "'" +
                    "data-background='" + background + "' data-onMap='false' data-planid='" + planId + "'><i class='material-icons'>place</i></a></div>");
                mapPtrContainer.append(pinContainer);
                pinMsgContainer = $("<div class='trafficEventPinMsgContainer'></div>");
                //pinMsgContainer.css("font-size", fontSizePin + "px");
                mapPtrContainer.append(pinMsgContainer);

                dataContainer.append(mapPtrContainer);


                decisionPtrContainer = $("<div class='trafficEventMapPtr'></div>");
                decisionPtrContainer.addClass(backgroundFieldsClass);
                decisionContainer = $("<div class='planDecisionContainer'><a class='planDecisionLink' data-background='" + background + "' data-planstatus='" + status + "' data-planid='" + planId + "'><img src='../img/planIcons/decision.png'/></a></div>");
                decisionPtrContainer.append(decisionContainer);
                decisionMsgContainer = $("<div class='decisionPinMsgContainer'></div>");
                //decisionMsgContainer.css("font-size", fontSizePin + "px");
                decisionPtrContainer.append(decisionMsgContainer);

                dataContainer.append(decisionPtrContainer);

                if (i < (plansNumber - 1)) {
                    newRow.css("margin-bottom", "4px");
                }
                else {
                    newRow.css("margin-bottom", "0px");
                }

                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);


                decisionContainer.find("a.planDecisionLink").hover(
                    function () {
                        if (status !== "CLOSED") {
                            $(this).css("cursor", "pointer");
                            $(this).find("img").attr("src", "../img/planIcons/decisionWhite.png");
                            $(this).parent().parent().find("div.decisionPinMsgContainer").html("decide");
                            $(this).parent().parent().find("div.decisionPinMsgContainer").css("color", "white");
                        }
                    },
                    function () {
                        if (status !== "CLOSED") {
                            $(this).css("cursor", "auto");
                            $(this).find("img").attr("src", "../img/planIcons/decision.png");
                            $(this).parent().parent().find("div.decisionPinMsgContainer").html("");
                            $(this).parent().parent().find("div.decisionPinMsgContainer").css("color", "black");
                        }
                    }
                );

                decisionContainer.find("a.planDecisionLink").click(
                    function () {
                        if ($(this).attr("data-planstatus") !== "CLOSED") {
                            //Freeze del widget, per evitare problemi quando il refresh intercorre prima di chiudere il modale
                            clearInterval(intervalRef);
                            clearInterval(scroller);
                            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").off();

                            $("#modalChangePlanStatusPlanId").val($(this).attr("data-planid"));
                            $("#modalChangePlanStatusCurrentStatus").val($(this).attr("data-planstatus"));
                            $("#modalChangePlanStatusTitle").html($(this).attr("data-planid"));
                            $("#modalChangePlanStatusStatus").html($(this).attr("data-planstatus"));
                            $("#modalChangePlanStatusStatus").css("background-color", $(this).attr("data-background"));

                            $("#modalChangePlanStatusSelect").empty();

                            switch ($(this).attr("data-planstatus")) {
                                case "PROPOSED":
                                    $("#modalChangePlanStatusSelect").append('<option value="IN_PROGRESS">in progress</option>');
                                    $("#modalChangePlanStatusSelect").append('<option value="APPROVED">approved</option>');
                                    $("#modalChangePlanStatusSelect").append('<option value="REJECTED">rejected</option>');
                                    break;

                                case "IN_PROGRESS":
                                    $("#modalChangePlanStatusSelect").append('<option value="APPROVED">approved</option>');
                                    $("#modalChangePlanStatusSelect").append('<option value="REJECTED">rejected</option>');
                                    break;

                                case "APPROVED":
                                case "REJECTED":
                                    $("#modalChangePlanStatusSelect").append('<option value="CLOSED">closed</option>');
                                    break;
                            }
                            $("#modalChangePlanStatusOk").hide();
                            $("#modalChangePlanStatusKo").hide();
                            $("#modalChangePlanStatusMain").show();
                            $("#modalChangePlanStatusFooter").show();
                            $("#modalChangePlanStatus").modal('show');
                        }
                    }
                );

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

                    for (var widgetName in widgetTargetList) {
                        for (var key in widgetTargetList[widgetName]) {
                            if (widgetTargetList[widgetName][key] === localEventType) {
                                if ($(this).attr("data-onMap") === 'false') {
                                    $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventPinMsgContainer").each(function () {
                                        $(this).html("");
                                    });

                                    $("#<?= $_REQUEST['name_w'] ?>_div a.trafficEventLink").each(function () {
                                        $(this).removeClass("onMapTrafficEventPinAnimated");
                                        $(this).attr("data-onMap", 'false');
                                    });

                                    $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventPinMsgContainer").each(function () {
                                        $(this).removeClass("onMapTrafficEventPinAnimated");
                                    });

                                    $(this).attr("data-onMap", 'true');
                                    $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                                    $(this).addClass("onMapTrafficEventPinAnimated");
                                    $(this).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                                    removeAllEventsFromMaps(widgetName);

                                    addEventToMap($(this), widgetName);
                                }
                                else {
                                    $(this).attr("data-onMap", 'false');
                                    $(this).parent().parent().find("div.trafficEventPinMsgContainer").html("");
                                    $(this).removeClass("onMapTrafficEventPinAnimated");
                                    $(this).parent().parent().find("div.trafficEventPinMsgContainer").removeClass("onMapTrafficEventPinAnimated");

                                    removeAllEventsFromMaps(widgetName);
                                }
                            }
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
            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .planDateContainer').eq(0).width() * 0.075;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .planStatusContainer ').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .planDateContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .planPathNumberContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventLink i').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 1.5) + "px");

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinMsgContainer').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 2.846) + "px");

            if (!fromSort) {
                var btnIndicatorFontSize = $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").eq(0).width() / 48.4375;
                $("#<?= $_REQUEST['name_w'] ?>_div div.trafficEventsButtonIndicator").css("font-size", btnIndicatorFontSize + "em");
            }
        }

        function removeAllEventsFromMaps(widgetName) {
                $.event.trigger({
                    type: "removeEvacuationPlans",
                    target: widgetName
                });
        }

        function addEventToMap(eventLink, widgetName) {
            let passedData = [];

            let planId = eventLink.attr("data-planid");
            let colors = ['red', 'blue', 'orange', 'green', 'yellow', 'black'];


            let coordsAndType = {};

            coordsAndType.plansObj = plansObj;
            coordsAndType.planId = planId;
            coordsAndType.colors = colors;
            coordsAndType.eventType = "evacuationPlan";

            passedData.push(coordsAndType);

            $.event.trigger({
                type: "addEvacuationPlan",
                target: widgetName,
                passedData: passedData
            });
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
            var subdataFontSize = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .planDateContainer').eq(0).width() * 0.075;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .planStatusContainer ').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .planDateContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .planPathNumberContainer').css("font-size", subdataFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventLink i').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 1.5) + "px");

            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinMsgContainer').css("font-size", parseFloat($('#<?= $_REQUEST['name_w'] ?>_rollerContainer .trafficEventPinContainer').eq(0).width() / 2.846) + "px");

            clearInterval(scroller);
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").scrollTop(0);
            scroller = setInterval(stepDownInterval, speed);
        }

        //Fine definizioni di funzione

        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer").css("background-color", $("#<?= $_REQUEST['name_w'] ?>_header").css("background-color"));
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        if (firstLoad === false) {
            showWidgetContent(widgetName);
        }
        else {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }

        //addLink(widgetName, url, linkElement, divContainer);
        //widgetProperties = getWidgetProperties(widgetName);

        //Nuova versione
        if (('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "") && ('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>' !== "null")) {
            styleParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['styleParameters']) ?>');
        }

        if ('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>'.length > 0) {
            widgetParameters = JSON.parse('<?= sanitizeJsonRelaxed2($_REQUEST['parameters']) ?>');
        }

        widgetTargetList = widgetParameters;

        for (var name in widgetTargetList) {
            plansOnMaps[name] = {
                noPointsUrl: null,
                shownPolyGroup: null,
                mapRef: null
            };
        }

        $.ajax({
            url: "../widgets/esbDao.php",
            type: "POST",
            data: {
                operation: "getEvacuationPlans"
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

                plansNumber = Object.keys(data).length;

                if (plansNumber === 0) {
                    $("#<?= $_REQUEST['name_w'] ?>_table").css("display", "none");
                    $("#<?= $_REQUEST['name_w'] ?>_noDataAlert").css("display", "block");
                }
                else {
                    widgetWidth = $('#<?= $_REQUEST['name_w'] ?>_div').width();
                    shownHeight = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").prop("offsetHeight");
                    rowPercHeight = 75 * 100 / shownHeight;
                    contentHeightPx = plansNumber * 100;
                    eventContentWPerc = null;

                    if (contentHeightPx > shownHeight) {
                        eventContentW = parseInt(widgetWidth - 45 - 22);
                    }
                    else {
                        eventContentW = parseInt(widgetWidth - 45 - 5);
                    }

                    eventContentWPerc = Math.floor(eventContentW / widgetWidth * 100);

                    //Inserimento una tantum degli eventi nell'apposito oggetto
                    var dataId = null;
                    for (var key in data) {
                        var localPayload = JSON.parse(data[key].originalPayload);
                        data[key].payload = localPayload;
                        delete data[key].originalPayload;

                        dataId = data[key].data_id.replace("urn:rixf:gr.certh/evacuationplan_ID/", "");

                        if ((data[key].lastStatus === "") || (data[key].lastStatus === "null") || (data[key].lastStatus === null) || (data[key].lastStatus === undefined)) {
                            data[key].lastStatus = "PROPOSED";
                        }

                        plansObj[dataId] = data[key];

                        plansOrderedIds.push(dataId);
                    }

                    plansOrderedIds.sort(function (a, b) {
                        var itemA = new Date(plansObj[a].event_time);
                        var itemB = new Date(plansObj[b].event_time);
                        if (itemA > itemB) {
                            return -1;
                        }
                        else {
                            if (itemA < itemB) {
                                return 1;
                            }
                            else {
                                return 0;
                            }
                        }
                    });

                    populateWidget(false);

                    //Abort evacuation plan status update
                    $("#modalChangePlanStatusCloseBtn").click(function () {
                        $("#modalChangePlanStatusConfirmBtn").off("click");
                        $("#modalChangePlanStatusCancelBtn").off("click");
                        $("#modalChangePlanStatusCloseBtn").off("click");
                        <?= $_REQUEST['name_w'] ?>(false);
                        $("#modalChangePlanStatus").modal('hide');
                    });

                    $("#modalChangePlanStatusCancelBtn").click(function () {
                        $("#modalChangePlanStatusConfirmBtn").off("click");
                        $("#modalChangePlanStatusCancelBtn").off("click");
                        $("#modalChangePlanStatusCloseBtn").off("click");
                        <?= $_REQUEST['name_w'] ?>(false);
                        $("#modalChangePlanStatus").modal('hide');
                    });

                    //Update evacuation plan status
                    $("#modalChangePlanStatusConfirmBtn").off("click");
                    $("#modalChangePlanStatusConfirmBtn").click(function () {
                        $("#modalChangePlanStatusMain").hide();
                        $("#modalChangePlanStatusFooter").hide();
                        $("#modalChangePlanStatusWait").show();

                        var newStatus = $("#modalChangePlanStatusSelect").val();
                        var planId = $("#modalChangePlanStatusPlanId").val();

                        $.ajax({
                            url: "https://www.resolute-eu.org/cxf/resolute/commands/plan/status/?consumerId=urn:rixf:org.disit/dashboard_manager&evacuationPlanId=urn:rixf:gr.certh/evacuationplan_ID/" + planId + "&status=" + newStatus,
                            type: "POST",
                            //contentType: 'application/json',
                            async: true,
                            success: function (result) {
                                clearInterval(intervalRef);
                                clearInterval(scroller);
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").off();
                                $("#modalChangePlanStatusWait").hide();
                                $("#modalChangePlanStatusOk").show();

                                setTimeout(function () {
                                    $("#modalChangePlanStatus").modal('hide');
                                    $("#modalChangePlanStatusConfirmBtn").off("click");
                                    $("#modalChangePlanStatusCancelBtn").off("click");
                                    $("#modalChangePlanStatusCloseBtn").off("click");

                                    <?= $_REQUEST['name_w'] ?>(false);
                                }, 2000);
                            },
                            error: function (result) {
                                console.log("KO");
                                console.log(JSON.stringify(result));

                                $("#modalChangePlanStatusWait").hide();
                                $("#modalChangePlanStatusKo").show();

                                setTimeout(function () {
                                    $("#modalChangePlanStatus").modal('hide');
                                    <?= $_REQUEST['name_w'] ?>(false);
                                }, 3000);
                            }
                        });

                    });

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

                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).hover(
                        function () {
                            $(this).css("cursor", "pointer");
                            $(this).css("color", "white");
                            $(this).find("img").attr("src", "../img/planIcons/decisionWhite.png");
                            switch (decisionSortState) {
                                case 0://Crescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down"></i>');
                                    $(this).attr("title", "Sort by status - Ascending");
                                    break;

                                case 1://Decrescente verso il basso
                                    $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up"></i>');
                                    $(this).attr("title", "Sort by status - Descending");
                                    break;

                                case 2://No sort
                                    $(this).find("div.trafficEventsButtonIndicator").html('no sort');
                                    $(this).attr("title", "Sort by status - None");
                                    break;
                            }
                        },
                        function () {
                            $(this).css("cursor", "auto");
                            $(this).css("color", "black");
                            $(this).find("img").attr("src", "../img/planIcons/decision.png");

                            switch (decisionSortState) {
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
                                timeSortState = 1;
                                plansOrderedIds.sort(function (a, b) {
                                    var itemA = new Date(plansObj[a].event_time);
                                    var itemB = new Date(plansObj[b].event_time);
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

                                //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                                for (var widgetName in widgetTargetList) {
                                    for (var key in widgetTargetList[widgetName]) {
                                        if (widgetTargetList[widgetName][key] === localEventType) {
                                            removeAllEventsFromMaps(widgetName);
                                            $("#" + widgetName + "_wrapper").hide();
                                            $("#" + widgetName + "_mapDiv").show();
                                            addEventToMap($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetName);
                                        }
                                    }
                                }

                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-down"></i>');
                                break;

                            case 1:
                                timeSortState = 2;
                                plansOrderedIds.sort(function (a, b) {
                                    var itemA = new Date(plansObj[a].event_time);
                                    var itemB = new Date(plansObj[b].event_time);
                                    if (itemA > itemB) {
                                        return -1;
                                    }
                                    else {
                                        if (itemA < itemB) {
                                            return 1;
                                        }
                                        else {
                                            return 0;
                                        }
                                    }
                                });

                                populateWidget(true);

                                //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                                for (var widgetName in widgetTargetList) {
                                    for (var key in widgetTargetList[widgetName]) {
                                        if (widgetTargetList[widgetName][key] === localEventType) {
                                            removeAllEventsFromMaps(widgetName);
                                            $("#" + widgetName + "_wrapper").hide();
                                            $("#" + widgetName + "_mapDiv").show();
                                            addEventToMap($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetName);
                                        }
                                    }
                                }

                                $(this).find("div.trafficEventsButtonIndicator").html('<i class="fa fa-caret-up"></i>');
                                break;

                            case 2:
                                timeSortState = 0;
                                plansOrderedIds.sort(function (a, b) {
                                    var itemA = new Date(plansObj[a].event_time);
                                    var itemB = new Date(plansObj[b].event_time);
                                    if (itemA > itemB) {
                                        return -1;
                                    }
                                    else {
                                        if (itemA < itemB) {
                                            return 1;
                                        }
                                        else {
                                            return 0;
                                        }
                                    }
                                });

                                populateWidget(true);
                                for (var widgetName in widgetTargetList) {
                                    for (var key in widgetTargetList[widgetName]) {
                                        removeAllEventsFromMaps(widgetName);
                                    }
                                }
                                $(this).find("div.trafficEventsButtonIndicator").html('');
                                break;
                        }

                        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html('');
                        decisionSortState = 0;
                    });

                    $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).click(function () {
                        var localEventType = null;

                        switch (decisionSortState) {
                            case 0:
                                decisionSortState = 1;
                                plansOrderedIds.sort(function (a, b) {
                                    var itemA = plansObj[a].lastStatus;
                                    var itemB = plansObj[b].lastStatus;
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
                                    return parseInt(a.id) - parseInt(b.id);
                                });

                                populateWidget(true);

                                //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                                for (var widgetName in widgetTargetList) {
                                    for (var key in widgetTargetList[widgetName]) {
                                        if (widgetTargetList[widgetName][key] === localEventType) {
                                            removeAllEventsFromMaps(widgetName);
                                            $("#" + widgetName + "_wrapper").hide();
                                            $("#" + widgetName + "_mapDiv").show();
                                            addEventToMap($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetName);
                                        }
                                    }
                                }

                                //Notifica agli altri widget esb affinché rimuovano lo stato "on map" dai propri eventi
                                $("#<?= $_REQUEST['name_w'] ?>_decisionSortMsg").html('<i class="fa fa-caret-down"></i>');
                                break;

                            case 1:
                                decisionSortState = 2;
                                plansOrderedIds.sort(function (a, b) {
                                    var itemA = plansObj[a].lastStatus;
                                    var itemB = plansObj[b].lastStatus;
                                    if (itemA > itemB) {
                                        return -1;
                                    }
                                    else {
                                        if (itemA < itemB) {
                                            return 1;
                                        }
                                        else {
                                            return 0;
                                        }
                                    }
                                });

                                populateWidget(true);

                                //Aggiunta su mappa dell'elemento in prima posizione post ordinamento
                                localEventType = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-eventType");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).attr("data-onmap", "true");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").html("map");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).addClass("onMapTrafficEventPinAnimated");
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0).parent().parent().find("div.trafficEventPinMsgContainer").addClass("onMapTrafficEventPinAnimated");

                                for (var widgetName in widgetTargetList) {
                                    for (var key in widgetTargetList[widgetName]) {
                                        if (widgetTargetList[widgetName][key] === localEventType) {
                                            removeAllEventsFromMaps(widgetName);
                                            $("#" + widgetName + "_wrapper").hide();
                                            $("#" + widgetName + "_mapDiv").show();
                                            addEventToMap($("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.trafficEventLink").eq(0), widgetName);
                                        }
                                    }
                                }

                                $("#<?= $_REQUEST['name_w'] ?>_decisionSortMsg").html('<i class="fa fa-caret-up"></i>');
                                break;

                            case 2:
                                decisionSortState = 0;
                                plansOrderedIds.sort(function (a, b) {
                                    var itemA = parseInt(plansObj[a].id);
                                    var itemB = parseInt(plansObj[b].id);
                                    return itemA - itemB;
                                });

                                populateWidget(true);
                                for (var widgetName in widgetTargetList) {
                                    for (var key in widgetTargetList[widgetName]) {
                                        removeAllEventsFromMaps(widgetName);
                                    }

                                }
                                $("#<?= $_REQUEST['name_w'] ?>_decisionSortMsg").html('');
                                break;
                        }

                        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html('');
                        timeSortState = 0;
                    });

                    scroller = setInterval(stepDownInterval, speed);
                    var timeToClearScroll = (timeToReload - 0.5) * 1000;

                    setTimeout(function () {
                        clearInterval(scroller);
                        $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").off();

                        //$(document).off("esbEventAdded");

                        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(0).find("div.trafficEventsButtonIndicator").html("");
                        $("#<?= $_REQUEST['name_w'] ?>_buttonsContainer div.trafficEventsButtonContainer").eq(1).find("div.trafficEventsButtonIndicator").html("");

                        //Ripristino delle homepage native per gli widget targets al reload, se pilotati per ultimi da questo widget
                        for (var widgetName in widgetTargetList) {
                            if ($("#" + widgetName + "_driverWidgetType").val() === 'evacuationPlans') {
                                loadDefaultMap(widgetName);
                            }
                            else {
                                //console.log("Attualmente non pilotato da evacuationPlans");
                            }
                        }

                    }, timeToClearScroll);


                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").mouseenter(function () {
                        clearInterval(scroller);
                    });

                    $("#<?= $_REQUEST['name_w'] ?>_rollerContainer").mouseleave(function () {
                        scroller = setInterval(stepDownInterval, speed);
                    });
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

        $(document).on('resizeHighchart_' + widgetName, function (event) {
            showHeader = event.showHeader;
        });

        $("#<?= $_REQUEST['name_w'] ?>").off('updateFrequency');
        $("#<?= $_REQUEST['name_w'] ?>").on('updateFrequency', function (event) {
            clearInterval(countdownRef);
            timeToReload = event.newTimeToReload;
            intervalRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
        });

        intervalRef = startCountdown(widgetName, timeToReload, <?= $_REQUEST['name_w'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);

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
                <div id="<?= $_REQUEST['name_w'] ?>_buttonsContainer"
                     class="trafficEventsButtonsContainer centerWithFlex">
                    <div class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/trafficIcons/time.png"/>
                        </div>
                        <div class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                    <div class="trafficEventsButtonContainer">
                        <div class="trafficEventsButtonIcon centerWithFlex">
                            <img src="../img/planIcons/decision.png"/>
                        </div>
                        <div id="<?= $_REQUEST['name_w'] ?>_decisionSortMsg"
                             class="trafficEventsButtonIndicator centerWithFlex"></div>
                    </div>
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_rollerContainer" class="trafficEventsRollerContainer"></div>
            </div>
        </div>
    </div>
</div> 