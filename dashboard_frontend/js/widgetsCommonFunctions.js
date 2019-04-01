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

//Globals
var loadingFontDim = 13;
var loadingIconDim = 20;
var widgetHeaderHeight = 25;
var getParametersWidgetUrl = "../widgets/getParametersWidgets.php";
var getMetricDataUrl = "../widgets/getDataMetrics.php";


//Usata in tutti gli widget, ma destinata ad essere eliminata: già inglobata in setWidgetLayout
function setHeaderFontColor(widget, color)
{
    $("#" + widget).css("color", color);
}

//Usata in tutti gli widget
function addLink(name, url, linkElement, elementToBeWrapped, target)
{
    if(url !== 'none' && url !== 'map') 
    {
        if(linkElement.length === 0)
        {
            if (target === null)
            {
             //   console.log("Arriva in commonFunctions.php  CASO BLANK TARGET = " + target);
                linkElement = $("<a id='" + name + "_link_w' href='" + url + "' target='_blank' class='elementLink2'></a>");
                elementToBeWrapped.wrap(linkElement);
            }
            else
            {
             //   console.log ("Arriva in commonFunctions.js  CASO SAME TARGET = " + target);
                linkElement = $("<a id='" + name + "_link_w' href='" + url + "' target='"+ target + "' class='elementLink2'></a>");
                elementToBeWrapped.wrap(linkElement);
            }
        }
    }
}

//Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie
function showWidgetContent(widgetName)
{
    $("#" + widgetName + "_loading").css("display", "none");
    $("#" + widgetName + "_content").css("display", "block");
}

//Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie
function setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer)
{
    var titleWidth, contentHeight = null;

    if((!widgetName.includes('widgetExternalContent'))&&(!widgetName.includes('widgetMap'))&&(!widgetName.includes('widgetGisWFS')))
    {
        $("#" + widgetName + "_buttonsDiv").remove();
    }

    //Impostazione header
    $("#" + widgetName + "_header").css("background-color", widgetHeaderColor);
    $("#" + widgetName + "_infoButtonDiv a.info_source").css("color", widgetHeaderFontColor);
    if(widgetHeaderFontColor !== widgetHeaderColor)
    {
        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer a.iconFullscreenModal").css("color", widgetHeaderFontColor);
        $("#" + widgetName + "_buttonsDiv div.singleBtnContainer a.iconFullscreenTab").css("color", widgetHeaderFontColor);
        $("#" + widgetName + "_countdownDiv").css("border-color", widgetHeaderFontColor);
    }
    
    if(showHeader)
    {
        $("#" + widgetName + "_header").show();
    }
    else
    {
        $("#" + widgetName + "_header").hide();
    }

    //Impostazione menu di contesto
  //  console.log($("#" + widgetName).width());/* aggiunto da berna*/
    var widgetCtxMenuBtnCntLeft = $("#" + widgetName).width() - $("#" + widgetName + "_widgetCtxMenuBtnCnt").width();
    if (location.href.includes('prova2') && ((widgetName == "DCTemp1_24_widgetTimeTrend6351")||(widgetName == "SensoreViaBolognese_24_widgetSingleContent6353"))){
        if (widgetName == "SensoreViaBolognese_24_widgetSingleContent6353"){
                    widgetCtxMenuBtnCntLeft = 230 - $("#" + widgetName + "_widgetCtxMenuBtnCnt").width();
                }
                else{
                    var widgetCtxMenuBtnCntLeft = 1225 - $("#" + widgetName + "_widgetCtxMenuBtnCnt").width();
                }
    }
    $("#" + widgetName + "_widgetCtxMenuBtnCnt").css("left", widgetCtxMenuBtnCntLeft + "px");

    if(hostFile === 'config')
    {
       $("#" + widgetName + "_widgetCtxMenuBtnCnt").show();
    }
    else
    {
       $("#" + widgetName + "_widgetCtxMenuBtnCnt").hide();
    }

    if(hostFile === 'config')
    {
        $("#" + widgetName + "_header").css("width", widgetCtxMenuBtnCntLeft + "px");

        if(showHeader)
        {
            $("#" + widgetName + "_widgetCtxMenuBtnCnt").css("color", widgetHeaderFontColor);
            $("#" + widgetName + "_widgetCtxMenuBtnCnt").css("background-color", widgetHeaderColor);
        }

        //TBD - Da specializzare in presenza/assenza di infoButton e bottoniFullscreen
        if(hasTimer === 'yes')
        {
            titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - $("#" + widgetName + "_infoButtonDiv").width() - $("#" + widgetName + "_countdownContainerDiv").width() - $("#" + widgetName + "_widgetCtxMenuBtnCnt").width()));
        }
        else
        {
            titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - $("#" + widgetName + "_infoButtonDiv").width() - $("#" + widgetName + "_widgetCtxMenuBtnCnt").width()));
            $("#" + widgetName + "_countdownContainerDiv").remove();
        }

        //Il caso widgetButton è gestito nel codice del widget, da qui non funziona e non si capisce perché

        if(widgetName.includes('widgetButton'))
        {
            titleWidth = parseInt(parseInt($("#" + widgetName).width() - $("#" + widgetName + "_infoButtonDiv").width() - $("#" + widgetName + "_widgetCtxMenuBtnCnt").width()));
        }
        
        var headerWidth = parseInt($("#" + widgetName).width() - $("#" + widgetName + "_widgetCtxMenuBtnCnt").width());
            if (location.href.includes('prova2') && ((widgetName == "DCTemp1_24_widgetTimeTrend6351")||(widgetName == "SensoreViaBolognese_24_widgetSingleContent6353"))){
                if (widgetName == "SensoreViaBolognese_24_widgetSingleContent6353"){
                    headerWidth = parseInt(230 - $("#" + widgetName + "_widgetCtxMenuBtnCnt").width());
                }
                else{
                    var headerWidth = parseInt(1225 - $("#" + widgetName + "_widgetCtxMenuBtnCnt").width());
                }
    }
        $("#" + widgetName + "_header").css("width", headerWidth + "px");
        $("#" + widgetName + "_titleDiv").css("width", Math.floor(titleWidth/headerWidth*100) + "%");
    }
    else
    {
        $("#" + widgetName + "_header").css("width", "100%");

        if(hasTimer === 'yes')
        {
            titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - $("#" + widgetName + "_infoButtonDiv").width() - $("#" + widgetName + "_countdownContainerDiv").width()));
        }
        else
        {
            titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - $("#" + widgetName + "_infoButtonDiv").width()));
            $("#" + widgetName + "_countdownContainerDiv").remove();
        }

        if(widgetName.includes('widgetButton'))
        {
            titleWidth = parseInt(parseInt($("#" + widgetName).width() - $("#" + widgetName + "_infoButtonDiv").width()));
        }
        $("#" + widgetName + "_titleDiv").css("width", Math.floor(titleWidth/$("#" + widgetName).width()*100) + "%");
        //TBD - Da specializzare in presenza/assenza di infoButton e bottoniFullscreen
    }

    $("#" + widgetName + "_titleDiv").css("color", widgetHeaderFontColor);
    $("#" + widgetName + "_countdownContainerDiv").css("color", widgetHeaderFontColor);
	
    if(showHeader)
    {
        //Impostazione altezza widget
        contentHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - headerHeight);
    }
    else
    {
        //Impostazione altezza widget
        contentHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight"));
        $('#' + widgetName + '_header').hide();
    }
    
    //Impostazione colore di background del widget
    $("#" + widgetName + "_content").css("background-color", widgetContentColor);
    
    $("#" + widgetName + "_content").css("height", contentHeight);
    if(widgetHeaderColor === widgetHeaderFontColor)
    {
        $("#" + widgetName + "_titleDiv").css("text-shadow", "none");
    }
}

//Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie
function startCountdownOld(widgetName, timeToReload, funcRef, elToEmpty, widgetType , scrollerTimeout, eventNamesArray, metricNameFromDriverLocal, widgetTitleFromDriverLocal, widgetHeaderColorFromDriverLocal, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef)
{
   var intervalRef = setInterval(function () {
        $("#" + widgetName + "_countdownDiv").text(timeToReload);
        timeToReload--;
        if (timeToReload > 60) 
        {
            $("#" + widgetName + "_countdownDiv").text(Math.floor(timeToReload / 60) + "m");
        } 
        else 
        {
            $("#" + widgetName + "_countdownDiv").text(timeToReload + "s");
        }
        
        if(timeToReload === 0) 
        {
            $("#" + widgetName + "_countdownDiv").text(timeToReload + "s");
            clearInterval(intervalRef);
            
            //Da ripristinare
            /*if(alarmSet)
            {
                $("#<?= $_GET['name'] ?>_alarmDiv").removeClass("alarmDivActive");
                $("#<?= $_GET['name'] ?>_alarmDiv").addClass("alarmDiv");  
            }*/
            setTimeout(funcRef(false, metricNameFromDriverLocal, widgetTitleFromDriverLocal, widgetHeaderColorFromDriverLocal, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef), 1000);
        }
    }, 1000);
    
    return intervalRef;
}

function startCountdown(widgetName, timeToReload, funcRef, metricNameFromDriverLocal, widgetTitleFromDriverLocal, widgetHeaderColorFromDriverLocal, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId)
{
   //console.log("fromGisFakeId in start countdown: " + fromGisFakeId); 
   var intervalRef = setInterval(function () {
        $("#" + widgetName + "_countdownDiv").text(timeToReload);
        timeToReload--;
        if (timeToReload > 60) 
        {
            $("#" + widgetName + "_countdownDiv").text(Math.floor(timeToReload / 60) + "m");
        } 
        else 
        {
            $("#" + widgetName + "_countdownDiv").text(timeToReload + "s");
        }
        
        if(timeToReload === 0) 
        {
            $("#" + widgetName).off('customResizeEvent');
            
            $("#" + widgetName + "_countdownDiv").text(timeToReload + "s");
            clearInterval(intervalRef);
            
            //Da ripristinare
            /*if(alarmSet)
            {
                $("#<?= $_GET['name'] ?>_alarmDiv").removeClass("alarmDivActive");
                $("#<?= $_GET['name'] ?>_alarmDiv").addClass("alarmDiv");  
            }*/
           
            setTimeout(funcRef(false, metricNameFromDriverLocal, widgetTitleFromDriverLocal, widgetHeaderColorFromDriverLocal, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, fromGisMarker, fromGisMapRef, fromGisFakeId), 1000);
        }
    }, 1000);
    
    return intervalRef;
}

//Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie
function setupLoadingPanel(widgetName, widgetContentColor, firstLoad)
{
    var height = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - widgetHeaderHeight);
    
    $("#" + widgetName + "_loading").css("height", height + "px");
    $("#" + widgetName + "_loading").css("background-color", widgetContentColor);
    $("#" + widgetName + "_loading p").css("font-size", loadingFontDim + "px");
    $("#" + widgetName + "_loading i").css("font-size", loadingIconDim + "px");
    
    if(firstLoad !== false)
    {
        $("#" + widgetName + "_loading").css("display", "block");
    }
}

//Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie
function getWidgetProperties(widgetName)
{
    var properties = null;
    
    $.ajax({
        url: getParametersWidgetUrl,
        type: "GET",
        data: {"nomeWidget": [widgetName]},
        async: false,
        dataType: 'json',
        success: function (data) 
        {
            properties = data;
        },
        error: function(errorData)
        {
           console.log("Errore in caricamento proprietà widget per widget " + widgetName);
           console.log(JSON.stringify(errorData));
        }
    });
    return properties;
}

function manageInfoButtonVisibility(infoMsg, headerContainer)
{
   if(infoMsg === null || infoMsg === undefined)
   {
       if(headerContainer.attr('id').includes('alarmDivPc'))
       {
           headerContainer.find('div.pcInfoContainer a.info_source').hide();
       }
       else
       {
           headerContainer.find('div.infoButtonContainer a.info_source').hide();
       }
   }
   else
   {
        if((infoMsg.trim() === "")||(infoMsg.trim().length === 0))
        {
            if(headerContainer.attr('id').includes('alarmDivPc'))
            {
                headerContainer.find('div.pcInfoContainer a.info_source').hide();
            }
            else
            {
                headerContainer.find('div.infoButtonContainer a.info_source').hide();
            }
        }
   }
}

//Usata in widgetTable.php, dashboard_configdash.php
function getMetricData(metricId)
{
    var metricData = null;
    $.ajax({
        url: getMetricDataUrl,
        type: "GET",
        data: {"IdMisura": [metricId]},
        async: false,
        dataType: 'json',
        success: function (data) 
        {
            metricData = data;
        },
        error: function()
        {
           metricData = null;
        }
    });
    return metricData;
}
