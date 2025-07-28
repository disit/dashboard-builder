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

console.log("Widgets Common Functions.")

//Globals
var loadingFontDim = 13;
var loadingIconDim = 20;
var widgetHeaderHeight = 25;
var getParametersWidgetUrl = "../widgets/getParametersWidgets.php";
var getMetricDataUrl = "../widgets/getDataMetrics.php";
var getIconsPoolUrl = "../widgets/getIconsPool.php";
var getBubbleMetricsUrl = "../widgets/getBubbleMetricsProxy.php";
var getSvgSingleVariableTemplatesUrl = "../controllers/getSvgSingleVarTemplates.php";
var getOrthomapsUrl = "../widgets/getOrthomaps.php";
var configVars = {};

function getConfigData(configVars, callback) {
    let params = configVars.join(',');

    $.ajax({
        url: '../configForJs.php',
        method: 'GET',
        data: { configVarsStr: params },
        success: function(response) {
            let data = JSON.parse(response);
            callback(null, data);
        },
        error: function(error) {
            callback(error);
        }
    });
}

let configVarsStr = ['kbUrlSuperServiceMap', 'baseServiceURI'];

getConfigData(configVarsStr, function(err, data) {
    if (err) {
        console.error('Error:', err);
    } else {
        Object.keys(data).forEach(key => {
            configVars[key] = data[key];
        });
        // console.log('Updated configVars:', configVars);
        // console.log('kbUrlSuperServiceMap:', configVars['kbUrlSuperServiceMap']);
    }
});


//Usata in tutti gli widget, ma destinata ad essere eliminata: già inglobata in setWidgetLayout
function setHeaderFontColor(widget, color)
{
    $("#" + widget).css("color", color);
}

/*function isColorFullyTransparent(rgbaColor) {
    // Rimuovi spazi e parentesi
    rgbaColor = rgbaColor.replace(/\s/g, '').replace(/[()]/g, '');

    // Dividi i valori RGBA
    const rgbaValues = rgbaColor.split(',');

    // Estrai il valore di alpha
    const alpha = parseFloat(rgbaValues[3]);

    // Verifica se l'alpha è uguale a 0 (trasparenza al 100%)
    return alpha === 0;
}*/

function isColorTransparent(rgbaColor) {
    // Rimuovi spazi e parentesi
    rgbaColor = rgbaColor.replace(/\s/g, '').replace(/[()]/g, '');

    // Dividi i valori RGBA
    const rgbaValues = rgbaColor.split(',');

    // Estrai il valore di alpha
    const alpha = parseFloat(rgbaValues[3]);

    // Verifica se l'alpha è minore di 1 (trasparenza parziale)
    return alpha < 1;
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
    var newLayout = (hostFile === 'baloon' || hostFile === 'baloon-dark' || hostFile === 'gea' || hostFile === 'gea-night' || hostFile === 'gea-green' || hostFile === 'gea-orange' || hostFile === 'pa') ? true : false;

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

    if (newLayout && isColorTransparent(widgetHeaderColor)) {
        $("#" + widgetName + "_header").css("cssText", "background-color: transparent !important;");
        $("#" + widgetName + "_titleDiv").css("cssText", "background-color: transparent !important;");
    } else {
        $("#" + widgetName + "_titleDiv").css("color", widgetHeaderFontColor);
    }
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
    if (newLayout && isColorTransparent(widgetContentColor)) {
        $("#" + widgetName).css("background-color", widgetContentColor);
        //$("li#" + widgetName).css('border', '1px solid grey');  // add borderColor parameter from view
        $("#" + widgetName + "_noDataAlertText").css("background-color", "transparent");
        $("#" + widgetName + "_noDataAlertIcon").css("background-color", "transparent");
        $("#" + widgetName + "_chartContainer").css("background-color", "transparent");
        $("#" + widgetName + "_content").css("background-color", "transparent");
    } else {
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

function getBubbleMetrics(query, idx, callback) {

    var properties = null;

    $.ajax({
        url: getBubbleMetricsUrl,
        type: "GET",
        data: {
            "query": query
        },
        async: true,
        dataType: 'json',
        success: function (data)
        {
            properties = [idx, data];
            callback(properties);
        },
        error: function(errorData)
        {
            console.log("Errore nel caricamento metriche");
            console.log(JSON.stringify(errorData));
        }
    });

}

function getSvgSingleVariableTemplates(idx, callback) {

    var properties = null;

    $.ajax({
        url: getSvgSingleVariableTemplatesUrl,
        type: "GET",
        data: {
            "action": "getAll"
        },
        async: true,
        dataType: 'json',
        success: function (data)
        {
            properties = [idx, data];
            callback(properties);
        },
        error: function(errorData)
        {
            console.log("Errore in caricamento SVG Templates");
            console.log(JSON.stringify(errorData));
        }
    });

}

function getOrthomaps(widgetName, callback) {

    var properties = null;

    $.ajax({
        url: getOrthomapsUrl,
        type: "GET",
        data: {
            "widgetName": widgetName
        },
        async: true,
        dataType: 'json',
        success: function (data)
        {
            properties = [data];
            callback(properties);
        },
        error: function(errorData)
        {
            console.log("Error while loading Orthomaps.'");
            console.log(JSON.stringify(errorData));
        }
    });

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
        url: encodeServiceUri(smUrl),
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
                        extractedData.value = [];
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

                            if (fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType] != null) {
                                if (fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType].value_unit != null) {
                                    tmpData.metricValueUnit = fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType].value_unit;
                                }
                            }
                            tmpData.measuredTime = originalData.realtime.results.bindings[t].measuredTime.value;
                            extractedData.value[t] = tmpData;
                            // extractedData[metric[i].metricName] = tmpData;
                        }
                    } else {
                        if (originalData.realtime.results.bindings[0][metric[i].metricType] != null) {
                            extractedData = originalData.realtime.results.bindings[0][metric[i].metricType];
                        } else {
                            extractedData.value = null;
                        }
                        extractedData.metricType = metric[i].metricType;
                        extractedData.metricName = metric[i].metricName;
                        let fatherNode = null;

                        if (originalData.hasOwnProperty("Sensor")) {
                            fatherNode = originalData.Sensor;
                        } else {
                            //Prevedi anche la gestione del caso in cui non c'è nessuna di queste tre, sennò il widget rimane appeso.
                            fatherNode = originalData.Service;
                        }

                        if (fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType] != null) {
                            if (fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType].value_unit != null) {
                                extractedData.metricValueUnit = fatherNode.features[0].properties.realtimeAttributes[metric[i].metricType].value_unit;
                            }
                        }
                        extractedData.measuredTime = originalData.realtime.results.bindings[0].measuredTime.value;
                    }
                    callback(extractedData);
                } else {
                    extractedData = [];
                    extractedData.metricType = metric[i].metricType;
                    extractedData.metricName = metric[i].metricName;
                    callback(extractedData);
                }
            } else {
                extractedData = [];
                extractedData.metricType = metric[i].metricType;
                extractedData.metricName = metric[i].metricName;
                callback(extractedData);
            }
        },
        error: function (data)
        {
         //   showWidgetContent(widgetName);
          //  $("#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_chartContainer").hide();
          //  $('#<?= str_replace('.', '_', str_replace('-', '_', $_REQUEST['name_w'])) ?>_noDataAlert').show();
            let extractedData = {};
            extractedData = [];
            extractedData.metricType = metric[i].metricType;
            extractedData.metricName = metric[i].metricName;
            console.log("Errore in scaricamento dati da Service Map");
            console.log(JSON.stringify(data));
            callback(extractedData);
        }
    });

}

function getMyKPIValues(metricObj, i, timeRange, lastValue, callback) {

    if (timeRange == null) {
        timeRange = "";
    }

    $.ajax({
        url: "../controllers/myKpiProxy.php",
        type: "GET",
        data: {
            myKpiId: metricObj[i].metricId,
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
            extractedData.metricType = metricObj[i].metricType;
            if (metricObj[i].metricId.includes("datamanager/api/v1/poidata/")) {
                extractedData.metricId = metricObj[i].metricId.split("datamanager/api/v1/poidata/")[1];
                extractedData.metricName = metricObj[i].metricName + "_" + metricObj[i].metricId.split("datamanager/api/v1/poidata/")[1];
            } else {
                extractedData.metricId = metricObj[i].metricId;
                extractedData.metricName = metricObj[i].metricName + "_" + metricObj[i].metricId;
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

function serializeSensorDataForBarSeries(dataArrayMap, labels1, labels2, flipFlag) {
    var jsonObj = {};
    jsonObj.firstAxis = {};
    jsonObj.secondAxis = {};
    if (flipFlag != true) {
    //    var desc1 = "metrics";
        var desc1 = "value type";
    //    var desc2 = "device";
        var desc2 = "value name";
    } else {
    //    var desc1 = "device";
        var desc1 = "value name";
    //    var desc2 = "metrics";
        var desc2 = "value type";
    }
    var series = [];

    jsonObj.firstAxis.desc = desc1;
    jsonObj.firstAxis.labels = labels1;

    for(i = 0; i < labels2.length; i++) {
        let seriesArray = [];
        for(j = 0; j < labels1.length; j++) {
                let metricIdx = findWithAttr(dataArrayMap[labels2[i]], labels1[j], flipFlag);
                if (metricIdx != -1) {
                    if (dataArrayMap[labels2[i]][metricIdx].value != '') {
                        if (!$.isNumeric(dataArrayMap[labels2[i]][metricIdx].value) && dataArrayMap[labels2[i]][metricIdx].value != null && dataArrayMap[labels2[i]][metricIdx].value != 'NaN') {
                            seriesArray.push(dataArrayMap[labels2[i]][metricIdx].value);
                        } else {
                            if (isNaN(parseFloat(dataArrayMap[labels2[i]][metricIdx].value))) {
                                seriesArray.push("");
                            } else {
                                seriesArray.push(parseFloat(parseFloat(dataArrayMap[labels2[i]][metricIdx].value).toFixed(2)));
                            }
                        }
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

function serializeDataForSeries(metricLabels, deviceLabels) {

    let series = {};
    series.firstAxis = {};
    //series.firstAxis.desc = "metrics";
    series.firstAxis.desc = "value type";
    series.firstAxis.labels = metricLabels;

    series.secondAxis = {};
    //series.secondAxis.desc = "devices";
    series.secondAxis.desc = "value name";
    series.secondAxis.labels = deviceLabels;
    series.secondAxis.series = [];

    return series;

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
            if (dataArray[n].metricHighLevelType == "MyKPI") {
                deviceLabels.push(dataArray[n].metricName + "_" + dataArray[n].metricId);
            } else {
                deviceLabels.push(dataArray[n].metricName);
            }
        }
    }
    return deviceLabels;

}

function getMetricLabelsForBarSeries(dataArray) {

    let metricLabels = [];
    for(n = 0; n < dataArray.length; n++) {
        if (dataArray[n].metricType != null) {
            if (!metricLabels.includes(dataArray[n].metricType)) {
                metricLabels.push(dataArray[n].metricType);
            }
        } else if (dataArray[n].smField != null) {
            if (!metricLabels.includes(dataArray[n].smField)) {
                metricLabels.push(dataArray[n].smField);
            }
        } else if (dataArray[n].metricHighLevelType == "KPI") {
            if (!metricLabels.includes(dataArray[n].metricId)) {
                metricLabels.push(dataArray[n].metricId);
            }
        }
    }
    return metricLabels;

}

function findWithAttr(array, attr, flipFlag) {
    for(var i = 0; i < array.length; i += 1) {
        if (flipFlag !== true) {
            if (array[i]['metricType'] === attr) {
                return i;
            }
        } else {
            if (array[i]['metricName'] === attr) {
                return i;
            }
        }
    }
    return -1;
}

function getMyKPIUpperTimeLimit(hours) {
    let now = new Date();
    let timeZoneOffsetHours = now.getTimezoneOffset() / 60;
    let upperTimeLimit = now.setHours(now.getHours() - hours - timeZoneOffsetHours);
    let upperTimeLimitUTC = new Date(upperTimeLimit).toUTCString();
    let upperTimeLimitISO = new Date(upperTimeLimitUTC).toISOString();
    let upperTimeLimitISOTrim = upperTimeLimitISO.substring(0, isoDate.length - 5);
    return upperTimeLimitISOTrim;
    //    myKPITimeRange = "&from=" + myKPIFromTimeRangeISOTrimmed + "&to=" + isoDateTrimmed;
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

function removeLoadingBubbleMetricsMsg(elem) {

    for (let sc = 0; sc < elem.length; sc++) {
        if (elem.options[sc].value == 'loading available metrics...' || elem.options[sc].value == 'no metrics available') {
            elem.remove(sc);
            break;
        }
    }

}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function encodeServiceUri (suri) {

    return encodeURI(suri);

}

function buildFontIconPicker(iconPoolDatasetJSON, i, widgetId) {
    $('#Selector_poolBtn_' + i).fontIconPicker({
        //  $('.poolBtn').fontIconPicker({
        //    source: svgs,
        source: iconPoolDatasetJSON,
        theme: 'fip-bootstrap',
        //    appendTo: 'self',
        iconGenerator: function (item, flipBoxTitle, index) {
            return '<i style="display: flex; align-items: center; justify-content: center; height: 100%;"><img id="' + widgetId + '_poolImg_' + i + '" class="poolImg" src="../img/widgetSelectorIconsPool' + item + '.svg" style="height:40px"></i>';
            // LAST WORKING OK!
            //        return '<i style="display: flex; align-items: center; justify-content: center; height: 100%;"><img src="../img/widgetSelectorIconsPool/hlt/' + item + '.svg" style="height:56px"></i>';
            //	return '<i style="display: flex; align-items: center; justify-content: center; height: 100%;"><svg style="height: 32px; width: auto;" class="svg-icon ' + item + '"><use xlink:href="#' + item + '"></use></svg></i>';
            //	return '<i style="display: flex; align-items: center; justify-content: center; height: 100%;"><svg style="height: 32px; width: auto;" class="svg-icon ' + item + '"><use xlink:href="C:/Apache24/htdocs/dashboardSmartCity/img/widgetSelectorIconsPool/hlt/' + item + '.svg"></use></svg></i>';
        }
    })
        .on('change', function () {
            var item = $(this).val(),
                liveView = $('#figura'),
                liveTitle = liveView.find('h3'),
                liveImage = liveView.find('img');
            if ('' === item) {
                liveTitle.html('Please Select…');
                //liveImage.attr( 'src', 'lib/svgs/placeholder.png' );
                return;
            }
            liveTitle.html(item.split('-').join(' '));
            liveImage.attr('src', '../img/widgetSelectorIconsPool/hlt' + item + '.svg');
        });
}


function checkFull(array) {

    for (let i = 0; i < array.length; i++) {
        if (array[i] == null) {
            return false;
        }
    }

    return true;
}


function buildSvgIcon (path, value, lolLevel, pinContainer, svgContainer, widgetName, sourceFlag, countSvgCnt, totalSvgCnt, desc, svgContainerArray, updateSingleMarkerFlag, sUri) {

    const LOGLEVEL_INFO = "info";
    const LOGLEVEL_ERROR = "error";
    const LOGLEVEL_OFF = "off";
    var lol = LOGLEVEL_INFO;
    function log(message, level) {
        if(lol == LOGLEVEL_INFO) {
            if(typeof message !== "object") {
            //    console.info(new Date().getTime());
            //    console.info("SIOW> "+message);
            }
            else {
            //    console.info(new Date().getTime());
            //    console.info(message);
            }
        }
        else if(lol == level) {
            if(typeof message !== "object") {
            //    console.error("SIOW> "+message);
            }
            else {
            //    console.error(message);
            }
        }
    }

    var tpl = path;
    svgContainer[1] = sUri;
    svgContainer[2] = updateSingleMarkerFlag;
    log(tpl, LOGLEVEL_INFO);
  //  var val = JSON.stringify({lastValue: url.searchParams.get("val")});
    var val = '{"lastValue":"' + value + '"}';
    log(val, LOGLEVEL_INFO);
 //   if(url.searchParams.get("lol")) lol = url.searchParams.get("lol");
    if (lolLevel) lol = lolLevel;
    log(lol, LOGLEVEL_INFO);

        svgContainer.load(tpl, function(response,status,xhr){
            var serviceUri = svgContainer[1];
            var updateSingleMarker = svgContainer[2];
            svgContainer.hide();
            if(status == "error") {
                log("Loading failed of static content from \""+tpl+"\". Error message is \""+xhr.status+" "+xhr.statusText+"\". Double check the staticSource configuration parameter. SIOW stops here. Nothing will work.", LOGLEVEL_ERROR);
                return;
            } else {
               /* if (desc != null) {
                    console.log("Build Cstuom SVG Pin # " + countSvgCnt + "for Event: " + desc);
                }*/
            }

            log("Static contents loaded.",LOGLEVEL_INFO);

            // scroll data attributes in static contents

            actions = [];
            var svgContId = svgContainer[0].id;
            $("#" + svgContId + " [data-siow]").each(function() {

                // parse attribute value

                var dataSiow = {};

                dataSiow.element = $(this);
                log("Element found that is of interest for SIOW:", LOGLEVEL_INFO);
                log(dataSiow.element, LOGLEVEL_INFO);

                try {
                    //  dataSiow.config = $(this).data("siow");
                    dataSiow.config = JSON.parse($(this).attr("data-siow"));
                    log("Successfully parsed the following:", LOGLEVEL_INFO);
                    log(dataSiow.config, LOGLEVEL_INFO);
                } catch (e) {
                    log("Unable to parse the following (" + e.message + ", skipping to next element):", LOGLEVEL_ERROR);
                    log(JSON.parse($(this).attr("data-siow")), LOGLEVEL_ERROR);
                    return true;
                }

                var cleanedConfig = [];

                $.each(dataSiow.config, function (i, aSiow) {

                    // validate event handler

                    // var aSiow = $(this);

                    if (!aSiow.hasOwnProperty("event")) {
                        log("Could not find mandatory event in the following handler definition (skipping to next):", LOGLEVEL_ERROR);
                        log(aSiow, LOGLEVEL_ERROR);
                        return true;

                    }
                    var event = aSiow.event;

                    log("event = " + event, LOGLEVEL_INFO);

                    if (!aSiow.hasOwnProperty("originator")) {
                        log("Could not find mandatory originator in the following handler definition (skipping to next):", LOGLEVEL_ERROR);
                        log(aSiow, LOGLEVEL_ERROR);
                        return true;
                    }
                    if (!(aSiow.originator == "server" || aSiow.originator == "client")) {
                        log("Invalid value \"" + aSiow.originator + "\" for originator in the following handler definition (allowed values are \"server\" or \"client\", skipping to next):", LOGLEVEL_ERROR);
                        log(aSiow, LOGLEVEL_ERROR);
                        return true;
                    }
                    var originator = aSiow.originator;

                    log("originator = " + originator, LOGLEVEL_INFO);

                    if (!aSiow.hasOwnProperty("actions")) {
                        log("Could not find mandatory actions in the following handler definition (skipping to next):", LOGLEVEL_ERROR);
                        log(aSiow, LOGLEVEL_ERROR);
                        return true;
                    }
                    if (!Array.isArray(aSiow.actions)) {
                        log("Invalid actions in the following handler definition (array expected, skipping to next handler):", LOGLEVEL_ERROR);
                        log(aSiow, LOGLEVEL_ERROR);
                        return true;
                    }

                    var cleanedActions = [];

                    $.each(aSiow.actions, function (ii, aSiowAction) {

                        // validate action

                        //var aSiowAction = $(this);
                        log("Parsing the following action:", LOGLEVEL_INFO);
                        log(aSiowAction, LOGLEVEL_INFO);

                        if (!aSiowAction.hasOwnProperty("target")) {
                            log("Could not find mandatory target in the following handler action definition (skipping to next):", LOGLEVEL_ERROR);
                            log(aSiowAction, LOGLEVEL_ERROR);
                            return true;
                        }
                        var target = aSiowAction.target;

                        log("target = " + target, LOGLEVEL_INFO);

                        /*if(!aSiowAction.hasOwnProperty("input")) {
                            log("Could not find mandatory input in the following handler action definition (skipping to next):",LOGLEVEL_ERROR);
                            log(aSiowAction,LOGLEVEL_ERROR);
                            return true;
                        }*/
                        var input = aSiowAction.input;

                        log("input = " + input, LOGLEVEL_INFO);

                        var normalize = null;
                        if (aSiowAction.hasOwnProperty("normalize")) {
                            var normArgs = aSiowAction.normalize.split(" ");
                            var valid = true;
                            if (normArgs.length != 4) {
                                log("Invalid normalize specification in the following handler action definition (expected four numbers separated by spaces, skipping normalization):", LOGLEVEL_ERROR);
                                log(aSiowAction, LOGLEVEL_ERROR);
                                valid = false;
                            }
                            for (var i = 0; i < 4; i++) {
                                if (isNaN(normArgs[i])) {
                                    var what = null;
                                    switch (i) {
                                        case 0:
                                            what = "minIn";
                                            break;
                                        case 1:
                                            what = "minOut";
                                            break;
                                        case 2:
                                            what = "maxIn";
                                            break;
                                        case 3:
                                            what = "maxOut";
                                            break;
                                    }
                                    log("Invalid " + what + " \"" + normArgs[i] + "\" in normalize specification in the following handler action definition (expected number, skipping normalization):", LOGLEVEL_ERROR);
                                    log(aSiowAction, LOGLEVEL_ERROR);
                                    valid = false;
                                }
                            }
                            if (valid) {
                                normalize = aSiowAction.normalize;
                                log("normalize = " + normalize, LOGLEVEL_INFO);
                            }
                        }

                        var thresholds = null;
                        if (aSiowAction.hasOwnProperty("thresholds")) {
                            if (aSiowAction.thresholds != null) {
                                var treshArgs = aSiowAction.thresholds.split(" ");
                                var valid = true;
                                if (treshArgs.length % 2 == 0) {
                                    log("Invalid thresholds in the following handler action definition (expected an odd quantity of space-separated values, skipping discretization):", LOGLEVEL_ERROR);
                                    log(aSiowAction, LOGLEVEL_ERROR);
                                    valid = false;
                                }
                                for (var i = 3; i < treshArgs.length; i = i + 2) {
                                    if ((!isNaN(treshArgs[i - 2])) && (!isNaN(treshArgs[i])) && Number(treshArgs[i - 2]) >= Number(treshArgs[i])) {
                                        log("Invalid thresholds in the following handler action definition (expected increasing limits, skipping discretization):", LOGLEVEL_ERROR);
                                        log(aSiowAction, LOGLEVEL_ERROR);
                                        valid = false;
                                    }
                                }
                                if (valid) {
                                    thresholds = aSiowAction.thresholds;
                                    log("thresholds = " + thresholds, LOGLEVEL_INFO);
                                }
                            }
                        }

                        var strformat = null;
                        if (aSiowAction.hasOwnProperty("format")) {
                            if (aSiowAction.format.includes("{0}")) {
                                strformat = aSiowAction.format;
                                log("format = " + strformat, LOGLEVEL_INFO);
                            } else {
                                log("Invalid format in the following handler action definition (placeholder {0} not found, skipping formatting):", LOGLEVEL_ERROR);
                                log(aSiowAction, LOGLEVEL_ERROR);
                            }
                        }

                        var find = null;
                        if (aSiowAction.hasOwnProperty("find")) {
                            find = aSiowAction.find;
                        }

                        var cleanedAction = {};
                        cleanedAction.element = dataSiow.element;
                        cleanedAction.event = event;
                        cleanedAction.originator = originator;
                        cleanedAction.target = target;
                        cleanedAction.input = input;
                        cleanedAction.normalize = normalize;
                        cleanedAction.thresholds = thresholds;
                        cleanedAction.strFormat = strformat;
                        cleanedAction.find = find;
                        cleanedActions.push(cleanedAction);

                    });

                    aSiow.actions = cleanedActions;

                    cleanedConfig.push(Object.assign({}, aSiow));

                });

                dataSiow.config = cleanedConfig;

                $.each(dataSiow.config, function (c, dsc) {
                    actions = actions.concat(dsc.actions);
                });

            });

            // PERFORM ACTIONS
            $.each(actions,function(iii,action){
                //var action = $(this);
                log("Arranging to perform the following action:",LOGLEVEL_INFO);
                log(action,LOGLEVEL_INFO);

                // reading from server
                var actualEvent = action.event;
                var data = val;
                try {

                    log("Client is going to handle an event originated by the server, event name is \""+action.event+"\", destination is \""+action.target+"\", received data are \""+data+"\", and the interested element is the following:",LOGLEVEL_INFO);
                    log(action.element,LOGLEVEL_INFO);

                    var value = data; log(value,LOGLEVEL_INFO); log(action.input,LOGLEVEL_INFO);

                    if(action.input) {
                        value = jsonPath(JSON.parse(value),action.input);
                        if(value.length == 1) value = value[0];
                        if(typeof value === "object" ) value = JSON.stringify(value);
                    }
                    log("Raw data that is going to be used for updating the synoptic:",LOGLEVEL_INFO);
                    log(value,LOGLEVEL_INFO);
                    if(action.normalize != null) {
                        var nArgs = action.normalize.split(" ");
                        var min13 = Math.min(Number(nArgs[1]),Number(nArgs[3]));

                        if(min13 < 0) {
                            nArgs[1] = Number(nArgs[1]) - min13;
                            nArgs[3] = Number(nArgs[3]) - min13;
                        }
                        var offset = ((Number(value)-Number(nArgs[0]))/(Number(nArgs[2])-Number(nArgs[0])))*(Number(nArgs[3])-Number(nArgs[1]));
                        value = (offset>=0?Math.min(Number(nArgs[1]),Number(nArgs[3])):Math.max(Number(nArgs[3]),Number(nArgs[1])))+offset;

                        if(min13 < 0) {
                            value+=min13;
                            nArgs[1]+=min13;
                            nArgs[3]+=min13;
                        }

                        if(Number(value) < Math.min(Number(nArgs[1]),Number(nArgs[3]))) value = Math.min(Number(nArgs[1]),Number(nArgs[3]))
                        if(Number(value) > Math.max(Number(nArgs[3]),Number(nArgs[1]))) value = Math.max(Number(nArgs[3]),Number(nArgs[1]))

                    }
                    log("Normalized data that is going to be used for updating the synoptic:",LOGLEVEL_INFO);
                    log(value,LOGLEVEL_INFO);
                    if(action.thresholds != null) {
                        var tArgs = action.thresholds.split(" ");
                        var newValue = tArgs[0];
                        var li = 1;
                        if(isNaN(value)) {
                            while(li < tArgs.length) {
                                if(tArgs[li] == value) {
                                    newValue = tArgs[li+1];
                                    break;
                                }
                                li = li + 2;
                            }
                        }
                        else {
                            while(Number(value) >= Number(tArgs[li])) {
                                newValue = tArgs[li+1];
                                li = li + 2;
                            }
                        }
                        value = newValue;
                    }
                    log("Discretized data that is going to be used for updating the synoptic:",LOGLEVEL_INFO);
                    log(value,LOGLEVEL_INFO);

                    if(action.find) {
                        var doPrefix = false;
                        var isSelectorValid = true;
                        if($(value).length == 1) { doPrefix = false; }
                        else if($("#"+value).length == 1) {  doPrefix = true; }
                        else { isSelectorValid = false; }
                        if(isSelectorValid && action.find.split(" ")[0] == "attribute") {
                            log("Value of "+action.find+" of "+value+": ",LOGLEVEL_INFO);
                            if(!doPrefix) value = $(value).first().attr(action.find.split(" ")[1]);
                            else value = $("#"+value).first().attr(action.find.split(" ")[1]);
                            log(value,LOGLEVEL_INFO);
                        }
                        else if(isSelectorValid && action.find.split(" ")[0] == "style"){
                            log("Value of "+action.find+" of "+value+": ",LOGLEVEL_INFO);
                            if(!doPrefix) value = $(value).first().css(action.find.split(" ")[1]);
                            else value = $("#"+value).first().css(action.find.split(" ")[1]);
                            log(value,LOGLEVEL_INFO);
                        }
                        else {
                            log("Client handled event from server, event name was \""+action.event+"\", the FIND selector or argument was not valid, the FIND was ignored",LOGLEVEL_ERROR);
                        }
                    }

                    if(action.strFormat) value = action.strFormat.replace("{0}",value);

                    if(!action.target) action.target = "textContent";
                    if(action.target == "textContent") {
                        action.element[0].textContent = value;
                        log("Client handled event from server, event name was \""+action.event+"\", destination was \""+action.target+"\", received value was \""+value+"\", and the interested element has been the following:",LOGLEVEL_INFO);
                        log(action.element,LOGLEVEL_INFO);
                    }
                    else if(action.target.split(" ")[0] == "attribute") {
                        action.element.attr(action.target.split(" ")[1],value);
                        log("Client handled event from server, event name was \""+action.event+"\", destination was \""+action.target+"\", received value was \""+value+"\", and the interested element has been the following:",LOGLEVEL_INFO);
                        log(action.element,LOGLEVEL_INFO);
                    }
                    else if(action.target.split(" ")[0] == "style"){
                        if(action.target.split(" ")[1] == "offset-distance") {
                        //    if(action.element.data("path")) {
                            if(JSON.parse(action.element.attr("data-path"))) {
                                var path = null;
                            //    if(action.element.data("path").startsWith("#")) {
                                if (JSON.parse(action.element.attr("data-path")).startsWith("#"))  {
                                 //   path = $(action.element.data("path")).attr("d");
                                    path = $(JSON.parse(action.element.attr("data-path"))).attr("d");
                                }
                                else {
                                //    path = action.element.data("path");
                                    path = JSON.parse(action.element.attr("data-path"));
                                }
                                action.element.css("motion-path","path('"+path+"')");
                                action.element.css("offset-path","path('"+path+"')");
                            }
                        }
                        action.element.css(action.target.split(" ")[1],value);
                        log("Client handled event from server, event name was \""+action.event+"\", destination was \""+action.target+"\", received value was \""+value+"\", and the interested element has been the following:",LOGLEVEL_INFO);
                        log(action.element,LOGLEVEL_INFO);
                    }
                    else {
                        log("Client handled event from server, event name was \""+action.event+"\", DESTINATION WAS NOT VALID AND THEREFORE NOTHING WAS DONE, received value was \""+value+"\", and the interested element has been the following:",LOGLEVEL_ERROR);
                        log(action.element,LOGLEVEL_ERROR);
                    }

                }
                catch(e) {
                    log("An error occurred (\""+e.message+"\") while handling the server-side event \""+action.event+"\" on this element:",LOGLEVEL_ERROR);
                    log(action.element,LOGLEVEL_ERROR);
                }

            });

            let svgElementHTML = "data:image/svg+xml;base64," + btoa(svgContainer[0].innerHTML.trim().replace(/(\r\n|\n|\r)/gm," "));
            let stopFlag = 0;
            if (sourceFlag == "selector") {
                pinContainer.children("a.gisPinLink").children("div.poolIcon").children(0).attr("src", svgElementHTML);

            } else if (sourceFlag == "map") {
                svgContainer.attr("src", svgElementHTML);
             //   console.log("Build Custom SVG Pin # " + countSvgCnt + "for Event: " + desc);
                svgContainerArray[countSvgCnt-1] = svgContainer;
                var srcUrl = null;
                if (svgContainerArray[countSvgCnt-1]) {
                    if (svgContainerArray[countSvgCnt-1].length > 0) {
                        srcUrl = svgContainerArray[countSvgCnt-1][0].attributes['src'].value;
                    }
                }
                if ((svgContainerArray.length == totalSvgCnt && checkFull(svgContainerArray)) || updateSingleMarkerFlag == true) {
                   // console.log("Show Custom SVG Pin for Event: " + desc);
                    $.event.trigger({
                        type: "updateCustomLeafletMarkers",
                        eventGenerator: $(this),
                        targetWidget: widgetName,
                        desc: desc,
                        id: countSvgCnt,
                        tpl: tpl,
                        serviceUri: serviceUri,
                        updateSingleMarkerFlag: updateSingleMarker,
                        srcUrl: srcUrl
                    });
                }
            }
         //   svgContainer.hide();
        });
 //   });

}

function checkSingleMetricObject(obj) {
    var isSingle = true;
    for (let n = 1; n < obj.length; n++) {
        if (obj[n].transX - obj[n-1].transX != 0) {
            isSingle = false;
        }
    }
    return isSingle
}

function sortSingleSerie(seriesObj, order) {
    seriesObj.sort(function (a, b) {
        if (typeof (a.data[0]) != "number") {
            a.data[0] = 0;
        }
        if (typeof (b.data[0]) != "number") {
            b.data[0] = 0;
        }
        if (order == "asc") {
            return a.data[0] - b.data[0];
        } else if (order == "desc") {
            return b.data[0] - a.data[0];
        }
    });
    return seriesObj;
}

function sortMultiSerieForBarCharts(seriesObj, order, chartType)
{
    for (let i = 0; i < seriesObj[0].points.length; i++) {
        let pointsPos = [];
        let pointsGroup = [];

        seriesObj.forEach(function (series, j) {
            let point = series.points[i];
            if (series.visible) {
                let args = point.shapeArgs;
                pointsGroup.push(series.points[i]);
                pointsPos.push({
                    transX: args.x,
                    width: args.width
                })
            }
        });

        let distX = 0;

        pointsGroup.sort(function (a, b) {
            if (typeof (a.y) != "number") {
                a.y = 0;
            }
            if (typeof (b.y) != "number") {
                b.y = 0;
            }
            if (order == "ascendent") {
                return a.y - b.y
            } else if (order == "descendent") {
                return b.y - a.y
            }
        }).forEach(function (point, i) {
            if (chartType != "horizontal") {
                if (point.dataLabel != null) {
                    point.dataLabel.attr({
                      //  x: pointsPos[i].transX
                        x: pointsPos[i].transX + pointsPos[i].width/3
                    })
                }
            } else {
                let chart = point.series.chart,
                    plotHeigh = chart.plotSizeX -20;
                if (point.dataLabel != null) {
                    point.dataLabel.attr({
                        y: plotHeigh - pointsPos[i].transX
                    })
                }
            }
            if (point.graphic != null) {
                point.graphic.attr({
                    x: pointsPos[i].transX
                })
            }
        })
    }
    return seriesObj;
}

function getOrganizationParams(callback) {

    var properties = null;

    $.ajax({
        type: "GET",
        url: "../controllers/getOrganizationParameters.php",
        data: {
            action: "getAllParameters",
        },
        async: true,
        dataType: 'json',
        success: function (data)
        {
            properties = [data];
            callback(properties);
        },
        error: function(errorData)
        {
            console.log("Error while loading Organization Parameters.'");
            console.log(JSON.stringify(errorData));
        }
    });

}

function getServiceUri(link) {
    var sUri = null;
    if (link) {
        if (link.includes("serviceUri=") && link.includes("&format=json")) {
            sUri = link.split("serviceUri=")[1].split("&format=json")[0];
        } else if (link.includes("serviceUri=")) {
            sUri = link.split("serviceUri=")[1];
        } else if (link.includes("datamanager/api/v1/poidata/=")) {
            sUri = link.spli("datamanager/api/v1/poidata/")[1];
        } else {
            sUri = link;
        }
        return sUri;
    } else {
        return "";
    }
}

function getMeanOfAllMetrics(originalData)
{
    var singleOriginalData, singleData, convertedDate = null;
    var convertedData = {
        data: []
    };

    var originalDataWithNoTime = 0;
    var originalDataNotNumeric = 0;
    var meanDataObj = {};

    if(originalData.hasOwnProperty("realtime"))
    {
        if(originalData.realtime.hasOwnProperty("results"))
        {
            if(originalData.realtime.results.hasOwnProperty("bindings"))
            {
                if(originalData.realtime.results.bindings.length > 0)
                {
                    let propertyJson = "";
                    if(originalData.hasOwnProperty("BusStop"))
                    {
                        propertyJson = originalData.BusStop;
                    }
                    else
                    {
                        if(originalData.hasOwnProperty("Sensor"))
                        {
                            propertyJson = originalData.Sensor;
                        }
                        else
                        {
                            if(originalData.hasOwnProperty("Service"))
                            {
                                propertyJson = originalData.Service;
                            }
                            else
                            {
                                propertyJson = originalData.Services;
                            }
                        }
                    }

                    for(var j = 0; j < originalData.realtime.head.vars.length; j++) {
                        var singleObj = {}
                        var field = originalData.realtime.head.vars[j];
                        var numericCount = 0;
                        var sum = 0;
                        var mean = 0;

                        if (field == "updating" || field == "measuredTime" || field == "instantTime" || field == "dateObserved") {
                            // convertedDate = singleOriginalData.updating.value;
                            continue;
                        }

                        for (var i = 0; i < originalData.realtime.results.bindings.length; i++) {
                            singleOriginalData = originalData.realtime.results.bindings[i];

                            if (singleOriginalData[field] !== undefined) {
                                if (!isNaN(parseFloat(singleOriginalData[field].value))) {
                                    numericCount++;
                                    sum = sum + parseFloat(singleOriginalData[field].value);
                                }
                            }

                        }
                        mean = sum / numericCount;
                        meanDataObj[field] = mean;

                    }

                    return meanDataObj;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

function buildDynamicPassedData(data, name) {
    var passedJson = [];
    for (const item in data) {
        var singleJson = {};
        singleJson["metricId"] = "";
        singleJson["metricHighLevelType"] = "Dynamic";
        singleJson["metricName"] = name;
        singleJson["metricType"] = item;
        singleJson["metricValueUnit"] = "";
        singleJson["value"] = data[item];
        passedJson.push(singleJson)
    }
    return passedJson;
}

function getParams(isIFrame = false){
    let queryString = null;
    if(isIFrame){
        queryString = window.parent.location.search;
    }else{
        queryString = window.location.search;
    }
    out_obj = {};
    const urlParams = new URLSearchParams(queryString);
    const entries = urlParams.entries();
    for(const entry of entries) {
        console.log(`${entry[0]}: ${entry[1]}`);
        out_obj[entry[0]] = entry[1];
    }
    console.log(out_obj);
    return JSON.stringify(out_obj);
}

function openNewDashboard(url, target){
    console.log(url)
    let a =  document.createElement('a');
    a.target = target;
    a.href = url;
    a.click();
}

function checkManualLabels(rowParams) {
    for (let k in rowParams) {
        if (rowParams[k].label) {
            return true;
        }
    }
    return false;
}

function findBarLabels(rowParams, origLabels, ref) {
    var labels = [];
    var target = "";
    var targetLab = "";
    if (ref == "value name") {
        target = "metricType";
    } else {
        target = "metricName"
    }
    for (var n in origLabels) {
        targetLab = origLabels[n];
        for (let k in rowParams) {
            if (rowParams[k][target] == targetLab && rowParams[k].label) {
                labels[n] = rowParams[k].label;
                break;
            }
        }
    }
    return labels;
}

function findBarSingleLabel(rowParams, targetLab, ref) {
    var res = null;
    var target = "";
    if (ref == "value name") {
        target = "metricType";
    } else {
        target = "metricName"
    }
    for (let k in rowParams) {
        if (rowParams[k][target] == targetLab) {
            if (rowParams[k].label && rowParams[k].label != "" && rowParams[k].label.trim().length != 0) {
                return rowParams[k].label;
            } else {
                return targetLab;
            }
        }
    }
    return res;
}

function checkBIDash(widgets) {
    for (let widgetId in widgets) {
        if (widgets[widgetId].code != null && widgets[widgetId].code != '') {
            return true;
        }
    }
}

var fetchAjax = function(queryUrl, dataObj, type, dataType, asyncFlag, timeoutVal) {
    if (queryUrl == null || queryUrl == "") {
        queryUrl = "../controllers/nullProxy.php";
    }
    // Return the $.ajax promise

    var serviceUriMatch = queryUrl.match(/serviceUri=([^&]*)/);

    if (serviceUriMatch) {
        var serviceUri = serviceUriMatch[1];
        queryUrl = "../controllers/superservicemapProxy.php/api/v1/?serviceUri=" + encodeServiceUri(serviceUri);
    }

    return $.ajax({
        url: queryUrl,
        data: dataObj,
        type: type,
        dataType: dataType,     // 'json'
        async: true,            // asyncFlag
        timeout: timeoutVal
    });
}

function isFloat(value) {
    if (
        typeof value === 'number' &&
        !Number.isNaN(value) &&
        !Number.isInteger(value)
    ) {
        return true;
    }

    return false;
}

function rgbToHex (rgb) {
    var hex = Number(rgb).toString(16);
    if (hex.length < 2) {
        hex = "0" + hex;
    }
    return hex;
};

function fullColorHex (rgbArray) {
    var red = rgbToHex((rgbArray.split(",")[0]).trim());
    var green = rgbToHex((rgbArray.split(",")[1]).trim());
    var blue = rgbToHex((rgbArray.split(",")[2]).trim());
    return red+green+blue;
};

function getBimColor(d, bimColorScaleString) {
    var bimColorScale = JSON.parse(bimColorScaleString);
    var min_val, max_val, bim_hex = null;
    for (let i = 0; i < bimColorScale.length; i++) {
        min_val = (bimColorScale[i]["min"] == null || bimColorScale[i]["min"] == '') ? Number.NEGATIVE_INFINITY : bimColorScale[i]["min"];
        max_val = (bimColorScale[i]["max"] == null || bimColorScale[i]["max"] == '') ? Number.POSITIVE_INFINITY : bimColorScale[i]["max"];
        bim_hex = '#'+fullColorHex(bimColorScale[i].rgb.substring(1, bimColorScale[i].rgb.length - 1));
        if (min_val == null) min_val = -1;
        if (d > parseFloat(min_val) && d <= parseFloat(max_val)) {
            return bim_hex;
        }
    }
}

function getSessionData(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

function getQueryString() {
    var queryStringKeyValue = window.parent.location.search.replace('?', '').split('&');
    var qsJsonObject = {};
    if (queryStringKeyValue != '') {
        for (i = 0; i < queryStringKeyValue.length; i++) {
            qsJsonObject[queryStringKeyValue[i].split('=')[0]] = queryStringKeyValue[i].split('=')[1];
        }
    }
    return qsJsonObject;
}

function composeSURI(baseUrl, key) {
    // var baseUrl = "http://www.disit.org/km4city/resource/iot/orionUNIFI/DISIT/";
    var suri = baseUrl;
    var key = getQueryString()[key];
    //var entityId = getQueryString().entityId;
    if (key != null) {
        return suri+key;
    } else {
        return null;
    }
}

function triggerMetricsForTrends(wName, listenerName, data, selMetrics, baseKbUrl, legendEntityName, timeRange, legendLabels) {
    var dataAttr = data.Service.features[0].properties.realtimeAttributes;
    const keys = Object.keys(dataAttr);
    var dataProcessedArray = [];
    var i=0;
    for (var n=0; n < keys.length; n++) {
       if (selMetrics.indexOf(keys[n]) != -1) {
        //if(selMetrics.includes(keys[n])) {
           var idx = selMetrics.indexOf(keys[n]);
           var serviceUri = data.Service.features[0].properties.serviceUri;
           var org = data.Service.features[0].properties.organization;
           var broker = data.Service.features[0].properties.brokerName;
           var name = data.Service.features[0].properties.name;
           //var metricName = org + ":" + broker + ":" + name;
           var metricName = (legendEntityName != null) ? legendEntityName : org + ":" + broker + ":" + name;
           dataProcessedArray[i] = {};
           dataProcessedArray[i].metricId = baseKbUrl + serviceUri + "&format=json";
           dataProcessedArray[i].metricHighLevelType = "IoT Device Variable";
           dataProcessedArray[i].metricName = metricName;
           dataProcessedArray[i].smField = keys[n];
           dataProcessedArray[i].metricType = keys[n];
           dataProcessedArray[i].serviceUri = serviceUri;
           if (wName.includes("TimeTrendCompare")) {
               dataProcessedArray[i].timeRange = timeRange;
           }
           if (wName.includes("CurvedLineSeries")) {
               if (legendLabels != null && legendLabels[i] != null) {
                   dataProcessedArray[i].legendLabels = legendLabels[idx];
               }
           }
           i++;
        }
    }

    $('body').trigger({
        type: listenerName,
        targetWidget: wName,
        passedData: dataProcessedArray
    });
}

function htmlEncode(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function prepareDataObjFromKBFeatures(features) {
    for (var f in features) {

    }
}

function convertHtmlEntities(str) {
    var map = {
        '&lt;': '<',
        '&gt;': '>',
        '&amp;': '&'
    };

    return str.replace(/&lt;|&gt;|&amp;/g, function(match) {
        return map[match];
    });
}

function csblTrigger(output_type, port_name, data, eventType, dt1_iso, dt2_iso, conn, coordsAndType) {


    //Find and check validity of the specific port of an event of a widget
    let check_validity = false,
        conn_id;
    let connections;
    if (conn.constructor === Array) {
        connections = conn;
    } else {
        connections = JSON.parse(conn.replace(/var connections = /g, '').replace(/;/g, '').replace(/\\/g, ''));
    }
    Object.keys(connections).forEach(id => {
        if (connections[id].port_name === port_name) {
            if (connections[id].output_type === output_type) {
                conn_id = id;
                check_validity = true;
            }
        }
    });

    if (check_validity) {
        let widget_type, widget_name;
        for (let i = 0; i < connections[conn_id].linked_target_widgets.length; i++) {

            widget_type = connections[conn_id].linked_target_widgets[i].widget_type;
            widget_name = connections[conn_id].linked_target_widgets[i].widget_name;

            switch (widget_type) {
                case 'widgetRadarSeries':
                    $('body').trigger({
                        type: "showRadarSeriesFromExternalContent_" + widget_name,
                        targetWidget: widget_name,
                        passedData: data
                    });
                    break;

                case 'widgetTimeTrend':
                    $('body').trigger({
                        type: "showTimeTrendFromExternalContent_" + widget_name,
                        targetWidget: widget_name,
                        passedData: data,
                        t1: dt1_iso,
                        t2: dt2_iso,
                        event: eventType
                    });
                    break;

                case 'widgetCurvedLineSeries':
                    $('body').trigger({
                        type: "showCurvedLinesFromExternalContent_" + widget_name,
                        targetWidget: widget_name,
                        field: data==null ? null : data[0].smField,
                        passedData: data,
                        t1: dt1_iso,
                        t2: dt2_iso,
                        event: eventType
                    });
                    break;

                case 'widgetPieChart':
                    $('body').trigger({
                        type: "showPieChartFromExternalContent_" + widget_name,
                        targetWidget: widget_name,
                        passedData: data
                    });
                    break;

                case 'widgetBarSeries':
                    $('body').trigger({
                        type: "showBarSeriesFromExternalContent_" + widget_name,
                        targetWidget: widget_name,
                        passedData: data
                    });
                    break;

                case 'widgetMap':
                    for (let n = 0; n < coordsAndType.length; n++) {
                        $('body').trigger({
                            type: "addSelectorPin",
                            target: widget_name,
                            passedData: coordsAndType[n]
                        });
                    }
                    break;

                case 'widgetSpeedometer':
                case 'widgetGaugeChart':
                case 'widgetSingleContent':
                    let title="";
                    if (data[0].metricName.includes(":")) {
                        let parts = data[0].metricName.split(":");
                        title = parts[parts.length - 1].trim(); // Assegna l'ultima parte della stringa e rimuove eventuali spazi bianchi
                    } else {
                        title = data[0].metricName.trim(); // Assegna l'intera stringa e rimuove eventuali spazi bianchi
                    }
                    title = title + " - " + data[0].metricType;
                    $('body').trigger({
                        type: "showLastDataFromExternalContentGis_" + widget_name,
                        targetWidget: widget_name,
                        field: data[0].metricType,
                        widgetTitle: title,
                        serviceUri: data[0].serviceUri
                    });
                    break;

                case 'widgetKnob':
                    break;

                case 'widgetNumericKeyboard':
                    break;

                case 'widgetExternalContent':
                    break;

                case 'widgetTable':
                    break;

                case 'widgetDeviceTable':
                    break;

                case 'widgetEventTable':
                    break;

                case 'widgetButton':
                    break;

                case 'widgetOnOffButton':
                    break;

                case 'widgetImpulseButton':
                    break;

                default:
            }
        }
    }
}

function sendMapSURI(port_name, jsonData){
    //data code
    var data = [];
    let h = 0;
    if (jsonData.layers[0].values) {
        var metrics = Object.keys(jsonData.layers[0].values);
        for (var l in jsonData.layers) {
            for (var m in metrics) {
                data[h] = {};
                data[h].serviceUri = jsonData.layers[l].serviceUri;
                data[h].metricId = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + jsonData.layers[l].serviceUri;
                data[h].metricHighLevelType = "Sensor";
                data[h].metricName = jsonData.layers[l].organization + ":" + jsonData.layers[l].serviceUri.split("resource/iot/")[1].split("/")[0] + ":" + jsonData.layers[l].deviceName;
                data[h].metricType = metrics[m];
                h++;
            }
        }
        csblTrigger("ListSURI", port_name, data, jsonData.event, null, null, jsonData.connections);
    } else if (Object.keys(jsonData.layers).length > 1) {
        getSmartCityAPIData = fetchAjax(configVars['kbUrlSuperServiceMap'] + "iot-search/?selection=" + jsonData.bounds._southWest.lat + ";" + jsonData.bounds._southWest.lng + ";" + jsonData.bounds._northEast.lat + ";" + jsonData.bounds._northEast.lng + ";&categories=" + jsonData.layers[0].tipo + "&maxResults=200&format=json", null, "GET", 'json', true, 0);
        getSmartCityAPIData.done(function(json_data) {
            var metrics = Object.keys(json_data.features[0].properties.values);
            for (var l in json_data.features) {
                for (var m in metrics) {
                    data[h] = {};
                    data[h].serviceUri = json_data.features[l].properties.serviceUri;
                    data[h].metricId = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + json_data.features[l].properties.serviceUri;
                    data[h].metricHighLevelType = "Sensor";
                    data[h].metricName = json_data.features[0].properties.organization + ":" + json_data.features[0].properties.serviceUri.split("resource/iot/")[1].split("/")[0] + ":" + json_data.features[l].properties.deviceName;
                    data[h].metricType = metrics[m];
                    h++;
                }
            }
            csblTrigger("ListSURI", port_name, data, jsonData.event, null, null, jsonData.connections);
        });
    } else {
        getSmartCityAPIData = fetchAjax(configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + jsonData.layers[0].serviceUri + "&maxResults=200&format=json", null, "GET", 'json', true, 0);
        getSmartCityAPIData.done(function(s4cData) {
            if (s4cData.realtime != null) {
                var metrics = Object.values(s4cData.realtime.head.vars);
                for (var l in s4cData.Service.features) {
                    for (var m in metrics) {
                        data[h] = {};
                        data[h].serviceUri = s4cData.Service.features[0].properties.serviceUri;
                        data[h].metricId = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + s4cData.Service.features[0].properties.serviceUri;
                        data[h].metricHighLevelType = "Sensor";
                        data[h].metricName = s4cData.Service.features[0].properties.organization + ":" + s4cData.Service.features[0].properties.serviceUri.split("resource/iot/")[1].split("/")[0] + ":" + s4cData.Service.features[0].properties.name;
                        data[h].metricType = metrics[m];
                        h++;
                    }
                }
                csblTrigger("ListSURI", port_name, data, jsonData.event, null, null, jsonData.connections);
            }
        });
    }
    //end data code
}

function sendSURI(port_name, jsonData){
    //data code
    var data = [];
    data[0] = {};
    var coordsAndType = [];
    coordsAndType[0] = {};
    var serviceUri = "";
    if (jsonData.value) {
        if (jsonData.value.metricName.includes(":")) {
            serviceUri = configVars['baseServiceURI'] + jsonData.value.metricName.split(":")[1] + "/" + jsonData.value.metricName.split(":")[0] + "/" + jsonData.value.metricName.split(":")[2];
            data[0].metricId = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + serviceUri + "&format=json";
            data[0].metricHighLevelType = "IoT Device Variable";
            coordsAndType[0].query = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + serviceUri + "&format=json";
            coordsAndType[0].queryType = "Default";
        } else {
            serviceUri = jsonData.value.metricId;
            data[0].metricId = serviceUri;
            data[0].metricHighLevelType = "MyKPI";
            coordsAndType[0].query = "datamanager/api/v1/poidata/" + serviceUri;
            coordsAndType[0].queryType = "MyPOI";
        }
        data[0].metricName = jsonData.value.metricName;
        data[0].metricType = jsonData.value.metricType;
        data[0].smField = jsonData.value.metricType;
        data[0].serviceUri = serviceUri;

        coordsAndType[0].desc = data[0].metricName;
        coordsAndType[0].color1 = "#ebb113";
        coordsAndType[0].color2 = "#eb8a13";
        csblTrigger("SURI", port_name, data, jsonData.event, null, null, jsonData.connections, coordsAndType);
    } else {
        let conn = jsonData.connections;
        delete jsonData["connections"]
        let event = jsonData.event;
        delete jsonData["event"];
        let count = 0;
        jsonData.forEach((item) => {
            data[count] = {};
            coordsAndType[count] = {};
            if (item.metricName.includes(":")) {
                serviceUri = configVars['baseServiceURI'] + item.metricName.split(":")[1] + "/" + item.metricName.split(":")[0] + "/" + item.metricName.split(":")[2];
                data[count].metricId = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + serviceUri + "&format=json";
                data[count].metricHighLevelType = "IoT Device Variable";
                coordsAndType[count].query = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + serviceUri + "&format=json";
                coordsAndType[count].queryType = "Default";
            } else {
                serviceUri = item.metricId;
                data[count].metricId = serviceUri;
                data[count].metricHighLevelType = "MyKPI";
                coordsAndType[count].query = "datamanager/api/v1/poidata/" + serviceUri;
                coordsAndType[count].queryType = "MyPOI";
            }
            data[count].metricName = item.metricName;
            data[count].metricType = item.metricType;
            data[count].smField = item.metricType;
            data[count].serviceUri = serviceUri;

            coordsAndType[count].desc = data[count].metricName;
            coordsAndType[count].color1 = "#ebb113";
            coordsAndType[count].color2 = "#eb8a13";

            /*console.log("Value: " + item.value);
            console.log("Metric Type: " + item.metricType);
            console.log("Metric Name: " + item.metricName);
            console.log("Metric Value Unit: " + item.metricValueUnit);
            console.log("Measured Time: " + item.measuredTime);
            console.log("--------------------");    */
            count++;
            //csblTrigger("SURI", port_name, data[count], event, null, null, conn, coordsAndType[count]);
        });
        csblTrigger("SURI", port_name, data, event, null, null, conn, coordsAndType);
    }
    //end data code
}

function sendListSURIAndMetrics(port_name, jsonData){
    //data code
    var coordsAndType = [];
    var data = [];
    var h = 0;
    var i = 0;
    var serviceUri = "";
    for (var l in jsonData.layers) {
        if (jsonData.layers[l].visible == true) {
            coordsAndType[i] = {};
            coordsAndType[i].desc = jsonData.layers[l].name;
            coordsAndType[i].color1 = "#ebb113";
            coordsAndType[i].color2 = "#eb8a13";
            if (!jsonData.metrics || jsonData.metrics.length<1) {
                if (jsonData.layers[l].realtimeAttributes) {
                    jsonData.metrics = Object.keys(jsonData.layers[l].realtimeAttributes);
                }
                if (jsonData.layers[l].kpidata) {
                    jsonData.metrics = jsonData.layers[l].name;
                }
            }
            for (var m in jsonData.metrics) {
                data[h] = {};
                if (jsonData.layers[l].name.includes(":")) {
                    serviceUri = configVars['baseServiceURI'] + jsonData.layers[l].name.split(":")[1] + "/" + jsonData.layers[l].name.split(":")[0] + "/" + jsonData.layers[l].name.split(":")[2];
                    data[h].metricId = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + serviceUri + "&format=json";
                    data[h].metricHighLevelType = "IoT Device Variable";
                    coordsAndType[i].query = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                    coordsAndType[i].queryType = "Default";
                } else if ((jsonData.layers[l].brokerName && jsonData.layers[l].brokerName != "") && (jsonData.layers[l].organization && jsonData.layers[l].organization != "")) {
                    serviceUri = configVars['baseServiceURI'] + jsonData.layers[l].brokerName + "/" + jsonData.layers[l].organization + "/" + jsonData.layers[l].name;
                    data[h].metricId = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + serviceUri + "&format=json";
                    data[h].metricHighLevelType = "IoT Device Variable";
                    coordsAndType[i].query = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + serviceUri + "&format=json";
                    coordsAndType[i].queryType = "Default";
                } else if (jsonData.layers[l].serviceUri && jsonData.layers[l].serviceUri != "") {
                    serviceUri = jsonData.layers[l].serviceUri;
                    data[h].metricId = configVars['kbUrlSuperServiceMap'] + "?serviceUri=" + serviceUri + "&format=json";
                    data[h].metricHighLevelType = "IoT Device Variable";
                    coordsAndType[i].query = "<?= $kbUrlSuperServiceMap ?>?serviceUri=" + serviceUri + "&format=json";
                    coordsAndType[i].queryType = "Default";
                } else {
                    if (jsonData.layers[l].name.includes("_")) {
                        serviceUri = "datamanager/api/v1/poidata/" + jsonData.layers[l].name.split("_")[1];
                    } else {
                        serviceUri = "datamanager/api/v1/poidata/" + jsonData.layers[l].name;
                    }
                    data[h].metricId = serviceUri;
                    data[h].metricHighLevelType = "MyKPI";
                    coordsAndType[i].query = serviceUri;
                    coordsAndType[i].queryType = "MyPOI";
                }
                data[h].metricName = jsonData.layers[l].name;
                data[h].metricType = jsonData.metrics[m];
                data[h].smField = jsonData.metrics[m];
                data[h].serviceUri = serviceUri;

                h++;
            }
            i++;
        }
    }
    csblTrigger("ListSURI", port_name, data, jsonData.event, null, null, jsonData.connections, coordsAndType);
    //end data code
}

function sendJSON(port_name, jsonData){

    csblTrigger("sendJSON", port_name, jsonData, jsonData.event, null, null, jsonData.connections);

}

function sendTimeRange(port_name, jsonData){

    if (jsonData.event == "reset zoom") {
        var dt1_iso = null;
        var dt2_iso = null;
    } else {
        var minT = jsonData["t1"];
        var maxT = jsonData["t2"];
        var dt1 = new Date(minT);
        var dt1_iso = dt1.toISOString().split(".")[0];
        var dt2 = new Date(maxT);
        var dt2_iso = dt2.toISOString().split(".")[0];
    }

    csblTrigger("DateTime_Interval", port_name, null, jsonData.event, dt1_iso, dt2_iso, jsonData.connections);
}

function extractSubstring(str, start, end) {
    //var start = "var connections = [{";
    //var end = "}];";
    var pattern = new RegExp(start + "(.*?)" + end, "s");
    var match = str.match(pattern);
    return match ? start + match[1] + end : null;
}

function removeSubstring(str, result) {
    var escapedResult = result.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
    return str.replace(escapedResult, '');
}

function insertSubstring(str, insertStr, afterStr) {
    var pos = str.indexOf(afterStr.trim());
    if (pos !== -1) {
        return str.slice(0, pos + afterStr.length) + insertStr + str.slice(pos + afterStr.length);
    }
    return str;
}

function encodeHTML(str) {
    var map = {
        '\n': '<br />',
        '\t': '&nbsp;&nbsp;&nbsp;&nbsp;',
        ' ': '&nbsp;',
        '"': '&quot;',
        "'": '&#39;',
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;'
    };

    var convertedStr = "<p>" + str.replace(/[\n\t "<>&']/g, function(m) { return map[m]; }) + "</p>";
    return convertedStr;
}

function decodeHTML(html) {
    var map = {
        '<br />': '\n',
        '&nbsp;&nbsp;&nbsp;&nbsp;': '\t',
        '&nbsp;': ' ',
        '&quot;': '"',
        '&#39;': "'",
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>'
    };

    return html.replace(/<br \/>|&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;|&quot;|&#39;|&amp;|&lt;|&gt;/g, function(m) { return map[m]; });
}

function checkCsblEncoding(code,conn) {
    if (!code) {
        return null;
    }
    if (conn != null || (code.includes("events_code_start") && code.includes("events_code_end")))
        return encodeHTML(code);
    else
        return code;
}

function replaceTabsWithSpaces(str) {
    return str.replace(/\\t/g, "    ");
}

function compareObj(obj1, obj2) {
    var keysObj1 = Object.keys(obj1);
    var keysObj2 = Object.keys(obj2);

    if (keysObj1.length !== keysObj2.length) {
        return false;
    }

    for (var key of keysObj1) {
        if (obj1[key] !== obj2[key]) {
            return false;
        }
    }

    return true;
}


function prepareCkEditorTemplate(ckEditorStr) {
    if (ckEditorStr == null || ckEditorStr.trim() === "") {
        let mod=1;
        if (mod===0) {
            return "/*&nbsp;<br>\n" +
                "&nbsp;&nbsp;&nbsp;&nbsp;Use the following template (comments included)<br>\n" +
                "&nbsp;&nbsp;&nbsp;&nbsp;if you want your CK Editor code to be compliant<br>\n" +
                "&nbsp;&nbsp;&nbsp;&nbsp;with the CSBL Editor expected format.<br>\n" +
                "*/<br>\n" +
                "<br>\n" +
                "function execute(){<br>\n" +
                "<br>\n" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;var e;<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if (IsJsonString(param)) {<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;e = JSON.parse(param);<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;} else {<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;e = param;<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>" +
                "<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;var connections = [{&quot;port_name&quot;:&quot;&lt;PORT_NAME&gt;&quot;,&quot;output_type&quot;:&quot;&lt;PORT_TYPE&gt;&quot;,&quot;linked_target_widgets&quot;:[{&quot;widget_name&quot;:&quot;&lt;TARGET_WIDGET_NAME&gt;&quot;,&quot;widget_type&quot;:&quot;&lt;TARGET_WIDGET_TYPE&gt;&quot;}]}];<br>" +
                "<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if (!e.connections || e.connections == []) e.connections = connections;<br>" +
                "<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//events_code_start<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if(e.event == &quot;&lt;EVENT_NAME&gt;&quot;){<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//events_code_part_start<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//events_code_part_end<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//events_code_end<br>" +
                "<br>\n" +
                "}";
        } else {
            return "/*&nbsp;<br>\n" +
                "&nbsp;&nbsp;&nbsp;&nbsp;Use the following template (comments included)<br>\n" +
                "&nbsp;&nbsp;&nbsp;&nbsp;if you want your CK Editor code to be compliant<br>\n" +
                "&nbsp;&nbsp;&nbsp;&nbsp;with the CSBL Editor.<br>\n" +
                "*/<br>\n" +
                "<br>\n" +
                "function execute(){<br>\n" +
                "<br>\n" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Connections JSON Template (CSBL Editor)<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;var connections = [{&quot;port_name&quot;:&quot;&lt;PORT_NAME&gt;&quot;,&quot;output_type&quot;:&quot;&lt;OUT_PORT_TYPE&gt;&quot;,&quot;linked_target_widgets&quot;:[{&quot;widget_name&quot;:&quot;&lt;TARGET_WIDGET_NAME&gt;&quot;,&quot;widget_type&quot;:&quot;&lt;TARGET_WIDGET_TYPE&gt;&quot;}]}];<br>" +
                "<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;var e=readInput(param, connections);<br>" +
                "<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//events_code_start<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if(e.event == &quot;&lt;EVENT_NAME&gt;&quot;){<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//events_code_part_start<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//events_code_part_end<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>" +
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;//events_code_end<br>" +
                "<br>\n" +
                "}";
        }
    } else return ckEditorStr;
}

function encodeMixedHTML(str) {

    // Replace & with its HTML equivalent
    str = str.replace(/&/g, '&amp;');

    // Replace <p>, </p>, <br> and <br /> tags with temporary markers
    str = str.replace(/<p>/g, 'TEMP_P_START');
    str = str.replace(/<\/p>/g, 'TEMP_P_END');
    str = str.replace(/<br \/>/g, 'TEMP_BR_ALT');
    str = str.replace(/<br>/g, 'TEMP_BR');

    // Encode remaining < and > tags
    str = str.replace(/</g, '&lt;');
    str = str.replace(/>/g, '&gt;');

    // Restore <p>, </p>, <br> and <br /> tags
    str = str.replace(/TEMP_P_START/g, '<p>');
    str = str.replace(/TEMP_P_END/g, '</p>');
    str = str.replace(/TEMP_BR_ALT/g, '<br />');
    str = str.replace(/TEMP_BR/g, '<br>');

    // Replace " with its HTML equivalent
    str = str.replace(/"/g, '&quot;');

    // Replace spaces between HTML tags with &nbsp;
    //str = str.split('>').join('>&nbsp;').split('<').join('&nbsp;<').replace(/&nbsp;<\/p>/g, '</p>').replace(/<p>&nbsp;/g, '<p>');

    return str;

}

function encodeMixHTML(html) {
    var temp = document.createElement('div');
    temp.innerHTML = html;
    var textNodes = temp.querySelectorAll('*:not(p):not(br)');

    textNodes.forEach(function(node) {
        var children = node.childNodes;
        for (var i = 0; i < children.length; i++) {
            if (children[i].nodeType === 3) {
                var textNode = children[i];
                var textContent = textNode.nodeValue;
                var encodedTextContent = textContent.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                textNode.nodeValue = encodedTextContent;
            }
        }
    });

    return temp.innerHTML;
}

function transformString(originalString) {
    // Sostituisci i caratteri < e > con le loro entità HTML equivalenti, escludendo quelli nei tag <p>, </p> e <br />
    let transformedString = originalString.replace(/<(?!p>|\/p>|br\s*\/?>)/gi, '&lt;').replace(/(?!<\/p|<br|<p)>/gi, '&gt;');

    // Sostituisci gli spazi tra i tag HTML con &nbsp;
    transformedString = transformedString.replace(/(?<=\>)( +)(?=\<)/g, function(match) {
        return '&nbsp;'.repeat(match.length);
    });

    return transformedString;
}

function removeTags(str) {
    // Rimuovi i tag <p>, </p> e <br />
    str = str.replace(/<p>/g, '');
    str = str.replace(/<\/p>/g, '');
    str = str.replace(/<br \/>/g, '');

    return str;
}

function readInput(param, connections) {
    let e;
    if (IsJsonString(param)) {
        e = JSON.parse(param);
    } else {
        e = param;
    }

    if ((!e.connections || e.connections === []) && connections != null) e.connections = connections;

    return e;
}

function darkenColor(hexColor, percent) {
    hexColor = hexColor.replace(/^#/, '');

    var r = parseInt(hexColor.substring(0, 2), 16);
    var g = parseInt(hexColor.substring(2, 4), 16);
    var b = parseInt(hexColor.substring(4, 6), 16);

    r = Math.floor(r * (1 - percent));
    g = Math.floor(g * (1 - percent));
    b = Math.floor(b * (1 - percent));

    var newColor = "#" +
        ("0" + r.toString(16)).slice(-2) +
        ("0" + g.toString(16)).slice(-2) +
        ("0" + b.toString(16)).slice(-2);

    return newColor;
}