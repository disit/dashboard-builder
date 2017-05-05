/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

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

/*Globals*/
var loadingFontDim = 13;
var loadingIconDim = 20;
var getParametersWidgetUrl = "../widgets/getParametersWidgets.php";
var getMetricDataUrl = "../widgets/getDataMetrics.php";


/*Usata in tutti gli widget, ma destinata ad essere eliminata: già inglobata in setWidgetLayout*/
function setHeaderFontColor(widget, color)
{
    $("#" + widget).css("color", color);
}

/*Usata in tutti gli widget*/
function addLink(name, url, linkElement, elementToBeWrapped)
{
    if(url) 
    {
        if(linkElement.length === 0)
        {
           linkElement = $("<a id='" + name + "_link_w' href='" + url + "' target='_blank' class='elementLink2'></a>");
           elementToBeWrapped.wrap(linkElement); 
        }
    }
}

/*Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie*/
function showWidgetContent(widgetName)
{
    $("#" + widgetName + "_loading").css("display", "none");
    $("#" + widgetName + "_content").css("display", "block");
}

/*Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie*/
function setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor)
{
    var titleWidth = null;
    
    /*Impostazione header*/
    $("#" + widgetName + "_header").css("background-color", widgetHeaderColor);
    if(hostFile === "config")
    {
        titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 90 - 2));
    }
    else
    {
        $("#" + widgetName + "_buttonsDiv").css("display", "none");
        titleWidth = parseInt(parseInt($("#" + widgetName + "_div").width() - 50 - 2));
    }
    $("#" + widgetName + "_titleDiv").css("width", titleWidth + "px");
    $("#" + widgetName + "_titleDiv").css("color", widgetHeaderFontColor);
    $("#" + widgetName + "_countdownDiv").css("color", widgetHeaderFontColor);
    
    /*Impostazione colore di background del widget*/
    if(widgetName.indexOf("widgetGenericContent") > 0)
    {
         $("#" + widgetName + "_content").css("background-color", widgetHeaderColor);
    }
    else
    {
        $("#" + widgetName + "_content").css("background-color", widgetContentColor);
    }
    
    /*Impostazione altezza widget*/
    var contentHeight = parseInt($("#" + widgetName + "_div").prop("offsetHeight") - 25);
    $("#" + widgetName + "_content").css("height", contentHeight);
}

/*Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie*/
function startCountdown(widgetName, timeToReload, funcRef, elToEmpty, widgetType ,scrollerTimeout, eventNamesArray)
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
            setTimeout(funcRef(false), 1000);
        }
    }, 1000);
}

/*Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie*/
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

/*Usata in widgetTable e tutti widget sulle serie, incluso nuovo pie*/
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
        error: function()
        {
           
        }
    });
    
    return properties;
}

/*Usata in widgetTable.php, dashboard_configdash.php*/
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
