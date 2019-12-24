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
var getIconsPoolUrl = "../widgets/getIconsPool.php"


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
    if (location.href.includes('inspector') && ((widgetName == "DCTemp1_24_widgetTimeTrend6351")||(widgetName == "SensoreViaBolognese_24_widgetSingleContent6353"))){
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
            if (location.href.includes('inspector') && ((widgetName == "DCTemp1_24_widgetTimeTrend6351")||(widgetName == "SensoreViaBolognese_24_widgetSingleContent6353"))){
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


function ecFixFormat(f) {
    clockf = localStorage.getItem('ec_clockf');
    if (clockf === '12' && (f.indexOf('h') === -1)) {
        f = f.replace(/H/g, "h");
        f = f + " A";
    }
    if (clockf === '24' && (f.indexOf('H') === -1)) {
        f = f.replace(/h/g, "H");
        f = f.replace(/[Aa]/, "");
    }
    return f;
}

function getQueryParams(qn) {
    var qs = document.location.search;
    qs = qs.split('+').join(' ');
    var params = {}, tokens, re = /[?&]?([^=]+)=([^&]*)/g;
    while (tokens = re.exec(qs)) {
        if (qn == "locale" && decodeURIComponent(tokens[1]) == 'locale')
            return decodeURIComponent(tokens[2]);
    }
    return false;
}

function getLocale() {
    var locale = getQueryParams('locale');
    var al = [];
    if (typeof moment !== "undefined") {
        al = moment.locales();
    }
    if (locale && al.indexOf(locale) > -1)
        return locale;
    locale = localStorage.getItem('ec_locale');
    if (locale && (al.indexOf(locale) > -1 || al.length === 0))
        return locale;
    return window.navigator.userLanguage || window.navigator.language || "en";
}

function getUTCDate(timestamp)
{
    var date = new Date(timestamp);

    var year = date.getUTCFullYear();
    var month = date.getUTCMonth() + 1; // getMonth() is zero-indexed
    var day = date.getUTCDate();
    var hours = date.getUTCHours();
    var minutes = date.getUTCMinutes();
    var seconds = date.getUTCSeconds();

    month = (month < 10) ? '0' + month : month;
    day = (day < 10) ? '0' + day : day;
    hours = (hours < 10) ? '0' + hours : hours;
    minutes = (minutes < 10) ? '0' + minutes : minutes;
    seconds = (seconds < 10) ? '0' + seconds: seconds;

    return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
}

/*    function getGMTDate(timestamp)
    {
        new Date(timestamp + new Date().getTimezoneOffset() * 60000);
    }*/

Date.prototype.epochConverterGMTString = function() {
    if (typeof moment === "undefined") {
        return this.toUTCString();
    }
    moment.locale('en');
    var md = moment(this);
    if (!md.isValid()) {
        return 'Invalid input.';
    }
    var locale = getLocale();
    var myLocaleData = moment.localeData(locale);
    var myFormat = ecFixFormat(myLocaleData.longDateFormat('LLLL')).replace(/\[([^\]]*)\]/g, " ");
    if (md.format("SSS") != '000') {
        myFormat = myFormat.replace(":mm", ":mm:ss.SSS");
    } else {
        myFormat = myFormat.replace(":mm", ":mm:ss");
    }
    return md.utc().format(myFormat);
}

Date.prototype.relativeDate=function(){if(typeof moment!=="undefined"){moment.locale('en');var md=moment(this);return md.fromNow();} return '';}

function isValidDate(d) {
    if (Object.prototype.toString.call(d) !== "[object Date]")
        return false;
    return !isNaN(d.getTime());
}


function compareJsonElementsByKeyValues(key, order='asc') {
    console.log("JSON Order Function.");
    return function(a, b) {
       /* if(!a.hasOwnProperty(key) ||
            !b.hasOwnProperty(key)) {
            return 0;
        }*/

        let varA, varB = null;

        if(a.hasOwnProperty(key)) {
            varA = (typeof a[key] === 'string') ?
                a[key].toUpperCase() : a[key];
            if (!isNaN(parseInt(varA))) {
                varA = parseInt(varA);
            } else {
                /* if (a['desc']) {
                     varA = a['desc'].toUpperCase();
                 }*/
                varA = 100;
            }
        } else {
            varA = 100;
        }

        if(b.hasOwnProperty(key)) {
            varB = (typeof b[key] === 'string') ?
                b[key].toUpperCase() : b[key];
            if (!isNaN(parseInt(varB))) {
                varB = parseInt(varB);
            } else {
                /* if (b['desc']) {
                     varB = b['desc'].toUpperCase();
                 }*/
                varB = 100;
            }
        } else {
            varB = 100;
        }

        let comparison = 0;
        if (varA > varB) {
            comparison = 1;
        } else if (varA < varB) {
            comparison = -1;
        }
        return (
            (order == 'desc') ?
                (comparison * -1) : comparison
        );
    };
}

function getIconsPool() {

    var properties = null;

    $.ajax({
        url: getIconsPoolUrl,
        type: "GET",
        data: {
            "action": "getAll"
        },
        async: false,
        dataType: 'json',
        success: function (data)
        {
            properties = data;
        },
        error: function(errorData)
        {
            console.log("Errore in caricamento proprietà 'IconsPool (All)'");
            console.log(JSON.stringify(errorData));
        }
    });
    return properties;

}

function getSuggestedIconsPool(hlt, nat, subNat) {

    var properties = null;

    $.ajax({
        url: getIconsPoolUrl,
        type: "GET",
        data: {
            "action": "getSuggested",
            "highLevelType" : hlt,
            "nature": nat,
            "subNature": subNat
        },
        async: false,
        dataType: 'json',
        success: function (data)
        {
            properties = data;
        },
        error: function(errorData)
        {
            console.log("Errore in caricamento proprietà 'IconsPool (Suggested)'");
            console.log(JSON.stringify(errorData));
        }
    });
    return properties;

}

function UrlExists(url)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status!=404;
}

// MS> Enforce status flag of widget content (displayed vs collapsed)
function setWidgetContentVisibility(widgetName, showContent) {
		if(showContent === "no") {
			$("#"+widgetName).css(
				"height", 
				(  parseInt($("#"+widgetName).prop("offsetHeight")) - parseInt($("#"+widgetName+"_content").prop("offsetHeight")) ) + "px" 
			);	
			$("#" + widgetName + "_content").hide(); 
		}
		else {
			$("#" + widgetName + "_content").show(); 
		}			
}
// <MS

function UrlExists(url)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    var res = http.status!=404
    return res;
}

function getSmartCitySensorValues(metric, i, smUrl, timeRange, syncFlag, callback) {

    if (timeRange == null) {
        timeRange = "";
    }

    $.ajax({
        url: smUrl,
        type: "GET",
        data: {},
        async: syncFlag,
        dataType: 'json',
        success: function(originalData)
        {
            let extractedData = {};
          //  let endFlag = false;
            if (originalData.realtime) {
                if (originalData.realtime.results) {
                    if (originalData.realtime.results.bindings.length > 1) {
                        var tmpData = [];
                        extractedData.metricType = metric[i].metricType;
                        extractedData.metricName = metric[i].metricName;
                        for (let t = 0; t < originalData.realtime.results.bindings.length; t++)  {
                            tmpData = originalData.realtime.results.bindings[t][metric[i].metricType];
                            let fatherNode = null;

                            if (originalData.hasOwnProperty("Sensor")) {
                                fatherNode = originalData.Sensor;
                            } else {
                                //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                                fatherNode = originalData.Service;
                            }

                            if (fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType].value_unit != null) {
                                tmpData.metricValueUnit = fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType].value_unit;
                            }
                            tmpData.measuredTime = originalData.realtime.results.bindings[0].measuredTime.value;
                            extractedData[t] = tmpData;
                            // extractedData[metric[i].metricName] = tmpData;
                        }
                    } else {
                        extractedData = originalData.realtime.results.bindings[0][metric[i].metricType];
                        extractedData.metricType = metric[i].metricType;
                        extractedData.metricName = metric[i].metricName;
                        let fatherNode = null;

                        if (originalData.hasOwnProperty("Sensor")) {
                            fatherNode = originalData.Sensor;
                        } else {
                            //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                            fatherNode = originalData.Service;
                        }

                        if (fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType].value_unit != null) {
                            extractedData.metricValueUnit = fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType].value_unit;
                        }
                        extractedData.measuredTime = originalData.realtime.results.bindings[0].measuredTime.value;
                    }
                    callback(extractedData);
                } else {
                    callback(undefined);
                }
            } else {
                callback(undefined);
            }
        },
        error: function (data)
        {
         //   showWidgetContent(widgetName);
          //  $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
          //  $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
            console.log("Errore in scaricamento dati da Service Map");
            console.log(JSON.stringify(data));
        }
    });

}

function getMyKPIValues(metricId, i, timeRange, lastValue, callback) {

    if (timeRange == null) {
        timeRange = "";
    }

    $.ajax({
        url: "../controllers/myKpiProxy.php",
        type: "GET",
        data: {
            myKpiId: metricId[i].metricId,
            timeRange: timeRange,
            lastValue: lastValue,
            action: "getValueUnit"
        },
        async: true,
        dataType: 'json',
        success: function(data) {
            let extractedData = {};
         //   var convertedData = convertDataFromMyKpiToDm(data);
            extractedData.value = data[0].value;
            extractedData.metricType = metricId[i].metricType;
            if (metricId[i].metricId.includes("datamanager/api/v1/poidata/")) {
                extractedData.metricId = metricId[i].metricId.split("datamanager/api/v1/poidata/")[1];
                extractedData.metricName = metricId[i].metricName + "_" + metricId[i].metricId.split("datamanager/api/v1/poidata/")[1];
            } else {
                extractedData.metricId = metricId[i].metricId;
                extractedData.metricName = metricId[i].metricName + "_" + metricId[i].metricId;
            }
            extractedData.measuredTime = new Date(data[0].dataTime).toUTCString();
            if(data[0].valueUnit != null) {
                extractedData.metricValueUnit = data[0].valueUnit;
            }
            callback(extractedData);
            //callback(data);
        },
        error: function (data) {
            console.log("Errore!");
            console.log(JSON.stringify(data));
        }
    });

}


function convertDataFromMyKpiToDm(originalData)
{
    var singleOriginalData, singleData, convertedDate = null;
    var convertedData = {
        data: []
    };

    for(var i = 0; i < originalData.length; i++)
    {
        singleData = {
            commit: {
                author: {
                    IdMetric_data: null, //Si può lasciare null, non viene usato dal widget
                    computationDate: null,
                    value_perc1: null, //Non lo useremo mai
                    value: null,
                    descrip: null, //Mettici il nome della metrica splittato
                    threshold: null, //Si può lasciare null, non viene usato dal widget
                    thresholdEval: null //Si può lasciare null, non viene usato dal widget
                },
                range_dates: 0//Si può lasciare null, non viene usato dal widget
            }
        };

        singleOriginalData = originalData[i];

        convertedDate = new Date(singleOriginalData.dataTime); //2001-11-23 03:08:46
        convertedDate = convertedDate.getFullYear() + "-" + parseInt(convertedDate.getMonth() + 1) + "-" + convertedDate.getDate() + " " + convertedDate.getHours() + ":" + convertedDate.getMinutes() + ":" + convertedDate.getSeconds();

        singleData.commit.author.computationDate = convertedDate;

        if(!isNaN(parseFloat(singleOriginalData.value)))
        {
            singleData.commit.author.value = parseFloat(singleOriginalData.value);
        }
        else
        {
            singleData.commit.author.value = singleOriginalData.value;
        }

        convertedData.data.push(singleData);
    }

    return convertedData;
}

function ObjectSize(obj)
{
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
}

function serializeSensorDataForBarSeries(dataArrayMap, labels1, labels2) {
    var jsonObj = {};
    jsonObj.firstAxis = {};
    jsonObj.secondAxis = {};
    var desc1 = "";
    var desc2 = "device";
    var series = [];

    jsonObj.firstAxis.desc = desc1;
    jsonObj.firstAxis.labels = labels1;

    for(i = 0; i < labels2.length; i++) {
        let seriesArray = [];
        for(j = 0; j < labels1.length; j++) {
                let metricIdx = findWithAttr(dataArrayMap[labels2[i]], labels1[j]);
                if (metricIdx != -1) {
                    if (dataArrayMap[labels2[i]][metricIdx].value != '') {
                        seriesArray.push(parseFloat(parseFloat(dataArrayMap[labels2[i]][metricIdx].value).toFixed(2)));
                    } else {
                        seriesArray.push("");
                    }
                } else {
                    seriesArray.push("");
                }
        }
        series.push(seriesArray);
    }

    jsonObj.secondAxis.desc = desc2;
    jsonObj.secondAxis.labels = labels2;
    jsonObj.secondAxis.series = series;
   // var outJson = JSON.stringify(jsonObj);
    return jsonObj;
}

function objectContains(obj, keyProp) {

    let res = false;
    for (n = 0; n < obj.length; n ++) {
        if (obj[n].metricType == keyProp) {
            res = true;
        }
    }
    return res;

}

function buildBarSeriesArrayMap(dataArray) {

    var dataArrayMap = dataArray.reduce((acc, {metricName, metricType, value}) => {
        (acc[metricName] || (acc[metricName] = [])).push({metricType, value})
        return acc
    }, {});

    return dataArrayMap;

}

function buildBarSeriesArrayMap2(dataArray) {

    var dataArrayMap = dataArray.reduce((acc, {metricType, metricName, value}) => {
        (acc[metricType] || (acc[metricType] = [])).push({metricName, value})
        return acc
    }, {});

    return dataArrayMap;

}

function getDeviceLabelsForBarSeries(dataArray) {

    let deviceLabels = [];
    for(n = 0; n < dataArray.length; n++) {

        if (!deviceLabels.includes(dataArray[n].metricName)) {
            if (dataArray[n].metricHighLevelType == "Sensor") {
                deviceLabels.push(dataArray[n].metricName);
            } else if (dataArray[n].metricHighLevelType == "MyKPI") {
                deviceLabels.push(dataArray[n].metricName + "_" + dataArray[n].metricId);
            }
        }
    }
    return deviceLabels;

}

function getMetricLabelsForBarSeries(dataArray) {

    let metricLabels = [];
    for(n = 0; n < dataArray.length; n++) {

        if (!metricLabels.includes(dataArray[n].metricType)) {
            metricLabels.push(dataArray[n].metricType);
        }
    }
    return metricLabels;

}

function findWithAttr(array, attr) {
    for(var i = 0; i < array.length; i += 1) {
        if(array[i]['metricType'] === attr) {
            return i;
        }
    }
    return -1;
}

/*function getMyKpiLabelsForBarSeries(dataArray) {

    let deviceLabels = [];
    for(n = 0; n < dataArray.length; n++) {

        if (!deviceLabels.includes(dataArray[n].metricName)) {
            deviceLabels.push(dataArray[n].metricName + "_" + dataArray[n].metricId);
        }
    }
    return deviceLabels;

}*/