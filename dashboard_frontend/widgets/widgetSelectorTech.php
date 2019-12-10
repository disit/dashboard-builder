
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
function css(a) {
    var sheets = document.styleSheets, o = {};
    for (var i in sheets) {
		try {
			var rules = sheets[i].cssRules;
			for (var r in rules) {
				if (a.is(rules[r].selectorText)) {
					o = $.extend(o, css2json(rules[r].style), css2json(a.attr('style')));
				}
			}
		}
		catch(e) {}
    }
    return o;
}

function css2json(css) {
    var s = {};
    if (!css) return s;
    if (css instanceof CSSStyleDeclaration) {
        for (var i in css) {
            if ((css[i]).toLowerCase) {
                s[(css[i]).toLowerCase()] = (css[css[i]]);
            }
        }
    } else if (typeof css == "string") {
        css = css.split("; ");
        for (var i in css) {
            var l = css[i].split(": ");
            s[l[0].toLowerCase()] = (l[1]);
        }
    }
    return s;
}

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
            eventLog("Returned the following ERROR in widgetSelectorTech.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
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
            queryDescContainerWidthPerc, pinContainerWidthPerc, defaultOption = null;

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
            queries.sort(compareJsonElementsByKeyValues('rowOrder'));
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

            // COSTRUZIONE RIGHE SELETTORE
			$('#<?= $_REQUEST['name_w'] ?>_rollerContainer').append("<ul id=\"<?= $_REQUEST['name_w'] ?>_treeData\" style=\"display: none;\">");
			$('#<?= $_REQUEST['name_w'] ?>_treeData').append("<li data-icon=\"../img/widgetSelectorImages/widgetSelectorTechDefault.png\" id=\"<?= $_REQUEST['name_w'] ?>_highLevelType_Heatmap\">Heatmap<ul></ul></li>");
			$('#<?= $_REQUEST['name_w'] ?>_treeData').append("<li data-icon=\"../img/widgetSelectorImages/widgetSelectorTechDefault.png\" id=\"<?= $_REQUEST['name_w'] ?>_highLevelType_MyPOI\">MyPOI<ul></ul></li>");			
			$('#<?= $_REQUEST['name_w'] ?>_treeData').append("<li data-icon=\"../img/widgetSelectorImages/widgetSelectorTechDefault.png\" id=\"<?= $_REQUEST['name_w'] ?>_highLevelType_Sensor\">Sensor<ul></ul></li>");
			$('#<?= $_REQUEST['name_w'] ?>_treeData').append("<li data-icon=\"../img/widgetSelectorImages/widgetSelectorTechDefault.png\" id=\"<?= $_REQUEST['name_w'] ?>_highLevelType_wfs\">wfs<ul></ul></li>");
			$('#<?= $_REQUEST['name_w'] ?>_treeData').append("<li data-icon=\"../img/widgetSelectorImages/widgetSelectorTechDefault.png\" id=\"<?= $_REQUEST['name_w'] ?>_highLevelType_POI\">POI<ul></ul></li>");
			var selectAtPageLoad = [];
			var foundTypes = [];
			for (var i = 0; i < queries.length; i++) {
				desc = queries[i].desc;
				queryType = queries[i].queryType;
				foundTypes.push(queryType);
				query = queries[i].query;				
				targets = queries[i].targets;
                display = queries[i].display; 
				defaultOption = queries[i].defaultOption;
				color1 = queries[i].color1;
                color2 = queries[i].color2;
				symbolMode = queries[i].symbolMode;
				symbolFile = queries[i].symbolFile;
				
				if(defaultOption) {
					selectAtPageLoad.push("<?= $_REQUEST['name_w'] ?>_subNature_"+desc.replace(/\W/g, ''));
				}
				
				if(document.getElementById('<?= $_REQUEST['name_w'] ?>_highLevelType_'+queryType.replace(/\W/g, ''))) { 
					$('#<?= $_REQUEST['name_w'] ?>_highLevelType_'+queryType.replace(/\W/g, '')).children("ul").append("<li data-symbolMode=\""+symbolMode+"\" data-symbolFile=\""+symbolFile+"\" data-icon=\""+symbolFile+"\" data-color1=\""+color1+"\" data-color2=\""+color2+"\" data-default=\""+defaultOption+"\" data-targets=\""+targets+"\" data-display=\""+display+"\" data-queryType=\""+queryType+"\" data-desc=\""+desc+"\" data-query=\""+query+"\" id=\"<?= $_REQUEST['name_w'] ?>_subNature_"+desc.replace(/\W/g, '')+"\">"+desc+"</li>"); 
				}
				else {
					$('#<?= $_REQUEST['name_w'] ?>_highLevelType_POI').children("ul").append("<li data-symbolMode=\""+symbolMode+"\" data-symbolFile=\""+symbolFile+"\"  data-icon=\""+symbolFile+"\" data-color1=\""+color1+"\" data-color2=\""+color2+"\" data-default=\""+defaultOption+"\" data-targets=\""+targets+"\" data-display=\""+display+"\" data-queryType=\""+queryType+"\" data-desc=\""+desc+"\" data-query=\""+query+"\" id=\"<?= $_REQUEST['name_w'] ?>_subNature_"+desc.replace(/\W/g, '')+"\">"+desc+"</li>");
				}
			}
			
			if(!foundTypes.includes('Heatmap')) {
				$('#<?= $_REQUEST['name_w'] ?>_treeData').find("#<?= $_REQUEST['name_w'] ?>_highLevelType_Heatmap").remove();
			}
			if(!foundTypes.includes('MyPOI')) {
				$('#<?= $_REQUEST['name_w'] ?>_treeData').find("#<?= $_REQUEST['name_w'] ?>_highLevelType_MyPOI").remove();
			}
			if(!foundTypes.includes('Sensor')) {
				$('#<?= $_REQUEST['name_w'] ?>_treeData').find("#<?= $_REQUEST['name_w'] ?>_highLevelType_Sensor").remove();
			}
			if(!foundTypes.includes('wfs')) {
				$('#<?= $_REQUEST['name_w'] ?>_treeData').find("#<?= $_REQUEST['name_w'] ?>_highLevelType_wfs").remove();
			}
			if(!foundTypes.includes('Default')) {
				$('#<?= $_REQUEST['name_w'] ?>_treeData').find("#<?= $_REQUEST['name_w'] ?>_highLevelType_POI").remove();
			}
				
			var oldSelKeys = [];
			
			$('#<?= $_REQUEST['name_w'] ?>_rollerContainer').fancytree({ 
					checkbox: true, 
					selectMode: 3,
					select: function(event, data) {
						var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
						  return node.key;
						});
						// ORIG MS
					//	var changedSelKeys = selKeys.diff(oldSelKeys).concat(oldSelKeys.diff(selKeys));
                    // CHANGE GP-MS
                        var osA = oldSelKeys.filter(function(i) {return selKeys.indexOf(i) < 0;});
                        var soA = selKeys.filter(function(i) {return oldSelKeys.indexOf(i) < 0;});
                        var changedSelKeys = osA.concat(soA);
						oldSelKeys = selKeys;
						changedSelKeys.forEach(
							function(item,index) {
								if(item.indexOf('Heatmap') > -1 && data.tree.getNodeByKey(item).isSelected() ) {
									data.tree.getNodeByKey(item).setSelected(false); 
								}
								if(item.indexOf('highLevelType') > -1) {
									return;
								}
								if($('#'+item).attr("data-query") && $('#'+item).attr("data-query").includes("scenario") && widgetTargetList.length > 0) {
									if(selKeys.includes(item)) {
										$.event.trigger({
											type: "addScenario",
											target: widgetTargetList[0]
										});
									}
									else {
										$.event.trigger({
											type: "removeScenario",
											target: widgetTargetList[0],
										});
									}
								}
								else if($('#'+item).attr("data-query") && $('#'+item).attr("data-query").includes("whatif") && widgetTargetList.length > 0) {
									if(selKeys.includes(item)) {										
										$.event.trigger({
											type: "addWhatif",
											target: widgetTargetList[0]
										});
									}
									else {
										$.event.trigger({
											type: "removeWhatif",
											target: widgetTargetList[0],
										});
									}
								}
								else if ($('#'+item).attr("data-query") && $('#'+item).attr("data-query").includes("trafficRTDetails") && (widgetTargetList.length > 0)) {
									if(selKeys.includes(item)) {			
										var coordsAndType = $('#'+item).attr("data-query");
										$.event.trigger({
											type: "addTrafficRealTimeDetails",
											target: widgetTargetList[0],
											passedData: coordsAndType
										});
									}
									else {
										$.event.trigger({
											type: "removeTrafficRealTimeDetails",
											target: widgetTargetList[0]
										});
									}
								}
								else if ($('#'+item).attr("data-query") && ($('#'+item).attr("data-query").includes("heatmap.php") || $('#'+item).attr("data-query").includes("wmsserver.snap4city.org")) && (widgetTargetList.length > 0)) {

									if(selKeys.includes(item)) {	
										
										selKeys.forEach(function(iitem,iindex){
											if($('#'+iitem).attr("data-query") && ($('#'+iitem).attr("data-query").includes("heatmap.php") || $('#'+iitem).attr("data-query").includes("wmsserver.snap4city.org")) && $('#'+iitem).attr("data-query") != $('#'+item).attr("data-query")) { 
												data.tree.getNodeByKey(iitem).setSelected(false);
											}
										});
										
										let coordsAndType = $('#'+item).attr("data-query");										
										let passedParams = {};
										passedParams.desc = $('#'+item).attr("data-desc");
										passedParams.color1 = $('#'+item).attr("data-color1");
										passedParams.color2 = $('#'+item).attr("data-color2");
										$.event.trigger({
											type: "addHeatmap",
											target: widgetTargetList[0],
											passedData: coordsAndType,
											passedParams: passedParams
										});
										
									}
									else {
										
										$.event.trigger({
											type: "removeHeatmap",
											target: widgetTargetList[0]
										});
										
									}	
									
								}
								else if($('#'+item).attr("data-query") && widgetTargetList.length > 0) {
									if(selKeys.includes(item)) {	
										addLayerToTargetMaps($('#'+item), $('#'+item).attr("data-desc"), $('#'+item).attr("data-query"), $('#'+item).attr("data-color1"), $('#'+item).attr("data-color2"), $('#'+item).attr("data-targets"), $('#'+item).attr("data-display"), $('#'+item).attr("data-queryType"));
									}
									else {
										removeLayerFromTargetMaps($('#'+item).attr("data-desc"), $('#'+item).attr("data-query"), $('#'+item).attr("data-color1"), $('#'+item).attr("data-color2"), $('#'+item).attr("data-targets"), $('#'+item).attr("data-display"));
									}
								}
								
							}
						);											
						
					},
					init: function(event, data) { 
						$("span:contains('Heatmap')").parent().find(".fancytree-checkbox").hide(); 
						setTimeout(function() { 
							
							for(var s = 0; s < selectAtPageLoad.length; s++) data.tree.getNodeByKey(selectAtPageLoad[s]).setSelected(true); 
						
						}, parseInt("<?php echo $crossWidgetDefaultLoadWaitTime; ?>")); 

					},
					click: function(event,data) {
						if(data.targetType == "icon" || data.targetType == "title") {
							if(data.node.title != "Heatmap") data.node.toggleSelected();
							return false;
						}
						else {
							return true;
						}
					},
					focus: function(event,data) {
						$("#<?= $_REQUEST['name_w'] ?>_rollerContainer ul.fancytree-container").css("outline","none");
					}
				}				
			);			

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
					
					setWidgetContentVisibility("<?= $_REQUEST['name_w'] ?>","<?= escapeForJS($_REQUEST['showContent']) ?>");
					
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
				<div id="<?= $_REQUEST['name_w'] ?>_rollerContainer" class="gisRollerContainer" style="overflow-y: auto;"></div>
            </div>
        </div>

        <!--<div id="<?= $_REQUEST['name_w'] ?>_resizeHandle" class='resizeHandle'>

        </div>    -->
    </div>
</div>