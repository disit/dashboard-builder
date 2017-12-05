<?php

/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange)  
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_GET['title'];
        ?> 
        var scroller, widgetProperties, styleParameters, serviceUri, 
            eventName, newRow, symbolMode, symbolFile, widgetTargetList, originalHeaderColor, fontFamily, originalBorderColor, 
            eventName, serviceUri, queriesNumber, widgetWidth, shownHeight, rowPercHeight, contentHeightPx, eventContentWPerc, 
            mapPtrContainer, pinContainer, queryDescContainer, activeFontColor, rowHeight, iconSize, queryDescContainerWidth, queryDescContainerWidthPerc, pinContainerWidthPerc = null;    
    
        var fontSize = "<?= $_GET['fontSize'] ?>";
        var speed = 65;
        var hostFile = "<?= $_GET['hostFile'] ?>";
        var widgetName = "<?= $_GET['name'] ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_mainContainer");
        var widgetContentColor = "<?= $_GET['color'] ?>";
        var widgetHeaderColor = "<?= $_GET['frame_color'] ?>";
        var widgetHeaderFontColor = "<?= $_GET['headerFontColor'] ?>";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var fontColor = "<?= $_GET['fontColor'] ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_rollerContainer");
        var url = "<?= $_GET['link_w'] ?>";
        var embedWidget = <?= $_GET['embedWidget'] ?>;
        var embedWidgetPolicy = '<?= $_GET['embedWidgetPolicy'] ?>';	
        var headerHeight = 25;
        var showTitle = "<?= $_GET['showTitle'] ?>";
	var showHeader = null;
        var pinContainerWidth = 40;
        
        if(url === "null")
        {
            url = null;
        }
        
        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")&&(hostFile === "index")))
	{
            showHeader = false;
	}
	else
	{
            showHeader = true;
	}
        
        //Definizioni di funzione
        function populateWidget()
        {
            var queries = JSON.parse(widgetProperties.param.parameters).queries;
            var desc, query, color1, color2, targets = null;
            $('#<?= $_GET['name'] ?>_rollerContainer').empty();
            
            if(firstLoad !== false)
            {
                showWidgetContent(widgetName);
            }
            else
            {
                elToEmpty.empty();
            }
           
            for(var i = 0; i < queries.length; i++)
            {
                desc = queries[i].desc;
                query = queries[i].query;
                color1 = queries[i].color1;
                color2 = queries[i].color2;
                targets = queries[i].targets;
                symbolMode = queries[i].symbolMode;
                
                newRow = $('<div></div>');
                newRow.css("width", "100%");
                newRow.css("height", rowPercHeight + "%");
                
                mapPtrContainer = $('<div class="gisMapPtrContainer"></div>'); 
                mapPtrContainer.css("background", color1);
                mapPtrContainer.css("background", "-webkit-linear-gradient(left top, " + color1 + ", " + color2 + ")");
                mapPtrContainer.css("background", "-o-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                mapPtrContainer.css("background", "-moz-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                mapPtrContainer.css("background", "linear-gradient(to bottom right, " + color1 + ", " + color2 + ")");
                
                rowHeight = $("#<?= $_GET['name'] ?>_content").height() * rowPercHeight / 100;
                iconSize = parseInt(rowHeight*0.75);
                
                if(iconSize > 34)
                {
                    iconSize = 34;
                }
                iconSize = iconSize + "px";
                
                var loadingIconSize = parseInt(rowHeight*0.6);
                
                if(loadingIconSize > 25)
                {
                    loadingIconSize = 25;
                }
                
                loadingIconSize = loadingIconSize + "px";

                pinContainer = $('<div class="gisPinContainer"><a class="gisPinLink" data-fontColor="' + fontColor + '" data-activeFontColor="' + activeFontColor + '" data-symbolMode="' + symbolMode + '" data-desc="' + desc + '" data-query="' + query + '" data-color1="' + color1 + '" data-color2="' + color2 + '" data-targets="' + targets + '" data-onMap="false"><span class="gisPinShowMsg">show</span><span class="gisPinHideMsg">hide</span><span class="gisPinNoQueryMsg">no query</span><span class="gisPinNoMapsMsg">no maps set</span><i class="material-icons gisPinIcon" style="font-size: ' + iconSize + '">navigation</i><div class="gisPinCustomIcon"><div class="gisPinCustomIconUp"></div><div class="gisPinCustomIconDown"><span><i class="fa fa-check"></i></span></div></div></a><i class="fa fa-circle-o-notch fa-spin gisLoadingIcon" style="font-size: ' + loadingIconSize + '"></i><i class="fa fa-close gisLoadErrorIcon" style="font-size: ' + iconSize + '"></i></div>');
                
                if(symbolMode === 'auto')
                {
                    pinContainer.find('div.gisPinCustomIcon').hide();
                    pinContainer.find('i.gisPinIcon').show();
                }
                else
                {
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
                    function()
                    {
                        var re1='(rgba)';	
                        var re2='(\\()';	
                        var re3='(\\d+)';	
                        var re4='(,)';	
                        var re5='(\\d+)';	
                        var re6='(,)';	
                        var re7='(\\d+)';	
                        var re8='(,)';	
                        var re9='(0)';	
                        var re10='(\\))';	
                        var transparentColorPattern = new RegExp(re1+re2+re3+re4+re5+re6+re7+re8+re9+re10,["g"]);
                        
                        originalHeaderColor = {};
                        originalBorderColor = {};
                        
                        if(widgetTargetList.length > 0)
                        {
                            if($(this).attr("data-query") === '')
                            {
                                if($(this).attr("data-symbolMode") === 'auto')
                                {
                                    $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                                }
                                else
                                {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                                }
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoMapsMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoQueryMsg").show();
                            }
                            else
                            {
                                for(var i in widgetTargetList)
                                {
                                    if(!$(this).attr("data-color1").match(transparentColorPattern))
                                    {
                                        originalHeaderColor[widgetTargetList[i]] = $("#" + widgetTargetList[i] + "_header").css("background-color");
                                        originalBorderColor[widgetTargetList[i]] = $("#" + widgetTargetList[i]).css("border-color");
                                        $("#" + widgetTargetList[i] + "_header").css("background", $(this).attr("data-color1"));
                                        $("#" + widgetTargetList[i]).css("border-color", $(this).attr("data-color1"));

                                        if($(this).attr("data-onMap") === "true")
                                        {
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
                                    else
                                    {
                                        //console.log("Trasparente");
                                    }
                                }

                                if($(this).attr("data-onMap") === "false")
                                {
                                    if($(this).attr("data-symbolMode") === 'auto')
                                    {
                                        $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                                    }
                                    else
                                    {
                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                                    }
                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                                    
                                    if($(this).attr("data-color1").match(transparentColorPattern))
                                    {
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").css("color", fontColor);
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").css("text-shadow", "none");
                                    }
                                    
                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").show();
                                }
                                else
                                {
                                    if($(this).attr("data-symbolMode") === 'auto')
                                    {
                                        $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                                    }
                                    else
                                    {
                                        $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                                    }
                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                                    
                                    if($(this).attr("data-color1").match(transparentColorPattern))
                                    {
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").css("color", fontColor);
                                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").css("text-shadow", "none");
                                    }
                                    
                                    $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").show();
                                } 
                            } 
                        }
                        else
                        {
                            if($(this).attr("data-symbolMode") === 'auto')
                            {
                                $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").hide();
                            }
                            else
                            {
                                $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").hide();
                            }
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoQueryMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoMapsMsg").show();
                        }
                    },
                    function()
                    {
                        if(widgetTargetList.length > 0)
                        {
                            if($(this).attr("data-query") === '')
                            {
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoQueryMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                                $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoMapsMsg").hide();
                                if($(this).attr("data-symbolMode") === 'auto')
                                {
                                    $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                                }
                                else
                                {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                                } 
                            }
                            else
                            {
                                for(var i in widgetTargetList)
                                {
                                    $("#" + widgetTargetList[i] + "_header").css("background", originalHeaderColor[widgetTargetList[i]]);
                                    $("#" + widgetTargetList[i]).css("border-color", originalBorderColor[widgetTargetList[i]]);

                                    if($(this).attr("data-onMap") === "true")
                                    {
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
                                if($(this).attr("data-symbolMode") === 'auto')
                                {
                                    $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                                }
                                else
                                {
                                    $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                                }
                            } 
                        }
                        else
                        {
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoQueryMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                            $(this).parents("div.gisMapPtrContainer").find("span.gisPinNoMapsMsg").hide();
                            if($(this).attr("data-symbolMode") === 'auto')
                            {
                                $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                            }
                            else
                            {
                                $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                            }
                        }
                    }
                );
                
                pinContainer.find("a.gisPinLink").click(function(){
                    if(($(this).attr("data-query") !== '')&&(widgetTargetList.length > 0))
                    {
                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinHideMsg").hide();
                        $(this).parents("div.gisMapPtrContainer").find("span.gisPinShowMsg").hide();
                        if($(this).attr("data-symbolMode") === 'auto')
                        {
                            $(this).parents("div.gisMapPtrContainer").find("i.gisPinIcon").show();
                        }
                        else
                        {
                            $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIcon").show();
                        }

                        if($(this).attr("data-onMap") === "false")
                        {
                           $(this).attr("data-onMap", "true");
                           $(this).hide();
                           $(this).parents("div.gisMapPtrContainer").find("i.gisLoadingIcon").show();
                           addLayerToTargetMaps($(this), $(this).attr("data-desc"), $(this).attr("data-query"), $(this).attr("data-color1"), $(this).attr("data-color2"), $(this).attr("data-targets")); 
                        }
                        else
                        {
                           $(this).attr("data-onMap", "false"); 
                           if($(this).attr("data-symbolMode") === 'auto')
                           {
                                $(this).find("i.gisPinIcon").html("navigation");
                                $(this).find("i.gisPinIcon").css("color", "black");
                                $(this).find("i.gisPinIcon").css("text-shadow", "none");
                           }
                           else
                           {
                               $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconUp").css("height", "100%");
                               $(this).parents("div.gisMapPtrContainer").find("div.gisPinCustomIconDown").css("display", "none");
                           }
                           
                           $(this).parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("font-weight", "normal");
                           $(this).parents('div.gisMapPtrContainer').siblings('div.gisQueryDescContainer').find('p.gisQueryDescPar').css("color", $(this).attr("data-fontColor"));
                           removeLayerFromTargetMaps($(this).attr("data-desc"), $(this).attr("data-query"), $(this).attr("data-color1"), $(this).attr("data-targets")); 
                        }  
                    }
                });
                
                queryDescContainer = $('<div class="gisQueryDescContainer"></div>');
                queryDescContainer.css("background", color1);
                queryDescContainer.css("background", "-webkit-linear-gradient(left top, " + color1 + ", " + color2 + ")");
                queryDescContainer.css("background", "-o-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                queryDescContainer.css("background", "-moz-linear-gradient(bottom right, " + color1 + ", " + color2 + ")");
                queryDescContainer.css("background", "linear-gradient(to bottom right, " + color1 + ", " + color2 + ")");
                queryDescContainer.html('<p class="gisQueryDescPar">' + desc + '</p>');
                queryDescContainer.find("p.gisQueryDescPar").css("font-size", fontSize + "px");
                queryDescContainer.find("p.gisQueryDescPar").css("color", fontColor);
                if(fontFamily !== 'Auto')
                {
                    queryDescContainer.find("p.gisQueryDescPar").css("font-family", fontFamily);
                }
                newRow.append(queryDescContainer);
               
                $('#<?= $_GET['name'] ?>_rollerContainer').append(newRow);
                
                if(contentHeightPx > shownHeight)
                {
                    queryDescContainerWidth = widgetWidth - pinContainerWidth - 25;
                    queryDescContainerWidthPerc = (queryDescContainerWidth / (widgetWidth - 25))*100;
                    pinContainerWidthPerc = 100 - queryDescContainerWidthPerc;
                }
                else
                {
                    queryDescContainerWidth = widgetWidth - pinContainerWidth;
                    queryDescContainerWidthPerc = (queryDescContainerWidth / widgetWidth)*100;
                    pinContainerWidthPerc = 100 - queryDescContainerWidthPerc;
                }
                
                mapPtrContainer.css("width", pinContainerWidthPerc + "%");
                queryDescContainer.css("width", queryDescContainerWidthPerc + "%");
                
                var descParHeight = queryDescContainer.find("p.gisQueryDescPar").height();
                var descParMarginTop = Math.floor((newRow.height() - descParHeight) / 2);
                queryDescContainer.find("p.gisQueryDescPar").css("margin-top", descParMarginTop + "px");
            }//Fine del for    
            
            $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop(0);
        }
        
        function addLayerToTargetMaps(eventGenerator, desc, query, color1, color2, targets)
        {
            for(var i in widgetTargetList)
            {
                $.event.trigger({
                    type: "addLayerFromGis_" + widgetTargetList[i],
                    eventGenerator: eventGenerator,
                    targetWidget: widgetTargetList[i],
                    desc: desc,
                    query: query,
                    color1: color1,
                    color2: color2,
                    targets: targets
                }); 
            }
        }
        
        function removeLayerFromTargetMaps(desc, query, color1, color2, targets)
        {
            for(var i in widgetTargetList)
            {
                 $.event.trigger({
                     type: "removeLayerFromGis_" + widgetTargetList[i],
                     targetWidget: widgetTargetList[i],
                     desc: desc,
                     query: query,
                     color1: color1,
                     color2: color2,
                     targets: targets
                 }); 
            }
        }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getInfoJson()
        {
            var infoJson = null;
            if(jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson); 
            }
            
            return infoJson;
        }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getStyleParameters()
        {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        
        function stepDownInterval()
        {
            var oldPos = $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop();
            var newPos = oldPos + 1;
            var oldScrollTop = $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop();
            $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop(newPos);
            var newScrollTop = $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop();
            
            if(oldScrollTop === newScrollTop)
            {
               $("#<?= $_GET['name'] ?>_rollerContainer").scrollTop(0);
            }
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight);
        
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        
        addLink(widgetName, url, linkElement, divContainer);
        $("#<?= $_GET['name'] ?>_titleDiv").html("<?= preg_replace($titlePatterns, $replacements, $title) ?>");
        //widgetProperties = getWidgetProperties(widgetName);
        
        $.ajax({
            url: getParametersWidgetUrl,
            type: "GET",
            data: {"nomeWidget": [widgetName]},
            async: true,
            dataType: 'json',
            success: function (data) 
            {
                widgetProperties = data;
                if((widgetProperties !== null) && (widgetProperties !== undefined))
                {
                    //Inizio eventuale codice ad hoc basato sulle proprietà del widget
                    styleParameters = getStyleParameters();//Restituisce null finché non si usa il campo per questo widget
                    //Fine eventuale codice ad hoc basato sulle proprietà del widget
                    fontFamily = widgetProperties.param.fontFamily;
                    widgetTargetList = JSON.parse(widgetProperties.param.parameters).targets;
                    queriesNumber = JSON.parse(widgetProperties.param.parameters).queries.length;
                    activeFontColor = styleParameters.activeFontColor;
                    widgetWidth = $('#<?= $_GET['name'] ?>_div').width();
                    shownHeight = $('#<?= $_GET['name'] ?>_div').height() - 25;
                    manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));

                    switch(styleParameters.rectDim)
                    {
                        case "1":
                            rowPercHeight =  25 * 100 / shownHeight;
                            break;

                        case "2":
                            rowPercHeight =  50 * 100 / shownHeight;
                            break;

                        case "3":
                            rowPercHeight =  75 * 100 / shownHeight;
                            break;

                        case "4":
                            rowPercHeight =  100 / queriesNumber;
                            break;    
                    }

                    contentHeightPx = queriesNumber * 100;
                    eventContentWPerc = null;

                    populateWidget();
                    scroller = setInterval(stepDownInterval, speed);

                    $("#<?= $_GET['name'] ?>_rollerContainer").mouseenter(function() 
                    {
                       clearInterval(scroller);
                    });

                    $("#<?= $_GET['name'] ?>_rollerContainer").mouseleave(function()
                    {    
                        scroller = setInterval(stepDownInterval, speed);
                    });
                }
                else
                {
                    console.log("Proprietà widget = null");
                    $("#<?= $_GET['name'] ?>_mainContainer").hide();
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            },
            error: function(errorData)
            {
               console.log("Errore in caricamento proprietà widget");
               console.log(JSON.stringify(errorData));
               showWidgetContent(widgetName);
               if(firstLoad !== false)
               { 
                  $("#<?= $_GET['name'] ?>_mainContainer").hide();
                  $('#<?= $_GET['name'] ?>_noDataAlert').show();
               }
            }
        });
        
        
    });//Fine document ready
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            </div>  
        </div>
        
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_GET['name'] ?>_content" class="content">
            <div id="<?= $_GET['name'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_GET['name'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_GET['name'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_GET['name'] ?>_mainContainer" class="chartContainer">
               <div id="<?= $_GET['name'] ?>_rollerContainer" class="gisRollerContainer"></div>
            </div>
        </div>
    </div>	
</div>