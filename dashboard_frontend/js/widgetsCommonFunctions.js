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

//Globals
var loadingFontDim = 13;
var loadingIconDim = 20;
var getParametersWidgetUrl = "../widgets/getParametersWidgets.php";
var getMetricDataUrl = "../widgets/getDataMetrics.php";


//Usata in tutti gli widget, ma destinata ad essere eliminata: già inglobata in setWidgetLayout
function setHeaderFontColor(widget, color)
{
    $("#" + widget).css("color", color);
}

//Usata in tutti gli widget
function addLink(name, url, linkElement, elementToBeWrapped)
{
    if(url !== 'none' && url !== 'map') 
    {
        if(linkElement.length === 0)
        {
           linkElement = $("<a id='" + name + "_link_w' href='" + url + "' target='_blank' class='elementLink2'></a>");
           elementToBeWrapped.wrap(linkElement); 
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
function setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight)
{
    var titleWidth, contentHeight = null;
    if(showHeader === true)
    {
        //Impostazione header
        $("#" + widgetName + "_header").css("background-color", widgetHeaderColor);
        $("#" + widgetName + "_infoButtonDiv a.info_source").css("color", widgetHeaderFontColor);
        if(widgetHeaderFontColor !== widgetHeaderColor)
        {
            $("#" + widgetName + "_buttonsDiv div.singleBtnContainer a.iconFullscreenModal").css("color", widgetHeaderFontColor);
            $("#" + widgetName + "_buttonsDiv div.singleBtnContainer a.iconFullscreenTab").css("color", widgetHeaderFontColor);
            $("#" + widgetName + "_countdownDiv").css("border-color", widgetHeaderFontColor);
        }
        
        if((!widgetName.includes("widgetButton"))&&(!widgetName.includes("widgetExternalContent"))&&(!widgetName.includes("widgetTrendMentions")))
        {
            if(hostFile === "config")
            {
                if(widgetName.includes("widgetSelector"))
                {
                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 2));
                }
                else
                {
                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 50 - 25 - 2));
                }
            }
            else
            {
                $("#" + widgetName + "_buttonsDiv").css("display", "none");
                if(widgetName.includes("widgetSelector"))
                {
                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 2));
                }
                else
                {
                    titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 25 - 25 - 2));
                }
            }
            $("#" + widgetName + "_titleDiv").css("width", titleWidth + "px");
        }

        $("#" + widgetName + "_titleDiv").css("color", widgetHeaderFontColor);
        $("#" + widgetName + "_countdownDiv").css("color", widgetHeaderFontColor);

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
    if(widgetName.indexOf("widgetGenericContent") > 0)
    {
       $("#" + widgetName + "_content").css("background-color", widgetHeaderColor);
    }
    else
    {
       $("#" + widgetName + "_content").css("background-color", widgetContentColor);
    }
    
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
    var height = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - 25);
    
    $("#" + widgetName + "_loading").css("height", height+"px");
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
