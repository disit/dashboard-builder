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
    $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange) {
        <?php
        $titlePatterns = array();
        $titlePatterns[0] = '/_/';
        $titlePatterns[1] = '/\'/';
        $replacements = array();
        $replacements[0] = ' ';
        $replacements[1] = '&apos;';
        $title = $_REQUEST['title_w'];
        ?>
        var scroller, widgetProperties, styleParameters, serviceUri, queryType,
            eventName, newRow, symbolMode, symbolFile, widgetTargetList, originalHeaderColor, fontFamily,
            originalBorderColor,
            eventName, serviceUri, queriesNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx,
            eventContentWPerc,
            mapPtrContainer, pinContainer, queryDescContainer, activeFontColor, rowHeight, iconSize,
            queryDescContainerWidth,
            queryDescContainerWidthPerc, pinContainerWidthPerc, defaultOption = null;

        var fontSize = "<?= $_REQUEST['fontSize'] ?>";
        var speed = 65;
        var hostFile = "<?= $_REQUEST['hostFile'] ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_mainContainer");
        var widgetContentColor = "<?= $_REQUEST['color_w'] ?>";
        var widgetHeaderColor = "<?= $_REQUEST['frame_color_w'] ?>";
        var widgetHeaderFontColor = "<?= $_REQUEST['headerFontColor'] ?>";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var fontColor = "<?= $_REQUEST['fontColor'] ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer");
        var url = "<?= $_REQUEST['link_w'] ?>";
        var embedWidget = <?= $_REQUEST['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_REQUEST['embedWidgetPolicy'] ?>';
        var headerHeight = 25;
        var showTitle = "<?= $_REQUEST['showTitle'] ?>";
        var showHeader = null;
        var defaultOptionUsed = false;
        var pinContainerWidth = 40;
        var hasTimer = "<?= $_REQUEST['hasTimer'] ?>";

        if (url === "null") {
            url = null;
        }

        if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
            showHeader = false;
        }
        else {
            showHeader = true;
        }

        //Definizioni di funzione
        function populateWidget() {
            var queries = JSON.parse(widgetProperties.param.parameters).queries;
            var desc, query, color1, color2, targets, display = null;
            $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').empty();

         /*   queries.sort(function (a, b) {
                if (a.desc < b.desc) return -1;
                if (a.desc > b.desc) return 1;
                return 0;
            }); */

            if (firstLoad !== false) {
                showWidgetContent(widgetName);
            }
            else {
                elToEmpty.empty();
            }

            for (var i = 0; i < queries.length; i++) {

                desc = queries[i].desc;
                query = queries[i].query;
                color1 = queries[i].color1;
                color2 = queries[i].color2;
                targets = queries[i].targets;

                symbolMode = queries[i].symbolMode;
                defaultOption = queries[i].defaultOption;
                display = queries[i].display;

                queryType = queries[i].queryType;

                newRow = $('<div class="selectorRow"></div>');
                newRow.css("width", "100%");
                newRow.css("height", rowPercHeight + "%");

                mapPtrContainer = $('<div class="gisMapPtrContainer"></div>');
                mapPtrContainer.css("background", color1);
                mapPtrContainer.css("background", "-webkit-linear-gradient(left top, " + color1 + ", " + color2 + ")");
                mapPtrContainer.css("background", "-o-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                mapPtrContainer.css("background", "-moz-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                mapPtrContainer.css("background", "linear-gradient(to bottom right, " + color1 + ", " + color2 + ")");

                rowHeight = $("#<?= $_REQUEST['name_w'] ?>_content").height() * rowPercHeight / 100;
                iconSize = parseInt(rowHeight * 0.75);
                var pinMsgFontSize = rowHeight / 3.25;

                pinContainer = $('<div class="gisPinContainer"><a class="gisPinLink" data-fontColor="' + fontColor + '" data-activeFontColor="' + activeFontColor + '" data-symbolMode="' + symbolMode + '" data-desc="' + desc + '" data-query="' + query + '" data-queryType="' + queryType + '" data-color1="' + color1 + '" data-color2="' + color2 + '" data-targets="' + targets + '" data-display="' + display + '" data-onMap="false"><span class="gisPinShowMsg" style="font-size: ' + pinMsgFontSize + 'px">show</span><span class="gisPinHideMsg" style="font-size: ' + pinMsgFontSize + 'px">hide</span><span class="gisPinNoQueryMsg" style="font-size: ' + pinMsgFontSize + 'px">no query</span><span class="gisPinNoMapsMsg" style="font-size: ' + pinMsgFontSize + 'px">no maps set</span><i class="material-icons gisPinIcon">navigation</i><div class="gisPinCustomIcon"><div class="gisPinCustomIconUp"></div><div class="gisPinCustomIconDown"><span><i class="fa fa-check"></i></span></div></div></a><i class="fa fa-circle-o-notch fa-spin gisLoadingIcon"></i><i class="fa fa-close gisLoadErrorIcon"></i></div>');

                if (symbolMode === 'auto') {
                    pinContainer.find('div.gisPinCustomIcon').hide();
                    pinContainer.find('i.gisPinIcon').show();
                }
                else {
                    symbolFile = queries[i].symbolFile;
                    pinContainer.find('i.gisPinIcon').hide();
                    pinContainer.find('div.gisPinCustomIcon').show();
                    pinContainer.find('div.gisPinCustomIconUp').show();
                    pinContainer.find('div.gisPinCustomIconUp').css("background", "url(" + symbolFile + ")");
                    pinContainer.find('div.gisPinCustomIconUp').css("background-size", "contain");
                    pinContainer.find('div.gisPinCustomIconUp').css("background-repeat", "no-repeat");
                    pinContainer.find('div.gisPinCustomIconUp').css("background-position", "center center");
                }

                mapPtrContainer.append(pinContainer);
                newRow.append(mapPtrContainer);

                pinContainer.find("a.gisPinLink").hover(
                    function () {
                        var re1 = '(rgba)';
                        var re2 = '(\\()';
                        var re3 = '(\\d+)';
                        var re4 = '(,)';
                        var re5 = '(\\d+)';
                        var re6 = '(,)';
                        var re7 = '(\\d+)';
                        var re8 = '(,)';
                        var re9 = '(0)';
                        var re10 = '(\\))';
                        var transparentColorPattern = new RegExp(re1 + re2 + re3 + re4 + re5 + re6 + re7 + re8 + re9 + re10, ["g"]);

                        originalHeaderColor = {};
                        originalBorderColor = {};

                        //layout

                        if (widgetTargetList.length > 0) {
                            if ($(this).attr("data-query") === '') {
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                                }
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoMapsMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoQueryMsg").show();
                            }
                            else {
                                for (var i in widgetTargetList) {
                                    if (!$(this).attr("data-color1").match(transparentColorPattern)) {
                                        originalHeaderColor[widgetTargetList[i]] = $("#" + widgetTargetList[i] + "_header").css("background-color");
                                        originalBorderColor[widgetTargetList[i]] = $("#" + widgetTargetList[i]).css("border-color");
                                        $("#" + widgetTargetList[i] + "_header").css("background", $(this).attr("data-color1"));
                                        $("#" + widgetTargetList[i]).css("border-color", $(this).attr("data-color1"));

                                        if ($(this).attr("data-onMap") === "true") {
                                            $.event.trigger({
                                                type: "mouseOverFromGis_" + widgetTargetList[i],
                                                eventGenerator: $(this),
                                                targetWidget: widgetTargetList[i],
                                                desc: $(this).attr("data-desc"),
                                                query: $(this).attr("data-query"),
                                                color1: $(this).attr("data-color1"),
                                                color2: $(this).attr("data-color2"),
                                                targets: $(this).attr("data-targets")
                                            });
                                        }
                                    }
                                    else {
                                        //console.log("Trasparente");
                                    }
                                }

                                if ($(this).attr("data-onMap") === "false") {
                                    if ($(this).attr("data-symbolMode") === 'auto') {
                                        $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                                    }
                                    else {
                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                                    }
                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();

                                    if ($(this).attr("data-color1").match(transparentColorPattern)) {
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").css("color", fontColor);
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").css("text-shadow", "none");
                                    }

                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").show();
                                }
                                else {
                                    if ($(this).attr("data-symbolMode") === 'auto') {
                                        $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                                    }
                                    else {
                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                                    }
                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();

                                    if ($(this).attr("data-color1").match(transparentColorPattern)) {
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").css("color", fontColor);
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").css("text-shadow", "none");
                                    }

                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").show();
                                }
                            }
                        }
                        else {
                            if ($(this).attr("data-symbolMode") === 'auto') {
                                $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                            }
                            else {
                                $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                            }
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoQueryMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoMapsMsg").show();
                        }
                    },
                    function () {
                        if (widgetTargetList.length > 0) {
                            if ($(this).attr("data-query") === '') {
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoQueryMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoMapsMsg").hide();
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                                }
                            }
                            else {
                                for (var i in widgetTargetList) {
                                    $("#" + widgetTargetList[i] + "_header").css("background", originalHeaderColor[widgetTargetList[i]]);
                                    $("#" + widgetTargetList[i]).css("border-color", originalBorderColor[widgetTargetList[i]]);

                                    if ($(this).attr("data-onMap") === "true") {
                                        $.event.trigger({
                                            type: "mouseOutFromGis_" + widgetTargetList[i],
                                            eventGenerator: $(this),
                                            targetWidget: widgetTargetList[i],
                                            desc: $(this).attr("data-desc"),
                                            query: $(this).attr("data-query"),
                                            color1: $(this).attr("data-color1"),
                                            color2: $(this).attr("data-color2"),
                                            targets: $(this).attr("data-targets")
                                        });
                                    }
                                }

                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                                }
                            }
                        }
                        else {
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoQueryMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoMapsMsg").hide();
                            if ($(this).attr("data-symbolMode") === 'auto') {
                                $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                            }
                            else {
                                $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                            }
                        }
                    }
                );

                pinContainer.find("a.gisPinLink").click(function (event) {
                        event.preventDefault();

                        //TrafficRealTimeDetails - Stefano
                        if (($(this).attr("data-query").includes("trafficRTDetails")) && (widgetTargetList.length > 0)) {

                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                            if ($(this).attr("data-symbolMode") === 'auto') {
                                $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                            }
                            else {
                                $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                            }

                            if ($(this).attr("data-onMap") === "false") {
                                $(this).attr("data-onMap", "true");
                                $(this).find("i.gisPinIcon").html("near_me");
                                $(this).find("i.gisPinIcon").css("color", "white");
                                $(this).find("i.gisPinIcon").css("text-shadow", "black 2px 2px 4px");

                                var coordsAndType = $(this).attr("data-query");

                                $.event.trigger({
                                    type: "addTrafficRealTimeDetails",
                                    target: widgetTargetList[0],
                                    passedData: coordsAndType
                                });
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    $(this).find("i.gisPinIcon").html("navigation");
                                    $(this).find("i.gisPinIcon").css("color", "black");
                                    $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                $.event.trigger({
                                    type: "removeTrafficRealTimeDetails",
                                    target: widgetTargetList[0]
                                });
                            }
                        }

                        //Heatmap - Daniele
                        if (($(this).attr("data-query").includes("heatmap.php") || $(this).attr("data-query").includes("wmsserver.snap4city.org")) && (widgetTargetList.length > 0)) {
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                            if ($(this).attr("data-symbolMode") === 'auto') {
                                $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                            }
                            else {
                                $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                            }

                            if ($(this).attr("data-onMap") === "false") {

                                var thisQuery = $(this).attr("data-query");
                                $('.gisPinLink').each(function( index ) {
                                    if(($(this).attr("data-query").includes("heatmap.php") || $(this).attr("data-query").includes("wmsserver.snap4city.org")) && $(this).attr("data-query") != thisQuery) {
                                        if ($(this).attr("data-onMap") === "true") {
                                            $(this).attr("data-onMap", "false");
                                            if ($(this).attr("data-symbolMode") === 'auto') {
                                                $(this).find("i.gisPinIcon").html("navigation");
                                                $(this).find("i.gisPinIcon").css("color", "black");
                                                $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                            } else {
                                                $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                                $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                            }
                                        }
                                    }
                                });

                                $(this).attr("data-onMap", "true");
                                $(this).find("i.gisPinIcon").html("near_me");
                                $(this).find("i.gisPinIcon").css("color", "white");
                                $(this).find("i.gisPinIcon").css("text-shadow", "black 2px 2px 4px");
                                let coordsAndType = $(this).attr("data-query");

                                $.event.trigger({
                                    type: "addHeatmap",
                                    target: widgetTargetList[0],
                                    passedData: coordsAndType
                                });
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    $(this).find("i.gisPinIcon").html("navigation");
                                    $(this).find("i.gisPinIcon").css("color", "black");
                                    $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                $.event.trigger({
                                    type: "removeHeatmap",
                                    target: widgetTargetList[0]
                                });
                            }
                        }

                        if ((($(this).attr("data-query").includes("trafficRTDetails")) !== true) && (($(this).attr("data-query").includes("heatmap.php") || $(this).attr("data-query").includes("wmsserver.snap4city.org")) !== true) && (widgetTargetList.length > 0)) {

                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                            if ($(this).attr("data-symbolMode") === 'auto') {
                                $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                            }
                            else {
                                $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                            }

                            if ($(this).attr("data-onMap") === "false") {
                                $(this).attr("data-onMap", "true");
                                $(this).hide();
                                $(this).parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").show();
                                addLayerToTargetMaps($(this), $(this).attr("data-desc"), $(this).attr("data-query"), $(this).attr("data-color1"), $(this).attr("data-color2"), $(this).attr("data-targets"), $(this).attr("data-display"), $(this).attr("data-queryType"));
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    $(this).find("i.gisPinIcon").html("navigation");
                                    $(this).find("i.gisPinIcon").css("color", "black");
                                    $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                $(this).parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('span.gisQueryDescPar').css("font-weight", "normal");
                                $(this).parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('span.gisQueryDescPar').css("color", $(this).attr("data-fontColor"));
                                removeLayerFromTargetMaps($(this).attr("data-desc"), $(this).attr("data-query"), $(this).attr("data-color1"), $(this).attr("data-color2"), $(this).attr("data-targets"), $(this).attr("data-display"));
                            }
                        }

                    }
                );

                queryDescContainer = $('<div class="gisQueryDescContainer centerWithFlex"></div>');
                queryDescContainer.css("background", color1);
                queryDescContainer.css("background", "-webkit-linear-gradient(left top, " + color1 + ", " + color2 + ")");
                queryDescContainer.css("background", "-o-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                queryDescContainer.css("background", "-moz-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                queryDescContainer.css("background", "linear-gradient(to bottom right, " + color1 + ", " + color2 + ")");
                queryDescContainer.html('<span class="gisQueryDescPar">' + desc + '</span>');
                queryDescContainer.find("span.gisQueryDescPar").css("color", fontColor);
                if (fontFamily !== 'Auto') {
                    queryDescContainer.find("span.gisQueryDescPar").css("font-family", fontFamily);
                }
                newRow.append(queryDescContainer);

                $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').append(newRow);


                mapPtrContainer.css("width", Math.floor((newRow.height()) / newRow.width() * 100) + "%");
                queryDescContainer.css("width", Math.floor((newRow.width() - Math.ceil(newRow.height())) / newRow.width() * 100) + "%");

                pinContainer.find('i.gisPinIcon').css("font-size", newRow.height() * 0.8 + "px");
                queryDescContainer.textfill({
                    maxFontPixels: fontSize
                });
            }//Fine del for    

            var minFontSize = fontSize;

            for (var k = 0; k < $('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').length; k++) {
                if (parseInt($('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').eq(k).find('span.gisQueryDescPar').css('font-size').replace('px', '')) < minFontSize) {
                    minFontSize = parseInt($('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').eq(k).find('span.gisQueryDescPar').css('font-size').replace('px', ''));
                }
            }

            if (minFontSize > fontSize) {
                minFontSize = fontSize;
            }

            $('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer span.gisQueryDescPar').css("font-size", minFontSize + "px");
        }

        function addLayerToTargetMaps(eventGenerator, desc, query, color1, color2, targets, display, queryType) {
            let coordsAndType = {};

            coordsAndType.eventGenerator = eventGenerator;
            coordsAndType.desc = desc;
            coordsAndType.query = query;
            coordsAndType.color1 = color1;
            coordsAndType.color2 = color2;
            coordsAndType.targets = targets;
            coordsAndType.display = display;
            coordsAndType.queryType = queryType;

            $.event.trigger({
                type: "addSelectorPin",
                target: widgetTargetList[0],
                passedData: coordsAndType
            });
        }

        function removeLayerFromTargetMaps(desc, query, color1, color2, targets, display) {
            let coordsAndType = {};

            coordsAndType.desc = desc;
            coordsAndType.query = query;
            coordsAndType.color1 = color1;
            coordsAndType.color2 = color2;
            coordsAndType.targets = targets;
            coordsAndType.display = display;

            $.event.trigger({
                type: "removeSelectorPin",
                target: widgetTargetList[0],
                passedData: coordsAndType
            });
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

        function resizeWidget() {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);

            //Resize dei due contenitori di pin e desc, rispettivamente
            var rowPxHeight = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').height() / queriesNumber;
            var descPxWidth = $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() - rowPxHeight;

            //$('#<?= $_REQUEST['name_w'] ?>_div div.gisMapPtrContainer').css("width", rowPxHeight + "px");
            //$('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').css("width", descPxWidth + "px");

            $('#<?= $_REQUEST['name_w'] ?>_div div.gisMapPtrContainer').css("width", Math.floor((rowPxHeight) / $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() * 100) + "%");
            $('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').css("width", Math.floor(($('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() - Math.ceil(rowPxHeight)) / $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() * 100) + "%");

            //Resize dei pin di default
            $('#<?= $_REQUEST['name_w'] ?>_div i.gisPinIcon').css("font-size", rowPxHeight * 0.8 + "px");

            //Resize dei messaggi no map, on map, show, hide
            var pinMsgFontSize = rowPxHeight / 3.25;
            $('#<?= $_REQUEST['name_w'] ?>_div span.gisPinShowMsg').css("font-size", pinMsgFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_div span.gisPinHideMsg').css("font-size", pinMsgFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_div span.gisPinNoQueryMsg').css("font-size", pinMsgFontSize + "px");
            $('#<?= $_REQUEST['name_w'] ?>_div span.gisPinNoMapsMsg').css("font-size", pinMsgFontSize + "px");

            //Resize delle descrizioni
            $('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').textfill({
                maxFontPixels: fontSize
            });

            var minFontSize = fontSize;

            for (var k = 0; k < $('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').length; k++) {
                if (parseInt($('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').eq(k).find('span.gisQueryDescPar').css('font-size').replace('px', '')) < minFontSize) {
                    minFontSize = parseInt($('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').eq(k).find('span.gisQueryDescPar').css('font-size').replace('px', ''));
                }
            }

            if (minFontSize > fontSize) {
                minFontSize = fontSize;
            }

            $('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer span.gisQueryDescPar').css("font-size", minFontSize + "px");
        }

        //Fine definizioni di funzione

        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);

        if (firstLoad === false) {
            showWidgetContent(widgetName);
        }
        else {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }

        //addLink(widgetName, url, linkElement, divContainer);
        //$("#<?= $_REQUEST['name_w'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");

        $.ajax({
            url: getParametersWidgetUrl,
            type: "GET",
            data: {"nomeWidget": [widgetName]},
            async: true,
            dataType: 'json',
            success: function (data) {
                widgetProperties = data;
                if ((widgetProperties !== null) && (widgetProperties !== undefined)) {
                    //Inizio eventuale codice ad hoc basato sulle proprietà del widget
                    styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
                    //Fine eventuale codice ad hoc basato sulle proprietà del widget
                    fontFamily = widgetProperties.param.fontFamily;
                    widgetTargetList = JSON.parse(widgetProperties.param.parameters).targets;
                    queriesNumber = JSON.parse(widgetProperties.param.parameters).queries.length;
                    activeFontColor = styleParameters.activeFontColor;
                    widgetWidth = $('#<?= $_REQUEST['name_w'] ?>_div').width();
                    shownHeight = $('#<?= $_REQUEST['name_w'] ?>_div').height() - 25;

                    rowPercHeight = 100 / queriesNumber;
                    contentHeightPx = queriesNumber * 100;
                    eventContentWPerc = null;

                    populateWidget();

                    setTimeout(function () {
                        for (var i = 0; i < JSON.parse(widgetProperties.param.parameters).queries.length; i++) {
                            defaultOption = JSON.parse(widgetProperties.param.parameters).queries[i].defaultOption;
                            if (defaultOption) {
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.gisPinLink").eq(i).trigger('click');
                                defaultOptionUsed = true;
                            }
                        }

                        if (!defaultOptionUsed) {
                            JSON.parse(widgetProperties.param.parameters).queries[0].defaultOption = true;
                            setTimeout(function() {
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.gisPinLink").eq(0).trigger('click');
                            }, 700);
                        }
                    }, parseInt("<?php echo $crossWidgetDefaultLoadWaitTime; ?>"));
                }
                else {
                    console.log("Proprietà widget = null");
                    $("#<?= $_REQUEST['name_w'] ?>_mainContainer").hide();
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                }
            },
            error: function (errorData) {
                console.log("Errore in caricamento proprietà widget");
                console.log(JSON.stringify(errorData));
                showWidgetContent(widgetName);
                if (firstLoad !== false) {
                    $("#<?= $_REQUEST['name_w'] ?>_mainContainer").hide();
                    $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                }
            }
        });

        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function (event) {
            resizeWidget();
        });

        $(document).on('resizeHighchart_' + widgetName, function (event) {
            showHeader = event.showHeader;
        });

        $(document).on('removeSelectorEventPin', function () {
            $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.gisPinLink").each(function () {
                $(this).attr("data-onMap", "false");
                if ($(this).attr("data-symbolMode") === 'auto') {
                    $(this).find("i.gisPinIcon").html("navigation");
                    $(this).find("i.gisPinIcon").css("color", "black");
                    $(this).find("i.gisPinIcon").css("text-shadow", "none");
                }
                else {
                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                }

                $(this).parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('span.gisQueryDescPar').css("font-weight", "normal");
                $(this).parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('span.gisQueryDescPar').css("color", $(this).attr("data-fontColor"));
            });
        });
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
                <div id="<?= $_REQUEST['name_w'] ?>_rollerContainer" class="gisRollerContainer"></div>
            </div>
        </div>

        <!--<div id="<?= $_REQUEST['name_w'] ?>_resizeHandle" class='resizeHandle'>
            
        </div>    -->
    </div>
</div>