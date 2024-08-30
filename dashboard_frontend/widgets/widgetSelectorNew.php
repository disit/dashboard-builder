<?php

/* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */
include('../config.php');
header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>

<!-- dom-to-image -->
<script type="text/javascript" src="../js/dom-to-image.min.js"></script>
<!-- <script src="../js/jsonpath-0.8.0.js"></script> --> <!-- SVG CustomPin Icon MOD -->

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
        $link = mysqli_connect($host, $username, $password);
        if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
            eventLog("Returned the following ERROR in widgetSelectorNew.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
            exit();
        }
        ?>
        var scroller, widgetProperties, styleParameters, serviceUri, queryType,
            eventName, newRow, symbolMode, symbolFile, widgetTargetList, originalHeaderColor, fontFamily,
            originalBorderColor,
            eventName, serviceUri, queriesNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx,
            eventContentWPerc,
            mapPtrContainer, pinContainer, queryDescContainer, activeFontColor, rowHeight, iconSize,
            queryDescContainerWidth,
            queryDescContainerWidthPerc, pinContainerWidthPerc, gisMapPtrContainerWidth, gisMapPtrContainerWidthPerc,
            defaultOption, iconTextMode, highLevelType, nature, subNature, mapPinIcon, altViewModeMode, bubbleMetrics, bubbleSelectedMetric, svgContainer = null;

        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var speed = 65;
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= escapeForJS($_REQUEST['name_w']) ?>";
        var divContainer = $("#<?= escapeForJS($_REQUEST['name_w']) ?>_mainContainer");
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_rollerContainer");
        var url = "<?= escapeForJS($_REQUEST['link_w']) ?>";
        var embedWidget = <?= $_REQUEST['embedWidget']=='true' ? 'true' : 'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var headerHeight = 25;
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var showHeader = null;
        var defaultOptionUsed = false;
        var pinContainerWidth = 40;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        console.log("Selector Widget loaded: " + widgetName);
        globalMapView = false;
        var newPinsContainer = null;
        var geoServerUrl, heatmapUrl = null;
     //   var bubbleMetricsArray = [];

     //   $('.poolIcon').tooltip();
     //   $('[data-toggle="tooltip"]').tooltip();
     //   $('[data-toggle="tooltip"]').tooltip({'delay': { show: 5000, hide: 3000 }});
     //   $('[data-toggle="popover"]').popover();
     //   $("body").tooltip({ selector: '[data-toggle=tooltip]' });

        if (url === "null") {
            url = null;
        }

        if (((embedWidget === true) && (embedWidgetPolicy === 'auto')) || ((embedWidget === true) && (embedWidgetPolicy === 'manual') && (showTitle === "no")) || ((embedWidget === false) && (showTitle === "no"))) {
            showHeader = false;
        }
        else {
            showHeader = true;
        }

        function componentToHex(c) {
            var hex = c.toString(16);
            return hex.length == 1 ? "0" + hex : hex;
        }

        function rgbToHex(r, g, b) {
            return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
        }

        function rgba2rgb(rgbaValue, alpha) {

            var rgbValue = [rgbaValue[0], rgbaValue[1], rgbaValue[2]];
            var backAlpha = 1;

            if (alpha >= 1) {
                return rgbaValue;
            }

            for(var i=0; i<3; i++) {
                rgbValue[i] = rgbValue[i] * alpha + rgbaValue[i] * backAlpha * (1 - alpha);
            }

         //   rgbValue.rgba[3] = alpha + color.rgba[3] * (1 - alpha);

            return rgbValue;

        }

        function luminance(r, g, b) {
            var a = [r, g, b].map(function (v) {
                v /= 255;
                return v <= 0.03928
                    ? v / 12.92
                    : Math.pow( (v + 0.055) / 1.055, 2.4 );
            });
            return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
        }

        function contrast(rgb1, rgb2) {
            return (luminance(rgb1[0], rgb1[1], rgb1[2]) + 0.05)
                / (luminance(rgb2[0], rgb2[1], rgb2[2]) + 0.05);
        }

        function convertHexToRGB(hex){
            hex = hex.replace('#','');

            var rgbArray = [];
            var r = parseInt(hex.substring(0,2), 16);
            var g = parseInt(hex.substring(2,4), 16);
            var b = parseInt(hex.substring(4,6), 16);
            rgbArray = [r, g, b];

            var resultString = 'rgb('+r+','+g+','+b+')';
            return rgbArray;
        }

        function convertHexToRGBA(hex,opacity){
            hex = hex.replace('#','');
            var alpha = null;
            if (opacity) {
                alpha = opacity/100;
            } else {
                alpha = 1;
            }
            var rgbaArray = [];
            var r = parseInt(hex.substring(0,2), 16);
            var g = parseInt(hex.substring(2,4), 16);
            var b = parseInt(hex.substring(4,6), 16);
            rgbaArray = [r, g, b, alpha];

            var resultString = 'rgba('+r+','+g+','+b+','+ alpha +')';
            return rgbaArray;
        }

        function checkColorContrast (color, param) {
            var contrastValue = null;
            if (color.split("rgba(").length > 1) {
                var rgbAlphaString = color.split("rgba(")[1].split(")")[0];
                var rComponent = null;
                var gComponent = null;
                var bComponent = null;
                var alpha = null;
                var rgbColor = null;
                if (color.split("rgba(")[1].split(")")[0].split(",")) {
                    rComponent = color.split("rgba(")[1].split(")")[0].split(",")[0].replace(/\s+/g, '');
                    gComponent = color.split("rgba(")[1].split(")")[0].split(",")[1].replace(/\s+/g, '');
                    bComponent = color.split("rgba(")[1].split(")")[0].split(",")[2].replace(/\s+/g, '');
                    if (color.split("rgba(")[1].split(")")[0].split(",").length > 3) {

                        alpha = color.split("rgba(")[1].split(")")[0].split(",")[3].replace(/\s+/g, '');
                        if (param == "black") {
                            contrastValue = contrast([rgba2rgb([rComponent, gComponent, bComponent], alpha)[0], rgba2rgb([rComponent, gComponent, bComponent], alpha)[1], rgba2rgb([rComponent, gComponent, bComponent], alpha)[2]], [0, 0, 0]);
                        } else {
                            contrastValue = contrast([rgba2rgb([rComponent, gComponent, bComponent], alpha)[0], rgba2rgb([rComponent, gComponent, bComponent], alpha)[1], rgba2rgb([rComponent, gComponent, bComponent], alpha)[2]], [255, 255, 255]);
                        }

                    } else {

                        rgbColor = convertHexToRGB(color);
                        if (param == "black") {
                            contrastValue = contrast([rComponent, gComponent, bComponent], [0, 0, 0]);
                        } else {
                            contrastValue = contrast([rComponent, gComponent, bComponent], [255, 255, 255]);
                        }

                    }
                }

            } else {
                rgbColor = convertHexToRGB(color);
                if (rgbColor) {
                    if (param == "black") {
                        contrastValue = contrast([rgbColor[0], rgbColor[1], rgbColor[2]], [0, 0, 0]);
                    } else {
                        contrastValue = contrast([rgbColor[0], rgbColor[1], rgbColor[2]], [255, 255, 255]);
                    }
                }
            }
            return contrastValue;
        }

        function swapWhiteIconWhitBlack (divElement) {

        }

        function swapBlackIconWhitWhite (divElement) {

        }

        function makeNewPinIcon(nature, subNature, symbolColor, iconPath, iconWhitePath, idx, widgetName) {

            var htmlNewPinIcon = "";
            var serviceTypeString = null;
            let symbolColorOk = "";
            let symbolColorAttr = "";
            let symbolColorForFileName = "";
            var symbolColorDef = "";
          //  var iconPath = null;
          //  var iconWhitePath = null;
          //  var iconWhitePathAuto = queries[i].iconPoolImg.split(".svg")[0] + "-white.svg";
            var iconWhitePathAuto = "../img/widgetSelectorIconsPool/subnature/generic-white.svg";
            var iconPathAuto = "../img/widgetSelectorIconsPool/subnature/generic-white.svg";
            var filePinPath = "";

            var widgetNameForFile = widgetName;
            if(iconPath.includes("/nature/")) {
                filePinPath = iconPath.split("/nature/")[1].split(".svg")[0];
            } else if (iconPath.includes("/subnature/")) {
                filePinPath = iconPath.split("/subnature/")[1].split(".svg")[0];
            } else if (iconPath.includes("/hlt/")) {
                filePinPath = iconPath.split("/hlt/")[1].split(".svg")[0];
            }

         /*   if (nature) {
                if (subNature) {
                    // iconPath = "../img/widgetSelectorIconsPool/subnature/"+ nature + "/" + nature + "_" + subNature + ".svg";
                    iconPath = "../img/widgetSelectorIconsPool/subnature/" + nature + "_" + subNature + ".svg";
                    //    iconWhitePath = "../img/widgetSelectorIconsPool/subnature/"+ nature + "/" + nature + "_" + subNature + "-white.svg";
                    iconWhitePath = "../img/widgetSelectorIconsPool/subnature/" + nature + "_" + subNature + "-white.svg";
                } else {
                    iconPath = "../img/widgetSelectorIconsPool/nature/" + nature + ".svg";
                    iconWhitePath = "../img/widgetSelectorIconsPool/nature/" + nature + "-white.svg";
                }
                if (UrlExists(iconPath)) {

                } else {

                }
            } else if (highLevelType) {
                iconPath = "../img/widgetSelectorIconsPool/hlt/" + highLevelType + ".svg";
                iconWhitePath = "../img/widgetSelectorIconsPool/hlt/" + highLevelType + "-white.svg";
                if (UrlExists(iconPath)) {

                } else {

                }
            } else {
              //  if (queries[i].iconPoolImg) {

                iconWhitePath = iconWhitePathAuto;
                iconPath = iconPathAuto;

                //      pinContainer.children("a.gisPinLink").children("div.poolIcon").children(0).attr("src", queries[i].iconPoolImg);
              //      pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconblack", queries[i].iconPoolImg);
              //      var iconWhitePathAuto = queries[i].iconPoolImg.split(".svg")[0] + "-white.svg";
              //      pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconwhite", iconWhitePathAuto);
              //      pinContainer.find('#' + widgetName + '_poolIcon' + i).show();

              //  } else {
              //      pinContainer.find('i.gisPinIcon').show();
              //      pinContainer.find('#' + widgetName + '_poolIcon' + i).hide();
              //  }
            }   */

            if (subNature != null) {
                if (nature != null) {
                    if (nature.replace(/\s/g, '') == "MobilityandTransport") {
                        nature = "TransferServiceAndRenting";
                    }
                    serviceTypeString = nature.replace(/\s/g, '') + "_" + subNature;
                } else {
                    // CTR SE SUB_NATURE MA NON NATURE ?!
                    serviceTypeString = "generic";
                }
            } else if (nature != null) {
                if (nature.replace(/\s/g, '') == "MobilityandTransport") {
                    nature = "TransferServiceAndRenting";
                }
                serviceTypeString = nature.replace(/\s/g, '');
            } else {
                serviceTypeString = "generic";
            }

        //    symbolColor = rgbToHex(rgba2rgb(symbolColor));

            if (symbolColor != null) {
                if (symbolColor.includes("#")) {
                    symbolColorForFileName = symbolColor.split("#")[1];
                } else {
                    symbolColorForFileName = symbolColor;
                }
            } else {
                symbolColorForFileName = symbolColor;
            }

            //if (!UrlExists("../img/outputPngIcons/" + serviceTypeString + "/" + serviceTypeString + "_" + symbolColorForFileName + "_" + widgetNameForFile + ".png")) {
            if (!UrlExists("../img/outputPngIcons/" + filePinPath + "/" + filePinPath + "_" + symbolColorForFileName + ".png") && !filePinPath.includes("heatmap")) {

               // if (!UrlExists("../img/outputPngIcons/" + serviceTypeString + "/" + serviceTypeString + "_default_" + widgetNameForFile + ".png")) {
                if (!UrlExists("../img/outputPngIcons/" + filePinPath + "/" + filePinPath + "_default.png")  && !filePinPath.includes("heatmap")) {
                    //    if (symbolColor == undefined) {
                    //   symbolColor = "rgba(204,203,203,1)";
                    $.ajax({
                        url: "../controllers/getDefaultPinColor.php",
                        type: "GET",
                        data: {
                            "nature": nature,
                            "subNature": subNature
                        },
                        async: false,
                        dataType: 'json',
                        success: function (data) {
                            symbolColorDef = data.defColour;
                            //if (!UrlExists("../img/outputPngIcons/" + serviceTypeString + "/" + serviceTypeString + "_default_" + widgetNameForFile + ".png")) {
                            if (!UrlExists("../img/outputPngIcons/" + filePinPath + "/" + filePinPath + "_default.png")  && !filePinPath.includes("heatmap")) {
                                htmlNewPinIcon = '<div id="' + filePinPath + '_default" class="newPinContainer" data-filename="' + filePinPath + '_default" data-currentpincolor="' + symbolColorDef + '">' +
                                    '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Livello_1" x="0px" y="0px" viewBox="0 0 184.3 184.3" style="enable-background:new 0 0 184.3 184.3;height: 32px;width: 32px;" xml:space="preserve">' +
                                    '<style type="text/css">' +
                                    '.st' + idx + '_default{fill-rule:evenodd;clip-rule:evenodd;fill:' + symbolColorDef + ';stroke:grey;stroke-width:3}' +
                                    //    '.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#' + symbolColorDef + ';stroke:grey;stroke-width:3}' +
                                    '</style>' +
                                    '<g id="Layer_x0020_1" style="&#10;    height: 32px;&#10;    width: 32px;&#10;">' +
                                    '<path class="st' + idx + '_default" d="M89.7,1.7h4.7c8.7,0.3,17.3,2.3,24.8,6c1.8,0.9,3.3,1.6,5,2.7C132,15,138.9,21.3,144,28.6   c11.5,16.4,15.4,39.5,8,58.2c-0.6,1.5-0.9,2.6-1.6,4.1c-0.6,1.4-1.2,2.5-1.9,3.8c-1.2,2.4-2.6,4.6-4,6.9l-45.5,76   c-1.4,2.3-2.4,5-5.9,5c-3.3-0.2-4.4-2.5-6-5l-4.8-7.2c-8.3-13.5-17.5-27.8-25.5-41.2c-2.9-4.7-5.8-9.1-8.6-13.9   c-5.7-9.6-12.2-18.1-16-28.5c-3.2-8.6-3.6-15.1-3.6-24.3c0-19.1,11.6-38.3,26.8-49.1c3.1-2.2,6-3.9,9.5-5.6C72.4,4,81,2,89.7,1.7z" style="&#10;    heigth: 32px;&#10;"/>' +
                                    '</g>' +
                                    '</svg>' +
                                    '<img class="innerImg" src="' + iconWhitePath + '" alt="">' +
                                    '</div>';
                            }
                        },
                        error: function (errorData) {
                            console.log("Error in get Default Pin Color");
                            console.log(JSON.stringify(errorData));
                        }
                    });
                }

                if (symbolColor == undefined) {
                    symbolColorOk = symbolColorDef;
                } else {
                    symbolColorOk = symbolColor;
                }

                if (symbolColorOk != null) {
                    if (symbolColorOk.includes("#")) {
                        symbolColorAttr = symbolColorOk.split("#")[1];
                    } else {
                        symbolColorAttr = symbolColorOk;
                    }
                } else {
                    symbolColorOk = "rgba(204,203,203,1)";
                    symbolColorAttr = symbolColorOk;
                }

                //    if (!UrlExists("../img/outputPngIcons/" + serviceTypeString + "/" + serviceTypeString + "_" + symbolColorOk + ".png")) {
                if (symbolColorAttr != "") {
                    //if (!UrlExists("../img/outputPngIcons/" + serviceTypeString + "/" + serviceTypeString + "_" + symbolColorAttr + "_" + widgetNameForFile + ".png")) {
                    if (!UrlExists("../img/outputPngIcons/" + filePinPath + "/" + filePinPath + "_" + symbolColorAttr + ".png")  && !filePinPath.includes("heatmap")) {
                        if (htmlNewPinIcon == "") {
                            htmlNewPinIcon = '<div id="' + filePinPath + '-white_' + symbolColorOk + '" class="newPinContainer" data-filename="' + filePinPath + '_' + symbolColorOk + '" data-currentpincolor="' + symbolColorAttr + '">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Livello_1" x="0px" y="0px" viewBox="0 0 184.3 184.3" style="enable-background:new 0 0 184.3 184.3;height: 32px;width: 32px;" xml:space="preserve">' +
                                '<style type="text/css">' +
                                '.st' + idx + '_filled{fill-rule:evenodd;clip-rule:evenodd;fill:' + symbolColorOk + ';stroke:grey;stroke-width:3}' +
                                //    '.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#' + symbolColorOk + ';stroke:grey;stroke-width:3}' +
                                '</style>' +
                                '<g id="Layer_x0020_1" style="&#10;    height: 32px;&#10;    width: 32px;&#10;">' +
                                '<path class="st' + idx + '_filled" d="M89.7,1.7h4.7c8.7,0.3,17.3,2.3,24.8,6c1.8,0.9,3.3,1.6,5,2.7C132,15,138.9,21.3,144,28.6   c11.5,16.4,15.4,39.5,8,58.2c-0.6,1.5-0.9,2.6-1.6,4.1c-0.6,1.4-1.2,2.5-1.9,3.8c-1.2,2.4-2.6,4.6-4,6.9l-45.5,76   c-1.4,2.3-2.4,5-5.9,5c-3.3-0.2-4.4-2.5-6-5l-4.8-7.2c-8.3-13.5-17.5-27.8-25.5-41.2c-2.9-4.7-5.8-9.1-8.6-13.9   c-5.7-9.6-12.2-18.1-16-28.5c-3.2-8.6-3.6-15.1-3.6-24.3c0-19.1,11.6-38.3,26.8-49.1c3.1-2.2,6-3.9,9.5-5.6C72.4,4,81,2,89.7,1.7z" style="&#10;    heigth: 32px;&#10;"/>' +
                                '</g>' +
                                '</svg>' +
                                '<img class="innerImg" src="' + iconWhitePath + '" alt="">' +
                                '</div>';
                        } else {
                            htmlNewPinIcon = htmlNewPinIcon + '<div id="' + filePinPath + '-white_' + symbolColorOk + '" class="newPinContainer" data-filename="' + filePinPath + '_' + symbolColorOk + '" data-currentpincolor="' + symbolColorAttr + '">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Livello_1" x="0px" y="0px" viewBox="0 0 184.3 184.3" style="enable-background:new 0 0 184.3 184.3;height: 32px;width: 32px;" xml:space="preserve">' +
                                '<style type="text/css">' +
                                '.st' + idx + '_filled{fill-rule:evenodd;clip-rule:evenodd;fill:' + symbolColorOk + ';stroke:grey;stroke-width:3}' +
                                //    '.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#' + symbolColorOk + ';stroke:grey;stroke-width:3}' +
                                '</style>' +
                                '<g id="Layer_x0020_1" style="&#10;    height: 32px;&#10;    width: 32px;&#10;">' +
                                '<path class="st' + idx + '_filled" d="M89.7,1.7h4.7c8.7,0.3,17.3,2.3,24.8,6c1.8,0.9,3.3,1.6,5,2.7C132,15,138.9,21.3,144,28.6   c11.5,16.4,15.4,39.5,8,58.2c-0.6,1.5-0.9,2.6-1.6,4.1c-0.6,1.4-1.2,2.5-1.9,3.8c-1.2,2.4-2.6,4.6-4,6.9l-45.5,76   c-1.4,2.3-2.4,5-5.9,5c-3.3-0.2-4.4-2.5-6-5l-4.8-7.2c-8.3-13.5-17.5-27.8-25.5-41.2c-2.9-4.7-5.8-9.1-8.6-13.9   c-5.7-9.6-12.2-18.1-16-28.5c-3.2-8.6-3.6-15.1-3.6-24.3c0-19.1,11.6-38.3,26.8-49.1c3.1-2.2,6-3.9,9.5-5.6C72.4,4,81,2,89.7,1.7z" style="&#10;    heigth: 32px;&#10;"/>' +
                                '</g>' +
                                '</svg>' +
                                '<img class="innerImg" src="' + iconWhitePath + '" alt="">' +
                                '</div>';
                        }
                    }
                }

            }

            //if (!UrlExists("../img/outputPngIcons/" + serviceTypeString + "/" + serviceTypeString + "-over_" + widgetNameForFile + ".png")) {
            if (!UrlExists("../img/outputPngIcons/" + filePinPath + "/" + filePinPath + "-over.png")  && !filePinPath.includes("heatmap")) {
                htmlNewPinIcon = htmlNewPinIcon + '<div id="' + filePinPath + '_FFFFFF" class="newPinContainer" data-filename="' + filePinPath + '-over" data-currentpincolor="' + symbolColorAttr + '">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Livello_1" x="0px" y="0px" viewBox="0 0 184.3 184.3" style="enable-background:new 0 0 184.3 184.3;height: 32px;width: 32px;" xml:space="preserve">' +
                    '<style type="text/css">' +
                    '.st' + idx + '_white{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;stroke:grey;stroke-width:3}' +
                    '</style>' +
                    '<g id="Layer_x0020_1" style="&#10;    height: 32px;&#10;    width: 32px;&#10;">' +
                    '<path class="st' + idx + '_white" d="M89.7,1.7h4.7c8.7,0.3,17.3,2.3,24.8,6c1.8,0.9,3.3,1.6,5,2.7C132,15,138.9,21.3,144,28.6   c11.5,16.4,15.4,39.5,8,58.2c-0.6,1.5-0.9,2.6-1.6,4.1c-0.6,1.4-1.2,2.5-1.9,3.8c-1.2,2.4-2.6,4.6-4,6.9l-45.5,76   c-1.4,2.3-2.4,5-5.9,5c-3.3-0.2-4.4-2.5-6-5l-4.8-7.2c-8.3-13.5-17.5-27.8-25.5-41.2c-2.9-4.7-5.8-9.1-8.6-13.9   c-5.7-9.6-12.2-18.1-16-28.5c-3.2-8.6-3.6-15.1-3.6-24.3c0-19.1,11.6-38.3,26.8-49.1c3.1-2.2,6-3.9,9.5-5.6C72.4,4,81,2,89.7,1.7z" style="&#10;    heigth: 32px;&#10;"/>' +
                    '</g>' +
                    '</svg>' +
                    '<img class="innerImg" src="' + iconPath + '" alt="">' +
                    '</div>';

            }
            return htmlNewPinIcon;

        }


        //Definizioni di funzione
        function populateWidget() {
            var queries = JSON.parse(widgetProperties.param.parameters).queries;
            queries.sort(compareJsonElementsByKeyValues('rowOrder'));
            var desc, query, color1, color2, symbolColor, targets, display = null;
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

            // COSTRUZIONE RIGHE SELETTORE
            for (var i = 0; i < queries.length; i++) {

                let newStyleSelectorFlag = false;
                let iconPath = "";
                let iconWhitePath = "";
                let = highLevelType = null;
                let nature = null;
                let subNature = null;
                let newMapPinColor = null;
                let icBlckArray = new Array(queries.length);
                let icWhtArray = new Array(queries.length);
            //    newPinsContainer = null;
                desc = queries[i].desc;
                query = queries[i].query;
                color1 = queries[i].color1;
                color2 = queries[i].color2;
                symbolColor = queries[i].symbolColor;
                targets = queries[i].targets;

                symbolMode = queries[i].symbolMode;
                defaultOption = queries[i].defaultOption;
                display = queries[i].display;

                queryType = queries[i].queryType;
                altViewMode = queries[i].bubble;
                if (altViewMode == null) {
                    altViewMode = 'None';
                }
                bubbleSelectedMetric = queries[i].bubbleMetrics;
                if (bubbleSelectedMetric == null) {
                    bubbleSelectedMetric = "";
                }

             /*   bubbleMetricsArray[i] = [];
                if (bubbleMode == "Yes") {
                    // RETRIEVE METRICS ARRAY FOR BUBBLE CHART (SERVER SIDE)
                    getBubbleMetrics(query, i, function(extractedMetrics) {
                        if (extractedMetrics) {
                            let index = extractedMetrics[0];
                            bubbleMetricsArray[index].push(extractedMetrics[1].metrics);
                            $('#' + widgetName +'_pinCtn' + index).attr("data-bubblemetricsarray", extractedMetrics[1].metrics);
                            var stopFlag = 1;
                        }
                    });
                }*/

                newRow = $('<div class="selectorRow"></div>');
                newRow.css("width", "100%");
                newRow.css("height", rowPercHeight + "%");

                rowHeight = $("#<?= $_REQUEST['name_w'] ?>_content").height() * rowPercHeight / 100;
                if (iconTextMode != "Icon Only") {
                    mapPtrContainer = $('<div class="gisMapPtrContainer"></div>');
                } else {
                    mapPtrContainer = $('<div class="gisMapPtrContainer"></div>');
                //    mapPtrContainer = $('<div class="gisMapPtrContainer"><div class="bckGrndCircle" style="background-image: url(\'../img/widgetSelectorBakcGrnd/cerchio-arancione.svg\')"></div></div>');
                    //    mapPtrContainer = $('<div class="gisMapPtrContainer"><div class="bckGrndCircle" style="position: relative; width: ' + rowHeight + '; height: ' + rowHeight + ';"><img src="../img/widgetSelectorBakcGrnd/cerchio-arancione.svg" alt="" style="position: absolute; top: 0; left: 0;"></div></div>');
                /*    mapPtrContainer = $('<div class="gisMapPtrContainer"><div class="whiteBckGrndCircle"></div><div class="blackBckGrndCircle"></div></div>');
                    //  mapPtrContainer = $('<div class="gisMapPtrContainer"><div class="whiteBckGrndCircle"></div></div>');
                    mapPtrContainer.find('div.whiteBckGrndCircle').css("width", rowHeight);
                    mapPtrContainer.find('div.whiteBckGrndCircle').hide();
                    mapPtrContainer.find('div.blackBckGrndCircle').css("width", rowHeight);
                    mapPtrContainer.find('div.blackBckGrndCircle').hide();*/
                }

            //    highLevelType = queries[i].high_level_type;
                if(queries[i].high_level_type) {
                    highLevelType = queries[i].high_level_type.replace(/\s+/g, '');
                } else {
                    highLevelType = undefined;
                }
                if(queries[i].nature) {
                    nature = queries[i].nature.replace(/\s+/g, '');
                } else {
                    nature = undefined;
                }
              //  nature = queries[i].nature;
                subNature = queries[i].sub_nature;

                if(highLevelType || nature || subNature || queries[i].iconPoolImg) {
                    newStyleSelectorFlag = true;
                }

                if(queries[i].newMapPinColor) {
                    newMapPinColor = queries[i].newMapPinColor.replace(/\s+/g, '');
                } else {
                    newMapPinColor = "Default";
                }

                if(iconTextMode != "Icon Only") {
                    mapPtrContainer.css("background", color1);
                    mapPtrContainer.css("background", "-webkit-linear-gradient(left top, " + color1 + ", " + color2 + ")");
                    mapPtrContainer.css("background", "-o-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                    mapPtrContainer.css("background", "-moz-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                    mapPtrContainer.css("background", "linear-gradient(to bottom right, " + color1 + ", " + color2 + ")");
                } else {
                    mapPtrContainer.css("background", color1);
                  /*  mapPtrContainer.css("border-style", "solid");
                    mapPtrContainer.css("border-width", "0.5px");
                    mapPtrContainer.css("border-color", "LightGrey"); */
                }

                rowHeight = $("#<?= $_REQUEST['name_w'] ?>_content").height() * rowPercHeight / 100;
                iconSize = parseInt(rowHeight * 0.75);
                var pinMsgFontSize = rowHeight / 3.25;
                var pinMsgFontDescSize = pinMsgFontSize - 8;
                var iconTextDataAttr = null;
                if (iconTextMode == "Icon Only") {
                    iconTextDataAttr = "icon";
                } else {
                    iconTextDataAttr = "text";
                }
                var pinattr = null;
                if (mapPinIcon == "Pin Icon") {
                    pinattr = "pin";
                } else {
                    pinattr = "square";
                }
                // Questa PRIMA SOTTO OK !
            //    pinContainer = $('<div class="gisPinContainer" data-toggle="tooltip" data-container="body"><a class="gisPinLink" data-fontColor="' + fontColor + '" data-activeFontColor="' + activeFontColor + '" data-symbolMode="' + symbolMode + '" data-desc="' + desc + '" data-query="' + query + '" data-queryType="' + queryType + '" data-color1="' + color1 + '" data-color2="' + color2 + '" data-targets="' + targets + '" data-display="' + display + '" data-onMap="false" data-iconTextMode="' + iconTextDataAttr + '"><span class="gisPinShowMsg" style="font-size: ' + pinMsgFontSize + 'px">show</span><span class="gisPinHideMsg" style="font-size: ' + pinMsgFontSize + 'px">hide</span><span class="gisPinNoQueryMsg" style="font-size: ' + pinMsgFontSize + 'px">no query</span></span><span class="gisPinNoMapsMsg" style="font-size: ' + pinMsgFontSize + 'px">no maps set</span><i class="material-icons gisPinIcon">navigation</i><div id = "' + widgetName + '_poolIcon_' + i + '" class = "poolIcon"><img class="svg" id="logo"></div><div class="gisPinCustomIcon"><div class="gisPinCustomIconUp"></div><div class="gisPinCustomIconDown"><span><i class="fa fa-check"></i></span></div></div></a><i class="fa fa-circle-o-notch fa-spin gisLoadingIcon"></i><i class="fa fa-close gisLoadErrorIcon"></i></div>');
                // LAST BACKGROUND ARANCIONE
            //    pinContainer = $('<div class="gisPinContainer" data-toggle="tooltip" data-container="body"><a class="gisPinLink" data-fontColor="' + fontColor + '" data-activeFontColor="' + activeFontColor + '" data-symbolMode="' + symbolMode + '" data-desc="' + desc + '" data-query="' + query + '" data-queryType="' + queryType + '" data-color1="' + color1 + '" data-color2="' + color2 + '" data-targets="' + targets + '" data-display="' + display + '" data-onMap="false" data-iconTextMode="' + iconTextDataAttr + '"><span class="gisPinShowMsg" style="font-size: ' + pinMsgFontSize + 'px">show</span><span class="gisPinHideMsg" style="font-size: ' + pinMsgFontSize + 'px">hide</span><span class="gisPinNoQueryMsg" style="font-size: ' + pinMsgFontSize + 'px">no query</span></span><span class="gisPinNoMapsMsg" style="font-size: ' + pinMsgFontSize + 'px">no maps set</span><i class="material-icons gisPinIcon">navigation</i><div id = "' + widgetName + '_poolIcon_' + i + '" class = "poolIcon" style="background-image: url(&quot;../img/widgetSelectorBakcGrnd/cerchio-arancione.svg&quot;); background-repeat: no-repeat; background-position: center center;"><img class="svg" id="logo"></div><div class="gisPinCustomIcon"><div class="gisPinCustomIconUp"></div><div class="gisPinCustomIconDown"><span><i class="fa fa-check"></i></span></div></div></a><i class="fa fa-circle-o-notch fa-spin gisLoadingIcon"></i><i class="fa fa-close gisLoadErrorIcon"></i></div>');
                // PROVA CON BACKGROUND-COLOR e BORDER-RADIUS!!!
                if (symbolColor != null) {
                    if (queries[i].bubble != "CustomPin" && queries[i].bubble != "DynamicCustomPin") {
                        pinContainer = $('<div class="gisPinContainer" data-toggle="tooltip" data-container="body"><a id="' + widgetName + '_pinCtn' + i + '" class="gisPinLink" data-fontColor="' + fontColor + '" data-activeFontColor="' + activeFontColor + '" data-symbolMode="' + symbolMode + '" data-desc="' + desc + '" data-query="' + query + '" data-queryType="' + queryType + '" data-color1="' + color1 + '" data-color2="' + color2 + '" data-targets="' + targets + '" data-display="' + display + '" data-onMap="false" data-iconTextMode="' + iconTextDataAttr + '" data-pinattr="' + pinattr + '" data-pincolor="' + newMapPinColor + '" data-symbolcolor="' + symbolColor + '" data-bubbleMode="' + altViewMode + '" data-bubbleSelectedMetric="' + bubbleSelectedMetric + '" data-bubbleMetricsArray="loading available metrics..."><span class="gisPinShowMsg" style="font-size: ' + pinMsgFontSize + 'px">show</span><span class="gisPinHideMsg" style="font-size: ' + pinMsgFontSize + 'px">hide</span><span class="gisPinNoQueryMsg" style="font-size: ' + pinMsgFontSize + 'px">no query</span></span><span class="gisPinNoMapsMsg" style="font-size: ' + pinMsgFontSize + 'px">no maps set</span><i class="material-icons gisPinIcon">navigation</i><div id = "' + widgetName + '_poolIcon_' + i + '" class = "poolIcon" style="background-color: ' + symbolColor + '; border-radius: 50%"><img class="svg" id="logo"></div><div class="gisPinCustomIcon"><div class="gisPinCustomIconUp"></div><div class="gisPinCustomIconDown"><span><i class="fa fa-check"></i></span></div></div></a><i class="fa fa-circle-o-notch fa-spin gisLoadingIcon"></i><i class="fa fa-close gisLoadErrorIcon"></i></div>');
                    } else {
                        pinContainer = $('<div class="gisPinContainer" data-toggle="tooltip" data-container="body"><a id="' + widgetName + '_pinCtn' + i + '" class="gisPinLink" data-fontColor="' + fontColor + '" data-activeFontColor="' + activeFontColor + '" data-symbolMode="' + symbolMode + '" data-desc="' + desc + '" data-query="' + query + '" data-queryType="' + queryType + '" data-color1="' + color1 + '" data-color2="' + color2 + '" data-targets="' + targets + '" data-display="' + display + '" data-onMap="false" data-iconTextMode="' + iconTextDataAttr + '" data-pinattr="' + pinattr + '" data-pincolor="' + newMapPinColor + '" data-symbolcolor="' + symbolColor + '" data-bubbleMode="' + altViewMode + '" data-bubbleSelectedMetric="' + bubbleSelectedMetric + '" data-bubbleMetricsArray="loading available metrics..."><span class="gisPinShowMsg" style="font-size: ' + pinMsgFontSize + 'px">show</span><span class="gisPinHideMsg" style="font-size: ' + pinMsgFontSize + 'px">hide</span><span class="gisPinNoQueryMsg" style="font-size: ' + pinMsgFontSize + 'px">no query</span></span><span class="gisPinNoMapsMsg" style="font-size: ' + pinMsgFontSize + 'px">no maps set</span><i class="material-icons gisPinIcon">navigation</i><div id = "' + widgetName + '_poolIcon_' + i + '" class = "poolIcon"><img class="svg" id="logo"></div><div class="gisPinCustomIcon"><div class="gisPinCustomIconUp"></div><div class="gisPinCustomIconDown"><span><i class="fa fa-check"></i></span></div></div></a><i class="fa fa-circle-o-notch fa-spin gisLoadingIcon"></i><i class="fa fa-close gisLoadErrorIcon"></i></div>');
                    }
                } else {
                    if (pinattr == "pin") {
                        //  if (symbolColor == undefined) {
                        //   symbolColor = "rgba(204,203,203,1)";
                        $.ajax({
                            url: "../controllers/getDefaultPinColor.php",
                            type: "GET",
                            data: {
                                "nature": nature,
                                "subNature": subNature
                            },
                            async: false,
                            dataType: 'json',
                            success: function (data) {
                                symbolColor = data.defColour
                            },
                            error: function (errorData) {
                                console.log("Error in get Default Pin Color");
                                console.log(JSON.stringify(errorData));
                            }
                        });

                        //   } else {
                        //       symbolColorOk = symbolColor;
                        //   }
                    }
                    pinContainer = $('<div class="gisPinContainer" data-toggle="tooltip" data-container="body"><a id="' + widgetName +'_pinCtn' + i + '" class="gisPinLink" data-fontColor="' + fontColor + '" data-activeFontColor="' + activeFontColor + '" data-symbolMode="' + symbolMode + '" data-desc="' + desc + '" data-query="' + query + '" data-queryType="' + queryType + '" data-color1="' + color1 + '" data-color2="' + color2 + '" data-targets="' + targets + '" data-display="' + display + '" data-onMap="false" data-iconTextMode="' + iconTextDataAttr + '" data-pinattr="' + pinattr + '" data-pincolor="' + newMapPinColor + '" data-symbolcolor="' + symbolColor + '" data-bubbleMode="' + altViewMode + '" data-bubbleSelectedMetric="' + bubbleSelectedMetric + '" data-bubbleMetricsArray="loading available metrics..."><span class="gisPinShowMsg" style="font-size: ' + pinMsgFontSize + 'px">show</span><span class="gisPinHideMsg" style="font-size: ' + pinMsgFontSize + 'px">hide</span><span class="gisPinNoQueryMsg" style="font-size: ' + pinMsgFontSize + 'px">no query</span></span><span class="gisPinNoMapsMsg" style="font-size: ' + pinMsgFontSize + 'px">no maps set</span><i class="material-icons gisPinIcon">navigation</i><div id = "' + widgetName + '_poolIcon_' + i + '" class = "poolIcon"><img class="svg" id="logo"></div><div class="gisPinCustomIcon"><div class="gisPinCustomIconUp"></div><div class="gisPinCustomIconDown"><span><i class="fa fa-check"></i></span></div></div></a><i class="fa fa-circle-o-notch fa-spin gisLoadingIcon"></i><i class="fa fa-close gisLoadErrorIcon"></i></div>');
                }
             //   pinContainer = $('<div class="gisPinContainer" data-toggle="tooltip" data-container="body"><a class="gisPinLink" data-fontColor="' + fontColor + '" data-activeFontColor="' + activeFontColor + '" data-symbolMode="' + symbolMode + '" data-desc="' + desc + '" data-query="' + query + '" data-queryType="' + queryType + '" data-color1="' + color1 + '" data-color2="' + color2 + '" data-targets="' + targets + '" data-display="' + display + '" data-onMap="false" data-iconTextMode="' + iconTextDataAttr + '"><span class="gisPinShowMsg" style="font-size: ' + pinMsgFontSize + 'px">show</span><span class="gisPinHideMsg" style="font-size: ' + pinMsgFontSize + 'px">hide</span><span class="gisPinNoQueryMsg" style="font-size: ' + pinMsgFontSize + 'px">no query</span></span><span class="gisPinNoMapsMsg" style="font-size: ' + pinMsgFontSize + 'px">no maps set</span><i class="material-icons gisPinIcon">navigation</i><div id = "' + widgetName + '_poolIcon_' + i + '" class = "poolIcon" style="position: relative; width: ' + rowHeight + '; height: ' + rowHeight + ';"><img src="../img/widgetSelectorBakcGrnd/cerchio-arancione.svg" alt="" style="top: 0; left: 0;"><img class="svg" id="logo" alt="" style="position: absolute; top: 0; left: 0;"></div><div class="gisPinCustomIcon"><div class="gisPinCustomIconUp"></div><div class="gisPinCustomIconDown"><span><i class="fa fa-check"></i></span></div></div></a><i class="fa fa-circle-o-notch fa-spin gisLoadingIcon"></i><i class="fa fa-close gisLoadErrorIcon"></i></div>');
                if (iconTextMode == "Icon Only") {
                    pinContainer.attr("title", desc);
                }
                var hoverTooltipText = pinContainer.children("a.gisPinLink").attr("data-desc");
            //    pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).css('height', ($("#<?= $_REQUEST['name_w'] ?>_content").height()/queries.length) * 1.1);
                pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).css('height', ($("#<?= $_REQUEST['name_w'] ?>_content").height()/queries.length) * 0.9);

                if (queries[i].bubble == 'CustomPin' || queries[i].bubble == 'DynamicCustomPin') {
                    svgContainer = $('<div id="' + widgetName + '_svgCtn' + i + '">');
                    pinContainer.append(svgContainer);
                }

                if (symbolMode === 'auto') {
                    pinContainer.find('div.gisPinCustomIcon').hide();
                    if (iconTextMode == "Icon Only") {
                        if (queries[i].iconPoolImg) {
                            if (queries[i].bubble == 'CustomPin' || queries[i].bubble == 'DynamicCustomPin') {     // SVG CustomPin Icon MOD
                                let tplPath, icBlck, icWht = "";
                                if(queries[i].iconPoolImg.split('synopticTemplates/svg')) {
                                    if(queries[i].iconPoolImg.split('synopticTemplates/svg')[1] != null) {
                                    //    tplPath = queries[i].iconPoolImg.split("/img/")[0] + '/img/outputPngIcons/_synoptic/' + queries[i].iconPoolImg.split('synopticTemplates/svg/')[1];
                                        tplPath = queries[i].iconPoolImg;

                                         //   icBlck = '../controllers/valuedisplay.html?val=0&tpl=' + queries[i].iconPoolImg;

                                        icBlckArray[i] = buildSvgIcon (tplPath, 0, 'error', pinContainer, svgContainer, widgetName, "selector");
                                    //    icBlckArray[i] = buildSvgIcon (tplPath, 0, 'error', pinContainer, pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0), widgetName);

                                        //    icWht = '../controllers/valuedisplay.html?val=1&tpl=' + queries[i].iconPoolImg;
                                        //    icWhtArray[i] = buildSvgIcon (tplPath, 1, 'error', pinContainer, svgContainer, widgetName);
                                    //    icBlck = 'http://localhost/dashboardSmartCIty/controllers/valuedisplay.html?val=0&tpl=' + queries[i].iconPoolImg;
                                    //    icWht = 'http://localhost/dashboardSmartCIty/controllers/valuedisplay.html?val=1&tpl=' + queries[i].iconPoolImg;

                                    /*    pinContainer.children("a.gisPinLink").children("div.poolIcon").children(0).attr("src", icBlck);   */
                                        pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconblack", tplPath);
                                        pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconwhite", "");
                                        pinContainer.find('#' + widgetName + '_poolIcon' + i).show();
                                        iconPath = queries[i].iconPoolImg;
                                        iconWhitePath = iconWhitePathAuto;
                                    }
                                }
                            } else {
                                pinContainer.children("a.gisPinLink").children("div.poolIcon").children(0).attr("src", queries[i].iconPoolImg);
                                pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconblack", queries[i].iconPoolImg);
                                var iconWhitePathAuto = queries[i].iconPoolImg.split(".svg")[0] + "-white.svg";
                                pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconwhite", iconWhitePathAuto);
                                pinContainer.find('#' + widgetName + '_poolIcon' + i).show();
                                iconPath = queries[i].iconPoolImg;
                                iconWhitePath = iconWhitePathAuto;
                            }
                        } else {
                            if (nature) {
                                if (nature.replace(/\s/g, '') == "MobilityandTransport") {
                                    nature = "TransferServiceAndRenting";
                                }
                                if (subNature) {
                                    // iconPath = "../img/widgetSelectorIconsPool/subnature/"+ nature + "/" + nature + "_" + subNature + ".svg";
                                    iconPath = "../img/widgetSelectorIconsPool/subnature/" + nature + "_" + subNature + ".svg";
                                    //    iconWhitePath = "../img/widgetSelectorIconsPool/subnature/"+ nature + "/" + nature + "_" + subNature + "-white.svg";
                                    iconWhitePath = "../img/widgetSelectorIconsPool/subnature/" + nature + "_" + subNature + "-white.svg";
                                } else {
                                    iconPath = "../img/widgetSelectorIconsPool/nature/" + nature + ".svg";
                                    iconWhitePath = "../img/widgetSelectorIconsPool/nature/" + nature + "-white.svg";
                                }
                                if (UrlExists(iconPath)) {
                                    pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("src", iconPath);
                                    pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconblack", iconPath);
                                    pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconwhite", iconWhitePath);
                                    pinContainer.find('#' + widgetName + '_poolIcon' + i).show();
                                } else {
                                    pinContainer.find('i.gisPinIcon').show();
                                    pinContainer.find('#' + widgetName + '_poolIcon' + i).hide();
                                }
                            } else if (highLevelType) {
                                iconPath = "../img/widgetSelectorIconsPool/hlt/" + highLevelType + ".svg";
                                iconWhitePath = "../img/widgetSelectorIconsPool/hlt/" + highLevelType + "-white.svg";
                                if (UrlExists(iconPath)) {
                                    pinContainer.children("a.gisPinLink").children("div.poolIcon").children(0).attr("src", iconPath);
                                    pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconblack", iconPath);
                                    pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconwhite", iconWhitePath);
                                    pinContainer.find('#' + widgetName + '_poolIcon' + i).show();
                                } else {
                                    pinContainer.find('i.gisPinIcon').show();
                                    pinContainer.find('#' + widgetName + '_poolIcon' + i).hide();
                                }
                            } else {
                                if (queries[i].iconPoolImg) {
                                    pinContainer.children("a.gisPinLink").children("div.poolIcon").children(0).attr("src", queries[i].iconPoolImg);
                                    pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconblack", queries[i].iconPoolImg);
                                    var iconWhitePathAuto = queries[i].iconPoolImg.split(".svg")[0] + "-white.svg";
                                    pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconwhite", iconWhitePathAuto);
                                    pinContainer.find('#' + widgetName + '_poolIcon' + i).show();
                                } else {
                                    pinContainer.find('i.gisPinIcon').show();
                                    pinContainer.find('#' + widgetName + '_poolIcon' + i).hide();
                                }
                            }
                        }
                    } else {
                        if (queries[i].bubble == 'CustomPin' || queries[i].bubble == 'DynamicCustomPin') {     // SVG CustomPin Icon MOD
                            let tplPath, icBlck, icWht = "";
                            if (queries[i].iconPoolImg != null) {
                                if (queries[i].iconPoolImg.split('synopticTemplates/svg')) {
                                    if (queries[i].iconPoolImg.split('synopticTemplates/svg')[1] != null) {
                                        //    tplPath = queries[i].iconPoolImg.split("/img/")[0] + '/img/outputPngIcons/_synoptic/' + queries[i].iconPoolImg.split('synopticTemplates/svg/')[1];
                                        tplPath = queries[i].iconPoolImg;

                                        //   icBlck = '../controllers/valuedisplay.html?val=0&tpl=' + queries[i].iconPoolImg;

                                        icBlckArray[i] = buildSvgIcon(tplPath, 0, 'error', pinContainer, svgContainer, widgetName, "selector");
                                        //    icBlckArray[i] = buildSvgIcon (tplPath, 0, 'error', pinContainer, pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0), widgetName);

                                        //    icWht = '../controllers/valuedisplay.html?val=1&tpl=' + queries[i].iconPoolImg;
                                        //    icWhtArray[i] = buildSvgIcon (tplPath, 1, 'error', pinContainer, svgContainer, widgetName);
                                        //    icBlck = 'http://localhost/dashboardSmartCIty/controllers/valuedisplay.html?val=0&tpl=' + queries[i].iconPoolImg;
                                        //    icWht = 'http://localhost/dashboardSmartCIty/controllers/valuedisplay.html?val=1&tpl=' + queries[i].iconPoolImg;

                                        /*    pinContainer.children("a.gisPinLink").children("div.poolIcon").children(0).attr("src", icBlck);   */
                                        pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconblack", tplPath);
                                        pinContainer.find('#' + widgetName + '_poolIcon_' + i).children(0).attr("data-iconwhite", "");
                                        pinContainer.find('#' + widgetName + '_poolIcon' + i).show();
                                        iconPath = queries[i].iconPoolImg;
                                        iconWhitePath = iconWhitePathAuto;
                                    }
                                }
                            }
                            pinContainer.find('i.gisPinIcon').hide();
                            pinContainer.find('#' + widgetName + '_poolIcon').show();
                        } else {
                            pinContainer.find('i.gisPinIcon').show();
                            pinContainer.find('#' + widgetName + '_poolIcon').hide();
                        }
                    }
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
             //   if (queries[i].newMapPinColor == "SymbolColor") {
                if (pinattr == "pin" && (queryType == "Default" || queryType == "Sensor")) {
                //    if (i > 0) {
                    if (newPinsContainer != null) {
                        newPinsContainer = newPinsContainer + makeNewPinIcon(queries[i].nature, queries[i].sub_nature, queries[i].symbolColor, iconPath, iconWhitePath, i, widgetTargetList[0].split("_widget")[1]);
                    } else {
                        newPinsContainer = makeNewPinIcon(queries[i].nature, queries[i].sub_nature, queries[i].symbolColor, iconPath, iconWhitePath, i, widgetTargetList[0].split("_widget")[1]);
                    }
                //    newRow.append(newPinsContainer);
                }

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
                                    // HOVER NON SELEZIONATO
                                    if ($(this).attr("data-symbolMode") === 'auto') {
                                        $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").hide();
                                    }
                                    else {
                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                                    }
                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();

                                    if ($(this).attr("data-color1").match(transparentColorPattern)) {
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").css("color", fontColor);
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").css("text-shadow", "none");
                                    }

                                //    $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").show();
                                    // NEW SELECTOR
                                /*    if ($('#<?= $_REQUEST['name_w'] ?>').attr("data-icontextmode") == "Icon Only") {
                                        if (newStyleSelectorFlag && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                            // Attualmente nessuna azione di hover con icone da nuovo Pool
                                        } else {
                                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").show();
                                        }
                                    } else {*/
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").show();
                                //    }
                                }
                                else {
                                    // HOVER SELEZIONATO
                                    if ($(this).attr("data-symbolMode") === 'auto') {
                                        if (newStyleSelectorFlag && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                            $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").hide();
                                        } else {
                                            $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                                        }
                                    }
                                    else {
                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                                    }
                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();

                                    if ($(this).attr("data-color1").match(transparentColorPattern)) {
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").css("color", fontColor);
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").css("text-shadow", "none");
                                    }

                                //    $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").show();
                                    // NEW SELECTOR
                                /*    if ($('#<?= $_REQUEST['name_w'] ?>').attr("data-icontextmode") == "Icon Only") {
                                        if (newStyleSelectorFlag && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                            // Attualmente nessuna azione di hover con icone da nuovo Pool
                                        } else {
                                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").show();
                                        }
                                    } else {*/
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").show();
                                //    }
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
                                    // NEW SELECTOR
                               //     if ($('#<?= $_REQUEST['name_w'] ?>').attr("data-icontextmode") == "Icon Only") {
                                        if (newStyleSelectorFlag && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                            // Attualmente nessuna azione di mouse out con icone da nuovo Pool
                                            $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").show();
                                        } else {
                                            $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                                        }
                                 //   } else {
                                 //       $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                                //    }
                                }
                                else {
                                /*    if ($('#<?= $_REQUEST['name_w'] ?>').attr("data-icontextmode") == "Icon Only") {
                                        if (newStyleSelectorFlag && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                            // Attualmente nessuna azione di mouse out con icone da nuovo Pool
                                        } else {
                                            $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                                        }
                                    } else {*/
                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                                //    }
                                }
                            }
                        }
                        else {
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoQueryMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoMapsMsg").hide();
                            if ($(this).attr("data-symbolMode") === 'auto') {
                                // NEW SELECTOR
                                if ($('#<?= $_REQUEST['name_w'] ?>').attr("data-icontextmode") == "Icon Only") {
                                    if (newStyleSelectorFlag && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                        // Attualmente nessuna azione di mouse out con icone da nuovo Pool
                                    } else {
                                        $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                                    }
                                } else {
                                    $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                                }
                            }
                            else {
                                // NEW SELECTOR
                                if ($('#<?= $_REQUEST['name_w'] ?>').attr("data-icontextmode") == "Icon Only") {
                                    if (newStyleSelectorFlag && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                        // Attualmente nessuna azione di mouse out con icone da nuovo Pool
                                    } else {
                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                                    }
                                } else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                                }
                            }
                        }
                    }
                );

                pinContainer.find("a.gisPinLink").click(function (event) {
                        event.preventDefault();

                        // NEW ADJUSTED
                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                        if ($(this).attr("data-symbolMode") === 'auto') {
                            if ($(this).attr("data-iconTextMode") == "text" || $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") == null) {
                                if ($(this).parents("div.gisPinContainer").children()[0].attributes['data-bubbleMode'].value == "CustomPin" || $(this).parents("div.gisPinContainer").children()[0].attributes['data-bubbleMode'].value == "DynamicCustomPin") {
                                    buildSvgIcon ($(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"), 9999, 'error', $(this).parents("div.gisPinContainer"), $('#' + $(this).parents("div.gisPinContainer").children()[3].id), widgetName, "selector");
                                } else {
                                    $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                                }
                            } else {
                                if ($(this).parents("div.gisPinContainer").children()[0].attributes['data-bubbleMode'].value == "CustomPin" || $(this).parents("div.gisPinContainer").children()[0].attributes['data-bubbleMode'].value == "DynamicCustomPin") {
                                    buildSvgIcon ($(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"), 9999, 'error', $(this).parents("div.gisPinContainer"), $('#' + $(this).parents("div.gisPinContainer").children()[3].id), widgetName, "selector");
                                } else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconwhite"))
                                    $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").show();
                                }
                            }
                        }
                        else {
                            $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                        }

                        // Cristiano : Dynamic Routing
                        if($(this).attr("data-query").includes("scenario") && !$(this).attr("data-query").includes("trafficflow/") && widgetTargetList.length > 0) {
                            
                            if ($(this).attr("data-onMap") === "false") {
                                $(this).attr("data-onMap", "true");
                                $(this).find("i.gisPinIcon").html("near_me");
                                $(this).find("i.gisPinIcon").css("color", "white");
                                $(this).find("i.gisPinIcon").css("text-shadow", "black 2px 2px 4px");

                                $.event.trigger({
                                    type: "addScenario",
                                    target: widgetTargetList[0]
                                });
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                    } else {
                                        $(this).find("i.gisPinIcon").html("navigation");
                                        $(this).find("i.gisPinIcon").css("color", "black");
                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                    }
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                $.event.trigger({
                                    type: "removeScenario",
                                    target: widgetTargetList[0],
                                });
                            }
                        }
                        if($(this).attr("data-query").includes("whatif") && widgetTargetList.length > 0) {
                            
                            if ($(this).attr("data-onMap") === "false") {
                                $(this).attr("data-onMap", "true");
                                $(this).find("i.gisPinIcon").html("near_me");
                                $(this).find("i.gisPinIcon").css("color", "white");
                                $(this).find("i.gisPinIcon").css("text-shadow", "black 2px 2px 4px");

                                $.event.trigger({
                                    type: "addWhatif",
                                    target: widgetTargetList[0]
                                });
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                    } else {
                                        $(this).find("i.gisPinIcon").html("navigation");
                                        $(this).find("i.gisPinIcon").css("color", "black");
                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                    }
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                $.event.trigger({
                                    type: "removeWhatif",
                                    target: widgetTargetList[0],
                                });
                            }
                        }
                        // end Cristiano

                        //TrafficRealTimeDetails - Stefano
                        if (($(this).attr("data-query").includes("trafficRTDetails")) && (widgetTargetList.length > 0)) {

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
                                    if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                    } else {
                                        $(this).find("i.gisPinIcon").html("navigation");
                                        $(this).find("i.gisPinIcon").css("color", "black");
                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                    }
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

                        //Collini: Traffic Scenary Builder trafficscenarybuilder
                        if (($(this).attr("data-query").includes("scenary")) && (widgetTargetList.length > 0)) {

                            if ($(this).attr("data-onMap") === "false") {
                                $(this).attr("data-onMap", "true");
                                $(this).find("i.gisPinIcon").html("near_me");
                                $(this).find("i.gisPinIcon").css("color", "white");
                                $(this).find("i.gisPinIcon").css("text-shadow", "black 2px 2px 4px");

                                var coordsAndType = $(this).attr("data-query");

                                $.event.trigger({
                                    type: "addTrafficScenary",
                                    target: widgetTargetList[0],
                                    //passedData: coordsAndType
                                });
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                    } else {
                                        $(this).find("i.gisPinIcon").html("navigation");
                                        $(this).find("i.gisPinIcon").css("color", "black");
                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                    }
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                $.event.trigger({
                                    type: "removeTrafficScenary",
                                    target: widgetTargetList[0]
                                });
                            }
                        }

                        //Collini: Traffic Scenary Builder trafficscenarybuilder FASE2
                        if (($(this).attr("data-query").includes("Fase2scenar")) && (widgetTargetList.length > 0)) {

                            if ($(this).attr("data-onMap") === "false") {
                                $(this).attr("data-onMap", "true");
                                $(this).find("i.gisPinIcon").html("near_me");
                                $(this).find("i.gisPinIcon").css("color", "white");
                                $(this).find("i.gisPinIcon").css("text-shadow", "black 2px 2px 4px");

                                var coordsAndType = $(this).attr("data-query");

                                $.event.trigger({
                                    type: "Fase2addTrafficScenar",
                                    target: widgetTargetList[0],
                                    //passedData: coordsAndType
                                });
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                    } else {
                                        $(this).find("i.gisPinIcon").html("navigation");
                                        $(this).find("i.gisPinIcon").css("color", "black");
                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                    }
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                $.event.trigger({
                                    type: "Fase2removeTrafficScenar",
                                    target: widgetTargetList[0]
                                });
                            }
                        }

                        //Heatmap - Daniele
                        if (($(this).attr("data-query").includes("heatmap.php") || $(this).attr("data-query").includes(geoServerUrl + "geoserver") || $(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") || $(this).attr("data-query").includes("<?= $kbUrlSuperServiceMap ?>" + "trafficflow")) && (widgetTargetList.length > 0)) {

                            if ($(this).attr("data-onMap") === "false") {

                                var thisQuery = $(this).attr("data-query");
                                var sourceSelector = event.currentTarget.offsetParent;

                                const isAddingTrafficFlowManagerHeatmap = $(this).attr("data-query").includes("&trafficflowmanager=true") || $(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") || $(this).attr("data-query").includes("<?= $kbUrlSuperServiceMap ?>" + "trafficflow");

                                $('.gisPinLink').each(function( index ) {
                                    if((($(this).attr("data-query").includes("heatmap.php") || $(this).attr("data-query").includes(geoServerUrl + "geoserver")) && $(this).attr("data-query") != thisQuery) || $(this).attr("data-query").includes("<?= $od_hostname ?>") || $(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") || $(this).attr("data-query").includes("<?= $kbUrlSuperServiceMap ?>" + "trafficflow")) {
                                        if (sourceSelector == $(this).offsetParent()[0]) {
                                            if ($(this).attr("data-onMap") === "true") {

                                                // logica additività trafficflowmanager
                                                // non rimuovere pin dal selettore se:
                                                // 1. cliccato su heatmap e sto rimuovendo pin traffico, oppure
                                                // 2. cliccato su traffico e sto rimuovendo pin heatmap
                                                const isRemovingTrafficFlowManagerPin = $(this).attr("data-query").includes("&trafficflowmanager=true") || $(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") || $(this).attr("data-query").includes("<?= $kbUrlSuperServiceMap ?>" + "trafficflow");
                                                if ((!isAddingTrafficFlowManagerHeatmap && isRemovingTrafficFlowManagerPin) || (isAddingTrafficFlowManagerHeatmap && !isRemovingTrafficFlowManagerPin)) {
                                                    return;
                                                }

                                                $(this).attr("data-onMap", "false");
                                                if ($(this).attr("data-symbolMode") === 'auto') {
                                                    if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                                    } else {
                                                        $(this).find("i.gisPinIcon").html("navigation");
                                                        $(this).find("i.gisPinIcon").css("color", "black");
                                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                                    }
                                                } else {
                                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                                }
                                            }
                                        }
                                    }
                                });

                                $(this).attr("data-onMap", "true");
                                $(this).find("i.gisPinIcon").html("near_me");
                                $(this).find("i.gisPinIcon").css("color", "white");
                                $(this).find("i.gisPinIcon").css("text-shadow", "black 2px 2px 4px");
                                let coordsAndType = $(this).attr("data-query");
                                let passedParams = {};
                                passedParams.desc = $(this).attr("data-desc");
                                passedParams.color1 = $(this).attr("data-color1");
                                passedParams.color2 = $(this).attr("data-color2");
                                passedParams.desc = $(this).attr("data-desc");
                                passedParams.targets = $(this).attr("data-targets");

                                $.event.trigger({
                                    type: "addHeatmap",
                                    target: widgetTargetList[0],
                                    passedData: coordsAndType,
                                    passedParams: passedParams
                                });
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                    } else {
                                        $(this).find("i.gisPinIcon").html("navigation");
                                        $(this).find("i.gisPinIcon").css("color", "black");
                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                    }
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                const isTrafficHeatmap = $(this).attr("data-query").includes("&trafficflowmanager=true") || $(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") || $(this).attr("data-query").includes("<?= $kbUrlSuperServiceMap ?>" + "trafficflow");

                                $.event.trigger({
                                    type: "removeHeatmap",
                                    target: widgetTargetList[0],
                                    isTrafficHeatmap: isTrafficHeatmap
                                });
                            }
                        }

                        //ODMatrix
                        if ($(this).attr("data-query").includes("<?= $od_hostname ?>") && (widgetTargetList.length > 0)) {
                            if ($(this).attr("data-onMap") === "false") {
                                var sourceSelector = event.currentTarget.offsetParent;

                                const isAddingTrafficFlowManagerHeatmap = $(this).attr("data-query").includes("&trafficflowmanager=true") || $(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") || $(this).attr("data-query").includes("<?= $kbUrlSuperServiceMap ?>" + "trafficflow");

                                $('.gisPinLink').each(function( index ) {
                                    if((($(this).attr("data-query").includes("heatmap.php") || $(this).attr("data-query").includes(geoServerUrl + "geoserver")) && $(this).attr("data-query") != thisQuery) || $(this).attr("data-query").includes("<?= $od_hostname ?>") || $(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") || $(this).attr("data-query").includes("<?= $kbUrlSuperServiceMap ?>" + "trafficflow")) {
                                    //if($(this).attr("data-query").includes("wmsserver.snap4city.org")) {
                                        if (sourceSelector == $(this).offsetParent()[0]) {
                                            if ($(this).attr("data-onMap") === "true") {

                                                const isRemovingTrafficFlowManagerPin = $(this).attr("data-query").includes("&trafficflowmanager=true") || $(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") || $(this).attr("data-query").includes("<?= $kbUrlSuperServiceMap ?>" + "trafficflow");
                                                if ((!isAddingTrafficFlowManagerHeatmap && isRemovingTrafficFlowManagerPin) || (isAddingTrafficFlowManagerHeatmap && !isRemovingTrafficFlowManagerPin)) {
                                                    return;
                                                }

                                                $(this).attr("data-onMap", "false");
                                                if ($(this).attr("data-symbolMode") === 'auto') {
                                                    if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                                    } else {
                                                        $(this).find("i.gisPinIcon").html("navigation");
                                                        $(this).find("i.gisPinIcon").css("color", "black");
                                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                                    }
                                                } else {
                                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                                }
                                            }
                                        }
                                    }
                                });

                                $(this).attr("data-onMap", "true");
                                $(this).find("i.gisPinIcon").html("near_me");
                                $(this).find("i.gisPinIcon").css("color", "white");
                                $(this).find("i.gisPinIcon").css("text-shadow", "black 2px 2px 4px");
                                let coordsAndType = $(this).attr("data-query");
                                let passedParams = {};
                                passedParams.desc = $(this).attr("data-desc");
                                passedParams.color1 = $(this).attr("data-color1");
                                passedParams.color2 = $(this).attr("data-color2");

                                $.event.trigger({
                                    type: "addOD",
                                    target: widgetTargetList[0],
                                    passedData: coordsAndType,
                                    passedParams: passedParams
                                });
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                        $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                    } else {
                                        $(this).find("i.gisPinIcon").html("navigation");
                                        $(this).find("i.gisPinIcon").css("color", "black");
                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                    }
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                $.event.trigger({
                                    type: "removeOD",
                                    target: widgetTargetList[0]
                                });
                            }
                        }

              //        if ((($(this).attr("data-query").includes("scenario")) !== true ) && (($(this).attr("data-query").includes("whatif")) !== true) &&  (($(this).attr("data-query").includes("Fase2scenar")) !== true) && (($(this).attr("data-query").includes("trafficRTDetails")) !== true) && (($(this).attr("data-query").includes("scenary")) !== true) && (($(this).attr("data-query").includes("heatmap.php") || $(this).attr("data-query").includes(geoServerUrl + "geoserver")) !== true) && (($(this).attr("data-query").includes("<?= $od_hostname ?>")) !== true) && $(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") !== true && (widgetTargetList.length > 0)) {
                        if (!$(this).attr("data-query").includes("scenario") &&
                            !$(this).attr("data-query").includes("whatif") &&
                            !$(this).attr("data-query").includes("Fase2scenar") &&
                            !$(this).attr("data-query").includes("trafficRTDetails") &&
                            !$(this).attr("data-query").includes("scenary") &&
                            !$(this).attr("data-query").includes("heatmap.php") &&
                            !$(this).attr("data-query").includes(geoServerUrl + "geoserver") &&
                            !$(this).attr("data-query").includes("<?= $od_hostname ?>") &&
                            !$(this).attr("data-query").includes("<?= $serviceMapUrlForTrendApi ?>" + "trafficflow") &&
                            !$(this).attr("data-query").includes("<?= $kbUrlSuperServiceMap ?>" + "trafficflow") &&
                            widgetTargetList.length > 0) {

                            if ($(this).attr("data-onMap") === "false") {

                                // BIM-Shape
                                if (($(this).attr("data-bubbleMode") == "BimShape" || $(this).attr("data-bubbleMode") == "BimShapePopup")) {
                                //if (($(this).attr("data-bubbleMode") == "BimShape" || $(this).attr("data-bubbleMode") == "BimShapePopup") && $(this).attr("data-query").includes("&model=")) {
                                    var sourceSelector = event.currentTarget.offsetParent;
                                    var count=0;
                                    $('.gisPinLink').each(function( index ) {
                                        if (sourceSelector == $(this).offsetParent()[0]) {
                                            if(JSON.parse(widgetProperties.param.parameters).queries[index-count].bubble && JSON.parse(widgetProperties.param.parameters).queries[index-count].bubble.includes("BimShape")) {
                                                if ($(this).attr("data-onMap") === "true") {
                                                    $(this).attr("data-onMap", "false");
                                                    if ($(this).attr("data-symbolMode") === 'auto') {
                                                        if ($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) {
                                                            $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                                        } else {
                                                            $(this).find("i.gisPinIcon").html("navigation");
                                                            $(this).find("i.gisPinIcon").css("color", "black");
                                                            $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                                        }
                                                    } else {
                                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                                    }
                                                }
                                            }
                                        } else {
                                            count++;
                                        }

                                    });
                                }

                                $(this).attr("data-onMap", "true");
                                if ($(this).attr("data-pinattr") == "pin") {

                                } else {
                                    $(this).find("i.gisPinIcon").html("near_me");
                                    $(this).find("i.gisPinIcon").css("color", "white");
                                    $(this).find("i.gisPinIcon").css("text-shadow", "black 2px 2px 4px");
                                }
                            //    $(this).hide();
                            //    $(this).parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").show();
                                addLayerToTargetMaps($(this), $(this).attr("data-desc"), $(this).attr("data-query"), $(this).attr("data-color1"), $(this).attr("data-color2"), $(this).attr("data-targets"), $(this).attr("data-display"), $(this).attr("data-queryType"), $(this).attr("data-icontextmode"), $(this).attr("data-pinattr"), $(this).attr("data-pincolor"), $(this).attr("data-symbolcolor"), $(this).find('img.svg').attr("data-iconblack"), $(this).attr("data-bubbleMode"), $(this).attr("data-bubbleselectedmetric"));
                            }
                            else {
                                $(this).attr("data-onMap", "false");
                                if ($(this).attr("data-symbolMode") === 'auto') {
                                    if (($(this).attr("data-iconTextMode") == "icon" && $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src") != null) || $(this).parents("div.gisPinContainer").children()[0].attributes['data-bubbleMode'].value == "CustomPin" || $(this).parents("div.gisPinContainer").children()[0].attributes['data-bubbleMode'].value == "DynamicCustomPin") {
                                        if ($(this).parents("div.gisPinContainer").children()[0].attributes['data-bubbleMode'].value == "CustomPin" || $(this).parents("div.gisPinContainer").children()[0].attributes['data-bubbleMode'].value == "DynamicCustomPin") {
                                            // Build custom SVG Icon even if text mode is selected for view
                                            buildSvgIcon ($(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"), 0, 'error', $(this).parents("div.gisPinContainer"), $('#' + $(this).parents("div.gisPinContainer").children()[3].id), widgetName, "selector");
                                        } else {
                                            $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("src", $(this).parents("div.gisMapPtrContainer").find("div.poolIcon").children(0).attr("data-iconblack"))
                                        }
                                    } else {
                                        $(this).find("i.gisPinIcon").html("navigation");
                                        $(this).find("i.gisPinIcon").css("color", "black");
                                        $(this).find("i.gisPinIcon").css("text-shadow", "none");
                                    }
                                }
                                else {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                                }

                                $(this).parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('span.gisQueryDescPar').css("font-weight", "normal");
                                $(this).parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('span.gisQueryDescPar').css("color", $(this).attr("data-fontColor"));
                                removeLayerFromTargetMaps($(this).attr("data-desc"), $(this).attr("data-query"), $(this).attr("data-color1"), $(this).attr("data-color2"), $(this).attr("data-targets"), $(this).attr("data-display"), $(this).attr("data-bubbleMode"));
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

                if(iconTextMode != "Icon Only") {
                    if (!gisMapPtrContainerWidthPerc) {
                        gisMapPtrContainerWidthPerc = Math.floor((newRow.height()) / newRow.width() * 100);
                    }
                    if (!queryDescContainerWidthPerc) {
                        queryDescContainerWidthPerc = Math.floor((newRow.width() - Math.ceil(newRow.height())) / newRow.width() * 100);
                    }
                    if ((gisMapPtrContainerWidthPerc + queryDescContainerWidthPerc) != 100) {
                        queryDescContainerWidthPerc = queryDescContainerWidthPerc + (100 - (gisMapPtrContainerWidthPerc + queryDescContainerWidthPerc));
                    }
                    mapPtrContainer.css("width", gisMapPtrContainerWidthPerc + "%");
                    queryDescContainer.css("width", queryDescContainerWidthPerc + "%");
                } else {
                    mapPtrContainer.css("width", "100%");
                    queryDescContainer.css("display", "none");
                    // DETECT AND MANAGE ICONs CONTRAST   -->     minimal recommended contrast ratio is 4.5, or 3 for larger font-sizes

                    var contrastBlack = checkColorContrast(color1, "black");
                    if (contrastBlack < 3) {
                      //  swapBlackIconWithWhite(mapPtrContainer);
                      //  alert("Too Little Contrast !");
                        var stopFlag = 3;
                    }

                }

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

        function triggerSelectorClickOnMapLoad(k) {
            if (globalMapView == true) {
                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.gisPinLink").eq(k).trigger('click');
                defaultOptionUsed = true;
            } else {
              //  setTimeout(triggerSelectorClickOnMapLoad, 50);
            }
        }

        function addLayerToTargetMaps(eventGenerator, desc, query, color1, color2, targets, display, queryType, iconTextMode, pinAttr, pinColor, symbolColor, iconFilePath, altViewMode, bubbleSelectedMetric) {
            let coordsAndType = {};

            coordsAndType.eventGenerator = eventGenerator;
            coordsAndType.desc = desc;
            coordsAndType.query = query;
            coordsAndType.color1 = color1;
            coordsAndType.color2 = color2;
            coordsAndType.targets = targets;
            coordsAndType.display = display;
            coordsAndType.queryType = queryType;
            coordsAndType.iconTextMode = iconTextMode;
            coordsAndType.pinattr = pinAttr;
            coordsAndType.pincolor = pinColor;
            coordsAndType.symbolcolor = symbolColor;
            coordsAndType.iconFilePath = iconFilePath;
            coordsAndType.altViewMode = altViewMode;
            coordsAndType.bubbleSelectedMetric = bubbleSelectedMetric;

            if (altViewMode == "Bubble" || altViewMode == "CustomPin" || altViewMode == "DynamicCustomPin") {

                $.event.trigger({
                    type: "addBubbleChart",
                    target: widgetTargetList[0],
                    passedData: coordsAndType
                });

            } else if (altViewMode == "BimShape" || altViewMode == "BimShapePopup") {

                if (altViewMode == "BimShapePopup") {
                    coordsAndType.bimShapePopup = true;
                }

                $.event.trigger({
                    type: "addBimShape",
                    target: widgetTargetList[0],
                    passedData: coordsAndType
                });

            } else {

                $.event.trigger({
                    type: "addSelectorPin",
                //    type: "addBubbleChart",
                    target: widgetTargetList[0],
                    passedData: coordsAndType
                });
            }
        }

        function removeLayerFromTargetMaps(desc, query, color1, color2, targets, display, bubbleFlag) {
            let coordsAndType = {};

            coordsAndType.desc = desc;
            coordsAndType.query = query;
            coordsAndType.color1 = color1;
            coordsAndType.color2 = color2;
            coordsAndType.targets = targets;
            coordsAndType.display = display;

            if (bubbleFlag != "Bubble") {
                if (bubbleFlag == "BimShape" || bubbleFlag == "BimShapePopup") {

                    if (bubbleFlag == "BimShapePopup") {
                        coordsAndType.bimShapePopup = true;
                    }

                    $.event.trigger({
                        type: "removeBimShape",
                        target: widgetTargetList[0],
                        passedData: coordsAndType
                    });

                } else {

                    $.event.trigger({
                        type: "removeSelectorPin",
                        target: widgetTargetList[0],
                        passedData: coordsAndType
                    });

                }
            } else {
                $.event.trigger({
                    type: "removeBubbles",
                    target: widgetTargetList[0],
                    passedData: coordsAndType
                });
            }
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

         /*   if(!gisMapPtrContainerWidthPerc) {
                gisMapPtrContainerWidthPerc = Math.floor((rowPxHeight) / $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() * 100);
            }
            var newQueryDescContainerWidth = Math.floor(($('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() - Math.ceil(rowPxHeight)) / $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() * 100);
            if ((gisMapPtrContainerWidthPerc + newQueryDescContainerWidth) != 100) {
                newQueryDescContainerWidth = newQueryDescContainerWidth + (100 - (gisMapPtrContainerWidthPerc + newQueryDescContainerWidth));
            }
            $('#<?= $_REQUEST['name_w'] ?>_div div.gisMapPtrContainer').css("width", gisMapPtrContainerWidthPerc + "%");
            $('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').css("width", newQueryDescContainerWidth + "%");*/

            if($('#'+widgetName).attr("data-icontextmode") == "Icon Only") {
                $('#<?= $_REQUEST['name_w'] ?>_div div.gisMapPtrContainer').css("width", "100%");
                $('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').css("width", "0%");
            } else {
                $('#<?= $_REQUEST['name_w'] ?>_div div.gisMapPtrContainer').css("width", Math.floor((rowPxHeight) / $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() * 100) + "%");
                $('#<?= $_REQUEST['name_w'] ?>_div div.gisQueryDescContainer').css("width", Math.floor(($('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() - Math.ceil(rowPxHeight)) / $('#<?= $_REQUEST['name_w'] ?>_rollerContainer').width() * 100) + "%");
            }

            //Resize dei pin di default e dell'icona del pool
            $('#<?= $_REQUEST['name_w'] ?>_div div.poolIcon').children(0).css("heigth", rowPxHeight * 0.8 + "px");
            $('div.poolIcon').children(0)
         //   ('#'#<?= $_REQUEST['name_w'] ?>_poolIcon_' + i).children(0).css('height', ($("#<?= $_REQUEST['name_w'] ?>_content").height()/queries.length) * 0.9);
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
                    iconTextMode = styleParameters.iconText;
                    mapPinIcon = styleParameters.mapPinIcon;
                    geoServerUrl = widgetProperties.geoServerUrl;
                    heatmapUrl = widgetProperties.heatmapUrl;
                    $('#<?= $_REQUEST['name_w'] ?>').attr("data-icontextmode", iconTextMode);

                    populateWidget();

                /*    var querySel = JSON.parse(widgetProperties.param.parameters).queries;
                    for (let no = 0; no < querySel.length; no++) {
                        bubbleMetricsArray[no] = [];
                        if (querySel[no].bubble == "Yes") {

                            // RETRIEVE METRICS ARRAY FOR BUBBLE CHART (SERVER SIDE)
                            getBubbleMetrics(querySel[no].query, no, function(extractedMetrics) {
                                if (extractedMetrics) {
                                    let index = extractedMetrics[0];
                                    if(extractedMetrics[1].metrics.length > 0) {
                                        bubbleMetricsArray[index].push(extractedMetrics[1].metrics);
                                        $('#' + widgetName + '_pinCtn' + index).attr("data-bubblemetricsarray", extractedMetrics[1].metrics);
                                        if (bubbleMetricsArray[index][0] != null) {
                                            if (bubbleMetricsArray[index][0].length > 0) {
                                                for (let k = 0; k < bubbleMetricsArray[index][0].length; k++) {
                                                    if (bubbleMetricsArray[index][k] === "loading available metrics..." || bubbleMetricsArray[index] === "no metrics available") {
                                                        $('#bubbleMetricsSelect' + index).append('<option style="color:darkgrey" value="' + bubbleMetricsArray[index][0][k] + '" disabled>' + bubbleMetricsArray[index][0][k] + '</option>');
                                                    } else {
                                                        $('#bubbleMetricsSelect' + index).append('<option value="' + bubbleMetricsArray[index][0][k] + '">' + bubbleMetricsArray[index][0][k] + '</option>');
                                                    }
                                                }
                                                for (let sc = 0; sc < $('#bubbleMetricsSelect' + index).length; sc++) {
                                                    if ($('#bubbleMetricsSelect' + index)[0].options[sc].value == 'loading available metrics...' || $('#bubbleMetricsSelect' + index).options[sc].value == 'no metrics available') {
                                                        $('#bubbleMetricsSelect' + index)[0].remove(sc);
                                                        break;
                                                    }
                                                }
                                            } else {
                                                $('#bubbleMetricsSelect' + index).append('<option style="color:darkgrey" value="no metrics available" disabled>no metrics available</option>');
                                                $('#bubbleMetricsSelect' + index).val("no metrics available");
                                                for (let sc = 0; sc < $('#bubbleMetricsSelect' + index).length; sc++) {
                                                    if ($('#bubbleMetricsSelect' + index)[0].options[sc].value == 'loading available metrics...' || $('#bubbleMetricsSelect' + index).options[sc].value == 'no metrics available') {
                                                        $('#bubbleMetricsSelect' + index)[0].remove(sc);
                                                        break;
                                                    }
                                                }
                                            }
                                        } else {
                                            $('#bubbleMetricsSelect' + index).append('<option style="color:darkgrey" value="no metrics available" disabled>no metrics available</option>');
                                            $('#bubbleMetricsSelect' + index).val("no metrics available");
                                            for (let sc = 0; sc < $('#bubbleMetricsSelect' + index).length; sc++) {
                                                if ($('#bubbleMetricsSelect' + index)[0].options[sc].value == 'loading available metrics...' || $('#bubbleMetricsSelect' + index).options[sc].value == 'no metrics available') {
                                                    $('#bubbleMetricsSelect' + index)[0].remove(sc);
                                                    break;
                                                }
                                            }
                                        }
                                        var stopFlag = 1;
                                    } else {
                                        $('#bubbleMetricsSelect' + index).append('<option style="color:darkgrey" value="no metrics available" disabled>no metrics available</option>');
                                        $('#bubbleMetricsSelect' + index).val("no metrics available");
                                        for (let sc = 0; sc < $('#bubbleMetricsSelect' + index).length; sc++) {
                                            if ($('#bubbleMetricsSelect' + index)[0].options[sc].value == 'loading available metrics...' || $('#bubbleMetricsSelect' + index).options[sc].value == 'no metrics available') {
                                                $('#bubbleMetricsSelect' + index)[0].remove(sc);
                                                break;
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }   */

                    $("#<?= $_REQUEST['name_w'] ?>_mainContainer").append(newPinsContainer);

                    $("div.newPinContainer").each(function (index) {
//	for (n = 0; n < newPinContainer.length; n++) {
                        //	var elementId = "image" + n;
                        //	var node = newPinContainer[n];
                        //	$(this)
                        //document.getElementById(elementId);
                        var fileName = $(this).attr("data-filename");
                    //    if (iconTextMode != "Text Description") {
                        domtoimage.toPng(this)
                            .then(function (dataUrl) {
                                var img = new Image();
                                //	var pngElementId = "pngBlob" + n;
                                //	img.id = pngElementId;
                                //	img.class = "pngImg";
                                img.src = dataUrl;

                            //    document.body.appendChild(img);

                                $.ajax({
                                    type: "POST",
                                    url: "../widgets/writePngOnDisk.php",
                                    data: {
                                        imgBase64Data: dataUrl,
                                        nameFile: fileName,
                                        nameFolder: "../img/outputPngIcons/"
                                    },
                                    success: function(data)
                                    {
                                        console.log("File Saved!");
                                        //   $('.newPinContainer').css("display", "none");
                                    },
                                    error: function(data)
                                    {
                                        console.log("Error in File Saving...");
                                    }
                                });
                            })
                            .catch(function (error) {
                                console.error('oops, something went wrong!', error);

                            });

                        // }    // END IF iconTextMode

                        var asynchronousFlag = true;
                        //	$('#image0').hide();

                    });


                    setTimeout(function () {
                        var sortedQueries = JSON.parse(widgetProperties.param.parameters).queries;
                        sortedQueries.sort(compareJsonElementsByKeyValues('rowOrder'));
                        for (var i = 0; i < sortedQueries.length; i++) {
                            defaultOption = sortedQueries[i].defaultOption;
                            if (defaultOption) {
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.gisPinLink").eq(i).trigger('click');
                                defaultOptionUsed = true;
                            //    triggerSelectorClickOnMapLoad(i);     // se si usa questa direttiva (test) commentare le 2 istruzioni sopra
                            }
                        }

                        if (!defaultOptionUsed) {
                            sortedQueries[0].defaultOption = true;
                            setTimeout(function() {
                                $("#<?= $_REQUEST['name_w'] ?>_rollerContainer a.gisPinLink").eq(0).trigger('click');
                            //    triggerSelectorClickOnMapLoad(0);     // se si usa questa direttiva (test) commentare l'istruzione sopra
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
            <div id="<?= $_REQUEST['name_w'] ?>_mainContainer" class="chartContainer selectorNew">
                <div id="<?= $_REQUEST['name_w'] ?>_rollerContainer" class="gisRollerContainer"></div>
            </div>
        </div>

        <!--<div id="<?= $_REQUEST['name_w'] ?>_resizeHandle" class='resizeHandle'>
            
        </div>    -->
    </div>
</div>